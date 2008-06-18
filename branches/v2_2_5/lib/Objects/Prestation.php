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

class Prestation extends _Prestation {
    // Constructeur {{{

    /**
     * Prestation::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}

    // Prestation::getPrestationPrice() {{{

    /**
     * Calcul le coût de la prestation.
     *
     * Calcul le coût de la prestation en fonction:
     * - de la durée
     * - de la zone de départ
     * - de la zone d'arrivée
     * - des ConcreteProducts à facturer
     * - des FlyTypes à facturer
     * - des Products à facturer
     * - des Actors à facturer
     *
     * Utilisation:
     * <code>
     * // paramètre de la prestation (durée, zone de départ et d'arrivée)
     * $params = array('time'            => 3000,
     *                 'departureZoneId' => 3,
     *                 'arrivalZoneId'   => 5,
     *                 'storeId'         => 2,
     *                 'productTypeId'   => 8,
     *                 'numberOfdays'    => 14));
     * // les ids des ConcreteProducts concernés avec pour chacun la quantitée
     * $concreteProductParams = array(6=>3, 8=>6);
     * // les ids des Products concernés avec pour chacun la quantité (uniquement
     * // si tous ont le même store et productType ou si il ne faut pas tenir compte
     * // du store et productType)
     * $productParams = array(34=>40, 65=>10, 66=>10);
     * // si plusieurs productType ou Store sont à facturer :
     * $productParams = array(
     *     array('id'=>34, 'qty'=>40,'storeId'=>12, 'productTypeId'=>23),
     *     array('id'=>65, 'qty'=>10,'storeId'=>13, 'productTypeId'=>11),
     *     array('id'=>66, 'qty'=>10,'storeId'=>12, 'productTypeId'=>2));
     * // les ids des FlyTypes concernés avec pour chacun la quantitée
     * $flyTypeParams = array(33=>1);
     * // les id des Actors concernés
     * $actorParams = array(1, 64);
     * $prestation->getPrestationPrice($params, $concreteProductParams,
     *      $flyTypeParams, $productParams, $actorParams);
     * </code>
     *
     * @param array $params un tableau avec comme clés: time, departureZoneId, arrivalZoneId
     * @param array $concreteProductIds id des ConcreteProduct à facturer
     * @param array $flyTypeIds id des FlyType à facturer
     * @param array $productIds id des Product à facturer
     * @return array
     * @todo $actorIds est à enlever mais il faut gérer les impacts sur la
     * commande de cours et la création de la facture de vol avant.
     * @todo voir si le code peut être optimise
     */
    public function getPrestationPrice($globalParams=array(),
    $concreteProductIds=array(), $flyTypeIds=array(),
    $productIds=array(), $actorIds=array(), $productTypeIds=array(),
    $forTransportACO=false) {
        require_once 'PrestationManager.php';
        if(!$this->getActive() || !$this->getFacturable()) {
            return array();
        }

        $result = array();
        $costTypesArray = array_keys(CostRange::getCostTypeConstArray());

        $globalTime = !empty($globalParams['time'])? $globalParams['time'] : 0;
        $globalDepartureZoneId = !empty($globalParams['departureZoneId'])?
            $globalParams['departureZoneId'] : 0;
        $globalArrivalZoneId = !empty($globalParams['arrivalZoneId'])?
            $globalParams['arrivalZoneId'] : 0;
        $globalStoreId = !empty($globalParams['storeId'])?
            $globalParams['storeId'] : 0;
        $globalProductTypeId = !empty($globalParams['productTypeId'])?
            $globalParams['productTypeId'] : 0;

        // calcul le coefficient à appliquer au coût de chaque CostRange en
        // fonction du mode de calcul des prix de la prestation et de son
        // type.
        $numberOfDays = isset($globalParams['numberOfDays'])?
            $globalParams['numberOfDays'] : NUMBER_OF_DAYS_PER_MONTH;
        $coeff = 1;
        if($this->getType() == Prestation::PRESTATION_TYPE_STOCKAGE
            && $this->getPeriodicity() == Prestation::PERIODICITY_MONTH) {
            $coeff = 1 / $numberOfDays;
        }

        // quelques mappers
        $ccPdtPrestCostMapper = Mapper::singleton('ConcreteProductPrestationCost');
        $flyTypePrestCostMapper = Mapper::singleton('FlyTypePrestationCost');
        $productPrestCostMapper = Mapper::singleton('ProductPrestationCost');

        // filtre sur les Zones
        $globalZonesFilters = PrestationManager::getZonesFilters(
            $globalDepartureZoneId, $globalArrivalZoneId);
        
        foreach($costTypesArray as $costType) {
            // on ne tient pas compte des prix au forfait par emplacement
            // pour les prestations qui servent à facturer une aco de 
            // transport
            if(($costType == CostRange::TYPE_FIXED_BY_LOCATION) && $forTransportACO) {
                continue;
            }
            
            // ETAPE 1: facturation des ConcreteProduct {{{
            foreach($concreteProductIds as $key=>$params) {
                $id = $params['id'];
                $qty = $params['qty'];
                $stoID = isset($params['storeId'])?
                    $params['storeId'] : $globalStoreId;
                $pdtTypeID = isset($params['productTypeId'])?
                    $params['productTypeId'] : $globalProductTypeId;
                $time = isset($params['time'])?$params['time'] : $globalTime;
                $departureZoneId = isset($params['departureZoneId'])?
                    $params['departureZoneId'] : $globalDepartureZoneId;
                $arrivalZoneId = isset($params['arrivalZoneId'])?
                    $params['arrivalZoneId'] : $globalArrivalZoneId;
                $acoId = isset($params['acoId']) ? $params['acoId'] : false;
                if(isset($params['departureZoneId']) ||
                isset($params['arrivalZoneId'])) {
                    $zonesFilters = PrestationManager::getZonesFilters(
                        $departureZoneId, $arrivalZoneId);
                } else {
                    $zonesFilters = $globalZonesFilters;
                }
                $factured = false;
                $concreteProduct = Object::load('ConcreteProduct', $id);
                $f = array(
                    SearchTools::newFilterComponent('ConcreteProduct',
                        'ConcreteProduct().Id', 'Equals', $id, 1,
                        'ConcreteProductPrestationCost'),
                    SearchTools::newFilterComponent('Prestation', 'Prestation.Id',
                        'Equals', $this->getId(), 1));
                $f = SearchTools::FilterAssembler($f);
                $ccPdtPrestCost = $ccPdtPrestCostMapper->load($f);
                if($ccPdtPrestCost instanceof ConcreteProductPrestationCost) {
                    $product = $concreteProduct->getProduct();

                    $weight = $qty * $product->getSellUnitWeight();
                    $volume = $qty * $product->getSellUnitWidth() *
                        $product->getSellUnitHeight() *
                        $product->getSellUnitLength();

                    // filtre pour le costType en cour
                    $filter = PrestationManager::buildFilterForCostType(
                        $costType, $weight, $volume, $qty);
                    $costRange = PrestationManager::getCostRange(
                        $ccPdtPrestCost, $zonesFilters, $filter, $stoID);
                    if($costRange instanceof CostRange) {
                        $price = $costRange->calculPrice(array(
                            'weight'   => $weight,
                            'volume'   => $volume,
                            'quantity' => $qty,
                            'time'     => $time), $coeff
                        );
                        if($costRange->getCostType() == CostRange::TYPE_FIXED) {
                            // si prix au forafit la qty est 1
                            $qty = 1;
                        }
                        $const = CostRange::TYPE_UNIT_FOR_QUANTITY;
                        $priceQty = ($const == $costRange->getCostType()) ?
                            $costRange->getEndRange() : 0;
                        $crID = $costRange->getId();
                        if(!isset($result[$crID])) {
                            $result[$crID] = array(
                                'qty'         => 0,
                                'priceQty'    => $priceQty,
                                'unitPriceHT' => $costRange->getCost(),
                                'priceHT'     => 0,
                                'costType'    => $costRange->getCostType(),
                                'acoIds'      => array()
                            );
                        }
                        $result[$crID]['priceHT'] += $price;
                        $result[$crID]['qty'] += $qty;
                        if($acoId) {
                            $result[$crID]['acoIds'] = array_unique(
                                    array_merge($result[$crID]['acoIds'], array($acoId)));
                        }
                        $factured = true;
                    }
                }
                if(!$factured) {
                    // si on a pas pu facturer le concreteProduct on l'ajoute aux
                    // products
                    $pdtId = $concreteProduct->getProductId();
                    $productIds[] = array(
                        'id'  => $pdtId,
                        'qty' => $qty,
                        'storeId' => $stoID,
                        'time' => $time,
                        'departureZoneId' => $departureZoneId,
                        'arrivalZoneId' => $arrivalZoneId,
                        'acoId' => $acoId
                    );
                }
            }
            // }}}
            // ETAPE 2: facturation des FlyType {{{
            foreach($flyTypeIds as $id=>$qty) {
                $f = array(
                    SearchTools::newFilterComponent('FlyType', 'FlyType().Id',
                        'Equals', $id, 1, 'FlyTypePrestationCost'),
                    SearchTools::newFilterComponent('Prestation',
                        'Prestation.Id', 'Equals', $this->getId(), 1)
                );
                $f = SearchTools::FilterAssembler($f);
                $flyTypePrestCost = $flyTypePrestCostMapper->load($f);
                if($flyTypePrestCost instanceof FlyTypePrestationCost) {
                    $flyType = $flyTypePrestCost->getFlyType();
                    $weight = $volume = 0;
                    // filtre pour le costType en cour
                    $filter = PrestationManager::buildFilterForCostType(
                        $costType, $weight, $volume, $qty);
                    $zonesFilters = $globalZonesFilters;
                    $storeId = $stoID;
                    $costRange = PrestationManager::getCostRange(
                        $flyTypePrestCost, $zonesFilters,
                        $filter, $storeId);
                    if($costRange instanceof CostRange) {
                        $price = $costRange->calculPrice(array(
                            'time'     => $time,
                            'weight'   => $weight,
                            'volume'   => $volume,
                            'quantity' => $qty), $coeff
                        );
                        if($costRange->getCostType() == CostRange::TYPE_FIXED) {
                            // si prix au forafit la qty est 1
                            $qty = 1;
                        }
                        $const = CostRange::TYPE_UNIT_FOR_QUANTITY;
                        $priceQty = ($const == $costRange->getCostType()) ?
                            $costRange->getEndRange() : 0;
                        $crID = $costRange->getId();
                        if(!isset($result[$crID])) {
                            $result[$crID] = array(
                                'qty'         => 0,
                                'priceQty'    => $priceQty,
                                'unitPriceHT' => $costRange->getCost(),
                                'priceHT'     => 0,
                                'costType'    => $costRange->getCostType()
                            );
                        }
                        $result[$crID]['priceHT'] += $price;
                        $result[$crID]['qty'] += $qty;
                    }
                }
            }
            // }}}
            $concreteProductIds = array();
            $remainingProducts = array();
            $facturedProductKeys = array();
            // ETAPE 3: facturation des Product (et des ConcreteProduct.Product?) {{{
            // non facturée à l'ETAPE 1
            
            foreach($productIds as $key=>$params) {
                // Seul cas où !isset($params['id']): dans ack::flightInvoice() 
                // qui ne doit deja pas marcher...
                $productId = isset($params['id'])?$params['id']:$key;
                $qty = $params['qty'];
                $packagingQty = isset($params['packagingqty']) && $params['packagingqty'] > 0 ?
                    $params['packagingqty'] : $qty;
                $stoID = isset($params['storeId'])?
                    $params['storeId'] : $globalStoreId;
                $pdtTypeID = isset($params['productTypeId'])?
                    $params['productTypeId'] : $globalProductTypeId;
                $unitTypeID = isset($params['unitTypeId'])?
                    $params['unitTypeId'] : 0;
                $time = isset($params['time'])?$params['time']:$globalTime;
                $departureZoneId = isset($params['departureZoneId'])?
                    $params['departureZoneId'] : $globalDepartureZoneId;
                $arrivalZoneId = isset($params['arrivalZoneId'])?
                    $params['arrivalZoneId'] : $globalArrivalZoneId;
                $acoId = isset($params['acoId']) ? $params['acoId'] : false;
                if(!isset($params['lemIds'])) {
                    $params['lemIds'] = array();
                }
                if(isset($params['departureZoneId']) ||
                isset($params['arrivalZoneId'])) {
                    $zonesFilters = PrestationManager::getZonesFilters(
                        $departureZoneId, $arrivalZoneId);
                } else {
                    $zonesFilters = $globalZonesFilters;
                }
                $product = Object::load('Product', $productId);
                $weight = $qty * $product->getSellUnitWeight();
                $volume = $qty * $product->getSellUnitWidth() *
                    $product->getSellUnitHeight() * $product->getSellUnitLength();
                
                $rs = request_getProductPrestationCost($this->getId(), $productId);
                if ($rs->EOF) {
                    // si on n'a pas pu facturer le Product on conserve les infos
                    // pour les facturer plus loin
                    $remainingProducts[] = array(
                        'productTypeId' => $pdtTypeID,
                        'storeId' => $stoID,
                        'unitTypeId' => $unitTypeID,
                        'qty' => $qty /*$params['qty']*/,
                        'packagingqty' => $packagingQty,
                        'weight' => $weight,
                        'volume' => $volume,
                        'lemIds' => $params['lemIds'],
                        'time' => $time,
                        'departureZoneId' => $departureZoneId,
                        'arrivalZoneId' => $arrivalZoneId,
                        'acoId' => $acoId);
                    continue;
                }
                $pcId = $rs->fields['pcId'];
                $productPrestCost = $productPrestCostMapper->load(array('Id' => $pcId));
                if($costType==CostRange::TYPE_AMOUNT && $unitTypeID>0) {
                    $qty = $packagingQty;
                }
                if($costType == CostRange::TYPE_FIXED) {
                    // si prix au forfait la qty est 1
                    $qty = 1;
                }
                $filter = PrestationManager::buildFilterForCostType(
                    $costType, $weight, $volume, $qty);
                $costRange = PrestationManager::getCostRange(
                    $productPrestCost, $zonesFilters, $filter, $stoID,
                    $pdtTypeID, $unitTypeID);
                if($costRange instanceof CostRange) {
                    $price = $costRange->calculPrice(array(
                        'weight'   => $weight,
                        'volume'   => $volume,
                        'quantity' => $qty,
                        'time'     => $time), $coeff
                    );
                    // Ne faudrait-il pas ajouter ce OR ?
                    if($costRange->getCostType() == CostRange::TYPE_FIXED /*|| $costRange->getCostType() == CostRange::TYPE_FIXED_QUANTITY*/) {
                        // si prix au forfait la qty est 1
                        $qty = 1;
                    }
                    $const = CostRange::TYPE_UNIT_FOR_QUANTITY;
                    $priceQty = ($const == $costRange->getCostType()) ?
                        $costRange->getEndRange() : 0;
                    $crID = $costRange->getId();
                    if(!isset($result[$crID])) {
                        $result[$crID] = array(
                            'qty'         => 0,
                            'priceQty'    => $priceQty,
                            'unitPriceHT' => $costRange->getCost(),
                            'priceHT'     => 0,
                            'costType'    => $costRange->getCostType(),
                            'lemIds'      => array(),
                            'acoIds'      => array());
                    }
                    $result[$crID]['priceHT'] += $price;
                    $result[$crID]['qty'] += $qty;
                    $result[$crID]['lemIds'] = array_merge(
                        $result[$crID]['lemIds'], $params['lemIds']);
                    if($acoId) {
                        $result[$crID]['acoIds'] = array_unique(
                                array_merge($result[$crID]['acoIds'], array($acoId)));
                    }

                    // Pour ne pas facturer n fois le meme Product et ne pas
                    // passer inutilement dans des boucles de boucles de boucles...
                    $facturedProductKeys[] = $key;
                }
                unset($costRange);
            }
            // On reduit $productIds pour les passages suivants ds la boucle ci-dessus
            if (!empty($facturedProductKeys)) {
                $productIds = array_diff_key($productIds, $facturedProductKeys);
            }
            // }}}
            foreach($productTypeIds as $key=>$params) {
                $pdtTypeId = isset($params['id'])?$params['id']:$key;
                $qty = $params['qty'];
                $packagingQty = isset($params['packagingqty']) && $params['packagingqty'] > 0 ?
                    $params['packagingqty'] : $qty;
                $stoID = isset($params['storeId']) ?
                    $params['storeId'] : $globalStoreId;
                $weight = (isset($params['weight']))? $params['weight'] : 0;
                $volume = (isset($params['volume']))?  $params['volume'] : 0;
                $unitTypeId = (isset($params['unitTypeId']))? $params['unitTypeId'] : 0;
                $time = isset($params['time']) ? $params['time'] : $globalTime;
                $arrivalZoneId = isset($params['arrivalZoneId']) ? $params['arrivalZoneId'] : $globalArrivalZoneId;
                $departureZoneId = isset($params['departureZoneId']) ? $params['departureZoneId'] : $globalDepartureZoneId;
                $acoId = isset($params['acoId']) ? $params['acoId'] : false;

                $remainingProducts[] = array(
                    'productTypeId' => $pdtTypeId,
                    'storeId' => $stoID,
                    'unitTypeId' => $unitTypeId,
                    'time' => $time,
                    'departureZoneId' => $departureZoneId,
                    'arrivalZoneId' => $arrivalZoneId,
                    'qty' => $qty,
                    'packagingqty' => $packagingQty,
                    'weight' => $weight,
                    'volume' => $volume,
                    'lemIds' => array(),
                    'acoId' => $acoId);
            }
            // ETAPE 4: CostRange de la Prestation {{{
            foreach($remainingProducts as $data) {
                $qty = $data['qty'];
                $packagingQty = $data['packagingqty'];
                $weight = $data['weight'];
                $volume = $data['volume'];
                $time = isset($data['time'])?$data['time']:$globalTime;
                $departureZoneId = isset($data['departureZoneId'])?$data['departureZoneId']:$globalDepartureZoneId;
                $arrivalZoneId = isset($data['arrivalZoneId'])?$data['arrivalZoneId']:$globalArrivalZoneId;
                $unitType = $data['unitTypeId'];
                $stoId = $data['storeId'];
                $pdtType = $data['productTypeId'];
                $acoId = isset($data['acoId']) ? $data['acoId'] : false;
                if($costType==CostRange::TYPE_AMOUNT && $unitType>0) {
                    $qty = $packagingQty;
                }
                $filter = PrestationManager::buildFilterForCostType(
                    $costType, $weight, $volume, $qty);
                if(isset($data['departureZoneId']) || isset($data['arrivalZoneId'])) {
                    $zonesFilters = PrestationManager::getZonesFilters(
                        $departureZoneId, $arrivalZoneId);
                } else {
                    $zonesFilters = $globalZonesFilters;
                }
                $costRange = PrestationManager::getCostRange(
                    $this, $zonesFilters, $filter, $stoId, $pdtType, $unitType);
                if($costRange instanceof CostRange) {
                    $crID = $costRange->getId();
                    
                    if($costRange->getCostType() == CostRange::TYPE_FIXED 
                    || $costRange->getCostType() == CostRange::TYPE_FIXED_QUANTITY) {
                        // si prix au forfait la qty est 1
                        
                        $qty = (isset($result[$crID]))?$result[$crID]['qty'] + 1:1;
                        // PATCH vilain sans trop comprendre
                        $result[$crID] = array(
                            'qty'         => $qty,
                            'priceQty'    => 0,
                            'unitPriceHT' => $costRange->getCost(),
                            'priceHT'     => $qty * $costRange->getCost(),
                            'costType'    => $costRange->getCostType(),
                            'lemIds'      => array(),
                            'acoIds' => (isset($result[$crID]['acoIds']))?$result[$crID]['acoIds']:array()
                        );
                        if($acoId) {
                            $result[$crID]['acoIds'][] = $acoId;
                        }
                        $result[$crID]['lemIds'] = $data['lemIds'];
                        continue;
                        // /fin PATCH vilain
                        
                        // patch à l'aveuglette....
                        $price = $costRange->getCost();
                    } 
                    else {
                        $price = $costRange->calculPrice(array(
                            'weight'   => $weight,
                            'volume'   => $volume,
                            'quantity' => $qty,
                            'time'     => $time), $coeff);
                    }
                    $const = CostRange::TYPE_UNIT_FOR_QUANTITY;
                    $priceQty = ($const == $costRange->getCostType()) ?
                        $costRange->getEndRange() : 0;
                    
                    if(!isset($result[$crID])) {
                        $result[$costRange->getId()] = array(
                            'qty'         => 0,
                            'priceQty'    => $priceQty,
                            'unitPriceHT' => $costRange->getCost(),
                            'priceHT'     => 0,
                            'costType'    => $costRange->getCostType(),
                            'lemIds'      => array(),
                            'acoIds'      => array(),
                        );
                    }
                    $result[$crID]['priceHT'] += $price;
                    $result[$crID]['qty'] += $qty;
                    $result[$crID]['lemIds'] = array_merge($result[$crID]['lemIds'], $data['lemIds']);
                    if($acoId) {
                        $result[$crID]['acoIds'] = array_unique(
                                array_merge($result[$crID]['acoIds'], array($acoId)));
                    }
                }
            }
            // }}}

            $weight = isset($globalParams['weight'])?$globalParams['weight']:0;
            $volume = isset($globalParams['volume'])?$globalParams['volume']:0;
            $qty = isset($globalParams['qty'])?$globalParams['qty']:0;
            $time = isset($globalParams['time'])?$globalParams['time'] : 0;

            if($weight > 0 || $volume > 0 || $qty > 0) {
                $filter = PrestationManager::buildFilterForCostType(
                    $costType, $weight, $volume, $qty);
                $zonesFilters = $globalZonesFilters;
                $storeId = isset($storeId) ? $storeId : $globalStoreId;
                $productTypeId = isset($productTypeId) ? $productTypeId : $globalProductTypeId;
                $costRange = PrestationManager::getCostRange(
                    $this, $zonesFilters, $filter, $storeId, $productTypeId);
                if($costRange instanceof CostRange) {
                    $price = $costRange->calculPrice(array(
                        'weight'   => $weight,
                        'volume'   => $volume,
                        'quantity' => $qty,
                        'time'     => $time), $coeff
                    );

                $const = CostRange::TYPE_UNIT_FOR_QUANTITY;
                    $priceQty = ($const == $costRange->getCostType()) ?
                        $costRange->getEndRange() : 0;
                    if($costRange->getCostType() == CostRange::TYPE_FIXED) {
                        // si prix au forafit la qty est 1
                        $qty = 1;
                    }
                    $crID = $costRange->getId();
                    if(!isset($result[$crID])) {
                        $result[$crID] = array(
                            'qty'         => 0,
                            'priceQty'    => $priceQty,
                            'unitPriceHT' => $costRange->getCost(),
                            'priceHT'     => 0,
                            'costType'    => $costRange->getCostType()
                        );
                    }
                    $result[$crID]['priceHT'] += $price;
                    $result[$crID]['qty'] += $qty;
                }
            }
        }
        return $result;
    }

