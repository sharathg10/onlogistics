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

class _TVA extends Object {
    // class constants {{{

    const TYPE_DELIVERY_EXPENSES = 1;
    const TYPE_PACKING = 2;
    const TYPE_INSURANCE = 3;
    const TYPE_OTHER = 4;
    const TYPE_NORMAL = 5;
    const TYPE_REDUCED = 6;

    // }}}
    // Constructeur {{{

    /**
     * _TVA::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // lastmodified property {{{

    /**
     * Date de dernière modification (ou création) au format Datetime mysql.
     *
     * @var string $lastModified
     * @access public
     */
     public $lastModified = 0;

    // }}}

    // Category i18n_string property + getter/setter {{{

    /**
     * Category foreignkey
     *
     * @access private
     * @var mixed object I18nString or integer
     */
    private $_Category = 0;

    /**
     * _TVA::getCategory
     *
     * @access public
     * @param string $locale optional, default is the current locale code
     * @param boolean $useDefaultLocaleIfEmpty determine if the getter must
     * return the translation in the DEFAULT_LOCALE if no translation is found
     * in the current locale.
     * @return string
     */
    public function getCategory($locale=false, $defaultLocaleIfEmpty=true) {
        $locale = $locale !== false ? $locale : I18N::getLocaleCode();
        if (is_int($this->_Category) && $this->_Category > 0) {
            $this->_Category = Object::load('I18nString', $this->_Category);
        }
        $ret = null;
        if ($this->_Category instanceof I18nString) {
            $getter = 'getStringValue_' . $locale;
            $ret = $this->_Category->$getter();
            if ($ret == null && $defaultLocaleIfEmpty) {
                $getter = 'getStringValue_' . LOCALE_DEFAULT;
                $ret = $this->_Category->$getter();
            }
        }
        return $ret;
    }

    /**
     * _TVA::getCategoryId
     *
     * @access public
     * @return integer
     */
    public function getCategoryId() {
        if ($this->_Category instanceof I18nString) {
            return $this->_Category->getId();
        }
        return (int)$this->_Category;
    }

    /**
     * _TVA::setCategory
     *
     * @access public
     * @param string $value
     * @param string $locale optional, default is the current locale code
     * @return void
     */
    public function setCategory($value, $locale=false) {
        if (is_numeric($value)) {
            $this->_Category = (int)$value;
        } else if ($value instanceof I18nString) {
            $this->_Category = $value;
        } else {
            $locale = $locale !== false ? $locale : I18N::getLocaleCode();
            if (!($this->_Category instanceof I18nString)) {
                $this->_Category = Object::load('I18nString', $this->_Category);
                if (!($this->_Category instanceof I18nString)) {
                    $this->_Category = new I18nString();
                }
            }
            $setter = 'setStringValue_'.$locale;
            $this->_Category->$setter($value);
            $this->_Category->save();
        }
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
     * _TVA::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * _TVA::setType
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
     * _TVA::getTypeConstArray
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
            _TVA::TYPE_DELIVERY_EXPENSES => _("Delivery expenses"), 
            _TVA::TYPE_PACKING => _("Packing"), 
            _TVA::TYPE_INSURANCE => _("Insurance"), 
            _TVA::TYPE_OTHER => _("Other"), 
            _TVA::TYPE_NORMAL => _("Normal"), 
            _TVA::TYPE_REDUCED => _("Reduced")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Rate float property + getter/setter {{{

    /**
     * Rate float property
     *
     * @access private
     * @var float
     */
    private $_Rate = 0;

    /**
     * _TVA::getRate
     *
     * @access public
     * @return float
     */
    public function getRate() {
        return $this->_Rate;
    }

    /**
     * _TVA::setRate
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRate($value) {
        if ($value !== null) {
            $this->_Rate = round(I18N::extractNumber($value), 2);
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
        return 'TVA';
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
        return _('VAT');
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
            'Category' => Object::TYPE_I18N_STRING,
            'Type' => Object::TYPE_CONST,
            'Rate' => Object::TYPE_DECIMAL);
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
                'field'         => 'TVA',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'CommandItem'=>array(
                'linkClass'     => 'CommandItem',
                'field'         => 'TVA',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'FlowItem'=>array(
                'linkClass'     => 'FlowItem',
                'field'         => 'TVA',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'FlowTypeItem'=>array(
                'linkClass'     => 'FlowTypeItem',
                'field'         => 'TVA',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'InvoiceItem'=>array(
                'linkClass'     => 'InvoiceItem',
                'field'         => 'TVA',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Prestation'=>array(
                'linkClass'     => 'Prestation',
                'field'         => 'TVA',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Product'=>array(
                'linkClass'     => 'Product',
                'field'         => 'TVA',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ToHave'=>array(
                'linkClass'     => 'ToHave',
                'field'         => 'TVA',
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
        $return = array('Type');
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
            'Category'=>array(
                'label'        => _('Name'),
                'shortlabel'   => _('Name'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Type'=>array(
                'label'        => _('Type'),
                'shortlabel'   => _('Type'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Rate'=>array(
                'label'        => _('Rate'),
                'shortlabel'   => _('Rate'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 2
            ));
        return $return;
    }

    // }}}
}

?>