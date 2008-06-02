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
require_once('ApportionmentManager.php');

$auth = Auth::singleton();
$auth->checkProfiles();

if (isset($_POST['Export']) || isset($_POST['ExportWithDetail']) || isset($_POST['ExportTVA'])) {
    $breakdownType = array();
    if(isset($_POST['ExportTVA'])) {
        $breakdownType[] = Account::BREAKDOWN_TVA;
        $_POST['ExportWithDetail'] = true;
    }
    $manager = new ApportionmentManager();
    $fromDate = sprintf(
        '%s-%s-%s 00:00:00', 
        $_POST['From_Year'], $_POST['From_Month'], $_POST['From_Day']
    );
    $toDate = sprintf(
        '%s-%s-%s 23:59:59', 
        $_POST['To_Year'], $_POST['To_Month'], $_POST['To_Day']
    );
    $accountingType = false;
    if($auth->getProfile() == UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT) {
        $actor = $auth->getActor();
        $accountingType = $actor->getAccountingTypeId();
    } elseif(isset($_POST['AccountingType']) && $_POST['AccountingType'] != '##') {
        $accountingType = $_POST['AccountingType'];
    }
    $manager->process($fromDate, $toDate, $accountingType, $breakdownType);
    $withDetail = isset($_POST['ExportWithDetail']);
    $data = $manager->toCSV($withDetail, $_POST['CSVSeparator']);
    header('Pragma: public');
    header('Content-Type: application/force-download');
    header('Content-Disposition: attachment;filename=ventilation.csv');
    echo $data;
    exit;
}

$smarty = new Template();
$smarty->assign('formAction', basename($_SERVER['PHP_SELF']));
$smarty->assign('oneMonthBefore', strtotime('-1 month'));
$smarty->assign('dateFormat', 
    strtoupper(I18N::getHTMLSelectDateFormat()));
$actor = $auth->getActor();
$smarty->assign('accTypeOptions', FormTools::writeOptionsFromObject('AccountingType', 
    $actor->getAccountingTypeId()));
$accTypeEnabled = $auth->getProfile()==UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT ?
    0:1;
$smarty->assign('accTypeEnabled', $accTypeEnabled);

$content = $smarty->fetch('ApportionmentExport/ApportionmentExport.html');
Template::page('', $content);

?>
