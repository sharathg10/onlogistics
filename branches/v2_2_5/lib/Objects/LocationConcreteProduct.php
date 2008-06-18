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

class LocationConcreteProduct extends Object {
    
    // Constructeur {{{

    /**
     * LocationConcreteProduct::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // ConcreteProduct foreignkey property + getter/setter {{{

    /**
     * ConcreteProduct foreignkey
     *
     * @access private
     * @var mixed object ConcreteProduct or integer
     */
    private $_ConcreteProduct = false;

    /**
     * LocationConcreteProduct::getConcreteProduct
     *
     * @access public
     * @return object ConcreteProduct
     */
    public function getConcreteProduct() {
        if (is_int($this->_ConcreteProduct) && $this->_ConcreteProduct > 0) {
            $mapper = Mapper::singleton('ConcreteProduct');
            $this->_ConcreteProduct = $mapper->load(
                array('Id'=>$this->_ConcreteProduct));
        }
        return $this->_ConcreteProduct;
    }

    /**
     * LocationConcreteProduct::getConcreteProductId
     *
     * @access public
     * @return integer
     */
    public function getConcreteProductId() {
        if ($this->_ConcreteProduct instanceof ConcreteProduct) {
            return $this->_ConcreteProduct->getId();
        }
        return (int)$this->_ConcreteProduct;
    }

    /**
     * LocationConcreteProduct::setConcreteProduct
     *
     * @access public
     * @param object ConcreteProduct $value
     * @return void
     */
    public function setConcreteProduct($value) {
        if (is_numeric($value)) {
            $this->_ConcreteProduct = (int)$value;
        } else {
            $this->_ConcreteProduct = $value;
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
     * LocationConcreteProduct::getLocation
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
     * LocationConcreteProduct::getLocationId
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
     * LocationConcreteProduct::setLocation
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
    // Quantity float property + getter/setter {{{

    /**
     * Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Quantity = 0;

    /**
     * LocationConcreteProduct::getQuantity
     *
     * @access public
     * @return float
     */
    public function getQuantity() {
        return $this->_Quantity;
    }

    /**
     * LocationConcreteProduct::setQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setQuantity($value) {
        if ($value !== null) {
            $this->_Quantity = round(I18N::extractNumber($value), 3);
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
        return 'LocationConcreteProduct';
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
            'ConcreteProduct' => 'ConcreteProduct',
            'Location' => 'Location',
            'Quantity' => Object::TYPE_DECIMAL);
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