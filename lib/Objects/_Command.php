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
 * _Command class
 *
 */
class _Command extends Object {
    // class constants {{{

    const TYPE_CUSTOMER = 1;
    const TYPE_SUPPLIER = 2;
    const TYPE_TRANSPORT = 3;
    const TYPE_PRESTATION = 4;
    const TYPE_COURSE = 5;
    const ACTIVEE = 0;
    const PREP_PARTIELLE = 1;
    const PREP_COMPLETE = 2;
    const LIV_PARTIELLE = 3;
    const LIV_COMPLETE = 4;
    const FACT_PARTIELLE = 5;
    const FACT_COMPLETE = 6;
    const REGLEMT_PARTIEL = 7;
    const REGLEMT_TOTAL = 8;
    const BLOCAGE_CDE = 9;

    // }}}
    // Constructeur {{{

    /**
     * _Command::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // CommandNo string property + getter/setter {{{

    /**
     * CommandNo string property
     *
     * @access private
     * @var string
     */
    private $_CommandNo = '';

    /**
     * _Command::getCommandNo
     *
     * @access public
     * @return string
     */
    public function getCommandNo() {
        return $this->_CommandNo;
    }

    /**
     * _Command::setCommandNo
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCommandNo($value) {
        $this->_CommandNo = $value;
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
     * _Command::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * _Command::setType
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
     * _Command::getTypeConstArray
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
            _Command::TYPE_CUSTOMER => _("Customer order"), 
            _Command::TYPE_SUPPLIER => _("Supplier order"), 
            _Command::TYPE_TRANSPORT => _("Carriage service"), 
            _Command::TYPE_PRESTATION => _("Service order"), 
            _Command::TYPE_COURSE => _("Class booking")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // CommandDate datetime property + getter/setter {{{

    /**
     * CommandDate int property
     *
     * @access private
     * @var string
     */
    private $_CommandDate = 0;

    /**
     * _Command::getCommandDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getCommandDate($format = false) {
        return $this->dateFormat($this->_CommandDate, $format);
    }

    /**
     * _Command::setCommandDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCommandDate($value) {
        $this->_CommandDate = $value;
    }

    // }}}
    // MerchandiseValue float property + getter/setter {{{

    /**
     * MerchandiseValue float property
     *
     * @access private
     * @var float
     */
    private $_MerchandiseValue = 0;

    /**
     * _Command::getMerchandiseValue
     *
     * @access public
     * @return float
     */
    public function getMerchandiseValue() {
        return $this->_MerchandiseValue;
    }

    /**
     * _Command::setMerchandiseValue
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMerchandiseValue($value) {
        if ($value !== null) {
            $this->_MerchandiseValue = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // AdditionnalGaranties int property + getter/setter {{{

    /**
     * AdditionnalGaranties int property
     *
     * @access private
     * @var integer
     */
    private $_AdditionnalGaranties = null;

    /**
     * _Command::getAdditionnalGaranties
     *
     * @access public
     * @return integer
     */
    public function getAdditionnalGaranties() {
        return $this->_AdditionnalGaranties;
    }

    /**
     * _Command::setAdditionnalGaranties
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setAdditionnalGaranties($value) {
        $this->_AdditionnalGaranties = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // Incoterm foreignkey property + getter/setter {{{

    /**
     * Incoterm foreignkey
     *
     * @access private
     * @var mixed object Incoterm or integer
     */
    private $_Incoterm = false;

    /**
     * _Command::getIncoterm
     *
     * @access public
     * @return object Incoterm
     */
    public function getIncoterm() {
        if (is_int($this->_Incoterm) && $this->_Incoterm > 0) {
            $mapper = Mapper::singleton('Incoterm');
            $this->_Incoterm = $mapper->load(
                array('Id'=>$this->_Incoterm));
        }
        return $this->_Incoterm;
    }

    /**
     * _Command::getIncotermId
     *
     * @access public
     * @return integer
     */
    public function getIncotermId() {
        if ($this->_Incoterm instanceof Incoterm) {
            return $this->_Incoterm->getId();
        }
        return (int)$this->_Incoterm;
    }

