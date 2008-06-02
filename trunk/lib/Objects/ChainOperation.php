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

class ChainOperation extends Object {
    
    // Constructeur {{{

    /**
     * ChainOperation::__construct()
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
     * ChainOperation::getActor
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
     * ChainOperation::getActorId
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
     * ChainOperation::setActor
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
    // Operation foreignkey property + getter/setter {{{

    /**
     * Operation foreignkey
     *
     * @access private
     * @var mixed object Operation or integer
     */
    private $_Operation = false;

    /**
     * ChainOperation::getOperation
     *
     * @access public
     * @return object Operation
     */
    public function getOperation() {
        if (is_int($this->_Operation) && $this->_Operation > 0) {
            $mapper = Mapper::singleton('Operation');
            $this->_Operation = $mapper->load(
                array('Id'=>$this->_Operation));
        }
        return $this->_Operation;
    }

    /**
     * ChainOperation::getOperationId
     *
     * @access public
     * @return integer
     */
    public function getOperationId() {
        if ($this->_Operation instanceof Operation) {
            return $this->_Operation->getId();
        }
        return (int)$this->_Operation;
    }

    /**
     * ChainOperation::setOperation
     *
     * @access public
     * @param object Operation $value
     * @return void
     */
    public function setOperation($value) {
        if (is_numeric($value)) {
            $this->_Operation = (int)$value;
        } else {
            $this->_Operation = $value;
        }
    }

    // }}}
    // Chain foreignkey property + getter/setter {{{

    /**
     * Chain foreignkey
     *
     * @access private
     * @var mixed object Chain or integer
     */
    private $_Chain = false;

    /**
     * ChainOperation::getChain
     *
     * @access public
     * @return object Chain
     */
    public function getChain() {
        if (is_int($this->_Chain) && $this->_Chain > 0) {
            $mapper = Mapper::singleton('Chain');
            $this->_Chain = $mapper->load(
                array('Id'=>$this->_Chain));
        }
        return $this->_Chain;
    }

    /**
     * ChainOperation::getChainId
     *
     * @access public
     * @return integer
     */
    public function getChainId() {
        if ($this->_Chain instanceof Chain) {
            return $this->_Chain->getId();
        }
        return (int)$this->_Chain;
    }

    /**
     * ChainOperation::setChain
     *
     * @access public
     * @param object Chain $value
     * @return void
     */
    public function setChain($value) {
        if (is_numeric($value)) {
            $this->_Chain = (int)$value;
        } else {
            $this->_Chain = $value;
        }
    }

    // }}}
    // Order string property + getter/setter {{{

    /**
     * Order int property
     *
     * @access private
     * @var integer
     */
    private $_Order = 0;

    /**
     * ChainOperation::getOrder
     *
     * @access public
     * @return integer
     */
    public function getOrder() {
        return $this->_Order;
    }

    /**
     * ChainOperation::setOrder
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setOrder($value) {
        if ($value !== null) {
            $this->_Order = (int)$value;
        }
    }

    // }}}
    // ChainTask one to many relation + getter/setter {{{

    /**
     * ChainTask 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ChainTaskCollection = false;

    /**
     * ChainOperation::getChainTaskCollection
     *
     * @access public
     * @return object Collection
     */
    public function getChainTaskCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ChainOperation');
            return $mapper->getOneToMany($this->getId(),
                'ChainTask', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ChainTaskCollection) {
            $mapper = Mapper::singleton('ChainOperation');
            $this->_ChainTaskCollection = $mapper->getOneToMany($this->getId(),
                'ChainTask');
        }
        return $this->_ChainTaskCollection;
    }

    /**
     * ChainOperation::getChainTaskCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getChainTaskCollectionIds($filter = array()) {
        $col = $this->getChainTaskCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * ChainOperation::setChainTaskCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setChainTaskCollection($value) {
        $this->_ChainTaskCollection = $value;
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
        return 'ChainOperation';
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
            'Actor' => 'Actor',
            'Operation' => 'Operation',
            'Chain' => 'Chain',
            'Order' => Object::TYPE_INT);
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
            'ChainTask'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'Operation',
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