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
 // utile ici pour la mise en session
require_once('Objects/LocationProductQuantities.php');
require_once('Objects/LocationConcreteProduct.php');
require_once('Objects/ConcreteProduct.php');
require_once('Objects/AeroConcreteProduct.php');
require_once('Objects/Product.php');
require_once('Objects/AeroProduct.php');
require_once('Objects/Location.php');
require_once('Objects/ActivatedChainTask.php');
require_once('ActivatedMovementTools.php');
require_once('ExecutionTools.php');
require_once('ProductionTaskValidationTools.php');
require_once('Objects/Alert.const.php');
require_once('Objects/LocationExecutedMovement.php');
require_once('ExecutedMovementTools.php');
require_once('AlertSender.php');

$auth = Auth::Singleton();
$session = Session::Singleton();
SearchTools::prolongDataInSession();
//Database::connection()->debug = true;

// Pour la confirm des donnees rentrees suite au message de demande de confirm
// On est passe par un message de confirmation
if (isset($_GET['ok'])) {
     //necessaire pour recuperer les infos rentrees dans le formulaire en cas
     // de confirmation
    $SessionVar = array('MvtTypeEntrieExit', 'ActivatedMvtId', 'lpqId', 'QuantityArray',
                         'EnvisagedQuantity', 'Product_id', 'FormSubmitted',
                          'Comment', 'EditBL', 'LEM', 'CancellationType', 'locId');
    session2Post($SessionVar);  // met le contenu de $_SESSION dans $_POST
}

if (isset($_REQUEST['LEM']) && $_REQUEST['LEM'] > 0) {  // c'est une reintegration!
    $CancelledMovement = Object::load('LocationExecutedMovement', $_REQUEST['LEM']);
    $cancelLink = 'LocationExecutedMovementDelete.php?LEM='
            . $_REQUEST['LEM'] . '&ok=1&CancellationType=' . $_REQUEST['CancellationType'];
    $okLink = $_SERVER['PHP_SELF'] . '?ok=1&LEM=' . $_REQUEST['LEM']
            . '&CancellationType=' . $_REQUEST['CancellationType']
            . '&TracingMode=' . $_REQUEST['TracingMode'];
    $FinishLink = (isset($_SESSION['firstarrival']))?
            'LocationExecutedMovementList.php?firstarrival=1':
            'LocationExecutedMovementList.php';
}
else{
    $cancelLink = 'ActivatedMovementAddWithPrevision.php?ActivatedMvtId='
            . $_REQUEST['ActivatedMvtId'];
    $okLink = $_SERVER['PHP_SELF'] . '?ActivatedMvtId=' . $_REQUEST['ActivatedMvtId']
            . '&ok=1&Comment=' . htmlentities(urlencode($_REQUEST['Comment']))
            . '&TracingMode=' . $_REQUEST['TracingMode']
            . '&MvtTypeEntrieExit=' . $_REQUEST['MvtTypeEntrieExit'];
    $FinishLink = 'ActivatedMovementList.php';
}
$cancelLink .= '&Comment='.htmlentities(urlencode($_REQUEST['Comment']));
$Product = Object::load('Product', $_POST['Product_id']);


//////////////////// Modifs des donnees de la commande /////////////////////////
//on veut changer le produit
if ((isset($_POST['ChangeProduct'])) && ($_POST['ChangeProduct'] == 'yes')) {
    if (isset($_SESSION['LCPCollection'])) {
        unset($_SESSION['LCPCollection']);
    }
    Template::confirmDialog(_('Do you want to change product reference ?'),
                      $cancelLink.'&Product='.$_POST['Product'], $cancelLink);
    exit;
}

