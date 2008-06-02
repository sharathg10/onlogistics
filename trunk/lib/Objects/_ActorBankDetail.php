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

class _ActorBankDetail extends Object {
    // class constants {{{

    const TYPE_AVENUE = 1;
    const TYPE_BOULEVARD = 2;
    const TYPE_CHEMIN = 3;
    const TYPE_IMPASSE = 4;
    const TYPE_PLACE = 5;
    const TYPE_ROUTE = 6;
    const TYPE_RUE = 7;

    // }}}
    // Constructeur {{{

    /**
     * _ActorBankDetail::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Iban string property + getter/setter {{{

    /**
     * Iban string property
     *
     * @access private
     * @var string
     */
    private $_Iban = '';

    /**
     * _ActorBankDetail::getIban
     *
     * @access public
     * @return string
     */
    public function getIban() {
        return $this->_Iban;
    }

    /**
     * _ActorBankDetail::setIban
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setIban($value) {
        $this->_Iban = $value;
    }

    // }}}
    // BankName string property + getter/setter {{{

    /**
     * BankName string property
     *
     * @access private
     * @var string
     */
    private $_BankName = '';

    /**
     * _ActorBankDetail::getBankName
     *
     * @access public
     * @return string
     */
    public function getBankName() {
        return $this->_BankName;
    }

    /**
     * _ActorBankDetail::setBankName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBankName($value) {
        $this->_BankName = $value;
    }

    // }}}
    // Swift string property + getter/setter {{{

    /**
     * Swift string property
     *
     * @access private
     * @var string
     */
    private $_Swift = '';

    /**
     * _ActorBankDetail::getSwift
     *
     * @access public
     * @return string
     */
    public function getSwift() {
        return $this->_Swift;
    }

    /**
     * _ActorBankDetail::setSwift
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setSwift($value) {
        $this->_Swift = $value;
    }

    // }}}
    // BankAddressNo string property + getter/setter {{{

    /**
     * BankAddressNo string property
     *
     * @access private
     * @var string
     */
    private $_BankAddressNo = '';

    /**
     * _ActorBankDetail::getBankAddressNo
     *
     * @access public
     * @return string
     */
    public function getBankAddressNo() {
        return $this->_BankAddressNo;
    }

    /**
     * _ActorBankDetail::setBankAddressNo
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBankAddressNo($value) {
        $this->_BankAddressNo = $value;
    }

    // }}}
    // BankAddressStreetType const property + getter/setter/getBankAddressStreetTypeConstArray {{{

    /**
     * BankAddressStreetType int property
     *
     * @access private
     * @var integer
     */
    private $_BankAddressStreetType = 0;

    /**
     * _ActorBankDetail::getBankAddressStreetType
     *
     * @access public
     * @return integer
     */
    public function getBankAddressStreetType() {
        return $this->_BankAddressStreetType;
    }

    /**
     * _ActorBankDetail::setBankAddressStreetType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setBankAddressStreetType($value) {
        if ($value !== null) {
            $this->_BankAddressStreetType = (int)$value;
        }
    }

    /**
     * _ActorBankDetail::getBankAddressStreetTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getBankAddressStreetTypeConstArray($keys = false) {
        $array = array(
            _ActorBankDetail::TYPE_AVENUE => _("Avenue"), 
            _ActorBankDetail::TYPE_BOULEVARD => _("Boulevard"), 
            _ActorBankDetail::TYPE_CHEMIN => _("Lane"), 
            _ActorBankDetail::TYPE_IMPASSE => _("Blind alley"), 
            _ActorBankDetail::TYPE_PLACE => _("Place"), 
            _ActorBankDetail::TYPE_ROUTE => _("Road"), 
            _ActorBankDetail::TYPE_RUE => _("Street")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // BankAddressStreet string property + getter/setter {{{

    /**
     * BankAddressStreet string property
     *
     * @access private
     * @var string
     */
    private $_BankAddressStreet = '';

    /**
     * _ActorBankDetail::getBankAddressStreet
     *
     * @access public
     * @return string
     */
    public function getBankAddressStreet() {
        return $this->_BankAddressStreet;
    }

    /**
     * _ActorBankDetail::setBankAddressStreet
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBankAddressStreet($value) {
        $this->_BankAddressStreet = $value;
    }

    // }}}
    // BankAddressAdd string property + getter/setter {{{

    /**
     * BankAddressAdd string property
     *
     * @access private
     * @var string
     */
    private $_BankAddressAdd = '';

    /**
     * _ActorBankDetail::getBankAddressAdd
     *
     * @access public
     * @return string
     */
    public function getBankAddressAdd() {
        return $this->_BankAddressAdd;
    }

    /**
     * _ActorBankDetail::setBankAddressAdd
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBankAddressAdd($value) {
        $this->_BankAddressAdd = $value;
    }

    // }}}
    // BankAddressCity string property + getter/setter {{{

    /**
     * BankAddressCity string property
     *
     * @access private
     * @var string
     */
    private $_BankAddressCity = '';

    /**
     * _ActorBankDetail::getBankAddressCity
     *
     * @access public
     * @return string
     */
    public function getBankAddressCity() {
        return $this->_BankAddressCity;
    }

