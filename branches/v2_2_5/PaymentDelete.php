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
require_once('Objects/Command.const.php');
require_once('ProductCommandTools.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
        UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES));

$retURL = isset($_REQUEST['ReturnURL'])?
    $_REQUEST['ReturnURL'] . '&cmdId=' . $_GET['cmdId']:
    'PaymentCommandList.php?cmdId=' . $_GET['cmdId'];
// prolonge les datas du form de recherche en session
SearchTools::ProlongDataInSession();

if (!isset($_REQUEST['pmtId'])) {
    Template::errorDialog(sprintf(E_MSG_MUST_SELECT_A, _('payment')), $retURL);
    Exit;
}
// Ouverture de la transaction
Database::connection()->startTrans();

$cmd = Object::load('Command', $_GET['cmdId']);
if (!($cmd instanceof Command)) {
    Template::errorDialog(sprintf(E_MSG_MUST_SELECT_A, _('order_command')), $retURL);
    exit;
}
$spc = $cmd->getSupplierCustomer();
// contient les Id des Payment impossibles a supprimer
$errors = array();

$alert = false;
foreach($_REQUEST['pmtId'] as $i => $pmtId) {
    $Payment = Object::load('Payment', $pmtId);
    $cancellationDate = $Payment->getCancellationDate();
    if ($cancellationDate != '0000-00-00 00:00:00') {
        // on ne supprime pas ce reglement, car deja supprime!!
        $errors[] = $Payment->getId();
        continue;
    }

    $Payment->setCancellationDate(date('Y-m-d H:i:s', time()));
    // Si un ActorBankDetail associe, on decremente son Amount du montant TTC
    $abd = $Payment->getActorBankDetail();
    if (!Tools::isEmptyObject($abd)) {
        // On incremente ou decremente en banque, selon que cmde Supplier ou customer
        $coef = ($cmd->getType() == Command::TYPE_SUPPLIER)?-1:1;
        $abd->setAmount($abd->getAmount() - $coef * $Payment->getTotalPriceTTC());
        saveInstance($abd, $retURL);
    }
    saveInstance($Payment, $retURL);
    $ipCollection = $Payment->getInvoicePaymentCollection();
    if (!Tools::isEmptyObject($ipCollection)) {
        for ($i = 0; $i < $ipCollection->getCount(); $i++) {
            $ip = $ipCollection->getItem($i);
            $Invoice = $ip->getInvoice();
            $Invoice->setToPay($Invoice->getToPay() + $ip->getPriceTTC());
            saveInstance($Invoice, $retURL);
            // Gestion des Avoirs
            $toHave = $ip->getToHave();
            if (!Tools::isEmptyObject($toHave)) {
                $spc->setToHaveTTC($spc->getToHaveTTC() + $ip->getPriceTTC());
                saveInstance($spc, $retURL);
                $toHave->setRemainingTTC($toHave->getRemainingTTC() + $ip->getPriceTTC());
                saveInstance($toHave, $retURL);
            }
            unset($ip, $toHave);
        }
    }
    // Blocage si besoin, MAJ encours, et envoi alerte (hors transaction)
    // ICI: Invoice est la derniere:
    // A modifier qd on creera une Alert specifique
    $alert = commandBlockage($cmd, $Invoice, $Payment->getTotalPriceTTC());
    unset($Payment, $Invoice);
}

/*  mise a jour de l'etat de la commande  */
// Payments non annules
$paymentCollection = $cmd->getPaymentCollection(1);
// S'il en reste pour la commande
if (!Tools::isEmptyObject($paymentCollection)) {
    $cmd->setState(Command::REGLEMT_PARTIEL);
} else { // s'il n'y a plus de payment non annule pour cette commande
	if (method_exists($cmd, 'isFactured')) {
	    if ($cmd->isFactured() == 0) {
	        $cmd->setState(Command::FACT_PARTIELLE);
	    } else {
	        $cmd->setState(Command::FACT_COMPLETE);
	    }
	}
	else {
		$cmd->setState(Command::FACT_COMPLETE);
	}

}
saveInstance($cmd, $retURL);
// commit de la transaction
if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    Database::connection()->rollbackTrans();
    Template::errorDialog(E_MSG_TRY_AGAIN, $retURL);
    exit;
}
Database::connection()->completeTrans();
// Seulement apres la transaction, on envoi l'alerte si necessaire
if (!Tools::isEmptyObject($alert)) {
    $alert->send();
}
// On tue les GridItems en session
$gridItemSessionVarName = SearchTools::getGridItemsSessionName('PaymentCommandList');
unset($_SESSION[$gridItemSessionVarName]);
// s'il y a eu des Payment impossibles a supprimer
if (!empty($errors)) {
    $errorMsg = _('The following payments could not be deleted') . ':<ul>';
    foreach($errors as $paymentId) {
        $errorMsg .= '<li>' . $paymentId . '</li>';
    }
    Template::errorDialog($errorMsg . '</ul>', $retURL);
    exit;
}

Tools::redirectTo($retURL);
exit;

?>
