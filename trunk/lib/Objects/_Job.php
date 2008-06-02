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

class _Job extends Object {
    
    // Constructeur {{{

    /**
     * _Job::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Name i18n_string property + getter/setter {{{

    /**
     * Name foreignkey
     *
     * @access private
     * @var mixed object I18nString or integer
     */
    private $_Name = 0;

    /**
     * _Job::getName
     *
     * @access public
     * @param string $locale optional, default is the current locale code
     * @param boolean $useDefaultLocaleIfEmpty determine if the getter must
     * return the translation in the DEFAULT_LOCALE if no translation is found
     * in the current locale.
     * @return string
     */
    public function getName($locale=false, $defaultLocaleIfEmpty=true) {
        $locale = $locale !== false ? $locale : I18N::getLocaleCode();
        if (is_int($this->_Name) && $this->_Name > 0) {
            $this->_Name = Object::load('I18nString', $this->_Name);
        }
        $ret = null;
        if ($this->_Name instanceof I18nString) {
            $getter = 'getStringValue_' . $locale;
            $ret = $this->_Name->$getter();
            if ($ret == null && $defaultLocaleIfEmpty) {
                $getter = 'getStringValue_' . LOCALE_DEFAULT;
                $ret = $this->_Name->$getter();
            }
        }
        return $ret;
    }

    /**
     * _Job::getNameId
     *
     * @access public
     * @return integer
     */
    public function getNameId() {
        if ($this->_Name instanceof I18nString) {
            return $this->_Name->getId();
        }
        return (int)$this->_Name;
    }

    /**
     * _Job::setName
     *
     * @access public
     * @param string $value
     * @param string $locale optional, default is the current locale code
     * @return void
     */
    public function setName($value, $locale=false) {
        if (is_numeric($value)) {
            $this->_Name = (int)$value;
        } else if ($value instanceof I18nString) {
            $this->_Name = $value;
        } else {
            $locale = $locale !== false ? $locale : I18N::getLocaleCode();
            if (!($this->_Name instanceof I18nString)) {
                $this->_Name = Object::load('I18nString', $this->_Name);
                if (!($this->_Name instanceof I18nString)) {
                    $this->_Name = new I18nString();
                }
            }
            $setter = 'setStringValue_'.$locale;
            $this->_Name->$setter($value);
            $this->_Name->save();
        }
    }

    // }}}
    // Actor one to many relation + getter/setter {{{

    /**
     * Actor *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActorCollection = false;

    /**
     * _Job::getActorCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActorCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Job');
            return $mapper->getManyToMany($this->getId(),
                'Actor', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActorCollection) {
            $mapper = Mapper::singleton('Job');
            $this->_ActorCollection = $mapper->getManyToMany($this->getId(),
                'Actor');
        }
        return $this->_ActorCollection;
    }

    /**
     * _Job::getActorCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActorCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getActorCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ActorCollection) {
            $mapper = Mapper::singleton('Job');
            return $mapper->getManyToManyIds($this->getId(), 'Actor');
        }
        return $this->_ActorCollection->getItemIds();
    }

    /**
     * _Job::setActorCollectionIds
     *
     * @access public
     * @return array
     */
    public function setActorCollectionIds($itemIds) {
        $this->_ActorCollection = new Collection('Actor');
        foreach ($itemIds as $id) {
            $this->_ActorCollection->setItem($id);
        }
    }

    /**
     * _Job::setActorCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActorCollection($value) {
        $this->_ActorCollection = $value;
    }

    /**
     * _Job::ActorCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ActorCollectionIsLoaded() {
        return ($this->_ActorCollection !== false);
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
        return 'Job';
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
            'Name' => Object::TYPE_I18N_STRING);
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
            'Actor'=>array(
                'linkClass'     => 'Actor',
                'field'         => 'ToJob',
                'linkTable'     => 'actJob',
                'linkField'     => 'FromActor',
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