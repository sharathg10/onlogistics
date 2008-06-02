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

class _InvoiceItem extends Object {
    
    // Constructeur {{{

    /**
     * _InvoiceItem::__construct()
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
     * _InvoiceItem::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _InvoiceItem::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
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
     * _InvoiceItem::getReference
     *
     * @access public
     * @return string
     */
    public function getReference() {
        return $this->_Reference;
    }

    /**
     * _InvoiceItem::setReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setReference($value) {
        $this->_Reference = $value;
    }

    // }}}
    // AssociatedReference string property + getter/setter {{{

    /**
     * AssociatedReference string property
     *
     * @access private
     * @var string
     */
    private $_AssociatedReference = '';

    /**
     * _InvoiceItem::getAssociatedReference
     *
     * @access public
     * @return string
     */
    public function getAssociatedReference() {
        return $this->_AssociatedReference;
    }

    /**
     * _InvoiceItem::setAssociatedReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setAssociatedReference($value) {
        $this->_AssociatedReference = $value;
    }

    // }}}
    // Handing string property + getter/setter {{{

    /**
     * Handing string property
     *
     * @access private
     * @var string
     */
    private $_Handing = '';

    /**
     * _InvoiceItem::getHanding
     *
     * @access public
     * @return string
     */
    public function getHanding() {
        return $this->_Handing;
    }

    /**
     * _InvoiceItem::setHanding
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setHanding($value) {
        $this->_Handing = $value;
    }

    // }}}
    // Quantity float property + getter/setter {{{

    /**
     * Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Quantity = 0;

    /**
     * _InvoiceItem::getQuantity
     *
     * @access public
     * @return float
     */
    public function getQuantity() {
        return $this->_Quantity;
    }

    /**
     * _InvoiceItem::setQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setQuantity($value) {
        if ($value !== null) {
            $this->_Quantity = round(I18N::extractNumber($value), 3);
        }
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
     * _InvoiceItem::getTVA
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
     * _InvoiceItem::getTVAId
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
     * _InvoiceItem::setTVA
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
    // UnitPriceHT float property + getter/setter {{{

    /**
     * UnitPriceHT float property
     *
     * @access private
     * @var float
     */
    private $_UnitPriceHT = 0;

    /**
     * _InvoiceItem::getUnitPriceHT
     *
     * @access public
     * @return float
     */
    public function getUnitPriceHT() {
        return $this->_UnitPriceHT;
    }

    /**
     * _InvoiceItem::setUnitPriceHT
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setUnitPriceHT($value) {
        if ($value !== null) {
            $this->_UnitPriceHT = round(I18N::extractNumber($value), 2);
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
     * _InvoiceItem::getInvoice
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
     * _InvoiceItem::getInvoiceId
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
     * _InvoiceItem::setInvoice
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
    // ActivatedMovement foreignkey property + getter/setter {{{

    /**
     * ActivatedMovement foreignkey
     *
     * @access private
     * @var mixed object ActivatedMovement or integer
     */
    private $_ActivatedMovement = false;

    /**
     * _InvoiceItem::getActivatedMovement
     *
     * @access public
     * @return object ActivatedMovement
     */
    public function getActivatedMovement() {
        if (is_int($this->_ActivatedMovement) && $this->_ActivatedMovement > 0) {
            $mapper = Mapper::singleton('ActivatedMovement');
            $this->_ActivatedMovement = $mapper->load(
                array('Id'=>$this->_ActivatedMovement));
        }
        return $this->_ActivatedMovement;
    }

    /**
     * _InvoiceItem::getActivatedMovementId
     *
     * @access public
     * @return integer
     */
    public function getActivatedMovementId() {
        if ($this->_ActivatedMovement instanceof ActivatedMovement) {
            return $this->_ActivatedMovement->getId();
        }
        return (int)$this->_ActivatedMovement;
    }

    /**
     * _InvoiceItem::setActivatedMovement
     *
     * @access public
     * @param object ActivatedMovement $value
     * @return void
     */
    public function setActivatedMovement($value) {
        if (is_numeric($value)) {
            $this->_ActivatedMovement = (int)$value;
        } else {
            $this->_ActivatedMovement = $value;
        }
    }

    // }}}
    // Prestation foreignkey property + getter/setter {{{

    /**
     * Prestation foreignkey
     *
     * @access private
     * @var mixed object Prestation or integer
     */
    private $_Prestation = false;

    /**
     * _InvoiceItem::getPrestation
     *
     * @access public
     * @return object Prestation
     */
    public function getPrestation() {
        if (is_int($this->_Prestation) && $this->_Prestation > 0) {
            $mapper = Mapper::singleton('Prestation');
            $this->_Prestation = $mapper->load(
                array('Id'=>$this->_Prestation));
        }
        return $this->_Prestation;
    }

