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
 * _Box class
 *
 */
class _Box extends Object {
    
    // Constructeur {{{

    /**
     * _Box::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
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
     * _Box::getReference
     *
     * @access public
     * @return string
     */
    public function getReference() {
        return $this->_Reference;
    }

    /**
     * _Box::setReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setReference($value) {
        $this->_Reference = $value;
    }

    // }}}
    // Level int property + getter/setter {{{

    /**
     * Level int property
     *
     * @access private
     * @var integer
     */
    private $_Level = null;

    /**
     * _Box::getLevel
     *
     * @access public
     * @return integer
     */
    public function getLevel() {
        return $this->_Level;
    }

    /**
     * _Box::setLevel
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setLevel($value) {
        $this->_Level = ($value===null || $value === '')?null:(int)$value;
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
     * _Box::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * _Box::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // Dimensions string property + getter/setter {{{

    /**
     * Dimensions string property
     *
     * @access private
     * @var string
     */
    private $_Dimensions = '';

    /**
     * _Box::getDimensions
     *
     * @access public
     * @return string
     */
    public function getDimensions() {
        return $this->_Dimensions;
    }

    /**
     * _Box::setDimensions
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDimensions($value) {
        $this->_Dimensions = $value;
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
     * _Box::getDate
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
     * _Box::setDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDate($value) {
        $this->_Date = $value;
    }

    // }}}
    // Weight float property + getter/setter {{{

    /**
     * Weight float property
     *
     * @access private
     * @var float
     */
    private $_Weight = 0;

    /**
     * _Box::getWeight
     *
     * @access public
     * @return float
     */
    public function getWeight() {
        return $this->_Weight;
    }

    /**
     * _Box::setWeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setWeight($value) {
        if ($value !== null) {
            $this->_Weight = I18N::extractNumber($value);
        }
    }

    // }}}
    // Volume float property + getter/setter {{{

    /**
     * Volume float property
     *
     * @access private
     * @var float
     */
    private $_Volume = 0;

    /**
     * _Box::getVolume
     *
     * @access public
     * @return float
     */
    public function getVolume() {
        return $this->_Volume;
    }

    /**
     * _Box::setVolume
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setVolume($value) {
        if ($value !== null) {
            $this->_Volume = I18N::extractNumber($value);
        }
    }

    // }}}
    // PrestationFactured string property + getter/setter {{{

    /**
     * PrestationFactured int property
     *
     * @access private
     * @var integer
     */
    private $_PrestationFactured = 0;

    /**
     * _Box::getPrestationFactured
     *
     * @access public
     * @return integer
     */
    public function getPrestationFactured() {
        return $this->_PrestationFactured;
    }

    /**
     * _Box::setPrestationFactured
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPrestationFactured($value) {
        if ($value !== null) {
            $this->_PrestationFactured = (int)$value;
        }
    }

    // }}}
    // InvoicePrestation foreignkey property + getter/setter {{{

    /**
     * InvoicePrestation foreignkey
     *
     * @access private
     * @var mixed object Invoice or integer
     */
    private $_InvoicePrestation = 0;

    /**
     * _Box::getInvoicePrestation
     *
     * @access public
     * @return object Invoice
     */
    public function getInvoicePrestation() {
        if (is_int($this->_InvoicePrestation) && $this->_InvoicePrestation > 0) {
            $mapper = Mapper::singleton('Invoice');
            $this->_InvoicePrestation = $mapper->load(
                array('Id'=>$this->_InvoicePrestation));
        }
        return $this->_InvoicePrestation;
    }

    /**
     * _Box::getInvoicePrestationId
     *
     * @access public
     * @return integer
     */
    public function getInvoicePrestationId() {
        if ($this->_InvoicePrestation instanceof Invoice) {
            return $this->_InvoicePrestation->getId();
        }
        return (int)$this->_InvoicePrestation;
    }

