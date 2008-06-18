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

class _ProductSubstitution extends Object {
    
    // Constructeur {{{

    /**
     * _ProductSubstitution::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // FromProduct foreignkey property + getter/setter {{{

    /**
     * FromProduct foreignkey
     *
     * @access private
     * @var mixed object Product or integer
     */
    private $_FromProduct = false;

    /**
     * _ProductSubstitution::getFromProduct
     *
     * @access public
     * @return object Product
     */
    public function getFromProduct() {
        if (is_int($this->_FromProduct) && $this->_FromProduct > 0) {
            $mapper = Mapper::singleton('Product');
            $this->_FromProduct = $mapper->load(
                array('Id'=>$this->_FromProduct));
        }
        return $this->_FromProduct;
    }

    /**
     * _ProductSubstitution::getFromProductId
     *
     * @access public
     * @return integer
     */
    public function getFromProductId() {
        if ($this->_FromProduct instanceof Product) {
            return $this->_FromProduct->getId();
        }
        return (int)$this->_FromProduct;
    }

    /**
     * _ProductSubstitution::setFromProduct
     *
     * @access public
     * @param object Product $value
     * @return void
     */
    public function setFromProduct($value) {
        if (is_numeric($value)) {
            $this->_FromProduct = (int)$value;
        } else {
            $this->_FromProduct = $value;
        }
    }

    // }}}
    // ByProduct foreignkey property + getter/setter {{{

    /**
     * ByProduct foreignkey
     *
     * @access private
     * @var mixed object Product or integer
     */
    private $_ByProduct = false;

    /**
     * _ProductSubstitution::getByProduct
     *
     * @access public
     * @return object Product
     */
    public function getByProduct() {
        if (is_int($this->_ByProduct) && $this->_ByProduct > 0) {
            $mapper = Mapper::singleton('Product');
            $this->_ByProduct = $mapper->load(
                array('Id'=>$this->_ByProduct));
        }
        return $this->_ByProduct;
    }

    /**
     * _ProductSubstitution::getByProductId
     *
     * @access public
     * @return integer
     */
    public function getByProductId() {
        if ($this->_ByProduct instanceof Product) {
            return $this->_ByProduct->getId();
        }
        return (int)$this->_ByProduct;
    }

    /**
     * _ProductSubstitution::setByProduct
     *
     * @access public
     * @param object Product $value
     * @return void
     */
    public function setByProduct($value) {
        if (is_numeric($value)) {
            $this->_ByProduct = (int)$value;
        } else {
            $this->_ByProduct = $value;
        }
    }

    // }}}
    // Interchangeable string property + getter/setter {{{

    /**
     * Interchangeable int property
     *
     * @access private
     * @var integer
     */
    private $_Interchangeable = 0;

    /**
     * _ProductSubstitution::getInterchangeable
     *
     * @access public
     * @return integer
     */
    public function getInterchangeable() {
        return $this->_Interchangeable;
    }

    /**
     * _ProductSubstitution::setInterchangeable
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setInterchangeable($value) {
        if ($value !== null) {
            $this->_Interchangeable = (int)$value;
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
        return 'ProductSubstitution';
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
            'FromProduct' => 'Product',
            'ByProduct' => 'Product',
            'Interchangeable' => Object::TYPE_BOOL);
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