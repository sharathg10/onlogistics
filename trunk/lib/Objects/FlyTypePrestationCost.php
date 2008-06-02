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

class FlyTypePrestationCost extends PrestationCost {
    
    // Constructeur {{{

    /**
     * FlyTypePrestationCost::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // FlyType one to many relation + getter/setter {{{

    /**
     * FlyType *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_FlyTypeCollection = false;

    /**
     * FlyTypePrestationCost::getFlyTypeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getFlyTypeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('FlyTypePrestationCost');
            return $mapper->getManyToMany($this->getId(),
                'FlyType', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_FlyTypeCollection) {
            $mapper = Mapper::singleton('FlyTypePrestationCost');
            $this->_FlyTypeCollection = $mapper->getManyToMany($this->getId(),
                'FlyType');
        }
        return $this->_FlyTypeCollection;
    }

    /**
     * FlyTypePrestationCost::getFlyTypeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getFlyTypeCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getFlyTypeCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_FlyTypeCollection) {
            $mapper = Mapper::singleton('FlyTypePrestationCost');
            return $mapper->getManyToManyIds($this->getId(), 'FlyType');
        }
        return $this->_FlyTypeCollection->getItemIds();
    }

    /**
     * FlyTypePrestationCost::setFlyTypeCollectionIds
     *
     * @access public
     * @return array
     */
    public function setFlyTypeCollectionIds($itemIds) {
        $this->_FlyTypeCollection = new Collection('FlyType');
        foreach ($itemIds as $id) {
            $this->_FlyTypeCollection->setItem($id);
        }
    }

    /**
     * FlyTypePrestationCost::setFlyTypeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setFlyTypeCollection($value) {
        $this->_FlyTypeCollection = $value;
    }

    /**
     * FlyTypePrestationCost::FlyTypeCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function FlyTypeCollectionIsLoaded() {
        return ($this->_FlyTypeCollection !== false);
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
        return _('Prices by airplane type associated to the service');
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
            'FlyType'=>array(
                'linkClass'     => 'FlyType',
                'field'         => 'FromFlyTypePrestationCost',
                'linkTable'     => 'fltpcFlyType',
                'linkField'     => 'ToFlyType',
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
    public static function getMapping($ownOnly = false) {
        $return = array(
            'FlyType'=>array(
                'label'        => _('Airplane type'),
                'shortlabel'   => _('Airplane type'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
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
        return 'PrestationCost';
    }

    // }}}
}

?>