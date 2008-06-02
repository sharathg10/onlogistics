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

class CommandExpeditionDetail extends Object {
    // class constants {{{

    const SHIPMENT_ROAD = 1;
    const SHIPMENT_RAIL = 2;
    const SHIPMENT_AIR = 3;
    const SHIPMENT_SEA = 4;

    // }}}
    // Constructeur {{{

    /**
     * CommandExpeditionDetail::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // LoadingPort string property + getter/setter {{{

    /**
     * LoadingPort string property
     *
     * @access private
     * @var string
     */
    private $_LoadingPort = '';

    /**
     * CommandExpeditionDetail::getLoadingPort
     *
     * @access public
     * @return string
     */
    public function getLoadingPort() {
        return $this->_LoadingPort;
    }

    /**
     * CommandExpeditionDetail::setLoadingPort
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setLoadingPort($value) {
        $this->_LoadingPort = $value;
    }

    // }}}
    // Shipment const property + getter/setter/getShipmentConstArray {{{

    /**
     * Shipment int property
     *
     * @access private
     * @var integer
     */
    private $_Shipment = 0;

    /**
     * CommandExpeditionDetail::getShipment
     *
     * @access public
     * @return integer
     */
    public function getShipment() {
        return $this->_Shipment;
    }

    /**
     * CommandExpeditionDetail::setShipment
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setShipment($value) {
        if ($value !== null) {
            $this->_Shipment = (int)$value;
        }
    }

    /**
     * CommandExpeditionDetail::getShipmentConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getShipmentConstArray($keys = false) {
        $array = array(
            CommandExpeditionDetail::SHIPMENT_ROAD => _("By road"), 
            CommandExpeditionDetail::SHIPMENT_RAIL => _("Railway"), 
            CommandExpeditionDetail::SHIPMENT_AIR => _("Air"), 
            CommandExpeditionDetail::SHIPMENT_SEA => _("Sea")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // CustomerCommandNo string property + getter/setter {{{

    /**
     * CustomerCommandNo string property
     *
     * @access private
     * @var string
     */
    private $_CustomerCommandNo = '';

    /**
     * CommandExpeditionDetail::getCustomerCommandNo
     *
     * @access public
     * @return string
     */
    public function getCustomerCommandNo() {
        return $this->_CustomerCommandNo;
    }

    /**
     * CommandExpeditionDetail::setCustomerCommandNo
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCustomerCommandNo($value) {
        $this->_CustomerCommandNo = $value;
    }

    // }}}
    // DestinatorStore string property + getter/setter {{{

    /**
     * DestinatorStore string property
     *
     * @access private
     * @var string
     */
    private $_DestinatorStore = '';

    /**
     * CommandExpeditionDetail::getDestinatorStore
     *
     * @access public
     * @return string
     */
    public function getDestinatorStore() {
        return $this->_DestinatorStore;
    }

    /**
     * CommandExpeditionDetail::setDestinatorStore
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDestinatorStore($value) {
        $this->_DestinatorStore = $value;
    }

    // }}}
    // DestinatorRange string property + getter/setter {{{

    /**
     * DestinatorRange string property
     *
     * @access private
     * @var string
     */
    private $_DestinatorRange = '';

    /**
     * CommandExpeditionDetail::getDestinatorRange
     *
     * @access public
     * @return string
     */
    public function getDestinatorRange() {
        return $this->_DestinatorRange;
    }

    /**
     * CommandExpeditionDetail::setDestinatorRange
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDestinatorRange($value) {
        $this->_DestinatorRange = $value;
    }

    // }}}
    // ReservationNo string property + getter/setter {{{

    /**
     * ReservationNo string property
     *
     * @access private
     * @var string
     */
    private $_ReservationNo = '';

    /**
     * CommandExpeditionDetail::getReservationNo
     *
     * @access public
     * @return string
     */
    public function getReservationNo() {
        return $this->_ReservationNo;
    }

    /**
     * CommandExpeditionDetail::setReservationNo
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setReservationNo($value) {
        $this->_ReservationNo = $value;
    }

    // }}}
    // Season string property + getter/setter {{{

    /**
     * Season string property
     *
     * @access private
     * @var string
     */
    private $_Season = '';

