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

class _Incoterm extends Object {
    // class constants {{{

    const INCOTERM_ALL = 0;
    const INCOTERM_MARITIME = 1;

    // }}}
    // Constructeur {{{

    /**
     * _Incoterm::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Code string property + getter/setter {{{

    /**
     * Code string property
     *
     * @access private
     * @var string
     */
    private $_Code = '';

    /**
     * _Incoterm::getCode
     *
     * @access public
     * @return string
     */
    public function getCode() {
        return $this->_Code;
    }

    /**
     * _Incoterm::setCode
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCode($value) {
        $this->_Code = $value;
    }

    // }}}
    // Label string property + getter/setter {{{

    /**
     * Label string property
     *
     * @access private
     * @var string
     */
    private $_Label = '';

    /**
     * _Incoterm::getLabel
     *
     * @access public
     * @return string
     */
    public function getLabel() {
        return $this->_Label;
    }

    /**
     * _Incoterm::setLabel
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setLabel($value) {
        $this->_Label = $value;
    }

    // }}}
    // Description i18n_string property + getter/setter {{{

    /**
     * Description foreignkey
     *
     * @access private
     * @var mixed object I18nString or integer
     */
    private $_Description = 0;

    /**
     * _Incoterm::getDescription
     *
     * @access public
     * @param string $locale optional, default is the current locale code
     * @param boolean $useDefaultLocaleIfEmpty determine if the getter must
     * return the translation in the DEFAULT_LOCALE if no translation is found
     * in the current locale.
     * @return string
     */
    public function getDescription($locale=false, $defaultLocaleIfEmpty=true) {
        $locale = $locale !== false ? $locale : I18N::getLocaleCode();
        if (is_int($this->_Description) && $this->_Description > 0) {
            $this->_Description = Object::load('I18nString', $this->_Description);
        }
        $ret = null;
        if ($this->_Description instanceof I18nString) {
            $getter = 'getStringValue_' . $locale;
            $ret = $this->_Description->$getter();
            if ($ret == null && $defaultLocaleIfEmpty) {
                $getter = 'getStringValue_' . LOCALE_DEFAULT;
                $ret = $this->_Description->$getter();
            }
        }
        return $ret;
    }

    /**
     * _Incoterm::getDescriptionId
     *
     * @access public
     * @return integer
     */
    public function getDescriptionId() {
        if ($this->_Description instanceof I18nString) {
            return $this->_Description->getId();
        }
        return (int)$this->_Description;
    }

    /**
     * _Incoterm::setDescription
     *
     * @access public
     * @param string $value
     * @param string $locale optional, default is the current locale code
     * @return void
     */
    public function setDescription($value, $locale=false) {
        if (is_numeric($value)) {
            $this->_Description = (int)$value;
        } else if ($value instanceof I18nString) {
            $this->_Description = $value;
        } else {
            $locale = $locale !== false ? $locale : I18N::getLocaleCode();
            if (!($this->_Description instanceof I18nString)) {
                $this->_Description = Object::load('I18nString', $this->_Description);
                if (!($this->_Description instanceof I18nString)) {
                    $this->_Description = new I18nString();
                }
            }
            $setter = 'setStringValue_'.$locale;
            $this->_Description->$setter($value);
            $this->_Description->save();
        }
    }

    // }}}
    // TransportType const property + getter/setter/getTransportTypeConstArray {{{

    /**
     * TransportType int property
     *
     * @access private
     * @var integer
     */
    private $_TransportType = 0;

    /**
     * _Incoterm::getTransportType
     *
     * @access public
     * @return integer
     */
    public function getTransportType() {
        return $this->_TransportType;
    }

    /**
     * _Incoterm::setTransportType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTransportType($value) {
        if ($value !== null) {
            $this->_TransportType = (int)$value;
        }
    }

    /**
     * _Incoterm::getTransportTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getTransportTypeConstArray($keys = false) {
        $array = array(
            _Incoterm::INCOTERM_ALL => _("All"), 
            _Incoterm::INCOTERM_MARITIME => _("SEA")
        );
        asort($array);
        return $keys?array_keys($array):$array;
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
        return 'Incoterm';
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
            'Code' => Object::TYPE_STRING,
            'Label' => Object::TYPE_STRING,
            'Description' => Object::TYPE_I18N_TEXT,
            'TransportType' => Object::TYPE_CONST);
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
            'Actor'=>array(
                'linkClass'     => 'Actor',
                'field'         => 'Incoterm',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Command'=>array(
                'linkClass'     => 'Command',
                'field'         => 'Incoterm',
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