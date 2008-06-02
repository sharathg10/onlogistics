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

class _Product extends Object {
    // class constants {{{

    const TRACINGMODE_None = 0;
    const TRACINGMODE_SN = 1;
    const TRACINGMODE_LOT = 2;

    // }}}
    // Constructeur {{{

    /**
     * _Product::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // lastmodified property {{{

    /**
     * Date de dernière modification (ou création) au format Datetime mysql.
     *
     * @var string $lastModified
     * @access public
     */
     public $lastModified = 0;

    // }}}

    // Name i18n_string property + getter/setter {{{

    /**
     * Name foreignkey
     *
     * @access private
     * @var mixed object I18nString or integer
     */
    private $_Name = 0;

    /**
     * _Product::getName
     *
     * @access public
     * @param string $locale optional, default is the current locale code
     * @param boolean $useDefaultLocaleIfEmpty determine if the getter must
     * return the translation in the DEFAULT_LOCALE if no translation is found
     * in the current locale.
     * @return string
     */
    public function getName($locale=false, $defaultLocaleIfEmpty=true) {
        $locale = $locale !== false ? $locale : I18N::getLocaleCode();
        if (is_int($this->_Name) && $this->_Name > 0) {
            $this->_Name = Object::load('I18nString', $this->_Name);
        }
        $ret = null;
        if ($this->_Name instanceof I18nString) {
            $getter = 'getStringValue_' . $locale;
            $ret = $this->_Name->$getter();
            if ($ret == null && $defaultLocaleIfEmpty) {
                $getter = 'getStringValue_' . LOCALE_DEFAULT;
                $ret = $this->_Name->$getter();
            }
        }
        return $ret;
    }

    /**
     * _Product::getNameId
     *
     * @access public
     * @return integer
     */
    public function getNameId() {
        if ($this->_Name instanceof I18nString) {
            return $this->_Name->getId();
        }
        return (int)$this->_Name;
    }

    /**
     * _Product::setName
     *
     * @access public
     * @param string $value
     * @param string $locale optional, default is the current locale code
     * @return void
     */
    public function setName($value, $locale=false) {
        if (is_numeric($value)) {
            $this->_Name = (int)$value;
        } else if ($value instanceof I18nString) {
            $this->_Name = $value;
        } else {
            $locale = $locale !== false ? $locale : I18N::getLocaleCode();
            if (!($this->_Name instanceof I18nString)) {
                $this->_Name = Object::load('I18nString', $this->_Name);
                if (!($this->_Name instanceof I18nString)) {
                    $this->_Name = new I18nString();
                }
            }
            $setter = 'setStringValue_'.$locale;
            $this->_Name->$setter($value);
            $this->_Name->save();
        }
    }

    // }}}
    // BaseReference string property + getter/setter {{{

    /**
     * BaseReference string property
     *
     * @access private
     * @var string
     */
    private $_BaseReference = '';

    /**
     * _Product::getBaseReference
     *
     * @access public
     * @return string
     */
    public function getBaseReference() {
        return $this->_BaseReference;
    }

    /**
     * _Product::setBaseReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBaseReference($value) {
        $this->_BaseReference = $value;
    }

    // }}}
    // Volume float property + getter/setter {{{

    /**
     * Volume float property
     *
     * @access private
     * @var float
     */
    private $_Volume = 0;

    /**
     * _Product::getVolume
     *
     * @access public
     * @return float
     */
    public function getVolume() {
        return $this->_Volume;
    }

    /**
     * _Product::setVolume
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setVolume($value) {
        if ($value !== null) {
            $this->_Volume = I18N::extractNumber($value);
        }
    }

    // }}}
    // CustomsNaming string property + getter/setter {{{

    /**
     * CustomsNaming string property
     *
     * @access private
     * @var string
     */
    private $_CustomsNaming = '';

    /**
     * _Product::getCustomsNaming
     *
     * @access public
     * @return string
     */
    public function getCustomsNaming() {
        return $this->_CustomsNaming;
    }

    /**
     * _Product::setCustomsNaming
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCustomsNaming($value) {
        $this->_CustomsNaming = $value;
    }

    // }}}
    // Category string property + getter/setter {{{

    /**
     * Category int property
     *
     * @access private
     * @var integer
     */
    private $_Category = 0;

    /**
     * _Product::getCategory
     *
     * @access public
     * @return integer
     */
    public function getCategory() {
        return $this->_Category;
    }

    /**
     * _Product::setCategory
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCategory($value) {
        if ($value !== null) {
            $this->_Category = (int)$value;
        }
    }

    // }}}
    // Activated string property + getter/setter {{{

    /**
     * Activated int property
     *
     * @access private
     * @var integer
     */
    private $_Activated = 1;

    /**
     * _Product::getActivated
     *
     * @access public
     * @return integer
     */
    public function getActivated() {
        return $this->_Activated;
    }

    /**
     * _Product::setActivated
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setActivated($value) {
        if ($value !== null) {
            $this->_Activated = (int)$value;
        }
    }

    // }}}
    // Affected string property + getter/setter {{{

    /**
     * Affected int property
     *
     * @access private
     * @var integer
     */
    private $_Affected = 0;

    /**
     * _Product::getAffected
     *
     * @access public
     * @return integer
     */
    public function getAffected() {
        return $this->_Affected;
    }

    /**
     * _Product::setAffected
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setAffected($value) {
        if ($value !== null) {
            $this->_Affected = (int)$value;
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
     * _Product::getProductType
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
     * _Product::getProductTypeId
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
     * _Product::setProductType
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
    // TracingMode const property + getter/setter/getTracingModeConstArray {{{

    /**
     * TracingMode int property
     *
     * @access private
     * @var integer
     */
    private $_TracingMode = 0;

    /**
     * _Product::getTracingMode
     *
     * @access public
     * @return integer
     */
    public function getTracingMode() {
        return $this->_TracingMode;
    }

