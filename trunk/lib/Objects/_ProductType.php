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

class _ProductType extends Object {
    
    // Constructeur {{{

    /**
     * _ProductType::__construct()
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
     * _ProductType::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _ProductType::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // GenericProductType foreignkey property + getter/setter {{{

    /**
     * GenericProductType foreignkey
     *
     * @access private
     * @var mixed object ProductType or integer
     */
    private $_GenericProductType = false;

    /**
     * _ProductType::getGenericProductType
     *
     * @access public
     * @return object ProductType
     */
    public function getGenericProductType() {
        if (is_int($this->_GenericProductType) && $this->_GenericProductType > 0) {
            $mapper = Mapper::singleton('ProductType');
            $this->_GenericProductType = $mapper->load(
                array('Id'=>$this->_GenericProductType));
        }
        return $this->_GenericProductType;
    }

    /**
     * _ProductType::getGenericProductTypeId
     *
     * @access public
     * @return integer
     */
    public function getGenericProductTypeId() {
        if ($this->_GenericProductType instanceof ProductType) {
            return $this->_GenericProductType->getId();
        }
        return (int)$this->_GenericProductType;
    }

    /**
     * _ProductType::setGenericProductType
     *
     * @access public
     * @param object ProductType $value
     * @return void
     */
    public function setGenericProductType($value) {
        if (is_numeric($value)) {
            $this->_GenericProductType = (int)$value;
        } else {
            $this->_GenericProductType = $value;
        }
    }

    // }}}
    // Generic string property + getter/setter {{{

    /**
     * Generic int property
     *
     * @access private
     * @var integer
     */
    private $_Generic = 0;

    /**
     * _ProductType::getGeneric
     *
     * @access public
     * @return integer
     */
    public function getGeneric() {
        return $this->_Generic;
    }

    /**
     * _ProductType::setGeneric
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setGeneric($value) {
        if ($value !== null) {
            $this->_Generic = (int)$value;
        }
    }

    // }}}
    // Property one to many relation + getter/setter {{{

    /**
     * Property *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_PropertyCollection = false;

    /**
     * _ProductType::getPropertyCollection
     *
     * @access public
     * @return object Collection
     */
    public function getPropertyCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ProductType');
            return $mapper->getManyToMany($this->getId(),
                'Property', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_PropertyCollection) {
            $mapper = Mapper::singleton('ProductType');
            $this->_PropertyCollection = $mapper->getManyToMany($this->getId(),
                'Property');
        }
        return $this->_PropertyCollection;
    }

    /**
     * _ProductType::getPropertyCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getPropertyCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getPropertyCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_PropertyCollection) {
            $mapper = Mapper::singleton('ProductType');
            return $mapper->getManyToManyIds($this->getId(), 'Property');
        }
        return $this->_PropertyCollection->getItemIds();
    }

    /**
     * _ProductType::setPropertyCollectionIds
     *
     * @access public
     * @return array
     */
    public function setPropertyCollectionIds($itemIds) {
        $this->_PropertyCollection = new Collection('Property');
        foreach ($itemIds as $id) {
            $this->_PropertyCollection->setItem($id);
        }
    }

    /**
     * _ProductType::setPropertyCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setPropertyCollection($value) {
        $this->_PropertyCollection = $value;
    }

    /**
     * _ProductType::PropertyCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function PropertyCollectionIsLoaded() {
        return ($this->_PropertyCollection !== false);
    }

    // }}}
    // ProductType one to many relation + getter/setter {{{

    /**
     * ProductType 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductTypeCollection = false;

    /**
     * _ProductType::getProductTypeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductTypeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ProductType');
            return $mapper->getOneToMany($this->getId(),
                'ProductType', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductTypeCollection) {
            $mapper = Mapper::singleton('ProductType');
            $this->_ProductTypeCollection = $mapper->getOneToMany($this->getId(),
                'ProductType');
        }
        return $this->_ProductTypeCollection;
    }

    /**
     * _ProductType::getProductTypeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductTypeCollectionIds($filter = array()) {
        $col = $this->getProductTypeCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ProductType::setProductTypeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductTypeCollection($value) {
        $this->_ProductTypeCollection = $value;
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
        return 'ProductType';
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
            'GenericProductType' => 'ProductType',
            'Generic' => Object::TYPE_BOOL);
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
            'Property'=>array(
                'linkClass'     => 'Property',
                'field'         => 'FromProductType',
                'linkTable'     => 'pdtProperty',
                'linkField'     => 'ToProperty',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ChainCommandItem'=>array(
                'linkClass'     => 'ChainCommandItem',
                'field'         => 'ProductType',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'CostRange'=>array(
                'linkClass'     => 'CostRange',
                'field'         => 'ProductType',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'InvoiceItem'=>array(
                'linkClass'     => 'InvoiceItem',
                'field'         => 'ProductType',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Product'=>array(
                'linkClass'     => 'Product',
                'field'         => 'ProductType',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ProductKind'=>array(
                'linkClass'     => 'ProductKind',
                'field'         => 'ProductType',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ProductType'=>array(
                'linkClass'     => 'ProductType',
                'field'         => 'GenericProductType',
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