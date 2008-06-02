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

class _CostRange extends Object {
    // class constants {{{

    const TYPE_FIXED = 0;
    const TYPE_HOUR = 1;
    const TYPE_HOUR_WEIGHT_RANGE = 2;
    const TYPE_HOUR_VOLUME_RANGE = 3;
    const TYPE_AMOUNT = 4;
    const TYPE_AMOUNT_WEIGHT_RANGE = 5;
    const TYPE_AMOUNT_VOLUME_RANGE = 6;
    const TYPE_UNIT = 7;
    const TYPE_FIXED_WEIGHT_RANGE = 8;
    const TYPE_FIXED_VOLUME_RANGE = 9;
    const TYPE_FIXED_BY_LOCATION = 10;
    const TYPE_UNIT_FOR_QUANTITY = 11;
    const TYPE_FIXED_QUANTITY = 12;
    const TYPE_UNIT_BY_RANGE_10 = 13;
    const TYPE_UNIT_BY_RANGE_100 = 14;

    // }}}
    // Constructeur {{{

    /**
     * _CostRange::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Cost float property + getter/setter {{{

    /**
     * Cost float property
     *
     * @access private
     * @var float
     */
    private $_Cost = 0;

    /**
     * _CostRange::getCost
     *
     * @access public
     * @return float
     */
    public function getCost() {
        return $this->_Cost;
    }

    /**
     * _CostRange::setCost
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCost($value) {
        if ($value !== null) {
            $this->_Cost = round(I18N::extractNumber($value), 3);
        }
    }

    // }}}
    // CostType const property + getter/setter/getCostTypeConstArray {{{

    /**
     * CostType int property
     *
     * @access private
     * @var integer
     */
    private $_CostType = 0;

    /**
     * _CostRange::getCostType
     *
     * @access public
     * @return integer
     */
    public function getCostType() {
        return $this->_CostType;
    }

    /**
     * _CostRange::setCostType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCostType($value) {
        if ($value !== null) {
            $this->_CostType = (int)$value;
        }
    }

    /**
     * _CostRange::getCostTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getCostTypeConstArray($keys = false) {
        $array = array(
            _CostRange::TYPE_FIXED => _("Forfeiture by calendar month"), 
            _CostRange::TYPE_HOUR => _("Amount by hour"), 
            _CostRange::TYPE_HOUR_WEIGHT_RANGE => _("Amount by hour and by weight range"), 
            _CostRange::TYPE_HOUR_VOLUME_RANGE => _("Amount by hour and by volume range"), 
            _CostRange::TYPE_AMOUNT => _("Price by range"), 
            _CostRange::TYPE_AMOUNT_WEIGHT_RANGE => _("Amount by range and by weight range"), 
            _CostRange::TYPE_AMOUNT_VOLUME_RANGE => _("Amount by range and by volume range"), 
            _CostRange::TYPE_UNIT => _("Price by unit"), 
            _CostRange::TYPE_FIXED_WEIGHT_RANGE => _("Amount by weight range"), 
            _CostRange::TYPE_FIXED_VOLUME_RANGE => _("Amount by volume range"), 
            _CostRange::TYPE_FIXED_BY_LOCATION => _("Amount by location"), 
            _CostRange::TYPE_UNIT_FOR_QUANTITY => _("Price by unit for a quantity"), 
            _CostRange::TYPE_FIXED_QUANTITY => _("Amount by range"), 
            _CostRange::TYPE_UNIT_BY_RANGE_10 => _("Price by weight range by 10"), 
            _CostRange::TYPE_UNIT_BY_RANGE_100 => _("Price by weight range by 100")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // BeginRange float property + getter/setter {{{

    /**
     * BeginRange float property
     *
     * @access private
     * @var float
     */
    private $_BeginRange = 0;

    /**
     * _CostRange::getBeginRange
     *
     * @access public
     * @return float
     */
    public function getBeginRange() {
        return $this->_BeginRange;
    }

    /**
     * _CostRange::setBeginRange
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setBeginRange($value) {
        if ($value !== null) {
            $this->_BeginRange = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // EndRange float property + getter/setter {{{

    /**
     * EndRange float property
     *
     * @access private
     * @var float
     */
    private $_EndRange = 0;

    /**
     * _CostRange::getEndRange
     *
     * @access public
     * @return float
     */
    public function getEndRange() {
        return $this->_EndRange;
    }

    /**
     * _CostRange::setEndRange
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setEndRange($value) {
        if ($value !== null) {
            $this->_EndRange = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // DepartureZone foreignkey property + getter/setter {{{

    /**
     * DepartureZone foreignkey
     *
     * @access private
     * @var mixed object Zone or integer
     */
    private $_DepartureZone = false;

    /**
     * _CostRange::getDepartureZone
     *
     * @access public
     * @return object Zone
     */
    public function getDepartureZone() {
        if (is_int($this->_DepartureZone) && $this->_DepartureZone > 0) {
            $mapper = Mapper::singleton('Zone');
            $this->_DepartureZone = $mapper->load(
                array('Id'=>$this->_DepartureZone));
        }
        return $this->_DepartureZone;
    }