    // }}}
    // Prestation::getTotalPrestationPrice() {{{

    /**
     * Calcul le coût de la prestation en additionnant les qtés prixHT des
     * CostRanges.
     *
     * @see Prestation::getPrestationPrice()
     * @return int
     */
    public function getTotalPrestationPrice($globalParams=array(),
        $concreteProductIds=array(), $flyTypeIds=array(),
        $productIds=array(), $actorIds=array())
    {
        $return = 0;
        /* XXX la méthode n'est utilisé que pour les aco donc le bloc qui suit
         * ne semble pas être nécéssaire ici.
         **/
        // XXX modif david jeudi 23 août 2007, 12:04:01
        $prices = $this->getFixedPrices($this->getOperationId() > 0);
        if (is_array($prices)) {
            foreach ($prices as $costrange=>$price) {
                $return += $price;
            }
        }

        $prices = $this->getPrestationPrice(
            $globalParams,
            $concreteProductIds,
            $flyTypeIds,
            $productIds,
            $actorIds
        );
        if (is_array($prices)) {
            foreach ($prices as $costrange=>$price) {
                //$return += $price['qty'] * $price['priceHT'];
                // La qty est prise en compte dans CostRange::calculPrice si 
                // nécéssaire il ne faut pas remultiplier ici
                $return += $price['priceHT'];
            }
        }
        return $return;
    }

