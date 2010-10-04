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

require_once('Objects/Prestation.php');
require_once('Objects/CostRange.php');
require_once('Objects/ActivatedChainOperation.php');
require_once('Objects/ActivatedChainOperation.inc.php');
require_once('Objects/Task.inc.php');

/**
 * Défini le nombre de jour dans un mois pour les prestations calculées avec un
 * prix par mois (Prestation.Periodicity=Prestation::PERIODICITY_MONTH)
 * @var int
 */
define('NUMBER_OF_DAYS_PER_MONTH', 30);

if(!defined('DEBUG')) {
    define('DEBUG', true);  // false
}

/**
 * PrestationManager
 *
 */
class PrestationManager {
    // propriétées {{{

    /**
     * Id de l'acteur de l'utilisateur connecté
     * @var int
     * @access private
     */
    private $_actorId;

    /**
     * Id du client à facturer
     * @var int
     * @access private
     */
    private $_customerId = 0;

    /**
     * supplierCustomer
     * @var Object SupplierCustomer
     * @access public
     */
    public $supplierCustomer = false;

    /**
     * withTVA
     * @var boolean
     * @access public
     */
    public $withTVA = true;

    /**
     * date de début
     * @var date
     * @access private
     */
    private $_beginDate = 0;

    /**
     * date de fin
     * @var date
     * @access private
     */
    private $_endDate = 0;

    /**
     * Id des prestationCustomers à facturer (si recherche avancée)
     * @var array
     * @access private
     */
    private $_prestationCustomerIds = array();

    /**
     * Id des sites de stockages à prendre en compte (si recherche avancée)
     * @var array
     * @access private
     */
    private $_storageSiteIds = array();

    /**
     * Id des stores à prendre en compte (si recherche avancée)
     * @var array
     * @access private
     */
    private $_storeIds = array();

    /**
     * Id des products à prendre en comte (si recherche avancée)
     * @var array
     * @access private
     */
    private $_productIds = array();

    /**
     * Id des propriétaire de prodiots à prendre en compte (si recherche avancée)
     * @var array
     * @access private
     */
    private $_productOwnerIds = array();

    /**
     * Tableau des éléments à facturer
     * <code>
     * array(
     *   prestationId => array(
     *      'products' => array(
     *          0 => array(
     *              'id' => productId,
     *              'qty' => productQty,
     *              'store' => storeId,
     *              'pdtTypeId' => productTypeId
     *          ),
     *          1 => array(
     *              [...]
     *          )
     *      ),
     *      'concreteproducts' => array(
     *          0 => array(
     *              'id' =>
     *              'departurezone' =>
     *              'arrivalzone' =>
     *          )
     *      )
     *   )
     * )
     * </code>
     * @var array
     * @access private
     */
    private $_toInvoice = array();

    /**
     * Collection de CommandItem.
     * @var Collection
     * @access private
     */
    public $commandItems;

    /**
     * permet de conserver les lem associés à un commandItem pour ensuite
     * affecté LEM.InvoiceItem.
     * @var array
     * @access private
     */
    private $_commandItemLemIds = array();

    /**
     * Ids des aco associé à un commandItem
     * 
     * @var array
     * @access private
     */
    private $_commandItemAcoIds = array();

    /**
     * Les produits associés à la prestatio
     */
    private $_additionalProducts = array();
    /**
     * Id des MovementType à prendre en compte (en fonction des prestations)
     * @var array
     * @access private
     */
    private $_movementTypeIds = array();

    /**
     * Id des Operations à prendre en compte (en fonction des prestations)
     * @var array
     * @access private
     */
    private $_operationIds = array();

    /**
     * Collection des ActivatedChainOperation qui doivent être facturés donc
     * mis à jour.
     * @var Collection
     * @access public
     */
    public $acoCollection;

    /**
     * Collection des LocationExecutedMovement qui doivent être facturés donc
     * mis à jour.
     * @var Collection
     * @access public
     */
    public $lemCollection;

    /**
     * Collection des OccupiedLocation qui doivent être facturés donc
     * mis à jour.
     * @var Collection
     * @access public
     */
    public $olCollection;

    /**
     * _logger
     *
     * @var mixed
     * @access private
     */
    private $_logger=false;

    /**
     * id des box utilisés pour facturer une aco de transport précédée d'une
     * tâche de regroupement
     *
     * @var array
     * @access private
     */
    private $_boxIds = array();

    /**
     * id des lem utilisés pour facturer une aco de transport précédée d'une
     * tache de sortie de stock
     *
     * @var array
     * @access private
     */
    private $_transportLEMIds = array();

    /**
     * contient les ids des prestations associé à une operation de transport.
     * 
     * @var array
     * @access private
     */
    private $_prestationUsedForTransportACO = array();

    /**
     * contient les ids de toutes les prestations facturables et actives associé 
     * au client à facturer. 
     * 
     * @var array
     * @access private
     */
    private $_prestationIDs = array();
    
    /**
     * permet d'ajouter un tableau au facture de prestation avec le détail de ce 
     * qui est facturé pour chaque aco de transport
     * 
     * @var array
     * @access private
     */
    private $_transportDetails = array();

    // }}}
    // PrestationManager::__construct() {{{

    /**
     * Instancie le PrestationManager.
     *
     * Les paramètres de prestations sont:
     * - customer id du client (obligatoire)
     * - begindate date de début (optionnel si les collections sont déjà
     * construites)
     * - enddate date de fin (optionnel si les collections sont déjà construites)
     * - prestationcustomer tableau d'id de PrestationCustomer à prendre en compte
     * (optionnel)
     * - store tableau d'id des Store à prendre en compte (optionnel)
     * - product tableau d'id des products à prendre en compte (optionnel)
     *
     * @param array $param paramètres de facturation
     * @access public
     * @return void
     */
    public function __construct($params=array()) {
        $auth = Auth::singleton();
        $this->_actorId = $auth->getActorId();

        $this->_logger = Tools::loggerFactory();

        // récupère les paramètres obligatoires:
        $this->_customerId = isset($params['customer']) ?
            $params['customer']:false;
        $this->_beginDate = isset($params['begindate'])?
            $params['begindate'] . ' 00:00:00':'';
        $this->_endDate = isset($params['enddate'])?
            $params['enddate'] . ' 23:59:59':'';

        // récupère les paramètres facultatifs
        $this->_prestationCustomerIds = isset($params['prestationcustomer']) ?
            $params['prestationcustomer'] : array();
        $this->_storageSiteIds = isset($params['storageSite']) ?
            $params['storageSite'] : array();
        $this->_storeIds = isset($params['store']) ?
            $params['store'] : array();
        $this->_productIds = isset($params['product']) ?
            $params['product'] : array();
        $this->_productOwnerIds = isset($params['productOwner']) ?
            $params['productOwner'] : array();

        // récupère le supplierCustomer
        $mapper = Mapper::singleton('SupplierCustomer');
        $this->supplierCustomer = $mapper->load(
            array('Customer' => $this->_customerId, 'Supplier' => $this->_actorId));

        // Faut il facturer la TVA ?
        $this->withTVA = ($this->supplierCustomer instanceof SupplierCustomer
            && $this->supplierCustomer->getHasTVA());

        // Récupère les collections si elles sont en session {{{
        if(isset($_SESSION['ACOCollection'])) {
            $this->acoCollection = Object::loadCollection(
                'ActivatedChainOperation',
                array('Id' => $_SESSION['ACOCollection']));
            Session::prolong('ACOCollection', 2);
        }
        if(isset($_SESSION['LEMCollection'])) {
            $this->lemCollection = Object::loadCollection(
                'LocationExecutedMovement',
                array('Id' => $_SESSION['LEMCollection']));
            Session::prolong('LEMCollection', 2);
        }
        if(isset($_SESSION['OLCollection'])) {
            $this->olCollection = Object::loadCollection('OccupiedLocation',
                array('Id' => $_SESSION['OLCollection']));
            Session::prolong('OLCollection', 2);
        }
        if(isset($_SESSION['PrestationCommandItemCollection'])) {
            $this->commandItems = unserialize(
                $_SESSION['PrestationCommandItemCollection']);
            Session::prolong('PrestationCommandItemCollection', 2);
        } else {
            $this->commandItems = new Collection();
        }
        if(isset($_SESSION['CommandItemLemIds'])) {
            $this->_commandItemLemIds = $_SESSION['CommandItemLemIds'];
            Session::prolong('CommandItemLemIds', 1);
        }
        if(isset($_SESSION['CommandItemACOIds'])) {
            $this->_commandItemAcoIds = $_SESSION['CommandItemACOIds'];
            Session::prolong('CommandItemACOIds', 1);
        }
        if(isset($_SESSION['facturedBoxIds'])) {
            $this->_boxIds = $_SESSION['facturedBoxIds'];
            Session::prolong('facturedBoxIds', 1);
        }
        if(isset($_SESSION['transportLEMIds'])) {
            $this->_transportLEMIds = $_SESSION['transportLEMIds'];
            Session::prolong('transportLEMIds', 1);
        }
        if(isset($_SESSION['TransportDetails'])) {
            $this->_transportDetails = $_SESSION['TransportDetails'];
            Session::prolong('TransportDetails', 1);
        }
        // }}}
    }