    /**
     * _CostRange::getDepartureZoneId
     *
     * @access public
     * @return integer
     */
    public function getDepartureZoneId() {
        if ($this->_DepartureZone instanceof Zone) {
            return $this->_DepartureZone->getId();
        }
        return (int)$this->_DepartureZone;
    }

    /**
     * _CostRange::setDepartureZone
     *
     * @access public
     * @param object Zone $value
     * @return void
     */
    public function setDepartureZone($value) {
        if (is_numeric($value)) {
            $this->_DepartureZone = (int)$value;
        } else {
            $this->_DepartureZone = $value;
        }
    }

    // }}}
    // ArrivalZone foreignkey property + getter/setter {{{

    /**
     * ArrivalZone foreignkey
     *
     * @access private
     * @var mixed object Zone or integer
     */
    private $_ArrivalZone = false;

    /**
     * _CostRange::getArrivalZone
     *
     * @access public
     * @return object Zone
     */
    public function getArrivalZone() {
        if (is_int($this->_ArrivalZone) && $this->_ArrivalZone > 0) {
            $mapper = Mapper::singleton('Zone');
            $this->_ArrivalZone = $mapper->load(
                array('Id'=>$this->_ArrivalZone));
        }
        return $this->_ArrivalZone;
    }

    /**
     * _CostRange::getArrivalZoneId
     *
     * @access public
     * @return integer
     */
    public function getArrivalZoneId() {
        if ($this->_ArrivalZone instanceof Zone) {
            return $this->_ArrivalZone->getId();
        }
        return (int)$this->_ArrivalZone;
    }

    /**
     * _CostRange::setArrivalZone
     *
     * @access public
     * @param object Zone $value
     * @return void
     */
    public function setArrivalZone($value) {
        if (is_numeric($value)) {
            $this->_ArrivalZone = (int)$value;
        } else {
            $this->_ArrivalZone = $value;
        }
    }

    // }}}
    // Store foreignkey property + getter/setter {{{

    /**
     * Store foreignkey
     *
     * @access private
     * @var mixed object Store or integer
     */
    private $_Store = false;

    /**
     * _CostRange::getStore
     *
     * @access public
     * @return object Store
     */
    public function getStore() {
        if (is_int($this->_Store) && $this->_Store > 0) {
            $mapper = Mapper::singleton('Store');
            $this->_Store = $mapper->load(
                array('Id'=>$this->_Store));
        }
        return $this->_Store;
    }

    /**
     * _CostRange::getStoreId
     *
     * @access public
     * @return integer
     */
    public function getStoreId() {
        if ($this->_Store instanceof Store) {
            return $this->_Store->getId();
        }
        return (int)$this->_Store;
    }

    /**
     * _CostRange::setStore
     *
     * @access public
     * @param object Store $value
     * @return void
     */
    public function setStore($value) {
        if (is_numeric($value)) {
            $this->_Store = (int)$value;
        } else {
            $this->_Store = $value;
        }
    }

    // }}}
    // ProductType foreignkey property + getter/setter {{{

    /**
     * ProductType foreignkey
     *
     * @access private
     * @var mixed object ProductType or integer
     */
    private $_ProductType = false;

    /**
     * _CostRange::getProductType
     *
     * @access public
     * @return object ProductType
     */
    public function getProductType() {
        if (is_int($this->_ProductType) && $this->_ProductType > 0) {
            $mapper = Mapper::singleton('ProductType');
            $this->_ProductType = $mapper->load(
                array('Id'=>$this->_ProductType));
        }
        return $this->_ProductType;
    }

    /**
     * _CostRange::getProductTypeId
     *
     * @access public
     * @return integer
     */
    public function getProductTypeId() {
        if ($this->_ProductType instanceof ProductType) {
            return $this->_ProductType->getId();
        }
        return (int)$this->_ProductType;
    }

    /**
     * _CostRange::setProductType
     *
     * @access public
     * @param object ProductType $value
     * @return void
     */
    public function setProductType($value) {
        if (is_numeric($value)) {
            $this->_ProductType = (int)$value;
        } else {
            $this->_ProductType = $value;
        }
    }

    // }}}
    // UnitType foreignkey property + getter/setter {{{

    /**
     * UnitType foreignkey
     *
     * @access private
     * @var mixed object SellUnitType or integer
     */
    private $_UnitType = false;

    /**
     * _CostRange::getUnitType
     *
     * @access public
     * @return object SellUnitType
     */
    public function getUnitType() {
        if (is_int($this->_UnitType) && $this->_UnitType > 0) {
            $mapper = Mapper::singleton('SellUnitType');
            $this->_UnitType = $mapper->load(
                array('Id'=>$this->_UnitType));
        }
        return $this->_UnitType;
    }

