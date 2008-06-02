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
require_once('SupplyingOptimizator.php');
require_once('LangTools.php');


$auth = Auth::Singleton();
$auth->checkProfiles();
$userId = $auth->getUserId();
$smarty = new Template();

//SearchTools::prolongDataInSession();

//$action = basename($_SERVER['PHP_SELF']);

/*  Si on a clique sur OK apres saisie*/
if (isset($_REQUEST['formSubmitted']) && $_REQUEST['formSubmitted'] == 1) {
//    Database::connection()->debug = true;
    // on sauve les preferences pour le user connecte si le formualaire vient
    // d'etre poste, et pas si rretour apres erreur sur l'ecran de cmde
    if (isset($_POST['Supplier'])) {
        PreferencesByUser::set('PassedWeekNumber', (int)$_POST['PassedWeekNumber'], $userId);
        PreferencesByUser::set('FutureWeekNumber', (int)$_POST['FutureWeekNumber'], $userId);
        PreferencesByUser::set('DefaultDeliveryDelay', (int)$_POST['DefaultDeliveryDelay'], $userId);
        PreferencesByUser::set('WithExtrapolation', isset($_POST['WithExtrapolation']), $userId);
        PreferencesByUser::save();
    }
    $supplierId = isset($_POST['Supplier'])?$_POST['Supplier']:$_SESSION['supplier'];
    $params = array('supplierId' => $supplierId);
    $optimizator = new SupplyingOptimizator($params);
    $data = $optimizator->getData();
    if ($data === false) {
        Template::errorDialog(_('No order found for last weeks.'), $_SERVER['PHP_SELF']);
		exit;
    }
    $data = $optimizator->renderData();

    $productInfoList = $data['ProductInfoList'];
    $smarty->assign('productInfoList', $productInfoList);
    $smarty->assign('substitutionInfoList', $data['SubstitutionInfoList']);
    $smarty->assign('orderedQtyPerWeek', $data['OrderedQtyPerWeek']);
    $smarty->assign('waitedQtyPerWeek', $data['WaitedQtyPerWeek']);
    $smarty->assign('qtyToOrder', $data['QtyToOrder']);

    $smarty->assign('DisplayForm', 'none');
    $smarty->assign('DisplayResult', 'block');
    // Attention, Supplier ou AeroSupplier!!
    $ActorMapper = Mapper::singleton('Actor');
    $supplier = $ActorMapper->load(array('Id' => $supplierId));
    $smarty->assign('supplierName', $supplier->getName());
    $futureWeekNumber = count($optimizator->wishedStartDates);
/*  Ceci fonctionne sur php 5.2.3 mais pas php 5.2.1 ....
    $smarty->assign(
        'wishedStartDateArray',
        array_map('I18n::formatDate',
                $optimizator->wishedStartDates,
                array_fill(0, $futureWeekNumber, I18n::DATE_LONG)));*/
    $wishedStartDateArray = array();
    foreach($optimizator->wishedStartDates as $date){
        $wishedStartDateArray[] = I18n::formatDate($date, I18n::DATE_LONG);
    }
    $smarty->assign(
        'wishedStartDateArray', $wishedStartDateArray);
    $smarty->assign('futureWeekNumber', $futureWeekNumber);
    $session = Session::singleton();
    // mise en session en vue de ProductCommandSupplier
    $session->register('supplier', $supplierId, 3);
    // mise en session pour ne pas refaire les calculs
    $session->register('qtyToOrder', $data['QtyToOrder'], 3);
    $session->register('wishedStartDates', $optimizator->wishedStartDates, 3);
}

