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
require_once('Objects/WorkOrder.php');
require_once('Objects/Command.const.php');
$Auth = Auth::Singleton();
$ProfileId = $Auth->getProfile();
$Auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ACTOR, UserAccount::PROFILE_SUPERVISOR, 
		  UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_TRANSPORTEUR));

SearchTools::ProlongDataInSession();  // prolonge les datas en session

if (isset($_REQUEST['WkoCloId']) && is_array($_REQUEST['WkoCloId'])) {
	$WorkOrder = Object::load('WorkOrder', $_REQUEST['WkoCloId'][0]);
	if (Tools::isEmptyObject($WorkOrder)) {
	    Template::errorDialog(E_MSG_TRY_AGAIN, 'WorkOrderList.php');
		exit;
	}
	if ((in_array($ProfileId, array(UserAccount::PROFILE_ACTOR, UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_TRANSPORTEUR))) 
		&& ($WorkOrder->getActorId() != $Auth->getActorId())) {
	    Template::errorDialog(_("You are not allowed to close this work order."), 
                      'WorkOrderList.php');
		exit;
	}

	if (!isset($_REQUEST['confirm'])) {
		// $from sert uniquement a filtrer WorkOrderList...
		$from = isset($_REQUEST['from'])?'&from=' . $_REQUEST['from']:'';
	    $okLink = $_SERVER['PHP_SELF'].'?confirm=OK&WkoCloId[]='.$_REQUEST['WkoCloId'][0].$from;
		foreach($_REQUEST['acoId'] as $acoId) {
			$okLink .= '&acoId[]='.$acoId;
		}
		$cancelLink = 'WorkOrderOpeTaskList.php?OtId=' . $_REQUEST['OtId'] 
					. '&returnURL=' . $_REQUEST['returnURL'] . $from;
		Template::confirmDialog(_("Are you sure you want to close selected work order ?"), 
                          $okLink, $cancelLink);
	    Exit;
	}
	
	Database::connection()->startTrans();  // demarrage transaction
	// si pas deja cloture
	if (!Tools::isEmptyObject($WorkOrder) && WorkOrder::WORK_ORDER_STATE_FULL != $WorkOrder->getState()) {  
		$WorkOrder->SetState(WorkOrder::WORK_ORDER_STATE_FULL);
		$WorkOrder->SetClotureDate(date('Y-m-d H:i:s'));
        saveInstance($WorkOrder, 'WorkOrderList.php');
	
		/* Desaffectation des ActivatedChainoperation non selectionnees dans le grid  */
		$ACOCollection = $WorkOrder->getActivatedChainOperationCollection();
		if (!Tools::isEmptyObject($ACOCollection)) {
			$ACOArrayIdToUnassign = array();  // Les Id de aco qui seront a desaffecter de l'OT
			$CommandNoArray = array();		  // Les No des commandes impactees
			for($i = 0; $i < $ACOCollection->getCount(); $i++){
		    	$ActivatedChainOperation = $ACOCollection->getItem($i);
				$acoId = $ActivatedChainOperation->getId();
				$CommandId = Tools::getValueFromMacro($ActivatedChainOperation, 
                    '%ActivatedChain.CommandItem()[0].Command.Id%');
				$Command = Object::load('Command', $CommandId);
				$CommandNo = $Command->getCommandNo();
				// si elle a ete cochee et si la Command associee n'est pas bloquee, 
				// on ne la desaffecte pas, et on affecte aco.RealActor
				if (in_array($acoId, $_REQUEST['acoId']) && $Command->getState() != Command::BLOCAGE_CDE) { 
                    $ActivatedChainOperation->setRealActor($Auth->getActor());
                    saveInstance($ActivatedChainOperation, 'WorkOrderList.php');
					if (!in_array($CommandNo, $CommandNoArray)) {
					    $CommandNoArray[] = $CommandNo;
					}
					continue;
				}
				$ACOArrayIdToUnassign[] = $acoId;
				unset($ActivatedChainOperation);
			}
			if (count($ACOArrayIdToUnassign) > 0) {
			    require_once('WorkOrderAssign.php');
				WorkOrderAssign($_REQUEST['WkoCloId'][0], $ACOArrayIdToUnassign, 
								HANDLER_OPERATION, false);
			}
		}
		unset($ACOCollection);
		
		/* Dupplication des ActivatedChainoperation dont le Mvt associe n'est pas termine  */
		$ACOCollection = $WorkOrder->getOperationCollectionToDupplicate();
		if (!Tools::isEmptyObject($ACOCollection)) {
 		    for($i = 0; $i < $ACOCollection->getCount(); $i++){
		    	$ActivatedChainOperation = $ACOCollection->getItem($i);
				$NewActivatedChainOperation = $ActivatedChainOperation->Dupplicate();
			}
		}
		$CommandCollection = $WorkOrder->getCommandCollection();
		if (!Tools::isEmptyObject($CommandCollection)) {
			
		    for($j = 0; $j < $CommandCollection->getCount(); $j++){
	    		$Command = $CommandCollection->getItem($j);
				// Une Command bloquue le reste
				if ($Command->getState() == Command::BLOCAGE_CDE) {
				    continue;
				}
				// Met a jour l'etat de la commande en fonction de l'etat de tous 
				$Command->UpdateStateWhenFinishWorkOrder();  // ses ActivatedMovemt...
                saveInstance($Command, 'WorkOrderList.php');
				unset($Command);
			}
		}
	}

    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(_("An error occurred: work order state could not be updated."), 
                       'WorkOrderList.php');
		exit;
    }
	Database::connection()->completeTrans();
	
	$ConfirmMsge = _("Work order has been terminated."); 
	$htmlListCommandNo = (count($CommandNoArray)>0)?
                _('Concerned orders are:') . '<ul>':"";
	for ($i=0; $i<count($CommandNoArray);$i++) {
		$htmlListCommandNo .= '<li>' . $CommandNoArray[$i] . '</li>';
	}
	$ConfirmMsge = (count($CommandNoArray)>0)?
                $ConfirmMsge.$htmlListCommandNo.'</ul>':$ConfirmMsge;
	// $from sert uniquement a filtrer WorkOrderList...
	$from = isset($_REQUEST['from'])?'?from=' . $_REQUEST['from']:'';
	Template::errorDialog($ConfirmMsge, 'WorkOrderList.php' . $from);
	exit;
}  // cloture OK

else {
	Template::errorDialog(E_MSG_TRY_AGAIN, 'WorkOrderList.php');
	exit;
}				   
						   
?>
