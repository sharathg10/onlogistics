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
 * @version   SVN: $Id: SupplierDelayStock.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once('config.inc.php');

$tradeContext = Preferences::get('TradeContext', array());

$form = new SearchForm('Invoice');
$commercialList = SearchTools::createArrayIDFromCollection(
    'UserAccount',
    array('Profile' => UserAccount::PROFILE_COMMERCIAL)
);
$form->addElement('select', 'Salesman', 
    _('Salesman'),
    array($commercialList),
    array('Path' => 'Command.Commercial')
);
$currencyList = SearchTools::createArrayIDFromCollection('Currency');
$form->addElement('select', 'Currency', 
    _('Currency'),
    array($currencyList),
    array('Path' => 'Command.Currency.Id')
);
if (in_array('readytowear', $tradeContext)) {
    $seasonList = SearchTools::createArrayIDFromCollection(
        'RTWSeason',
        array(),
        MSG_SELECT_AN_ELEMENT
    );
    $form->addElement('select', 'Season', 
        _('Season'),
        array($seasonList),
        array('Path' => 'Command@ProductCommand.CommandItem().Product@RTWProduct.Model.Season.Id')
    );
    $form->addBlankElement();
}

$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
    array('', 'onClick="$(\\\'Date1\\\')'
            . '.style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));
$form->addDate2DateElement(
    array('Name' => 'StartDate', 'Path' => 'EditionDate'),
    array('Name' => 'EndDate', 'Path' => 'EditionDate'),
    array(
        'StartDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
        'EndDate'   => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
    )
);
$defaultValues = array();
$euro = Object::load('Currency', array('Name'=>'Euro'));
$defaultValues['Currency'] = ($euro instanceof Currency) ? $euro->getId() : 0;
$form->setDefaultValues(array_merge($form->getDefaultValues(), $defaultValues));

if (true === $form->displayGrid()) {
	$grid = new Grid();
	$grid->itemPerPage = 200;
	// Actions
	$grid->NewAction('Export', array('FileName' => 'commission_commercial'));
	$grid->NewAction('Print');
	// Colonnes
	$grid->NewColumn('FieldMapper', _('Salesman'), array('Macro'=>'%Command.Commercial.Identity%'));
	$grid->NewColumn(
		'FieldMapper',
		_('Date'), array('Macro' => '%EditionDate|formatdate%',
	    'Sortable' => false)
	);
	$grid->NewColumn('FieldMapper', _('Order'), array('Macro'=>'%Command.CommandNo%'));
	$grid->NewColumn('FieldMapper', _('Invoice'), array('Macro'=>'%DocumentNo%'));
	$grid->NewColumn('FieldMapper', _('Invoice total ht'), array('Macro'=>'%TotalPriceHT|formatnumber%'));
	$grid->NewColumn('FieldMapper', _('Commission percent'), array('Macro'=>'%CommercialCommissionPercent|formatpercent%'));
	$grid->NewColumn('FieldMapper', _('Commission amount'), array('Macro'=>'%CommercialCommissionAmount|formatnumber%'));
	$grid->NewColumn('FieldMapper', _('Currency'), array('Macro'=>'%Command.Currency.Name%'));

    $filter = SearchTools::FilterAssembler($form->buildFilterComponentArray());
	$order = array('EditionDate' => SORT_DESC);

	$form->displayResult($grid, true, $filter, $order);
} // fin affichage Grid

else {  // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}

?>