// On veut creer un nouvel emplacement (on a clique sur le bouton 'plus')
// pour le produit ssi on est en entree
elseif (isset($_POST['plus_x']) && $_POST['plus_x'] == 1) {
    $cancelLink .= '&Product=' . $_POST['Product_id'];
    if ($_POST['newLocationId'] == 0) {
        Template::errorDialog(_('Please select a location.'), $cancelLink);
        exit;
    }
    $LPQMapper = Mapper::singleton('LocationProductQuantities');
    //  Si des Nouveaux LPQ sont crees, ils sont en session avant d'etre sauves en base
    if (isset($_SESSION['LPQCollection'])) {
        $LPQCollectionForSession = unserialize($_SESSION['LPQCollection']);
    }
    else {
        $LPQCollectionForSession = new Collection();
    }
    $LPQ = Object::load('LocationProductQuantities');
    $LPQ->setProduct($Product);
    $LocationMapper = Mapper::singleton('Location');
    $Location = $LocationMapper->load(array('Id' => $_POST['newLocationId']));
    $LPQ->setLocation($Location);
    $LPQCollectionForSession->setItem($LPQ);

    /*  Mise en session  */
    $LPQCollectionSerialized = serialize($LPQCollectionForSession);
    $session->register('LPQCollection', $LPQCollectionSerialized, 10);  //  mise en session pour 10 pages

    Tools::redirectTo($cancelLink);
    exit;
}
///////////////////////// fin modif donnes de la commande ///////////////////////////


