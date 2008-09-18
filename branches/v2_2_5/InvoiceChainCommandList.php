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

require_once('Objects/Command.const.php');
require_once('Objects/Command.php');
require_once('ProductCommandTools.php');
require_once('Objects/TVA.inc.php');
require_once('PrestationManager.php');

//Database::connection()->debug = 1;

$auth = Auth::Singleton();
$auth->checkProfiles(
array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
      UserAccount::PROFILE_AERO_ADMIN_VENTES));

SearchTools::prolongDataInSession(); // prolonge les datas du form de recherche en session

/*
quelques variables
*/
$InvoiceMapper = Mapper::singleton('Invoice');
$retURL = isset($_GET['returnURL'])?$_GET['returnURL']:'ChainCommandList.php';

//le formulaire à été posté via "régler"
if (isset($_REQUEST['FormSubmitted']) && $_REQUEST['FormSubmitted'] == 'true') {
    require_once('InvoiceItemTools.php');
    $errorURL = 'javascript:history.go(-1)';
    Database::connection()->startTrans();
    $Invoice = Object::load('Invoice');
    $Command = Object::load('Command', $_POST['HiddenCommandID']);
    $Invoice->setCommand($Command);

    /*
    Numéro de facture
    */
    if (isset($_POST['InvoiceNumero']) && ($_POST['InvoiceNumero'] != "")) {
		if (DocumentNoExist($_POST['InvoiceNumero'])) {
            Template::errorDialog(
                _('A document with the same number already exists, please correct.'),
				$errorURL );
       		Exit();
		}
        $DocumentNo = $_POST['InvoiceNumero'];
    } else {
        $cdetype = 'FT';
		$InvoiceId = $InvoiceMapper->generateId();
        $DocumentNo = GenerateDocumentNo($cdetype, 'AbstractDocument', $InvoiceId);
    }
    if (!isset($InvoiceId)) {
	    $InvoiceId = $InvoiceMapper->generateId();
	}
    $Invoice->setId($InvoiceId);
	$Invoice->setDocumentNo($DocumentNo);
	$DocumentModel = $Invoice->FindDocumentModel();
	if (!(false == $DocumentModel)) {
	    $Invoice->setDocumentModel($DocumentModel);
	}
    /*
    Mise à jour de AbstractDocument
    */
    $Invoice->setCommand($Command);
    $Invoice->setCommandType($Command->getInvoiceCommandType());
    $Invoice->setAccountingTypeActor($Command->getDestinatorId());
    $Invoice->setSupplierCustomer($Command->getSupplierCustomer());
    $Invoice->setCurrency($Command->getCurrency());
    $Invoice->setPacking($_POST['Packing']);
    $Invoice->setInsurance($_POST['Insurance']);
    $Invoice->setGlobalHanding($_POST['GlobalHanding']);
    $Invoice->setTotalPriceHT($_POST['totalpriceHT']);
    $Invoice->setTotalPriceTTC($_POST['totalpriceTTC']);
    $Invoice->setToPay($_POST['ToPay']);

    $Invoice->setEditionDate(date('Y-m-d H:i:s'));
    savePaymentDate($Invoice, $Command);

    if(isset($_POST['IAEComment'])) {
        $Invoice->setComment($_POST['IAEComment']);
    }
    //$Invoice->setRemainingTTC($_POST['ToPay']);


    /*
    la commande passe à l'état facturée complétement
    */
    $Command->setState(Command::FACT_COMPLETE);

    /*
    Sauvegarde
    */
    saveInstance($Invoice, $errorURL);
    saveInstance($Command, $errorURL);
    // CREATION DES INVOICE ITEM POUR LE DETAIL DES PRESTATIONS
    if(isset($_SESSION['ChainCommandInvoiceItem'])) {
        $invItemCol = $_SESSION['ChainCommandInvoiceItem'];
        foreach($invItemCol as $invoiceItem) {
            $invoiceItem->setInvoice($Invoice);
            $invoiceItem->save();
        }
    }
    /*
     * MAJ ACO pour ne pas les refacturer
     */
    $prsManager = new PrestationManager();
    $acoCol = $prsManager->findACOForCommand($Command);
    foreach($acoCol as $aco) {
        $aco->setPrestationFactured(true);
        $aco->setPrestationCommandDate(date('Y-m-d H:i:s', time()));
        saveInstance($aco, $errorURL);
    }
    // met à jour les box utilisé pour la facturation des aco
    $prsManager->updateBox($Invoice);

    /*
    commit de la transaction
    */
    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        die('erreur sql');
    }
    Database::connection()->completeTrans();

    Tools::redirectTo($retURL.'?InvoiceId='.$Invoice->getId().
               '&print=1');
    exit;
}

/*
affichage de l'ecran de facturation
*/
if (empty($_GET['CommandId'])) {
    Template::errorDialog(
        _('Please select an order for billing.'),
        $retURL);
    exit;
}

if(!empty($_GET['CommandId']) && !is_array($_GET['CommandId'])) {
    $CommandId = $_GET['CommandId'];
} else {
    $CommandId = $_GET['CommandId'][0];
}

$Command = Object::load('ChainCommand', $CommandId);

