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

SearchTools::prolongDataInSession();

// tableau des Components qui n'ont pas tu etre supprimes
$notDeletedItemsNb = 0;

// Ouverture de la transaction
Database::connection()->startTrans();

// pour chaque Component
foreach($_REQUEST['cmpId'] as $cmpId) {
	$Component = Object::load('Component', $cmpId);
	if (Tools::isEmptyObject($Component)) {
	    continue;
	}

	// Il ne doit pas y avoir de composant qui lui est rattaché
	// et interdiction de supprimer les Components de niveau 0
	$ComponentCollection = $Component->getComponentCollection();

	if (!Tools::isEmptyObject($ComponentCollection) || $Component->getLevel() == 0) {
		$notDeletedItemsNb ++;
	    continue;
	}
    deleteInstance($Component, $_SESSION['arboURL']);
	unset($Component);
}

// commit de la transaction
if (Database::connection()->HasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    Database::connection()->rollbackTrans();
    Template::errorDialog(E_MSG_TRY_AGAIN, $_SESSION['arboURL']);
    exit;
}
Database::connection()->completeTrans();


// enfin on redirige vers message d'information qui avertira le cas echeant si
// un ou plusieurs Components n'ont pu etre supprimes.
if ($notDeletedItemsNb == 0) {
	$msg = I_ITEMS_DELETED;
} else if ($notDeletedItemsNb == count($_REQUEST['cmpId'])) {
    $msg = I_NO_ITEMS_DELETED;
} else {
    $msg = I_NOT_DELETED;
}

Template::infoDialog($msg, $_SESSION['arboURL']);

?>