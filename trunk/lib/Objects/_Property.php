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

class _Property extends Object {
    // class constants {{{

    const OBJECT_TYPE = 0;
    const STRING_TYPE = 1;
    const INT_TYPE = 2;
    const BOOL_TYPE = 3;
    const FLOAT_TYPE = 4;
    const DATE_TYPE = 10;

    // }}}
    // Constructeur {{{

    /**
     * _Property::__construct()
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
     * _Property::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _Property::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // DisplayName string property + getter/setter {{{

    /**
     * DisplayName string property
     *
     * @access private
     * @var string
     */
    private $_DisplayName = '';

    /**
     * _Property::getDisplayName
     *
     * @access public
     * @return string
     */
    public function getDisplayName() {
        return $this->_DisplayName;
    }

    /**
     * _Property::setDisplayName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDisplayName($value) {
        $this->_DisplayName = $value;
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
     * _Property::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * _Property::setType
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
     * _Property::getTypeConstArray
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
            _Property::OBJECT_TYPE => _("Foreign key"), 
            _Property::STRING_TYPE => _("Chain"), 
            _Property::INT_TYPE => _("Integer"), 
            _Property::BOOL_TYPE => _("Boolean"), 
            _Property::FLOAT_TYPE => _("Float"), 
            _Property::DATE_TYPE => _("Date")
        );
        asort($array);
        return $keys?array_keys($array):$array;
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
     * _Property::getCatalogCriteriaCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCatalogCriteriaCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Property');
            return $mapper->getOneToMany($this->getId(),
                'CatalogCriteria', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CatalogCriteriaCollection) {
            $mapper = Mapper::singleton('Property');
            $this->_CatalogCriteriaCollection = $mapper->getOneToMany($this->getId(),
                'CatalogCriteria');
        }
        return $this->_CatalogCriteriaCollection;
    }

    /**
     * _Property::getCatalogCriteriaCollectionIds
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
     * _Property::setCatalogCriteriaCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCatalogCriteriaCollection($value) {
        $this->_CatalogCriteriaCollection = $value;
    }

    // }}}
    // PropertyValue one to many relation + getter/setter {{{

    /**
     * PropertyValue 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_PropertyValueCollection = false;

    /**
     * _Property::getPropertyValueCollection
     *
     * @access public
     * @return object Collection
     */
    public function getPropertyValueCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Property');
            return $mapper->getOneToMany($this->getId(),
                'PropertyValue', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_PropertyValueCollection) {
            $mapper = Mapper::singleton('Property');
            $this->_PropertyValueCollection = $mapper->getOneToMany($this->getId(),
                'PropertyValue');
        }
        return $this->_PropertyValueCollection;
    }

    /**
     * _Property::getPropertyValueCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getPropertyValueCollectionIds($filter = array()) {
        $col = $this->getPropertyValueCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Property::setPropertyValueCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setPropertyValueCollection($value) {
        $this->_PropertyValueCollection = $value;
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
        return 'Property';
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
            'DisplayName' => Object::TYPE_STRING,
            'Type' => Object::TYPE_CONST);
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
            'CatalogCriteria'=>array(
                'linkClass'     => 'CatalogCriteria',
                'field'         => 'Property',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'DocumentModelProperty'=>array(
                'linkClass'     => 'DocumentModelProperty',
                'field'         => 'Property',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'PropertyValue'=>array(
                'linkClass'     => 'PropertyValue',
                'field'         => 'Property',
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
    // toString() {{{

    /**
     * Retourne la représentation texte de l'objet
     *
     * @access public
     * @return string
     */
    public function toString() {
        return $this->getDisplayName();
    }

    // }}}
    // getToStringAttribute() {{{

    /**
     * Retourne le nom de l'attribut pointé par toString()
     *
     * @static
     * @access public
     * @return string
     */
    public function getToStringAttribute() {
        return 'DisplayName';
    }

    // }}}
}

?>