    // }}}
    // PrestationManager::process() {{{

    /**
     * Lance la recherche des Prestations à facturer, puis celles des ACO, LEM
     * et OccupiedLocation.
     *
     * @access public
     * @return void
     */
    public function process() {
        $this->findPrestations();
        if (!empty($this->_operationIds)) {
            $this->findActivatedChainOperations();
        }
        $this->findLocationExecutedMovements();
        $this->findOccupiedLocations();
    }

    // }}}
    // PrestationManager::findPrestations() {{{

    /**
     * Recherche les Prestation à facturer en fonction du customer et
     * éventuellement des PrestationCustomer sélectionnés.
     *
     * Trouve ensuite les MovementType et Operation à prendre en compte pour la
     * recherhce des LEM et ACO.
     *
     * Puis crée les commandItems pour les prix fixes de chaque prestation.
     *
     * @access public
     * @return void
     */
    public function findPrestations() {
        $filter = array(
            SearchTools::newFilterComponent('Actor',
                'PrestationCustomer().Actor.Id', 'Equals', $this->_customerId,
                1, 'Prestation'),
            SearchTools::newFilterComponent('Active', '', 'Equals', 1, 1),
            SearchTools::newFilterComponent('Facturable', '', 'Equals', 1, 1)
        );
        if(!empty($this->_prestationCustomerIds)) {
            $filter[] = SearchTools::newFilterComponent('PrestationCustomer',
                'PrestationCustomer().Id', 'In', $this->_prestationCustomerIds,
                1, 'Prestation');
        }
        $filter = SearchTools::filterAssembler($filter);
        $prestationCol = Object::loadCollection('Prestation', $filter);
        $this->_prestationIDs = $prestationCol->getItemIds();
        $this->log('prestations trouvées:' . implode(',', $this->_prestationIDs));
        $count = $prestationCol->getCount();
        for($i=0; $i<$count; $i++) {
            $prs = $prestationCol->getItem($i);
            $this->_additionalProducts = array_merge(
                $this->_additionalProducts, $prs->getProductIds());
            $this->_movementTypeIds = array_merge($this->_movementTypeIds,
                $prs->getMovementTypeCollectionIds());
            if ($prs->getOperationId() != 0) {
                $this->_operationIds[] = $prs->getOperationId();
            }          
            $prices = $prs->getFixedPrices();
            foreach($prices as $crID => $cost) {
                $commandItem = new PrestationCommandItem();
                $commandItem->setPrestation($prs->getId());
                if($this->withTVA) {
                    $commandItem->setTVA($prs->getTVA());
                }
                $commandItem->setQuantity(1);
                $commandItem->setUnitPriceHT($cost);
                $commandItem->setPriceHT($cost);
                $commandItem->setCostType(CostRange::TYPE_FIXED);
                $this->commandItems->setItem($commandItem);
            }
        }

        $this->_movementTypeIds = array_unique($this->_movementTypeIds);
        $this->_additionalProducts = array_unique($this->_additionalProducts);

        $this->log('opérations trouvées:' . implode(',', $this->_operationIds));
        $this->log('mouvements trouvés:' . implode(',', $this->_movementTypeIds));
    }

    // }}}
    // PrestationManager::findActivatedChainOperations() {{{

    /**
     * Recherche les ACO pas encore facturé totalement, comprisent dans la plage
     * de date, associée à une des operations à prendre en compte, avec l'expediteur
     * ou le destinataire de la commande qui est le client à facturer.
     *
     * @access public
     * @return void
     */
    public function findActivatedChainOperations() {
        require_once('SQLRequest.php');
        $acoIds = array();
        $rs = request_prestation_findACO(
            $this->_operationIds, 
            $this->_beginDate, 
            $this->_endDate, 
            $this->_customerId);
        
        while ($rs && !$rs->EOF) {
    		$acoIds[] = $rs->fields['acoId'];
    		$rs->moveNext();
    	}
    	$acoIds = array_unique($acoIds);
    	if (empty($acoIds)) {  // Rien a facturer dans ce cas
    	    return false;
    	}
        $fixedPrices = array();
        // recherche des aco
        $acoCollection = Object::loadCollection(
            'ActivatedChainOperation', array('Id' => $acoIds));
        $count = $acoCollection->getCount();
        $tmp = array();
        $globalsParams = array();
            
        $torm = array();
        for($i = 0; $i < $count; $i++) {
            $continue = true;
            $aco = $acoCollection->getItem($i);
            // on ignore les aco qui n'ont que des tâches d'activation
            $ackCol = $aco->getActivatedChainTaskCollection();
            $ackCount = $ackCol->getCount();
            for($j=0;$j<$ackCount;$j++) {
                $ack = $ackCol->getItem($j);
                if($ack->gettaskId() != TASK_ACTIVATION) {
                    $continue = false;
                    break;
                }
            }
            if($continue) {
                // l'aco sera à enlever de la collection
                $torm[] = $i;
                continue;
            }
            $prs = $aco->findPrestation($this->_customerId);
            $prsId = $prs->getId();
            $duration = $aco->getACODuration() / 3600;
            $departureZoneId = Tools::getValueFromMacro($aco,
                '%FirstTask.ActorSiteTransition.DepartureSite.Zone.Id%');
            $arrivalZoneId = Tools::getValueFromMacro($aco,
                '%LastTask.ActorSiteTransition.ArrivalSite.Zone.Id%');
            $this->log('XXX ' . $departureZoneId . ' to ' . $arrivalZoneId);

            // préparation des products
            $ach = $aco->getActivatedChain();
            $cmdItemCol = $ach->getCommandItemCollection();
            $jcount = $cmdItemCol->getCount();
            $tmp = array();
            for ($j=0 ; $j<$jcount ; $j++) {
                $cmdItem = $cmdItemCol->getItem($j);
                if(method_exists($cmdItem, 'getProduct')) {
                    $product = $cmdItem->getProduct();
                    $unitTypeId = $product->getSellUnitTypeId();
                    $pdtID = $product->getId();
                    $tmp['products'][] = array(
                        'id' => $pdtID,
                        'unitTypeId' => $unitTypeId,
                        'qty' => $cmdItem->getQuantity(),
                        'packagingqty' => $product->packagingUnitNumber($cmdItem->getQuantity()),
                        'weight' => $cmdItem->getQuantity() * $product->getSellUnitWeight(),
                        'volume' => $cmdItem->getQuantity() * $product->getSellUnitLength() * 
                            $product->getSellUnitHeight() * $product->getSellUnitWidth(),
                        'productTypeId' => $product->getProductTypeId(),
                        'time' => $duration,
                        'departureZoneId' => $departureZoneId,
                        'arrivalZoneId' => $arrivalZoneId,
                        'acoId' => $aco->getId()
                    );
                } elseif($cmdItem instanceof ChainCommandItem) {
                    $pdtTypeId = $cmdItem->getProductTypeId();
                    $coverType = $cmdItem->getCoverType();
                    $unitTypeId = $coverType->getUnitTypeId();
                    $tmp['producttypes'][] = array(
                        'id' => $pdtTypeId,
                        'unitTypeId' => $unitTypeId,
                        'qty' => $cmdItem->getQuantity(),
                        'packagingqty' => $cmdItem->getQuantity(),
                        'weight' => $cmdItem->getQuantity() * $cmdItem->getWeight(),
                        'volume' => $cmdItem->getQuantity() * $cmdItem->getLength() * 
                            $cmdItem->getHeight() * $cmdItem->getWidth(),
                        'time' => $duration,
                        'departureZoneId' => $departureZoneId,
                        'arrivalZoneId' => $arrivalZoneId,
                        'acoId' => $aco->getId()
                    );
                }
            }

            if(isTransportOperation($aco)) {
                $this->_prestationUsedForTransportACO[$prsId] = $aco->getId();
                $cmd = $cmdItemCol->getItem(0)->getCommand();
                $result = $this->_getParamsForTransportACO($aco, $cmd->getId(), true);
                if(is_array($result)) {
                    $tmp = $result;
                }
            }

            foreach($tmp as $key=>$data) {
                if(!isset($this->_toInvoice[$prsId][$key])) {
                    $this->_toInvoice[$prsId][$key] = array();
                }
                $this->_toInvoice[$prsId][$key] = array_merge($this->_toInvoice[$prsId][$key], $data);
            }

            // préparation du concreteProduct
            $ccpId = $aco->getConcreteProductId();
            if($ccpId > 0) {
                $this->_toInvoice[$prsId]['concreteproduct'][] = array(
                    'qty' => 1,
                    'time' => $duration,
                    'departureZoneId' => $departureZoneId,
                    'arrivalZoneId' => $arrivalZoneId);
            }
            // prix fixes de la prestations
            if(!isTransportOperation($aco)) {
                $prices = $prs->getFixedPrices(true);
                foreach($prices as $crID => $cost) {
                    if(!isset($fixedPrices[$crID])) {
                        $fixedPrices[$crID] = array(
                            'prestation' => $prs->getId(),
                            'tva' => $prs->getTVA(),
                            'cost' => $cost,
                            'qty'=>1
                        );
                    }
                }
            }
        }
        $acoCollection->removeItem($torm);
        Session::register('ACOCollection', $acoCollection->getItemIds(), 2);
        $this->log('ACO facturées:' . implode(',', $_SESSION['ACOCollection']));
        // création des commandsItems pour les prix fixes
        foreach($fixedPrices as $crID => $params) {
            $commandItem = new PrestationCommandItem();
            $commandItem->setPrestation($params['prestation']);
            if($this->withTVA) {
                $commandItem->setTVA($params['tva']);
            }
            $commandItem->setQuantity($params['qty']);
            $commandItem->setUnitPriceHT($params['cost']);
            $commandItem->setPriceHT($params['cost']*$params['qty']);
            $commandItem->setCostType(CostRange::TYPE_FIXED);
            $this->commandItems->setItem($commandItem);
        }

        foreach($globalsParams as $prsId=>$data) {
            $this->_toInvoice[$prsId]['globals'] = $data;
        }
    }

