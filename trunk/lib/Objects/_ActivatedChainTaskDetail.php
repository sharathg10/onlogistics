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

class _ActivatedChainTaskDetail extends Object {
    // class constants {{{

    const AUCUN = 0;
    const PILOTE = 1;
    const PILOTE_ELEVE = 2;
    const PILOTE_INSTRUCTEUR = 3;
    const COPILOTE = 4;
    const NATURE_NONE = 0;
    const NATURE_INST = 1;
    const NATURE_TP = 2;
    const NATURE_TA = 3;
    const NATURE_SIM = 4;

    // }}}
    // Constructeur {{{

    /**
     * _ActivatedChainTaskDetail::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // OilAdded float property + getter/setter {{{

    /**
     * OilAdded float property
     *
     * @access private
     * @var float
     */
    private $_OilAdded = null;

    /**
     * _ActivatedChainTaskDetail::getOilAdded
     *
     * @access public
     * @return float
     */
    public function getOilAdded() {
        return $this->_OilAdded;
    }

    /**
     * _ActivatedChainTaskDetail::setOilAdded
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setOilAdded($value) {
        $this->_OilAdded = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // CarburantRest float property + getter/setter {{{

    /**
     * CarburantRest float property
     *
     * @access private
     * @var float
     */
    private $_CarburantRest = null;

    /**
     * _ActivatedChainTaskDetail::getCarburantRest
     *
     * @access public
     * @return float
     */
    public function getCarburantRest() {
        return $this->_CarburantRest;
    }

    /**
     * _ActivatedChainTaskDetail::setCarburantRest
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCarburantRest($value) {
        $this->_CarburantRest = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // CarburantAdded float property + getter/setter {{{

    /**
     * CarburantAdded float property
     *
     * @access private
     * @var float
     */
    private $_CarburantAdded = null;

    /**
     * _ActivatedChainTaskDetail::getCarburantAdded
     *
     * @access public
     * @return float
     */
    public function getCarburantAdded() {
        return $this->_CarburantAdded;
    }

    /**
     * _ActivatedChainTaskDetail::setCarburantAdded
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCarburantAdded($value) {
        $this->_CarburantAdded = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // CarburantTotal float property + getter/setter {{{

    /**
     * CarburantTotal float property
     *
     * @access private
     * @var float
     */
    private $_CarburantTotal = null;

    /**
     * _ActivatedChainTaskDetail::getCarburantTotal
     *
     * @access public
     * @return float
     */
    public function getCarburantTotal() {
        return $this->_CarburantTotal;
    }

    /**
     * _ActivatedChainTaskDetail::setCarburantTotal
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCarburantTotal($value) {
        $this->_CarburantTotal = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // CarburantUsed float property + getter/setter {{{

    /**
     * CarburantUsed float property
     *
     * @access private
     * @var float
     */
    private $_CarburantUsed = null;

    /**
     * _ActivatedChainTaskDetail::getCarburantUsed
     *
     * @access public
     * @return float
     */
    public function getCarburantUsed() {
        return $this->_CarburantUsed;
    }

    /**
     * _ActivatedChainTaskDetail::setCarburantUsed
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCarburantUsed($value) {
        $this->_CarburantUsed = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
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
     * _ActivatedChainTaskDetail::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * _ActivatedChainTaskDetail::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // InstructorSeat const property + getter/setter/getInstructorSeatConstArray {{{

    /**
     * InstructorSeat int property
     *
     * @access private
     * @var integer
     */
    private $_InstructorSeat = 0;

    /**
     * _ActivatedChainTaskDetail::getInstructorSeat
     *
     * @access public
     * @return integer
     */
    public function getInstructorSeat() {
        return $this->_InstructorSeat;
    }

