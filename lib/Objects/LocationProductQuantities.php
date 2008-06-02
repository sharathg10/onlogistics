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

class LocationProductQuantities extends Object {
    
    // Constructeur {{{

    /**
     * LocationProductQuantities::__construct()
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
     * LocationProductQuantities::getProduct
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
     * LocationProductQuantities::getProductId
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
     * LocationProductQuantities::setProduct
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
    // Location foreignkey property + getter/setter {{{

    /**
     * Location foreignkey
     *
     * @access private
     * @var mixed object Location or integer
     */
    private $_Location = false;

    /**
     * LocationProductQuantities::getLocation
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
     * LocationProductQuantities::getLocationId
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
     * LocationProductQuantities::setLocation
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
    // RealQuantity float property + getter/setter {{{

    /**
     * RealQuantity float property
     *
     * @access private
     * @var float
     */
    private $_RealQuantity = 0;

    /**
     * LocationProductQuantities::getRealQuantity
     *
     * @access public
     * @return float
     */
    public function getRealQuantity() {
        return $this->_RealQuantity;
    }

    /**
     * LocationProductQuantities::setRealQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealQuantity($value) {
        if ($value !== null) {
            $this->_RealQuantity = round(I18N::extractNumber($value), 3);
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
     * LocationProductQuantities::getActivated
     *
     * @access public
     * @return integer
     */
    public function getActivated() {
        return $this->_Activated;
    }

    /**
     * LocationProductQuantities::setActivated
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
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'LocationProductQuantities';
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
            'Location' => 'Location',
            'RealQuantity' => Object::TYPE_DECIMAL,
            'Activated' => Object::TYPE_BOOL);
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