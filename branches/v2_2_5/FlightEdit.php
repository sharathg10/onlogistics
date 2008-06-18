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
//require_once('Objects/ConcreteProduct.const.php');
require_once('Objects/Task.inc.php');
require_once('Objects/ActivatedChainTask.php');
require_once('Objects/Command.const.php');
require_once('Objects/ActivatedChain.php');
require_once ('Objects/ActivatedChainTaskDetail.php');
require_once ('Objects/AeroConcreteProduct.php');
require_once('Objects/ActivatedChainTaskDetail.const.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_OPERATOR,
						   UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_AERO_CUSTOMER));

$pageTitle = _('Flight data recording');
$PotentialErrorBody = _('This flight cannot be validated because a potential has reached an incorrect value: SN ');
$returnURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'ActivatedChainTaskList.php';
//Database::connection()->debug = true;

// Pour conserver les criteres de recherche dans ACKList
SearchTools::ProlongDataInSession();

// Test sur l'Id de l'ActivatedChainTask
if (!isset($_REQUEST['ackId'])) {
	Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
	exit;
}
$ActivatedChainTask = Object::load('ActivatedChainTask', $_REQUEST['ackId']);


// Impossible de valider la tâche de vol si la tâche précédente de préparation
// qui a été activée ( s'il y en a une) n'a pas été validée.
$FlightPreparationTask = $ActivatedChainTask->getPreviousTask(
        array(), 'isFlightPreparationTask');
if (!Tools::isEmptyObject($FlightPreparationTask)
        && $FlightPreparationTask->getState() != ActivatedChainTask::STATE_FINISHED) {
    Template::errorDialog(_('Flight preparation was not validated.'), $returnURL);
   	exit;
}

// Si deja valide, on est la pour consultation ou modif de la 1ere validation
if ($ActivatedChainTask->getState() == ActivatedChainTask::STATE_FINISHED) {
    $returnURL = 'ActivatedChainTaskHistory.php';
}
if (Tools::isEmptyObject($ActivatedChainTask) || ('VOL' != $ActivatedChainTask->getName())) {
	Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
   	exit;
}

//  Si on a modifie l'appareil du vol, on doit confirmer, en cas de 1ere validation
elseif (!isset($_REQUEST['ok']) && isset($_REQUEST['newConcretePdt'])
        && $_REQUEST['newConcretePdt'] == 1) {
	Template::confirmDialog(_('are you sure you want to substitute the airplane ?'),
					  $_SERVER['PHP_SELF'].'?ackId='.$_REQUEST['ackId'].'&RealConcreteProduct='
					  .$_REQUEST['RealConcreteProduct'].'&RealActor='
					  .$_REQUEST['RealActor'].'&ok=1'.'&edit=1',
					  $_SERVER['PHP_SELF'].'?ackId='.$_REQUEST['ackId']
					  .'&RealActor='.$_REQUEST['RealActor'].'&edit=1');
	exit;
}
$CommandId = Tools::getValueFromMacro($ActivatedChainTask,
        '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Id%');
$Command = Object::load('CourseCommand', $CommandId);
$readonly = ($Command->hasBeenFactured())?'readonly':'';
$Customer = $Command->getCustomer();

