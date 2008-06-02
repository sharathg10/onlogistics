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

class _ProductQuantityByCategory extends Object {
    // class constants {{{

    const QTY_TYPE_QCONQUE = 0;
    const QTY_TYPE_MULTIPLE = 1;

    // }}}
    // Constructeur {{{

    /**
     * _ProductQuantityByCategory::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // MinimumQuantity float property + getter/setter {{{

    /**
     * MinimumQuantity float property
     *
     * @access private
     * @var float
     */
    private $_MinimumQuantity = null;

    /**
     * _ProductQuantityByCategory::getMinimumQuantity
     *
     * @access public
     * @return float
     */
    public function getMinimumQuantity() {
        return $this->_MinimumQuantity;
    }

    /**
     * _ProductQuantityByCategory::setMinimumQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMinimumQuantity($value) {
        $this->_MinimumQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // MinimumQuantityType const property + getter/setter/getMinimumQuantityTypeConstArray {{{

    /**
     * MinimumQuantityType int property
     *
     * @access private
     * @var integer
     */
    private $_MinimumQuantityType = 0;

    /**
     * _ProductQuantityByCategory::getMinimumQuantityType
     *
     * @access public
     * @return integer
     */
    public function getMinimumQuantityType() {
        return $this->_MinimumQuantityType;
    }

    /**
     * _ProductQuantityByCategory::setMinimumQuantityType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setMinimumQuantityType($value) {
        if ($value !== null) {
            $this->_MinimumQuantityType = (int)$value;
        }
    }

    /**
     * _ProductQuantityByCategory::getMinimumQuantityTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getMinimumQuantityTypeConstArray($keys = false) {
        $array = array(
            _ProductQuantityByCategory::QTY_TYPE_QCONQUE => _("any"), 
            _ProductQuantityByCategory::QTY_TYPE_MULTIPLE => _("multiple")
        );
        asort($array);
        return $keys?array_keys($array):$array;
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
     * _ProductQuantityByCategory::getProduct
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
     * _ProductQuantityByCategory::getProductId
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
     * _ProductQuantityByCategory::setProduct
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
    // Category foreignkey property + getter/setter {{{

    /**
     * Category foreignkey
     *
     * @access private
     * @var mixed object Category or integer
     */
    private $_Category = false;

    /**
     * _ProductQuantityByCategory::getCategory
     *
     * @access public
     * @return object Category
     */
    public function getCategory() {
        if (is_int($this->_Category) && $this->_Category > 0) {
            $mapper = Mapper::singleton('Category');
            $this->_Category = $mapper->load(
                array('Id'=>$this->_Category));
        }
        return $this->_Category;
    }

    /**
     * _ProductQuantityByCategory::getCategoryId
     *
     * @access public
     * @return integer
     */
    public function getCategoryId() {
        if ($this->_Category instanceof Category) {
            return $this->_Category->getId();
        }
        return (int)$this->_Category;
    }

    /**
     * _ProductQuantityByCategory::setCategory
     *
     * @access public
     * @param object Category $value
     * @return void
     */
    public function setCategory($value) {
        if (is_numeric($value)) {
            $this->_Category = (int)$value;
        } else {
            $this->_Category = $value;
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
        return 'ProductQuantityByCategory';
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
            'MinimumQuantity' => Object::TYPE_DECIMAL,
            'MinimumQuantityType' => Object::TYPE_CONST,
            'Product' => 'Product',
            'Category' => 'Category');
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