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

$auth = Auth::singleton();
$auth->checkProfiles();

SearchTools::prolongDataInSession();

$retURL = isset($_REQUEST['retURL']) ? $_REQUEST['retURL'] : 'dispatcher.php?entity=FlowType';

if(!isset($_REQUEST['objID'])) {
    Template::errorDialog(I_NEED_SINGLE_ITEM, $retURL);
    exit();
}

$flowType = Object::load('FlowType', $_REQUEST['objID']);

if(isset($_POST['ok'])) {
    Database::connection()->startTrans();

    $newFlowType = Tools::duplicateObject($flowType);
    $newFlowType->setName($_POST['FlowType_Name']);
    $newFlowType->setInvoiceType(0);

    $flowTypeItems = $flowType->getFlowTypeItemCollection();
    foreach($flowTypeItems as $flowTypeItem) {
        $newFlowTypeItem = Tools::duplicateObject($flowTypeItem);
        $newFlowTypeItem->setFlowType($newFlowType->getId());
        saveInstance($newFlowTypeItem, $retURL);
    }
    saveInstance($newFlowType, $retURL);

    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
    }
    Database::connection()->completeTrans();
    Tools::redirectTo($retURL);
}

$smarty = new Template();
$smarty->assign('formAction', $_SERVER['PHP_SELF']);
$smarty->assign('objID', $_REQUEST['objID']);
$smarty->assign('retURL', $retURL);
$smarty->assign('FlowType_Name', $flowType->getName() . '_copy');
$content = $smarty->fetch('FlowType/FlowTypeDuplicate.html');
Template::page(_('Expenses and receipts model copy'), $content);
?>
