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

class _OnlogisticsAccount extends Object {
    // class constants {{{

    const ENV_CURRENT = 0;
    const ENV_RECETTE = 1;
    const ENV_DEMO = 2;
    const ENV_PROD = 3;

    // }}}
    // Constructeur {{{

    /**
     * _OnlogisticsAccount::__construct()
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
     * _OnlogisticsAccount::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _OnlogisticsAccount::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Environment const property + getter/setter/getEnvironmentConstArray {{{

    /**
     * Environment int property
     *
     * @access private
     * @var integer
     */
    private $_Environment = 0;

    /**
     * _OnlogisticsAccount::getEnvironment
     *
     * @access public
     * @return integer
     */
    public function getEnvironment() {
        return $this->_Environment;
    }

    /**
     * _OnlogisticsAccount::setEnvironment
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setEnvironment($value) {
        if ($value !== null) {
            $this->_Environment = (int)$value;
        }
    }

    /**
     * _OnlogisticsAccount::getEnvironmentConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getEnvironmentConstArray($keys = false) {
        $array = array(
            _OnlogisticsAccount::ENV_CURRENT => _("current"),
            _OnlogisticsAccount::ENV_RECETTE => _("recette"),
            _OnlogisticsAccount::ENV_DEMO => _("demo"),
            _OnlogisticsAccount::ENV_PROD => _("prod")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // ParentAccount foreignkey property + getter/setter {{{

    /**
     * ParentAccount foreignkey
     *
     * @access private
     * @var mixed object OnlogisticsAccount or integer
     */
    private $_ParentAccount = false;

    /**
     * _OnlogisticsAccount::getParentAccount
     *
     * @access public
     * @return object OnlogisticsAccount
     */
    public function getParentAccount() {
        if (is_int($this->_ParentAccount) && $this->_ParentAccount > 0) {
            $mapper = Mapper::singleton('OnlogisticsAccount');
            $this->_ParentAccount = $mapper->load(
                array('Id'=>$this->_ParentAccount));
        }
        return $this->_ParentAccount;
    }

    /**
     * _OnlogisticsAccount::getParentAccountId
     *
     * @access public
     * @return integer
     */
    public function getParentAccountId() {
        if ($this->_ParentAccount instanceof OnlogisticsAccount) {
            return $this->_ParentAccount->getId();
        }
        return (int)$this->_ParentAccount;
    }

    /**
     * _OnlogisticsAccount::setParentAccount
     *
     * @access public
     * @param object OnlogisticsAccount $value
     * @return void
     */
    public function setParentAccount($value) {
        if (is_numeric($value)) {
            $this->_ParentAccount = (int)$value;
        } else {
            $this->_ParentAccount = $value;
        }
    }

    // }}}
    // OnlogisticsAccount one to many relation + getter/setter {{{

