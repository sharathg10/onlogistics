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
 * _RTWModel class
 *
 */
class _RTWModel extends Object {
    
    // Constructeur {{{

    /**
     * _RTWModel::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Season foreignkey property + getter/setter {{{

    /**
     * Season foreignkey
     *
     * @access private
     * @var mixed object RTWSeason or integer
     */
    private $_Season = false;

    /**
     * _RTWModel::getSeason
     *
     * @access public
     * @return object RTWSeason
     */
    public function getSeason() {
        if (is_int($this->_Season) && $this->_Season > 0) {
            $mapper = Mapper::singleton('RTWSeason');
            $this->_Season = $mapper->load(
                array('Id'=>$this->_Season));
        }
        return $this->_Season;
    }

    /**
     * _RTWModel::getSeasonId
     *
     * @access public
     * @return integer
     */
    public function getSeasonId() {
        if ($this->_Season instanceof RTWSeason) {
            return $this->_Season->getId();
        }
        return (int)$this->_Season;
    }

    /**
     * _RTWModel::setSeason
     *
     * @access public
     * @param object RTWSeason $value
     * @return void
     */
    public function setSeason($value) {
        if (is_numeric($value)) {
            $this->_Season = (int)$value;
        } else {
            $this->_Season = $value;
        }
    }

    // }}}
    // Shape foreignkey property + getter/setter {{{

    /**
     * Shape foreignkey
     *
     * @access private
     * @var mixed object RTWShape or integer
     */
    private $_Shape = false;

    /**
     * _RTWModel::getShape
     *
     * @access public
     * @return object RTWShape
     */
    public function getShape() {
        if (is_int($this->_Shape) && $this->_Shape > 0) {
            $mapper = Mapper::singleton('RTWShape');
            $this->_Shape = $mapper->load(
                array('Id'=>$this->_Shape));
        }
        return $this->_Shape;
    }

    /**
     * _RTWModel::getShapeId
     *
     * @access public
     * @return integer
     */
    public function getShapeId() {
        if ($this->_Shape instanceof RTWShape) {
            return $this->_Shape->getId();
        }
        return (int)$this->_Shape;
    }

    /**
     * _RTWModel::setShape
     *
     * @access public
     * @param object RTWShape $value
     * @return void
     */
    public function setShape($value) {
        if (is_numeric($value)) {
            $this->_Shape = (int)$value;
        } else {
            $this->_Shape = $value;
        }
    }

    // }}}
    // PressName foreignkey property + getter/setter {{{

    /**
     * PressName foreignkey
     *
     * @access private
     * @var mixed object RTWPressName or integer
     */
    private $_PressName = false;

    /**
     * _RTWModel::getPressName
     *
     * @access public
     * @return object RTWPressName
     */
    public function getPressName() {
        if (is_int($this->_PressName) && $this->_PressName > 0) {
            $mapper = Mapper::singleton('RTWPressName');
            $this->_PressName = $mapper->load(
                array('Id'=>$this->_PressName));
        }
        return $this->_PressName;
    }

    /**
     * _RTWModel::getPressNameId
     *
     * @access public
     * @return integer
     */
    public function getPressNameId() {
        if ($this->_PressName instanceof RTWPressName) {
            return $this->_PressName->getId();
        }
        return (int)$this->_PressName;
    }

    /**
     * _RTWModel::setPressName
     *
     * @access public
     * @param object RTWPressName $value
     * @return void
     */
    public function setPressName($value) {
        if (is_numeric($value)) {
            $this->_PressName = (int)$value;
        } else {
            $this->_PressName = $value;
        }
    }

    // }}}
    // StyleNumber string property + getter/setter {{{

    /**
     * StyleNumber string property
     *
     * @access private
     * @var string
     */
    private $_StyleNumber = '';

    /**
     * _RTWModel::getStyleNumber
     *
     * @access public
     * @return string
     */
    public function getStyleNumber() {
        return $this->_StyleNumber;
    }

    /**
     * _RTWModel::setStyleNumber
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStyleNumber($value) {
        $this->_StyleNumber = $value;
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
     * _RTWModel::getDescription
     *
     * @access public
     * @return string
     */
    public function getDescription() {
        return $this->_Description;
    }

    /**
     * _RTWModel::setDescription
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDescription($value) {
        $this->_Description = $value;
    }

    // }}}
    // Manufacturer foreignkey property + getter/setter {{{

    /**
     * Manufacturer foreignkey
     *
     * @access private
     * @var mixed object Supplier or integer
     */
    private $_Manufacturer = false;

    /**
     * _RTWModel::getManufacturer
     *
     * @access public
     * @return object Supplier
     */
    public function getManufacturer() {
        if (is_int($this->_Manufacturer) && $this->_Manufacturer > 0) {
            $mapper = Mapper::singleton('Supplier');
            $this->_Manufacturer = $mapper->load(
                array('Id'=>$this->_Manufacturer));
        }
        return $this->_Manufacturer;
    }

    /**
     * _RTWModel::getManufacturerId
     *
     * @access public
     * @return integer
     */
    public function getManufacturerId() {
        if ($this->_Manufacturer instanceof Supplier) {
            return $this->_Manufacturer->getId();
        }
        return (int)$this->_Manufacturer;
    }

    /**
     * _RTWModel::setManufacturer
     *
     * @access public
     * @param object Supplier $value
     * @return void
     */
    public function setManufacturer($value) {
        if (is_numeric($value)) {
            $this->_Manufacturer = (int)$value;
        } else {
            $this->_Manufacturer = $value;
        }
    }

    // }}}
    // ConstructionType foreignkey property + getter/setter {{{

    /**
     * ConstructionType foreignkey
     *
     * @access private
     * @var mixed object RTWConstructionType or integer
     */
    private $_ConstructionType = false;

    /**
     * _RTWModel::getConstructionType
     *
     * @access public
     * @return object RTWConstructionType
     */
    public function getConstructionType() {
        if (is_int($this->_ConstructionType) && $this->_ConstructionType > 0) {
            $mapper = Mapper::singleton('RTWConstructionType');
            $this->_ConstructionType = $mapper->load(
                array('Id'=>$this->_ConstructionType));
        }
        return $this->_ConstructionType;
    }

    /**
     * _RTWModel::getConstructionTypeId
     *
     * @access public
     * @return integer
     */
    public function getConstructionTypeId() {
        if ($this->_ConstructionType instanceof RTWConstructionType) {
            return $this->_ConstructionType->getId();
        }
        return (int)$this->_ConstructionType;
    }

    /**
     * _RTWModel::setConstructionType
     *
     * @access public
     * @param object RTWConstructionType $value
     * @return void
     */
    public function setConstructionType($value) {
        if (is_numeric($value)) {
            $this->_ConstructionType = (int)$value;
        } else {
            $this->_ConstructionType = $value;
        }
    }

    // }}}
    // ConstructionCode foreignkey property + getter/setter {{{

    /**
     * ConstructionCode foreignkey
     *
     * @access private
     * @var mixed object RTWConstructionCode or integer
     */
    private $_ConstructionCode = false;

    /**
     * _RTWModel::getConstructionCode
     *
     * @access public
     * @return object RTWConstructionCode
     */
    public function getConstructionCode() {
        if (is_int($this->_ConstructionCode) && $this->_ConstructionCode > 0) {
            $mapper = Mapper::singleton('RTWConstructionCode');
            $this->_ConstructionCode = $mapper->load(
                array('Id'=>$this->_ConstructionCode));
        }
        return $this->_ConstructionCode;
    }

