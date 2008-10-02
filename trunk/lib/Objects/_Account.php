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
 * _Account class
 *
 */
class _Account extends Object {
    // class constants {{{

    const BREAKDOWN_HT = 0;
    const BREAKDOWN_TVA = 1;
    const BREAKDOWN_TTC = 2;
    const BREAKDOWN_DISCOUNT = 3;

    // }}}
    // Constructeur {{{

    /**
     * _Account::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Number string property + getter/setter {{{

    /**
     * Number string property
     *
     * @access private
     * @var string
     */
    private $_Number = '';

    /**
     * _Account::getNumber
     *
     * @access public
     * @return string
     */
    public function getNumber() {
        return $this->_Number;
    }

    /**
     * _Account::setNumber
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setNumber($value) {
        $this->_Number = $value;
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
     * _Account::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _Account::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Currency foreignkey property + getter/setter {{{

    /**
     * Currency foreignkey
     *
     * @access private
     * @var mixed object Currency or integer
     */
    private $_Currency = false;

    /**
     * _Account::getCurrency
     *
     * @access public
     * @return object Currency
     */
    public function getCurrency() {
        if (is_int($this->_Currency) && $this->_Currency > 0) {
            $mapper = Mapper::singleton('Currency');
            $this->_Currency = $mapper->load(
                array('Id'=>$this->_Currency));
        }
        return $this->_Currency;
    }

    /**
     * _Account::getCurrencyId
     *
     * @access public
     * @return integer
     */
    public function getCurrencyId() {
        if ($this->_Currency instanceof Currency) {
            return $this->_Currency->getId();
        }
        return (int)$this->_Currency;
    }

    /**
     * _Account::setCurrency
     *
     * @access public
     * @param object Currency $value
     * @return void
     */
    public function setCurrency($value) {
        if (is_numeric($value)) {
            $this->_Currency = (int)$value;
        } else {
            $this->_Currency = $value;
        }
    }

    // }}}
    // BreakdownType const property + getter/setter/getBreakdownTypeConstArray {{{

    /**
     * BreakdownType int property
     *
     * @access private
     * @var integer
     */
    private $_BreakdownType = 0;

    /**
     * _Account::getBreakdownType
     *
     * @access public
     * @return integer
     */
    public function getBreakdownType() {
        return $this->_BreakdownType;
    }

    /**
     * _Account::setBreakdownType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setBreakdownType($value) {
        if ($value !== null) {
            $this->_BreakdownType = (int)$value;
        }
    }

    /**
     * _Account::getBreakdownTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getBreakdownTypeConstArray($keys = false) {
        $array = array(
            _Account::BREAKDOWN_HT => _("breaking-down of amount excl. VAT"), 
            _Account::BREAKDOWN_TVA => _("breaking-down of VAT amount"), 
            _Account::BREAKDOWN_TTC => _("breaking-down of amount incl. VAT"), 
            _Account::BREAKDOWN_DISCOUNT => _("breaking-down of discount")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // TVA foreignkey property + getter/setter {{{

    /**
     * TVA foreignkey
     *
     * @access private
     * @var mixed object TVA or integer
     */
    private $_TVA = false;

    /**
     * _Account::getTVA
     *
     * @access public
     * @return object TVA
     */
    public function getTVA() {
        if (is_int($this->_TVA) && $this->_TVA > 0) {
            $mapper = Mapper::singleton('TVA');
            $this->_TVA = $mapper->load(
                array('Id'=>$this->_TVA));
        }
        return $this->_TVA;
    }

    /**
     * _Account::getTVAId
     *
     * @access public
     * @return integer
     */
    public function getTVAId() {
        if ($this->_TVA instanceof TVA) {
            return $this->_TVA->getId();
        }
        return (int)$this->_TVA;
    }