    // }}}
    // PrestationManager::findLocationExecutedMovements() {{{

    /**
     * Recherche les LEM
     *
     * Par défaut on prend les LEM tels que:
     * - LEM.PrestationFactured=0
     * - ET LEM.Cancelled NOT IN (-1, 1)
     * - ET LEM.ExecutedMovement.Type dans les Types de mouvements associés aux
     * prestations facturables du clients
     * - ET LEM.Date dans le créneau sélectionné
     * - ET LEM.Location.Store.StorageSite.Owner = Acteur de l'utilisateur
     * connecté'
     * - ET (LEM.Product.Owner = client sélectionné
     *      OU Lem.Product dans les Produits addociés aux prestation facturables
     *      du clients)
     * @access public
     * @return void
     */
    public function findLocationExecutedMovements() {
        // construction du filtre
        $filter = array(
            SearchTools::newFilterComponent('PrestationFactured', '', 'Equals', 0, 1),
            SearchTools::newFilterComponent('Cancelled', '', 'NotIn', array(-1, 1), 1),
            SearchTools::newFilterComponent('MovementType',
                'ExecutedMovement.Type', 'In', $this->_movementTypeIds, 1),
            SearchTools::NewFilterComponent('Date', '', 'GreaterThanOrEquals',
                $this->_beginDate, 1),
            SearchTools::NewFilterComponent('Date', '', 'LowerThanOrEquals',
                $this->_endDate, 1)
        );
        if(empty($this->_storeIds) && empty($this->_storageSiteIds)) {
            $filter[] = SearchTools::newFilterComponent('Store',
                'Location.Store.StorageSite.Owner', 'Equals',
                $this->_actorId, 1);
        } elseif(empty($this->_storeIds)) {
            $filter[] = SearchTools::newFilterComponent('StorageSite',
                'Location.Store.StorageSite', 'In', $this->_storageSiteIds, 1);
        } else {
            $filter[] = SearchTools::newFilterComponent('Store',
                'Location.Store', 'In', $this->_storeIds, 1);
        }

        $filterProduct = array();
        if(empty($this->_productOwnerIds)) {
            $filterProduct[] = SearchTools::newFilterComponent('Owner', 'Product.Owner',
                'Equals', $this->_customerId, 1);
        } elseif(empty($this->_productIds)) {
            $filterProduct[] = SearchTools::newFilterComponent('Owner', 'Product.Owner',
                'In', $this->_productOwnerIds, 1);
        } else {
            $filterProduct[] = SearchTools::newFilterComponent('Product', 'Product',
                'In', $this->_productIds, 1);
        }
        if(!empty($this->_additionalProducts)) {
            $filterProduct[] = SearchTools::newFilterComponent('Product', 'Product',
                'In', $this->_additionalProducts, 1);
            $filter[] = SearchTools::filterAssembler($filterProduct, FilterComponent::OPERATOR_OR);
        } else {
            $filter[] = $filterProduct[0];
        }

        $filter = SearchTools::filterAssembler($filter);
        // recherche des lem
        $lemCollection = Object::loadCollection('LocationExecutedMovement',
            $filter);
        Session::register('LEMCollection', $lemCollection->getItemIds(), 2);

        $this->log('LEM à facturer:' . implode(',', $_SESSION['LEMCollection']));

        // mise en forme des lem
        $count = $lemCollection->getCount();
        if ($count == 0) {
            return false;
        }
        $tmp = array();
        for($i = 0; $i < $count; $i++) {
            $lem = $lemCollection->getItem($i);
            $product = $lem->getProduct();
            $prsCol = $lem->findPrestation($this->_customerId, $this->_prestationIDs);
            $kcount = $prsCol->getCount();
            for($k=0 ; $k<$kcount ; $k++) {
                $prs=$prsCol->getItem($k);
                $prsId = $prs->getId();
                // préparation des products
                $storeId = Tools::getValueFromMacro($lem, '%Location.Store.Id%');
                $pdtId = $product->getId();
                $this->_toInvoice[$prsId]['products'][] = array(
                    'id' => $pdtId,
                    'storeId' => $storeId,
                    'qty' => $lem->getQuantity(),
                    'productTypeId' => $product->getProductTypeId(),
                    'lemIds' => array($lem->getId()));
                // préparation des concreteProducts
                $lemCcpCol = $lem->getLEMConcreteProductCollection();
                $jcount = $lemCcpCol->getCount();
                $concreteProductParams = array();
                for($j=0 ; $j<$jcount ; $j++) {
                    $lemCCP = $lemCcpCol->getItem($j);
                    $ccpId = $lemCCP->getConcreteProductId();
                    $this->_toInvoice[$prsId]['concreteproducts'][] = array(
                        'id' => $ccpId,
                        'storeId' => $storeId,
                        'productTypeId' => $product->getProductTypeId(),
                        'qty' => $lemCCP->getQuantity(),
                        'lemIds' => array($lem->getId()));
                }
            }
        }
    }

    // }}}
    // PrestationManager::findOccupiedLocations() {{{