    /**
     * _Command::setIncoterm
     *
     * @access public
     * @param object Incoterm $value
     * @return void
     */
    public function setIncoterm($value) {
        if (is_numeric($value)) {
            $this->_Incoterm = (int)$value;
        } else {
            $this->_Incoterm = $value;
        }
    }

    // }}}
    // WishedStartDate datetime property + getter/setter {{{

    /**
     * WishedStartDate int property
     *
     * @access private
     * @var string
     */
    private $_WishedStartDate = 0;

    /**
     * _Command::getWishedStartDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getWishedStartDate($format = false) {
        return $this->dateFormat($this->_WishedStartDate, $format);
    }

    /**
     * _Command::setWishedStartDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setWishedStartDate($value) {
        $this->_WishedStartDate = $value;
    }

    // }}}
    // WishedEndDate datetime property + getter/setter {{{

    /**
     * WishedEndDate int property
     *
     * @access private
     * @var string
     */
    private $_WishedEndDate = 0;

    /**
     * _Command::getWishedEndDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getWishedEndDate($format = false) {
        return $this->dateFormat($this->_WishedEndDate, $format);
    }

    /**
     * _Command::setWishedEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setWishedEndDate($value) {
        $this->_WishedEndDate = $value;
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
     * _Command::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * _Command::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // Expeditor foreignkey property + getter/setter {{{

    /**
     * Expeditor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Expeditor = false;

    /**
     * _Command::getExpeditor
     *
     * @access public
     * @return object Actor
     */
    public function getExpeditor() {
        if (is_int($this->_Expeditor) && $this->_Expeditor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Expeditor = $mapper->load(
                array('Id'=>$this->_Expeditor));
        }
        return $this->_Expeditor;
    }

    /**
     * _Command::getExpeditorId
     *
     * @access public
     * @return integer
     */
    public function getExpeditorId() {
        if ($this->_Expeditor instanceof Actor) {
            return $this->_Expeditor->getId();
        }
        return (int)$this->_Expeditor;
    }

    /**
     * _Command::setExpeditor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setExpeditor($value) {
        if (is_numeric($value)) {
            $this->_Expeditor = (int)$value;
        } else {
            $this->_Expeditor = $value;
        }
    }

    // }}}
    // Destinator foreignkey property + getter/setter {{{

    /**
     * Destinator foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Destinator = false;

    /**
     * _Command::getDestinator
     *
     * @access public
     * @return object Actor
     */
    public function getDestinator() {
        if (is_int($this->_Destinator) && $this->_Destinator > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Destinator = $mapper->load(
                array('Id'=>$this->_Destinator));
        }
        return $this->_Destinator;
    }

    /**
     * _Command::getDestinatorId
     *
     * @access public
     * @return integer
     */
    public function getDestinatorId() {
        if ($this->_Destinator instanceof Actor) {
            return $this->_Destinator->getId();
        }
        return (int)$this->_Destinator;
    }

    /**
     * _Command::setDestinator
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setDestinator($value) {
        if (is_numeric($value)) {
            $this->_Destinator = (int)$value;
        } else {
            $this->_Destinator = $value;
        }
    }

    // }}}
    // ExpeditorSite foreignkey property + getter/setter {{{

    /**
     * ExpeditorSite foreignkey
     *
     * @access private
     * @var mixed object Site or integer
     */
    private $_ExpeditorSite = false;

    /**
     * _Command::getExpeditorSite
     *
     * @access public
     * @return object Site
     */
    public function getExpeditorSite() {
        if (is_int($this->_ExpeditorSite) && $this->_ExpeditorSite > 0) {
            $mapper = Mapper::singleton('Site');
            $this->_ExpeditorSite = $mapper->load(
                array('Id'=>$this->_ExpeditorSite));
        }
        return $this->_ExpeditorSite;
    }

    /**
     * _Command::getExpeditorSiteId
     *
     * @access public
     * @return integer
     */
    public function getExpeditorSiteId() {
        if ($this->_ExpeditorSite instanceof Site) {
            return $this->_ExpeditorSite->getId();
        }
        return (int)$this->_ExpeditorSite;
    }

