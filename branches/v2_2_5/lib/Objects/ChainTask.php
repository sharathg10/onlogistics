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

class ChainTask extends Object {
    // class constants {{{

    const DURATIONTYPE_FORFAIT = 0;
    const DURATIONTYPE_KG = 1;
    const DURATIONTYPE_METER = 2;
    const DURATIONTYPE_LM = 3;
    const DURATIONTYPE_QUANTITY = 4;
    const DURATIONTYPE_KM = 5;
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
    const TRIGGERMODE_MANUAL = 0;
    const TRIGGERMODE_AUTO = 1;
    const TRIGGERMODE_TEMP = 2;
    const WISHED_START_DATE_TYPE_BEGIN_TASK_PLUS_X = 0;
    const WISHED_START_DATE_TYPE_BEGIN_TASK_MINUS_X = 1;
    const WISHED_START_DATE_TYPE_COMMAND_PLUS_X = 2;
    const WISHED_START_DATE_TYPE_COMMAND_MINUS_X = 3;

    // }}}
    // Constructeur {{{

    /**
     * ChainTask::__construct()
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
     * ChainTask::getOrder
     *
     * @access public
     * @return integer
     */
    public function getOrder() {
        return $this->_Order;
    }

    /**
     * ChainTask::setOrder
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
    // Interuptible string property + getter/setter {{{

    /**
     * Interuptible int property
     *
     * @access private
     * @var integer
     */
    private $_Interuptible = 0;

    /**
     * ChainTask::getInteruptible
     *
     * @access public
     * @return integer
     */
    public function getInteruptible() {
        return $this->_Interuptible;
    }

    /**
     * ChainTask::setInteruptible
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
    // Duration float property + getter/setter {{{

    /**
     * Duration float property
     *
     * @access private
     * @var float
     */
    private $_Duration = 0;

    /**
     * ChainTask::getDuration
     *
     * @access public
     * @return float
     */
    public function getDuration() {
        return $this->_Duration;
    }

    /**
     * ChainTask::setDuration
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
    // DurationType const property + getter/setter/getDurationTypeConstArray {{{

    /**
     * DurationType int property
     *
     * @access private
     * @var integer
     */
    private $_DurationType = 0;

    /**
     * ChainTask::getDurationType
     *
     * @access public
     * @return integer
     */
    public function getDurationType() {
        return $this->_DurationType;
    }

    /**
     * ChainTask::setDurationType
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
     * ChainTask::getDurationTypeConstArray
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
            ChainTask::DURATIONTYPE_FORFAIT => _("fixed price"), 
            ChainTask::DURATIONTYPE_KG => _("Kg"), 
            ChainTask::DURATIONTYPE_METER => _("by cube meter"), 
            ChainTask::DURATIONTYPE_LM => _("by linear meter"), 
            ChainTask::DURATIONTYPE_QUANTITY => _("by unit"), 
            ChainTask::DURATIONTYPE_KM => _("by kilometer")
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
     * ChainTask::getKilometerNumber
     *
     * @access public
     * @return float
     */
    public function getKilometerNumber() {
        return $this->_KilometerNumber;
    }

    /**
     * ChainTask::setKilometerNumber
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
    // Cost float property + getter/setter {{{

    /**
     * Cost float property
     *
     * @access private
     * @var float
     */
    private $_Cost = 0;

    /**
     * ChainTask::getCost
     *
     * @access public
     * @return float
     */
    public function getCost() {
        return $this->_Cost;
    }

    /**
     * ChainTask::setCost
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
    // CostType const property + getter/setter/getCostTypeConstArray {{{

    /**
     * CostType int property
     *
     * @access private
     * @var integer
     */
    private $_CostType = 0;

    /**
     * ChainTask::getCostType
     *
     * @access public
     * @return integer
     */
    public function getCostType() {
        return $this->_CostType;
    }

    /**
     * ChainTask::setCostType
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
     * ChainTask::getCostTypeConstArray
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
            ChainTask::COSTTYPE_FORFAIT => _("fixed price"), 
            ChainTask::COSTTYPE_DAILY => _("daily"), 
            ChainTask::COSTTYPE_HOURLY => _("hourly"), 
            ChainTask::COSTTYPE_KG => _("Kg"), 
            ChainTask::COSTTYPE_SQUAREMETTER => _("by square meter"), 
            ChainTask::COSTTYPE_CUBEMETTER => _("by cube meter"), 
            ChainTask::COSTTYPE_LM => _("by linear meter"), 
            ChainTask::COSTTYPE_QUANTITY => _("by unit"), 
            ChainTask::COSTTYPE_KM => _("by kilometer"), 
            ChainTask::COSTTYPE_KWATT => _("by kilowatt")
        );
        asort($array);
        return $keys?array_keys($array):$array;
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
     * ChainTask::getInstructions
     *
     * @access public
     * @return string
     */
    public function getInstructions() {
        return $this->_Instructions;
    }

