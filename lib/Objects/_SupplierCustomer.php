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
 * _SupplierCustomer class
 *
 */
class _SupplierCustomer extends Object {
    // class constants {{{

    const INVOICE_BY_MAIL_NONE = 0;
    const INVOICE_BY_MAIL_ALERT = 1;
    const INVOICE_BY_MAIL_YES = 2;
    const WITH_INVOICE = 0;
    const NO_INVOICE_CLOSED_AFTER_PREPARED = 1;
    const NO_INVOICE_CLOSED_AFTER_DELIVERED = 2;

    // }}}
    // Constructeur {{{

    /**
     * _SupplierCustomer::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // MaxIncur float property + getter/setter {{{

    /**
     * MaxIncur float property
     *
     * @access private
     * @var float
     */
    private $_MaxIncur = null;

    /**
     * _SupplierCustomer::getMaxIncur
     *
     * @access public
     * @return float
     */
    public function getMaxIncur() {
        return $this->_MaxIncur;
    }

    /**
     * _SupplierCustomer::setMaxIncur
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaxIncur($value) {
        $this->_MaxIncur = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // UpdateIncur float property + getter/setter {{{

    /**
     * UpdateIncur float property
     *
     * @access private
     * @var float
     */
    private $_UpdateIncur = null;

    /**
     * _SupplierCustomer::getUpdateIncur
     *
     * @access public
     * @return float
     */
    public function getUpdateIncur() {
        return $this->_UpdateIncur;
    }

    /**
     * _SupplierCustomer::setUpdateIncur
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setUpdateIncur($value) {
        $this->_UpdateIncur = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // ToHaveTTC float property + getter/setter {{{

    /**
     * ToHaveTTC float property
     *
     * @access private
     * @var float
     */
    private $_ToHaveTTC = null;

    /**
     * _SupplierCustomer::getToHaveTTC
     *
     * @access public
     * @return float
     */
    public function getToHaveTTC() {
        return $this->_ToHaveTTC;
    }

    /**
     * _SupplierCustomer::setToHaveTTC
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setToHaveTTC($value) {
        $this->_ToHaveTTC = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // InvoiceByMail const property + getter/setter/getInvoiceByMailConstArray {{{

    /**
     * InvoiceByMail int property
     *
     * @access private
     * @var integer
     */
    private $_InvoiceByMail = 0;

    /**
     * _SupplierCustomer::getInvoiceByMail
     *
     * @access public
     * @return integer
     */
    public function getInvoiceByMail() {
        return $this->_InvoiceByMail;
    }

    /**
     * _SupplierCustomer::setInvoiceByMail
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setInvoiceByMail($value) {
        if ($value !== null) {
            $this->_InvoiceByMail = (int)$value;
        }
    }

    /**
     * _SupplierCustomer::getInvoiceByMailConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getInvoiceByMailConstArray($keys = false) {
        $array = array(
            _SupplierCustomer::INVOICE_BY_MAIL_NONE => _("No mail sending"), 
            _SupplierCustomer::INVOICE_BY_MAIL_ALERT => _("Send simple alert"), 
            _SupplierCustomer::INVOICE_BY_MAIL_YES => _("Send an invoice by email")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // CustomerProductCommandBehaviour const property + getter/setter/getCustomerProductCommandBehaviourConstArray {{{

    /**
     * CustomerProductCommandBehaviour int property
     *
     * @access private
     * @var integer
     */
    private $_CustomerProductCommandBehaviour = 0;

    /**
     * _SupplierCustomer::getCustomerProductCommandBehaviour
     *
     * @access public
     * @return integer
     */
    public function getCustomerProductCommandBehaviour() {
        return $this->_CustomerProductCommandBehaviour;
    }

