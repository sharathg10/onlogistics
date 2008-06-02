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
// pour les sessions
require_once('Objects/Nomenclature.php');
require_once('Objects/Product.php');
require_once('Objects/AeroProduct.php');
require_once('Objects/Component.php');
require_once('Objects/ComponentGroup.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

SearchTools::ProlongDataInSession();

// On checke que la Nomenclature soit bien en session
if (!isset($_SESSION['Nomenclature'])) {
    Template::errorDialog(E_ERROR_IN_EXEC, 'NomenclatureList.php');
    exit;
}

// tableau des ComponentGroups qui n'ont pas tu etre supprimes
$notDeletedItemsNb = 0;
$deletedItemsIds = array();

// pour chaque ComponentGroup
foreach($_REQUEST['cgrId'] as $cgrId) {
	$ComponentGroup = Object::load('ComponentGroup', $cgrId);
	if (Tools::isEmptyObject($ComponentGroup)) {
		// En cas de ComponentGroup pas encore sauve en base, il faudra 
		// qd meme le supprimer de la collection
		$deletedItemsIds[] = $cgrId;
	    continue;
	}
	
	// Il ne doit pas y avoir de composant qui lui est rattaché
	$ComponentCollection = $ComponentGroup->getComponentCollection();
	if (!Tools::isEmptyObject($ComponentCollection)) {
		$notDeletedItemsNb ++;
	    continue;
	}
	$deletedItemsIds[] = $ComponentGroup->getId();
    deleteInstance($ComponentGroup, 'NomenclatureAddEdit.php');
	unset($ComponentGroup);
} 


$ComponentGroupCollection = $_SESSION['Nomenclature']->getComponentGroupCollection();
$count = $ComponentGroupCollection->getCount();
for($i=0; $i<$count; $i++){
    $item = $ComponentGroupCollection->getItem($i);
    if ($item instanceof ComponentGroup && in_array($item->getId(), $deletedItemsIds)) {
        $ComponentGroupCollection->removeItem($i);
    }
}

// enfin on redirige vers message d'information qui avertira le cas echeant si 
// un ou plusieurs ComponentGroups n'ont pu etre supprimes.
if ($notDeletedItemsNb == 0) {
	$msg = I_ITEMS_DELETED;
} else if ($notDeletedItemsNb == count($_REQUEST['cgrId'])) {
    $msg = I_NO_ITEMS_DELETED;
} else {
    $msg = I_NOT_DELETED;
}

Template::infoDialog($msg, 'NomenclatureAddEdit.php');

?>