    /**
     * _InvoiceItem::getPrestationId
     *
     * @access public
     * @return integer
     */
    public function getPrestationId() {
        if ($this->_Prestation instanceof Prestation) {
            return $this->_Prestation->getId();
        }
        return (int)$this->_Prestation;
    }

    /**
     * _InvoiceItem::setPrestation
     *
     * @access public
     * @param object Prestation $value
     * @return void
     */
    public function setPrestation($value) {
        if (is_numeric($value)) {
            $this->_Prestation = (int)$value;
        } else {
            $this->_Prestation = $value;
        }
    }

    // }}}
    // PrestationPeriodicity string property + getter/setter {{{

    /**
     * PrestationPeriodicity int property
     *
     * @access private
     * @var integer
     */
    private $_PrestationPeriodicity = 0;

    /**
     * _InvoiceItem::getPrestationPeriodicity
     *
     * @access public
     * @return integer
     */
    public function getPrestationPeriodicity() {
        return $this->_PrestationPeriodicity;
    }

    /**
     * _InvoiceItem::setPrestationPeriodicity
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPrestationPeriodicity($value) {
        if ($value !== null) {
            $this->_PrestationPeriodicity = (int)$value;
        }
    }

    // }}}
    // PrestationCost float property + getter/setter {{{

    /**
     * PrestationCost float property
     *
     * @access private
     * @var float
     */
    private $_PrestationCost = 0;

    /**
     * _InvoiceItem::getPrestationCost
     *
     * @access public
     * @return float
     */
    public function getPrestationCost() {
        return $this->_PrestationCost;
    }

    /**
     * _InvoiceItem::setPrestationCost
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPrestationCost($value) {
        if ($value !== null) {
            $this->_PrestationCost = round(I18N::extractNumber($value), 3);
        }
    }

    // }}}
    // QuantityForPrestationCost float property + getter/setter {{{

    /**
     * QuantityForPrestationCost float property
     *
     * @access private
     * @var float
     */
    private $_QuantityForPrestationCost = 0;

    /**
     * _InvoiceItem::getQuantityForPrestationCost
     *
     * @access public
     * @return float
     */
    public function getQuantityForPrestationCost() {
        return $this->_QuantityForPrestationCost;
    }

    /**
     * _InvoiceItem::setQuantityForPrestationCost
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setQuantityForPrestationCost($value) {
        if ($value !== null) {
            $this->_QuantityForPrestationCost = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // CostType string property + getter/setter {{{

    /**
     * CostType int property
     *
     * @access private
     * @var integer
     */
    private $_CostType = -1;

    /**
     * _InvoiceItem::getCostType
     *
     * @access public
     * @return integer
     */
    public function getCostType() {
        return $this->_CostType;
    }

    /**
     * _InvoiceItem::setCostType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCostType($value) {
        if ($value !== null) {
            $this->_CostType = (int)$value;
        }
    }

    // }}}
    // ActivatedChainOperationFactured one to many relation + getter/setter {{{

    /**
     * ActivatedChainOperationFactured *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActivatedChainOperationFacturedCollection = false;

    /**
     * _InvoiceItem::getActivatedChainOperationFacturedCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedChainOperationFacturedCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('InvoiceItem');
            return $mapper->getManyToMany($this->getId(),
                'ActivatedChainOperationFactured', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedChainOperationFacturedCollection) {
            $mapper = Mapper::singleton('InvoiceItem');
            $this->_ActivatedChainOperationFacturedCollection = $mapper->getManyToMany($this->getId(),
                'ActivatedChainOperationFactured');
        }
        return $this->_ActivatedChainOperationFacturedCollection;
    }

    /**
     * _InvoiceItem::getActivatedChainOperationFacturedCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActivatedChainOperationFacturedCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getActivatedChainOperationFacturedCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ActivatedChainOperationFacturedCollection) {
            $mapper = Mapper::singleton('InvoiceItem');
            return $mapper->getManyToManyIds($this->getId(), 'ActivatedChainOperationFactured');
        }
        return $this->_ActivatedChainOperationFacturedCollection->getItemIds();
    }

    /**
     * _InvoiceItem::setActivatedChainOperationFacturedCollectionIds
     *
     * @access public
     * @return array
     */
    public function setActivatedChainOperationFacturedCollectionIds($itemIds) {
        $this->_ActivatedChainOperationFacturedCollection = new Collection('ActivatedChainOperationFactured');
        foreach ($itemIds as $id) {
            $this->_ActivatedChainOperationFacturedCollection->setItem($id);
        }
    }

