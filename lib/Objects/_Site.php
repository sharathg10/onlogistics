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

class _Site extends Object {
    // class constants {{{

    const TYPE_AVENUE = 1;
    const TYPE_BOULEVARD = 2;
    const TYPE_CHEMIN = 3;
    const TYPE_IMPASSE = 4;
    const TYPE_PLACE = 5;
    const TYPE_ROUTE = 6;
    const TYPE_RUE = 7;
    const SITE_TYPE_QUELCONQUE = 0;
    const SITE_TYPE_FACTURATION = 1;
    const SITE_TYPE_LIVRAISON = 2;
    const SITE_TYPE_FACTURATION_LIVRAISON = 3;

    // }}}
    // Constructeur {{{

    /**
     * _Site::__construct()
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
     * _Site::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _Site::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Email string property + getter/setter {{{

    /**
     * Email string property
     *
     * @access private
     * @var string
     */
    private $_Email = '';

    /**
     * _Site::getEmail
     *
     * @access public
     * @return string
     */
    public function getEmail() {
        return $this->_Email;
    }

    /**
     * _Site::setEmail
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEmail($value) {
        $this->_Email = $value;
    }

    // }}}
    // Fax string property + getter/setter {{{

    /**
     * Fax string property
     *
     * @access private
     * @var string
     */
    private $_Fax = '';

    /**
     * _Site::getFax
     *
     * @access public
     * @return string
     */
    public function getFax() {
        return $this->_Fax;
    }

    /**
     * _Site::setFax
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setFax($value) {
        $this->_Fax = $value;
    }

    // }}}
    // Phone string property + getter/setter {{{

    /**
     * Phone string property
     *
     * @access private
     * @var string
     */
    private $_Phone = '';

    /**
     * _Site::getPhone
     *
     * @access public
     * @return string
     */
    public function getPhone() {
        return $this->_Phone;
    }

    /**
     * _Site::setPhone
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPhone($value) {
        $this->_Phone = $value;
    }

    // }}}
    // Mobile string property + getter/setter {{{

    /**
     * Mobile string property
     *
     * @access private
     * @var string
     */
    private $_Mobile = '';

    /**
     * _Site::getMobile
     *
     * @access public
     * @return string
     */
    public function getMobile() {
        return $this->_Mobile;
    }

    /**
     * _Site::setMobile
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setMobile($value) {
        $this->_Mobile = $value;
    }

    // }}}
    // PreferedCommunicationMode int property + getter/setter {{{

    /**
     * PreferedCommunicationMode int property
     *
     * @access private
     * @var integer
     */
    private $_PreferedCommunicationMode = null;

    /**
     * _Site::getPreferedCommunicationMode
     *
     * @access public
     * @return integer
     */
    public function getPreferedCommunicationMode() {
        return $this->_PreferedCommunicationMode;
    }

    /**
     * _Site::setPreferedCommunicationMode
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPreferedCommunicationMode($value) {
        $this->_PreferedCommunicationMode = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // StreetNumber string property + getter/setter {{{

    /**
     * StreetNumber string property
     *
     * @access private
     * @var string
     */
    private $_StreetNumber = '';

    /**
     * _Site::getStreetNumber
     *
     * @access public
     * @return string
     */
    public function getStreetNumber() {
        return $this->_StreetNumber;
    }

    /**
     * _Site::setStreetNumber
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStreetNumber($value) {
        $this->_StreetNumber = $value;
    }

    // }}}
    // StreetType const property + getter/setter/getStreetTypeConstArray {{{

    /**
     * StreetType int property
     *
     * @access private
     * @var integer
     */
    private $_StreetType = 0;

    /**
     * _Site::getStreetType
     *
     * @access public
     * @return integer
     */
    public function getStreetType() {
        return $this->_StreetType;
    }

    /**
     * _Site::setStreetType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setStreetType($value) {
        if ($value !== null) {
            $this->_StreetType = (int)$value;
        }
    }

    /**
     * _Site::getStreetTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getStreetTypeConstArray($keys = false) {
        $array = array(
            _Site::TYPE_AVENUE => _("Avenue"), 
            _Site::TYPE_BOULEVARD => _("Boulevard"), 
            _Site::TYPE_CHEMIN => _("Lane"), 
            _Site::TYPE_IMPASSE => _("Blind alley"), 
            _Site::TYPE_PLACE => _("Place"), 
            _Site::TYPE_ROUTE => _("Road"), 
            _Site::TYPE_RUE => _("Street")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // StreetName string property + getter/setter {{{

    /**
     * StreetName string property
     *
     * @access private
     * @var string
     */
    private $_StreetName = '';

