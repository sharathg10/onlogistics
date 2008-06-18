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

class _WeeklyPlanning extends Object {
    
    // Constructeur {{{

    /**
     * _WeeklyPlanning::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Monday foreignkey property + getter/setter {{{

    /**
     * Monday foreignkey
     *
     * @access private
     * @var mixed object DailyPlanning or integer
     */
    private $_Monday = false;

    /**
     * _WeeklyPlanning::getMonday
     *
     * @access public
     * @return object DailyPlanning
     */
    public function getMonday() {
        if (is_int($this->_Monday) && $this->_Monday > 0) {
            $mapper = Mapper::singleton('DailyPlanning');
            $this->_Monday = $mapper->load(
                array('Id'=>$this->_Monday));
        }
        return $this->_Monday;
    }

    /**
     * _WeeklyPlanning::getMondayId
     *
     * @access public
     * @return integer
     */
    public function getMondayId() {
        if ($this->_Monday instanceof DailyPlanning) {
            return $this->_Monday->getId();
        }
        return (int)$this->_Monday;
    }

    /**
     * _WeeklyPlanning::setMonday
     *
     * @access public
     * @param object DailyPlanning $value
     * @return void
     */
    public function setMonday($value) {
        if (is_numeric($value)) {
            $this->_Monday = (int)$value;
        } else {
            $this->_Monday = $value;
        }
    }

    // }}}
    // Tuesday foreignkey property + getter/setter {{{

    /**
     * Tuesday foreignkey
     *
     * @access private
     * @var mixed object DailyPlanning or integer
     */
    private $_Tuesday = false;

    /**
     * _WeeklyPlanning::getTuesday
     *
     * @access public
     * @return object DailyPlanning
     */
    public function getTuesday() {
        if (is_int($this->_Tuesday) && $this->_Tuesday > 0) {
            $mapper = Mapper::singleton('DailyPlanning');
            $this->_Tuesday = $mapper->load(
                array('Id'=>$this->_Tuesday));
        }
        return $this->_Tuesday;
    }

    /**
     * _WeeklyPlanning::getTuesdayId
     *
     * @access public
     * @return integer
     */
    public function getTuesdayId() {
        if ($this->_Tuesday instanceof DailyPlanning) {
            return $this->_Tuesday->getId();
        }
        return (int)$this->_Tuesday;
    }

    /**
     * _WeeklyPlanning::setTuesday
     *
     * @access public
     * @param object DailyPlanning $value
     * @return void
     */
    public function setTuesday($value) {
        if (is_numeric($value)) {
            $this->_Tuesday = (int)$value;
        } else {
            $this->_Tuesday = $value;
        }
    }

    // }}}
    // Wednesday foreignkey property + getter/setter {{{

    /**
     * Wednesday foreignkey
     *
     * @access private
     * @var mixed object DailyPlanning or integer
     */
    private $_Wednesday = false;

    /**
     * _WeeklyPlanning::getWednesday
     *
     * @access public
     * @return object DailyPlanning
     */
    public function getWednesday() {
        if (is_int($this->_Wednesday) && $this->_Wednesday > 0) {
            $mapper = Mapper::singleton('DailyPlanning');
            $this->_Wednesday = $mapper->load(
                array('Id'=>$this->_Wednesday));
        }
        return $this->_Wednesday;
    }

    /**
     * _WeeklyPlanning::getWednesdayId
     *
     * @access public
     * @return integer
     */
    public function getWednesdayId() {
        if ($this->_Wednesday instanceof DailyPlanning) {
            return $this->_Wednesday->getId();
        }
        return (int)$this->_Wednesday;
    }

    /**
     * _WeeklyPlanning::setWednesday
     *
     * @access public
     * @param object DailyPlanning $value
     * @return void
     */
    public function setWednesday($value) {
        if (is_numeric($value)) {
            $this->_Wednesday = (int)$value;
        } else {
            $this->_Wednesday = $value;
        }
    }

    // }}}
    // Thursday foreignkey property + getter/setter {{{

    /**
     * Thursday foreignkey
     *
     * @access private
     * @var mixed object DailyPlanning or integer
     */
    private $_Thursday = false;

    /**
     * _WeeklyPlanning::getThursday
     *
     * @access public
     * @return object DailyPlanning
     */
    public function getThursday() {
        if (is_int($this->_Thursday) && $this->_Thursday > 0) {
            $mapper = Mapper::singleton('DailyPlanning');
            $this->_Thursday = $mapper->load(
                array('Id'=>$this->_Thursday));
        }
        return $this->_Thursday;
    }

    /**
     * _WeeklyPlanning::getThursdayId
     *
     * @access public
     * @return integer
     */
    public function getThursdayId() {
        if ($this->_Thursday instanceof DailyPlanning) {
            return $this->_Thursday->getId();
        }
        return (int)$this->_Thursday;
    }

    /**
     * _WeeklyPlanning::setThursday
     *
     * @access public
     * @param object DailyPlanning $value
     * @return void
     */
    public function setThursday($value) {
        if (is_numeric($value)) {
            $this->_Thursday = (int)$value;
        } else {
            $this->_Thursday = $value;
        }
    }

    // }}}
    // Friday foreignkey property + getter/setter {{{

    /**
     * Friday foreignkey
     *
     * @access private
     * @var mixed object DailyPlanning or integer
     */
    private $_Friday = false;

    /**
     * _WeeklyPlanning::getFriday
     *
     * @access public
     * @return object DailyPlanning
     */
    public function getFriday() {
        if (is_int($this->_Friday) && $this->_Friday > 0) {
            $mapper = Mapper::singleton('DailyPlanning');
            $this->_Friday = $mapper->load(
                array('Id'=>$this->_Friday));
        }
        return $this->_Friday;
    }

