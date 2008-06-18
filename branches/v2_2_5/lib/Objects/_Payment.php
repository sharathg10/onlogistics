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

class _Payment extends Object {
    
    // Constructeur {{{

    /**
     * _Payment::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Date datetime property + getter/setter {{{

    /**
     * Date int property
     *
     * @access private
     * @var string
     */
    private $_Date = 0;

    /**
     * _Payment::getDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getDate($format = false) {
        return $this->dateFormat($this->_Date, $format);
    }

    /**
     * _Payment::setDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDate($value) {
        $this->_Date = $value;
    }

    // }}}
    // Modality string property + getter/setter {{{

    /**
     * Modality int property
     *
     * @access private
     * @var integer
     */
    private $_Modality = 0;

    /**
     * _Payment::getModality
     *
     * @access public
     * @return integer
     */
    public function getModality() {
        return $this->_Modality;
    }

    /**
     * _Payment::setModality
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setModality($value) {
        if ($value !== null) {
            $this->_Modality = (int)$value;
        }
    }

    // }}}
    // Reference string property + getter/setter {{{

    /**
     * Reference string property
     *
     * @access private
     * @var string
     */
    private $_Reference = '';

    /**
     * _Payment::getReference
     *
     * @access public
     * @return string
     */
    public function getReference() {
        return $this->_Reference;
    }

    /**
     * _Payment::setReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setReference($value) {
        $this->_Reference = $value;
    }

    // }}}
    // TotalPriceTTC float property + getter/setter {{{

    /**
     * TotalPriceTTC float property
     *
     * @access private
     * @var float
     */
    private $_TotalPriceTTC = 0;

    /**
     * _Payment::getTotalPriceTTC
     *
     * @access public
     * @return float
     */
    public function getTotalPriceTTC() {
        return $this->_TotalPriceTTC;
    }

    /**
     * _Payment::setTotalPriceTTC
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTotalPriceTTC($value) {
        if ($value !== null) {
            $this->_TotalPriceTTC = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // CancellationDate datetime property + getter/setter {{{

    /**
     * CancellationDate int property
     *
     * @access private
     * @var string
     */
    private $_CancellationDate = 0;

    /**
     * _Payment::getCancellationDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getCancellationDate($format = false) {
        return $this->dateFormat($this->_CancellationDate, $format);
    }

    /**
     * _Payment::setCancellationDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCancellationDate($value) {
        $this->_CancellationDate = $value;
    }

    // }}}
    // ActorBankDetail foreignkey property + getter/setter {{{

    /**
     * ActorBankDetail foreignkey
     *
     * @access private
     * @var mixed object ActorBankDetail or integer
     */
    private $_ActorBankDetail = false;

    /**
     * _Payment::getActorBankDetail
     *
     * @access public
     * @return object ActorBankDetail
     */
    public function getActorBankDetail() {
        if (is_int($this->_ActorBankDetail) && $this->_ActorBankDetail > 0) {
            $mapper = Mapper::singleton('ActorBankDetail');
            $this->_ActorBankDetail = $mapper->load(
                array('Id'=>$this->_ActorBankDetail));
        }
        return $this->_ActorBankDetail;
    }

    /**
     * _Payment::getActorBankDetailId
     *
     * @access public
     * @return integer
     */
    public function getActorBankDetailId() {
        if ($this->_ActorBankDetail instanceof ActorBankDetail) {
            return $this->_ActorBankDetail->getId();
        }
        return (int)$this->_ActorBankDetail;
    }

    /**
     * _Payment::setActorBankDetail
     *
     * @access public
     * @param object ActorBankDetail $value
     * @return void
     */
    public function setActorBankDetail($value) {
        if (is_numeric($value)) {
            $this->_ActorBankDetail = (int)$value;
        } else {
            $this->_ActorBankDetail = $value;
        }
    }

    // }}}
    // InvoicePayment one to many relation + getter/setter {{{

    /**
     * InvoicePayment 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_InvoicePaymentCollection = false;

    /**
     * _Payment::getInvoicePaymentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getInvoicePaymentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Payment');
            return $mapper->getOneToMany($this->getId(),
                'InvoicePayment', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_InvoicePaymentCollection) {
            $mapper = Mapper::singleton('Payment');
            $this->_InvoicePaymentCollection = $mapper->getOneToMany($this->getId(),
                'InvoicePayment');
        }
        return $this->_InvoicePaymentCollection;
    }

    /**
     * _Payment::getInvoicePaymentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getInvoicePaymentCollectionIds($filter = array()) {
        $col = $this->getInvoicePaymentCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Payment::setInvoicePaymentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setInvoicePaymentCollection($value) {
        $this->_InvoicePaymentCollection = $value;
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
        return 'Payment';
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
            'Date' => Object::TYPE_DATETIME,
            'Modality' => Object::TYPE_INT,
            'Reference' => Object::TYPE_STRING,
            'TotalPriceTTC' => Object::TYPE_DECIMAL,
            'CancellationDate' => Object::TYPE_DATETIME,
            'ActorBankDetail' => 'ActorBankDetail');
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
            'InvoicePayment'=>array(
                'linkClass'     => 'InvoicePayment',
                'field'         => 'Payment',
                'ondelete'      => 'cascade',
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