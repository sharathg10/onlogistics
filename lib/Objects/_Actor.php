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
 * _Actor class
 *
 */
class _Actor extends Object {
    // class constants {{{

    const QUALITY_NONE = 0;
    const QUALITY_MLLE = 1;
    const QUALITY_MME = 2;
    const QUALITY_M = 3;

    // }}}
    // Constructeur {{{

    /**
     * _Actor::__construct()
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
     * _Actor::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _Actor::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // DatabaseOwner string property + getter/setter {{{

    /**
     * DatabaseOwner int property
     *
     * @access private
     * @var integer
     */
    private $_DatabaseOwner = 0;

    /**
     * _Actor::getDatabaseOwner
     *
     * @access public
     * @return integer
     */
    public function getDatabaseOwner() {
        return $this->_DatabaseOwner;
    }

    /**
     * _Actor::setDatabaseOwner
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDatabaseOwner($value) {
        if ($value !== null) {
            $this->_DatabaseOwner = (int)$value;
        }
    }

    // }}}
    // Quality const property + getter/setter/getQualityConstArray {{{

    /**
     * Quality int property
     *
     * @access private
     * @var integer
     */
    private $_Quality = 0;

    /**
     * _Actor::getQuality
     *
     * @access public
     * @return integer
     */
    public function getQuality() {
        return $this->_Quality;
    }

    /**
     * _Actor::setQuality
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setQuality($value) {
        if ($value !== null) {
            $this->_Quality = (int)$value;
        }
    }

    /**
     * _Actor::getQualityConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getQualityConstArray($keys = false) {
        $array = array(
            _Actor::QUALITY_NONE => _("None"), 
            _Actor::QUALITY_MLLE => _("Miss"), 
            _Actor::QUALITY_MME => _("Madam"), 
            _Actor::QUALITY_M => _("Sir")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Code string property + getter/setter {{{

    /**
     * Code string property
     *
     * @access private
     * @var string
     */
    private $_Code = '';

    /**
     * _Actor::getCode
     *
     * @access public
     * @return string
     */
    public function getCode() {
        return $this->_Code;
    }

    /**
     * _Actor::setCode
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCode($value) {
        $this->_Code = $value;
    }

    // }}}
    // Siret string property + getter/setter {{{

    /**
     * Siret string property
     *
     * @access private
     * @var string
     */
    private $_Siret = '';

    /**
     * _Actor::getSiret
     *
     * @access public
     * @return string
     */
    public function getSiret() {
        return $this->_Siret;
    }

    /**
     * _Actor::setSiret
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setSiret($value) {
        $this->_Siret = $value;
    }

    // }}}
    // IATA string property + getter/setter {{{

    /**
     * IATA string property
     *
     * @access private
     * @var string
     */
    private $_IATA = '';

    /**
     * _Actor::getIATA
     *
     * @access public
     * @return string
     */
    public function getIATA() {
        return $this->_IATA;
    }

    /**
     * _Actor::setIATA
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setIATA($value) {
        $this->_IATA = $value;
    }

    // }}}
    // Logo string property + getter/setter {{{

    /**
     * Logo string property
     *
     * @access private
     * @var string
     */
    private $_Logo = '';

    /**
     * _Actor::getLogo
     *
     * @access public
     * @return string
     */
    public function getLogo() {
        return $this->_Logo;
    }

    /**
     * _Actor::setLogo
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setLogo($value) {
        $this->_Logo = $value;
    }

    // }}}
    // Slogan i18n_string property + getter/setter {{{

    /**
     * Slogan foreignkey
     *
     * @access private
     * @var mixed object I18nString or integer
     */
    private $_Slogan = 0;

    /**
     * _Actor::getSlogan
     *
     * @access public
     * @param string $locale optional, default is the current locale code
     * @param boolean $useDefaultLocaleIfEmpty determine if the getter must
     * return the translation in the DEFAULT_LOCALE if no translation is found
     * in the current locale.
     * @return string
     */
    public function getSlogan($locale=false, $defaultLocaleIfEmpty=true) {
        $locale = $locale !== false ? $locale : I18N::getLocaleCode();
        if (is_int($this->_Slogan) && $this->_Slogan > 0) {
            $this->_Slogan = Object::load('I18nString', $this->_Slogan);
        }
        $ret = null;
        if ($this->_Slogan instanceof I18nString) {
            $getter = 'getStringValue_' . $locale;
            $ret = $this->_Slogan->$getter();
            if ($ret == null && $defaultLocaleIfEmpty) {
                $getter = 'getStringValue_' . LOCALE_DEFAULT;
                $ret = $this->_Slogan->$getter();
            }
        }
        return $ret;
    }

    /**
     * _Actor::getSloganId
     *
     * @access public
     * @return integer
     */
    public function getSloganId() {
        if ($this->_Slogan instanceof I18nString) {
            return $this->_Slogan->getId();
        }
        return (int)$this->_Slogan;
    }

