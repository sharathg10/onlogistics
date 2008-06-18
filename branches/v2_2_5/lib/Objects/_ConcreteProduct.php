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

class _ConcreteProduct extends Object {
    // class constants {{{

    const EN_MARCHE = 0;
    const EN_REPARATION = 1;
    const AU_REBUS = 2;
    const EN_STOCK = 3;
    const EN_LOCATION = 4;

    // }}}
    // Constructeur {{{

    /**
     * _ConcreteProduct::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Immatriculation string property + getter/setter {{{

    /**
     * Immatriculation string property
     *
     * @access private
     * @var string
     */
    private $_Immatriculation = '';

    /**
     * _ConcreteProduct::getImmatriculation
     *
     * @access public
     * @return string
     */
    public function getImmatriculation() {
        return $this->_Immatriculation;
    }

    /**
     * _ConcreteProduct::setImmatriculation
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setImmatriculation($value) {
        $this->_Immatriculation = $value;
    }

    // }}}
    // SerialNumber string property + getter/setter {{{

    /**
     * SerialNumber string property
     *
     * @access private
     * @var string
     */
    private $_SerialNumber = '';

    /**
     * _ConcreteProduct::getSerialNumber
     *
     * @access public
     * @return string
     */
    public function getSerialNumber() {
        return $this->_SerialNumber;
    }

    /**
     * _ConcreteProduct::setSerialNumber
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setSerialNumber($value) {
        $this->_SerialNumber = $value;
    }

    // }}}
    // Weight float property + getter/setter {{{

    /**
     * Weight float property
     *
     * @access private
     * @var float
     */
    private $_Weight = 0;

    /**
     * _ConcreteProduct::getWeight
     *
     * @access public
     * @return float
     */
    public function getWeight() {
        return $this->_Weight;
    }

    /**
     * _ConcreteProduct::setWeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setWeight($value) {
        if ($value !== null) {
            $this->_Weight = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // BirthDate datetime property + getter/setter {{{

    /**
     * BirthDate int property
     *
     * @access private
     * @var string
     */
    private $_BirthDate = 0;

    /**
     * _ConcreteProduct::getBirthDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getBirthDate($format = false) {
        return $this->dateFormat($this->_BirthDate, $format);
    }

    /**
     * _ConcreteProduct::setBirthDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBirthDate($value) {
        $this->_BirthDate = $value;
    }

    // }}}
    // OnServiceDate datetime property + getter/setter {{{

    /**
     * OnServiceDate int property
     *
     * @access private
     * @var string
     */
    private $_OnServiceDate = 0;

    /**
     * _ConcreteProduct::getOnServiceDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getOnServiceDate($format = false) {
        return $this->dateFormat($this->_OnServiceDate, $format);
    }

    /**
     * _ConcreteProduct::setOnServiceDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setOnServiceDate($value) {
        $this->_OnServiceDate = $value;
    }

    // }}}
    // EndOfLifeDate datetime property + getter/setter {{{

    /**
     * EndOfLifeDate int property
     *
     * @access private
     * @var string
     */
    private $_EndOfLifeDate = 0;

    /**
     * _ConcreteProduct::getEndOfLifeDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEndOfLifeDate($format = false) {
        return $this->dateFormat($this->_EndOfLifeDate, $format);
    }

    /**
     * _ConcreteProduct::setEndOfLifeDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndOfLifeDate($value) {
        $this->_EndOfLifeDate = $value;
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
     * _ConcreteProduct::getOwner
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
     * _ConcreteProduct::getOwnerId
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
     * _ConcreteProduct::setOwner
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
    // OnCondition string property + getter/setter {{{

    /**
     * OnCondition int property
     *
     * @access private
     * @var integer
     */
    private $_OnCondition = false;

    /**
     * _ConcreteProduct::getOnCondition
     *
     * @access public
     * @return integer
     */
    public function getOnCondition() {
        return $this->_OnCondition;
    }

    /**
     * _ConcreteProduct::setOnCondition
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setOnCondition($value) {
        if ($value !== null) {
            $this->_OnCondition = (int)$value;
        }
    }

    // }}}
    // WarrantyBeginDate datetime property + getter/setter {{{

    /**
     * WarrantyBeginDate int property
     *
     * @access private
     * @var string
     */
    private $_WarrantyBeginDate = 0;

