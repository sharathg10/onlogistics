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

class _StorageSite extends Site {
    
    // Constructeur {{{

    /**
     * _StorageSite::__construct()
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
     * _StorageSite::getCustoms
     *
     * @access public
     * @return integer
     */
    public function getCustoms() {
        return $this->_Customs;
    }

    /**
     * _StorageSite::setCustoms
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
    // StockOwner foreignkey property + getter/setter {{{

    /**
     * StockOwner foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_StockOwner = false;

    /**
     * _StorageSite::getStockOwner
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
     * _StorageSite::getStockOwnerId
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
     * _StorageSite::setStockOwner
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
    // Store one to many relation + getter/setter {{{

    /**
     * Store 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_StoreCollection = false;

    /**
     * _StorageSite::getStoreCollection
     *
     * @access public
     * @return object Collection
     */
    public function getStoreCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('StorageSite');
            return $mapper->getOneToMany($this->getId(),
                'Store', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_StoreCollection) {
            $mapper = Mapper::singleton('StorageSite');
            $this->_StoreCollection = $mapper->getOneToMany($this->getId(),
                'Store');
        }
        return $this->_StoreCollection;
    }

    /**
     * _StorageSite::getStoreCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getStoreCollectionIds($filter = array()) {
        $col = $this->getStoreCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _StorageSite::setStoreCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setStoreCollection($value) {
        $this->_StoreCollection = $value;
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
        return 'Site';
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
    public static function getProperties($ownOnly = false) {
        $return = array(
            'Customs' => Object::TYPE_BOOL,
            'StockOwner' => 'Actor');
        return $ownOnly?$return:array_merge(parent::getProperties(), $return);
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
    public static function getLinks($ownOnly = false) {
        $return = array(
            'Inventory'=>array(
                'linkClass'     => 'Inventory',
                'field'         => 'StorageSite',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Store'=>array(
                'linkClass'     => 'Store',
                'field'         => 'StorageSite',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ));
        return $ownOnly?$return:array_merge(parent::getLinks(), $return);
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
        return array_merge(parent::getUniqueProperties(), $return);
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
        return array_merge(parent::getEmptyForDeleteProperties(), $return);
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
    public static function getMapping($ownOnly = false) {
        $return = array();
        return $ownOnly?$return:array_merge(parent::getMapping(), $return);
    }

    // }}}
    // useInheritance() {{{

    /**
     * Détermine si l'entité est une entité qui utilise l'héritage.
     * (classe parente ou classe fille). Ceci afin de differencier les entités
     * dans le mapper car classes filles et parentes sont mappées dans la même
     * table.
     *
     * @static
     * @access public
     * @return bool
     */
    public static function useInheritance() {
        return true;
    }

    // }}}
    // getParentClassName() {{{

    /**
     * Retourne le nom de la première classe parente
     *
     * @static
     * @access public
     * @return string
     */
    public static function getParentClassName() {
        return 'Site';
    }

    // }}}
}

?>