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
          				   UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_COMMERCIAL,
                           UserAccount::PROFILE_CLIENT_TRANSPORT, UserAccount::PROFILE_DIR_COMMERCIAL));

// Prolonge les datas du form de recherche en session
SearchTools::prolongDataInSession();
$retURL = isset($_REQUEST['returnURL'])?$_REQUEST['returnURL']:'CommandList.php';

if (empty($_REQUEST['cmdId'])) {
    Template::errorDialog(sprintf(E_MSG_MUST_SELECT_A, 'commande'), $_GET['returnURL']);
    exit;
}

$Command = Object::load('Command', $_REQUEST['cmdId']);
$PaymentCollection = $Command->getPaymentCollection();

$grid = new Grid();
$grid->itemPerPage = 50;

$phpself = $_SERVER['PHP_SELF'].'?cmdId='.$_REQUEST['cmdId'].'&returnURL='.$retURL;
$grid->NewAction('Delete',
    array(
        'TransmitedArrayName' => 'pmtId',
        'EntityType' => 'Payment',
        'Query' => 'cmdId=' . $_REQUEST['cmdId'] . '&ReturnURL='
                . $_SERVER['PHP_SELF'] . '?returnURL=' . $retURL,
        'ReturnURL' => $phpself,
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL)
    )
);

$grid->NewAction('Cancel', array('Caption'=>_('State of orders'), 'ReturnURL'=>$retURL));

$grid->NewColumn('PaymentId', _('Payment number'), array('Sortable' => false));
$grid->NewColumn('FieldMapper', _('Edition date'),
    	array('Macro' => '%Date|formatdate@DATE_SHORT%'));
$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%DocumentNo%'));
$grid->NewColumn('FieldMapperWithTranslation', _('Means of payment'),
    	array('Macro' => '%Modality%',
              'TranslationMap' => array(-1=>_('N/A')) + TermsOfPaymentItem::getPaymentModalityConstArray()));
$grid->NewColumn('FieldMapper', _('Bank'),
    	array('Macro' => '%ActorBankDetail.BankName|default%'));
$grid->NewColumn('MultiPrice', _('Total amount incl. VAT'),
    	array('Method' => 'getTotalPriceTTC', 'Currency' => $Command->getCurrency()));
$grid->NewColumn('FieldMapper', _('Invoice(s)'),
        array('Macro' => '%InvoiceCollection%', 'Sortable' => false));
$grid->NewColumn('FieldMapperWithTranslation', _('Order'),
        array('Macro' => '%Command.CommandNo%' , '0' => 'N/A',
            'TranslationMap' => array(0 => _('N/A'))));

if ($grid->isPendingAction()) {
    $PaymentCollection = false;
	$PaymentMapper = Mapper::singleton('Payment');
    $grid->setMapper($PaymentMapper);
    $dispatchResult = $grid->dispatchAction($PaymentCollection);
    if (Tools::isException($dispatchResult)) {
        Template::errorDialog($dispatchResult->getMessage(), $phpself);
    }
} else {
//    $PaymentCollection = $Command->getInstalmentCollection() ;
  	$result = $grid->render($PaymentCollection);
    Template::page(
	    sprintf(_('List of payments for order %s'), $Command->getCommandNo()) . '  ',
        '<form>'. $result. '</form>'
    );
}

?>
