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

class _FlowTypeItem extends Object {
    // class constants {{{

    const BREAKDOWN_INSURANCE = 1;
    const BREAKDOWN_PACKING = 2;
    const BREAKDOWN_PORT = 3;
    const BREAKDOWN_INVOICE_ITEM = 4;

    // }}}
    // Constructeur {{{

    /**
     * _FlowTypeItem::__construct()
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
     * _FlowTypeItem::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _FlowTypeItem::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
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
     * _FlowTypeItem::getTVA
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
     * _FlowTypeItem::getTVAId
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
     * _FlowTypeItem::setTVA
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
    // FlowType foreignkey property + getter/setter {{{

    /**
     * FlowType foreignkey
     *
     * @access private
     * @var mixed object FlowType or integer
     */
    private $_FlowType = false;

    /**
     * _FlowTypeItem::getFlowType
     *
     * @access public
     * @return object FlowType
     */
    public function getFlowType() {
        if (is_int($this->_FlowType) && $this->_FlowType > 0) {
            $mapper = Mapper::singleton('FlowType');
            $this->_FlowType = $mapper->load(
                array('Id'=>$this->_FlowType));
        }
        return $this->_FlowType;
    }

    /**
     * _FlowTypeItem::getFlowTypeId
     *
     * @access public
     * @return integer
     */
    public function getFlowTypeId() {
        if ($this->_FlowType instanceof FlowType) {
            return $this->_FlowType->getId();
        }
        return (int)$this->_FlowType;
    }

    /**
     * _FlowTypeItem::setFlowType
     *
     * @access public
     * @param object FlowType $value
     * @return void
     */
    public function setFlowType($value) {
        if (is_numeric($value)) {
            $this->_FlowType = (int)$value;
        } else {
            $this->_FlowType = $value;
        }
    }

    // }}}
    // BreakdownPart const property + getter/setter/getBreakdownPartConstArray {{{

    /**
     * BreakdownPart int property
     *
     * @access private
     * @var integer
     */
    private $_BreakdownPart = 0;

    /**
     * _FlowTypeItem::getBreakdownPart
     *
     * @access public
     * @return integer
     */
    public function getBreakdownPart() {
        return $this->_BreakdownPart;
    }

    /**
     * _FlowTypeItem::setBreakdownPart
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setBreakdownPart($value) {
        if ($value !== null) {
            $this->_BreakdownPart = (int)$value;
        }
    }

    /**
     * _FlowTypeItem::getBreakdownPartConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getBreakdownPartConstArray($keys = false) {
        $array = array(
            _FlowTypeItem::BREAKDOWN_INSURANCE => _("insurance charges"), 
            _FlowTypeItem::BREAKDOWN_PACKING => _("packing charges"), 
            _FlowTypeItem::BREAKDOWN_PORT => _("forwarding charges"), 
            _FlowTypeItem::BREAKDOWN_INVOICE_ITEM => _("invoices items")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // FlowItem one to many relation + getter/setter {{{

    /**
     * FlowItem 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_FlowItemCollection = false;

    /**
     * _FlowTypeItem::getFlowItemCollection
     *
     * @access public
     * @return object Collection
     */
    public function getFlowItemCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('FlowTypeItem');
            return $mapper->getOneToMany($this->getId(),
                'FlowItem', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_FlowItemCollection) {
            $mapper = Mapper::singleton('FlowTypeItem');
            $this->_FlowItemCollection = $mapper->getOneToMany($this->getId(),
                'FlowItem');
        }
        return $this->_FlowItemCollection;
    }

    /**
     * _FlowTypeItem::getFlowItemCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getFlowItemCollectionIds($filter = array()) {
        $col = $this->getFlowItemCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _FlowTypeItem::setFlowItemCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setFlowItemCollection($value) {
        $this->_FlowItemCollection = $value;
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
        return 'FlowTypeItem';
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
        return _('Expenses and receipts lines');
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
            'TVA' => 'TVA',
            'FlowType' => 'FlowType',
            'BreakdownPart' => Object::TYPE_CONST);
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
            'FlowItem'=>array(
                'linkClass'     => 'FlowItem',
                'field'         => 'Type',
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
        return array('add', 'edit');
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
                'usedby'       => array('addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'TVA'=>array(
                'label'        => _('VAT'),
                'shortlabel'   => _('VAT'),
                'usedby'       => array('addedit'),
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