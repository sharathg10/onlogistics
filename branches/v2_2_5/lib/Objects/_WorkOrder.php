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

class _WorkOrder extends Object {
    // class constants {{{

    const WORK_ORDER_STATE_TOFILL = 0;
    const WORK_ORDER_STATE_FULL = 1;

    // }}}
    // Constructeur {{{

    /**
     * _WorkOrder::__construct()
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
     * _WorkOrder::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _WorkOrder::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // ValidityStart datetime property + getter/setter {{{

    /**
     * ValidityStart int property
     *
     * @access private
     * @var string
     */
    private $_ValidityStart = 0;

    /**
     * _WorkOrder::getValidityStart
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getValidityStart($format = false) {
        return $this->dateFormat($this->_ValidityStart, $format);
    }

    /**
     * _WorkOrder::setValidityStart
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setValidityStart($value) {
        $this->_ValidityStart = $value;
    }

    // }}}
    // ValidityEnd datetime property + getter/setter {{{

    /**
     * ValidityEnd int property
     *
     * @access private
     * @var string
     */
    private $_ValidityEnd = 0;

    /**
     * _WorkOrder::getValidityEnd
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getValidityEnd($format = false) {
        return $this->dateFormat($this->_ValidityEnd, $format);
    }

    /**
     * _WorkOrder::setValidityEnd
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setValidityEnd($value) {
        $this->_ValidityEnd = $value;
    }

    // }}}
    // Actor foreignkey property + getter/setter {{{

    /**
     * Actor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Actor = false;

    /**
     * _WorkOrder::getActor
     *
     * @access public
     * @return object Actor
     */
    public function getActor() {
        if (is_int($this->_Actor) && $this->_Actor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Actor = $mapper->load(
                array('Id'=>$this->_Actor));
        }
        return $this->_Actor;
    }

    /**
     * _WorkOrder::getActorId
     *
     * @access public
     * @return integer
     */
    public function getActorId() {
        if ($this->_Actor instanceof Actor) {
            return $this->_Actor->getId();
        }
        return (int)$this->_Actor;
    }

    /**
     * _WorkOrder::setActor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setActor($value) {
        if (is_numeric($value)) {
            $this->_Actor = (int)$value;
        } else {
            $this->_Actor = $value;
        }
    }

    // }}}
    // Comment string property + getter/setter {{{

    /**
     * Comment string property
     *
     * @access private
     * @var string
     */
    private $_Comment = '';

    /**
     * _WorkOrder::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * _WorkOrder::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // MaxVolume float property + getter/setter {{{

    /**
     * MaxVolume float property
     *
     * @access private
     * @var float
     */
    private $_MaxVolume = 0;

    /**
     * _WorkOrder::getMaxVolume
     *
     * @access public
     * @return float
     */
    public function getMaxVolume() {
        return $this->_MaxVolume;
    }

    /**
     * _WorkOrder::setMaxVolume
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaxVolume($value) {
        if ($value !== null) {
            $this->_MaxVolume = I18N::extractNumber($value);
        }
    }

    // }}}
    // MaxLM float property + getter/setter {{{

    /**
     * MaxLM float property
     *
     * @access private
     * @var float
     */
    private $_MaxLM = 0;

    /**
     * _WorkOrder::getMaxLM
     *
     * @access public
     * @return float
     */
    public function getMaxLM() {
        return $this->_MaxLM;
    }

    /**
     * _WorkOrder::setMaxLM
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaxLM($value) {
        if ($value !== null) {
            $this->_MaxLM = I18N::extractNumber($value);
        }
    }

    // }}}
    // MaxWeigth float property + getter/setter {{{

    /**
     * MaxWeigth float property
     *
     * @access private
     * @var float
     */
    private $_MaxWeigth = 0;

    /**
     * _WorkOrder::getMaxWeigth
     *
     * @access public
     * @return float
     */
    public function getMaxWeigth() {
        return $this->_MaxWeigth;
    }

    /**
     * _WorkOrder::setMaxWeigth
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaxWeigth($value) {
        if ($value !== null) {
            $this->_MaxWeigth = I18N::extractNumber($value);
        }
    }

    // }}}
    // MaxDistance float property + getter/setter {{{

    /**
     * MaxDistance float property
     *
     * @access private
     * @var float
     */
    private $_MaxDistance = 0;

    /**
     * _WorkOrder::getMaxDistance
     *
     * @access public
     * @return float
     */
    public function getMaxDistance() {
        return $this->_MaxDistance;
    }

    /**
     * _WorkOrder::setMaxDistance
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaxDistance($value) {
        if ($value !== null) {
            $this->_MaxDistance = I18N::extractNumber($value);
        }
    }

    // }}}
    // MaxDuration datetime property + getter/setter {{{

    /**
     * MaxDuration int property
     *
     * @access private
     * @var string
     */
    private $_MaxDuration = '00:00';

    /**
     * _WorkOrder::getMaxDuration
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getMaxDuration($format = false) {
        return $this->dateFormat($this->_MaxDuration, $format);
    }

    /**
     * _WorkOrder::setMaxDuration
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setMaxDuration($value) {
        $this->_MaxDuration = $value;
    }

    // }}}
    // State const property + getter/setter/getStateConstArray {{{

    /**
     * State int property
     *
     * @access private
     * @var integer
     */
    private $_State = 0;

    /**
     * _WorkOrder::getState
     *
     * @access public
     * @return integer
     */
    public function getState() {
        return $this->_State;
    }