    /**
     * _WeeklyPlanning::getFridayId
     *
     * @access public
     * @return integer
     */
    public function getFridayId() {
        if ($this->_Friday instanceof DailyPlanning) {
            return $this->_Friday->getId();
        }
        return (int)$this->_Friday;
    }

    /**
     * _WeeklyPlanning::setFriday
     *
     * @access public
     * @param object DailyPlanning $value
     * @return void
     */
    public function setFriday($value) {
        if (is_numeric($value)) {
            $this->_Friday = (int)$value;
        } else {
            $this->_Friday = $value;
        }
    }

    // }}}
    // Saturday foreignkey property + getter/setter {{{

    /**
     * Saturday foreignkey
     *
     * @access private
     * @var mixed object DailyPlanning or integer
     */
    private $_Saturday = false;

    /**
     * _WeeklyPlanning::getSaturday
     *
     * @access public
     * @return object DailyPlanning
     */
    public function getSaturday() {
        if (is_int($this->_Saturday) && $this->_Saturday > 0) {
            $mapper = Mapper::singleton('DailyPlanning');
            $this->_Saturday = $mapper->load(
                array('Id'=>$this->_Saturday));
        }
        return $this->_Saturday;
    }

    /**
     * _WeeklyPlanning::getSaturdayId
     *
     * @access public
     * @return integer
     */
    public function getSaturdayId() {
        if ($this->_Saturday instanceof DailyPlanning) {
            return $this->_Saturday->getId();
        }
        return (int)$this->_Saturday;
    }

    /**
     * _WeeklyPlanning::setSaturday
     *
     * @access public
     * @param object DailyPlanning $value
     * @return void
     */
    public function setSaturday($value) {
        if (is_numeric($value)) {
            $this->_Saturday = (int)$value;
        } else {
            $this->_Saturday = $value;
        }
    }

    // }}}
    // Sunday foreignkey property + getter/setter {{{

    /**
     * Sunday foreignkey
     *
     * @access private
     * @var mixed object DailyPlanning or integer
     */
    private $_Sunday = false;

    /**
     * _WeeklyPlanning::getSunday
     *
     * @access public
     * @return object DailyPlanning
     */
    public function getSunday() {
        if (is_int($this->_Sunday) && $this->_Sunday > 0) {
            $mapper = Mapper::singleton('DailyPlanning');
            $this->_Sunday = $mapper->load(
                array('Id'=>$this->_Sunday));
        }
        return $this->_Sunday;
    }

    /**
     * _WeeklyPlanning::getSundayId
     *
     * @access public
     * @return integer
     */
    public function getSundayId() {
        if ($this->_Sunday instanceof DailyPlanning) {
            return $this->_Sunday->getId();
        }
        return (int)$this->_Sunday;
    }

    /**
     * _WeeklyPlanning::setSunday
     *
     * @access public
     * @param object DailyPlanning $value
     * @return void
     */
    public function setSunday($value) {
        if (is_numeric($value)) {
            $this->_Sunday = (int)$value;
        } else {
            $this->_Sunday = $value;
        }
    }

    // }}}
    // ConcreteProduct one to many relation + getter/setter {{{

    /**
     * ConcreteProduct 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ConcreteProductCollection = false;

    /**
     * _WeeklyPlanning::getConcreteProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getConcreteProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('WeeklyPlanning');
            return $mapper->getOneToMany($this->getId(),
                'ConcreteProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ConcreteProductCollection) {
            $mapper = Mapper::singleton('WeeklyPlanning');
            $this->_ConcreteProductCollection = $mapper->getOneToMany($this->getId(),
                'ConcreteProduct');
        }
        return $this->_ConcreteProductCollection;
    }

    /**
     * _WeeklyPlanning::getConcreteProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getConcreteProductCollectionIds($filter = array()) {
        $col = $this->getConcreteProductCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _WeeklyPlanning::setConcreteProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setConcreteProductCollection($value) {
        $this->_ConcreteProductCollection = $value;
    }

    // }}}
    // Unavailability one to many relation + getter/setter {{{

    /**
     * Unavailability 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_UnavailabilityCollection = false;

    /**
     * _WeeklyPlanning::getUnavailabilityCollection
     *
     * @access public
     * @return object Collection
     */
    public function getUnavailabilityCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('WeeklyPlanning');
            return $mapper->getOneToMany($this->getId(),
                'Unavailability', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_UnavailabilityCollection) {
            $mapper = Mapper::singleton('WeeklyPlanning');
            $this->_UnavailabilityCollection = $mapper->getOneToMany($this->getId(),
                'Unavailability');
        }
        return $this->_UnavailabilityCollection;
    }

    /**
     * _WeeklyPlanning::getUnavailabilityCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getUnavailabilityCollectionIds($filter = array()) {
        $col = $this->getUnavailabilityCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _WeeklyPlanning::setUnavailabilityCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setUnavailabilityCollection($value) {
        $this->_UnavailabilityCollection = $value;
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
        return 'WeeklyPlanning';
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
            'Monday' => 'DailyPlanning',
            'Tuesday' => 'DailyPlanning',
            'Wednesday' => 'DailyPlanning',
            'Thursday' => 'DailyPlanning',
            'Friday' => 'DailyPlanning',
            'Saturday' => 'DailyPlanning',
            'Sunday' => 'DailyPlanning');
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
            'ConcreteProduct'=>array(
                'linkClass'     => 'ConcreteProduct',
                'field'         => 'WeeklyPlanning',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Site'=>array(
                'linkClass'     => 'Site',
                'field'         => 'Planning',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Unavailability'=>array(
                'linkClass'     => 'Unavailability',
                'field'         => 'WeeklyPlanning',
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