    /**
     * _Account::setTVA
     *
     * @access public
     * @param object TVA $value
     * @return void
     */
    public function setTVA($value) {
        if (is_numeric($value)) {
            $this->_TVA = (int)$value;
        } else {
            $this->_TVA = $value;
        }
    }

    // }}}
    // AccountingType one to many relation + getter/setter {{{

    /**
     * AccountingType *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_AccountingTypeCollection = false;

    /**
     * _Account::getAccountingTypeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getAccountingTypeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Account');
            return $mapper->getManyToMany($this->getId(),
                'AccountingType', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_AccountingTypeCollection) {
            $mapper = Mapper::singleton('Account');
            $this->_AccountingTypeCollection = $mapper->getManyToMany($this->getId(),
                'AccountingType');
        }
        return $this->_AccountingTypeCollection;
    }

    /**
     * _Account::getAccountingTypeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getAccountingTypeCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getAccountingTypeCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_AccountingTypeCollection) {
            $mapper = Mapper::singleton('Account');
            return $mapper->getManyToManyIds($this->getId(), 'AccountingType');
        }
        return $this->_AccountingTypeCollection->getItemIds();
    }

    /**
     * _Account::setAccountingTypeCollectionIds
     *
     * @access public
     * @return array
     */
    public function setAccountingTypeCollectionIds($itemIds) {
        $this->_AccountingTypeCollection = new Collection('AccountingType');
        foreach ($itemIds as $id) {
            $this->_AccountingTypeCollection->setItem($id);
        }
    }

    /**
     * _Account::setAccountingTypeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setAccountingTypeCollection($value) {
        $this->_AccountingTypeCollection = $value;
    }

    /**
     * _Account::AccountingTypeCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function AccountingTypeCollectionIsLoaded() {
        return ($this->_AccountingTypeCollection !== false);
    }

    // }}}
    // FlowType one to many relation + getter/setter {{{

    /**
     * FlowType *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_FlowTypeCollection = false;

    /**
     * _Account::getFlowTypeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getFlowTypeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Account');
            return $mapper->getManyToMany($this->getId(),
                'FlowType', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_FlowTypeCollection) {
            $mapper = Mapper::singleton('Account');
            $this->_FlowTypeCollection = $mapper->getManyToMany($this->getId(),
                'FlowType');
        }
        return $this->_FlowTypeCollection;
    }

    /**
     * _Account::getFlowTypeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getFlowTypeCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getFlowTypeCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_FlowTypeCollection) {
            $mapper = Mapper::singleton('Account');
            return $mapper->getManyToManyIds($this->getId(), 'FlowType');
        }
        return $this->_FlowTypeCollection->getItemIds();
    }

    /**
     * _Account::setFlowTypeCollectionIds
     *
     * @access public
     * @return array
     */
    public function setFlowTypeCollectionIds($itemIds) {
        $this->_FlowTypeCollection = new Collection('FlowType');
        foreach ($itemIds as $id) {
            $this->_FlowTypeCollection->setItem($id);
        }
    }

