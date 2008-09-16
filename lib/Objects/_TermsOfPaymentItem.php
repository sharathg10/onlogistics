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
 * @version   SVN: $Id: SiteAddEdit.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * _TermsOfPaymentItem class
 *
 */
class _TermsOfPaymentItem extends Object {
    // class constants {{{

    const NET = 1;
    const END_OF_MONTH = 2;
    const END_OF_NEXT_MONTH = 3;
    const ORDER = 5;
    const DELIVERY = 6;
    const CHECK = 0;
    const CASH = 1;
    const CREDIT_CARD = 2;
    const DRAFT = 3;
    const TRANSFER = 4;
    const ASSETS = 5;
    const PROMISSORY_NOTE = 6;

    // }}}
    // Constructeur {{{

    /**
     * _TermsOfPaymentItem::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // PercentOfTotal float property + getter/setter {{{

    /**
     * PercentOfTotal float property
     *
     * @access private
     * @var float
     */
    private $_PercentOfTotal = null;

    /**
     * _TermsOfPaymentItem::getPercentOfTotal
     *
     * @access public
     * @return float
     */
    public function getPercentOfTotal() {
        return $this->_PercentOfTotal;
    }

    /**
     * _TermsOfPaymentItem::setPercentOfTotal
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPercentOfTotal($value) {
        $this->_PercentOfTotal = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // PaymentDelay string property + getter/setter {{{

    /**
     * PaymentDelay int property
     *
     * @access private
     * @var integer
     */
    private $_PaymentDelay = 0;

    /**
     * _TermsOfPaymentItem::getPaymentDelay
     *
     * @access public
     * @return integer
     */
    public function getPaymentDelay() {
        return $this->_PaymentDelay;
    }

    /**
     * _TermsOfPaymentItem::setPaymentDelay
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPaymentDelay($value) {
        if ($value !== null) {
            $this->_PaymentDelay = (int)$value;
        }
    }

    // }}}
    // PaymentOption const property + getter/setter/getPaymentOptionConstArray {{{

    /**
     * PaymentOption int property
     *
     * @access private
     * @var integer
     */
    private $_PaymentOption = 0;

    /**
     * _TermsOfPaymentItem::getPaymentOption
     *
     * @access public
     * @return integer
     */
    public function getPaymentOption() {
        return $this->_PaymentOption;
    }

    /**
     * _TermsOfPaymentItem::setPaymentOption
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPaymentOption($value) {
        if ($value !== null) {
            $this->_PaymentOption = (int)$value;
        }
    }

    /**
     * _TermsOfPaymentItem::getPaymentOptionConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getPaymentOptionConstArray($keys = false) {
        $array = array(
            _TermsOfPaymentItem::NET => _("Net"), 
            _TermsOfPaymentItem::END_OF_MONTH => _("End of month"), 
            _TermsOfPaymentItem::END_OF_NEXT_MONTH => _("End of next month")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // PaymentEvent const property + getter/setter/getPaymentEventConstArray {{{

    /**
     * PaymentEvent int property
     *
     * @access private
     * @var integer
     */
    private $_PaymentEvent = 0;

    /**
     * _TermsOfPaymentItem::getPaymentEvent
     *
     * @access public
     * @return integer
     */
    public function getPaymentEvent() {
        return $this->_PaymentEvent;
    }

    /**
     * _TermsOfPaymentItem::setPaymentEvent
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPaymentEvent($value) {
        if ($value !== null) {
            $this->_PaymentEvent = (int)$value;
        }
    }

    /**
     * _TermsOfPaymentItem::getPaymentEventConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getPaymentEventConstArray($keys = false) {
        $array = array(
            _TermsOfPaymentItem::ORDER => _("Order"), 
            _TermsOfPaymentItem::DELIVERY => _("Delivery")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // PaymentModality const property + getter/setter/getPaymentModalityConstArray {{{

    /**
     * PaymentModality int property
     *
     * @access private
     * @var integer
     */
    private $_PaymentModality = 0;

    /**
     * _TermsOfPaymentItem::getPaymentModality
     *
     * @access public
     * @return integer
     */
    public function getPaymentModality() {
        return $this->_PaymentModality;
    }

    /**
     * _TermsOfPaymentItem::setPaymentModality
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPaymentModality($value) {
        if ($value !== null) {
            $this->_PaymentModality = (int)$value;
        }
    }

    /**
     * _TermsOfPaymentItem::getPaymentModalityConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getPaymentModalityConstArray($keys = false) {
        $array = array(
            _TermsOfPaymentItem::CHECK => _("Check"), 
            _TermsOfPaymentItem::CASH => _("Cash"), 
            _TermsOfPaymentItem::CREDIT_CARD => _("Credit card"), 
            _TermsOfPaymentItem::DRAFT => _("Draft"), 
            _TermsOfPaymentItem::TRANSFER => _("Transfer"), 
            _TermsOfPaymentItem::ASSETS => _("Assets"), 
            _TermsOfPaymentItem::PROMISSORY_NOTE => _("Promissory note")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // TermsOfPayment foreignkey property + getter/setter {{{

    /**
     * TermsOfPayment foreignkey
     *
     * @access private
     * @var mixed object TermsOfPayment or integer
     */
    private $_TermsOfPayment = false;

    /**
     * _TermsOfPaymentItem::getTermsOfPayment
     *
     * @access public
     * @return object TermsOfPayment
     */
    public function getTermsOfPayment() {
        if (is_int($this->_TermsOfPayment) && $this->_TermsOfPayment > 0) {
            $mapper = Mapper::singleton('TermsOfPayment');
            $this->_TermsOfPayment = $mapper->load(
                array('Id'=>$this->_TermsOfPayment));
        }
        return $this->_TermsOfPayment;
    }

    /**
     * _TermsOfPaymentItem::getTermsOfPaymentId
     *
     * @access public
     * @return integer
     */
    public function getTermsOfPaymentId() {
        if ($this->_TermsOfPayment instanceof TermsOfPayment) {
            return $this->_TermsOfPayment->getId();
        }
        return (int)$this->_TermsOfPayment;
    }

    /**
     * _TermsOfPaymentItem::setTermsOfPayment
     *
     * @access public
     * @param object TermsOfPayment $value
     * @return void
     */
    public function setTermsOfPayment($value) {
        if (is_numeric($value)) {
            $this->_TermsOfPayment = (int)$value;
        } else {
            $this->_TermsOfPayment = $value;
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
        return 'TermsOfPaymentItem';
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
        return _('Terms of payment item');
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
            'PercentOfTotal' => Object::TYPE_DECIMAL,
            'PaymentDelay' => Object::TYPE_INT,
            'PaymentOption' => Object::TYPE_CONST,
            'PaymentEvent' => Object::TYPE_CONST,
            'PaymentModality' => Object::TYPE_CONST,
            'TermsOfPayment' => 'TermsOfPayment');
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
        return array('add', 'edit', 'del', 'grid', 'searchform');
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
            'PercentOfTotal'=>array(
                'label'        => _('Percent of total amount incl. taxes'),
                'shortlabel'   => _('Percent of total amount incl. taxes'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 2
            ),
            'PaymentDelay'=>array(
                'label'        => _('Delay in days'),
                'shortlabel'   => _('Delay in days'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'PaymentOption'=>array(
                'label'        => _('Option'),
                'shortlabel'   => _('Option'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'PaymentEvent'=>array(
                'label'        => _('Event'),
                'shortlabel'   => _('Event'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'PaymentModality'=>array(
                'label'        => _('Modality'),
                'shortlabel'   => _('Modality'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
}

?>