    /**
     * _ConcreteProduct::getWarrantyBeginDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getWarrantyBeginDate($format = false) {
        return $this->dateFormat($this->_WarrantyBeginDate, $format);
    }

    /**
     * _ConcreteProduct::setWarrantyBeginDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setWarrantyBeginDate($value) {
        $this->_WarrantyBeginDate = $value;
    }

    // }}}
    // WarrantyEndDate datetime property + getter/setter {{{

    /**
     * WarrantyEndDate int property
     *
     * @access private
     * @var string
     */
    private $_WarrantyEndDate = 0;

    /**
     * _ConcreteProduct::getWarrantyEndDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getWarrantyEndDate($format = false) {
        return $this->dateFormat($this->_WarrantyEndDate, $format);
    }

    /**
     * _ConcreteProduct::setWarrantyEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setWarrantyEndDate($value) {
        $this->_WarrantyEndDate = $value;
    }

    // }}}
    // BuyingPriceHT float property + getter/setter {{{

    /**
     * BuyingPriceHT float property
     *
     * @access private
     * @var float
     */
    private $_BuyingPriceHT = 0;

    /**
     * _ConcreteProduct::getBuyingPriceHT
     *
     * @access public
     * @return float
     */
    public function getBuyingPriceHT() {
        return $this->_BuyingPriceHT;
    }

    /**
     * _ConcreteProduct::setBuyingPriceHT
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setBuyingPriceHT($value) {
        if ($value !== null) {
            $this->_BuyingPriceHT = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // SellingPriceHT float property + getter/setter {{{

    /**
     * SellingPriceHT float property
     *
     * @access private
     * @var float
     */
    private $_SellingPriceHT = 0;

    /**
     * _ConcreteProduct::getSellingPriceHT
     *
     * @access public
     * @return float
     */
    public function getSellingPriceHT() {
        return $this->_SellingPriceHT;
    }

    /**
     * _ConcreteProduct::setSellingPriceHT
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setSellingPriceHT($value) {
        if ($value !== null) {
            $this->_SellingPriceHT = round(I18N::extractNumber($value), 2);
        }
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
     * _ConcreteProduct::getState
     *
     * @access public
     * @return integer
     */
    public function getState() {
        return $this->_State;
    }

    /**
     * _ConcreteProduct::setState
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
     * _ConcreteProduct::getStateConstArray
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
            _ConcreteProduct::EN_MARCHE => _("up"), 
            _ConcreteProduct::EN_REPARATION => _("in fixing"), 
            _ConcreteProduct::AU_REBUS => _("waste/discarded"), 
            _ConcreteProduct::EN_STOCK => _("in stock"), 
            _ConcreteProduct::EN_LOCATION => _("in rental")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // ConformityNumber string property + getter/setter {{{

    /**
     * ConformityNumber string property
     *
     * @access private
     * @var string
     */
    private $_ConformityNumber = '';

    /**
     * _ConcreteProduct::getConformityNumber
     *
     * @access public
     * @return string
     */
    public function getConformityNumber() {
        return $this->_ConformityNumber;
    }

    /**
     * _ConcreteProduct::setConformityNumber
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setConformityNumber($value) {
        $this->_ConformityNumber = $value;
    }

    // }}}
    // FME string property + getter/setter {{{

    /**
     * FME int property
     *
     * @access private
     * @var integer
     */
    private $_FME = false;

    /**
     * _ConcreteProduct::getFME
     *
     * @access public
     * @return integer
     */
    public function getFME() {
        return $this->_FME;
    }

    /**
     * _ConcreteProduct::setFME
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setFME($value) {
        if ($value !== null) {
            $this->_FME = (int)$value;
        }
    }

    // }}}
    // RealHourSinceNew float property + getter/setter {{{

    /**
     * RealHourSinceNew float property
     *
     * @access private
     * @var float
     */
    private $_RealHourSinceNew = 0;

    /**
     * _ConcreteProduct::getRealHourSinceNew
     *
     * @access public
     * @return float
     */
    public function getRealHourSinceNew() {
        return $this->_RealHourSinceNew;
    }

    /**
     * _ConcreteProduct::setRealHourSinceNew
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealHourSinceNew($value) {
        if ($value !== null) {
            $this->_RealHourSinceNew = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // RealHourSinceOverall float property + getter/setter {{{

    /**
     * RealHourSinceOverall float property
     *
     * @access private
     * @var float
     */
    private $_RealHourSinceOverall = 0;