    /**
     * _ActivatedChainTaskDetail::setInstructorSeat
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setInstructorSeat($value) {
        if ($value !== null) {
            $this->_InstructorSeat = (int)$value;
        }
    }

    /**
     * _ActivatedChainTaskDetail::getInstructorSeatConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getInstructorSeatConstArray($keys = false) {
        $array = array(
            _ActivatedChainTaskDetail::AUCUN => _("None"), 
            _ActivatedChainTaskDetail::PILOTE => _("Pilot (P)"), 
            _ActivatedChainTaskDetail::PILOTE_ELEVE => _("Pilot (EP)"), 
            _ActivatedChainTaskDetail::PILOTE_INSTRUCTEUR => _("Pilot (IP)"), 
            _ActivatedChainTaskDetail::COPILOTE => _("Co-pilot student (CP)")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // CustomerSeat const property + getter/setter/getCustomerSeatConstArray {{{

    /**
     * CustomerSeat int property
     *
     * @access private
     * @var integer
     */
    private $_CustomerSeat = 0;

    /**
     * _ActivatedChainTaskDetail::getCustomerSeat
     *
     * @access public
     * @return integer
     */
    public function getCustomerSeat() {
        return $this->_CustomerSeat;
    }

    /**
     * _ActivatedChainTaskDetail::setCustomerSeat
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCustomerSeat($value) {
        if ($value !== null) {
            $this->_CustomerSeat = (int)$value;
        }
    }

    /**
     * _ActivatedChainTaskDetail::getCustomerSeatConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getCustomerSeatConstArray($keys = false) {
        $array = array(
            _ActivatedChainTaskDetail::AUCUN => _("None"), 
            _ActivatedChainTaskDetail::PILOTE => _("Pilot (P)"), 
            _ActivatedChainTaskDetail::PILOTE_ELEVE => _("Pilot (EP)"), 
            _ActivatedChainTaskDetail::PILOTE_INSTRUCTEUR => _("Pilot (IP)"), 
            _ActivatedChainTaskDetail::COPILOTE => _("Co-pilot student (CP)")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // CycleEngine1N1 float property + getter/setter {{{

    /**
     * CycleEngine1N1 float property
     *
     * @access private
     * @var float
     */
    private $_CycleEngine1N1 = null;

    /**
     * _ActivatedChainTaskDetail::getCycleEngine1N1
     *
     * @access public
     * @return float
     */
    public function getCycleEngine1N1() {
        return $this->_CycleEngine1N1;
    }

    /**
     * _ActivatedChainTaskDetail::setCycleEngine1N1
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCycleEngine1N1($value) {
        $this->_CycleEngine1N1 = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // CycleEngine1N2 float property + getter/setter {{{

    /**
     * CycleEngine1N2 float property
     *
     * @access private
     * @var float
     */
    private $_CycleEngine1N2 = null;

    /**
     * _ActivatedChainTaskDetail::getCycleEngine1N2
     *
     * @access public
     * @return float
     */
    public function getCycleEngine1N2() {
        return $this->_CycleEngine1N2;
    }

    /**
     * _ActivatedChainTaskDetail::setCycleEngine1N2
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCycleEngine1N2($value) {
        $this->_CycleEngine1N2 = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // CycleEngine1 float property + getter/setter {{{

    /**
     * CycleEngine1 float property
     *
     * @access private
     * @var float
     */
    private $_CycleEngine1 = null;

    /**
     * _ActivatedChainTaskDetail::getCycleEngine1
     *
     * @access public
     * @return float
     */
    public function getCycleEngine1() {
        return $this->_CycleEngine1;
    }

    /**
     * _ActivatedChainTaskDetail::setCycleEngine1
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCycleEngine1($value) {
        $this->_CycleEngine1 = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // CycleEngine2N1 float property + getter/setter {{{

    /**
     * CycleEngine2N1 float property
     *
     * @access private
     * @var float
     */
    private $_CycleEngine2N1 = null;

    /**
     * _ActivatedChainTaskDetail::getCycleEngine2N1
     *
     * @access public
     * @return float
     */
    public function getCycleEngine2N1() {
        return $this->_CycleEngine2N1;
    }

