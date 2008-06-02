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

class _ComponentGroup extends Object {
    
    // Constructeur {{{

    /**
     * _ComponentGroup::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Name string property + getter/setter {{{

    /**
     * Name string property
     *
     * @access private
     * @var string
     */
    private $_Name = '';

    /**
     * _ComponentGroup::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _ComponentGroup::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
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
     * _ComponentGroup::getNomenclature
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
     * _ComponentGroup::getNomenclatureId
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
     * _ComponentGroup::setNomenclature
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
    // Component one to many relation + getter/setter {{{

    /**
     * Component 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ComponentCollection = false;

    /**
     * _ComponentGroup::getComponentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getComponentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ComponentGroup');
            return $mapper->getOneToMany($this->getId(),
                'Component', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ComponentCollection) {
            $mapper = Mapper::singleton('ComponentGroup');
            $this->_ComponentCollection = $mapper->getOneToMany($this->getId(),
                'Component');
        }
        return $this->_ComponentCollection;
    }

    /**
     * _ComponentGroup::getComponentCollectionIds
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
     * _ComponentGroup::setComponentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setComponentCollection($value) {
        $this->_ComponentCollection = $value;
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
        return 'ComponentGroup';
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
            'Name' => Object::TYPE_STRING,
            'Nomenclature' => 'Nomenclature');
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
            'Component'=>array(
                'linkClass'     => 'Component',
                'field'         => 'ComponentGroup',
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