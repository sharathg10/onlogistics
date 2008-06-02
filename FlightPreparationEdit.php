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
require_once('Objects/ActivatedChainTaskDetail.const.php');
require_once('Objects/Task.inc.php');
require_once('Objects/ActivatedChainTask.php');
require_once('FormatNumber.php');
$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_OPERATOR,
						   UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_AERO_CUSTOMER));

$PotentialErrorBody = _('This flight cannot be validated because a potential has reached an incorrect value: SN ');
$returnURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'ActivatedChainTaskList.php';
$url = $_SERVER['PHP_SELF'].'?ackId=' . $_REQUEST['ackId'];
//Database::connection()->debug = true;

SearchTools::prolongDataInSession();  // pour conserver les criteres de recherche dans ACKList

// Test sur l'Id de l'ActivatedChainTask
if (!isset($_REQUEST['ackId'])) {
	Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
	exit;
}
$ActivatedChainTask = Object::load('ActivatedChainTask', $_REQUEST['ackId']);


if (Tools::isEmptyObject($ActivatedChainTask) || ('PREPARATION VOL' != $ActivatedChainTask->getName())) {
    Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
  	exit;
}
// Si deja valide, on est la pour consultation ou modif de la 1ere validation
if ($ActivatedChainTask->getState() == ActivatedChainTask::STATE_FINISHED) {
    $returnURL = 'ActivatedChainTaskHistory.php';
}

$ActivatedChainOperation = $ActivatedChainTask->getActivatedOperation();
$CommandId = Tools::getValueFromMacro($ActivatedChainTask,
							   '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Id%');
$Command = Object::load('Command', $CommandId);
$duration = DateTimeTools::getHundredthsOfHour($Command->getDuration());

//  Si on a modifie l'appareil du vol, on doit confirmer
if (!isset($_REQUEST['ok']) && isset($_REQUEST['newConcretePdt'])
        && $_REQUEST['newConcretePdt'] == 1) {
	// Controle si on veut changer l'appareil prevu: est-il disponible?
	$start = DateTimeTools::MySQLDateToTimeStamp($Command->getWishedStartDate())
												 - $Command->_getTolerance();
    $end = DateTimeTools::MySQLDateToTimeStamp($Command->getWishedEndDate());

	$AeroConcreteProduct = Object::load('AeroConcreteProduct', $_REQUEST['RealConcreteProduct']);
	if (!$AeroConcreteProduct->isAvailableFor($start, $end)) {
		Template::errorDialog(_('Selected airplane will not be available, please change it.'),
					   $url . '&edit=1');
        exit;
    }
	// S'il est disponible, on demande confirmation
	Template::confirmDialog(_('are you sure you want to substitute the airplane ?'),
					  $url . '&RealConcreteProduct='
					  	   . $_REQUEST['RealConcreteProduct'].'&ok=1'.'&edit=1',
					  $url . '&edit=1');
	exit;
}


