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
require_once('Objects/CommandExpeditionDetail.php');
require_once('Objects/CommandExpeditionDetail.inc.php');

define('I_PAGE_TITLE', _('Shipping and tracking note for order "%s"'));

// autentification
$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
        UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL));

// prolonger les données du formulaire de recherche
SearchTools::prolongDataInSession();

// url de retour
$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'CommandList.php';
$errorMsge = _('Please select an order first.');

// Vérification de l'id, et récupération de la commande si possible
if (!isset($_REQUEST['cmdID'])) {
    Template::errorDialog($errorMsge, $retURL);
    exit;
}
$cmdID = $_REQUEST['cmdID'];
$command = Object::load('Command', $cmdID);
if (Tools::isEmptyObject($command)) {
    Template::errorDialog($errorMsge, $retURL);
    exit;
}
// La commande ne peut etre cloturee
if ($command->getClosed()) {
    Template::errorDialog(
            E_ERROR_IMPOSSIBLE_ACTION . ' ' . _('because') . ' '
                . _('this order is closed.'),
            $retURL);
    exit;
}

// récupération du CommandExpeditionDetail qui lui est rattaché
$ced = $command->getCommandExpeditionDetail();
if (!($ced instanceof CommandExpeditionDetail)) {
    // s'il n'y en a pas on le crée et on l'affecte à la commande
    $ced = new CommandExpeditionDetail();
    $command->setCommandExpeditionDetail($ced);
}

if (isset($_REQUEST['formSubmitted'])) {
    // démarrage de la transaction
    Database::connection()->startTrans();
    // remplissage de l'objet
    FormTools::autoHandlePostData($_POST, $ced);
    // sauvegarde de la commande et de son CommandExpeditionDetail
    saveInstance($ced, $retURL);
    saveInstance($command, $retURL);
    // commit de la transaction
    Database::connection()->completeTrans();
    // redirection
    Tools::redirectTo($retURL);
    exit;
}

$smarty = new Template();
$smarty->assign('formAction', basename($_SERVER['PHP_SELF']));
$smarty->assign('retURL', $retURL);
$smarty->assign('cmdID', $cmdID);
// propriétés du CommandExpeditionDetail
$smarty->assign('LoadingPort', $ced->getLoadingPort());
$smarty->assign('CustomerCommandNo', $ced->getCustomerCommandNo());
$smarty->assign('DestinatorStore', $ced->getDestinatorStore());
$smarty->assign('DestinatorRange', $ced->getDestinatorRange());
$smarty->assign('ReservationNo', $ced->getReservationNo());
$smarty->assign('Season', $ced->getSeason());
$smarty->assign('Deal', $ced->getDeal());
$smarty->assign('PackingList', $ced->getPackingList());
$smarty->assign('AirwayBill', $ced->getAirwayBill());
$smarty->assign('SupplierCode', $ced->getSupplierCode());
$smarty->assign('Comment', $ced->getComment());
$smarty->assign('Weight', $ced->getWeight());
$smarty->assign('NumberOfContainer', $ced->getNumberOfContainer());

$shOptions = getShipmentOptions($ced->getShipment());
$smarty->assign('ShipmentOptions', implode("\n", $shOptions));

// affichage de la page
$content = $smarty->fetch('Command/CommandExpeditionDetailAddEdit.html');
Template::page(sprintf(I_PAGE_TITLE, $command->getCommandNo()), $content);

?>