    /**
     * _ActivatedChainTaskDetail::setCycleEngine2N1
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCycleEngine2N1($value) {
        $this->_CycleEngine2N1 = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // CycleEngine2N2 float property + getter/setter {{{

    /**
     * CycleEngine2N2 float property
     *
     * @access private
     * @var float
     */
    private $_CycleEngine2N2 = null;

    /**
     * _ActivatedChainTaskDetail::getCycleEngine2N2
     *
     * @access public
     * @return float
     */
    public function getCycleEngine2N2() {
        return $this->_CycleEngine2N2;
    }

    /**
     * _ActivatedChainTaskDetail::setCycleEngine2N2
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCycleEngine2N2($value) {
        $this->_CycleEngine2N2 = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // CycleEngine2 float property + getter/setter {{{

    /**
     * CycleEngine2 float property
     *
     * @access private
     * @var float
     */
    private $_CycleEngine2 = null;

    /**
     * _ActivatedChainTaskDetail::getCycleEngine2
     *
     * @access public
     * @return float
     */
    public function getCycleEngine2() {
        return $this->_CycleEngine2;
    }

    /**
     * _ActivatedChainTaskDetail::setCycleEngine2
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCycleEngine2($value) {
        $this->_CycleEngine2 = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // CycleCellule int property + getter/setter {{{

    /**
     * CycleCellule int property
     *
     * @access private
     * @var integer
     */
    private $_CycleCellule = null;

    /**
     * _ActivatedChainTaskDetail::getCycleCellule
     *
     * @access public
     * @return integer
     */
    public function getCycleCellule() {
        return $this->_CycleCellule;
    }

    /**
     * _ActivatedChainTaskDetail::setCycleCellule
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCycleCellule($value) {
        $this->_CycleCellule = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // CycleTreuillage int property + getter/setter {{{

    /**
     * CycleTreuillage int property
     *
     * @access private
     * @var integer
     */
    private $_CycleTreuillage = null;

    /**
     * _ActivatedChainTaskDetail::getCycleTreuillage
     *
     * @access public
     * @return integer
     */
    public function getCycleTreuillage() {
        return $this->_CycleTreuillage;
    }

    /**
     * _ActivatedChainTaskDetail::setCycleTreuillage
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCycleTreuillage($value) {
        $this->_CycleTreuillage = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // CycleCharge int property + getter/setter {{{

    /**
     * CycleCharge int property
     *
     * @access private
     * @var integer
     */
    private $_CycleCharge = null;

    /**
     * _ActivatedChainTaskDetail::getCycleCharge
     *
     * @access public
     * @return integer
     */
    public function getCycleCharge() {
        return $this->_CycleCharge;
    }

    /**
     * _ActivatedChainTaskDetail::setCycleCharge
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCycleCharge($value) {
        $this->_CycleCharge = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // RealCommercialDuration float property + getter/setter {{{

    /**
     * RealCommercialDuration float property
     *
     * @access private
     * @var float
     */
    private $_RealCommercialDuration = null;

    /**
     * _ActivatedChainTaskDetail::getRealCommercialDuration
     *
     * @access public
     * @return float
     */
    public function getRealCommercialDuration() {
        return $this->_RealCommercialDuration;
    }

    /**
     * _ActivatedChainTaskDetail::setRealCommercialDuration
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealCommercialDuration($value) {
        $this->_RealCommercialDuration = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // EngineOn datetime property + getter/setter {{{

    /**
     * EngineOn int property
     *
     * @access private
     * @var string
     */
    private $_EngineOn = 0;

    /**
     * _ActivatedChainTaskDetail::getEngineOn
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEngineOn($format = false) {
        return $this->dateFormat($this->_EngineOn, $format);
    }

    /**
     * _ActivatedChainTaskDetail::setEngineOn
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEngineOn($value) {
        $this->_EngineOn = $value;
    }

    // }}}
    // EngineOff datetime property + getter/setter {{{

    /**
     * EngineOff int property
     *
     * @access private
     * @var string
     */
    private $_EngineOff = 0;

