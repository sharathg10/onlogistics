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

class _Store extends Object {
    
    // Constructeur {{{

    /**
     * _Store::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
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
     * _Store::getCustoms
     *
     * @access public
     * @return integer
     */
    public function getCustoms() {
        return $this->_Customs;
    }

    /**
     * _Store::setCustoms
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
    // Name string property + getter/setter {{{

    /**
     * Name string property
     *
     * @access private
     * @var string
     */
    private $_Name = '';

    /**
     * _Store::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _Store::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
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
     * _Store::getActivated
     *
     * @access public
     * @return integer
     */
    public function getActivated() {
        return $this->_Activated;
    }

    /**
     * _Store::setActivated
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
    // StockOwner foreignkey property + getter/setter {{{

    /**
     * StockOwner foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_StockOwner = false;

    /**
     * _Store::getStockOwner
     *
     * @access public
     * @return object Actor
     */
    public function getStockOwner() {
        if (is_int($this->_StockOwner) && $this->_StockOwner > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_StockOwner = $mapper->load(
                array('Id'=>$this->_StockOwner));
        }
        return $this->_StockOwner;
    }

    /**
     * _Store::getStockOwnerId
     *
     * @access public
     * @return integer
     */
    public function getStockOwnerId() {
        if ($this->_StockOwner instanceof Actor) {
            return $this->_StockOwner->getId();
        }
        return (int)$this->_StockOwner;
    }

    /**
     * _Store::setStockOwner
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setStockOwner($value) {
        if (is_numeric($value)) {
            $this->_StockOwner = (int)$value;
        } else {
            $this->_StockOwner = $value;
        }
    }

    // }}}
    // StorageSite foreignkey property + getter/setter {{{

    /**
     * StorageSite foreignkey
     *
     * @access private
     * @var mixed object StorageSite or integer
     */
    private $_StorageSite = false;

    /**
     * _Store::getStorageSite
     *
     * @access public
     * @return object StorageSite
     */
    public function getStorageSite() {
        if (is_int($this->_StorageSite) && $this->_StorageSite > 0) {
            $mapper = Mapper::singleton('StorageSite');
            $this->_StorageSite = $mapper->load(
                array('Id'=>$this->_StorageSite));
        }
        return $this->_StorageSite;
    }

    /**
     * _Store::getStorageSiteId
     *
     * @access public
     * @return integer
     */
    public function getStorageSiteId() {
        if ($this->_StorageSite instanceof StorageSite) {
            return $this->_StorageSite->getId();
        }
        return (int)$this->_StorageSite;
    }

    /**
     * _Store::setStorageSite
     *
     * @access public
     * @param object StorageSite $value
     * @return void
     */
    public function setStorageSite($value) {
        if (is_numeric($value)) {
            $this->_StorageSite = (int)$value;
        } else {
            $this->_StorageSite = $value;
        }
    }

    // }}}
    // Location one to many relation + getter/setter {{{

    /**
     * Location 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LocationCollection = false;

    /**
     * _Store::getLocationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLocationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Store');
            return $mapper->getOneToMany($this->getId(),
                'Location', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LocationCollection) {
            $mapper = Mapper::singleton('Store');
            $this->_LocationCollection = $mapper->getOneToMany($this->getId(),
                'Location');
        }
        return $this->_LocationCollection;
    }

    /**
     * _Store::getLocationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLocationCollectionIds($filter = array()) {
        $col = $this->getLocationCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Store::setLocationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLocationCollection($value) {
        $this->_LocationCollection = $value;
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
        return 'Store';
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
            'Customs' => Object::TYPE_BOOL,
            'Name' => Object::TYPE_STRING,
            'Activated' => Object::TYPE_BOOL,
            'StockOwner' => 'Actor',
            'StorageSite' => 'StorageSite');
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
            'CostRange'=>array(
                'linkClass'     => 'CostRange',
                'field'         => 'Store',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Inventory'=>array(
                'linkClass'     => 'Inventory',
                'field'         => 'Store',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Location'=>array(
                'linkClass'     => 'Location',
                'field'         => 'Store',
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