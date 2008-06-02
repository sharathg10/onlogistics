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

class CurrencyConverter extends Object {
    
    // Constructeur {{{

    /**
     * CurrencyConverter::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // FromCurrency foreignkey property + getter/setter {{{

    /**
     * FromCurrency foreignkey
     *
     * @access private
     * @var mixed object Currency or integer
     */
    private $_FromCurrency = false;

    /**
     * CurrencyConverter::getFromCurrency
     *
     * @access public
     * @return object Currency
     */
    public function getFromCurrency() {
        if (is_int($this->_FromCurrency) && $this->_FromCurrency > 0) {
            $mapper = Mapper::singleton('Currency');
            $this->_FromCurrency = $mapper->load(
                array('Id'=>$this->_FromCurrency));
        }
        return $this->_FromCurrency;
    }

    /**
     * CurrencyConverter::getFromCurrencyId
     *
     * @access public
     * @return integer
     */
    public function getFromCurrencyId() {
        if ($this->_FromCurrency instanceof Currency) {
            return $this->_FromCurrency->getId();
        }
        return (int)$this->_FromCurrency;
    }

    /**
     * CurrencyConverter::setFromCurrency
     *
     * @access public
     * @param object Currency $value
     * @return void
     */
    public function setFromCurrency($value) {
        if (is_numeric($value)) {
            $this->_FromCurrency = (int)$value;
        } else {
            $this->_FromCurrency = $value;
        }
    }

    // }}}
    // ToCurrency foreignkey property + getter/setter {{{

    /**
     * ToCurrency foreignkey
     *
     * @access private
     * @var mixed object Currency or integer
     */
    private $_ToCurrency = false;

    /**
     * CurrencyConverter::getToCurrency
     *
     * @access public
     * @return object Currency
     */
    public function getToCurrency() {
        if (is_int($this->_ToCurrency) && $this->_ToCurrency > 0) {
            $mapper = Mapper::singleton('Currency');
            $this->_ToCurrency = $mapper->load(
                array('Id'=>$this->_ToCurrency));
        }
        return $this->_ToCurrency;
    }

    /**
     * CurrencyConverter::getToCurrencyId
     *
     * @access public
     * @return integer
     */
    public function getToCurrencyId() {
        if ($this->_ToCurrency instanceof Currency) {
            return $this->_ToCurrency->getId();
        }
        return (int)$this->_ToCurrency;
    }

    /**
     * CurrencyConverter::setToCurrency
     *
     * @access public
     * @param object Currency $value
     * @return void
     */
    public function setToCurrency($value) {
        if (is_numeric($value)) {
            $this->_ToCurrency = (int)$value;
        } else {
            $this->_ToCurrency = $value;
        }
    }

    // }}}
    // BeginDate datetime property + getter/setter {{{

    /**
     * BeginDate int property
     *
     * @access private
     * @var string
     */
    private $_BeginDate = 0;

    /**
     * CurrencyConverter::getBeginDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getBeginDate($format = false) {
        return $this->dateFormat($this->_BeginDate, $format);
    }

    /**
     * CurrencyConverter::setBeginDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBeginDate($value) {
        $this->_BeginDate = $value;
    }

    // }}}
    // EndDate datetime property + getter/setter {{{

    /**
     * EndDate int property
     *
     * @access private
     * @var string
     */
    private $_EndDate = 0;

    /**
     * CurrencyConverter::getEndDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEndDate($format = false) {
        return $this->dateFormat($this->_EndDate, $format);
    }

    /**
     * CurrencyConverter::setEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndDate($value) {
        $this->_EndDate = $value;
    }

    // }}}
    // Rate float property + getter/setter {{{

    /**
     * Rate float property
     *
     * @access private
     * @var float
     */
    private $_Rate = 1;

    /**
     * CurrencyConverter::getRate
     *
     * @access public
     * @return float
     */
    public function getRate() {
        return $this->_Rate;
    }

    /**
     * CurrencyConverter::setRate
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRate($value) {
        if ($value !== null) {
            $this->_Rate = round(I18N::extractNumber($value), 6);
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
        return 'CurrencyConverter';
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
        return _('Currency converter');
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
            'FromCurrency' => 'Currency',
            'ToCurrency' => 'Currency',
            'BeginDate' => Object::TYPE_DATE,
            'EndDate' => Object::TYPE_DATE,
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
        $return = array();
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
        return array('searchform', 'grid', 'add', 'edit', 'del');
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
            'FromCurrency'=>array(
                'label'        => _('From currency'),
                'shortlabel'   => _('From currency'),
                'usedby'       => array('grid', 'addedit', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ToCurrency'=>array(
                'label'        => _('To currency'),
                'shortlabel'   => _('To currency'),
                'usedby'       => array('grid', 'addedit', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'BeginDate'=>array(
                'label'        => _('Validity begin'),
                'shortlabel'   => _('Validity begin'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'EndDate'=>array(
                'label'        => _('Validity end'),
                'shortlabel'   => _('Validity end'),
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
                'dec_num'      => 6
            ));
        return $return;
    }

    // }}}
}

?>