    /**
     * _ActivatedChainTaskDetail::getEngineOff
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEngineOff($format = false) {
        return $this->dateFormat($this->_EngineOff, $format);
    }

    /**
     * _ActivatedChainTaskDetail::setEngineOff
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEngineOff($value) {
        $this->_EngineOff = $value;
    }

    // }}}
    // TakeOff datetime property + getter/setter {{{

    /**
     * TakeOff int property
     *
     * @access private
     * @var string
     */
    private $_TakeOff = 0;

    /**
     * _ActivatedChainTaskDetail::getTakeOff
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getTakeOff($format = false) {
        return $this->dateFormat($this->_TakeOff, $format);
    }

    /**
     * _ActivatedChainTaskDetail::setTakeOff
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setTakeOff($value) {
        $this->_TakeOff = $value;
    }

    // }}}
    // Landing datetime property + getter/setter {{{

    /**
     * Landing int property
     *
     * @access private
     * @var string
     */
    private $_Landing = 0;

    /**
     * _ActivatedChainTaskDetail::getLanding
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getLanding($format = false) {
        return $this->dateFormat($this->_Landing, $format);
    }

    /**
     * _ActivatedChainTaskDetail::setLanding
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setLanding($value) {
        $this->_Landing = $value;
    }

    // }}}
    // TechnicalHour float property + getter/setter {{{

    /**
     * TechnicalHour float property
     *
     * @access private
     * @var float
     */
    private $_TechnicalHour = null;

    /**
     * _ActivatedChainTaskDetail::getTechnicalHour
     *
     * @access public
     * @return float
     */
    public function getTechnicalHour() {
        return $this->_TechnicalHour;
    }

    /**
     * _ActivatedChainTaskDetail::setTechnicalHour
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTechnicalHour($value) {
        $this->_TechnicalHour = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // CelluleHour float property + getter/setter {{{

    /**
     * CelluleHour float property
     *
     * @access private
     * @var float
     */
    private $_CelluleHour = null;

    /**
     * _ActivatedChainTaskDetail::getCelluleHour
     *
     * @access public
     * @return float
     */
    public function getCelluleHour() {
        return $this->_CelluleHour;
    }

    /**
     * _ActivatedChainTaskDetail::setCelluleHour
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCelluleHour($value) {
        $this->_CelluleHour = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // Nature const property + getter/setter/getNatureConstArray {{{

    /**
     * Nature int property
     *
     * @access private
     * @var integer
     */
    private $_Nature = 0;

    /**
     * _ActivatedChainTaskDetail::getNature
     *
     * @access public
     * @return integer
     */
    public function getNature() {
        return $this->_Nature;
    }

    /**
     * _ActivatedChainTaskDetail::setNature
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setNature($value) {
        if ($value !== null) {
            $this->_Nature = (int)$value;
        }
    }

    /**
     * _ActivatedChainTaskDetail::getNatureConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getNatureConstArray($keys = false) {
        $array = array(
            _ActivatedChainTaskDetail::NATURE_NONE => _("N/A"), 
            _ActivatedChainTaskDetail::NATURE_INST => _("Instruction"), 
            _ActivatedChainTaskDetail::NATURE_TP => _("Public transport"), 
            _ActivatedChainTaskDetail::NATURE_TA => _("Air labour"), 
            _ActivatedChainTaskDetail::NATURE_SIM => _("Simulation")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // IFRLanding string property + getter/setter {{{

    /**
     * IFRLanding int property
     *
     * @access private
     * @var integer
     */
    private $_IFRLanding = 0;

    /**
     * _ActivatedChainTaskDetail::getIFRLanding
     *
     * @access public
     * @return integer
     */
    public function getIFRLanding() {
        return $this->_IFRLanding;
    }

