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

function checkExistingSNInStock($productId, $serialNumber, $className='ConcreteProduct')
{
    $CPMapper = Mapper::singleton($className);
    $testConcreteProduct = $CPMapper->load(
						array('Product' => $productId,
							  'SerialNumber' => $serialNumber));

	// Ce ConcreteProduct existe deja
	if (!Tools::isEmptyObject($testConcreteProduct)) {
		$testLCPCollection = $testConcreteProduct->getLocationConcreteProductCollection();
		// Il n'est pas en stock: alors on accepte la saisie
		if (!Tools::isEmptyObject($testLCPCollection)) {
		    return true;
		}
	}
	return false;
}

/**
 * retourne le CP lié au product  pour
 * le SN/Lot. Le crée si nécéssaire.
 *
 * @param Object $product Product
 * @param string $serialNumber n° de sn/lot
 * @param string $endDateOfLife date de fin de vie du CP (pour création)
 * @param string $className 'ConcreteProduct' ou 'AeroConcreteProduct'
 * @access public
 * @return Object ConcreteProduct/AeroConcreteProduct
 */
function getConcreteProduct($product, $serialNumber,
$endDateOfLife, $className='ConcreteProduct')
{
    $CPMapper = Mapper::singleton($className);
    $testConcreteProduct = $CPMapper->load(
    	array('Product' => $product->getId(),
    		  'SerialNumber' => $serialNumber));

	// Ce ConcreteProduct existe deja
	if (!Tools::isEmptyObject($testConcreteProduct)) {
	    $testConcreteProduct->setEndOfLifeDate($endDateOfLife);
	    saveInstance($testConcreteProduct);
		return $testConcreteProduct;
	} else {  // Il faut le creer
		$ConcreteProduct = Object::load($className);
		$ConcreteProduct->setProduct($product);
		$ConcreteProduct->setSerialNumber($serialNumber);
		if (!empty($endDateOfLife)) {
		    $ConcreteProduct->setEndOfLifeDate($endDateOfLife);
		}
	    saveInstance($ConcreteProduct);
		return $ConcreteProduct;
	}
}

/**
 * verifie que le sn/lot existe (pour le Product donné) pour
 * l'emplacement donné
 * Retourne true si le LCP existe pour le CP de
 * serialNumber donné et le Location donné
 *
 * @param string $serialNumber n° du SN/Lot
 * @param int $locationId id de la Location
 * @param int $productId id du Product associe au concreteProduct
 * @access public
 * @return boolean
 */
function checkSerialIsInLocation($serialNumber, $locationId, $productId)
{
    $LCPMapper = Mapper::singleton('LocationConcreteProduct');
    $LCP = $LCPMapper->load(
    	array('Location' => $locationId,
    		  'ConcreteProduct.SerialNumber' => $serialNumber,
    		  'ConcreteProduct.Product' => $productId));

	if (Tools::isEmptyObject($LCP)) {
		return false;
	}
	return true;
}

/**
 * Effectue les controles sur les SN/Lots saisis si reintegration, et
 * créé le LCP correspondant
 *
 * @param Integer $LEMId Id du LEM
 * @param Integer $locationId Id du Location
 * @param String $serialNumber SerialNumber du ConcreteProduct
 * @param float $quantity Quantité de CP saisie
 * @param float $LCPQuantity Quantité pour le LCP
 * @access public
 * @return Object LocationConcreteProduct
 **/
