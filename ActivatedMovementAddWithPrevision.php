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
require_once('Objects/SellUnitType.const.php');
require_once('ActivatedMovementTools.php');
require_once('ProductionTaskValidationTools.php');
require_once('Objects/ActivatedMovement.php');
require_once('Objects/ActivatedChainTask.php');
 // utile ici pour session et serialisation

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN,
    UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
    UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR));
$session = Session::singleton();
$uac = $auth->getUser();
$siteIds = $uac->getSiteCollectionIds();
//Database::connection()->debug = true;

SearchTools::prolongDataInSession();

if (empty($_GET['ActivatedMvtId'])){
    Template::errorDialog(_('No movement selected'), 'ActivatedMovementList.php');
    exit;
}
$ProfileId = $auth->getProfile();
$UserConnectedActorId = $auth->getActorId();

$ActivatedMovement = Object::load('ActivatedMovement', $_GET['ActivatedMvtId']);
$MvtTypeEntrieExit = Tools::getValueFromMacro($ActivatedMovement, '%Type.EntrieExit%'); // E/S: 0/1
$MvtTypeId         = Tools::getValueFromMacro($ActivatedMovement, '%Type.Id%');

// Test si l'ActivatedMovement n'est pas deja en cours ou bloque, ou execute totalement
if (in_array($ActivatedMovement->getState(), array(ActivatedMovement::ACM_EN_COURS, ActivatedMovement::ACM_EXECUTE_TOTALEMENT, ActivatedMovement::BLOQUE))) {
    $msge = (ActivatedMovement::BLOQUE == $ActivatedMovement->getState())?_('Selected movement is locked. '):
            _('Selected movement is already being executed or was executed completely.');
    $retURL = isset($_GET['returnURL'])?$_GET['returnURL']:'ActivatedMovementList.php';
    Template::errorDialog($msge . _('You cannot execute it.'), $retURL);
    exit;
}

// démarre ou redémarre l'ack liée; note: on ne sauve pas encore la tache, on 
// la met en session pour la sauver au moment ou l'execution est validée
$ack = $ActivatedMovement->getActivatedChainTask();
if ($ack instanceof ActivatedChainTask) {
    if ($ack->getState() == ActivatedChainTask::STATE_STOPPED) {
        handleTaskAction(ACTION_RESTART, $ack, $auth->getUser(), false, false, false, false);
    } else {
        handleTaskAction(ACTION_START, $ack, $auth->getUser(), false, false, false, false);
    }
    $session->register('ActivatedChainTask', $ack, 2);
}

//Produit commande
$OrderedProductId = Tools::getValueFromMacro($ActivatedMovement, '%Product.Id%');

// Recupere le nouveau produit si un product est selectionné
// ou sinon le produit commande
$ProductId = (isset($_GET['Product']) && ($_GET['Product'] != 0))?
        $_GET['Product']:$OrderedProductId;
$Product = Object::load('Product', $ProductId);
$orderedProduct = Object::load('Product', $OrderedProductId);

// Collection des Products par lesquels on peut substituer Product
// Si pas pilote, filtre sur les Product vus
$SitOwnerId = (in_array($ProfileId,
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)))?
    0:$UserConnectedActorId;
// param 1: pour les LPQ et Location actives
$SubstitutCollection = $orderedProduct->getProductCollectionForSubstitution($SitOwnerId, 1);

// Recuperation des infos sur les produits pour le SELECT
$ProductMapper = Mapper::singleton('Product');

$supplier = $supplierID = 0;
$command = $ActivatedMovement->getProductCommand();
if ($command instanceof Command) {
    $supplier = $command->getExpeditor();
    $supplierID = $command->getExpeditorId();
}

