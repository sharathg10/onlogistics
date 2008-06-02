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
require_once('Objects/ToHave.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
						   UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES));

SearchTools::ProlongDataInSession();  // prolonge les datas en session

$returnURL = isset($_REQUEST['retURL']) ? $_REQUEST['retURL'] : 'ToHaveList.php';

//  Si on a clique sur Valider ou Valider et imprimer
if (isset($_REQUEST['formSubmitted'])) {
    
    // formatage des montants:
    $_REQUEST['TH_TotalPriceTTC'] = I18N::extractNumber($_REQUEST['TH_TotalPriceTTC']);
	Database::connection()->startTrans();
	
	$ToHave = Object::load('ToHave');
	
	$SCMapper = Mapper::singleton('SupplierCustomer');
	$SupplierCustomer = $SCMapper->load(array('Supplier' => $_REQUEST['TH_Supplier'], 
														  'Customer' => $_REQUEST['TH_Customer']));
	if (Tools::isEmptyObject($SupplierCustomer)) {
	    $SupplierCustomer = Object::load('SupplierCustomer');
		$SupplierCustomer->setSupplier($_REQUEST['TH_Supplier']);
		$SupplierCustomer->setCustomer($_REQUEST['TH_Customer']);
		$SupplierCustomer->setUpdateIncur(0); // Car NULL par defaut
	}
	$SupplierCustomer->setToHaveTTC($SupplierCustomer->getToHaveTTC() + $_REQUEST['TH_TotalPriceTTC']);
	
	// MAJ de l'encours courant du couple sp
	$SupplierCustomer->setUpdateIncur($SupplierCustomer->getUpdateIncur() - $_REQUEST['TH_TotalPriceTTC']);
    saveInstance($SupplierCustomer, $returnURL);
	
	$ToHave->setSupplierCustomer($SupplierCustomer);
	// L'Id sert a composer le DocumentNo, s'il n'est pas saisi
	$ToHaveMapper = Mapper::singleton('ToHave');
    $ToHaveId = $ToHaveMapper->generateId();
	$ToHave->setId($ToHaveId);
	
	if (!empty($_REQUEST['TH_DocumentNo'])) {
	    $DocumentNo = $_REQUEST['TH_DocumentNo'];
	}
	else {
		require_once('InvoiceItemTools.php');
		$DocumentNo = GenerateDocumentNo('AV', 'ToHave', $ToHaveId);
	}
	
	$ToHave->setDocumentNo($DocumentNo);
	$ToHave->setEditionDate(date('Y-m-d H:i:s'));
	$ToHave->setComment($_REQUEST['TH_Comment']);
	$ToHave->setType($_REQUEST['TH_Type']);
	$ToHave->setTotalPriceHT($_REQUEST['TH_TotalPriceHT']);
	$ToHave->setTotalPriceTTC($_REQUEST['TH_TotalPriceTTC']);
	$ToHave->setTVA((isset($_REQUEST['TH_TVA']) ? $_REQUEST['TH_TVA'] : 0));
	$ToHave->setRemainingTTC($_REQUEST['TH_TotalPriceTTC']);
	$ToHave->setCurrency($_REQUEST['TH_Currency']);
	$DocumentModel = $ToHave->FindDocumentModel();
	if (!(false == $DocumentModel)) {
	    $ToHave->setDocumentModel($DocumentModel);
	}
    // gestion de la remise par pourcentage du CA annuel, il faut la diminuer 
    // du montant HT de l'avoir 
    if ($SupplierCustomer->getAnnualTurnoverDiscountPercent()) {
        // si sujet à la remise
        // IMPORTANT: le montant passé est négatif !
        $SupplierCustomer->updateAnnualTurnoverDiscount(-$ToHave->getTotalPriceHT());
    }
    saveInstance($ToHave, $returnURL);
	
	// Commit de la transaction, si elle a réussi
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog($errorBody, $returnURL);
        Exit;
    } 
	Database::connection()->completeTrans();

	Tools::redirectTo('ToHaveList.php?printId=' . $ToHaveId);
	exit;
}


/*  Formulaire */
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('ToHaveAdd', 'post', $_SERVER['PHP_SELF']);
// Validation des saisies
$form->updateAttributes(array('onsubmit' => "return checkBeforeSubmit();"));

// Prefixe TH (pour ToHave) pour eviter les interferences en session avec le form de recherche
$SupplierArray = SearchTools::CreateArrayIDFromCollection(array('Actor', 'AeroActor'), 
    array('Generic'=>0), _('Select a supplier'));
$form->addElement('select', 'TH_Supplier', _('Supplier'), $SupplierArray, 
    'style="width:100%" onchange="onTypeSelectChanged(true);"');
$CustomerArray = SearchTools::CreateArrayIDFromCollection(array('Actor', 'AeroActor'), 
    array('Generic'=>0), _('Select a customer'));
$form->addElement('select', 'TH_Customer', _('Customer'), $CustomerArray, 
    'style="width:100%" onchange="onTypeSelectChanged(true);"');

$form->addElement('text', 'TH_DocumentNo', _('Credit note number'), 'style="width:100%"');  
$form->addElement('text', 'TH_TotalPriceHT', 'Total HT', 'style="width:100%" onKeyUp="calculTTC()"');
$TVAArray = SearchTools::CreateArrayIDFromCollection('TVA', array(), _('Select a rate'));
$form->addElement('select', 'TH_TVA', 'TVA', $TVAArray, 'style="width:100%" onchange="calculTTC()"');
// TotalTTC ne correspond pas a un attribut de la classe
$form->addElement('text', 'TH_TotalPriceTTC', _('Amount incl. VAT'), 'readonly style="width:100%"'); ////
$ToHaveTypeArray = array(0 => _('Select a type')) + ToHave::getTypeConstArray();
$form->addElement('select', 'TH_Type', _('Type'), $ToHaveTypeArray,
    'style="width:100%" onchange="onTypeSelectChanged();"');
$form->addElement('textarea', 'TH_Comment', _('Comment'), 'style="width:100%"');

// devise
$currencyArray = SearchTools::CreateArrayIDFromCollection(array('Currency'));
$form->addElement('select', 'TH_Currency', _('Currency'), $currencyArray, 'style="width:100%"');

$form->setDefaults(array('TH_Currency'=>1)); // euro
$form->accept($renderer); // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$pageContent = $smarty->fetch('ToHave/ToHaveAdd.html');

// JS_ToHaveAdd.php contient les validations JS
Template::ajaxPage(_('Add a credit note'), $pageContent,
    array('js/lib-functions/FormatNumber.js', 'JS_ToHaveAdd.php'));

?>
