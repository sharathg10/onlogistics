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
require_once('AccountTools.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                             UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL));

$retURL = isset($_REQUEST['returnURL'])?$_REQUEST['returnURL']:'CommandList.php';

//recupere l'acteur connecté (donc le supplier ...)
$ConnectedActorId = $auth->getActorId();
$Command = Object::load('Command', $_REQUEST['CommandId']);
$SupplierCustomer = $Command->getSupplierCustomer();
$destinator = $Command->getDestinator();

SearchTools::ProlongDataInSession();  // prolonge les datas du form de recherche en session

if (isset($_REQUEST['formSubmitted'])) { // Validation de l'acompte saisi pour la commande
    // Ouverture de la transaction
    Database::connection()->startTrans();

    $initInstallment = $Command->getInstallment();

    // Gestion du ActorBankDetail
    if ($Command->getInstallment() != $_POST['Installment']
            || $Command->getInstallmentBankId() != $_POST['ActorBankDetail']) {
        $initABD = $Command->getInstallmentBank();
        if (!Tools::isEmptyObject($initABD)) {
            $initABD->setAmount($initABD->getAmount() - $Command->getInstallment());
            saveInstance($initABD, $retURL);
        }
    }
    $Command->setInstallment($_POST['Installment']);
    $Command->setInstallmentBank($_POST['ActorBankDetail']);

    if ($_POST['ActorBankDetail'] != 0) {
        // On incremente ou decremente en banque, selon que cmde fourniss. ou client
        $coef = ($Command->getType() == Command::TYPE_CUSTOMER)?1:-1;
        $abd = Object::load('ActorBankDetail', $_POST['ActorBankDetail']);
        $abd->setAmount($abd->getAmount() + $coef * $_POST['Installment']);
        saveInstance($abd, $retURL);
    }

    $InitialUpdateIncur = $SupplierCustomer->getUpdateIncur();
    // Si en mode EDIT, on fait la difference entre l'ancien et le nouvel accompte
    $SupplierCustomer->setUpdateIncur(
            $SupplierCustomer->getUpdateIncur()
            - ($Command->getInstallment() - $initInstallment));

    if ($SupplierCustomer->getMaxIncur() > 0 ) { // l'encours autorisé est défini
        if (($InitialUpdateIncur > $SupplierCustomer->getMaxIncur())
                && ($SupplierCustomer->getMaxIncur() < $SupplierCustomer->getUpdateIncur())) {
            require_once('ProductCommandTools.php');
            // Debloque toutes les cdes bloquees, ds la limite de UpdateIncur - MaxIncur
            blockDeblockage($ConnectedActorId, $destinator, 0);
        }
    }
    saveInstance($SupplierCustomer, $retURL);
    saveInstance($Command, $retURL);

    // Commit de la transaction
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        die(E_ERROR_SQL);
    }
    Database::connection()->completeTrans();

    Tools::redirectTo($retURL);
    exit;
} //fin du traitement


//  Dans tous ces cas: Redirect vers la liste des commandes
if (!($Command instanceof ProductCommand)) {
    Template::errorDialog(_('Selected order was not found in the database.'), $retURL);
    exit;
}
// La commande doit etre facturable
if (!$Command->isBillable()) {
    Template::errorDialog(
        E_ERROR_IMPOSSIBLE_ACTION . ' ' . _('because') . ' '
            . _('this order is not billable.'),
        $retURL);
    exit;
}
//if ($Command->getState() != Command::ACTIVEE) {
//    Template::errorDialog(_('Impossible de saisir un acompte pour cette commande. Elle n\'est plus à l\'état activée.'), $retURL);
//    exit;
//}


// Affichage du formulaire avec smarty
$smarty = new Template();
$smarty->assign('returnURL', $retURL);
$smarty->assign('CommandNo', $Command->getCommandNo());
$smarty->assign('TotalTTC', $Command->getTotalPriceTTC());
$smarty->assign('ToPay', $Command->getTotalPriceTTC() - $Command->getInstallment());
$smarty->assign('ActorName', $destinator->getName());
$smarty->assign('CommandId', $Command->getId());

// Gestion des ActorBankDetail: ceux lies au DataBaseOwner
$ActorBankDetailList = getActorBankDetailList($Command->getInstallmentBankId());
$smarty->assign('ActorBankDetailList', $ActorBankDetailList);
$smarty->assign('installmentAmount', $Command->getInstallment());

if (!($SupplierCustomer instanceof SupplierCustomer)) {
    $smarty->assign('UpdateIncur',_('N/A'));
    $smarty->assign('MaxIncur',_('N/A'));
}
else {
    $smarty->assign('UpdateIncur', $SupplierCustomer->getUpdateIncur());
    $smarty->assign('MaxIncur', $SupplierCustomer->getMaxIncur());
}
$JSRequirements = array('js/lib-functions/FormatNumber.js',
        'js/includes/InstallmentAddEdit.js');
Template::page(_('Add instalment'), $smarty->fetch('InstallmentAddEdit.html'),
        $JSRequirements);
?>
