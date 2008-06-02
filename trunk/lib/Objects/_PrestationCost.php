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

class _PrestationCost extends Object {
    
    // Constructeur {{{

    /**
     * _PrestationCost::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
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
     * _PrestationCost::getPrestation
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
     * _PrestationCost::getPrestationId
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
     * _PrestationCost::setPrestation
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
    // CostRange one to many relation + getter/setter {{{

    /**
     * CostRange 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CostRangeCollection = false;

    /**
     * _PrestationCost::getCostRangeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCostRangeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('PrestationCost');
            return $mapper->getOneToMany($this->getId(),
                'CostRange', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CostRangeCollection) {
            $mapper = Mapper::singleton('PrestationCost');
            $this->_CostRangeCollection = $mapper->getOneToMany($this->getId(),
                'CostRange');
        }
        return $this->_CostRangeCollection;
    }

    /**
     * _PrestationCost::getCostRangeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCostRangeCollectionIds($filter = array()) {
        $col = $this->getCostRangeCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _PrestationCost::setCostRangeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCostRangeCollection($value) {
        $this->_CostRangeCollection = $value;
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
        return 'PrestationCost';
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
            'Prestation' => 'Prestation');
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
            'CostRange'=>array(
                'linkClass'     => 'CostRange',
                'field'         => 'PrestationCost',
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
        return array('grid', 'add', 'edit', 'del');
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