//  Si on a clique sur OK apres saisie des donnees de l'ActivatedChainTask(Detail)
if ((isset($_REQUEST['formSubmitted']) && $_REQUEST['formSubmitted'] == 1)) {
	Database::connection()->startTrans();

	// Si deja valide
	if ($ActivatedChainTask->getState() == ActivatedChainTask::STATE_FINISHED) {
		$ActivatedChainTaskDetail = $ActivatedChainTask->getActivatedChainTaskDetail();
		// Copie de l'objet!
		$initActivatedChainTaskDetail = clone $ActivatedChainTaskDetail;
		// On annule l'impact de la validation precedente sur les heures de vol
		$initActivatedChainTaskDetail->updateActorsHours($Command, $_REQUEST['RealActor'], -1);
	}
	else { // 1ere validation
		$ActivatedChainTaskDetail = Object::load('ActivatedChainTaskDetail');
		$ActivatedChainTask->setBegin(date('Y-m-d H-i-s'));
		$ActivatedChainTask->setEnd(date('Y-m-d H-i-s'));
		$Command->setState(Command::LIV_COMPLETE);
        saveInstance($Command, $returnURL);
	}

	FormTools::autoHandlePostData($_POST, $ActivatedChainTaskDetail);

	// Si non renseigne, RealCommercialDuration prend la valeur de TechnicalHour
	if (empty($_POST['ActivatedChainTaskDetail_RealCommercialDuration'])) {
	    $ActivatedChainTaskDetail->setRealCommercialDuration(
                $ActivatedChainTaskDetail->getTechnicalHour());
	}
	$ActivatedChainTaskDetail->setEngineOn(DateTimeTools::QuickFormDateToMySQL('Date_EngineOn'));
	$ActivatedChainTaskDetail->setEngineOff(DateTimeTools::QuickFormDateToMySQL('Date_EngineOff'));
	$ActivatedChainTaskDetail->setTakeOff(DateTimeTools::QuickFormDateToMySQL('Date_TakeOff'));
	$ActivatedChainTaskDetail->setLanding(DateTimeTools::QuickFormDateToMySQL('Date_Landing'));

	// Selon si c'est un vol bimoteur ou non, on ne met pas a jour les memes chps
	$ActivatedChainTaskDetail->updateHours($_REQUEST['RealActor']);
    saveInstance($ActivatedChainTaskDetail, $returnURL);
	$ActivatedChainTask->setActivatedChainTaskDetail($ActivatedChainTaskDetail);
	$ActivatedChainTask->setValidationUser($auth->getUserId());
	$ActivatedChainOperation = $ActivatedChainTask->getActivatedOperation();

	// Si deja valide
	if ($ActivatedChainTask->getState() == ActivatedChainTask::STATE_FINISHED) { // MAJ des potentiels et autres...
		$initConcretePdt = $ActivatedChainOperation->getRealConcreteProduct();
		$AeroConcretePdt = Object::load('AeroConcreteProduct', $_REQUEST['RealConcreteProduct']);
		// MAJ des potentiels reels et virtuels
		$updated = $ActivatedChainTask->reUpdateRealConcreteProduct(
                $initConcretePdt, $AeroConcretePdt, $initActivatedChainTaskDetail);
		if (true !== $updated) {
		    Template::errorDialog($PotentialErrorBody . $updated . '.', $returnURL);
	   		Exit;
		}
		$ActivatedChainTaskDetail->updateActorsHours($Command, $_REQUEST['RealActor']);
	}
	else { // lors d'une 1ere validation
		// La validation impacte sur les heures de vol: doit etre fait avant facturation eventuelle

		$ActivatedChainTaskDetail->updateActorsHours($Command, $_REQUEST['RealActor']);
        // necessaire ici
		$ActivatedChainOperation->setRealConcreteProduct($_REQUEST['RealConcreteProduct']);
		// MAJ des potentiels reele et virtuels
		$updated = $ActivatedChainTask->UpdateRealConcreteProduct();
		if (true !== $updated) {
		    Template::errorDialog($PotentialErrorBody . $updated . '.', $returnURL);
	   		Exit;
		}

		// On facture si l'ACK de facturation est en automatique
		require_once('Objects/ActivatedChainTask.php');
		$nextActdChTask = $ActivatedChainTask->getNextTask();
		$invoicingTask = $ActivatedChainTask->getNextTask(
                array(ActivatedChainTask::TRIGGERMODE_AUTO), 'isInvoicingTask');

		// Si la tache de facturation associee, ou la tache
		// suivant le vol est en mode automatique, on facture
		if ($nextActdChTask->getTriggerMode() == ActivatedChainTask::TRIGGERMODE_AUTO
                || !Tools::isEmptyObject($invoicingTask)) {
		    $Alert = $ActivatedChainTask->flightInvoice();
			$Command->setState(Command::FACT_COMPLETE);
            saveInstance($Command, $returnURL);
		}
	}
	$ActivatedChainOperation->setRealConcreteProduct($_REQUEST['RealConcreteProduct']);
	$ActivatedChainOperation->setRealActor($_REQUEST['RealActor']);
	$ActivatedChainOperation->setState(ActivatedChainTask::STATE_FINISHED);
    saveInstance($ActivatedChainOperation, $returnURL);
	$ActivatedChainTask->setState(ActivatedChainTask::STATE_FINISHED);
    saveInstance($ActivatedChainTask, $returnURL);

	// Commit de la transaction, si elle a réussi
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
        Exit;
    }
	Database::connection()->completeTrans();

	// Seulement apres la transaction, on envoi l'alerte si necessaire
	if (!Tools::isEmptyObject($Alert)) {
	    $Alert->send();
	}
	Tools::redirectTo($returnURL);
	exit;
}


