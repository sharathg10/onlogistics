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
 * _Invoice class
 *
 */
class _Invoice extends AbstractDocument {
    
    // Constructeur {{{

    /**
     * _Invoice::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Port float property + getter/setter {{{

    /**
     * Port float property
     *
     * @access private
     * @var float
     */
    private $_Port = 0;

    /**
     * _Invoice::getPort
     *
     * @access public
     * @return float
     */
    public function getPort() {
        return $this->_Port;
    }

    /**
     * _Invoice::setPort
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPort($value) {
        if ($value !== null) {
            $this->_Port = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Packing float property + getter/setter {{{

    /**
     * Packing float property
     *
     * @access private
     * @var float
     */
    private $_Packing = 0;

    /**
     * _Invoice::getPacking
     *
     * @access public
     * @return float
     */
    public function getPacking() {
        return $this->_Packing;
    }

    /**
     * _Invoice::setPacking
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPacking($value) {
        if ($value !== null) {
            $this->_Packing = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Insurance float property + getter/setter {{{

    /**
     * Insurance float property
     *
     * @access private
     * @var float
     */
    private $_Insurance = 0;

    /**
     * _Invoice::getInsurance
     *
     * @access public
     * @return float
     */
    public function getInsurance() {
        return $this->_Insurance;
    }

    /**
     * _Invoice::setInsurance
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setInsurance($value) {
        if ($value !== null) {
            $this->_Insurance = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // GlobalHanding float property + getter/setter {{{

    /**
     * GlobalHanding float property
     *
     * @access private
     * @var float
     */
    private $_GlobalHanding = 0;

    /**
     * _Invoice::getGlobalHanding
     *
     * @access public
     * @return float
     */
    public function getGlobalHanding() {
        return $this->_GlobalHanding;
    }

    /**
     * _Invoice::setGlobalHanding
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setGlobalHanding($value) {
        if ($value !== null) {
            $this->_GlobalHanding = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // TotalPriceHT float property + getter/setter {{{

    /**
     * TotalPriceHT float property
     *
     * @access private
     * @var float
     */
    private $_TotalPriceHT = 0;

    /**
     * _Invoice::getTotalPriceHT
     *
     * @access public
     * @return float
     */
    public function getTotalPriceHT() {
        return $this->_TotalPriceHT;
    }

    /**
     * _Invoice::setTotalPriceHT
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTotalPriceHT($value) {
        if ($value !== null) {
            $this->_TotalPriceHT = round(I18N::extractNumber($value), 2);
        }
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
     * _Invoice::getTotalPriceTTC
     *
     * @access public
     * @return float
     */
    public function getTotalPriceTTC() {
        return $this->_TotalPriceTTC;
    }

    /**
     * _Invoice::setTotalPriceTTC
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
    // ToPay float property + getter/setter {{{

    /**
     * ToPay float property
     *
     * @access private
     * @var float
     */
    private $_ToPay = 0;

    /**
     * _Invoice::getToPay
     *
     * @access public
     * @return float
     */
    public function getToPay() {
        return $this->_ToPay;
    }

    /**
     * _Invoice::setToPay
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setToPay($value) {
        if ($value !== null) {
            $this->_ToPay = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // PaymentDate datetime property + getter/setter {{{

    /**
     * PaymentDate int property
     *
     * @access private
     * @var string
     */
    private $_PaymentDate = 0;

    /**
     * _Invoice::getPaymentDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getPaymentDate($format = false) {
        return $this->dateFormat($this->_PaymentDate, $format);
    }

    /**
     * _Invoice::setPaymentDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPaymentDate($value) {
        $this->_PaymentDate = $value;
    }

    // }}}
    // Comment string property + getter/setter {{{

    /**
     * Comment string property
     *
     * @access private
     * @var string
     */
    private $_Comment = '';

    /**
     * _Invoice::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * _Invoice::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // TvaSurtaxRate float property + getter/setter {{{

    /**
     * TvaSurtaxRate float property
     *
     * @access private
     * @var float
     */
    private $_TvaSurtaxRate = 0;

    /**
     * _Invoice::getTvaSurtaxRate
     *
     * @access public
     * @return float
     */
    public function getTvaSurtaxRate() {
        return $this->_TvaSurtaxRate;
    }

    /**
     * _Invoice::setTvaSurtaxRate
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTvaSurtaxRate($value) {
        if ($value !== null) {
            $this->_TvaSurtaxRate = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // FodecTaxRate float property + getter/setter {{{

    /**
     * FodecTaxRate float property
     *
     * @access private
     * @var float
     */
    private $_FodecTaxRate = 0;

