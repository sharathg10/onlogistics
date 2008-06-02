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
require_once('Objects/Preferences.const.php');

$auth = Auth::singleton();
$auth->checkProfiles();

$confirmMessage = _('Preferences were successfully saved.');

// Le formulaire a ete poste
if (isset($_POST['Ok'])) {
    Database::connection()->startTrans();
    Preferences::set('ProductCommandQtyInCatalog', isset($_POST['ProductCommandQtyInCatalog']));
    Preferences::set('ProductCommandUEQty', isset($_POST['ProductCommandUEQty']));
    Preferences::set('MercurialForClient', isset($_POST['MercurialForClient']));
    Preferences::set('CalendarAwareOfPlanning', isset($_POST['CalendarAwareOfPlanning']));
    Preferences::set('EstimateBehaviour', (int)$_POST['EstimateBehaviour']);
    Preferences::set('EstimateValidityDays', $_POST['EstimateValidityDays']);
    Preferences::set('CommandActivateMultipleChains', isset($_POST['CommandActivateMultipleChains']));
    $behaviour = (int)$_POST['CustomerProductCommandBehaviour'];
    Preferences::set('CustomerProductCommandBehaviour', $behaviour);

    /* traitement special pour CustomerProductCommandBehaviour:
     * Si comportement identique pour toutes les commandes, on MAJ
     * tous les SupplierCustomer, et les Command.Closed si besoin
     */
    if ($behaviour != MANAGED_BY_SUPPLIERCUSTOMER) {
        $spcColl = Object::loadCollection('SupplierCustomer');
        if (!Tools::isEmptyObject($spcColl)) {
            $count = $spcColl->getCount();
            for($i = 0; $i < $count; $i++) {
                $item = $spcColl->getItem($i);
                $item->setCustomerProductCommandBehaviour($behaviour);
                saveInstance($item, $_SERVER['PHP_SELF']);
            }
        }
    }
    $behaviour = (int)$_POST['ChainCommandBillingBehaviour'];
    Preferences::set('ChainCommandBillingBehaviour', $behaviour);
    
    Preferences::set('TvaSurtax', I18N::extractNumber($_POST['TvaSurtax']));
    Preferences::set('FodecTax', I18N::extractNumber($_POST['FodecTax']));
    Preferences::set('TaxStamp', I18N::extractNumber($_POST['TaxStamp']));
    
    Preferences::save();

    // Gestion des erreurs
    if (Database::connection()->hasFailedTrans()) {
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_ERROR_SQL, $_SERVER['PHP_SELF']);
        exit;
    }
    // Commit de la transaction
    Database::connection()->completeTrans();

    Template::infoDialog($confirmMessage, $_SERVER['PHP_SELF']);
    exit(0);
}

$smarty = new Template();
$smarty->assign('formAction', $_SERVER['PHP_SELF']);
$smarty->assign('ProductCommandQtyInCatalog',
        Preferences::get('ProductCommandQtyInCatalog'));
$smarty->assign('ProductCommandUEQty', Preferences::get('ProductCommandUEQty'));
$smarty->assign('CommandActivateMultipleChains',
        Preferences::get('CommandActivateMultipleChains'));
$smarty->assign('MercurialForClient', Preferences::get('MercurialForClient'));
$smarty->assign('CalendarAwareOfPlanning', Preferences::get('CalendarAwareOfPlanning'));
$smarty->assign('EstimateBehaviour', Preferences::get('EstimateBehaviour'));
$smarty->assign('EstimateValidityDays', Preferences::get('EstimateValidityDays'));
$smarty->assign('CustomerProductCommandBehaviour',
        Preferences::get('CustomerProductCommandBehaviour'));
$smarty->assign('ChainCommandBillingBehaviour',
        Preferences::get('ChainCommandBillingBehaviour'));
$smarty->assign('TvaSurtax', I18N::formatNumber(Preferences::get('TvaSurtax', 0)));
$smarty->assign('FodecTax', I18N::formatNumber(Preferences::get('FodecTax', 0)));
$smarty->assign('TaxStamp', I18N::formatNumber(Preferences::get('TaxStamp', 0)));
Template::page('', $smarty->fetch('Preferences/PreferencesCommand.html'));

?>