    /**
     * _ActivatedChainTaskDetail::setIFRLanding
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setIFRLanding($value) {
        if ($value !== null) {
            $this->_IFRLanding = (int)$value;
        }
    }

    // }}}
    // PilotHours string property + getter/setter {{{

    /**
     * PilotHours int property
     *
     * @access private
     * @var integer
     */
    private $_PilotHours = 0;

    /**
     * _ActivatedChainTaskDetail::getPilotHours
     *
     * @access public
     * @return integer
     */
    public function getPilotHours() {
        return $this->_PilotHours;
    }

    /**
     * _ActivatedChainTaskDetail::setPilotHours
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPilotHours($value) {
        if ($value !== null) {
            $this->_PilotHours = (int)$value;
        }
    }

    // }}}
    // PilotHoursBiEngine string property + getter/setter {{{

    /**
     * PilotHoursBiEngine int property
     *
     * @access private
     * @var integer
     */
    private $_PilotHoursBiEngine = 0;

    /**
     * _ActivatedChainTaskDetail::getPilotHoursBiEngine
     *
     * @access public
     * @return integer
     */
    public function getPilotHoursBiEngine() {
        return $this->_PilotHoursBiEngine;
    }

    /**
     * _ActivatedChainTaskDetail::setPilotHoursBiEngine
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPilotHoursBiEngine($value) {
        if ($value !== null) {
            $this->_PilotHoursBiEngine = (int)$value;
        }
    }

    // }}}
    // CoPilotHours string property + getter/setter {{{

    /**
     * CoPilotHours int property
     *
     * @access private
     * @var integer
     */
    private $_CoPilotHours = 0;

    /**
     * _ActivatedChainTaskDetail::getCoPilotHours
     *
     * @access public
     * @return integer
     */
    public function getCoPilotHours() {
        return $this->_CoPilotHours;
    }

    /**
     * _ActivatedChainTaskDetail::setCoPilotHours
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoPilotHours($value) {
        if ($value !== null) {
            $this->_CoPilotHours = (int)$value;
        }
    }

    // }}}
    // CoPilotHoursBiEngine string property + getter/setter {{{

    /**
     * CoPilotHoursBiEngine int property
     *
     * @access private
     * @var integer
     */
    private $_CoPilotHoursBiEngine = 0;

    /**
     * _ActivatedChainTaskDetail::getCoPilotHoursBiEngine
     *
     * @access public
     * @return integer
     */
    public function getCoPilotHoursBiEngine() {
        return $this->_CoPilotHoursBiEngine;
    }

    /**
     * _ActivatedChainTaskDetail::setCoPilotHoursBiEngine
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoPilotHoursBiEngine($value) {
        if ($value !== null) {
            $this->_CoPilotHoursBiEngine = (int)$value;
        }
    }

    // }}}
    // PilotHoursNight string property + getter/setter {{{

    /**
     * PilotHoursNight int property
     *
     * @access private
     * @var integer
     */
    private $_PilotHoursNight = 0;

    /**
     * _ActivatedChainTaskDetail::getPilotHoursNight
     *
     * @access public
     * @return integer
     */
    public function getPilotHoursNight() {
        return $this->_PilotHoursNight;
    }

    /**
     * _ActivatedChainTaskDetail::setPilotHoursNight
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPilotHoursNight($value) {
        if ($value !== null) {
            $this->_PilotHoursNight = (int)$value;
        }
    }

    // }}}
    // PilotHoursBiEngineNight string property + getter/setter {{{

    /**
     * PilotHoursBiEngineNight int property
     *
     * @access private
     * @var integer
     */
    private $_PilotHoursBiEngineNight = 0;

    /**
     * _ActivatedChainTaskDetail::getPilotHoursBiEngineNight
     *
     * @access public
     * @return integer
     */
    public function getPilotHoursBiEngineNight() {
        return $this->_PilotHoursBiEngineNight;
    }

