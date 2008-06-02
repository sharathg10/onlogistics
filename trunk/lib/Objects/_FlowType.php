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

class _FlowType extends Object {
    // class constants {{{

    const CHARGE = 0;
    const RECETTE = 1;
    const INVOICE_SUPPLIER_PRODUCT = 1;
    const INVOICE_SUPPLIER_TRANSPORT = 2;
    const INVOICE_SUPPLIER_COURSE = 3;
    const INVOICE_SUPPLIER_PRESTATION = 4;
    const INVOICE_CUSTOMER_PRODUCT = 5;
    const INVOICE_CUSTOMER_TRANSPORT = 6;
    const INVOICE_CUSTOMER_COURSE = 7;
    const INVOICE_CUSTOMER_PRESTATION = 8;

    // }}}
    // Constructeur {{{

    /**
     * _FlowType::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Name string property + getter/setter {{{

    /**
     * Name string property
     *
     * @access private
     * @var string
     */
    private $_Name = '';

    /**
     * _FlowType::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _FlowType::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
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
     * _FlowType::getActorBankDetail
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
     * _FlowType::getActorBankDetailId
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
     * _FlowType::setActorBankDetail
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
    // Type const property + getter/setter/getTypeConstArray {{{

    /**
     * Type int property
     *
     * @access private
     * @var integer
     */
    private $_Type = 0;

    /**
     * _FlowType::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * _FlowType::setType
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
     * _FlowType::getTypeConstArray
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
            _FlowType::CHARGE => _("Expenses"), 
            _FlowType::RECETTE => _("Receipts")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // FlowCategory foreignkey property + getter/setter {{{

    /**
     * FlowCategory foreignkey
     *
     * @access private
     * @var mixed object FlowCategory or integer
     */
    private $_FlowCategory = false;

    /**
     * _FlowType::getFlowCategory
     *
     * @access public
     * @return object FlowCategory
     */
    public function getFlowCategory() {
        if (is_int($this->_FlowCategory) && $this->_FlowCategory > 0) {
            $mapper = Mapper::singleton('FlowCategory');
            $this->_FlowCategory = $mapper->load(
                array('Id'=>$this->_FlowCategory));
        }
        return $this->_FlowCategory;
    }

    /**
     * _FlowType::getFlowCategoryId
     *
     * @access public
     * @return integer
     */
    public function getFlowCategoryId() {
        if ($this->_FlowCategory instanceof FlowCategory) {
            return $this->_FlowCategory->getId();
        }
        return (int)$this->_FlowCategory;
    }

    /**
     * _FlowType::setFlowCategory
     *
     * @access public
     * @param object FlowCategory $value
     * @return void
     */
    public function setFlowCategory($value) {
        if (is_numeric($value)) {
            $this->_FlowCategory = (int)$value;
        } else {
            $this->_FlowCategory = $value;
        }
    }

    // }}}
    // AccountingType foreignkey property + getter/setter {{{

    /**
     * AccountingType foreignkey
     *
     * @access private
     * @var mixed object AccountingType or integer
     */
    private $_AccountingType = false;

    /**
     * _FlowType::getAccountingType
     *
     * @access public
     * @return object AccountingType
     */
    public function getAccountingType() {
        if (is_int($this->_AccountingType) && $this->_AccountingType > 0) {
            $mapper = Mapper::singleton('AccountingType');
            $this->_AccountingType = $mapper->load(
                array('Id'=>$this->_AccountingType));
        }
        return $this->_AccountingType;
    }

    /**
     * _FlowType::getAccountingTypeId
     *
     * @access public
     * @return integer
     */
    public function getAccountingTypeId() {
        if ($this->_AccountingType instanceof AccountingType) {
            return $this->_AccountingType->getId();
        }
        return (int)$this->_AccountingType;
    }

    /**
     * _FlowType::setAccountingType
     *
     * @access public
     * @param object AccountingType $value
     * @return void
     */
    public function setAccountingType($value) {
        if (is_numeric($value)) {
            $this->_AccountingType = (int)$value;
        } else {
            $this->_AccountingType = $value;
        }
    }

    // }}}
    // ThirdParty foreignkey property + getter/setter {{{

    /**
     * ThirdParty foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_ThirdParty = false;

    /**
     * _FlowType::getThirdParty
     *
     * @access public
     * @return object Actor
     */
    public function getThirdParty() {
        if (is_int($this->_ThirdParty) && $this->_ThirdParty > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_ThirdParty = $mapper->load(
                array('Id'=>$this->_ThirdParty));
        }
        return $this->_ThirdParty;
    }

