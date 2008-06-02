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

class _UserAccount extends Object {
    // class constants {{{

    const PROFILE_ROOT = -1;
    const PROFILE_ADMIN = 1;
    const PROFILE_ACTOR = 2;
    const PROFILE_SUPERVISOR = 3;
    const PROFILE_OPERATOR = 4;
    const PROFILE_COMMERCIAL = 5;
    const PROFILE_CUSTOMER = 6;
    const PROFILE_SUPPLIER = 7;
    const PROFILE_ADMIN_VENTES = 8;
    const PROFILE_GESTIONNAIRE_STOCK = 9;
    const PROFILE_SUPPLIER_CONSIGNE = 10;
    const PROFILE_TRANSPORTEUR = 11;
    const PROFILE_ADMIN_WITHOUT_CASHFLOW = 12;
    const PROFILE_AERO_CUSTOMER = 13;
    const PROFILE_AERO_SUPPLIER = 14;
    const PROFILE_AERO_INSTRUCTOR = 15;
    const PROFILE_AERO_OPERATOR = 16;
    const PROFILE_AERO_ADMIN_VENTES = 17;
    const PROFILE_CLIENT_TRANSPORT = 18;
    const PROFILE_ACCOUNTANT = 19;
    const PROFILE_DIR_COMMERCIAL = 20;
    const PROFILE_EXTERNAL_CUSTOMER = 21;
    const PROFILE_OWNER_CUSTOMER = 22;
    const PROFILE_SUBSIDIARY_ACCOUNTANT = 23;
    const PROFILE_GED_PROJECT_MANAGER = 24;
    const PROFILE_PRODUCT_MANAGER = 25;

    // }}}
    // Constructeur {{{

    /**
     * _UserAccount::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Identity string property + getter/setter {{{

    /**
     * Identity string property
     *
     * @access private
     * @var string
     */
    private $_Identity = '';

    /**
     * _UserAccount::getIdentity
     *
     * @access public
     * @return string
     */
    public function getIdentity() {
        return $this->_Identity;
    }

    /**
     * _UserAccount::setIdentity
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setIdentity($value) {
        $this->_Identity = $value;
    }

    // }}}
    // Actor foreignkey property + getter/setter {{{

    /**
     * Actor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Actor = false;

    /**
     * _UserAccount::getActor
     *
     * @access public
     * @return object Actor
     */
    public function getActor() {
        if (is_int($this->_Actor) && $this->_Actor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Actor = $mapper->load(
                array('Id'=>$this->_Actor));
        }
        return $this->_Actor;
    }

    /**
     * _UserAccount::getActorId
     *
     * @access public
     * @return integer
     */
    public function getActorId() {
        if ($this->_Actor instanceof Actor) {
            return $this->_Actor->getId();
        }
        return (int)$this->_Actor;
    }

    /**
     * _UserAccount::setActor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setActor($value) {
        if (is_numeric($value)) {
            $this->_Actor = (int)$value;
        } else {
            $this->_Actor = $value;
        }
    }

    // }}}
    // Login string property + getter/setter {{{

    /**
     * Login string property
     *
     * @access private
     * @var string
     */
    private $_Login = '';

    /**
     * _UserAccount::getLogin
     *
     * @access public
     * @return string
     */
    public function getLogin() {
        return $this->_Login;
    }

    /**
     * _UserAccount::setLogin
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setLogin($value) {
        $this->_Login = $value;
    }

    // }}}
    // Password string property + getter/setter {{{

    /**
     * Password string property
     *
     * @access private
     * @var string
     */
    private $_Password = '';

    /**
     * _UserAccount::getPassword
     *
     * @access public
     * @return string
     */
    public function getPassword() {
        return $this->_Password;
    }

    /**
     * _UserAccount::setPassword
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPassword($value) {
        $this->_Password = $value;
    }

    // }}}
    // Phone string property + getter/setter {{{

    /**
     * Phone string property
     *
     * @access private
     * @var string
     */
    private $_Phone = '';

    /**
     * _UserAccount::getPhone
     *
     * @access public
     * @return string
     */
    public function getPhone() {
        return $this->_Phone;
    }

    /**
     * _UserAccount::setPhone
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPhone($value) {
        $this->_Phone = $value;
    }

    // }}}
    // Fax string property + getter/setter {{{

    /**
     * Fax string property
     *
     * @access private
     * @var string
     */
    private $_Fax = '';

    /**
     * _UserAccount::getFax
     *
     * @access public
     * @return string
     */
    public function getFax() {
        return $this->_Fax;
    }

