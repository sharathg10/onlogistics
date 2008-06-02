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
require_once('ProductCommandTools.php');
require_once('Objects/ProductCommand.inc.php');
require_once('Objects/Command.const.php');
require_once('Objects/ActivatedChainOperation.php');
require_once('Objects/MovementType.const.php');

$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL));

$retURL = isset($_REQUEST['returnURL'])?$_REQUEST['returnURL']:'CommandList.php';
$false = false;
SearchTools::prolongDataInSession(); // prolonge les datas du form de recherche en session
//Database::connection()->debug=true;
$cmdMapper = Mapper::singleton('Command');
$cmd = $cmdMapper->load(array('Id' => $_REQUEST['CommandId']));

if (!($cmd instanceof Command)) {
    Template::errorDialog(_('Order was not found in the database.'), $retURL);
    exit;
}
if ($cmd->getCommand() instanceof Command) {
    // on ne peut supprimer un devis qui est lié à une commande
    Template::errorDialog(_('You cannot delete an estimate that is linked to an order'), $retURL);
    exit;
}
if ($cmd->getState() != Command::ACTIVEE) {
    if(!($cmd instanceof PrestationCommand)) {
        Template::errorDialog(_('You cannot delete an order which state is different from "Activated"'), $retURL);
        exit;
    }
}
// La commande ne peut etre cloturee
if ($cmd->getClosed()) {
    Template::errorDialog(_('You cannot delete a closed order.'), $retURL);
    exit;
}

if (!isset($_GET['ok'])) {
    Template::confirmDialog(
        sprintf(
            _('Are you sure you want to delete %s ?'),
            ($cmd->getIsEstimate() ? _('this estimate') : _('this order'))
        ),
        'CommandDelete.php?ok=1&CommandId=' . $_REQUEST['CommandId'] .
            '&returnURL=' . $retURL, $retURL);
    exit;
}

// GED, on ne peut pas supprimer une commande dont une des ack est liée à un
// ou plusieurs docs
$context = Preferences::get('TradeContext', array());
if (is_array($context) && in_array('consulting', $context)) {
    $udCol = Object::loadCollection('UploadedDocument', array(
        'ActivatedChainTask.ActivatedOperation.ActivatedChain.CommandItem().Command.Id' => $cmd->getId()
    ));
    if ($udCol->getCount() > 0) {
        Template::errorDialog(
            _('This order cannot be deleted because one of its task is linked to a document.'),
            $retURL
        );
        exit(1);
    }
}

/*
 * Si ProductCommand: suppression des ProductCommandItem, ActivatedMovement,
 * via le ON DELETE CASCADE emule, mais APRES avoir mis a jour les Qtes
 * virtuelles.
 *
 * Si Command, suppression des ActivatedChain, ActivatedChainOperation,
 * ActivatedChainTask
 * via le ON DELETE CASCADE emule.
 *
 **/

/*  Ouverture de la transaction  */
Database::connection()->startTrans();

// MAJ des Qtes virtuelles: gestion des E/S internes ou normales,
// Code spécifique à la commande de produit
if ($cmd instanceof ProductCommand) {
    $cmd->updateQvbeforeDeleteACM();
}