    /**
     * _RTWModel::getConstructionCodeId
     *
     * @access public
     * @return integer
     */
    public function getConstructionCodeId() {
        if ($this->_ConstructionCode instanceof RTWConstructionCode) {
            return $this->_ConstructionCode->getId();
        }
        return (int)$this->_ConstructionCode;
    }

    /**
     * _RTWModel::setConstructionCode
     *
     * @access public
     * @param object RTWConstructionCode $value
     * @return void
     */
    public function setConstructionCode($value) {
        if (is_numeric($value)) {
            $this->_ConstructionCode = (int)$value;
        } else {
            $this->_ConstructionCode = $value;
        }
    }

    // }}}
    // Label foreignkey property + getter/setter {{{

    /**
     * Label foreignkey
     *
     * @access private
     * @var mixed object RTWLabel or integer
     */
    private $_Label = false;

    /**
     * _RTWModel::getLabel
     *
     * @access public
     * @return object RTWLabel
     */
    public function getLabel() {
        if (is_int($this->_Label) && $this->_Label > 0) {
            $mapper = Mapper::singleton('RTWLabel');
            $this->_Label = $mapper->load(
                array('Id'=>$this->_Label));
        }
        return $this->_Label;
    }

    /**
     * _RTWModel::getLabelId
     *
     * @access public
     * @return integer
     */
    public function getLabelId() {
        if ($this->_Label instanceof RTWLabel) {
            return $this->_Label->getId();
        }
        return (int)$this->_Label;
    }

    /**
     * _RTWModel::setLabel
     *
     * @access public
     * @param object RTWLabel $value
     * @return void
     */
    public function setLabel($value) {
        if (is_numeric($value)) {
            $this->_Label = (int)$value;
        } else {
            $this->_Label = $value;
        }
    }

    // }}}
    // HeelHeight foreignkey property + getter/setter {{{

    /**
     * HeelHeight foreignkey
     *
     * @access private
     * @var mixed object RTWHeelHeight or integer
     */
    private $_HeelHeight = false;

    /**
     * _RTWModel::getHeelHeight
     *
     * @access public
     * @return object RTWHeelHeight
     */
    public function getHeelHeight() {
        if (is_int($this->_HeelHeight) && $this->_HeelHeight > 0) {
            $mapper = Mapper::singleton('RTWHeelHeight');
            $this->_HeelHeight = $mapper->load(
                array('Id'=>$this->_HeelHeight));
        }
        return $this->_HeelHeight;
    }

    /**
     * _RTWModel::getHeelHeightId
     *
     * @access public
     * @return integer
     */
    public function getHeelHeightId() {
        if ($this->_HeelHeight instanceof RTWHeelHeight) {
            return $this->_HeelHeight->getId();
        }
        return (int)$this->_HeelHeight;
    }

    /**
     * _RTWModel::setHeelHeight
     *
     * @access public
     * @param object RTWHeelHeight $value
     * @return void
     */
    public function setHeelHeight($value) {
        if (is_numeric($value)) {
            $this->_HeelHeight = (int)$value;
        } else {
            $this->_HeelHeight = $value;
        }
    }

    // }}}
    // HeelReference foreignkey property + getter/setter {{{

    /**
     * HeelReference foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_HeelReference = false;

    /**
     * _RTWModel::getHeelReference
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getHeelReference() {
        if (is_int($this->_HeelReference) && $this->_HeelReference > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_HeelReference = $mapper->load(
                array('Id'=>$this->_HeelReference));
        }
        return $this->_HeelReference;
    }

    /**
     * _RTWModel::getHeelReferenceId
     *
     * @access public
     * @return integer
     */
    public function getHeelReferenceId() {
        if ($this->_HeelReference instanceof RTWMaterial) {
            return $this->_HeelReference->getId();
        }
        return (int)$this->_HeelReference;
    }

    /**
     * _RTWModel::setHeelReference
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setHeelReference($value) {
        if (is_numeric($value)) {
            $this->_HeelReference = (int)$value;
        } else {
            $this->_HeelReference = $value;
        }
    }

    // }}}
    // HeelReferenceQuantity float property + getter/setter {{{

    /**
     * HeelReferenceQuantity float property
     *
     * @access private
     * @var float
     */
    private $_HeelReferenceQuantity = null;

    /**
     * _RTWModel::getHeelReferenceQuantity
     *
     * @access public
     * @return float
     */
    public function getHeelReferenceQuantity() {
        return $this->_HeelReferenceQuantity;
    }

    /**
     * _RTWModel::setHeelReferenceQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setHeelReferenceQuantity($value) {
        $this->_HeelReferenceQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // HeelReferenceNomenclature string property + getter/setter {{{

    /**
     * HeelReferenceNomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_HeelReferenceNomenclature = 1;

    /**
     * _RTWModel::getHeelReferenceNomenclature
     *
     * @access public
     * @return integer
     */
    public function getHeelReferenceNomenclature() {
        return $this->_HeelReferenceNomenclature;
    }

    /**
     * _RTWModel::setHeelReferenceNomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setHeelReferenceNomenclature($value) {
        if ($value !== null) {
            $this->_HeelReferenceNomenclature = (int)$value;
        }
    }

    // }}}
    // Sole foreignkey property + getter/setter {{{

    /**
     * Sole foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Sole = false;

    /**
     * _RTWModel::getSole
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getSole() {
        if (is_int($this->_Sole) && $this->_Sole > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Sole = $mapper->load(
                array('Id'=>$this->_Sole));
        }
        return $this->_Sole;
    }

    /**
     * _RTWModel::getSoleId
     *
     * @access public
     * @return integer
     */
    public function getSoleId() {
        if ($this->_Sole instanceof RTWMaterial) {
            return $this->_Sole->getId();
        }
        return (int)$this->_Sole;
    }

    /**
     * _RTWModel::setSole
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setSole($value) {
        if (is_numeric($value)) {
            $this->_Sole = (int)$value;
        } else {
            $this->_Sole = $value;
        }
    }

    // }}}
    // SoleQuantity float property + getter/setter {{{

    /**
     * SoleQuantity float property
     *
     * @access private
     * @var float
     */
    private $_SoleQuantity = null;

    /**
     * _RTWModel::getSoleQuantity
     *
     * @access public
     * @return float
     */
    public function getSoleQuantity() {
        return $this->_SoleQuantity;
    }

    /**
     * _RTWModel::setSoleQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setSoleQuantity($value) {
        $this->_SoleQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // SoleNomenclature string property + getter/setter {{{

    /**
     * SoleNomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_SoleNomenclature = 1;

    /**
     * _RTWModel::getSoleNomenclature
     *
     * @access public
     * @return integer
     */
    public function getSoleNomenclature() {
        return $this->_SoleNomenclature;
    }

    /**
     * _RTWModel::setSoleNomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setSoleNomenclature($value) {
        if ($value !== null) {
            $this->_SoleNomenclature = (int)$value;
        }
    }

    // }}}
    // Box foreignkey property + getter/setter {{{

    /**
     * Box foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Box = false;

    /**
     * _RTWModel::getBox
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getBox() {
        if (is_int($this->_Box) && $this->_Box > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Box = $mapper->load(
                array('Id'=>$this->_Box));
        }
        return $this->_Box;
    }

    /**
     * _RTWModel::getBoxId
     *
     * @access public
     * @return integer
     */
    public function getBoxId() {
        if ($this->_Box instanceof RTWMaterial) {
            return $this->_Box->getId();
        }
        return (int)$this->_Box;
    }

    /**
     * _RTWModel::setBox
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setBox($value) {
        if (is_numeric($value)) {
            $this->_Box = (int)$value;
        } else {
            $this->_Box = $value;
        }
    }

    // }}}
    // BoxQuantity float property + getter/setter {{{

    /**
     * BoxQuantity float property
     *
     * @access private
     * @var float
     */
    private $_BoxQuantity = null;