    /**
     * _ConcreteProduct::getRealHourSinceOverall
     *
     * @access public
     * @return float
     */
    public function getRealHourSinceOverall() {
        return $this->_RealHourSinceOverall;
    }

    /**
     * _ConcreteProduct::setRealHourSinceOverall
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealHourSinceOverall($value) {
        if ($value !== null) {
            $this->_RealHourSinceOverall = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // RealHourSinceRepared float property + getter/setter {{{

    /**
     * RealHourSinceRepared float property
     *
     * @access private
     * @var float
     */
    private $_RealHourSinceRepared = 0;

    /**
     * _ConcreteProduct::getRealHourSinceRepared
     *
     * @access public
     * @return float
     */
    public function getRealHourSinceRepared() {
        return $this->_RealHourSinceRepared;
    }

    /**
     * _ConcreteProduct::setRealHourSinceRepared
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealHourSinceRepared($value) {
        if ($value !== null) {
            $this->_RealHourSinceRepared = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // VirtualHourSinceNew float property + getter/setter {{{

    /**
     * VirtualHourSinceNew float property
     *
     * @access private
     * @var float
     */
    private $_VirtualHourSinceNew = 0;

    /**
     * _ConcreteProduct::getVirtualHourSinceNew
     *
     * @access public
     * @return float
     */
    public function getVirtualHourSinceNew() {
        return $this->_VirtualHourSinceNew;
    }

    /**
     * _ConcreteProduct::setVirtualHourSinceNew
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setVirtualHourSinceNew($value) {
        if ($value !== null) {
            $this->_VirtualHourSinceNew = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // VirtualHourSinceOverall float property + getter/setter {{{

    /**
     * VirtualHourSinceOverall float property
     *
     * @access private
     * @var float
     */
    private $_VirtualHourSinceOverall = 0;

    /**
     * _ConcreteProduct::getVirtualHourSinceOverall
     *
     * @access public
     * @return float
     */
    public function getVirtualHourSinceOverall() {
        return $this->_VirtualHourSinceOverall;
    }

    /**
     * _ConcreteProduct::setVirtualHourSinceOverall
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setVirtualHourSinceOverall($value) {
        if ($value !== null) {
            $this->_VirtualHourSinceOverall = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Active string property + getter/setter {{{

    /**
     * Active int property
     *
     * @access private
     * @var integer
     */
    private $_Active = 1;

    /**
     * _ConcreteProduct::getActive
     *
     * @access public
     * @return integer
     */
    public function getActive() {
        return $this->_Active;
    }

    /**
     * _ConcreteProduct::setActive
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setActive($value) {
        if ($value !== null) {
            $this->_Active = (int)$value;
        }
    }

    // }}}
    // Component foreignkey property + getter/setter {{{

    /**
     * Component foreignkey
     *
     * @access private
     * @var mixed object Component or integer
     */
    private $_Component = false;

    /**
     * _ConcreteProduct::getComponent
     *
     * @access public
     * @return object Component
     */
    public function getComponent() {
        if (is_int($this->_Component) && $this->_Component > 0) {
            $mapper = Mapper::singleton('Component');
            $this->_Component = $mapper->load(
                array('Id'=>$this->_Component));
        }
        return $this->_Component;
    }

    /**
     * _ConcreteProduct::getComponentId
     *
     * @access public
     * @return integer
     */
    public function getComponentId() {
        if ($this->_Component instanceof Component) {
            return $this->_Component->getId();
        }
        return (int)$this->_Component;
    }

    /**
     * _ConcreteProduct::setComponent
     *
     * @access public
     * @param object Component $value
     * @return void
     */
    public function setComponent($value) {
        if (is_numeric($value)) {
            $this->_Component = (int)$value;
        } else {
            $this->_Component = $value;
        }
    }

    // }}}
    // Product foreignkey property + getter/setter {{{

    /**
     * Product foreignkey
     *
     * @access private
     * @var mixed object Product or integer
     */
    private $_Product = false;

    /**
     * _ConcreteProduct::getProduct
     *
     * @access public
     * @return object Product
     */
    public function getProduct() {
        if (is_int($this->_Product) && $this->_Product > 0) {
            $mapper = Mapper::singleton('Product');
            $this->_Product = $mapper->load(
                array('Id'=>$this->_Product));
        }
        return $this->_Product;
    }