    // }}}
    // Prestation::getActorIds() {{{

    /**
     * Retourne les ids des acteurs associés à la prestation via
     * PrestationCustomer.
     *
     * @access public
     * @return array
     */
    public function getActorIds() {
        $return = array();
        $prsCust = $this->getPrestationCustomerCollection();
        $c = $prsCust->getCount();
        for($i=0 ; $i<$c ; $i++) {
            $o = $prsCust->getItem($i);
            $return[] = $o->getActorId();
        }
        return $return;
    }

    // }}}
    // Prestation::getNameForCustomer() {{{

    /**
     * Retourne le nom de prestation défini pour le client passé en paramètre
     * ou le nom général de la prestation si non trouvé.
     *
     * @access public
     * @param mixed Object Customer $customer or integer $customer ID.
     * @return string
     */
    function getNameForCustomer($customer) {
        if ($customer instanceof Customer) {
            $customer = $customer->getId();
        }
        $col = $this->getPrestationCustomerCollection(array('Actor'=>$customer));
        if ($col->getCount() > 0) {
            return $col->getItem(0)->toString();
        }
        return $this->getName();
    }
    // }}}
    // Prestation::getFixedPrices() {{{

    /**
     * getFixedPrices
     *
     * Retourne les prix fixes associés à la prestation.
     *
     * @param bool $forOperation à false on n'à un résultat que si la prestation 
     * est associé à des mouvements
     * @access public
     * @return array
     */
    public function getFixedPrices($forOperation=false) {
        $return = array();
        $mvt = $this->getMovementTypeCollectionIds();
        if(empty($mvt) && !$forOperation) {
            return $return;
        }
        // prix fixes de la prestation
        $CRcol = Object::loadCollection('CostRange', array(
            'Prestation' => $this->getId(),
            'CostType'   => CostRange::TYPE_FIXED,
            'Store'      => 0)
        );
        $count = $CRcol->getcount();
        for($i=0 ; $i<$count ; $i++) {
            $CR = $CRcol->getItem($i);
            $return[$CR->getId()] = $CR->getCost();
        }
        // prix associés aux PrestationCost
        $prsCostcol = Object::loadCollection('PrestationCost', array(
            'Prestation'=>$this->getId())
        );
        $ids = $prsCostcol->getItemIds();
        $CRcol = Object::loadCollection('CostRange', array(
            'PrestationCost' => $ids,
            'CostType'       => CostRange::TYPE_FIXED,
            'Store'          => 0)
        );
        $count = $CRcol->getcount();
        for($i=0 ; $i<$count ; $i++) {
            $CR = $CRcol->getItem($i);
            $return[$CR->getId()] = $CR->getCost();
        }
        return $return;
    }