    /**
     * _RTWModel::getBoxQuantity
     *
     * @access public
     * @return float
     */
    public function getBoxQuantity() {
        return $this->_BoxQuantity;
    }

    /**
     * _RTWModel::setBoxQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setBoxQuantity($value) {
        $this->_BoxQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // BoxNomenclature string property + getter/setter {{{

    /**
     * BoxNomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_BoxNomenclature = 1;

    /**
     * _RTWModel::getBoxNomenclature
     *
     * @access public
     * @return integer
     */
    public function getBoxNomenclature() {
        return $this->_BoxNomenclature;
    }

    /**
     * _RTWModel::setBoxNomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setBoxNomenclature($value) {
        if ($value !== null) {
            $this->_BoxNomenclature = (int)$value;
        }
    }

    // }}}
    // HandBag foreignkey property + getter/setter {{{

    /**
     * HandBag foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_HandBag = false;

    /**
     * _RTWModel::getHandBag
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getHandBag() {
        if (is_int($this->_HandBag) && $this->_HandBag > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_HandBag = $mapper->load(
                array('Id'=>$this->_HandBag));
        }
        return $this->_HandBag;
    }

    /**
     * _RTWModel::getHandBagId
     *
     * @access public
     * @return integer
     */
    public function getHandBagId() {
        if ($this->_HandBag instanceof RTWMaterial) {
            return $this->_HandBag->getId();
        }
        return (int)$this->_HandBag;
    }

    /**
     * _RTWModel::setHandBag
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setHandBag($value) {
        if (is_numeric($value)) {
            $this->_HandBag = (int)$value;
        } else {
            $this->_HandBag = $value;
        }
    }

    // }}}
    // HandBagQuantity float property + getter/setter {{{

    /**
     * HandBagQuantity float property
     *
     * @access private
     * @var float
     */
    private $_HandBagQuantity = null;

    /**
     * _RTWModel::getHandBagQuantity
     *
     * @access public
     * @return float
     */
    public function getHandBagQuantity() {
        return $this->_HandBagQuantity;
    }

    /**
     * _RTWModel::setHandBagQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setHandBagQuantity($value) {
        $this->_HandBagQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // HandBagNomenclature string property + getter/setter {{{

    /**
     * HandBagNomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_HandBagNomenclature = 1;

    /**
     * _RTWModel::getHandBagNomenclature
     *
     * @access public
     * @return integer
     */
    public function getHandBagNomenclature() {
        return $this->_HandBagNomenclature;
    }

    /**
     * _RTWModel::setHandBagNomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setHandBagNomenclature($value) {
        if ($value !== null) {
            $this->_HandBagNomenclature = (int)$value;
        }
    }

    // }}}
    // Material1 foreignkey property + getter/setter {{{

    /**
     * Material1 foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Material1 = false;

    /**
     * _RTWModel::getMaterial1
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getMaterial1() {
        if (is_int($this->_Material1) && $this->_Material1 > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Material1 = $mapper->load(
                array('Id'=>$this->_Material1));
        }
        return $this->_Material1;
    }

    /**
     * _RTWModel::getMaterial1Id
     *
     * @access public
     * @return integer
     */
    public function getMaterial1Id() {
        if ($this->_Material1 instanceof RTWMaterial) {
            return $this->_Material1->getId();
        }
        return (int)$this->_Material1;
    }

    /**
     * _RTWModel::setMaterial1
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setMaterial1($value) {
        if (is_numeric($value)) {
            $this->_Material1 = (int)$value;
        } else {
            $this->_Material1 = $value;
        }
    }

    // }}}
    // Material1Quantity float property + getter/setter {{{

    /**
     * Material1Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Material1Quantity = null;

    /**
     * _RTWModel::getMaterial1Quantity
     *
     * @access public
     * @return float
     */
    public function getMaterial1Quantity() {
        return $this->_Material1Quantity;
    }

    /**
     * _RTWModel::setMaterial1Quantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaterial1Quantity($value) {
        $this->_Material1Quantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // Material1Nomenclature string property + getter/setter {{{

    /**
     * Material1Nomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_Material1Nomenclature = 1;

    /**
     * _RTWModel::getMaterial1Nomenclature
     *
     * @access public
     * @return integer
     */
    public function getMaterial1Nomenclature() {
        return $this->_Material1Nomenclature;
    }

    /**
     * _RTWModel::setMaterial1Nomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setMaterial1Nomenclature($value) {
        if ($value !== null) {
            $this->_Material1Nomenclature = (int)$value;
        }
    }

    // }}}
    // Material2 foreignkey property + getter/setter {{{

    /**
     * Material2 foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Material2 = false;

    /**
     * _RTWModel::getMaterial2
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getMaterial2() {
        if (is_int($this->_Material2) && $this->_Material2 > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Material2 = $mapper->load(
                array('Id'=>$this->_Material2));
        }
        return $this->_Material2;
    }

    /**
     * _RTWModel::getMaterial2Id
     *
     * @access public
     * @return integer
     */
    public function getMaterial2Id() {
        if ($this->_Material2 instanceof RTWMaterial) {
            return $this->_Material2->getId();
        }
        return (int)$this->_Material2;
    }

    /**
     * _RTWModel::setMaterial2
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setMaterial2($value) {
        if (is_numeric($value)) {
            $this->_Material2 = (int)$value;
        } else {
            $this->_Material2 = $value;
        }
    }

    // }}}
    // Material2Quantity float property + getter/setter {{{

    /**
     * Material2Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Material2Quantity = null;

    /**
     * _RTWModel::getMaterial2Quantity
     *
     * @access public
     * @return float
     */
    public function getMaterial2Quantity() {
        return $this->_Material2Quantity;
    }

    /**
     * _RTWModel::setMaterial2Quantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaterial2Quantity($value) {
        $this->_Material2Quantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // Material2Nomenclature string property + getter/setter {{{

    /**
     * Material2Nomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_Material2Nomenclature = 1;

    /**
     * _RTWModel::getMaterial2Nomenclature
     *
     * @access public
     * @return integer
     */
    public function getMaterial2Nomenclature() {
        return $this->_Material2Nomenclature;
    }

    /**
     * _RTWModel::setMaterial2Nomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setMaterial2Nomenclature($value) {
        if ($value !== null) {
            $this->_Material2Nomenclature = (int)$value;
        }
    }

    // }}}
    // Material3 foreignkey property + getter/setter {{{

    /**
     * Material3 foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Material3 = false;

    /**
     * _RTWModel::getMaterial3
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getMaterial3() {
        if (is_int($this->_Material3) && $this->_Material3 > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Material3 = $mapper->load(
                array('Id'=>$this->_Material3));
        }
        return $this->_Material3;
    }

    /**
     * _RTWModel::getMaterial3Id
     *
     * @access public
     * @return integer
     */
    public function getMaterial3Id() {
        if ($this->_Material3 instanceof RTWMaterial) {
            return $this->_Material3->getId();
        }
        return (int)$this->_Material3;
    }

    /**
     * _RTWModel::setMaterial3
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setMaterial3($value) {
        if (is_numeric($value)) {
            $this->_Material3 = (int)$value;
        } else {
            $this->_Material3 = $value;
        }
    }

    // }}}
    // Material3Quantity float property + getter/setter {{{

    /**
     * Material3Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Material3Quantity = null;

    /**
     * _RTWModel::getMaterial3Quantity
     *
     * @access public
     * @return float
     */
    public function getMaterial3Quantity() {
        return $this->_Material3Quantity;
    }

