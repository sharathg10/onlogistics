<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IMPORTANT: This is a generated file, please do not edit.
 *
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

/**
 * Contact class
 *
 */
class Contact extends Object {
    
    // Constructeur {{{

    /**
     * Contact::__construct()
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
     * Contact::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * Contact::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
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
     * Contact::getPhone
     *
     * @access public
     * @return string
     */
    public function getPhone() {
        return $this->_Phone;
    }

    /**
     * Contact::setPhone
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPhone($value) {
        $this->_Phone = $value;
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
     * Contact::getFax
     *
     * @access public
     * @return string
     */
    public function getFax() {
        return $this->_Fax;
    }

    /**
     * Contact::setFax
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setFax($value) {
        $this->_Fax = $value;
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
     * Contact::getMobile
     *
     * @access public
     * @return string
     */
    public function getMobile() {
        return $this->_Mobile;
    }

    /**
     * Contact::setMobile
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setMobile($value) {
        $this->_Mobile = $value;
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
     * Contact::getEmail
     *
     * @access public
     * @return string
     */
    public function getEmail() {
        return $this->_Email;
    }

    /**
     * Contact::setEmail
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEmail($value) {
        $this->_Email = $value;
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
     * Contact::getCommunicationModality
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
     * Contact::getCommunicationModalityId
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
     * Contact::setCommunicationModality
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
    // Role foreignkey property + getter/setter {{{

    /**
     * Role foreignkey
     *
     * @access private
     * @var mixed object ContactRole or integer
     */
    private $_Role = false;

    /**
     * Contact::getRole
     *
     * @access public
     * @return object ContactRole
     */
    public function getRole() {
        if (is_int($this->_Role) && $this->_Role > 0) {
            $mapper = Mapper::singleton('ContactRole');
            $this->_Role = $mapper->load(
                array('Id'=>$this->_Role));
        }
        return $this->_Role;
    }

    /**
     * Contact::getRoleId
     *
     * @access public
     * @return integer
     */
    public function getRoleId() {
        if ($this->_Role instanceof ContactRole) {
            return $this->_Role->getId();
        }
        return (int)$this->_Role;
    }

    /**
     * Contact::setRole
     *
     * @access public
     * @param object ContactRole $value
     * @return void
     */
    public function setRole($value) {
        if (is_numeric($value)) {
            $this->_Role = (int)$value;
        } else {
            $this->_Role = $value;
        }
    }

    // }}}
    // Site one to many relation + getter/setter {{{

    /**
     * Site *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_SiteCollection = false;

    /**
     * Contact::getSiteCollection
     *
     * @access public
     * @return object Collection
     */
    public function getSiteCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Contact');
            return $mapper->getManyToMany($this->getId(),
                'Site', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_SiteCollection) {
            $mapper = Mapper::singleton('Contact');
            $this->_SiteCollection = $mapper->getManyToMany($this->getId(),
                'Site');
        }
        return $this->_SiteCollection;
    }

    /**
     * Contact::getSiteCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getSiteCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getSiteCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_SiteCollection) {
            $mapper = Mapper::singleton('Contact');
            return $mapper->getManyToManyIds($this->getId(), 'Site');
        }
        return $this->_SiteCollection->getItemIds();
    }

    /**
     * Contact::setSiteCollectionIds
     *
     * @access public
     * @return array
     */
    public function setSiteCollectionIds($itemIds) {
        $this->_SiteCollection = new Collection('Site');
        foreach ($itemIds as $id) {
            $this->_SiteCollection->setItem($id);
        }
    }

    /**
     * Contact::setSiteCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setSiteCollection($value) {
        $this->_SiteCollection = $value;
    }

    /**
     * Contact::SiteCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function SiteCollectionIsLoaded() {
        return ($this->_SiteCollection !== false);
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
        return 'Contact';
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
            'Phone' => Object::TYPE_STRING,
            'Fax' => Object::TYPE_STRING,
            'Mobile' => Object::TYPE_STRING,
            'Email' => Object::TYPE_STRING,
            'CommunicationModality' => 'CommunicationModality',
            'Role' => 'ContactRole');
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
            'Site'=>array(
                'linkClass'     => 'Site',
                'field'         => 'ToContact',
                'linkTable'     => 'sitContact',
                'linkField'     => 'FromSite',
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