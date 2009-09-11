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
require_once('Objects/TVA.inc.php');
require_once('Objects/Prestation.php');
require_once('InvoicePrestationTools.php');
require_once('PrestationManager.php');
require_once('LangTools.php');
require_once('AlertSender.php');
//includeSessionRequirements();
$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
$session = Session::singleton();

$pageTitle = _('Services invoices');
$returnURL = 'InvoicePrestationList.php';
//Database::connection()->debug = true;
$customerID = SearchTools::requestOrSessionExist('Customer');

if (!$customerID) {
    Template::errorDialog(E_ERROR_SESSION);
    exit(1);
}
$customer = Object::load('Actor', $customerID);
$session->register('Customer', $customerID, 2);

$supplier = $auth->getActor();
// Devise
$currency = $customer->getCurrency();
$curStr = $currency instanceof Currency?$currency->getSymbol():'&euro;';

$mapper = Mapper::singleton('SupplierCustomer');
$supplierCustomer = $mapper->load(
        array('Customer' => $customer->getId(), 'Supplier' => $supplier->getId()));

$manager = new PrestationManager(array('customer' => $customer->getId()));
$manager->deleteCommandItems();

// Validation et enregistrement en base
if (isset($_REQUEST['formSubmitted']) && $_REQUEST['formSubmitted'] == 'true') {
    checkDataBeforeSubmit();
    Database::connection()->startTrans();

    // Sauvegarde tout en base
    $ivId = $manager->saveAll();
    PrestationManager::cleanSession();
    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
        exit;
    }

    Database::connection()->completeTrans();

    // Si commande fournisseur, pas d'envoi par mail
    // Idem si parametre comme ça dans SupplierCustomer
    $invoice = Object::load('Invoice', $ivId);
    if ($invoice->getCommandType() == AbstractDocument::TYPE_SUPPLIER_PRESTATION
    || $supplierCustomer->getInvoiceByMail() == SupplierCustomer::INVOICE_BY_MAIL_NONE) {
        Tools::redirectTo($returnURL . '?InvoiceId=' . $ivId);
        exit();
    }

	// Envoi de mail: simplement une alerte: la facture est disponible
	else if ($supplierCustomer->getInvoiceByMail() == SupplierCustomer::INVOICE_BY_MAIL_ALERT) {
        // Si pas d'envoi de mail car pas de userAccount parametre pour
        // les recevoir: cas gere via l'exception retournee par MailTools::send()
        $result = AlertSender::send_ALERT_INVOICE_TO_DOWNLOAD($invoice, $auth->getUser());
	}
	// Envoi de mail: la facture en piece jointe
	else if ($supplierCustomer->getInvoiceByMail() == SupplierCustomer::INVOICE_BY_MAIL_YES) {
        // Si pas d'envoi de mail car pas de userAccount parametre pour
        // les recevoir: cas gere via l'exception retournee par MailTools::send()
        $result = AlertSender::send_ALERT_INVOICE_BY_MAIL($invoice, $auth->getUser());
	}
    if (Tools::isException($result)) {
        Template::errorDialog(
            _('Invoice could not be sent by email because no user ')
            . _('is set to receive it.'),
            $returnURL
        );
    }else {
        Template::infoDialog(_('Invoice was successfully sent by email.'), $returnURL);
    }
	exit;
} // /Validation


// Formulaire de saisie
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('PrestationInvoiceAddEdit', 'post');

$form->addElement('text', 'cmdNumber', _('Order number'), 'style="width:100%"');
$form->addElement('text', 'invoiceDocumentNo', _('Invoice number'),
        'style="width:100%"');
$commercialFilter = array('Profile' => UserAccount::PROFILE_COMMERCIAL, 'Actor' => $auth->getActorId());
$commercialArray = SearchTools::createArrayIDFromCollection(
        'UserAccount', $commercialFilter, MSG_SELECT_AN_ELEMENT, 'Identity');
$form->addElement('select', 'Commercial', _('Salesman'),
        $commercialArray, 'style="width:100%"');
$supplSiteArray = SearchTools::createArrayIDFromCollection(
        'Site', array('Owner' => $supplier->getId()), MSG_SELECT_AN_ELEMENT, 'Name');
$form->addElement('select', 'SupplierSite', _('Supplier billing site'),
        $supplSiteArray, 'style="width:100%"');

$custSiteArray = SearchTools::createArrayIDFromCollection(
        'Site', array('Owner' => $customer->getId()), MSG_SELECT_AN_ELEMENT, 'Name');
$form->addElement('select', 'CustomerSite', _('Customer billing site'),
        $custSiteArray, 'style="width:100%"');
$onKeyUp = 'onKeyUp="RecalculateTotal();RecalculateToPay()"';
$form->addElement('text', 'Port', _('Forwarding charges'), $onKeyUp);
$form->addElement('text', 'Emballage', _('Handling charges'), $onKeyUp);
$form->addElement('text', 'Assurance', _('Insurances charges'), $onKeyUp);
$form->addElement('text', 'GlobalHanding', _('Global discount'), $onKeyUp);
$form->addElement('text', 'Instalment', _('Instalment'),
        'onKeyUp="RecalculateToPay();RecalculateUpdateIncur();" '
        . 'size="15" class="FieldWhite" value="0"');
$form->addElement('select', 'InstalmentModality', 
    _('Instalment').' - '._('Means of payment'), 
    TermsOfPaymentItem::getPaymentModalityConstArray(), 'style="width:80%"');

$form->addElement('textarea', 'cmdComment', _('Comment'), 'cols="100%" rows="3"');

// A partir de $_REQUEST, si retour apres erreur
$defaultValue = SearchTools::createDefaultValueArray();
$form->setDefaults($defaultValue);
$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
// /form


