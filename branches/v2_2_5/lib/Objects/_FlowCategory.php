<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IMPORTANT: This is a generated file, please do not edit.
 *
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

/**
 * _FlowCategory class
 *
 */
class _FlowCategory extends Object {
    
    // Constructeur {{{

    /**
     * _FlowCategory::__construct()
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
     * _FlowCategory::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _FlowCategory::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Parent foreignkey property + getter/setter {{{

    /**
     * Parent foreignkey
     *
     * @access private
     * @var mixed object FlowCategory or integer
     */
    private $_Parent = false;

    /**
     * _FlowCategory::getParent
     *
     * @access public
     * @return object FlowCategory
     */
    public function getParent() {
        if (is_int($this->_Parent) && $this->_Parent > 0) {
            $mapper = Mapper::singleton('FlowCategory');
            $this->_Parent = $mapper->load(
                array('Id'=>$this->_Parent));
        }
        return $this->_Parent;
    }

    /**
     * _FlowCategory::getParentId
     *
     * @access public
     * @return integer
     */
    public function getParentId() {
        if ($this->_Parent instanceof FlowCategory) {
            return $this->_Parent->getId();
        }
        return (int)$this->_Parent;
    }

    /**
     * _FlowCategory::setParent
     *
     * @access public
     * @param object FlowCategory $value
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
    // DisplayOrder string property + getter/setter {{{

    /**
     * DisplayOrder int property
     *
     * @access private
     * @var integer
     */
    private $_DisplayOrder = 0;

    /**
     * _FlowCategory::getDisplayOrder
     *
     * @access public
     * @return integer
     */
    public function getDisplayOrder() {
        return $this->_DisplayOrder;
    }

    /**
     * _FlowCategory::setDisplayOrder
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDisplayOrder($value) {
        if ($value !== null) {
            $this->_DisplayOrder = (int)$value;
        }
    }

    // }}}
    // FlowCategory one to many relation + getter/setter {{{

    /**
     * FlowCategory 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_FlowCategoryCollection = false;

    /**
     * _FlowCategory::getFlowCategoryCollection
     *
     * @access public
     * @return object Collection
     */
    public function getFlowCategoryCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('FlowCategory');
            return $mapper->getOneToMany($this->getId(),
                'FlowCategory', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_FlowCategoryCollection) {
            $mapper = Mapper::singleton('FlowCategory');
            $this->_FlowCategoryCollection = $mapper->getOneToMany($this->getId(),
                'FlowCategory');
        }
        return $this->_FlowCategoryCollection;
    }

    /**
     * _FlowCategory::getFlowCategoryCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getFlowCategoryCollectionIds($filter = array()) {
        $col = $this->getFlowCategoryCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _FlowCategory::setFlowCategoryCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setFlowCategoryCollection($value) {
        $this->_FlowCategoryCollection = $value;
    }

    // }}}
    // FlowType one to many relation + getter/setter {{{

    /**
     * FlowType 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_FlowTypeCollection = false;

    /**
     * _FlowCategory::getFlowTypeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getFlowTypeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('FlowCategory');
            return $mapper->getOneToMany($this->getId(),
                'FlowType', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_FlowTypeCollection) {
            $mapper = Mapper::singleton('FlowCategory');
            $this->_FlowTypeCollection = $mapper->getOneToMany($this->getId(),
                'FlowType');
        }
        return $this->_FlowTypeCollection;
    }

    /**
     * _FlowCategory::getFlowTypeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getFlowTypeCollectionIds($filter = array()) {
        $col = $this->getFlowTypeCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _FlowCategory::setFlowTypeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setFlowTypeCollection($value) {
        $this->_FlowTypeCollection = $value;
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
        return 'FlowCategory';
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
        return _('Categories of expenses or receipts');
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
            'Parent' => 'FlowCategory',
            'DisplayOrder' => Object::TYPE_INT);
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
            'FlowCategory'=>array(
                'linkClass'     => 'FlowCategory',
                'field'         => 'Parent',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'FlowType'=>array(
                'linkClass'     => 'FlowType',
                'field'         => 'FlowCategory',
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
        return array('grid', 'searchform', 'add', 'edit', 'del');
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
                'usedby'       => array('grid', 'addedit', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Parent'=>array(
                'label'        => _('Parent'),
                'shortlabel'   => _('Parent'),
                'usedby'       => array('grid', 'addedit', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'DisplayOrder'=>array(
                'label'        => _('Display order'),
                'shortlabel'   => _('Display order'),
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