    /**
     * _RTWModel::setMaterial3Quantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaterial3Quantity($value) {
        $this->_Material3Quantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // Material3Nomenclature string property + getter/setter {{{

    /**
     * Material3Nomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_Material3Nomenclature = 1;

    /**
     * _RTWModel::getMaterial3Nomenclature
     *
     * @access public
     * @return integer
     */
    public function getMaterial3Nomenclature() {
        return $this->_Material3Nomenclature;
    }

    /**
     * _RTWModel::setMaterial3Nomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setMaterial3Nomenclature($value) {
        if ($value !== null) {
            $this->_Material3Nomenclature = (int)$value;
        }
    }

    // }}}
    // Accessory1 foreignkey property + getter/setter {{{

    /**
     * Accessory1 foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Accessory1 = false;

    /**
     * _RTWModel::getAccessory1
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getAccessory1() {
        if (is_int($this->_Accessory1) && $this->_Accessory1 > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Accessory1 = $mapper->load(
                array('Id'=>$this->_Accessory1));
        }
        return $this->_Accessory1;
    }

    /**
     * _RTWModel::getAccessory1Id
     *
     * @access public
     * @return integer
     */
    public function getAccessory1Id() {
        if ($this->_Accessory1 instanceof RTWMaterial) {
            return $this->_Accessory1->getId();
        }
        return (int)$this->_Accessory1;
    }

    /**
     * _RTWModel::setAccessory1
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setAccessory1($value) {
        if (is_numeric($value)) {
            $this->_Accessory1 = (int)$value;
        } else {
            $this->_Accessory1 = $value;
        }
    }

    // }}}
    // Accessory1Quantity float property + getter/setter {{{

    /**
     * Accessory1Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Accessory1Quantity = null;

    /**
     * _RTWModel::getAccessory1Quantity
     *
     * @access public
     * @return float
     */
    public function getAccessory1Quantity() {
        return $this->_Accessory1Quantity;
    }

    /**
     * _RTWModel::setAccessory1Quantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setAccessory1Quantity($value) {
        $this->_Accessory1Quantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // Accessory1Nomenclature string property + getter/setter {{{

    /**
     * Accessory1Nomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_Accessory1Nomenclature = 1;

    /**
     * _RTWModel::getAccessory1Nomenclature
     *
     * @access public
     * @return integer
     */
    public function getAccessory1Nomenclature() {
        return $this->_Accessory1Nomenclature;
    }

    /**
     * _RTWModel::setAccessory1Nomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setAccessory1Nomenclature($value) {
        if ($value !== null) {
            $this->_Accessory1Nomenclature = (int)$value;
        }
    }

    // }}}
    // Accessory2 foreignkey property + getter/setter {{{

    /**
     * Accessory2 foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Accessory2 = false;

    /**
     * _RTWModel::getAccessory2
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getAccessory2() {
        if (is_int($this->_Accessory2) && $this->_Accessory2 > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Accessory2 = $mapper->load(
                array('Id'=>$this->_Accessory2));
        }
        return $this->_Accessory2;
    }

    /**
     * _RTWModel::getAccessory2Id
     *
     * @access public
     * @return integer
     */
    public function getAccessory2Id() {
        if ($this->_Accessory2 instanceof RTWMaterial) {
            return $this->_Accessory2->getId();
        }
        return (int)$this->_Accessory2;
    }

    /**
     * _RTWModel::setAccessory2
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setAccessory2($value) {
        if (is_numeric($value)) {
            $this->_Accessory2 = (int)$value;
        } else {
            $this->_Accessory2 = $value;
        }
    }

    // }}}
    // Accessory2Quantity float property + getter/setter {{{

    /**
     * Accessory2Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Accessory2Quantity = null;

    /**
     * _RTWModel::getAccessory2Quantity
     *
     * @access public
     * @return float
     */
    public function getAccessory2Quantity() {
        return $this->_Accessory2Quantity;
    }

    /**
     * _RTWModel::setAccessory2Quantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setAccessory2Quantity($value) {
        $this->_Accessory2Quantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // Accessory2Nomenclature string property + getter/setter {{{

    /**
     * Accessory2Nomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_Accessory2Nomenclature = 1;

    /**
     * _RTWModel::getAccessory2Nomenclature
     *
     * @access public
     * @return integer
     */
    public function getAccessory2Nomenclature() {
        return $this->_Accessory2Nomenclature;
    }

    /**
     * _RTWModel::setAccessory2Nomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setAccessory2Nomenclature($value) {
        if ($value !== null) {
            $this->_Accessory2Nomenclature = (int)$value;
        }
    }

    // }}}
    // Accessory3 foreignkey property + getter/setter {{{

    /**
     * Accessory3 foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Accessory3 = false;

    /**
     * _RTWModel::getAccessory3
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getAccessory3() {
        if (is_int($this->_Accessory3) && $this->_Accessory3 > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Accessory3 = $mapper->load(
                array('Id'=>$this->_Accessory3));
        }
        return $this->_Accessory3;
    }

    /**
     * _RTWModel::getAccessory3Id
     *
     * @access public
     * @return integer
     */
    public function getAccessory3Id() {
        if ($this->_Accessory3 instanceof RTWMaterial) {
            return $this->_Accessory3->getId();
        }
        return (int)$this->_Accessory3;
    }

    /**
     * _RTWModel::setAccessory3
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setAccessory3($value) {
        if (is_numeric($value)) {
            $this->_Accessory3 = (int)$value;
        } else {
            $this->_Accessory3 = $value;
        }
    }

    // }}}
    // Accessory3Quantity float property + getter/setter {{{

    /**
     * Accessory3Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Accessory3Quantity = null;

    /**
     * _RTWModel::getAccessory3Quantity
     *
     * @access public
     * @return float
     */
    public function getAccessory3Quantity() {
        return $this->_Accessory3Quantity;
    }

    /**
     * _RTWModel::setAccessory3Quantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setAccessory3Quantity($value) {
        $this->_Accessory3Quantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // Accessory3Nomenclature string property + getter/setter {{{

    /**
     * Accessory3Nomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_Accessory3Nomenclature = 1;

    /**
     * _RTWModel::getAccessory3Nomenclature
     *
     * @access public
     * @return integer
     */
    public function getAccessory3Nomenclature() {
        return $this->_Accessory3Nomenclature;
    }

    /**
     * _RTWModel::setAccessory3Nomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setAccessory3Nomenclature($value) {
        if ($value !== null) {
            $this->_Accessory3Nomenclature = (int)$value;
        }
    }

    // }}}
    // Lining foreignkey property + getter/setter {{{

    /**
     * Lining foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Lining = false;

    /**
     * _RTWModel::getLining
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getLining() {
        if (is_int($this->_Lining) && $this->_Lining > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Lining = $mapper->load(
                array('Id'=>$this->_Lining));
        }
        return $this->_Lining;
    }

    /**
     * _RTWModel::getLiningId
     *
     * @access public
     * @return integer
     */
    public function getLiningId() {
        if ($this->_Lining instanceof RTWMaterial) {
            return $this->_Lining->getId();
        }
        return (int)$this->_Lining;
    }

    /**
     * _RTWModel::setLining
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setLining($value) {
        if (is_numeric($value)) {
            $this->_Lining = (int)$value;
        } else {
            $this->_Lining = $value;
        }
    }

    // }}}
    // LiningQuantity float property + getter/setter {{{

    /**
     * LiningQuantity float property
     *
     * @access private
     * @var float
     */
    private $_LiningQuantity = null;

    /**
     * _RTWModel::getLiningQuantity
     *
     * @access public
     * @return float
     */
    public function getLiningQuantity() {
        return $this->_LiningQuantity;
    }