    /**
     * _CostRange::getUnitTypeId
     *
     * @access public
     * @return integer
     */
    public function getUnitTypeId() {
        if ($this->_UnitType instanceof SellUnitType) {
            return $this->_UnitType->getId();
        }
        return (int)$this->_UnitType;
    }

    /**
     * _CostRange::setUnitType
     *
     * @access public
     * @param object SellUnitType $value
     * @return void
     */
    public function setUnitType($value) {
        if (is_numeric($value)) {
            $this->_UnitType = (int)$value;
        } else {
            $this->_UnitType = $value;
        }
    }

    // }}}
    // Prestation foreignkey property + getter/setter {{{

    /**
     * Prestation foreignkey
     *
     * @access private
     * @var mixed object Prestation or integer
     */
    private $_Prestation = false;

    /**
     * _CostRange::getPrestation
     *
     * @access public
     * @return object Prestation
     */
    public function getPrestation() {
        if (is_int($this->_Prestation) && $this->_Prestation > 0) {
            $mapper = Mapper::singleton('Prestation');
            $this->_Prestation = $mapper->load(
                array('Id'=>$this->_Prestation));
        }
        return $this->_Prestation;
    }

    /**
     * _CostRange::getPrestationId
     *
     * @access public
     * @return integer
     */
    public function getPrestationId() {
        if ($this->_Prestation instanceof Prestation) {
            return $this->_Prestation->getId();
        }
        return (int)$this->_Prestation;
    }

    /**
     * _CostRange::setPrestation
     *
     * @access public
     * @param object Prestation $value
     * @return void
     */
    public function setPrestation($value) {
        if (is_numeric($value)) {
            $this->_Prestation = (int)$value;
        } else {
            $this->_Prestation = $value;
        }
    }

    // }}}
    // PrestationCost foreignkey property + getter/setter {{{

    /**
     * PrestationCost foreignkey
     *
     * @access private
     * @var mixed object PrestationCost or integer
     */
    private $_PrestationCost = false;

    /**
     * _CostRange::getPrestationCost
     *
     * @access public
     * @return object PrestationCost
     */
    public function getPrestationCost() {
        if (is_int($this->_PrestationCost) && $this->_PrestationCost > 0) {
            $mapper = Mapper::singleton('PrestationCost');
            $this->_PrestationCost = $mapper->load(
                array('Id'=>$this->_PrestationCost));
        }
        return $this->_PrestationCost;
    }

    /**
     * _CostRange::getPrestationCostId
     *
     * @access public
     * @return integer
     */
    public function getPrestationCostId() {
        if ($this->_PrestationCost instanceof PrestationCost) {
            return $this->_PrestationCost->getId();
        }
        return (int)$this->_PrestationCost;
    }

    /**
     * _CostRange::setPrestationCost
     *
     * @access public
     * @param object PrestationCost $value
     * @return void
     */
    public function setPrestationCost($value) {
        if (is_numeric($value)) {
            $this->_PrestationCost = (int)$value;
        } else {
            $this->_PrestationCost = $value;
        }
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
        return 'CostRange';
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
        return _('Add/Update service cost');
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
            'Cost' => Object::TYPE_DECIMAL,
            'CostType' => Object::TYPE_CONST,
            'BeginRange' => Object::TYPE_DECIMAL,
            'EndRange' => Object::TYPE_DECIMAL,
            'DepartureZone' => 'Zone',
            'ArrivalZone' => 'Zone',
            'Store' => 'Store',
            'ProductType' => 'ProductType',
            'UnitType' => 'SellUnitType',
            'Prestation' => 'Prestation',
            'PrestationCost' => 'PrestationCost');
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
        $return = array();
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
        return array('add', 'edit', 'grid');
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
            'Cost'=>array(
                'label'        => _('Price'),
                'shortlabel'   => _('Price'),
                'usedby'       => array('addedit', 'grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'CostType'=>array(
                'label'        => _('Cost type'),
                'shortlabel'   => _('Cost type'),
                'usedby'       => array('addedit', 'grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'BeginRange'=>array(
                'label'        => _('Lower bound'),
                'shortlabel'   => _('Lower bound'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 2
            ),
            'EndRange'=>array(
                'label'        => _('Upper bound'),
                'shortlabel'   => _('Upper bound'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 2
            ),
            'DepartureZone'=>array(
                'label'        => _('Departure zone'),
                'shortlabel'   => _('Departure zone'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ArrivalZone'=>array(
                'label'        => _('Arrival zone'),
                'shortlabel'   => _('Arrival zone'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Store'=>array(
                'label'        => _('Store'),
                'shortlabel'   => _('Store'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ProductType'=>array(
                'label'        => _('Product type'),
                'shortlabel'   => _('Product type'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'UnitType'=>array(
                'label'        => _('Unit type'),
                'shortlabel'   => _('Unit type'),
                'usedby'       => array('grid', 'addedit'),
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