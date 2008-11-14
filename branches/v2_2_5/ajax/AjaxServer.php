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

class OnlogisticsAjaxServer extends AjaxServer {

    // Constructor {{{

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct() {
        parent::__construct();
        // pour rendre une méthode ajoutée dispo, faire:
        // $this->registeredMethods[] = 'nomMethode';
        $this->registeredMethods[] = 'zoneaddedit_getCollection';
        $this->registeredMethods[] = 'stockAccountingExport_getCollection';
        $this->registeredMethods[] = 'job_getActorCollection';
        $this->registeredMethods[] = 'zone_getActors';
        $this->registeredMethods[] = 'flowAddEdit_flowTypeItems';
        $this->registeredMethods[] = 'spreadsheetaddedit_getPropertyTypeList';
        $this->registeredMethods[] = 'chainaddedit_getSiteCollection';
        $this->registeredMethods[] = 'chaincommand_calculateprice';
        $this->registeredMethods[] = 'siteaddedit_sitenameIsOk';
        $this->registeredMethods[] = 'questionSelect_getQuestions';
        $this->registeredMethods[] = 'forwardingForm_getLocations';
        $this->registeredMethods[] = 'actoraddedit_checkActors';
        $this->registeredMethods[] = 'getStoresForStorageSite';
        $this->registeredMethods[] = 'toHaveAdd_updateTotalHT';
        $this->registeredMethods[] = 'productCommand_getSitePlanningAndUnavailabilities';
        $this->registeredMethods[] = 'productCommand_getSelectedExpeditorSite';
        $this->registeredMethods[] = 'productCommand_getSelectedDestinatorSite';
    }
    // }}}

    // mettre les méthodes additionnelles ici, sans oublier de les inscrire
    // dans le registre, cf commentaire dans le constructeur ci-dessus

    // zoneaddedit_getCollection() {{{

    /**
     * Remplit les options d'un select de CountryCity ou Site
     *
     * @access public
     */
    public function zoneaddedit_getCollection($entity, $filter=array()) {
        $return = array();
        $filterCmptArray = array();
        if ($entity == 'Site') {
            $filterCmptArray[] = SearchTools::NewFilterComponent(
                    'Zone', '', 'Equals', 0, 1);
            foreach($filter as $path => $value) {
                $value = is_array($value)?$value:array($value);
                $filterCmptArray[] = SearchTools::NewFilterComponent(
                        $path, $path, 'In', $value, 1);
            }
            $filter = SearchTools::filterAssembler($filterCmptArray);
            $array = SearchTools::createArrayIDFromCollection($entity, $filter);
            foreach($array as $id => $toString){
                $return[] = array($id, utf8_encode($toString));
            }
        }
        // Traitement special, pour les perfs
        else {
            require_once('SQLRequest.php');
            $paths = array_keys($filter);
            // Pas actif sur Country, car depassement de memoire lie a json_encode()
            $country = ($paths[0] == 'Country')?$filter['Country']:0;
            $department = ($paths[0] == 'Department')?$filter['Department']:0;
            $state = ($paths[0] == 'State')?$filter['State']:0;
            $return = request_CountryCityForSelect($country, $state, $department);
        }

        return json_encode($return);
    }

    // }}}
    // stockAccountingExport_getCollection() {{{

    /**
     * Remplit les options d'un select de Property en fonction d'un ProductType
     *
     */
    public function stockAccountingExport_getCollection($entity, $filter=array()) {
        $return = array();

        // Relation *..* entre ProductType et Property => on ne peut utiliser
        // $entity et $filter tels quels pour construire une collection
        $productTypeId = $filter['ProductType'];
        $productType = Object::load('ProductType', $filter['ProductType']);

        $propertyIds = $productType->getPropertyCollectionIds();
    	// Idem pour le ProductType generic s'il y en a un pour le ProductType
        // selectionne
        $genericProductType = $productType->getGenericProductType();
        $genericPropertyIds = array();
        if (!Tools::isEmptyObject($genericProductType)) {
            $genericPropertyIds = $genericProductType->getPropertyCollectionIds();
        }
        $filter = array('Id' => array_merge($propertyIds, $genericPropertyIds));
        $array = SearchTools::createArrayIDFromCollection('Property', $filter);
        foreach($array as $id => $toString){
            $return[] = array($id, utf8_encode($toString));
        }
        return json_encode($return);
    }

    // }}}
    // job_getActorCollection() {{{