    /**
     * _RTWModel::setLiningQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setLiningQuantity($value) {
        $this->_LiningQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // LiningNomenclature string property + getter/setter {{{

    /**
     * LiningNomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_LiningNomenclature = 1;

    /**
     * _RTWModel::getLiningNomenclature
     *
     * @access public
     * @return integer
     */
    public function getLiningNomenclature() {
        return $this->_LiningNomenclature;
    }

    /**
     * _RTWModel::setLiningNomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setLiningNomenclature($value) {
        if ($value !== null) {
            $this->_LiningNomenclature = (int)$value;
        }
    }

    // }}}
    // Insole foreignkey property + getter/setter {{{

    /**
     * Insole foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Insole = false;

    /**
     * _RTWModel::getInsole
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getInsole() {
        if (is_int($this->_Insole) && $this->_Insole > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Insole = $mapper->load(
                array('Id'=>$this->_Insole));
        }
        return $this->_Insole;
    }

    /**
     * _RTWModel::getInsoleId
     *
     * @access public
     * @return integer
     */
    public function getInsoleId() {
        if ($this->_Insole instanceof RTWMaterial) {
            return $this->_Insole->getId();
        }
        return (int)$this->_Insole;
    }

    /**
     * _RTWModel::setInsole
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setInsole($value) {
        if (is_numeric($value)) {
            $this->_Insole = (int)$value;
        } else {
            $this->_Insole = $value;
        }
    }

    // }}}
    // InsoleQuantity float property + getter/setter {{{

    /**
     * InsoleQuantity float property
     *
     * @access private
     * @var float
     */
    private $_InsoleQuantity = null;

    /**
     * _RTWModel::getInsoleQuantity
     *
     * @access public
     * @return float
     */
    public function getInsoleQuantity() {
        return $this->_InsoleQuantity;
    }

    /**
     * _RTWModel::setInsoleQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setInsoleQuantity($value) {
        $this->_InsoleQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // InsoleNomenclature string property + getter/setter {{{

    /**
     * InsoleNomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_InsoleNomenclature = 1;

    /**
     * _RTWModel::getInsoleNomenclature
     *
     * @access public
     * @return integer
     */
    public function getInsoleNomenclature() {
        return $this->_InsoleNomenclature;
    }

    /**
     * _RTWModel::setInsoleNomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setInsoleNomenclature($value) {
        if ($value !== null) {
            $this->_InsoleNomenclature = (int)$value;
        }
    }

    // }}}
    // UnderSole foreignkey property + getter/setter {{{

    /**
     * UnderSole foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_UnderSole = false;

    /**
     * _RTWModel::getUnderSole
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getUnderSole() {
        if (is_int($this->_UnderSole) && $this->_UnderSole > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_UnderSole = $mapper->load(
                array('Id'=>$this->_UnderSole));
        }
        return $this->_UnderSole;
    }

    /**
     * _RTWModel::getUnderSoleId
     *
     * @access public
     * @return integer
     */
    public function getUnderSoleId() {
        if ($this->_UnderSole instanceof RTWMaterial) {
            return $this->_UnderSole->getId();
        }
        return (int)$this->_UnderSole;
    }

    /**
     * _RTWModel::setUnderSole
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setUnderSole($value) {
        if (is_numeric($value)) {
            $this->_UnderSole = (int)$value;
        } else {
            $this->_UnderSole = $value;
        }
    }

    // }}}
    // UnderSoleQuantity float property + getter/setter {{{

    /**
     * UnderSoleQuantity float property
     *
     * @access private
     * @var float
     */
    private $_UnderSoleQuantity = null;

    /**
     * _RTWModel::getUnderSoleQuantity
     *
     * @access public
     * @return float
     */
    public function getUnderSoleQuantity() {
        return $this->_UnderSoleQuantity;
    }

    /**
     * _RTWModel::setUnderSoleQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setUnderSoleQuantity($value) {
        $this->_UnderSoleQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // UnderSoleNomenclature string property + getter/setter {{{

    /**
     * UnderSoleNomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_UnderSoleNomenclature = 1;

    /**
     * _RTWModel::getUnderSoleNomenclature
     *
     * @access public
     * @return integer
     */
    public function getUnderSoleNomenclature() {
        return $this->_UnderSoleNomenclature;
    }

    /**
     * _RTWModel::setUnderSoleNomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setUnderSoleNomenclature($value) {
        if ($value !== null) {
            $this->_UnderSoleNomenclature = (int)$value;
        }
    }

    // }}}
    // MediaPlanta foreignkey property + getter/setter {{{

    /**
     * MediaPlanta foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_MediaPlanta = false;

    /**
     * _RTWModel::getMediaPlanta
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getMediaPlanta() {
        if (is_int($this->_MediaPlanta) && $this->_MediaPlanta > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_MediaPlanta = $mapper->load(
                array('Id'=>$this->_MediaPlanta));
        }
        return $this->_MediaPlanta;
    }

    /**
     * _RTWModel::getMediaPlantaId
     *
     * @access public
     * @return integer
     */
    public function getMediaPlantaId() {
        if ($this->_MediaPlanta instanceof RTWMaterial) {
            return $this->_MediaPlanta->getId();
        }
        return (int)$this->_MediaPlanta;
    }

    /**
     * _RTWModel::setMediaPlanta
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setMediaPlanta($value) {
        if (is_numeric($value)) {
            $this->_MediaPlanta = (int)$value;
        } else {
            $this->_MediaPlanta = $value;
        }
    }

    // }}}
    // MediaPlantaQuantity float property + getter/setter {{{

    /**
     * MediaPlantaQuantity float property
     *
     * @access private
     * @var float
     */
    private $_MediaPlantaQuantity = null;

    /**
     * _RTWModel::getMediaPlantaQuantity
     *
     * @access public
     * @return float
     */
    public function getMediaPlantaQuantity() {
        return $this->_MediaPlantaQuantity;
    }

    /**
     * _RTWModel::setMediaPlantaQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMediaPlantaQuantity($value) {
        $this->_MediaPlantaQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // MediaPlantaNomenclature string property + getter/setter {{{

    /**
     * MediaPlantaNomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_MediaPlantaNomenclature = 1;

    /**
     * _RTWModel::getMediaPlantaNomenclature
     *
     * @access public
     * @return integer
     */
    public function getMediaPlantaNomenclature() {
        return $this->_MediaPlantaNomenclature;
    }

    /**
     * _RTWModel::setMediaPlantaNomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setMediaPlantaNomenclature($value) {
        if ($value !== null) {
            $this->_MediaPlantaNomenclature = (int)$value;
        }
    }

    // }}}
    // Lagrima foreignkey property + getter/setter {{{

    /**
     * Lagrima foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Lagrima = false;

    /**
     * _RTWModel::getLagrima
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getLagrima() {
        if (is_int($this->_Lagrima) && $this->_Lagrima > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Lagrima = $mapper->load(
                array('Id'=>$this->_Lagrima));
        }
        return $this->_Lagrima;
    }

    /**
     * _RTWModel::getLagrimaId
     *
     * @access public
     * @return integer
     */
    public function getLagrimaId() {
        if ($this->_Lagrima instanceof RTWMaterial) {
            return $this->_Lagrima->getId();
        }
        return (int)$this->_Lagrima;
    }

    /**
     * _RTWModel::setLagrima
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setLagrima($value) {
        if (is_numeric($value)) {
            $this->_Lagrima = (int)$value;
        } else {
            $this->_Lagrima = $value;
        }
    }

    // }}}
    // LagrimaQuantity float property + getter/setter {{{

    /**
     * LagrimaQuantity float property
     *
     * @access private
     * @var float
     */
    private $_LagrimaQuantity = null;