    /**
     * Recherche les occupiedlocations et facturation du stockage
     *
     * @access public
     * @return void
     */
    public function findOccupiedLocations() {
        require_once('SQLRequest.php');
        // on cherche les OL un mois à la fois pour le calcul des prix aux mois
        // où il faut tenir compte du nombre de jour du mois.
        $beginDate = $this->_beginDate;
        $endDate = $this->_endDate;
        $continue = true;
        $totalPrices = array();
        $pMapper = Mapper::singleton('Prestation');
        $olIds = array();  // Sera mis en session
        
        if(empty($this->_productOwnerIds)) {
            $productOwnerIds = array($this->_customerId);
            $productIds = array();
        } elseif(empty($this->_productIds)) {
            $productOwnerIds = $this->_productOwnerIds;
            $productIds = array();
        } else {
            $productIds = $this->_productIds;
            $productOwnerIds = array();
        }
        
        $prestationFilter = array(
                'Id' => $this->_prestationIDs, 
                'Type' => Prestation::PRESTATION_TYPE_STOCKAGE
        );
        $prestationCol = $pMapper->loadCollection($prestationFilter);
        $nb = $prestationCol->getCount();
        $array2 = DateTimeTools::dateExploder($endDate);
        $zoneFilter = PrestationManager::getZonesFilters();
        $costTypeFilter = new FilterComponent(
                new FilterRule('CostType', 
                FilterRule::OPERATOR_EQUALS, 
                CostRange::TYPE_FIXED_BY_LOCATION)
        );

        // On boucle sur le nombre de mois chevauchés par le creneau choisi
        while ($continue) {
            $array1 = DateTimeTools::dateExploder($beginDate);
            // 1er jour du mois de debut de creneau
            $startTime = mktime(
                0, 0, 0,
                $array1['month'], $array1['day'], $array1['year']
            );
            $numberOfDaysInCurrentMonth = date('t', $startTime);
            // Si debut et fin de creneau concernent le meme mois:
            if($array1['month'] == $array2['month'] && $array1['year']==$array2['year']) {
                $continue = false;
                $dateInf = $beginDate;
                $dateSup = $endDate;
            } else {
                // La derniere seconde du mois 
                $endTime = strtotime("+". $numberOfDaysInCurrentMonth . " day", $startTime - 1);
                //$endTime = $startTime + (86400 * date('t', $startTime)) - 1;
                $dateInf = $beginDate;
                // On fixe a 23:59:59 a cause du chagt heure ete/hiver!!!
                $dateSup = date('Y-m-d 23:59:59', $endTime);
                // Pour le prochain passage ds la boucle : le 1er jour du mois suivant à 0h
                // Un peu complique mais necessaire...
                $beginDate = date(
                    'Y-m-d 00:00:00', DateTimeTools::mySQLDateToTimeStamp($dateSup) + 1);
            }
            
            // recherche des ol facturables, et repondant aux criteres de recherche
            // (Si delai de carence pas atteint, un OL est non facturable)
            // On recupere juste les ids d'OccupiedLocation dans un 1er temps
        	$rs = request_prestation_findOLIds(
                $this->_customerId, 
                $this->_storeIds, 
                $this->_productOwnerIds, 
                $productIds, 
                $dateInf, 
                $dateSup);
        
            while (!$rs->EOF) {
        		$olIds[] = $rs->fields['olId'];
        		$rs->moveNext();
        	}
            $this->log('OccupiedLocations trouvées pour le mois de ' .
                $array1['month'] . ': ' . implode(',', $olIds));
            
            $locationsArray = $productIds = array();
                
            // ATTENTION: meme si pas de ol trouve, il peut y avoir un stockage
            // a facturer: si la prestation est au FORFAIT!!
        	if (!empty($olIds)) {  // //////Rien a facturer dans ce cas
        	    /* On fait ensuite 2 req avec des group by, pour: 
                - sommer le nombre de OccupiedLocation pour la 1ere requete
                - cumuler les qtes pour la 2nde requete
                */
                $rs = request_prestation_stockage($olIds, 'storeId');
    
                while (!$rs->EOF) {
            		$locationsArray[$rs->fields['storeId']][$rs->fields['pdtTypeId']] = 
                        array(
                            'numberOfDays' => $numberOfDaysInCurrentMonth, // NB de jours ds le mois !!!
                            'qty' => $rs->fields['olNumber']
                        );
            		$rs->moveNext();
            	}
            	$rs = request_prestation_stockage($olIds, 'pdtId');
                while (!$rs->EOF) {
            		$productIds[] = array(
                        'id' => $rs->fields['pdtId'],
                        'storeId' => $rs->fields['storeId'],
                        'productTypeId' => $rs->fields['pdtTypeId'],
                        'qty' => $rs->fields['qty']);
            		$rs->moveNext();
            	} 
        	}
///            
       	
            for($i=0 ; $i<$nb ; $i++) {
                $prestation = $prestationCol->getItem($i);
                // ajout des prix fixes par emplacements
                $prices1 = $prestation->getFixedPricesByLocations(
                    $locationsArray, $zoneFilter, $costTypeFilter);
                $prices2 = $prestation->getPrestationPrice(
                    array('numberOfDays' => $numberOfDaysInCurrentMonth),
                    array(), array(), $productIds);

                $prices = $prices1 + $prices2;
                foreach($prices as $crID => $params) {
                    if(!isset($totalPrices[$crID])) {
                        $totalPrices[$crID] = array(
                            'qty' => 0,
                            'unitPriceHT' => $params['unitPriceHT'],
                            'priceHT' => 0,
                            'costType' => $params['costType'],
                            'priceQty' => isset($params['priceQty']) ? $params['priceQty'] :0);
                    }
                    $totalPrices[$crID]['qty'] += $params['qty'];
                    $totalPrices[$crID]['priceHT'] += $params['priceHT'];
                }
                unset($prices1, $prices2);
            }
        }
        // Serviva dans le saveAll(), a leur affecter un InvoiceItemId
        Session::register('OLCollection', $olIds, 2);
     
        foreach($totalPrices as $crID=>$params) {
            $cr = Object::load('CostRange', $crID);
            $prestation = $cr->findPrestation();
            $commandItem = new PrestationCommandItem();
            $commandItem->setPrestation($prestation->getId());
            if($this->withTVA) {
                $commandItem->setTVA($prestation->getTVA());
            }
            $commandItem->setQuantity($params['qty']);
            $commandItem->setQuantityForPrestationCost($params['priceQty']);
            $commandItem->setUnitPriceHT($params['unitPriceHT']);
            $commandItem->setPriceHT($params['priceHT']);
            $commandItem->setCostType($params['costType']);
            $this->commandItems->setItem($commandItem);
            $this->log('CommandItem créé pour le CostRange: ' . $crID);
        }
    }

    // }}}
    // PrestationManager::createCommandItems() {{{

    /**
     * createCommandItems
     *
     * @access public
     * @return void
     */
    public function createCommandItems() {
        $mapperCmi = Mapper::singleton('PrestationCommandItem');

        foreach($this->_toInvoice as $prsId=>$data) {
            if(!isset($data['concreteproducts'])) {
                $data['concreteproducts'] = array();
            }
            if(!isset($data['flytypes'])) {
                $data['flytypes'] = array();
            }
            if(!isset($data['products'])) {
                $data['products'] = array();
            }
            if(!isset($data['producttypes'])) {
                $data['producttypes'] = array();
            }
            if(!isset($data['globals'])) {
                $data['globals'] = array();
            }

            $prestation = Object::load('Prestation', $prsId);
            $usedForTransport = isset($this->_prestationUsedForTransportACO[$prsId]);
            $prices = $prestation->getPrestationPrice($data['globals'],
                $data['concreteproducts'], $data['flytypes'],
                $data['products'], array(), $data['producttypes'],
                $usedForTransport);

            foreach($prices as $crID=>$params) {
                $commandItem = new PrestationCommandItem();
                $commandItem->setId($mapperCmi->generateId());
                $commandItem->setPrestation($prestation->getId());
                if($this->withTVA) {
                    $commandItem->setTVA($prestation->getTVA());
                }
                $commandItem->setQuantity($params['qty']);
                $commandItem->setQuantityForPrestationCost($params['priceQty']);
                $commandItem->setUnitPriceHT($params['unitPriceHT']);
                $commandItem->setPriceHT($params['priceHT']);
                $commandItem->setCostType($params['costType']);
                $this->_commandItemLemIds[$commandItem->getId()] = isset($params['lemIds'])?$params['lemIds']:array();
                $this->commandItems->setItem($commandItem);
                
                if($usedForTransport) {
                    $this->_commandItemAcoIds[$commandItem->getId()] = isset($params['acoIds'])?$params['acoIds']:array();
                    // si la prestations est utilisé pour facturer une aco de 
                    // transport, il faut détailler ce qui concerne le transport 
                    // dans la facture, l'aco et le productType seront lié à l'InvoiceItem
                    $costRange = Object::load('CostRange', $crID);
                    $this->_transportDetails[$commandItem->getId()] = array(
                        'departureZoneId' => $costRange->getDepartureZoneId(),
                        'arrivalZoneId' => $costRange->getArrivalZoneId(),
                        'productType'   => $costRange->getProductTypeId()
                    );
                }
            }
        }
        $this->commandItems->sort('PrestationName');
        Session::register('PrestationCommandItemCollection',
            serialize($this->commandItems), 2);
        Session::register('CommandItemLemIds', $this->_commandItemLemIds, 2);
        Session::register('CommandItemACOIds', $this->_commandItemAcoIds, 2);
        Session::register('TransportDetails', $this->_transportDetails, 2);
        return $this->commandItems->getCount();
    }

    // }}}
    // PrestationManager::updateACO() {{{