    /**
     * ChainTask::setInstructions
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setInstructions($value) {
        $this->_Instructions = $value;
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
     * ChainTask::getTriggerMode
     *
     * @access public
     * @return integer
     */
    public function getTriggerMode() {
        return $this->_TriggerMode;
    }

    /**
     * ChainTask::setTriggerMode
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
     * ChainTask::getTriggerModeConstArray
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
            ChainTask::TRIGGERMODE_MANUAL => _("Manual"), 
            ChainTask::TRIGGERMODE_AUTO => _("Automatic"), 
            ChainTask::TRIGGERMODE_TEMP => _("Temporal")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // TriggerDelta float property + getter/setter {{{

    /**
     * TriggerDelta float property
     *
     * @access private
     * @var float
     */
    private $_TriggerDelta = 0;

    /**
     * ChainTask::getTriggerDelta
     *
     * @access public
     * @return float
     */
    public function getTriggerDelta() {
        return $this->_TriggerDelta;
    }

    /**
     * ChainTask::setTriggerDelta
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTriggerDelta($value) {
        if ($value !== null) {
            $this->_TriggerDelta = I18N::extractNumber($value);
        }
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
     * ChainTask::getTask
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
     * ChainTask::getTaskId
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
     * ChainTask::setTask
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
     * ChainTask::getActorSiteTransition
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
     * ChainTask::getActorSiteTransitionId
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
     * ChainTask::setActorSiteTransition
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
     * ChainTask::getDepartureInstant
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
     * ChainTask::getDepartureInstantId
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
     * ChainTask::setDepartureInstant
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
     * ChainTask::getArrivalInstant
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
     * ChainTask::getArrivalInstantId
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
     * ChainTask::setArrivalInstant
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
    // Operation foreignkey property + getter/setter {{{

    /**
     * Operation foreignkey
     *
     * @access private
     * @var mixed object ChainOperation or integer
     */
    private $_Operation = false;

    /**
     * ChainTask::getOperation
     *
     * @access public
     * @return object ChainOperation
     */
    public function getOperation() {
        if (is_int($this->_Operation) && $this->_Operation > 0) {
            $mapper = Mapper::singleton('ChainOperation');
            $this->_Operation = $mapper->load(
                array('Id'=>$this->_Operation));
        }
        return $this->_Operation;
    }

    /**
     * ChainTask::getOperationId
     *
     * @access public
     * @return integer
     */
    public function getOperationId() {
        if ($this->_Operation instanceof ChainOperation) {
            return $this->_Operation->getId();
        }
        return (int)$this->_Operation;
    }

    /**
     * ChainTask::setOperation
     *
     * @access public
     * @param object ChainOperation $value
     * @return void
     */
    public function setOperation($value) {
        if (is_numeric($value)) {
            $this->_Operation = (int)$value;
        } else {
            $this->_Operation = $value;
        }
    }

    // }}}
    // AutoAlert int property + getter/setter {{{

    /**
     * AutoAlert int property
     *
     * @access private
     * @var integer
     */
    private $_AutoAlert = null;

    /**
     * ChainTask::getAutoAlert
     *
     * @access public
     * @return integer
     */
    public function getAutoAlert() {
        return $this->_AutoAlert;
    }

    /**
     * ChainTask::setAutoAlert
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setAutoAlert($value) {
        $this->_AutoAlert = ($value===null || $value === '')?null:(int)$value;
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
     * ChainTask::getProductCommandType
     *
     * @access public
     * @return integer
     */
    public function getProductCommandType() {
        return $this->_ProductCommandType;
    }

    /**
     * ChainTask::setProductCommandType
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
     * ChainTask::getDepartureActor
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
     * ChainTask::getDepartureActorId
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
     * ChainTask::setDepartureActor
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
     * ChainTask::getDepartureSite
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
     * ChainTask::getDepartureSiteId
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
     * ChainTask::setDepartureSite
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
     * ChainTask::getArrivalActor
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
     * ChainTask::getArrivalActorId
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
     * ChainTask::setArrivalActor
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
     * ChainTask::getArrivalSite
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
     * ChainTask::getArrivalSiteId
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
     * ChainTask::setArrivalSite
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
     * ChainTask::getWishedDateType
     *
     * @access public
     * @return integer
     */
    public function getWishedDateType() {
        return $this->_WishedDateType;
    }

    /**
     * ChainTask::setWishedDateType
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
     * ChainTask::getWishedDateTypeConstArray
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
            ChainTask::WISHED_START_DATE_TYPE_BEGIN_TASK_PLUS_X => _("Same as activation task beginning date minus X hours"), 
            ChainTask::WISHED_START_DATE_TYPE_BEGIN_TASK_MINUS_X => _("Same as activation task beginning date plus X hours"), 
            ChainTask::WISHED_START_DATE_TYPE_COMMAND_PLUS_X => _("initial order wished date plus X hours"), 
            ChainTask::WISHED_START_DATE_TYPE_COMMAND_MINUS_X => _("initial order wished date minus X hours")
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
     * ChainTask::getDelta
     *
     * @access public
     * @return integer
     */
    public function getDelta() {
        return $this->_Delta;
    }