    /**
     * _SupplierCustomer::setCustomerProductCommandBehaviour
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCustomerProductCommandBehaviour($value) {
        if ($value !== null) {
            $this->_CustomerProductCommandBehaviour = (int)$value;
        }
    }

    /**
     * _SupplierCustomer::getCustomerProductCommandBehaviourConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getCustomerProductCommandBehaviourConstArray($keys = false) {
        $array = array(
            _SupplierCustomer::WITH_INVOICE => _("Billable"), 
            _SupplierCustomer::NO_INVOICE_CLOSED_AFTER_PREPARED => _("Not billable, closed after prepared"), 
            _SupplierCustomer::NO_INVOICE_CLOSED_AFTER_DELIVERED => _("Not billable, closed after delivery")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Supplier foreignkey property + getter/setter {{{

    /**
     * Supplier foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Supplier = false;

    /**
     * _SupplierCustomer::getSupplier
     *
     * @access public
     * @return object Actor
     */
    public function getSupplier() {
        if (is_int($this->_Supplier) && $this->_Supplier > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Supplier = $mapper->load(
                array('Id'=>$this->_Supplier));
        }
        return $this->_Supplier;
    }

    /**
     * _SupplierCustomer::getSupplierId
     *
     * @access public
     * @return integer
     */
    public function getSupplierId() {
        if ($this->_Supplier instanceof Actor) {
            return $this->_Supplier->getId();
        }
        return (int)$this->_Supplier;
    }

    /**
     * _SupplierCustomer::setSupplier
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setSupplier($value) {
        if (is_numeric($value)) {
            $this->_Supplier = (int)$value;
        } else {
            $this->_Supplier = $value;
        }
    }

    // }}}
    // Customer foreignkey property + getter/setter {{{

    /**
     * Customer foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Customer = false;

    /**
     * _SupplierCustomer::getCustomer
     *
     * @access public
     * @return object Actor
     */
    public function getCustomer() {
        if (is_int($this->_Customer) && $this->_Customer > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Customer = $mapper->load(
                array('Id'=>$this->_Customer));
        }
        return $this->_Customer;
    }

    /**
     * _SupplierCustomer::getCustomerId
     *
     * @access public
     * @return integer
     */
    public function getCustomerId() {
        if ($this->_Customer instanceof Actor) {
            return $this->_Customer->getId();
        }
        return (int)$this->_Customer;
    }

    /**
     * _SupplierCustomer::setCustomer
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setCustomer($value) {
        if (is_numeric($value)) {
            $this->_Customer = (int)$value;
        } else {
            $this->_Customer = $value;
        }
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
     * _SupplierCustomer::getTermsOfPayment
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
     * _SupplierCustomer::getTermsOfPaymentId
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
     * _SupplierCustomer::setTermsOfPayment
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
    // MaxDeliveryDay int property + getter/setter {{{

    /**
     * MaxDeliveryDay int property
     *
     * @access private
     * @var integer
     */
    private $_MaxDeliveryDay = null;

    /**
     * _SupplierCustomer::getMaxDeliveryDay
     *
     * @access public
     * @return integer
     */
    public function getMaxDeliveryDay() {
        return $this->_MaxDeliveryDay;
    }

    /**
     * _SupplierCustomer::setMaxDeliveryDay
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setMaxDeliveryDay($value) {
        $this->_MaxDeliveryDay = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // TotalDeliveryDay int property + getter/setter {{{

    /**
     * TotalDeliveryDay int property
     *
     * @access private
     * @var integer
     */
    private $_TotalDeliveryDay = null;

    /**
     * _SupplierCustomer::getTotalDeliveryDay
     *
     * @access public
     * @return integer
     */
    public function getTotalDeliveryDay() {
        return $this->_TotalDeliveryDay;
    }

    /**
     * _SupplierCustomer::setTotalDeliveryDay
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTotalDeliveryDay($value) {
        $this->_TotalDeliveryDay = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // DeliveryType int property + getter/setter {{{

    /**
     * DeliveryType int property
     *
     * @access private
     * @var integer
     */
    private $_DeliveryType = null;

    /**
     * _SupplierCustomer::getDeliveryType
     *
     * @access public
     * @return integer
     */
    public function getDeliveryType() {
        return $this->_DeliveryType;
    }

    /**
     * _SupplierCustomer::setDeliveryType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDeliveryType($value) {
        $this->_DeliveryType = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // HasTVA string property + getter/setter {{{

    /**
     * HasTVA int property
     *
     * @access private
     * @var integer
     */
    private $_HasTVA = 0;

    /**
     * _SupplierCustomer::getHasTVA
     *
     * @access public
     * @return integer
     */
    public function getHasTVA() {
        return $this->_HasTVA;
    }

