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
require_once('GetTotalCaByActorAndDates.php');
require_once('Objects/Command.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles();
$userConnectedActorId = $Auth->getActorId();

$FilterComponentArray = array();

/*  Contruction du formulaire de recherche */
$form = new SearchForm('Category');

$CategoryArray = SearchTools::createArrayIDFromCollection(
        'Category', array(), _('Select one or more categories'));
$form->addElement('select', 'Id', _('Category'),
        array($CategoryArray, 'multiple size="5"'), array());
$form->addElement('select', 'CommandType', _('Type of order'),
        array(Command::getTypeConstArray()), array('Disable'=>true));
$currencyArray = SearchTools::createArrayIDFromCollection(
    'Currency');
$form->addElement('select', 'Currency', _('Currency'), array($currencyArray), array('Disable'=>true));

$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
        array('', 'onClick="$(\\\'Date1\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(array('Name'   => 'StartDate',
								 'Format' => array('minYear' => date('Y') - 2),
								 'Disable' => true),
						   array('Name'   => 'EndDate',
								 'Format' => array('minYear' => date('Y') - 2),
								 'Disable' => true),
						  array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
					   			'StartDate' => array('Y' => date('Y')))
						  );

$form->addElement('checkbox', 'NoNullCommands',
				  _('Only categories having at least an order'),
				  array(), array('Disable' => true));

$defaultCurrency = Object::load('Currency', array('Name'=>'Euro'));
$defaultValues = array();
$defaultValues['Currency'] = $defaultCurrency->getId();
// array_merge pour ne pas perdre les valeurs par défaut des dates passées dans 
// addDate2DateElement()
$form->setDefaultValues(array_merge($form->getDefaultValues(), $defaultValues));

/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
    // Evite les interaction entre $_POST et $_SESSION
	SearchTools::cleanCheckBoxDataSession(array('NoNullCommands', 'DateOrder1'));

	$StartDate = SearchTools::requestOrSessionExist('StartDate');
	$MySQLStartDate = DateTimeTools::quickFormDateToMySQL('StartDate') . ' 00:00:00';
	$EndDate = SearchTools::requestOrSessionExist('EndDate');
	$MySQLEndDate = DateTimeTools::quickFormDateToMySQL('EndDate') . ' 23:59:59';
	$commandType = SearchTools::requestOrSessionExist('CommandType');
	$currency = SearchTools::requestOrSessionExist('Currency');

	list($ca_ht_num, $ca_ht_str) = getTotalCaByActorAndDates(
            $userConnectedActorId, $MySQLStartDate, $MySQLEndDate, $commandType, $currency, true);
	if (SearchTools::requestOrSessionExist('NoNullCommands')) {
		$categoryWithCommandArray = getCategoryWithCommands(
                $userConnectedActorId, $MySQLStartDate, $MySQLEndDate, $commandType, $currency);
	    $FilterComponentArray[] = SearchTools::NewFilterComponent(
                'Id', '', 'In', $categoryWithCommandArray, 1);
	}


	/*  Construction du filtre  */
	$FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray());
	$Filter = SearchTools::filterAssembler($FilterComponentArray);

	define('CATEGORY_LIST_ITEM_PER_PAGE', 100);
	$grid = new Grid();
	$grid->itemPerPage = CATEGORY_LIST_ITEM_PER_PAGE;
	$grid->withNoCheckBox = true;

	$grid->NewAction('Print', array());
	$grid->NewAction('Export', array('FileName' => 'CAparCategorie'));

	$grid->NewColumn('FieldMapper', _('Name'), array('Macro' => '%Name%'));
	// Nombre de commandes
	$grid->NewColumn('BoardCategory', _('Number of orders'),
					 array('req' => 'command_num',
					 	   'start' => $MySQLStartDate, 'end' => $MySQLEndDate,
					 	   'commandType' => $commandType, 'currency'=>$currency));
	// ca par client
	$grid->NewColumn('BoardCategory', _('Turnover excl. VAT'),
					 array('req' => 'ca_par_cat',
					 	   'start' => $MySQLStartDate, 'end' => $MySQLEndDate,
					 	   'commandType' => $commandType, 'currency'=>$currency));
	// % ca par Category
	$grid->NewColumn('BoardCategory', _('% of total turnover'),
				array('req' => 'ca_percent', 'totalCa' => $ca_ht_num,
					  'start' => $MySQLStartDate, 'end' => $MySQLEndDate,
				 	   'commandType' => $commandType, 'currency'=>$currency));

	$grid->withNoSortableColumn = true;
	$Order = array('Name' => SORT_ASC);

	$form->displayResult($grid, true, $Filter, $Order, '', array(),
						 array('between' => $ca_ht_str));
} // fin DisplayGrid

else { // on n'affiche que le formulaire de recherche, pas le Grid
	Template::page('', $form->render() . '</form>');
}
?>