    /**
     * ChainTask::setDelta
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
     * ChainTask::getComponentQuantityRatio
     *
     * @access public
     * @return integer
     */
    public function getComponentQuantityRatio() {
        return $this->_ComponentQuantityRatio;
    }

    /**
     * ChainTask::setComponentQuantityRatio
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
     * ChainTask::getActivationPerSupplier
     *
     * @access public
     * @return integer
     */
    public function getActivationPerSupplier() {
        return $this->_ActivationPerSupplier;
    }

    /**
     * ChainTask::setActivationPerSupplier
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
    // Component foreignkey property + getter/setter {{{

    /**
     * Component foreignkey
     *
     * @access private
     * @var mixed object Component or integer
     */
    private $_Component = false;

    /**
     * ChainTask::getComponent
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
     * ChainTask::getComponentId
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
     * ChainTask::setComponent
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
     * ChainTask::getChainToActivate
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
     * ChainTask::getChainToActivateId
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
     * ChainTask::setChainToActivate
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
     * ChainTask::getRessourceGroup
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
     * ChainTask::getRessourceGroupId
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
     * ChainTask::setRessourceGroup
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
     * ChainTask::getUserAccountCollection
     *
     * @access public
     * @return object Collection
     */
    public function getUserAccountCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ChainTask');
            return $mapper->getManyToMany($this->getId(),
                'UserAccount', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_UserAccountCollection) {
            $mapper = Mapper::singleton('ChainTask');
            $this->_UserAccountCollection = $mapper->getManyToMany($this->getId(),
                'UserAccount');
        }
        return $this->_UserAccountCollection;
    }

    /**
     * ChainTask::getUserAccountCollectionIds
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
            $mapper = Mapper::singleton('ChainTask');
            return $mapper->getManyToManyIds($this->getId(), 'UserAccount');
        }
        return $this->_UserAccountCollection->getItemIds();
    }

    /**
     * ChainTask::setUserAccountCollectionIds
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
     * ChainTask::setUserAccountCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setUserAccountCollection($value) {
        $this->_UserAccountCollection = $value;
    }

    /**
     * ChainTask::UserAccountCollectionIsLoaded
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
     * ChainTask::getComponentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getComponentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ChainTask');
            return $mapper->getManyToMany($this->getId(),
                'Component', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ComponentCollection) {
            $mapper = Mapper::singleton('ChainTask');
            $this->_ComponentCollection = $mapper->getManyToMany($this->getId(),
                'Component');
        }
        return $this->_ComponentCollection;
    }

    /**
     * ChainTask::getComponentCollectionIds
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
            $mapper = Mapper::singleton('ChainTask');
            return $mapper->getManyToManyIds($this->getId(), 'Component');
        }
        return $this->_ComponentCollection->getItemIds();
    }

    /**
     * ChainTask::setComponentCollectionIds
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
     * ChainTask::setComponentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setComponentCollection($value) {
        $this->_ComponentCollection = $value;
    }

    /**
     * ChainTask::ComponentCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ComponentCollectionIsLoaded() {
        return ($this->_ComponentCollection !== false);
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
        return 'ChainTask';
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
            'Order' => Object::TYPE_INT,
            'Interuptible' => Object::TYPE_INT,
            'Duration' => Object::TYPE_FLOAT,
            'DurationType' => Object::TYPE_CONST,
            'KilometerNumber' => Object::TYPE_FLOAT,
            'Cost' => Object::TYPE_DECIMAL,
            'CostType' => Object::TYPE_CONST,
            'Instructions' => Object::TYPE_STRING,
            'TriggerMode' => Object::TYPE_CONST,
            'TriggerDelta' => Object::TYPE_FLOAT,
            'Task' => 'Task',
            'ActorSiteTransition' => 'ActorSiteTransition',
            'DepartureInstant' => 'AbstractInstant',
            'ArrivalInstant' => 'AbstractInstant',
            'Operation' => 'ChainOperation',
            'AutoAlert' => Object::TYPE_INT,
            'ProductCommandType' => Object::TYPE_INT,
            'DepartureActor' => 'Actor',
            'DepartureSite' => 'Site',
            'ArrivalActor' => 'Actor',
            'ArrivalSite' => 'Site',
            'WishedDateType' => Object::TYPE_CONST,
            'Delta' => Object::TYPE_INT,
            'ComponentQuantityRatio' => Object::TYPE_BOOL,
            'ActivationPerSupplier' => Object::TYPE_BOOL,
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
                'field'         => 'FromChainTask',
                'linkTable'     => 'chtUserAccount',
                'linkField'     => 'ToUserAccount',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Component'=>array(
                'linkClass'     => 'Component',
                'field'         => 'FromChainTask',
                'linkTable'     => 'chtComponent',
                'linkField'     => 'ToComponent',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Chain'=>array(
                'linkClass'     => 'Chain',
                'field'         => 'PivotTask',
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