    /**
     * _Product::setTracingMode
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTracingMode($value) {
        if ($value !== null) {
            $this->_TracingMode = (int)$value;
        }
    }

    /**
     * _Product::getTracingModeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getTracingModeConstArray($keys = false) {
        $array = array(
            _Product::TRACINGMODE_None => _("None"), 
            _Product::TRACINGMODE_SN => _("SN"), 
            _Product::TRACINGMODE_LOT => _("Lot")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // TracingModeBeginRange int property + getter/setter {{{

    /**
     * TracingModeBeginRange int property
     *
     * @access private
     * @var integer
     */
    private $_TracingModeBeginRange = null;

    /**
     * _Product::getTracingModeBeginRange
     *
     * @access public
     * @return integer
     */
    public function getTracingModeBeginRange() {
        return $this->_TracingModeBeginRange;
    }

    /**
     * _Product::setTracingModeBeginRange
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTracingModeBeginRange($value) {
        $this->_TracingModeBeginRange = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // TracingModeEndRange int property + getter/setter {{{

    /**
     * TracingModeEndRange int property
     *
     * @access private
     * @var integer
     */
    private $_TracingModeEndRange = null;

    /**
     * _Product::getTracingModeEndRange
     *
     * @access public
     * @return integer
     */
    public function getTracingModeEndRange() {
        return $this->_TracingModeEndRange;
    }

    /**
     * _Product::setTracingModeEndRange
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTracingModeEndRange($value) {
        $this->_TracingModeEndRange = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // SellUnitType foreignkey property + getter/setter {{{

    /**
     * SellUnitType foreignkey
     *
     * @access private
     * @var mixed object SellUnitType or integer
     */
    private $_SellUnitType = false;

    /**
     * _Product::getSellUnitType
     *
     * @access public
     * @return object SellUnitType
     */
    public function getSellUnitType() {
        if (is_int($this->_SellUnitType) && $this->_SellUnitType > 0) {
            $mapper = Mapper::singleton('SellUnitType');
            $this->_SellUnitType = $mapper->load(
                array('Id'=>$this->_SellUnitType));
        }
        return $this->_SellUnitType;
    }

    /**
     * _Product::getSellUnitTypeId
     *
     * @access public
     * @return integer
     */
    public function getSellUnitTypeId() {
        if ($this->_SellUnitType instanceof SellUnitType) {
            return $this->_SellUnitType->getId();
        }
        return (int)$this->_SellUnitType;
    }

    /**
     * _Product::setSellUnitType
     *
     * @access public
     * @param object SellUnitType $value
     * @return void
     */
    public function setSellUnitType($value) {
        if (is_numeric($value)) {
            $this->_SellUnitType = (int)$value;
        } else {
            $this->_SellUnitType = $value;
        }
    }

    // }}}
    // FirstRankParcelNumber string property + getter/setter {{{

    /**
     * FirstRankParcelNumber int property
     *
     * @access private
     * @var integer
     */
    private $_FirstRankParcelNumber = 0;

    /**
     * _Product::getFirstRankParcelNumber
     *
     * @access public
     * @return integer
     */
    public function getFirstRankParcelNumber() {
        return $this->_FirstRankParcelNumber;
    }

    /**
     * _Product::setFirstRankParcelNumber
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setFirstRankParcelNumber($value) {
        if ($value !== null) {
            $this->_FirstRankParcelNumber = (int)$value;
        }
    }

    // }}}
    // SellUnitQuantity float property + getter/setter {{{

    /**
     * SellUnitQuantity float property
     *
     * @access private
     * @var float
     */
    private $_SellUnitQuantity = 0;

    /**
     * _Product::getSellUnitQuantity
     *
     * @access public
     * @return float
     */
    public function getSellUnitQuantity() {
        return $this->_SellUnitQuantity;
    }

    /**
     * _Product::setSellUnitQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setSellUnitQuantity($value) {
        if ($value !== null) {
            $this->_SellUnitQuantity = round(I18N::extractNumber($value), 3);
        }
    }

    // }}}
    // SellUnitVirtualQuantity float property + getter/setter {{{

    /**
     * SellUnitVirtualQuantity float property
     *
     * @access private
     * @var float
     */
    private $_SellUnitVirtualQuantity = 0;

    /**
     * _Product::getSellUnitVirtualQuantity
     *
     * @access public
     * @return float
     */
    public function getSellUnitVirtualQuantity() {
        return $this->_SellUnitVirtualQuantity;
    }

    /**
     * _Product::setSellUnitVirtualQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setSellUnitVirtualQuantity($value) {
        if ($value !== null) {
            $this->_SellUnitVirtualQuantity = round(I18N::extractNumber($value), 3);
        }
    }

    // }}}
    // SellUnitMinimumStoredQuantity float property + getter/setter {{{

    /**
     * SellUnitMinimumStoredQuantity float property
     *
     * @access private
     * @var float
     */
    private $_SellUnitMinimumStoredQuantity = 0;

    /**
     * _Product::getSellUnitMinimumStoredQuantity
     *
     * @access public
     * @return float
     */
    public function getSellUnitMinimumStoredQuantity() {
        return $this->_SellUnitMinimumStoredQuantity;
    }

    /**
     * _Product::setSellUnitMinimumStoredQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setSellUnitMinimumStoredQuantity($value) {
        if ($value !== null) {
            $this->_SellUnitMinimumStoredQuantity = round(I18N::extractNumber($value), 3);
        }
    }

    // }}}
    // SellUnitLength float property + getter/setter {{{

    /**
     * SellUnitLength float property
     *
     * @access private
     * @var float
     */
    private $_SellUnitLength = 0;

    /**
     * _Product::getSellUnitLength
     *
     * @access public
     * @return float
     */
    public function getSellUnitLength() {
        return $this->_SellUnitLength;
    }

