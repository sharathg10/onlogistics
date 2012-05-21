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
 * @version   SVN: $Id: config.inc.php 45 2008-06-19 08:15:01Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once 'config.inc.php';
require_once 'DocumentGenerator.php';

$auth = Auth::singleton();
$auth->checkProfiles(array(
    UserAccount::PROFILE_ADMIN,
    UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
    UserAccount::PROFILE_RTW_SUPPLIER,
));

$productInfo = array();

if (isset($_REQUEST['cmdId'])) {
    $order = Object::load('Command', $_REQUEST['cmdId']);
    if (!$order instanceof Command) {
        Template::errorDialog(MSG_SELECT_AN_ELEMENT);
        exit(1);
    }
    $cmiCol = $order->getCommandItemCollection();
    foreach ($cmiCol as $cmi) {
        $product  = $cmi->getProduct();
        //if (!$product instanceof RTWProduct) {
        //    continue;
        //}
        $quantity = $cmi->getQuantity();
        $productInfo[] = array($product, $quantity);
    }

} else if (isset($_REQUEST['pdtIds'])) {
    foreach ($_REQUEST['pdtIds'] as $id) {
       	$product = Object::load('RTWProduct', $id);
   	if (!$product instanceof RTWProduct) {
           $product = Object::load('Product', $id);
	   if (!$product instanceof Product) {
	      continue;
	   }
        }
        $fname = 'qty_' . $id;
        $quantity = isset($_SESSION[$fname]) && is_numeric($_SESSION[$fname]) ?
            $_SESSION[$fname] : 1;
        $productInfo[] = array($product, $quantity);
    }
}

if (!count($productInfo)) {
    Template::errorDialog(E_NO_RECORD_FOUND);
    exit(1);
}

$gen = new ProductLabelGenerator($productInfo);
$pdf = $gen->render();
$pdf->output();

?>
