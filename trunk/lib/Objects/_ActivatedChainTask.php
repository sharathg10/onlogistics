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

class _ActivatedChainTask extends Object {
    // class constants {{{

    const DURATIONTYPE_FORFAIT = 0;
    const DURATIONTYPE_KG = 1;
    const DURATIONTYPE_METER = 2;
    const DURATIONTYPE_LM = 3;
    const DURATIONTYPE_QUANTITY = 4;
    const DURATIONTYPE_KM = 5;
    const TRIGGERMODE_MANUAL = 0;
    const TRIGGERMODE_AUTO = 1;
    const TRIGGERMODE_TEMP = 2;
    const COSTTYPE_FORFAIT = 0;
    const COSTTYPE_DAILY = 1;
    const COSTTYPE_HOURLY = 2;
    const COSTTYPE_KG = 3;
    const COSTTYPE_SQUAREMETTER = 4;
    const COSTTYPE_CUBEMETTER = 5;
    const COSTTYPE_LM = 6;
    const COSTTYPE_QUANTITY = 7;
    const COSTTYPE_KM = 8;
    const COSTTYPE_KWATT = 9;
    const STATE_TODO = 0;
    const STATE_IN_PROGRESS = 1;
    const STATE_FINISHED = 2;
    const STATE_STOPPED = 3;
    const WISHED_START_DATE_TYPE_BEGIN_TASK_PLUS_X = 0;
    const WISHED_START_DATE_TYPE_BEGIN_TASK_MINUS_X = 1;
    const WISHED_START_DATE_TYPE_COMMAND_PLUS_X = 2;
    const WISHED_START_DATE_TYPE_COMMAND_MINUS_X = 3;

    // }}}
    // Constructeur {{{

    /**
     * _ActivatedChainTask::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Order string property + getter/setter {{{

    /**
     * Order int property
     *
     * @access private
     * @var integer
     */
    private $_Order = 0;

    /**
     * _ActivatedChainTask::getOrder
     *
     * @access public
     * @return integer
     */
    public function getOrder() {
        return $this->_Order;
    }

    /**
     * _ActivatedChainTask::setOrder
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setOrder($value) {
        if ($value !== null) {
            $this->_Order = (int)$value;
        }
    }

    // }}}
    // Ghost foreignkey property + getter/setter {{{

    /**
     * Ghost foreignkey
     *
     * @access private
     * @var mixed object ActivatedChainTask or integer
     */
    private $_Ghost = false;

