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

class PropertyValue extends Object {
    
    // Constructeur {{{

    /**
     * PropertyValue::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
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
     * PropertyValue::getProduct
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
     * PropertyValue::getProductId
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
     * PropertyValue::setProduct
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
    // Property foreignkey property + getter/setter {{{

    /**
     * Property foreignkey
     *
     * @access private
     * @var mixed object Property or integer
     */
    private $_Property = false;

    /**
     * PropertyValue::getProperty
     *
     * @access public
     * @return object Property
     */
    public function getProperty() {
        if (is_int($this->_Property) && $this->_Property > 0) {
            $mapper = Mapper::singleton('Property');
            $this->_Property = $mapper->load(
                array('Id'=>$this->_Property));
        }
        return $this->_Property;
    }

    /**
     * PropertyValue::getPropertyId
     *
     * @access public
     * @return integer
     */
    public function getPropertyId() {
        if ($this->_Property instanceof Property) {
            return $this->_Property->getId();
        }
        return (int)$this->_Property;
    }

    /**
     * PropertyValue::setProperty
     *
     * @access public
     * @param object Property $value
     * @return void
     */
    public function setProperty($value) {
        if (is_numeric($value)) {
            $this->_Property = (int)$value;
        } else {
            $this->_Property = $value;
        }
    }

    // }}}
    // StringValue string property + getter/setter {{{

    /**
     * StringValue string property
     *
     * @access private
     * @var string
     */
    private $_StringValue = '';

    /**
     * PropertyValue::getStringValue
     *
     * @access public
     * @return string
     */
    public function getStringValue() {
        return $this->_StringValue;
    }

    /**
     * PropertyValue::setStringValue
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStringValue($value) {
        $this->_StringValue = $value;
    }

    // }}}
    // IntValue string property + getter/setter {{{

    /**
     * IntValue int property
     *
     * @access private
     * @var integer
     */
    private $_IntValue = 0;

    /**
     * PropertyValue::getIntValue
     *
     * @access public
     * @return integer
     */
    public function getIntValue() {
        return $this->_IntValue;
    }

    /**
     * PropertyValue::setIntValue
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setIntValue($value) {
        if ($value !== null) {
            $this->_IntValue = (int)$value;
        }
    }

    // }}}
    // FloatValue float property + getter/setter {{{

    /**
     * FloatValue float property
     *
     * @access private
     * @var float
     */
    private $_FloatValue = 0;

    /**
     * PropertyValue::getFloatValue
     *
     * @access public
     * @return float
     */
    public function getFloatValue() {
        return $this->_FloatValue;
    }

    /**
     * PropertyValue::setFloatValue
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setFloatValue($value) {
        if ($value !== null) {
            $this->_FloatValue = I18N::extractNumber($value);
        }
    }

    // }}}
    // DateValue datetime property + getter/setter {{{

    /**
     * DateValue int property
     *
     * @access private
     * @var string
     */
    private $_DateValue = 0;

    /**
     * PropertyValue::getDateValue
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getDateValue($format = false) {
        return $this->dateFormat($this->_DateValue, $format);
    }

    /**
     * PropertyValue::setDateValue
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDateValue($value) {
        $this->_DateValue = $value;
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
        return 'PropertyValue';
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
            'Product' => 'Product',
            'Property' => 'Property',
            'StringValue' => Object::TYPE_STRING,
            'IntValue' => Object::TYPE_INT,
            'FloatValue' => Object::TYPE_FLOAT,
            'DateValue' => Object::TYPE_DATETIME);
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