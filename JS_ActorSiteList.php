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
require_once('CacheTools.php');
/*require_once('Objects/CacheData.php');
require_once('Objects/CacheData.const.php');*/

if(isset($_REQUEST['update_cache'])){
    $auth = Auth::Singleton();
    $auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
        array('showErrorDialog' => true));
}
/*
// Gestion du cache javascript
$mapper   = Mapper::singleton('CacheData');
$cacheID = isset($_REQUEST['withJob'])?ACTORSITEJOBS_CACHEID:ACTORSITE_CACHEID;
$cache = $mapper->load(array('Id' => $cacheID));

if ($cache instanceof CacheData && !isset($_REQUEST['update_cache'])
    && !isset($_REQUEST['no_cache'])) {
    echo gzuncompress($cache->getData());
    exit;
}
*/
$dumpSites = !isset($_REQUEST['withoutSite']) || ($_REQUEST['withoutSite'] != 1);
$withJob = isset($_REQUEST['withJob']) && ($_REQUEST['withJob'] == 1);
$data = generateActorSiteJobCache($dumpSites, $withJob);
echo $data;
/*
// save the cache in the database
Database::connection()->startTrans();
if (!($cache instanceof CacheData)) {
    $cache = new CacheData();
    $cache->setId($cacheID);
}
$cache->setData(gzcompress($data));
saveInstance($cache, null, false);
if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    Database::connection()->rollbackTrans();
    Template::errorDialog(E_MSG_TRY_AGAIN, $retURL);
    exit;
}
Database::connection()->completeTrans();

// Flush the js only if we are not in update mode
if(isset($_REQUEST['update_cache'])) {
    $retURL = isset($_REQUEST['returnURL'])?$_REQUEST['returnURL']:'home.php';
    Template::infoDialog(I_CONFIRM_DO, $retURL);
    exit;
} else {
    echo $data;
}*/

?>
