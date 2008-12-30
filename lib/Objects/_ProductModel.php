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
 * @version   SVN: $Id: SiteAddEdit.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * _ProductModel class
 *
 */
class _ProductModel extends Object {
    
    // Constructeur {{{

    /**
     * _ProductModel::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // BaseReference string property + getter/setter {{{

    /**
     * BaseReference string property
     *
     * @access private
     * @var string
     */
    private $_BaseReference = '';

    /**
     * _ProductModel::getBaseReference
     *
     * @access public
     * @return string
     */
    public function getBaseReference() {
        return $this->_BaseReference;
    }

    /**
     * _ProductModel::setBaseReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBaseReference($value) {
        $this->_BaseReference = $value;
    }

    // }}}
    // ProductType foreignkey property + getter/setter {{{

    /**
     * ProductType foreignkey
     *
     * @access private
     * @var mixed object ProductType or integer
     */
    private $_ProductType = false;

    /**
     * _ProductModel::getProductType
     *
     * @access public
     * @return object ProductType
     */
    public function getProductType() {
        if (is_int($this->_ProductType) && $this->_ProductType > 0) {
            $mapper = Mapper::singleton('ProductType');
            $this->_ProductType = $mapper->load(
                array('Id'=>$this->_ProductType));
        }
        return $this->_ProductType;
    }

    /**
     * _ProductModel::getProductTypeId
     *
     * @access public
     * @return integer
     */
    public function getProductTypeId() {
        if ($this->_ProductType instanceof ProductType) {
            return $this->_ProductType->getId();
        }
        return (int)$this->_ProductType;
    }

    /**
     * _ProductModel::setProductType
     *
     * @access public
     * @param object ProductType $value
     * @return void
     */
    public function setProductType($value) {
        if (is_numeric($value)) {
            $this->_ProductType = (int)$value;
        } else {
            $this->_ProductType = $value;
        }
    }

    // }}}
    // Owner foreignkey property + getter/setter {{{

    /**
     * Owner foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Owner = false;

    /**
     * _ProductModel::getOwner
     *
     * @access public
     * @return object Actor
     */
    public function getOwner() {
        if (is_int($this->_Owner) && $this->_Owner > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Owner = $mapper->load(
                array('Id'=>$this->_Owner));
        }
        return $this->_Owner;
    }

    /**
     * _ProductModel::getOwnerId
     *
     * @access public
     * @return integer
     */
    public function getOwnerId() {
        if ($this->_Owner instanceof Actor) {
            return $this->_Owner->getId();
        }
        return (int)$this->_Owner;
    }

    /**
     * _ProductModel::setOwner
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setOwner($value) {
        if (is_numeric($value)) {
            $this->_Owner = (int)$value;
        } else {
            $this->_Owner = $value;
        }
    }

    // }}}
    // Manufacturer foreignkey property + getter/setter {{{

    /**
     * Manufacturer foreignkey
     *
     * @access private
     * @var mixed object Supplier or integer
     */
    private $_Manufacturer = false;

    /**
     * _ProductModel::getManufacturer
     *
     * @access public
     * @return object Supplier
     */
    public function getManufacturer() {
        if (is_int($this->_Manufacturer) && $this->_Manufacturer > 0) {
            $mapper = Mapper::singleton('Supplier');
            $this->_Manufacturer = $mapper->load(
                array('Id'=>$this->_Manufacturer));
        }
        return $this->_Manufacturer;
    }

    /**
     * _ProductModel::getManufacturerId
     *
     * @access public
     * @return integer
     */
    public function getManufacturerId() {
        if ($this->_Manufacturer instanceof Supplier) {
            return $this->_Manufacturer->getId();
        }
        return (int)$this->_Manufacturer;
    }

    /**
     * _ProductModel::setManufacturer
     *
     * @access public
     * @param object Supplier $value
     * @return void
     */
    public function setManufacturer($value) {
        if (is_numeric($value)) {
            $this->_Manufacturer = (int)$value;
        } else {
            $this->_Manufacturer = $value;
        }
    }

    // }}}
    // TVA foreignkey property + getter/setter {{{

    /**
     * TVA foreignkey
     *
     * @access private
     * @var mixed object TVA or integer
     */
    private $_TVA = false;

    /**
     * _ProductModel::getTVA
     *
     * @access public
     * @return object TVA
     */
    public function getTVA() {
        if (is_int($this->_TVA) && $this->_TVA > 0) {
            $mapper = Mapper::singleton('TVA');
            $this->_TVA = $mapper->load(
                array('Id'=>$this->_TVA));
        }
        return $this->_TVA;
    }

