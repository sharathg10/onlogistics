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
require_once('Objects/Alert.const.php');
require_once('ExecutionTools.php');
 // utile ici pour la mise en session
require_once('Objects/LocationProductQuantities.php');
require_once('Objects/LocationConcreteProduct.php');
require_once('Objects/ConcreteProduct.php');
require_once('Objects/AeroConcreteProduct.php');
require_once('Objects/AeroProduct.php');
require_once('Objects/Product.php');
require_once('Objects/Location.php');
require_once('ExecutedMovementTools.php');
require_once('AlertSender.php');

$Auth = Auth::Singleton();
//Database::connection()->debug = true;
$Auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_GESTIONNAIRE_STOCK));

$Product = Object::load('Product', $_POST['Product_id']);
$LPQMapper = Mapper::singleton('LocationProductQuantities');


$cancelLink = 'ActivatedMovementAddWithoutPrevision.php?product=' . $_POST['Product_id']
			. '&MvtType=' . $_POST['MvtType'] . '&Comment=' . htmlentities(urlencode($_POST['Comment']))
			. '&TracingMode=' . $_REQUEST['TracingMode'];

// on veut creer un nouvel emplacement (on a cliqué sur le bouton 'plus' )
// pour le produit possible seulement si on est en entree
if (isset($_POST['plus_x']) && $_POST['plus_x'] == 1) {
    // test superflu, mais erreur notice detectee sur utilisation zarbe
	if (isset($_POST['newLocationId'])) {
		// Si des nouveaux LPQ sont crees, ils sont en session avant d'etre sauves en base
		if (isset($_SESSION['LPQCollection'])) {
	        $LPQCollectionForSession = unserialize($_SESSION['LPQCollection']);
	    }
		else {
			$LPQCollectionForSession = new Collection();
		}
		$LPQ = Object::load('LocationProductQuantities');
		$LPQ->setProduct($Product);
		$Location = Object::load('Location', $_POST['newLocationId']);
		$LPQ->setLocation($Location);
		$LPQCollectionForSession->setItem($LPQ);

		/*  Mise en session  */
		$session = Session::Singleton();
		$LPQCollectionSerialized = serialize($LPQCollectionForSession);
		$session->register('LPQCollection', $LPQCollectionSerialized, 10);  //  mise en session pour 10 pages
	}

	Tools::redirectTo($cancelLink);
	exit;
}