    // }}}
    // Prestation::getFixedPricesByLocations() {{{

    /**
     * getFixedPricesByLocations
     *
     * calcul le prix fixe par emplacement
     * paramètres:
     * <code>
     * $locations = array(
     *     3 => array(          // id de Store (magasin)
     *          4 => array(     // id de ProductType
     *              'numberOfDays' => 30 // nb de jours ds le mois
     *              'qty'     => 8  // nombre de jours
     *          ),
     *     ),
     *     6 => array(          // id de Store
     *          4 => array(     // id de ProductType
     *              'numberOfDays' => 30 // nb de jours ds le mois
     *              'qty'     => 8  // nombre de jours
     *          ),
     *     ), ...
     * );
     * </code>
     * @param array $locations
     * @param array $zoneFilter FilterComponentArray
     * @param object $costTypeFilter FilterComponent filtre dependant du costType
     * @access public
     * @return mixed array
     * @todo: avec un SUM(qty) GROUP BY, on y gagnerait!!!!! #####################
     */
    public function getFixedPricesByLocations($locations, $zoneFilter, $costTypeFilter) {
        $return = array();
        // Pour eviter de recalculer n fois la meme chose, on stocke les couples
        // (storeId, productTypeId) traites, car pour 2 locations donnees,
        // on peut avoir le meme couple
        $alreadyDone = array();
        foreach($locations as $storeId => $productTypes) {
            foreach($productTypes as $productTypeId => $params) {
                if (!array_key_exists($storeId . '_' . $productTypeId, $alreadyDone)) {
                    $CR = PrestationManager::getCostRange($this, $zoneFilter,
                        $costTypeFilter, $storeId, $productTypeId);
                    $alreadyDone[$storeId . '_' . $productTypeId] = $CR;
                }
                else {
                    $CR = $alreadyDone[$storeId . '_' . $productTypeId];
                }
                
                if($CR === false) {  // Pas de CostRange trouve
                    continue;
                }
                $CRid = $CR->getId();
                if(!isset($return[$CRid])) {
                    $return[$CRid] = array(
                        'qty'         => 0,
                        'unitPriceHT' => $CR->getCost(),
                        'priceHT'     => 0,
                        'costType'    => $CR->getCostType()
                    );
                }
                $return[$CRid]['qty'] += $params['qty'];
                $coeff = 1;
                if($this->getPeriodicity() == Prestation::PERIODICITY_MONTH) {
                    $numberOfDays = isset($params['numberOfDays'])?
                        $params['numberOfDays'] : NUMBER_OF_DAYS_PER_MONTH;
                    $coeff = 1 / $numberOfDays;
                }
                $return[$CRid]['priceHT'] += $params['qty'] * $CR->getCost() * $coeff;
            }
        }
        return $return;
    }

    // }}}
    // Prestation::getProductIds() {{{

    /**
     * getProductIds
     *
     * Retourne les ids des produits associés à la prestation
     *
     * @access public
     * @return void
     */
    public function getProductIds() {
        $pdtIds = array();
        $ppcCol = $this->getPrestationCostCollection(array('ClassName'=>'ProductPrestationCost'));
        $count = $ppcCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $ppc = $ppcCol->getItem($i);
            $pdtIds = array_merge($pdtIds, $ppc->getProductCollectionIds());
        }
        return $pdtIds;
    }

    // }}}
}

?>