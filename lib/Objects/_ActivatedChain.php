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

class _ActivatedChain extends Object {
    // class constants {{{

    const PIVOTTASK_BEGIN = 1;
    const PIVOTTASK_END = 2;

    // }}}
    // Constructeur {{{

    /**
     * _ActivatedChain::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Reference string property + getter/setter {{{

    /**
     * Reference string property
     *
     * @access private
     * @var string
     */
    private $_Reference = '';

    /**
     * _ActivatedChain::getReference
     *
     * @access public
     * @return string
     */
    public function getReference() {
        return $this->_Reference;
    }

    /**
     * _ActivatedChain::setReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setReference($value) {
        $this->_Reference = $value;
    }

    // }}}
    // Description string property + getter/setter {{{

    /**
     * Description string property
     *
     * @access private
     * @var string
     */
    private $_Description = '';

    /**
     * _ActivatedChain::getDescription
     *
     * @access public
     * @return string
     */
    public function getDescription() {
        return $this->_Description;
    }

    /**
     * _ActivatedChain::setDescription
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDescription($value) {
        $this->_Description = $value;
    }

    // }}}
    // DescriptionCatalog string property + getter/setter {{{

    /**
     * DescriptionCatalog string property
     *
     * @access private
     * @var string
     */
    private $_DescriptionCatalog = '';

    /**
     * _ActivatedChain::getDescriptionCatalog
     *
     * @access public
     * @return string
     */
    public function getDescriptionCatalog() {
        return $this->_DescriptionCatalog;
    }

    /**
     * _ActivatedChain::setDescriptionCatalog
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDescriptionCatalog($value) {
        $this->_DescriptionCatalog = $value;
    }

    // }}}
    // Owner foreignkey property + getter/setter {{{

    /**
     * Owner foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Owner = false;

    /**
     * _ActivatedChain::getOwner
     *
     * @access public
     * @return object Actor
     */
    public function getOwner() {
        if (is_int($this->_Owner) && $this->_Owner > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Owner = $mapper->load(
                array('Id'=>$this->_Owner));
        }
        return $this->_Owner;
    }

    /**
     * _ActivatedChain::getOwnerId
     *
     * @access public
     * @return integer
     */
    public function getOwnerId() {
        if ($this->_Owner instanceof Actor) {
            return $this->_Owner->getId();
        }
        return (int)$this->_Owner;
    }

    /**
     * _ActivatedChain::setOwner
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setOwner($value) {
        if (is_numeric($value)) {
            $this->_Owner = (int)$value;
        } else {
            $this->_Owner = $value;
        }
    }

    // }}}
    // SiteTransition foreignkey property + getter/setter {{{

    /**
     * SiteTransition foreignkey
     *
     * @access private
     * @var mixed object ActorSiteTransition or integer
     */
    private $_SiteTransition = false;

    /**
     * _ActivatedChain::getSiteTransition
     *
     * @access public
     * @return object ActorSiteTransition
     */
    public function getSiteTransition() {
        if (is_int($this->_SiteTransition) && $this->_SiteTransition > 0) {
            $mapper = Mapper::singleton('ActorSiteTransition');
            $this->_SiteTransition = $mapper->load(
                array('Id'=>$this->_SiteTransition));
        }
        return $this->_SiteTransition;
    }

    /**
     * _ActivatedChain::getSiteTransitionId
     *
     * @access public
     * @return integer
     */
    public function getSiteTransitionId() {
        if ($this->_SiteTransition instanceof ActorSiteTransition) {
            return $this->_SiteTransition->getId();
        }
        return (int)$this->_SiteTransition;
    }

    /**
     * _ActivatedChain::setSiteTransition
     *
     * @access public
     * @param object ActorSiteTransition $value
     * @return void
     */
    public function setSiteTransition($value) {
        if (is_numeric($value)) {
            $this->_SiteTransition = (int)$value;
        } else {
            $this->_SiteTransition = $value;
        }
    }

    // }}}
    // PivotTask foreignkey property + getter/setter {{{

    /**
     * PivotTask foreignkey
     *
     * @access private
     * @var mixed object ActivatedChainTask or integer
     */
    private $_PivotTask = false;

