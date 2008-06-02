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
require_once('Objects/SupplierCustomer.php');

$Auth = Auth::Singleton();
$Actor = $Auth->getActor();

SearchTools::ProlongDataInSession();  // prolonge les datas du form de recherche en session

$SupplierCustomerMapper = Mapper::singleton('SupplierCustomer');

//validation du formulaire
if (isset($_POST['submit_x'])) {
	$ActorSupplierCustomer = $ActorMapper->load(array('Id' => $_POST['HiddenactId']));//recupere l'acteur selectionné
	
	/**
	* Ouverture de la transaction
	**/
	Database::connection()->startTrans();
	
	//creation
	if ($_POST['HiddenAction'] == 'Add') {
		if ($_POST['ActorType'] == 1) {//l'acteur selectionné est un client
	   		$Supplier = $Actor;
	   		$Customer = $ActorSupplierCustomer;
		}
		else {//l'acteur selectionné est un fournisseur
			$Supplier = $ActorSupplierCustomer;
			$Customer = $Actor;
		}
		$SupplierCustomer = Object::load('SupplierCustomer');
		$SupplierCustomer->SetMaxIncur($_POST['MaxIncur']);
		$SupplierCustomer->SetSupplier($Supplier);
		$SupplierCustomer->SetCustomer($Customer);
		$SupplierCustomer->SetModality($_POST['Modality']);
		$SupplierCustomer->SetTotalDays($_POST['TotalDays']);
		$SupplierCustomer->SetOption($_POST['Option']);
        saveInstance($SupplierCustomer, 'ActorList.php');
	}
	
	else { //edition
		if ($_POST['HiddenActorSelectedStatus'] == 'Client') {//acteur connecté fournisseur et acteur selectionné client
			$SupplierCustomer = $SupplierCustomerMapper->load(array('Customer' => $ActorSupplierCustomer, 
																				'Supplier' => $Actor));
			$Expeditor = $Actor;
			$Destinator = $ActorSupplierCustomer;
		}
		if ($_POST['HiddenActorSelectedStatus'] == 'Fournisseur') {// acteur connecté client et acteur selectionné fournisseur
			$SupplierCustomer = $SupplierCustomerMapper->load(array('Customer' => $Actor, 
																				'Supplier' => $ActorSupplierCustomer));
			$Expeditor = $ActorSupplierCustomer;
			$Destinator = $Actor;
		}
		
		$SupplierCustomer->SetMaxIncur($_POST['MaxIncur']);
		
		if (!(is_null($SupplierCustomer->GetMaxIncur()))) { //l'encours autorisé est défini
			require_once('ProductCommandTools.php');
			if ($SupplierCustomer->GetMaxIncur() < $SupplierCustomer->GetUpdateIncur()) {  // encours depasse
			    BlockDeblockage($Expeditor, $Destinator);    // Bloque toutes les cdes activees
			}
			else {  // Debloque toutes les cdes bloquees, ds la limite de UpdateIncur - MaxIncur 
				BlockDeblockage($Expeditor, $Destinator, 0);
			}
		}
/* Mis en commentaire car ne semble pas avoir de sens 
 		else {
			require_once("ProductCommandTools.php");
			BlockDeblockage($Expeditor, $Destinator, 0); // Debloque toutes les cdes bloquees
		}*/
		$SupplierCustomer->SetModality($_POST['Modality']);
		$SupplierCustomer->SetTotalDays($_POST['TotalDays']);
		$SupplierCustomer->SetOption($_POST['Option']);
        saveInstance($SupplierCustomer, 'ActorList.php');
	}
		
	/**
	* commit de la transaction 
	**/
	if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
		Database::connection()->rollbackTrans();
		die('erreur sql');
	} 
	Database::connection()->completeTrans();
	/**
	* Redirect vers la liste des acteurs
	**/
	
	Tools::redirectTo('ActorList.php?'.SID);
	exit;
}