//  Si on a clique sur OK apres saisie des donnees de l'ActivatedChainTask(Detail)
if ((isset($_REQUEST['formSubmitted']) && $_REQUEST['formSubmitted'] == 1)) {
	Database::connection()->startTrans();

	// Controle de la charge de l'appareil
	$AeroConcreteProduct = Object::load('AeroConcreteProduct',
													 $_REQUEST['RealConcreteProduct']);
	$totalWeight = $AeroConcreteProduct->getWeight()
				 + $Command->getPassengerWeight()
				 + convertVolume($_POST['ActivatedChainTaskDetail_CarburantTotal'],
						  		 AeroConcreteProduct::LITRE, AeroConcreteProduct::KILOGRAMME);
	if ($totalWeight > $AeroConcreteProduct->getMaxWeightOnTakeOff()) {
	    Template::errorDialog(E_MAXWEIGHT_OVER, $url . '&edit=1');
        exit;
	}

	// Si deja valide
	if ($ActivatedChainTask->getState() == ActivatedChainTask::STATE_FINISHED) {
		$ActivatedChainTaskDetail = $ActivatedChainTask->getActivatedChainTaskDetail();
		// MAJ des potentiels virtuels si chgt d'appareil
		if ($_REQUEST['RealConcreteProduct'] != $ActivatedChainOperation->getConcreteProductId()) {
		    // Appareil initialement prevu (aussi pour les AeroConcreteProduct composant
			$initCP = Object::load(
					'ConcreteProduct',
					$ActivatedChainOperation->getRealConcreteProductId());
			$updated = $initCP->updatePotentials(
					array(0 => array('attributes' => array('VirtualHourSinceNew', 'VirtualHourSinceOverall'),
									 'value' => -$duration
						)
					));
			if (true !== $updated) {
			    Template::errorDialog($PotentialErrorBody . $updated . '.', $url . '&edit=1');
		   		Exit;
			}
			// Appareil desormais prevu
			$newCP = Object::load('ConcreteProduct',
												$_REQUEST['RealConcreteProduct']);
			$updated = $newCP->updatePotentials(
					array(0 => array('attributes' => array('VirtualHourSinceNew', 'VirtualHourSinceOverall'),
									 'value' => $duration
						)
					));  // Pas besoin de controle ici, car on ajoute
		}
	}
	else {  // 1ere validation
		$ActivatedChainTaskDetail = Object::load('ActivatedChainTaskDetail');
		$ActivatedChainTask->setBegin(date('Y-m-d H-i-s'));
		$ActivatedChainTask->setEnd(date('Y-m-d H-i-s'));
		// MAJ des potentiels virtuels si chgt d'appareil
		if ($_REQUEST['RealConcreteProduct'] != $ActivatedChainOperation->getConcreteProductId()) {
		    // Appareil initialement prevu
			$initCP = Object::load(
					'ConcreteProduct',
					$ActivatedChainOperation->getConcreteProductId());
			$updated = $initCP->updatePotentials(
					array(0 => array('attributes' => array('VirtualHourSinceNew', 'VirtualHourSinceOverall'),
									 'value' => -$duration
						)
					));
			if (true !== $updated) {
			    Template::errorDialog($PotentialErrorBody . $updated . '.', $url . '&edit=1');
		   		Exit;
			}
			// Appareil desormais prevu
			$newCP = Object::load('ConcreteProduct',
												$_REQUEST['RealConcreteProduct']);
			$updated = $newCP->updatePotentials(
					array(0 => array('attributes' => array('VirtualHourSinceNew', 'VirtualHourSinceOverall'),
									 'value' => $duration
						)
					));  // Pas besoin de controle ici, car on ajoute
		}
	}

	// Conversion en litres des volumes saisis: on recupere la conversion effectuee en js
	$_POST['ActivatedChainTaskDetail_CarburantRest'] = $_POST['ConvertedCarburantRest'];
	$_POST['ActivatedChainTaskDetail_CarburantAdded'] = $_POST['ConvertedCarburantAdded'];
	// ActivatedChainTaskDetail_CarburantTotal est deja en litres
	// mais il faut remplacer la ',' par un '.'
	$_POST['ActivatedChainTaskDetail_CarburantTotal'] =
			troncature($_POST['ActivatedChainTaskDetail_CarburantTotal']);

	FormTools::autoHandlePostData($_POST, $ActivatedChainTaskDetail);
    saveInstance($ActivatedChainTaskDetail, $returnURL);
	$ActivatedChainTask->setActivatedChainTaskDetail($ActivatedChainTaskDetail);
	$ActivatedChainTask->setState(ActivatedChainTask::STATE_FINISHED);
	$ActivatedChainTask->setValidationUser($auth->getUserId());
	$ActivatedChainOperation->setRealConcreteProduct($_REQUEST['RealConcreteProduct']);
	$ActivatedChainOperation->setRealActor($auth->getActorId());
    saveInstance($ActivatedChainOperation, $returnURL);
	// M A J du vol associe: l'ACO suivant (Order + 1) dans l'ActivatedChain
	$AssociatedFlight = $ActivatedChainOperation->getNextOperation();
	if (!Tools::isEmptyObject($AssociatedFlight) && ('VOL' == $AssociatedFlight->getName())) {
	    $AssociatedFlight->setConcreteProduct($_REQUEST['RealConcreteProduct']);
        saveInstance($AssociatedFlight, $returnURL);
	}
    saveInstance($ActivatedChainTask, $returnURL);

	// Commit de la transaction, si elle a réussi
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
        Exit;
    }
	Database::connection()->completeTrans();

	Tools::redirectTo($returnURL);
	exit;
}


/*  Formulaire */
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

$smarty->assign('CommandNo', $Command->GetCommandNo());

if (isset($_REQUEST['RealConcreteProduct'])) {
	$AeroCPdtId = $_REQUEST['RealConcreteProduct'];
}
elseif ($ActivatedChainTask->getState() == ActivatedChainTask::STATE_FINISHED) {
	$AeroCPdtId = Tools::getValueFromMacro($ActivatedChainTask, '%ActivatedOperation.RealConcreteProduct.Id%');
}
else {
	$AeroCPdtId = Tools::getValueFromMacro($ActivatedChainTask,
            '%ActivatedOperation.ConcreteProduct.Id%');
}
$AeroConcreteProduct = Object::load('AeroConcreteProduct', $AeroCPdtId);

$smarty->assign('FlyTypeName',
				Tools::getValueFromMacro($AeroConcreteProduct, '%Product.FlyType.Name%'));
$smarty->assign('MaxWeightTakeOff', $AeroConcreteProduct->getmaxWeightOnTakeOff());
$smarty->assign('PassengerWeight', $Command->getPassengerWeight());
$smarty->assign('TankCapacity', $AeroConcreteProduct->getTankCapacity());
$UnitArray = $AeroConcreteProduct->getTankUnitTypeConstArray();//getCarburantUnitArray();
$smarty->assign('TankUnitType', $UnitArray[$AeroConcreteProduct->getTankUnitType()]);

$smarty->assign('Duration', $duration);
$smarty->assign('ConnectedUserIdentity', $auth->getIdentity());

$smarty->assign('VolumeUnityList', join("\n\t\t", //getCarburantAsOptions()));
    FormTools::writeOptionsFromArray($UnitArray)));


