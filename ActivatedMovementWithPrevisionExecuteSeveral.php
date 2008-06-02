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
require_once('Objects/MovementType.const.php');
require_once('ActivatedMovementTools.php');
require_once('ExecutionTools.php');
require_once('ProductionTaskValidationTools.php');
require_once('Objects/Alert.const.php');
require_once('ExecutedMovementTools.php');
require_once('AlertSender.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, 
        UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
        UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR));
$session = Session::Singleton();
SearchTools::prolongDataInSession();
//Database::connection()->debug = true;

// Verifications sur les acm selectionnes
// Note: on refait la verif si on a selectionne un Location pour des entrees!
checkBeforeSeveralMovements();


// Tous les acm selectionnes etant lies a la meme cmde
$acm = Object::load('ActivatedMovement', $_REQUEST['acmId'][0]);
$entrieExit = Tools::getValueFromMacro($acm, '%Type.EntrieExit%');
$cmd = $acm->getProductCommand();
$returnURL = 'ActivatedMovementList.php';

// Si ce sont des entrees normales, on affiche un select pour choisir un Location
if (!isset($_REQUEST['check']) && $entrieExit == ENTREE) {
    $uac = $auth->getUser();
    $ProfileId = $auth->getProfile();
    $UserConnectedActorId = $auth->getActorId();
    $siteIds = $uac->getSiteCollectionIds();
    $LPQCollection = new Collection();
    $HTMLLocationSelect = getHTMLLocationSelect($LPQCollection, $ProfileId,
            $UserConnectedActorId, $siteIds);
    
    $smarty = new Template();
    $smarty->assign('formAction', $_SERVER['PHP_SELF']);
    $smarty->assign('returnURL', $returnURL);
    $smarty->assign('HTMLLocationSelect', $HTMLLocationSelect);
    $smarty->assign('hiddenAcmIds', UrlTools::buildHiddenFieldsFromURL('acmId'));
    Template::page(
            _('Execute several movement'),
            $smarty->fetch('ActivatedMovement/ACMWithPrevExecuteSeveral.html')
        );
    exit;
}


$AlertArray = array();

/* On demarre une transaction */
Database::connection()->startTrans();

foreach ($_REQUEST['acmId'] as $acmId) {
    $acm = Object::load('ActivatedMovement', $acmId);
    $exm = $acm->getExecutedmovement();
    if ($exm !== false) {
        die('Error!!');
    }
    else {
        $exm = $acm->createExecutedMovement($acm->getQuantity(), $acm->getProductId());
    }
    saveInstance($exm, $returnURL);
    
    $acm->setState(ActivatedMovement::ACM_EXECUTE_TOTALEMENT);
    saveInstance($acm, $returnURL);
    $LPQMapper = Mapper::singleton('LocationProductQuantities');
    $pdtId = $acm->getProductId();
    $Product = $acm->getProduct();
    
    if ($entrieExit == SORTIE) {
        $Filter = getLpqFilter($pdtId);
        $LPQCollection = $LPQMapper->loadCollection($Filter);
        // Cette coll ne contient qu'un et un seul element (controle en amont)
        $lpq = $LPQCollection->getItem(0);
        // Servira pour le BL (dommage de mettre ca ici, dans la boucle...
        $siteId = Tools::getValueFromMacro($lpq, '%Location.Store.StorageSite.Id%');
        $location = $lpq->getLocation();  // servira pour creation du LEM
        $newQty = $lpq->getRealQuantity() - $acm->getQuantity();
        if ($newQty == 0) {  // On suppr le LPQ si qty arrive a 0
            deleteInstance($lpq, $returnURL);
        } else {
            $lpq->setRealQuantity($newQty);
            saveInstance($lpq, $returnURL);
        }
    }
    else {  // Si c'est une entree, on cree le LPQ si besoin
        $locId = $_REQUEST['locId'];
        $lpq = $LPQMapper->load(array('Product' => $pdtId, 'Location' => $locId));
        if (!($lpq instanceof LocationProductQuantities)) {
            $lpq = Object::load('LocationProductQuantities');
            $lpq->setProduct($pdtId);
            $lpq->setLocation($locId);
            $lpq->setActivated(1);
        }
        $lpq->setRealQuantity($lpq->getRealQuantity() + $acm->getQuantity());
        saveInstance($lpq, $returnURL);
        $location = $lpq->getLocation();  // servira pour creation du LEM
    }
    
    //création du LEM
    $LEMParams = array(
            'ExecutedMovement' => $exm,
            'Product' => $pdtId,
            'Date' => $exm->getEndDate(),
            'Location' => $location,
            'Quantity' => $acm->getQuantity());
    
    if ($acm->getHasBeenFactured() == ActivatedMovement::ACM_FACTURE) {
        $InvoiceItemMapper = Mapper::singleton('InvoiceItem');
        $InvoiceItem = $InvoiceItemMapper->load(array('ActivatedMovement' => $acm->getId()));
        if ($InvoiceItem instanceof InvoiceItem) {
            $LEMParams['InvoiceItem'] = $InvoiceItem;
        }
    }
    
    $LEM = createLocationExecutedMovement($LEMParams, $returnURL);
    
    // Creation des box si sortie de stock
    if ($entrieExit == SORTIE) {
        $LEM->createBoxes();
    }
    
    // On met a jour le RealActor de l'ACO associee
    $acoId = Tools::getValueFromMacro($acm, '%ActivatedChainTask.ActivatedOperation.Id%');
    $aco = Object::load('ActivatedChainOperation', $acoId);
    if (!Tools::isEmptyObject($aco)) {
        $aco->setRealActor($auth->getActor());
        saveInstance($aco, $returnURL);
    }
    
    // Verif que la qte totale de pdt en stock est > a la Qte minimum autorisee
    // Qte totale en stock de ce pdt
    $TotalRealStockQuantity = $Product->getRealQuantity();
    // Alerte Qte reelle, qui sera envoyee si la transaction MySql se passe bien
    if ($TotalRealStockQuantity <= $Product->getSellUnitMinimumStoredQuantity()) {
        $AlertArray[] = array(ALERT_STOCK_QR_MINI, $Product);
    }
    
}

// On met a jour l'etat de LA commande si besoin
$UpdateCommand = $cmd->updateState();
saveInstance($cmd, $returnURL);
    
/* Gestion des erreurs */
if (Database::connection()->hasFailedTrans()) {
    Database::connection()->rollbackTrans();
    Template::errorDialog(E_ERROR_SQL, $_GET['returnURL']);
    exit;
}
/*   On commite la transaction  */
Database::connection()->completeTrans();

// Pour edition eventuelle d'un BL, determination du site
if ($entrieExit == SORTIE) {
    $Site = Object::load('StorageSite', $siteId);
    $OtherMovementBeforBL = $cmd->getOtherPossibleMovement(0, $Site);
        if(false === $OtherMovementBeforBL) {
            $returnURL .= '?editBL=1' . '&cmdId=' . $cmd->getId();
        }
}

for ($i=0;$i<count($AlertArray);$i++) {
    $alert = Object::load('Alert', $AlertArray[$i][0]);
    $Product = $AlertArray[$i][1];
    AlertSender::sendStockAlert($alert->getId(), $Product);
    unset($alert);
}
    
Tools::redirectTo($returnURL);
exit;
    
?>