    /**
     * _ActivatedChainTaskDetail::setPilotHoursBiEngineNight
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPilotHoursBiEngineNight($value) {
        if ($value !== null) {
            $this->_PilotHoursBiEngineNight = (int)$value;
        }
    }

    // }}}
    // CoPilotHoursNight string property + getter/setter {{{

    /**
     * CoPilotHoursNight int property
     *
     * @access private
     * @var integer
     */
    private $_CoPilotHoursNight = 0;

    /**
     * _ActivatedChainTaskDetail::getCoPilotHoursNight
     *
     * @access public
     * @return integer
     */
    public function getCoPilotHoursNight() {
        return $this->_CoPilotHoursNight;
    }

    /**
     * _ActivatedChainTaskDetail::setCoPilotHoursNight
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoPilotHoursNight($value) {
        if ($value !== null) {
            $this->_CoPilotHoursNight = (int)$value;
        }
    }

    // }}}
    // CoPilotHoursBiEngineNight string property + getter/setter {{{

    /**
     * CoPilotHoursBiEngineNight int property
     *
     * @access private
     * @var integer
     */
    private $_CoPilotHoursBiEngineNight = 0;

    /**
     * _ActivatedChainTaskDetail::getCoPilotHoursBiEngineNight
     *
     * @access public
     * @return integer
     */
    public function getCoPilotHoursBiEngineNight() {
        return $this->_CoPilotHoursBiEngineNight;
    }

    /**
     * _ActivatedChainTaskDetail::setCoPilotHoursBiEngineNight
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoPilotHoursBiEngineNight($value) {
        if ($value !== null) {
            $this->_CoPilotHoursBiEngineNight = (int)$value;
        }
    }

    // }}}
    // PilotHoursIFR string property + getter/setter {{{

    /**
     * PilotHoursIFR int property
     *
     * @access private
     * @var integer
     */
    private $_PilotHoursIFR = 0;

    /**
     * _ActivatedChainTaskDetail::getPilotHoursIFR
     *
     * @access public
     * @return integer
     */
    public function getPilotHoursIFR() {
        return $this->_PilotHoursIFR;
    }

    /**
     * _ActivatedChainTaskDetail::setPilotHoursIFR
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPilotHoursIFR($value) {
        if ($value !== null) {
            $this->_PilotHoursIFR = (int)$value;
        }
    }

    // }}}
    // CoPilotHoursIFR string property + getter/setter {{{

    /**
     * CoPilotHoursIFR int property
     *
     * @access private
     * @var integer
     */
    private $_CoPilotHoursIFR = 0;

    /**
     * _ActivatedChainTaskDetail::getCoPilotHoursIFR
     *
     * @access public
     * @return integer
     */
    public function getCoPilotHoursIFR() {
        return $this->_CoPilotHoursIFR;
    }

    /**
     * _ActivatedChainTaskDetail::setCoPilotHoursIFR
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoPilotHoursIFR($value) {
        if ($value !== null) {
            $this->_CoPilotHoursIFR = (int)$value;
        }
    }

    // }}}
    // PublicHours string property + getter/setter {{{

    /**
     * PublicHours int property
     *
     * @access private
     * @var integer
     */
    private $_PublicHours = 0;

    /**
     * _ActivatedChainTaskDetail::getPublicHours
     *
     * @access public
     * @return integer
     */
    public function getPublicHours() {
        return $this->_PublicHours;
    }

    /**
     * _ActivatedChainTaskDetail::setPublicHours
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPublicHours($value) {
        if ($value !== null) {
            $this->_PublicHours = (int)$value;
        }
    }

    // }}}
    // VLAEHours string property + getter/setter {{{

    /**
     * VLAEHours int property
     *
     * @access private
     * @var integer
     */
    private $_VLAEHours = 0;

    /**
     * _ActivatedChainTaskDetail::getVLAEHours
     *
     * @access public
     * @return integer
     */
    public function getVLAEHours() {
        return $this->_VLAEHours;
    }