    /**
     * Remplit les options d'un select d'Actor en fonction d'un Job
     * Si le job est 0, on retourne TOUS les Actors (actifs ou generic)
     */
    public function job_getActorCollection($entity, $filter=array()) {
        $return = array();

        // Relation *..* entre Job et Actor => on ne peut utiliser
        // $entity et $filter tels quels pour construire une collection
        $jobId = $filter['Job'];
        $job = Object::load('Job', $filter['Job']);

        // Filter sur les actor
        $filter = new FilterComponent(
            new FilterComponent(
                new FilterRule(
                    'Active',
                    FilterRule::OPERATOR_EQUALS,
                    1
                )
            ),
            FilterComponent::OPERATOR_OR,
            new FilterComponent(
                new FilterRule(
                    'Generic',
                    FilterRule::OPERATOR_EQUALS,
                    1
                )
            )
        );
        // Récupération des Acteurs
        if ($jobId > 0) {
            $actorIds = $job->getActorCollectionIds($filter, array('Name'), array('Id'));
            $filter = array('Id' => $actorIds);
        }

    	$array = SearchTools::createArrayIDFromCollection('Actor', $filter);
        foreach($array as $id => $toString){
            $return[] = array($id, utf8_encode($toString));
        }
        return json_encode($return);
    }

    // }}}
    // zone_getActors() {{{

    /**
     * Remplit les options d'un select d'Actor en fonction d'une Zone
     * Si la zone est 0, on retourne TOUS les Actors (actifs ou generic)
     */
    public function zone_getActors($entity, $filter=array()) {
        $return = array();//array(array(-2, utf8_encode(_('All sites'))));
        // Relation *..* entre Job et Actor => on ne peut utiliser
        // $entity et $filter tels quels pour construire une collection
        $zoneId = $filter['Zone'];
        $zone = Object::load('Zone', $filter['Zone']);

        // Filter sur les actor
        $filter = new FilterComponent(
            new FilterComponent(
                new FilterRule(
                    'Active',
                    FilterRule::OPERATOR_EQUALS,
                    1
                )
            ),
            FilterComponent::OPERATOR_OR,
            new FilterComponent(
                new FilterRule(
                    'Generic',
                    FilterRule::OPERATOR_EQUALS,
                    1
                )
            )
        );
        // Récupération des Acteurs
        if ($zoneId > 0) {
            $actorColl = $zone->getActorCollection($filter);
            $filter = array('Id' => $actorColl->getItemIds());
        }

    	$array = SearchTools::createArrayIDFromCollection('Actor', $filter);
        foreach($array as $id => $toString){
            $return[] = array($id, utf8_encode($toString));
        }
        return json_encode($return);
    }

    // }}}
    // flowAddEdit_flowTypeItems() {{{

    /**
     * Retourne les infos pour construire le tableau des FlowTypeItem dans
     * FlowAddEdit.php
     * data = array(
     *     0 => FlowTypeItem.Id,
     *     1 => FlowTypeItem.Name,
     *     2 => FlowTypeItem.FlowItem.totalHT,
     *     3 => FlowTypeItem.TVA.Rate,
     *     4 => FlowTypeItem.TVA.Id);
     *
     * @param int $flowTypeId id du FlowType
     * @param int $flowid id du Flow
     * @return json encoded string
     */
    public function flowAddEdit_flowTypeItems($flowTypeId, $flowId) {
        $flowType = Object::load('FlowType', $flowTypeId);
        $count = 0;
        if($flowType instanceof FlowType) {
            $flowTypeItemCol = $flowType->getFlowTypeItemCollection();
            $count = $flowTypeItemCol->getCount();
        }
        $flowItemMapper = Mapper::singleton('FlowItem');

        $result = array();
        for ($i=0 ; $i<$count ; $i++) {
            $flowTypeItem = $flowTypeItemCol->getItem($i);
            $flowItem = $flowItemMapper->load(
                array('Type'=>$flowTypeItem->getId(),
                'Flow'=>$flowId));
            if ($flowItem instanceof FlowItem) {
                $totalHT = $flowItem->getTotalHT();
                $handing = $flowItem->getHanding();
            } else {
                $totalHT = 0;
                $handing = 0;
            }
            $tva = $flowTypeItem->getTVA();
            $tvaRate = $tva instanceof TVA?$tva->getRate():0;
            $tvaID = $tva instanceof TVA?$tva->getId():0;

            $result[] = array(
                $flowTypeItem->getId(),
                utf8_encode($flowTypeItem->getName()),
                $totalHT,
                $tvaRate,
                $tvaID,
                $handing);
        }
        return json_encode($result);
    }

