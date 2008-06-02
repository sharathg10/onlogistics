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

class _Component extends Object {
    
    // Constructeur {{{

    /**
     * _Component::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
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
     * _Component::getLevel
     *
     * @access public
     * @return integer
     */
    public function getLevel() {
        return $this->_Level;
    }

    /**
     * _Component::setLevel
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setLevel($value) {
        $this->_Level = ($value===null || $value === '')?null:(int)$value;
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
     * _Component::getQuantity
     *
     * @access public
     * @return float
     */
    public function getQuantity() {
        return $this->_Quantity;
    }

    /**
     * _Component::setQuantity
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
    // Nomenclature foreignkey property + getter/setter {{{

    /**
     * Nomenclature foreignkey
     *
     * @access private
     * @var mixed object Nomenclature or integer
     */
    private $_Nomenclature = false;

    /**
     * _Component::getNomenclature
     *
     * @access public
     * @return object Nomenclature
     */
    public function getNomenclature() {
        if (is_int($this->_Nomenclature) && $this->_Nomenclature > 0) {
            $mapper = Mapper::singleton('Nomenclature');
            $this->_Nomenclature = $mapper->load(
                array('Id'=>$this->_Nomenclature));
        }
        return $this->_Nomenclature;
    }

    /**
     * _Component::getNomenclatureId
     *
     * @access public
     * @return integer
     */
    public function getNomenclatureId() {
        if ($this->_Nomenclature instanceof Nomenclature) {
            return $this->_Nomenclature->getId();
        }
        return (int)$this->_Nomenclature;
    }

    /**
     * _Component::setNomenclature
     *
     * @access public
     * @param object Nomenclature $value
     * @return void
     */
    public function setNomenclature($value) {
        if (is_numeric($value)) {
            $this->_Nomenclature = (int)$value;
        } else {
            $this->_Nomenclature = $value;
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
     * _Component::getProduct
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
     * _Component::getProductId
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
     * _Component::setProduct
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
    // Parent foreignkey property + getter/setter {{{

    /**
     * Parent foreignkey
     *
     * @access private
     * @var mixed object Component or integer
     */
    private $_Parent = false;

    /**
     * _Component::getParent
     *
     * @access public
     * @return object Component
     */
    public function getParent() {
        if (is_int($this->_Parent) && $this->_Parent > 0) {
            $mapper = Mapper::singleton('Component');
            $this->_Parent = $mapper->load(
                array('Id'=>$this->_Parent));
        }
        return $this->_Parent;
    }

    /**
     * _Component::getParentId
     *
     * @access public
     * @return integer
     */
    public function getParentId() {
        if ($this->_Parent instanceof Component) {
            return $this->_Parent->getId();
        }
        return (int)$this->_Parent;
    }

    /**
     * _Component::setParent
     *
     * @access public
     * @param object Component $value
     * @return void
     */
    public function setParent($value) {
        if (is_numeric($value)) {
            $this->_Parent = (int)$value;
        } else {
            $this->_Parent = $value;
        }
    }

    // }}}
    // ComponentGroup foreignkey property + getter/setter {{{

    /**
     * ComponentGroup foreignkey
     *
     * @access private
     * @var mixed object ComponentGroup or integer
     */
    private $_ComponentGroup = false;

    /**
     * _Component::getComponentGroup
     *
     * @access public
     * @return object ComponentGroup
     */
    public function getComponentGroup() {
        if (is_int($this->_ComponentGroup) && $this->_ComponentGroup > 0) {
            $mapper = Mapper::singleton('ComponentGroup');
            $this->_ComponentGroup = $mapper->load(
                array('Id'=>$this->_ComponentGroup));
        }
        return $this->_ComponentGroup;
    }

    /**
     * _Component::getComponentGroupId
     *
     * @access public
     * @return integer
     */
    public function getComponentGroupId() {
        if ($this->_ComponentGroup instanceof ComponentGroup) {
            return $this->_ComponentGroup->getId();
        }
        return (int)$this->_ComponentGroup;
    }

    /**
     * _Component::setComponentGroup
     *
     * @access public
     * @param object ComponentGroup $value
     * @return void
     */
    public function setComponentGroup($value) {
        if (is_numeric($value)) {
            $this->_ComponentGroup = (int)$value;
        } else {
            $this->_ComponentGroup = $value;
        }
    }

    // }}}
    // Component one to many relation + getter/setter {{{

    /**
     * Component 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ComponentCollection = false;

    /**
     * _Component::getComponentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getComponentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Component');
            return $mapper->getOneToMany($this->getId(),
                'Component', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ComponentCollection) {
            $mapper = Mapper::singleton('Component');
            $this->_ComponentCollection = $mapper->getOneToMany($this->getId(),
                'Component');
        }
        return $this->_ComponentCollection;
    }

    /**
     * _Component::getComponentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getComponentCollectionIds($filter = array()) {
        $col = $this->getComponentCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Component::setComponentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setComponentCollection($value) {
        $this->_ComponentCollection = $value;
    }

    // }}}
    // ConcreteProduct one to many relation + getter/setter {{{

    /**
     * ConcreteProduct 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ConcreteProductCollection = false;

    /**
     * _Component::getConcreteProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getConcreteProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Component');
            return $mapper->getOneToMany($this->getId(),
                'ConcreteProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ConcreteProductCollection) {
            $mapper = Mapper::singleton('Component');
            $this->_ConcreteProductCollection = $mapper->getOneToMany($this->getId(),
                'ConcreteProduct');
        }
        return $this->_ConcreteProductCollection;
    }

    /**
     * _Component::getConcreteProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getConcreteProductCollectionIds($filter = array()) {
        $col = $this->getConcreteProductCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Component::setConcreteProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setConcreteProductCollection($value) {
        $this->_ConcreteProductCollection = $value;
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
        return 'Component';
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
            'Level' => Object::TYPE_INT,
            'Quantity' => Object::TYPE_DECIMAL,
            'Nomenclature' => 'Nomenclature',
            'Product' => 'Product',
            'Parent' => 'Component',
            'ComponentGroup' => 'ComponentGroup');
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
                'field'         => 'Component',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainTask'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'Component',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Component'=>array(
                'linkClass'     => 'Component',
                'field'         => 'Parent',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ConcreteProduct'=>array(
                'linkClass'     => 'ConcreteProduct',
                'field'         => 'Component',
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