require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('ActivatedChainTaskEdit', 'post', $_SERVER['PHP_SELF']);

$form->addElement('hidden', 'newConcretePdt', '0');
$form->addElement('hidden', 'ackId', $_REQUEST['ackId']);
$form->addElement('hidden', 'TankCapacity', $AeroConcreteProduct->getTankCapacity());
$form->addElement('hidden', 'TankUnitType', $AeroConcreteProduct->getTankUnitType());
$form->addElement('hidden', 'ConvertedCarburantRest');
$form->addElement('hidden', 'ConvertedCarburantAdded');

$FilterComponentArray[] = SearchTools::NewFilterComponent('Product.FlyType', '', 'NotEquals', 0, 1);
$FilterComponentArray[] = SearchTools::NewFilterComponent('State', '', 'Equals', 0, 1);
$filter = SearchTools::FilterAssembler($FilterComponentArray);
$AeroConcreteProductArray = SearchTools::CreateArrayIDFromCollection('AeroConcreteProduct', $filter);
$form->addElement('select', 'RealConcreteProduct', _('Airplane matriculation'),
                  $AeroConcreteProductArray,
				  'onChange="document.ActivatedChainTaskEdit.newConcretePdt.value=1;
				   ActivatedChainTaskEdit.submit()"');
$form->addElement('text', 'ActivatedChainTaskDetail_CarburantRest', _('Remaining quantity'),
				  'onKeyUp="RecalculateCarburantTotal();"');
$form->addElement('text', 'ActivatedChainTaskDetail_CarburantAdded', _('Added quantity'),
				  'onKeyUp="RecalculateCarburantTotal();"');
$form->addElement('text', 'ActivatedChainTaskDetail_CarburantTotal', _('Total quantity'), 'readonly');
$form->addElement('text', 'ActivatedChainTaskDetail_OilAdded', _('Oil: added quantity (L)'));
$form->addElement('textarea', 'ActivatedChainTaskDetail_Comment',
				  _('Comment'), array('rows' => 4, 'cols' => 80));
$form->addElement('select', 'CarburantRestUnitType', '',
                   $AeroConcreteProduct->getTankUnitTypeConstArray(),
				  'onChange="RecalculateCarburantTotal();"');
$form->addElement('select', 'CarburantAddedUnitType', '',
                    $AeroConcreteProduct->getTankUnitTypeConstArray(),
				  'onChange="RecalculateCarburantTotal();"');
$form->addElement('button', 'Cancel', A_CANCEL,
        'OnClick="javascript:window.location=\''.$returnURL.'\'"');

$defaultConcreteProduct = (isset($_REQUEST['RealConcreteProduct']))?
				$_REQUEST['RealConcreteProduct']:$AeroConcreteProduct->getId();
$defaultValues = array('RealConcreteProduct' => $defaultConcreteProduct);

$ActivatedChainTaskDetail = $ActivatedChainTask->getActivatedChainTaskDetail();

// Si deja valide
if ($ActivatedChainTask->getState() == ActivatedChainTask::STATE_FINISHED) {
	foreach($ActivatedChainTaskDetail->getProperties() as $name => $class) {
	    $getter = 'get' . $name;
	    $val = $ActivatedChainTaskDetail->$getter();
		$defaultValues['ActivatedChainTaskDetail_' . $name] = $ActivatedChainTaskDetail->$getter();
	}
	// En base, on ne stocke que des litres, qque soit l'unite choisie
	$defaultValues['CarburantRestUnitType'] = AeroConcreteProduct::LITRE;
	$defaultValues['CarburantAddedUnitType'] = AeroConcreteProduct::LITRE;
	$defaultValues['ConvertedCarburantRest'] = $ActivatedChainTaskDetail->getCarburantRest();
	$defaultValues['ConvertedCarburantAdded'] = $ActivatedChainTaskDetail->getCarburantAdded();
	$defaultValues['RealConcreteProduct'] = (isset($_REQUEST['RealConcreteProduct']))?
						$_REQUEST['RealConcreteProduct']:
						Tools::getValueFromMacro($ActivatedChainTask,
                                '%ActivatedOperation.RealConcreteProduct.Id%');
}
$form->setDefaults($defaultValues);  // affiche le form avec les valeurs par defaut

// Si deja valide
if ($ActivatedChainTask->getState() == ActivatedChainTask::STATE_FINISHED) {
	if (!isset($_REQUEST['edit'])) {
	    $form->freeze();
		$form->addElement('button', 'update', A_UPDATE,
					  	  'OnClick="javascript:window.location=\'' . $url . '&edit=1\'"');
	}
    else {
		$form->addElement('submit', 'submitForm', A_VALIDATE);
	}
}
else {
	$form->addElement('submit', 'submitForm', A_VALIDATE);
}
$form->accept($renderer); // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$pageContent = $smarty->fetch('ActivatedChainTask/FlightPreparationEdit.html');
Template::page(_('Flight preparation validation'), $pageContent,
		array('js/lib-functions/FormatNumber.js', 'js/includes/FlightPreparationEdit.js'));

?>