    /**
     * _RTWModel::getLagrimaQuantity
     *
     * @access public
     * @return float
     */
    public function getLagrimaQuantity() {
        return $this->_LagrimaQuantity;
    }

    /**
     * _RTWModel::setLagrimaQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setLagrimaQuantity($value) {
        $this->_LagrimaQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // LagrimaNomenclature string property + getter/setter {{{

    /**
     * LagrimaNomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_LagrimaNomenclature = 1;

    /**
     * _RTWModel::getLagrimaNomenclature
     *
     * @access public
     * @return integer
     */
    public function getLagrimaNomenclature() {
        return $this->_LagrimaNomenclature;
    }

    /**
     * _RTWModel::setLagrimaNomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setLagrimaNomenclature($value) {
        if ($value !== null) {
            $this->_LagrimaNomenclature = (int)$value;
        }
    }

    // }}}
    // HeelCovering foreignkey property + getter/setter {{{

    /**
     * HeelCovering foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_HeelCovering = false;

    /**
     * _RTWModel::getHeelCovering
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getHeelCovering() {
        if (is_int($this->_HeelCovering) && $this->_HeelCovering > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_HeelCovering = $mapper->load(
                array('Id'=>$this->_HeelCovering));
        }
        return $this->_HeelCovering;
    }

    /**
     * _RTWModel::getHeelCoveringId
     *
     * @access public
     * @return integer
     */
    public function getHeelCoveringId() {
        if ($this->_HeelCovering instanceof RTWMaterial) {
            return $this->_HeelCovering->getId();
        }
        return (int)$this->_HeelCovering;
    }

    /**
     * _RTWModel::setHeelCovering
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setHeelCovering($value) {
        if (is_numeric($value)) {
            $this->_HeelCovering = (int)$value;
        } else {
            $this->_HeelCovering = $value;
        }
    }

    // }}}
    // HeelCoveringQuantity float property + getter/setter {{{

    /**
     * HeelCoveringQuantity float property
     *
     * @access private
     * @var float
     */
    private $_HeelCoveringQuantity = null;

    /**
     * _RTWModel::getHeelCoveringQuantity
     *
     * @access public
     * @return float
     */
    public function getHeelCoveringQuantity() {
        return $this->_HeelCoveringQuantity;
    }

    /**
     * _RTWModel::setHeelCoveringQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setHeelCoveringQuantity($value) {
        $this->_HeelCoveringQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // HeelCoveringNomenclature string property + getter/setter {{{

    /**
     * HeelCoveringNomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_HeelCoveringNomenclature = 1;

    /**
     * _RTWModel::getHeelCoveringNomenclature
     *
     * @access public
     * @return integer
     */
    public function getHeelCoveringNomenclature() {
        return $this->_HeelCoveringNomenclature;
    }

    /**
     * _RTWModel::setHeelCoveringNomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setHeelCoveringNomenclature($value) {
        if ($value !== null) {
            $this->_HeelCoveringNomenclature = (int)$value;
        }
    }

    // }}}
    // Selvedge foreignkey property + getter/setter {{{

    /**
     * Selvedge foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Selvedge = false;

    /**
     * _RTWModel::getSelvedge
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getSelvedge() {
        if (is_int($this->_Selvedge) && $this->_Selvedge > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Selvedge = $mapper->load(
                array('Id'=>$this->_Selvedge));
        }
        return $this->_Selvedge;
    }

    /**
     * _RTWModel::getSelvedgeId
     *
     * @access public
     * @return integer
     */
    public function getSelvedgeId() {
        if ($this->_Selvedge instanceof RTWMaterial) {
            return $this->_Selvedge->getId();
        }
        return (int)$this->_Selvedge;
    }

    /**
     * _RTWModel::setSelvedge
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setSelvedge($value) {
        if (is_numeric($value)) {
            $this->_Selvedge = (int)$value;
        } else {
            $this->_Selvedge = $value;
        }
    }

    // }}}
    // SelvedgeQuantity float property + getter/setter {{{

    /**
     * SelvedgeQuantity float property
     *
     * @access private
     * @var float
     */
    private $_SelvedgeQuantity = null;

    /**
     * _RTWModel::getSelvedgeQuantity
     *
     * @access public
     * @return float
     */
    public function getSelvedgeQuantity() {
        return $this->_SelvedgeQuantity;
    }

    /**
     * _RTWModel::setSelvedgeQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setSelvedgeQuantity($value) {
        $this->_SelvedgeQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // SelvedgeNomenclature string property + getter/setter {{{

    /**
     * SelvedgeNomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_SelvedgeNomenclature = 1;

    /**
     * _RTWModel::getSelvedgeNomenclature
     *
     * @access public
     * @return integer
     */
    public function getSelvedgeNomenclature() {
        return $this->_SelvedgeNomenclature;
    }

    /**
     * _RTWModel::setSelvedgeNomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setSelvedgeNomenclature($value) {
        if ($value !== null) {
            $this->_SelvedgeNomenclature = (int)$value;
        }
    }

    // }}}
    // Thread1 foreignkey property + getter/setter {{{

    /**
     * Thread1 foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Thread1 = false;

    /**
     * _RTWModel::getThread1
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getThread1() {
        if (is_int($this->_Thread1) && $this->_Thread1 > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Thread1 = $mapper->load(
                array('Id'=>$this->_Thread1));
        }
        return $this->_Thread1;
    }

    /**
     * _RTWModel::getThread1Id
     *
     * @access public
     * @return integer
     */
    public function getThread1Id() {
        if ($this->_Thread1 instanceof RTWMaterial) {
            return $this->_Thread1->getId();
        }
        return (int)$this->_Thread1;
    }

    /**
     * _RTWModel::setThread1
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setThread1($value) {
        if (is_numeric($value)) {
            $this->_Thread1 = (int)$value;
        } else {
            $this->_Thread1 = $value;
        }
    }

    // }}}
    // Thread2 foreignkey property + getter/setter {{{

    /**
     * Thread2 foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Thread2 = false;

    /**
     * _RTWModel::getThread2
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getThread2() {
        if (is_int($this->_Thread2) && $this->_Thread2 > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Thread2 = $mapper->load(
                array('Id'=>$this->_Thread2));
        }
        return $this->_Thread2;
    }

    /**
     * _RTWModel::getThread2Id
     *
     * @access public
     * @return integer
     */
    public function getThread2Id() {
        if ($this->_Thread2 instanceof RTWMaterial) {
            return $this->_Thread2->getId();
        }
        return (int)$this->_Thread2;
    }

    /**
     * _RTWModel::setThread2
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setThread2($value) {
        if (is_numeric($value)) {
            $this->_Thread2 = (int)$value;
        } else {
            $this->_Thread2 = $value;
        }
    }

    // }}}
    // Bamboo foreignkey property + getter/setter {{{

    /**
     * Bamboo foreignkey
     *
     * @access private
     * @var mixed object RTWMaterial or integer
     */
    private $_Bamboo = false;

    /**
     * _RTWModel::getBamboo
     *
     * @access public
     * @return object RTWMaterial
     */
    public function getBamboo() {
        if (is_int($this->_Bamboo) && $this->_Bamboo > 0) {
            $mapper = Mapper::singleton('RTWMaterial');
            $this->_Bamboo = $mapper->load(
                array('Id'=>$this->_Bamboo));
        }
        return $this->_Bamboo;
    }

    /**
     * _RTWModel::getBambooId
     *
     * @access public
     * @return integer
     */
    public function getBambooId() {
        if ($this->_Bamboo instanceof RTWMaterial) {
            return $this->_Bamboo->getId();
        }
        return (int)$this->_Bamboo;
    }

