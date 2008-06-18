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

class _Promotion extends Object {
    // class constants {{{

    const PROMO_TYPE_MONTANT = 0;
    const PROMO_TYPE_PERCENT = 1;

    // }}}
    // Constructeur {{{

    /**
     * _Promotion::__construct()
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
     * _Promotion::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _Promotion::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // StartDate datetime property + getter/setter {{{

    /**
     * StartDate int property
     *
     * @access private
     * @var string
     */
    private $_StartDate = 0;

    /**
     * _Promotion::getStartDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getStartDate($format = false) {
        return $this->dateFormat($this->_StartDate, $format);
    }

    /**
     * _Promotion::setStartDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStartDate($value) {
        $this->_StartDate = $value;
    }

    // }}}
    // EndDate datetime property + getter/setter {{{

    /**
     * EndDate int property
     *
     * @access private
     * @var string
     */
    private $_EndDate = 0;

    /**
     * _Promotion::getEndDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEndDate($format = false) {
        return $this->dateFormat($this->_EndDate, $format);
    }

    /**
     * _Promotion::setEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndDate($value) {
        $this->_EndDate = $value;
    }

    // }}}
    // Rate float property + getter/setter {{{

    /**
     * Rate float property
     *
     * @access private
     * @var float
     */
    private $_Rate = 0;

    /**
     * _Promotion::getRate
     *
     * @access public
     * @return float
     */
    public function getRate() {
        return $this->_Rate;
    }

    /**
     * _Promotion::setRate
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRate($value) {
        if ($value !== null) {
            $this->_Rate = round(I18N::extractNumber($value), 2);
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
    private $_Type = 0;

    /**
     * _Promotion::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * _Promotion::setType
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
     * _Promotion::getTypeConstArray
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
            _Promotion::PROMO_TYPE_MONTANT => _("Total"), 
            _Promotion::PROMO_TYPE_PERCENT => _("%")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // ApproImpactRate float property + getter/setter {{{

    /**
     * ApproImpactRate float property
     *
     * @access private
     * @var float
     */
    private $_ApproImpactRate = 0;

    /**
     * _Promotion::getApproImpactRate
     *
     * @access public
     * @return float
     */
    public function getApproImpactRate() {
        return $this->_ApproImpactRate;
    }

    /**
     * _Promotion::setApproImpactRate
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setApproImpactRate($value) {
        if ($value !== null) {
            $this->_ApproImpactRate = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Product one to many relation + getter/setter {{{

    /**
     * Product *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductCollection = false;

    /**
     * _Promotion::getProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Promotion');
            return $mapper->getManyToMany($this->getId(),
                'Product', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductCollection) {
            $mapper = Mapper::singleton('Promotion');
            $this->_ProductCollection = $mapper->getManyToMany($this->getId(),
                'Product');
        }
        return $this->_ProductCollection;
    }

    /**
     * _Promotion::getProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getProductCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ProductCollection) {
            $mapper = Mapper::singleton('Promotion');
            return $mapper->getManyToManyIds($this->getId(), 'Product');
        }
        return $this->_ProductCollection->getItemIds();
    }

    /**
     * _Promotion::setProductCollectionIds
     *
     * @access public
     * @return array
     */
    public function setProductCollectionIds($itemIds) {
        $this->_ProductCollection = new Collection('Product');
        foreach ($itemIds as $id) {
            $this->_ProductCollection->setItem($id);
        }
    }

    /**
     * _Promotion::setProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductCollection($value) {
        $this->_ProductCollection = $value;
    }

    /**
     * _Promotion::ProductCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ProductCollectionIsLoaded() {
        return ($this->_ProductCollection !== false);
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
     * _Promotion::getCategoryCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCategoryCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Promotion');
            return $mapper->getManyToMany($this->getId(),
                'Category', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CategoryCollection) {
            $mapper = Mapper::singleton('Promotion');
            $this->_CategoryCollection = $mapper->getManyToMany($this->getId(),
                'Category');
        }
        return $this->_CategoryCollection;
    }

    /**
     * _Promotion::getCategoryCollectionIds
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
            $mapper = Mapper::singleton('Promotion');
            return $mapper->getManyToManyIds($this->getId(), 'Category');
        }
        return $this->_CategoryCollection->getItemIds();
    }

    /**
     * _Promotion::setCategoryCollectionIds
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
     * _Promotion::setCategoryCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCategoryCollection($value) {
        $this->_CategoryCollection = $value;
    }

    /**
     * _Promotion::CategoryCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function CategoryCollectionIsLoaded() {
        return ($this->_CategoryCollection !== false);
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
     * _Promotion::getCurrency
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
     * _Promotion::getCurrencyId
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
     * _Promotion::setCurrency
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
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'Promotion';
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
            'StartDate' => Object::TYPE_DATETIME,
            'EndDate' => Object::TYPE_DATETIME,
            'Rate' => Object::TYPE_DECIMAL,
            'Type' => Object::TYPE_CONST,
            'ApproImpactRate' => Object::TYPE_DECIMAL,
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
            'Product'=>array(
                'linkClass'     => 'Product',
                'field'         => 'FromPromotion',
                'linkTable'     => 'prmProduct',
                'linkField'     => 'ToProduct',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Category'=>array(
                'linkClass'     => 'Category',
                'field'         => 'FromPromotion',
                'linkTable'     => 'prmCategory',
                'linkField'     => 'ToCategory',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ProductCommandItem'=>array(
                'linkClass'     => 'ProductCommandItem',
                'field'         => 'Promotion',
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