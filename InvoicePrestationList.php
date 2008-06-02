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
require_once('Objects/MovementType.const.php');
require_once('InvoicePrestationTools.php');
require_once('PrestationManager.php');
require_once('Objects/Task.const.php');
require_once('LangTools.php');
//includeSessionRequirements();

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

// Cleanage sessions
PrestationManager::cleanSession();

// id du client à facturer
$customerId = 0;
if(isset($_POST['Customer'])) {
    $customerId = $_POST['Customer'];
} elseif(isset($_SESSION['Customer'])) {
    $customerId = $_SESSION['Customer'];
}
Session::register('Customer', $customerId, 2);

$pageTitle = _('Billing criterion');
$actorArray = SearchTools::createArrayIDFromCollection('Actor',
    array('Active'=>1, 'Generic'=>0), _('Select a customer'), 'Name');

// construction du formulaire de recherche {{{
$form = new SearchForm('Prestation');
$form->addElement('select', 'Customer', _('Customer to charge'),
    array($actorArray, 'style="width:100%;" ' .
    'onchange="fw.ajax.updateSelect(\'customer\', \'prestationcustomer\', \'PrestationCustomer\', \'Actor\');'.
    '"')
);
$form->addBlankElement();
$form->addElement('date', 'StartDate', _('From'), array(
    'format' => I18N::getHTMLSelectDateFormat(),
    'minYear' => date('Y')-4,
    'maxYear' => date('Y')+1,
    'Value'   => array('StartDate'=>array('m'=>date('m'), 'Y'=>date('Y')))));
$form->addElement('date', 'EndDate', _('to'), array(
    'format' => I18N::getHTMLSelectDateFormat(),
    'minYear' => date('Y')-4,
    'maxYear' => date('Y')+1,
    'Value'   => array('EndDate'=>array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')))));

// partie recherche détaillée
$smarty = new Template();
$siteOptions = FormTools::writeOptionsFromObject('Site',
    SearchTools::requestOrSessionExist('Site'),
    array('Owner'=>$auth->getActorId()));
$siteOptions += FormTools::writeOptionsFromObject('Site',
    SearchTools::requestOrSessionExist('Site'),
    array('StockOwner'=>$auth->getActorId()));
$siteOptions = array_unique($siteOptions);

$actorOptions = FormTools::writeOptionsFromObject('Actor',
    SearchTools::requestOrSessionExist('ProductOwner'),
    array('Generic'=>0, 'Active'=>1), array('Name'=>SORT_ASC));
$smarty->assign('siteOptions', implode("\n\t", $siteOptions));
$smarty->assign('EditInvoice', getJSforInvoice());
$smarty->assign('actorOptions', implode("\n\t", $actorOptions));
$addContent = $smarty->fetch('Invoice/InvoicePrestationList.html');
$form->addSmartyValues(array('addContent' => $addContent));
// }}}

if (true === $form->displayGrid()) {
    //Database::connection()->debug = true;
    $advancedSearch = SearchTools::requestOrSessionExist('advancedSearch') ?
           true : false;
    $startDate = sprintf('%s-%02d-%02d', $_POST['StartDate']['Y'],
        $_POST['StartDate']['m'], $_POST['StartDate']['d']);
    $endDate = sprintf('%s-%02d-%02d', $_POST['EndDate']['Y'],
        $_POST['EndDate']['m'], $_POST['EndDate']['d']);
    $prestationCommandItemColl = new Collection();

    require_once('PrestationManager.php');

    $params = array(
        'customer'  => $customerId,
        'begindate' => $startDate,
        'enddate'   => $endDate);
    if($advancedSearch) {
        if(SearchTools::requestOrSessionExist('PrestationCustomer')) {
            $params['prestationcustomer'] = SearchTools::requestOrSessionExist('PrestationCustomer');
        }
        if(SearchTools::requestOrSessionExist('ProductOwner')) {
            $params['productOwner'] = SearchTools::requestOrSessionExist('ProductOwner');
        }
        if(SearchTools::requestOrSessionExist('Product')) {
            $params['product'] = SearchTools::requestOrSessionExist('Product');
        }
        if(SearchTools::requestOrSessionExist('Store')) {
            $params['store'] = SearchTools::requestOrSessionExist('Store');
        }
    }

    $manager = new PrestationManager($params);
    $manager->process();
    $count = $manager->createCommandItems();
//exit;
    if($count > 0) {
        // redirection vers PrestationInvoiceAddEdit.php
        Tools::redirectTo('PrestationInvoiceAddEdit.php');
        exit();
    } else {
        Template::infoDialog(
            _('There\'s is nothing to charge for selected date range and for this customer.'),
            'InvoicePrestationList.php');
        exit();
    }
} else {
    $js = array('JS_AjaxTools.php');
    Template::page($pageTitle, $form->render() . '</form>', $js);
}

?>