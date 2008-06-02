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
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
						   UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES));

SearchTools::prolongDataInSession();  // prolonge les datas en session

// messages
define('I_NOT_DELETED_TOHAVE',  _("Credit note \"%s\" could not be deleted because it is already used in another payment."));
define('I_NOT_DELETED_TOHAVES', _("The following credit notes could not be deleted because they are already used in another payment: %s"));

// tableau des Avoirs qui n'ont pas tu etre supprimes
$notDeletedToHaves = array();

// pour chaque Avoir
foreach($_REQUEST['thId'] as $thId) {
	$ToHave = Object::load('ToHave', $thId);
	if (Tools::isEmptyObject($ToHave)) {
	    continue;
	}

	$InvoicePaymentCollection = $ToHave->getInvoicePaymentCollection();
	if (!Tools::isEmptyObject($InvoicePaymentCollection)) {
		// Il ne doit pas y avoir de reglement pour cet Avoir
        $notDeletedToHaves[] = $ToHave->getDocumentNo();
	    continue;
	}

	/** Ouverture de la transaction  **/
	Database::connection()->startTrans();
	$SupplierCustomer = $ToHave->getSupplierCustomer();
	// On met a jout l'avoir du SupplierCustomer associe
	if (!Tools::isEmptyObject($SupplierCustomer)) {
        $remainingTTC = $ToHave->getRemainingTTC();
        $SupplierCustomer->setToHaveTTC(
            $SupplierCustomer->getToHaveTTC() - $remainingTTC);
        // maj encours courant
        $SupplierCustomer->setUpdateIncur(
            $SupplierCustomer->getUpdateIncur() - $remainingTTC);
        // gestion de la remise par pourcentage du CA annuel, il faut
        // l'augmenter du montant restant de l'avoir
        $date = $ToHave->getEditionDate();
        if (false !== $SupplierCustomer->getAnnualTurnoverDiscountPercent($date)) {
            // si sujet à cette remise
            $SupplierCustomer->updateAnnualTurnoverDiscount(
                $ToHave->getTotalPriceHT(), $date);
        }
        saveInstance($SupplierCustomer, 'ToHaveList.php');
	}
    deleteInstance($ToHave, 'ToHaveList.php');
	unset($ToHave, $InvoicePaymentCollection, $SupplierCustomer);

	/** commit de la transaction **/
    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_ERROR_IN_EXEC, 'ToHaveList.php');
        exit;
    }
	Database::connection()->completeTrans();
}


// enfin on redirige vers message d'information qui avertira le cas echeant si
// un ou plusieurs Avoirs n'ont pu etre supprimes.
if (count($notDeletedToHaves) == 1) {
    $msg = sprintf(I_NOT_DELETED_TOHAVE, $notDeletedToHaves[0]);
} else if (count($notDeletedToHaves) > 1) {
    $str = "<ul><li>" . implode("</li><li>", $notDeletedToHaves) . "</li></ul>";
    $msg = sprintf(I_NOT_DELETED_TOHAVES, $str);
} else {
    $msg = I_ITEMS_DELETED;
}
Template::infoDialog($msg, 'ToHaveList.php');

?>
