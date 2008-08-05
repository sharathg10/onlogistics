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
 * SellUnitType class
 *
 */
class SellUnitType extends Object {
    
    // Constructeur {{{

    /**
     * SellUnitType::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // ShortName i18n_string property + getter/setter {{{

    /**
     * ShortName foreignkey
     *
     * @access private
     * @var mixed object I18nString or integer
     */
    private $_ShortName = 0;

    /**
     * SellUnitType::getShortName
     *
     * @access public
     * @param string $locale optional, default is the current locale code
     * @param boolean $useDefaultLocaleIfEmpty determine if the getter must
     * return the translation in the DEFAULT_LOCALE if no translation is found
     * in the current locale.
     * @return string
     */
    public function getShortName($locale=false, $defaultLocaleIfEmpty=true) {
        $locale = $locale !== false ? $locale : I18N::getLocaleCode();
        if (is_int($this->_ShortName) && $this->_ShortName > 0) {
            $this->_ShortName = Object::load('I18nString', $this->_ShortName);
        }
        $ret = null;
        if ($this->_ShortName instanceof I18nString) {
            $getter = 'getStringValue_' . $locale;
            $ret = $this->_ShortName->$getter();
            if ($ret == null && $defaultLocaleIfEmpty) {
                $getter = 'getStringValue_' . LOCALE_DEFAULT;
                $ret = $this->_ShortName->$getter();
            }
        }
        return $ret;
    }

    /**
     * SellUnitType::getShortNameId
     *
     * @access public
     * @return integer
     */
    public function getShortNameId() {
        if ($this->_ShortName instanceof I18nString) {
            return $this->_ShortName->getId();
        }
        return (int)$this->_ShortName;
    }

    /**
     * SellUnitType::setShortName
     *
     * @access public
     * @param string $value
     * @param string $locale optional, default is the current locale code
     * @return void
     */
    public function setShortName($value, $locale=false) {
        if (is_numeric($value)) {
            $this->_ShortName = (int)$value;
        } else if ($value instanceof I18nString) {
            $this->_ShortName = $value;
        } else {
            $locale = $locale !== false ? $locale : I18N::getLocaleCode();
            if (!($this->_ShortName instanceof I18nString)) {
                $this->_ShortName = Object::load('I18nString', $this->_ShortName);
                if (!($this->_ShortName instanceof I18nString)) {
                    $this->_ShortName = new I18nString();
                }
            }
            $setter = 'setStringValue_'.$locale;
            $this->_ShortName->$setter($value);
            $this->_ShortName->save();
        }
    }

    // }}}
    // LongName i18n_string property + getter/setter {{{

    /**
     * LongName foreignkey
     *
     * @access private
     * @var mixed object I18nString or integer
     */
    private $_LongName = 0;

    /**
     * SellUnitType::getLongName
     *
     * @access public
     * @param string $locale optional, default is the current locale code
     * @param boolean $useDefaultLocaleIfEmpty determine if the getter must
     * return the translation in the DEFAULT_LOCALE if no translation is found
     * in the current locale.
     * @return string
     */
    public function getLongName($locale=false, $defaultLocaleIfEmpty=true) {
        $locale = $locale !== false ? $locale : I18N::getLocaleCode();
        if (is_int($this->_LongName) && $this->_LongName > 0) {
            $this->_LongName = Object::load('I18nString', $this->_LongName);
        }
        $ret = null;
        if ($this->_LongName instanceof I18nString) {
            $getter = 'getStringValue_' . $locale;
            $ret = $this->_LongName->$getter();
            if ($ret == null && $defaultLocaleIfEmpty) {
                $getter = 'getStringValue_' . LOCALE_DEFAULT;
                $ret = $this->_LongName->$getter();
            }
        }
        return $ret;
    }

    /**
     * SellUnitType::getLongNameId
     *
     * @access public
     * @return integer
     */
    public function getLongNameId() {
        if ($this->_LongName instanceof I18nString) {
            return $this->_LongName->getId();
        }
        return (int)$this->_LongName;
    }

    /**
     * SellUnitType::setLongName
     *
     * @access public
     * @param string $value
     * @param string $locale optional, default is the current locale code
     * @return void
     */
    public function setLongName($value, $locale=false) {
        if (is_numeric($value)) {
            $this->_LongName = (int)$value;
        } else if ($value instanceof I18nString) {
            $this->_LongName = $value;
        } else {
            $locale = $locale !== false ? $locale : I18N::getLocaleCode();
            if (!($this->_LongName instanceof I18nString)) {
                $this->_LongName = Object::load('I18nString', $this->_LongName);
                if (!($this->_LongName instanceof I18nString)) {
                    $this->_LongName = new I18nString();
                }
            }
            $setter = 'setStringValue_'.$locale;
            $this->_LongName->$setter($value);
            $this->_LongName->save();
        }
    }

    // }}}
    // ConstName string property + getter/setter {{{

    /**
     * ConstName string property
     *
     * @access private
     * @var string
     */
    private $_ConstName = '';

    /**
     * SellUnitType::getConstName
     *
     * @access public
     * @return string
     */
    public function getConstName() {
        return $this->_ConstName;
    }

    /**
     * SellUnitType::setConstName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setConstName($value) {
        $this->_ConstName = $value;
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
        return 'SellUnitType';
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
        return _('Selling or buying unit types');
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
            'ShortName' => Object::TYPE_I18N_STRING,
            'LongName' => Object::TYPE_I18N_STRING,
            'ConstName' => Object::TYPE_STRING);
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
            'ActorProduct'=>array(
                'linkClass'     => 'ActorProduct',
                'field'         => 'BuyUnitType',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'CostRange'=>array(
                'linkClass'     => 'CostRange',
                'field'         => 'UnitType',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'CoverType'=>array(
                'linkClass'     => 'CoverType',
                'field'         => 'UnitType',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Product'=>array(
                'linkClass'     => 'Product',
                'field'         => 'SellUnitType',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Product_1'=>array(
                'linkClass'     => 'Product',
                'field'         => 'SellUnitTypeInContainer',
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
        $return = array('ConstName');
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
        return array('grid', 'add', 'edit');
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
        $return = array(
            'ShortName'=>array(
                'label'        => _('Short name'),
                'shortlabel'   => _('Short name'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'LongName'=>array(
                'label'        => _('Long name'),
                'shortlabel'   => _('Long name'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ConstName'=>array(
                'label'        => _('Constant name (for exemple: SELLUNITTYPE_UB)'),
                'shortlabel'   => _('Constant name (for exemple: SELLUNITTYPE_UB)'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
    // toString() {{{

    /**
     * Retourne la représentation texte de l'objet
     *
     * @access public
     * @return string
     */
    public function toString() {
        return $this->getLongName();
    }

    // }}}
    // getToStringAttribute() {{{

    /**
     * Retourne le nom de l'attribut pointé par toString()
     *
     * @static
     * @access public
     * @return string
     */
    public function getToStringAttribute() {
        return 'LongName';
    }

    // }}}
}

?>