    /**
     * CommandExpeditionDetail::getSeason
     *
     * @access public
     * @return string
     */
    public function getSeason() {
        return $this->_Season;
    }

    /**
     * CommandExpeditionDetail::setSeason
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setSeason($value) {
        $this->_Season = $value;
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
     * CommandExpeditionDetail::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * CommandExpeditionDetail::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // Deal string property + getter/setter {{{

    /**
     * Deal string property
     *
     * @access private
     * @var string
     */
    private $_Deal = '';

    /**
     * CommandExpeditionDetail::getDeal
     *
     * @access public
     * @return string
     */
    public function getDeal() {
        return $this->_Deal;
    }

    /**
     * CommandExpeditionDetail::setDeal
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDeal($value) {
        $this->_Deal = $value;
    }

    // }}}
    // AirwayBill string property + getter/setter {{{

    /**
     * AirwayBill string property
     *
     * @access private
     * @var string
     */
    private $_AirwayBill = '';

    /**
     * CommandExpeditionDetail::getAirwayBill
     *
     * @access public
     * @return string
     */
    public function getAirwayBill() {
        return $this->_AirwayBill;
    }

    /**
     * CommandExpeditionDetail::setAirwayBill
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setAirwayBill($value) {
        $this->_AirwayBill = $value;
    }

    // }}}
    // PackingList string property + getter/setter {{{

    /**
     * PackingList string property
     *
     * @access private
     * @var string
     */
    private $_PackingList = '';

    /**
     * CommandExpeditionDetail::getPackingList
     *
     * @access public
     * @return string
     */
    public function getPackingList() {
        return $this->_PackingList;
    }

    /**
     * CommandExpeditionDetail::setPackingList
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPackingList($value) {
        $this->_PackingList = $value;
    }

    // }}}
    // SupplierCode string property + getter/setter {{{

    /**
     * SupplierCode string property
     *
     * @access private
     * @var string
     */
    private $_SupplierCode = '';

    /**
     * CommandExpeditionDetail::getSupplierCode
     *
     * @access public
     * @return string
     */
    public function getSupplierCode() {
        return $this->_SupplierCode;
    }

    /**
     * CommandExpeditionDetail::setSupplierCode
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setSupplierCode($value) {
        $this->_SupplierCode = $value;
    }

    // }}}
    // Weight float property + getter/setter {{{

    /**
     * Weight float property
     *
     * @access private
     * @var float
     */
    private $_Weight = 0;

    /**
     * CommandExpeditionDetail::getWeight
     *
     * @access public
     * @return float
     */
    public function getWeight() {
        return $this->_Weight;
    }

    /**
     * CommandExpeditionDetail::setWeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setWeight($value) {
        if ($value !== null) {
            $this->_Weight = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // NumberOfContainer string property + getter/setter {{{

    /**
     * NumberOfContainer int property
     *
     * @access private
     * @var integer
     */
    private $_NumberOfContainer = 0;

    /**
     * CommandExpeditionDetail::getNumberOfContainer
     *
     * @access public
     * @return integer
     */
    public function getNumberOfContainer() {
        return $this->_NumberOfContainer;
    }

    /**
     * CommandExpeditionDetail::setNumberOfContainer
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setNumberOfContainer($value) {
        if ($value !== null) {
            $this->_NumberOfContainer = (int)$value;
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
        return 'CommandExpeditionDetail';
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
            'LoadingPort' => Object::TYPE_STRING,
            'Shipment' => Object::TYPE_CONST,
            'CustomerCommandNo' => Object::TYPE_STRING,
            'DestinatorStore' => Object::TYPE_STRING,
            'DestinatorRange' => Object::TYPE_STRING,
            'ReservationNo' => Object::TYPE_STRING,
            'Season' => Object::TYPE_STRING,
            'Comment' => Object::TYPE_TEXT,
            'Deal' => Object::TYPE_STRING,
            'AirwayBill' => Object::TYPE_STRING,
            'PackingList' => Object::TYPE_STRING,
            'SupplierCode' => Object::TYPE_STRING,
            'Weight' => Object::TYPE_DECIMAL,
            'NumberOfContainer' => Object::TYPE_INT);
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
                'field'         => 'CommandExpeditionDetail',
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