    /**
     * _SupplierCustomer::setHasTVA
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setHasTVA($value) {
        if ($value !== null) {
            $this->_HasTVA = (int)$value;
        }
    }

    // }}}
    // HasTvaSurtax string property + getter/setter {{{

    /**
     * HasTvaSurtax int property
     *
     * @access private
     * @var integer
     */
    private $_HasTvaSurtax = 0;

    /**
     * _SupplierCustomer::getHasTvaSurtax
     *
     * @access public
     * @return integer
     */
    public function getHasTvaSurtax() {
        return $this->_HasTvaSurtax;
    }

    /**
     * _SupplierCustomer::setHasTvaSurtax
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setHasTvaSurtax($value) {
        if ($value !== null) {
            $this->_HasTvaSurtax = (int)$value;
        }
    }

    // }}}
    // HasFodecTax string property + getter/setter {{{

    /**
     * HasFodecTax int property
     *
     * @access private
     * @var integer
     */
    private $_HasFodecTax = 0;

    /**
     * _SupplierCustomer::getHasFodecTax
     *
     * @access public
     * @return integer
     */
    public function getHasFodecTax() {
        return $this->_HasFodecTax;
    }

    /**
     * _SupplierCustomer::setHasFodecTax
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setHasFodecTax($value) {
        if ($value !== null) {
            $this->_HasFodecTax = (int)$value;
        }
    }

    // }}}
    // HasTaxStamp string property + getter/setter {{{

    /**
     * HasTaxStamp int property
     *
     * @access private
     * @var integer
     */
    private $_HasTaxStamp = 0;

    /**
     * _SupplierCustomer::getHasTaxStamp
     *
     * @access public
     * @return integer
     */
    public function getHasTaxStamp() {
        return $this->_HasTaxStamp;
    }

    /**
     * _SupplierCustomer::setHasTaxStamp
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setHasTaxStamp($value) {
        if ($value !== null) {
            $this->_HasTaxStamp = (int)$value;
        }
    }

    // }}}
    // Factor foreignkey property + getter/setter {{{

    /**
     * Factor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Factor = false;

    /**
     * _SupplierCustomer::getFactor
     *
     * @access public
     * @return object Actor
     */
    public function getFactor() {
        if (is_int($this->_Factor) && $this->_Factor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Factor = $mapper->load(
                array('Id'=>$this->_Factor));
        }
        return $this->_Factor;
    }

    /**
     * _SupplierCustomer::getFactorId
     *
     * @access public
     * @return integer
     */
    public function getFactorId() {
        if ($this->_Factor instanceof Actor) {
            return $this->_Factor->getId();
        }
        return (int)$this->_Factor;
    }