    /**
     * _UserAccount::setFax
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setFax($value) {
        $this->_Fax = $value;
    }

    // }}}
    // Email string property + getter/setter {{{

    /**
     * Email string property
     *
     * @access private
     * @var string
     */
    private $_Email = '';

    /**
     * _UserAccount::getEmail
     *
     * @access public
     * @return string
     */
    public function getEmail() {
        return $this->_Email;
    }

    /**
     * _UserAccount::setEmail
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEmail($value) {
        $this->_Email = $value;
    }

    // }}}
    // Profile const property + getter/setter/getProfileConstArray {{{

    /**
     * Profile int property
     *
     * @access private
     * @var integer
     */
    private $_Profile = 0;

    /**
     * _UserAccount::getProfile
     *
     * @access public
     * @return integer
     */
    public function getProfile() {
        return $this->_Profile;
    }

    /**
     * _UserAccount::setProfile
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setProfile($value) {
        if ($value !== null) {
            $this->_Profile = (int)$value;
        }
    }

    /**
     * _UserAccount::getProfileConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getProfileConstArray($keys = false) {
        $array = array(
            _UserAccount::PROFILE_ROOT => _("Root user"), 
            _UserAccount::PROFILE_ADMIN => _("Admin"), 
            _UserAccount::PROFILE_ACTOR => _("Actor"), 
            _UserAccount::PROFILE_SUPERVISOR => _("Supervisor"), 
            _UserAccount::PROFILE_OPERATOR => _("Operator"), 
            _UserAccount::PROFILE_COMMERCIAL => _("Salesman"), 
            _UserAccount::PROFILE_CUSTOMER => _("Customer"), 
            _UserAccount::PROFILE_SUPPLIER => _("Supplier"), 
            _UserAccount::PROFILE_ADMIN_VENTES => _("Sales management"), 
            _UserAccount::PROFILE_GESTIONNAIRE_STOCK => _("Stock management"), 
            _UserAccount::PROFILE_SUPPLIER_CONSIGNE => _("Deposited supplier"), 
            _UserAccount::PROFILE_TRANSPORTEUR => _("Carrier"), 
            _UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW => _("Admin level 2"), 
            _UserAccount::PROFILE_AERO_CUSTOMER => _("Aeronautical customer"), 
            _UserAccount::PROFILE_AERO_SUPPLIER => _("Aeronautical supplier"), 
            _UserAccount::PROFILE_AERO_INSTRUCTOR => _("Aircraft instructor"), 
            _UserAccount::PROFILE_AERO_OPERATOR => _("Aeronautical operator"), 
            _UserAccount::PROFILE_AERO_ADMIN_VENTES => _("Aeronautics sales management"), 
            _UserAccount::PROFILE_CLIENT_TRANSPORT => _("Carriage customer"), 
            _UserAccount::PROFILE_ACCOUNTANT => _("Accountant"), 
            _UserAccount::PROFILE_DIR_COMMERCIAL => _("Sales manager"), 
            _UserAccount::PROFILE_EXTERNAL_CUSTOMER => _("External customer"), 
            _UserAccount::PROFILE_OWNER_CUSTOMER => _("Owner customer"), 
            _UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT => _("Subsidiary accountant"), 
            _UserAccount::PROFILE_GED_PROJECT_MANAGER => _("Project manager"), 
            _UserAccount::PROFILE_PRODUCT_MANAGER => _("Product manager")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Catalog foreignkey property + getter/setter {{{

    /**
     * Catalog foreignkey
     *
     * @access private
     * @var mixed object Catalog or integer
     */
    private $_Catalog = false;

    /**
     * _UserAccount::getCatalog
     *
     * @access public
     * @return object Catalog
     */
    public function getCatalog() {
        if (is_int($this->_Catalog) && $this->_Catalog > 0) {
            $mapper = Mapper::singleton('Catalog');
            $this->_Catalog = $mapper->load(
                array('Id'=>$this->_Catalog));
        }
        return $this->_Catalog;
    }

    /**
     * _UserAccount::getCatalogId
     *
     * @access public
     * @return integer
     */
    public function getCatalogId() {
        if ($this->_Catalog instanceof Catalog) {
            return $this->_Catalog->getId();
        }
        return (int)$this->_Catalog;
    }

