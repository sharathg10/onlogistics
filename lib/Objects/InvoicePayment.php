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

class InvoicePayment extends Object {
    
    // Constructeur {{{

    /**
     * InvoicePayment::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // PriceTTC float property + getter/setter {{{

    /**
     * PriceTTC float property
     *
     * @access private
     * @var float
     */
    private $_PriceTTC = 0;

    /**
     * InvoicePayment::getPriceTTC
     *
     * @access public
     * @return float
     */
    public function getPriceTTC() {
        return $this->_PriceTTC;
    }

    /**
     * InvoicePayment::setPriceTTC
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPriceTTC($value) {
        if ($value !== null) {
            $this->_PriceTTC = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Invoice foreignkey property + getter/setter {{{

    /**
     * Invoice foreignkey
     *
     * @access private
     * @var mixed object Invoice or integer
     */
    private $_Invoice = false;

    /**
     * InvoicePayment::getInvoice
     *
     * @access public
     * @return object Invoice
     */
    public function getInvoice() {
        if (is_int($this->_Invoice) && $this->_Invoice > 0) {
            $mapper = Mapper::singleton('Invoice');
            $this->_Invoice = $mapper->load(
                array('Id'=>$this->_Invoice));
        }
        return $this->_Invoice;
    }

    /**
     * InvoicePayment::getInvoiceId
     *
     * @access public
     * @return integer
     */
    public function getInvoiceId() {
        if ($this->_Invoice instanceof Invoice) {
            return $this->_Invoice->getId();
        }
        return (int)$this->_Invoice;
    }

    /**
     * InvoicePayment::setInvoice
     *
     * @access public
     * @param object Invoice $value
     * @return void
     */
    public function setInvoice($value) {
        if (is_numeric($value)) {
            $this->_Invoice = (int)$value;
        } else {
            $this->_Invoice = $value;
        }
    }

    // }}}
    // Payment foreignkey property + getter/setter {{{

    /**
     * Payment foreignkey
     *
     * @access private
     * @var mixed object Payment or integer
     */
    private $_Payment = false;

    /**
     * InvoicePayment::getPayment
     *
     * @access public
     * @return object Payment
     */
    public function getPayment() {
        if (is_int($this->_Payment) && $this->_Payment > 0) {
            $mapper = Mapper::singleton('Payment');
            $this->_Payment = $mapper->load(
                array('Id'=>$this->_Payment));
        }
        return $this->_Payment;
    }

    /**
     * InvoicePayment::getPaymentId
     *
     * @access public
     * @return integer
     */
    public function getPaymentId() {
        if ($this->_Payment instanceof Payment) {
            return $this->_Payment->getId();
        }
        return (int)$this->_Payment;
    }

    /**
     * InvoicePayment::setPayment
     *
     * @access public
     * @param object Payment $value
     * @return void
     */
    public function setPayment($value) {
        if (is_numeric($value)) {
            $this->_Payment = (int)$value;
        } else {
            $this->_Payment = $value;
        }
    }

    // }}}
    // ToHave foreignkey property + getter/setter {{{

    /**
     * ToHave foreignkey
     *
     * @access private
     * @var mixed object ToHave or integer
     */
    private $_ToHave = false;

    /**
     * InvoicePayment::getToHave
     *
     * @access public
     * @return object ToHave
     */
    public function getToHave() {
        if (is_int($this->_ToHave) && $this->_ToHave > 0) {
            $mapper = Mapper::singleton('ToHave');
            $this->_ToHave = $mapper->load(
                array('Id'=>$this->_ToHave));
        }
        return $this->_ToHave;
    }

    /**
     * InvoicePayment::getToHaveId
     *
     * @access public
     * @return integer
     */
    public function getToHaveId() {
        if ($this->_ToHave instanceof ToHave) {
            return $this->_ToHave->getId();
        }
        return (int)$this->_ToHave;
    }

    /**
     * InvoicePayment::setToHave
     *
     * @access public
     * @param object ToHave $value
     * @return void
     */
    public function setToHave($value) {
        if (is_numeric($value)) {
            $this->_ToHave = (int)$value;
        } else {
            $this->_ToHave = $value;
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
        return 'InvoicePayment';
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
            'PriceTTC' => Object::TYPE_DECIMAL,
            'Invoice' => 'Invoice',
            'Payment' => 'Payment',
            'ToHave' => 'ToHave');
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