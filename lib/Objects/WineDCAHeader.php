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
 * @version   SVN: $Id: _ForwardingForm.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

class WineDCAHeader extends AbstractDocument {
    
    // Constructeur {{{

    /**
     * _ForwardingForm::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // ForwardingFormPacking one to many relation + getter/setter {{{

    /**
     * ForwardingFormPacking 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ForwardingFormPackingCollection = false;

    /**
     * _ForwardingForm::getForwardingFormPackingCollection
     *
     * @access public
     * @return object Collection
     */
    public function getForwardingFormPackingCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ForwardingForm');
            return $mapper->getOneToMany($this->getId(),
                'ForwardingFormPacking', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ForwardingFormPackingCollection) {
            $mapper = Mapper::singleton('ForwardingForm');
            $this->_ForwardingFormPackingCollection = $mapper->getOneToMany($this->getId(),
                'ForwardingFormPacking');
        }
        return $this->_ForwardingFormPackingCollection;
    }

    /**
     * _ForwardingForm::getForwardingFormPackingCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getForwardingFormPackingCollectionIds($filter = array()) {
        $col = $this->getForwardingFormPackingCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ForwardingForm::setForwardingFormPackingCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setForwardingFormPackingCollection($value) {
        $this->_ForwardingFormPackingCollection = $value;
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
     * _ForwardingForm::getLocationExecutedMovementCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLocationExecutedMovementCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ForwardingForm');
            return $mapper->getOneToMany($this->getId(),
                'LocationExecutedMovement', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LocationExecutedMovementCollection) {
            $mapper = Mapper::singleton('ForwardingForm');
            $this->_LocationExecutedMovementCollection = $mapper->getOneToMany($this->getId(),
                'LocationExecutedMovement');
        }
        return $this->_LocationExecutedMovementCollection;
    }

    /**
     * _ForwardingForm::getLocationExecutedMovementCollectionIds
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
     * _ForwardingForm::setLocationExecutedMovementCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLocationExecutedMovementCollection($value) {
        $this->_LocationExecutedMovementCollection = $value;
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
        $return = array();
        return array_merge(parent::getProperties(), $return);
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
            'ForwardingFormPacking'=>array(
                'linkClass'     => 'ForwardingFormPacking',
                'field'         => 'ForwardingForm',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'LocationExecutedMovement'=>array(
                'linkClass'     => 'LocationExecutedMovement',
                'field'         => 'ForwardingForm',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
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
