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

class _ConcreteComponent extends Object {
    
    // Constructeur {{{

    /**
     * _ConcreteComponent::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Quantity float property + getter/setter {{{

    /**
     * Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Quantity = 1;

    /**
     * _ConcreteComponent::getQuantity
     *
     * @access public
     * @return float
     */
    public function getQuantity() {
        return $this->_Quantity;
    }

    /**
     * _ConcreteComponent::setQuantity
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
    // Parent foreignkey property + getter/setter {{{

    /**
     * Parent foreignkey
     *
     * @access private
     * @var mixed object ConcreteProduct or integer
     */
    private $_Parent = false;

    /**
     * _ConcreteComponent::getParent
     *
     * @access public
     * @return object ConcreteProduct
     */
    public function getParent() {
        if (is_int($this->_Parent) && $this->_Parent > 0) {
            $mapper = Mapper::singleton('ConcreteProduct');
            $this->_Parent = $mapper->load(
                array('Id'=>$this->_Parent));
        }
        return $this->_Parent;
    }

    /**
     * _ConcreteComponent::getParentId
     *
     * @access public
     * @return integer
     */
    public function getParentId() {
        if ($this->_Parent instanceof ConcreteProduct) {
            return $this->_Parent->getId();
        }
        return (int)$this->_Parent;
    }

    /**
     * _ConcreteComponent::setParent
     *
     * @access public
     * @param object ConcreteProduct $value
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
    // ConcreteProduct foreignkey property + getter/setter {{{

    /**
     * ConcreteProduct foreignkey
     *
     * @access private
     * @var mixed object ConcreteProduct or integer
     */
    private $_ConcreteProduct = false;

    /**
     * _ConcreteComponent::getConcreteProduct
     *
     * @access public
     * @return object ConcreteProduct
     */
    public function getConcreteProduct() {
        if (is_int($this->_ConcreteProduct) && $this->_ConcreteProduct > 0) {
            $mapper = Mapper::singleton('ConcreteProduct');
            $this->_ConcreteProduct = $mapper->load(
                array('Id'=>$this->_ConcreteProduct));
        }
        return $this->_ConcreteProduct;
    }

    /**
     * _ConcreteComponent::getConcreteProductId
     *
     * @access public
     * @return integer
     */
    public function getConcreteProductId() {
        if ($this->_ConcreteProduct instanceof ConcreteProduct) {
            return $this->_ConcreteProduct->getId();
        }
        return (int)$this->_ConcreteProduct;
    }

    /**
     * _ConcreteComponent::setConcreteProduct
     *
     * @access public
     * @param object ConcreteProduct $value
     * @return void
     */
    public function setConcreteProduct($value) {
        if (is_numeric($value)) {
            $this->_ConcreteProduct = (int)$value;
        } else {
            $this->_ConcreteProduct = $value;
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
     * _ConcreteComponent::getActivatedChainTaskCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedChainTaskCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ConcreteComponent');
            return $mapper->getManyToMany($this->getId(),
                'ActivatedChainTask', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedChainTaskCollection) {
            $mapper = Mapper::singleton('ConcreteComponent');
            $this->_ActivatedChainTaskCollection = $mapper->getManyToMany($this->getId(),
                'ActivatedChainTask');
        }
        return $this->_ActivatedChainTaskCollection;
    }

    /**
     * _ConcreteComponent::getActivatedChainTaskCollectionIds
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
            $mapper = Mapper::singleton('ConcreteComponent');
            return $mapper->getManyToManyIds($this->getId(), 'ActivatedChainTask');
        }
        return $this->_ActivatedChainTaskCollection->getItemIds();
    }

    /**
     * _ConcreteComponent::setActivatedChainTaskCollectionIds
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
     * _ConcreteComponent::setActivatedChainTaskCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedChainTaskCollection($value) {
        $this->_ActivatedChainTaskCollection = $value;
    }

    /**
     * _ConcreteComponent::ActivatedChainTaskCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ActivatedChainTaskCollectionIsLoaded() {
        return ($this->_ActivatedChainTaskCollection !== false);
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
        return 'ConcreteComponent';
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
            'Quantity' => Object::TYPE_DECIMAL,
            'Parent' => 'ConcreteProduct',
            'ConcreteProduct' => 'ConcreteProduct');
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
                'field'         => 'FromConcreteComponent',
                'linkTable'     => 'ackConcreteComponent',
                'linkField'     => 'ToActivatedChainTask',
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