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

class ProductHandingByCategory extends Object {
    // class constants {{{

    const TYPE_PERCENT = 1;
    const TYPE_AMOUNT = 2;

    // }}}
    // Constructeur {{{

    /**
     * ProductHandingByCategory::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // UpdateDate datetime property + getter/setter {{{

    /**
     * UpdateDate int property
     *
     * @access private
     * @var string
     */
    private $_UpdateDate = 0;

    /**
     * ProductHandingByCategory::getUpdateDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getUpdateDate($format = false) {
        return $this->dateFormat($this->_UpdateDate, $format);
    }

    /**
     * ProductHandingByCategory::setUpdateDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setUpdateDate($value) {
        $this->_UpdateDate = $value;
    }

    // }}}
    // Handing float property + getter/setter {{{

    /**
     * Handing float property
     *
     * @access private
     * @var float
     */
    private $_Handing = 0;

    /**
     * ProductHandingByCategory::getHanding
     *
     * @access public
     * @return float
     */
    public function getHanding() {
        return $this->_Handing;
    }

    /**
     * ProductHandingByCategory::setHanding
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setHanding($value) {
        if ($value !== null) {
            $this->_Handing = round(I18N::extractNumber($value), 2);
        }
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
     * ProductHandingByCategory::getProduct
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
     * ProductHandingByCategory::getProductId
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
     * ProductHandingByCategory::setProduct
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
    // Type const property + getter/setter/getTypeConstArray {{{

    /**
     * Type int property
     *
     * @access private
     * @var integer
     */
    private $_Type = 1;

    /**
     * ProductHandingByCategory::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * ProductHandingByCategory::setType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setType($value) {
        if ($value !== null) {
            $this->_Type = (int)$value;
        }
    }

    /**
     * ProductHandingByCategory::getTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getTypeConstArray($keys = false) {
        $array = array(
            ProductHandingByCategory::TYPE_PERCENT => _("%"), 
            ProductHandingByCategory::TYPE_AMOUNT => _("Total")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Currency foreignkey property + getter/setter {{{

    /**
     * Currency foreignkey
     *
     * @access private
     * @var mixed object Currency or integer
     */
    private $_Currency = false;

    /**
     * ProductHandingByCategory::getCurrency
     *
     * @access public
     * @return object Currency
     */
    public function getCurrency() {
        if (is_int($this->_Currency) && $this->_Currency > 0) {
            $mapper = Mapper::singleton('Currency');
            $this->_Currency = $mapper->load(
                array('Id'=>$this->_Currency));
        }
        return $this->_Currency;
    }

    /**
     * ProductHandingByCategory::getCurrencyId
     *
     * @access public
     * @return integer
     */
    public function getCurrencyId() {
        if ($this->_Currency instanceof Currency) {
            return $this->_Currency->getId();
        }
        return (int)$this->_Currency;
    }

    /**
     * ProductHandingByCategory::setCurrency
     *
     * @access public
     * @param object Currency $value
     * @return void
     */
    public function setCurrency($value) {
        if (is_numeric($value)) {
            $this->_Currency = (int)$value;
        } else {
            $this->_Currency = $value;
        }
    }

    // }}}
    // Category one to many relation + getter/setter {{{

    /**
     * Category *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CategoryCollection = false;

    /**
     * ProductHandingByCategory::getCategoryCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCategoryCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ProductHandingByCategory');
            return $mapper->getManyToMany($this->getId(),
                'Category', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CategoryCollection) {
            $mapper = Mapper::singleton('ProductHandingByCategory');
            $this->_CategoryCollection = $mapper->getManyToMany($this->getId(),
                'Category');
        }
        return $this->_CategoryCollection;
    }

    /**
     * ProductHandingByCategory::getCategoryCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCategoryCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getCategoryCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_CategoryCollection) {
            $mapper = Mapper::singleton('ProductHandingByCategory');
            return $mapper->getManyToManyIds($this->getId(), 'Category');
        }
        return $this->_CategoryCollection->getItemIds();
    }

    /**
     * ProductHandingByCategory::setCategoryCollectionIds
     *
     * @access public
     * @return array
     */
    public function setCategoryCollectionIds($itemIds) {
        $this->_CategoryCollection = new Collection('Category');
        foreach ($itemIds as $id) {
            $this->_CategoryCollection->setItem($id);
        }
    }

    /**
     * ProductHandingByCategory::setCategoryCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCategoryCollection($value) {
        $this->_CategoryCollection = $value;
    }

    /**
     * ProductHandingByCategory::CategoryCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function CategoryCollectionIsLoaded() {
        return ($this->_CategoryCollection !== false);
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
        return 'ProductHandingByCategory';
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
            'UpdateDate' => Object::TYPE_DATETIME,
            'Handing' => Object::TYPE_DECIMAL,
            'Product' => 'Product',
            'Type' => Object::TYPE_CONST,
            'Currency' => 'Currency');
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
            'Category'=>array(
                'linkClass'     => 'Category',
                'field'         => 'FromProductHandingByCategory',
                'linkTable'     => 'phcCategory',
                'linkField'     => 'ToCategory',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
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