// Le Grid
$grid = new Grid();
$grid->customizationEnabled = false;
$grid->withNoSortableColumn = true;
$grid->NewColumn('PrestationName', _('Service'));
/*$grid->NewColumn('FieldMapper', _('Base HT ') . $curStr,
        array('Macro' => '%PriceHT|formatnumber% <input type="hidden" name="baseHT[]"'
                . ' value="%PriceHT%">'));*/
$grid->NewColumn('PrestationCmdItemTVA', _('% VAT'), array('spc' => $supplierCustomer));

/*$grid->NewColumn('FieldMapper', _('% VAT'), array(
        'Macro' => '%RealTvaRate|formatnumber%<input name="HiddenTVA[]"'
        . ' value="%RealTvaRate%" type="hidden">'));*/
$grid->NewColumn('FieldMapper', _('Qty'), array('Macro' => '%Quantity% <input '
    . 'type="hidden" name="qty[]" value="%Quantity%" >'));
$grid->NewColumn('FieldMapper', _('Discount'),
        array('Macro' => '<input type="text" name="Handing[]" size="5" '.$onKeyUp.'>'));
#$grid->NewColumn('FieldMapper', _('Unit price excl. VAT ') . $curStr,
#        array('Macro' => '%UnitPriceHT|formatnumber@3% <input type="hidden" '
#                       . 'name="UnitPriceHT[]" value="%UnitPriceHT%">'));
$grid->NewColumn('PrestationUnitPriceHT', _('Unit price excl. VAT'));
$grid->NewColumn('FieldMapper', _('Price excl. VAT ') . $curStr,
        array('Macro' => '%PriceHT|formatnumber% <input type="hidden" '
                       . 'name="PriceHT[]" value="%PriceHT%">'));
$grid->NewColumn('FieldMapper', sprintf(_('Total price excl. VAT %s'), $curStr),
        array('Macro' => '<input type="text" name="PTHT[]" size="7" '
                       . 'class="ReadOnlyField" readonly>'));

$gridRender = $grid->render($manager->commandItems, false, array(),
        array('Prestation.Name' => SORT_ASC), 'Product/ProductGrid.html');
// /grid


// Pour afficher les infos de stock si besoin
//$smarty->assign('withStock', $withStock);
$smarty->assign('Currency', $curStr);
$smarty->assign('customerName', $customer->getName());
$smarty->assign('remExcep', $customer->getRemExcep());
$smarty->assign('supplierName', $supplier->getName());
$smarty->assign('gridRender', $gridRender);
$smarty->assign('returnURL', $returnURL);

// Encours
if ($supplierCustomer instanceof SupplierCustomer) {
    // Gestion de la tva surtaxee
    $hasTvaSurtax = $supplierCustomer->getHasTvaSurtax();
    if ($hasTvaSurtax) {
        $tvaSurtaxRate = Preferences::get('TvaSurtax', 0);
        $tvaSurtaxRateFormated = I18N::formatNumber($tvaSurtaxRate);
        $smarty->assign('TvaSurtaxRateFormated', $tvaSurtaxRateFormated);
        $smarty->assign('TvaSurtaxRate', $tvaSurtaxRate);
    } else {
        $tvaSurtaxRate = 0;
        $smarty->assign('TvaSurtaxRate', 0);
    }
    
    // Attention: number_format(NULL) = 0 !!, d'ou le test suivant
    $MaxIncur = (is_null($supplierCustomer->getMaxIncur()))?
        _('Undefined'):I18N::formatNumber($supplierCustomer->getMaxIncur());
    $smarty->assign('MaxIncur', $MaxIncur);
    $smarty->assign('UpdateIncur',
        I18N::formatNumber($supplierCustomer->getUpdateIncur()));

    if ($supplierCustomer->getHasTVA()) {
        $smarty->assign('port_tva_rate', getTVARateByCategory(TVA::TYPE_DELIVERY_EXPENSES, $tvaSurtaxRate));
        $smarty->assign('packing_tva_rate', getTVARateByCategory(TVA::TYPE_PACKING, $tvaSurtaxRate));
        $smarty->assign('insurance_tva_rate', getTVARateByCategory(TVA::TYPE_INSURANCE, $tvaSurtaxRate));
    }
        
    // Gestion de la taxe Fodec
    $hasFodecTax = $supplierCustomer->getHasFodecTax();
    $fodecTaxRate = Preferences::get('FodecTax', 0);
    if ($hasFodecTax) {
        $fodecTaxRateFormated = I18N::formatNumber($fodecTaxRate);
        $smarty->assign('FodecTaxRateFormated', $fodecTaxRateFormated);
        $smarty->assign('FodecTaxRate', $fodecTaxRate);
    } else {
        $smarty->assign('FodecTaxRate', 0);
    }
    // Gestion du timbre fiscal
    $hasTaxStamp = $supplierCustomer->getHasTaxStamp();
    $taxStamp = Preferences::get('TaxStamp', 0);
    if ($hasTaxStamp) {
        $taxStampFormated = I18N::formatNumber($taxStamp);
        $smarty->assign('TaxStampFormated', $taxStampFormated);
        $smarty->assign('TaxStamp', $taxStamp);
    } else {
        $smarty->assign('TaxStamp', 0);
    }
}

$content = $smarty->fetch('Invoice/PrestationInvoiceAddEdit.html');
$js = array(
    'js/lib-functions/FormatNumber.js',
    'js/includes/PrestationInvoiceAddEdit.js',
    'js/jscalendar/calendar.js',
    getJSCalendarLangFile(),
    'js/jscalendar/calendar-setup.js');

Template::page($pageTitle, $content . '</form>', $js);
?>