    /**
     * _Box::setInvoicePrestation
     *
     * @access public
     * @param object Invoice $value
     * @return void
     */
    public function setInvoicePrestation($value) {
        if (is_numeric($value)) {
            $this->_InvoicePrestation = (int)$value;
        } else {
            $this->_InvoicePrestation = $value;
        }
    }

    // }}}
    // ParentBox foreignkey property + getter/setter {{{

    /**
     * ParentBox foreignkey
     *
     * @access private
     * @var mixed object Box or integer
     */
    private $_ParentBox = false;

    /**
     * _Box::getParentBox
     *
     * @access public
     * @return object Box
     */
    public function getParentBox() {
        if (is_int($this->_ParentBox) && $this->_ParentBox > 0) {
            $mapper = Mapper::singleton('Box');
            $this->_ParentBox = $mapper->load(
                array('Id'=>$this->_ParentBox));
        }
        return $this->_ParentBox;
    }

    /**
     * _Box::getParentBoxId
     *
     * @access public
     * @return integer
     */
    public function getParentBoxId() {
        if ($this->_ParentBox instanceof Box) {
            return $this->_ParentBox->getId();
        }
        return (int)$this->_ParentBox;
    }

    /**
     * _Box::setParentBox
     *
     * @access public
     * @param object Box $value
     * @return void
     */
    public function setParentBox($value) {
        if (is_numeric($value)) {
            $this->_ParentBox = (int)$value;
        } else {
            $this->_ParentBox = $value;
        }
    }

    // }}}
    // ActivatedChain foreignkey property + getter/setter {{{

    /**
     * ActivatedChain foreignkey
     *
     * @access private
     * @var mixed object ActivatedChain or integer
     */
    private $_ActivatedChain = false;

    /**
     * _Box::getActivatedChain
     *
     * @access public
     * @return object ActivatedChain
     */
    public function getActivatedChain() {
        if (is_int($this->_ActivatedChain) && $this->_ActivatedChain > 0) {
            $mapper = Mapper::singleton('ActivatedChain');
            $this->_ActivatedChain = $mapper->load(
                array('Id'=>$this->_ActivatedChain));
        }
        return $this->_ActivatedChain;
    }

    /**
     * _Box::getActivatedChainId
     *
     * @access public
     * @return integer
     */
    public function getActivatedChainId() {
        if ($this->_ActivatedChain instanceof ActivatedChain) {
            return $this->_ActivatedChain->getId();
        }
        return (int)$this->_ActivatedChain;
    }

    /**
     * _Box::setActivatedChain
     *
     * @access public
     * @param object ActivatedChain $value
     * @return void
     */
    public function setActivatedChain($value) {
        if (is_numeric($value)) {
            $this->_ActivatedChain = (int)$value;
        } else {
            $this->_ActivatedChain = $value;
        }
    }

    // }}}
    // CommandItem foreignkey property + getter/setter {{{

    /**
     * CommandItem foreignkey
     *
     * @access private
     * @var mixed object CommandItem or integer
     */
    private $_CommandItem = false;

    /**
     * _Box::getCommandItem
     *
     * @access public
     * @return object CommandItem
     */
    public function getCommandItem() {
        if (is_int($this->_CommandItem) && $this->_CommandItem > 0) {
            $mapper = Mapper::singleton('CommandItem');
            $this->_CommandItem = $mapper->load(
                array('Id'=>$this->_CommandItem));
        }
        return $this->_CommandItem;
    }

    /**
     * _Box::getCommandItemId
     *
     * @access public
     * @return integer
     */
    public function getCommandItemId() {
        if ($this->_CommandItem instanceof CommandItem) {
            return $this->_CommandItem->getId();
        }
        return (int)$this->_CommandItem;
    }

    /**
     * _Box::setCommandItem
     *
     * @access public
     * @param object CommandItem $value
     * @return void
     */
    public function setCommandItem($value) {
        if (is_numeric($value)) {
            $this->_CommandItem = (int)$value;
        } else {
            $this->_CommandItem = $value;
        }
    }

    // }}}
    // LocationExecutedMovement foreignkey property + getter/setter {{{