    /**
     * _Actor::setSlogan
     *
     * @access public
     * @param string $value
     * @param string $locale optional, default is the current locale code
     * @return void
     */
    public function setSlogan($value, $locale=false) {
        if (is_numeric($value)) {
            $this->_Slogan = (int)$value;
        } else if ($value instanceof I18nString) {
            $this->_Slogan = $value;
        } else {
            $locale = $locale !== false ? $locale : I18N::getLocaleCode();
            if (!($this->_Slogan instanceof I18nString)) {
                $this->_Slogan = Object::load('I18nString', $this->_Slogan);
                if (!($this->_Slogan instanceof I18nString)) {
                    $this->_Slogan = new I18nString();
                }
            }
            $setter = 'setStringValue_'.$locale;
            $this->_Slogan->$setter($value);
            $this->_Slogan->save();
        }
    }

    // }}}
    // TVA string property + getter/setter {{{

    /**
     * TVA string property
     *
     * @access private
     * @var string
     */
    private $_TVA = '';

    /**
     * _Actor::getTVA
     *
     * @access public
     * @return string
     */
    public function getTVA() {
        return $this->_TVA;
    }

    /**
     * _Actor::setTVA
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setTVA($value) {
        $this->_TVA = $value;
    }

    // }}}
    // RCS string property + getter/setter {{{

    /**
     * RCS string property
     *
     * @access private
     * @var string
     */
    private $_RCS = '';

    /**
     * _Actor::getRCS
     *
     * @access public
     * @return string
     */
    public function getRCS() {
        return $this->_RCS;
    }

    /**
     * _Actor::setRCS
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setRCS($value) {
        $this->_RCS = $value;
    }

    // }}}
    // Role string property + getter/setter {{{

    /**
     * Role string property
     *
     * @access private
     * @var string
     */
    private $_Role = '';

    /**
     * _Actor::getRole
     *
     * @access public
     * @return string
     */
    public function getRole() {
        return $this->_Role;
    }

    /**
     * _Actor::setRole
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setRole($value) {
        $this->_Role = $value;
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
     * _Actor::getActive
     *
     * @access public
     * @return integer
     */
    public function getActive() {
        return $this->_Active;
    }

    /**
     * _Actor::setActive
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
    // PaymentCondition string property + getter/setter {{{

    /**
     * PaymentCondition int property
     *
     * @access private
     * @var integer
     */
    private $_PaymentCondition = 0;

    /**
     * _Actor::getPaymentCondition
     *
     * @access public
     * @return integer
     */
    public function getPaymentCondition() {
        return $this->_PaymentCondition;
    }

    /**
     * _Actor::setPaymentCondition
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPaymentCondition($value) {
        if ($value !== null) {
            $this->_PaymentCondition = (int)$value;
        }
    }

    // }}}
    // Incoterm foreignkey property + getter/setter {{{

    /**
     * Incoterm foreignkey
     *
     * @access private
     * @var mixed object Incoterm or integer
     */
    private $_Incoterm = false;

    /**
     * _Actor::getIncoterm
     *
     * @access public
     * @return object Incoterm
     */
    public function getIncoterm() {
        if (is_int($this->_Incoterm) && $this->_Incoterm > 0) {
            $mapper = Mapper::singleton('Incoterm');
            $this->_Incoterm = $mapper->load(
                array('Id'=>$this->_Incoterm));
        }
        return $this->_Incoterm;
    }

    /**
     * _Actor::getIncotermId
     *
     * @access public
     * @return integer
     */
    public function getIncotermId() {
        if ($this->_Incoterm instanceof Incoterm) {
            return $this->_Incoterm->getId();
        }
        return (int)$this->_Incoterm;
    }

    /**
     * _Actor::setIncoterm
     *
     * @access public
     * @param object Incoterm $value
     * @return void
     */
    public function setIncoterm($value) {
        if (is_numeric($value)) {
            $this->_Incoterm = (int)$value;
        } else {
            $this->_Incoterm = $value;
        }
    }

    // }}}
    // PackageCondition int property + getter/setter {{{

    /**
     * PackageCondition int property
     *
     * @access private
     * @var integer
     */
    private $_PackageCondition = null;

    /**
     * _Actor::getPackageCondition
     *
     * @access public
     * @return integer
     */
    public function getPackageCondition() {
        return $this->_PackageCondition;
    }

    /**
     * _Actor::setPackageCondition
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPackageCondition($value) {
        $this->_PackageCondition = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // Commercial foreignkey property + getter/setter {{{

    /**
     * Commercial foreignkey
     *
     * @access private
     * @var mixed object UserAccount or integer
     */
    private $_Commercial = false;