    // }}}
    // spreadsheetaddedit_getPropertyTypeList() {{{

    /**
     * Retourne la liste des propriétés pour un nom de classe donné.
     *
     * @param  mixed $class
     * @param  boolean $withFK
     * @return json encoded string
     */
    public function spreadsheetaddedit_getPropertyTypeList($class, $withFK = true) {
        require_once('SpreadSheetAddEditTools.php');
        $return = array();
        if (is_int($class)) {
            $classObj = Object::load('Entity', $class);
            $class = $classObj->getName();
            if (!($classObj instanceof Entity)) {
                return $this->error('L\'entité Entity avec l\'id '
                    . $class . ' n\'existe pas.', 1);
            }
        }
        if($class != '##') {
            $result = getProperties($class, $withFK);
        }
        return json_encode($result);
    }

    // }}}
    // chainaddedit_getSiteCollection() {{{

    /**
     * Retourne le tableau nécessaire pour les options du select des sites
     *
     * @access public
     */
    public function chainaddedit_getSiteCollection($entity, $filter=array()) {
        $act = Object::load('Actor', $filter['Owner']);
        $return = array();
        if ($act instanceof Actor && $act->getGeneric()) {
            $return[] = array(0, utf8_encode(_('Generic actor site')));
        } else {
            $array = SearchTools::createArrayIDFromCollection('Site', $filter);
            foreach($array as $id => $toString){
                $return[] = array($id, utf8_encode($toString));
            }
        }
        return json_encode($return);
    }

    // }}}
    // chaincommand_calculateprice() {{{

    /**
     * Retourne le tableau nécessaire pour les options du select des sites
     *
     * @access public
     */
    public function chaincommand_calculateprice($formdata, $commitTrans=false)
    {
        //$logger = Tools::loggerFactory();
        $auth = Auth::singleton();
        $session = Session::singleton();
        Database::connection()->startTrans();
        $chainCommand = Object::load('ChainCommand');
        $chainCommand->generateId();
        $chainCommand->setIsEstimate(isset($formdata['isEstimate']));
        $chainCommand->setType(Command::TYPE_TRANSPORT);
        $chainCommand->setCustomer($auth->getActor());
        $chain = Object::load('Chain', $_SESSION['chnId']);
        $chainCommand->setChain($chain);
        $collection = $chainCommand->getCommandItemCollection();
        $collection->reset();
        FormTools::autoHandlePostData($formdata, $chainCommand);
        for ($i=0; $i < count($formdata['ChainCommandItems']); $i++) {
            // mise en mémoire des infos de la commande
            $cmi = new ChainCommandItem();
            $cmi->setCommand($chainCommand);
            FormTools::autoHandlePostData($formdata['ChainCommandItems'][$i], $cmi);
            $collection->setItem($cmi);
        }
        require_once 'CommandManager.php';
        $manager = new CommandManager(array(
            'CommandType'        => 'ChainCommand',
            'ProductCommandType' => false,
            'UseTransaction'     => false,
            'IsEstimate'         => isset($formdata['isEstimate'])
        ));
        $manager->command = $chainCommand;
        // Activation du processus
        $result = $manager->activateProcess(false);
        if ($result instanceof Exception) {
            return json_encode(utf8_encode($result->getMessage()));
        }
        // validation de la commande
        $ht = $formdata['PriceModified']==1?$formdata['ChainCommand_RawHT']:false;
        $result = $manager->validateChainCommand($ht, $commitTrans);
        if ($result instanceof Exception) {
            return json_encode(utf8_encode($result->getMessage()));
        }
        if ($commitTrans) {
            $session->register('chainCommandNo', $chainCommand->getCommandNo(), 1);
            $session->register('chainCommandId', $chainCommand->getId(), 1);
            Database::connection()->completeTrans();
        } else {
            Database::connection()->rollbackTrans();
        }
        return json_encode($result);
    }

    // }}}
    // siteaddedit_sitenameIsOk() {{{

    /**
     * Check si un nom de site saisi est acceptable
     * Controle:
     *  - pas deja un Site avec ce Name en base
     *  - pas deja un Site avec ce Name dans $actor->getSiteCollection() (en session)
     *
     * @access public
     * @param string $siteName
     * @return json encoded string
     */
    public function siteaddedit_sitenameIsOk($siteName)
    {
        //$this->log('Dans siteaddedit_sitenameIsOk()...');
        $siteTest = $_SESSION['actor']->getSiteCollection()->getItemByObjectProperty(
                'Name', $siteName);
        $site = $_SESSION['site'];
        $mapper = Mapper::singleton('Site');
        //$this->log('$siteName: ' . $siteName . ' et Site->getName(): ' . $site->getName());
        if (($siteName != $site->getName())
        && ($mapper->alreadyExists(array('Name' => $siteName))
                || !Tools::isEmptyObject($siteTest))) {
            return json_encode(false);
        }
        else return json_encode(true);
    }

