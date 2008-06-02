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

require_once("config.inc.php");

$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
    array('showErrorDialog'=>true, 'debug'=>false));

if (!isset($_REQUEST["SaisId"]) && !isset($_REQUEST["prmId"])) {
    Template::errorDialog(_('An error occurred, please close this popup window.'), 'javascript:window.close();', BASE_POPUP_TEMPLATE);
	exit;
}

if (isset($_REQUEST["SaisId"])) {
	$Saisonality = Object::load("Saisonality", $_REQUEST["SaisId"]);
	$ProductIdArray = $Saisonality->GetProductCollectionIds();
	$Entity = _('seasonality');
}
if (isset($_REQUEST["prmId"])) {
	$Promotion = Object::load("Promotion", $_REQUEST["prmId"]);
	$ProductIdArray = $Promotion->GetProductCollectionIds();
	$Entity = _('offer on sale');
}

define('SAISONALITY_PROMOTION_PRODUCTLIST_ITEMPERPAGE', 350);
$grid = new Grid();
$grid->itemPerPage = SAISONALITY_PROMOTION_PRODUCTLIST_ITEMPERPAGE;
$grid->displayCancelFilter = false;
$grid->withNoCheckBox = true;

$grid->NewAction('Close');
$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%BaseReference%'));
$grid->NewColumn('FieldMapper', _('Designation'),array('Macro' => '%Name%'));

Template::pageWithGrid(
    $grid,
    'Product',
    sprintf(_('List of products assigned to %s'), $Entity),
    array('Id' => $ProductIdArray),
    array('BaseReference' => SORT_ASC),
    BASE_POPUP_TEMPLATE
);

?>
