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

$auth = Auth::Singleton();
$auth->checkProfiles();

$ProfileId = $auth->getProfile();
$UserConnectedActorId = $auth->getActorId();

$FilterComponentArray = array();  // Tableau de filtres
// Acces aux magasins dont il est proprietaire du stock
if ($ProfileId == UserAccount::PROFILE_SUPPLIER_CONSIGNE) {
	$filter = new FilterComponent();
	$filter->setItem(
            new FilterRule(
                'Inventory.StorageSite.StockOwner',
                FilterRule::OPERATOR_EQUALS,
				$UserConnectedActorId)
    );
	$filter->operator = FilterComponent::OPERATOR_AND;
	$FilterComponentArray[] = $filter;
}
else if ($ProfileId == UserAccount::PROFILE_GESTIONNAIRE_STOCK
        || $ProfileId == UserAccount::PROFILE_TRANSPORTEUR) {
        // Acces aux magasins dont il est proprietaire
	$filter = new FilterComponent();
	$filter->setItem(
            new FilterRule(
                'Inventory.StorageSite.Owner',
                FilterRule::OPERATOR_EQUALS,
				$UserConnectedActorId)
);
	$filter->operator = FilterComponent::OPERATOR_AND;
	$FilterComponentArray[] = $filter;
}

/*  Contruction du formulaire de recherche */
$form = new SearchForm('InventoryDetail');
$form->addElement('text', 'StorageSiteName', _('Site'), array(),
        array('Path' => 'Inventory.StorageSiteName'));
$form->addElement('text', 'ProductReference', _('Reference'));
$form->addElement('text', 'StoreName', _('Store'), array(),
        array('Path' => 'Inventory.StoreName'));
$form->addElement('text', 'UserName', _('User'), array(),
        array('Path' => 'Inventory.UserName'));
$form->addElement('text', 'LocationName', _('Location'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
				  array('', 'onClick="$(\\\'Date1\\\').style.display=this.checked?'
                        . '\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name'   => 'BeginDate', 'Path' => 'Inventory.BeginDate'),
		array('Name'   => 'EndDate', 'Path' => 'Inventory.EndDate'),
		array('EndDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')),
		      'BeginDate' => array('Y'=>date('Y')))
);


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
	/*  Construction du filtre  */
    $FilterComponentArray = array_merge(
            $FilterComponentArray, $form->buildFilterComponentArray());
    //  Création du filtre complet
	$filter = SearchTools::filterAssembler($FilterComponentArray);

    // si recherche lancee sans critere de date, il faut tuer la var de
    // session pour la case a cocher
	if (isset($_POST['formSubmitted']) && !isset($_POST['DateOrder'])) {
	    unset($_SESSION['DateOrder']);
	}

	$grid = new Grid();
	$grid->itemPerPage = 100;
	$grid->withNoCheckBox = true;
	$grid->NewAction('Print');
	$grid->NewAction('Export', array('FileName' => _('Inventory')));

	$grid->NewColumn('FieldMapper', _('Inventory'),
            array('Macro' => '%Inventory.Id%', 'Sortable' => false));
	$grid->NewColumn('FieldMapper', _('End date'),
            array('Macro' => '%Inventory.EndDate|formatdate@DATE_SHORT%'));
	$grid->NewColumn('FieldMapper', _('User'),
            array('Macro' => '%Inventory.UserName%'));
	$grid->NewColumn('FieldMapper', _('Site'),
            array('Macro' => '%Inventory.StorageSiteName%'));
	$grid->NewColumn('FieldMapper', _('Store'),
            array('Macro' => '%Inventory.StoreName%'));
	$grid->NewColumn('FieldMapper', _('Location'),
            array('Macro' => '%LocationName%'));
	$grid->NewColumn('FieldMapper', _('Product'),
            array('Macro' => '%ProductReference%'));
	$grid->NewColumn('FieldMapper', _('Qty'),
            array('Macro' => '%Quantity|formatnumber@3@1%', 'DataType' => 'numeric'));
	$grid->NewColumn('FieldMapper', _('Purchase unit price excl. VAT'),
            array('Macro' => '%BuyingPriceHT|formatnumber% %Currency%'));

	$order = array('Inventory.EndDate' => SORT_DESC);

	$form->displayResult($grid, true, $filter, $order);
}

else { // on n'affiche que le formulaire de recherche, pas le Grid
	Template::page('', $form->render() . '</form>');
}
?>