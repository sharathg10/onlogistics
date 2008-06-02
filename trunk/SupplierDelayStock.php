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
$ProfileId = $Auth->getProfile();
$Auth->checkProfiles();
$ConnectedActor = $Auth->getActor();

/*  Si on a valide les saisies  */
if (isset($_REQUEST['SupplierCustomerId'])) {
    // contient les Id des SupplierCustomer pour lesquels la saisie est incorrecte
    $ErrorIdArray = array();

	foreach($_REQUEST['SupplierCustomerId'] as $key => $IdValue){
		// TotalDeliveryDay et DeliveryType sont des chps obligatoires
		if ((!empty($_REQUEST['TotalDeliveryDay'][$key]) && $_REQUEST['DeliveryType'][$key] == -1)
			|| ($_REQUEST['TotalDeliveryDay'][$key] == '' && $_REQUEST['DeliveryType'][$key] != -1)
			|| ($_REQUEST['TotalDeliveryDay'][$key] == '' && $_REQUEST['DeliveryType'][$key] == -1)
				&& !empty($_REQUEST['MaxDeliveryDay'][$key])) {
		    $ErrorIdArray[] = $IdValue;
			continue;
		}

		$SupplierCustomer = Object::load('SupplierCustomer', $IdValue);
		if ($_REQUEST['MaxDeliveryDay'][$key] != '') {
		    $SupplierCustomer->setMaxDeliveryDay($_REQUEST['MaxDeliveryDay'][$key]);
		}
		if ($_REQUEST['TotalDeliveryDay'][$key] != '') {
		    $SupplierCustomer->setTotalDeliveryDay($_REQUEST['TotalDeliveryDay'][$key]);
		}
		if ($_REQUEST['DeliveryType'][$key] != -1) {
		    $SupplierCustomer->setDeliveryType($_REQUEST['DeliveryType'][$key]);
		}
        saveInstance($SupplierCustomer, 'SupplierDelayStock.php');
	}

	if (!empty($ErrorIdArray)) { // s'il y a eu des erreurs de saisie

		$ErrorMsge = _('Wrong data provided for the following suppliers') . ':<ul>';
		foreach($ErrorIdArray as $SupplierCustomerId) {
			$SupplierCustomer = Object::load('SupplierCustomer', $SupplierCustomerId);
			$ErrorMsge .= '<li>' . Tools::getValueFromMacro($SupplierCustomer, '%Supplier.Name%') . '</li>';
		}
		Template::errorDialog($ErrorMsge . '</ul>' .
            _('Please correct data provided for these suppliers') . '.',
					   'SupplierDelayStock.php');
        exit;
	}
}

$grid = new Grid();
$grid->withNoCheckBox = true;
$grid->displayCancelFilter = false;
$grid->paged = false;
$grid->withNoSortableColumn = true;
define('SUPPLIER_DELAY_LIST_ITEMPERPAGE', 200);
$grid->itemPerPage = SUPPLIER_DELAY_LIST_ITEMPERPAGE;

$grid->NewAction('Submit');

$grid->NewColumn('FieldMapper', _('Supplier'), array('Macro' => '%Supplier.Name%'));
$grid->NewColumn('FieldMapper', _('Maximum number of days acceptable for delivery'),
				 array('Macro' => '<input type="text" name="MaxDeliveryDay[]" size="10" value="%MaxDeliveryDay%" >'));
$grid->NewColumn('FieldMapper', _('Number of days for delivery'),
				 array('Macro' => '<input type="text" name="TotalDeliveryDay[]" size="10" value="%TotalDeliveryDay%" >'));
$grid->NewColumn('SupplierDelayStockAppro', _('Supplying mode'));

Template::pageWithGrid($grid, 'SupplierCustomer', '',
	array('Customer' => $ConnectedActor->getId()),
	array('Supplier.Name' => SORT_ASC)
);
?>