    /**
     * _Site::getStreetName
     *
     * @access public
     * @return string
     */
    public function getStreetName() {
        return $this->_StreetName;
    }

    /**
     * _Site::setStreetName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStreetName($value) {
        $this->_StreetName = $value;
    }

    // }}}
    // StreetAddons string property + getter/setter {{{

    /**
     * StreetAddons string property
     *
     * @access private
     * @var string
     */
    private $_StreetAddons = '';

    /**
     * _Site::getStreetAddons
     *
     * @access public
     * @return string
     */
    public function getStreetAddons() {
        return $this->_StreetAddons;
    }

    /**
     * _Site::setStreetAddons
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStreetAddons($value) {
        $this->_StreetAddons = $value;
    }

    // }}}
    // Cedex string property + getter/setter {{{

    /**
     * Cedex string property
     *
     * @access private
     * @var string
     */
    private $_Cedex = '';

    /**
     * _Site::getCedex
     *
     * @access public
     * @return string
     */
    public function getCedex() {
        return $this->_Cedex;
    }

    /**
     * _Site::setCedex
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCedex($value) {
        $this->_Cedex = $value;
    }

    // }}}
    // GPS string property + getter/setter {{{

    /**
     * GPS string property
     *
     * @access private
     * @var string
     */
    private $_GPS = '';

    /**
     * _Site::getGPS
     *
     * @access public
     * @return string
     */
    public function getGPS() {
        return $this->_GPS;
    }

    /**
     * _Site::setGPS
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setGPS($value) {
        $this->_GPS = $value;
    }

    // }}}
    // CountryCity foreignkey property + getter/setter {{{

    /**
     * CountryCity foreignkey
     *
     * @access private
     * @var mixed object CountryCity or integer
     */
    private $_CountryCity = false;

    /**
     * _Site::getCountryCity
     *
     * @access public
     * @return object CountryCity
     */
    public function getCountryCity() {
        if (is_int($this->_CountryCity) && $this->_CountryCity > 0) {
            $mapper = Mapper::singleton('CountryCity');
            $this->_CountryCity = $mapper->load(
                array('Id'=>$this->_CountryCity));
        }
        return $this->_CountryCity;
    }

    /**
     * _Site::getCountryCityId
     *
     * @access public
     * @return integer
     */
    public function getCountryCityId() {
        if ($this->_CountryCity instanceof CountryCity) {
            return $this->_CountryCity->getId();
        }
        return (int)$this->_CountryCity;
    }

    /**
     * _Site::setCountryCity
     *
     * @access public
     * @param object CountryCity $value
     * @return void
     */
    public function setCountryCity($value) {
        if (is_numeric($value)) {
            $this->_CountryCity = (int)$value;
        } else {
            $this->_CountryCity = $value;
        }
    }

    // }}}
    // Zone foreignkey property + getter/setter {{{

    /**
     * Zone foreignkey
     *
     * @access private
     * @var mixed object Zone or integer
     */
    private $_Zone = false;

    /**
     * _Site::getZone
     *
     * @access public
     * @return object Zone
     */
    public function getZone() {
        if (is_int($this->_Zone) && $this->_Zone > 0) {
            $mapper = Mapper::singleton('Zone');
            $this->_Zone = $mapper->load(
                array('Id'=>$this->_Zone));
        }
        return $this->_Zone;
    }

    /**
     * _Site::getZoneId
     *
     * @access public
     * @return integer
     */
    public function getZoneId() {
        if ($this->_Zone instanceof Zone) {
            return $this->_Zone->getId();
        }
        return (int)$this->_Zone;
    }

    /**
     * _Site::setZone
     *
     * @access public
     * @param object Zone $value
     * @return void
     */
    public function setZone($value) {
        if (is_numeric($value)) {
            $this->_Zone = (int)$value;
        } else {
            $this->_Zone = $value;
        }
    }

    // }}}
    // Planning foreignkey property + getter/setter {{{

    /**
     * Planning foreignkey
     *
     * @access private
     * @var mixed object WeeklyPlanning or integer
     */
    private $_Planning = false;

