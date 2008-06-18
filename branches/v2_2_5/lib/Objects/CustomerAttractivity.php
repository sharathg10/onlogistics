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

class CustomerAttractivity extends Object {
    
    // Constructeur {{{

    /**
     * CustomerAttractivity::__construct()
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
     * CustomerAttractivity::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * CustomerAttractivity::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Level string property + getter/setter {{{

    /**
     * Level int property
     *
     * @access private
     * @var integer
     */
    private $_Level = 1;

    /**
     * CustomerAttractivity::getLevel
     *
     * @access public
     * @return integer
     */
    public function getLevel() {
        return $this->_Level;
    }

    /**
     * CustomerAttractivity::setLevel
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setLevel($value) {
        if ($value !== null) {
            $this->_Level = (int)$value;
        }
    }

    // }}}
    // Category one to many relation + getter/setter {{{

    /**
     * Category 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CategoryCollection = false;

    /**
     * CustomerAttractivity::getCategoryCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCategoryCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('CustomerAttractivity');
            return $mapper->getOneToMany($this->getId(),
                'Category', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CategoryCollection) {
            $mapper = Mapper::singleton('CustomerAttractivity');
            $this->_CategoryCollection = $mapper->getOneToMany($this->getId(),
                'Category');
        }
        return $this->_CategoryCollection;
    }

    /**
     * CustomerAttractivity::getCategoryCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCategoryCollectionIds($filter = array()) {
        $col = $this->getCategoryCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * CustomerAttractivity::setCategoryCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCategoryCollection($value) {
        $this->_CategoryCollection = $value;
    }

    // }}}
    // CustomerFrequency one to many relation + getter/setter {{{

    /**
     * CustomerFrequency 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CustomerFrequencyCollection = false;

    /**
     * CustomerAttractivity::getCustomerFrequencyCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCustomerFrequencyCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('CustomerAttractivity');
            return $mapper->getOneToMany($this->getId(),
                'CustomerFrequency', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CustomerFrequencyCollection) {
            $mapper = Mapper::singleton('CustomerAttractivity');
            $this->_CustomerFrequencyCollection = $mapper->getOneToMany($this->getId(),
                'CustomerFrequency');
        }
        return $this->_CustomerFrequencyCollection;
    }

    /**
     * CustomerAttractivity::getCustomerFrequencyCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCustomerFrequencyCollectionIds($filter = array()) {
        $col = $this->getCustomerFrequencyCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * CustomerAttractivity::setCustomerFrequencyCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCustomerFrequencyCollection($value) {
        $this->_CustomerFrequencyCollection = $value;
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
        return 'CustomerAttractivity';
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
            'Level' => Object::TYPE_INT);
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
            'Category'=>array(
                'linkClass'     => 'Category',
                'field'         => 'Attractivity',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'CustomerFrequency'=>array(
                'linkClass'     => 'CustomerFrequency',
                'field'         => 'Attractivity',
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