/*  Si on a clique sur 'Sum needs' apres selection d'une date */
if (isset($_REQUEST['formSubmitted']) && $_REQUEST['formSubmitted'] == 2) {
    $pdtIds = $pdtQties = array();  // les Id de produit a commander, et leur qte
    // Determination des semaines a traiter en fonction de la date saisie
    $needsWeekNumber = 0;
    $wishedStartDatePerWeek = $_SESSION['wishedStartDates'];
    $selDate = $_POST['StartDate'];  // la date selectionnee
    
    // Si on saisit une date > la plus grde des dates de reappro, faut stopper la boucle
    while(isset($wishedStartDatePerWeek[$needsWeekNumber]) 
    && $wishedStartDatePerWeek[$needsWeekNumber] <= $selDate) {
        $needsWeekNumber++;
    }

    // Reordonne par rapport aux baseReference puis mise en session des pdtIds
    // en vue de ProductCommandSupplier
    $pdtIdArray = array_keys($_SESSION['qtyToOrder']);  // trié par id...
    $coll = Object::loadCollection('Product', array('Id' => $pdtIdArray),
            array('BaseReference' => SORT_ASC), array('Id'));
    $pdtIdArray = $coll->getItemIds();  // trie par BaseReference
    foreach($pdtIdArray as $pdtId) {
        $qtyArray = $_SESSION['qtyToOrder'][$pdtId];
        $pdtQty = array_sum(array_slice($qtyArray, 0, $needsWeekNumber + 1));
        if ($pdtQty != 0) {
            $pdtIds[] = $pdtId;
            $pdtQties[] = $pdtQty;
        }
    }
    if (empty($pdtIds)) {
        Template::errorDialog(_('No suggested quantity to order for the selected date.'),
                $_SERVER['PHP_SELF']);
		exit;
    }
    $session = Session::singleton();
    $session->register('pdt', $pdtIds, 3);
    $session->register('qty', $pdtQties, 3);
    Tools::redirectTo('ProductCommandSupplier.php?from=optimappro&StartDate='.$selDate.' 09:00:00');
    exit;
}

/*  Formulaire */
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
require_once('HTML/QuickForm.php');
$form = new HTML_QuickForm('SupplyingOptimization', 'post', $_SERVER['PHP_SELF']);
unset($form->_attributes['name']);  // xhtml compliant

$supplierArray = SearchTools::createArrayIDFromCollection(
        array('Supplier', 'AeroSupplier'), array('Active'=>1));
$form->addElement('select', 'Supplier', _('Supplier'), $supplierArray,
        'style="width:100%" id="Supplier"');
$form->addElement('text', 'PassedWeekNumber', _('Number of weeks in history'),
        'style="width:100%" id="PassedWeekNumber"');
$form->addElement('text', 'FutureWeekNumber', _('Number of weeks of forecast'),
        'style="width:100%" id="FutureWeekNumber"');
$form->addElement('text', 'DefaultDeliveryDelay', _('Default delivery within (days)'),
        'style="width:100%" id="DefaultDeliveryDelay"');
$form->addElement('checkbox', 'WithExtrapolation',
        _('Forecasts with extrapolation'));

$defaultValues = array(
    'PassedWeekNumber' => PreferencesByUser::get('PassedWeekNumber', 6, $userId),
    'FutureWeekNumber' => PreferencesByUser::get('FutureWeekNumber', 6, $userId),
    'DefaultDeliveryDelay' => PreferencesByUser::get('DefaultDeliveryDelay', 7, $userId),
    'WithExtrapolation' => PreferencesByUser::get('WithExtrapolation', 0, $userId)
);
// Si retour apres erreur depuis ProductCommandSupplier
if (isset($_SESSION['Supplier'])) {
    $defaultValues['Supplier'] = $_SESSION['Supplier'];
}
$form->setDefaults($defaultValues);
$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());

//$smarty->assign('dateFormat', I18N::getHTMLSelectDateFormat());
$pageContent = $smarty->fetch('Stock/SupplyingOptimization.html');

Template::page('', $pageContent,
		array('js/lib-functions/checkForm.js', 'js/includes/SupplyingOptimization.js',
        'js/jscalendar/calendar.js', getJSCalendarLangFile(),
        'js/jscalendar/calendar-setup.js'));

?>