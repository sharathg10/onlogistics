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

$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

$znId = isset($_REQUEST['znId'])?$_REQUEST['znId']:false;
$Zone = Object::load('Zone', $znId);

if ((!$znId) || Tools::isException($Zone)) {
    Template::errorDialog($Zone->getMessage(), 'ZoneList.php');
    exit;
}

$retURL = 'ZoneAddEdit.php?znId=' . $znId;

// Ouverture de la transaction
Database::connection()->startTrans();

if (isset($_REQUEST['ccyId'])) {
    // Pour chaque CountryCity
    foreach($_REQUEST['ccyId'] as $ccyId) {
    	$CountryCity = Object::load('CountryCity', $ccyId);
    	if (Tools::isEmptyObject($CountryCity)) {
    	    continue;
    	}
        $CountryCity->setZone(0);
        saveInstance($CountryCity, 'ZoneList.php');
    	unset($CountryCity);
    }
}
if (isset($_REQUEST['sitId'])) {
    // Pour chaque Site
    foreach($_REQUEST['sitId'] as $sitId) {
    	$Site = Object::load('Site', $sitId);
    	if (Tools::isEmptyObject($Site)) {
    	    continue;
    	}
        $Site->setZone(0);
        saveInstance($Site, 'ZoneList.php');
    	unset($Site);
    }
}

// commit de la transaction
if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    Database::connection()->rollbackTrans();
    Template::errorDialog(E_MSG_TRY_AGAIN, $retURL);
    exit;
}
Database::connection()->completeTrans();

Tools::redirectTo($retURL);

?>