    /**
     * _ConcreteProduct::getProductId
     *
     * @access public
     * @return integer
     */
    public function getProductId() {
        if ($this->_Product instanceof Product) {
            return $this->_Product->getId();
        }
        return (int)$this->_Product;
    }

    /**
     * _ConcreteProduct::setProduct
     *
     * @access public
     * @param object Product $value
     * @return void
     */
    public function setProduct($value) {
        if (is_numeric($value)) {
            $this->_Product = (int)$value;
        } else {
            $this->_Product = $value;
        }
    }

    // }}}
    // WeeklyPlanning foreignkey property + getter/setter {{{

    /**
     * WeeklyPlanning foreignkey
     *
     * @access private
     * @var mixed object WeeklyPlanning or integer
     */
    private $_WeeklyPlanning = false;

    /**
     * _ConcreteProduct::getWeeklyPlanning
     *
     * @access public
     * @return object WeeklyPlanning
     */
    public function getWeeklyPlanning() {
        if (is_int($this->_WeeklyPlanning) && $this->_WeeklyPlanning > 0) {
            $mapper = Mapper::singleton('WeeklyPlanning');
            $this->_WeeklyPlanning = $mapper->load(
                array('Id'=>$this->_WeeklyPlanning));
        }
        return $this->_WeeklyPlanning;
    }

    /**
     * _ConcreteProduct::getWeeklyPlanningId
     *
     * @access public
     * @return integer
     */
    public function getWeeklyPlanningId() {
        if ($this->_WeeklyPlanning instanceof WeeklyPlanning) {
            return $this->_WeeklyPlanning->getId();
        }
        return (int)$this->_WeeklyPlanning;
    }

    /**
     * _ConcreteProduct::setWeeklyPlanning
     *
     * @access public
     * @param object WeeklyPlanning $value
     * @return void
     */
    public function setWeeklyPlanning($value) {
        if (is_numeric($value)) {
            $this->_WeeklyPlanning = (int)$value;
        } else {
            $this->_WeeklyPlanning = $value;
        }
    }

    // }}}
    // ConcreteProduct one to many relation + getter/setter {{{

    /**
     * ConcreteProduct *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ConcreteProductCollection = false;

    /**
     * _ConcreteProduct::getConcreteProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getConcreteProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ConcreteProduct');
            return $mapper->getManyToMany($this->getId(),
                'ConcreteProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ConcreteProductCollection) {
            $mapper = Mapper::singleton('ConcreteProduct');
            $this->_ConcreteProductCollection = $mapper->getManyToMany($this->getId(),
                'ConcreteProduct');
        }
        return $this->_ConcreteProductCollection;
    }

    /**
     * _ConcreteProduct::getConcreteProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getConcreteProductCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getConcreteProductCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ConcreteProductCollection) {
            $mapper = Mapper::singleton('ConcreteProduct');
            return $mapper->getManyToManyIds($this->getId(), 'ConcreteProduct');
        }
        return $this->_ConcreteProductCollection->getItemIds();
    }

    /**
     * _ConcreteProduct::setConcreteProductCollectionIds
     *
     * @access public
     * @return array
     */
    public function setConcreteProductCollectionIds($itemIds) {
        $this->_ConcreteProductCollection = new Collection('ConcreteProduct');
        foreach ($itemIds as $id) {
            $this->_ConcreteProductCollection->setItem($id);
        }
    }