    /**
     * _Account::setFlowTypeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setFlowTypeCollection($value) {
        $this->_FlowTypeCollection = $value;
    }

    /**
     * _Account::FlowTypeCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function FlowTypeCollectionIsLoaded() {
        return ($this->_FlowTypeCollection !== false);
    }

    // }}}
    // FlowTypeItem one to many relation + getter/setter {{{

    /**
     * FlowTypeItem *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_FlowTypeItemCollection = false;

    /**
     * _Account::getFlowTypeItemCollection
     *
     * @access public
     * @return object Collection
     */
    public function getFlowTypeItemCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Account');
            return $mapper->getManyToMany($this->getId(),
                'FlowTypeItem', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_FlowTypeItemCollection) {
            $mapper = Mapper::singleton('Account');
            $this->_FlowTypeItemCollection = $mapper->getManyToMany($this->getId(),
                'FlowTypeItem');
        }
        return $this->_FlowTypeItemCollection;
    }

    /**
     * _Account::getFlowTypeItemCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getFlowTypeItemCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getFlowTypeItemCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_FlowTypeItemCollection) {
            $mapper = Mapper::singleton('Account');
            return $mapper->getManyToManyIds($this->getId(), 'FlowTypeItem');
        }
        return $this->_FlowTypeItemCollection->getItemIds();
    }

    /**
     * _Account::setFlowTypeItemCollectionIds
     *
     * @access public
     * @return array
     */
    public function setFlowTypeItemCollectionIds($itemIds) {
        $this->_FlowTypeItemCollection = new Collection('FlowTypeItem');
        foreach ($itemIds as $id) {
            $this->_FlowTypeItemCollection->setItem($id);
        }
    }

    /**
     * _Account::setFlowTypeItemCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setFlowTypeItemCollection($value) {
        $this->_FlowTypeItemCollection = $value;
    }

    /**
     * _Account::FlowTypeItemCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function FlowTypeItemCollectionIsLoaded() {
        return ($this->_FlowTypeItemCollection !== false);
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
     * _Account::getActorBankDetail
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
     * _Account::getActorBankDetailId
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
     * _Account::setActorBankDetail
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
    // Comment string property + getter/setter {{{

    /**
     * Comment string property
     *
     * @access private
     * @var string
     */
    private $_Comment = '';

    /**
     * _Account::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * _Account::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
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
        return 'Account';
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
        return _('Accounts');
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
            'Number' => Object::TYPE_STRING,
            'Name' => Object::TYPE_STRING,
            'Currency' => 'Currency',
            'BreakdownType' => Object::TYPE_CONST,
            'TVA' => 'TVA',
            'ActorBankDetail' => 'ActorBankDetail',
            'Comment' => Object::TYPE_TEXT);
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
            'AccountingType'=>array(
                'linkClass'     => 'AccountingType',
                'field'         => 'FromAccount',
                'linkTable'     => 'accountAccountingType',
                'linkField'     => 'ToAccountingType',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'FlowType'=>array(
                'linkClass'     => 'FlowType',
                'field'         => 'FromAccount',
                'linkTable'     => 'accountFlowType',
                'linkField'     => 'ToFlowType',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'FlowTypeItem'=>array(
                'linkClass'     => 'FlowTypeItem',
                'field'         => 'FromAccount',
                'linkTable'     => 'accountFlowTypeItem',
                'linkField'     => 'ToFlowTypeItem',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
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
        $return = array('TVA','AccountingType','FlowType');
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
        return array('grid', 'searchform', 'add', 'edit', 'del');
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
            'Number'=>array(
                'label'        => _('Account number'),
                'shortlabel'   => _('Number'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Name'=>array(
                'label'        => _('Account name'),
                'shortlabel'   => _('Name'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Currency'=>array(
                'label'        => _('Currency'),
                'shortlabel'   => _('Currency'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'BreakdownType'=>array(
                'label'        => _('Type of breaking down'),
                'shortlabel'   => _('Type of breaking down'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'TVA'=>array(
                'label'        => _('VAT'),
                'shortlabel'   => _('VAT'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'AccountingType'=>array(
                'label'        => _('Accounting model'),
                'shortlabel'   => _('Accounting model'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'FlowType'=>array(
                'label'        => _('Flow type'),
                'shortlabel'   => _('Flow type'),
                'usedby'       => array('searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'FlowTypeItem'=>array(
                'label'        => _('expenses and receipts'),
                'shortlabel'   => _('expenses and receipts'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('expenses and receipts')
            ),
            'ActorBankDetail'=>array(
                'label'        => _('bank'),
                'shortlabel'   => _('bank'),
                'usedby'       => array('addedit', 'grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('Bank')
            ),
            'Comment'=>array(
                'label'        => _('comment'),
                'shortlabel'   => _('comment'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('comment')
            ));
        return $return;
    }

    // }}}
}

?>