    /**
     * _ActorBankDetail::setBankAddressCity
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBankAddressCity($value) {
        $this->_BankAddressCity = $value;
    }

    // }}}
    // BankAddressZipCode string property + getter/setter {{{

    /**
     * BankAddressZipCode string property
     *
     * @access private
     * @var string
     */
    private $_BankAddressZipCode = '';

    /**
     * _ActorBankDetail::getBankAddressZipCode
     *
     * @access public
     * @return string
     */
    public function getBankAddressZipCode() {
        return $this->_BankAddressZipCode;
    }

    /**
     * _ActorBankDetail::setBankAddressZipCode
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBankAddressZipCode($value) {
        $this->_BankAddressZipCode = $value;
    }

    // }}}
    // BankAddressCountry string property + getter/setter {{{

    /**
     * BankAddressCountry string property
     *
     * @access private
     * @var string
     */
    private $_BankAddressCountry = '';

    /**
     * _ActorBankDetail::getBankAddressCountry
     *
     * @access public
     * @return string
     */
    public function getBankAddressCountry() {
        return $this->_BankAddressCountry;
    }

    /**
     * _ActorBankDetail::setBankAddressCountry
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBankAddressCountry($value) {
        $this->_BankAddressCountry = $value;
    }

    // }}}
    // AccountNumber string property + getter/setter {{{

    /**
     * AccountNumber string property
     *
     * @access private
     * @var string
     */
    private $_AccountNumber = '';

    /**
     * _ActorBankDetail::getAccountNumber
     *
     * @access public
     * @return string
     */
    public function getAccountNumber() {
        return $this->_AccountNumber;
    }

    /**
     * _ActorBankDetail::setAccountNumber
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setAccountNumber($value) {
        $this->_AccountNumber = $value;
    }

    // }}}
    // Amount float property + getter/setter {{{

    /**
     * Amount float property
     *
     * @access private
     * @var float
     */
    private $_Amount = 0;

    /**
     * _ActorBankDetail::getAmount
     *
     * @access public
     * @return float
     */
    public function getAmount() {
        return $this->_Amount;
    }

    /**
     * _ActorBankDetail::setAmount
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setAmount($value) {
        if ($value !== null) {
            $this->_Amount = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // LastUpdate datetime property + getter/setter {{{

    /**
     * LastUpdate int property
     *
     * @access private
     * @var string
     */
    private $_LastUpdate = 0;

    /**
     * _ActorBankDetail::getLastUpdate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getLastUpdate($format = false) {
        return $this->dateFormat($this->_LastUpdate, $format);
    }

    /**
     * _ActorBankDetail::setLastUpdate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setLastUpdate($value) {
        $this->_LastUpdate = $value;
    }

    // }}}
    // Active string property + getter/setter {{{

    /**
     * Active int property
     *
     * @access private
     * @var integer
     */
    private $_Active = 1;

    /**
     * _ActorBankDetail::getActive
     *
     * @access public
     * @return integer
     */
    public function getActive() {
        return $this->_Active;
    }

    /**
     * _ActorBankDetail::setActive
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setActive($value) {
        if ($value !== null) {
            $this->_Active = (int)$value;
        }
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
     * _ActorBankDetail::getActor
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
     * _ActorBankDetail::getActorId
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
     * _ActorBankDetail::setActor
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
    // Currency foreignkey property + getter/setter {{{

    /**
     * Currency foreignkey
     *
     * @access private
     * @var mixed object Currency or integer
     */
    private $_Currency = false;

    /**
     * _ActorBankDetail::getCurrency
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
     * _ActorBankDetail::getCurrencyId
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
     * _ActorBankDetail::setCurrency
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
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'ActorBankDetail';
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
            'Iban' => Object::TYPE_STRING,
            'BankName' => Object::TYPE_STRING,
            'Swift' => Object::TYPE_STRING,
            'BankAddressNo' => Object::TYPE_STRING,
            'BankAddressStreetType' => Object::TYPE_CONST,
            'BankAddressStreet' => Object::TYPE_STRING,
            'BankAddressAdd' => Object::TYPE_STRING,
            'BankAddressCity' => Object::TYPE_STRING,
            'BankAddressZipCode' => Object::TYPE_STRING,
            'BankAddressCountry' => Object::TYPE_STRING,
            'AccountNumber' => Object::TYPE_STRING,
            'Amount' => Object::TYPE_DECIMAL,
            'LastUpdate' => Object::TYPE_DATE,
            'Active' => Object::TYPE_BOOL,
            'Actor' => 'Actor',
            'Currency' => 'Currency');
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
            'Account'=>array(
                'linkClass'     => 'Account',
                'field'         => 'ActorBankDetail',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'AccountingType'=>array(
                'linkClass'     => 'AccountingType',
                'field'         => 'ActorBankDetail',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Command'=>array(
                'linkClass'     => 'Command',
                'field'         => 'ActorBankDetail',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Command_1'=>array(
                'linkClass'     => 'Command',
                'field'         => 'InstallmentBank',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Flow'=>array(
                'linkClass'     => 'Flow',
                'field'         => 'ActorBankDetail',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'FlowType'=>array(
                'linkClass'     => 'FlowType',
                'field'         => 'ActorBankDetail',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Payment'=>array(
                'linkClass'     => 'Payment',
                'field'         => 'ActorBankDetail',
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