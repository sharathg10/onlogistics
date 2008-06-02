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

class _ActorProduct extends Object {
    
    // Constructeur {{{

    /**
     * _ActorProduct::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Actor foreignkey property + getter/setter {{{

    /**
     * Actor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Actor = false;

    /**
     * _ActorProduct::getActor
     *
     * @access public
     * @return object Actor
     */
    public function getActor() {
        if (is_int($this->_Actor) && $this->_Actor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Actor = $mapper->load(
                array('Id'=>$this->_Actor));
        }
        return $this->_Actor;
    }

    /**
     * _ActorProduct::getActorId
     *
     * @access public
     * @return integer
     */
    public function getActorId() {
        if ($this->_Actor instanceof Actor) {
            return $this->_Actor->getId();
        }
        return (int)$this->_Actor;
    }

    /**
     * _ActorProduct::setActor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setActor($value) {
        if (is_numeric($value)) {
            $this->_Actor = (int)$value;
        } else {
            $this->_Actor = $value;
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
    private $_Product = false;

    /**
     * _ActorProduct::getProduct
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
     * _ActorProduct::getProductId
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
     * _ActorProduct::setProduct
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
    // AssociatedProductReference string property + getter/setter {{{

    /**
     * AssociatedProductReference string property
     *
     * @access private
     * @var string
     */
    private $_AssociatedProductReference = '';

    /**
     * _ActorProduct::getAssociatedProductReference
     *
     * @access public
     * @return string
     */
    public function getAssociatedProductReference() {
        return $this->_AssociatedProductReference;
    }

    /**
     * _ActorProduct::setAssociatedProductReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setAssociatedProductReference($value) {
        $this->_AssociatedProductReference = $value;
    }

    // }}}
    // BuyUnitQuantity float property + getter/setter {{{

    /**
     * BuyUnitQuantity float property
     *
     * @access private
     * @var float
     */
    private $_BuyUnitQuantity = null;

    /**
     * _ActorProduct::getBuyUnitQuantity
     *
     * @access public
     * @return float
     */
    public function getBuyUnitQuantity() {
        return $this->_BuyUnitQuantity;
    }

    /**
     * _ActorProduct::setBuyUnitQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setBuyUnitQuantity($value) {
        $this->_BuyUnitQuantity = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 3);
    }

    // }}}
    // BuyUnitType foreignkey property + getter/setter {{{

    /**
     * BuyUnitType foreignkey
     *
     * @access private
     * @var mixed object SellUnitType or integer
     */
    private $_BuyUnitType = false;

    /**
     * _ActorProduct::getBuyUnitType
     *
     * @access public
     * @return object SellUnitType
     */
    public function getBuyUnitType() {
        if (is_int($this->_BuyUnitType) && $this->_BuyUnitType > 0) {
            $mapper = Mapper::singleton('SellUnitType');
            $this->_BuyUnitType = $mapper->load(
                array('Id'=>$this->_BuyUnitType));
        }
        return $this->_BuyUnitType;
    }

    /**
     * _ActorProduct::getBuyUnitTypeId
     *
     * @access public
     * @return integer
     */
    public function getBuyUnitTypeId() {
        if ($this->_BuyUnitType instanceof SellUnitType) {
            return $this->_BuyUnitType->getId();
        }
        return (int)$this->_BuyUnitType;
    }

    /**
     * _ActorProduct::setBuyUnitType
     *
     * @access public
     * @param object SellUnitType $value
     * @return void
     */
    public function setBuyUnitType($value) {
        if (is_numeric($value)) {
            $this->_BuyUnitType = (int)$value;
        } else {
            $this->_BuyUnitType = $value;
        }
    }

    // }}}
    // Priority string property + getter/setter {{{

    /**
     * Priority int property
     *
     * @access private
     * @var integer
     */
    private $_Priority = 0;

    /**
     * _ActorProduct::getPriority
     *
     * @access public
     * @return integer
     */
    public function getPriority() {
        return $this->_Priority;
    }

    /**
     * _ActorProduct::setPriority
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPriority($value) {
        if ($value !== null) {
            $this->_Priority = (int)$value;
        }
    }

    // }}}
    // PriceByCurrency one to many relation + getter/setter {{{

    /**
     * PriceByCurrency 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_PriceByCurrencyCollection = false;

    /**
     * _ActorProduct::getPriceByCurrencyCollection
     *
     * @access public
     * @return object Collection
     */
    public function getPriceByCurrencyCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActorProduct');
            return $mapper->getOneToMany($this->getId(),
                'PriceByCurrency', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_PriceByCurrencyCollection) {
            $mapper = Mapper::singleton('ActorProduct');
            $this->_PriceByCurrencyCollection = $mapper->getOneToMany($this->getId(),
                'PriceByCurrency');
        }
        return $this->_PriceByCurrencyCollection;
    }

    /**
     * _ActorProduct::getPriceByCurrencyCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getPriceByCurrencyCollectionIds($filter = array()) {
        $col = $this->getPriceByCurrencyCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ActorProduct::setPriceByCurrencyCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setPriceByCurrencyCollection($value) {
        $this->_PriceByCurrencyCollection = $value;
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
        return 'ActorProduct';
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
        return _('Add/Update product references by customer');
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
            'Actor' => 'Actor',
            'Product' => 'Product',
            'AssociatedProductReference' => Object::TYPE_STRING,
            'BuyUnitQuantity' => Object::TYPE_DECIMAL,
            'BuyUnitType' => 'SellUnitType',
            'Priority' => Object::TYPE_BOOL);
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
            'PriceByCurrency'=>array(
                'linkClass'     => 'PriceByCurrency',
                'field'         => 'ActorProduct',
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
            'Actor'=>array(
                'label'        => _('Customer'),
                'shortlabel'   => _('Customer'),
                'usedby'       => array('grid', 'addedit', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Product'=>array(
                'label'        => _('Product'),
                'shortlabel'   => _('Product'),
                'usedby'       => array('grid', 'addedit', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'AssociatedProductReference'=>array(
                'label'        => _('Customer reference'),
                'shortlabel'   => _('Customer reference'),
                'usedby'       => array('grid', 'addedit', 'searchform'),
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