    /**
     * _Product::setSellUnitLength
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setSellUnitLength($value) {
        if ($value !== null) {
            $this->_SellUnitLength = I18N::extractNumber($value);
        }
    }

    // }}}
    // SellUnitWidth float property + getter/setter {{{

    /**
     * SellUnitWidth float property
     *
     * @access private
     * @var float
     */
    private $_SellUnitWidth = 0;

    /**
     * _Product::getSellUnitWidth
     *
     * @access public
     * @return float
     */
    public function getSellUnitWidth() {
        return $this->_SellUnitWidth;
    }

    /**
     * _Product::setSellUnitWidth
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setSellUnitWidth($value) {
        if ($value !== null) {
            $this->_SellUnitWidth = I18N::extractNumber($value);
        }
    }

    // }}}
    // SellUnitHeight float property + getter/setter {{{

    /**
     * SellUnitHeight float property
     *
     * @access private
     * @var float
     */
    private $_SellUnitHeight = 0;

    /**
     * _Product::getSellUnitHeight
     *
     * @access public
     * @return float
     */
    public function getSellUnitHeight() {
        return $this->_SellUnitHeight;
    }

    /**
     * _Product::setSellUnitHeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setSellUnitHeight($value) {
        if ($value !== null) {
            $this->_SellUnitHeight = I18N::extractNumber($value);
        }
    }

    // }}}
    // SellUnitWeight float property + getter/setter {{{

    /**
     * SellUnitWeight float property
     *
     * @access private
     * @var float
     */
    private $_SellUnitWeight = 0;

    /**
     * _Product::getSellUnitWeight
     *
     * @access public
     * @return float
     */
    public function getSellUnitWeight() {
        return $this->_SellUnitWeight;
    }

    /**
     * _Product::setSellUnitWeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setSellUnitWeight($value) {
        if ($value !== null) {
            $this->_SellUnitWeight = I18N::extractNumber($value);
        }
    }

    // }}}
    // SellUnitMasterDimension string property + getter/setter {{{

    /**
     * SellUnitMasterDimension int property
     *
     * @access private
     * @var integer
     */
    private $_SellUnitMasterDimension = 0;

    /**
     * _Product::getSellUnitMasterDimension
     *
     * @access public
     * @return integer
     */
    public function getSellUnitMasterDimension() {
        return $this->_SellUnitMasterDimension;
    }

    /**
     * _Product::setSellUnitMasterDimension
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setSellUnitMasterDimension($value) {
        if ($value !== null) {
            $this->_SellUnitMasterDimension = (int)$value;
        }
    }

    // }}}
    // SellUnitGerbability string property + getter/setter {{{

    /**
     * SellUnitGerbability int property
     *
     * @access private
     * @var integer
     */
    private $_SellUnitGerbability = 0;

    /**
     * _Product::getSellUnitGerbability
     *
     * @access public
     * @return integer
     */
    public function getSellUnitGerbability() {
        return $this->_SellUnitGerbability;
    }

    /**
     * _Product::setSellUnitGerbability
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setSellUnitGerbability($value) {
        if ($value !== null) {
            $this->_SellUnitGerbability = (int)$value;
        }
    }

    // }}}
    // SellUnitTypeInContainer foreignkey property + getter/setter {{{

    /**
     * SellUnitTypeInContainer foreignkey
     *
     * @access private
     * @var mixed object SellUnitType or integer
     */
    private $_SellUnitTypeInContainer = false;

    /**
     * _Product::getSellUnitTypeInContainer
     *
     * @access public
     * @return object SellUnitType
     */
    public function getSellUnitTypeInContainer() {
        if (is_int($this->_SellUnitTypeInContainer) && $this->_SellUnitTypeInContainer > 0) {
            $mapper = Mapper::singleton('SellUnitType');
            $this->_SellUnitTypeInContainer = $mapper->load(
                array('Id'=>$this->_SellUnitTypeInContainer));
        }
        return $this->_SellUnitTypeInContainer;
    }

    /**
     * _Product::getSellUnitTypeInContainerId
     *
     * @access public
     * @return integer
     */
    public function getSellUnitTypeInContainerId() {
        if ($this->_SellUnitTypeInContainer instanceof SellUnitType) {
            return $this->_SellUnitTypeInContainer->getId();
        }
        return (int)$this->_SellUnitTypeInContainer;
    }

    /**
     * _Product::setSellUnitTypeInContainer
     *
     * @access public
     * @param object SellUnitType $value
     * @return void
     */
    public function setSellUnitTypeInContainer($value) {
        if (is_numeric($value)) {
            $this->_SellUnitTypeInContainer = (int)$value;
        } else {
            $this->_SellUnitTypeInContainer = $value;
        }
    }

    // }}}
    // Turnable int property + getter/setter {{{

    /**
     * Turnable int property
     *
     * @access private
     * @var integer
     */
    private $_Turnable = null;

    /**
     * _Product::getTurnable
     *
     * @access public
     * @return integer
     */
    public function getTurnable() {
        return $this->_Turnable;
    }

    /**
     * _Product::setTurnable
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTurnable($value) {
        $this->_Turnable = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // TVA foreignkey property + getter/setter {{{

    /**
     * TVA foreignkey
     *
     * @access private
     * @var mixed object TVA or integer
     */
    private $_TVA = false;

    /**
     * _Product::getTVA
     *
     * @access public
     * @return object TVA
     */
    public function getTVA() {
        if (is_int($this->_TVA) && $this->_TVA > 0) {
            $mapper = Mapper::singleton('TVA');
            $this->_TVA = $mapper->load(
                array('Id'=>$this->_TVA));
        }
        return $this->_TVA;
    }

    /**
     * _Product::getTVAId
     *
     * @access public
     * @return integer
     */
    public function getTVAId() {
        if ($this->_TVA instanceof TVA) {
            return $this->_TVA->getId();
        }
        return (int)$this->_TVA;
    }

