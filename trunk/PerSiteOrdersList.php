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
 * @version   SVN: $Id: CommandList.php 291 2008-12-16 17:26:10Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */


require_once('config.inc.php');
require_once('Objects/Command.php');
require_once('Objects/Command.const.php');
//require_once('SQLRequest.php');
require_once('MixedObjects/ProductStyle.php');

$auth = Auth::Singleton();
//Database::connection()->debug = true;
$auth->checkProfiles();
$ProfileId = $auth->getProfile();
$UserConnectedActorId = $auth->getActorId();
$uac = $auth->getUser();
$siteIds = $uac->getSiteCollectionIds();

$FilterComponentArray = array(); // Tableau de filtres

// on n'affiche pas les devis
$FilterComponentArray[] = SearchTools::newFilterComponent('IsEstimate', '', 'Equals', 0, 1);

$smarty = new Template();

/*  Contruction du formulaire de recherche */
$form = new SearchForm('ProductCommand');

// $form->customizationEnabled = true;  // les criteres de recherches sont desactivables

$form->addElement('text', 'Destinator', _('Destinator'), array(), array('Path' => 'Destinator.name'));
$form->addElement('text', 'Expeditor', _('Expeditor'), array(), array('Path' => 'Expeditor.Name'));

$CommandStateArray = array('##'=>_('Select one or more states')) + Command::getStateConstArray();
$form->addElement('select', 'State', _('State'), array($CommandStateArray, 'multiple="multiple" size="5"'));

$seasonArray = SearchTools::createArrayIDFromCollection('RTWSeason', array(), MSG_SELECT_AN_ELEMENT);
$form->addElement('select', 'Season', _('Season'), array($seasonArray), array('Disable'=>true));


$defaultValues = $form->getDefaultValues();
// Valeur par defaut: checked pour Non cloture
// array_merge pour ne pas ecraser les defaultvalues issues du date2dateElement

/*  Affichage du Grid  */
if (true === $form->displayGrid()) {

    /*  Construction du filtre  */
    $FilterComponentArray = array_merge($FilterComponentArray,
        $form->buildFilterComponentArray());


    $season = SearchTools::requestOrSessionExist('Season');
    if ($season !== false && $season != '##') {
        $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Season',
            'CommandItem()@ProductCommandItem.Product@RTWProduct.Model.Season.Id',
            'Equals', $season, 1, 'Command');
    }

    $Filter = SearchTools::filterAssembler($FilterComponentArray);
    $returnURL = basename($_SERVER['PHP_SELF']);

    define('COMMAND_EVENT_LIST_ITEMPERPAGE', 50);

    $grid = new Grid();
	$grid->customizationEnabled = true;
	$grid->javascriptFormOwnerName = 'PerSiteOrdersList';  // Pour ne pas avoir d'erreur js

    // mettre ici: [nb de colonnes du SubGrid] (pour affichage correct)
    // deprecated ? 
    //$grid->setNbSubGridColumns(-1);

    $grid->itemPerPage = COMMAND_EVENT_LIST_ITEMPERPAGE;
    $grid->NewAction('Print');
    $grid->NewAction('Export', array('FileName' => 'Commandes'));

    $grid->NewColumn('CommandProduct', _('Order'),
        array('Sortable' => false));
    $grid->NewColumn('FieldMapper', _('Date'),
        array('Macro' => '%CommandDate|formatdate@DATE_SHORT%'));
    $grid->NewColumn('FieldMapper', _('Wished Date'),
        array('Macro' => '%WishedStartDate|formatdate@DATE_SHORT%'));
    $grid->NewColumn('FieldMapper', _('Stock Exit Date'),
        array('Macro' => '%StockExitDate|formatdate@DATE_SHORT%','Sortable' => false));
    $grid->NewColumn('FieldMapperWithTranslation', _('State'),
        array('Macro' => '%State%','TranslationMap' => $ShortCommandStateArray));
    $grid->NewColumn('MultiPrice', _('Amount incl. VAT'),
        array('Method' => 'getTotalPriceTTC', 'Sortable' => false,
        'DataType' => 'numeric'));
    $grid->NewColumn('FieldMapper', _('Shipper'),
        array('Macro' => '%Expeditor.Name%'));
    $grid->NewColumn('FieldMapper', _('Addressee'),
        array('Macro' => '%Destinator.Name%'));
    $grid->NewColumn('FieldMapper', _('Addressee site'),
        array('Macro' => '%DestinatorSite.Name%'));
    $grid->NewColumn('FieldMapper', _('Country'),
        array('Macro' => '%DestinatorSite.Country%'));
    $grid->NewColumn('FieldMapper', _('City'),
        array('Macro' => '%DestinatorSite.CityName%'));
    $cols = array(_('Style'),_('Press name'),
        _('Description'), _('Quantity'));
    $grid->NewColumn('FieldMapper', _('Total quantity'),
        array('Macro' => '%TotalQuantity%', 'Sortable' => false,
        'DataType' => 'numeric'));

    // Colonne Custom pour le détail regrouppé par modèle ...
    $grid->NewColumn('CommandRTWModelList', $cols, array(
            'Sortable' => false)
    );

    $order = array('WishedStartDate' => SORT_DESC);
    $form->displayResult($grid, true, $Filter, $order);
} // fin DisplayGrid

else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}
?>