function createLCPForCancellation($LEMId, $locationId, $productId, $serialNumber,
$quantity, $LCPQuantity)
{
    $error = 0;
    $CPMapper = Mapper::singleton('ConcreteProduct');

    $LEM = Object::load('LocationExecutedMovement', $LEMId);
    $LEMCPCollection = $LEM->getLEMConcreteProductCollection();

    // Pas de mode de suivi
    if (Tools::isEmptyObject($LEMCPCollection)) {
        return false;
    }
    $count = $LEMCPCollection->getCount();
    // Contiendra les SN/No de lot possibles et les qtes max associees
    $waitedCPids = array();
    $returnedQty = array();  // Contiendra les Qtes reintegrees par No de lot

    for($i = 0; $i < $count; $i++){
        $LEMCP = $LEMCPCollection->getItem($i);
        $CPid = $LEMCP->getConcreteProductId();
        $qty = isset($waitedCPids[$CPid])?
                $waitedCPids[$CPid] + $LEMCP->getQuantity():$LEMCP->getQuantity();
        $waitedCPids[$CPid] = $qty;
    }

    if ($serialNumber != '') {
        $testConcreteProduct = $CPMapper->load(
            array(
                'Product' => $productId,
                'SerialNumber' => $serialNumber));

        // Ce ConcreteProduct n'existe pas: erreur
        if (Tools::isEmptyObject($testConcreteProduct)) {
            $error = 1;
        }
        // Le SN/lot saisi existe mais ne correspond pas au mvt annule
        elseif (! in_array($testConcreteProduct->getId(), array_keys($waitedCPids))) {
            $error = 2;
        }
        // La Qte saisie est superieure au max possible (mode de suivi=LOT)
        elseif (isset($quantity) && $quantity > 0) {
            $CPid = $testConcreteProduct->getId();
            $qty = isset($returnedQty[$CPid])?
                    $returnedQty[$CPid] + $quantity:$quantity;
            if ($qty > $waitedCPids[$testConcreteProduct->getId()]) {
                $error = 3;
            }
            $returnedQty[$CPid] = $qty;
        }
        // Le concreteProduct est desactive
        elseif($testConcreteProduct->getActive() == 0) {
            $error = 4;
        }

        // Sinon, tout est OK
        $LCP = Object::load('LocationConcreteProduct');
        $LCP->setConcreteProduct($testConcreteProduct);
        $Location = Object::load('Location', $locationId);
        $LCP->setLocation($Location);
        $LCP->setQuantity($LCPQuantity);
    }

    return array('LCP'=>$LCP, 'error'=>$error);
}

/**
 * Retourne le message d'erreur correspondant au code
 *
 * @param $erroCode le code d'erreur
 * @access public
 * @return string
 */
function getErrorMessage($errorCode)
{
    $errorMsge = array(
        -1 => _('Unknown error.'),
        0 => _('No error.'),
        1 => _('Wrong data provided: some SN or Lot was not found in the database.'),
        2 => _('Wrong data provided: some SN or Lot does not match cancelled movement.'),
        3 => _('Wrong data provided: some quantity is too great.'),
        4 => _('Wrong data provided: some SN or Lot is deactivated.'),
        5 => _('Error in data provided please correct.'),
        6 => _('Some SN is already assigned to a product, it cannot be entered again.'),
        7 => _('No location matches for this product')
    );
    if(isset($errorMsge[$errorCode])) {
        return $errorMsge[$errorCode];
    }
    return $errorMsge[-1];
}

/**
 * Retourne un tableau contenant le LCP màj ou eventuellement créé
 * ainsi qu'un code d'erreur. Crée également le CP si nécéssaire
 *
 * les paramètres sont de la forme
 * $tracingMode : Le mode de suivi
 * $movementType : Si pas un Objet MovementType, doit etre un tableau avec
 *   'MvtTypeEntrieExit' => MovementType.EntrieExit
 *   'MvtType' => MovementType.Id
 * $CPParams : paramètre lié au concrete product
 *   'SerialNumber' => num de serie du SN/Lot
 *   'Product' => Obje Produit
 *   'EndOfLifeDate' => Date de fin de vie
 * $LCPParams : paramètre lié au LCP
 *   'Location' => Objet Location
 *   'LCPQuantity' => Quantité
 *
 * @param int $tracingMode le mode de suivi
 * @param array/Object $movementType infos liées au typre de mouvement
 * @param array $CPParams paramètres liés au ConcreteProduct
 * @param array $LCPParams paramètres liés au LocationConcreteProduct
 * @access public
 * @return array
 */