elseif (isset($_POST['FormSubmitted'])) { // on valide le formulaire (clic sur 'ok')
    $cancelLink .= '&Product=' . $_POST['Product_id'];
    $AlertArray = array();  // contiendra si necessaire les alertes a envoyer
    
    // Effectue des controles sur les saisies et gestion des formats de 
    // nombres selon les locales
    checkBeforeMovement($cancelLink);

    $LPQMapper = Mapper::singleton('LocationProductQuantities');
    $ActivatedMovement = Object::load('ActivatedMovement', $_POST['ActivatedMvtId']);
    $ProductCommandItem = $ActivatedMovement->getProductCommandItem();
    $TotalRealQuantity = array_sum($_POST['QuantityArray']);
    
    
    // On blinde pour ne pas que 2 users distincts ne valident le meme mvt
    // en ayant affiche le form de saisie simultanement
    if ($ActivatedMovement->getState() == ActivatedMovement::ACM_EXECUTE_TOTALEMENT && !isCancellation()){
        Template::errorDialog(
            _('Movement could not be executed because it was already executed.'),
            $FinishLink
        );
        exit;
    } 
    
    // Qte nulle interdite si reintegration
    if ($TotalRealQuantity <= 0 && isCancellation()) {
       Template::errorDialog(_('Please provide a positive quantity.'), $cancelLink);
       exit;
    }

    // donc si on n'a pas du confirmer avant ou si pdt<>celui prevu et qte>celle prevue
    if (!isset($_GET['ok']) || isset($GET['chgPdt'])) {
        $PostVar = array('MvtTypeEntrieExit', 'ActivatedMvtId', 'lpqId', 'QuantityArray',
                         'Comment', 'EnvisagedQuantity', 'Product_id', 'FormSubmitted',
                         'LEM', 'CancellationType', 'locId');
        // On met le contenu de $_POST dans $_SESSION
        Post2Session($PostVar, $session, 2);
        Post2Session(array('EditBL'), $session, 2, 0);

        // Message d'erreur si une qte >0 a ete saisie pour un LPQ finalement desactive
        ActivatedLPQControl();

        if ($_POST['MvtTypeEntrieExit'] == MovementType::TYPE_ENTRY
                    && $TotalRealQuantity > $_POST['EnvisagedQuantity']) {
            // Si pas une reintegration en stock
            if (!isCancellation()) {
                Template::confirmDialog(
                    _('Total quantity exceeds expected quantity, do you still want to execute the movement ?'),
                    $okLink, $cancelLink);
            }
            else {
                Template::errorDialog(
                    _('Movement could not be executed: returned quantity exceeds issued quantity.'),
                    $cancelLink);
            }
            exit;
        }
        if ($_POST['MvtTypeEntrieExit'] == MovementType::TYPE_EXIT) {
            // Produit commande
            $InitialProduct = $ActivatedMovement->getProduct();
            $InitialProductId = $InitialProduct->getId();

            // Les test sur $GET['chgPdt'] permet de ne pas afficher le meme
            // msge d'erreur en boucle
            // Verifie si les qtes sont suffisantes en stock
            checkStockQty($cancelLink);

            if (!isset($GET['chgPdt']) && $TotalRealQuantity > $_POST['EnvisagedQuantity'] ) {
            // S'il y a eu un chgt de Product, on autorise a sortir une Qte > celle prevue
                if ($_POST['Product_id'] != $InitialProductId) {
                    Template::confirmDialog(
                        _('Total quantity exceeds expected quantity, do you still want to execute the movement ?'),
                        $okLink.'&chgPdt=1', $cancelLink);
                    exit;
                }
                // Si le Product est celui commande, on refuse la sortie,
                // A moins que ce soit autorise dans les Preferences
                elseif (!Preferences::get('SupQuantityAuthorized')) {
                    Template::errorDialog(
                        _('Movement could not be executed: issued quantity exceeds expected quantity.'),
                        $cancelLink);
                    exit;
                }
            }
        }
        // Possibilite de mouvement partiel pour une entree ou une sortie
        if ($TotalRealQuantity < $_POST['EnvisagedQuantity'] && !isCancellation()) {
            Template::questionDialog(
            _('Total quantity is lower than expected.<br/>Do you want the movement to be considered executed completely ?<br/>If you answer "No", it will be executed partially.<br/><br/><div align="right"><strong>TOTAL</strong>&nbsp;&nbsp;<strong>PARTIAL</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>'),
            $okLink, $okLink.'&partial=1', $cancelLink);
            exit;
        }
    }

    /*---------------------------------------------------------------------
    -- Cree un EXM correspondant au mvt prevu, ou reprend le mvt partiel --
    ----------------------------------------------------------------------*/
     /* on demarre une transaction */
    Database::connection()->startTrans();

    require_once('Objects/ActivatedMovement.php');
    require_once('Objects/ExecutedMovement.php');
    $State = ((isset($_GET['partial'])) && ($_GET['partial'] == 1))?
            ExecutedMovement::EXECUTE_PARTIELLEMENT:ExecutedMovement::EXECUTE_TOTALEMENT;
    //  sert a incrementer ou decrementer les qtes en stock
    $coef = ($_POST['MvtTypeEntrieExit'] == MovementType::TYPE_ENTRY || isCancellation())?1:-1;

    // Si deja partielmt execute ou si reintegr. en stock, on recupere l'EXM correspdt
    $EXMMapper = Mapper::singleton('ExecutedMovement');

    /*  Si reprise de partiel!!  */
    if ($ActivatedMovement->getState() == ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT || isCancellation()) {
        $ExecutedMovement = $EXMMapper->load(
                array('ActivatedMovement' => $_POST['ActivatedMvtId']));
        if (!($ExecutedMovement instanceof ExecutedMovement)) {
            Template::errorDialog(
                _('Movement could not be executed: no related partial movement found.'),
                $_GET['returnURL']);
            exit;
        }
        // qty mouvementee jusque la
        $InitialRealQuantity = $ExecutedMovement->getRealQuantity();
        $initialEXMState = $ExecutedMovement->getState();
        $Qty = (isCancellation())?-$TotalRealQuantity:$TotalRealQuantity;
        $ExecutedMovement->setRealQuantity($InitialRealQuantity + $Qty);
        // si reintegration en stock
        if (isCancellation()) {
            // utilise plus loin pour les Qtes virt du pdt commande
            $InitialState = $ActivatedMovement->getState();
            $ActivatedMovement->setState(ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT);
            $ExecutedMovement->setState(ExecutedMovement::EXECUTE_PARTIELLEMENT);
        }
        else { // met a jour l'etat de l'ActivatedMovement a TERMINE ou PARTIEL
            $ActivatedMovement->setState($State + 1);
            $ExecutedMovement->setState($State);
        }
        $ExecutedMovement->setEndDate(date('Y-m-d H:i:s'));
        $ExecutedMovement->setComment(
                stripcslashes(stripcslashes(html_entity_decode(urldecode($_POST['Comment'])))));

    }
    else {  // on cree un nouveau ExectdMovt
        $ExecutedMovement = $ActivatedMovement->createExecutedMovement(
                $TotalRealQuantity, $_POST['Product_id'],
                stripcslashes(stripcslashes(html_entity_decode(urldecode($_POST['Comment'])))),
                $State);
        $ActivatedMovement->setState($State + 1);
    }
    // mise à jour de l'état de l'ack
    if (isset($_SESSION['ActivatedChainTask'])) {
        if ($ActivatedMovement->getState() == ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT) {
            handleTaskAction(ACTION_STOP, $_SESSION['ActivatedChainTask'], $auth->getUser());
        } else {
            handleTaskAction(ACTION_FINISH, $_SESSION['ActivatedChainTask'], $auth->getUser());
        }
    }
    saveInstance($ActivatedMovement, $cancelLink);
    saveInstance($ExecutedMovement, $cancelLink);

     //on met a jour la table LocationExecutedMovement
    while(isset($_POST['lpqId'][count($_POST['lpqId'])-1])
                && $_POST['lpqId'][count($_POST['lpqId'])-1] == 0) {
        // suppression des items correspondant a ceux en session,
        // s'il y en a, pour garder le meme ordre ds la collection
        $lpqId = array_pop($_POST['lpqId']);
    }

    $lpqIdArray = (count($_POST['lpqId']) == 0)?array(0):$_POST['lpqId'];
    $LPQCollection = $LPQMapper->loadCollection(
            array('Id' => $lpqIdArray), array('Location.Name' => SORT_ASC));
    if (isset($_SESSION['LPQCollection'])) {
        $LPQCollectionForSession = unserialize($_SESSION['LPQCollection']);
        for ($i=0;$i<$LPQCollectionForSession->getCount();$i++) {
            $LPQCollection->setItem($LPQCollectionForSession->getItem($i));
        }
    }
    $LEMParams = array(
            'ExecutedMovement' => $ExecutedMovement,
            'Product' => $Product,
            'Date' => $ExecutedMovement->getEndDate());
    // si reintegration en stock
    if (isCancellation()) {
        $LEMParams['CancelledMovement'] = $CancelledMovement;
        $LEMParams['Cancelled'] = $_REQUEST['CancellationType'];
    }
    // Si l'ACM est FACTURE completement, on renseigne la FK LEM.InvoiceItem
    elseif ($ActivatedMovement->getHasBeenFactured() == ActivatedMovement::ACM_FACTURE
            && $LEMParams['Quantity'] > 0) {
        $InvoiceItemMapper = Mapper::singleton('InvoiceItem');
        $InvoiceItem = $InvoiceItemMapper->load(
                array('ActivatedMovement' => $_POST['ActivatedMvtId']));
    }

	$lpqCount = $LPQCollection->getCount();
    $Site = false;
    for($i = 0; $i < $lpqCount; $i++) {
        if (isset($_POST['QuantityArray'][$i]) && $_POST['QuantityArray'][$i] > 0) {
            $lpq = $LPQCollection->getItem($i);
            $LEMParams['Location'] = $lpq->getLocation();
            $LEMParams['Quantity'] = $_POST['QuantityArray'][$i];

            if ($LEMParams['Quantity'] > 0 && isset($InvoiceItem)
                    && !Tools::isEmptyObject($InvoiceItem)) {
                $LEMParams['InvoiceItem'] = $InvoiceItem;
            }

            //création du LEM
            $LEM = createLocationExecutedMovement($LEMParams, $cancelLink);


            // Creation des LEMConcreteProduct si mode de suivi = SN ou lot
            if ($_REQUEST['TracingMode'] != 0) {
                $LEMCPCollection = createLEMConcreteProduct(
                        $LEM, $_SESSION['LCPCollection'], $_REQUEST['TracingMode'],
                        (isset($CancelledMovement))?$_REQUEST['LEM']:0, $cancelLink);
            }


            // Creation des box si sortie de stock
            if ($_POST['MvtTypeEntrieExit'] == MovementType::TYPE_EXIT
                    && $ActivatedMovement->getTypeId() != SORTIE_INTERNE) {
                $LEM->createBoxes();
            }

            //  MAJ des LPQ et si le LPQ est en session seulemt,
            // il faut le sauver en base
            if (empty($_POST['lpqId'][$i])) {
                $lpq->setId($LPQMapper->generateId());
            }
            $lpq->setRealQuantity($lpq->getRealQuantity()
                    + ($coef * $_POST['QuantityArray'][$i]));

            // Creation/suppr/MAJ des LocationConcreteProduct
            // si mode de suivi = SN ou lot
            if ($_REQUEST['TracingMode'] != 0
                    && false == updateLocationConcreteProduct($lpq, $LEMCPCollection)) {
                Template::errorDialog(E_MSG_TRY_AGAIN, $cancelLink);
                exit;
            }
            // Pour edition eventuelle d'un BL, determination du site
            if ($_SESSION['MvtTypeEntrieExit'] == MovementType::TYPE_EXIT && !$Site) {
                $Site = Object::load(
                        'Site',
                        Tools::getValueFromMacro(
                                $lpq, '%Location.Store.StorageSite.Id%')
                );
            }
            // Suppression des emplacements ayant une quantite nulle
            if ($lpq->getRealQuantity() == 0) {
                deleteInstance($lpq, $cancelLink);
            }
            else {
                saveInstance($lpq, $cancelLink);
            }

            unset($LEM, $lpq, $LEMCPCollection);
        }
    }

    //$OrderedProductId = $ProductCommandItem->getProductId();
    $OrderedProduct   = $ActivatedMovement->getProduct();
    $OrderedProductId = $OrderedProduct->getId();

    // si reintegration en stock
    if (isCancellation()) {
        $CancelledMovement->setCancelled(-1);
        saveInstance($CancelledMovement, $cancelLink);
        // le pdt commande

        $OrderedQuantity = $ActivatedMovement->getQuantity();

        // MAJ des VirtualQty : seulement pour le Pdt reintegre et si != celui cmde
        // Oubien si le mvt etait total, avec Qte < Qte commandee
         if ($OrderedProductId != $Product->getId()) {
            $Product->setSellUnitVirtualQuantity(
                    $Product->getSellUnitVirtualQuantity() + $TotalRealQuantity);
            saveInstance($Product, $cancelLink);
         }
         elseif ($initialEXMState == ExecutedMovement::EXECUTE_TOTALEMENT
                && $InitialRealQuantity != $OrderedQuantity) {
            $Product->setSellUnitVirtualQuantity($Product->getSellUnitVirtualQuantity()
                    - ($OrderedQuantity - $InitialRealQuantity));
            saveInstance($Product, $cancelLink);
         }
    }
    else {
        $AlertArray = $ExecutedMovement->setProductVirtualQuantity();
        // Envoi d'Alerte si Product mouvemente est different de celui commande
        if ($OrderedProductId != $Product->getId()) {
            $AlertArray[] = array(ALERT_PRODUCT_CHANGED, $Product,
                    $OrderedProduct->getBaseReference());
         }
    }
    $Command = $ActivatedMovement->getProductCommand();

    // On met a jour l'etat de la commande SI PAS MOUVEMENT INTERNE
    if (!in_array($ActivatedMovement->getTypeId(), array(ENTREE_INTERNE, SORTIE_INTERNE))) {
        require_once('Objects/Command.const.php');
        // dans ce cas, pas besoin d'aller voir
        // l'etat des autres mvts lies a la commande
        if ($State != ExecutedMovement::EXECUTE_TOTALEMENT && $_POST['MvtTypeEntrieExit'] == MovementType::TYPE_ENTRY) {
            $Command->setState(Command::LIV_PARTIELLE);
        }
        // MAJ l'etat de la Command en fonction aussi des autres mvts lies a elle
        else {
            $UpdateCommand = $Command->updateState();
        }
        saveInstance($Command, $cancelLink);
    }

    // On met a jour le RealActor de l'ACO associee
    $acoId = Tools::getValueFromMacro($ActivatedMovement,
            '%ActivatedChainTask.ActivatedOperation.Id%');
    $aco = Object::load('ActivatedChainOperation', $acoId);
    if (!Tools::isEmptyObject($aco)) {
        $aco->setRealActor($auth->getActor());
        saveInstance($aco, $cancelLink);
    }

    /* gestion des erreurs */
    if (Database::connection()->hasFailedTrans()) {
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_ERROR_SQL, $_GET['returnURL']);
        exit;
    }
    /*   on commite la transaction  */
    Database::connection()->completeTrans();