    /**
     * _InvoiceItem::setActivatedChainOperationFacturedCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedChainOperationFacturedCollection($value) {
        $this->_ActivatedChainOperationFacturedCollection = $value;
    }

    /**
     * _InvoiceItem::ActivatedChainOperationFacturedCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ActivatedChainOperationFacturedCollectionIsLoaded() {
        return ($this->_ActivatedChainOperationFacturedCollection !== false);
    }

    // }}}
    // ProductType foreignkey property + getter/setter {{{

    /**
     * ProductType foreignkey
     *
     * @access private
     * @var mixed object ProductType or integer
     */
    private $_ProductType = false;

    /**
     * _InvoiceItem::getProductType
     *
     * @access public
     * @return object ProductType
     */
    public function getProductType() {
        if (is_int($this->_ProductType) && $this->_ProductType > 0) {
            $mapper = Mapper::singleton('ProductType');
            $this->_ProductType = $mapper->load(
                array('Id'=>$this->_ProductType));
        }
        return $this->_ProductType;
    }

    /**
     * _InvoiceItem::getProductTypeId
     *
     * @access public
     * @return integer
     */
    public function getProductTypeId() {
        if ($this->_ProductType instanceof ProductType) {
            return $this->_ProductType->getId();
        }
        return (int)$this->_ProductType;
    }

    /**
     * _InvoiceItem::setProductType
     *
     * @access public
     * @param object ProductType $value
     * @return void
     */
    public function setProductType($value) {
        if (is_numeric($value)) {
            $this->_ProductType = (int)$value;
        } else {
            $this->_ProductType = $value;
        }
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
     * _InvoiceItem::getLocationExecutedMovementCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLocationExecutedMovementCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('InvoiceItem');
            return $mapper->getOneToMany($this->getId(),
                'LocationExecutedMovement', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LocationExecutedMovementCollection) {
            $mapper = Mapper::singleton('InvoiceItem');
            $this->_LocationExecutedMovementCollection = $mapper->getOneToMany($this->getId(),
                'LocationExecutedMovement');
        }
        return $this->_LocationExecutedMovementCollection;
    }

    /**
     * _InvoiceItem::getLocationExecutedMovementCollectionIds
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
     * _InvoiceItem::setLocationExecutedMovementCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLocationExecutedMovementCollection($value) {
        $this->_LocationExecutedMovementCollection = $value;
    }

    // }}}
    // OccupiedLocation one to many relation + getter/setter {{{

    /**
     * OccupiedLocation 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_OccupiedLocationCollection = false;

    /**
     * _InvoiceItem::getOccupiedLocationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getOccupiedLocationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('InvoiceItem');
            return $mapper->getOneToMany($this->getId(),
                'OccupiedLocation', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_OccupiedLocationCollection) {
            $mapper = Mapper::singleton('InvoiceItem');
            $this->_OccupiedLocationCollection = $mapper->getOneToMany($this->getId(),
                'OccupiedLocation');
        }
        return $this->_OccupiedLocationCollection;
    }

    /**
     * _InvoiceItem::getOccupiedLocationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getOccupiedLocationCollectionIds($filter = array()) {
        $col = $this->getOccupiedLocationCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _InvoiceItem::setOccupiedLocationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setOccupiedLocationCollection($value) {
        $this->_OccupiedLocationCollection = $value;
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
        return 'InvoiceItem';
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
            'Name' => Object::TYPE_STRING,
            'Reference' => Object::TYPE_STRING,
            'AssociatedReference' => Object::TYPE_STRING,
            'Handing' => Object::TYPE_STRING,
            'Quantity' => Object::TYPE_DECIMAL,
            'TVA' => 'TVA',
            'UnitPriceHT' => Object::TYPE_DECIMAL,
            'Invoice' => 'Invoice',
            'ActivatedMovement' => 'ActivatedMovement',
            'Prestation' => 'Prestation',
            'PrestationPeriodicity' => Object::TYPE_INT,
            'PrestationCost' => Object::TYPE_DECIMAL,
            'QuantityForPrestationCost' => Object::TYPE_DECIMAL,
            'CostType' => Object::TYPE_INT,
            'ProductType' => 'ProductType');
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
            'ActivatedChainOperationFactured'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'FromInvoiceItem',
                'linkTable'     => 'FromInvoiceItemToACO',
                'linkField'     => 'ToACO',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ActivatedChainOperation'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'InvoiceItem',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'LocationExecutedMovement'=>array(
                'linkClass'     => 'LocationExecutedMovement',
                'field'         => 'InvoiceItem',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'OccupiedLocation'=>array(
                'linkClass'     => 'OccupiedLocation',
                'field'         => 'InvoiceItem',
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