/*  Formulaire */
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

$smarty->assign('CommandNo', $Command->getCommandNo());
$smarty->assign('FlyTypeName',
				Tools::getValueFromMacro($ActivatedChainTask,
                        '%ActivatedOperation.ConcreteProduct.Product.FlyType.Name%'));
$smarty->assign('Immatriculation',
				Tools::getValueFromMacro($ActivatedChainTask,
                        '%ActivatedOperation.ConcreteProduct.Immatriculation%'));
$smarty->assign('Actor', Tools::getValueFromMacro($ActivatedChainTask,
        '%ActivatedOperation.Actor.Name%'));
$smarty->assign('Customer', $Customer->getName());

$Duration = DateTimeTools::getHundredthsOfHour($Command->getDuration());
$smarty->assign('Duration', $Duration);
$smarty->assign('ConnectedUserIdentity', $auth->getIdentity());

$smarty->assign('VolumeUnityList', join("\n\t\t", //getCarburantAsOptions()));
        FormTools::writeOptionsFromArray(
            AeroConcreteProduct::getTankUnitTypeConstArray())));

require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('ActivatedChainTaskEdit', 'post', $_SERVER['PHP_SELF']);
$form->updateAttributes(array('onSubmit' => 'return validation();'));

$form->addElement('hidden', 'newConcretePdt', '0');
$form->addElement('hidden', 'newActor', '0');
$form->addElement('hidden', 'ackId', $_REQUEST['ackId']);
$AeroInstructorArray = SearchTools::createArrayIDFromCollection(
        'AeroInstructor', array(), _('No instructor'));
$form->addElement('select', 'RealActor', _('Pilot') . ' 2', $AeroInstructorArray,
		'onChange="selectNature();document.ActivatedChainTaskEdit.newActor.value=1;"');

$FilterComponentArray[] = SearchTools::NewFilterComponent(
        'Product.FlyType', '', 'NotEquals', 0, 1);
$FilterComponentArray[] = SearchTools::NewFilterComponent(
        'State', '', 'Equals', ConcreteProduct::EN_MARCHE, 1);
$filter = SearchTools::FilterAssembler($FilterComponentArray);
$AeroConcreteProductArray = SearchTools::createArrayIDFromCollection(
        'AeroConcreteProduct', $filter);
