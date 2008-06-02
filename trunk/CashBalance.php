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

$auth = Auth::Singleton();
$auth->checkProfiles();
SearchTools::cleanDataSession('noPrefix');

//  Contruction du formulaire de recherche {{{
$form = new SearchForm('ForecastFlow');

$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
    array('', 'onclick="$(\\\'Date1\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));
$form->addDate2DateElement(
    array('Name' => 'bDate'),
    array('Name' => 'eDate'), array());
$currencyArray = SearchTools::CreateArrayIDFromCollection('Currency', array(),
    '', 'ShortName');
$form->addElement('select', 'Currency', _('Currency'), array($currencyArray));
$accountingType = SearchTools::CreateArrayIDFromCollection('AccountingType', array(), 
    MSG_SELECT_AN_ELEMENT);
$form->addElement('select', 'AccountingType', _('Accounting type'), array($accountingType));
$form->addElement('select', 'CurrencyConverter', _('Convert to currency'), 
        array(array('##' => MSG_SELECT_AN_ELEMENT) + $currencyArray ), array('Disable' => true));

// valeurs par défaut du formulaire
$curMapper = Mapper::singleton('Currency');
$currency = $curMapper->load(array('Name'=>'Euro'));
$defaultValues['Currency'] = $currency->getId();
$defaultValues['bDate'] = array('Y'=>date('Y'));
$defaultValues['eDate'] = array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y'));
$form->setDefaultValues($defaultValues);
// }}}

/* Gestion de la recherche */
$content = '';
$title = _('Cash flow');
if (true === $form->displayGrid() || isset($_REQUEST['Export'])) {
    $accountingType = false;
    if(isset($_REQUEST['AccountingType']) && $_REQUEST['AccountingType'] != '##') {
        $accountingType = $_REQUEST['AccountingType'];
    }
    require_once('CashBalanceManager.php'); 
    $cm = new CashBalanceManager(array(
        'beginDate' => $_REQUEST['bDate'], 
        'endDate' => $_REQUEST['eDate'],
        'currency' => $_REQUEST['Currency'],
        'toCurrency' => $_REQUEST['CurrencyConverter'],
        'accountingType' => $accountingType)
    );
    
    $url = $_SERVER['PHP_SELF'];
    // Controle du creneau par rapport aux CurrencyConverter definis
    if ($cm->toCurrency !== false) {
        $check = $cm->checkConverter();
    }
    
    // export des données
    if (isset($_POST['Export'])) {
        $cm->process();
        $data = $cm->toCSV();
        header('Pragma: public');
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment;filename=cashBalance.csv');
        echo $data;
        exit;
    }
    // affichage
    $cm->process(true); // true pour ne pas utiliser les données en cache
    $content = $cm->toHTML();
    
    $monthes = I18N::getMonthesArray();
    $title = sprintf(_('Cash flow from %s %s to %s %s'), 
        $monthes[$_REQUEST['bDate']['m']],
        $_REQUEST['bDate']['Y'],
        $monthes[$_REQUEST['eDate']['m']],
        $_REQUEST['eDate']['Y']);
    if($accountingType!=false) {
        $obj = Object::load('AccountingType', array('Id'=>$accountingType));
        $title .= ' to the accounting type ' . $obj->getType();
    }

}
// Affichage
Template::page($title, $form->render() . $content . '</form>');
?>