    /**
     * _ProductModel::getTVAId
     *
     * @access public
     * @return integer
     */
    public function getTVAId() {
        if ($this->_TVA instanceof TVA) {
            return $this->_TVA->getId();
        }
        return (int)$this->_TVA;
    }

    /**
     * _ProductModel::setTVA
     *
     * @access public
     * @param object TVA $value
     * @return void
     */
    public function setTVA($value) {
        if (is_numeric($value)) {
            $this->_TVA = (int)$value;
        } else {
            $this->_TVA = $value;
        }
    }

    // }}}
    // Description string property + getter/setter {{{

    /**
     * Description string property
     *
     * @access private
     * @var string
     */
    private $_Description = '';

    /**
     * _ProductModel::getDescription
     *
     * @access public
     * @return string
     */
    public function getDescription() {
        return $this->_Description;
    }

    /**
     * _ProductModel::setDescription
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDescription($value) {
        $this->_Description = $value;
    }

    // }}}
    // Size one to many relation + getter/setter {{{

    /**
     * Size *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_SizeCollection = false;

    /**
     * _ProductModel::getSizeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getSizeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ProductModel');
            return $mapper->getManyToMany($this->getId(),
                'Size', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_SizeCollection) {
            $mapper = Mapper::singleton('ProductModel');
            $this->_SizeCollection = $mapper->getManyToMany($this->getId(),
                'Size');
        }
        return $this->_SizeCollection;
    }

    /**
     * _ProductModel::getSizeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getSizeCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getSizeCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_SizeCollection) {
            $mapper = Mapper::singleton('ProductModel');
            return $mapper->getManyToManyIds($this->getId(), 'Size');
        }
        return $this->_SizeCollection->getItemIds();
    }

    /**
     * _ProductModel::setSizeCollectionIds
     *
     * @access public
     * @return array
     */
    public function setSizeCollectionIds($itemIds) {
        $this->_SizeCollection = new Collection('Size');
        foreach ($itemIds as $id) {
            $this->_SizeCollection->setItem($id);
        }
    }

    /**
     * _ProductModel::setSizeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setSizeCollection($value) {
        $this->_SizeCollection = $value;
    }

    /**
     * _ProductModel::SizeCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function SizeCollectionIsLoaded() {
        return ($this->_SizeCollection !== false);
    }

    // }}}
    // Product one to many relation + getter/setter {{{

    /**
     * Product 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ProductCollection = false;

    /**
     * _ProductModel::getProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ProductModel');
            return $mapper->getOneToMany($this->getId(),
                'Product', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ProductCollection) {
            $mapper = Mapper::singleton('ProductModel');
            $this->_ProductCollection = $mapper->getOneToMany($this->getId(),
                'Product');
        }
        return $this->_ProductCollection;
    }

    /**
     * _ProductModel::getProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getProductCollectionIds($filter = array()) {
        $col = $this->getProductCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ProductModel::setProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setProductCollection($value) {
        $this->_ProductCollection = $value;
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
        return 'ProductModel';
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
            'BaseReference' => Object::TYPE_STRING,
            'ProductType' => 'ProductType',
            'Owner' => 'Actor',
            'Manufacturer' => 'Supplier',
            'TVA' => 'TVA',
            'Description' => Object::TYPE_TEXT);
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
            'Size'=>array(
                'linkClass'     => 'RTWSize',
                'field'         => 'FromProductModel',
                'linkTable'     => 'productModelRTWSize',
                'linkField'     => 'ToRTWSize',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Product'=>array(
                'linkClass'     => 'Product',
                'field'         => 'ProductModel',
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
        $return = array('BaseReference');
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
            'BaseReference'=>array(
                'label'        => _('Reference'),
                'shortlabel'   => _('Reference'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ProductType'=>array(
                'label'        => _('Product type'),
                'shortlabel'   => _('Product type'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Owner'=>array(
                'label'        => _('Owner'),
                'shortlabel'   => _('Owner'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Manufacturer'=>array(
                'label'        => _('Manufacturer'),
                'shortlabel'   => _('Manufacturer'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'TVA'=>array(
                'label'        => _('VAT'),
                'shortlabel'   => _('VAT'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Description'=>array(
                'label'        => _('Description'),
                'shortlabel'   => _('Description'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Size'=>array(
                'label'        => _('Available sizes'),
                'shortlabel'   => _('Available sizes'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
    // toString() {{{

    /**
     * Retourne la représentation texte de l'objet
     *
     * @access public
     * @return string
     */
    public function toString() {
        return $this->getBaseReference();
    }

    // }}}
    // getToStringAttribute() {{{

    /**
     * Retourne le nom de l'attribut pointé par toString()
     *
     * @static
     * @access public
     * @return string
     */
    public function getToStringAttribute() {
        return 'BaseReference';
    }

    // }}}
}

?>