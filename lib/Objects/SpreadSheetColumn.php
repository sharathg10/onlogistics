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

class SpreadSheetColumn extends Object {
    
    // Constructeur {{{

    /**
     * SpreadSheetColumn::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Name string property + getter/setter {{{

    /**
     * Name string property
     *
     * @access private
     * @var string
     */
    private $_Name = '';

    /**
     * SpreadSheetColumn::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * SpreadSheetColumn::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // PropertyName string property + getter/setter {{{

    /**
     * PropertyName string property
     *
     * @access private
     * @var string
     */
    private $_PropertyName = '';

    /**
     * SpreadSheetColumn::getPropertyName
     *
     * @access public
     * @return string
     */
    public function getPropertyName() {
        return $this->_PropertyName;
    }

    /**
     * SpreadSheetColumn::setPropertyName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPropertyName($value) {
        $this->_PropertyName = $value;
    }

    // }}}
    // FkeyPropertyName string property + getter/setter {{{

    /**
     * FkeyPropertyName string property
     *
     * @access private
     * @var string
     */
    private $_FkeyPropertyName = '';

    /**
     * SpreadSheetColumn::getFkeyPropertyName
     *
     * @access public
     * @return string
     */
    public function getFkeyPropertyName() {
        return $this->_FkeyPropertyName;
    }

    /**
     * SpreadSheetColumn::setFkeyPropertyName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setFkeyPropertyName($value) {
        $this->_FkeyPropertyName = $value;
    }

    // }}}
    // PropertyType int property + getter/setter {{{

    /**
     * PropertyType int property
     *
     * @access private
     * @var integer
     */
    private $_PropertyType = null;

    /**
     * SpreadSheetColumn::getPropertyType
     *
     * @access public
     * @return integer
     */
    public function getPropertyType() {
        return $this->_PropertyType;
    }

    /**
     * SpreadSheetColumn::setPropertyType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPropertyType($value) {
        $this->_PropertyType = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // PropertyClass string property + getter/setter {{{

    /**
     * PropertyClass string property
     *
     * @access private
     * @var string
     */
    private $_PropertyClass = '';

    /**
     * SpreadSheetColumn::getPropertyClass
     *
     * @access public
     * @return string
     */
    public function getPropertyClass() {
        return $this->_PropertyClass;
    }

    /**
     * SpreadSheetColumn::setPropertyClass
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPropertyClass($value) {
        $this->_PropertyClass = $value;
    }

    // }}}
    // Order string property + getter/setter {{{

    /**
     * Order int property
     *
     * @access private
     * @var integer
     */
    private $_Order = 0;

    /**
     * SpreadSheetColumn::getOrder
     *
     * @access public
     * @return integer
     */
    public function getOrder() {
        return $this->_Order;
    }

    /**
     * SpreadSheetColumn::setOrder
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setOrder($value) {
        if ($value !== null) {
            $this->_Order = (int)$value;
        }
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
     * SpreadSheetColumn::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * SpreadSheetColumn::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // Default string property + getter/setter {{{

    /**
     * Default string property
     *
     * @access private
     * @var string
     */
    private $_Default = '';

    /**
     * SpreadSheetColumn::getDefault
     *
     * @access public
     * @return string
     */
    public function getDefault() {
        return $this->_Default;
    }

    /**
     * SpreadSheetColumn::setDefault
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDefault($value) {
        $this->_Default = $value;
    }

    // }}}
    // Width string property + getter/setter {{{

    /**
     * Width int property
     *
     * @access private
     * @var integer
     */
    private $_Width = 0;

    /**
     * SpreadSheetColumn::getWidth
     *
     * @access public
     * @return integer
     */
    public function getWidth() {
        return $this->_Width;
    }

    /**
     * SpreadSheetColumn::setWidth
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setWidth($value) {
        if ($value !== null) {
            $this->_Width = (int)$value;
        }
    }

    // }}}
    // Required string property + getter/setter {{{

    /**
     * Required int property
     *
     * @access private
     * @var integer
     */
    private $_Required = 0;

    /**
     * SpreadSheetColumn::getRequired
     *
     * @access public
     * @return integer
     */
    public function getRequired() {
        return $this->_Required;
    }

    /**
     * SpreadSheetColumn::setRequired
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setRequired($value) {
        if ($value !== null) {
            $this->_Required = (int)$value;
        }
    }

    // }}}
    // SpreadSheet foreignkey property + getter/setter {{{

    /**
     * SpreadSheet foreignkey
     *
     * @access private
     * @var mixed object SpreadSheet or integer
     */
    private $_SpreadSheet = false;

    /**
     * SpreadSheetColumn::getSpreadSheet
     *
     * @access public
     * @return object SpreadSheet
     */
    public function getSpreadSheet() {
        if (is_int($this->_SpreadSheet) && $this->_SpreadSheet > 0) {
            $mapper = Mapper::singleton('SpreadSheet');
            $this->_SpreadSheet = $mapper->load(
                array('Id'=>$this->_SpreadSheet));
        }
        return $this->_SpreadSheet;
    }

    /**
     * SpreadSheetColumn::getSpreadSheetId
     *
     * @access public
     * @return integer
     */
    public function getSpreadSheetId() {
        if ($this->_SpreadSheet instanceof SpreadSheet) {
            return $this->_SpreadSheet->getId();
        }
        return (int)$this->_SpreadSheet;
    }

    /**
     * SpreadSheetColumn::setSpreadSheet
     *
     * @access public
     * @param object SpreadSheet $value
     * @return void
     */
    public function setSpreadSheet($value) {
        if (is_numeric($value)) {
            $this->_SpreadSheet = (int)$value;
        } else {
            $this->_SpreadSheet = $value;
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
        return 'SpreadSheetColumn';
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
            'Name' => Object::TYPE_STRING,
            'PropertyName' => Object::TYPE_STRING,
            'FkeyPropertyName' => Object::TYPE_STRING,
            'PropertyType' => Object::TYPE_INT,
            'PropertyClass' => Object::TYPE_STRING,
            'Order' => Object::TYPE_INT,
            'Comment' => Object::TYPE_TEXT,
            'Default' => Object::TYPE_STRING,
            'Width' => Object::TYPE_INT,
            'Required' => Object::TYPE_BOOL,
            'SpreadSheet' => 'SpreadSheet');
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