function createOrUpdateLCP($tracingMode, $movementType, $CPParams, $LCPParams)
{
    // mise en forme des variables :
    //Mvmt vars
    if($movementType instanceof MovementType) {
        $MvtTypeEntrieExit = $movementType->getEntrieExit();
        $MvtType = $movementType->getId();
    } else {
        $MvtTypeEntrieExit = $movementType['MvtTypeEntrieExit'];
        $MvtType = $movementType['MvtType'];
    }

    //CP Vars
    $serialNumber = '';
    if(isset($CPParams['SerialNumber'])) {
        $serialNumber = $CPParams['SerialNumber'];
    }
    $product = $CPParams['Product'];
    $EndOfLifeDate = $CPParams['EndOfLifeDate'];

    //LCP Vars
    $Location = $LCPParams['Location'];
    $LCPQuantity = $LCPParams['LCPQuantity'];

    $error=0;
    $ClassName = ($product instanceof AeroProduct)?'AeroConcreteProduct':'ConcreteProduct';
//    $CPMapper = Mapper::singleton($ClassName);
    $LCPMapper = Mapper::singleton('LocationConcreteProduct');

    // Si tracing au SN, une ligne par product voulu
	if ($tracingMode == Product::TRACINGMODE_SN) {
		// Si pas de SN saisi,
		if (empty($serialNumber)) {
			$LCP = Object::load('LocationConcreteProduct');
		}
		// Si LPQ pas encore sauve en base (cas d'une entree en stock)
		// Il faut peut etre creer un ConcreteProduct dans ce cas
		elseif ($MvtTypeEntrieExit == MovementType::TYPE_ENTRY && !mvmtIsChangeOfPosition($MvtType)) {
			$LCP = Object::load('LocationConcreteProduct');


			if(checkExistingSNInStock($product->getId(),
			   $serialNumber, $ClassName)) {
			   $error = 6;
			   $LCP = Object::load('LocationConcreteProduct');
			} else {
			    //le CP n'existe pas en stock avec ce SN, on peut le créer
			    $CP = getConcreteProduct($product,
			       $serialNumber, $EndOfLifeDate, $ClassName);
			    $LCP->setConcreteProduct($CP);
			}
		}
		// Sortie: le ConcreteProduct doit exister
		else {
		    if (!checkSerialIsInLocation($serialNumber, $Location->getId(), $product->getId())) {
		        $error = 5;
		        $LCP = Object::load('LocationConcreteProduct');
		    } else {
                $LCP = $LCPMapper->load(
    	            array('Location' => $Location->getId(),
    		              'ConcreteProduct.SerialNumber' => $serialNumber,
                          'ConcreteProduct.Product' => $product->getId()));
            }
		}
		$LCP->setLocation($Location);
		$LCP->setQuantity(1);
	}

	// Si tracing au LOT, une ligne par ConcreteProduct trouve ds les Locations choisis
	elseif ($tracingMode == Product::TRACINGMODE_LOT) {
		// Si sortie ou chgt de position
		if ($MvtTypeEntrieExit == MovementType::TYPE_EXIT || mvmtIsChangeOfPosition($MvtType)) {

		    if (!checkSerialIsInLocation($serialNumber, $Location->getId(), $product->getId())) {
		        $error = 5;
		        $LCP = Object::load('LocationConcreteProduct');
		    } else {
		        $LCP = $LCPMapper->load(
    	            array('Location' => $Location->getId(),
    		              'ConcreteProduct.SerialNumber' => $serialNumber,
                          'ConcreteProduct.Product' => $product->getId()));
            }
		}

		else {  // Entree
			// Si pas de No de lot saisi
			if (empty($serialNumber)) {
				$LCP = Object::load('LocationConcreteProduct');
			}
			else {
			    $ConcreteProduct = getConcreteProduct($product,
			       $serialNumber, $EndOfLifeDate, $ClassName);

				$LCP = $LCPMapper->load(
						array('Location' => $Location->getId(),
							  'ConcreteProduct.SerialNumber' => $serialNumber,
                              'ConcreteProduct.Product' => $product->getId()));
				// Le ConcreteProduct n'existe deja en stock: Il faut creer le LCP
				if (Tools::isEmptyObject($LCP)) {
					$LCP = Object::load('LocationConcreteProduct');
					$LCP->setConcreteProduct($ConcreteProduct);
				}
			}
			$LCP->setLocation($Location);

		}
		$LCP->setQuantity($LCPQuantity);
	}
	return array('LCP'=>$LCP, 'error'=>$error);
}

/**
 * Retounre True si le mouvement est un
 * changement de position
 *
 * @param int $mvtType l'id du MovementType
 * @access public
 * @return boolean
 */
function mvmtIsChangeOfPosition($mvtType) {
    return (isset($mvtType) && ENTREE_DEPLACEMT == $mvtType);
}

/*
fin fx créées à partir de LocationConcreteProductList
*/

/**
 * Vérifie que le concreteProduct en stock est
 * en quantité suffisante (via LocationConcreteProduct)
 * pour une sortie.
 * Retourne true si le mouvment peut être effectué.
 *
 * @param integer $ConcreteProductId Id du CP
 * @param integer $LocationId Id du Location
 * @param float $quantity Quantité à mouvementé
 * @access public
 * @return true
 */