// Si SORTIE, on n'affiche ds le select que les Product substituts qui sont en stock et actives!
if ($MvtTypeEntrieExit == SORTIE) {
    // Collection des Product en stock et actives, et substituts
    $ProductCollection = $SubstitutCollection;
} elseif ($MvtTypeEntrieExit == ENTREE) {
    $supplierId = Tools::getValueFromMacro($ActivatedMovement, '%ProductCommand.Expeditor.Id%');
    $filters = array(
        SearchTools::NewFilterComponent('Supplier', 'ActorProduct().Actor.Id', 'Equals', $supplierId, 1, 'Product'),
        SearchTools::NewFilterComponent('Activated', '', 'Equals', 1, 1)
    );
    $filter = new FilterComponent(
        SearchTools::filterAssembler($filters),
        new FilterRule('Id', FilterRule::OPERATOR_EQUALS, $orderedProduct->getId())
    );
    $filter->operator = FilterComponent::OPERATOR_OR;
    $order  = array('BaseReference' => SORT_ASC);
    $ProductCollection = $ProductMapper->loadCollection($filter, $order, array('BaseReference'));
}

//$ProductCollection->acceptDuplicate = false;
//$ProductCollection->setItem($orderedProduct);
//$ProductCollection->sort('BaseReference', SORT_ASC);
//die();

for($i = 0; $i < $ProductCollection->getCount(); $i++) {
    $item = $ProductCollection->getItem($i);
    if ($MvtTypeId == ENTREE_NORMALE) {
        $ref = $item->getReferenceByActor($supplier);
        $ref = empty($ref)? $item->getBaseReference() : $ref;
    } else {
        $ref = $item->getBaseReference();
    }
    $ProductIdBaseRefArray[$item->getId()] = $ref;
    unset($item);
}

// Permet de construire un select, avec les refs substituables en couleur!
require_once('ListItems.php');
if ($MvtTypeEntrieExit == SORTIE) { // affichage seulement des substituts
    $monSelectProductHTML = itemsArrayToHtml($ProductIdBaseRefArray, $ProductId);
}
else {  // Affichage des Product, dont ceux substituts affiches en rouge
    $monSelectProductHTML = itemsArrayToHtml($ProductIdBaseRefArray,
                                             $ProductId,
                                             $SubstitutCollection->getItemIds());
}


// si ActivatedMovement deja partiellement execute => on recupere les quantites
// deja mouvementees + Comment
if (ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT == $ActivatedMovement->getState()) {
    $PartialExecutedMovement = $ActivatedMovement->getExecutedMovement();
    if ($PartialExecutedMovement instanceof ExecutedMovement && $PartialExecutedMovement->getId() > 0){
        $PartialQuantity = $PartialExecutedMovement->getRealQuantity();
        $Comment = $PartialExecutedMovement->getComment();
    }
}
// Qty a mouvementer= Qte commandee ou (Qte commandee - Qte deja mouvementee)
// si le mvt e deja ete execute partiellement
$OrderedQty = Tools::getValueFromMacro($ActivatedMovement, '%Quantity%');
$Quantity = (isset($PartialQuantity))?$OrderedQty - $PartialQuantity:$OrderedQty;


// gestion des emplacements autorises pour ce produit TO DO
// on recupere les id et les noms des emplacements deja affectes pour le produit
$LPQMapper = Mapper::singleton('LocationProductQuantities');
$filter = array('Product' => $ProductId,
                'Location.Activated' => 1,
                'Activated' => 1);

if (!in_array($ProfileId,
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW))) {
    $filter = array_merge($filter,
            array('Location.Store.StorageSite.Owner' => $UserConnectedActorId));
}

$LPQCollection = $LPQMapper->loadCollection(
        $filter, array('Location.Name' => SORT_ASC));

/* En cas d'entree, on construit le SELECT qu'il faut afficher.
   Et il est possible que des LPQ aient ete crees en session.  */
if ($MvtTypeEntrieExit == ENTREE) {
    if (isset($_SESSION['LPQCollection'])) {
        $LPQCollectionForSession = unserialize($_SESSION['LPQCollection']);
        for ($i=0;$i<$LPQCollectionForSession->getCount();$i++) {
            $LPQCollection->setItem($LPQCollectionForSession->getItem($i));
        }
    }
    $HTMLLocationSelect = getHTMLLocationSelect($LPQCollection, $ProfileId,
        $UserConnectedActorId, $siteIds);
}


