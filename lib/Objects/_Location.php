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

class _Location extends Object {
    
    // Constructeur {{{

    /**
     * _Location::__construct()
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
     * _Location::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _Location::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Customs string property + getter/setter {{{

    /**
     * Customs int property
     *
     * @access private
     * @var integer
     */
    private $_Customs = false;

    /**
     * _Location::getCustoms
     *
     * @access public
     * @return integer
     */
    public function getCustoms() {
        return $this->_Customs;
    }

    /**
     * _Location::setCustoms
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCustoms($value) {
        if ($value !== null) {
            $this->_Customs = (int)$value;
        }
    }

    // }}}
    // Store foreignkey property + getter/setter {{{

    /**
     * Store foreignkey
     *
     * @access private
     * @var mixed object Store or integer
     */
    private $_Store = false;

    /**
     * _Location::getStore
     *
     * @access public
     * @return object Store
     */
    public function getStore() {
        if (is_int($this->_Store) && $this->_Store > 0) {
            $mapper = Mapper::singleton('Store');
            $this->_Store = $mapper->load(
                array('Id'=>$this->_Store));
        }
        return $this->_Store;
    }

    /**
     * _Location::getStoreId
     *
     * @access public
     * @return integer
     */
    public function getStoreId() {
        if ($this->_Store instanceof Store) {
            return $this->_Store->getId();
        }
        return (int)$this->_Store;
    }

    /**
     * _Location::setStore
     *
     * @access public
     * @param object Store $value
     * @return void
     */
    public function setStore($value) {
        if (is_numeric($value)) {
            $this->_Store = (int)$value;
        } else {
            $this->_Store = $value;
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
     * _Location::getActivated
     *
     * @access public
     * @return integer
     */
    public function getActivated() {
        return $this->_Activated;
    }

    /**
     * _Location::setActivated
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
    // AllowedProduct one to many relation + getter/setter {{{

    /**
     * AllowedProduct *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_AllowedProductCollection = false;

    /**
     * _Location::getAllowedProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getAllowedProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Location');
            return $mapper->getManyToMany($this->getId(),
                'AllowedProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_AllowedProductCollection) {
            $mapper = Mapper::singleton('Location');
            $this->_AllowedProductCollection = $mapper->getManyToMany($this->getId(),
                'AllowedProduct');
        }
        return $this->_AllowedProductCollection;
    }

    /**
     * _Location::getAllowedProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getAllowedProductCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getAllowedProductCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_AllowedProductCollection) {
            $mapper = Mapper::singleton('Location');
            return $mapper->getManyToManyIds($this->getId(), 'AllowedProduct');
        }
        return $this->_AllowedProductCollection->getItemIds();
    }

    /**
     * _Location::setAllowedProductCollectionIds
     *
     * @access public
     * @return array
     */
    public function setAllowedProductCollectionIds($itemIds) {
        $this->_AllowedProductCollection = new Collection('AllowedProduct');
        foreach ($itemIds as $id) {
            $this->_AllowedProductCollection->setItem($id);
        }
    }

    /**
     * _Location::setAllowedProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setAllowedProductCollection($value) {
        $this->_AllowedProductCollection = $value;
    }

    /**
     * _Location::AllowedProductCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function AllowedProductCollectionIsLoaded() {
        return ($this->_AllowedProductCollection !== false);
    }

    // }}}
    // LocationConcreteProduct one to many relation + getter/setter {{{

    /**
     * LocationConcreteProduct 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LocationConcreteProductCollection = false;

    /**
     * _Location::getLocationConcreteProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLocationConcreteProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Location');
            return $mapper->getOneToMany($this->getId(),
                'LocationConcreteProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LocationConcreteProductCollection) {
            $mapper = Mapper::singleton('Location');
            $this->_LocationConcreteProductCollection = $mapper->getOneToMany($this->getId(),
                'LocationConcreteProduct');
        }
        return $this->_LocationConcreteProductCollection;
    }

    /**
     * _Location::getLocationConcreteProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLocationConcreteProductCollectionIds($filter = array()) {
        $col = $this->getLocationConcreteProductCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Location::setLocationConcreteProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLocationConcreteProductCollection($value) {
        $this->_LocationConcreteProductCollection = $value;
    }

    // }}}
    // LocationProductQuantities one to many relation + getter/setter {{{

    /**
     * LocationProductQuantities 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LocationProductQuantitiesCollection = false;

    /**
     * _Location::getLocationProductQuantitiesCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLocationProductQuantitiesCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Location');
            return $mapper->getOneToMany($this->getId(),
                'LocationProductQuantities', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LocationProductQuantitiesCollection) {
            $mapper = Mapper::singleton('Location');
            $this->_LocationProductQuantitiesCollection = $mapper->getOneToMany($this->getId(),
                'LocationProductQuantities');
        }
        return $this->_LocationProductQuantitiesCollection;
    }

    /**
     * _Location::getLocationProductQuantitiesCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLocationProductQuantitiesCollectionIds($filter = array()) {
        $col = $this->getLocationProductQuantitiesCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Location::setLocationProductQuantitiesCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLocationProductQuantitiesCollection($value) {
        $this->_LocationProductQuantitiesCollection = $value;
    }

    // }}}
    // OccupiedLocation one to many relation + getter/setter {{{

    /**
     * OccupiedLocation 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_OccupiedLocationCollection = false;

    /**
     * _Location::getOccupiedLocationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getOccupiedLocationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Location');
            return $mapper->getOneToMany($this->getId(),
                'OccupiedLocation', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_OccupiedLocationCollection) {
            $mapper = Mapper::singleton('Location');
            $this->_OccupiedLocationCollection = $mapper->getOneToMany($this->getId(),
                'OccupiedLocation');
        }
        return $this->_OccupiedLocationCollection;
    }

    /**
     * _Location::getOccupiedLocationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getOccupiedLocationCollectionIds($filter = array()) {
        $col = $this->getOccupiedLocationCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Location::setOccupiedLocationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setOccupiedLocationCollection($value) {
        $this->_OccupiedLocationCollection = $value;
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
        return 'Location';
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
            'Customs' => Object::TYPE_BOOL,
            'Store' => 'Store',
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
        $return = array(
            'AllowedProduct'=>array(
                'linkClass'     => 'Product',
                'field'         => 'FromLocation',
                'linkTable'     => 'locProduct',
                'linkField'     => 'ToProduct',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'InventoryDetail'=>array(
                'linkClass'     => 'InventoryDetail',
                'field'         => 'Location',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'LocationConcreteProduct'=>array(
                'linkClass'     => 'LocationConcreteProduct',
                'field'         => 'Location',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'LocationExecutedMovement'=>array(
                'linkClass'     => 'LocationExecutedMovement',
                'field'         => 'Location',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'LocationProductQuantities'=>array(
                'linkClass'     => 'LocationProductQuantities',
                'field'         => 'Location',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'OccupiedLocation'=>array(
                'linkClass'     => 'OccupiedLocation',
                'field'         => 'Location',
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