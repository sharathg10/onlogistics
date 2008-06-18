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
$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
						   UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES));

SearchTools::prolongDataInSession();  // prolonge les datas en session

if (!isset($_REQUEST['thId'])) {
	Template::errorDialog(E_MSG_TRY_AGAIN, 'ToHaveList.php');
   	Exit;

}
$ToHave = Object::load('ToHave', $_REQUEST['thId']);
if (Tools::isEmptyObject($ToHave)) {
    Template::errorDialog(E_MSG_TRY_AGAIN, 'ToHaveList.php');
  	Exit;
}

$InvoicePaymentCollection = $ToHave->getInvoicePaymentCollection();

$grid = new Grid();
$grid->withNoCheckBox = true;
$grid->displayCancelFilter = false;

$grid->NewColumn('FieldMapper', _('Payment number'), array('Macro' =>'%Payment.Id%'));
$grid->NewColumn('FieldMapper', _('Payment date'),array('Macro' => '%Payment.Date|formatdate@DATE_SHORT%'));
$grid->NewColumn('MultiPrice', _('Amount paid'), array('Method' => 'getPriceTTC'));
$grid->NewColumn('FieldMapper', _('Invoice number'), array('Macro' => '%Invoice.DocumentNo%'));
$grid->NewColumn('FieldMapper', _('Invoice date'), array('Macro' =>'%Invoice.EditionDate|formatdate@DATE_SHORT%'));
$grid->NewColumn('FieldMapper', _('Order number'), array('Macro' =>'%Invoice.Command.CommandNo%'));

$grid->withNoSortableColumn = true;
$Order = array('Payment.Date' => SORT_ASC);
$gridContent = $grid->render($InvoicePaymentCollection, false, array(), $Order);


//Affichage du formulaire avec smarty
$Smarty = new Template();
$Smarty->assign('gridContent', $gridContent);
$Smarty->assign('thId', $_REQUEST['thId']);
$Smarty->assign('DocumentNo', $ToHave->getDocumentNo());
$Smarty->assign('EditionDate', $ToHave->getEditionDate('localedate_short'));
$cur = $ToHave->getCurrency();
$Smarty->assign('Currency', $cur instanceof Currency?$cur->getSymbol():'&euro;');
$Smarty->assign('CustomerName',Tools::getValueFromMacro($ToHave, '%SupplierCustomer.Customer.Name%'));
$Smarty->assign('SupplierName', Tools::getValueFromMacro($ToHave, '%SupplierCustomer.Supplier.Name%'));
$Smarty->assign('TotalPriceHT', I18N::formatNumber($ToHave->getTotalPriceHT()));
$Smarty->assign('TVARate',I18N::formatNumber(Tools::getValueFromMacro($ToHave, '%TVA.Rate%')));
$Smarty->assign('TotalPriceTTC', I18N::formatNumber($ToHave->getTotalPriceTTC()));
$Smarty->assign('RemainingTTC',I18N::formatNumber($ToHave->getRemainingTTC()));
$Smarty->assign('Comment', $ToHave->getComment());


$pageContent = $Smarty->fetch('ToHave/ToHavePaymentList.html');
Template::page(_('Credit note details'), $pageContent);

?>