    /**
     * updateACO
     *
     * @param mixed $invoice
     * @access public
     * @return void
     */
    function updateACO($invoice) {
        if (!($this->acoCollection instanceof Collection)) {
            return;
        }
        // maj des ACO
        $count = $this->acoCollection->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $aco = $this->acoCollection->getItem($i);
            $aco->setPrestationCommandDate(date('Y-m-d H:i:s', time()));
            $updated = false;  // Sert de test plus loin
            $acmCollection = $aco->getActivatedMovementCollection();
            $acmCount = $acmCollection->getCount();
            if ($acmCount > 0) {
                for($j=0; $j<$acmCount; $j++) {
                    $acm = $acmCollection->getItem($j);
                    if ($acm->getState() != ActivatedMovement::ACM_EXECUTE_TOTALEMENT) {
                        $aco->setPrestationFactured(ActivatedChainOperation::FACTURED_PARTIAL);
                        $updated = true;
                        break;
                    }
                }
            }
            if ($updated === false) {
                $aco->setInvoicePrestation($invoice);
                $aco->setPrestationFactured(ActivatedChainOperation::FACTURED_FULL);
            }
            $aco->save();
        }
    }

    // }}}
    // PrestationManager::saveAll() {{{

    /**
     * saveAll
     *
     * @access public
     * @return void
     */
    public function saveAll() {
        require_once('InvoiceItemTools.php');
        // Champs numeriques a traiter avant de les sauver
        $numFields = array('Port', 'Emballage', 'Assurance', 'Instalment', 'InstalmentModality',
            'GlobalHanding', 'TotalHT', 'TotalTTC', 'ToPay');
        foreach($numFields as $field) {
            $$field = isset($_REQUEST[$field]) ?
                I18N::extractNumber($_REQUEST[$field]) : 0;
        }

        $customer = Object::load('Actor', $this->_customerId);
        $currency = $customer->getCurrency();

        // Creation d'un SupplierCustomer si besoin {{{
        if (Tools::isEmptyObject($this->supplierCustomer)) {
            $this->supplierCustomer = Object::load('SupplierCustomer');
            $this->supplierCustomer->setCustomer($this->_customerId);
            $this->supplierCustomer->setSupplier($this->_actorId);
            $this->supplierCustomer->setUpdateIncur(0);
        }
        $this->supplierCustomer->setUpdateIncur(
            $this->supplierCustomer->getUpdateIncur() + $TotalTTC);
        $this->supplierCustomer->save();
        // }}}
        // Creation de la commande {{{
        $command = new PrestationCommand();
        $cmdNo = empty($_REQUEST['cmdNumber']) ?
            $command->generateId() : $_REQUEST['cmdNumber'];
        $command->setCommandNo($cmdNo);
        $command->setCommandDate(date('Y-m-d H:i:s', time()));
        $command->setWishedStartDate($_REQUEST['StartDate']);
        $command->setExpeditor($this->_actorId);
        $command->setDestinator($this->_customerId);
        $command->setExpeditorSite($_REQUEST['SupplierSite']);
        $command->setDestinatorSite($_REQUEST['CustomerSite']);
        $command->setCustomer($this->_customerId);
        $command->setCommercial($_REQUEST['Commercial']);
        $command->setState(Command::FACT_COMPLETE);
        $command->setPort($Port);
        $command->setPacking($Emballage);
        $command->setInsurance($Assurance);
        $command->setHanding($GlobalHanding);
        $command->setTotalPriceHT($TotalHT);
        $command->setTotalPriceTTC($TotalTTC);
        $command->setCustomerRemExcep($_REQUEST['RemExcep']);
        $spc = findSupplierCustomer(
            $command->getExpeditor(),
            $command->getDestinator(),
            ($TotalTTC - $TotalHT > 0)
        );

        $command->setSupplierCustomer($spc);
        $command->setCurrency($currency);
        $command->setComment(stripslashes($_REQUEST['cmdComment']));
        $command->setType(Command::TYPE_PRESTATION);
        $command->setCommandItemCollection($this->commandItems);
        $command->save();

        // }}}
        // Creation d'un Instalment (Accompte) si besoin {{{
        if($Instalment > 0) { // On doit creer un nouvel accompte
	        $newInstalment = Object::load('Instalment');
            $InstalmentMapper = Mapper::singleton('Instalment');
            $InstalmentId = $InstalmentMapper->generateId();
            $newInstalment->setId($InstalmentId);
            $DocumentNo = GenerateDocumentNo('IN', 'Instalment', $InstalmentId);
            $newInstalment->setDocumentNo($DocumentNo);
            $newInstalment->setCommand($command);
            $newInstalment->setSupplierCustomer($spc);
            $newInstalment->setInstalmentDate(date('Y-m-d H:i:s'));
            $newInstalment->setModality($InstallmentModality);
            $newInstalment->setInstalment($Instalment);
            $newInstalment->setCurrency($currency);
            $newInstalment->save();
        }
        // }}}
        // Création de la facture {{{
        $invoice = Object::load('Invoice');
        $invoice->generateId();
        $docNo = empty($_REQUEST['invoiceDocumentNo']) ?
            generateDocumentNo('FP', 'AbstractDocument', $invoice->getId()) :
            $_REQUEST['invoiceDocumentNo'];
        $invoice->setDocumentNo($docNo);
        $invoice->setEditionDate($command->getWishedStartDate());
        $invoice->setCommand($command);
        $invoice->setCommandType($command->getInvoiceCommandType());
        $invoice->setSupplierCustomer($spc);
        $invoice->setCurrency($currency);
        $documentModel = $invoice->findDocumentModel();
	    if (!(false == $documentModel)) {
	        $invoice->setDocumentModel($documentModel);
	    }
        $invoice->setPort($Port);
        $invoice->setPacking($Emballage);
        $invoice->setInsurance($Assurance);
        $invoice->setGlobalHanding($GlobalHanding);
        $invoice->setTotalPriceHT($TotalHT);
        $invoice->setTotalPriceTTC($TotalTTC);
	    $invoice->setTvaSurtaxRate($_REQUEST['tvaSurtaxRate']);  // deja formate
	    $invoice->setFodecTaxRate($_REQUEST['fodecTaxRate']);    // deja formate
	    $invoice->setTaxStamp($_REQUEST['taxStamp']);            // deja formate
        $invoice->setToPay($ToPay);
        $invoice->setAccountingTypeActor($command->getDestinatorId());
        $invoice->setComment(stripslashes($_REQUEST['cmdComment']));
        savePaymentDate($invoice, $command);
        $invoice->save();
        // }}}
        $count = $this->commandItems->getCount();
        for ($i = 0;$i < $count; $i++) {
            // Sauvegardes des CommandItems et création des InvoiceItems {{{
            $cmdItem = $this->commandItems->getItem($i);
            if (!($cmdItem instanceof PrestationCommandItem)) {
                continue;
            }
            $cmdItem->setCommand($command);
            $cmdItem->setHanding($_REQUEST['Handing'][$i]);
            $cmdItem->setQuantity($_REQUEST['qty'][$i]);
            $cmdItem->setPriceHT($_REQUEST['UnitPriceHT'][$i]);
            $cmdItem->setPriceHT($_REQUEST['PriceHT'][$i]);
            $cmdItem->save();

            $prestation = $cmdItem->getPrestation();
            $prestationCustomer = Object::load('PrestationCustomer', array(
                'Prestation'=>$prestation->getId(),
                'Actor'=>$this->_customerId));
            $invItem = Object::load('InvoiceItem');
            $invItem->setName($prestationCustomer->toString());
            $invItem->setHanding($_REQUEST['Handing'][$i]);
            $invItem->setQuantity($_REQUEST['qty'][$i]);
            $invItem->setUnitPriceHT($_REQUEST['PriceHT'][$i]);
            $invItem->setTVA($cmdItem->getTVA());
            $invItem->setPrestation($prestation);
            $invItem->setPrestationCost($_REQUEST['UnitPriceHT'][$i]);
            $invItem->setPrestationPeriodicity($prestation->getPeriodicity());
            $invItem->setQuantityForPrestationCost($cmdItem->getQuantityForPrestationCost());
            $invItem->setCostType($cmdItem->getCostType());
            $invItem->setInvoice($invoice);
            // sauegarde de l'aco et du productType pour le détails du transport
            if(isset($this->_transportDetails[$cmdItem->getId()])) {
                $invItem->setProductType($this->_transportDetails[$cmdItem->getId()]['productType']);
            }
            
            if(isset($this->_commandItemAcoIds[$cmdItem->getId()])) {
                $invItem->setActivatedChainOperationFacturedCollectionIds($this->_commandItemAcoIds[$cmdItem->getId()]);
            }
            $invItem->save();
            // }}}
            // màj des lem {{{
            if(isset($this->_commandItemLemIds[$cmdItem->getId()])) {
                $lemCol = $this->_commandItemLemIds[$cmdItem->getId()];
                $jcount=count($lemCol);
                $mapperLem = Mapper::singleton('LocationExecutedMovement');
                foreach($lemCol as $key=>$lemId) {
                    $lem = $mapperLem->load(array('Id'=>$lemId));
                    $lem->setInvoiceItem($invItem->getId());
                    $lem->setPrestationFactured(1);
                    $lem->save();
                    unset($lem);
                }
            }
            // }}}
            // On affecte OccupiedLocation.InvoiceItem {{{
            if ($prestation->getType() == Prestation::PRESTATION_TYPE_STOCKAGE) {
                if (!Tools::isEmptyObject($this->olCollection)) {
                    $jcount = $this->olCollection->getCount();
                    for($j = 0; $j < $jcount; $j++){
                        $item = $this->olCollection->getItem($j);
                        $item->setInvoiceItem($invItem);
                        $item->save();
                    }
                }
            }
            unset($invItem, $cmdItem);
            // }}}
        }
        $this->updateBox($invoice);
        $this->updateTransportLEM($invoice);
        $this->updateACO($invoice);
        //retourne l'id de l'invoice pour impression de la facture
//exit;
        return $invoice->getId();
    }

    // }}}
    // PrestationManager::cleanSession() {{{

    /**
     * Clean les donnees en sessions utiles a la facturation
     *
     * @param mixed $preserve array des noms de vars en session a ne pas suppr.
     *
     * @static
     * @return void
     */
    public static function cleanSession($preserve=array()) {
        $sessionVarNames = array('ACOCollection', 'LEMCollection',
            'OLCollection', 'PrestationCommandItemCollection');
        foreach($sessionVarNames as $name) {
            if(in_array($name, $preserve)) {
                continue;
            }
            if(isset($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
        }
    }

    // }}}
    // PrestationManager::getCostRange() {{{

    /**
     * Retourne le CostRange à utiliser pour la prestation $entity en fonction des
     * filtres.
     *
     * @param Object $entity Prestation ou XXXPrestationCost.
     * @param Object FilterComponent array $zoneFilters tableau de filtre des zone,
     * par ordre de priorité. (cf getZonesFilters)
     * @param Object FilterComponent $baseFilter Filtre construit en fonction du
     * CostType, du poids et du volume.
     * @return Object CostRange ou false
     */
    static function getCostRange($entity, $zoneFilters, $baseFilter, 
        $storeId=0, $productType=0, $unitTypeId=0)
    {
        $thirdPartFilters = array();
        $thirdPartFilters[] =  new FilterComponent(
            new FilterRule('Store', FilterRule::OPERATOR_EQUALS, $storeId),
            new FilterRule('ProductType', FilterRule::OPERATOR_EQUALS, $productType),
            new FilterRule('UnitType', FilterRule::OPERATOR_EQUALS, $unitTypeId),
            FilterComponent::OPERATOR_AND);
        if ($unitTypeId != 0) {
            $thirdPartFilters[] =  new FilterComponent(
                new FilterRule('Store', FilterRule::OPERATOR_EQUALS, $storeId),
                new FilterRule('ProductType', FilterRule::OPERATOR_EQUALS, $productType),
                new FilterRule('UnitType', FilterRule::OPERATOR_EQUALS, 0),
                FilterComponent::OPERATOR_AND);
        }
        if ($productType != 0) {
            $thirdPartFilters[] =  new FilterComponent(
                new FilterRule('Store', FilterRule::OPERATOR_EQUALS, $storeId),
                new FilterRule('ProductType', FilterRule::OPERATOR_EQUALS, 0),
                new FilterRule('UnitType', FilterRule::OPERATOR_EQUALS, $unitTypeId),
                FilterComponent::OPERATOR_AND);
        }
        if ($productType * $unitTypeId != 0) {
            $thirdPartFilters[] =  new FilterComponent(
                new FilterRule('Store', FilterRule::OPERATOR_EQUALS, $storeId),
                new FilterRule('ProductType', FilterRule::OPERATOR_EQUALS, 0),
                new FilterRule('UnitType', FilterRule::OPERATOR_EQUALS, 0),
                FilterComponent::OPERATOR_AND);
        }
        if ($storeId != 0) {
            $thirdPartFilters[] =  new FilterComponent(
                new FilterRule('Store', FilterRule::OPERATOR_EQUALS, 0),
                new FilterRule('ProductType', FilterRule::OPERATOR_EQUALS, $productType),
                new FilterRule('UnitType', FilterRule::OPERATOR_EQUALS, $unitTypeId),
                FilterComponent::OPERATOR_AND);
        }
        if ($storeId * $unitTypeId != 0) {
            $thirdPartFilters[] =  new FilterComponent(
                new FilterRule('Store', FilterRule::OPERATOR_EQUALS, 0),
                new FilterRule('ProductType', FilterRule::OPERATOR_EQUALS, $productType),
                new FilterRule('UnitType', FilterRule::OPERATOR_EQUALS, 0),
                FilterComponent::OPERATOR_AND);
        }
        if ($storeId * $productType != 0) {
            $thirdPartFilters[] =  new FilterComponent(
                new FilterRule('Store', FilterRule::OPERATOR_EQUALS, 0),
                new FilterRule('ProductType', FilterRule::OPERATOR_EQUALS, 0),
                new FilterRule('UnitType', FilterRule::OPERATOR_EQUALS, $unitTypeId),
                FilterComponent::OPERATOR_AND);
        }
        if($storeId * $productType * $unitTypeId != 0) {
            $thirdPartFilters[] =  new FilterComponent(
                new FilterRule('Store', FilterRule::OPERATOR_EQUALS, 0),
                new FilterRule('ProductType', FilterRule::OPERATOR_EQUALS, 0),
                new FilterRule('UnitType', FilterRule::OPERATOR_EQUALS, 0),
                FilterComponent::OPERATOR_AND);
        }

        $filterComponents = array(
                $baseFilter, 
                SearchTools::filterAssembler($thirdPartFilters, 'OR'), 
                SearchTools::filterAssembler($zoneFilters, 'OR')
        );
        $filter = SearchTools::filterAssembler($filterComponents);
        $costRangeCol = $entity->getCostRangeCollection($filter);
        // On retourne le 1er trouvé (tout ça pour ça !!)
        if($costRangeCol->getCount() > 0) {
            return $costRangeCol->getItem(0);
        }

        return false;
    }

    // }}}
    // PrestationManager::getZonesFilters() {{{

    /**
     * Retourne un tableau de FilterComponent pour la recherche des CostRange.
     *
     * @param int $departureZoneId id de la zone de départ
     * @param int $arrivalZoneId id de la zone d'arrivée
     * @return array
     */
    static function getZonesFilters($departureZoneId=0, $arrivalZoneId=0) {
        // construction des filtres sur les zones
        $filtersZone = array();
        $filtersZone[] = new FilterComponent(
            new FilterRule(
                'DepartureZone',
                FilterRule::OPERATOR_EQUALS,
                $departureZoneId),
            new FilterRule(
                'ArrivalZone',
                FilterRule::OPERATOR_EQUALS,
                $arrivalZoneId),
            FilterComponent::OPERATOR_AND);
        // si nécéssaire on ajoute la possibilité d'avoir un costRange
        // avec juste la zone de depart ou d'arrivé définie
        if($departureZoneId && $arrivalZoneId) {
            $filtersZone[] = new FilterComponent(
                new FilterRule(
                    'DepartureZone',
                    FilterRule::OPERATOR_EQUALS,
                    $departureZoneId),
                new FilterRule(
                    'ArrivalZone',
                    FilterRule::OPERATOR_EQUALS,
                    0),
                FilterComponent::OPERATOR_AND);
            $filtersZone[] = new FilterComponent(
                new FilterRule(
                    'DepartureZone',
                    FilterRule::OPERATOR_EQUALS,
                    0),
                new FilterRule(
                    'ArrivalZone',
                    FilterRule::OPERATOR_EQUALS,
                    $arrivalZoneId),
                FilterComponent::OPERATOR_AND);
        }
        // si nécéssaire on ajoute la possibilité d'avoir un costRange
        // sans zones définies
        if($departureZoneId || $arrivalZoneId) {
            $filtersZone[] = new FilterComponent(
                new FilterRule(
                    'DepartureZone',
                    FilterRule::OPERATOR_EQUALS,
                    0),
                new FilterRule(
                    'ArrivalZone',
                    FilterRule::OPERATOR_EQUALS,
                    0),
                FilterComponent::OPERATOR_AND);
        }

        return $filtersZone;
    }

    // }}}
    // PrestationManager::buildFilterForCostType() {{{

    /**
     * Construit le filtre pour la recherche du CostRange en fonction du CostType,
     * du poids et du volume.
     *
     * @param int $costType CostType
     * @param float $weight le poids
     * @param float $volume le volume
     */
    static function buildFilterForCostType($costType, $weight=0, $volume=0, $qty=0, $unitType=0) {
        $costTypesSimple = array(CostRange::TYPE_FIXED, CostRange::TYPE_HOUR, 
            CostRange::TYPE_UNIT, CostRange::TYPE_FIXED_BY_LOCATION, 
            CostRange::TYPE_UNIT_FOR_QUANTITY);
        if(in_array($costType, $costTypesSimple)) {
            return new FilterComponent(
                new FilterRule('CostType', FilterRule::OPERATOR_EQUALS, $costType));//,
                //new FilterRule('UnitType', FilterRule::OPERATOR_EQUALS, $unitType),
                //FilterComponent::OPERATOR_AND);
        }
        $costTypesRange = array(
            CostRange::TYPE_AMOUNT, 
            CostRange::TYPE_FIXED_QUANTITY);
        if(in_array($costType, $costTypesRange)) {
            return new FilterComponent(
                new FilterRule(
                    'CostType',
                    FilterRule::OPERATOR_EQUALS,
                    $costType),
                new FilterRule(
                    'BeginRange',
                    FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                    $qty),
                new FilterRule(
                    'EndRange',
                    FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                    $qty),
                //new FilterRule('UnitType', FilterRule::OPERATOR_EQUALS, $unitType),
                FilterComponent::OPERATOR_AND);
        }

        $costTypesWeight = array(CostRange::TYPE_HOUR_WEIGHT_RANGE,
            CostRange::TYPE_AMOUNT_WEIGHT_RANGE, CostRange::TYPE_FIXED_WEIGHT_RANGE,
            CostRange::TYPE_UNIT_BY_RANGE_10, CostRange::TYPE_UNIT_BY_RANGE_100);
        if(in_array($costType, $costTypesWeight)) {
            return new FilterComponent(
                new FilterRule(
                    'CostType',
                    FilterRule::OPERATOR_EQUALS,
                    $costType),
                new FilterRule(
                    'BeginRange',
                    FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                    $weight),
                new FilterRule(
                    'EndRange',
                    FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                    $weight),
                FilterComponent::OPERATOR_AND);
        }

        $costTypesVolume = array(CostRange::TYPE_HOUR_VOLUME_RANGE,
            CostRange::TYPE_AMOUNT_VOLUME_RANGE, CostRange::TYPE_FIXED_VOLUME_RANGE);
        if(in_array($costType, $costTypesVolume)) {
            return new FilterComponent(
                new FilterRule(
                    'CostType',
                    FilterRule::OPERATOR_EQUALS,
                    $costType),
                new FilterRule(
                    'BeginRange',
                    FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                    $volume),
                new FilterRule(
                    'EndRange',
                    FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                    $volume),
                FilterComponent::OPERATOR_AND);
        }
    }

    // }}}
    // PrestationManager::deleteCommandItems() {{{

    /**
     * Supprime les commandItems sélectionnés.
     *
     * @access public
     * @return void
     */
    public function deleteCommandItems() {
        if (isset($_REQUEST['suppr_x']) && isset($_REQUEST['gridItems'])) {
            foreach ($_REQUEST['gridItems'] as $prestationcmdItemId) {
            	$this->commandItems->removeItemById($prestationcmdItemId);
            }
            Session::register('PrestationCommandItemCollection',
                serialize($this->commandItems), 1);
        }
    }

    // }}}
    // PrestationManager::log() {{{

    /**
     * log
     *
     * @param mixed $data
     * @param mixed $level
     * @access protected
     * @return void
     */
    protected function log($data, $level=PEAR_LOG_NOTICE){
        if(DEBUG) {
            $this->_logger->log('PrestationManager: ' . $data, $level);
        }
    }

    // }}}
    // PrestationManager::calculChainCommandCost() {{{

    /**
     * calculChainCommandCost
     *
     * @param mixed $chainCommand
     * @param mixed $activatedChain
     * @static
     * @access public
     * @return void
     */
    public function calculChainCommandCost($chainCommand) {
        $this->log('calculChainCommandCost');
        $invoiceItemCol = new Collection('InvoiceItem');
        $detail = array();
        // calcul de la volumétrie via les commandItems
        $params = array();
        $commandItemCol = $chainCommand->getCommandItemCollection();
        foreach($commandItemCol as $cmi) {
            $productTypeId = $cmi->getProductTypeId();
            $coverType = $cmi->getCoverType();
            $unitTypeId = $coverType->getUnitTypeId();
            $qty = $cmi->getQuantity();
            $weight = $cmi->getWeight();
            $volume = $cmi->getLength() * $cmi->getWidth() * $cmi->getHeight();
            $params['producttypes'][] = array(
                'id' => $productTypeId,
                'unitTypeId' => $unitTypeId,
                'qty' => $qty,
                'weight' => $weight * $qty,
                'volume' => $volume * $qty);
        }
        $initialParams = $params;

        $acoCol = $this->findACOForCommand($chainCommand);
        foreach($acoCol as $aco) {
            $toInvoice = $initialParams;
            if(IsTransportOperation($aco)) {
                $result = $this->_getParamsForTransportACO($aco, $chainCommand->getId());
                if(is_array($result)) {
                    $toInvoice = $result;
                }
            }

            $this->log('ACO facture: ' . $aco->getId());
            $globalParams = array(
                'time'            => $aco->getACODuration() / 3600,
                'departureZoneId' => Tools::getValueFromMacro($aco,
                    '%FirstTask.ActorSiteTransition.DepartureZone.Id%'),
                'arrivalZoneId'   => Tools::getValueFromMacro($aco,
                    '%LastTask.ActorSiteTransition.ArrivalZone.Id%'));
            $this->log('params pour ACO (time, depZone, arrZone): '. implode(',', $globalParams));
            $filter = array(
                SearchTools::newFilterComponent('Actor',
                    'PrestationCustomer().Actor', 'Equals',
                    $chainCommand->getCustomerId(), 1, 'Prestation'),
                SearchTools::newFilterComponent('Operation', '', 'Equals',
                    $aco->getOperationId(), 1),
                SearchTools::newFilterComponent('Active', '', 'Equals', 1, 1),
                SearchTools::newFilterComponent('Facturable', '', 'Equals', 1, 1)
            );
            $filter = SearchTools::filterAssembler($filter);
            $prestationCollection = Object::loadCollection('Prestation', $filter);
            $this->log('prestation facturées: ' . implode(',', $prestationCollection->getItemIds()));
            foreach($prestationCollection as $prestation) {
                $tvaCoeff = 1; // TTC = HT * $tvaCoeff
                $tva = $prestation->getTva();
                if($tva instanceof TVA) {
                    $tvaCoeff = 1 + $tva->getRate() / 100;
                }

                $prices = $prestation->getPrestationPrice($globalParams, 
                    isset($toInvoice['concreteproducts']) ? $toInvoice['concreteproducts'] : array(), 
                    isset($toInvoice['flytypes']) ? $toInvoice['flytypes'] : array(), 
                    isset($toInvoice['products']) ? $toInvoice['products'] : array(), 
                    array(),
                    isset($toInvoice['producttypes']) ? $toInvoice['producttypes'] : array(),
                    true);
                foreach ($prices as $costrange=>$price) {
                    // création des invoiceItems qui serviront à détaillé la 
                    // facture pour les commandes de transport
                    $prestationCustomer = Object::load('PrestationCustomer', array(
                        'Prestation' => $prestation->getId(),
                        'Actor'      => $chainCommand->getCustomerId()));
                    $invItem = Object::load('InvoiceItem');
                    $invItem->setName($prestationCustomer->toString());
                    $invItem->setQuantity($price['qty']);
                    $invItem->setUnitPriceHT($price['priceHT']);
                    $invItem->setTVA($prestation->getTVA());
                    $invItem->setPrestation($prestation);
                    $invItem->setPrestationCost($price['unitPriceHT']);
                    $invoiceItemCol->setItem($invItem);
                    // calcul du total
                    if(!isset($detail[$prestation->getId()])) {
                        $detail[$prestation->getId()] = array(
                            'totalttc'=>0, 'totalht'=>0);
                    }
                    $detail[$prestation->getId()]['totalht'] += $price['priceHT'];
                    $detail[$prestation->getId()]['totalttc'] += $price['priceHT']*$tvaCoeff;
                }
            }
        }
        Session::register('ChainCommandInvoiceItem', $invoiceItemCol);
        return $detail;
    }

    // }}}
    // PrestationManager::findACOForCommand() {{{

    /**
     * findACOForCommand
     *
     * @param mixed $command
     * @access public
     * @return void
     */
    public function findACOForCommand($command) {
        $this->_customerId = $command->getCustomerId();
        $this->findPrestations();
        $filter = array(
        SearchTools::newFilterComponent('PrestationFactured', '',
                'NotEquals', ActivatedChainOperation::FACTURED_FULL, 1),
            SearchTools::newFilterComponent('Operation', '', 'In',
                $this->_operationIds, 1),
            SearchTools::newFilterComponent('Command',
                'ActivatedChain.CommandItem().Command', 'Equals',
                $command->getId(), 1, 'ActivatedChainOperation'),
        );
        $filter = SearchTools::filterAssembler($filter);
        $acoCol = Object::loadCollection('ActivatedChainOperation', $filter);
        return $acoCol;
    }

    // }}}
    // PrestationManager::updateBox() {{{

    /**
     * updateBox
     *
     * Met à jour Box::prestationFactured() des Box::$_boxId, il faut appeler
     * cette méthode au moment de la sauvegarde de la facture.
     *
     * @access public
     * @return void
     */
    public function updateBox($invoice) {
        $mapper = Mapper::singleton('Box');
        foreach($this->_boxIds as $id) {
            $box = $mapper->load(array('Id'=>$id));
            $box->setPrestationFactured(true);
            $box->setInvoicePrestation($invoice);
            $box->save();
        }
    }

    // }}}
    // PrestationManager::updateTransportLEM() {{{

    /**
     * updateTransportLEM
     *
     * Met à jour LEM::TransportPrestationFactured des LEM ayant servi à
     * facturer une ACO de transport.
     *
     * @access public
     * @return void
     */
    public function updateTransportLEM($invoice) {
        $mapper = Mapper::singleton('LocationExecutedMovement');
        foreach($this->_transportLEMIds as $id) {
            $lem = $mapper->load(array('Id'=>$id));
            $lem->setTransportPrestationFactured(true);
            $lem->setInvoicePrestation($invoice);
            $lem->save();
        }
    }

    // }}}
    // PrestationManager::_getParamsForTransportACO() {{{

    /**
     * _getParamsForTransportACO 
     * 
     * @param mixed $aco 
     * @param mixed $commandId 
     * @param mixed $force 
     * @access private
     * @return void
     */
    private function _getParamsForTransportACO($aco, $commandId, $force=false) {
        $this->log('_getParamsForTransportACO('.$aco->getId().', '.$commandId.')');
        if(!(Object::load('ChainCommand', $commandId) instanceof ChainCommand) && !$force) {
            return false;
        }
        
        // Récupère la dernière ack de l'aco puis remonte jusque la dernière 
        // ack de transport
        $ackTransport = $aco->getLastTask();
        if(!($ackTransport instanceof ActivatedChainTask)) {
            return false;
        }
        if(!IsTransportTask($ackTransport->GetTask())) {
            $ackTransport = $ackTransport->GetPreviousTask(array(), 'isTransportTask');
            if(!($ackTransport instanceof ActivatedChainTask)) {
                return false;
            }
        }
        
        // cas particulier d'une aco de transport
        // quand le calcul est basé sur le réel
        if(Preferences::get('ChainCommandBillingBehaviour')) {//} &&
        //(Object::load('ChainCommand',$commandId) instanceof ChainCommand)) {
            $duration = $aco->getACODuration() / 3600;
            $departureZoneId = Tools::getValueFromMacro($aco,
                '%FirstTask.ActorSiteTransition.DepartureSite.Zone.Id%');
            $arrivalZoneId = Tools::getValueFromMacro($aco,
                '%LastTask.ActorSiteTransition.ArrivalSite.Zone.Id%');
                        
            $ach = $aco->getActivatedChain();
            // cherche une tache de regroupement ou de sortie de stock avant le transport
            if($ack = $ackTransport->GetPreviousTask(array(), 'isGroupingOrStockExitTask')) {
                if(IsGroupingTask($ack->getTask())) {
                    $ackRegroupement = $ack; // to clarify
                    // la tache de regroupement est la plus près de celle du
                    // transport
                    if(ActivatedChainTask::STATE_TODO==$ackRegroupement->getState()) {
                        $this->log('on ne peut pas facturer, les regroupements sont à faire, cmdId: ' . $commandId);
                        return array();
                    }
                    // on récupère les parentBox liées à l'ack qui ne sont
                    // pas facturées
                    $filter = array(
                        SearchTools::newFilterComponent('ActivatedChainTask', 'ActivatedChainTask().Id',
                            'Equals', $ackRegroupement->getId(), 1, 'Box'),
                        SearchTools::newFilterComponent('PrestationFactured', '', 'Equals', 0, 1));
                    $filter = SearchTools::filterAssembler($filter);
                    $boxCol = Object::loadCollection('Box', $filter);
                    // on calculs les paramètres de la prestation, qty,
                    // poids et volume pour cahque product ou productType si
                    // pas de product associé au commandItem de la
                    // prestation 
                    $params = array();
                    foreach($boxCol as $box) {
                        if($box->getPrestationFactured()) {
                            continue;
                        }
                        $coverType = $box->getCoverType();
                        $unitTypeId = $coverType->getUnitTypeId();
                        $this->_boxIds[] = $box->getId();
                        $childBoxCollection = $box->getChildrenBoxes(1);
                        foreach($childBoxCollection as $childBox) {
                            $cmi = $childBox->getCommandItem();
                            if(method_exists($cmi, 'getProduct')) {
                                $pdt = $cmi->getProduct();
                                $pdtId = $pdt->getId();
                                $params['products'][] = array(
                                    'id' => $pdtId,
                                    'unitTypeId' => $unitTypeId,
                                    'productTypeId' => $pdt->getProductTypeId(),
                                    'qty' => 1 / count($childBoxCollection),
                                    'packagingqty' => 1,
                                    'time' => $duration,
                                    'departureZoneId' => $departureZoneId,
                                    'arrivalZoneId' => $arrivalZoneId,
                                    'acoId' => $aco->getId());
                            } else {//}if($cmi instanceof ChainCommandItem) {
                                $pdtTypeId = $cmi->getProductTypeId();
                                // xxx ne pas sommer
                                $params['producttypes'][] = array(
                                    'id' => $pdtTypeId,
                                    'unitTypeId' => $unitTypeId,
                                    'qty' => 1 / count($childBoxCollection),
                                    'packagingqty' => 1/count($childBoxCollection),
                                    'weight' => 0,
                                    'volume' => 0,
                                    'time' => $duration,
                                    'departureZoneId' => $departureZoneId,
                                    'arrivalZoneId' => $arrivalZoneId,
                                    'acoId' => $aco->getId());
                            }
                        }
                    }
                    $this->log('facturation des box: ' . implode(',', $this->_boxIds));
                    // on met les box en session pour ensuite mettre à jour
                    // Box::$prestationFactured si la facture est sauvegardée
                    $_SESSION['facturedBoxIds'] = $this->_boxIds;
                    return $params;
                } 
                elseif(getTaskId($ack) == TASK_STOCK_EXIT) {
                    $command = Object::load('Command', $commandId);
                    if($command instanceof ChainCommand) {
                        // on ne crée pas de mouvement pour les chaînes de
                        // transport
                        return false;
                    }
                    // la tache de sortie de stock est la plus prés de celle du
                    // transport
                    $ackStockExit = $ack; // to clarify
                    // on récupère les lem associès à l'ack
                    $filter = array(
                        SearchTools::newFilterComponent('LEM',
                            'ExecutedMovement.ActivatedMovement.ActivatedChainTask.Id',
                            'Equals', $ackStockExit->getId(), 1),
                        SearchTools::newFilterComponent('TransportPrestationFactured', '',
                            'Equals', 0, 1)
                        );
                    $filter = SearchTools::filterAssembler($filter);
                    $lemCol = Object::loadCollection('LocationExecutedMovement', $filter);
                    $params = array();
$totalQty = $totalPackagingQty = 0;
                    foreach($lemCol as $lem) {
                        $this->_transportLEMIds[] = $lem->getId();
                        $pdt = $lem->getProduct();
                        $unitTypeId = $pdt->getSellUnitTypeId();
                        $pdtID = $pdt->getId();
                        $storeId = Tools::getValueFromMacro($lem, '%Location.Store.Id%');
                        $totalQty += $lem->getQuantity();
                        $totalPackagingQty += $pdt->packagingUnitNumber($lem->getQuantity());
                    }
// PATCH HORRIBLE ET FAUXXX QUI POURRAIT CORRIGER LE BUG 0003772 
// MAIS DOIT AVOIR DES IMPACTS DESASTREUX DANS LES AUTRES CAS DE PARAMETRAGE
        // On ne prend que le dernier (faux!!) mais pour avoir le cumul des qtes...
        if ($lemCol->getCount() > 0) {
            $params['products'][] = array(
                'id' => $pdtID,
                'storeId' => $storeId,
                'unitTypeId' => $unitTypeId,
                'productTypeId' => $pdt->getProductTypeId(),
                'qty' => $totalQty,
                'packagingqty' => $totalPackagingQty,
                'weight' => $lem->getWeight(),
                'volume' => $lem->getVolume(),
                'lemIds' => array(), // pas utilise dans ce cas
                'time' => $duration,
                'departureZoneId' => $departureZoneId,
                'arrivalZoneId' => $arrivalZoneId,
                'acoId' => $aco->getId()
            );
        }
        
                    $this->_transportLEMIds = array_unique($this->_transportLEMIds);
                    $_SESSION['transportLEMIds'] = $this->_transportLEMIds;
                    $this->log('lem utilisés: ' . implode(',', $this->_transportLEMIds));
                    return $params;
                }
            }
        }
        // Meme si la pref de facturation n'est pas au reel, on remplit
        $ack = $ackTransport->getPreviousTask(array(), 'isGroupingOrStockExitTask');
        if($ack !== false && getTaskId($ack) == TASK_STOCK_EXIT) {
            $ackStockExitId = $ack->getId();
            /* Duplication du code au dessus... */
            $filter = array(
                SearchTools::newFilterComponent('LEM',
                    'ExecutedMovement.ActivatedMovement.ActivatedChainTask.Id',
                    'Equals', $ackStockExitId, 1),
                SearchTools::newFilterComponent('TransportPrestationFactured', '',
                    'Equals', 0, 1)
                );
            $filter = SearchTools::filterAssembler($filter);
            $lemCol = Object::loadCollection('LocationExecutedMovement', $filter);
            /* fin dupplication */
            $this->_transportLEMIds = array_merge($this->_transportLEMIds, $lemCol->getItemIds());
        }
        return false;
    }

    // }}}
    // getChainCommands() {{{

    /**
     * Returns all chaincommand instances used in the prestation.
     *
     * @return array
     */
    public function getChainCommands()
    {
        $chainCommands = array();
        foreach($this->acoCollection as $aco) {
            try {
                $cmi = $aco->getActivatedChain()->getCommandItemCollection(array('Command.Type' => Command::TYPE_TRANSPORT))->getItem(0);
                if (!$cmi instanceof CommandItem) {
                    continue;
                }
                $cmd = $cmi->getCommand();
            } catch (Exception $exc) {
                continue;
            }
            if (!isset($chainCommands[$cmd->getId()])) {
                $chainCommands[$cmd->getId()] = $cmd;
            }
        }    
        return array_values($chainCommands);
    }
    
    // }}}
}

?>
