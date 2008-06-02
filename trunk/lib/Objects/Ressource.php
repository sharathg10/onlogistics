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

class Ressource extends Object {
    // class constants {{{

    const RESSOURCE_TYPE_ENERGY = 0;
    const RESSOURCE_TYPE_HUMAN = 1;
    const RESSOURCE_TYPE_ENVIRONMENT = 2;
    const RESSOURCE_TYPE_SUBCONTRACTING = 3;
    const RESSOURCE_TYPE_MACHINE = 4;
    const RESSOURCE_TYPE_MISC = 5;
    const RESSOURCE_TYPE_PRODUCT = 6;
    const COSTTYPE_FORFAIT = 0;
    const COSTTYPE_DAILY = 1;
    const COSTTYPE_HOURLY = 2;
    const COSTTYPE_KG = 3;
    const COSTTYPE_SQUAREMETTER = 4;
    const COSTTYPE_CUBEMETTER = 5;
    const COSTTYPE_LM = 6;
    const COSTTYPE_QUANTITY = 7;
    const COSTTYPE_KM = 8;

    // }}}
    // Constructeur {{{

    /**
     * Ressource::__construct()
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
     * Ressource::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * Ressource::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Type const property + getter/setter/getTypeConstArray {{{

    /**
     * Type int property
     *
     * @access private
     * @var integer
     */
    private $_Type = 0;

    /**
     * Ressource::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * Ressource::setType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setType($value) {
        if ($value !== null) {
            $this->_Type = (int)$value;
        }
    }

    /**
     * Ressource::getTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getTypeConstArray($keys = false) {
        $array = array(
            Ressource::RESSOURCE_TYPE_ENERGY => _("Energy"), 
            Ressource::RESSOURCE_TYPE_HUMAN => _("Workforce"), 
            Ressource::RESSOURCE_TYPE_ENVIRONMENT => _("Environment"), 
            Ressource::RESSOURCE_TYPE_SUBCONTRACTING => _("Subcontracting"), 
            Ressource::RESSOURCE_TYPE_MACHINE => _("Machine"), 
            Ressource::RESSOURCE_TYPE_MISC => _("Miscellaneous"), 
            Ressource::RESSOURCE_TYPE_PRODUCT => _("Product")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Cost float property + getter/setter {{{

    /**
     * Cost float property
     *
     * @access private
     * @var float
     */
    private $_Cost = 0;

    /**
     * Ressource::getCost
     *
     * @access public
     * @return float
     */
    public function getCost() {
        return $this->_Cost;
    }

    /**
     * Ressource::setCost
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCost($value) {
        if ($value !== null) {
            $this->_Cost = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Quantity float property + getter/setter {{{

    /**
     * Quantity float property
     *
     * @access private
     * @var float
     */
    private $_Quantity = 0;

    /**
     * Ressource::getQuantity
     *
     * @access public
     * @return float
     */
    public function getQuantity() {
        return $this->_Quantity;
    }

    /**
     * Ressource::setQuantity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setQuantity($value) {
        if ($value !== null) {
            $this->_Quantity = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // CostType const property + getter/setter/getCostTypeConstArray {{{

    /**
     * CostType int property
     *
     * @access private
     * @var integer
     */
    private $_CostType = 0;

    /**
     * Ressource::getCostType
     *
     * @access public
     * @return integer
     */
    public function getCostType() {
        return $this->_CostType;
    }

    /**
     * Ressource::setCostType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCostType($value) {
        if ($value !== null) {
            $this->_CostType = (int)$value;
        }
    }

    /**
     * Ressource::getCostTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getCostTypeConstArray($keys = false) {
        $array = array(
            Ressource::COSTTYPE_FORFAIT => _("fixed price"), 
            Ressource::COSTTYPE_DAILY => _("daily"), 
            Ressource::COSTTYPE_HOURLY => _("hourly"), 
            Ressource::COSTTYPE_KG => _("Kg"), 
            Ressource::COSTTYPE_SQUAREMETTER => _("by square meter"), 
            Ressource::COSTTYPE_CUBEMETTER => _("by cube meter"), 
            Ressource::COSTTYPE_LM => _("by linear meter"), 
            Ressource::COSTTYPE_QUANTITY => _("by unit"), 
            Ressource::COSTTYPE_KM => _("by kilometer")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Product foreignkey property + getter/setter {{{

    /**
     * Product foreignkey
     *
     * @access private
     * @var mixed object Product or integer
     */
    private $_Product = false;

    /**
     * Ressource::getProduct
     *
     * @access public
     * @return object Product
     */
    public function getProduct() {
        if (is_int($this->_Product) && $this->_Product > 0) {
            $mapper = Mapper::singleton('Product');
            $this->_Product = $mapper->load(
                array('Id'=>$this->_Product));
        }
        return $this->_Product;
    }

    /**
     * Ressource::getProductId
     *
     * @access public
     * @return integer
     */
    public function getProductId() {
        if ($this->_Product instanceof Product) {
            return $this->_Product->getId();
        }
        return (int)$this->_Product;
    }

    /**
     * Ressource::setProduct
     *
     * @access public
     * @param object Product $value
     * @return void
     */
    public function setProduct($value) {
        if (is_numeric($value)) {
            $this->_Product = (int)$value;
        } else {
            $this->_Product = $value;
        }
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
     * Ressource::getRessourceRessourceGroupCollection
     *
     * @access public
     * @return object Collection
     */
    public function getRessourceRessourceGroupCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Ressource');
            return $mapper->getOneToMany($this->getId(),
                'RessourceRessourceGroup', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_RessourceRessourceGroupCollection) {
            $mapper = Mapper::singleton('Ressource');
            $this->_RessourceRessourceGroupCollection = $mapper->getOneToMany($this->getId(),
                'RessourceRessourceGroup');
        }
        return $this->_RessourceRessourceGroupCollection;
    }

    /**
     * Ressource::getRessourceRessourceGroupCollectionIds
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
     * Ressource::setRessourceRessourceGroupCollection
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
        return 'Ressource';
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
        return _('Resources');
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
            'Type' => Object::TYPE_CONST,
            'Cost' => Object::TYPE_DECIMAL,
            'Quantity' => Object::TYPE_DECIMAL,
            'CostType' => Object::TYPE_CONST,
            'Product' => 'Product');
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
            'RessourceRessourceGroup'=>array(
                'linkClass'     => 'RessourceRessourceGroup',
                'field'         => 'Ressource',
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
            'Type'=>array(
                'label'        => _('Resource type'),
                'shortlabel'   => _('Resource type'),
                'usedby'       => array('searchform', 'addedit', 'grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Cost'=>array(
                'label'        => _('Cost'),
                'shortlabel'   => _('Cost'),
                'usedby'       => array('addedit', 'grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 2
            ),
            'CostType'=>array(
                'label'        => _('Cost unit'),
                'shortlabel'   => _('Cost unit'),
                'usedby'       => array('addedit', 'grid'),
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