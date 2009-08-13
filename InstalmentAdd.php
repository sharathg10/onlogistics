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
 * @version   SVN: $Id: InstalmentAdd.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once('config.inc.php');
require_once('Objects/Instalment.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
						   UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES));

$Command = Object::load('Command', $_REQUEST['CommandId']);
SearchTools::ProlongDataInSession();  // prolonge les datas en session

//  Dans tous ces cas: Redirect vers la liste des commandes
if (!($Command instanceof ProductCommand)) {
    Template::errorDialog(_('Selected order was not found in the database.'), $returnURL);
    exit;
}
$SupplierCustomer=$Command->getSupplierCustomer();

$invoices = $Command->getInvoiceCollection();
if (!Tools::isEmptyObject($invoices)) {
    Template::errorDialog(_('An instalment is only possible when the order has no invoice.'), $returnURL);
    exit;
}

$returnURL = isset($_REQUEST['returnURL']) ? $_REQUEST['returnURL'] : 'ExpectedInstalmentList.php';

//  Si on a clique sur Valider ou Valider et imprimer
if (isset($_REQUEST['formSubmitted'])) {
    
    // formatage des montants:
    $_REQUEST['TI_Instalment'] = I18N::extractNumber($_REQUEST['TI_Instalment']);
	Database::connection()->startTrans();
    $InstalmentMapper = Mapper::singleton('Instalment');
    $Instalment = Object::load('Instalment');

	// L'Id sert a composer le DocumentNo, s'il n'est pas saisi
    $InstalmentId = $InstalmentMapper->generateId();
    $Instalment->setId($InstalmentId);
	if (!empty($_REQUEST['TI_DocumentNo'])) {
	    $DocumentNo = $_REQUEST['TI_DocumentNo'];
	} else {
		require_once('InvoiceItemTools.php');
		$DocumentNo = GenerateDocumentNo('IN', 'Instalment', $InstalmentId);
	}
	$Instalment->setDocumentNo($DocumentNo);

    $Instalment->setCommand($Command);

	
	$Instalment->setDate(date('Y-m-d H:i:s'));
	$Instalment->setModality($_REQUEST['TI_Modality']);
    $Instalment->setTotalPriceTTC($_REQUEST['TI_Instalment']);

    $InitialUpdateIncur = $SupplierCustomer->getUpdateIncur();
    $SupplierCustomer->setUpdateIncur($SupplierCustomer->getUpdateIncur() - $_REQUEST['TI_Instalment']);
    saveInstance($SupplierCustomer, $returnURL);
    if ($SupplierCustomer->getMaxIncur() > 0 ) { // l'encours autorisé est défini
        if (($InitialUpdateIncur > $SupplierCustomer->getMaxIncur())
            && ($SupplierCustomer->getMaxIncur() < $SupplierCustomer->getUpdateIncur())) {
                require_once('ProductCommandTools.php');
                // Debloque toutes les cdes bloquees, ds la limite de UpdateIncur - MaxIncur
                blockDeblockage($ConnectedActorId, $destinator, 0);
        } 
    }

    saveInstance($Instalment, $returnURL);
	
	// Commit de la transaction, si elle a réussi
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog($errorBody, $returnURL);
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

require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('InstalmentAdd', 'post', $_SERVER['PHP_SELF']);
// Validation des saisies
$form->updateAttributes(array('onsubmit' => "return checkBeforeSubmit();"));


// Prefixe TI  (pour Treso Instalment) pour eviter les interferences en session avec le form de recherche
$SupplierArray = SearchTools::CreateArrayIDFromCollection(array('Actor', 'AeroActor'), 
    array('Generic'=>0), _('Select a supplier'));
$form->addElement('select', 'TI_Supplier', _('Supplier'), $SupplierArray, 
    'style="width:100%" onchange="onTypeSelectChanged(true);"');
$CustomerArray = SearchTools::CreateArrayIDFromCollection(array('Actor', 'AeroActor'), 
    array('Generic'=>0), _('Select a customer'));
$form->addElement('select', 'TI_Customer', _('Customer'), $CustomerArray, 
    'style="width:100%" onchange="onTypeSelectChanged(true);"');

$form->addElement('text', 'TI_DocumentNo', _('Instalment DocumentNo'), 'style="width:100%"');  
$form->addElement('text', 'TI_Instalment', 'Total', 'style="width:100%"');
$InstalmentModalityArray = array(0 => _('Select a type')) + TermsOfPaymentItem::getPaymentModalityConstArray();
$form->addElement('select', 'TI_Modality', _('Type'), $InstalmentModalityArray,
    'style="width:100%" onchange="onTypeSelectChanged();"');

// devise
$currencyArray = SearchTools::CreateArrayIDFromCollection(array('Currency'));
$form->addElement('select', 'TI_Currency', _('Currency'), $currencyArray, 'style="width:100%"');

$form->setDefaults(array('TI_Currency'=>1)); // euro

// On vient de la liste des commandes et on ajoute un accompte pour la commande 

$SupplierCustomer = $Command->getSupplierCustomer();
$form->setDefaults(array(
    'TI_Currency' => $Command->getCurrency()->getId() ,
    'TI_Supplier' => $SupplierCustomer->getSupplierId(),
    'TI_Customer' => $SupplierCustomer->getCustomerId()
));
$form->getElement('TI_Supplier')->setAttribute('disabled', 'disabled');
$form->getElement('TI_Customer')->setAttribute('disabled', 'disabled');
$form->getElement('TI_Currency')->setAttribute('disabled', 'disabled');

$form->addElement('hidden', 'CommandId', 'pouet' );
$form->setDefaults(array('CommandId'=> $_REQUEST['CommandId'])); 

require_once('lib/SQLRequest.php');
$InstalmentToPay = request_checkCommandInstalments($_REQUEST['CommandId']);
$InstalmentPaid = $Command->getTotalInstalments() ;
$TotalToPay = $Command->getTotalPriceTTC() - $Command->getTotalInstalments() ;
if($InstalmentToPay == FALSE) $InstalmentToPay = 0 ;
if($InstalmentToPay != 0 ) $form->setDefaults(array('TI_Instalment' => $InstalmentToPay - $InstalmentPaid));

$form->accept($renderer); // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$pageContent = $smarty->fetch('Instalment/InstalmentAdd.html');
$curSymb = $Command->getCurrency()->getSymbol() ;
$TotalToPay .= " ".$curSymb ;
$InstalmentToPay .= " ".$curSymb ;
$totalTTC = $Command->getTotalPriceTTC()." ".$curSymb ;
// JS_InstalmentAdd.php contient les validations JS
Template::ajaxPage(
    sprintf(_('Add an instalment for order %s'), $Command->getCommandNo()). ' - '
    .sprintf(_('Total : %s'), $totalTTC)." - "
    .sprintf(_('To Pay : %s'), $TotalToPay).' - '
    .sprintf(_('Expected Instalment : %s'), $InstalmentToPay).' ',
    $pageContent,
    array('js/lib-functions/FormatNumber.js', 'JS_InstalmentAdd.php'));

?>
