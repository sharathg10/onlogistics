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

class CustomerSituation extends Object {
    // class constants {{{

    const TYPE_SITUATION_NORMAL = 1;
    const TYPE_SITUATION_ALERT = 2;
    const TYPE_SITUATION_PROSPECT = 3;
    const TYPE_SITUATION_INACTIVE = 4;

    // }}}
    // Constructeur {{{

    /**
     * CustomerSituation::__construct()
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
     * CustomerSituation::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * CustomerSituation::setName
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
    private $_Type = 1;

    /**
     * CustomerSituation::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * CustomerSituation::setType
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
     * CustomerSituation::getTypeConstArray
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
            CustomerSituation::TYPE_SITUATION_NORMAL => _("Normal"), 
            CustomerSituation::TYPE_SITUATION_ALERT => _("Alert"), 
            CustomerSituation::TYPE_SITUATION_PROSPECT => _("Prospect"), 
            CustomerSituation::TYPE_SITUATION_INACTIVE => _("Inactive")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // InactivityDelay int property + getter/setter {{{

    /**
     * InactivityDelay int property
     *
     * @access private
     * @var integer
     */
    private $_InactivityDelay = null;

    /**
     * CustomerSituation::getInactivityDelay
     *
     * @access public
     * @return integer
     */
    public function getInactivityDelay() {
        return $this->_InactivityDelay;
    }

    /**
     * CustomerSituation::setInactivityDelay
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setInactivityDelay($value) {
        $this->_InactivityDelay = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // CustomerProperties one to many relation + getter/setter {{{

    /**
     * CustomerProperties 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CustomerPropertiesCollection = false;

    /**
     * CustomerSituation::getCustomerPropertiesCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCustomerPropertiesCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('CustomerSituation');
            return $mapper->getOneToMany($this->getId(),
                'CustomerProperties', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CustomerPropertiesCollection) {
            $mapper = Mapper::singleton('CustomerSituation');
            $this->_CustomerPropertiesCollection = $mapper->getOneToMany($this->getId(),
                'CustomerProperties');
        }
        return $this->_CustomerPropertiesCollection;
    }

    /**
     * CustomerSituation::getCustomerPropertiesCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCustomerPropertiesCollectionIds($filter = array()) {
        $col = $this->getCustomerPropertiesCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * CustomerSituation::setCustomerPropertiesCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCustomerPropertiesCollection($value) {
        $this->_CustomerPropertiesCollection = $value;
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
        return 'CustomerSituation';
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
        return _('Situation');
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
            'InactivityDelay' => Object::TYPE_INT);
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
            'CustomerProperties'=>array(
                'linkClass'     => 'CustomerProperties',
                'field'         => 'Situation',
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
        $return = array(
            'Name'=>array(
                'label'        => _('Name'),
                'shortlabel'   => _('Name'),
                'usedby'       => array('addedit', 'grid'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Type'=>array(
                'label'        => _('Type'),
                'shortlabel'   => _('Type'),
                'usedby'       => array('addedit', 'grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'InactivityDelay'=>array(
                'label'        => _('Time (in month) when ...'),
                'shortlabel'   => _('Time (in month)'),
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