    /**
     * LocationExecutedMovement foreignkey
     *
     * @access private
     * @var mixed object LocationExecutedMovement or integer
     */
    private $_LocationExecutedMovement = false;

    /**
     * _Box::getLocationExecutedMovement
     *
     * @access public
     * @return object LocationExecutedMovement
     */
    public function getLocationExecutedMovement() {
        if (is_int($this->_LocationExecutedMovement) && $this->_LocationExecutedMovement > 0) {
            $mapper = Mapper::singleton('LocationExecutedMovement');
            $this->_LocationExecutedMovement = $mapper->load(
                array('Id'=>$this->_LocationExecutedMovement));
        }
        return $this->_LocationExecutedMovement;
    }

    /**
     * _Box::getLocationExecutedMovementId
     *
     * @access public
     * @return integer
     */
    public function getLocationExecutedMovementId() {
        if ($this->_LocationExecutedMovement instanceof LocationExecutedMovement) {
            return $this->_LocationExecutedMovement->getId();
        }
        return (int)$this->_LocationExecutedMovement;
    }

    /**
     * _Box::setLocationExecutedMovement
     *
     * @access public
     * @param object LocationExecutedMovement $value
     * @return void
     */
    public function setLocationExecutedMovement($value) {
        if (is_numeric($value)) {
            $this->_LocationExecutedMovement = (int)$value;
        } else {
            $this->_LocationExecutedMovement = $value;
        }
    }

    // }}}
    // CoverType foreignkey property + getter/setter {{{

    /**
     * CoverType foreignkey
     *
     * @access private
     * @var mixed object CoverType or integer
     */
    private $_CoverType = false;

    /**
     * _Box::getCoverType
     *
     * @access public
     * @return object CoverType
     */
    public function getCoverType() {
        if (is_int($this->_CoverType) && $this->_CoverType > 0) {
            $mapper = Mapper::singleton('CoverType');
            $this->_CoverType = $mapper->load(
                array('Id'=>$this->_CoverType));
        }
        return $this->_CoverType;
    }

    /**
     * _Box::getCoverTypeId
     *
     * @access public
     * @return integer
     */
    public function getCoverTypeId() {
        if ($this->_CoverType instanceof CoverType) {
            return $this->_CoverType->getId();
        }
        return (int)$this->_CoverType;
    }

    /**
     * _Box::setCoverType
     *
     * @access public
     * @param object CoverType $value
     * @return void
     */
    public function setCoverType($value) {
        if (is_numeric($value)) {
            $this->_CoverType = (int)$value;
        } else {
            $this->_CoverType = $value;
        }
    }

    // }}}
    // Expeditor foreignkey property + getter/setter {{{

    /**
     * Expeditor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Expeditor = false;

    /**
     * _Box::getExpeditor
     *
     * @access public
     * @return object Actor
     */
    public function getExpeditor() {
        if (is_int($this->_Expeditor) && $this->_Expeditor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Expeditor = $mapper->load(
                array('Id'=>$this->_Expeditor));
        }
        return $this->_Expeditor;
    }

    /**
     * _Box::getExpeditorId
     *
     * @access public
     * @return integer
     */
    public function getExpeditorId() {
        if ($this->_Expeditor instanceof Actor) {
            return $this->_Expeditor->getId();
        }
        return (int)$this->_Expeditor;
    }

    /**
     * _Box::setExpeditor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setExpeditor($value) {
        if (is_numeric($value)) {
            $this->_Expeditor = (int)$value;
        } else {
            $this->_Expeditor = $value;
        }
    }

    // }}}
    // ExpeditorSite foreignkey property + getter/setter {{{

    /**
     * ExpeditorSite foreignkey
     *
     * @access private
     * @var mixed object Site or integer
     */
    private $_ExpeditorSite = false;

