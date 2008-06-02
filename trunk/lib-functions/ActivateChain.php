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

require_once('Objects/ActivatedChain.php');
require_once('Objects/ActivatedChainOperation.php');
require_once('Objects/ActivatedChainTask.php');
require_once('Objects/Task.inc.php');

define('E_NO_CHAIN', _('Parameter to pass to the function must be chain.'));
define('E_NO_OPERATION', _('Chain "%s" does not have any operation, please contact your admin.'));
define('E_NO_TASK', _('Operation "%s" does not have any task, please contact your admin.'));
define('E_NOMENCLATURE_EXPIRED', _('Wished product version is not available anymore, please contact your admin.'));
define('E_NO_COMPONENT',
    _('Task associated components could not be found.')
    . ': <ul><li>%s</li></ul>'
    . _('Please contact your admin and tell him to fix chain "%s".')
);


/**
 * Duplique une chaine, ses opérations et ses taches
 * en chaine activée
 *
 * @param object $ Chain
 * @param object $ ChainCommand ou ProductCommand
 * @access public
 * @return void
 */
function ActivateChain($chn, $command)
{
    if (!($chn instanceof Chain)) {
        return new Exception(E_NO_CHAIN);
    }
    $errors = array();
    // Activation de la chaîne
	$achMapper = Mapper::singleton('ActivatedChain');
	$ach = Tools::singleton('ActivatedChain', $achMapper->generateId());
    //$ach = new ActivatedChain();
    $ach->setReference($chn->getReference());
    $ach->setDescription($chn->getDescription());
    $ach->setDescriptionCatalog($chn->getDescriptionCatalog());
    $ach->setOwner($chn->getOwner());
    $ach->setPivotDateType($chn->getPivotDateType());
    $ach->setSiteTransition(
        	getRealActorSiteTransition($chn->getSiteTransition(), $command));
    $ach->setProductTypeCollection(
        	$chn->getProductTypeCollection());
    $ach->setBarCodeType($chn->getBarCodeType());
    $ach->setType($chn->getType());
    //$ProductCollection = $chn->getProductCollection();
    //$ach->setProductCollection($ProductCollection);
    /**
     * Activation des opérations
     */
    $choCollection = $chn->getChainOperationCollection();
    if (!($choCollection instanceof Collection) ||
        $choCollection->getCount() == 0) {
        return new Exception(
            sprintf(E_NO_OPERATION, $chn->getReference())
        );
    }
    $ach->save();

    for($i = 0; $i < $choCollection->getCount(); $i++) {
        unset($cho);
        $cho = $choCollection->getItem($i);
        unset($aco);

		$acoMapper = Mapper::singleton('ActivatedChainOperation');
		$aco = Tools::singleton('ActivatedChainOperation', $acoMapper->generateId());
        //$aco = new ActivatedChainOperation();
        $aco->setOrder($cho->getOrder());
        $ope = $cho->getOperation();
        $aco->setOperation($ope);
        $aco->setActivatedChain($ach);
        /**
         * Gestion acteur générique
         */
        $actor = guessOperationActor($cho, $command);
        if (Tools::isException($actor)) {
            return $actor;
        }
        if ($ope->getIsConcreteProductNeeded() && $command instanceof CourseCommand) {
            // on doit affecter le concreteproduct de la commande à l'operation
            $aco->setConcreteProduct($command->getAeroConcreteProduct());
        }
        $aco->setActor($actor);
        $aco->save();
        /**
         * Activation des taches
         */
        unset($chtCollection);
        $chtCollection = $cho->getChainTaskCollection();
        if (!($chtCollection instanceof Collection) ||
            $chtCollection->getCount() == 0) {
            return new Exception(
                sprintf(E_NO_TASK, $ope->getName())
            );
        }
        for($j = 0; $j < $chtCollection->getCount(); $j++) {
            unset($cht);
            $cht = $chtCollection->getItem($j);  // une ChainTask
            unset($act);

			$actMapper = Mapper::singleton('ActivatedChainTask');
			$act = Tools::singleton('ActivatedChainTask', $actMapper->generateId());
            //$act = new ActivatedChainTask();
            $act->setOrder($cht->getOrder());
            $act->setActivatedOperation($aco->getId());
            $act->setInteruptible($cht->getInteruptible());
            $act->setRawDuration($cht->getDuration());
            $act->setDurationType($cht->getDurationType());
            $act->setKilometerNumber($cht->getKilometerNumber());
            $act->setTriggerMode($cht->getTriggerMode());
            $act->setTriggerDelta($cht->getTriggerDelta());
            $act->setRawCost($cht->getCost());
            $act->setCostType($cht->getCostType());
            $act->setInstructions($cht->getInstructions());
            $tsk = $cht->getTask();
            $act->setTask($tsk);
            $ast = getRealActorSiteTransition($cht->getActorSiteTransition(), 
            $command, isTransportTask($cht->getTask()));
            $act->setActorSiteTransition($ast);
            $DepartureInstant = $cht->getDepartureInstantId();
            $act->setDepartureInstant($DepartureInstant);
            $ArrivalInstant = $cht->getArrivalInstantId();
            $act->setArrivalInstant($ArrivalInstant);
            $act->setUserAccountCollection($cht->getUserAccountCollection());
			// Pour les Taches d'activation:
            $act->setDepartureActor($cht->getDepartureActor());
            $act->setDepartureSite($cht->getDepartureSite());
            $act->setArrivalActor($cht->getArrivalActor());
            $act->setArrivalSite($cht->getArrivalSite());
            $act->setWishedDateType($cht->getWishedDateType());
            $act->setDelta($cht->getDelta());
            $act->setChainToActivate($cht->getChainToActivate());
            $act->setProductCommandType($cht->getProductCommandType());
            $cpnCollection = $cht->getComponentCollection();
            $act->setComponentCollection($cpnCollection);
            $act->setComponentQuantityRatio($cht->getComponentQuantityRatio());
            $act->setActivationPerSupplier($cht->getActivationPerSupplier());
            $act->setRessourceGroup($cht->getRessourceGroup());
            $cpn = $cht->getComponent();
            // vérification:
            // si tache d'assemblage et pas de composant associé, ou tache de
            // stock interne et collection de composants vide
            // erreur (remontée hors de la boucle);
            $tskID = $cht->getTaskId();
            $isInternalTask = in_array($tskID,
                array(TASK_INTERNAL_STOCK_EXIT, TASK_INTERNAL_STOCK_ENTRY));
            if ((($tskID == TASK_ASSEMBLY || $tskID == TASK_SUIVI_MATIERE) && !($cpn instanceof Component))
             || ($isInternalTask && $cpnCollection->getCount() == 0))
            {
                $errors[] = sprintf(
                    _('%s (task no %s in operation %s)'),
                    $tsk->getName(),
                    $cht->getOrder(),
                    $ope->getName()
                );
            }
            // vérification:
            // si la a nomenclature une période de validité dépassée
            // l'activation est impossible
            if ($cpn instanceof Component) {
                $nom = $cpn->getNomenclature();
                if ($nom instanceof Nomenclature && $nom->getEndDate('timestamp') < time()) {
                    return new Exception(E_NOMENCLATURE_EXPIRED);
                }
            }
            $act->setComponent($cpn);
            $act->save();

            // Assignation des propriétés PivotTask de ach et FirstTask et
			// LastTask de aco
            if ($cht->getId() == $chn->getPivotTaskId()) {
                $ach->setPivotTask($act);
            }
            if (0 == $j) {
                $aco->setFirstTask($act);
            }
            if ($chtCollection->getCount() - 1 == $j) {
                $aco->setLastTask($act);
            }
        }
        $aco->setTaskCount($chtCollection->getCount());
        $aco->save();
    }
    // gestion des erreurs de la boucle
    if (count($errors) > 0) {
        return new Exception(
            sprintf(E_NO_COMPONENT, implode('</li><li>', $errors), $chn->getReference())
        );
    }
    $ach->save();
    return $ach;
}

