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

class _DeliveryOrder extends AbstractDocument {
    
    // Constructeur {{{

    /**
     * _DeliveryOrder::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
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
     * _DeliveryOrder::getInvoice
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
     * _DeliveryOrder::getInvoiceId
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
     * _DeliveryOrder::setInvoice
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
    // ExecutedMovement one to many relation + getter/setter {{{

    /**
     * ExecutedMovement *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ExecutedMovementCollection = false;

    /**
     * _DeliveryOrder::getExecutedMovementCollection
     *
     * @access public
     * @return object Collection
     */
    public function getExecutedMovementCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('DeliveryOrder');
            return $mapper->getManyToMany($this->getId(),
                'ExecutedMovement', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ExecutedMovementCollection) {
            $mapper = Mapper::singleton('DeliveryOrder');
            $this->_ExecutedMovementCollection = $mapper->getManyToMany($this->getId(),
                'ExecutedMovement');
        }
        return $this->_ExecutedMovementCollection;
    }

    /**
     * _DeliveryOrder::getExecutedMovementCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getExecutedMovementCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getExecutedMovementCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ExecutedMovementCollection) {
            $mapper = Mapper::singleton('DeliveryOrder');
            return $mapper->getManyToManyIds($this->getId(), 'ExecutedMovement');
        }
        return $this->_ExecutedMovementCollection->getItemIds();
    }

    /**
     * _DeliveryOrder::setExecutedMovementCollectionIds
     *
     * @access public
     * @return array
     */
    public function setExecutedMovementCollectionIds($itemIds) {
        $this->_ExecutedMovementCollection = new Collection('ExecutedMovement');
        foreach ($itemIds as $id) {
            $this->_ExecutedMovementCollection->setItem($id);
        }
    }

    /**
     * _DeliveryOrder::setExecutedMovementCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setExecutedMovementCollection($value) {
        $this->_ExecutedMovementCollection = $value;
    }

    /**
     * _DeliveryOrder::ExecutedMovementCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ExecutedMovementCollectionIsLoaded() {
        return ($this->_ExecutedMovementCollection !== false);
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
        return 'AbstractDocument';
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
    public static function getProperties($ownOnly = false) {
        $return = array(
            'Invoice' => 'Invoice');
        return $ownOnly?$return:array_merge(parent::getProperties(), $return);
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
    public static function getLinks($ownOnly = false) {
        $return = array(
            'ExecutedMovement'=>array(
                'linkClass'     => 'ExecutedMovement',
                'field'         => 'FromDeliveryOrder',
                'linkTable'     => 'adcExecutedMovement',
                'linkField'     => 'ToExecutedMovement',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ));
        return $ownOnly?$return:array_merge(parent::getLinks(), $return);
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
        return array_merge(parent::getUniqueProperties(), $return);
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
        return array_merge(parent::getEmptyForDeleteProperties(), $return);
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
    public static function getMapping($ownOnly = false) {
        $return = array();
        return $ownOnly?$return:array_merge(parent::getMapping(), $return);
    }

    // }}}
    // useInheritance() {{{

    /**
     * Détermine si l'entité est une entité qui utilise l'héritage.
     * (classe parente ou classe fille). Ceci afin de differencier les entités
     * dans le mapper car classes filles et parentes sont mappées dans la même
     * table.
     *
     * @static
     * @access public
     * @return bool
     */
    public static function useInheritance() {
        return true;
    }

    // }}}
    // getParentClassName() {{{

    /**
     * Retourne le nom de la première classe parente
     *
     * @static
     * @access public
     * @return string
     */
    public static function getParentClassName() {
        return 'AbstractDocument';
    }

    // }}}
}

?>