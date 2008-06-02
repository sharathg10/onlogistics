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

class AnnualTurnoverDiscount extends Object {
    
    // Constructeur {{{

    /**
     * AnnualTurnoverDiscount::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Amount float property + getter/setter {{{

    /**
     * Amount float property
     *
     * @access private
     * @var float
     */
    private $_Amount = null;

    /**
     * AnnualTurnoverDiscount::getAmount
     *
     * @access public
     * @return float
     */
    public function getAmount() {
        return $this->_Amount;
    }

    /**
     * AnnualTurnoverDiscount::setAmount
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setAmount($value) {
        $this->_Amount = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // Year int property + getter/setter {{{

    /**
     * Year int property
     *
     * @access private
     * @var integer
     */
    private $_Year = null;

    /**
     * AnnualTurnoverDiscount::getYear
     *
     * @access public
     * @return integer
     */
    public function getYear() {
        return $this->_Year;
    }

    /**
     * AnnualTurnoverDiscount::setYear
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setYear($value) {
        $this->_Year = ($value===null || $value === '')?null:(int)$value;
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
     * AnnualTurnoverDiscount::getSupplierCustomer
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
     * AnnualTurnoverDiscount::getSupplierCustomerId
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
     * AnnualTurnoverDiscount::setSupplierCustomer
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
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'AnnualTurnoverDiscount';
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
            'Amount' => Object::TYPE_DECIMAL,
            'Year' => Object::TYPE_INT,
            'SupplierCustomer' => 'SupplierCustomer');
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