    /**
     * _Box::getExpeditorSite
     *
     * @access public
     * @return object Site
     */
    public function getExpeditorSite() {
        if (is_int($this->_ExpeditorSite) && $this->_ExpeditorSite > 0) {
            $mapper = Mapper::singleton('Site');
            $this->_ExpeditorSite = $mapper->load(
                array('Id'=>$this->_ExpeditorSite));
        }
        return $this->_ExpeditorSite;
    }

    /**
     * _Box::getExpeditorSiteId
     *
     * @access public
     * @return integer
     */
    public function getExpeditorSiteId() {
        if ($this->_ExpeditorSite instanceof Site) {
            return $this->_ExpeditorSite->getId();
        }
        return (int)$this->_ExpeditorSite;
    }

    /**
     * _Box::setExpeditorSite
     *
     * @access public
     * @param object Site $value
     * @return void
     */
    public function setExpeditorSite($value) {
        if (is_numeric($value)) {
            $this->_ExpeditorSite = (int)$value;
        } else {
            $this->_ExpeditorSite = $value;
        }
    }

    // }}}
    // Destinator foreignkey property + getter/setter {{{

    /**
     * Destinator foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Destinator = false;

    /**
     * _Box::getDestinator
     *
     * @access public
     * @return object Actor
     */
    public function getDestinator() {
        if (is_int($this->_Destinator) && $this->_Destinator > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Destinator = $mapper->load(
                array('Id'=>$this->_Destinator));
        }
        return $this->_Destinator;
    }

    /**
     * _Box::getDestinatorId
     *
     * @access public
     * @return integer
     */
    public function getDestinatorId() {
        if ($this->_Destinator instanceof Actor) {
            return $this->_Destinator->getId();
        }
        return (int)$this->_Destinator;
    }

    /**
     * _Box::setDestinator
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setDestinator($value) {
        if (is_numeric($value)) {
            $this->_Destinator = (int)$value;
        } else {
            $this->_Destinator = $value;
        }
    }

    // }}}
    // DestinatorSite foreignkey property + getter/setter {{{

    /**
     * DestinatorSite foreignkey
     *
     * @access private
     * @var mixed object Site or integer
     */
    private $_DestinatorSite = false;

    /**
     * _Box::getDestinatorSite
     *
     * @access public
     * @return object Site
     */
    public function getDestinatorSite() {
        if (is_int($this->_DestinatorSite) && $this->_DestinatorSite > 0) {
            $mapper = Mapper::singleton('Site');
            $this->_DestinatorSite = $mapper->load(
                array('Id'=>$this->_DestinatorSite));
        }
        return $this->_DestinatorSite;
    }

    /**
     * _Box::getDestinatorSiteId
     *
     * @access public
     * @return integer
     */
    public function getDestinatorSiteId() {
        if ($this->_DestinatorSite instanceof Site) {
            return $this->_DestinatorSite->getId();
        }
        return (int)$this->_DestinatorSite;
    }

    /**
     * _Box::setDestinatorSite
     *
     * @access public
     * @param object Site $value
     * @return void
     */
    public function setDestinatorSite($value) {
        if (is_numeric($value)) {
            $this->_DestinatorSite = (int)$value;
        } else {
            $this->_DestinatorSite = $value;
        }
    }

    // }}}
    // PackingList foreignkey property + getter/setter {{{

    /**
     * PackingList foreignkey
     *
     * @access private
     * @var mixed object PackingList or integer
     */
    private $_PackingList = false;

    /**
     * _Box::getPackingList
     *
     * @access public
     * @return object PackingList
     */
    public function getPackingList() {
        if (is_int($this->_PackingList) && $this->_PackingList > 0) {
            $mapper = Mapper::singleton('PackingList');
            $this->_PackingList = $mapper->load(
                array('Id'=>$this->_PackingList));
        }
        return $this->_PackingList;
    }

    /**
     * _Box::getPackingListId
     *
     * @access public
     * @return integer
     */
    public function getPackingListId() {
        if ($this->_PackingList instanceof PackingList) {
            return $this->_PackingList->getId();
        }
        return (int)$this->_PackingList;
    }