if ($_GET['actId'] == "") {
    Template::errorDialog('&nbsp;&nbsp;&nbsp;&nbsp;' . _('No actor selected.'), 'ActorList.php?'.SID);
   	Exit;
}

$ActorSupplierCustomer = Object::load('Actor', $_GET['actId']);

if (!($ActorSupplierCustomer instanceof Actor)) {
    Template::errorDialog(_('Actor was not found in the database.'), 'ActorList.php?'.SID);
   	Exit;
}

$SupplierCustomer = $SupplierCustomerMapper->load(array('Customer' => $Actor, 'Supplier' => $ActorSupplierCustomer));
$SupplierCustomer2 = $SupplierCustomerMapper->load(array('Customer' => $ActorSupplierCustomer, 'Supplier' => $Actor));

$Smarty = new Template();
// Edition : acteur connecté client et acteur selectionné fournisseur
if (($SupplierCustomer instanceof SupplierCustomer) && (!($SupplierCustomer2 instanceof SupplierCustomer))) {
	$MaxIncur = (is_null($SupplierCustomer->GetMaxIncur()))?_('Undefined'):$SupplierCustomer->GetMaxIncur();

	$Smarty->Assign('MaxIncur', $MaxIncur);
	$Smarty->Assign('UpdateIncur', $SupplierCustomer->GetUpdateIncur());
	$Smarty->Assign('HiddenAction', 'Edit');
	$Smarty->Assign('ActorSelectedStatus', 'Fournisseur');
	$Smarty->Assign('ModalitySelected', $SupplierCustomer->GetModality());
	$Smarty->Assign('OptionSelected', $SupplierCustomer->GetOption());
	$Smarty->Assign('TotalDays', $SupplierCustomer->GetTotalDays());
} 

//edition : acteur connecté fournisseur et acteur selectionné client
elseif ((!($SupplierCustomer instanceof SupplierCustomer)) && ($SupplierCustomer2 instanceof SupplierCustomer)) {//edition
	$MaxIncur = (is_null($SupplierCustomer2->GetMaxIncur()))?_('Undefined'):$SupplierCustomer->GetMaxIncur();
	
	$Smarty->Assign('MaxIncur', $MaxIncur);
	$Smarty->Assign('UpdateIncur', $SupplierCustomer2->GetUpdateIncur());
	$Smarty->Assign('HiddenAction', 'Edit');
	$Smarty->Assign('ActorSelectedStatus', 'Client');
	$Smarty->Assign('ModalitySelected', $SupplierCustomer2->GetModality());
	$Smarty->Assign('OptionSelected', $SupplierCustomer2->GetOption());
	$Smarty->Assign('TotalDays', $SupplierCustomer2->GetTotalDays());
}
//edition : acteur connecté fournisseur et acteur selectionné client ou edition acteur connecté client et acteur selectionné fournisseur
elseif (($SupplierCustomer instanceof SupplierCustomer) && ($SupplierCustomer2 instanceof SupplierCustomer)) {//edition
}
//creation
elseif ((!($SupplierCustomer instanceof SupplierCustomer)) && (!($SupplierCustomer2 instanceof SupplierCustomer))) {
	$Smarty->Assign('HiddenAction', 'Add');
}

$Smarty->Assign('Modality_Id_Select', SupplierCustomer::getModalityConstArray(true)); 
$Smarty->Assign('Modality_Names_Select', array_values(SupplierCustomer::getModalityConstArray()));
$Smarty->Assign('Option_Id_Select', SupplierCustomer::getOptionConstArray(true));
$Smarty->Assign('Option_Names_Select', array_values(SupplierCustomer::getOptionConstArray()));

$Smarty->Assign('ActorName', $Actor->GetName());
$Smarty->Assign('ActorSupplierCustomer', $ActorSupplierCustomer->GetName());
$Smarty->Assign('HiddenactId', $ActorSupplierCustomer->GetId());
$Smarty->Assign('ReturnUrl', 'ActorList.php?'.SID);
  
Template::page(_('Outstanding debts and payments management'), $Smarty->fetch('Actor/ActorIncur.html'));

?>