    /**
     * _Product::setTVA
     *
     * @access public
     * @param object TVA $value
     * @return void
     */
    public function setTVA($value) {
        if (is_numeric($value)) {
            $this->_TVA = (int)$value;
        } else {
            $this->_TVA = $value;
        }
    }

    // }}}
    // ConditioningRecommended foreignkey property + getter/setter {{{

    /**
     * ConditioningRecommended foreignkey
     *
     * @access private
     * @var mixed object Container or integer
     */
    private $_ConditioningRecommended = false;

    /**
     * _Product::getConditioningRecommended
     *
     * @access public
     * @return object Container
     */
    public function getConditioningRecommended() {
        if (is_int($this->_ConditioningRecommended) && $this->_ConditioningRecommended > 0) {
            $mapper = Mapper::singleton('Container');
            $this->_ConditioningRecommended = $mapper->load(
                array('Id'=>$this->_ConditioningRecommended));
        }
        return $this->_ConditioningRecommended;
    }

    /**
     * _Product::getConditioningRecommendedId
     *
     * @access public
     * @return integer
     */
    public function getConditioningRecommendedId() {
        if ($this->_ConditioningRecommended instanceof Container) {
            return $this->_ConditioningRecommended->getId();
        }
        return (int)$this->_ConditioningRecommended;
    }

    /**
     * _Product::setConditioningRecommended
     *
     * @access public
     * @param object Container $value
     * @return void
     */
    public function setConditioningRecommended($value) {
        if (is_numeric($value)) {
            $this->_ConditioningRecommended = (int)$value;
        } else {
            $this->_ConditioningRecommended = $value;
        }
    }

    // }}}
    // UnitNumberInConditioning string property + getter/setter {{{

    /**
     * UnitNumberInConditioning int property
     *
     * @access private
     * @var integer
     */
    private $_UnitNumberInConditioning = 0;

    /**
     * _Product::getUnitNumberInConditioning
     *
     * @access public
     * @return integer
     */
    public function getUnitNumberInConditioning() {
        return $this->_UnitNumberInConditioning;
    }

    /**
     * _Product::setUnitNumberInConditioning
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setUnitNumberInConditioning($value) {
        if ($value !== null) {
            $this->_UnitNumberInConditioning = (int)$value;
        }
    }

    // }}}
    // ConditionedProductReference string property + getter/setter {{{

    /**
     * ConditionedProductReference string property
     *
     * @access private
     * @var string
     */
    private $_ConditionedProductReference = '';

    /**
     * _Product::getConditionedProductReference
     *
     * @access public
     * @return string
     */
    public function getConditionedProductReference() {
        return $this->_ConditionedProductReference;
    }

    /**
     * _Product::setConditionedProductReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setConditionedProductReference($value) {
        $this->_ConditionedProductReference = $value;
    }

    // }}}
    // ConditioningGerbability string property + getter/setter {{{

    /**
     * ConditioningGerbability int property
     *
     * @access private
     * @var integer
     */
    private $_ConditioningGerbability = 0;

    /**
     * _Product::getConditioningGerbability
     *
     * @access public
     * @return integer
     */
    public function getConditioningGerbability() {
        return $this->_ConditioningGerbability;
    }

    /**
     * _Product::setConditioningGerbability
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setConditioningGerbability($value) {
        if ($value !== null) {
            $this->_ConditioningGerbability = (int)$value;
        }
    }

    // }}}
    // ConditioningMasterDimension string property + getter/setter {{{

    /**
     * ConditioningMasterDimension int property
     *
     * @access private
     * @var integer
     */
    private $_ConditioningMasterDimension = 0;

    /**
     * _Product::getConditioningMasterDimension
     *
     * @access public
     * @return integer
     */
    public function getConditioningMasterDimension() {
        return $this->_ConditioningMasterDimension;
    }

    /**
     * _Product::setConditioningMasterDimension
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setConditioningMasterDimension($value) {
        if ($value !== null) {
            $this->_ConditioningMasterDimension = (int)$value;
        }
    }

    // }}}
    // PackagingRecommended foreignkey property + getter/setter {{{

    /**
     * PackagingRecommended foreignkey
     *
     * @access private
     * @var mixed object Container or integer
     */
    private $_PackagingRecommended = false;

    /**
     * _Product::getPackagingRecommended
     *
     * @access public
     * @return object Container
     */
    public function getPackagingRecommended() {
        if (is_int($this->_PackagingRecommended) && $this->_PackagingRecommended > 0) {
            $mapper = Mapper::singleton('Container');
            $this->_PackagingRecommended = $mapper->load(
                array('Id'=>$this->_PackagingRecommended));
        }
        return $this->_PackagingRecommended;
    }

    /**
     * _Product::getPackagingRecommendedId
     *
     * @access public
     * @return integer
     */
    public function getPackagingRecommendedId() {
        if ($this->_PackagingRecommended instanceof Container) {
            return $this->_PackagingRecommended->getId();
        }
        return (int)$this->_PackagingRecommended;
    }

    /**
     * _Product::setPackagingRecommended
     *
     * @access public
     * @param object Container $value
     * @return void
     */
    public function setPackagingRecommended($value) {
        if (is_numeric($value)) {
            $this->_PackagingRecommended = (int)$value;
        } else {
            $this->_PackagingRecommended = $value;
        }
    }

    // }}}
    // UnitNumberInPackaging string property + getter/setter {{{

    /**
     * UnitNumberInPackaging int property
     *
     * @access private
     * @var integer
     */
    private $_UnitNumberInPackaging = 0;

    /**
     * _Product::getUnitNumberInPackaging
     *
     * @access public
     * @return integer
     */
    public function getUnitNumberInPackaging() {
        return $this->_UnitNumberInPackaging;
    }

