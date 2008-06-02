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

class _Saisonality extends Object {
    
    // Constructeur {{{

    /**
     * _Saisonality::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
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
     * _Saisonality::getStartDate
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
     * _Saisonality::setStartDate
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
     * _Saisonality::getEndDate
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
     * _Saisonality::setEndDate
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
     * _Saisonality::getRate
     *
     * @access public
     * @return float
     */
    public function getRate() {
        return $this->_Rate;
    }

    /**
     * _Saisonality::setRate
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
    // Product one to many relation + getter/setter {{{

    /**
     * Product *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductCollection = false;

    /**
     * _Saisonality::getProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Saisonality');
            return $mapper->getManyToMany($this->getId(),
                'Product', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductCollection) {
            $mapper = Mapper::singleton('Saisonality');
            $this->_ProductCollection = $mapper->getManyToMany($this->getId(),
                'Product');
        }
        return $this->_ProductCollection;
    }

    /**
     * _Saisonality::getProductCollectionIds
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
            $mapper = Mapper::singleton('Saisonality');
            return $mapper->getManyToManyIds($this->getId(), 'Product');
        }
        return $this->_ProductCollection->getItemIds();
    }

    /**
     * _Saisonality::setProductCollectionIds
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
     * _Saisonality::setProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductCollection($value) {
        $this->_ProductCollection = $value;
    }

    /**
     * _Saisonality::ProductCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ProductCollectionIsLoaded() {
        return ($this->_ProductCollection !== false);
    }

    // }}}
    // ProductKind one to many relation + getter/setter {{{

    /**
     * ProductKind *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductKindCollection = false;

    /**
     * _Saisonality::getProductKindCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductKindCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Saisonality');
            return $mapper->getManyToMany($this->getId(),
                'ProductKind', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductKindCollection) {
            $mapper = Mapper::singleton('Saisonality');
            $this->_ProductKindCollection = $mapper->getManyToMany($this->getId(),
                'ProductKind');
        }
        return $this->_ProductKindCollection;
    }

    /**
     * _Saisonality::getProductKindCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductKindCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getProductKindCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ProductKindCollection) {
            $mapper = Mapper::singleton('Saisonality');
            return $mapper->getManyToManyIds($this->getId(), 'ProductKind');
        }
        return $this->_ProductKindCollection->getItemIds();
    }

    /**
     * _Saisonality::setProductKindCollectionIds
     *
     * @access public
     * @return array
     */
    public function setProductKindCollectionIds($itemIds) {
        $this->_ProductKindCollection = new Collection('ProductKind');
        foreach ($itemIds as $id) {
            $this->_ProductKindCollection->setItem($id);
        }
    }

    /**
     * _Saisonality::setProductKindCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductKindCollection($value) {
        $this->_ProductKindCollection = $value;
    }

    /**
     * _Saisonality::ProductKindCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ProductKindCollectionIsLoaded() {
        return ($this->_ProductKindCollection !== false);
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
        return 'Saisonality';
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
            'StartDate' => Object::TYPE_DATETIME,
            'EndDate' => Object::TYPE_DATETIME,
            'Rate' => Object::TYPE_DECIMAL);
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
                'field'         => 'FromSaisonality',
                'linkTable'     => 'saiProduct',
                'linkField'     => 'ToProduct',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ProductKind'=>array(
                'linkClass'     => 'ProductKind',
                'field'         => 'FromSaisonality',
                'linkTable'     => 'saiProductKind',
                'linkField'     => 'ToProductKind',
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