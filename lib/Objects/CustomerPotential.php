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

class CustomerPotential extends Object {
    // class constants {{{

    const TYPE_POTENTIAL_CA = 1;
    const TYPE_POTENTIAL_VA = 2;
    const TYPE_POTENTIAL_MARGE = 3;
    const TYPE_POTENTIAL_TONNE = 4;

    // }}}
    // Constructeur {{{

    /**
     * CustomerPotential::__construct()
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
     * CustomerPotential::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * CustomerPotential::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // UnitType const property + getter/setter/getUnitTypeConstArray {{{

    /**
     * UnitType int property
     *
     * @access private
     * @var integer
     */
    private $_UnitType = 1;

    /**
     * CustomerPotential::getUnitType
     *
     * @access public
     * @return integer
     */
    public function getUnitType() {
        return $this->_UnitType;
    }

    /**
     * CustomerPotential::setUnitType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setUnitType($value) {
        if ($value !== null) {
            $this->_UnitType = (int)$value;
        }
    }

    /**
     * CustomerPotential::getUnitTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getUnitTypeConstArray($keys = false) {
        $array = array(
            CustomerPotential::TYPE_POTENTIAL_CA => _("Turnover"), 
            CustomerPotential::TYPE_POTENTIAL_VA => _("Value added"), 
            CustomerPotential::TYPE_POTENTIAL_MARGE => _("Margin"), 
            CustomerPotential::TYPE_POTENTIAL_TONNE => _("Ton")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // MinValue float property + getter/setter {{{

    /**
     * MinValue float property
     *
     * @access private
     * @var float
     */
    private $_MinValue = null;

    /**
     * CustomerPotential::getMinValue
     *
     * @access public
     * @return float
     */
    public function getMinValue() {
        return $this->_MinValue;
    }

    /**
     * CustomerPotential::setMinValue
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMinValue($value) {
        $this->_MinValue = ($value===null || $value === '')?null:I18N::extractNumber($value);
    }

    // }}}
    // MaxValue float property + getter/setter {{{

    /**
     * MaxValue float property
     *
     * @access private
     * @var float
     */
    private $_MaxValue = null;

    /**
     * CustomerPotential::getMaxValue
     *
     * @access public
     * @return float
     */
    public function getMaxValue() {
        return $this->_MaxValue;
    }

    /**
     * CustomerPotential::setMaxValue
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaxValue($value) {
        $this->_MaxValue = ($value===null || $value === '')?null:I18N::extractNumber($value);
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
     * CustomerPotential::getCustomerFrequencyCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCustomerFrequencyCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('CustomerPotential');
            return $mapper->getOneToMany($this->getId(),
                'CustomerFrequency', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CustomerFrequencyCollection) {
            $mapper = Mapper::singleton('CustomerPotential');
            $this->_CustomerFrequencyCollection = $mapper->getOneToMany($this->getId(),
                'CustomerFrequency');
        }
        return $this->_CustomerFrequencyCollection;
    }

    /**
     * CustomerPotential::getCustomerFrequencyCollectionIds
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
     * CustomerPotential::setCustomerFrequencyCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCustomerFrequencyCollection($value) {
        $this->_CustomerFrequencyCollection = $value;
    }

    // }}}
    // CustomerProperties one to many relation + getter/setter {{{

    /**
     * CustomerProperties 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CustomerPropertiesCollection = false;

    /**
     * CustomerPotential::getCustomerPropertiesCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCustomerPropertiesCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('CustomerPotential');
            return $mapper->getOneToMany($this->getId(),
                'CustomerProperties', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CustomerPropertiesCollection) {
            $mapper = Mapper::singleton('CustomerPotential');
            $this->_CustomerPropertiesCollection = $mapper->getOneToMany($this->getId(),
                'CustomerProperties');
        }
        return $this->_CustomerPropertiesCollection;
    }

    /**
     * CustomerPotential::getCustomerPropertiesCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCustomerPropertiesCollectionIds($filter = array()) {
        $col = $this->getCustomerPropertiesCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * CustomerPotential::setCustomerPropertiesCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCustomerPropertiesCollection($value) {
        $this->_CustomerPropertiesCollection = $value;
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
        return 'CustomerPotential';
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
        return _('potential');
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
            'UnitType' => Object::TYPE_CONST,
            'MinValue' => Object::TYPE_FLOAT,
            'MaxValue' => Object::TYPE_FLOAT);
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
            'CustomerFrequency'=>array(
                'linkClass'     => 'CustomerFrequency',
                'field'         => 'Potential',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'CustomerProperties'=>array(
                'linkClass'     => 'CustomerProperties',
                'field'         => 'Potential',
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
        return array('grid', 'add', 'edit', 'del');
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
        $return = array(
            'Name'=>array(
                'label'        => _('Name'),
                'shortlabel'   => _('Name'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'UnitType'=>array(
                'label'        => _('Measure unit'),
                'shortlabel'   => _('Unit'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'MinValue'=>array(
                'label'        => _('Minimum value'),
                'shortlabel'   => _('Minimum'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'MaxValue'=>array(
                'label'        => _('Maximum value'),
                'shortlabel'   => _('Maximum'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
}

?>