    /**
     * _FlowType::getThirdPartyId
     *
     * @access public
     * @return integer
     */
    public function getThirdPartyId() {
        if ($this->_ThirdParty instanceof Actor) {
            return $this->_ThirdParty->getId();
        }
        return (int)$this->_ThirdParty;
    }

    /**
     * _FlowType::setThirdParty
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setThirdParty($value) {
        if (is_numeric($value)) {
            $this->_ThirdParty = (int)$value;
        } else {
            $this->_ThirdParty = $value;
        }
    }

    // }}}
    // InvoiceType const property + getter/setter/getInvoiceTypeConstArray {{{

    /**
     * InvoiceType int property
     *
     * @access private
     * @var integer
     */
    private $_InvoiceType = 0;

    /**
     * _FlowType::getInvoiceType
     *
     * @access public
     * @return integer
     */
    public function getInvoiceType() {
        return $this->_InvoiceType;
    }

    /**
     * _FlowType::setInvoiceType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setInvoiceType($value) {
        if ($value !== null) {
            $this->_InvoiceType = (int)$value;
        }
    }

    /**
     * _FlowType::getInvoiceTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getInvoiceTypeConstArray($keys = false) {
        $array = array(
            _FlowType::INVOICE_SUPPLIER_PRODUCT => _("Supplier invoice for product order"), 
            _FlowType::INVOICE_SUPPLIER_TRANSPORT => _("Supplier invoice for carriage order"), 
            _FlowType::INVOICE_SUPPLIER_COURSE => _("Supplier invoice for class order"), 
            _FlowType::INVOICE_SUPPLIER_PRESTATION => _("Supplier invoice for service"), 
            _FlowType::INVOICE_CUSTOMER_PRODUCT => _("Customer invoice for product order"), 
            _FlowType::INVOICE_CUSTOMER_TRANSPORT => _("Customer invoice for carriage order"), 
            _FlowType::INVOICE_CUSTOMER_COURSE => _("Customer invoice for class order"), 
            _FlowType::INVOICE_CUSTOMER_PRESTATION => _("Customer invoice for service")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // FlowTypeItem one to many relation + getter/setter {{{

    /**
     * FlowTypeItem 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_FlowTypeItemCollection = false;

    /**
     * _FlowType::getFlowTypeItemCollection
     *
     * @access public
     * @return object Collection
     */
    public function getFlowTypeItemCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('FlowType');
            return $mapper->getOneToMany($this->getId(),
                'FlowTypeItem', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_FlowTypeItemCollection) {
            $mapper = Mapper::singleton('FlowType');
            $this->_FlowTypeItemCollection = $mapper->getOneToMany($this->getId(),
                'FlowTypeItem');
        }
        return $this->_FlowTypeItemCollection;
    }

    /**
     * _FlowType::getFlowTypeItemCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getFlowTypeItemCollectionIds($filter = array()) {
        $col = $this->getFlowTypeItemCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _FlowType::setFlowTypeItemCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setFlowTypeItemCollection($value) {
        $this->_FlowTypeItemCollection = $value;
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
        return 'FlowType';
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
        return _('Expenses and receipts types');
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
            'Name' => Object::TYPE_STRING,
            'ActorBankDetail' => 'ActorBankDetail',
            'Type' => Object::TYPE_CONST,
            'FlowCategory' => 'FlowCategory',
            'AccountingType' => 'AccountingType',
            'ThirdParty' => 'Actor',
            'InvoiceType' => Object::TYPE_CONST);
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
            'Flow'=>array(
                'linkClass'     => 'Flow',
                'field'         => 'FlowType',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'FlowTypeItem'=>array(
                'linkClass'     => 'FlowTypeItem',
                'field'         => 'FlowType',
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
            'Name'=>array(
                'label'        => _('Name'),
                'shortlabel'   => _('Name'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ActorBankDetail'=>array(
                'label'        => _('Bank'),
                'shortlabel'   => _('Bank'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Type'=>array(
                'label'        => _('Expense or receipt'),
                'shortlabel'   => _('Expense or receipt'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'FlowCategory'=>array(
                'label'        => _('Category'),
                'shortlabel'   => _('Category'),
                'usedby'       => array('addedit', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'AccountingType'=>array(
                'label'        => _('Accounting type'),
                'shortlabel'   => _('Accounting type'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ThirdParty'=>array(
                'label'        => _('Third party'),
                'shortlabel'   => _('Third party'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
}

?>