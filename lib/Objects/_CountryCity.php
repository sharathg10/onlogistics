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

class _CountryCity extends Object {
    
    // Constructeur {{{

    /**
     * _CountryCity::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Zip foreignkey property + getter/setter {{{

    /**
     * Zip foreignkey
     *
     * @access private
     * @var mixed object Zip or integer
     */
    private $_Zip = false;

    /**
     * _CountryCity::getZip
     *
     * @access public
     * @return object Zip
     */
    public function getZip() {
        if (is_int($this->_Zip) && $this->_Zip > 0) {
            $mapper = Mapper::singleton('Zip');
            $this->_Zip = $mapper->load(
                array('Id'=>$this->_Zip));
        }
        return $this->_Zip;
    }

    /**
     * _CountryCity::getZipId
     *
     * @access public
     * @return integer
     */
    public function getZipId() {
        if ($this->_Zip instanceof Zip) {
            return $this->_Zip->getId();
        }
        return (int)$this->_Zip;
    }

    /**
     * _CountryCity::setZip
     *
     * @access public
     * @param object Zip $value
     * @return void
     */
    public function setZip($value) {
        if (is_numeric($value)) {
            $this->_Zip = (int)$value;
        } else {
            $this->_Zip = $value;
        }
    }

    // }}}
    // Country foreignkey property + getter/setter {{{

    /**
     * Country foreignkey
     *
     * @access private
     * @var mixed object Country or integer
     */
    private $_Country = false;

    /**
     * _CountryCity::getCountry
     *
     * @access public
     * @return object Country
     */
    public function getCountry() {
        if (is_int($this->_Country) && $this->_Country > 0) {
            $mapper = Mapper::singleton('Country');
            $this->_Country = $mapper->load(
                array('Id'=>$this->_Country));
        }
        return $this->_Country;
    }

    /**
     * _CountryCity::getCountryId
     *
     * @access public
     * @return integer
     */
    public function getCountryId() {
        if ($this->_Country instanceof Country) {
            return $this->_Country->getId();
        }
        return (int)$this->_Country;
    }

    /**
     * _CountryCity::setCountry
     *
     * @access public
     * @param object Country $value
     * @return void
     */
    public function setCountry($value) {
        if (is_numeric($value)) {
            $this->_Country = (int)$value;
        } else {
            $this->_Country = $value;
        }
    }

    // }}}
    // CityName foreignkey property + getter/setter {{{

    /**
     * CityName foreignkey
     *
     * @access private
     * @var mixed object CityName or integer
     */
    private $_CityName = false;

    /**
     * _CountryCity::getCityName
     *
     * @access public
     * @return object CityName
     */
    public function getCityName() {
        if (is_int($this->_CityName) && $this->_CityName > 0) {
            $mapper = Mapper::singleton('CityName');
            $this->_CityName = $mapper->load(
                array('Id'=>$this->_CityName));
        }
        return $this->_CityName;
    }

    /**
     * _CountryCity::getCityNameId
     *
     * @access public
     * @return integer
     */
    public function getCityNameId() {
        if ($this->_CityName instanceof CityName) {
            return $this->_CityName->getId();
        }
        return (int)$this->_CityName;
    }

    /**
     * _CountryCity::setCityName
     *
     * @access public
     * @param object CityName $value
     * @return void
     */
    public function setCityName($value) {
        if (is_numeric($value)) {
            $this->_CityName = (int)$value;
        } else {
            $this->_CityName = $value;
        }
    }

    // }}}
    // Zone foreignkey property + getter/setter {{{

    /**
     * Zone foreignkey
     *
     * @access private
     * @var mixed object Zone or integer
     */
    private $_Zone = false;

    /**
     * _CountryCity::getZone
     *
     * @access public
     * @return object Zone
     */
    public function getZone() {
        if (is_int($this->_Zone) && $this->_Zone > 0) {
            $mapper = Mapper::singleton('Zone');
            $this->_Zone = $mapper->load(
                array('Id'=>$this->_Zone));
        }
        return $this->_Zone;
    }

    /**
     * _CountryCity::getZoneId
     *
     * @access public
     * @return integer
     */
    public function getZoneId() {
        if ($this->_Zone instanceof Zone) {
            return $this->_Zone->getId();
        }
        return (int)$this->_Zone;
    }

    /**
     * _CountryCity::setZone
     *
     * @access public
     * @param object Zone $value
     * @return void
     */
    public function setZone($value) {
        if (is_numeric($value)) {
            $this->_Zone = (int)$value;
        } else {
            $this->_Zone = $value;
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
        return 'CountryCity';
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
            'Zip' => 'Zip',
            'Country' => 'Country',
            'CityName' => 'CityName',
            'Zone' => 'Zone');
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
            'Site'=>array(
                'linkClass'     => 'Site',
                'field'         => 'CountryCity',
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