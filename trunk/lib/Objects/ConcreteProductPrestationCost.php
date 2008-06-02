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

class ConcreteProductPrestationCost extends PrestationCost {
    
    // Constructeur {{{

    /**
     * ConcreteProductPrestationCost::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // ConcreteProduct one to many relation + getter/setter {{{

    /**
     * ConcreteProduct *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ConcreteProductCollection = false;

    /**
     * ConcreteProductPrestationCost::getConcreteProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getConcreteProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ConcreteProductPrestationCost');
            return $mapper->getManyToMany($this->getId(),
                'ConcreteProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ConcreteProductCollection) {
            $mapper = Mapper::singleton('ConcreteProductPrestationCost');
            $this->_ConcreteProductCollection = $mapper->getManyToMany($this->getId(),
                'ConcreteProduct');
        }
        return $this->_ConcreteProductCollection;
    }

    /**
     * ConcreteProductPrestationCost::getConcreteProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getConcreteProductCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getConcreteProductCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ConcreteProductCollection) {
            $mapper = Mapper::singleton('ConcreteProductPrestationCost');
            return $mapper->getManyToManyIds($this->getId(), 'ConcreteProduct');
        }
        return $this->_ConcreteProductCollection->getItemIds();
    }

    /**
     * ConcreteProductPrestationCost::setConcreteProductCollectionIds
     *
     * @access public
     * @return array
     */
    public function setConcreteProductCollectionIds($itemIds) {
        $this->_ConcreteProductCollection = new Collection('ConcreteProduct');
        foreach ($itemIds as $id) {
            $this->_ConcreteProductCollection->setItem($id);
        }
    }

    /**
     * ConcreteProductPrestationCost::setConcreteProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setConcreteProductCollection($value) {
        $this->_ConcreteProductCollection = $value;
    }

    /**
     * ConcreteProductPrestationCost::ConcreteProductCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ConcreteProductCollectionIsLoaded() {
        return ($this->_ConcreteProductCollection !== false);
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
        return _('Price by SN/Lot associated to the service');
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
            'ConcreteProduct'=>array(
                'linkClass'     => 'ConcreteProduct',
                'field'         => 'FromConcreteProductPrestationCost',
                'linkTable'     => 'cppcConcreteProduct',
                'linkField'     => 'ToConcreteProduct',
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
            'ConcreteProduct'=>array(
                'label'        => _('SN/Lot'),
                'shortlabel'   => _('SN/Lot'),
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