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

SearchTools::ProlongDataInSession();  // prolonge les datas en session

// tableau des Nomenclatures qui n'ont pas tu etre supprimees
$notDeletedItemsNb = 0;
$ComponentMapper = Mapper::singleton('Component');
/*  Ouverture de la transaction  */
Database::connection()->startTrans();

// pour chaque Nomenclature
foreach($_REQUEST['nomId'] as $nomId) {
	$Nomenclature = Object::load('Nomenclature', $nomId);
	if (Tools::isEmptyObject($Nomenclature)) {
	    continue;
	}

	// Il ne doit pas y avoir de composant de level >0 qui lui est rattaché
    $filter = SearchTools::NewFilterComponent('Level', '', 'NotEquals', 0, 1);
	$ComponentCollection = $Nomenclature->getComponentCollection($filter);
	if (!Tools::isEmptyObject($ComponentCollection)) {
		$notDeletedItemsNb ++;
	    continue;
	}

    deleteInstance($Nomenclature, 'NomenclatureList.php');
    // Ca supprime en cascade le Component de level 0 et les eventuels ComponentGroup
	unset($Nomenclature);
}

/*  Commit de la transaction  */
if (Database::connection()->HasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    Database::connection()->rollbackTrans();
    Template::errorDialog(E_ERROR_IMPOSSIBLE_ACTION, 'NomenclatureList.php');
    exit;
}

Database::connection()->completeTrans();

// enfin on redirige vers message d'information qui avertira le cas echeant si
// une ou plusieurs Nomenclatures n'ont pu etre supprimes.
if ($notDeletedItemsNb == 0) {
	$msg = I_ITEMS_DELETED;
} else if ($notDeletedItemsNb == count($_REQUEST['nomId'])) {
    $msg = I_NO_ITEMS_DELETED;
} else {
    $msg = I_NOT_DELETED;
}

Template::infoDialog($msg, 'NomenclatureList.php');

?>