    /**
     * _RTWModel::setBamboo
     *
     * @access public
     * @param object RTWMaterial $value
     * @return void
     */
    public function setBamboo($value) {
        if (is_numeric($value)) {
            $this->_Bamboo = (int)$value;
        } else {
            $this->_Bamboo = $value;
        }
    }

    // }}}
    // BambooQuantity float property + getter/setter {{{

    /**
     * BambooQuantity float property
     *
     * @access private
     * @var float
     */
    private $_BambooQuantity = null;

    /**
     * _RTWModel::getBambooQuantity
     *
     * @access public
     * @return float
     */
    public function getBambooQuantity() {
        return $this->_BambooQuantity;
    }

    /**
     * _RTWModel::setBambooQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setBambooQuantity($value) {
        $this->_BambooQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // BambooNomenclature string property + getter/setter {{{

    /**
     * BambooNomenclature int property
     *
     * @access private
     * @var integer
     */
    private $_BambooNomenclature = 1;

    /**
     * _RTWModel::getBambooNomenclature
     *
     * @access public
     * @return integer
     */
    public function getBambooNomenclature() {
        return $this->_BambooNomenclature;
    }

    /**
     * _RTWModel::setBambooNomenclature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setBambooNomenclature($value) {
        if ($value !== null) {
            $this->_BambooNomenclature = (int)$value;
        }
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
     * _RTWModel::getImage
     *
     * @access public
     * @return string
     */
    public function getImage() {
        return $this->_Image;
    }

    /**
     * _RTWModel::setImage
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setImage($value) {
        $this->_Image = $value;
    }

    // }}}
    // ColorImage string property + getter/setter {{{

    /**
     * ColorImage string property
     *
     * @access private
     * @var string
     */
    private $_ColorImage = '';

    /**
     * _RTWModel::getColorImage
     *
     * @access public
     * @return string
     */
    public function getColorImage() {
        return $this->_ColorImage;
    }

    /**
     * _RTWModel::setColorImage
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setColorImage($value) {
        $this->_ColorImage = $value;
    }

    // }}}
    // Size one to many relation + getter/setter {{{

    /**
     * Size *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_SizeCollection = false;

    /**
     * _RTWModel::getSizeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getSizeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('RTWModel');
            return $mapper->getManyToMany($this->getId(),
                'Size', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_SizeCollection) {
            $mapper = Mapper::singleton('RTWModel');
            $this->_SizeCollection = $mapper->getManyToMany($this->getId(),
                'Size');
        }
        return $this->_SizeCollection;
    }

    /**
     * _RTWModel::getSizeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getSizeCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getSizeCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_SizeCollection) {
            $mapper = Mapper::singleton('RTWModel');
            return $mapper->getManyToManyIds($this->getId(), 'Size');
        }
        return $this->_SizeCollection->getItemIds();
    }

    /**
     * _RTWModel::setSizeCollectionIds
     *
     * @access public
     * @return array
     */
    public function setSizeCollectionIds($itemIds) {
        $this->_SizeCollection = new Collection('Size');
        foreach ($itemIds as $id) {
            $this->_SizeCollection->setItem($id);
        }
    }

    /**
     * _RTWModel::setSizeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setSizeCollection($value) {
        $this->_SizeCollection = $value;
    }

    /**
     * _RTWModel::SizeCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function SizeCollectionIsLoaded() {
        return ($this->_SizeCollection !== false);
    }

    // }}}
    // Comment string property + getter/setter {{{

    /**
     * Comment string property
     *
     * @access private
     * @var string
     */
    private $_Comment = '';

