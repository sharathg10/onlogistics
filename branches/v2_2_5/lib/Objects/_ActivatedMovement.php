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

class _ActivatedMovement extends Object {
    // class constants {{{

    const CREE = 0;
    const ACM_EN_COURS = 1;
    const ACM_EXECUTE_TOTALEMENT = 2;
    const ACM_EXECUTE_PARTIELLEMENT = 3;
    const BLOQUE = 4;
    const ACM_NON_FACTURE = 0;
    const ACM_FACTURE = 1;
    const ACM_FACTURE_PARTIEL = 2;

    // }}}
    // Constructeur {{{

    /**
     * _ActivatedMovement::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // StartDate datetime property + getter/setter {{{

    /**
     * StartDate int property
     *
     * @access private
     * @var string
     */
    private $_StartDate = 0;

    /**
     * _ActivatedMovement::getStartDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getStartDate($format = false) {
        return $this->dateFormat($this->_StartDate, $format);
    }

    /**
     * _ActivatedMovement::setStartDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStartDate($value) {
        $this->_StartDate = $value;
    }

    // }}}
    // EndDate datetime property + getter/setter {{{

    /**
     * EndDate int property
     *
     * @access private
     * @var string
     */
    private $_EndDate = 0;

    /**
     * _ActivatedMovement::getEndDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEndDate($format = false) {
        return $this->dateFormat($this->_EndDate, $format);
    }

    /**
     * _ActivatedMovement::setEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndDate($value) {
        $this->_EndDate = $value;
    }

    // }}}
    // State const property + getter/setter/getStateConstArray {{{

    /**
     * State int property
     *
     * @access private
     * @var integer
     */
    private $_State = 0;

    /**
     * _ActivatedMovement::getState
     *
     * @access public
     * @return integer
     */
    public function getState() {
        return $this->_State;
    }

    /**
     * _ActivatedMovement::setState
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setState($value) {
        if ($value !== null) {
            $this->_State = (int)$value;
        }
    }

    /**
     * _ActivatedMovement::getStateConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getStateConstArray($keys = false) {
        $array = array(
            _ActivatedMovement::CREE => _("Created"), 
            _ActivatedMovement::ACM_EN_COURS => _("In progress"), 
            _ActivatedMovement::ACM_EXECUTE_TOTALEMENT => _("Completely executed"), 
            _ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT => _("Partially executed"), 
            _ActivatedMovement::BLOQUE => _("Locked")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // HasBeenFactured const property + getter/setter/getHasBeenFacturedConstArray {{{

    /**
     * HasBeenFactured int property
     *
     * @access private
     * @var integer
     */
    private $_HasBeenFactured = 0;

    /**
     * _ActivatedMovement::getHasBeenFactured
     *
     * @access public
     * @return integer
     */
    public function getHasBeenFactured() {
        return $this->_HasBeenFactured;
    }

    /**
     * _ActivatedMovement::setHasBeenFactured
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setHasBeenFactured($value) {
        if ($value !== null) {
            $this->_HasBeenFactured = (int)$value;
        }
    }

    /**
     * _ActivatedMovement::getHasBeenFacturedConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getHasBeenFacturedConstArray($keys = false) {
        $array = array(
            _ActivatedMovement::ACM_NON_FACTURE => _("Not charged"), 
            _ActivatedMovement::ACM_FACTURE => _("Completely charged"), 
            _ActivatedMovement::ACM_FACTURE_PARTIEL => _("Partially charged")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Quantity float property + getter/setter {{{

    /**
     * Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Quantity = null;

    /**
     * _ActivatedMovement::getQuantity
     *
     * @access public
     * @return float
     */
    public function getQuantity() {
        return $this->_Quantity;
    }

    /**
     * _ActivatedMovement::setQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setQuantity($value) {
        $this->_Quantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // Type foreignkey property + getter/setter {{{

    /**
     * Type foreignkey
     *
     * @access private
     * @var mixed object MovementType or integer
     */
    private $_Type = false;

    /**
     * _ActivatedMovement::getType
     *
     * @access public
     * @return object MovementType
     */
    public function getType() {
        if (is_int($this->_Type) && $this->_Type > 0) {
            $mapper = Mapper::singleton('MovementType');
            $this->_Type = $mapper->load(
                array('Id'=>$this->_Type));
        }
        return $this->_Type;
    }