    /**
     * _WorkOrder::setState
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setState($value) {
        if ($value !== null) {
            $this->_State = (int)$value;
        }
    }

    /**
     * _WorkOrder::getStateConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getStateConstArray($keys = false) {
        $array = array(
            _WorkOrder::WORK_ORDER_STATE_TOFILL => _("Not closed"), 
            _WorkOrder::WORK_ORDER_STATE_FULL => _("closed")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // ClotureDate datetime property + getter/setter {{{

    /**
     * ClotureDate int property
     *
     * @access private
     * @var string
     */
    private $_ClotureDate = 0;

    /**
     * _WorkOrder::getClotureDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getClotureDate($format = false) {
        return $this->dateFormat($this->_ClotureDate, $format);
    }

    /**
     * _WorkOrder::setClotureDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setClotureDate($value) {
        $this->_ClotureDate = $value;
    }

    // }}}
    // Massified string property + getter/setter {{{

    /**
     * Massified int property
     *
     * @access private
     * @var integer
     */
    private $_Massified = 0;

    /**
     * _WorkOrder::getMassified
     *
     * @access public
     * @return integer
     */
    public function getMassified() {
        return $this->_Massified;
    }

    /**
     * _WorkOrder::setMassified
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setMassified($value) {
        if ($value !== null) {
            $this->_Massified = (int)$value;
        }
    }

    // }}}
    // DepartureDate datetime property + getter/setter {{{

    /**
     * DepartureDate int property
     *
     * @access private
     * @var string
     */
    private $_DepartureDate = 0;

    /**
     * _WorkOrder::getDepartureDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getDepartureDate($format = false) {
        return $this->dateFormat($this->_DepartureDate, $format);
    }

    /**
     * _WorkOrder::setDepartureDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDepartureDate($value) {
        $this->_DepartureDate = $value;
    }

    // }}}
    // ArrivalDate datetime property + getter/setter {{{

    /**
     * ArrivalDate int property
     *
     * @access private
     * @var string
     */
    private $_ArrivalDate = 0;

    /**
     * _WorkOrder::getArrivalDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getArrivalDate($format = false) {
        return $this->dateFormat($this->_ArrivalDate, $format);
    }

    /**
     * _WorkOrder::setArrivalDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setArrivalDate($value) {
        $this->_ArrivalDate = $value;
    }

    // }}}
    // DepartureKm float property + getter/setter {{{

    /**
     * DepartureKm float property
     *
     * @access private
     * @var float
     */
    private $_DepartureKm = 0;

    /**
     * _WorkOrder::getDepartureKm
     *
     * @access public
     * @return float
     */
    public function getDepartureKm() {
        return $this->_DepartureKm;
    }

    /**
     * _WorkOrder::setDepartureKm
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setDepartureKm($value) {
        if ($value !== null) {
            $this->_DepartureKm = I18N::extractNumber($value);
        }
    }

    // }}}
    // ArrivalKm float property + getter/setter {{{

    /**
     * ArrivalKm float property
     *
     * @access private
     * @var float
     */
    private $_ArrivalKm = 0;

    /**
     * _WorkOrder::getArrivalKm
     *
     * @access public
     * @return float
     */
    public function getArrivalKm() {
        return $this->_ArrivalKm;
    }

    /**
     * _WorkOrder::setArrivalKm
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setArrivalKm($value) {
        if ($value !== null) {
            $this->_ArrivalKm = I18N::extractNumber($value);
        }
    }

    // }}}
    // ActivatedChainOperation one to many relation + getter/setter {{{

    /**
     * ActivatedChainOperation 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActivatedChainOperationCollection = false;

    /**
     * _WorkOrder::getActivatedChainOperationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedChainOperationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('WorkOrder');
            return $mapper->getOneToMany($this->getId(),
                'ActivatedChainOperation', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedChainOperationCollection) {
            $mapper = Mapper::singleton('WorkOrder');
            $this->_ActivatedChainOperationCollection = $mapper->getOneToMany($this->getId(),
                'ActivatedChainOperation');
        }
        return $this->_ActivatedChainOperationCollection;
    }

    /**
     * _WorkOrder::getActivatedChainOperationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActivatedChainOperationCollectionIds($filter = array()) {
        $col = $this->getActivatedChainOperationCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _WorkOrder::setActivatedChainOperationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedChainOperationCollection($value) {
        $this->_ActivatedChainOperationCollection = $value;
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
        return 'WorkOrder';
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
            'ValidityStart' => Object::TYPE_DATETIME,
            'ValidityEnd' => Object::TYPE_DATETIME,
            'Actor' => 'Actor',
            'Comment' => Object::TYPE_STRING,
            'MaxVolume' => Object::TYPE_FLOAT,
            'MaxLM' => Object::TYPE_FLOAT,
            'MaxWeigth' => Object::TYPE_FLOAT,
            'MaxDistance' => Object::TYPE_FLOAT,
            'MaxDuration' => Object::TYPE_TIME,
            'State' => Object::TYPE_CONST,
            'ClotureDate' => Object::TYPE_DATETIME,
            'Massified' => Object::TYPE_INT,
            'DepartureDate' => Object::TYPE_DATETIME,
            'ArrivalDate' => Object::TYPE_DATETIME,
            'DepartureKm' => Object::TYPE_FLOAT,
            'ArrivalKm' => Object::TYPE_FLOAT);
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
            'ActivatedChain'=>array(
                'linkClass'     => 'ActivatedChain',
                'field'         => 'OwnerWorkerOrder',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainOperation'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'OwnerWorkerOrder',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'OwnerWorkerOrder',
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