    /**
     * _UserAccount::setCatalog
     *
     * @access public
     * @param object Catalog $value
     * @return void
     */
    public function setCatalog($value) {
        if (is_numeric($value)) {
            $this->_Catalog = (int)$value;
        } else {
            $this->_Catalog = $value;
        }
    }

    // }}}
    // SupplierCatalog foreignkey property + getter/setter {{{

    /**
     * SupplierCatalog foreignkey
     *
     * @access private
     * @var mixed object Catalog or integer
     */
    private $_SupplierCatalog = false;

    /**
     * _UserAccount::getSupplierCatalog
     *
     * @access public
     * @return object Catalog
     */
    public function getSupplierCatalog() {
        if (is_int($this->_SupplierCatalog) && $this->_SupplierCatalog > 0) {
            $mapper = Mapper::singleton('Catalog');
            $this->_SupplierCatalog = $mapper->load(
                array('Id'=>$this->_SupplierCatalog));
        }
        return $this->_SupplierCatalog;
    }

    /**
     * _UserAccount::getSupplierCatalogId
     *
     * @access public
     * @return integer
     */
    public function getSupplierCatalogId() {
        if ($this->_SupplierCatalog instanceof Catalog) {
            return $this->_SupplierCatalog->getId();
        }
        return (int)$this->_SupplierCatalog;
    }

    /**
     * _UserAccount::setSupplierCatalog
     *
     * @access public
     * @param object Catalog $value
     * @return void
     */
    public function setSupplierCatalog($value) {
        if (is_numeric($value)) {
            $this->_SupplierCatalog = (int)$value;
        } else {
            $this->_SupplierCatalog = $value;
        }
    }

    // }}}
    // Site one to many relation + getter/setter {{{