    /**
     * _Actor::getCommercial
     *
     * @access public
     * @return object UserAccount
     */
    public function getCommercial() {
        if (is_int($this->_Commercial) && $this->_Commercial > 0) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_Commercial = $mapper->load(
                array('Id'=>$this->_Commercial));
        }
        return $this->_Commercial;
    }

    /**
     * _Actor::getCommercialId
     *
     * @access public
     * @return integer
     */
    public function getCommercialId() {
        if ($this->_Commercial instanceof UserAccount) {
            return $this->_Commercial->getId();
        }
        return (int)$this->_Commercial;
    }

    /**
     * _Actor::setCommercial
     *
     * @access public
     * @param object UserAccount $value
     * @return void
     */
    public function setCommercial($value) {
        if (is_numeric($value)) {
            $this->_Commercial = (int)$value;
        } else {
            $this->_Commercial = $value;
        }
    }

    // }}}
    // PlanningComment string property + getter/setter {{{

    /**
     * PlanningComment string property
     *
     * @access private
     * @var string
     */
    private $_PlanningComment = '';

    /**
     * _Actor::getPlanningComment
     *
     * @access public
     * @return string
     */
    public function getPlanningComment() {
        return $this->_PlanningComment;
    }

    /**
     * _Actor::setPlanningComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPlanningComment($value) {
        $this->_PlanningComment = $value;
    }

    // }}}
    // MainSite foreignkey property + getter/setter {{{

    /**
     * MainSite foreignkey
     *
     * @access private
     * @var mixed object Site or integer
     */
    private $_MainSite = false;

    /**
     * _Actor::getMainSite
     *
     * @access public
     * @return object Site
     */
    public function getMainSite() {
        if (is_int($this->_MainSite) && $this->_MainSite > 0) {
            $mapper = Mapper::singleton('Site');
            $this->_MainSite = $mapper->load(
                array('Id'=>$this->_MainSite));
        }
        return $this->_MainSite;
    }

    /**
     * _Actor::getMainSiteId
     *
     * @access public
     * @return integer
     */
    public function getMainSiteId() {
        if ($this->_MainSite instanceof Site) {
            return $this->_MainSite->getId();
        }
        return (int)$this->_MainSite;
    }

    /**
     * _Actor::setMainSite
     *
     * @access public
     * @param object Site $value
     * @return void
     */
    public function setMainSite($value) {
        if (is_numeric($value)) {
            $this->_MainSite = (int)$value;
        } else {
            $this->_MainSite = $value;
        }
    }

    // }}}
    // Category foreignkey property + getter/setter {{{

    /**
     * Category foreignkey
     *
     * @access private
     * @var mixed object Category or integer
     */
    private $_Category = false;

    /**
     * _Actor::getCategory
     *
     * @access public
     * @return object Category
     */
    public function getCategory() {
        if (is_int($this->_Category) && $this->_Category > 0) {
            $mapper = Mapper::singleton('Category');
            $this->_Category = $mapper->load(
                array('Id'=>$this->_Category));
        }
        return $this->_Category;
    }

    /**
     * _Actor::getCategoryId
     *
     * @access public
     * @return integer
     */
    public function getCategoryId() {
        if ($this->_Category instanceof Category) {
            return $this->_Category->getId();
        }
        return (int)$this->_Category;
    }

    /**
     * _Actor::setCategory
     *
     * @access public
     * @param object Category $value
     * @return void
     */
    public function setCategory($value) {
        if (is_numeric($value)) {
            $this->_Category = (int)$value;
        } else {
            $this->_Category = $value;
        }
    }

    // }}}
    // RemExcep float property + getter/setter {{{

    /**
     * RemExcep float property
     *
     * @access private
     * @var float
     */
    private $_RemExcep = 0;

    /**
     * _Actor::getRemExcep
     *
     * @access public
     * @return float
     */
    public function getRemExcep() {
        return $this->_RemExcep;
    }

    /**
     * _Actor::setRemExcep
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRemExcep($value) {
        if ($value !== null) {
            $this->_RemExcep = I18N::extractNumber($value);
        }
    }

    // }}}
    // Generic string property + getter/setter {{{

    /**
     * Generic int property
     *
     * @access private
     * @var integer
     */
    private $_Generic = 0;

    /**
     * _Actor::getGeneric
     *
     * @access public
     * @return integer
     */
    public function getGeneric() {
        return $this->_Generic;
    }

    /**
     * _Actor::setGeneric
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setGeneric($value) {
        if ($value !== null) {
            $this->_Generic = (int)$value;
        }
    }

    // }}}
    // GenericActor foreignkey property + getter/setter {{{

    /**
     * GenericActor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_GenericActor = false;

    /**
     * _Actor::getGenericActor
     *
     * @access public
     * @return object Actor
     */
    public function getGenericActor() {
        if (is_int($this->_GenericActor) && $this->_GenericActor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_GenericActor = $mapper->load(
                array('Id'=>$this->_GenericActor));
        }
        return $this->_GenericActor;
    }

    /**
     * _Actor::getGenericActorId
     *
     * @access public
     * @return integer
     */
    public function getGenericActorId() {
        if ($this->_GenericActor instanceof Actor) {
            return $this->_GenericActor->getId();
        }
        return (int)$this->_GenericActor;
    }

    /**
     * _Actor::setGenericActor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setGenericActor($value) {
        if (is_numeric($value)) {
            $this->_GenericActor = (int)$value;
        } else {
            $this->_GenericActor = $value;
        }
    }

    // }}}
    // Trademark string property + getter/setter {{{

    /**
     * Trademark string property
     *
     * @access private
     * @var string
     */
    private $_Trademark = '';

    /**
     * _Actor::getTrademark
     *
     * @access public
     * @return string
     */
    public function getTrademark() {
        return $this->_Trademark;
    }

    /**
     * _Actor::setTrademark
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setTrademark($value) {
        $this->_Trademark = $value;
    }

    // }}}
    // CompanyType string property + getter/setter {{{

    /**
     * CompanyType string property
     *
     * @access private
     * @var string
     */
    private $_CompanyType = '';

    /**
     * _Actor::getCompanyType
     *
     * @access public
     * @return string
     */
    public function getCompanyType() {
        return $this->_CompanyType;
    }

    /**
     * _Actor::setCompanyType
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCompanyType($value) {
        $this->_CompanyType = $value;
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
     * _Actor::getCreationDate
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
     * _Actor::setCreationDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCreationDate($value) {
        $this->_CreationDate = $value;
    }

    // }}}
    // Job one to many relation + getter/setter {{{

    /**
     * Job *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_JobCollection = false;

    /**
     * _Actor::getJobCollection
     *
     * @access public
     * @return object Collection
     */
    public function getJobCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getManyToMany($this->getId(),
                'Job', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_JobCollection) {
            $mapper = Mapper::singleton('Actor');
            $this->_JobCollection = $mapper->getManyToMany($this->getId(),
                'Job');
        }
        return $this->_JobCollection;
    }

    /**
     * _Actor::getJobCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getJobCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getJobCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_JobCollection) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getManyToManyIds($this->getId(), 'Job');
        }
        return $this->_JobCollection->getItemIds();
    }

    /**
     * _Actor::setJobCollectionIds
     *
     * @access public
     * @return array
     */
    public function setJobCollectionIds($itemIds) {
        $this->_JobCollection = new Collection('Job');
        foreach ($itemIds as $id) {
            $this->_JobCollection->setItem($id);
        }
    }

    /**
     * _Actor::setJobCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setJobCollection($value) {
        $this->_JobCollection = $value;
    }

    /**
     * _Actor::JobCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function JobCollectionIsLoaded() {
        return ($this->_JobCollection !== false);
    }

    // }}}
    // Operation one to many relation + getter/setter {{{

    /**
     * Operation *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_OperationCollection = false;

    /**
     * _Actor::getOperationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getOperationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getManyToMany($this->getId(),
                'Operation', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_OperationCollection) {
            $mapper = Mapper::singleton('Actor');
            $this->_OperationCollection = $mapper->getManyToMany($this->getId(),
                'Operation');
        }
        return $this->_OperationCollection;
    }

    /**
     * _Actor::getOperationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getOperationCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getOperationCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_OperationCollection) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getManyToManyIds($this->getId(), 'Operation');
        }
        return $this->_OperationCollection->getItemIds();
    }

    /**
     * _Actor::setOperationCollectionIds
     *
     * @access public
     * @return array
     */
    public function setOperationCollectionIds($itemIds) {
        $this->_OperationCollection = new Collection('Operation');
        foreach ($itemIds as $id) {
            $this->_OperationCollection->setItem($id);
        }
    }

    /**
     * _Actor::setOperationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setOperationCollection($value) {
        $this->_OperationCollection = $value;
    }

    /**
     * _Actor::OperationCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function OperationCollectionIsLoaded() {
        return ($this->_OperationCollection !== false);
    }

    // }}}
    // Currency foreignkey property + getter/setter {{{

    /**
     * Currency foreignkey
     *
     * @access private
     * @var mixed object Currency or integer
     */
    private $_Currency = false;

    /**
     * _Actor::getCurrency
     *
     * @access public
     * @return object Currency
     */
    public function getCurrency() {
        if (is_int($this->_Currency) && $this->_Currency > 0) {
            $mapper = Mapper::singleton('Currency');
            $this->_Currency = $mapper->load(
                array('Id'=>$this->_Currency));
        }
        return $this->_Currency;
    }

    /**
     * _Actor::getCurrencyId
     *
     * @access public
     * @return integer
     */
    public function getCurrencyId() {
        if ($this->_Currency instanceof Currency) {
            return $this->_Currency->getId();
        }
        return (int)$this->_Currency;
    }

    /**
     * _Actor::setCurrency
     *
     * @access public
     * @param object Currency $value
     * @return void
     */
    public function setCurrency($value) {
        if (is_numeric($value)) {
            $this->_Currency = (int)$value;
        } else {
            $this->_Currency = $value;
        }
    }

    // }}}
    // PricingZone foreignkey property + getter/setter {{{

    /**
     * PricingZone foreignkey
     *
     * @access private
     * @var mixed object PricingZone or integer
     */
    private $_PricingZone = false;

    /**
     * _Actor::getPricingZone
     *
     * @access public
     * @return object PricingZone
     */
    public function getPricingZone() {
        if (is_int($this->_PricingZone) && $this->_PricingZone > 0) {
            $mapper = Mapper::singleton('PricingZone');
            $this->_PricingZone = $mapper->load(
                array('Id'=>$this->_PricingZone));
        }
        return $this->_PricingZone;
    }

    /**
     * _Actor::getPricingZoneId
     *
     * @access public
     * @return integer
     */
    public function getPricingZoneId() {
        if ($this->_PricingZone instanceof PricingZone) {
            return $this->_PricingZone->getId();
        }
        return (int)$this->_PricingZone;
    }

    /**
     * _Actor::setPricingZone
     *
     * @access public
     * @param object PricingZone $value
     * @return void
     */
    public function setPricingZone($value) {
        if (is_numeric($value)) {
            $this->_PricingZone = (int)$value;
        } else {
            $this->_PricingZone = $value;
        }
    }

    // }}}
    // AccountingType foreignkey property + getter/setter {{{

    /**
     * AccountingType foreignkey
     *
     * @access private
     * @var mixed object AccountingType or integer
     */
    private $_AccountingType = false;

    /**
     * _Actor::getAccountingType
     *
     * @access public
     * @return object AccountingType
     */
    public function getAccountingType() {
        if (is_int($this->_AccountingType) && $this->_AccountingType > 0) {
            $mapper = Mapper::singleton('AccountingType');
            $this->_AccountingType = $mapper->load(
                array('Id'=>$this->_AccountingType));
        }
        return $this->_AccountingType;
    }

    /**
     * _Actor::getAccountingTypeId
     *
     * @access public
     * @return integer
     */
    public function getAccountingTypeId() {
        if ($this->_AccountingType instanceof AccountingType) {
            return $this->_AccountingType->getId();
        }
        return (int)$this->_AccountingType;
    }

    /**
     * _Actor::setAccountingType
     *
     * @access public
     * @param object AccountingType $value
     * @return void
     */
    public function setAccountingType($value) {
        if (is_numeric($value)) {
            $this->_AccountingType = (int)$value;
        } else {
            $this->_AccountingType = $value;
        }
    }

    // }}}
    // CustomerProperties foreignkey property + getter/setter {{{

    /**
     * CustomerProperties foreignkey
     *
     * @access private
     * @var mixed object CustomerProperties or integer
     */
    private $_CustomerProperties = false;

    /**
     * _Actor::getCustomerProperties
     *
     * @access public
     * @return object CustomerProperties
     */
    public function getCustomerProperties() {
        if (is_int($this->_CustomerProperties) && $this->_CustomerProperties > 0) {
            $mapper = Mapper::singleton('CustomerProperties');
            $this->_CustomerProperties = $mapper->load(
                array('Id'=>$this->_CustomerProperties));
        }
        return $this->_CustomerProperties;
    }

    /**
     * _Actor::getCustomerPropertiesId
     *
     * @access public
     * @return integer
     */
    public function getCustomerPropertiesId() {
        if ($this->_CustomerProperties instanceof CustomerProperties) {
            return $this->_CustomerProperties->getId();
        }
        return (int)$this->_CustomerProperties;
    }

    /**
     * _Actor::setCustomerProperties
     *
     * @access public
     * @param object CustomerProperties $value
     * @return void
     */
    public function setCustomerProperties($value) {
        if (is_numeric($value)) {
            $this->_CustomerProperties = (int)$value;
        } else {
            $this->_CustomerProperties = $value;
        }
    }

    // }}}
    // ActorDetail foreignkey property + getter/setter {{{

    /**
     * ActorDetail foreignkey
     *
     * @access private
     * @var mixed object ActorDetail or integer
     */
    private $_ActorDetail = false;

    /**
     * _Actor::getActorDetail
     *
     * @access public
     * @return object ActorDetail
     */
    public function getActorDetail() {
        if (is_int($this->_ActorDetail) && $this->_ActorDetail > 0) {
            $mapper = Mapper::singleton('ActorDetail');
            $this->_ActorDetail = $mapper->load(
                array('Id'=>$this->_ActorDetail));
        }
        return $this->_ActorDetail;
    }

    /**
     * _Actor::getActorDetailId
     *
     * @access public
     * @return integer
     */
    public function getActorDetailId() {
        if ($this->_ActorDetail instanceof ActorDetail) {
            return $this->_ActorDetail->getId();
        }
        return (int)$this->_ActorDetail;
    }

    /**
     * _Actor::setActorDetail
     *
     * @access public
     * @param object ActorDetail $value
     * @return void
     */
    public function setActorDetail($value) {
        if (is_numeric($value)) {
            $this->_ActorDetail = (int)$value;
        } else {
            $this->_ActorDetail = $value;
        }
    }

    // }}}
    // DocumentAppendix one to many relation + getter/setter {{{

    /**
     * DocumentAppendix *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_DocumentAppendixCollection = false;

    /**
     * _Actor::getDocumentAppendixCollection
     *
     * @access public
     * @return object Collection
     */
    public function getDocumentAppendixCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getManyToMany($this->getId(),
                'DocumentAppendix', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_DocumentAppendixCollection) {
            $mapper = Mapper::singleton('Actor');
            $this->_DocumentAppendixCollection = $mapper->getManyToMany($this->getId(),
                'DocumentAppendix');
        }
        return $this->_DocumentAppendixCollection;
    }

    /**
     * _Actor::getDocumentAppendixCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getDocumentAppendixCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getDocumentAppendixCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_DocumentAppendixCollection) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getManyToManyIds($this->getId(), 'DocumentAppendix');
        }
        return $this->_DocumentAppendixCollection->getItemIds();
    }

    /**
     * _Actor::setDocumentAppendixCollectionIds
     *
     * @access public
     * @return array
     */
    public function setDocumentAppendixCollectionIds($itemIds) {
        $this->_DocumentAppendixCollection = new Collection('DocumentAppendix');
        foreach ($itemIds as $id) {
            $this->_DocumentAppendixCollection->setItem($id);
        }
    }

    /**
     * _Actor::setDocumentAppendixCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setDocumentAppendixCollection($value) {
        $this->_DocumentAppendixCollection = $value;
    }

    /**
     * _Actor::DocumentAppendixCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function DocumentAppendixCollectionIsLoaded() {
        return ($this->_DocumentAppendixCollection !== false);
    }

    // }}}
    // Actor one to many relation + getter/setter {{{

    /**
     * Actor 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActorCollection = false;

    /**
     * _Actor::getActorCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActorCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getOneToMany($this->getId(),
                'Actor', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActorCollection) {
            $mapper = Mapper::singleton('Actor');
            $this->_ActorCollection = $mapper->getOneToMany($this->getId(),
                'Actor');
        }
        return $this->_ActorCollection;
    }

    /**
     * _Actor::getActorCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActorCollectionIds($filter = array()) {
        $col = $this->getActorCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Actor::setActorCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActorCollection($value) {
        $this->_ActorCollection = $value;
    }

    // }}}
    // ActorBankDetail one to many relation + getter/setter {{{

    /**
     * ActorBankDetail 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActorBankDetailCollection = false;

    /**
     * _Actor::getActorBankDetailCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActorBankDetailCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getOneToMany($this->getId(),
                'ActorBankDetail', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActorBankDetailCollection) {
            $mapper = Mapper::singleton('Actor');
            $this->_ActorBankDetailCollection = $mapper->getOneToMany($this->getId(),
                'ActorBankDetail');
        }
        return $this->_ActorBankDetailCollection;
    }

    /**
     * _Actor::getActorBankDetailCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActorBankDetailCollectionIds($filter = array()) {
        $col = $this->getActorBankDetailCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Actor::setActorBankDetailCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActorBankDetailCollection($value) {
        $this->_ActorBankDetailCollection = $value;
    }

    // }}}
    // ActorProduct one to many relation + getter/setter {{{

    /**
     * ActorProduct 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActorProductCollection = false;

    /**
     * _Actor::getActorProductCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActorProductCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getOneToMany($this->getId(),
                'ActorProduct', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActorProductCollection) {
            $mapper = Mapper::singleton('Actor');
            $this->_ActorProductCollection = $mapper->getOneToMany($this->getId(),
                'ActorProduct');
        }
        return $this->_ActorProductCollection;
    }

    /**
     * _Actor::getActorProductCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActorProductCollectionIds($filter = array()) {
        $col = $this->getActorProductCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Actor::setActorProductCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActorProductCollection($value) {
        $this->_ActorProductCollection = $value;
    }

    // }}}
    // Site one to many relation + getter/setter {{{

    /**
     * Site 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_SiteCollection = false;

    /**
     * _Actor::getSiteCollection
     *
     * @access public
     * @return object Collection
     */
    public function getSiteCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getOneToMany($this->getId(),
                'Site', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_SiteCollection) {
            $mapper = Mapper::singleton('Actor');
            $this->_SiteCollection = $mapper->getOneToMany($this->getId(),
                'Site');
        }
        return $this->_SiteCollection;
    }

    /**
     * _Actor::getSiteCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getSiteCollectionIds($filter = array()) {
        $col = $this->getSiteCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Actor::setSiteCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setSiteCollection($value) {
        $this->_SiteCollection = $value;
    }

    // }}}
    // StorageSite one to many relation + getter/setter {{{

    /**
     * StorageSite 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_StorageSiteCollection = false;

    /**
     * _Actor::getStorageSiteCollection
     *
     * @access public
     * @return object Collection
     */
    public function getStorageSiteCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getOneToMany($this->getId(),
                'StorageSite', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_StorageSiteCollection) {
            $mapper = Mapper::singleton('Actor');
            $this->_StorageSiteCollection = $mapper->getOneToMany($this->getId(),
                'StorageSite');
        }
        return $this->_StorageSiteCollection;
    }

    /**
     * _Actor::getStorageSiteCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getStorageSiteCollectionIds($filter = array()) {
        $col = $this->getStorageSiteCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Actor::setStorageSiteCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setStorageSiteCollection($value) {
        $this->_StorageSiteCollection = $value;
    }

    // }}}
    // Store one to many relation + getter/setter {{{

    /**
     * Store 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_StoreCollection = false;

    /**
     * _Actor::getStoreCollection
     *
     * @access public
     * @return object Collection
     */
    public function getStoreCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getOneToMany($this->getId(),
                'Store', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_StoreCollection) {
            $mapper = Mapper::singleton('Actor');
            $this->_StoreCollection = $mapper->getOneToMany($this->getId(),
                'Store');
        }
        return $this->_StoreCollection;
    }

    /**
     * _Actor::getStoreCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getStoreCollectionIds($filter = array()) {
        $col = $this->getStoreCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Actor::setStoreCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setStoreCollection($value) {
        $this->_StoreCollection = $value;
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
     * _Actor::getUploadedDocumentCollection
     *
     * @access public
     * @return object Collection
     */
    public function getUploadedDocumentCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getOneToMany($this->getId(),
                'UploadedDocument', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_UploadedDocumentCollection) {
            $mapper = Mapper::singleton('Actor');
            $this->_UploadedDocumentCollection = $mapper->getOneToMany($this->getId(),
                'UploadedDocument');
        }
        return $this->_UploadedDocumentCollection;
    }

    /**
     * _Actor::getUploadedDocumentCollectionIds
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
     * _Actor::setUploadedDocumentCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setUploadedDocumentCollection($value) {
        $this->_UploadedDocumentCollection = $value;
    }

    // }}}
    // UserAccount one to many relation + getter/setter {{{

    /**
     * UserAccount 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_UserAccountCollection = false;

    /**
     * _Actor::getUserAccountCollection
     *
     * @access public
     * @return object Collection
     */
    public function getUserAccountCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Actor');
            return $mapper->getOneToMany($this->getId(),
                'UserAccount', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_UserAccountCollection) {
            $mapper = Mapper::singleton('Actor');
            $this->_UserAccountCollection = $mapper->getOneToMany($this->getId(),
                'UserAccount');
        }
        return $this->_UserAccountCollection;
    }

    /**
     * _Actor::getUserAccountCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getUserAccountCollectionIds($filter = array()) {
        $col = $this->getUserAccountCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Actor::setUserAccountCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setUserAccountCollection($value) {
        $this->_UserAccountCollection = $value;
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
        return 'Actor';
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
            'DatabaseOwner' => Object::TYPE_BOOL,
            'Quality' => Object::TYPE_CONST,
            'Code' => Object::TYPE_STRING,
            'Siret' => Object::TYPE_STRING,
            'IATA' => Object::TYPE_STRING,
            'Logo' => Object::TYPE_TEXT,
            'Slogan' => Object::TYPE_I18N_STRING,
            'TVA' => Object::TYPE_STRING,
            'RCS' => Object::TYPE_STRING,
            'Role' => Object::TYPE_STRING,
            'Active' => Object::TYPE_INT,
            'PaymentCondition' => Object::TYPE_INT,
            'Incoterm' => 'Incoterm',
            'PackageCondition' => Object::TYPE_INT,
            'Commercial' => 'UserAccount',
            'PlanningComment' => Object::TYPE_STRING,
            'MainSite' => 'Site',
            'Category' => 'Category',
            'RemExcep' => Object::TYPE_FLOAT,
            'Generic' => Object::TYPE_INT,
            'GenericActor' => 'Actor',
            'Trademark' => Object::TYPE_STRING,
            'CompanyType' => Object::TYPE_STRING,
            'CreationDate' => Object::TYPE_DATETIME,
            'Currency' => 'Currency',
            'PricingZone' => 'PricingZone',
            'AccountingType' => 'AccountingType',
            'CustomerProperties' => 'CustomerProperties',
            'ActorDetail' => 'ActorDetail');
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
            'Job'=>array(
                'linkClass'     => 'Job',
                'field'         => 'FromActor',
                'linkTable'     => 'actJob',
                'linkField'     => 'ToJob',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Operation'=>array(
                'linkClass'     => 'Operation',
                'field'         => 'FromActor',
                'linkTable'     => 'actOperation',
                'linkField'     => 'ToOperation',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ActorDetail'=>array(
                'linkClass'     => 'ActorDetail',
                'field'         => 'Actor',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetoone'
            ),
            'DocumentAppendix'=>array(
                'linkClass'     => 'DocumentAppendix',
                'field'         => 'FromActor',
                'linkTable'     => 'actDocumentAppendix',
                'linkField'     => 'ToDocumentAppendix',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'AbstractDocument'=>array(
                'linkClass'     => 'AbstractDocument',
                'field'         => 'AccountingTypeActor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Action'=>array(
                'linkClass'     => 'Action',
                'field'         => 'Actor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChain'=>array(
                'linkClass'     => 'ActivatedChain',
                'field'         => 'Owner',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainOperation'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'Actor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainOperation_1'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'RealActor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'DepartureActor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainTask_1'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'ArrivalActor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Actor'=>array(
                'linkClass'     => 'Actor',
                'field'         => 'GenericActor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActorBankDetail'=>array(
                'linkClass'     => 'ActorBankDetail',
                'field'         => 'Actor',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ActorDetail_1'=>array(
                'linkClass'     => 'ActorDetail',
                'field'         => 'InternalAffectation',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActorDetail_2'=>array(
                'linkClass'     => 'ActorDetail',
                'field'         => 'Signatory',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActorDetail_3'=>array(
                'linkClass'     => 'ActorDetail',
                'field'         => 'BusinessProvider',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActorProduct'=>array(
                'linkClass'     => 'ActorProduct',
                'field'         => 'Actor',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ActorSiteTransition'=>array(
                'linkClass'     => 'ActorSiteTransition',
                'field'         => 'DepartureActor',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'ActorSiteTransition_1'=>array(
                'linkClass'     => 'ActorSiteTransition',
                'field'         => 'ArrivalActor',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'Box'=>array(
                'linkClass'     => 'Box',
                'field'         => 'Expeditor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Box_1'=>array(
                'linkClass'     => 'Box',
                'field'         => 'Destinator',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Chain'=>array(
                'linkClass'     => 'Chain',
                'field'         => 'Owner',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainOperation'=>array(
                'linkClass'     => 'ChainOperation',
                'field'         => 'Actor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainTask'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'DepartureActor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainTask_1'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'ArrivalActor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Command'=>array(
                'linkClass'     => 'Command',
                'field'         => 'Expeditor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Command_1'=>array(
                'linkClass'     => 'Command',
                'field'         => 'Destinator',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Command_2'=>array(
                'linkClass'     => 'Command',
                'field'         => 'Customer',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ConcreteProduct'=>array(
                'linkClass'     => 'ConcreteProduct',
                'field'         => 'Owner',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'DocumentModel'=>array(
                'linkClass'     => 'DocumentModel',
                'field'         => 'Actor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'FlowType'=>array(
                'linkClass'     => 'FlowType',
                'field'         => 'ThirdParty',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ForwardingForm'=>array(
                'linkClass'     => 'ForwardingForm',
                'field'         => 'Transporter',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'PrestationCustomer'=>array(
                'linkClass'     => 'PrestationCustomer',
                'field'         => 'Actor',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Product'=>array(
                'linkClass'     => 'Product',
                'field'         => 'Owner',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Site'=>array(
                'linkClass'     => 'Site',
                'field'         => 'Owner',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'StorageSite'=>array(
                'linkClass'     => 'StorageSite',
                'field'         => 'StockOwner',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Store'=>array(
                'linkClass'     => 'Store',
                'field'         => 'StockOwner',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'SupplierCustomer'=>array(
                'linkClass'     => 'SupplierCustomer',
                'field'         => 'Supplier',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'SupplierCustomer_1'=>array(
                'linkClass'     => 'SupplierCustomer',
                'field'         => 'Customer',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'UploadedDocument'=>array(
                'linkClass'     => 'UploadedDocument',
                'field'         => 'Customer',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'UserAccount'=>array(
                'linkClass'     => 'UserAccount',
                'field'         => 'Actor',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'WorkOrder'=>array(
                'linkClass'     => 'WorkOrder',
                'field'         => 'Actor',
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
    // _Actor::mutate() {{{

    /**
     * "Mutation" d'un objet parent en classe fille et vice-versa.
     * Cela permet par exemple dans un formulaire de modifier la classe d'un
     * objet via un select.
     *
     * @access public
     * @param string type le type de l'objet vers lequel 'muter'
     * @return object
     **/
    public function mutate($type){
        // on instancie le bon objet
        require_once('Objects/' . $type . '.php');
        $mutant = new $type();
        if(!($mutant instanceof _Actor)) {
            trigger_error('Invalid classname provided.', E_USER_ERROR);
        }
        // propriétés fixes
        $mutant->hasBeenInitialized = $this->hasBeenInitialized;
        $mutant->readonly = $this->readonly;
        $mutant->setId($this->getId());
        // propriétés simples
        $properties = $this->getProperties();
        foreach($properties as $property=>$type){
            $getter = 'get' . $property;
            $setter = 'set' . $property;
            if (method_exists($mutant, $setter)) {
                $mutant->$setter($this->$getter());
            }
        }
        // relations
        $links = $this->getLinks();
        foreach($links as $property=>$data){
            $getter = 'get' . $property . 'Collection';
            $setter = 'set' . $property . 'Collection';
            if (method_exists($mutant, $setter)) {
                $mutant->$setter($this->$getter());
            }
        }
        return $mutant;
    }

    // }}}
}

?>