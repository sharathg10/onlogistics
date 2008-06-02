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
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
        UserAccount::PROFILE_GESTIONNAIRE_STOCK));
$pfID  = $auth->getProfile();
$actID = $auth->getActorId();

// URL de retour
$retURL = isset($_REQUEST['returnURL'])?$_REQUEST['returnURL']:'home.php';

if (!(isset($_REQUEST['StoreId']) && is_array($_REQUEST['StoreId']))) {
    Tools::redirectTo($retURL);
}

// Les stores qui n'auront pas été supprimés
$deactivatedStores = $notTouchedStores = array();

// On demarre une transaction
Database::connection()->startTrans();

// Pour chaque store on vérifie et on le supprime si possible
foreach($_REQUEST['StoreId'] as $i => $storeId) {
    $store = Object::load('Store', $storeId);
    if ($pfID == UserAccount::PROFILE_GESTIONNAIRE_STOCK &&
            $actID != Tools::getValueFromMacro($store, '%StorageSite.Owner.Id%')) {
        // L'user n'a pas le droit de supprimer ce Store
        $deactivatedStores[] = $store->getName();
        continue;
    }
    $state = $store->isDeletable();
    // Si on ne peut pas supprimer ce Store
    if ($state != Store::DELETABLE) {
        if ($state == Store::CAN_BE_DISABLED) {
            $deactivatedStores[] = $store->getName();
            $store->setActivated(false, true);  // Update les Locations associees
            saveInstance($store, $retURL);
        }
        // Sinon: on ne fait rien
        $notTouchedStores[] = $store->getName();
        continue;
    }
    // sinon ok on supprime
    // et ondelete cascade sur les Locations inclus
    deleteInstance($store, $retURL);
}

// On commite la transaction ou on l'annule si echec
if (Database::connection()->hasFailedTrans()){
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
	Database::connection()->rollbackTrans();
    Template::errorDialog(E_ERROR_IN_EXEC, $retURL);
    exit;
}
Database::connection()->completeTrans();

// Enfin on redirige vers message d'information qui avertira si besoin si
// un ou plusieurs magasins n'ont pu etre supprimes.
$msg = '';
if (!empty($deactivatedStores)) {
    $str = '<ul><li>' . implode('</li><li>', $deactivatedStores) . '</li></ul>';
    $msg = sprintf(
        _('The following stores could not be deleted, they were only deactivated: %s'),
        $str
    );
}
if (!empty($notTouchedStores) >= 1) {
    $str = '<ul><li>' . implode('</li><li>', $notTouchedStores) . '</li></ul>';
    $msg .= sprintf(
        _('The following stores could not be deleted nor deactivated, because one of theirs locations is not empty: %s'),
        $str
    );
}
// sinon:
$msg = ($msg == '')?I_ITEMS_DELETED:$msg;

Template::infoDialog($msg, $retURL);

?>