$cmdItemCollection = $cmd->getCommandItemCollection();
$cmdNo = $cmd->getCommandNo();
$ProductMapper = Mapper::singleton('Product');
$count = $cmdItemCollection->getCount();
for($i = 0; $i < $count; $i++) {
    $item = $cmdItemCollection->getItem($i);
    // suppression de la chaîne, des opérations et taches
    $ach = $item->getActivatedChain();
    if ($ach instanceof ActivatedChain) {
        // suppression de l'actorsitetransition de la chaine
        $siteTransition = $ach->getSiteTransition();
        if ($siteTransition instanceof ActorSiteTransition) {
            deleteInstance($siteTransition, $retURL);
        }
        // suppression de l'actorsitetransition de chaque tache
        $acoCollection = $ach->getActivatedChainOperationCollection(
            array(), array(), array('Id'));
        $jcount = $acoCollection->getCount();
        for($j = 0; $j < $jcount; $j++) {
            $aco = $acoCollection->getItem($j);
            $actCollection = $aco->getActivatedChainTaskCollection(
                array(), array(), array('Id', 'ActorSiteTransition'));
            $kcount = $actCollection->getCount();
            for($k = 0; $k < $kcount; $k++){
            	$ack = $actCollection->getItem($k);
            	$siteTransition = $ack->getActorSiteTransition();
                if ($siteTransition instanceof ActorSiteTransition) {
                    deleteInstance($siteTransition, $retURL);
                }
                unset($ack);
            } // for

            /* Bruno commente: on ne doit JAMAIS passer ici, car cmdItem.ach = 0 pour une cmd de prestation!!!  */ 
            /* pour les commandes de prestation, maj
            ACO.PrestationFactured, ACO.PrestationCommandDate
            et ACO.InvoiceItem */
            if($cmd instanceof PrestationCommand) {
                $aco->setPrestationFactured(ActivatedChainOperation::FACTURED_NONE);
                $aco->setPrestationCommandDate(NULL);
                $aco->setInvoiceItem($false);
                saveInstance($aco, $retURL);
            }
            unset($aco, $actCollection);
        } // for j
        // cascade sur les opérations et taches
        deleteInstance($ach, $retURL);
    }
    unset($item);
}

// Code spécifique à la commande de cours
if ($cmd instanceof CourseCommand) {
    $ccp = $cmd->getAeroConcreteProduct();
    // mise à jour des potentiels virtuels de l'appareil
    $ccp->updatePotentials(array(
        array(
            'attributes' => array('VirtualHourSinceNew', 'VirtualHourSinceOverall'),
            'value' => -(DateTimeTools::getHundredthsOfHour($cmd->getDuration()))
	    ))
    );
    saveInstance($ccp, $retURL);
}

// Code spécifique à la commande de prestation
$invoiceMapper = Mapper::singleton('Invoice');
$occLocMapper = Mapper::singleton('OccupiedLocation');
$lemMapper = Mapper::singleton('LocationExecutedMovement');

if ($cmd instanceof PrestationCommand) {
    $invoice = $invoiceMapper->load(array('Command' => $cmd->getId()));
    if ($invoice instanceof Invoice) {
        // maj des LEM et OccupiedLocation des InvoiceItem dans
        // InvoiceItem::delete() (méthode surchargée)
        // Invoice::delete() est aussi surchargée
        deleteInstance($invoice, $retURL);
        unset($invoice);
    }
}
// suppression de l'evenntuel CommandExpeditionDetail lié
$ced = $cmd->getCommandExpeditionDetail();
if ($ced instanceof CommandExpeditionDetail) {
    deleteInstance($ced, $retURL);
}

$SupplierCustomer = $cmd->getSupplierCustomer();
if (!$cmd->getIsEstimate() && $SupplierCustomer instanceof SupplierCustomer) {
    // Gestion de l'acompte eventuel
    if ($cmd->getInstallment() > 0) {
        $SupplierCustomer->setUpdateIncur($SupplierCustomer->getUpdateIncur() +
            $cmd->getInstallment());
        saveInstance($SupplierCustomer, $retURL);
    }
    // gestion de la remise par pourcentage du CA annuel, il faut la diminuer 
    // du montant HT de la commande % purcentage paramétré 
    $date = $cmd->getCommandDate();
    $totalHT = $cmd->getTotalPriceHT();
    $percent = $SupplierCustomer->getAnnualTurnoverDiscountPercent($date);
    if (false !== $percent) {
        // si sujet à cette remise
        $amount = $totalHT * ($percent/100);
        // IMPORTANT: le montant passé est négatif !
        $SupplierCustomer->updateAnnualTurnoverDiscount(-$amount, $date);
    }
}

// suppression de la commande
deleteInstance($cmd, $retURL);
//exit;
/*  Commit de la transaction  */
if (Database::connection()->HasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    Database::connection()->rollbackTrans();
    die('erreur sql');
} else {
    Database::connection()->completeTrans();
    require_once('Objects/Alert.const.php');
    $alert = Object::load('Alert', ALERT_COMMAND_DELETE);
    $alert->prepare(array('Numcde' => $cmdNo, 'UserName' => $auth->getIdentity()));
    $alert->send(); // on envoie l'alerte
}

// Redirect vers la liste des commandes
Tools::redirectTo($retURL);
exit;

?>