    /**
     * _ConcreteProduct::setConcreteProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setConcreteProductCollection($value) {
        $this->_ConcreteProductCollection = $value;
    }

    /**
     * _ConcreteProduct::ConcreteProductCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ConcreteProductCollectionIsLoaded() {
        return ($this->_ConcreteProductCollection !== false);
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
     * _ConcreteProduct::getActivatedChainOperationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedChainOperationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ConcreteProduct');
            return $mapper->getOneToMany($this->getId(),
                'ActivatedChainOperation', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedChainOperationCollection) {
            $mapper = Mapper::singleton('ConcreteProduct');
            $this->_ActivatedChainOperationCollection = $mapper->getOneToMany($this->getId(),
                'ActivatedChainOperation');
        }
        return $this->_ActivatedChainOperationCollection;
    }

    /**
     * _ConcreteProduct::getActivatedChainOperationCollectionIds
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
     * _ConcreteProduct::setActivatedChainOperationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedChainOperationCollection($value) {
        $this->_ActivatedChainOperationCollection = $value;
    }

    // }}}
    // RealActivatedChainOperation one to many relation + getter/setter {{{

    /**
     * RealActivatedChainOperation 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_RealActivatedChainOperationCollection = false;

    /**
     * _ConcreteProduct::getRealActivatedChainOperationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getRealActivatedChainOperationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ConcreteProduct');
            return $mapper->getOneToMany($this->getId(),
                'RealActivatedChainOperation', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_RealActivatedChainOperationCollection) {
            $mapper = Mapper::singleton('ConcreteProduct');
            $this->_RealActivatedChainOperationCollection = $mapper->getOneToMany($this->getId(),
                'RealActivatedChainOperation');
        }
        return $this->_RealActivatedChainOperationCollection;
    }

    /**
     * _ConcreteProduct::getRealActivatedChainOperationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getRealActivatedChainOperationCollectionIds($filter = array()) {
        $col = $this->getRealActivatedChainOperationCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ConcreteProduct::setRealActivatedChainOperationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setRealActivatedChainOperationCollection($value) {
        $this->_RealActivatedChainOperationCollection = $value;
    }

    // }}}
    // ConcreteComponent one to many relation + getter/setter {{{

    /**
     * ConcreteComponent 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ConcreteComponentCollection = false;

    /**
     * _ConcreteProduct::getConcreteComponentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getConcreteComponentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ConcreteProduct');
            return $mapper->getOneToMany($this->getId(),
                'ConcreteComponent', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ConcreteComponentCollection) {
            $mapper = Mapper::singleton('ConcreteProduct');
            $this->_ConcreteComponentCollection = $mapper->getOneToMany($this->getId(),
                'ConcreteComponent');
        }
        return $this->_ConcreteComponentCollection;
    }

    /**
     * _ConcreteProduct::getConcreteComponentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getConcreteComponentCollectionIds($filter = array()) {
        $col = $this->getConcreteComponentCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ConcreteProduct::setConcreteComponentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setConcreteComponentCollection($value) {
        $this->_ConcreteComponentCollection = $value;
    }

    // }}}
    // LEMConcreteProduct one to many relation + getter/setter {{{

    /**
     * LEMConcreteProduct 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LEMConcreteProductCollection = false;

    /**
     * _ConcreteProduct::getLEMConcreteProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLEMConcreteProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ConcreteProduct');
            return $mapper->getOneToMany($this->getId(),
                'LEMConcreteProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LEMConcreteProductCollection) {
            $mapper = Mapper::singleton('ConcreteProduct');
            $this->_LEMConcreteProductCollection = $mapper->getOneToMany($this->getId(),
                'LEMConcreteProduct');
        }
        return $this->_LEMConcreteProductCollection;
    }

    /**
     * _ConcreteProduct::getLEMConcreteProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLEMConcreteProductCollectionIds($filter = array()) {
        $col = $this->getLEMConcreteProductCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ConcreteProduct::setLEMConcreteProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLEMConcreteProductCollection($value) {
        $this->_LEMConcreteProductCollection = $value;
    }

    // }}}
    // LocationConcreteProduct one to many relation + getter/setter {{{

    /**
     * LocationConcreteProduct 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LocationConcreteProductCollection = false;

    /**
     * _ConcreteProduct::getLocationConcreteProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLocationConcreteProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ConcreteProduct');
            return $mapper->getOneToMany($this->getId(),
                'LocationConcreteProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LocationConcreteProductCollection) {
            $mapper = Mapper::singleton('ConcreteProduct');
            $this->_LocationConcreteProductCollection = $mapper->getOneToMany($this->getId(),
                'LocationConcreteProduct');
        }
        return $this->_LocationConcreteProductCollection;
    }

    /**
     * _ConcreteProduct::getLocationConcreteProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLocationConcreteProductCollectionIds($filter = array()) {
        $col = $this->getLocationConcreteProductCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ConcreteProduct::setLocationConcreteProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLocationConcreteProductCollection($value) {
        $this->_LocationConcreteProductCollection = $value;
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
        return 'ConcreteProduct';
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
            'Immatriculation' => Object::TYPE_STRING,
            'SerialNumber' => Object::TYPE_STRING,
            'Weight' => Object::TYPE_DECIMAL,
            'BirthDate' => Object::TYPE_DATETIME,
            'OnServiceDate' => Object::TYPE_DATETIME,
            'EndOfLifeDate' => Object::TYPE_DATETIME,
            'Owner' => 'Actor',
            'OnCondition' => Object::TYPE_BOOL,
            'WarrantyBeginDate' => Object::TYPE_DATETIME,
            'WarrantyEndDate' => Object::TYPE_DATETIME,
            'BuyingPriceHT' => Object::TYPE_DECIMAL,
            'SellingPriceHT' => Object::TYPE_DECIMAL,
            'State' => Object::TYPE_CONST,
            'ConformityNumber' => Object::TYPE_STRING,
            'FME' => Object::TYPE_BOOL,
            'RealHourSinceNew' => Object::TYPE_DECIMAL,
            'RealHourSinceOverall' => Object::TYPE_DECIMAL,
            'RealHourSinceRepared' => Object::TYPE_DECIMAL,
            'VirtualHourSinceNew' => Object::TYPE_DECIMAL,
            'VirtualHourSinceOverall' => Object::TYPE_DECIMAL,
            'Active' => Object::TYPE_BOOL,
            'Component' => 'Component',
            'Product' => 'Product',
            'WeeklyPlanning' => 'WeeklyPlanning');
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
            'ConcreteProduct'=>array(
                'linkClass'     => 'ConcreteProduct',
                'field'         => 'Head',
                'linkTable'     => 'cptHead',
                'linkField'     => 'ConcreteProduct',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ActivatedChainOperation'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'ConcreteProduct',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RealActivatedChainOperation'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'RealConcreteProduct',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ConcreteComponent'=>array(
                'linkClass'     => 'ConcreteComponent',
                'field'         => 'Parent',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ConcreteComponent'=>array(
                'linkClass'     => 'ConcreteComponent',
                'field'         => 'ConcreteProduct',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'LEMConcreteProduct'=>array(
                'linkClass'     => 'LEMConcreteProduct',
                'field'         => 'ConcreteProduct',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'LocationConcreteProduct'=>array(
                'linkClass'     => 'LocationConcreteProduct',
                'field'         => 'ConcreteProduct',
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
    // useInheritance() {{{

    /**
     * Détermine si l'entité est une entité qui utilise l'héritage.
     * (classe parente ou classe fille). Ceci afin de differencier les entités
     * dans le mapper car classes filles et parentes sont mappées dans la même
     * table.
     *
     * @static
     * @access public
     * @return bool
     */
    public static function useInheritance() {
        return true;
    }