    /**
     * _Product::setUnitNumberInPackaging
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setUnitNumberInPackaging($value) {
        if ($value !== null) {
            $this->_UnitNumberInPackaging = (int)$value;
        }
    }

    // }}}
    // PackagedProductReference string property + getter/setter {{{

    /**
     * PackagedProductReference string property
     *
     * @access private
     * @var string
     */
    private $_PackagedProductReference = '';

    /**
     * _Product::getPackagedProductReference
     *
     * @access public
     * @return string
     */
    public function getPackagedProductReference() {
        return $this->_PackagedProductReference;
    }

    /**
     * _Product::setPackagedProductReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPackagedProductReference($value) {
        $this->_PackagedProductReference = $value;
    }

    // }}}
    // PackagingGerbability string property + getter/setter {{{

    /**
     * PackagingGerbability int property
     *
     * @access private
     * @var integer
     */
    private $_PackagingGerbability = 0;

    /**
     * _Product::getPackagingGerbability
     *
     * @access public
     * @return integer
     */
    public function getPackagingGerbability() {
        return $this->_PackagingGerbability;
    }

    /**
     * _Product::setPackagingGerbability
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPackagingGerbability($value) {
        if ($value !== null) {
            $this->_PackagingGerbability = (int)$value;
        }
    }

    // }}}
    // PackagingMasterDimension string property + getter/setter {{{

    /**
     * PackagingMasterDimension int property
     *
     * @access private
     * @var integer
     */
    private $_PackagingMasterDimension = 0;

    /**
     * _Product::getPackagingMasterDimension
     *
     * @access public
     * @return integer
     */
    public function getPackagingMasterDimension() {
        return $this->_PackagingMasterDimension;
    }

    /**
     * _Product::setPackagingMasterDimension
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPackagingMasterDimension($value) {
        if ($value !== null) {
            $this->_PackagingMasterDimension = (int)$value;
        }
    }

    // }}}
    // GroupingRecommended foreignkey property + getter/setter {{{

    /**
     * GroupingRecommended foreignkey
     *
     * @access private
     * @var mixed object Container or integer
     */
    private $_GroupingRecommended = false;

    /**
     * _Product::getGroupingRecommended
     *
     * @access public
     * @return object Container
     */
    public function getGroupingRecommended() {
        if (is_int($this->_GroupingRecommended) && $this->_GroupingRecommended > 0) {
            $mapper = Mapper::singleton('Container');
            $this->_GroupingRecommended = $mapper->load(
                array('Id'=>$this->_GroupingRecommended));
        }
        return $this->_GroupingRecommended;
    }

    /**
     * _Product::getGroupingRecommendedId
     *
     * @access public
     * @return integer
     */
    public function getGroupingRecommendedId() {
        if ($this->_GroupingRecommended instanceof Container) {
            return $this->_GroupingRecommended->getId();
        }
        return (int)$this->_GroupingRecommended;
    }

    /**
     * _Product::setGroupingRecommended
     *
     * @access public
     * @param object Container $value
     * @return void
     */
    public function setGroupingRecommended($value) {
        if (is_numeric($value)) {
            $this->_GroupingRecommended = (int)$value;
        } else {
            $this->_GroupingRecommended = $value;
        }
    }

    // }}}
    // UnitNumberInGrouping string property + getter/setter {{{

    /**
     * UnitNumberInGrouping int property
     *
     * @access private
     * @var integer
     */
    private $_UnitNumberInGrouping = 0;

    /**
     * _Product::getUnitNumberInGrouping
     *
     * @access public
     * @return integer
     */
    public function getUnitNumberInGrouping() {
        return $this->_UnitNumberInGrouping;
    }

    /**
     * _Product::setUnitNumberInGrouping
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setUnitNumberInGrouping($value) {
        if ($value !== null) {
            $this->_UnitNumberInGrouping = (int)$value;
        }
    }

    // }}}
    // GroupedProductReference string property + getter/setter {{{

    /**
     * GroupedProductReference string property
     *
     * @access private
     * @var string
     */
    private $_GroupedProductReference = '';

    /**
     * _Product::getGroupedProductReference
     *
     * @access public
     * @return string
     */
    public function getGroupedProductReference() {
        return $this->_GroupedProductReference;
    }

    /**
     * _Product::setGroupedProductReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setGroupedProductReference($value) {
        $this->_GroupedProductReference = $value;
    }

    // }}}
    // GroupingGerbability string property + getter/setter {{{

    /**
     * GroupingGerbability int property
     *
     * @access private
     * @var integer
     */
    private $_GroupingGerbability = 0;

    /**
     * _Product::getGroupingGerbability
     *
     * @access public
     * @return integer
     */
    public function getGroupingGerbability() {
        return $this->_GroupingGerbability;
    }

    /**
     * _Product::setGroupingGerbability
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setGroupingGerbability($value) {
        if ($value !== null) {
            $this->_GroupingGerbability = (int)$value;
        }
    }

    // }}}
    // GroupingMasterDimension string property + getter/setter {{{

    /**
     * GroupingMasterDimension int property
     *
     * @access private
     * @var integer
     */
    private $_GroupingMasterDimension = 0;

    /**
     * _Product::getGroupingMasterDimension
     *
     * @access public
     * @return integer
     */
    public function getGroupingMasterDimension() {
        return $this->_GroupingMasterDimension;
    }

    /**
     * _Product::setGroupingMasterDimension
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setGroupingMasterDimension($value) {
        if ($value !== null) {
            $this->_GroupingMasterDimension = (int)$value;
        }
    }

    // }}}
    // LicenceName string property + getter/setter {{{

    /**
     * LicenceName string property
     *
     * @access private
     * @var string
     */
    private $_LicenceName = '';

    /**
     * _Product::getLicenceName
     *
     * @access public
     * @return string
     */
    public function getLicenceName() {
        return $this->_LicenceName;
    }