if ($Command->getState() == Command::BLOCAGE_CDE) {
    Template::errorDialog(
        _('Order is locked because outstanding debts amount exceeds its maximum value.'),
        $retURL);
    exit;
}
/*
si la commande est à l'état facturation compléte on redirige vers la liste
des factures de la commande
*/
if ($Command->getState() == Command::FACT_COMPLETE) {
    Tools::redirectTo('InvoiceCommandList.php?CommandId=' . $CommandId . '&returnURL=' . $retURL);
    exit;
}
$pref = Preferences::get('ChainCommandBillingBehaviour');
// base sur reel => verifier que les ack de regroupement precedant le
// transport ne sont pas toutes encore a faire
if ($pref == 1 && $Command->hasAllGoupingTaskToDo()) {
    Template::errorDialog(
        _('Carriage services billing is based on reality: a grouping task have to be executed before billing.'),
        $retURL);
    exit;
}

$ChainCommandItemCollection = $Command->getCommandItemCollection();
$Expeditor = $Command->getExpeditor();
$Destinator = $Command->getDestinator();
$sp = $Command->getSupplierCustomer();
$hasTVA = $sp->getHasTVA();

/*
pour l'affichage du grid
*/
$grid = new Grid();
$grid->withNoCheckBox = true;

//Tableau de rappel du détail de la commande
$grid->NewColumn('FieldMapper', _('Parcel number'), array('Macro' => '%Id%'));
$grid->NewColumn('FieldMapper', _('Parcel type'), array('Macro' => '%CoverType.Name%'));
$grid->NewColumn('FieldMapper', _('Content'), array('Macro' => '%ProductType.Name%'));
$grid->NewColumn('FieldMapper', _('Weight'), array('Macro' => '%Weight%'));
$grid->NewColumn('FieldMapper', _('Dimensions'), array('Macro' => '%Height%*%Length%*%Width%'));
$grid->NewColumn('FieldMapper', _('Qty'), array('Macro' => '%quantity%'));

$InvoiceItemGrid = $grid->Render($ChainCommandItemCollection, false, array(), array(), 'Invoice/InvoiceItemGrid.html');

/*
assignation des variables smarty
*/
$smarty = new Template();
//varaibles diverses
$smarty->assign('returnURL', $retURL);
$smarty->assign('CommandNo', $Command->getCommandNo());
$smarty->assign('CmdType', $Command->getType());
$smarty->assign('HiddenCommandID', $Command->getId());

$cur = $Command->getCurrency();
$smarty->assign('Currency', $cur instanceof Currency?$cur->getSymbol():'&euro;');

//Information liées au supplierCustomer
$MaxIncur = (is_null($sp->getMaxIncur()))?_('Undefined'):I18N::formatNumber($sp->getMaxIncur());
$top = $sp->getTermsOfPayment();
if ($top instanceof TermsOfPayment) {
    $smarty->assign('TermsOfPayment', $top->getName());
}
$smarty->assign('Customer', Tools::getValueFromMacro($Command, '%Customer.Name%'));
$smarty->assign('MaxIncur', $MaxIncur);
$smarty->assign('UpdateIncur', I18N::formatNumber($sp->getUpdateIncur()));
$smarty->assign('ToHaveTTC', I18N::formatNumber($sp->getToHaveTTC()));

//Tableau de rappel du détail de la commande
$smarty->assign('InvoiceItemGrid', $InvoiceItemGrid);

// calcul de la facture
$prsManager = new PrestationManager();
$prices = $prsManager->calculChainCommandCost($Command);
$ht = $ttc = 0;
foreach($prices as $key=>$values) {
    $ht += $values['totalht'];
    $ttc += $values['totalttc'];
}
$rawht = $ht;
$handing   = $Command->getHanding();
$insurance = $Command->getInsurance();
$packing   = $Command->getPacking();
$ht  = ($ht - ($ht * $handing/100)) + $insurance + $packing;
$ttc = ($ttc - ($ttc * $handing/100)) + $insurance + $packing;
$sc  = $Command->getSupplierCustomer();
if (!($sc instanceof SupplierCustomer) || $sc->getHasTVA()) {
    require_once('Objects/TVA.inc.php');
    $ttc += ($insurance * (getTVARateByCategory(TVA::TYPE_INSURANCE)/100))
        + ($packing   * (getTVARateByCategory(TVA::TYPE_PACKING)/100));
}
//frais annexes et remises
$smarty->assign('Packing', $packing);
$smarty->assign('Insurance', $insurance);
$smarty->assign('totalPrestHT', $rawht);
$smarty->assign('TotalPriceHT', $ht);
$smarty->assign('ToPay', $ttc-$Command->getInstallment());
$smarty->assign('TVATotal', $ttc-$ht);
$smarty->assign('GlobalHanding', $handing);

if ($hasTVA) {
    $smarty->assign('packing_tva_rate', getTVARateByCategory(TVA::TYPE_PACKING));
    $smarty->assign('insurance_tva_rate', getTVARateByCategory(TVA::TYPE_INSURANCE));
}

//Totaux
$smarty->assign('Installment', $Command->getInstallment());

/*
Affichage
*/
Template::page(
    _('Invoice'),
    $smarty->fetch('Invoice/InvoiceChainCommandList.html'),
    array(
            'js/lib-functions/FormatNumber.js',
            'js/includes/InvoiceChainCommandList.js'
        )
);
?>