    /**
     * _ActivatedChain::getPivotTask
     *
     * @access public
     * @return object ActivatedChainTask
     */
    public function getPivotTask() {
        if (is_int($this->_PivotTask) && $this->_PivotTask > 0) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_PivotTask = $mapper->load(
                array('Id'=>$this->_PivotTask));
        }
        return $this->_PivotTask;
    }

    /**
     * _ActivatedChain::getPivotTaskId
     *
     * @access public
     * @return integer
     */
    public function getPivotTaskId() {
        if ($this->_PivotTask instanceof ActivatedChainTask) {
            return $this->_PivotTask->getId();
        }
        return (int)$this->_PivotTask;
    }

    /**
     * _ActivatedChain::setPivotTask
     *
     * @access public
     * @param object ActivatedChainTask $value
     * @return void
     */
    public function setPivotTask($value) {
        if (is_numeric($value)) {
            $this->_PivotTask = (int)$value;
        } else {
            $this->_PivotTask = $value;
        }
    }

    // }}}
    // PivotDateType const property + getter/setter/getPivotDateTypeConstArray {{{

    /**
     * PivotDateType int property
     *
     * @access private
     * @var integer
     */
    private $_PivotDateType = 0;

    /**
     * _ActivatedChain::getPivotDateType
     *
     * @access public
     * @return integer
     */
    public function getPivotDateType() {
        return $this->_PivotDateType;
    }

    /**
     * _ActivatedChain::setPivotDateType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPivotDateType($value) {
        if ($value !== null) {
            $this->_PivotDateType = (int)$value;
        }
    }

    /**
     * _ActivatedChain::getPivotDateTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getPivotDateTypeConstArray($keys = false) {
        $array = array(
            _ActivatedChain::PIVOTTASK_BEGIN => _("Beginning"), 
            _ActivatedChain::PIVOTTASK_END => _("End")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // BeginDate datetime property + getter/setter {{{

    /**
     * BeginDate int property
     *
     * @access private
     * @var string
     */
    private $_BeginDate = 0;

    /**
     * _ActivatedChain::getBeginDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getBeginDate($format = false) {
        return $this->dateFormat($this->_BeginDate, $format);
    }

    /**
     * _ActivatedChain::setBeginDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBeginDate($value) {
        $this->_BeginDate = $value;
    }

    // }}}
    // EndDate datetime property + getter/setter {{{

    /**
     * EndDate int property
     *
     * @access private
     * @var string
     */
    private $_EndDate = 0;

    /**
     * _ActivatedChain::getEndDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEndDate($format = false) {
        return $this->dateFormat($this->_EndDate, $format);
    }

    /**
     * _ActivatedChain::setEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndDate($value) {
        $this->_EndDate = $value;
    }

    // }}}
    // OwnerWorkerOrder foreignkey property + getter/setter {{{

    /**
     * OwnerWorkerOrder foreignkey
     *
     * @access private
     * @var mixed object WorkOrder or integer
     */
    private $_OwnerWorkerOrder = false;

    /**
     * _ActivatedChain::getOwnerWorkerOrder
     *
     * @access public
     * @return object WorkOrder
     */
    public function getOwnerWorkerOrder() {
        if (is_int($this->_OwnerWorkerOrder) && $this->_OwnerWorkerOrder > 0) {
            $mapper = Mapper::singleton('WorkOrder');
            $this->_OwnerWorkerOrder = $mapper->load(
                array('Id'=>$this->_OwnerWorkerOrder));
        }
        return $this->_OwnerWorkerOrder;
    }

    /**
     * _ActivatedChain::getOwnerWorkerOrderId
     *
     * @access public
     * @return integer
     */
    public function getOwnerWorkerOrderId() {
        if ($this->_OwnerWorkerOrder instanceof WorkOrder) {
            return $this->_OwnerWorkerOrder->getId();
        }
        return (int)$this->_OwnerWorkerOrder;
    }

    /**
     * _ActivatedChain::setOwnerWorkerOrder
     *
     * @access public
     * @param object WorkOrder $value
     * @return void
     */
    public function setOwnerWorkerOrder($value) {
        if (is_numeric($value)) {
            $this->_OwnerWorkerOrder = (int)$value;
        } else {
            $this->_OwnerWorkerOrder = $value;
        }
    }

    // }}}
    // ExecutionSequence string property + getter/setter {{{

    /**
     * ExecutionSequence int property
     *
     * @access private
     * @var integer
     */
    private $_ExecutionSequence = 0;

    /**
     * _ActivatedChain::getExecutionSequence
     *
     * @access public
     * @return integer
     */
    public function getExecutionSequence() {
        return $this->_ExecutionSequence;
    }

    /**
     * _ActivatedChain::setExecutionSequence
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setExecutionSequence($value) {
        if ($value !== null) {
            $this->_ExecutionSequence = (int)$value;
        }
    }

    // }}}
    // BarCodeType foreignkey property + getter/setter {{{

    /**
     * BarCodeType foreignkey
     *
     * @access private
     * @var mixed object BarCodeType or integer
     */
    private $_BarCodeType = false;

    /**
     * _ActivatedChain::getBarCodeType
     *
     * @access public
     * @return object BarCodeType
     */
    public function getBarCodeType() {
        if (is_int($this->_BarCodeType) && $this->_BarCodeType > 0) {
            $mapper = Mapper::singleton('BarCodeType');
            $this->_BarCodeType = $mapper->load(
                array('Id'=>$this->_BarCodeType));
        }
        return $this->_BarCodeType;
    }

    /**
     * _ActivatedChain::getBarCodeTypeId
     *
     * @access public
     * @return integer
     */
    public function getBarCodeTypeId() {
        if ($this->_BarCodeType instanceof BarCodeType) {
            return $this->_BarCodeType->getId();
        }
        return (int)$this->_BarCodeType;
    }

    /**
     * _ActivatedChain::setBarCodeType
     *
     * @access public
     * @param object BarCodeType $value
     * @return void
     */
    public function setBarCodeType($value) {
        if (is_numeric($value)) {
            $this->_BarCodeType = (int)$value;
        } else {
            $this->_BarCodeType = $value;
        }
    }

    // }}}
    // Type string property + getter/setter {{{

    /**
     * Type int property
     *
     * @access private
     * @var integer
     */
    private $_Type = 0;

    /**
     * _ActivatedChain::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * _ActivatedChain::setType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setType($value) {
        if ($value !== null) {
            $this->_Type = (int)$value;
        }
    }

    // }}}
    // ProductType one to many relation + getter/setter {{{

    /**
     * ProductType *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductTypeCollection = false;

    /**
     * _ActivatedChain::getProductTypeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductTypeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChain');
            return $mapper->getManyToMany($this->getId(),
                'ProductType', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductTypeCollection) {
            $mapper = Mapper::singleton('ActivatedChain');
            $this->_ProductTypeCollection = $mapper->getManyToMany($this->getId(),
                'ProductType');
        }
        return $this->_ProductTypeCollection;
    }

    /**
     * _ActivatedChain::getProductTypeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductTypeCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getProductTypeCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ProductTypeCollection) {
            $mapper = Mapper::singleton('ActivatedChain');
            return $mapper->getManyToManyIds($this->getId(), 'ProductType');
        }
        return $this->_ProductTypeCollection->getItemIds();
    }

    /**
     * _ActivatedChain::setProductTypeCollectionIds
     *
     * @access public
     * @return array
     */
    public function setProductTypeCollectionIds($itemIds) {
        $this->_ProductTypeCollection = new Collection('ProductType');
        foreach ($itemIds as $id) {
            $this->_ProductTypeCollection->setItem($id);
        }
    }

    /**
     * _ActivatedChain::setProductTypeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductTypeCollection($value) {
        $this->_ProductTypeCollection = $value;
    }

    /**
     * _ActivatedChain::ProductTypeCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ProductTypeCollectionIsLoaded() {
        return ($this->_ProductTypeCollection !== false);
    }

    // }}}
    // DangerousProductType one to many relation + getter/setter {{{

    /**
     * DangerousProductType *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_DangerousProductTypeCollection = false;

    /**
     * _ActivatedChain::getDangerousProductTypeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getDangerousProductTypeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChain');
            return $mapper->getManyToMany($this->getId(),
                'DangerousProductType', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_DangerousProductTypeCollection) {
            $mapper = Mapper::singleton('ActivatedChain');
            $this->_DangerousProductTypeCollection = $mapper->getManyToMany($this->getId(),
                'DangerousProductType');
        }
        return $this->_DangerousProductTypeCollection;
    }

    /**
     * _ActivatedChain::getDangerousProductTypeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getDangerousProductTypeCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getDangerousProductTypeCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_DangerousProductTypeCollection) {
            $mapper = Mapper::singleton('ActivatedChain');
            return $mapper->getManyToManyIds($this->getId(), 'DangerousProductType');
        }
        return $this->_DangerousProductTypeCollection->getItemIds();
    }

    /**
     * _ActivatedChain::setDangerousProductTypeCollectionIds
     *
     * @access public
     * @return array
     */
    public function setDangerousProductTypeCollectionIds($itemIds) {
        $this->_DangerousProductTypeCollection = new Collection('DangerousProductType');
        foreach ($itemIds as $id) {
            $this->_DangerousProductTypeCollection->setItem($id);
        }
    }

    /**
     * _ActivatedChain::setDangerousProductTypeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setDangerousProductTypeCollection($value) {
        $this->_DangerousProductTypeCollection = $value;
    }

    /**
     * _ActivatedChain::DangerousProductTypeCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function DangerousProductTypeCollectionIsLoaded() {
        return ($this->_DangerousProductTypeCollection !== false);
    }

    // }}}
    // Product one to many relation + getter/setter {{{

    /**
     * Product *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductCollection = false;

    /**
     * _ActivatedChain::getProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChain');
            return $mapper->getManyToMany($this->getId(),
                'Product', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductCollection) {
            $mapper = Mapper::singleton('ActivatedChain');
            $this->_ProductCollection = $mapper->getManyToMany($this->getId(),
                'Product');
        }
        return $this->_ProductCollection;
    }

    /**
     * _ActivatedChain::getProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getProductCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ProductCollection) {
            $mapper = Mapper::singleton('ActivatedChain');
            return $mapper->getManyToManyIds($this->getId(), 'Product');
        }
        return $this->_ProductCollection->getItemIds();
    }

    /**
     * _ActivatedChain::setProductCollectionIds
     *
     * @access public
     * @return array
     */
    public function setProductCollectionIds($itemIds) {
        $this->_ProductCollection = new Collection('Product');
        foreach ($itemIds as $id) {
            $this->_ProductCollection->setItem($id);
        }
    }

    /**
     * _ActivatedChain::setProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductCollection($value) {
        $this->_ProductCollection = $value;
    }

    /**
     * _ActivatedChain::ProductCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ProductCollectionIsLoaded() {
        return ($this->_ProductCollection !== false);
    }

    // }}}
    // ActivatedChainOperation one to many relation + getter/setter {{{

    /**
     * ActivatedChainOperation 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActivatedChainOperationCollection = false;

    /**
     * _ActivatedChain::getActivatedChainOperationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedChainOperationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChain');
            return $mapper->getOneToMany($this->getId(),
                'ActivatedChainOperation', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedChainOperationCollection) {
            $mapper = Mapper::singleton('ActivatedChain');
            $this->_ActivatedChainOperationCollection = $mapper->getOneToMany($this->getId(),
                'ActivatedChainOperation');
        }
        return $this->_ActivatedChainOperationCollection;
    }

    /**
     * _ActivatedChain::getActivatedChainOperationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActivatedChainOperationCollectionIds($filter = array()) {
        $col = $this->getActivatedChainOperationCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ActivatedChain::setActivatedChainOperationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedChainOperationCollection($value) {
        $this->_ActivatedChainOperationCollection = $value;
    }

    // }}}
    // Box one to many relation + getter/setter {{{

    /**
     * Box 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_BoxCollection = false;

    /**
     * _ActivatedChain::getBoxCollection
     *
     * @access public
     * @return object Collection
     */
    public function getBoxCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChain');
            return $mapper->getOneToMany($this->getId(),
                'Box', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_BoxCollection) {
            $mapper = Mapper::singleton('ActivatedChain');
            $this->_BoxCollection = $mapper->getOneToMany($this->getId(),
                'Box');
        }
        return $this->_BoxCollection;
    }

    /**
     * _ActivatedChain::getBoxCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getBoxCollectionIds($filter = array()) {
        $col = $this->getBoxCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ActivatedChain::setBoxCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setBoxCollection($value) {
        $this->_BoxCollection = $value;
    }

    // }}}
    // CommandItem one to many relation + getter/setter {{{

    /**
     * CommandItem 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CommandItemCollection = false;

    /**
     * _ActivatedChain::getCommandItemCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCommandItemCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChain');
            return $mapper->getOneToMany($this->getId(),
                'CommandItem', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CommandItemCollection) {
            $mapper = Mapper::singleton('ActivatedChain');
            $this->_CommandItemCollection = $mapper->getOneToMany($this->getId(),
                'CommandItem');
        }
        return $this->_CommandItemCollection;
    }

    /**
     * _ActivatedChain::getCommandItemCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCommandItemCollectionIds($filter = array()) {
        $col = $this->getCommandItemCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ActivatedChain::setCommandItemCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCommandItemCollection($value) {
        $this->_CommandItemCollection = $value;
    }

    // }}}
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'ActivatedChain';
    }

    // }}}
    // getObjectLabel() {{{

    /**
     * Retourne le "label" de la classe.
     *
     * @static
     * @access public
     * @return string
     */
    public static function getObjectLabel() {
        return _('None');
    }

    // }}}
    // getProperties() {{{

    /**
     * Retourne le tableau des propriétés.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getProperties() {
        $return = array(
            'Reference' => Object::TYPE_STRING,
            'Description' => Object::TYPE_STRING,
            'DescriptionCatalog' => Object::TYPE_STRING,
            'Owner' => 'Actor',
            'SiteTransition' => 'ActorSiteTransition',
            'PivotTask' => 'ActivatedChainTask',
            'PivotDateType' => Object::TYPE_CONST,
            'BeginDate' => Object::TYPE_DATETIME,
            'EndDate' => Object::TYPE_DATETIME,
            'OwnerWorkerOrder' => 'WorkOrder',
            'ExecutionSequence' => Object::TYPE_INT,
            'BarCodeType' => 'BarCodeType',
            'Type' => Object::TYPE_INT);
        return $return;
    }

    // }}}
    // getLinks() {{{

    /**
     * Retourne le tableau des entités liées.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getLinks() {
        $return = array(
            'ProductType'=>array(
                'linkClass'     => 'ProductType',
                'field'         => 'FromActivatedChain',
                'linkTable'     => 'achProductType',
                'linkField'     => 'ToProductType',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'DangerousProductType'=>array(
                'linkClass'     => 'DangerousProductType',
                'field'         => 'FromActivatedChain',
                'linkTable'     => 'achDangerousProductType',
                'linkField'     => 'ToDangerousProductType',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Product'=>array(
                'linkClass'     => 'Product',
                'field'         => 'FromActivatedChain',
                'linkTable'     => 'achProduct',
                'linkField'     => 'ToProduct',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ActivatedChainOperation'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'ActivatedChain',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'Box'=>array(
                'linkClass'     => 'Box',
                'field'         => 'ActivatedChain',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'CommandItem'=>array(
                'linkClass'     => 'CommandItem',
                'field'         => 'ActivatedChain',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ));
        return $return;
    }

    // }}}
    // getUniqueProperties() {{{

    /**
     * Retourne le tableau des propriétés qui ne peuvent prendre la même valeur
     * pour 2 occurrences.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getUniqueProperties() {
        $return = array();
        return $return;
    }

    // }}}
    // getEmptyForDeleteProperties() {{{

    /**
     * Retourne le tableau des propriétés doivent être "vides" (0 ou '') pour
     * qu'une occurrence puisse être supprimée en base de données.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getEmptyForDeleteProperties() {
        $return = array();
        return $return;
    }

    // }}}
    // getFeatures() {{{

    /**
     * Retourne le tableau des "fonctionalités" pour l'objet en cours.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getFeatures() {
        return array();
    }

    // }}}
    // getMapping() {{{

    /**
     * Retourne le mapping nécessaires aux composants génériques.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getMapping() {
        $return = array();
        return $return;
    }

    // }}}
}

?>