    // }}}
    // _ConcreteProduct::mutate() {{{

    /**
     * "Mutation" d'un objet parent en classe fille et vice-versa.
     * Cela permet par exemple dans un formulaire de modifier la classe d'un
     * objet via un select.
     *
     * @access public
     * @param string type le type de l'objet vers lequel 'muter'
     * @return object
     **/
    public function mutate($type){
        // on instancie le bon objet
        require_once('Objects/' . $type . '.php');
        $mutant = new $type();
        if(!($mutant instanceof _ConcreteProduct)) {
            trigger_error('Invalid classname provided.', E_USER_ERROR);
        }
        // propriétés fixes
        $mutant->hasBeenInitialized = $this->hasBeenInitialized;
        $mutant->readonly = $this->readonly;
        $mutant->setId($this->getId());
        // propriétés simples
        $properties = $this->getProperties();
        foreach($properties as $property=>$type){
            $getter = 'get' . $property;
            $setter = 'set' . $property;
            if (method_exists($mutant, $setter)) {
                $mutant->$setter($this->$getter());
            }
        }
        // relations
        $links = $this->getLinks();
        foreach($links as $property=>$data){
            $getter = 'get' . $property . 'Collection';
            $setter = 'set' . $property . 'Collection';
            if (method_exists($mutant, $setter)) {
                $mutant->$setter($this->$getter());
            }
        }
        return $mutant;
    }

    // }}}
    // toString() {{{

    /**
     * Retourne la représentation texte de l'objet
     *
     * @access public
     * @return string
     */
    public function toString() {
        return $this->getImmatriculation();
    }

    // }}}
    // getToStringAttribute() {{{

    /**
     * Retourne le nom de l'attribut pointé par toString()
     *
     * @static
     * @access public
     * @return string
     */
    public function getToStringAttribute() {
        return 'Immatriculation';
    }

    // }}}
}

?>