function checkCPQuantity($ConcreteProductId, $LocationId, $quantity)
{
    $LCPMapper = Mapper::singleton('LocationConcreteProduct');
    $LCP = $LCPMapper->load(
        array('ConcreteProduct'=>$ConcreteProductId,
              'Location'=>$LocationId));
    if(!($LCP instanceof LocationConcreteProduct) || $LCP->getQuantity() < $quantity) {
        return false;
    }
    return true;
}

/**
 * Cree le ou les LEMConcreteProduct pour un LEM donne
 *
 * @param Object $LEM LocationExecutedMovement
 * @param Object $LCPCollection Collection de LCP
 * @param integer $tracingMode mode de suivi
 * @param boolean $cancelledLEMId != 0 si reintegration en stock
 * @access public
 * @return object LEMConcreteProductCollection
 **/
function createLEMConcreteProduct($LEM, $LCPCollection, $tracingMode,
    $cancelledLEMId=0, $retURL=null, $showErrorDialog=true)
{
    $LEMCPCollection = new Collection();  // Collection qui sera retournee
    if ($tracingMode == 0) { // Si pas de mode de suivi
        return $LEMCPCollection;
    }
    $CPMapper = Mapper::singleton('ConcreteProduct');
    $LocationId = $LEM->getLocationId();

    $quantity = 1; // Par defaut, on prend: TracingMode=Product::TRACINGMODE_SN
    $count = $LCPCollection->getCount();

    for($i = 0; $i <$count ; $i++){
        $LCP = $LCPCollection->getItem($i);
        if ($LocationId != $LCP->getLocationId() || $LCP->getQuantity() == 0) {
            continue;
        }
        if ($tracingMode == Product::TRACINGMODE_LOT) {
            $quantity = $LCP->getQuantity();
        }
        $LEMCP = Object::load('LEMConcreteProduct');

        $ConcreteProduct = $LCP->getConcreteProduct();

        // Si nouveau ConcreteProduct: il faut le sauver
        // la condition suivante ne suffit pas
        if ($ConcreteProduct->getId() == 0) {
            $testConcreteProduct = $CPMapper->load(
                            array('Product' => $ConcreteProduct->getProductId(),
                                  'SerialNumber' => $ConcreteProduct->getSerialNumber()));

            // Ce ConcreteProduct n'existe pas: il faut le creer
            if (Tools::isEmptyObject($testConcreteProduct)) {
                saveInstance($ConcreteProduct, $retURL, $showErrorDialog);
            }
            else {
                $ConcreteProduct = $testConcreteProduct;
                $LCP->setConcreteProduct($testConcreteProduct);
            }
        }

        $LEMCP->setConcreteProduct($ConcreteProduct);
        $LEMCP->setLocationExecutedMovement($LEM);
        $LEMCP->setQuantity($quantity);
        // Si reintegration en stock, on renseigne Cancelled et
        // CancelledLEMConcreteProduct, et Cancelled du LEMCP reintegre
        if ($cancelledLEMId > 0) {
            $LEMCP->setCancelled(LEMConcreteProduct::LEMCP_CANCELLER);
            $LEMCPMapper = Mapper::singleton('LEMConcreteProduct');
            $cancelledLEMCP = $LEMCPMapper->load(
                    array('LocationExecutedMovement' => $cancelledLEMId,
                          'ConcreteProduct' => $ConcreteProduct->getId()));
            if (!Tools::isEmptyObject($cancelledLEMCP)) {
                $cancelledLEMCP->setCancelled(-1);
                saveInstance($cancelledLEMCP, $retURL, $showErrorDialog);
                $LEMCP->setCancelledLEMConcreteProduct($cancelledLEMCP);
            }
        }
        // TODO: la FK CancelledLEMConcreteProduct serait à mettre a jour!!
        saveInstance($LEMCP, $retURL, $showErrorDialog);
        $LEMCPCollection->setItem($LEMCP);

    }
    return $LEMCPCollection;
}

/**
 * créé et sauve un Inventory en fonction des
 * paramètres donnés dans $params :
 *      'StartDate'
 *      'EndDate'
 *      'UserAccount'
 *      'StorageSite'
 *      'Store'
 *
 * @param array $params les donnés de l'Inventory
 */