    /**
     * _Product::setLicenceName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setLicenceName($value) {
        $this->_LicenceName = $value;
    }

    // }}}
    // LicenceYear int property + getter/setter {{{

    /**
     * LicenceYear int property
     *
     * @access private
     * @var integer
     */
    private $_LicenceYear = null;

    /**
     * _Product::getLicenceYear
     *
     * @access public
     * @return integer
     */
    public function getLicenceYear() {
        return $this->_LicenceYear;
    }

    /**
     * _Product::setLicenceYear
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setLicenceYear($value) {
        $this->_LicenceYear = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // Location one to many relation + getter/setter {{{

    /**
     * Location *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LocationCollection = false;

    /**
     * _Product::getLocationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLocationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getManyToMany($this->getId(),
                'Location', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LocationCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_LocationCollection = $mapper->getManyToMany($this->getId(),
                'Location');
        }
        return $this->_LocationCollection;
    }

    /**
     * _Product::getLocationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLocationCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getLocationCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_LocationCollection) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getManyToManyIds($this->getId(), 'Location');
        }
        return $this->_LocationCollection->getItemIds();
    }

    /**
     * _Product::setLocationCollectionIds
     *
     * @access public
     * @return array
     */
    public function setLocationCollectionIds($itemIds) {
        $this->_LocationCollection = new Collection('Location');
        foreach ($itemIds as $id) {
            $this->_LocationCollection->setItem($id);
        }
    }

    /**
     * _Product::setLocationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLocationCollection($value) {
        $this->_LocationCollection = $value;
    }

    /**
     * _Product::LocationCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function LocationCollectionIsLoaded() {
        return ($this->_LocationCollection !== false);
    }

    // }}}
    // ActivatedChain one to many relation + getter/setter {{{

    /**
     * ActivatedChain *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActivatedChainCollection = false;

    /**
     * _Product::getActivatedChainCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedChainCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getManyToMany($this->getId(),
                'ActivatedChain', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedChainCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_ActivatedChainCollection = $mapper->getManyToMany($this->getId(),
                'ActivatedChain');
        }
        return $this->_ActivatedChainCollection;
    }

    /**
     * _Product::getActivatedChainCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActivatedChainCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getActivatedChainCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ActivatedChainCollection) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getManyToManyIds($this->getId(), 'ActivatedChain');
        }
        return $this->_ActivatedChainCollection->getItemIds();
    }

    /**
     * _Product::setActivatedChainCollectionIds
     *
     * @access public
     * @return array
     */
    public function setActivatedChainCollectionIds($itemIds) {
        $this->_ActivatedChainCollection = new Collection('ActivatedChain');
        foreach ($itemIds as $id) {
            $this->_ActivatedChainCollection->setItem($id);
        }
    }

    /**
     * _Product::setActivatedChainCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedChainCollection($value) {
        $this->_ActivatedChainCollection = $value;
    }

    /**
     * _Product::ActivatedChainCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ActivatedChainCollectionIsLoaded() {
        return ($this->_ActivatedChainCollection !== false);
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
     * _Product::getDescription
     *
     * @access public
     * @return string
     */
    public function getDescription() {
        return $this->_Description;
    }

    /**
     * _Product::setDescription
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDescription($value) {
        $this->_Description = $value;
    }

    // }}}
    // Image string property + getter/setter {{{

    /**
     * Image string property
     *
     * @access private
     * @var string
     */
    private $_Image = '';

    /**
     * _Product::getImage
     *
     * @access public
     * @return string
     */
    public function getImage() {
        return $this->_Image;
    }