    // }}}
    // questionSelect_getQuestions() {{{

    public function questionSelect_getQuestions($themeId, $categoryId) {
        $filter = array();
        if($themeId!='##' && $themeId!=0) {
            $filter[] = SearchTools::NewFilterComponent('Theme', '', 'Equals', $themeId, 1);
        }
        if($categoryId!='##' && $categoryId!=0) {
            $filter[] = SearchTools::NewFilterComponent('Category', 'Category().Id', 'Equals', $categoryId, 1, 'Question');
        }
        $filter = SearchTools::filterAssembler($filter);
        $array = SearchTools::createArrayIDFromCollection('Question', $filter, _('Select'));
        foreach($array as $id => $toString){
            $return[] = array('id'=>$id, 'toString'=>utf8_encode($toString));
        }
        return json_encode($return);
    }

    // }}}
    // forwardingForm_getLocations() {{{

    public function forwardingForm_getLocations() {
        $return = array();
        $locationCol = Object::loadCollection('Location', array(),
            array('Name' => SORT_ASC));
        $count = $locationCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $location = $locationCol->getItem($i);
            $str = $location->getName() . ' (' .
                Tools::getValueFromMacro($location, '%Store.StorageSite.Name%') .
                ')';
            $return[] = array('id'=>$location->getId(), 'toString'=>utf8_encode($str));
        }
        return json_encode($return);
    }

    // }}}
    // actoraddedit_checkActors() {{{

    /**
     * Check si l'Actor connecte existe dans la base $realm, ainsi qu'un Actor
     * de Name $supplierName.
     *
     * @access public
     * @param string $supplierName
     * @param string $realm
     * @return json encoded string
     */
    public function actoraddedit_checkActors($supplierName, $realm) {
        $auth = Auth::singleton();
        $actorConnectedName = $auth->getActor()->getName();
        // Attention, ne pas modifier dans ol le mdp de ecustomer
        $cli = new XmlRpcClient(
            ONLOGISTICS_API,
            array(
                'user'  => 'ecustomer@' . $realm,
                'passwd' => 'koa6shaU' /* ,'verbose'=>true */
            )
        );
        $result = $cli->order__checkActors($actorConnectedName, $supplierName);
        $result = (is_string($result))?utf8_encode($result):$result;
        return json_encode($result);
    }

    // }}}
    // getStoresForStorageSite {{{
    
    /**
     * getStoresForStorageSite
     *
     * Retourne les magasins qui ont au moins un emplacement actif.
     * 
     * @param mixed $entity 
     * @param mixed $filter 
     * @access public
     * @return void
     */
    public function getStoresForStorageSite($entity, $filter) {
        $storageSite = Object::load('StorageSite', $filter['StorageSite']);
        $storeCol = $storageSite->getStoreCollection(array(), array('Name'=>SORT_ASC));
        $return = array();
        foreach ($storeCol as $store) {
            $locationCol = $store->getLocationCollection();
            $activated = false;
            foreach($locationCol as $location) {
                if($location->getActivated()) {
                    $activated = true;
                }
            }
            if($activated) {
                $return[] = array($store->getId(), $store->toString());
            }
        }
        return json_encode($return);
    }
    // }}}
    // toHaveAdd_updateTotalHT() {{{
    
    /**
     * toHaveAdd_updateTotalHT
     * 
     * @access public
     * @return void
     */
    public function toHaveAdd_updateTotalHT($supId, $custId) {
        $sc = Object::load(
            'SupplierCustomer',
            array('Supplier' => $supId, 'Customer' => $custId)
        );
        if ($sc instanceof SupplierCustomer && $sc->getAnnualTurnoverDiscountPercent()) {
            $return = $sc->getAnnualTurnoverDiscountTotal();
        } else {
            $return = '';
        }
        return json_encode($return);
    }
    // }}}
    // productCommand_getSitePlanningAndUnavailabilities() {{{
    
    /**
     * Methode utilisée pour griser les jours/heures dans le calendrier de la 
     * commande produit en fonction du planning et des indisponibilités du site 
     * du client.
     *
     * Le tableau est de la forme:
     *
     * <code>
     * array(
     *     'planning' => array(
     *         0 => ('08:00:00', '12:00:00', '14:00:00', 17:00:00), // lundi
     *         1 => ('08:00:00', '12:00:00', '14:00:00', 17:00:00), // mardi
     *         2 => ('08:00:00', '00:00:00', '00:00:00', 12:00:00),       // mercredi
     *         3 => ('08:00:00', '12:00:00', '14:00:00', 17:00:00), // jeudi
     *         4 => ('08:00:00', '12:00:00', '14:00:00', 17:00:00), // vendredi
     *         5 => '00:00:00',                               // samedi
     *         6 => '00:00:00'                                // dimanche
     *     ),
     *     'unavailabilities' => array(
     *         array('2007-11-06 08:00:00', '2007-11-06 10:00:00'),
     *         array('2007-11-07 16:00:00', '2007-11-06 17:00:00')
     *     )
     * );
     * </code>
     * 
     * @access public
     * @param  integer the customer site id
     * @return string array encoded in a json string
     */
    public function productCommand_getSitePlanningAndUnavailabilities($siteId)
    {
        $return = array('planning' => array(), 'unavailabilities' => array());
        $site = Object::load('Site', $siteId);
        if (!($site instanceof Site)) {
            return json_encode($return);
        }
        $planning = $site->getPlanning();
        if (!($planning instanceof WeeklyPlanning)) {
            return json_encode($return);
        }
        $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday',
                 'Friday', 'Saturday');
        foreach($days as $dayname) {
            $getter = 'get' . $dayname;
            $day = $planning->$getter();
            $return['planning'][] = array(
                DateTimeTools::timeToTimeStamp($day->getStart()),
                DateTimeTools::timeToTimeStamp($day->getPause()),
                DateTimeTools::timeToTimeStamp($day->getRestart()),
                DateTimeTools::timeToTimeStamp($day->getEnd())
            );
        }
        $ucol = $planning->getUnavailabilityCollection(
            array(),
            array('BeginDate' => SORT_ASC)
        );
        foreach($ucol as $u) {
            $return['unavailabilities'][] = array(
                DateTimeTools::mySQLDateToTimeStamp($u->getBeginDate()),
                DateTimeTools::mySQLDateToTimeStamp($u->getEndDate())
            );
        }
        return json_encode($return);
    }
    // }}}
    // productCommand_getSelectedExpeditorSite() {{{
    
    /**
     * Returns the expeditor site to pre-select.
     *
     * @param integer The expeditor ID
     * @param integer The destinator site ID
     *
     * @access public
     * @return integer ID of the selected site
     */
    public function productCommand_getSelectedExpeditorSite($expId, $destSiteId)
    {
        $expSiteCol = Object::loadCollection('Site', array('Owner' => $expId));
        $exp        = Object::load('Actor', $expId);
        $destSite   = Object::load('Site', $destSiteId);
        if (!($exp instanceof Actor)) {
            return json_encode(0);
        }
        if (!($destSite instanceof Site)) {
            return json_encode($exp->getMainSiteId());
        }
        foreach ($expSiteCol as $expSite) {
            if (($zoneId = $expSite->getZoneId()) == 0) {
                continue;
            }
            if ($zoneId == $destSite->getZoneId()) {
                return json_encode($expSite->getId());
            }
        }
        return json_encode($exp->getMainSiteId());
    }

    // }}}
    // productCommand_getSelectedDestinatorSite() {{{
    
    /**
     * Returns the destinator site to pre-select.
     *
     * @param integer The destinator ID
     * @param integer The expeditor site ID
     *
     * @access public
     * @return integer ID of the selected site
     */
    public function productCommand_getSelectedDestinatorSite($destId, $expSiteId)
    {
        $destSiteCol = Object::loadCollection('Site', array(
            'Owner' => $destId,
            'Type'  => array(
                Site::SITE_TYPE_LIVRAISON,
                Site::SITE_TYPE_FACTURATION_LIVRAISON
            )
        ));
        $dest        = Object::load('Actor', $destId);
        $expSite     = Object::load('Site', $expSiteId);
        if (!($dest instanceof Actor)) {
            return json_encode(0);
        }
        if (!($expSite instanceof Site)) {
            return json_encode($dest->getMainSiteId());
        }
        foreach ($destSiteCol as $destSite) {
            if (($zoneId = $destSite->getZoneId()) == 0) {
                continue;
            }
            if ($zoneId == $expSite->getZoneId()) {
                return json_encode($destSite->getId());
            }
        }
        return json_encode($dest->getMainSiteId());
    }

    // }}}
}

?>