    /**
     * OnlogisticsAccount *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_OnlogisticsAccountCollection = false;

    /**
     * _OnlogisticsAccount::getOnlogisticsAccountCollection
     *
     * @access public
     * @return object Collection
     */
    public function getOnlogisticsAccountCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('OnlogisticsAccount');
            return $mapper->getManyToMany($this->getId(),
                'OnlogisticsAccount', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_OnlogisticsAccountCollection) {
            $mapper = Mapper::singleton('OnlogisticsAccount');
            $this->_OnlogisticsAccountCollection = $mapper->getManyToMany($this->getId(),
                'OnlogisticsAccount');
        }
        return $this->_OnlogisticsAccountCollection;
    }

    /**
     * _OnlogisticsAccount::getOnlogisticsAccountCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getOnlogisticsAccountCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getOnlogisticsAccountCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_OnlogisticsAccountCollection) {
            $mapper = Mapper::singleton('OnlogisticsAccount');
            return $mapper->getManyToManyIds($this->getId(), 'OnlogisticsAccount');
        }
        return $this->_OnlogisticsAccountCollection->getItemIds();
    }

    /**
     * _OnlogisticsAccount::setOnlogisticsAccountCollectionIds
     *
     * @access public
     * @return array
     */
    public function setOnlogisticsAccountCollectionIds($itemIds) {
        $this->_OnlogisticsAccountCollection = new Collection('OnlogisticsAccount');
        foreach ($itemIds as $id) {
            $this->_OnlogisticsAccountCollection->setItem($id);
        }
    }

    /**
     * _OnlogisticsAccount::setOnlogisticsAccountCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setOnlogisticsAccountCollection($value) {
        $this->_OnlogisticsAccountCollection = $value;
    }

    /**
     * _OnlogisticsAccount::OnlogisticsAccountCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function OnlogisticsAccountCollectionIsLoaded() {
        return ($this->_OnlogisticsAccountCollection !== false);
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
     * _OnlogisticsAccount::getActive
     *
     * @access public
     * @return integer
     */
    public function getActive() {
        return $this->_Active;
    }

    /**
     * _OnlogisticsAccount::setActive
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
    // CreationDate datetime property + getter/setter {{{

    /**
     * CreationDate int property
     *
     * @access private
     * @var string
     */
    private $_CreationDate = 0;

    /**
     * _OnlogisticsAccount::getCreationDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getCreationDate($format = false) {
        return $this->dateFormat($this->_CreationDate, $format);
    }

    /**
     * _OnlogisticsAccount::setCreationDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCreationDate($value) {
        $this->_CreationDate = $value;
    }

    // }}}
    // LastAccessDate datetime property + getter/setter {{{

    /**
     * LastAccessDate int property
     *
     * @access private
     * @var string
     */
    private $_LastAccessDate = 0;

    /**
     * _OnlogisticsAccount::getLastAccessDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getLastAccessDate($format = false) {
        return $this->dateFormat($this->_LastAccessDate, $format);
    }

    /**
     * _OnlogisticsAccount::setLastAccessDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setLastAccessDate($value) {
        $this->_LastAccessDate = $value;
    }

    // }}}
    // ChildAccount one to many relation + getter/setter {{{

    /**
     * ChildAccount 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ChildAccountCollection = false;

    /**
     * _OnlogisticsAccount::getChildAccountCollection
     *
     * @access public
     * @return object Collection
     */
    public function getChildAccountCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('OnlogisticsAccount');
            return $mapper->getOneToMany($this->getId(),
                'ChildAccount', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ChildAccountCollection) {
            $mapper = Mapper::singleton('OnlogisticsAccount');
            $this->_ChildAccountCollection = $mapper->getOneToMany($this->getId(),
                'ChildAccount');
        }
        return $this->_ChildAccountCollection;
    }

    /**
     * _OnlogisticsAccount::getChildAccountCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getChildAccountCollectionIds($filter = array()) {
        $col = $this->getChildAccountCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _OnlogisticsAccount::setChildAccountCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setChildAccountCollection($value) {
        $this->_ChildAccountCollection = $value;
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
        return 'OnlogisticsAccount';
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
        return _('Accounts');
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
            'Environment' => Object::TYPE_CONST,
            'ParentAccount' => 'OnlogisticsAccount',
            'Active' => Object::TYPE_BOOL,
            'CreationDate' => Object::TYPE_DATETIME,
            'LastAccessDate' => Object::TYPE_DATETIME);
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
            'OnlogisticsAccount'=>array(
                'linkClass'     => 'OnlogisticsAccount',
                'field'         => 'Account',
                'linkTable'     => 'OnlogisticsAccountLinks',
                'linkField'     => 'LinkedAccount',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 1
            ),
            'ChildAccount'=>array(
                'linkClass'     => 'OnlogisticsAccount',
                'field'         => 'ParentAccount',
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
                'label'      => _('Name'),
                'shortlabel' => _('Name'),
                'usedby'     => array('addedit', 'grid', 'searchform'),
                'required'   => true,
                'section'    => ''
            ),
            'Environment'=>array(
                'label'      => _('Environment'),
                'shortlabel' => _('Environment'),
                'usedby'     => array('grid', 'searchform', 'addedit'),
                'required'   => false,
                'section'    => ''
            ),
            'ParentAccount'=>array(
                'label'      => _('Parent account'),
                'shortlabel' => _('Parent account'),
                'usedby'     => array('grid', 'searchform', 'addedit'),
                'required'   => false,
                'section'    => ''
            ),
            'OnlogisticsAccount'=>array(
                'label'      => _('Related accounts'),
                'shortlabel' => _('Related accounts'),
                'usedby'     => array('grid', 'addedit'),
                'required'   => false,
                'section'    => ''
            ),
            'Active'=>array(
                'label'      => _('Active'),
                'shortlabel' => _('Active'),
                'usedby'     => array('grid', 'searchform', 'addedit'),
                'required'   => false,
                'section'    => ''
            ),
            'CreationDate'=>array(
                'label'      => _('Creation date'),
                'shortlabel' => _('Creation date'),
                'usedby'     => array('grid'),
                'required'   => false,
                'section'    => ''
            ),
            'LastAccessDate'=>array(
                'label'      => _('Last access date'),
                'shortlabel' => _('Last access date'),
                'usedby'     => array('grid'),
                'required'   => false,
                'section'    => ''
            ));
        return $return;
    }

    // }}}
}

?>