$form->addElement('select', 'RealConcreteProduct', _('Airplane matriculation'),
                  $AeroConcreteProductArray,
				  'onChange="document.ActivatedChainTaskEdit.newConcretePdt.value=1;
				  ActivatedChainTaskEdit.submit();"');
$form->addElement('select', 'ActivatedChainTaskDetail_CustomerSeat',
				  _('On board role'),
				  ActivatedChainTaskDetail::getCustomerSeatConstArray(),
                  'onchange="selectNature();"');
$form->addElement('select', 'ActivatedChainTaskDetail_InstructorSeat',
				  _('On board role'),
				  ActivatedChainTaskDetail::getInstructorSeatConstArray(),
                  'onchange="selectNature();"');
$form->addElement('text', 'ActivatedChainTaskDetail_CycleEngine1N1', _('N1/NG cycle'));
$form->addElement('text', 'ActivatedChainTaskDetail_CycleEngine2N1', _('N1/NG cycle'));
$form->addElement('text', 'ActivatedChainTaskDetail_CycleEngine1N2', _('N2/NTL cycle'));
$form->addElement('text', 'ActivatedChainTaskDetail_CycleEngine2N2', _('N2/NTL cycle'));
//$form->addElement('text', 'ActivatedChainTaskDetail_CycleEngine1', _('Cycle'));
//$form->addElement('text', 'ActivatedChainTaskDetail_CycleEngine2', _('Cycle'));
$form->addElement('text', 'ActivatedChainTaskDetail_CycleCellule', _('Unit cycle'));
$form->addElement('text', 'ActivatedChainTaskDetail_CycleTreuillage', _('Number of winchings'));
$form->addElement('text', 'ActivatedChainTaskDetail_CycleCharge', _('Number of loads'));
$form->addElement('text', 'ActivatedChainTaskDetail_RealCommercialDuration',
        _('Commercial hours'), $readonly);
$form->addElement('select', 'ActivatedChainTaskDetail_Nature', _('Flight purpose'),
				  ActivatedChainTaskDetail::getNatureConstArray(), 'onchange="selectNature();"');
$form->addElement('text', 'ActivatedChainTaskDetail_TakeOffNumber', _('Number of startups'));
$form->addElement('text', 'ActivatedChainTaskDetail_LandingNumber', _('Number of landings'));
$form->addElement('date', 'Date_EngineOn', _('Engine startup'),
				  array('format'    => 'd/m/Y H:i',
					    'minYear'   => date('Y')-1,
					    'maxYear'   => date('Y')));
$form->addElement('date', 'Date_EngineOff', _('Engine shutdown'),
				  array('format'    => 'd/m/Y H:i',
					    'minYear'   => date('Y')-1,
					    'maxYear'   => date('Y')));
$form->addElement('date', 'Date_TakeOff', _('Lift-off'),
				  array('format'    => 'd/m/Y H:i',
					    'minYear'   => date('Y')-1,
					    'maxYear'   => date('Y')));
$form->addElement('date', 'Date_Landing', _('Landing'),
				  array('format'    => 'd/m/Y H:i',
					    'minYear'   => date('Y')-1,
					    'maxYear'   => date('Y')));

$form->addElement('text', 'ActivatedChainTaskDetail_TechnicalHour',
        _('Actual flight operating duration'));
$form->addElement('text', 'PilotHours', _('Daily hours'));
$form->addElement('text', 'PilotHoursNight', _('Nightly hours'));
$form->addElement('text', 'ActivatedChainTaskDetail_PilotHoursIFR', _('IFR hours'));
$form->addElement('text', 'ActivatedChainTaskDetail_IFRLanding', _('IFR landings'));

$form->addElement('text', 'ActivatedChainTaskDetail_CelluleHour', _('Cell'));
$form->addElement('text', 'ActivatedChainTaskDetail_PublicHours', _('Air transport hours'));
$form->addElement('text', 'ActivatedChainTaskDetail_VLAEHours', _('VLAE hours'));
$form->addElement('text', 'ActivatedChainTaskDetail_CarburantUsed', _('Consumed fuel'));
$form->addElement('textarea', 'ActivatedChainTaskDetail_Comment', _('Comment'), array('rows' => 4, 'cols' => 80));
$form->addElement('select', 'ActivatedChainTaskDetail_CarburantUsedUnitType',
				  '', AeroConcreteProduct::getTankUnitTypeConstArray());
				  //getCarburantUnitArray());
$form->addElement('button', 'Cancel', A_CANCEL,
        'OnClick="javascript:window.location=\''.$returnURL.'\'"');

$defaultConcreteProduct = (isset($_REQUEST['RealConcreteProduct']))?
        $_REQUEST['RealConcreteProduct']:
				Tools::getValueFromMacro($ActivatedChainTask,
                        '%ActivatedOperation.ConcreteProduct.Id%');
///$defaultActor = ($_REQUEST['NewActor'] == 1)?$_REQUEST['RealActor']:$Command->GetInstructorId();

$defaultBeginValue = DateTimeTools::MySQLToQuickFormDate($ActivatedChainTask->getBegin());
$defaultEndValue = DateTimeTools::MySQLToQuickFormDate($ActivatedChainTask->getEnd());
$defaultValues = array('RealConcreteProduct' => $defaultConcreteProduct,
					   'RealActor' => isset($_REQUEST['RealActor'])?
                            $_REQUEST['RealActor']:$Command->getInstructorId(),
					   'Date_EngineOn' => $defaultBeginValue,
					   'Date_EngineOff' => $defaultEndValue,
					   'Date_TakeOff' => $defaultBeginValue,
					   'Date_Landing' => $defaultEndValue
					   );
$ActivatedChainTaskDetail = $ActivatedChainTask->getActivatedChainTaskDetail();

// Si deja valide
if ($ActivatedChainTask->getState() == ActivatedChainTask::STATE_FINISHED) {
	foreach($ActivatedChainTaskDetail->getProperties() as $name => $class) {
	    $getter = 'get' . $name;
	    $val = $ActivatedChainTaskDetail->$getter();
		$defaultValues['ActivatedChainTaskDetail_' . $name] = $ActivatedChainTaskDetail->$getter();
	}
	$defaultValues['RealConcreteProduct'] = (isset($_REQUEST['RealConcreteProduct']))?
						$_REQUEST['RealConcreteProduct']:
						Tools::getValueFromMacro($ActivatedChainTask,
                                '%ActivatedOperation.RealConcreteProduct.Id%');
	$defaultValues['RealActor'] = Tools::getValueFromMacro($ActivatedChainTask,
            '%ActivatedOperation.realActor.Id%');
	$defaultValues['Date_EngineOn'] = DateTimeTools::MySQLToQuickFormDate($ActivatedChainTaskDetail->getEngineOn());
	$defaultValues['Date_EngineOff'] = DateTimeTools::MySQLToQuickFormDate($ActivatedChainTaskDetail->getEngineOff());
	$defaultValues['Date_TakeOff'] = DateTimeTools::MySQLToQuickFormDate($ActivatedChainTaskDetail->getTakeOff());
	$defaultValues['Date_Landing'] = DateTimeTools::MySQLToQuickFormDate($ActivatedChainTaskDetail->getLanding());

	$defaultValues['PilotHours'] = $ActivatedChainTaskDetail->getPilotHours()
			+ $ActivatedChainTaskDetail->getPilotHoursBiEngine();
	$defaultValues['PilotHoursNight'] = $ActivatedChainTaskDetail->getPilotHoursNight()
			+ $ActivatedChainTaskDetail->getPilotHoursBiEngineNight();
}

$form->setDefaults($defaultValues);  // affiche le form avec les valeurs par defaut

// Si deja valide
if ($ActivatedChainTask->getState() == ActivatedChainTask::STATE_FINISHED) {
	if (!isset($_REQUEST['edit'])) {
	    $form->freeze();
		$form->addElement('button', 'update', A_UPDATE,
					  	  'OnClick="javascript:window.location=\''
                          . $_SERVER['PHP_SELF'].'?ackId='.$_REQUEST['ackId'].'&edit=1\'"');
	}
    else {
		$form->addElement('submit', 'submitForm', A_VALIDATE);
	}
}
else {
	$form->addElement('submit', 'submitForm', A_VALIDATE);
}
$form->accept($renderer); // affecte au form le renderer personnalise

// Por determiner les blocks a afficher ou non selon le Profil du uder connecte
$displayBlock = (in_array($auth->getProfile(), array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_INSTRUCTOR)))?
				'yes':'none';
$smarty->assign('displayBlock', $displayBlock);
$smarty->assign('form', $renderer->toArray());
$pageContent = $smarty->fetch('ActivatedChainTask/FlightEdit.html');
Template::page($pageTitle, $pageContent, array('js/includes/FlightEdit.js'));

?>