    /**
     * _Box::setPackingList
     *
     * @access public
     * @param object PackingList $value
     * @return void
     */
    public function setPackingList($value) {
        if (is_numeric($value)) {
            $this->_PackingList = (int)$value;
        } else {
            $this->_PackingList = $value;
        }
    }

    // }}}
    // ActivatedChainTask one to many relation + getter/setter {{{

    /**
     * ActivatedChainTask *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActivatedChainTaskCollection = false;

    /**
     * _Box::getActivatedChainTaskCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedChainTaskCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Box');
            return $mapper->getManyToMany($this->getId(),
                'ActivatedChainTask', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedChainTaskCollection) {
            $mapper = Mapper::singleton('Box');
            $this->_ActivatedChainTaskCollection = $mapper->getManyToMany($this->getId(),
                'ActivatedChainTask');
        }
        return $this->_ActivatedChainTaskCollection;
    }

    /**
     * _Box::getActivatedChainTaskCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActivatedChainTaskCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getActivatedChainTaskCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ActivatedChainTaskCollection) {
            $mapper = Mapper::singleton('Box');
            return $mapper->getManyToManyIds($this->getId(), 'ActivatedChainTask');
        }
        return $this->_ActivatedChainTaskCollection->getItemIds();
    }

    /**
     * _Box::setActivatedChainTaskCollectionIds
     *
     * @access public
     * @return array
     */
    public function setActivatedChainTaskCollectionIds($itemIds) {
        $this->_ActivatedChainTaskCollection = new Collection('ActivatedChainTask');
        foreach ($itemIds as $id) {
            $this->_ActivatedChainTaskCollection->setItem($id);
        }
    }

    /**
     * _Box::setActivatedChainTaskCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedChainTaskCollection($value) {
        $this->_ActivatedChainTaskCollection = $value;
    }

    /**
     * _Box::ActivatedChainTaskCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ActivatedChainTaskCollectionIsLoaded() {
        return ($this->_ActivatedChainTaskCollection !== false);
    }

    // }}}
    // Box one to many relation + getter/setter {{{

    /**
     * Box 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_BoxCollection = false;

    /**
     * _Box::getBoxCollection
     *
     * @access public
     * @return object Collection
     */
    public function getBoxCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Box');
            return $mapper->getOneToMany($this->getId(),
                'Box', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_BoxCollection) {
            $mapper = Mapper::singleton('Box');
            $this->_BoxCollection = $mapper->getOneToMany($this->getId(),
                'Box');
        }
        return $this->_BoxCollection;
    }

    /**
     * _Box::getBoxCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getBoxCollectionIds($filter = array()) {
        $col = $this->getBoxCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Box::setBoxCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setBoxCollection($value) {
        $this->_BoxCollection = $value;
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
        return 'Box';
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
            'Reference' => Object::TYPE_STRING,
            'Level' => Object::TYPE_INT,
            'Comment' => Object::TYPE_TEXT,
            'Dimensions' => Object::TYPE_TEXT,
            'Date' => Object::TYPE_DATETIME,
            'Weight' => Object::TYPE_FLOAT,
            'Volume' => Object::TYPE_FLOAT,
            'PrestationFactured' => Object::TYPE_BOOL,
            'InvoicePrestation' => 'Invoice',
            'ParentBox' => 'Box',
            'ActivatedChain' => 'ActivatedChain',
            'CommandItem' => 'CommandItem',
            'LocationExecutedMovement' => 'LocationExecutedMovement',
            'CoverType' => 'CoverType',
            'Expeditor' => 'Actor',
            'ExpeditorSite' => 'Site',
            'Destinator' => 'Actor',
            'DestinatorSite' => 'Site',
            'PackingList' => 'PackingList');
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
            'ActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'FromBox',
                'linkTable'     => 'boxActivatedChainTask',
                'linkField'     => 'ToActivatedChainTask',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Box'=>array(
                'linkClass'     => 'Box',
                'field'         => 'ParentBox',
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