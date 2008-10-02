<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IMPORTANT: This is a generated file, please do not edit.
 *
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

/**
 * _Chain class
 *
 */
class _Chain extends Object {
    // class constants {{{

    const CHAIN_TYPE_PRODUCT = 0;
    const CHAIN_TYPE_TRANSPORT = 1;
    const CHAIN_TYPE_MAINTENANCE = 2;
    const CHAIN_TYPE_COURSE = 3;
    const CHAIN_TYPE_HIRING = 4;
    const AUTOASSIGN_NONE = 0;
    const AUTOASSIGN_PRODUCTS = 1;
    const AUTOASSIGN_MATERIALS = 2;
    const CHAIN_STATE_UNSET = 0;
    const CHAIN_STATE_CREATED = 1;
    const CHAIN_STATE_BUILT = 2;
    const CHAIN_STATE_ACTIVABLE = 3;

    // }}}
    // Constructeur {{{

    /**
     * _Chain::__construct()
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
     * _Chain::getReference
     *
     * @access public
     * @return string
     */
    public function getReference() {
        return $this->_Reference;
    }

    /**
     * _Chain::setReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setReference($value) {
        $this->_Reference = $value;
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
     * _Chain::getOwner
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
     * _Chain::getOwnerId
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
     * _Chain::setOwner
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
    // Description string property + getter/setter {{{

    /**
     * Description string property
     *
     * @access private
     * @var string
     */
    private $_Description = '';

    /**
     * _Chain::getDescription
     *
     * @access public
     * @return string
     */
    public function getDescription() {
        return $this->_Description;
    }

    /**
     * _Chain::setDescription
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
     * _Chain::getDescriptionCatalog
     *
     * @access public
     * @return string
     */
    public function getDescriptionCatalog() {
        return $this->_DescriptionCatalog;
    }

    /**
     * _Chain::setDescriptionCatalog
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDescriptionCatalog($value) {
        $this->_DescriptionCatalog = $value;
    }

    // }}}
    // Type const property + getter/setter/getTypeConstArray {{{

    /**
     * Type int property
     *
     * @access private
     * @var integer
     */
    private $_Type = 0;

    /**
     * _Chain::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * _Chain::setType
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

    /**
     * _Chain::getTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getTypeConstArray($keys = false) {
        $array = array(
            _Chain::CHAIN_TYPE_PRODUCT => _("products"), 
            _Chain::CHAIN_TYPE_TRANSPORT => _("carriage"), 
            _Chain::CHAIN_TYPE_MAINTENANCE => _("maintenance"), 
            _Chain::CHAIN_TYPE_COURSE => _("class"), 
            _Chain::CHAIN_TYPE_HIRING => _("helicopter rental")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // AutoAssignTo const property + getter/setter/getAutoAssignToConstArray {{{

    /**
     * AutoAssignTo int property
     *
     * @access private
     * @var integer
     */
    private $_AutoAssignTo = 0;

    /**
     * _Chain::getAutoAssignTo
     *
     * @access public
     * @return integer
     */
    public function getAutoAssignTo() {
        return $this->_AutoAssignTo;
    }

    /**
     * _Chain::setAutoAssignTo
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setAutoAssignTo($value) {
        if ($value !== null) {
            $this->_AutoAssignTo = (int)$value;
        }
    }

    /**
     * _Chain::getAutoAssignToConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getAutoAssignToConstArray($keys = false) {
        $array = array(
            _Chain::AUTOASSIGN_NONE => _("Select an item"), 
            _Chain::AUTOASSIGN_PRODUCTS => _("Products"), 
            _Chain::AUTOASSIGN_MATERIALS => _("Materials")
        );
        asort($array);
        return $keys?array_keys($array):$array;
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
     * _Chain::getBarCodeType
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
     * _Chain::getBarCodeTypeId
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
     * _Chain::setBarCodeType
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
    // SiteTransition foreignkey property + getter/setter {{{

    /**
     * SiteTransition foreignkey
     *
     * @access private
     * @var mixed object ActorSiteTransition or integer
     */
    private $_SiteTransition = false;

    /**
     * _Chain::getSiteTransition
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
     * _Chain::getSiteTransitionId
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
     * _Chain::setSiteTransition
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
     * @var mixed object ChainTask or integer
     */
    private $_PivotTask = false;

    /**
     * _Chain::getPivotTask
     *
     * @access public
     * @return object ChainTask
     */
    public function getPivotTask() {
        if (is_int($this->_PivotTask) && $this->_PivotTask > 0) {
            $mapper = Mapper::singleton('ChainTask');
            $this->_PivotTask = $mapper->load(
                array('Id'=>$this->_PivotTask));
        }
        return $this->_PivotTask;
    }