    /**
     * _Invoice::getFodecTaxRate
     *
     * @access public
     * @return float
     */
    public function getFodecTaxRate() {
        return $this->_FodecTaxRate;
    }

    /**
     * _Invoice::setFodecTaxRate
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setFodecTaxRate($value) {
        if ($value !== null) {
            $this->_FodecTaxRate = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // TaxStamp float property + getter/setter {{{

    /**
     * TaxStamp float property
     *
     * @access private
     * @var float
     */
    private $_TaxStamp = 0;

    /**
     * _Invoice::getTaxStamp
     *
     * @access public
     * @return float
     */
    public function getTaxStamp() {
        return $this->_TaxStamp;
    }

    /**
     * _Invoice::setTaxStamp
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTaxStamp($value) {
        if ($value !== null) {
            $this->_TaxStamp = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // CommercialCommissionPercent float property + getter/setter {{{

    /**
     * CommercialCommissionPercent float property
     *
     * @access private
     * @var float
     */
    private $_CommercialCommissionPercent = 0;

    /**
     * _Invoice::getCommercialCommissionPercent
     *
     * @access public
     * @return float
     */
    public function getCommercialCommissionPercent() {
        return $this->_CommercialCommissionPercent;
    }

    /**
     * _Invoice::setCommercialCommissionPercent
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCommercialCommissionPercent($value) {
        if ($value !== null) {
            $this->_CommercialCommissionPercent = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // CommercialCommissionAmount float property + getter/setter {{{

    /**
     * CommercialCommissionAmount float property
     *
     * @access private
     * @var float
     */
    private $_CommercialCommissionAmount = 0;

    /**
     * _Invoice::getCommercialCommissionAmount
     *
     * @access public
     * @return float
     */
    public function getCommercialCommissionAmount() {
        return $this->_CommercialCommissionAmount;
    }