    /**
     * _Command::setExpeditorSite
     *
     * @access public
     * @param object Site $value
     * @return void
     */
    public function setExpeditorSite($value) {
        if (is_numeric($value)) {
            $this->_ExpeditorSite = (int)$value;
        } else {
            $this->_ExpeditorSite = $value;
        }
    }

    // }}}
    // DestinatorSite foreignkey property + getter/setter {{{

    /**
     * DestinatorSite foreignkey
     *
     * @access private
     * @var mixed object Site or integer
     */
    private $_DestinatorSite = false;

    /**
     * _Command::getDestinatorSite
     *
     * @access public
     * @return object Site
     */
    public function getDestinatorSite() {
        if (is_int($this->_DestinatorSite) && $this->_DestinatorSite > 0) {
            $mapper = Mapper::singleton('Site');
            $this->_DestinatorSite = $mapper->load(
                array('Id'=>$this->_DestinatorSite));
        }
        return $this->_DestinatorSite;
    }

    /**
     * _Command::getDestinatorSiteId
     *
     * @access public
     * @return integer
     */
    public function getDestinatorSiteId() {
        if ($this->_DestinatorSite instanceof Site) {
            return $this->_DestinatorSite->getId();
        }
        return (int)$this->_DestinatorSite;
    }

    /**
     * _Command::setDestinatorSite
     *
     * @access public
     * @param object Site $value
     * @return void
     */
    public function setDestinatorSite($value) {
        if (is_numeric($value)) {
            $this->_DestinatorSite = (int)$value;
        } else {
            $this->_DestinatorSite = $value;
        }
    }

    // }}}
    // Customer foreignkey property + getter/setter {{{

    /**
     * Customer foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Customer = false;

    /**
     * _Command::getCustomer
     *
     * @access public
     * @return object Actor
     */
    public function getCustomer() {
        if (is_int($this->_Customer) && $this->_Customer > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Customer = $mapper->load(
                array('Id'=>$this->_Customer));
        }
        return $this->_Customer;
    }

    /**
     * _Command::getCustomerId
     *
     * @access public
     * @return integer
     */
    public function getCustomerId() {
        if ($this->_Customer instanceof Actor) {
            return $this->_Customer->getId();
        }
        return (int)$this->_Customer;
    }

    /**
     * _Command::setCustomer
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setCustomer($value) {
        if (is_numeric($value)) {
            $this->_Customer = (int)$value;
        } else {
            $this->_Customer = $value;
        }
    }

    // }}}
    // SupplierCustomer foreignkey property + getter/setter {{{

    /**
     * SupplierCustomer foreignkey
     *
     * @access private
     * @var mixed object SupplierCustomer or integer
     */
    private $_SupplierCustomer = false;

    /**
     * _Command::getSupplierCustomer
     *
     * @access public
     * @return object SupplierCustomer
     */
    public function getSupplierCustomer() {
        if (is_int($this->_SupplierCustomer) && $this->_SupplierCustomer > 0) {
            $mapper = Mapper::singleton('SupplierCustomer');
            $this->_SupplierCustomer = $mapper->load(
                array('Id'=>$this->_SupplierCustomer));
        }
        return $this->_SupplierCustomer;
    }

    /**
     * _Command::getSupplierCustomerId
     *
     * @access public
     * @return integer
     */
    public function getSupplierCustomerId() {
        if ($this->_SupplierCustomer instanceof SupplierCustomer) {
            return $this->_SupplierCustomer->getId();
        }
        return (int)$this->_SupplierCustomer;
    }

    /**
     * _Command::setSupplierCustomer
     *
     * @access public
     * @param object SupplierCustomer $value
     * @return void
     */
    public function setSupplierCustomer($value) {
        if (is_numeric($value)) {
            $this->_SupplierCustomer = (int)$value;
        } else {
            $this->_SupplierCustomer = $value;
        }
    }

    // }}}
    // Commercial foreignkey property + getter/setter {{{

    /**
     * Commercial foreignkey
     *
     * @access private
     * @var mixed object UserAccount or integer
     */
    private $_Commercial = false;

