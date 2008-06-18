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

class _Zone extends Object {
    
    // Constructeur {{{

    /**
     * _Zone::__construct()
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
     * _Zone::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _Zone::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // CountryCity one to many relation + getter/setter {{{

    /**
     * CountryCity 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CountryCityCollection = false;

    /**
     * _Zone::getCountryCityCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCountryCityCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Zone');
            return $mapper->getOneToMany($this->getId(),
                'CountryCity', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CountryCityCollection) {
            $mapper = Mapper::singleton('Zone');
            $this->_CountryCityCollection = $mapper->getOneToMany($this->getId(),
                'CountryCity');
        }
        return $this->_CountryCityCollection;
    }

    /**
     * _Zone::getCountryCityCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCountryCityCollectionIds($filter = array()) {
        $col = $this->getCountryCityCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Zone::setCountryCityCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCountryCityCollection($value) {
        $this->_CountryCityCollection = $value;
    }

    // }}}
    // Site one to many relation + getter/setter {{{

    /**
     * Site 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_SiteCollection = false;

    /**
     * _Zone::getSiteCollection
     *
     * @access public
     * @return object Collection
     */
    public function getSiteCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Zone');
            return $mapper->getOneToMany($this->getId(),
                'Site', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_SiteCollection) {
            $mapper = Mapper::singleton('Zone');
            $this->_SiteCollection = $mapper->getOneToMany($this->getId(),
                'Site');
        }
        return $this->_SiteCollection;
    }

    /**
     * _Zone::getSiteCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getSiteCollectionIds($filter = array()) {
        $col = $this->getSiteCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Zone::setSiteCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setSiteCollection($value) {
        $this->_SiteCollection = $value;
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
        return 'Zone';
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
            'Name' => Object::TYPE_STRING);
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
            'ActorSiteTransition'=>array(
                'linkClass'     => 'ActorSiteTransition',
                'field'         => 'DepartureZone',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActorSiteTransition_1'=>array(
                'linkClass'     => 'ActorSiteTransition',
                'field'         => 'ArrivalZone',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'CostRange'=>array(
                'linkClass'     => 'CostRange',
                'field'         => 'DepartureZone',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'CostRange_1'=>array(
                'linkClass'     => 'CostRange',
                'field'         => 'ArrivalZone',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'CountryCity'=>array(
                'linkClass'     => 'CountryCity',
                'field'         => 'Zone',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Site'=>array(
                'linkClass'     => 'Site',
                'field'         => 'Zone',
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