    /**
     * _Invoice::setCommercialCommissionAmount
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCommercialCommissionAmount($value) {
        if ($value !== null) {
            $this->_CommercialCommissionAmount = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // ActivatedChainOperation one to many relation + getter/setter {{{

    /**
     * ActivatedChainOperation 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActivatedChainOperationCollection = false;

    /**
     * _Invoice::getActivatedChainOperationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedChainOperationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Invoice');
            return $mapper->getOneToMany($this->getId(),
                'ActivatedChainOperation', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedChainOperationCollection) {
            $mapper = Mapper::singleton('Invoice');
            $this->_ActivatedChainOperationCollection = $mapper->getOneToMany($this->getId(),
                'ActivatedChainOperation');
        }
        return $this->_ActivatedChainOperationCollection;
    }

    /**
     * _Invoice::getActivatedChainOperationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActivatedChainOperationCollectionIds($filter = array()) {
        $col = $this->getActivatedChainOperationCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Invoice::setActivatedChainOperationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedChainOperationCollection($value) {
        $this->_ActivatedChainOperationCollection = $value;
    }

    // }}}
    // DeliveryOrder one to many relation + getter/setter {{{

    /**
     * DeliveryOrder 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_DeliveryOrderCollection = false;

    /**
     * _Invoice::getDeliveryOrderCollection
     *
     * @access public
     * @return object Collection
     */
    public function getDeliveryOrderCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Invoice');
            return $mapper->getOneToMany($this->getId(),
                'DeliveryOrder', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_DeliveryOrderCollection) {
            $mapper = Mapper::singleton('Invoice');
            $this->_DeliveryOrderCollection = $mapper->getOneToMany($this->getId(),
                'DeliveryOrder');
        }
        return $this->_DeliveryOrderCollection;
    }

    /**
     * _Invoice::getDeliveryOrderCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getDeliveryOrderCollectionIds($filter = array()) {
        $col = $this->getDeliveryOrderCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Invoice::setDeliveryOrderCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setDeliveryOrderCollection($value) {
        $this->_DeliveryOrderCollection = $value;
    }

    // }}}
    // InvoiceItem one to many relation + getter/setter {{{

    /**
     * InvoiceItem 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_InvoiceItemCollection = false;

    /**
     * _Invoice::getInvoiceItemCollection
     *
     * @access public
     * @return object Collection
     */
    public function getInvoiceItemCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Invoice');
            return $mapper->getOneToMany($this->getId(),
                'InvoiceItem', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_InvoiceItemCollection) {
            $mapper = Mapper::singleton('Invoice');
            $this->_InvoiceItemCollection = $mapper->getOneToMany($this->getId(),
                'InvoiceItem');
        }
        return $this->_InvoiceItemCollection;
    }

    /**
     * _Invoice::getInvoiceItemCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getInvoiceItemCollectionIds($filter = array()) {
        $col = $this->getInvoiceItemCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Invoice::setInvoiceItemCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setInvoiceItemCollection($value) {
        $this->_InvoiceItemCollection = $value;
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
     * _Invoice::getInvoicePaymentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getInvoicePaymentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Invoice');
            return $mapper->getOneToMany($this->getId(),
                'InvoicePayment', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_InvoicePaymentCollection) {
            $mapper = Mapper::singleton('Invoice');
            $this->_InvoicePaymentCollection = $mapper->getOneToMany($this->getId(),
                'InvoicePayment');
        }
        return $this->_InvoicePaymentCollection;
    }

    /**
     * _Invoice::getInvoicePaymentCollectionIds
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
     * _Invoice::setInvoicePaymentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setInvoicePaymentCollection($value) {
        $this->_InvoicePaymentCollection = $value;
    }

    // }}}
    // LocationExecutedMovement one to many relation + getter/setter {{{

    /**
     * LocationExecutedMovement 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LocationExecutedMovementCollection = false;

    /**
     * _Invoice::getLocationExecutedMovementCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLocationExecutedMovementCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Invoice');
            return $mapper->getOneToMany($this->getId(),
                'LocationExecutedMovement', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LocationExecutedMovementCollection) {
            $mapper = Mapper::singleton('Invoice');
            $this->_LocationExecutedMovementCollection = $mapper->getOneToMany($this->getId(),
                'LocationExecutedMovement');
        }
        return $this->_LocationExecutedMovementCollection;
    }

    /**
     * _Invoice::getLocationExecutedMovementCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLocationExecutedMovementCollectionIds($filter = array()) {
        $col = $this->getLocationExecutedMovementCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Invoice::setLocationExecutedMovementCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLocationExecutedMovementCollection($value) {
        $this->_LocationExecutedMovementCollection = $value;
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
        return 'AbstractDocument';
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
    public static function getProperties($ownOnly = false) {
        $return = array(
            'Port' => Object::TYPE_DECIMAL,
            'Packing' => Object::TYPE_DECIMAL,
            'Insurance' => Object::TYPE_DECIMAL,
            'GlobalHanding' => Object::TYPE_DECIMAL,
            'TotalPriceHT' => Object::TYPE_DECIMAL,
            'TotalPriceTTC' => Object::TYPE_DECIMAL,
            'ToPay' => Object::TYPE_DECIMAL,
            'PaymentDate' => Object::TYPE_DATETIME,
            'Comment' => Object::TYPE_TEXT,
            'TvaSurtaxRate' => Object::TYPE_DECIMAL,
            'FodecTaxRate' => Object::TYPE_DECIMAL,
            'TaxStamp' => Object::TYPE_DECIMAL,
            'CommercialCommissionPercent' => Object::TYPE_DECIMAL,
            'CommercialCommissionAmount' => Object::TYPE_DECIMAL);
        return $ownOnly?$return:array_merge(parent::getProperties(), $return);
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
    public static function getLinks($ownOnly = false) {
        $return = array(
            'ActivatedChainOperation'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'InvoicePrestation',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Box'=>array(
                'linkClass'     => 'Box',
                'field'         => 'InvoicePrestation',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'DeliveryOrder'=>array(
                'linkClass'     => 'DeliveryOrder',
                'field'         => 'Invoice',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'InvoiceItem'=>array(
                'linkClass'     => 'InvoiceItem',
                'field'         => 'Invoice',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'InvoicePayment'=>array(
                'linkClass'     => 'InvoicePayment',
                'field'         => 'Invoice',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'LocationExecutedMovement'=>array(
                'linkClass'     => 'LocationExecutedMovement',
                'field'         => 'InvoicePrestation',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ));
        return $ownOnly?$return:array_merge(parent::getLinks(), $return);
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
        return array_merge(parent::getUniqueProperties(), $return);
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
        return array_merge(parent::getEmptyForDeleteProperties(), $return);
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
    public static function getMapping($ownOnly = false) {
        $return = array();
        return $ownOnly?$return:array_merge(parent::getMapping(), $return);
    }

    // }}}
    // useInheritance() {{{

    /**
     * Détermine si l'entité est une entité qui utilise l'héritage.
     * (classe parente ou classe fille). Ceci afin de differencier les entités
     * dans le mapper car classes filles et parentes sont mappées dans la même
     * table.
     *
     * @static
     * @access public
     * @return bool
     */
    public static function useInheritance() {
        return true;
    }

    // }}}
    // getParentClassName() {{{

    /**
     * Retourne le nom de la première classe parente
     *
     * @static
     * @access public
     * @return string
     */
    public static function getParentClassName() {
        return 'AbstractDocument';
    }

    // }}}
}

?>