    /**
     * _Chain::getPivotTaskId
     *
     * @access public
     * @return integer
     */
    public function getPivotTaskId() {
        if ($this->_PivotTask instanceof ChainTask) {
            return $this->_PivotTask->getId();
        }
        return (int)$this->_PivotTask;
    }

    /**
     * _Chain::setPivotTask
     *
     * @access public
     * @param object ChainTask $value
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
    // PivotDateType string property + getter/setter {{{

    /**
     * PivotDateType int property
     *
     * @access private
     * @var integer
     */
    private $_PivotDateType = 0;

    /**
     * _Chain::getPivotDateType
     *
     * @access public
     * @return integer
     */
    public function getPivotDateType() {
        return $this->_PivotDateType;
    }

    /**
     * _Chain::setPivotDateType
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

    // }}}
    // CommandSequence string property + getter/setter {{{

    /**
     * CommandSequence int property
     *
     * @access private
     * @var integer
     */
    private $_CommandSequence = 0;

    /**
     * _Chain::getCommandSequence
     *
     * @access public
     * @return integer
     */
    public function getCommandSequence() {
        return $this->_CommandSequence;
    }

    /**
     * _Chain::setCommandSequence
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCommandSequence($value) {
        if ($value !== null) {
            $this->_CommandSequence = (int)$value;
        }
    }

    // }}}
    // CreatedDate datetime property + getter/setter {{{

    /**
     * CreatedDate int property
     *
     * @access private
     * @var string
     */
    private $_CreatedDate = 0;

    /**
     * _Chain::getCreatedDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getCreatedDate($format = false) {
        return $this->dateFormat($this->_CreatedDate, $format);
    }

    /**
     * _Chain::setCreatedDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCreatedDate($value) {
        $this->_CreatedDate = $value;
    }

    // }}}
    // State const property + getter/setter/getStateConstArray {{{

    /**
     * State int property
     *
     * @access private
     * @var integer
     */
    private $_State = 0;

    /**
     * _Chain::getState
     *
     * @access public
     * @return integer
     */
    public function getState() {
        return $this->_State;
    }

