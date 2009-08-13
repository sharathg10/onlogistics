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
 * @version   SVN: $Id: InstalmentDelete.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once('config.inc.php');
$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
						   UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES));

SearchTools::prolongDataInSession();  // prolonge les datas en session

// messages
define('I_NOT_DELETED_Instalment',  _("Credit note \"%s\" could not be deleted because it is already used in another payment."));
define('I_NOT_DELETED_InstalmentS', _("The following credit notes could not be deleted because they are already used in another payment: %s"));

// tableau des Avoirs qui n'ont pas tu etre supprimes
$notDeletedInstalments = array();

// pour chaque Avoir
foreach($_REQUEST['thId'] as $thId) {
	$Instalment = Object::load('Instalment', $thId);
	if (Tools::isEmptyObject($Instalment)) {
	    continue;
	}

	/** Ouverture de la transaction  **/
	Database::connection()->startTrans();
	$SupplierCustomer = $Instalment->getCommand()->getSupplierCustomer();
	// On met a jout l'avoir du SupplierCustomer associe
	if (!Tools::isEmptyObject($SupplierCustomer)) {
        $remainingTTC = $Instalment->getTotalPriceTTC();
        // maj encours courant
        $SupplierCustomer->setUpdateIncur(
            $SupplierCustomer->getUpdateIncur() + $remainingTTC);
        saveInstance($SupplierCustomer, 'InstalmentList.php');
	}
    deleteInstance($Instalment, 'InstalmentList.php');
	unset($Instalment, $SupplierCustomer);

	/** commit de la transaction **/
    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_ERROR_IN_EXEC, 'InstalmentList.php');
        exit;
    }
	Database::connection()->completeTrans();
}


// enfin on redirige vers message d'information qui avertira le cas echeant si
// un ou plusieurs Avoirs n'ont pu etre supprimes.
if (count($notDeletedInstalments) == 1) {
    $msg = sprintf(I_NOT_DELETED_Instalment, $notDeletedInstalments[0]);
} else if (count($notDeletedInstalments) > 1) {
    $str = "<ul><li>" . implode("</li><li>", $notDeletedInstalments) . "</li></ul>";
    $msg = sprintf(I_NOT_DELETED_InstalmentS, $str);
} else {
    $msg = I_ITEMS_DELETED;
}
Template::infoDialog($msg, 'InstalmentList.php');

?>