    /**
     * _ActivatedMovement::getTypeId
     *
     * @access public
     * @return integer
     */
    public function getTypeId() {
        if ($this->_Type instanceof MovementType) {
            return $this->_Type->getId();
        }
        return (int)$this->_Type;
    }

    /**
     * _ActivatedMovement::setType
     *
     * @access public
     * @param object MovementType $value
     * @return void
     */
    public function setType($value) {
        if (is_numeric($value)) {
            $this->_Type = (int)$value;
        } else {
            $this->_Type = $value;
        }
    }

    // }}}
    // ProductCommandItem foreignkey property + getter/setter {{{

    /**
     * ProductCommandItem foreignkey
     *
     * @access private
     * @var mixed object ProductCommandItem or integer
     */
    private $_ProductCommandItem = false;

    /**
     * _ActivatedMovement::getProductCommandItem
     *
     * @access public
     * @return object ProductCommandItem
     */
    public function getProductCommandItem() {
        if (is_int($this->_ProductCommandItem) && $this->_ProductCommandItem > 0) {
            $mapper = Mapper::singleton('ProductCommandItem');
            $this->_ProductCommandItem = $mapper->load(
                array('Id'=>$this->_ProductCommandItem));
        }
        return $this->_ProductCommandItem;
    }

    /**
     * _ActivatedMovement::getProductCommandItemId
     *
     * @access public
     * @return integer
     */
    public function getProductCommandItemId() {
        if ($this->_ProductCommandItem instanceof ProductCommandItem) {
            return $this->_ProductCommandItem->getId();
        }
        return (int)$this->_ProductCommandItem;
    }

    /**
     * _ActivatedMovement::setProductCommandItem
     *
     * @access public
     * @param object ProductCommandItem $value
     * @return void
     */
    public function setProductCommandItem($value) {
        if (is_numeric($value)) {
            $this->_ProductCommandItem = (int)$value;
        } else {
            $this->_ProductCommandItem = $value;
        }
    }

    // }}}
    // ActivatedChainTask foreignkey property + getter/setter {{{

    /**
     * ActivatedChainTask foreignkey
     *
     * @access private
     * @var mixed object ActivatedChainTask or integer
     */
    private $_ActivatedChainTask = false;