/**
 * getRealActorSiteTransition()
 *
 * Retourne la relation acteur/site en fonction de la commande
 * et non du modèle de chaine, ceci afin de gérer les chaines avec
 * des acteurs génériques.
 *
 * Par ex: si un modele de chaine à un acteur de départ générique,
 * la chaine activée aura pour acteur de départ l'expéditeur de la commande
 * et non l'acteur générique
 *
 * @param  $actorSiteTransition object ActorSiteTransition
 * @param  $command object Command
 * @return object ActorSiteTransition
 */
function getRealActorSiteTransition($actorSiteTransition, $command, $isTransportTask=false)
{
    if (false == $command) {
        // pas de commande passée ? on retourne l'actorsitetransition
        return $actorSiteTransition;
    }
    if (false == $actorSiteTransition) {
        return $actorSiteTransition;
    }
    $hasChanged = false;
	/* acteur */
    $departureActor = $actorSiteTransition->getDepartureActor();
    $arrivalActor = $actorSiteTransition->getArrivalActor();

	/* site */
    $departureSite = $actorSiteTransition->getDepartureSite();
    $arrivalSite = $actorSiteTransition->getArrivalSite();
    if (!Tools::isEmptyObject($departureActor) && $departureActor->isGeneric()) {
        $departureActor = $command->getExpeditor();
        $departureSite = $command->getExpeditorSite();
    }
    if (!Tools::isEmptyObject($arrivalActor) && $arrivalActor->isGeneric()) {
        $arrivalActor = $command->getDestinator();
        $arrivalSite = $command->getDestinatorSite();
    }

	require_once('Objects/ActorSiteTransition.php');
	$ActorSiteTransition = new ActorSiteTransition();

	$ActorSiteTransition->setDepartureActor($departureActor);
    $ActorSiteTransition->setDepartureSite($departureSite);

    if(!$isTransportTask) {
        $departureZone = false;
        $arrivalZone = false;
        if(method_exists($departureActor, 'getMainSite')) {
            $site = $departureActor->getMainSite();
            if($site instanceof Site) {
                $departureZone = $site->getZone();
            } else {
                Template::errorDialog(sprintf(_('No site assigned to actor %s'), 
                    $departureActor->getName()), $_SERVER['PHP_SELF']);
                exit();
            }
        }
        if(method_exists($arrivalActor, 'getMainSite')) {
            $site = $arrivalActor->getMainSite();
            if($site instanceof Site) {
                $arrivalZone = $site->getZone();
            } else {
                Template::errorDialog(sprintf(_('No site assigned to actor %s'), 
                    $arrivalActor->getName()), $_SERVER['PHP_SELF']);
                exit();
            }
        }
    } else {
	    $departureZone = method_exists($departureActor, 'getMainSite')?
            $departureSite->getZone():false;
	    $arrivalZone = method_exists($arrivalActor, 'getMainSite')?
            $arrivalSite->getZone():false;
    }
	$ActorSiteTransition->setDepartureZone($departureZone);

	$ActorSiteTransition->setArrivalActor($arrivalActor);
	$ActorSiteTransition->setArrivalSite($arrivalSite);
	$ActorSiteTransition->setArrivalZone($arrivalZone);

	$ActorSiteTransition->save();
    return $ActorSiteTransition;
}

