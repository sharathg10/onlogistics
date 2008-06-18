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

class _LEMConcreteProduct extends Object {
    // class constants {{{

    const LEMCP_NORMAL = 0;
    const LEMCP_CANCELLER = 1;
    const LEMCP_CANCELLED = -1;

    // }}}
    // Constructeur {{{

    /**
     * _LEMConcreteProduct::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // LocationExecutedMovement foreignkey property + getter/setter {{{

    /**
     * LocationExecutedMovement foreignkey
     *
     * @access private
     * @var mixed object LocationExecutedMovement or integer
     */
    private $_LocationExecutedMovement = false;

    /**
     * _LEMConcreteProduct::getLocationExecutedMovement
     *
     * @access public
     * @return object LocationExecutedMovement
     */
    public function getLocationExecutedMovement() {
        if (is_int($this->_LocationExecutedMovement) && $this->_LocationExecutedMovement > 0) {
            $mapper = Mapper::singleton('LocationExecutedMovement');
            $this->_LocationExecutedMovement = $mapper->load(
                array('Id'=>$this->_LocationExecutedMovement));
        }
        return $this->_LocationExecutedMovement;
    }

    /**
     * _LEMConcreteProduct::getLocationExecutedMovementId
     *
     * @access public
     * @return integer
     */
    public function getLocationExecutedMovementId() {
        if ($this->_LocationExecutedMovement instanceof LocationExecutedMovement) {
            return $this->_LocationExecutedMovement->getId();
        }
        return (int)$this->_LocationExecutedMovement;
    }

    /**
     * _LEMConcreteProduct::setLocationExecutedMovement
     *
     * @access public
     * @param object LocationExecutedMovement $value
     * @return void
     */
    public function setLocationExecutedMovement($value) {
        if (is_numeric($value)) {
            $this->_LocationExecutedMovement = (int)$value;
        } else {
            $this->_LocationExecutedMovement = $value;
        }
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
     * _LEMConcreteProduct::getConcreteProduct
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
     * _LEMConcreteProduct::getConcreteProductId
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
     * _LEMConcreteProduct::setConcreteProduct
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
    // Quantity float property + getter/setter {{{

    /**
     * Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Quantity = 0;

    /**
     * _LEMConcreteProduct::getQuantity
     *
     * @access public
     * @return float
     */
    public function getQuantity() {
        return $this->_Quantity;
    }

    /**
     * _LEMConcreteProduct::setQuantity
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
    // Cancelled const property + getter/setter/getCancelledConstArray {{{

    /**
     * Cancelled int property
     *
     * @access private
     * @var integer
     */
    private $_Cancelled = 0;

    /**
     * _LEMConcreteProduct::getCancelled
     *
     * @access public
     * @return integer
     */
    public function getCancelled() {
        return $this->_Cancelled;
    }

    /**
     * _LEMConcreteProduct::setCancelled
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCancelled($value) {
        if ($value !== null) {
            $this->_Cancelled = (int)$value;
        }
    }

    /**
     * _LEMConcreteProduct::getCancelledConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getCancelledConstArray($keys = false) {
        $array = array(
            _LEMConcreteProduct::LEMCP_NORMAL => _("normal"), 
            _LEMConcreteProduct::LEMCP_CANCELLER => _("That caused cancellation"), 
            _LEMConcreteProduct::LEMCP_CANCELLED => _("Cancelled")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // CancelledLEMConcreteProduct foreignkey property + getter/setter {{{

    /**
     * CancelledLEMConcreteProduct foreignkey
     *
     * @access private
     * @var mixed object LEMConcreteProduct or integer
     */
    private $_CancelledLEMConcreteProduct = false;

    /**
     * _LEMConcreteProduct::getCancelledLEMConcreteProduct
     *
     * @access public
     * @return object LEMConcreteProduct
     */
    public function getCancelledLEMConcreteProduct() {
        if (is_int($this->_CancelledLEMConcreteProduct) && $this->_CancelledLEMConcreteProduct > 0) {
            $mapper = Mapper::singleton('LEMConcreteProduct');
            $this->_CancelledLEMConcreteProduct = $mapper->load(
                array('Id'=>$this->_CancelledLEMConcreteProduct));
        }
        return $this->_CancelledLEMConcreteProduct;
    }

    /**
     * _LEMConcreteProduct::getCancelledLEMConcreteProductId
     *
     * @access public
     * @return integer
     */
    public function getCancelledLEMConcreteProductId() {
        if ($this->_CancelledLEMConcreteProduct instanceof LEMConcreteProduct) {
            return $this->_CancelledLEMConcreteProduct->getId();
        }
        return (int)$this->_CancelledLEMConcreteProduct;
    }

    /**
     * _LEMConcreteProduct::setCancelledLEMConcreteProduct
     *
     * @access public
     * @param object LEMConcreteProduct $value
     * @return void
     */
    public function setCancelledLEMConcreteProduct($value) {
        if (is_numeric($value)) {
            $this->_CancelledLEMConcreteProduct = (int)$value;
        } else {
            $this->_CancelledLEMConcreteProduct = $value;
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
        return 'LEMConcreteProduct';
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
            'LocationExecutedMovement' => 'LocationExecutedMovement',
            'ConcreteProduct' => 'ConcreteProduct',
            'Quantity' => Object::TYPE_DECIMAL,
            'Cancelled' => Object::TYPE_CONST,
            'CancelledLEMConcreteProduct' => 'LEMConcreteProduct');
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
            'LEMConcreteProduct'=>array(
                'linkClass'     => 'LEMConcreteProduct',
                'field'         => 'CancelledLEMConcreteProduct',
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