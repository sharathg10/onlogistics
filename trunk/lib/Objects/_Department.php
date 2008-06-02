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

class _Department extends Object {
    
    // Constructeur {{{

    /**
     * _Department::__construct()
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
     * _Department::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _Department::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Number string property + getter/setter {{{

    /**
     * Number string property
     *
     * @access private
     * @var string
     */
    private $_Number = '';

    /**
     * _Department::getNumber
     *
     * @access public
     * @return string
     */
    public function getNumber() {
        return $this->_Number;
    }

    /**
     * _Department::setNumber
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setNumber($value) {
        $this->_Number = $value;
    }

    // }}}
    // State foreignkey property + getter/setter {{{

    /**
     * State foreignkey
     *
     * @access private
     * @var mixed object State or integer
     */
    private $_State = false;

    /**
     * _Department::getState
     *
     * @access public
     * @return object State
     */
    public function getState() {
        if (is_int($this->_State) && $this->_State > 0) {
            $mapper = Mapper::singleton('State');
            $this->_State = $mapper->load(
                array('Id'=>$this->_State));
        }
        return $this->_State;
    }

    /**
     * _Department::getStateId
     *
     * @access public
     * @return integer
     */
    public function getStateId() {
        if ($this->_State instanceof State) {
            return $this->_State->getId();
        }
        return (int)$this->_State;
    }

    /**
     * _Department::setState
     *
     * @access public
     * @param object State $value
     * @return void
     */
    public function setState($value) {
        if (is_numeric($value)) {
            $this->_State = (int)$value;
        } else {
            $this->_State = $value;
        }
    }

    // }}}
    // Country foreignkey property + getter/setter {{{

    /**
     * Country foreignkey
     *
     * @access private
     * @var mixed object Country or integer
     */
    private $_Country = false;

    /**
     * _Department::getCountry
     *
     * @access public
     * @return object Country
     */
    public function getCountry() {
        if (is_int($this->_Country) && $this->_Country > 0) {
            $mapper = Mapper::singleton('Country');
            $this->_Country = $mapper->load(
                array('Id'=>$this->_Country));
        }
        return $this->_Country;
    }

    /**
     * _Department::getCountryId
     *
     * @access public
     * @return integer
     */
    public function getCountryId() {
        if ($this->_Country instanceof Country) {
            return $this->_Country->getId();
        }
        return (int)$this->_Country;
    }

    /**
     * _Department::setCountry
     *
     * @access public
     * @param object Country $value
     * @return void
     */
    public function setCountry($value) {
        if (is_numeric($value)) {
            $this->_Country = (int)$value;
        } else {
            $this->_Country = $value;
        }
    }

    // }}}
    // CityName one to many relation + getter/setter {{{

    /**
     * CityName 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CityNameCollection = false;

    /**
     * _Department::getCityNameCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCityNameCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Department');
            return $mapper->getOneToMany($this->getId(),
                'CityName', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CityNameCollection) {
            $mapper = Mapper::singleton('Department');
            $this->_CityNameCollection = $mapper->getOneToMany($this->getId(),
                'CityName');
        }
        return $this->_CityNameCollection;
    }

    /**
     * _Department::getCityNameCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCityNameCollectionIds($filter = array()) {
        $col = $this->getCityNameCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Department::setCityNameCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCityNameCollection($value) {
        $this->_CityNameCollection = $value;
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
        return 'Department';
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
            'Number' => Object::TYPE_STRING,
            'State' => 'State',
            'Country' => 'Country');
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
            'CityName'=>array(
                'linkClass'     => 'CityName',
                'field'         => 'Department',
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