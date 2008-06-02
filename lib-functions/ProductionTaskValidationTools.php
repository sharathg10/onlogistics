<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of Onlogistics, a web based ERP and supply chain 
 * management application. 
 *
 * Copyright (C) 2003-2008 ATEOR
 *
 * This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU Affero General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or (at your 
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public 
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5.1.0+
 *
 * @package   Onlogistics
 * @author    ATEOR dev team <dev@ateor.com>
 * @copyright 2003-2008 ATEOR <contact@ateor.com> 
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU AGPL
 * @version   SVN: $Id$
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once('Objects/Task.inc.php');
require_once('Objects/ActivatedChainTask.php');

// constantes des codes action
define('ACTION_START',       10);
define('ACTION_STOP',        11);
define('ACTION_RESTART',     12);
define('ACTION_FINISH',      13);
define('ACTION_SUSPEND_ALL', 99);

// messages d'erreur
define('E_INVALID_STATE', _('Wrong task type provided.'));
define('E_TASKINFO', _('Task "%s" number %s: %s'));
define('E_NOUSER', _('Task operator was not be found.'));
define('E_TASKSTART', _('Only tasks with state set to "in progress" can be started.'));
define('E_TASKRESTART', _('Only tasks with state set to "supended" can be restarted.'));
define('E_TASKSTOP', _('Only tasks with state set to "in progress" can be suspended.'));
define('E_TASKFINISH', _('Only tasks with state set to "in progress" can be finished.'));
define('E_TASKUNKNOWN', _('Unknown action.'));


/**
 * Gère les actions possibles pour une ack de production donnée.
 *
 * @access public
 * @param int $action, le code de l'action (cf. constantes)
 * @param ActivatedChainTask $ack, la tâche en cours
 * @param UserAccount $user, l'utilisateur qui valide la tâche
 * @param string $date la date de validation au format mysql datetime
 * @return mixed true ou une exception contenant le message d'erreur
 */
function handleTaskAction($action, $ack, $user, $date=false, $qty=false,
    $comment=false, $commitChanges=true) {
    if (!$date) {
        $date = date('Y-m-d H:i:s', time());
    }
    if ($action == ACTION_START) {
        // démarrage de tache
        if ($ack->getState() != ActivatedChainTask::STATE_TODO) {
            return handleTaskActionException(E_TASKSTART, $ack);
        }
        $ack->setRealBegin($date);
        $ack->setState(ActivatedChainTask::STATE_IN_PROGRESS);
    } elseif ($action == ACTION_RESTART) {
        // reprise de tache
        if ($ack->getState() != ActivatedChainTask::STATE_STOPPED) {
            return handleTaskActionException(E_TASKRESTART, $ack);
        }
        $ack->setRestartDate($date);
        $ack->setState(ActivatedChainTask::STATE_IN_PROGRESS);
    } elseif (in_array($action, array(ACTION_STOP, ACTION_FINISH))) {
        // arrêt ou terminaison de tache
        if ($ack->getRestartDate() == 0) {
            // la tache n'a pas été interrompue
            $addon = DateTimeTools::MySQLDateSubstract(
                $date, $ack->getRealBegin(), true);
        } else {
            // la tache à été interrompue
            $addon = DateTimeTools::MySQLDateSubstract(
                $date, $ack->getRestartDate(), true);
        }
        $ack->setRealDuration($ack->getRealDuration() + $addon);
        if ($action == ACTION_STOP) {
            // interruption de tache
            if ($ack->getState() != ActivatedChainTask::STATE_IN_PROGRESS) {
                return handleTaskActionException(E_TASKSTOP, $ack);
            }
            $ack->setInterruptionDate($date);
            $ack->setState(ActivatedChainTask::STATE_STOPPED);
        } else {
            // terminaison de tache
            if ($ack->getState() != ActivatedChainTask::STATE_IN_PROGRESS) {
                return handleTaskActionException(E_TASKFINISH, $ack);
            }
            $ack->setState(ActivatedChainTask::STATE_FINISHED);
            $ack->setRealEnd($date);
            // il faut supprimer les indisponibilités liés à l'aco
            // correspondante, on met le flag à true
            $isFinished = true;
        }
    } else {
        // action inconnue !
        return handleTaskActionException(E_TASKUNKNOWN, $ack);
    }
    // à faire dans tous les cas
    if ($commitChanges) {
        Database::connection()->startTrans();
    }
    // on assigne l'utilisateur
    $ack->setValidationUser($user);
    // optionnel: gestion de la quantité saisie
    if ($qty) {
        $ack->setRealQuantity($qty);
    }
    // optionnel: gestion du commentaire
    if ($comment) {
        $ackDetail = $ack->getActivatedChainTaskDetail();
        if (!($ackDetail instanceof ActivatedChainTaskDetail)) {
            $ackDetail = Object::load('ActivatedChainTaskDetail');
            $ack->setActivatedChaintaskDetail($ackDetail);
        }
        $ackDetail->setComment($comment);
        if ($commitChanges) {
            $ackDetail->save();
        }
    }
    // sauvegarde et suppression eventuelles des indisponibilités liés à
    // l'ACO correspondante
    if ($commitChanges) {
        $ack->save();
    }
    if (isset($isFinished)) {
        $unaMapper = Mapper::singleton('Unavailability');
        $unaCol = $unaMapper->loadCollection(
            array('ActivatedChainOperation' => $ack->getActivatedOperationId()));
        $unaMapper->delete($unaCol->getItemIds());
        // recalcul du cout réél
        $achID = Tools::getValueFromMacro($ack, 
            '%ActivatedOperation.ActivatedChain.Id%');
        $commandID = Tools::getValueFromMacro($ack,
            '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Id%');
        $command = Object::load('Command', array('Id'=>$commandID));
        $ach = Object::load('ActivatedChain', array('Id'=>$achID));
        if ($command instanceof ProductCommand) {
            require_once 'Quantificator/ProductCommandQuantificator.php';
            $qtf = new ProductCommandQuantificator($ach, $command, true);
        } else if ($command instanceof ChainCommand) {
            require_once 'Quantificator/ChainCommandQuantificator.php';
            $qtf = new ChainCommandQuantificator($ach, $command, true);
        } else {
            $qtf = false; 
        }
        if ($qtf) {
            $qtf->execute(SORT_ASC, $ack->getActivatedOperationId(), $ack->getId());
        }
    }
    if ($commitChanges) {
        Database::connection()->completeTrans();
    }
    return true;
}

/**
 * Retourne une exception formatée avec le msg et la tache passée en
 * paramètres
 *
 * @access public
 * @param string $msg
 * @param ActivatedChainTask $ack
 *
 */
function handleTaskActionException($msg, $ack) {
    return new Exception(
        sprintf(
            E_TASKINFO,
            Tools::getValueFromMacro($ack, '%Task.Name%'),
            $ack->getId(),
            $msg
        )
    );
}

/**
 * Retourne un objet filter pour filtrer les tâches de production validables.
 * Conformément au SC, la règle est la suivante:
 * "On restreint l'affichage aux  ack de type production et validables
 * (tobeValidated = 1) et aux ack liées à un acm (mouvement interne ou normal)."
 *
 * @access public
 * @return object filter
 */
function getValidationTaskFilter($todo=false){
    $cpn1 = new FilterComponent(
        new FilterRule(
            'Task.ToBeValidated',
            FilterRule::OPERATOR_EQUALS,
            true
        ),
        new FilterRule(
            'Task.Id',
            FilterRule::OPERATOR_IN,
            in_array('consulting', Preferences::get('TradeContext', array())) ?
                getConsultingTaskIds() : getProductionTaskIds()
        ),
        FilterComponent::OPERATOR_AND
    );
    if ($todo == true) {
        $cpn1->setItem(
            new FilterRule(
                'State',
                FilterRule::OPERATOR_EQUALS,
                ActivatedChainTask::STATE_TODO
            )
        );
    }
    $cpn2 = new FilterRule(
        'Task.Id',
        FilterRule::OPERATOR_IN,
        array(
            TASK_STOCK_ENTRY, TASK_STOCK_EXIT,
            TASK_INTERNAL_STOCK_ENTRY, TASK_INTERNAL_STOCK_EXIT
        )
    );
    return new FilterComponent($cpn1, $cpn2, FilterComponent::OPERATOR_OR);
}

?>
