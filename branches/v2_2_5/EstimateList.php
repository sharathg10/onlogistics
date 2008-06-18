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
require_once('Objects/Command.php');
require_once('Objects/Command.const.php');

// auth
$auth = Auth::Singleton();
$auth->checkProfiles();

// tableau des filtres
$filterArray = array();

// filtre par défaut, on n'affiche que les devis
$filterArray[] = new FilterComponent(
    new FilterRule('IsEstimate', FilterRule::OPERATOR_EQUALS, 1)
);

// gestion des droits pour l'admin des ventes il ne voit que les devis
// enregistrées par son acteur
$pfID  = $auth->getProfile();
$actID = $auth->getActorId();
if ($pfID == UserAccount::PROFILE_ADMIN_VENTES || 
    $pfID == UserAccount::PROFILE_AERO_ADMIN_VENTES)
{
    $filterArray[] = new FilterComponent(
        new FilterRule('Expeditor', FilterRule::OPERATOR_EQUALS, $actID),
        new FilterRule('Destinator', FilterRule::OPERATOR_EQUALS, $actID),
        new FilterRule('Customer', FilterRule::OPERATOR_EQUALS, $actID),
        FilterComponent::OPERATOR_OR
    );
}

$smarty = new Template();

// Contruction du formulaire de recherche
$form = new SearchForm('Command');
$form->addElement('text', 'CommandNo', _('Estimate number'));
$form->addElement('text', 'CommandCommandNo', _('Command number'),
    array('Path' => 'Command.CommandNo'));
$form->addElement('text', 'BaseReference', _('Product reference'), array(),
    array('Path' => 'CommandItem().Product.BaseReference'));
$form->addElement('text', 'Destinator', _('Customer'), array(),
    array('Path' => 'Destinator.Name'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by estimate date'),
    array('', 'onClick="$(\\\'Date1\\\').'
        . 'style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));
$form->addDate2DateElement(
    array('Name'=>'StartCommandDate', 'Path'=>'CommandDate'),
    array('Name'=>'EndCommandDate', 'Path'=>'CommandDate'),
    array(
        'EndDate'=>array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')),
        'StartDate'=>array('Y'=>date('Y'))
    )
);
$defaultValues = $form->getDefaultValues();

// Affichage du Grid
if (true === $form->displayGrid()) {
    // Si recherche lancee sans critere de date, il faut tuer la var de session
    // pour la case a cocher
    if (isset($_REQUEST['formSubmitted']) && !isset($_REQUEST['DateOrder1'])) {
        unset($_SESSION['DateOrder1']);
    }
    //  Construction du filtre
    $filter = SearchTools::filterAssembler(
        array_merge($filterArray, $form->buildFilterComponentArray())
    );
    $retURL = basename($_SERVER['PHP_SELF']);
    $grid = new Grid();
    // mettre ici: [nb de colonnes du SubGrid] (pour affichage correct)
    $grid->itemPerPage = 50;

    $grid->newAction('Redirect', array(
        'Caption' => A_DELETE,
        'URL' => 'CommandDelete.php?CommandId=%d&returnURL=' . $retURL
    ));
    $grid->newAction('Redirect', array(
        'Caption' => _('Print estimate'),
        'Title' => _('Print estimate document'),
        'TargetPopup' => true,
	    'URL' => 'EstimateEdit.php?estId=%d',
        'ReturnURL' => 'javascript:window.close();'
    ));

    $grid->newColumn('FieldMapper', _('Estimate number'),
        array('Macro' => '<a href="EstimateToOrder.php?estId=%Id%">%CommandNo%</a>'));
    $grid->newColumn('FieldMapper', _('Customer'), 
        array('Macro' => '%Destinator.Name%'));
    $grid->newColumn('MultiPrice', _('Amount excl. VAT'),
        array('Method' => 'getTotalPriceHT', 'Sortable' => false, 
              'DataType' => 'numeric'));
    $grid->newColumn('MultiPrice', _('Amount incl. VAT'),
        array('Method' => 'getTotalPriceTTC', 'Sortable' => false,
              'DataType' => 'numeric'));
    $order = array('CommandDate' => SORT_DESC);

    $form->displayResult($grid, true, $filter, $order);
} else {
    // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}

?>