    /**
     * _Site::getPlanning
     *
     * @access public
     * @return object WeeklyPlanning
     */
    public function getPlanning() {
        if (is_int($this->_Planning) && $this->_Planning > 0) {
            $mapper = Mapper::singleton('WeeklyPlanning');
            $this->_Planning = $mapper->load(
                array('Id'=>$this->_Planning));
        }
        return $this->_Planning;
    }

    /**
     * _Site::getPlanningId
     *
     * @access public
     * @return integer
     */
    public function getPlanningId() {
        if ($this->_Planning instanceof WeeklyPlanning) {
            return $this->_Planning->getId();
        }
        return (int)$this->_Planning;
    }

    /**
     * _Site::setPlanning
     *
     * @access public
     * @param object WeeklyPlanning $value
     * @return void
     */
    public function setPlanning($value) {
        if (is_numeric($value)) {
            $this->_Planning = (int)$value;
        } else {
            $this->_Planning = $value;
        }
    }

    // }}}
    // CommunicationModality foreignkey property + getter/setter {{{

    /**
     * CommunicationModality foreignkey
     *
     * @access private
     * @var mixed object CommunicationModality or integer
     */
    private $_CommunicationModality = false;

    /**
     * _Site::getCommunicationModality
     *
     * @access public
     * @return object CommunicationModality
     */
    public function getCommunicationModality() {
        if (is_int($this->_CommunicationModality) && $this->_CommunicationModality > 0) {
            $mapper = Mapper::singleton('CommunicationModality');
            $this->_CommunicationModality = $mapper->load(
                array('Id'=>$this->_CommunicationModality));
        }
        return $this->_CommunicationModality;
    }

    /**
     * _Site::getCommunicationModalityId
     *
     * @access public
     * @return integer
     */
    public function getCommunicationModalityId() {
        if ($this->_CommunicationModality instanceof CommunicationModality) {
            return $this->_CommunicationModality->getId();
        }
        return (int)$this->_CommunicationModality;
    }

    /**
     * _Site::setCommunicationModality
     *
     * @access public
     * @param object CommunicationModality $value
     * @return void
     */
    public function setCommunicationModality($value) {
        if (is_numeric($value)) {
            $this->_CommunicationModality = (int)$value;
        } else {
            $this->_CommunicationModality = $value;
        }
    }

    // }}}
    // Owner foreignkey property + getter/setter {{{

    /**
     * Owner foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Owner = false;

    /**
     * _Site::getOwner
     *
     * @access public
     * @return object Actor
     */
    public function getOwner() {
        if (is_int($this->_Owner) && $this->_Owner > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Owner = $mapper->load(
                array('Id'=>$this->_Owner));
        }
        return $this->_Owner;
    }

    /**
     * _Site::getOwnerId
     *
     * @access public
     * @return integer
     */
    public function getOwnerId() {
        if ($this->_Owner instanceof Actor) {
            return $this->_Owner->getId();
        }
        return (int)$this->_Owner;
    }