/*  Creation du grid  */
$grid = executionGrid($Product->getTracingMode(), $MvtTypeEntrieExit,
    $LPQCollection, $Quantity);
if ($grid->isPendingAction()) {
    $LPQtyCollection = false;
    $grid->setMapper($LPQMapper);
    $dispatchResult = $grid->dispatchAction($LPQtyCollection);
}
else {
    $ActivatedMvtGrid = $grid->render($LPQCollection);
}



// Affichage du formulaire avec smarty
$_GET['returnURL'] = 'ActivatedMovementList.php';
$returnURL = 'ActivatedMovementList.php';

$Smarty = new Template();
$cmdTitle = _('Execution of an expected movement');
$JSRequirements = array('js/includes/ActivatedMovementAdd.js');
$Smarty->assign('FormAction', 'ActivatedMovementAddWithPrevisionToBeExecuted.php'
                                . '?returnURL=' . $_GET['returnURL']);
$Smarty->assign('ActivatedMvtGrid', $ActivatedMvtGrid);
$Smarty->assign('Quantity', $Quantity);
$Smarty->assign('displayedQuantity', I18N::formatNumber($Quantity, 3, true));
$Smarty->assign('CommandNo', Tools::getValueFromMacro($ActivatedMovement, '%ProductCommand.CommandNo%'));
$Smarty->assign('ActivatedMvt', $_GET['ActivatedMvtId']);
$Smarty->assign('SelectProduct', $ProductId);
$DisplayedProduct = Object::load('Product', $ProductId);  // Produit affiche
$Smarty->assign('ProductName', $DisplayedProduct->getName());
// si on a une uv avec unité de mesure
$sut = $DisplayedProduct->getSellUnitType();
if ($sut instanceof SellUnitType && $sut->getId() > SELLUNITTYPE_UR) {
    $Smarty->assign('SellUnitType', $sut->getShortName());
}
$Smarty->assign('monSelectProductHTML', $monSelectProductHTML);
$Smarty->assign('TracingMode', $Product->getTracingMode());
if ($Product->getTracingMode() > 0) {
    $tmArray = Product::getTracingModeConstArray();
    $Smarty->assign('TracingModeName', $tmArray[$Product->getTracingMode()]);
}


// si reprise de partiel, sert pour ouvrir le popup d'historique
if (isset($PartialExecutedMovement)) {
    $Smarty->assign('ExecutedMovtId', $PartialExecutedMovement->getId());
}

if (isset($_REQUEST['Comment'])){
    $exmComment = stripcslashes(stripcslashes(html_entity_decode(
            urldecode($_REQUEST['Comment']))));
}
else  {
    $exmComment = (isset($Comment))?$Comment:'';
}
$Smarty->assign('Comment', $exmComment);

if ($MvtTypeEntrieExit == ENTREE){
    $Smarty->assign('HTMLLocationSelect', $HTMLLocationSelect);
    //$Smarty->assign('EditBL', ""); // on ne propose pas l'edition de BL si ENTREE
}
/*if (($MvtTypeEntrieExit == SORTIE) &&
    ($MvtTypeId != SORTIE_INTERNE)) {
    $Smarty->assign('EditBL', '<tr class="gris4"><td>
                    <b> ' . _('Print delivery order') . '&nbsp;</b>
                    <input type="checkbox" name="EditBL" value="1" />&nbsp;
                    </td></tr>');
}*/
$Smarty->assign('MvtTypeEntrieExit', $MvtTypeEntrieExit);
$Smarty->assign('MvtTypeName', Tools::getValueFromMacro($ActivatedMovement, '%Type.name%'));
$Smarty->assign('fillPopup', (isset($_SESSION['LCPCollection']))?1:0);
$Smarty->assign('returnURL', $returnURL);

Template::page(
    $cmdTitle,
    $Smarty->fetch('ActivatedMovement/ActivatedMovementAddWithPrevision.html'),
    $JSRequirements
);

?>