    /**
     * _ActivatedMovement::getActivatedChainTask
     *
     * @access public
     * @return object ActivatedChainTask
     */
    public function getActivatedChainTask() {
        if (is_int($this->_ActivatedChainTask) && $this->_ActivatedChainTask > 0) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_ActivatedChainTask = $mapper->load(
                array('Id'=>$this->_ActivatedChainTask));
        }
        return $this->_ActivatedChainTask;
    }

    /**
     * _ActivatedMovement::getActivatedChainTaskId
     *
     * @access public
     * @return integer
     */
    public function getActivatedChainTaskId() {
        if ($this->_ActivatedChainTask instanceof ActivatedChainTask) {
            return $this->_ActivatedChainTask->getId();
        }
        return (int)$this->_ActivatedChainTask;
    }

    /**
     * _ActivatedMovement::setActivatedChainTask
     *
     * @access public
     * @param object ActivatedChainTask $value
     * @return void
     */
    public function setActivatedChainTask($value) {
        if (is_numeric($value)) {
            $this->_ActivatedChainTask = (int)$value;
        } else {
            $this->_ActivatedChainTask = $value;
        }
    }

    // }}}
    // Product foreignkey property + getter/setter {{{

    /**
     * Product foreignkey
     *
     * @access private
     * @var mixed object Product or integer
     */
    private $_Product = 0;

    /**
     * _ActivatedMovement::getProduct
     *
     * @access public
     * @return object Product
     */
    public function getProduct() {
        if (is_int($this->_Product) && $this->_Product > 0) {
            $mapper = Mapper::singleton('Product');
            $this->_Product = $mapper->load(
                array('Id'=>$this->_Product));
        }
        return $this->_Product;
    }

    /**
     * _ActivatedMovement::getProductId
     *
     * @access public
     * @return integer
     */
    public function getProductId() {
        if ($this->_Product instanceof Product) {
            return $this->_Product->getId();
        }
        return (int)$this->_Product;
    }

    /**
     * _ActivatedMovement::setProduct
     *
     * @access public
     * @param object Product $value
     * @return void
     */
    public function setProduct($value) {
        if (is_numeric($value)) {
            $this->_Product = (int)$value;
        } else {
            $this->_Product = $value;
        }
    }

    // }}}
    // ProductCommand foreignkey property + getter/setter {{{

    /**
     * ProductCommand foreignkey
     *
     * @access private
     * @var mixed object ProductCommand or integer
     */
    private $_ProductCommand = 0;

    /**
     * _ActivatedMovement::getProductCommand
     *
     * @access public
     * @return object ProductCommand
     */
    public function getProductCommand() {
        if (is_int($this->_ProductCommand) && $this->_ProductCommand > 0) {
            $mapper = Mapper::singleton('ProductCommand');
            $this->_ProductCommand = $mapper->load(
                array('Id'=>$this->_ProductCommand));
        }
        return $this->_ProductCommand;
    }

    /**
     * _ActivatedMovement::getProductCommandId
     *
     * @access public
     * @return integer
     */
    public function getProductCommandId() {
        if ($this->_ProductCommand instanceof ProductCommand) {
            return $this->_ProductCommand->getId();
        }
        return (int)$this->_ProductCommand;
    }

    /**
     * _ActivatedMovement::setProductCommand
     *
     * @access public
     * @param object ProductCommand $value
     * @return void
     */
    public function setProductCommand($value) {
        if (is_numeric($value)) {
            $this->_ProductCommand = (int)$value;
        } else {
            $this->_ProductCommand = $value;
        }
    }

    // }}}
    // Location one to many relation + getter/setter {{{

    /**
     * Location *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LocationCollection = false;

    /**
     * _ActivatedMovement::getLocationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLocationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedMovement');
            return $mapper->getManyToMany($this->getId(),
                'Location', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LocationCollection) {
            $mapper = Mapper::singleton('ActivatedMovement');
            $this->_LocationCollection = $mapper->getManyToMany($this->getId(),
                'Location');
        }
        return $this->_LocationCollection;
    }

    /**
     * _ActivatedMovement::getLocationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLocationCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getLocationCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_LocationCollection) {
            $mapper = Mapper::singleton('ActivatedMovement');
            return $mapper->getManyToManyIds($this->getId(), 'Location');
        }
        return $this->_LocationCollection->getItemIds();
    }

    /**
     * _ActivatedMovement::setLocationCollectionIds
     *
     * @access public
     * @return array
     */
    public function setLocationCollectionIds($itemIds) {
        $this->_LocationCollection = new Collection('Location');
        foreach ($itemIds as $id) {
            $this->_LocationCollection->setItem($id);
        }
    }

    /**
     * _ActivatedMovement::setLocationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLocationCollection($value) {
        $this->_LocationCollection = $value;
    }

    /**
     * _ActivatedMovement::LocationCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function LocationCollectionIsLoaded() {
        return ($this->_LocationCollection !== false);
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
        return 'ActivatedMovement';
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
            'StartDate' => Object::TYPE_DATETIME,
            'EndDate' => Object::TYPE_DATETIME,
            'State' => Object::TYPE_CONST,
            'HasBeenFactured' => Object::TYPE_CONST,
            'Quantity' => Object::TYPE_DECIMAL,
            'Type' => 'MovementType',
            'ProductCommandItem' => 'ProductCommandItem',
            'ActivatedChainTask' => 'ActivatedChainTask',
            'Product' => 'Product',
            'ProductCommand' => 'ProductCommand');
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
            'Location'=>array(
                'linkClass'     => 'Location',
                'field'         => 'FromActivatedMovement',
                'linkTable'     => 'acmLocation',
                'linkField'     => 'ToLocation',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ExecutedMovement'=>array(
                'linkClass'     => 'ExecutedMovement',
                'field'         => 'ActivatedMovement',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'InvoiceItem'=>array(
                'linkClass'     => 'InvoiceItem',
                'field'         => 'ActivatedMovement',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ProductCommandItem'=>array(
                'linkClass'     => 'ProductCommandItem',
                'field'         => 'ActivatedMovement',
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