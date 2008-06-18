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
$auth->checkProfiles(
    array(
        UserAccount::PROFILE_ADMIN,
        UserAccount::PROFILE_ADMIN_VENTES,
        UserAccount::PROFILE_AERO_ADMIN_VENTES
    )
);
$session = Session::Singleton();

if(!isset($_REQUEST['nosearchform'])) {
    // form de recherche
    $form = new SearchForm('Invoice');
    $actorArray = SearchTools::CreateArrayIDFromCollection('Actor', array(),
        _('Select a customer'), 'Name');
    $form->addElement('select', 'Destinator', _('Customer'),
        array($actorArray, 'multiple size="5"'),
        array('Path' => 'Command.Destinator.Id'));
    $currencyArray = SearchTools::CreateArrayIDFromCollection('Currency',
        array(), '', 'ShortName');
    $form->addElement('select', 'Currency', _('Currency'), array($currencyArray));
    $form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
        array('',
        'onClick="$(\\\'Date1\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'
    ));
    $startDate = mktime(0, 0, 0, date('m')-1 , date('d'), date('Y'));
    $form->addDate2DateElement(
        array(
            'Name'   => 'StartDate',
            'Path' => 'PaymentDate'),
        array(
            'Name'   => 'EndDate',
            'Path' => 'PaymentDate'),
        array());

    $curMapper = Mapper::singleton('Currency');
    $currency = $curMapper->load(array('Name'=>'Euro'));
    $defaultValues['Currency'] = $currency->getId();
    $defaultValues['StartDate'] = array(
        'd' => date('d', $startDate),
        'm' => date('m', $startDate),
        'Y' => date('Y', $startDate));
    $defaultValues['EndDate'] = array(
        'd' => date('d'),
        'm' => date('m'),
        'Y' => date('Y'));
    $form->setDefaultValues($defaultValues);
} else {
    $customer = Object::load('Actor', $_REQUEST['cust']);
}

// Affichage du Grid
if (isset($_REQUEST['nosearchform']) || true === $form->DisplayGrid()) {
    /* si recherche lancee sans critere de date,
    il faut tuer la var de session pour la case a cocher */
    if (isset($_REQUEST['formSubmitted']) && !isset($_REQUEST['DateOrder1'])) {
        unset($_SESSION['DateOrder1'], $_SESSION['beginDate'], $_SESSION['endDate']);
    } else {
        if(isset($_REQUEST['StartDate']) && isset($_REQUEST['EndDate'])) {
            // on met les critère de dates en session pour l'edition du doc
            $beginDate = $_REQUEST['StartDate']['d'] . '/' .
                $_REQUEST['StartDate']['m'] . '/' . $_REQUEST['StartDate']['Y'];
            $endDate = $_REQUEST['EndDate']['d'] . '/' .
                $_REQUEST['EndDate']['m'] . '/' . $_REQUEST['EndDate']['Y'];
            $session->register('beginDate', $beginDate, 2);
            $session->register('endDate', $endDate, 2);
        }
    }
	// Construction du filtre
	$FilterComponentArray = array();
    $FilterComponentArray[] = SearchTools::NewFilterComponent('ToPay', '',
        'GreaterThan', 0, 1);

    if(!isset($_REQUEST['nosearchform'])) {
        $FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray());
	    $Order = array('Command.Destinator.Name' => SORT_ASC);
    } else {
        $FilterComponentArray[] = SearchTools::NewFilterComponent('PaymentDate', '',
            'LowerThan', date('Y-m-d 23:59:59', mktime()), 1);
        $FilterComponentArray[] = SearchTools::NewFilterComponent('Customer',
            'Command.Destinator.Id', 'Equals', $customer->getId(), 1, 'Invoice');
        $FilterComponentArray[] = SearchTools::NewFilterComponent('Currency',
            'Currency', 'Equals', $customer->getCurrencyId(), 1);
        $Order = array('PaymentDate' => SORT_ASC);
    }
	$Filter = SearchTools::filterAssembler($FilterComponentArray);

    if(!isset($_REQUEST['nosearchform'])) {
	    $grid = new Grid();
	    $grid->itemPerPage = 300;

	    // actions
	    $grid->NewAction('Redirect', array(
            'Caption' => _('Simple statement of invoices'),
            'TargetPopup' => true,
	        'URL' => 'EditInvoicesList.php',
	        'TransmitedArrayName' => 'ivId'));
	    $grid->NewAction('Redirect', array(
	        'Caption' => _('Statement of invoices with letter of exchange.'),
            'TargetPopup' => true,
	        'URL' => 'EditInvoicesList.php?full=1',
	        'TransmitedArrayName' => 'ivId'));
	    $grid->NewAction('Print');
        $grid->NewAction('Export', array('FileName'=>'BalanceAgee'));
        $grid->NewAction('Close');

	    // colonnes
	    $grid->NewColumn('FieldMapper', _('Customer'),
	        array('Macro' => '%Command.Destinator.Name%'));
	    $grid->NewColumn('FieldMapper', _('Currency'),
	        array('Macro' => '%Currency.Symbol%'));
	    $grid->NewColumn('FieldMapper', _('Number'),
        	array('Macro' => '%DocumentNo%'));
	    $grid->NewColumn('FieldMapper', _('Edition date'),
        	array('Macro' => '%EditionDate|formatdate@DATE_SHORT%'));
	    $grid->NewColumn('FieldMapper', _('Payment date'),
        	array('Macro' => '%PaymentDate|formatdate@DATE_SHORT%'));
	    $grid->NewColumn('FieldMapper', _('Remaining to pay'),
	        array('Macro' => '%ToPay|formatnumber%'));
    }
    // tableau récapitulatif par devise
    $content = array();
    $content['afterGrid'] = '';
    $content['between'] = '';
    $ivMapper = Mapper::singleton('Invoice');
    $ivCol = $ivMapper->loadCollection($Filter);

    $count = $ivCol->getCount();
    $total = array();
    $do = array();
    $index = 0;
    for ($i=0 ; $i<$count ; $i++) {
        $iv = $ivCol->getItem($i);
        $month = $iv->getPaymentDate('m');
        $year = $iv->getPaymentDate('Y');
        $time = mktime(0,0,0, $month+1, 0, $year);
        $nbDays = ceil(ceil((mktime() - $time) / (60*60*24)) / 30) * 30;
        $date=I18N::formatdate($time, I18N::DATE_LONG) .
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $nbDays . ' ' . _('days');
        $currency = $iv->getCurrency();
        if($currency instanceof Currency) {
            $symbol = $currency->getSymbol();
            if(!isset($total[$symbol])) {
                $total[$symbol] = array();
            }
            if(!isset($total[$symbol][$date])) {
                $total[$symbol][$date] = 0;
            }
            $total[$symbol][$date] += $iv->getToPay();
        }
    }
    $smarty = new Template();
    $smarty->assign('totals', $total);
    if(isset($_REQUEST['nosearchform'])) {
        $smarty->assign('displayCloseButton', true);
    }
    $fetch = $smarty->fetch('AgedTrialBalance/TotalTable.html');

    // Affichage
    if(!isset($_REQUEST['nosearchform'])) {
        $content['afterGrid'] = $fetch;
        $content['between'] = $fetch;
        $form->displayResult($grid, true, $Filter, $Order, '', array(), $content);
    } else {
        // Affiche juste le grid dans une fenêtre popup
        Template::page('Carramba', $fetch, array(), array(), BASE_POPUP_TEMPLATE);
    }
} else {
	Template::page('', $form->render() . '</form>');
}
?>