    /**
     * Site *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_SiteCollection = false;

    /**
     * _UserAccount::getSiteCollection
     *
     * @access public
     * @return object Collection
     */
    public function getSiteCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('UserAccount');
            return $mapper->getManyToMany($this->getId(),
                'Site', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_SiteCollection) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_SiteCollection = $mapper->getManyToMany($this->getId(),
                'Site');
        }
        return $this->_SiteCollection;
    }

    /**
     * _UserAccount::getSiteCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getSiteCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getSiteCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_SiteCollection) {
            $mapper = Mapper::singleton('UserAccount');
            return $mapper->getManyToManyIds($this->getId(), 'Site');
        }
        return $this->_SiteCollection->getItemIds();
    }

    /**
     * _UserAccount::setSiteCollectionIds
     *
     * @access public
     * @return array
     */
    public function setSiteCollectionIds($itemIds) {
        $this->_SiteCollection = new Collection('Site');
        foreach ($itemIds as $id) {
            $this->_SiteCollection->setItem($id);
        }
    }

    /**
     * _UserAccount::setSiteCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setSiteCollection($value) {
        $this->_SiteCollection = $value;
    }

    /**
     * _UserAccount::SiteCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function SiteCollectionIsLoaded() {
        return ($this->_SiteCollection !== false);
    }

    // }}}
    // ChainTask one to many relation + getter/setter {{{

    /**
     * ChainTask *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ChainTaskCollection = false;

    /**
     * _UserAccount::getChainTaskCollection
     *
     * @access public
     * @return object Collection
     */
    public function getChainTaskCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('UserAccount');
            return $mapper->getManyToMany($this->getId(),
                'ChainTask', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ChainTaskCollection) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_ChainTaskCollection = $mapper->getManyToMany($this->getId(),
                'ChainTask');
        }
        return $this->_ChainTaskCollection;
    }

    /**
     * _UserAccount::getChainTaskCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getChainTaskCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getChainTaskCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ChainTaskCollection) {
            $mapper = Mapper::singleton('UserAccount');
            return $mapper->getManyToManyIds($this->getId(), 'ChainTask');
        }
        return $this->_ChainTaskCollection->getItemIds();
    }

    /**
     * _UserAccount::setChainTaskCollectionIds
     *
     * @access public
     * @return array
     */
    public function setChainTaskCollectionIds($itemIds) {
        $this->_ChainTaskCollection = new Collection('ChainTask');
        foreach ($itemIds as $id) {
            $this->_ChainTaskCollection->setItem($id);
        }
    }

    /**
     * _UserAccount::setChainTaskCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setChainTaskCollection($value) {
        $this->_ChainTaskCollection = $value;
    }

    /**
     * _UserAccount::ChainTaskCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ChainTaskCollectionIsLoaded() {
        return ($this->_ChainTaskCollection !== false);
    }

    // }}}
    // ActivatedChainTask one to many relation + getter/setter {{{

    /**
     * ActivatedChainTask *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActivatedChainTaskCollection = false;

    /**
     * _UserAccount::getActivatedChainTaskCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedChainTaskCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('UserAccount');
            return $mapper->getManyToMany($this->getId(),
                'ActivatedChainTask', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedChainTaskCollection) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_ActivatedChainTaskCollection = $mapper->getManyToMany($this->getId(),
                'ActivatedChainTask');
        }
        return $this->_ActivatedChainTaskCollection;
    }

    /**
     * _UserAccount::getActivatedChainTaskCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActivatedChainTaskCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getActivatedChainTaskCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_ActivatedChainTaskCollection) {
            $mapper = Mapper::singleton('UserAccount');
            return $mapper->getManyToManyIds($this->getId(), 'ActivatedChainTask');
        }
        return $this->_ActivatedChainTaskCollection->getItemIds();
    }

    /**
     * _UserAccount::setActivatedChainTaskCollectionIds
     *
     * @access public
     * @return array
     */
    public function setActivatedChainTaskCollectionIds($itemIds) {
        $this->_ActivatedChainTaskCollection = new Collection('ActivatedChainTask');
        foreach ($itemIds as $id) {
            $this->_ActivatedChainTaskCollection->setItem($id);
        }
    }

    /**
     * _UserAccount::setActivatedChainTaskCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedChainTaskCollection($value) {
        $this->_ActivatedChainTaskCollection = $value;
    }

    /**
     * _UserAccount::ActivatedChainTaskCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function ActivatedChainTaskCollectionIsLoaded() {
        return ($this->_ActivatedChainTaskCollection !== false);
    }

    // }}}
    // Alert one to many relation + getter/setter {{{

    /**
     * Alert *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_AlertCollection = false;

    /**
     * _UserAccount::getAlertCollection
     *
     * @access public
     * @return object Collection
     */
    public function getAlertCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('UserAccount');
            return $mapper->getManyToMany($this->getId(),
                'Alert', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_AlertCollection) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_AlertCollection = $mapper->getManyToMany($this->getId(),
                'Alert');
        }
        return $this->_AlertCollection;
    }

    /**
     * _UserAccount::getAlertCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getAlertCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getAlertCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_AlertCollection) {
            $mapper = Mapper::singleton('UserAccount');
            return $mapper->getManyToManyIds($this->getId(), 'Alert');
        }
        return $this->_AlertCollection->getItemIds();
    }

    /**
     * _UserAccount::setAlertCollectionIds
     *
     * @access public
     * @return array
     */
    public function setAlertCollectionIds($itemIds) {
        $this->_AlertCollection = new Collection('Alert');
        foreach ($itemIds as $id) {
            $this->_AlertCollection->setItem($id);
        }
    }

    /**
     * _UserAccount::setAlertCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setAlertCollection($value) {
        $this->_AlertCollection = $value;
    }

    /**
     * _UserAccount::AlertCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function AlertCollectionIsLoaded() {
        return ($this->_AlertCollection !== false);
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
     * _UserAccount::getActionCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActionCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('UserAccount');
            return $mapper->getOneToMany($this->getId(),
                'Action', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActionCollection) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_ActionCollection = $mapper->getOneToMany($this->getId(),
                'Action');
        }
        return $this->_ActionCollection;
    }

    /**
     * _UserAccount::getActionCollectionIds
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
     * _UserAccount::setActionCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActionCollection($value) {
        $this->_ActionCollection = $value;
    }

    // }}}
    // ValidatedActivatedChainTask one to many relation + getter/setter {{{

    /**
     * ValidatedActivatedChainTask 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ValidatedActivatedChainTaskCollection = false;

    /**
     * _UserAccount::getValidatedActivatedChainTaskCollection
     *
     * @access public
     * @return object Collection
     */
    public function getValidatedActivatedChainTaskCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('UserAccount');
            return $mapper->getOneToMany($this->getId(),
                'ValidatedActivatedChainTask', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ValidatedActivatedChainTaskCollection) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_ValidatedActivatedChainTaskCollection = $mapper->getOneToMany($this->getId(),
                'ValidatedActivatedChainTask');
        }
        return $this->_ValidatedActivatedChainTaskCollection;
    }

    /**
     * _UserAccount::getValidatedActivatedChainTaskCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getValidatedActivatedChainTaskCollectionIds($filter = array()) {
        $col = $this->getValidatedActivatedChainTaskCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _UserAccount::setValidatedActivatedChainTaskCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setValidatedActivatedChainTaskCollection($value) {
        $this->_ValidatedActivatedChainTaskCollection = $value;
    }

    // }}}
    // UploadedDocument one to many relation + getter/setter {{{

    /**
     * UploadedDocument 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_UploadedDocumentCollection = false;

    /**
     * _UserAccount::getUploadedDocumentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getUploadedDocumentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('UserAccount');
            return $mapper->getOneToMany($this->getId(),
                'UploadedDocument', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_UploadedDocumentCollection) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_UploadedDocumentCollection = $mapper->getOneToMany($this->getId(),
                'UploadedDocument');
        }
        return $this->_UploadedDocumentCollection;
    }

    /**
     * _UserAccount::getUploadedDocumentCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getUploadedDocumentCollectionIds($filter = array()) {
        $col = $this->getUploadedDocumentCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _UserAccount::setUploadedDocumentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setUploadedDocumentCollection($value) {
        $this->_UploadedDocumentCollection = $value;
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
        return 'UserAccount';
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
        return _('Add/Update user account');
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
            'Identity' => Object::TYPE_STRING,
            'Actor' => 'Actor',
            'Login' => Object::TYPE_STRING,
            'Password' => Object::TYPE_PASSWORD,
            'Phone' => Object::TYPE_STRING,
            'Fax' => Object::TYPE_STRING,
            'Email' => Object::TYPE_STRING,
            'Profile' => Object::TYPE_CONST,
            'Catalog' => 'Catalog',
            'SupplierCatalog' => 'Catalog');
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
            'Site'=>array(
                'linkClass'     => 'Site',
                'field'         => 'ToUserAccount',
                'linkTable'     => 'siteUserAccount',
                'linkField'     => 'FromSite',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ChainTask'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'ToUserAccount',
                'linkTable'     => 'chtUserAccount',
                'linkField'     => 'FromChainTask',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'ToUserAccount',
                'linkTable'     => 'ackUserAccount',
                'linkField'     => 'FromActivatedChainTask',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Alert'=>array(
                'linkClass'     => 'Alert',
                'field'         => 'ToUserAccount',
                'linkTable'     => 'alertUserAccount',
                'linkField'     => 'FromAlert',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Action'=>array(
                'linkClass'     => 'Action',
                'field'         => 'Commercial',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ValidatedActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'ValidationUser',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Actor'=>array(
                'linkClass'     => 'Actor',
                'field'         => 'Commercial',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Command'=>array(
                'linkClass'     => 'Command',
                'field'         => 'Commercial',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Inventory'=>array(
                'linkClass'     => 'Inventory',
                'field'         => 'UserAccount',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'UploadedDocument'=>array(
                'linkClass'     => 'UploadedDocument',
                'field'         => 'UserAccount',
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
            'Identity'=>array(
                'label'        => _('Full name'),
                'shortlabel'   => _('Name'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('User account data')
            ),
            'Actor'=>array(
                'label'        => _('Actor'),
                'shortlabel'   => _('Actor'),
                'usedby'       => array('addedit', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('User account data')
            ),
            'Login'=>array(
                'label'        => _('Username'),
                'shortlabel'   => _('Username'),
                'usedby'       => array('addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('User account data')
            ),
            'Password'=>array(
                'label'        => _('Password'),
                'shortlabel'   => _('Password'),
                'usedby'       => array('addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('User account data')
            ),
            'Phone'=>array(
                'label'        => _('Phone'),
                'shortlabel'   => _('Phone'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('User account data')
            ),
            'Fax'=>array(
                'label'        => _('Fax'),
                'shortlabel'   => _('Fax'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('User account data')
            ),
            'Email'=>array(
                'label'        => _('Email'),
                'shortlabel'   => _('Email'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('User account data')
            ),
            'Profile'=>array(
                'label'        => _('Profile'),
                'shortlabel'   => _('Profile'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('User account data')
            ),
            'Catalog'=>array(
                'label'        => _('Customer catalogue'),
                'shortlabel'   => _('Customer catalogue'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('User account data')
            ),
            'SupplierCatalog'=>array(
                'label'        => _('Supplier catalogue'),
                'shortlabel'   => _('Supplier catalogue'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('User account data')
            ),
            'Site'=>array(
                'label'        => _('Sites'),
                'shortlabel'   => _('Sites'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('User account data')
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
        return $this->getIdentity();
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
        return 'Identity';
    }

    // }}}
}

?>