/////////////////// EDITION DU BL ////////////////////
    /* Dans le cas d'une sortie différente d'une sortie interne, on regarde
     * s'il existe des ActivatedMvmts de sortie de stock autres que celui
     * passe en parametre prevus pour cette commande en etat partiel ou a faire
     * avec une quantite en stock (pour le produit dans des emplacemts situes
     * dans le meme StorageSite) > 0, pour savoir si on force l'edition d'un BL */
    $activatedMovement = Object::load(
            'ActivatedMovement', $_SESSION['ActivatedMvtId']);
    if (($_SESSION['MvtTypeEntrieExit'] == MovementType::TYPE_EXIT)
        && ($activatedMovement->getTypeId() != SORTIE_INTERNE)) {
        $OtherMovementBeforBL = $Command->getOtherPossibleMovement(
                $ActivatedMovement, $Site);

        //if ((isset($_POST['EditBL']) && $_POST['EditBL'] == 1)
        //            || false === $OtherMovementBeforBL) {
        // si pas de lem, pas de bl
        $lemCol=$ExecutedMovement->getLocationExecutedMovementCollection();
        if(false === $OtherMovementBeforBL && $lemCol->getCount() > 0) {
            $FinishLink .= '?editBL=1' . '&cmdId=' . $Command->getId();
        }
    }