    /**
     * _SupplierCustomer::setFactor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setFactor($value) {
        if (is_numeric($value)) {
            $this->_Factor = (int)$value;
        } else {
            $this->_Factor = $value;
        }
    }

    // }}}
    // DocumentModel one to many relation + getter/setter {{{

    /**
     * DocumentModel *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_DocumentModelCollection = false;

    /**
     * _SupplierCustomer::getDocumentModelCollection
     *
     * @access public
     * @return object Collection
     */
    public function getDocumentModelCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('SupplierCustomer');
            return $mapper->getManyToMany($this->getId(),
                'DocumentModel', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_DocumentModelCollection) {
            $mapper = Mapper::singleton('SupplierCustomer');
            $this->_DocumentModelCollection = $mapper->getManyToMany($this->getId(),
                'DocumentModel');
        }
        return $this->_DocumentModelCollection;
    }

    /**
     * _SupplierCustomer::getDocumentModelCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getDocumentModelCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getDocumentModelCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_DocumentModelCollection) {
            $mapper = Mapper::singleton('SupplierCustomer');
            return $mapper->getManyToManyIds($this->getId(), 'DocumentModel');
        }
        return $this->_DocumentModelCollection->getItemIds();
    }

    /**
     * _SupplierCustomer::setDocumentModelCollectionIds
     *
     * @access public
     * @return array
     */
    public function setDocumentModelCollectionIds($itemIds) {
        $this->_DocumentModelCollection = new Collection('DocumentModel');
        foreach ($itemIds as $id) {
            $this->_DocumentModelCollection->setItem($id);
        }
    }

    /**
     * _SupplierCustomer::setDocumentModelCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setDocumentModelCollection($value) {
        $this->_DocumentModelCollection = $value;
    }

    /**
     * _SupplierCustomer::DocumentModelCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function DocumentModelCollectionIsLoaded() {
        return ($this->_DocumentModelCollection !== false);
    }

    // }}}
    // AbstractDocument one to many relation + getter/setter {{{

    /**
     * AbstractDocument 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_AbstractDocumentCollection = false;

    /**
     * _SupplierCustomer::getAbstractDocumentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getAbstractDocumentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('SupplierCustomer');
            return $mapper->getOneToMany($this->getId(),
                'AbstractDocument', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_AbstractDocumentCollection) {
            $mapper = Mapper::singleton('SupplierCustomer');
            $this->_AbstractDocumentCollection = $mapper->getOneToMany($this->getId(),
                'AbstractDocument');
        }
        return $this->_AbstractDocumentCollection;
    }

    /**
     * _SupplierCustomer::getAbstractDocumentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getAbstractDocumentCollectionIds($filter = array()) {
        $col = $this->getAbstractDocumentCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _SupplierCustomer::setAbstractDocumentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setAbstractDocumentCollection($value) {
        $this->_AbstractDocumentCollection = $value;
    }

    // }}}
    // AnnualTurnoverDiscount one to many relation + getter/setter {{{

    /**
     * AnnualTurnoverDiscount 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_AnnualTurnoverDiscountCollection = false;

    /**
     * _SupplierCustomer::getAnnualTurnoverDiscountCollection
     *
     * @access public
     * @return object Collection
     */
    public function getAnnualTurnoverDiscountCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('SupplierCustomer');
            return $mapper->getOneToMany($this->getId(),
                'AnnualTurnoverDiscount', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_AnnualTurnoverDiscountCollection) {
            $mapper = Mapper::singleton('SupplierCustomer');
            $this->_AnnualTurnoverDiscountCollection = $mapper->getOneToMany($this->getId(),
                'AnnualTurnoverDiscount');
        }
        return $this->_AnnualTurnoverDiscountCollection;
    }

    /**
     * _SupplierCustomer::getAnnualTurnoverDiscountCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getAnnualTurnoverDiscountCollectionIds($filter = array()) {
        $col = $this->getAnnualTurnoverDiscountCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _SupplierCustomer::setAnnualTurnoverDiscountCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setAnnualTurnoverDiscountCollection($value) {
        $this->_AnnualTurnoverDiscountCollection = $value;
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
        return 'SupplierCustomer';
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
            'MaxIncur' => Object::TYPE_DECIMAL,
            'UpdateIncur' => Object::TYPE_DECIMAL,
            'ToHaveTTC' => Object::TYPE_DECIMAL,
            'InvoiceByMail' => Object::TYPE_CONST,
            'CustomerProductCommandBehaviour' => Object::TYPE_CONST,
            'Supplier' => 'Actor',
            'Customer' => 'Actor',
            'TermsOfPayment' => 'TermsOfPayment',
            'MaxDeliveryDay' => Object::TYPE_INT,
            'TotalDeliveryDay' => Object::TYPE_INT,
            'DeliveryType' => Object::TYPE_INT,
            'HasTVA' => Object::TYPE_BOOL,
            'HasTvaSurtax' => Object::TYPE_BOOL,
            'HasFodecTax' => Object::TYPE_BOOL,
            'HasTaxStamp' => Object::TYPE_BOOL,
            'Factor' => 'Actor');
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
            'DocumentModel'=>array(
                'linkClass'     => 'DocumentModel',
                'field'         => 'FromSupplierCustomer',
                'linkTable'     => 'spcDocumentModel',
                'linkField'     => 'ToDocumentModel',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'AbstractDocument'=>array(
                'linkClass'     => 'AbstractDocument',
                'field'         => 'SupplierCustomer',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'AnnualTurnoverDiscount'=>array(
                'linkClass'     => 'AnnualTurnoverDiscount',
                'field'         => 'SupplierCustomer',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Command'=>array(
                'linkClass'     => 'Command',
                'field'         => 'SupplierCustomer',
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