    /**
     * _Product::setImage
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setImage($value) {
        $this->_Image = $value;
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
     * _Product::getOwner
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
     * _Product::getOwnerId
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
     * _Product::setOwner
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
    // ActorProduct one to many relation + getter/setter {{{

    /**
     * ActorProduct 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActorProductCollection = false;

    /**
     * _Product::getActorProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActorProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getOneToMany($this->getId(),
                'ActorProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActorProductCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_ActorProductCollection = $mapper->getOneToMany($this->getId(),
                'ActorProduct');
        }
        return $this->_ActorProductCollection;
    }

    /**
     * _Product::getActorProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActorProductCollectionIds($filter = array()) {
        $col = $this->getActorProductCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Product::setActorProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActorProductCollection($value) {
        $this->_ActorProductCollection = $value;
    }

    // }}}
    // Component one to many relation + getter/setter {{{

    /**
     * Component 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ComponentCollection = false;

    /**
     * _Product::getComponentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getComponentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getOneToMany($this->getId(),
                'Component', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ComponentCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_ComponentCollection = $mapper->getOneToMany($this->getId(),
                'Component');
        }
        return $this->_ComponentCollection;
    }

    /**
     * _Product::getComponentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getComponentCollectionIds($filter = array()) {
        $col = $this->getComponentCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Product::setComponentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setComponentCollection($value) {
        $this->_ComponentCollection = $value;
    }

    // }}}
    // ConcreteProduct one to many relation + getter/setter {{{

    /**
     * ConcreteProduct 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ConcreteProductCollection = false;

    /**
     * _Product::getConcreteProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getConcreteProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getOneToMany($this->getId(),
                'ConcreteProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ConcreteProductCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_ConcreteProductCollection = $mapper->getOneToMany($this->getId(),
                'ConcreteProduct');
        }
        return $this->_ConcreteProductCollection;
    }

    /**
     * _Product::getConcreteProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getConcreteProductCollectionIds($filter = array()) {
        $col = $this->getConcreteProductCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Product::setConcreteProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setConcreteProductCollection($value) {
        $this->_ConcreteProductCollection = $value;
    }

    // }}}
    // LocationProductQuantities one to many relation + getter/setter {{{

    /**
     * LocationProductQuantities 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LocationProductQuantitiesCollection = false;

    /**
     * _Product::getLocationProductQuantitiesCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLocationProductQuantitiesCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getOneToMany($this->getId(),
                'LocationProductQuantities', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LocationProductQuantitiesCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_LocationProductQuantitiesCollection = $mapper->getOneToMany($this->getId(),
                'LocationProductQuantities');
        }
        return $this->_LocationProductQuantitiesCollection;
    }

    /**
     * _Product::getLocationProductQuantitiesCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLocationProductQuantitiesCollectionIds($filter = array()) {
        $col = $this->getLocationProductQuantitiesCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Product::setLocationProductQuantitiesCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLocationProductQuantitiesCollection($value) {
        $this->_LocationProductQuantitiesCollection = $value;
    }

    // }}}
    // OccupiedLocation one to many relation + getter/setter {{{

    /**
     * OccupiedLocation 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_OccupiedLocationCollection = false;

    /**
     * _Product::getOccupiedLocationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getOccupiedLocationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getOneToMany($this->getId(),
                'OccupiedLocation', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_OccupiedLocationCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_OccupiedLocationCollection = $mapper->getOneToMany($this->getId(),
                'OccupiedLocation');
        }
        return $this->_OccupiedLocationCollection;
    }

    /**
     * _Product::getOccupiedLocationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getOccupiedLocationCollectionIds($filter = array()) {
        $col = $this->getOccupiedLocationCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Product::setOccupiedLocationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setOccupiedLocationCollection($value) {
        $this->_OccupiedLocationCollection = $value;
    }

    // }}}
    // PriceByCurrency one to many relation + getter/setter {{{

    /**
     * PriceByCurrency 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_PriceByCurrencyCollection = false;

    /**
     * _Product::getPriceByCurrencyCollection
     *
     * @access public
     * @return object Collection
     */
    public function getPriceByCurrencyCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getOneToMany($this->getId(),
                'PriceByCurrency', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_PriceByCurrencyCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_PriceByCurrencyCollection = $mapper->getOneToMany($this->getId(),
                'PriceByCurrency');
        }
        return $this->_PriceByCurrencyCollection;
    }

    /**
     * _Product::getPriceByCurrencyCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getPriceByCurrencyCollectionIds($filter = array()) {
        $col = $this->getPriceByCurrencyCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Product::setPriceByCurrencyCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setPriceByCurrencyCollection($value) {
        $this->_PriceByCurrencyCollection = $value;
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
     * _Product::getProductChainLinkCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductChainLinkCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getOneToMany($this->getId(),
                'ProductChainLink', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductChainLinkCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_ProductChainLinkCollection = $mapper->getOneToMany($this->getId(),
                'ProductChainLink');
        }
        return $this->_ProductChainLinkCollection;
    }

    /**
     * _Product::getProductChainLinkCollectionIds
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
     * _Product::setProductChainLinkCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductChainLinkCollection($value) {
        $this->_ProductChainLinkCollection = $value;
    }

    // }}}
    // ProductCommandItem one to many relation + getter/setter {{{

    /**
     * ProductCommandItem 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductCommandItemCollection = false;

    /**
     * _Product::getProductCommandItemCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductCommandItemCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getOneToMany($this->getId(),
                'ProductCommandItem', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductCommandItemCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_ProductCommandItemCollection = $mapper->getOneToMany($this->getId(),
                'ProductCommandItem');
        }
        return $this->_ProductCommandItemCollection;
    }

    /**
     * _Product::getProductCommandItemCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductCommandItemCollectionIds($filter = array()) {
        $col = $this->getProductCommandItemCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Product::setProductCommandItemCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductCommandItemCollection($value) {
        $this->_ProductCommandItemCollection = $value;
    }

    // }}}
    // ProductQuantityByCategory one to many relation + getter/setter {{{

    /**
     * ProductQuantityByCategory 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductQuantityByCategoryCollection = false;

    /**
     * _Product::getProductQuantityByCategoryCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductQuantityByCategoryCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getOneToMany($this->getId(),
                'ProductQuantityByCategory', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductQuantityByCategoryCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_ProductQuantityByCategoryCollection = $mapper->getOneToMany($this->getId(),
                'ProductQuantityByCategory');
        }
        return $this->_ProductQuantityByCategoryCollection;
    }

    /**
     * _Product::getProductQuantityByCategoryCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductQuantityByCategoryCollectionIds($filter = array()) {
        $col = $this->getProductQuantityByCategoryCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Product::setProductQuantityByCategoryCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductQuantityByCategoryCollection($value) {
        $this->_ProductQuantityByCategoryCollection = $value;
    }

    // }}}
    // ProductSubstitution one to many relation + getter/setter {{{

    /**
     * ProductSubstitution 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductSubstitutionCollection = false;

    /**
     * _Product::getProductSubstitutionCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductSubstitutionCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getOneToMany($this->getId(),
                'ProductSubstitution', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductSubstitutionCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_ProductSubstitutionCollection = $mapper->getOneToMany($this->getId(),
                'ProductSubstitution');
        }
        return $this->_ProductSubstitutionCollection;
    }

    /**
     * _Product::getProductSubstitutionCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductSubstitutionCollectionIds($filter = array()) {
        $col = $this->getProductSubstitutionCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Product::setProductSubstitutionCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductSubstitutionCollection($value) {
        $this->_ProductSubstitutionCollection = $value;
    }

    // }}}
    // PropertyValue one to many relation + getter/setter {{{

    /**
     * PropertyValue 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_PropertyValueCollection = false;

    /**
     * _Product::getPropertyValueCollection
     *
     * @access public
     * @return object Collection
     */
    public function getPropertyValueCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Product');
            return $mapper->getOneToMany($this->getId(),
                'PropertyValue', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_PropertyValueCollection) {
            $mapper = Mapper::singleton('Product');
            $this->_PropertyValueCollection = $mapper->getOneToMany($this->getId(),
                'PropertyValue');
        }
        return $this->_PropertyValueCollection;
    }

    /**
     * _Product::getPropertyValueCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getPropertyValueCollectionIds($filter = array()) {
        $col = $this->getPropertyValueCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Product::setPropertyValueCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setPropertyValueCollection($value) {
        $this->_PropertyValueCollection = $value;
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
        return 'Product';
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
            'Name' => Object::TYPE_I18N_STRING,
            'BaseReference' => Object::TYPE_STRING,
            'Volume' => Object::TYPE_FLOAT,
            'CustomsNaming' => Object::TYPE_STRING,
            'Category' => Object::TYPE_INT,
            'Activated' => Object::TYPE_BOOL,
            'Affected' => Object::TYPE_BOOL,
            'ProductType' => 'ProductType',
            'TracingMode' => Object::TYPE_CONST,
            'TracingModeBeginRange' => Object::TYPE_INT,
            'TracingModeEndRange' => Object::TYPE_INT,
            'SellUnitType' => 'SellUnitType',
            'FirstRankParcelNumber' => Object::TYPE_INT,
            'SellUnitQuantity' => Object::TYPE_DECIMAL,
            'SellUnitVirtualQuantity' => Object::TYPE_DECIMAL,
            'SellUnitMinimumStoredQuantity' => Object::TYPE_DECIMAL,
            'SellUnitLength' => Object::TYPE_FLOAT,
            'SellUnitWidth' => Object::TYPE_FLOAT,
            'SellUnitHeight' => Object::TYPE_FLOAT,
            'SellUnitWeight' => Object::TYPE_FLOAT,
            'SellUnitMasterDimension' => Object::TYPE_INT,
            'SellUnitGerbability' => Object::TYPE_INT,
            'SellUnitTypeInContainer' => 'SellUnitType',
            'Turnable' => Object::TYPE_INT,
            'TVA' => 'TVA',
            'ConditioningRecommended' => 'Container',
            'UnitNumberInConditioning' => Object::TYPE_INT,
            'ConditionedProductReference' => Object::TYPE_STRING,
            'ConditioningGerbability' => Object::TYPE_INT,
            'ConditioningMasterDimension' => Object::TYPE_INT,
            'PackagingRecommended' => 'Container',
            'UnitNumberInPackaging' => Object::TYPE_INT,
            'PackagedProductReference' => Object::TYPE_STRING,
            'PackagingGerbability' => Object::TYPE_INT,
            'PackagingMasterDimension' => Object::TYPE_INT,
            'GroupingRecommended' => 'Container',
            'UnitNumberInGrouping' => Object::TYPE_INT,
            'GroupedProductReference' => Object::TYPE_STRING,
            'GroupingGerbability' => Object::TYPE_INT,
            'GroupingMasterDimension' => Object::TYPE_INT,
            'LicenceName' => Object::TYPE_STRING,
            'LicenceYear' => Object::TYPE_INT,
            'Description' => Object::TYPE_STRING,
            'Image' => Object::TYPE_STRING,
            'Owner' => 'Actor');
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
            'Location'=>array(
                'linkClass'     => 'Location',
                'field'         => 'ToProduct',
                'linkTable'     => 'locProduct',
                'linkField'     => 'FromLocation',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ActivatedChain'=>array(
                'linkClass'     => 'ActivatedChain',
                'field'         => 'ToProduct',
                'linkTable'     => 'achProduct',
                'linkField'     => 'FromActivatedChain',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ActivatedMovement'=>array(
                'linkClass'     => 'ActivatedMovement',
                'field'         => 'Product',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActorProduct'=>array(
                'linkClass'     => 'ActorProduct',
                'field'         => 'Product',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'Component'=>array(
                'linkClass'     => 'Component',
                'field'         => 'Product',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ConcreteProduct'=>array(
                'linkClass'     => 'ConcreteProduct',
                'field'         => 'Product',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ExecutedMovement'=>array(
                'linkClass'     => 'ExecutedMovement',
                'field'         => 'RealProduct',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ForwardingFormPacking'=>array(
                'linkClass'     => 'ForwardingFormPacking',
                'field'         => 'Product',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'InventoryDetail'=>array(
                'linkClass'     => 'InventoryDetail',
                'field'         => 'Product',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'LocationExecutedMovement'=>array(
                'linkClass'     => 'LocationExecutedMovement',
                'field'         => 'Product',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'LocationProductQuantities'=>array(
                'linkClass'     => 'LocationProductQuantities',
                'field'         => 'Product',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'Nomenclature'=>array(
                'linkClass'     => 'Nomenclature',
                'field'         => 'Product',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'OccupiedLocation'=>array(
                'linkClass'     => 'OccupiedLocation',
                'field'         => 'Product',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'PriceByCurrency'=>array(
                'linkClass'     => 'PriceByCurrency',
                'field'         => 'Product',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ProductChainLink'=>array(
                'linkClass'     => 'ProductChainLink',
                'field'         => 'Product',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ProductCommandItem'=>array(
                'linkClass'     => 'ProductCommandItem',
                'field'         => 'Product',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ProductHandingByCategory'=>array(
                'linkClass'     => 'ProductHandingByCategory',
                'field'         => 'Product',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ProductQuantityByCategory'=>array(
                'linkClass'     => 'ProductQuantityByCategory',
                'field'         => 'Product',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ProductSubstitution'=>array(
                'linkClass'     => 'ProductSubstitution',
                'field'         => 'FromProduct',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ProductSubstitution_1'=>array(
                'linkClass'     => 'ProductSubstitution',
                'field'         => 'ByProduct',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'PropertyValue'=>array(
                'linkClass'     => 'PropertyValue',
                'field'         => 'Product',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'Ressource'=>array(
                'linkClass'     => 'Ressource',
                'field'         => 'Product',
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
        $return = array('BaseReference');
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
    // _Product::mutate() {{{

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
        if(!($mutant instanceof _Product)) {
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
}

?>