    /**
     * _Chain::setState
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setState($value) {
        if ($value !== null) {
            $this->_State = (int)$value;
        }
    }

    /**
     * _Chain::getStateConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getStateConstArray($keys = false) {
        $array = array(
            _Chain::CHAIN_STATE_UNSET => _("N/A"), 
            _Chain::CHAIN_STATE_CREATED => _("Created"), 
            _Chain::CHAIN_STATE_BUILT => _("Contructed"), 
            _Chain::CHAIN_STATE_ACTIVABLE => _("Can be activated")
        );
        asort($array);
        return $keys?array_keys($array):$array;
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
     * _Chain::getProductTypeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductTypeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Chain');
            return $mapper->getManyToMany($this->getId(),
                'ProductType', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductTypeCollection) {
            $mapper = Mapper::singleton('Chain');
            $this->_ProductTypeCollection = $mapper->getManyToMany($this->getId(),
                'ProductType');
        }
        return $this->_ProductTypeCollection;
    }

    /**
     * _Chain::getProductTypeCollectionIds
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
            $mapper = Mapper::singleton('Chain');
            return $mapper->getManyToManyIds($this->getId(), 'ProductType');
        }
        return $this->_ProductTypeCollection->getItemIds();
    }

    /**
     * _Chain::setProductTypeCollectionIds
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
     * _Chain::setProductTypeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductTypeCollection($value) {
        $this->_ProductTypeCollection = $value;
    }

    /**
     * _Chain::ProductTypeCollectionIsLoaded
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
     * _Chain::getDangerousProductTypeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getDangerousProductTypeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Chain');
            return $mapper->getManyToMany($this->getId(),
                'DangerousProductType', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_DangerousProductTypeCollection) {
            $mapper = Mapper::singleton('Chain');
            $this->_DangerousProductTypeCollection = $mapper->getManyToMany($this->getId(),
                'DangerousProductType');
        }
        return $this->_DangerousProductTypeCollection;
    }

    /**
     * _Chain::getDangerousProductTypeCollectionIds
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
            $mapper = Mapper::singleton('Chain');
            return $mapper->getManyToManyIds($this->getId(), 'DangerousProductType');
        }
        return $this->_DangerousProductTypeCollection->getItemIds();
    }

    /**
     * _Chain::setDangerousProductTypeCollectionIds
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
     * _Chain::setDangerousProductTypeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setDangerousProductTypeCollection($value) {
        $this->_DangerousProductTypeCollection = $value;
    }

    /**
     * _Chain::DangerousProductTypeCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function DangerousProductTypeCollectionIsLoaded() {
        return ($this->_DangerousProductTypeCollection !== false);
    }

    // }}}
    // ChainOperation one to many relation + getter/setter {{{

    /**
     * ChainOperation 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ChainOperationCollection = false;

    /**
     * _Chain::getChainOperationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getChainOperationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Chain');
            return $mapper->getOneToMany($this->getId(),
                'ChainOperation', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ChainOperationCollection) {
            $mapper = Mapper::singleton('Chain');
            $this->_ChainOperationCollection = $mapper->getOneToMany($this->getId(),
                'ChainOperation');
        }
        return $this->_ChainOperationCollection;
    }

    /**
     * _Chain::getChainOperationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getChainOperationCollectionIds($filter = array()) {
        $col = $this->getChainOperationCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Chain::setChainOperationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setChainOperationCollection($value) {
        $this->_ChainOperationCollection = $value;
    }

    // }}}
    // ProductChainLink one to many relation + getter/setter {{{

    /**
     * ProductChainLink 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductChainLinkCollection = false;

    /**
     * _Chain::getProductChainLinkCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductChainLinkCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Chain');
            return $mapper->getOneToMany($this->getId(),
                'ProductChainLink', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductChainLinkCollection) {
            $mapper = Mapper::singleton('Chain');
            $this->_ProductChainLinkCollection = $mapper->getOneToMany($this->getId(),
                'ProductChainLink');
        }
        return $this->_ProductChainLinkCollection;
    }

    /**
     * _Chain::getProductChainLinkCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductChainLinkCollectionIds($filter = array()) {
        $col = $this->getProductChainLinkCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Chain::setProductChainLinkCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductChainLinkCollection($value) {
        $this->_ProductChainLinkCollection = $value;
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
        return 'Chain';
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
        return _('Chains');
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
            'Owner' => 'Actor',
            'Description' => Object::TYPE_STRING,
            'DescriptionCatalog' => Object::TYPE_STRING,
            'Type' => Object::TYPE_CONST,
            'AutoAssignTo' => Object::TYPE_CONST,
            'BarCodeType' => 'BarCodeType',
            'SiteTransition' => 'ActorSiteTransition',
            'PivotTask' => 'ChainTask',
            'PivotDateType' => Object::TYPE_INT,
            'CommandSequence' => Object::TYPE_INT,
            'CreatedDate' => Object::TYPE_DATETIME,
            'State' => Object::TYPE_CONST);
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
                'field'         => 'FromChain',
                'linkTable'     => 'chnProductType',
                'linkField'     => 'ToProductType',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'DangerousProductType'=>array(
                'linkClass'     => 'DangerousProductType',
                'field'         => 'FromChain',
                'linkTable'     => 'chnDangerousProductType',
                'linkField'     => 'ToDangerousProductType',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'ChainToActivate',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ChainCommand'=>array(
                'linkClass'     => 'ChainCommand',
                'field'         => 'Chain',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainOperation'=>array(
                'linkClass'     => 'ChainOperation',
                'field'         => 'Chain',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ChainTask'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'ChainToActivate',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ProductChainLink'=>array(
                'linkClass'     => 'ProductChainLink',
                'field'         => 'Chain',
                'ondelete'      => 'cascade',
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
        $return = array('Reference');
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
        return array('searchform', 'grid', 'add', 'edit', 'del');
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
        $return = array(
            'Reference'=>array(
                'label'        => _('Reference'),
                'shortlabel'   => _('Reference'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Owner'=>array(
                'label'        => _('Creator'),
                'shortlabel'   => _('Creator'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Description'=>array(
                'label'        => _('Designation'),
                'shortlabel'   => _('Designation'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'DescriptionCatalog'=>array(
                'label'        => _('Catalog description'),
                'shortlabel'   => _('Catalog description'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Type'=>array(
                'label'        => _('Type'),
                'shortlabel'   => _('Type'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'AutoAssignTo'=>array(
                'label'        => _('Auto assign to'),
                'shortlabel'   => _('Auto assign to'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'BarCodeType'=>array(
                'label'        => _('Barcode type'),
                'shortlabel'   => _('Barcode type'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'SiteTransition'=>array(
                'label'        => _('Site'),
                'shortlabel'   => _('Site'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'CreatedDate'=>array(
                'label'        => _('Creation date'),
                'shortlabel'   => _('Creation date'),
                'usedby'       => array('grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'State'=>array(
                'label'        => _('State'),
                'shortlabel'   => _('State'),
                'usedby'       => array('searchform', 'grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ProductType'=>array(
                'label'        => _('Product type'),
                'shortlabel'   => _('Product type'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
}

?>