    /**
     * _ActivatedChainTaskDetail::setVLAEHours
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setVLAEHours($value) {
        if ($value !== null) {
            $this->_VLAEHours = (int)$value;
        }
    }

    // }}}
    // TakeOffNumber string property + getter/setter {{{

    /**
     * TakeOffNumber int property
     *
     * @access private
     * @var integer
     */
    private $_TakeOffNumber = 0;

    /**
     * _ActivatedChainTaskDetail::getTakeOffNumber
     *
     * @access public
     * @return integer
     */
    public function getTakeOffNumber() {
        return $this->_TakeOffNumber;
    }

    /**
     * _ActivatedChainTaskDetail::setTakeOffNumber
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTakeOffNumber($value) {
        if ($value !== null) {
            $this->_TakeOffNumber = (int)$value;
        }
    }

    // }}}
    // LandingNumber string property + getter/setter {{{

    /**
     * LandingNumber int property
     *
     * @access private
     * @var integer
     */
    private $_LandingNumber = 0;

    /**
     * _ActivatedChainTaskDetail::getLandingNumber
     *
     * @access public
     * @return integer
     */
    public function getLandingNumber() {
        return $this->_LandingNumber;
    }

    /**
     * _ActivatedChainTaskDetail::setLandingNumber
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setLandingNumber($value) {
        if ($value !== null) {
            $this->_LandingNumber = (int)$value;
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
        return 'ActivatedChainTaskDetail';
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
            'OilAdded' => Object::TYPE_DECIMAL,
            'CarburantRest' => Object::TYPE_DECIMAL,
            'CarburantAdded' => Object::TYPE_DECIMAL,
            'CarburantTotal' => Object::TYPE_DECIMAL,
            'CarburantUsed' => Object::TYPE_DECIMAL,
            'Comment' => Object::TYPE_TEXT,
            'InstructorSeat' => Object::TYPE_CONST,
            'CustomerSeat' => Object::TYPE_CONST,
            'CycleEngine1N1' => Object::TYPE_DECIMAL,
            'CycleEngine1N2' => Object::TYPE_DECIMAL,
            'CycleEngine1' => Object::TYPE_DECIMAL,
            'CycleEngine2N1' => Object::TYPE_DECIMAL,
            'CycleEngine2N2' => Object::TYPE_DECIMAL,
            'CycleEngine2' => Object::TYPE_DECIMAL,
            'CycleCellule' => Object::TYPE_INT,
            'CycleTreuillage' => Object::TYPE_INT,
            'CycleCharge' => Object::TYPE_INT,
            'RealCommercialDuration' => Object::TYPE_DECIMAL,
            'EngineOn' => Object::TYPE_DATETIME,
            'EngineOff' => Object::TYPE_DATETIME,
            'TakeOff' => Object::TYPE_DATETIME,
            'Landing' => Object::TYPE_DATETIME,
            'TechnicalHour' => Object::TYPE_DECIMAL,
            'CelluleHour' => Object::TYPE_DECIMAL,
            'Nature' => Object::TYPE_CONST,
            'IFRLanding' => Object::TYPE_INT,
            'PilotHours' => Object::TYPE_INT,
            'PilotHoursBiEngine' => Object::TYPE_INT,
            'CoPilotHours' => Object::TYPE_INT,
            'CoPilotHoursBiEngine' => Object::TYPE_INT,
            'PilotHoursNight' => Object::TYPE_INT,
            'PilotHoursBiEngineNight' => Object::TYPE_INT,
            'CoPilotHoursNight' => Object::TYPE_INT,
            'CoPilotHoursBiEngineNight' => Object::TYPE_INT,
            'PilotHoursIFR' => Object::TYPE_INT,
            'CoPilotHoursIFR' => Object::TYPE_INT,
            'PublicHours' => Object::TYPE_INT,
            'VLAEHours' => Object::TYPE_INT,
            'TakeOffNumber' => Object::TYPE_INT,
            'LandingNumber' => Object::TYPE_INT);
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
            'ActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'ActivatedChainTaskDetail',
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