//////////////////////////////////////////////////////

    // Verif que la qte totale de pdt en stock est > a la Qte minimum autorisee
    // Qte totale en stock de ce pdt
    $TotalRealStockQuantity = $Product->getRealQuantity();
    // Alerte Qte reelle
    if ($TotalRealStockQuantity <= $Product->getSellUnitMinimumStoredQuantity()) {
        $AlertArray[] = array(ALERT_STOCK_QR_MINI, $Product);
    }

    if ($TotalRealQuantity > $_POST['EnvisagedQuantity']) {
        // Alert si qte entree > celle prevue(interdit pour une sortie)
        $AlertArray[] = array(ALERT_MOVEMENT_QTY_OVER, $Product);
    }

    $partialMvtBodyAddon = $comment = $CancellationType = '';

    // Si mouvement partiel
    if ($ExecutedMovement->getState() == ExecutedMovement::EXECUTE_PARTIELLEMENT && (!isCancellation())) {
        $AlertArray[] = array(ALERT_PARTIAL_MOVEMENT, $Product);

        $productTMP   = $ActivatedMovement->getProduct();
        if ($Product->getId()!= $productTMP->getId()) {
            $partialMvtBodyAddon .= sprintf(
                _('Expected reference (%s) has been replaced by %s.'),
                Tools::getValueFromMacro($ProductCommandItem, '%Product.BaseReference%'),
                $Product->getBaseReference() );
        }
        if ((is_string($ExecutedMovement->getComment()))
                && !($ExecutedMovement->getComment() == "")) {
            $comment = _('Comment: ') . $ExecutedMovement->getComment();
        }
    }
    // si reintegration en stock
    if (isCancellation()) {
        $AlertArray[] = array(ALERT_REINTEGRATION_STOCK, $Product);
        $comment = stripcslashes(stripcslashes(html_entity_decode(urldecode($_POST['Comment']))));
        $cancellationTypeArray = LocationExecutedMovement::getCancelledConstArray();
        $CancellationType = $cancellationTypeArray[$_REQUEST['CancellationType']];
        // Si deja facture, Alert supplementaire
        if ($ActivatedMovement->getHasBeenFactured() == 1) {
            $AlertArray[] = array(ALERT_REINTEGRATION_STOCK_FACTURED, $Product);
        }
    }

    for ($i=0;$i<count($AlertArray);$i++) {
        if (is_array($AlertArray[$i])) {
            $alert = Object::load('Alert', $AlertArray[$i][0]);
            $Product = $AlertArray[$i][1];
        } else {
            $alert = $AlertArray[$i];
        }
        if (AlertSender::isStockAlert($alert->getId())) {
            AlertSender::sendStockAlert($alert->getId(), $Product);
        } else {
            $OrderedBaseReference = (isset($AlertArray[$i][2]))?$AlertArray[$i][2]:'';
            $params = array(
                'ProductBaseReference' => $Product->getBaseReference(),
                'OrderedBaseReference'=> $OrderedBaseReference,
                'ProductMinimumStock' => $Product->getSellUnitMinimumStoredQuantity(),
                'ProductName' => $Product->getName(),
                'ProductSupplierName' => Tools::getValueFromMacro(
                    $Product, '%MainSupplier.Name%'),
                'Quantity' => array_sum($_POST['QuantityArray']),
                'TotalRealQuantity' => $TotalRealQuantity,
                'EnvisagedQuantity' => $_POST['EnvisagedQuantity'],
                'NumCde' => $Command->getCommandNo(),
                'PartialMvtBodyAddon' => $partialMvtBodyAddon,
                'Comment' => $comment,
                'CancellationType' => $CancellationType
            );
            $alert->prepare($params);
            $alert->send();  // on envoie l'alerte
        }
        unset($alert);
    }
    if (isset($_SESSION['LPQCollection'])) {
        unset($_SESSION['LPQCollection']);
    }
    Tools::redirectTo($FinishLink);
    exit;
}

?>
