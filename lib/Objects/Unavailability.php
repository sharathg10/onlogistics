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

class Unavailability extends Object {
    
    // Constructeur {{{

    /**
     * Unavailability::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Purpose string property + getter/setter {{{

    /**
     * Purpose string property
     *
     * @access private
     * @var string
     */
    private $_Purpose = '';

    /**
     * Unavailability::getPurpose
     *
     * @access public
     * @return string
     */
    public function getPurpose() {
        return $this->_Purpose;
    }

    /**
     * Unavailability::setPurpose
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPurpose($value) {
        $this->_Purpose = $value;
    }

    // }}}
    // BeginDate datetime property + getter/setter {{{

    /**
     * BeginDate int property
     *
     * @access private
     * @var string
     */
    private $_BeginDate = 0;

    /**
     * Unavailability::getBeginDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getBeginDate($format = false) {
        return $this->dateFormat($this->_BeginDate, $format);
    }

    /**
     * Unavailability::setBeginDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBeginDate($value) {
        $this->_BeginDate = $value;
    }

    // }}}
    // EndDate datetime property + getter/setter {{{

    /**
     * EndDate int property
     *
     * @access private
     * @var string
     */
    private $_EndDate = 0;

    /**
     * Unavailability::getEndDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEndDate($format = false) {
        return $this->dateFormat($this->_EndDate, $format);
    }

    /**
     * Unavailability::setEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndDate($value) {
        $this->_EndDate = $value;
    }

    // }}}
    // WeeklyPlanning foreignkey property + getter/setter {{{

    /**
     * WeeklyPlanning foreignkey
     *
     * @access private
     * @var mixed object WeeklyPlanning or integer
     */
    private $_WeeklyPlanning = false;

    /**
     * Unavailability::getWeeklyPlanning
     *
     * @access public
     * @return object WeeklyPlanning
     */
    public function getWeeklyPlanning() {
        if (is_int($this->_WeeklyPlanning) && $this->_WeeklyPlanning > 0) {
            $mapper = Mapper::singleton('WeeklyPlanning');
            $this->_WeeklyPlanning = $mapper->load(
                array('Id'=>$this->_WeeklyPlanning));
        }
        return $this->_WeeklyPlanning;
    }

    /**
     * Unavailability::getWeeklyPlanningId
     *
     * @access public
     * @return integer
     */
    public function getWeeklyPlanningId() {
        if ($this->_WeeklyPlanning instanceof WeeklyPlanning) {
            return $this->_WeeklyPlanning->getId();
        }
        return (int)$this->_WeeklyPlanning;
    }

    /**
     * Unavailability::setWeeklyPlanning
     *
     * @access public
     * @param object WeeklyPlanning $value
     * @return void
     */
    public function setWeeklyPlanning($value) {
        if (is_numeric($value)) {
            $this->_WeeklyPlanning = (int)$value;
        } else {
            $this->_WeeklyPlanning = $value;
        }
    }

    // }}}
    // Command foreignkey property + getter/setter {{{

    /**
     * Command foreignkey
     *
     * @access private
     * @var mixed object Command or integer
     */
    private $_Command = false;

    /**
     * Unavailability::getCommand
     *
     * @access public
     * @return object Command
     */
    public function getCommand() {
        if (is_int($this->_Command) && $this->_Command > 0) {
            $mapper = Mapper::singleton('Command');
            $this->_Command = $mapper->load(
                array('Id'=>$this->_Command));
        }
        return $this->_Command;
    }

    /**
     * Unavailability::getCommandId
     *
     * @access public
     * @return integer
     */
    public function getCommandId() {
        if ($this->_Command instanceof Command) {
            return $this->_Command->getId();
        }
        return (int)$this->_Command;
    }

    /**
     * Unavailability::setCommand
     *
     * @access public
     * @param object Command $value
     * @return void
     */
    public function setCommand($value) {
        if (is_numeric($value)) {
            $this->_Command = (int)$value;
        } else {
            $this->_Command = $value;
        }
    }

    // }}}
    // ActivatedChainOperation foreignkey property + getter/setter {{{

    /**
     * ActivatedChainOperation foreignkey
     *
     * @access private
     * @var mixed object ActivatedChainOperation or integer
     */
    private $_ActivatedChainOperation = false;

    /**
     * Unavailability::getActivatedChainOperation
     *
     * @access public
     * @return object ActivatedChainOperation
     */
    public function getActivatedChainOperation() {
        if (is_int($this->_ActivatedChainOperation) && $this->_ActivatedChainOperation > 0) {
            $mapper = Mapper::singleton('ActivatedChainOperation');
            $this->_ActivatedChainOperation = $mapper->load(
                array('Id'=>$this->_ActivatedChainOperation));
        }
        return $this->_ActivatedChainOperation;
    }

    /**
     * Unavailability::getActivatedChainOperationId
     *
     * @access public
     * @return integer
     */
    public function getActivatedChainOperationId() {
        if ($this->_ActivatedChainOperation instanceof ActivatedChainOperation) {
            return $this->_ActivatedChainOperation->getId();
        }
        return (int)$this->_ActivatedChainOperation;
    }

    /**
     * Unavailability::setActivatedChainOperation
     *
     * @access public
     * @param object ActivatedChainOperation $value
     * @return void
     */
    public function setActivatedChainOperation($value) {
        if (is_numeric($value)) {
            $this->_ActivatedChainOperation = (int)$value;
        } else {
            $this->_ActivatedChainOperation = $value;
        }
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
        return 'Unavailability';
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
            'Purpose' => Object::TYPE_STRING,
            'BeginDate' => Object::TYPE_DATETIME,
            'EndDate' => Object::TYPE_DATETIME,
            'WeeklyPlanning' => 'WeeklyPlanning',
            'Command' => 'Command',
            'ActivatedChainOperation' => 'ActivatedChainOperation');
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
        $return = array();
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