    /**
     * _Site::setOwner
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setOwner($value) {
        if (is_numeric($value)) {
            $this->_Owner = (int)$value;
        } else {
            $this->_Owner = $value;
        }
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
     * _Site::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * _Site::setType
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
     * _Site::getTypeConstArray
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
            _Site::SITE_TYPE_QUELCONQUE => _("Any"), 
            _Site::SITE_TYPE_FACTURATION => _("Billing."), 
            _Site::SITE_TYPE_LIVRAISON => _("Delivery"), 
            _Site::SITE_TYPE_FACTURATION_LIVRAISON => _("Billing and delivery")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Contact one to many relation + getter/setter {{{

    /**
     * Contact *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ContactCollection = false;

    /**
     * _Site::getContactCollection
     *
     * @access public
     * @return object Collection
     */
    public function getContactCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Site');
            return $mapper->getManyToMany($this->getId(),
                'Contact', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ContactCollection) {
            $mapper = Mapper::singleton('Site');
            $this->_ContactCollection = $mapper->getManyToMany($this->getId(),
                'Contact');
        }
        return $this->_ContactCollection;
    }

    /**
     * _Site::getContactCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getContactCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getContactCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ContactCollection) {
            $mapper = Mapper::singleton('Site');
            return $mapper->getManyToManyIds($this->getId(), 'Contact');
        }
        return $this->_ContactCollection->getItemIds();
    }

    /**
     * _Site::setContactCollectionIds
     *
     * @access public
     * @return array
     */
    public function setContactCollectionIds($itemIds) {
        $this->_ContactCollection = new Collection('Contact');
        foreach ($itemIds as $id) {
            $this->_ContactCollection->setItem($id);
        }
    }

    /**
     * _Site::setContactCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setContactCollection($value) {
        $this->_ContactCollection = $value;
    }

    /**
     * _Site::ContactCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ContactCollectionIsLoaded() {
        return ($this->_ContactCollection !== false);
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
    public static function getProperties() {
        $return = array(
            'Name' => Object::TYPE_STRING,
            'Email' => Object::TYPE_STRING,
            'Fax' => Object::TYPE_STRING,
            'Phone' => Object::TYPE_STRING,
            'Mobile' => Object::TYPE_STRING,
            'PreferedCommunicationMode' => Object::TYPE_INT,
            'StreetNumber' => Object::TYPE_STRING,
            'StreetType' => Object::TYPE_CONST,
            'StreetName' => Object::TYPE_STRING,
            'StreetAddons' => Object::TYPE_STRING,
            'Cedex' => Object::TYPE_STRING,
            'GPS' => Object::TYPE_STRING,
            'CountryCity' => 'CountryCity',
            'Zone' => 'Zone',
            'Planning' => 'WeeklyPlanning',
            'CommunicationModality' => 'CommunicationModality',
            'Owner' => 'Actor',
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
            'Contact'=>array(
                'linkClass'     => 'Contact',
                'field'         => 'FromSite',
                'linkTable'     => 'sitContact',
                'linkField'     => 'ToContact',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'DepartureSite',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainTask_1'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'ArrivalSite',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Actor'=>array(
                'linkClass'     => 'Actor',
                'field'         => 'MainSite',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActorSiteTransition'=>array(
                'linkClass'     => 'ActorSiteTransition',
                'field'         => 'DepartureSite',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ActorSiteTransition_1'=>array(
                'linkClass'     => 'ActorSiteTransition',
                'field'         => 'ArrivalSite',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'Box'=>array(
                'linkClass'     => 'Box',
                'field'         => 'ExpeditorSite',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Box_1'=>array(
                'linkClass'     => 'Box',
                'field'         => 'DestinatorSite',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainTask'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'DepartureSite',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainTask_1'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'ArrivalSite',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Command'=>array(
                'linkClass'     => 'Command',
                'field'         => 'ExpeditorSite',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Command_1'=>array(
                'linkClass'     => 'Command',
                'field'         => 'DestinatorSite',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ForwardingForm'=>array(
                'linkClass'     => 'ForwardingForm',
                'field'         => 'DestinatorSite',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ForwardingForm_1'=>array(
                'linkClass'     => 'ForwardingForm',
                'field'         => 'ConveyorArrivalSite',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ForwardingForm_2'=>array(
                'linkClass'     => 'ForwardingForm',
                'field'         => 'ConveyorDepartureSite',
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
    // _Site::mutate() {{{

    /**
     * "Mutation" d'un objet parent en classe fille et vice-versa.
     * Cela permet par exemple dans un formulaire de modifier la classe d'un
     * objet via un select.
     *
     * @access public
     * @param string type le type de l'objet vers lequel 'muter'
     * @return object
     **/
    public function mutate($type){
        // on instancie le bon objet
        require_once('Objects/' . $type . '.php');
        $mutant = new $type();
        if(!($mutant instanceof _Site)) {
            trigger_error('Invalid classname provided.', E_USER_ERROR);
        }
        // propriétés fixes
        $mutant->hasBeenInitialized = $this->hasBeenInitialized;
        $mutant->readonly = $this->readonly;
        $mutant->setId($this->getId());
        // propriétés simples
        $properties = $this->getProperties();
        foreach($properties as $property=>$type){
            $getter = 'get' . $property;
            $setter = 'set' . $property;
            if (method_exists($mutant, $setter)) {
                $mutant->$setter($this->$getter());
            }
        }
        // relations
        $links = $this->getLinks();
        foreach($links as $property=>$data){
            $getter = 'get' . $property . 'Collection';
            $setter = 'set' . $property . 'Collection';
            if (method_exists($mutant, $setter)) {
                $mutant->$setter($this->$getter());
            }
        }
        return $mutant;
    }

    // }}}
}

?>