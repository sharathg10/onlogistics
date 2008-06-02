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

require_once('Objects/MovementType.const.php');
require_once('Objects/ActivatedMovement.php');
require_once('Objects/ExecutedMovement.php');
require_once('ExecutedMovementTools.php');
require_once('RPCTools.php');  // pour clean()
require_once('AlertSender.php');
require_once('Objects/Alert.const.php');

// function processExecutedMovement($xmlEXM, $auth) {}
/**
 *
 * @return boolean  (true if OK) or string (error)
 */
function process($auth, $xmldata)
{
    $errMsge = "Erreur lors du traitement des mouvements non attendus: <br />";

    // on parse le xml
    $xmlEXM = simplexml_load_string($xmldata);
    if (!$xmlEXM) {
        return $errMsge .  "erreur de parsing...";
    }

    Database::connection()->startTrans();
	// acteur et utilisateur
	$uac = $auth->getUser();
	$actor = $auth->getActor();
    // on récupère le type
    $movementType = Object::load(
            'MovementType', getMovementTypeForZaurus((int)$xmlEXM->type));
	$movementTypeID = $movementType->getId();

	// on charge le produit associé
	$pdtMapper = Mapper::singleton('Product');
	$productRef = clean((string)$xmlEXM->reference);
    $product = $pdtMapper->load(
                array('BaseReference' => $productRef));
    // si le produit n'existe pas on retourne une erreur
    if (!($product instanceof Product)) {
        return $errMsge . sprintf("Aucun produit n'est associé à la référence %s",
            $productRef);
    }
    // on crée un executed movement
    $exmParams = array(
        'StartDate' => clean((string)$xmlEXM->startdate),
        'EndDate' => clean((string)$xmlEXM->enddate),
        'RealQuantity' => (float)$xmlEXM->quantity,
        'State' => ExecutedMovement::EXECUTE_TOTALEMENT,
        'Type' => $movementType,
        'Comment' => clean((string)$xmlEXM->comment),
        'RealProduct' => $product);
    try {
        $exm = createExecutedMovement($exmParams, null, false);
    } catch(Exception $e) {
        return $e->getMessage();
    }

	// l'id du store et du site
	$storeId = (int)$xmlEXM->storeid;
	$siteId  = (int)$xmlEXM->storagesiteid;
    // on charge le(s) emplacement(s) associé(s)
    // pour chaque location on verifie qu'elle existe
    foreach ($xmlEXM->location as $xmlLoc) {
        $locRef = clean((string)$xmlLoc->reference);
		// on charge la location, pour gérer les locations qui peuvent avoir le
		// même nom, on se base aussi sur le site et le store
		$location = Object::load('Location',
			array('Name'=>$locRef, 'Store'=>$storeId, 'Store.StorageSite'=>$siteId));
        if (!($location instanceof Location)
			|| !locationIsCompatibleWithActor($location, $actor->getId())) {
            return $errMsge . sprintf(
				"aucun emplacement ne correspond à la référence %s pour l'acteur %s",
                $locRef,
                $uac->getIdentity()
            );
        }

        $locQuantity = (float)$xmlLoc->quantity;
        // Mise à jour des LocationProductQuantities
        try {
            $lpq = getLocationProductQuantities(
                $product, $location, $movementType->getEntrieExit(), null, false);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // si le mouvement est une sortie, le couple Location/Product doit exister
        // en bdd dans un site appartenant à l’acteur de l’utilisateur connecté.
        if ($movementType->getEntrieExit() == SORTIE) {
            if (Tools::isEmptyObject($lpq)) {
                return $errMsge . sprintf(
					"le couple emplacement/produit n'existe pas pour " .
					"la référence %s avec l'emplacement %s",
                    $productRef,
                    $locRef
                );
            }
            if ($locQuantity > $lpq->getRealQuantity()) {
                return $errMsge . sprintf(
					"la quantité de référence %s mouvementée pour l'emplacement %s (%s " .
					"unités) ne peut être supérieure à la quantité en stock (%s unités).",
                    $productRef,
                    $locRef,
                    $locQuantity,
                    $lpq->getRealQuantity()
                );
            }
        }
        // Tout est ok, on procede

        // Mise à jour des LocationConcreteProduct et ConcreteProduct
        $tracingMode = $product->getTracingMode();
        $CPParams['Product'] = $product;
        $LCPParams['Location'] = $location;
        $LCPCollection = new Collection();

        foreach ($xmlLoc->concreteproduct as $xmlCP) {
            $SerialNumber = clean((string)$xmlCP->serialnumber);
            $EndOfLifeDate = '';
            $quantity = (float)$xmlCP->quantity;

            $CPParams['SerialNumber'] = $SerialNumber;
            $CPParams['EndOfLifeDate'] = $EndOfLifeDate;
	        $LCPParams['LCPQuantity'] = $quantity;

	        $result = createOrUpdateLCP(
	               $tracingMode, $movementType, $CPParams, $LCPParams);

            $LCP = $result['LCP'];
            $errorCode = $result['error'];
            if($errorCode==0 && $LCP instanceof LocationConcreteProduct) {
                $LCP->save();
                $LCPCollection->setItem($LCP);
            } else {
                return $errMsge . getErrorMessage($errorCode);
            }
        }
        // fin maj des LCP

        // Creation des LocationExecutedMovement
        $LEMParams = array('Location'=>$location,
            'ExecutedMovement'=>$exm->getId(),
            'Product'=>$product->getId(),
            'Quantity'=>$locQuantity,
            'Date'=>$exm->getEnddate());
        try {
            $locExm = createLocationExecutedMovement($LEMParams, null, false);
        } catch(Exception $e) {
            return $e->getMessage();
        }

        // Si entrée ou sortie inventaire, on crée les entrées apropriées
		if ($movementTypeID == SORTIE_INVENT || $movementTypeID == ENTREE_INVENT) {
		    // recup storageSite
		    $storageSiteId = (int)$xmlEXM->storagesiteid;
			$storageSite = Object::load('StorageSite', $storageSiteId);
            // recup Store
			$storeId = (int)$xmlEXM->storeid;
			$store = Object::load('Store', $storeId);
            // inventory params
		    $inventoryParams = array('StartDate'=>$exm->getStartDate(),
		        'EndDate' => $exm->getEndDate(),
		        'UserAccount' => $uac,
		        'StorageSite' => $storageSite,
		        'Store' => $store);
		    $inventory = createInventory($inventoryParams, null, false);
			// si inventaire on crée le inventorydetail
			$locExm->createInventoryDetail($inventory);
		}

        try {
		    // maj qty lpq
		    $lpq = updateLPQQuantity($lpq, $locQuantity, $movementType->getEntrieExit(), null, false);
		    $locExm->save();
		    // maj LEMCP
            $LEMCPCollection = createLEMConcreteProduct($locExm, $LCPCollection, $tracingMode, 0, null, false);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        unset($locExm, $lpq, $location);
    } // for
    // mise à jour des quantité virtuelles
    $AlertArray = $exm->setProductVirtualQuantity();
    // Peut contenir des donnees a envoyer par mail
    $exm->save();

    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        return $errMsge . sprintf(
			"erreur lors de l'enregistrement des données pour la référence %s.",
            $productRef
        );
    }
    Database::connection()->completeTrans();
    /**
     * Envoi des mails d'alerte
     */
	// Verification que la qte totale de product en stock
    // est superieure a la Qte minimum autorisee
    if ($product->getRealQuantity() <= $product->getSellUnitMinimumStoredQuantity()) {
		$AlertArray[] = Object::load('Alert', ALERT_STOCK_QR_MINI);
    }
	// mail d'alerte sur le mouvement non prévu
	$AlertArray[] = Object::load('Alert', ALERT_MOVEMENT_NON_FORESEEABLE);

	$params = array(
        'ProductBaseReference' => $productRef,
		'MvtTypeName' => Tools::getValueFromMacro($exm, '%Type.Name%'),
		'ProductMinimumStock' => $product->getSellUnitMinimumStoredQuantity(),
		'ProductName' => $product->getName(),
		'ProductSupplierName' => Tools::getValueFromMacro($product, '%MainSupplier.Name%'),
		'RealQuantity' => $exm->getRealQuantity(),
		'Comment' => $exm->getComment()
	);

	for ($i=0;$i<count($AlertArray);$i++) {
		$alert = $AlertArray[$i];
        // pfff la méthode EXM::setProductVirtualQuantity peut retourner un 
        // tableau de tableaux ou d'objets alertes!
        if (is_array($alert)) {
            $alert = Object::load('Alert', $alert[0]);
        }
        if (AlertSender::isStockalert($alert->getId())) {
            AlertSender::sendStockAlert($alert->getId(), $product);
        } else {
		    $alert->prepare($params);
            $alert->send();  // on envoie l'alerte
        }
		unset($alert);
	}

    return true;
}


/**
 *
 * @access public
 * @return void
 */
function locationIsCompatibleWithActor($loc, $actorID)
{
    $store = $loc->getStore();
    $storageSite = $store->getStorageSite();
    return $actorID == $storageSite->getOwnerID();
}

?>
