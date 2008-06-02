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

require_once('config.inc.php');
require_once('ProductionTaskValidationTools.php');
require_once('HTML/QuickForm.php');
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once('Objects/ActivatedChainTask.php');

// auth, url de retour, variables {{{
$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
        UserAccount::PROFILE_GED_PROJECT_MANAGER));

$user = $auth->getUser();
$retURL = isset($_REQUEST['retURL'])?
    $_REQUEST['retURL']:'ActivatedChainTaskList.php';

SearchTools::ProlongDataInSession();
// }}}

// récupération de l'ack et validations {{{
$mapper = Mapper::singleton('ActivatedChainTask');
$ack = $ackID = false;
if (isset($_REQUEST['ackId'])) {
    $ackID = $_REQUEST['ackId'];
    $ack = $mapper->load(array('Id'=>$ackID));
}

// tâche non trouvée
if (!($ack instanceof ActivatedChainTask)) {
    Template::errorDialog(E_NO_ITEM_FOUND, $retURL);
    exit(1);
}
// }}}

// traitement après soumission du formulaire {{{
if (isset($_REQUEST['StartTask']) || isset($_REQUEST['ReStartTask']) 
 || isset($_REQUEST['FinishTask']) || isset($_REQUEST['StopTask'])) {

    if (isset($_REQUEST['StartTask'])) {
        $action = ACTION_START;
    } elseif (isset($_REQUEST['StopTask'])) {
        $action = ACTION_STOP;
    } elseif (isset($_REQUEST['ReStartTask'])) {
        $action = ACTION_RESTART;
    } elseif (isset($_REQUEST['FinishTask'])) {
        $action = ACTION_FINISH;
    }
    $comment = isset($_POST['TaskComment'])?$_POST['TaskComment']:false;
    $qty = isset($_POST['TaskRealQuantity'])?$_POST['TaskRealQuantity']:false;
    $result = handleTaskAction($action, $ack, $user, false, $qty, $comment);
    if (Tools::isException($result)) {
        Template::errorDialog($result->getMessage(), $retURL);
        exit(1);
    }
    Tools::redirectTo($retURL);
    exit(0);
}
// }}}

// traitement du template avec quickform {{{
$smarty = new Template();
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('CustomerAttractivityAddEdit', 'post');

// champs hidden
$form->addElement('hidden', 'retURL', $retURL);
$form->addElement('hidden', 'ackId', $ackID);
$form->addElement('hidden', 'formSubmitted', 1);

// champs readonly
$form->addElement('text', 'CommandNo', _('Order number'), 
    'style="width:100%;" class="ReadOnlyField" readonly');
$form->addElement('text', 'TaskNameAndId', _('Task name and number'), 
    'style="width:100%;" class="ReadOnlyField" readonly');
$form->addElement('text', 'TaskDuration', _('Expected duration'), 
    'style="width:100%;" class="ReadOnlyField" readonly');
$form->addElement('text', 'TaskOperator', _('Expected operator'), 
    'style="width:100%;" class="ReadOnlyField" readonly');
$form->addElement('text', 'TaskBeginDate', _('Expected beginning date'), 
    'style="width:100%;" class="ReadOnlyField" readonly');
$form->addElement('text', 'TaskEndDate', _('Expected end date'), 
    'style="width:100%;" class="ReadOnlyField" readonly');
$form->addElement('text', 'TaskRealOperator', _('Operator'), 
    'style="width:100%;" class="ReadOnlyField" readonly');
$form->addElement('text', 'TaskState', _('Task state'), 
    'style="width:100%;" class="ReadOnlyField" readonly');
$form->addElement('text', 'TaskRealBeginDate', _('Started on'), 
    'style="width:100%;" class="ReadOnlyField" readonly');
$form->addElement('text', 'TaskRealEndDate', _('Finished on'), 
    'style="width:100%;" class="ReadOnlyField" readonly');
$form->addElement('text', 'TaskRealDuration', _('Actual duration'), 
    'style="width:100%;" class="ReadOnlyField" readonly');
// champs de saisie
$form->addElement('textarea', 'TaskComment', _('Comment'), 
    'style="width:100%;"');
$form->addElement('text', 'TaskRealQuantity', _('Parts quantity'));

// actions (boutons)
$css = ' class="button"';
$disabled = $ack->getState()==ActivatedChainTask::STATE_TODO?'':'disabled';
$form->addElement('submit', 'StartTask', _('Start'), $disabled . $css);
$disabled = $ack->getState()==ActivatedChainTask::STATE_IN_PROGRESS?'':'disabled';
$form->addElement('submit', 'StopTask', _('Pause'), $disabled . $css);
$disabled = $ack->getState()==ActivatedChainTask::STATE_STOPPED?'':'disabled';
$form->addElement('submit', 'ReStartTask', _('Restart'), $disabled . $css);
$disabled = $ack->getState()==ActivatedChainTask::STATE_IN_PROGRESS?'':'disabled';
$confirm = ' onclick="if (!confirm(ProductionTaskValidation_0)) return false;"';
$form->addElement('submit', 'FinishTask', _('Finish'), $disabled . $confirm . $css);
$form->addElement('button', 'Back', _('Back to list of tasks'), 
    sprintf('onClick="window.location.href=\'%s\'"%s', $retURL, $css));
// }}}

// valeurs par défaut {{{
$defaultValues = array();
$defaultValues['CommandNo'] = Tools::getValueFromMacro($ack, 
    '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.CommandNo%');
$defaultValues['TaskNameAndId'] = $ack->getName() . ' n°' . $ack->getId();
$defaultValues['TaskBeginDate'] = I18N::formatDate($ack->getBegin());
$defaultValues['TaskEndDate'] = I18N::formatDate($ack->getEnd());
$defaultValues['TaskDuration'] = I18N::formatDuration($ack->getDuration());
$defaultValues['TaskOperator'] = Tools::getValueFromMacro($ack, 
    '%ActivatedOperation.Actor.Name%');
$defaultValues['TaskRealOperator'] = $user->getIdentity();
$stateArray = ActivatedChainTask::getStateConstArray();
$defaultValues['TaskState'] = $stateArray[$ack->getState()];
$defaultValues['TaskRealBeginDate'] = I18N::formatDate($ack->getRealBegin());
$realEndDate = I18N::formatDate($ack->getRealEnd());
$defaultValues['TaskRealEndDate'] = $realEndDate==0?"":$realEndDate;
$defaultValues['TaskRealDuration'] = I18N::formatDuration(
    $ack->getRealDuration());
$defaultValues['TaskRealQuantity'] = $ack->getRealQuantity();
$ackDetail = $ack->getActivatedChainTaskDetail();
$defaultValues['TaskComment'] = $ackDetail instanceof ActivatedChainTaskDetail?$ackDetail->getComment():''; 
$form->setDefaults($defaultValues);
// }}}

// validations côté client {{{
// aucune pour l'instant ?
// }}}

// affichage de la page {{{
$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());
$smarty->assign('TaskState', $ack->getState());
$smarty->assign('valign', 'valign="middle"');
$pageTitle = _('Task validation ') 
    . Tools::getValueFromMacro($ack, '%Task.Name%');
$pageContent = $smarty->fetch('ActivatedChainTask/ProductionTaskValidation.html');
Template::page($pageTitle, $pageContent);
// }}}

?>