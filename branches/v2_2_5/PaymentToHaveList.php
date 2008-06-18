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
$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
						   UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES));

// prolonge les datas du form de recherche en session
SearchTools::prolongDataInSession();

if (!isset($_REQUEST['spcId'])) {
	Template::errorDialog(E_MSG_TRY_AGAIN, 'javascript: window.close()');
   	exit;
}
$SupplierCustomer = Object::load('SupplierCustomer', $_REQUEST['spcId']);
if (Tools::isEmptyObject($SupplierCustomer)) {
    Template::errorDialog(E_MSG_TRY_AGAIN, 'javascript: window.close();', BASE_POPUP_TEMPLATE);
  	exit;
}

$title = '<table border="0" cellspacing="0" cellpadding="5" class="MenuContainer" width="30%">
        <tr>
			<td class="Title">
		    <img src="images/arrow.gif" border="0" alt=">">&nbsp;<b>'
        . sprintf(_('List of available credit notes for customer %s for payment of %s'),
		        Tools::getValueFromMacro($SupplierCustomer, '%Customer.Name%'),
		        '<script type="text/javascript">displaySelectedInvoice();</script>.')
        . '<br>&nbsp;'
        . sprintf(_('Total incl. VAT to pay for selected invoices: EUR %s'),
            isset($_REQUEST['RemainingPaymentPriceTTC'])?$_REQUEST['RemainingPaymentPriceTTC']:'0')
		. '</b><br><br>
		   </td>
		</tr>
      </table>';

$filter = SearchTools::filterAssembler(
    array(
        SearchTools::NewFilterComponent('RemainingTTC', '', 'GreaterThan', 0, 1),
        SearchTools::NewFilterComponent('SupplierCustomer', '', 'Equals',
            $SupplierCustomer->getId(), 1)
    )
);
$toHaveMapper = Mapper::singleton('ToHave');
$ToHaveCollection = $toHaveMapper->loadCollection($filter);

$grid = new Grid();
$grid->withNoSortableColumn = true;
$grid->NewAction('JS', array('jsActionArray' => array('updateOpener()')));
$grid->NewAction('Close');

//  Colonnes du grid
$grid->NewColumn('FieldMapper', _('Credit note number'), array('Macro' => '%DocumentNo%'));
$grid->NewColumn('FieldMapper', _('Date'),
    array('Macro' => '%EditionDate|formatdate@DATE_SHORT%' .
    '<input type="hidden" name="RemainingTTC[]" ' .
    'value="%RemainingTTC|formatnumber%">'));
$grid->NewColumn('MultiPrice', _('Amount excl. VAT'), array('Method' => 'getTotalPriceHT'));
$grid->NewColumn('ToHaveTVA', _('VAT'));
$grid->NewColumn('MultiPrice', _('Amount incl. VAT'), array('Method' => 'getTotalPriceTTC'));
$grid->NewColumn('MultiPrice', _('Remaining amount incl. VAT'), array('Method' => 'getRemainingTTC'));

$Order = array('EditionDate' => SORT_ASC);

$result = $grid->render($ToHaveCollection, false, array(), $Order);
$JSRequirements = array('js/lib-functions/FormatNumber.js',
		'js/includes/PaymentToHaveList.js');

// Pour cocher les items selectionnes precedemment si re-ouverture du popup
$js = '<script type="text/javascript">SelectGridItems();	</script>';

Template::page(
    _('List of credit notes'),
    $title . '<form>' . $result . '</form>' . $js,
    $JSRequirements,
    array(),
    BASE_POPUP_TEMPLATE
);

?>