    /**
     * _RTWModel::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * _RTWModel::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // RTWProduct one to many relation + getter/setter {{{

    /**
     * RTWProduct 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_RTWProductCollection = false;

    /**
     * _RTWModel::getRTWProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getRTWProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('RTWModel');
            return $mapper->getOneToMany($this->getId(),
                'RTWProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_RTWProductCollection) {
            $mapper = Mapper::singleton('RTWModel');
            $this->_RTWProductCollection = $mapper->getOneToMany($this->getId(),
                'RTWProduct');
        }
        return $this->_RTWProductCollection;
    }

    /**
     * _RTWModel::getRTWProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getRTWProductCollectionIds($filter = array()) {
        $col = $this->getRTWProductCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _RTWModel::setRTWProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setRTWProductCollection($value) {
        $this->_RTWProductCollection = $value;
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
        return 'RTWModel';
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
        return _('Models');
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
            'Season' => 'RTWSeason',
            'Shape' => 'RTWShape',
            'PressName' => 'RTWPressName',
            'StyleNumber' => Object::TYPE_STRING,
            'Description' => Object::TYPE_STRING,
            'Manufacturer' => 'Supplier',
            'ConstructionType' => 'RTWConstructionType',
            'ConstructionCode' => 'RTWConstructionCode',
            'Label' => 'RTWLabel',
            'HeelHeight' => 'RTWHeelHeight',
            'HeelReference' => 'RTWMaterial',
            'HeelReferenceQuantity' => Object::TYPE_DECIMAL,
            'HeelReferenceNomenclature' => Object::TYPE_BOOL,
            'Sole' => 'RTWMaterial',
            'SoleQuantity' => Object::TYPE_DECIMAL,
            'SoleNomenclature' => Object::TYPE_BOOL,
            'Box' => 'RTWMaterial',
            'BoxQuantity' => Object::TYPE_DECIMAL,
            'BoxNomenclature' => Object::TYPE_BOOL,
            'HandBag' => 'RTWMaterial',
            'HandBagQuantity' => Object::TYPE_DECIMAL,
            'HandBagNomenclature' => Object::TYPE_BOOL,
            'Material1' => 'RTWMaterial',
            'Material1Quantity' => Object::TYPE_DECIMAL,
            'Material1Nomenclature' => Object::TYPE_BOOL,
            'Material2' => 'RTWMaterial',
            'Material2Quantity' => Object::TYPE_DECIMAL,
            'Material2Nomenclature' => Object::TYPE_BOOL,
            'Material3' => 'RTWMaterial',
            'Material3Quantity' => Object::TYPE_DECIMAL,
            'Material3Nomenclature' => Object::TYPE_BOOL,
            'Accessory1' => 'RTWMaterial',
            'Accessory1Quantity' => Object::TYPE_DECIMAL,
            'Accessory1Nomenclature' => Object::TYPE_BOOL,
            'Accessory2' => 'RTWMaterial',
            'Accessory2Quantity' => Object::TYPE_DECIMAL,
            'Accessory2Nomenclature' => Object::TYPE_BOOL,
            'Accessory3' => 'RTWMaterial',
            'Accessory3Quantity' => Object::TYPE_DECIMAL,
            'Accessory3Nomenclature' => Object::TYPE_BOOL,
            'Lining' => 'RTWMaterial',
            'LiningQuantity' => Object::TYPE_DECIMAL,
            'LiningNomenclature' => Object::TYPE_BOOL,
            'Insole' => 'RTWMaterial',
            'InsoleQuantity' => Object::TYPE_DECIMAL,
            'InsoleNomenclature' => Object::TYPE_BOOL,
            'UnderSole' => 'RTWMaterial',
            'UnderSoleQuantity' => Object::TYPE_DECIMAL,
            'UnderSoleNomenclature' => Object::TYPE_BOOL,
            'MediaPlanta' => 'RTWMaterial',
            'MediaPlantaQuantity' => Object::TYPE_DECIMAL,
            'MediaPlantaNomenclature' => Object::TYPE_BOOL,
            'Lagrima' => 'RTWMaterial',
            'LagrimaQuantity' => Object::TYPE_DECIMAL,
            'LagrimaNomenclature' => Object::TYPE_BOOL,
            'HeelCovering' => 'RTWMaterial',
            'HeelCoveringQuantity' => Object::TYPE_DECIMAL,
            'HeelCoveringNomenclature' => Object::TYPE_BOOL,
            'Selvedge' => 'RTWMaterial',
            'SelvedgeQuantity' => Object::TYPE_DECIMAL,
            'SelvedgeNomenclature' => Object::TYPE_BOOL,
            'Thread1' => 'RTWMaterial',
            'Thread2' => 'RTWMaterial',
            'Bamboo' => 'RTWMaterial',
            'BambooQuantity' => Object::TYPE_DECIMAL,
            'BambooNomenclature' => Object::TYPE_BOOL,
            'Image' => Object::TYPE_IMAGE,
            'ColorImage' => Object::TYPE_IMAGE,
            'Comment' => Object::TYPE_TEXT);
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
            'Size'=>array(
                'linkClass'     => 'RTWSize',
                'field'         => 'FromRTWModel',
                'linkTable'     => 'rTWModelRTWSize',
                'linkField'     => 'ToRTWSize',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'RTWProduct'=>array(
                'linkClass'     => 'RTWProduct',
                'field'         => 'Model',
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
        return array('add', 'edit', 'del', 'grid', 'searchform');
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
            'Season'=>array(
                'label'        => _('Season'),
                'shortlabel'   => _('Season'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Shape'=>array(
                'label'        => _('Shape'),
                'shortlabel'   => _('Shape'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'PressName'=>array(
                'label'        => _('Press name'),
                'shortlabel'   => _('Press name'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'StyleNumber'=>array(
                'label'        => _('Style number'),
                'shortlabel'   => _('Style number'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Description'=>array(
                'label'        => _('Description'),
                'shortlabel'   => _('Description'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Manufacturer'=>array(
                'label'        => _('Manufacturer'),
                'shortlabel'   => _('Manufacturer'),
                'usedby'       => array('addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ConstructionType'=>array(
                'label'        => _('Construction type'),
                'shortlabel'   => _('Construction type'),
                'usedby'       => array('addedit', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ConstructionCode'=>array(
                'label'        => _('Construction code'),
                'shortlabel'   => _('Construction code'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Label'=>array(
                'label'        => _('Label (griffe)'),
                'shortlabel'   => _('Label (griffe)'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'HeelHeight'=>array(
                'label'        => _('Heel height'),
                'shortlabel'   => _('Heel height'),
                'usedby'       => array('addedit', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'HeelReference'=>array(
                'label'        => _('Heel reference'),
                'shortlabel'   => _('Heel reference'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'HeelReferenceQuantity'=>array(
                'label'        => _('Heel reference quantity'),
                'shortlabel'   => _('Heel reference quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Sole'=>array(
                'label'        => _('Sole'),
                'shortlabel'   => _('Sole'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'SoleQuantity'=>array(
                'label'        => _('Sole quantity'),
                'shortlabel'   => _('Sole quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Box'=>array(
                'label'        => _('Box'),
                'shortlabel'   => _('Box'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'BoxQuantity'=>array(
                'label'        => _('Box quantity'),
                'shortlabel'   => _('Box quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'HandBag'=>array(
                'label'        => _('Hand bag'),
                'shortlabel'   => _('Hand bag'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'HandBagQuantity'=>array(
                'label'        => _('Hand bag quantity'),
                'shortlabel'   => _('Hand bag quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Material1'=>array(
                'label'        => _('Material 1'),
                'shortlabel'   => _('Material 1'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Material1Quantity'=>array(
                'label'        => _('Material 1 quantity'),
                'shortlabel'   => _('Material 1 quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Material2'=>array(
                'label'        => _('Material 2'),
                'shortlabel'   => _('Material 2'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Material2Quantity'=>array(
                'label'        => _('Material 2 quantity'),
                'shortlabel'   => _('Material 2 quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Material3'=>array(
                'label'        => _('Material 3'),
                'shortlabel'   => _('Material 3'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Material3Quantity'=>array(
                'label'        => _('Material 3 quantity'),
                'shortlabel'   => _('Material 3 quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Accessory1'=>array(
                'label'        => _('Accessory 1'),
                'shortlabel'   => _('Accessory 1'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Accessory1Quantity'=>array(
                'label'        => _('Accessory 1 quantity'),
                'shortlabel'   => _('Accessory 1 quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Accessory2'=>array(
                'label'        => _('Accessory 2'),
                'shortlabel'   => _('Accessory 2'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Accessory2Quantity'=>array(
                'label'        => _('Accessory 2 quantity'),
                'shortlabel'   => _('Accessory 2 quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Accessory3'=>array(
                'label'        => _('Accessory 3'),
                'shortlabel'   => _('Accessory 3'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Accessory3Quantity'=>array(
                'label'        => _('Accessory 3 quantity'),
                'shortlabel'   => _('Accessory 3 quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Lining'=>array(
                'label'        => _('Lining'),
                'shortlabel'   => _('Lining'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'LiningQuantity'=>array(
                'label'        => _('Lining quantity'),
                'shortlabel'   => _('Lining quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Insole'=>array(
                'label'        => _('Insole'),
                'shortlabel'   => _('Insole'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'InsoleQuantity'=>array(
                'label'        => _('Insole quantity'),
                'shortlabel'   => _('Insole quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'UnderSole'=>array(
                'label'        => _('Under-sole'),
                'shortlabel'   => _('Under-sole'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'UnderSoleQuantity'=>array(
                'label'        => _('Under-sole quantity'),
                'shortlabel'   => _('Under-sole quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'MediaPlanta'=>array(
                'label'        => _('Media planta'),
                'shortlabel'   => _('Media planta'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'MediaPlantaQuantity'=>array(
                'label'        => _('Media planta quantity'),
                'shortlabel'   => _('Media planta quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Lagrima'=>array(
                'label'        => _('Lagrima'),
                'shortlabel'   => _('Lagrima'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'LagrimaQuantity'=>array(
                'label'        => _('Lagrima quantity'),
                'shortlabel'   => _('Lagrima quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'HeelCovering'=>array(
                'label'        => _('Heel covering'),
                'shortlabel'   => _('Heel covering'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'HeelCoveringQuantity'=>array(
                'label'        => _('Heel covering quantity'),
                'shortlabel'   => _('Heel covering quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Selvedge'=>array(
                'label'        => _('Selvedge'),
                'shortlabel'   => _('Selvedge'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'SelvedgeQuantity'=>array(
                'label'        => _('Selvedge quantity'),
                'shortlabel'   => _('Selvedge quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Thread1'=>array(
                'label'        => _('Thread 1'),
                'shortlabel'   => _('Thread 1'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Thread2'=>array(
                'label'        => _('Thread 2'),
                'shortlabel'   => _('Thread 2'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Bamboo'=>array(
                'label'        => _('Bamboo'),
                'shortlabel'   => _('Bamboo'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'BambooQuantity'=>array(
                'label'        => _('Bamboo quantity'),
                'shortlabel'   => _('Bamboo quantity'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 3
            ),
            'Image'=>array(
                'label'        => _('Black and white image'),
                'shortlabel'   => _('Black and white image'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('Images')
            ),
            'ColorImage'=>array(
                'label'        => _('Color image'),
                'shortlabel'   => _('Color image'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('Images')
            ),
            'Size'=>array(
                'label'        => _('Available sizes'),
                'shortlabel'   => _('Available sizes'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Comment'=>array(
                'label'        => _('Comment'),
                'shortlabel'   => _('Comment'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('Comment')
            ));
        return $return;
    }

    // }}}
}

?>