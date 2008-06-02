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

class RessourceGroup extends Object {
    
    // Constructeur {{{

    /**
     * RessourceGroup::__construct()
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
     * RessourceGroup::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * RessourceGroup::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Active string property + getter/setter {{{

    /**
     * Active int property
     *
     * @access private
     * @var integer
     */
    private $_Active = 1;

    /**
     * RessourceGroup::getActive
     *
     * @access public
     * @return integer
     */
    public function getActive() {
        return $this->_Active;
    }

    /**
     * RessourceGroup::setActive
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setActive($value) {
        if ($value !== null) {
            $this->_Active = (int)$value;
        }
    }

    // }}}
    // AddNomenclatureCosts string property + getter/setter {{{

    /**
     * AddNomenclatureCosts int property
     *
     * @access private
     * @var integer
     */
    private $_AddNomenclatureCosts = 1;

    /**
     * RessourceGroup::getAddNomenclatureCosts
     *
     * @access public
     * @return integer
     */
    public function getAddNomenclatureCosts() {
        return $this->_AddNomenclatureCosts;
    }

    /**
     * RessourceGroup::setAddNomenclatureCosts
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setAddNomenclatureCosts($value) {
        if ($value !== null) {
            $this->_AddNomenclatureCosts = (int)$value;
        }
    }

    // }}}
    // ActivatedChainTask one to many relation + getter/setter {{{

    /**
     * ActivatedChainTask 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActivatedChainTaskCollection = false;

    /**
     * RessourceGroup::getActivatedChainTaskCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedChainTaskCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('RessourceGroup');
            return $mapper->getOneToMany($this->getId(),
                'ActivatedChainTask', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedChainTaskCollection) {
            $mapper = Mapper::singleton('RessourceGroup');
            $this->_ActivatedChainTaskCollection = $mapper->getOneToMany($this->getId(),
                'ActivatedChainTask');
        }
        return $this->_ActivatedChainTaskCollection;
    }

    /**
     * RessourceGroup::getActivatedChainTaskCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActivatedChainTaskCollectionIds($filter = array()) {
        $col = $this->getActivatedChainTaskCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * RessourceGroup::setActivatedChainTaskCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedChainTaskCollection($value) {
        $this->_ActivatedChainTaskCollection = $value;
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
     * RessourceGroup::getChainTaskCollection
     *
     * @access public
     * @return object Collection
     */
    public function getChainTaskCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('RessourceGroup');
            return $mapper->getOneToMany($this->getId(),
                'ChainTask', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ChainTaskCollection) {
            $mapper = Mapper::singleton('RessourceGroup');
            $this->_ChainTaskCollection = $mapper->getOneToMany($this->getId(),
                'ChainTask');
        }
        return $this->_ChainTaskCollection;
    }

    /**
     * RessourceGroup::getChainTaskCollectionIds
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
     * RessourceGroup::setChainTaskCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setChainTaskCollection($value) {
        $this->_ChainTaskCollection = $value;
    }

    // }}}
    // RessourceRessourceGroup one to many relation + getter/setter {{{

    /**
     * RessourceRessourceGroup 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_RessourceRessourceGroupCollection = false;

    /**
     * RessourceGroup::getRessourceRessourceGroupCollection
     *
     * @access public
     * @return object Collection
     */
    public function getRessourceRessourceGroupCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('RessourceGroup');
            return $mapper->getOneToMany($this->getId(),
                'RessourceRessourceGroup', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_RessourceRessourceGroupCollection) {
            $mapper = Mapper::singleton('RessourceGroup');
            $this->_RessourceRessourceGroupCollection = $mapper->getOneToMany($this->getId(),
                'RessourceRessourceGroup');
        }
        return $this->_RessourceRessourceGroupCollection;
    }

    /**
     * RessourceGroup::getRessourceRessourceGroupCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getRessourceRessourceGroupCollectionIds($filter = array()) {
        $col = $this->getRessourceRessourceGroupCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * RessourceGroup::setRessourceRessourceGroupCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setRessourceRessourceGroupCollection($value) {
        $this->_RessourceRessourceGroupCollection = $value;
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
        return 'RessourceGroup';
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
        return _('Costs models');
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
            'Active' => Object::TYPE_BOOL,
            'AddNomenclatureCosts' => Object::TYPE_BOOL);
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
                'field'         => 'RessourceGroup',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainTask'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'RessourceGroup',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RessourceRessourceGroup'=>array(
                'linkClass'     => 'RessourceRessourceGroup',
                'field'         => 'RessourceGroup',
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
        $return = array('Name');
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
        return array('searchform', 'grid', 'add', 'edit', 'del');
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
            'Name'=>array(
                'label'        => _('Name'),
                'shortlabel'   => _('Name'),
                'usedby'       => array('searchform', 'addedit', 'grid'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Active'=>array(
                'label'        => _('Active'),
                'shortlabel'   => _('Active'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'AddNomenclatureCosts'=>array(
                'label'        => _('Include nomenclature costs'),
                'shortlabel'   => _('Include nomenclature costs'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
}

?>