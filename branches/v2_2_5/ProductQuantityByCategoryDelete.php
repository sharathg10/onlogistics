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
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

$retURL = !isset($_REQUEST['pdtId'])?'ProductList.php'
    	:'ProductQuantityByCategoryList.php?pdtId=' . $_REQUEST['pdtId'];

// Pour prolonger la session du formulaire de recherche de ProductList
SearchTools::ProlongDataInSession();

// verification des données GET
if (!isset($_REQUEST['pqcIds'])) {
    Template::errorDialog(I_NEED_SELECT_ITEM, $retURL);
    exit;
}

Database::connection()->startTrans();

$mapper = Mapper::singleton('ProductQuantityByCategory');
foreach($_REQUEST['pqcIds'] as $pqcId) {
	$pqc = Object::load('ProductQuantityByCategory', $pqcId);
	if (Tools::isEmptyObject($pqc)) {
	    continue;
	}
    $mapper->delete($pqcId);
	unset($pqc);
}

if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    Database::connection()->rollbackTrans();
    Template::errorDialog(Database::connection()->errorMsg(), $retURL);
    exit;
}
Database::connection()->completeTrans();

// Redirect vers ProductQuantityByCategoryList
Tools::redirectTo($retURL);

?>