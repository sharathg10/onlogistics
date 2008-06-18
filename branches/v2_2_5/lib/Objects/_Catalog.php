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

class _Catalog extends Object {
    
    // Constructeur {{{

    /**
     * _Catalog::__construct()
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
     * _Catalog::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _Catalog::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // ItemPerPage string property + getter/setter {{{

    /**
     * ItemPerPage int property
     *
     * @access private
     * @var integer
     */
    private $_ItemPerPage = 0;

    /**
     * _Catalog::getItemPerPage
     *
     * @access public
     * @return integer
     */
    public function getItemPerPage() {
        return $this->_ItemPerPage;
    }

    /**
     * _Catalog::setItemPerPage
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setItemPerPage($value) {
        if ($value !== null) {
            $this->_ItemPerPage = (int)$value;
        }
    }

    // }}}
    // Page string property + getter/setter {{{

    /**
     * Page string property
     *
     * @access private
     * @var string
     */
    private $_Page = '';

    /**
     * _Catalog::getPage
     *
     * @access public
     * @return string
     */
    public function getPage() {
        return $this->_Page;
    }

    /**
     * _Catalog::setPage
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPage($value) {
        $this->_Page = $value;
    }

    // }}}
    // CadencedOrder string property + getter/setter {{{

    /**
     * CadencedOrder int property
     *
     * @access private
     * @var integer
     */
    private $_CadencedOrder = 0;

    /**
     * _Catalog::getCadencedOrder
     *
     * @access public
     * @return integer
     */
    public function getCadencedOrder() {
        return $this->_CadencedOrder;
    }

    /**
     * _Catalog::setCadencedOrder
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCadencedOrder($value) {
        if ($value !== null) {
            $this->_CadencedOrder = (int)$value;
        }
    }

    // }}}
    // ProductType one to many relation + getter/setter {{{

    /**
     * ProductType *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductTypeCollection = false;

    /**
     * _Catalog::getProductTypeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductTypeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Catalog');
            return $mapper->getManyToMany($this->getId(),
                'ProductType', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductTypeCollection) {
            $mapper = Mapper::singleton('Catalog');
            $this->_ProductTypeCollection = $mapper->getManyToMany($this->getId(),
                'ProductType');
        }
        return $this->_ProductTypeCollection;
    }

    /**
     * _Catalog::getProductTypeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductTypeCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getProductTypeCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ProductTypeCollection) {
            $mapper = Mapper::singleton('Catalog');
            return $mapper->getManyToManyIds($this->getId(), 'ProductType');
        }
        return $this->_ProductTypeCollection->getItemIds();
    }

    /**
     * _Catalog::setProductTypeCollectionIds
     *
     * @access public
     * @return array
     */
    public function setProductTypeCollectionIds($itemIds) {
        $this->_ProductTypeCollection = new Collection('ProductType');
        foreach ($itemIds as $id) {
            $this->_ProductTypeCollection->setItem($id);
        }
    }

    /**
     * _Catalog::setProductTypeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductTypeCollection($value) {
        $this->_ProductTypeCollection = $value;
    }

    /**
     * _Catalog::ProductTypeCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ProductTypeCollectionIsLoaded() {
        return ($this->_ProductTypeCollection !== false);
    }

    // }}}
    // CatalogCriteria one to many relation + getter/setter {{{

    /**
     * CatalogCriteria 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CatalogCriteriaCollection = false;

    /**
     * _Catalog::getCatalogCriteriaCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCatalogCriteriaCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Catalog');
            return $mapper->getOneToMany($this->getId(),
                'CatalogCriteria', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CatalogCriteriaCollection) {
            $mapper = Mapper::singleton('Catalog');
            $this->_CatalogCriteriaCollection = $mapper->getOneToMany($this->getId(),
                'CatalogCriteria');
        }
        return $this->_CatalogCriteriaCollection;
    }

    /**
     * _Catalog::getCatalogCriteriaCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCatalogCriteriaCollectionIds($filter = array()) {
        $col = $this->getCatalogCriteriaCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Catalog::setCatalogCriteriaCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCatalogCriteriaCollection($value) {
        $this->_CatalogCriteriaCollection = $value;
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
        return 'Catalog';
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
            'ItemPerPage' => Object::TYPE_INT,
            'Page' => Object::TYPE_STRING,
            'CadencedOrder' => Object::TYPE_BOOL);
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
            'ProductType'=>array(
                'linkClass'     => 'ProductType',
                'field'         => 'FromCatalog',
                'linkTable'     => 'ctgProductType',
                'linkField'     => 'ToProductType',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'CatalogCriteria'=>array(
                'linkClass'     => 'CatalogCriteria',
                'field'         => 'Catalog',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'UserAccount'=>array(
                'linkClass'     => 'UserAccount',
                'field'         => 'Catalog',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'UserAccount_1'=>array(
                'linkClass'     => 'UserAccount',
                'field'         => 'SupplierCatalog',
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