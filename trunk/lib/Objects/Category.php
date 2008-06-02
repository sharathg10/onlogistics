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

class Category extends Object {
    
    // Constructeur {{{

    /**
     * Category::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // lastmodified property {{{

    /**
     * Date de dernière modification (ou création) au format Datetime mysql.
     *
     * @var string $lastModified
     * @access public
     */
     public $lastModified = 0;

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
     * Category::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * Category::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Attractivity foreignkey property + getter/setter {{{

    /**
     * Attractivity foreignkey
     *
     * @access private
     * @var mixed object CustomerAttractivity or integer
     */
    private $_Attractivity = false;

    /**
     * Category::getAttractivity
     *
     * @access public
     * @return object CustomerAttractivity
     */
    public function getAttractivity() {
        if (is_int($this->_Attractivity) && $this->_Attractivity > 0) {
            $mapper = Mapper::singleton('CustomerAttractivity');
            $this->_Attractivity = $mapper->load(
                array('Id'=>$this->_Attractivity));
        }
        return $this->_Attractivity;
    }

    /**
     * Category::getAttractivityId
     *
     * @access public
     * @return integer
     */
    public function getAttractivityId() {
        if ($this->_Attractivity instanceof CustomerAttractivity) {
            return $this->_Attractivity->getId();
        }
        return (int)$this->_Attractivity;
    }

    /**
     * Category::setAttractivity
     *
     * @access public
     * @param object CustomerAttractivity $value
     * @return void
     */
    public function setAttractivity($value) {
        if (is_numeric($value)) {
            $this->_Attractivity = (int)$value;
        } else {
            $this->_Attractivity = $value;
        }
    }

    // }}}
    // ProductHandingByCategory one to many relation + getter/setter {{{

    /**
     * ProductHandingByCategory *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductHandingByCategoryCollection = false;

    /**
     * Category::getProductHandingByCategoryCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductHandingByCategoryCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Category');
            return $mapper->getManyToMany($this->getId(),
                'ProductHandingByCategory', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductHandingByCategoryCollection) {
            $mapper = Mapper::singleton('Category');
            $this->_ProductHandingByCategoryCollection = $mapper->getManyToMany($this->getId(),
                'ProductHandingByCategory');
        }
        return $this->_ProductHandingByCategoryCollection;
    }

    /**
     * Category::getProductHandingByCategoryCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductHandingByCategoryCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getProductHandingByCategoryCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ProductHandingByCategoryCollection) {
            $mapper = Mapper::singleton('Category');
            return $mapper->getManyToManyIds($this->getId(), 'ProductHandingByCategory');
        }
        return $this->_ProductHandingByCategoryCollection->getItemIds();
    }

    /**
     * Category::setProductHandingByCategoryCollectionIds
     *
     * @access public
     * @return array
     */
    public function setProductHandingByCategoryCollectionIds($itemIds) {
        $this->_ProductHandingByCategoryCollection = new Collection('ProductHandingByCategory');
        foreach ($itemIds as $id) {
            $this->_ProductHandingByCategoryCollection->setItem($id);
        }
    }

    /**
     * Category::setProductHandingByCategoryCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductHandingByCategoryCollection($value) {
        $this->_ProductHandingByCategoryCollection = $value;
    }

    /**
     * Category::ProductHandingByCategoryCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ProductHandingByCategoryCollectionIsLoaded() {
        return ($this->_ProductHandingByCategoryCollection !== false);
    }

    // }}}
    // Promotion one to many relation + getter/setter {{{

    /**
     * Promotion *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_PromotionCollection = false;

    /**
     * Category::getPromotionCollection
     *
     * @access public
     * @return object Collection
     */
    public function getPromotionCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Category');
            return $mapper->getManyToMany($this->getId(),
                'Promotion', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_PromotionCollection) {
            $mapper = Mapper::singleton('Category');
            $this->_PromotionCollection = $mapper->getManyToMany($this->getId(),
                'Promotion');
        }
        return $this->_PromotionCollection;
    }

    /**
     * Category::getPromotionCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getPromotionCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getPromotionCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_PromotionCollection) {
            $mapper = Mapper::singleton('Category');
            return $mapper->getManyToManyIds($this->getId(), 'Promotion');
        }
        return $this->_PromotionCollection->getItemIds();
    }

    /**
     * Category::setPromotionCollectionIds
     *
     * @access public
     * @return array
     */
    public function setPromotionCollectionIds($itemIds) {
        $this->_PromotionCollection = new Collection('Promotion');
        foreach ($itemIds as $id) {
            $this->_PromotionCollection->setItem($id);
        }
    }

    /**
     * Category::setPromotionCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setPromotionCollection($value) {
        $this->_PromotionCollection = $value;
    }

    /**
     * Category::PromotionCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function PromotionCollectionIsLoaded() {
        return ($this->_PromotionCollection !== false);
    }

    // }}}
    // Description string property + getter/setter {{{

    /**
     * Description string property
     *
     * @access private
     * @var string
     */
    private $_Description = '';

    /**
     * Category::getDescription
     *
     * @access public
     * @return string
     */
    public function getDescription() {
        return $this->_Description;
    }

    /**
     * Category::setDescription
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDescription($value) {
        $this->_Description = $value;
    }

    // }}}
    // AnnualTurnoverDiscountPercent one to many relation + getter/setter {{{

    /**
     * AnnualTurnoverDiscountPercent 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_AnnualTurnoverDiscountPercentCollection = false;

    /**
     * Category::getAnnualTurnoverDiscountPercentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getAnnualTurnoverDiscountPercentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Category');
            return $mapper->getOneToMany($this->getId(),
                'AnnualTurnoverDiscountPercent', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_AnnualTurnoverDiscountPercentCollection) {
            $mapper = Mapper::singleton('Category');
            $this->_AnnualTurnoverDiscountPercentCollection = $mapper->getOneToMany($this->getId(),
                'AnnualTurnoverDiscountPercent');
        }
        return $this->_AnnualTurnoverDiscountPercentCollection;
    }

    /**
     * Category::getAnnualTurnoverDiscountPercentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getAnnualTurnoverDiscountPercentCollectionIds($filter = array()) {
        $col = $this->getAnnualTurnoverDiscountPercentCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * Category::setAnnualTurnoverDiscountPercentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setAnnualTurnoverDiscountPercentCollection($value) {
        $this->_AnnualTurnoverDiscountPercentCollection = $value;
    }

    // }}}
    // HandingByRange one to many relation + getter/setter {{{

    /**
     * HandingByRange 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_HandingByRangeCollection = false;

    /**
     * Category::getHandingByRangeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getHandingByRangeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Category');
            return $mapper->getOneToMany($this->getId(),
                'HandingByRange', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_HandingByRangeCollection) {
            $mapper = Mapper::singleton('Category');
            $this->_HandingByRangeCollection = $mapper->getOneToMany($this->getId(),
                'HandingByRange');
        }
        return $this->_HandingByRangeCollection;
    }

    /**
     * Category::getHandingByRangeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getHandingByRangeCollectionIds($filter = array()) {
        $col = $this->getHandingByRangeCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * Category::setHandingByRangeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setHandingByRangeCollection($value) {
        $this->_HandingByRangeCollection = $value;
    }

    // }}}
    // MiniAmountToOrder one to many relation + getter/setter {{{

    /**
     * MiniAmountToOrder 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_MiniAmountToOrderCollection = false;

    /**
     * Category::getMiniAmountToOrderCollection
     *
     * @access public
     * @return object Collection
     */
    public function getMiniAmountToOrderCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Category');
            return $mapper->getOneToMany($this->getId(),
                'MiniAmountToOrder', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_MiniAmountToOrderCollection) {
            $mapper = Mapper::singleton('Category');
            $this->_MiniAmountToOrderCollection = $mapper->getOneToMany($this->getId(),
                'MiniAmountToOrder');
        }
        return $this->_MiniAmountToOrderCollection;
    }

    /**
     * Category::getMiniAmountToOrderCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getMiniAmountToOrderCollectionIds($filter = array()) {
        $col = $this->getMiniAmountToOrderCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * Category::setMiniAmountToOrderCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setMiniAmountToOrderCollection($value) {
        $this->_MiniAmountToOrderCollection = $value;
    }

    // }}}
    // ProductQuantityByCategory one to many relation + getter/setter {{{

    /**
     * ProductQuantityByCategory 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductQuantityByCategoryCollection = false;

    /**
     * Category::getProductQuantityByCategoryCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductQuantityByCategoryCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Category');
            return $mapper->getOneToMany($this->getId(),
                'ProductQuantityByCategory', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductQuantityByCategoryCollection) {
            $mapper = Mapper::singleton('Category');
            $this->_ProductQuantityByCategoryCollection = $mapper->getOneToMany($this->getId(),
                'ProductQuantityByCategory');
        }
        return $this->_ProductQuantityByCategoryCollection;
    }

    /**
     * Category::getProductQuantityByCategoryCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductQuantityByCategoryCollectionIds($filter = array()) {
        $col = $this->getProductQuantityByCategoryCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * Category::setProductQuantityByCategoryCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductQuantityByCategoryCollection($value) {
        $this->_ProductQuantityByCategoryCollection = $value;
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
        return 'Category';
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
        return _('Categories');
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
            'Attractivity' => 'CustomerAttractivity',
            'Description' => Object::TYPE_STRING);
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
            'ProductHandingByCategory'=>array(
                'linkClass'     => 'ProductHandingByCategory',
                'field'         => 'ToCategory',
                'linkTable'     => 'phcCategory',
                'linkField'     => 'FromProductHandingByCategory',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Promotion'=>array(
                'linkClass'     => 'Promotion',
                'field'         => 'ToCategory',
                'linkTable'     => 'prmCategory',
                'linkField'     => 'FromPromotion',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Actor'=>array(
                'linkClass'     => 'Actor',
                'field'         => 'Category',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'AnnualTurnoverDiscountPercent'=>array(
                'linkClass'     => 'AnnualTurnoverDiscountPercent',
                'field'         => 'Category',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'HandingByRange'=>array(
                'linkClass'     => 'HandingByRange',
                'field'         => 'Category',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'MiniAmountToOrder'=>array(
                'linkClass'     => 'MiniAmountToOrder',
                'field'         => 'Category',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ProductQuantityByCategory'=>array(
                'linkClass'     => 'ProductQuantityByCategory',
                'field'         => 'Category',
                'ondelete'      => 'cascade',
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
        $return = array('Name');
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
        return array('grid', 'add', 'edit', 'del');
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
        $return = array(
            'Name'=>array(
                'label'        => _('Name'),
                'shortlabel'   => _('Name'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
}

?>