// on valide le formulaire (on a cliqué sur le bouton "ok")
elseif (isset($_POST['FormSubmitted'])) {
    // contiendra si besoin les donnees a envoyer en mail d'alerte stock
	$AlertArray = array();
	
	// Effectue des controle sur les saisies et gestion des formats de 
    // nombres selon les locales
	checkBeforeMovement($cancelLink, false);
	
	// Affiche un message d'erreur si une qte >0 a ete saisie pour un LPQ
    // finalement desactive
	ActivatedLPQControl();

	// Controle des quantites saisies par emplacemt lorsque on est en sortie
    // ou si deplacement
	if ($_POST['MvtTypeEntrieExit'] == MovementType::TYPE_EXIT || isChangeOfPosition()) {
		checkStockQty($cancelLink);
	}

	// controle qu'un Location d'arrivee est bien choisi lorsque c'est un deplacement
	if (isChangeOfPosition()) {
	    if ($_POST['newLocationId'] == 0) {
		    Template::errorDialog(_('Please select a destination location'), $cancelLink);
			exit;
		}
	}

	/*--------------------------------------------------------------------------
	---- on cree un ExecutedMovement correspondant a un mouvement non prevu ----
	--------------------------------------------------------------------------*/
	/* on demarre une transaction */
    Database::connection()->startTrans();

	require_once('DateWidget.php');
	$TotalRealQuantity = array_sum($_POST['QuantityArray']);
	$MvtTypeId = (isChangeOfPosition())?SORTIE_DEPLACEMT:$_POST['MvtType'];
	$exmParams = array(
        'StartDate'=>date('Y-m-d H:i:s', time()),
        'EndDate'=>date('Y-m-d H:i:s', time()),
        'RealQuantity'=>$TotalRealQuantity,
        'State'=>1,
        'Type'=>$MvtTypeId,
        'Comment'=>stripcslashes(stripcslashes(html_entity_decode(urldecode($_POST['Comment'])))),
        'RealProduct'=>$Product);
    $ExecutedMovement = createExecutedMovement($exmParams, $cancelLink);

	// On ne met pas a jour les Qtes virtuelles si c'est un mvt de deplacemt ds le stock!
	if (($_POST['MvtType'] != SORTIE_DEPLACEMT) && ($_POST['MvtType'] != ENTREE_DEPLACEMT)) {
	    /* MAJ des VirtualQty et recup des infos de mail(s) d'alerte eventuel(s) */
	    $AlertArray = $ExecutedMovement->setProductVirtualQuantity();
	}

	//on met a jour la table LocationExecutedMovement
    $LPQCollection = $LPQMapper->loadCollection(
        array('Id' => $_POST['lpqId']),
        array('Location.Name' => SORT_ASC));

	if (isset($_SESSION['LPQCollection'])) {
	    $LPQCollectionForSession = unserialize($_SESSION['LPQCollection']);
		for ($i=0;$i<$LPQCollectionForSession->getCount();$i++) {
			$LPQCollection->setItem($LPQCollectionForSession->getItem($i));
		}
	}
	$Inventory = 0;
	// si INVENTAIRE
	if ($_POST['MvtType'] == SORTIE_INVENT || $_POST['MvtType'] == ENTREE_INVENT) {
	    /*  Recuperation du Store et StorageSite ds lequel on est  */
	    $LPQ = $LPQCollection->getItem(0);
		$StorageSiteId = Tools::getValueFromMacro($LPQ, '%Location.Store.StorageSite.Id%');
		$StorageSite = Object::load('StorageSite', $StorageSiteId);
		$StoreId = Tools::getValueFromMacro($LPQ, '%Location.Store.Id%');
		$Store = Object::load('Store', $StoreId);
		$user = $Auth->getUser();

		$inventoryParams = array('StartDate'=>$ExecutedMovement->getStartDate(),
		        'EndDate' => $ExecutedMovement->getEndDate(),
		        'UserAccount' => $user,
		        'StorageSite' => $StorageSite,
		        'Store' => $Store);
		$Inventory = createInventory($inventoryParams, $cancelLink);
	}
	// Ne servira que si chgt de position
	$globalLEMCPCollection = new Collection();

	for($i = 0; $i < $LPQCollection->getCount(); $i++) {
		if (isset($_POST['QuantityArray'][$i]) && $_POST['QuantityArray'][$i] > 0) {
		 	$lpq = $LPQCollection->getItem($i);
			$Location = $lpq->getLocation();
			$LEMParams = array('Location'=>$Location,
			    'ExecutedMovement'=>$ExecutedMovement->getId(),
			    'Quantity'=>$_POST['QuantityArray'][$i],
			    'Product'=>$Product->getId(),
			    'Date'=>$ExecutedMovement->getEndDate());
			$LEM = createLocationExecutedMovement($LEMParams, $cancelLink);

			// Creation des LEMConcreteProduct si mode de suivi = SN ou lot
			$lcpCollection = isset($_SESSION['LCPCollection'])?
			    $_SESSION['LCPCollection']:new Collection();
			$LEMCPCollection = createLEMConcreteProduct($LEM, $lcpCollection, $_REQUEST['TracingMode'], 0, $cancelLink);

			// Si chgt de position, on sauve ces donnees pour le(s) entree(s)
			// qui suivront (car chgt de position = Entrees + Sortie)
			if (isChangeOfPosition()) {
			    $globalLEMCPCollection = $globalLEMCPCollection->merge($LEMCPCollection);
			}

			/*  MAJ des LPQ  */
			if ($_POST['lpqId'][$i] == 0) {
				// Si le LPQ est en session seulemt, il faut le sauver en base
                saveInstance($lpq, $cancelLink);
			}

			// Creation/suppr/MAJ des LocationConcreteProduct si mode de suivi = SN ou lot
			if (false == updateLocationConcreteProduct($lpq, $LEMCPCollection)) {
				Template::errorDialog(E_MSG_TRY_AGAIN, $cancelLink);
				exit;
			}

			// laisser ici car il faut que le lpq soit sauvé *avant* l'appel à
			// CreateInventoryDetail()
			if ($Inventory instanceof Inventory) {
			    $LEM->createInventoryDetail($Inventory);
			}
			//le mouvement est de type sortie ou deplacement
			if ($_POST['MvtTypeEntrieExit'] == MovementType::TYPE_EXIT || isChangeOfPosition()) {
			    $mvtTypeForUpLPQ = MovementType::TYPE_EXIT;
			} elseif ($_POST['MvtTypeEntrieExit'] == MovementType::TYPE_ENTRY && !isChangeOfPosition()){
			    $mvtTypeForUpLPQ = MovementType::TYPE_ENTRY;
			}
			$lpq = updateLPQQuantity($lpq, $_POST['QuantityArray'][$i],
			    $mvtTypeForUpLPQ, $cancelLink);

			unset($LEM, $Location, $lpq, $LEMCPCollection);
		}
	}
	// Si deplacement, creation de l'entree deplacemment, creation du LEM correspondant,
	if (isChangeOfPosition()) {  // et MAJ ou creation d'un LPQ pour le Location d'arrivee
	    $ExecutedMovement2 = Object::load('ExecutedMovement');
		$ExecutedMovement2->setStartDate(date('Y-m-d H:i:s', time()));
		$ExecutedMovement2->setEndDate(date('Y-m-d H:i:s', time()));
		$ExecutedMovement2->setState(1);
		$MvtTypeId = ENTREE_DEPLACEMT;
		$ExecutedMovement2->setType($MvtTypeId);
		$ExecutedMovement2->setRealQuantity($TotalRealQuantity);
		$ExecutedMovement2->setComment(stripcslashes(stripcslashes(html_entity_decode(urldecode($_POST['Comment'])))));
		$ExecutedMovement2->setRealProduct($Product);
        saveInstance($ExecutedMovement2, $cancelLink);

		// creation du LEM correspondant,
		$Location = Object::load('Location', $_POST['newLocationId']);
		$LEM = Object::load('LocationExecutedMovement');
	 	$LEM->setLocation($Location);
		$LEM->setExecutedMovement($ExecutedMovement2);
		$LEM->setQuantity($TotalRealQuantity);
		$LEM->setProduct($Product);
		$LEM->setDate($ExecutedMovement2->getEndDate());
        saveInstance($LEM, $cancelLink);

		// Creation des LEMConcreteProduct et des LocationConcreteProduct
		// si mode de suivi = SN ou lot
		$LCPMapper = Mapper::singleton('LocationConcreteProduct');
		for($i = 0; $i < $globalLEMCPCollection->getCount(); $i++){
			$LEMCP = $globalLEMCPCollection->getItem($i);
			$entryLEMCP = Object::load('LEMConcreteProduct');
			$entryLEMCP->setConcreteProduct($LEMCP->getConcreteProduct());
			$entryLEMCP->setLocationExecutedMovement($LEM);
			$entryLEMCP->setQuantity($LEMCP->getQuantity());
            saveInstance($entryLEMCP, $cancelLink);

			$testLCP = $LCPMapper->load(
        			array('ConcreteProduct' => $LEMCP->getConcreteProductId(),
        				  'Location' => $LEM->getLocationId()));
			// Le LCP existe deja
			if (!Tools::isEmptyObject($testLCP)) {
				$testLCP->setQuantity($testLCP->getQuantity() + $LEMCP->getQuantity());
                saveInstance($testLCP, $cancelLink);
			}
			else {  // MovementType::TYPE_ENTRY sur nouvel emplacement: creation de LCP
				$LCP = Object::load('LocationConcreteProduct');
				$LCP->setConcreteProduct($LEMCP->getConcreteProduct());
				$LCP->setLocation($LEM->getLocation());
				$LCP->setQuantity($LEMCP->getQuantity());
                saveInstance($LCP, $cancelLink);
			}
		}


		// MAJ ou creation d'un LPQ pour le Location d'arrivee
		$LPQ = $LPQMapper->load(array('Location' => $Location,
												  'Product' => $Product));
		if ($LPQ instanceof LocationProductQuantities && $LPQ->getId() > 0
		    && $TotalRealQuantity > 0) {
			$LPQ->setRealQuantity($LPQ->getRealQuantity() + $TotalRealQuantity);
            saveInstance($LPQ, $cancelLink);
		}
		elseif ($TotalRealQuantity > 0) { // pour eviter de creer des LPQ avec Qty=0
			$LPQ = Object::load('LocationProductQuantities');
			$LPQ->setLocation($Location);
			$LPQ->setProduct($Product);
			$LPQ->setRealQuantity($TotalRealQuantity);
            saveInstance($LPQ, $cancelLink);
		}
	}

	/* gestion des erreurs */
	if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    	Database::connection()->rollbackTrans();
    	Template::errorDialog('Erreur SQL...', $_GET['returnURL']);
        exit;
    }
	/* on committe la transaction */
    Database::connection()->completeTrans();

	$TotalRealQuantity = $Product->getRealQuantity();  // qte totale en stock de ce pdt
	$comment = '';

	if (($_POST['MvtType'] != SORTIE_DEPLACEMT) && ($_POST['MvtType'] != ENTREE_DEPLACEMT)) {
		// Verification que la qte totale de product en stock est superieure a
        // la Qte minimum autorisee
		if ($TotalRealQuantity <= $Product->getSellUnitMinimumStoredQuantity()) {
	    	$AlertArray[] = Object::load('Alert', ALERT_STOCK_QR_MINI);
		}
		$comment = $ExecutedMovement->getComment();
		$AlertArray[] = Object::load('Alert', ALERT_MOVEMENT_NON_FORESEEABLE);
	}

	$params = array('ProductBaseReference' => $Product->getBaseReference(),
					'MvtTypeName' => Tools::getValueFromMacro($ExecutedMovement, '%Type.Name%'),
					'ProductMinimumStock' => $Product->getSellUnitMinimumStoredQuantity(),
					'ProductName' => $Product->getName(),
					'ProductSupplierName' => Tools::getValueFromMacro($Product, '%MainSupplier.Name%'),
					'RealQuantity' => $ExecutedMovement->getRealQuantity(),
					'Comment' => $comment
				);

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
		    $alert->prepare($params);
		    $alert->send();  // on envoie l'alerte
        }
		unset($alert);
	}
	if (isset($_SESSION['LPQCollection'])) {
	    unset($_SESSION['LPQCollection']);
	}
	Tools::redirectTo('ActivatedMovementAddWithoutPrevision.php');
	exit;
}
?>
