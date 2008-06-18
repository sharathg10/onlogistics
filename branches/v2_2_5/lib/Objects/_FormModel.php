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

class _FormModel extends Object {
    // class constants {{{

    const ACTION_TYPE_PHONING = 1;
    const ACTION_TYPE_MEETING = 2;
    const ACTION_TYPE_PHONING_CAMPAIGN = 3;
    const ACTION_TYPE_MAILING_CAMPAIGN = 4;
    const ACTION_TYPE_REPARING = 5;
    const ACTION_TYPE_UPDATE_POTENTIAL_KO = 6;
    const ACTION_TYPE_UPDATE_POTENTIAL_OK = 7;
    const ACTION_TYPE_CUSTOMER_ALERT = 8;

    // }}}
    // Constructeur {{{

    /**
     * _FormModel::__construct()
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
     * _FormModel::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _FormModel::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Activ string property + getter/setter {{{

    /**
     * Activ int property
     *
     * @access private
     * @var integer
     */
    private $_Activ = 1;

    /**
     * _FormModel::getActiv
     *
     * @access public
     * @return integer
     */
    public function getActiv() {
        return $this->_Activ;
    }

    /**
     * _FormModel::setActiv
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setActiv($value) {
        if ($value !== null) {
            $this->_Activ = (int)$value;
        }
    }

    // }}}
    // ActionType const property + getter/setter/getActionTypeConstArray {{{

    /**
     * ActionType int property
     *
     * @access private
     * @var integer
     */
    private $_ActionType = 0;

    /**
     * _FormModel::getActionType
     *
     * @access public
     * @return integer
     */
    public function getActionType() {
        return $this->_ActionType;
    }

    /**
     * _FormModel::setActionType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setActionType($value) {
        if ($value !== null) {
            $this->_ActionType = (int)$value;
        }
    }

    /**
     * _FormModel::getActionTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getActionTypeConstArray($keys = false) {
        $array = array(
            _FormModel::ACTION_TYPE_PHONING => _("phoning"), 
            _FormModel::ACTION_TYPE_MEETING => _("visit"), 
            _FormModel::ACTION_TYPE_PHONING_CAMPAIGN => _("phoning campaign"), 
            _FormModel::ACTION_TYPE_MAILING_CAMPAIGN => _("mailing campaign"), 
            _FormModel::ACTION_TYPE_REPARING => _("repairing"), 
            _FormModel::ACTION_TYPE_UPDATE_POTENTIAL_KO => _("potential update refused"), 
            _FormModel::ACTION_TYPE_UPDATE_POTENTIAL_OK => _("potential update accepted"), 
            _FormModel::ACTION_TYPE_CUSTOMER_ALERT => _("set customer in alert state")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Action one to many relation + getter/setter {{{

    /**
     * Action 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActionCollection = false;

    /**
     * _FormModel::getActionCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActionCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('FormModel');
            return $mapper->getOneToMany($this->getId(),
                'Action', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActionCollection) {
            $mapper = Mapper::singleton('FormModel');
            $this->_ActionCollection = $mapper->getOneToMany($this->getId(),
                'Action');
        }
        return $this->_ActionCollection;
    }

    /**
     * _FormModel::getActionCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActionCollectionIds($filter = array()) {
        $col = $this->getActionCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _FormModel::setActionCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActionCollection($value) {
        $this->_ActionCollection = $value;
    }

    // }}}
    // LinkFormModelParagraphModel one to many relation + getter/setter {{{

    /**
     * LinkFormModelParagraphModel 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LinkFormModelParagraphModelCollection = false;

    /**
     * _FormModel::getLinkFormModelParagraphModelCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLinkFormModelParagraphModelCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('FormModel');
            return $mapper->getOneToMany($this->getId(),
                'LinkFormModelParagraphModel', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LinkFormModelParagraphModelCollection) {
            $mapper = Mapper::singleton('FormModel');
            $this->_LinkFormModelParagraphModelCollection = $mapper->getOneToMany($this->getId(),
                'LinkFormModelParagraphModel');
        }
        return $this->_LinkFormModelParagraphModelCollection;
    }

    /**
     * _FormModel::getLinkFormModelParagraphModelCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLinkFormModelParagraphModelCollectionIds($filter = array()) {
        $col = $this->getLinkFormModelParagraphModelCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _FormModel::setLinkFormModelParagraphModelCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLinkFormModelParagraphModelCollection($value) {
        $this->_LinkFormModelParagraphModelCollection = $value;
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
        return 'FormModel';
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
            'Activ' => Object::TYPE_BOOL,
            'ActionType' => Object::TYPE_CONST);
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
            'Action'=>array(
                'linkClass'     => 'Action',
                'field'         => 'FormModel',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'LinkFormModelParagraphModel'=>array(
                'linkClass'     => 'LinkFormModelParagraphModel',
                'field'         => 'FormModel',
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