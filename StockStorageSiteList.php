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
$Auth->checkProfiles();
$ProfileId = $Auth -> getProfile();
$UserConnectedActorId = $Auth->getActorId();
//Database::connection()->debug = true;

// nettoyage session
$n = SearchTools::getGridItemsSessionName('StorebyStorageSiteList.php');
if(isset($_SESSION[$n])) {
    unset($_SESSION[$n]);
}

$FilterComponentArray = array();  // Tableau de filtres
if (in_array($ProfileId, array(UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR))) {
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Owner', '', 'Equals', $UserConnectedActorId, 1);
}
elseif ($ProfileId == UserAccount::PROFILE_SUPPLIER_CONSIGNE) {
	$StorageSiteArrayId = array();
	$StorageSiteMapper = Mapper::singleton('StorageSite');
	$StorageSiteCollection = $StorageSiteMapper->loadCollection();
	for($i = 0; $i < $StorageSiteCollection->getCount(); $i++){
    	$item = $StorageSiteCollection->getItem($i);
		if ($item->ContendStoreWithStockOwner($UserConnectedActorId)) {
		    $StorageSiteArrayId[] = $item->getId();
		}
	}
	$FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Id', '', 'In', $StorageSiteArrayId, 1);
}

$form = new SearchForm('StorageSite');
$form->addElement('text', 'BaseReference', _('Reference'), array(),
    array('Path' => 'Store().Location().LocationProductQuantities().Product.BaseReference'));
$form->addElement('text', 'PdtName', _('Designation'), array(),
    array('Path' => 'Store().Location().LocationProductQuantities().Product.Name'));
$form->addElement('text', 'SerialNumber', _('SN / Lot'), array(),
    array('Path' => 'Store().Location().LocationConcreteProduct().ConcreteProduct.SerialNumber'));

if (in_array($Auth->getProfile(), array(UserAccount::PROFILE_OWNER_CUSTOMER, UserAccount::PROFILE_SUPPLIER_CONSIGNE))) {
    $owners = SearchTools::createArrayIDFromCollection(
            'Actor', array('Id' => $Auth->getActorId()));
}else {
    $owners = SearchTools::createArrayIDFromCollection(
            'Actor', array('Active' => 1),_('Select an actor'));
}
$form->addElement('select', 'Owner', _('Product owner'), array($owners),
    array('Path' => 'Store().Location().LocationProductQuantities().Product.Owner'));



/*  Affichage du Grid  */
if (true === $form->displayGrid(1)) {
    $FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray());
	$filter = SearchTools::filterAssembler($FilterComponentArray);

    define('STOCKSTORAGESITELIST_ITEMPERPAGE', 10);
    $grid = new Grid();
    $grid->itemPerPage = STOCKSTORAGESITELIST_ITEMPERPAGE;
    $grid->withNoCheckBox = true;
    $grid->displayCancelFilter = false;

    $grid->NewAction('Print', array());
    $grid->NewAction('Export', array('FileName' => 'SitesStock'));

    $grid->NewColumn('FieldMapper', _('Storage site'),
            array('Macro' => '<a href="StorebyStorageSiteList.php?stoId=%ID%&amp;' .
                'returnURL=StockStorageSiteList.php">%Name%</a>'));
    $grid->NewColumn('FieldMapper', _('Actor linked'),
            array('Macro' => '%Owner.Name%'));
    $grid->NewColumn('FieldMapper', _('Stock owner'),
            array('Macro' => '%StockOwner.Name%'));
    $grid->NewColumn('FieldMapper', _('City'),
            array('Macro' => '%CountryCity.CityName.Name%'));

    $order = array('Name' => SORT_ASC);
    $form->setDisplayForm(false);  // Par defaut, on n'affiche pas le form de rech
    $form->displayResult($grid, true, $filter, $order);
}

?>