    /**
     * _ActivatedChainTask::getGhost
     *
     * @access public
     * @return object ActivatedChainTask
     */
    public function getGhost() {
        if (is_int($this->_Ghost) && $this->_Ghost > 0) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_Ghost = $mapper->load(
                array('Id'=>$this->_Ghost));
        }
        return $this->_Ghost;
    }

    /**
     * _ActivatedChainTask::getGhostId
     *
     * @access public
     * @return integer
     */
    public function getGhostId() {
        if ($this->_Ghost instanceof ActivatedChainTask) {
            return $this->_Ghost->getId();
        }
        return (int)$this->_Ghost;
    }

    /**
     * _ActivatedChainTask::setGhost
     *
     * @access public
     * @param object ActivatedChainTask $value
     * @return void
     */
    public function setGhost($value) {
        if (is_numeric($value)) {
            $this->_Ghost = (int)$value;
        } else {
            $this->_Ghost = $value;
        }
    }

    // }}}
    // Interuptible string property + getter/setter {{{

    /**
     * Interuptible int property
     *
     * @access private
     * @var integer
     */
    private $_Interuptible = 0;

    /**
     * _ActivatedChainTask::getInteruptible
     *
     * @access public
     * @return integer
     */
    public function getInteruptible() {
        return $this->_Interuptible;
    }

    /**
     * _ActivatedChainTask::setInteruptible
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setInteruptible($value) {
        if ($value !== null) {
            $this->_Interuptible = (int)$value;
        }
    }

    // }}}
    // RawDuration float property + getter/setter {{{

    /**
     * RawDuration float property
     *
     * @access private
     * @var float
     */
    private $_RawDuration = 0;

    /**
     * _ActivatedChainTask::getRawDuration
     *
     * @access public
     * @return float
     */
    public function getRawDuration() {
        return $this->_RawDuration;
    }

    /**
     * _ActivatedChainTask::setRawDuration
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRawDuration($value) {
        if ($value !== null) {
            $this->_RawDuration = I18N::extractNumber($value);
        }
    }

    // }}}
    // DurationType const property + getter/setter/getDurationTypeConstArray {{{

    /**
     * DurationType int property
     *
     * @access private
     * @var integer
     */
    private $_DurationType = 0;

    /**
     * _ActivatedChainTask::getDurationType
     *
     * @access public
     * @return integer
     */
    public function getDurationType() {
        return $this->_DurationType;
    }

    /**
     * _ActivatedChainTask::setDurationType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDurationType($value) {
        if ($value !== null) {
            $this->_DurationType = (int)$value;
        }
    }

    /**
     * _ActivatedChainTask::getDurationTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getDurationTypeConstArray($keys = false) {
        $array = array(
            _ActivatedChainTask::DURATIONTYPE_FORFAIT => _("fixed price"), 
            _ActivatedChainTask::DURATIONTYPE_KG => _("Kg"), 
            _ActivatedChainTask::DURATIONTYPE_METER => _("by cube meter"), 
            _ActivatedChainTask::DURATIONTYPE_LM => _("by linear meter"), 
            _ActivatedChainTask::DURATIONTYPE_QUANTITY => _("by unit"), 
            _ActivatedChainTask::DURATIONTYPE_KM => _("by kilometer")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // KilometerNumber float property + getter/setter {{{

    /**
     * KilometerNumber float property
     *
     * @access private
     * @var float
     */
    private $_KilometerNumber = 0;

    /**
     * _ActivatedChainTask::getKilometerNumber
     *
     * @access public
     * @return float
     */
    public function getKilometerNumber() {
        return $this->_KilometerNumber;
    }

    /**
     * _ActivatedChainTask::setKilometerNumber
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setKilometerNumber($value) {
        if ($value !== null) {
            $this->_KilometerNumber = I18N::extractNumber($value);
        }
    }

    // }}}
    // TriggerMode const property + getter/setter/getTriggerModeConstArray {{{

    /**
     * TriggerMode int property
     *
     * @access private
     * @var integer
     */
    private $_TriggerMode = 0;

    /**
     * _ActivatedChainTask::getTriggerMode
     *
     * @access public
     * @return integer
     */
    public function getTriggerMode() {
        return $this->_TriggerMode;
    }

    /**
     * _ActivatedChainTask::setTriggerMode
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTriggerMode($value) {
        if ($value !== null) {
            $this->_TriggerMode = (int)$value;
        }
    }

    /**
     * _ActivatedChainTask::getTriggerModeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getTriggerModeConstArray($keys = false) {
        $array = array(
            _ActivatedChainTask::TRIGGERMODE_MANUAL => _("Manual"), 
            _ActivatedChainTask::TRIGGERMODE_AUTO => _("Automatic"), 
            _ActivatedChainTask::TRIGGERMODE_TEMP => _("Temporal")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // TriggerDelta string property + getter/setter {{{

    /**
     * TriggerDelta int property
     *
     * @access private
     * @var integer
     */
    private $_TriggerDelta = 0;

    /**
     * _ActivatedChainTask::getTriggerDelta
     *
     * @access public
     * @return integer
     */
    public function getTriggerDelta() {
        return $this->_TriggerDelta;
    }

    /**
     * _ActivatedChainTask::setTriggerDelta
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTriggerDelta($value) {
        if ($value !== null) {
            $this->_TriggerDelta = (int)$value;
        }
    }

    // }}}
    // RawCost float property + getter/setter {{{

    /**
     * RawCost float property
     *
     * @access private
     * @var float
     */
    private $_RawCost = 0;

    /**
     * _ActivatedChainTask::getRawCost
     *
     * @access public
     * @return float
     */
    public function getRawCost() {
        return $this->_RawCost;
    }

    /**
     * _ActivatedChainTask::setRawCost
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRawCost($value) {
        if ($value !== null) {
            $this->_RawCost = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // CostType const property + getter/setter/getCostTypeConstArray {{{

    /**
     * CostType int property
     *
     * @access private
     * @var integer
     */
    private $_CostType = 0;

    /**
     * _ActivatedChainTask::getCostType
     *
     * @access public
     * @return integer
     */
    public function getCostType() {
        return $this->_CostType;
    }

    /**
     * _ActivatedChainTask::setCostType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCostType($value) {
        if ($value !== null) {
            $this->_CostType = (int)$value;
        }
    }

    /**
     * _ActivatedChainTask::getCostTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getCostTypeConstArray($keys = false) {
        $array = array(
            _ActivatedChainTask::COSTTYPE_FORFAIT => _("fixed price"), 
            _ActivatedChainTask::COSTTYPE_DAILY => _("daily"), 
            _ActivatedChainTask::COSTTYPE_HOURLY => _("hourly"), 
            _ActivatedChainTask::COSTTYPE_KG => _("Kg"), 
            _ActivatedChainTask::COSTTYPE_SQUAREMETTER => _("by square meter"), 
            _ActivatedChainTask::COSTTYPE_CUBEMETTER => _("by cube meter"), 
            _ActivatedChainTask::COSTTYPE_LM => _("by linear meter"), 
            _ActivatedChainTask::COSTTYPE_QUANTITY => _("by unit"), 
            _ActivatedChainTask::COSTTYPE_KM => _("by kilometer"), 
            _ActivatedChainTask::COSTTYPE_KWATT => _("by kilowatt")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Duration float property + getter/setter {{{

    /**
     * Duration float property
     *
     * @access private
     * @var float
     */
    private $_Duration = 0;

    /**
     * _ActivatedChainTask::getDuration
     *
     * @access public
     * @return float
     */
    public function getDuration() {
        return $this->_Duration;
    }

    /**
     * _ActivatedChainTask::setDuration
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setDuration($value) {
        if ($value !== null) {
            $this->_Duration = I18N::extractNumber($value);
        }
    }

    // }}}
    // Cost float property + getter/setter {{{

    /**
     * Cost float property
     *
     * @access private
     * @var float
     */
    private $_Cost = 0;

    /**
     * _ActivatedChainTask::getCost
     *
     * @access public
     * @return float
     */
    public function getCost() {
        return $this->_Cost;
    }

    /**
     * _ActivatedChainTask::setCost
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCost($value) {
        if ($value !== null) {
            $this->_Cost = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Instructions string property + getter/setter {{{

    /**
     * Instructions string property
     *
     * @access private
     * @var string
     */
    private $_Instructions = '';

    /**
     * _ActivatedChainTask::getInstructions
     *
     * @access public
     * @return string
     */
    public function getInstructions() {
        return $this->_Instructions;
    }

    /**
     * _ActivatedChainTask::setInstructions
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setInstructions($value) {
        $this->_Instructions = $value;
    }

    // }}}
    // Task foreignkey property + getter/setter {{{

    /**
     * Task foreignkey
     *
     * @access private
     * @var mixed object Task or integer
     */
    private $_Task = false;

    /**
     * _ActivatedChainTask::getTask
     *
     * @access public
     * @return object Task
     */
    public function getTask() {
        if (is_int($this->_Task) && $this->_Task > 0) {
            $mapper = Mapper::singleton('Task');
            $this->_Task = $mapper->load(
                array('Id'=>$this->_Task));
        }
        return $this->_Task;
    }

    /**
     * _ActivatedChainTask::getTaskId
     *
     * @access public
     * @return integer
     */
    public function getTaskId() {
        if ($this->_Task instanceof Task) {
            return $this->_Task->getId();
        }
        return (int)$this->_Task;
    }

    /**
     * _ActivatedChainTask::setTask
     *
     * @access public
     * @param object Task $value
     * @return void
     */
    public function setTask($value) {
        if (is_numeric($value)) {
            $this->_Task = (int)$value;
        } else {
            $this->_Task = $value;
        }
    }

    // }}}
    // ActorSiteTransition foreignkey property + getter/setter {{{

    /**
     * ActorSiteTransition foreignkey
     *
     * @access private
     * @var mixed object ActorSiteTransition or integer
     */
    private $_ActorSiteTransition = false;

    /**
     * _ActivatedChainTask::getActorSiteTransition
     *
     * @access public
     * @return object ActorSiteTransition
     */
    public function getActorSiteTransition() {
        if (is_int($this->_ActorSiteTransition) && $this->_ActorSiteTransition > 0) {
            $mapper = Mapper::singleton('ActorSiteTransition');
            $this->_ActorSiteTransition = $mapper->load(
                array('Id'=>$this->_ActorSiteTransition));
        }
        return $this->_ActorSiteTransition;
    }

    /**
     * _ActivatedChainTask::getActorSiteTransitionId
     *
     * @access public
     * @return integer
     */
    public function getActorSiteTransitionId() {
        if ($this->_ActorSiteTransition instanceof ActorSiteTransition) {
            return $this->_ActorSiteTransition->getId();
        }
        return (int)$this->_ActorSiteTransition;
    }

    /**
     * _ActivatedChainTask::setActorSiteTransition
     *
     * @access public
     * @param object ActorSiteTransition $value
     * @return void
     */
    public function setActorSiteTransition($value) {
        if (is_numeric($value)) {
            $this->_ActorSiteTransition = (int)$value;
        } else {
            $this->_ActorSiteTransition = $value;
        }
    }

    // }}}
    // DepartureInstant foreignkey property + getter/setter {{{

    /**
     * DepartureInstant foreignkey
     *
     * @access private
     * @var mixed object AbstractInstant or integer
     */
    private $_DepartureInstant = false;

    /**
     * _ActivatedChainTask::getDepartureInstant
     *
     * @access public
     * @return object AbstractInstant
     */
    public function getDepartureInstant() {
        if (is_int($this->_DepartureInstant) && $this->_DepartureInstant > 0) {
            $mapper = Mapper::singleton('AbstractInstant');
            $this->_DepartureInstant = $mapper->load(
                array('Id'=>$this->_DepartureInstant));
        }
        return $this->_DepartureInstant;
    }

    /**
     * _ActivatedChainTask::getDepartureInstantId
     *
     * @access public
     * @return integer
     */
    public function getDepartureInstantId() {
        if ($this->_DepartureInstant instanceof AbstractInstant) {
            return $this->_DepartureInstant->getId();
        }
        return (int)$this->_DepartureInstant;
    }

    /**
     * _ActivatedChainTask::setDepartureInstant
     *
     * @access public
     * @param object AbstractInstant $value
     * @return void
     */
    public function setDepartureInstant($value) {
        if (is_numeric($value)) {
            $this->_DepartureInstant = (int)$value;
        } else {
            $this->_DepartureInstant = $value;
        }
    }

    // }}}
    // ArrivalInstant foreignkey property + getter/setter {{{

    /**
     * ArrivalInstant foreignkey
     *
     * @access private
     * @var mixed object AbstractInstant or integer
     */
    private $_ArrivalInstant = false;

    /**
     * _ActivatedChainTask::getArrivalInstant
     *
     * @access public
     * @return object AbstractInstant
     */
    public function getArrivalInstant() {
        if (is_int($this->_ArrivalInstant) && $this->_ArrivalInstant > 0) {
            $mapper = Mapper::singleton('AbstractInstant');
            $this->_ArrivalInstant = $mapper->load(
                array('Id'=>$this->_ArrivalInstant));
        }
        return $this->_ArrivalInstant;
    }

    /**
     * _ActivatedChainTask::getArrivalInstantId
     *
     * @access public
     * @return integer
     */
    public function getArrivalInstantId() {
        if ($this->_ArrivalInstant instanceof AbstractInstant) {
            return $this->_ArrivalInstant->getId();
        }
        return (int)$this->_ArrivalInstant;
    }

    /**
     * _ActivatedChainTask::setArrivalInstant
     *
     * @access public
     * @param object AbstractInstant $value
     * @return void
     */
    public function setArrivalInstant($value) {
        if (is_numeric($value)) {
            $this->_ArrivalInstant = (int)$value;
        } else {
            $this->_ArrivalInstant = $value;
        }
    }

    // }}}
    // Begin datetime property + getter/setter {{{

    /**
     * Begin int property
     *
     * @access private
     * @var string
     */
    private $_Begin = 0;

    /**
     * _ActivatedChainTask::getBegin
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getBegin($format = false) {
        return $this->dateFormat($this->_Begin, $format);
    }

    /**
     * _ActivatedChainTask::setBegin
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBegin($value) {
        $this->_Begin = $value;
    }

    // }}}
    // End datetime property + getter/setter {{{

    /**
     * End int property
     *
     * @access private
     * @var string
     */
    private $_End = 0;

    /**
     * _ActivatedChainTask::getEnd
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEnd($format = false) {
        return $this->dateFormat($this->_End, $format);
    }

    /**
     * _ActivatedChainTask::setEnd
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEnd($value) {
        $this->_End = $value;
    }

    // }}}
    // InterruptionDate datetime property + getter/setter {{{

    /**
     * InterruptionDate int property
     *
     * @access private
     * @var string
     */
    private $_InterruptionDate = 0;

    /**
     * _ActivatedChainTask::getInterruptionDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getInterruptionDate($format = false) {
        return $this->dateFormat($this->_InterruptionDate, $format);
    }

    /**
     * _ActivatedChainTask::setInterruptionDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setInterruptionDate($value) {
        $this->_InterruptionDate = $value;
    }

    // }}}
    // RestartDate datetime property + getter/setter {{{

    /**
     * RestartDate int property
     *
     * @access private
     * @var string
     */
    private $_RestartDate = 0;

    /**
     * _ActivatedChainTask::getRestartDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getRestartDate($format = false) {
        return $this->dateFormat($this->_RestartDate, $format);
    }

    /**
     * _ActivatedChainTask::setRestartDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setRestartDate($value) {
        $this->_RestartDate = $value;
    }

    // }}}
    // RealBegin datetime property + getter/setter {{{

    /**
     * RealBegin int property
     *
     * @access private
     * @var string
     */
    private $_RealBegin = 0;

    /**
     * _ActivatedChainTask::getRealBegin
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getRealBegin($format = false) {
        return $this->dateFormat($this->_RealBegin, $format);
    }

    /**
     * _ActivatedChainTask::setRealBegin
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setRealBegin($value) {
        $this->_RealBegin = $value;
    }

    // }}}
    // RealEnd datetime property + getter/setter {{{

    /**
     * RealEnd int property
     *
     * @access private
     * @var string
     */
    private $_RealEnd = 0;

    /**
     * _ActivatedChainTask::getRealEnd
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getRealEnd($format = false) {
        return $this->dateFormat($this->_RealEnd, $format);
    }

    /**
     * _ActivatedChainTask::setRealEnd
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setRealEnd($value) {
        $this->_RealEnd = $value;
    }

    // }}}
    // RealDuration float property + getter/setter {{{

    /**
     * RealDuration float property
     *
     * @access private
     * @var float
     */
    private $_RealDuration = 0;

    /**
     * _ActivatedChainTask::getRealDuration
     *
     * @access public
     * @return float
     */
    public function getRealDuration() {
        return $this->_RealDuration;
    }

    /**
     * _ActivatedChainTask::setRealDuration
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealDuration($value) {
        if ($value !== null) {
            $this->_RealDuration = I18N::extractNumber($value);
        }
    }

    // }}}
    // RealQuantity float property + getter/setter {{{

    /**
     * RealQuantity float property
     *
     * @access private
     * @var float
     */
    private $_RealQuantity = null;

    /**
     * _ActivatedChainTask::getRealQuantity
     *
     * @access public
     * @return float
     */
    public function getRealQuantity() {
        return $this->_RealQuantity;
    }

    /**
     * _ActivatedChainTask::setRealQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealQuantity($value) {
        $this->_RealQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // RealCost float property + getter/setter {{{

    /**
     * RealCost float property
     *
     * @access private
     * @var float
     */
    private $_RealCost = null;

    /**
     * _ActivatedChainTask::getRealCost
     *
     * @access public
     * @return float
     */
    public function getRealCost() {
        return $this->_RealCost;
    }

    /**
     * _ActivatedChainTask::setRealCost
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealCost($value) {
        $this->_RealCost = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // ActivatedOperation foreignkey property + getter/setter {{{

    /**
     * ActivatedOperation foreignkey
     *
     * @access private
     * @var mixed object ActivatedChainOperation or integer
     */
    private $_ActivatedOperation = false;

    /**
     * _ActivatedChainTask::getActivatedOperation
     *
     * @access public
     * @return object ActivatedChainOperation
     */
    public function getActivatedOperation() {
        if (is_int($this->_ActivatedOperation) && $this->_ActivatedOperation > 0) {
            $mapper = Mapper::singleton('ActivatedChainOperation');
            $this->_ActivatedOperation = $mapper->load(
                array('Id'=>$this->_ActivatedOperation));
        }
        return $this->_ActivatedOperation;
    }

    /**
     * _ActivatedChainTask::getActivatedOperationId
     *
     * @access public
     * @return integer
     */
    public function getActivatedOperationId() {
        if ($this->_ActivatedOperation instanceof ActivatedChainOperation) {
            return $this->_ActivatedOperation->getId();
        }
        return (int)$this->_ActivatedOperation;
    }

    /**
     * _ActivatedChainTask::setActivatedOperation
     *
     * @access public
     * @param object ActivatedChainOperation $value
     * @return void
     */
    public function setActivatedOperation($value) {
        if (is_numeric($value)) {
            $this->_ActivatedOperation = (int)$value;
        } else {
            $this->_ActivatedOperation = $value;
        }
    }

    // }}}
    // ValidationUser foreignkey property + getter/setter {{{

    /**
     * ValidationUser foreignkey
     *
     * @access private
     * @var mixed object UserAccount or integer
     */
    private $_ValidationUser = false;

    /**
     * _ActivatedChainTask::getValidationUser
     *
     * @access public
     * @return object UserAccount
     */
    public function getValidationUser() {
        if (is_int($this->_ValidationUser) && $this->_ValidationUser > 0) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_ValidationUser = $mapper->load(
                array('Id'=>$this->_ValidationUser));
        }
        return $this->_ValidationUser;
    }

    /**
     * _ActivatedChainTask::getValidationUserId
     *
     * @access public
     * @return integer
     */
    public function getValidationUserId() {
        if ($this->_ValidationUser instanceof UserAccount) {
            return $this->_ValidationUser->getId();
        }
        return (int)$this->_ValidationUser;
    }

    /**
     * _ActivatedChainTask::setValidationUser
     *
     * @access public
     * @param object UserAccount $value
     * @return void
     */
    public function setValidationUser($value) {
        if (is_numeric($value)) {
            $this->_ValidationUser = (int)$value;
        } else {
            $this->_ValidationUser = $value;
        }
    }

    // }}}
    // OwnerWorkerOrder foreignkey property + getter/setter {{{

    /**
     * OwnerWorkerOrder foreignkey
     *
     * @access private
     * @var mixed object WorkOrder or integer
     */
    private $_OwnerWorkerOrder = false;

    /**
     * _ActivatedChainTask::getOwnerWorkerOrder
     *
     * @access public
     * @return object WorkOrder
     */
    public function getOwnerWorkerOrder() {
        if (is_int($this->_OwnerWorkerOrder) && $this->_OwnerWorkerOrder > 0) {
            $mapper = Mapper::singleton('WorkOrder');
            $this->_OwnerWorkerOrder = $mapper->load(
                array('Id'=>$this->_OwnerWorkerOrder));
        }
        return $this->_OwnerWorkerOrder;
    }

    /**
     * _ActivatedChainTask::getOwnerWorkerOrderId
     *
     * @access public
     * @return integer
     */
    public function getOwnerWorkerOrderId() {
        if ($this->_OwnerWorkerOrder instanceof WorkOrder) {
            return $this->_OwnerWorkerOrder->getId();
        }
        return (int)$this->_OwnerWorkerOrder;
    }

    /**
     * _ActivatedChainTask::setOwnerWorkerOrder
     *
     * @access public
     * @param object WorkOrder $value
     * @return void
     */
    public function setOwnerWorkerOrder($value) {
        if (is_numeric($value)) {
            $this->_OwnerWorkerOrder = (int)$value;
        } else {
            $this->_OwnerWorkerOrder = $value;
        }
    }

    // }}}
    // ActivatedChainTaskDetail foreignkey property + getter/setter {{{

    /**
     * ActivatedChainTaskDetail foreignkey
     *
     * @access private
     * @var mixed object ActivatedChainTaskDetail or integer
     */
    private $_ActivatedChainTaskDetail = false;

    /**
     * _ActivatedChainTask::getActivatedChainTaskDetail
     *
     * @access public
     * @return object ActivatedChainTaskDetail
     */
    public function getActivatedChainTaskDetail() {
        if (is_int($this->_ActivatedChainTaskDetail) && $this->_ActivatedChainTaskDetail > 0) {
            $mapper = Mapper::singleton('ActivatedChainTaskDetail');
            $this->_ActivatedChainTaskDetail = $mapper->load(
                array('Id'=>$this->_ActivatedChainTaskDetail));
        }
        return $this->_ActivatedChainTaskDetail;
    }

    /**
     * _ActivatedChainTask::getActivatedChainTaskDetailId
     *
     * @access public
     * @return integer
     */
    public function getActivatedChainTaskDetailId() {
        if ($this->_ActivatedChainTaskDetail instanceof ActivatedChainTaskDetail) {
            return $this->_ActivatedChainTaskDetail->getId();
        }
        return (int)$this->_ActivatedChainTaskDetail;
    }

    /**
     * _ActivatedChainTask::setActivatedChainTaskDetail
     *
     * @access public
     * @param object ActivatedChainTaskDetail $value
     * @return void
     */
    public function setActivatedChainTaskDetail($value) {
        if (is_numeric($value)) {
            $this->_ActivatedChainTaskDetail = (int)$value;
        } else {
            $this->_ActivatedChainTaskDetail = $value;
        }
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
     * _ActivatedChainTask::getMassified
     *
     * @access public
     * @return integer
     */
    public function getMassified() {
        return $this->_Massified;
    }

    /**
     * _ActivatedChainTask::setMassified
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
    // DataProvider foreignkey property + getter/setter {{{

    /**
     * DataProvider foreignkey
     *
     * @access private
     * @var mixed object ActivatedChainTask or integer
     */
    private $_DataProvider = false;

    /**
     * _ActivatedChainTask::getDataProvider
     *
     * @access public
     * @return object ActivatedChainTask
     */
    public function getDataProvider() {
        if (is_int($this->_DataProvider) && $this->_DataProvider > 0) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_DataProvider = $mapper->load(
                array('Id'=>$this->_DataProvider));
        }
        return $this->_DataProvider;
    }

    /**
     * _ActivatedChainTask::getDataProviderId
     *
     * @access public
     * @return integer
     */
    public function getDataProviderId() {
        if ($this->_DataProvider instanceof ActivatedChainTask) {
            return $this->_DataProvider->getId();
        }
        return (int)$this->_DataProvider;
    }

    /**
     * _ActivatedChainTask::setDataProvider
     *
     * @access public
     * @param object ActivatedChainTask $value
     * @return void
     */
    public function setDataProvider($value) {
        if (is_numeric($value)) {
            $this->_DataProvider = (int)$value;
        } else {
            $this->_DataProvider = $value;
        }
    }

    // }}}
    // WithForecast string property + getter/setter {{{

    /**
     * WithForecast int property
     *
     * @access private
     * @var integer
     */
    private $_WithForecast = 0;

    /**
     * _ActivatedChainTask::getWithForecast
     *
     * @access public
     * @return integer
     */
    public function getWithForecast() {
        return $this->_WithForecast;
    }

    /**
     * _ActivatedChainTask::setWithForecast
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setWithForecast($value) {
        if ($value !== null) {
            $this->_WithForecast = (int)$value;
        }
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
     * _ActivatedChainTask::getState
     *
     * @access public
     * @return integer
     */
    public function getState() {
        return $this->_State;
    }

    /**
     * _ActivatedChainTask::setState
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
     * _ActivatedChainTask::getStateConstArray
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
            _ActivatedChainTask::STATE_TODO => _("To do"), 
            _ActivatedChainTask::STATE_IN_PROGRESS => _("In progress"), 
            _ActivatedChainTask::STATE_FINISHED => _("Finished"), 
            _ActivatedChainTask::STATE_STOPPED => _("Suspended")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // ProductCommandType string property + getter/setter {{{

    /**
     * ProductCommandType int property
     *
     * @access private
     * @var integer
     */
    private $_ProductCommandType = 0;

    /**
     * _ActivatedChainTask::getProductCommandType
     *
     * @access public
     * @return integer
     */
    public function getProductCommandType() {
        return $this->_ProductCommandType;
    }

    /**
     * _ActivatedChainTask::setProductCommandType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setProductCommandType($value) {
        if ($value !== null) {
            $this->_ProductCommandType = (int)$value;
        }
    }

    // }}}
    // DepartureActor foreignkey property + getter/setter {{{

    /**
     * DepartureActor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_DepartureActor = false;

    /**
     * _ActivatedChainTask::getDepartureActor
     *
     * @access public
     * @return object Actor
     */
    public function getDepartureActor() {
        if (is_int($this->_DepartureActor) && $this->_DepartureActor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_DepartureActor = $mapper->load(
                array('Id'=>$this->_DepartureActor));
        }
        return $this->_DepartureActor;
    }

    /**
     * _ActivatedChainTask::getDepartureActorId
     *
     * @access public
     * @return integer
     */
    public function getDepartureActorId() {
        if ($this->_DepartureActor instanceof Actor) {
            return $this->_DepartureActor->getId();
        }
        return (int)$this->_DepartureActor;
    }

    /**
     * _ActivatedChainTask::setDepartureActor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setDepartureActor($value) {
        if (is_numeric($value)) {
            $this->_DepartureActor = (int)$value;
        } else {
            $this->_DepartureActor = $value;
        }
    }

    // }}}
    // DepartureSite foreignkey property + getter/setter {{{

    /**
     * DepartureSite foreignkey
     *
     * @access private
     * @var mixed object Site or integer
     */
    private $_DepartureSite = false;

    /**
     * _ActivatedChainTask::getDepartureSite
     *
     * @access public
     * @return object Site
     */
    public function getDepartureSite() {
        if (is_int($this->_DepartureSite) && $this->_DepartureSite > 0) {
            $mapper = Mapper::singleton('Site');
            $this->_DepartureSite = $mapper->load(
                array('Id'=>$this->_DepartureSite));
        }
        return $this->_DepartureSite;
    }

    /**
     * _ActivatedChainTask::getDepartureSiteId
     *
     * @access public
     * @return integer
     */
    public function getDepartureSiteId() {
        if ($this->_DepartureSite instanceof Site) {
            return $this->_DepartureSite->getId();
        }
        return (int)$this->_DepartureSite;
    }

    /**
     * _ActivatedChainTask::setDepartureSite
     *
     * @access public
     * @param object Site $value
     * @return void
     */
    public function setDepartureSite($value) {
        if (is_numeric($value)) {
            $this->_DepartureSite = (int)$value;
        } else {
            $this->_DepartureSite = $value;
        }
    }

    // }}}
    // ArrivalActor foreignkey property + getter/setter {{{

    /**
     * ArrivalActor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_ArrivalActor = false;

    /**
     * _ActivatedChainTask::getArrivalActor
     *
     * @access public
     * @return object Actor
     */
    public function getArrivalActor() {
        if (is_int($this->_ArrivalActor) && $this->_ArrivalActor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_ArrivalActor = $mapper->load(
                array('Id'=>$this->_ArrivalActor));
        }
        return $this->_ArrivalActor;
    }

    /**
     * _ActivatedChainTask::getArrivalActorId
     *
     * @access public
     * @return integer
     */
    public function getArrivalActorId() {
        if ($this->_ArrivalActor instanceof Actor) {
            return $this->_ArrivalActor->getId();
        }
        return (int)$this->_ArrivalActor;
    }

    /**
     * _ActivatedChainTask::setArrivalActor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setArrivalActor($value) {
        if (is_numeric($value)) {
            $this->_ArrivalActor = (int)$value;
        } else {
            $this->_ArrivalActor = $value;
        }
    }

    // }}}
    // ArrivalSite foreignkey property + getter/setter {{{

    /**
     * ArrivalSite foreignkey
     *
     * @access private
     * @var mixed object Site or integer
     */
    private $_ArrivalSite = false;

    /**
     * _ActivatedChainTask::getArrivalSite
     *
     * @access public
     * @return object Site
     */
    public function getArrivalSite() {
        if (is_int($this->_ArrivalSite) && $this->_ArrivalSite > 0) {
            $mapper = Mapper::singleton('Site');
            $this->_ArrivalSite = $mapper->load(
                array('Id'=>$this->_ArrivalSite));
        }
        return $this->_ArrivalSite;
    }

    /**
     * _ActivatedChainTask::getArrivalSiteId
     *
     * @access public
     * @return integer
     */
    public function getArrivalSiteId() {
        if ($this->_ArrivalSite instanceof Site) {
            return $this->_ArrivalSite->getId();
        }
        return (int)$this->_ArrivalSite;
    }

    /**
     * _ActivatedChainTask::setArrivalSite
     *
     * @access public
     * @param object Site $value
     * @return void
     */
    public function setArrivalSite($value) {
        if (is_numeric($value)) {
            $this->_ArrivalSite = (int)$value;
        } else {
            $this->_ArrivalSite = $value;
        }
    }

    // }}}
    // WishedDateType const property + getter/setter/getWishedDateTypeConstArray {{{

    /**
     * WishedDateType int property
     *
     * @access private
     * @var integer
     */
    private $_WishedDateType = 0;

    /**
     * _ActivatedChainTask::getWishedDateType
     *
     * @access public
     * @return integer
     */
    public function getWishedDateType() {
        return $this->_WishedDateType;
    }

    /**
     * _ActivatedChainTask::setWishedDateType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setWishedDateType($value) {
        if ($value !== null) {
            $this->_WishedDateType = (int)$value;
        }
    }

    /**
     * _ActivatedChainTask::getWishedDateTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getWishedDateTypeConstArray($keys = false) {
        $array = array(
            _ActivatedChainTask::WISHED_START_DATE_TYPE_BEGIN_TASK_PLUS_X => _("Same as activation task beginning date plus X hours"), 
            _ActivatedChainTask::WISHED_START_DATE_TYPE_BEGIN_TASK_MINUS_X => _("Same as activation task beginning date minus X hours"), 
            _ActivatedChainTask::WISHED_START_DATE_TYPE_COMMAND_PLUS_X => _("initial order wished date plus X hours"), 
            _ActivatedChainTask::WISHED_START_DATE_TYPE_COMMAND_MINUS_X => _("initial order wished date minus X hours")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Delta string property + getter/setter {{{

    /**
     * Delta int property
     *
     * @access private
     * @var integer
     */
    private $_Delta = 0;

    /**
     * _ActivatedChainTask::getDelta
     *
     * @access public
     * @return integer
     */
    public function getDelta() {
        return $this->_Delta;
    }

    /**
     * _ActivatedChainTask::setDelta
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDelta($value) {
        if ($value !== null) {
            $this->_Delta = (int)$value;
        }
    }

    // }}}
    // ComponentQuantityRatio string property + getter/setter {{{

    /**
     * ComponentQuantityRatio int property
     *
     * @access private
     * @var integer
     */
    private $_ComponentQuantityRatio = 0;

    /**
     * _ActivatedChainTask::getComponentQuantityRatio
     *
     * @access public
     * @return integer
     */
    public function getComponentQuantityRatio() {
        return $this->_ComponentQuantityRatio;
    }

    /**
     * _ActivatedChainTask::setComponentQuantityRatio
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setComponentQuantityRatio($value) {
        if ($value !== null) {
            $this->_ComponentQuantityRatio = (int)$value;
        }
    }

    // }}}
    // ActivationPerSupplier string property + getter/setter {{{

    /**
     * ActivationPerSupplier int property
     *
     * @access private
     * @var integer
     */
    private $_ActivationPerSupplier = 0;

    /**
     * _ActivatedChainTask::getActivationPerSupplier
     *
     * @access public
     * @return integer
     */
    public function getActivationPerSupplier() {
        return $this->_ActivationPerSupplier;
    }

    /**
     * _ActivatedChainTask::setActivationPerSupplier
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setActivationPerSupplier($value) {
        if ($value !== null) {
            $this->_ActivationPerSupplier = (int)$value;
        }
    }

    // }}}
    // AssembledQuantity float property + getter/setter {{{

    /**
     * AssembledQuantity float property
     *
     * @access private
     * @var float
     */
    private $_AssembledQuantity = null;

    /**
     * _ActivatedChainTask::getAssembledQuantity
     *
     * @access public
     * @return float
     */
    public function getAssembledQuantity() {
        return $this->_AssembledQuantity;
    }

    /**
     * _ActivatedChainTask::setAssembledQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setAssembledQuantity($value) {
        $this->_AssembledQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // AssembledRealQuantity float property + getter/setter {{{

    /**
     * AssembledRealQuantity float property
     *
     * @access private
     * @var float
     */
    private $_AssembledRealQuantity = null;

    /**
     * _ActivatedChainTask::getAssembledRealQuantity
     *
     * @access public
     * @return float
     */
    public function getAssembledRealQuantity() {
        return $this->_AssembledRealQuantity;
    }

    /**
     * _ActivatedChainTask::setAssembledRealQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setAssembledRealQuantity($value) {
        $this->_AssembledRealQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // Component foreignkey property + getter/setter {{{

    /**
     * Component foreignkey
     *
     * @access private
     * @var mixed object Component or integer
     */
    private $_Component = false;

    /**
     * _ActivatedChainTask::getComponent
     *
     * @access public
     * @return object Component
     */
    public function getComponent() {
        if (is_int($this->_Component) && $this->_Component > 0) {
            $mapper = Mapper::singleton('Component');
            $this->_Component = $mapper->load(
                array('Id'=>$this->_Component));
        }
        return $this->_Component;
    }

    /**
     * _ActivatedChainTask::getComponentId
     *
     * @access public
     * @return integer
     */
    public function getComponentId() {
        if ($this->_Component instanceof Component) {
            return $this->_Component->getId();
        }
        return (int)$this->_Component;
    }

    /**
     * _ActivatedChainTask::setComponent
     *
     * @access public
     * @param object Component $value
     * @return void
     */
    public function setComponent($value) {
        if (is_numeric($value)) {
            $this->_Component = (int)$value;
        } else {
            $this->_Component = $value;
        }
    }

    // }}}
    // ChainToActivate foreignkey property + getter/setter {{{

    /**
     * ChainToActivate foreignkey
     *
     * @access private
     * @var mixed object Chain or integer
     */
    private $_ChainToActivate = false;

    /**
     * _ActivatedChainTask::getChainToActivate
     *
     * @access public
     * @return object Chain
     */
    public function getChainToActivate() {
        if (is_int($this->_ChainToActivate) && $this->_ChainToActivate > 0) {
            $mapper = Mapper::singleton('Chain');
            $this->_ChainToActivate = $mapper->load(
                array('Id'=>$this->_ChainToActivate));
        }
        return $this->_ChainToActivate;
    }

    /**
     * _ActivatedChainTask::getChainToActivateId
     *
     * @access public
     * @return integer
     */
    public function getChainToActivateId() {
        if ($this->_ChainToActivate instanceof Chain) {
            return $this->_ChainToActivate->getId();
        }
        return (int)$this->_ChainToActivate;
    }

    /**
     * _ActivatedChainTask::setChainToActivate
     *
     * @access public
     * @param object Chain $value
     * @return void
     */
    public function setChainToActivate($value) {
        if (is_numeric($value)) {
            $this->_ChainToActivate = (int)$value;
        } else {
            $this->_ChainToActivate = $value;
        }
    }

    // }}}
    // RessourceGroup foreignkey property + getter/setter {{{

    /**
     * RessourceGroup foreignkey
     *
     * @access private
     * @var mixed object RessourceGroup or integer
     */
    private $_RessourceGroup = false;

    /**
     * _ActivatedChainTask::getRessourceGroup
     *
     * @access public
     * @return object RessourceGroup
     */
    public function getRessourceGroup() {
        if (is_int($this->_RessourceGroup) && $this->_RessourceGroup > 0) {
            $mapper = Mapper::singleton('RessourceGroup');
            $this->_RessourceGroup = $mapper->load(
                array('Id'=>$this->_RessourceGroup));
        }
        return $this->_RessourceGroup;
    }

    /**
     * _ActivatedChainTask::getRessourceGroupId
     *
     * @access public
     * @return integer
     */
    public function getRessourceGroupId() {
        if ($this->_RessourceGroup instanceof RessourceGroup) {
            return $this->_RessourceGroup->getId();
        }
        return (int)$this->_RessourceGroup;
    }

    /**
     * _ActivatedChainTask::setRessourceGroup
     *
     * @access public
     * @param object RessourceGroup $value
     * @return void
     */
    public function setRessourceGroup($value) {
        if (is_numeric($value)) {
            $this->_RessourceGroup = (int)$value;
        } else {
            $this->_RessourceGroup = $value;
        }
    }

    // }}}
    // UserAccount one to many relation + getter/setter {{{

    /**
     * UserAccount *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_UserAccountCollection = false;

    /**
     * _ActivatedChainTask::getUserAccountCollection
     *
     * @access public
     * @return object Collection
     */
    public function getUserAccountCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            return $mapper->getManyToMany($this->getId(),
                'UserAccount', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_UserAccountCollection) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_UserAccountCollection = $mapper->getManyToMany($this->getId(),
                'UserAccount');
        }
        return $this->_UserAccountCollection;
    }

    /**
     * _ActivatedChainTask::getUserAccountCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getUserAccountCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getUserAccountCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_UserAccountCollection) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            return $mapper->getManyToManyIds($this->getId(), 'UserAccount');
        }
        return $this->_UserAccountCollection->getItemIds();
    }

    /**
     * _ActivatedChainTask::setUserAccountCollectionIds
     *
     * @access public
     * @return array
     */
    public function setUserAccountCollectionIds($itemIds) {
        $this->_UserAccountCollection = new Collection('UserAccount');
        foreach ($itemIds as $id) {
            $this->_UserAccountCollection->setItem($id);
        }
    }

    /**
     * _ActivatedChainTask::setUserAccountCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setUserAccountCollection($value) {
        $this->_UserAccountCollection = $value;
    }

    /**
     * _ActivatedChainTask::UserAccountCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function UserAccountCollectionIsLoaded() {
        return ($this->_UserAccountCollection !== false);
    }

    // }}}
    // Component one to many relation + getter/setter {{{

    /**
     * Component *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ComponentCollection = false;

    /**
     * _ActivatedChainTask::getComponentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getComponentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            return $mapper->getManyToMany($this->getId(),
                'Component', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ComponentCollection) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_ComponentCollection = $mapper->getManyToMany($this->getId(),
                'Component');
        }
        return $this->_ComponentCollection;
    }

    /**
     * _ActivatedChainTask::getComponentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getComponentCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getComponentCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ComponentCollection) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            return $mapper->getManyToManyIds($this->getId(), 'Component');
        }
        return $this->_ComponentCollection->getItemIds();
    }

    /**
     * _ActivatedChainTask::setComponentCollectionIds
     *
     * @access public
     * @return array
     */
    public function setComponentCollectionIds($itemIds) {
        $this->_ComponentCollection = new Collection('Component');
        foreach ($itemIds as $id) {
            $this->_ComponentCollection->setItem($id);
        }
    }

    /**
     * _ActivatedChainTask::setComponentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setComponentCollection($value) {
        $this->_ComponentCollection = $value;
    }

    /**
     * _ActivatedChainTask::ComponentCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ComponentCollectionIsLoaded() {
        return ($this->_ComponentCollection !== false);
    }

    // }}}
    // ConcreteComponent one to many relation + getter/setter {{{

    /**
     * ConcreteComponent *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ConcreteComponentCollection = false;

    /**
     * _ActivatedChainTask::getConcreteComponentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getConcreteComponentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            return $mapper->getManyToMany($this->getId(),
                'ConcreteComponent', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ConcreteComponentCollection) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_ConcreteComponentCollection = $mapper->getManyToMany($this->getId(),
                'ConcreteComponent');
        }
        return $this->_ConcreteComponentCollection;
    }

    /**
     * _ActivatedChainTask::getConcreteComponentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getConcreteComponentCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getConcreteComponentCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ConcreteComponentCollection) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            return $mapper->getManyToManyIds($this->getId(), 'ConcreteComponent');
        }
        return $this->_ConcreteComponentCollection->getItemIds();
    }

    /**
     * _ActivatedChainTask::setConcreteComponentCollectionIds
     *
     * @access public
     * @return array
     */
    public function setConcreteComponentCollectionIds($itemIds) {
        $this->_ConcreteComponentCollection = new Collection('ConcreteComponent');
        foreach ($itemIds as $id) {
            $this->_ConcreteComponentCollection->setItem($id);
        }
    }

    /**
     * _ActivatedChainTask::setConcreteComponentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setConcreteComponentCollection($value) {
        $this->_ConcreteComponentCollection = $value;
    }

    /**
     * _ActivatedChainTask::ConcreteComponentCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ConcreteComponentCollectionIsLoaded() {
        return ($this->_ConcreteComponentCollection !== false);
    }

    // }}}
    // ActivatedChainTask one to many relation + getter/setter {{{

    /**
     * ActivatedChainTask 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActivatedChainTaskCollection = false;

    /**
     * _ActivatedChainTask::getActivatedChainTaskCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedChainTaskCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            return $mapper->getOneToMany($this->getId(),
                'ActivatedChainTask', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedChainTaskCollection) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_ActivatedChainTaskCollection = $mapper->getOneToMany($this->getId(),
                'ActivatedChainTask');
        }
        return $this->_ActivatedChainTaskCollection;
    }

    /**
     * _ActivatedChainTask::getActivatedChainTaskCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActivatedChainTaskCollectionIds($filter = array()) {
        $col = $this->getActivatedChainTaskCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ActivatedChainTask::setActivatedChainTaskCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedChainTaskCollection($value) {
        $this->_ActivatedChainTaskCollection = $value;
    }

    // }}}
    // UploadedDocument one to many relation + getter/setter {{{

    /**
     * UploadedDocument 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_UploadedDocumentCollection = false;

    /**
     * _ActivatedChainTask::getUploadedDocumentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getUploadedDocumentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            return $mapper->getOneToMany($this->getId(),
                'UploadedDocument', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_UploadedDocumentCollection) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_UploadedDocumentCollection = $mapper->getOneToMany($this->getId(),
                'UploadedDocument');
        }
        return $this->_UploadedDocumentCollection;
    }

    /**
     * _ActivatedChainTask::getUploadedDocumentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getUploadedDocumentCollectionIds($filter = array()) {
        $col = $this->getUploadedDocumentCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ActivatedChainTask::setUploadedDocumentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setUploadedDocumentCollection($value) {
        $this->_UploadedDocumentCollection = $value;
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
        return 'ActivatedChainTask';
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
        return _('Tasks');
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
            'Order' => Object::TYPE_INT,
            'Ghost' => 'ActivatedChainTask',
            'Interuptible' => Object::TYPE_INT,
            'RawDuration' => Object::TYPE_FLOAT,
            'DurationType' => Object::TYPE_CONST,
            'KilometerNumber' => Object::TYPE_FLOAT,
            'TriggerMode' => Object::TYPE_CONST,
            'TriggerDelta' => Object::TYPE_INT,
            'RawCost' => Object::TYPE_DECIMAL,
            'CostType' => Object::TYPE_CONST,
            'Duration' => Object::TYPE_FLOAT,
            'Cost' => Object::TYPE_DECIMAL,
            'Instructions' => Object::TYPE_STRING,
            'Task' => 'Task',
            'ActorSiteTransition' => 'ActorSiteTransition',
            'DepartureInstant' => 'AbstractInstant',
            'ArrivalInstant' => 'AbstractInstant',
            'Begin' => Object::TYPE_DATETIME,
            'End' => Object::TYPE_DATETIME,
            'InterruptionDate' => Object::TYPE_DATETIME,
            'RestartDate' => Object::TYPE_DATETIME,
            'RealBegin' => Object::TYPE_DATETIME,
            'RealEnd' => Object::TYPE_DATETIME,
            'RealDuration' => Object::TYPE_FLOAT,
            'RealQuantity' => Object::TYPE_DECIMAL,
            'RealCost' => Object::TYPE_DECIMAL,
            'ActivatedOperation' => 'ActivatedChainOperation',
            'ValidationUser' => 'UserAccount',
            'OwnerWorkerOrder' => 'WorkOrder',
            'ActivatedChainTaskDetail' => 'ActivatedChainTaskDetail',
            'Massified' => Object::TYPE_INT,
            'DataProvider' => 'ActivatedChainTask',
            'WithForecast' => Object::TYPE_INT,
            'State' => Object::TYPE_CONST,
            'ProductCommandType' => Object::TYPE_INT,
            'DepartureActor' => 'Actor',
            'DepartureSite' => 'Site',
            'ArrivalActor' => 'Actor',
            'ArrivalSite' => 'Site',
            'WishedDateType' => Object::TYPE_CONST,
            'Delta' => Object::TYPE_INT,
            'ComponentQuantityRatio' => Object::TYPE_BOOL,
            'ActivationPerSupplier' => Object::TYPE_BOOL,
            'AssembledQuantity' => Object::TYPE_DECIMAL,
            'AssembledRealQuantity' => Object::TYPE_DECIMAL,
            'Component' => 'Component',
            'ChainToActivate' => 'Chain',
            'RessourceGroup' => 'RessourceGroup');
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
            'UserAccount'=>array(
                'linkClass'     => 'UserAccount',
                'field'         => 'FromActivatedChainTask',
                'linkTable'     => 'ackUserAccount',
                'linkField'     => 'ToUserAccount',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Component'=>array(
                'linkClass'     => 'Component',
                'field'         => 'FromActivatedChainTask',
                'linkTable'     => 'ackComponent',
                'linkField'     => 'ToComponent',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ConcreteComponent'=>array(
                'linkClass'     => 'ConcreteComponent',
                'field'         => 'ToActivatedChainTask',
                'linkTable'     => 'ackConcreteComponent',
                'linkField'     => 'FromConcreteComponent',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ActivatedChain'=>array(
                'linkClass'     => 'ActivatedChain',
                'field'         => 'PivotTask',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainOperation'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'FirstTask',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainOperation_1'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'LastTask',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'Ghost',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainTask_1'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'DataProvider',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedMovement'=>array(
                'linkClass'     => 'ActivatedMovement',
                'field'         => 'ActivatedChainTask',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'RealBox'=>array(
                'linkClass'     => 'RealBox',
                'field'         => 'ActivatedChainTask',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'UploadedDocument'=>array(
                'linkClass'     => 'UploadedDocument',
                'field'         => 'ActivatedChainTask',
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
        return array('grid', 'searchform');
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