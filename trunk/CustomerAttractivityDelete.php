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

$retURL = isset($_REQUEST['retURL'])?
    $_REQUEST['retURL']:'CustomerAttractivityList.php';

// pas d'ids passés en paramètre...
if (!isset($_REQUEST['cuaIDs'])) {
    Template::errorDialog(E_ERROR_IMPOSSIBLE_ACTION, $retURL); 
    exit(1);
}

// On supprime les elements sélectionnés 
Database::connection()->startTrans();
$notdeleted = array();
$mapper = Mapper::singleton('CustomerAttractivity');

foreach($_REQUEST['cuaIDs'] as $id){
    $cua = $mapper->load(array('Id'=>$id));
    // la suppression n'est possible que si l'acttractivité n'est liée à 
    // aucune catégorie.
    if ($cua instanceof CustomerAttractivity) { 
        if (count($cua->getCategoryCollectionIds()) == 0) {
            deleteInstance($cua, $retURL);
        } else {
            $notdeleted[] = $cua->getName();
        }
    }
}

if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
	Database::connection()->rollbackTrans();
    Template::errorDialog(E_ERROR_SQL, $retURL);
	Exit;
}
Database::connection()->completeTrans();

// si certaines attractivités n'ont pu être supprimées, on informe l'user
if (count($notdeleted) > 0) {
    Template::infoDialog(
        _('The following attractivities cannot be deleted because they are link to one or more categories') 
            . sprintf(':<ul><li>%s</li></ul>', implode('</li><li>', $notdeleted)),
        $retURL
    );
    exit();
}
Tools::redirectTo($retURL);

?>
