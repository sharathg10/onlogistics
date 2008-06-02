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
$Auth->checkProfiles(
        array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_GESTIONNAIRE_STOCK));

$returnURL = 'StoreAddEdit.php?returnURL=StorebyStorageSiteList.php&stoId='
        . $_REQUEST['stoId'] . '&StoreId='. $_REQUEST['StoreId'];


if (isset($_REQUEST['locId']) && is_array($_REQUEST['locId'])) {
	// Contiendra les Name des Locations impossibles a supprimer
    $ErrorNameArray = array();

    // On demarre une transaction
    Database::connection()->startTrans();

	foreach($_REQUEST['locId'] as $i => $locId) {
		$Location = Object::load('Location', $locId);
		// Controle qu'on ait bien le droit de supprimer chaque Location!!
		if ($_REQUEST['StoreId'] != $Location->getStoreId()) {
		    continue;
		}
		$state = $Location->isDeletable();
		if ($state != Store::DELETABLE) {
		    $ErrorNameArray[] = $Location->getName();
            // On ne le supprime pas, on le desactive, si possible
            if ($state == Store::CAN_BE_DISABLED) {
    		    $Location->setActivated(0);
                saveInstance($Location, $returnURL);
            }
            // Sinon, on ne fait rien
		    continue;
		}
        deleteInstance($Location, $returnURL);
	}

	// On commite la transaction ou on l'annule si echec
    if (Database::connection()->hasFailedTrans()){
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    	Database::connection()->rollbackTrans();
        Template::errorDialog(E_ERROR_IN_EXEC, $returnURL);
        exit;
    }
    Database::connection()->completeTrans();

	// S'il y a eu des Location impossibles a supprimer
	if (!empty($ErrorNameArray)) {
		$ErrorMsge = _('The following locations could not be deleted') . ':<ul>';
		foreach($ErrorNameArray as $LocationName) {
			$ErrorMsge .= '<li>' . $LocationName . '</li>';
		}
		Template::errorDialog($ErrorMsge . '</ul>', $returnURL);
        exit;
	}
}

Tools::redirectTo($returnURL);

?>
