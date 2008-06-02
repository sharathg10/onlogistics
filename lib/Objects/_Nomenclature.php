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

class _Nomenclature extends Object {
    
    // Constructeur {{{

    /**
     * _Nomenclature::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Version string property + getter/setter {{{

    /**
     * Version string property
     *
     * @access private
     * @var string
     */
    private $_Version = '';

    /**
     * _Nomenclature::getVersion
     *
     * @access public
     * @return string
     */
    public function getVersion() {
        return $this->_Version;
    }

    /**
     * _Nomenclature::setVersion
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setVersion($value) {
        $this->_Version = $value;
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
     * _Nomenclature::getBeginDate
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
     * _Nomenclature::setBeginDate
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
     * _Nomenclature::getEndDate
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
     * _Nomenclature::setEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndDate($value) {
        $this->_EndDate = $value;
    }

    // }}}
    // Buildable string property + getter/setter {{{

    /**
     * Buildable int property
     *
     * @access private
     * @var integer
     */
    private $_Buildable = 1;

    /**
     * _Nomenclature::getBuildable
     *
     * @access public
     * @return integer
     */
    public function getBuildable() {
        return $this->_Buildable;
    }

    /**
     * _Nomenclature::setBuildable
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setBuildable($value) {
        if ($value !== null) {
            $this->_Buildable = (int)$value;
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
     * _Nomenclature::getProduct
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
     * _Nomenclature::getProductId
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
     * _Nomenclature::setProduct
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
    // Component one to many relation + getter/setter {{{

    /**
     * Component 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ComponentCollection = false;

    /**
     * _Nomenclature::getComponentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getComponentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Nomenclature');
            return $mapper->getOneToMany($this->getId(),
                'Component', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ComponentCollection) {
            $mapper = Mapper::singleton('Nomenclature');
            $this->_ComponentCollection = $mapper->getOneToMany($this->getId(),
                'Component');
        }
        return $this->_ComponentCollection;
    }

    /**
     * _Nomenclature::getComponentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getComponentCollectionIds($filter = array()) {
        $col = $this->getComponentCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Nomenclature::setComponentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setComponentCollection($value) {
        $this->_ComponentCollection = $value;
    }

    // }}}
    // ComponentGroup one to many relation + getter/setter {{{

    /**
     * ComponentGroup 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ComponentGroupCollection = false;

    /**
     * _Nomenclature::getComponentGroupCollection
     *
     * @access public
     * @return object Collection
     */
    public function getComponentGroupCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Nomenclature');
            return $mapper->getOneToMany($this->getId(),
                'ComponentGroup', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ComponentGroupCollection) {
            $mapper = Mapper::singleton('Nomenclature');
            $this->_ComponentGroupCollection = $mapper->getOneToMany($this->getId(),
                'ComponentGroup');
        }
        return $this->_ComponentGroupCollection;
    }

    /**
     * _Nomenclature::getComponentGroupCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getComponentGroupCollectionIds($filter = array()) {
        $col = $this->getComponentGroupCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Nomenclature::setComponentGroupCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setComponentGroupCollection($value) {
        $this->_ComponentGroupCollection = $value;
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
        return 'Nomenclature';
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
            'Version' => Object::TYPE_STRING,
            'BeginDate' => Object::TYPE_DATETIME,
            'EndDate' => Object::TYPE_DATETIME,
            'Buildable' => Object::TYPE_BOOL,
            'Product' => 'Product');
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
            'Component'=>array(
                'linkClass'     => 'Component',
                'field'         => 'Nomenclature',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ComponentGroup'=>array(
                'linkClass'     => 'ComponentGroup',
                'field'         => 'Nomenclature',
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