function createInventory($params, $retURL=null, $showErrorDialog=true)
{
    $inventory = Object::load('Inventory');

    $inventory->setBeginDate($params['StartDate']);
	$inventory->setEndDate($params['EndDate']);
	$inventory->setUserAccount($params['UserAccount']);
	$inventory->setUserName($params['UserAccount']->getIdentity());

	if ($params['StorageSite'] instanceof StorageSite) {
	    $inventory->setStorageSite($params['StorageSite']);
	    $inventory->setStorageSiteName($params['StorageSite']->getName());
	}
	if ($params['Store'] instanceof Store) {
	    $inventory->setStore($params['Store']);
	    $inventory->setStoreName($params['Store']->getName());
	}
	saveInstance($inventory, $retURL, $showErrorDialog);
	return $inventory;
}

/**
 * Cré un exm avec les paramètres donnés
 *      'StartDate'
 *      'EndDate'
 *      'RealQuantity'
 *      'State'
 *      'Type'
 *      'Comment'
 *      'RealProduct'
 * @param array $params les paramètres
 * @access public
 * @return object EXM
 */
function createExecutedMovement($params, $retURL=null, $showErrorDialog=true)
{
    $exm = Object::load('ExecutedMovement');
    foreach ($params as $key=>$value) {
        $setter = 'set' . $key;
        if(method_exists($exm, $setter)) {
            if ($value instanceof Object) {
                $value = $value->getId();
            }
            $exm->$setter($value);
        }
    }
    saveInstance($exm, $retURL, $showErrorDialog);
    return $exm;
}

/**
 * crée et sauve un LEM avec les paramètres donnés
 *      'Location'
 *      'ExecutedMovement'
 *      'Product'
 *      'Quantity'
 *      'Date'
 * @param array $params
 * @access public
 * @return LEM
 */
function createLocationExecutedMovement($params, $retURL=null, $showErrorDialog=true)
{
    $lem = Object::load('LocationExecutedMovement');
    foreach ($params as $key=>$value) {
        $setter = 'set' . $key;
        if (method_exists($lem, $setter)) {
            if ($value instanceof Object) {
                $value = $value->getId();
            }
            $lem->$setter($value);
        }
    }
    saveInstance($lem, $retURL, $showErrorDialog);
    return $lem;
}

/**
 *
 */
function getLocationProductQuantities($product, $location,
    $MvtTypeEntrieExit=MovementType::TYPE_ENTRY, $retURL=null, $showErrorDialog=true)
{
    $LPQMapper = Mapper::singleton('LocationProductQuantities');
    $LPQ = $LPQMapper->load(
        array('Product'=>$product->getId(),
			'Location'=>$location->getId()
		)
	);
    if (!($LPQ instanceof LocationProductQuantities)
    || (0 == $LPQ->getId())) { // si un tel LPQ n'existe pas
        if ($MvtTypeEntrieExit == MovementType::TYPE_EXIT) {
            return false;
        }
        if ($MvtTypeEntrieExit == MovementType::TYPE_ENTRY) {
            $LPQ = Object::load('LocationProductQuantities');
            $LPQ->setProduct($product);
            $LPQ->setLocation($location);
            saveInstance($LPQ, $retURL, $showErrorDialog);
        }
    }
    return $LPQ;
}

/**
 * Met à jour la realquantity du LPQ avec la quantity
 * en fonction du type de moubement
 * Retourne le LPQ mis à jour ou false si il à été supprimé.
 *
 * @param Object $LPQ le LPQ
 * @param float $quantity la quantoté mouvementé
 * @param int $MvtTypeEntrieExit le type de mouvement
 * @access public
 * @return Object/Boolean
 */
function updateLPQQuantity($LPQ, $quantity, $MvtTypeEntryExit=MovementType::TYPE_ENTRY,
    $retURL=null, $showErrorDialog=true)
{
    if($MvtTypeEntryExit == MovementType::TYPE_EXIT) {
        $quantity = $LPQ->getRealQuantity() - $quantity;
    } elseif ($MvtTypeEntryExit == MovementType::TYPE_ENTRY) {
        $quantity = $LPQ->getRealQuantity() + $quantity;
    }
    if($quantity==0) {
        deleteInstance($LPQ, $showErrorDialog);
		return false;
    }
    $LPQ->setRealQuantity($quantity);
    saveInstance($LPQ, $retURL, $showErrorDialog);
    return $LPQ;
}
?>
