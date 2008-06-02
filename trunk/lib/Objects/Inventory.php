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

class Inventory extends Object {
    
    // Constructeur {{{

    /**
     * Inventory::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // BeginDate datetime property + getter/setter {{{

    /**
     * BeginDate int property
     *
     * @access private
     * @var string
     */
    private $_BeginDate = 0;

    /**
     * Inventory::getBeginDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getBeginDate($format = false) {
        return $this->dateFormat($this->_BeginDate, $format);
    }

    /**
     * Inventory::setBeginDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBeginDate($value) {
        $this->_BeginDate = $value;
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
     * Inventory::getEndDate
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
     * Inventory::setEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndDate($value) {
        $this->_EndDate = $value;
    }

    // }}}
    // UserAccount foreignkey property + getter/setter {{{

    /**
     * UserAccount foreignkey
     *
     * @access private
     * @var mixed object UserAccount or integer
     */
    private $_UserAccount = false;

    /**
     * Inventory::getUserAccount
     *
     * @access public
     * @return object UserAccount
     */
    public function getUserAccount() {
        if (is_int($this->_UserAccount) && $this->_UserAccount > 0) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_UserAccount = $mapper->load(
                array('Id'=>$this->_UserAccount));
        }
        return $this->_UserAccount;
    }

    /**
     * Inventory::getUserAccountId
     *
     * @access public
     * @return integer
     */
    public function getUserAccountId() {
        if ($this->_UserAccount instanceof UserAccount) {
            return $this->_UserAccount->getId();
        }
        return (int)$this->_UserAccount;
    }

    /**
     * Inventory::setUserAccount
     *
     * @access public
     * @param object UserAccount $value
     * @return void
     */
    public function setUserAccount($value) {
        if (is_numeric($value)) {
            $this->_UserAccount = (int)$value;
        } else {
            $this->_UserAccount = $value;
        }
    }

    // }}}
    // UserName string property + getter/setter {{{

    /**
     * UserName string property
     *
     * @access private
     * @var string
     */
    private $_UserName = '';

    /**
     * Inventory::getUserName
     *
     * @access public
     * @return string
     */
    public function getUserName() {
        return $this->_UserName;
    }

    /**
     * Inventory::setUserName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setUserName($value) {
        $this->_UserName = $value;
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
     * Inventory::getStorageSite
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
     * Inventory::getStorageSiteId
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
     * Inventory::setStorageSite
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
    // StorageSiteName string property + getter/setter {{{

    /**
     * StorageSiteName string property
     *
     * @access private
     * @var string
     */
    private $_StorageSiteName = '';

    /**
     * Inventory::getStorageSiteName
     *
     * @access public
     * @return string
     */
    public function getStorageSiteName() {
        return $this->_StorageSiteName;
    }

    /**
     * Inventory::setStorageSiteName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStorageSiteName($value) {
        $this->_StorageSiteName = $value;
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
     * Inventory::getStore
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
     * Inventory::getStoreId
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
     * Inventory::setStore
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
    // StoreName string property + getter/setter {{{

    /**
     * StoreName string property
     *
     * @access private
     * @var string
     */
    private $_StoreName = '';

    /**
     * Inventory::getStoreName
     *
     * @access public
     * @return string
     */
    public function getStoreName() {
        return $this->_StoreName;
    }

    /**
     * Inventory::setStoreName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStoreName($value) {
        $this->_StoreName = $value;
    }

    // }}}
    // InventoryDetail one to many relation + getter/setter {{{

    /**
     * InventoryDetail 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_InventoryDetailCollection = false;

    /**
     * Inventory::getInventoryDetailCollection
     *
     * @access public
     * @return object Collection
     */
    public function getInventoryDetailCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Inventory');
            return $mapper->getOneToMany($this->getId(),
                'InventoryDetail', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_InventoryDetailCollection) {
            $mapper = Mapper::singleton('Inventory');
            $this->_InventoryDetailCollection = $mapper->getOneToMany($this->getId(),
                'InventoryDetail');
        }
        return $this->_InventoryDetailCollection;
    }

    /**
     * Inventory::getInventoryDetailCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getInventoryDetailCollectionIds($filter = array()) {
        $col = $this->getInventoryDetailCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * Inventory::setInventoryDetailCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setInventoryDetailCollection($value) {
        $this->_InventoryDetailCollection = $value;
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
        return 'Inventory';
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
            'BeginDate' => Object::TYPE_DATETIME,
            'EndDate' => Object::TYPE_DATETIME,
            'UserAccount' => 'UserAccount',
            'UserName' => Object::TYPE_STRING,
            'StorageSite' => 'StorageSite',
            'StorageSiteName' => Object::TYPE_STRING,
            'Store' => 'Store',
            'StoreName' => Object::TYPE_STRING);
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
            'InventoryDetail'=>array(
                'linkClass'     => 'InventoryDetail',
                'field'         => 'Inventory',
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