    /**
     * _Command::getCommercial
     *
     * @access public
     * @return object UserAccount
     */
    public function getCommercial() {
        if (is_int($this->_Commercial) && $this->_Commercial > 0) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_Commercial = $mapper->load(
                array('Id'=>$this->_Commercial));
        }
        return $this->_Commercial;
    }

    /**
     * _Command::getCommercialId
     *
     * @access public
     * @return integer
     */
    public function getCommercialId() {
        if ($this->_Commercial instanceof UserAccount) {
            return $this->_Commercial->getId();
        }
        return (int)$this->_Commercial;
    }

    /**
     * _Command::setCommercial
     *
     * @access public
     * @param object UserAccount $value
     * @return void
     */
    public function setCommercial($value) {
        if (is_numeric($value)) {
            $this->_Commercial = (int)$value;
        } else {
            $this->_Commercial = $value;
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
     * _Command::getState
     *
     * @access public
     * @return integer
     */
    public function getState() {
        return $this->_State;
    }

    /**
     * _Command::setState
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
     * _Command::getStateConstArray
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
            _Command::ACTIVEE => _("Activated"), 
            _Command::PREP_PARTIELLE => _("Partially prepared"), 
            _Command::PREP_COMPLETE => _("Completely prepared"), 
            _Command::LIV_PARTIELLE => _("Partially delivered"), 
            _Command::LIV_COMPLETE => _("Completely delivered"), 
            _Command::FACT_PARTIELLE => _("Partially charged"), 
            _Command::FACT_COMPLETE => _("Completely charged"), 
            _Command::REGLEMT_PARTIEL => _("Partially paid"), 
            _Command::REGLEMT_TOTAL => _("Completely paid"), 
            _Command::BLOCAGE_CDE => _("Locked")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Handing float property + getter/setter {{{

    /**
     * Handing float property
     *
     * @access private
     * @var float
     */
    private $_Handing = 0;

    /**
     * _Command::getHanding
     *
     * @access public
     * @return float
     */
    public function getHanding() {
        return $this->_Handing;
    }

    /**
     * _Command::setHanding
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setHanding($value) {
        if ($value !== null) {
            $this->_Handing = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // HandingByRangePercent float property + getter/setter {{{

    /**
     * HandingByRangePercent float property
     *
     * @access private
     * @var float
     */
    private $_HandingByRangePercent = 0;

    /**
     * _Command::getHandingByRangePercent
     *
     * @access public
     * @return float
     */
    public function getHandingByRangePercent() {
        return $this->_HandingByRangePercent;
    }

    /**
     * _Command::setHandingByRangePercent
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setHandingByRangePercent($value) {
        if ($value !== null) {
            $this->_HandingByRangePercent = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Port float property + getter/setter {{{

    /**
     * Port float property
     *
     * @access private
     * @var float
     */
    private $_Port = 0;

    /**
     * _Command::getPort
     *
     * @access public
     * @return float
     */
    public function getPort() {
        return $this->_Port;
    }

    /**
     * _Command::setPort
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPort($value) {
        if ($value !== null) {
            $this->_Port = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Packing float property + getter/setter {{{

    /**
     * Packing float property
     *
     * @access private
     * @var float
     */
    private $_Packing = 0;

    /**
     * _Command::getPacking
     *
     * @access public
     * @return float
     */
    public function getPacking() {
        return $this->_Packing;
    }

    /**
     * _Command::setPacking
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPacking($value) {
        if ($value !== null) {
            $this->_Packing = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Insurance float property + getter/setter {{{

    /**
     * Insurance float property
     *
     * @access private
     * @var float
     */
    private $_Insurance = 0;

    /**
     * _Command::getInsurance
     *
     * @access public
     * @return float
     */
    public function getInsurance() {
        return $this->_Insurance;
    }

    /**
     * _Command::setInsurance
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setInsurance($value) {
        if ($value !== null) {
            $this->_Insurance = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // TotalPriceHT float property + getter/setter {{{

    /**
     * TotalPriceHT float property
     *
     * @access private
     * @var float
     */
    private $_TotalPriceHT = 0;

    /**
     * _Command::getTotalPriceHT
     *
     * @access public
     * @return float
     */
    public function getTotalPriceHT() {
        return $this->_TotalPriceHT;
    }

    /**
     * _Command::setTotalPriceHT
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTotalPriceHT($value) {
        if ($value !== null) {
            $this->_TotalPriceHT = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // TotalPriceTTC float property + getter/setter {{{

    /**
     * TotalPriceTTC float property
     *
     * @access private
     * @var float
     */
    private $_TotalPriceTTC = 0;

    /**
     * _Command::getTotalPriceTTC
     *
     * @access public
     * @return float
     */
    public function getTotalPriceTTC() {
        return $this->_TotalPriceTTC;
    }

    /**
     * _Command::setTotalPriceTTC
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTotalPriceTTC($value) {
        if ($value !== null) {
            $this->_TotalPriceTTC = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Processed string property + getter/setter {{{

    /**
     * Processed int property
     *
     * @access private
     * @var integer
     */
    private $_Processed = 0;

    /**
     * _Command::getProcessed
     *
     * @access public
     * @return integer
     */
    public function getProcessed() {
        return $this->_Processed;
    }

    /**
     * _Command::setProcessed
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setProcessed($value) {
        if ($value !== null) {
            $this->_Processed = (int)$value;
        }
    }

    // }}}
    // Installment float property + getter/setter {{{

    /**
     * Installment float property
     *
     * @access private
     * @var float
     */
    private $_Installment = 0;

    /**
     * _Command::getInstallment
     *
     * @access public
     * @return float
     */
    public function getInstallment() {
        return $this->_Installment;
    }

    /**
     * _Command::setInstallment
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setInstallment($value) {
        if ($value !== null) {
            $this->_Installment = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // CustomerRemExcep float property + getter/setter {{{

    /**
     * CustomerRemExcep float property
     *
     * @access private
     * @var float
     */
    private $_CustomerRemExcep = 0;

    /**
     * _Command::getCustomerRemExcep
     *
     * @access public
     * @return float
     */
    public function getCustomerRemExcep() {
        return $this->_CustomerRemExcep;
    }

    /**
     * _Command::setCustomerRemExcep
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCustomerRemExcep($value) {
        if ($value !== null) {
            $this->_CustomerRemExcep = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Duration datetime property + getter/setter {{{

    /**
     * Duration int property
     *
     * @access private
     * @var string
     */
    private $_Duration = 0;

    /**
     * _Command::getDuration
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getDuration($format = false) {
        return $this->dateFormat($this->_Duration, $format);
    }

    /**
     * _Command::setDuration
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDuration($value) {
        $this->_Duration = $value;
    }

    // }}}
    // Cadenced string property + getter/setter {{{

    /**
     * Cadenced int property
     *
     * @access private
     * @var integer
     */
    private $_Cadenced = false;

    /**
     * _Command::getCadenced
     *
     * @access public
     * @return integer
     */
    public function getCadenced() {
        return $this->_Cadenced;
    }

    /**
     * _Command::setCadenced
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCadenced($value) {
        if ($value !== null) {
            $this->_Cadenced = (int)$value;
        }
    }

    // }}}
    // Closed string property + getter/setter {{{

    /**
     * Closed int property
     *
     * @access private
     * @var integer
     */
    private $_Closed = false;

    /**
     * _Command::getClosed
     *
     * @access public
     * @return integer
     */
    public function getClosed() {
        return $this->_Closed;
    }

    /**
     * _Command::setClosed
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setClosed($value) {
        if ($value !== null) {
            $this->_Closed = (int)$value;
        }
    }

    // }}}
    // IsEstimate string property + getter/setter {{{

    /**
     * IsEstimate int property
     *
     * @access private
     * @var integer
     */
    private $_IsEstimate = false;

    /**
     * _Command::getIsEstimate
     *
     * @access public
     * @return integer
     */
    public function getIsEstimate() {
        return $this->_IsEstimate;
    }

    /**
     * _Command::setIsEstimate
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setIsEstimate($value) {
        if ($value !== null) {
            $this->_IsEstimate = (int)$value;
        }
    }

    // }}}
    // CommandExpeditionDetail foreignkey property + getter/setter {{{

    /**
     * CommandExpeditionDetail foreignkey
     *
     * @access private
     * @var mixed object CommandExpeditionDetail or integer
     */
    private $_CommandExpeditionDetail = false;

    /**
     * _Command::getCommandExpeditionDetail
     *
     * @access public
     * @return object CommandExpeditionDetail
     */
    public function getCommandExpeditionDetail() {
        if (is_int($this->_CommandExpeditionDetail) && $this->_CommandExpeditionDetail > 0) {
            $mapper = Mapper::singleton('CommandExpeditionDetail');
            $this->_CommandExpeditionDetail = $mapper->load(
                array('Id'=>$this->_CommandExpeditionDetail));
        }
        return $this->_CommandExpeditionDetail;
    }

    /**
     * _Command::getCommandExpeditionDetailId
     *
     * @access public
     * @return integer
     */
    public function getCommandExpeditionDetailId() {
        if ($this->_CommandExpeditionDetail instanceof CommandExpeditionDetail) {
            return $this->_CommandExpeditionDetail->getId();
        }
        return (int)$this->_CommandExpeditionDetail;
    }

    /**
     * _Command::setCommandExpeditionDetail
     *
     * @access public
     * @param object CommandExpeditionDetail $value
     * @return void
     */
    public function setCommandExpeditionDetail($value) {
        if (is_numeric($value)) {
            $this->_CommandExpeditionDetail = (int)$value;
        } else {
            $this->_CommandExpeditionDetail = $value;
        }
    }

    // }}}
    // Currency foreignkey property + getter/setter {{{

    /**
     * Currency foreignkey
     *
     * @access private
     * @var mixed object Currency or integer
     */
    private $_Currency = false;

    /**
     * _Command::getCurrency
     *
     * @access public
     * @return object Currency
     */
    public function getCurrency() {
        if (is_int($this->_Currency) && $this->_Currency > 0) {
            $mapper = Mapper::singleton('Currency');
            $this->_Currency = $mapper->load(
                array('Id'=>$this->_Currency));
        }
        return $this->_Currency;
    }

    /**
     * _Command::getCurrencyId
     *
     * @access public
     * @return integer
     */
    public function getCurrencyId() {
        if ($this->_Currency instanceof Currency) {
            return $this->_Currency->getId();
        }
        return (int)$this->_Currency;
    }

    /**
     * _Command::setCurrency
     *
     * @access public
     * @param object Currency $value
     * @return void
     */
    public function setCurrency($value) {
        if (is_numeric($value)) {
            $this->_Currency = (int)$value;
        } else {
            $this->_Currency = $value;
        }
    }

    // }}}
    // ActorBankDetail foreignkey property + getter/setter {{{

    /**
     * ActorBankDetail foreignkey
     *
     * @access private
     * @var mixed object ActorBankDetail or integer
     */
    private $_ActorBankDetail = false;

    /**
     * _Command::getActorBankDetail
     *
     * @access public
     * @return object ActorBankDetail
     */
    public function getActorBankDetail() {
        if (is_int($this->_ActorBankDetail) && $this->_ActorBankDetail > 0) {
            $mapper = Mapper::singleton('ActorBankDetail');
            $this->_ActorBankDetail = $mapper->load(
                array('Id'=>$this->_ActorBankDetail));
        }
        return $this->_ActorBankDetail;
    }

    /**
     * _Command::getActorBankDetailId
     *
     * @access public
     * @return integer
     */
    public function getActorBankDetailId() {
        if ($this->_ActorBankDetail instanceof ActorBankDetail) {
            return $this->_ActorBankDetail->getId();
        }
        return (int)$this->_ActorBankDetail;
    }

    /**
     * _Command::setActorBankDetail
     *
     * @access public
     * @param object ActorBankDetail $value
     * @return void
     */
    public function setActorBankDetail($value) {
        if (is_numeric($value)) {
            $this->_ActorBankDetail = (int)$value;
        } else {
            $this->_ActorBankDetail = $value;
        }
    }

    // }}}
    // InstallmentBank foreignkey property + getter/setter {{{

    /**
     * InstallmentBank foreignkey
     *
     * @access private
     * @var mixed object ActorBankDetail or integer
     */
    private $_InstallmentBank = false;

    /**
     * _Command::getInstallmentBank
     *
     * @access public
     * @return object ActorBankDetail
     */
    public function getInstallmentBank() {
        if (is_int($this->_InstallmentBank) && $this->_InstallmentBank > 0) {
            $mapper = Mapper::singleton('ActorBankDetail');
            $this->_InstallmentBank = $mapper->load(
                array('Id'=>$this->_InstallmentBank));
        }
        return $this->_InstallmentBank;
    }

    /**
     * _Command::getInstallmentBankId
     *
     * @access public
     * @return integer
     */
    public function getInstallmentBankId() {
        if ($this->_InstallmentBank instanceof ActorBankDetail) {
            return $this->_InstallmentBank->getId();
        }
        return (int)$this->_InstallmentBank;
    }

    /**
     * _Command::setInstallmentBank
     *
     * @access public
     * @param object ActorBankDetail $value
     * @return void
     */
    public function setInstallmentBank($value) {
        if (is_numeric($value)) {
            $this->_InstallmentBank = (int)$value;
        } else {
            $this->_InstallmentBank = $value;
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
     * _Command::getCommand
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
     * _Command::getCommandId
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
     * _Command::setCommand
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
    // ParentCommand foreignkey property + getter/setter {{{

    /**
     * ParentCommand foreignkey
     *
     * @access private
     * @var mixed object Command or integer
     */
    private $_ParentCommand = false;

    /**
     * _Command::getParentCommand
     *
     * @access public
     * @return object Command
     */
    public function getParentCommand() {
        if (is_int($this->_ParentCommand) && $this->_ParentCommand > 0) {
            $mapper = Mapper::singleton('Command');
            $this->_ParentCommand = $mapper->load(
                array('Id'=>$this->_ParentCommand));
        }
        return $this->_ParentCommand;
    }

    /**
     * _Command::getParentCommandId
     *
     * @access public
     * @return integer
     */
    public function getParentCommandId() {
        if ($this->_ParentCommand instanceof Command) {
            return $this->_ParentCommand->getId();
        }
        return (int)$this->_ParentCommand;
    }

    /**
     * _Command::setParentCommand
     *
     * @access public
     * @param object Command $value
     * @return void
     */
    public function setParentCommand($value) {
        if (is_numeric($value)) {
            $this->_ParentCommand = (int)$value;
        } else {
            $this->_ParentCommand = $value;
        }
    }

    // }}}
    // ProjectManager foreignkey property + getter/setter {{{

    /**
     * ProjectManager foreignkey
     *
     * @access private
     * @var mixed object ProjectManager or integer
     */
    private $_ProjectManager = false;

    /**
     * _Command::getProjectManager
     *
     * @access public
     * @return object ProjectManager
     */
    public function getProjectManager() {
        if (is_int($this->_ProjectManager) && $this->_ProjectManager > 0) {
            $mapper = Mapper::singleton('ProjectManager');
            $this->_ProjectManager = $mapper->load(
                array('Id'=>$this->_ProjectManager));
        }
        return $this->_ProjectManager;
    }

    /**
     * _Command::getProjectManagerId
     *
     * @access public
     * @return integer
     */
    public function getProjectManagerId() {
        if ($this->_ProjectManager instanceof ProjectManager) {
            return $this->_ProjectManager->getId();
        }
        return (int)$this->_ProjectManager;
    }

    /**
     * _Command::setProjectManager
     *
     * @access public
     * @param object ProjectManager $value
     * @return void
     */
    public function setProjectManager($value) {
        if (is_numeric($value)) {
            $this->_ProjectManager = (int)$value;
        } else {
            $this->_ProjectManager = $value;
        }
    }

    // }}}
    // AbstractDocument one to many relation + getter/setter {{{

    /**
     * AbstractDocument 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_AbstractDocumentCollection = false;

    /**
     * _Command::getAbstractDocumentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getAbstractDocumentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Command');
            return $mapper->getOneToMany($this->getId(),
                'AbstractDocument', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_AbstractDocumentCollection) {
            $mapper = Mapper::singleton('Command');
            $this->_AbstractDocumentCollection = $mapper->getOneToMany($this->getId(),
                'AbstractDocument');
        }
        return $this->_AbstractDocumentCollection;
    }

    /**
     * _Command::getAbstractDocumentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getAbstractDocumentCollectionIds($filter = array()) {
        $col = $this->getAbstractDocumentCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Command::setAbstractDocumentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setAbstractDocumentCollection($value) {
        $this->_AbstractDocumentCollection = $value;
    }

    // }}}
    // CommandItem one to many relation + getter/setter {{{

    /**
     * CommandItem 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CommandItemCollection = false;

    /**
     * _Command::getCommandItemCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCommandItemCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Command');
            return $mapper->getOneToMany($this->getId(),
                'CommandItem', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CommandItemCollection) {
            $mapper = Mapper::singleton('Command');
            $this->_CommandItemCollection = $mapper->getOneToMany($this->getId(),
                'CommandItem');
        }
        return $this->_CommandItemCollection;
    }

    /**
     * _Command::getCommandItemCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCommandItemCollectionIds($filter = array()) {
        $col = $this->getCommandItemCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Command::setCommandItemCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCommandItemCollection($value) {
        $this->_CommandItemCollection = $value;
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
     * _Command::getUnavailabilityCollection
     *
     * @access public
     * @return object Collection
     */
    public function getUnavailabilityCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Command');
            return $mapper->getOneToMany($this->getId(),
                'Unavailability', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_UnavailabilityCollection) {
            $mapper = Mapper::singleton('Command');
            $this->_UnavailabilityCollection = $mapper->getOneToMany($this->getId(),
                'Unavailability');
        }
        return $this->_UnavailabilityCollection;
    }

    /**
     * _Command::getUnavailabilityCollectionIds
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
     * _Command::setUnavailabilityCollection
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
        return 'Command';
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
            'CommandNo' => Object::TYPE_STRING,
            'Type' => Object::TYPE_CONST,
            'CommandDate' => Object::TYPE_DATETIME,
            'MerchandiseValue' => Object::TYPE_DECIMAL,
            'AdditionnalGaranties' => Object::TYPE_INT,
            'Incoterm' => 'Incoterm',
            'WishedStartDate' => Object::TYPE_DATETIME,
            'WishedEndDate' => Object::TYPE_DATETIME,
            'Comment' => Object::TYPE_STRING,
            'Expeditor' => 'Actor',
            'Destinator' => 'Actor',
            'ExpeditorSite' => 'Site',
            'DestinatorSite' => 'Site',
            'Customer' => 'Actor',
            'SupplierCustomer' => 'SupplierCustomer',
            'Commercial' => 'UserAccount',
            'State' => Object::TYPE_CONST,
            'Handing' => Object::TYPE_DECIMAL,
            'HandingByRangePercent' => Object::TYPE_DECIMAL,
            'Port' => Object::TYPE_DECIMAL,
            'Packing' => Object::TYPE_DECIMAL,
            'Insurance' => Object::TYPE_DECIMAL,
            'TotalPriceHT' => Object::TYPE_DECIMAL,
            'TotalPriceTTC' => Object::TYPE_DECIMAL,
            'Processed' => Object::TYPE_INT,
            'Installment' => Object::TYPE_DECIMAL,
            'CustomerRemExcep' => Object::TYPE_DECIMAL,
            'Duration' => Object::TYPE_TIME,
            'Cadenced' => Object::TYPE_BOOL,
            'Closed' => Object::TYPE_BOOL,
            'IsEstimate' => Object::TYPE_BOOL,
            'CommandExpeditionDetail' => 'CommandExpeditionDetail',
            'Currency' => 'Currency',
            'ActorBankDetail' => 'ActorBankDetail',
            'InstallmentBank' => 'ActorBankDetail',
            'Command' => 'Command',
            'ParentCommand' => 'Command',
            'ProjectManager' => 'ProjectManager');
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
            'Command'=>array(
                'linkClass'     => 'Command',
                'field'         => 'Command',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetoone'
            ),
            'AbstractDocument'=>array(
                'linkClass'     => 'AbstractDocument',
                'field'         => 'Command',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'Command_1'=>array(
                'linkClass'     => 'Command',
                'field'         => 'ParentCommand',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'CommandItem'=>array(
                'linkClass'     => 'CommandItem',
                'field'         => 'Command',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'Unavailability'=>array(
                'linkClass'     => 'Unavailability',
                'field'         => 'Command',
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
    // _Command::mutate() {{{

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
        if(!($mutant instanceof _Command)) {
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