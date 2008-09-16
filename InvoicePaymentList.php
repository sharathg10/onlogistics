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
$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
          				   UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_ACCOUNTANT));

SearchTools::ProlongDataInSession();  // prolonge les datas du form de recherche en session

if (empty($_REQUEST['invId'])) {
    Template::errorDialog(sprintf(E_MSG_MUST_SELECT_A, _('Invoice')), 'InvoiceList.php');
    exit;
}

$mapper = Mapper::singleton('InvoicePayment');
$InvoicePaymentCollection = $mapper->loadCollection(
        array('Invoice' => $_REQUEST['invId']));

$grid = new Grid();
$grid->paged = false;
$grid->displayCancelFilter = false;
$grid->withNoCheckBox = true;

$grid->NewAction('Print');
$grid->NewAction('Export', array('FileName' => 'Factures'));
$grid->NewAction('Cancel', array('Caption' => 'Retour', 'ReturnURL' => 'InvoiceList.php'));

$grid->NewColumn('FieldMapper', _('Invoice number'), array('Macro' => '%Invoice.DocumentNo%'));
$grid->NewColumn('FieldMapper', _('Edition date'),
    	array('Macro' => '%Invoice.EditionDate|formatdate@DATE_SHORT%'));
$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%Payment.Reference%'));
$grid->NewColumn('FieldMapperWithTranslation', _('Means of payment'),
    	array('Macro' => '%Payment.Modality%',
              'TranslationMap' => TermsOfPaymentItem::getPaymentModalityConstArray()));
$grid->NewColumn('FieldMapper', _('Bank'),
    	array('Macro' => '%Payment.ActorBankDetail.BankName|default%'));
$grid->NewColumn('FieldMapper', _('Total amount incl. VAT'),
    	array('Macro' => '%PriceTTC|formatnumber%'));
$grid->NewColumn('FieldMapperWithTranslation', _('Cancellation date'),
        array('Macro' => '%Payment.CancellationDate|formatdate@DATE_SHORT%',
              'TranslationMap' => array('00/00/00' => '')));
$grid->NewColumn('FieldMapper', _('Remaining to pay'),
        array('Macro' => '%Invoice.ToPay|formatnumber%'));

if ($grid->isPendingAction()) {
    $InvoicePaymentCollection = false;
    $grid->setMapper($mapper);
    $dispatchResult = $grid->dispatchAction($InvoicePaymentCollection);
    if (Tools::isException($dispatchResult)) {
        Template::errorDialog($dispatchResult->getMessage(),
                       $_SERVER['PHP_SELF'] . '?invId=' . $_REQUEST['invId']);
    }
}
else {
  	$result = $grid->render($InvoicePaymentCollection);
    Template::page(
        _('List of payments for selected invoices'),
        '<form>'. $result. '</form>'
    );
}

?>
