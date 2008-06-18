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

class InventoryDetail extends Object {
    
    // Constructeur {{{

    /**
     * InventoryDetail::__construct()
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
     * InventoryDetail::getProduct
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
     * InventoryDetail::getProductId
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
     * InventoryDetail::setProduct
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
    // ProductReference string property + getter/setter {{{

    /**
     * ProductReference string property
     *
     * @access private
     * @var string
     */
    private $_ProductReference = '';

    /**
     * InventoryDetail::getProductReference
     *
     * @access public
     * @return string
     */
    public function getProductReference() {
        return $this->_ProductReference;
    }

    /**
     * InventoryDetail::setProductReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setProductReference($value) {
        $this->_ProductReference = $value;
    }

    // }}}
    // Currency string property + getter/setter {{{

    /**
     * Currency string property
     *
     * @access private
     * @var string
     */
    private $_Currency = '';

    /**
     * InventoryDetail::getCurrency
     *
     * @access public
     * @return string
     */
    public function getCurrency() {
        return $this->_Currency;
    }

    /**
     * InventoryDetail::setCurrency
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCurrency($value) {
        $this->_Currency = $value;
    }

    // }}}
    // BuyingPriceHT float property + getter/setter {{{

    /**
     * BuyingPriceHT float property
     *
     * @access private
     * @var float
     */
    private $_BuyingPriceHT = null;

    /**
     * InventoryDetail::getBuyingPriceHT
     *
     * @access public
     * @return float
     */
    public function getBuyingPriceHT() {
        return $this->_BuyingPriceHT;
    }

    /**
     * InventoryDetail::setBuyingPriceHT
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setBuyingPriceHT($value) {
        $this->_BuyingPriceHT = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // Location foreignkey property + getter/setter {{{

    /**
     * Location foreignkey
     *
     * @access private
     * @var mixed object Location or integer
     */
    private $_Location = false;

    /**
     * InventoryDetail::getLocation
     *
     * @access public
     * @return object Location
     */
    public function getLocation() {
        if (is_int($this->_Location) && $this->_Location > 0) {
            $mapper = Mapper::singleton('Location');
            $this->_Location = $mapper->load(
                array('Id'=>$this->_Location));
        }
        return $this->_Location;
    }

    /**
     * InventoryDetail::getLocationId
     *
     * @access public
     * @return integer
     */
    public function getLocationId() {
        if ($this->_Location instanceof Location) {
            return $this->_Location->getId();
        }
        return (int)$this->_Location;
    }

    /**
     * InventoryDetail::setLocation
     *
     * @access public
     * @param object Location $value
     * @return void
     */
    public function setLocation($value) {
        if (is_numeric($value)) {
            $this->_Location = (int)$value;
        } else {
            $this->_Location = $value;
        }
    }

    // }}}
    // LocationName string property + getter/setter {{{

    /**
     * LocationName string property
     *
     * @access private
     * @var string
     */
    private $_LocationName = '';

    /**
     * InventoryDetail::getLocationName
     *
     * @access public
     * @return string
     */
    public function getLocationName() {
        return $this->_LocationName;
    }

    /**
     * InventoryDetail::setLocationName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setLocationName($value) {
        $this->_LocationName = $value;
    }

    // }}}
    // Quantity float property + getter/setter {{{

    /**
     * Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Quantity = null;

    /**
     * InventoryDetail::getQuantity
     *
     * @access public
     * @return float
     */
    public function getQuantity() {
        return $this->_Quantity;
    }

    /**
     * InventoryDetail::setQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setQuantity($value) {
        $this->_Quantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // Inventory foreignkey property + getter/setter {{{

    /**
     * Inventory foreignkey
     *
     * @access private
     * @var mixed object Inventory or integer
     */
    private $_Inventory = false;

    /**
     * InventoryDetail::getInventory
     *
     * @access public
     * @return object Inventory
     */
    public function getInventory() {
        if (is_int($this->_Inventory) && $this->_Inventory > 0) {
            $mapper = Mapper::singleton('Inventory');
            $this->_Inventory = $mapper->load(
                array('Id'=>$this->_Inventory));
        }
        return $this->_Inventory;
    }

    /**
     * InventoryDetail::getInventoryId
     *
     * @access public
     * @return integer
     */
    public function getInventoryId() {
        if ($this->_Inventory instanceof Inventory) {
            return $this->_Inventory->getId();
        }
        return (int)$this->_Inventory;
    }

    /**
     * InventoryDetail::setInventory
     *
     * @access public
     * @param object Inventory $value
     * @return void
     */
    public function setInventory($value) {
        if (is_numeric($value)) {
            $this->_Inventory = (int)$value;
        } else {
            $this->_Inventory = $value;
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
        return 'InventoryDetail';
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
            'ProductReference' => Object::TYPE_STRING,
            'Currency' => Object::TYPE_STRING,
            'BuyingPriceHT' => Object::TYPE_DECIMAL,
            'Location' => 'Location',
            'LocationName' => Object::TYPE_STRING,
            'Quantity' => Object::TYPE_DECIMAL,
            'Inventory' => 'Inventory');
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