/**
 * guessOperationActor()
 *
 * Trouve l'acteur réel d'une operation si celui-ci est un acteur générique.
 * ALGO: SI ACTEUR_OPERATION == GENERIQUE ALORS:
 * 		 	SI ACTEUR_OPERATION == ACTEUR_DEPART_CHAINE:
 * 				ACTEUR_OPERATION_ACTIVEE = ACTEUR_DEPART_CHAINE_ACTIVEE
 * 		 	SINON SI ACTEUR_OPERATION == ACTEUR_ARRIVEE_CHAINE:
 * 				ACTEUR_OPERATION_ACTIVEE = ACTEUR_ARRIVEE_CHAINE_ACTIVEE
 * 		 	SINON:
 * 		 		RETOURNE ERREUR "Impossible de déterminer l'acteur"
 * 		 SINON:
 * 		 	ACTEUR_OPERATION_ACTIVEE = ACTEUR_OPERATION
 * 		 RETOURNE ACTEUR_OPERATION_ACTIVEE
 *
 * @access public
 * @return void
 */
function guessOperationActor($operation, $command)
{
    require_once('Objects/Operation.const.php');
    $actor = $operation->getActor();
    $operationModel = $operation->getOperation();
    /**
     * Cas particulier pour les opérations de vol:
     * SI ACTEUR GENERIQUE:
     * On prend soit l'instructeur défini dans la commande, soit le client si
     * c'est un vol en solo, ou on renvoie une erreur.
     * SINON:
     * On prend soit l'instructeur défini dans la commande, soit le client si
     * c'est un vol en solo, sinon on laisse tel quel l'acteur défini dans l'
     * opération.
     **/
    if ($operation->getOperationId() == OPERATION_VOL) {
        $instructor = $command->getInstructor();
        if ($instructor instanceof AeroInstructor) {
            return $instructor;
        }
        $customer = $command->getCustomer();
        if ($customer instanceof AeroCustomer) {
            return $customer;
        }
        if (!($actor instanceof Actor) || $actor->isGeneric()) {
            return new Exception(
                sprintf(_("Actor assigned to operation \"%s\" not found"),
                    $operationModel->getName())
            );
        }
        return $actor;
    }
    // cas particulier: si operation de type consulting, il faut remplacer 
    // l'acteur générique "chef de projet" de l'opération
    if ($operationModel->getType() == Operation::OPERATION_TYPE_CONS) {
        $pm = $command->getProjectManager();
        if ($pm instanceof Actor && $actor->getId() == $pm->getGenericActorID()) {
            return $pm;
        } 
    }
    // Autres cas
	if (!($actor instanceof Actor)) {
            return new Exception(
                sprintf(_("No actor is assigned to operation \"%s\""),
                    $operationModel->getName())
            );
	}
    if ($actor->isGeneric()) {
        $chn = $operation->getChain();
        $ast = $chn->getSiteTransition();
        if ($actor->getId() == $ast->getDepartureActorID()) {
            return $command->getExpeditor();
        } else if ($actor->getId() == $ast->getArrivalActorID()) {
            return $command->getDestinator();
        } else {
            return new Exception(
                sprintf(_("Actor assigned to operation \"%s\" not found"),
                    $operationModel->getName())
            );
        }
    } else {
        return $actor;
    }
}

?>
