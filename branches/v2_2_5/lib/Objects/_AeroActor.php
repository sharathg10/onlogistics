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

class _AeroActor extends Actor {
    
    // Constructeur {{{

    /**
     * _AeroActor::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Weight float property + getter/setter {{{

    /**
     * Weight float property
     *
     * @access private
     * @var float
     */
    private $_Weight = 0;

    /**
     * _AeroActor::getWeight
     *
     * @access public
     * @return float
     */
    public function getWeight() {
        return $this->_Weight;
    }

    /**
     * _AeroActor::setWeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setWeight($value) {
        if ($value !== null) {
            $this->_Weight = I18N::extractNumber($value);
        }
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
     * _AeroActor::getCost
     *
     * @access public
     * @return float
     */
    public function getCost() {
        return $this->_Cost;
    }

    /**
     * _AeroActor::setCost
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCost($value) {
        if ($value !== null) {
            $this->_Cost = I18N::extractNumber($value);
        }
    }

    // }}}
    // IFRLanding string property + getter/setter {{{

    /**
     * IFRLanding int property
     *
     * @access private
     * @var integer
     */
    private $_IFRLanding = 0;

    /**
     * _AeroActor::getIFRLanding
     *
     * @access public
     * @return integer
     */
    public function getIFRLanding() {
        return $this->_IFRLanding;
    }

    /**
     * _AeroActor::setIFRLanding
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setIFRLanding($value) {
        if ($value !== null) {
            $this->_IFRLanding = (int)$value;
        }
    }

    // }}}
    // PilotHours string property + getter/setter {{{

    /**
     * PilotHours int property
     *
     * @access private
     * @var integer
     */
    private $_PilotHours = 0;

    /**
     * _AeroActor::getPilotHours
     *
     * @access public
     * @return integer
     */
    public function getPilotHours() {
        return $this->_PilotHours;
    }

    /**
     * _AeroActor::setPilotHours
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPilotHours($value) {
        if ($value !== null) {
            $this->_PilotHours = (int)$value;
        }
    }

    // }}}
    // PilotHoursBiEngine string property + getter/setter {{{

    /**
     * PilotHoursBiEngine int property
     *
     * @access private
     * @var integer
     */
    private $_PilotHoursBiEngine = 0;

    /**
     * _AeroActor::getPilotHoursBiEngine
     *
     * @access public
     * @return integer
     */
    public function getPilotHoursBiEngine() {
        return $this->_PilotHoursBiEngine;
    }

    /**
     * _AeroActor::setPilotHoursBiEngine
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPilotHoursBiEngine($value) {
        if ($value !== null) {
            $this->_PilotHoursBiEngine = (int)$value;
        }
    }

    // }}}
    // CoPilotHours string property + getter/setter {{{

    /**
     * CoPilotHours int property
     *
     * @access private
     * @var integer
     */
    private $_CoPilotHours = 0;

    /**
     * _AeroActor::getCoPilotHours
     *
     * @access public
     * @return integer
     */
    public function getCoPilotHours() {
        return $this->_CoPilotHours;
    }

    /**
     * _AeroActor::setCoPilotHours
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoPilotHours($value) {
        if ($value !== null) {
            $this->_CoPilotHours = (int)$value;
        }
    }

    // }}}
    // CoPilotHoursBiEngine string property + getter/setter {{{

    /**
     * CoPilotHoursBiEngine int property
     *
     * @access private
     * @var integer
     */
    private $_CoPilotHoursBiEngine = 0;

    /**
     * _AeroActor::getCoPilotHoursBiEngine
     *
     * @access public
     * @return integer
     */
    public function getCoPilotHoursBiEngine() {
        return $this->_CoPilotHoursBiEngine;
    }

    /**
     * _AeroActor::setCoPilotHoursBiEngine
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoPilotHoursBiEngine($value) {
        if ($value !== null) {
            $this->_CoPilotHoursBiEngine = (int)$value;
        }
    }

    // }}}
    // PilotHoursNight string property + getter/setter {{{

    /**
     * PilotHoursNight int property
     *
     * @access private
     * @var integer
     */
    private $_PilotHoursNight = 0;

    /**
     * _AeroActor::getPilotHoursNight
     *
     * @access public
     * @return integer
     */
    public function getPilotHoursNight() {
        return $this->_PilotHoursNight;
    }

    /**
     * _AeroActor::setPilotHoursNight
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPilotHoursNight($value) {
        if ($value !== null) {
            $this->_PilotHoursNight = (int)$value;
        }
    }

    // }}}
    // PilotHoursBiEngineNight string property + getter/setter {{{

    /**
     * PilotHoursBiEngineNight int property
     *
     * @access private
     * @var integer
     */
    private $_PilotHoursBiEngineNight = 0;

    /**
     * _AeroActor::getPilotHoursBiEngineNight
     *
     * @access public
     * @return integer
     */
    public function getPilotHoursBiEngineNight() {
        return $this->_PilotHoursBiEngineNight;
    }

    /**
     * _AeroActor::setPilotHoursBiEngineNight
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPilotHoursBiEngineNight($value) {
        if ($value !== null) {
            $this->_PilotHoursBiEngineNight = (int)$value;
        }
    }

    // }}}
    // CoPilotHoursNight string property + getter/setter {{{

    /**
     * CoPilotHoursNight int property
     *
     * @access private
     * @var integer
     */
    private $_CoPilotHoursNight = 0;

    /**
     * _AeroActor::getCoPilotHoursNight
     *
     * @access public
     * @return integer
     */
    public function getCoPilotHoursNight() {
        return $this->_CoPilotHoursNight;
    }

    /**
     * _AeroActor::setCoPilotHoursNight
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoPilotHoursNight($value) {
        if ($value !== null) {
            $this->_CoPilotHoursNight = (int)$value;
        }
    }

    // }}}
    // CoPilotHoursBiEngineNight string property + getter/setter {{{

    /**
     * CoPilotHoursBiEngineNight int property
     *
     * @access private
     * @var integer
     */
    private $_CoPilotHoursBiEngineNight = 0;

    /**
     * _AeroActor::getCoPilotHoursBiEngineNight
     *
     * @access public
     * @return integer
     */
    public function getCoPilotHoursBiEngineNight() {
        return $this->_CoPilotHoursBiEngineNight;
    }

    /**
     * _AeroActor::setCoPilotHoursBiEngineNight
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoPilotHoursBiEngineNight($value) {
        if ($value !== null) {
            $this->_CoPilotHoursBiEngineNight = (int)$value;
        }
    }

    // }}}
    // PilotHoursIFR string property + getter/setter {{{

    /**
     * PilotHoursIFR int property
     *
     * @access private
     * @var integer
     */
    private $_PilotHoursIFR = 0;

    /**
     * _AeroActor::getPilotHoursIFR
     *
     * @access public
     * @return integer
     */
    public function getPilotHoursIFR() {
        return $this->_PilotHoursIFR;
    }

    /**
     * _AeroActor::setPilotHoursIFR
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPilotHoursIFR($value) {
        if ($value !== null) {
            $this->_PilotHoursIFR = (int)$value;
        }
    }

    // }}}
    // CoPilotHoursIFR string property + getter/setter {{{

    /**
     * CoPilotHoursIFR int property
     *
     * @access private
     * @var integer
     */
    private $_CoPilotHoursIFR = 0;

    /**
     * _AeroActor::getCoPilotHoursIFR
     *
     * @access public
     * @return integer
     */
    public function getCoPilotHoursIFR() {
        return $this->_CoPilotHoursIFR;
    }

    /**
     * _AeroActor::setCoPilotHoursIFR
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoPilotHoursIFR($value) {
        if ($value !== null) {
            $this->_CoPilotHoursIFR = (int)$value;
        }
    }

    // }}}
    // StudentHours string property + getter/setter {{{

    /**
     * StudentHours int property
     *
     * @access private
     * @var integer
     */
    private $_StudentHours = 0;

    /**
     * _AeroActor::getStudentHours
     *
     * @access public
     * @return integer
     */
    public function getStudentHours() {
        return $this->_StudentHours;
    }

    /**
     * _AeroActor::setStudentHours
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setStudentHours($value) {
        if ($value !== null) {
            $this->_StudentHours = (int)$value;
        }
    }

    // }}}
    // InstructorHours string property + getter/setter {{{

    /**
     * InstructorHours int property
     *
     * @access private
     * @var integer
     */
    private $_InstructorHours = 0;

    /**
     * _AeroActor::getInstructorHours
     *
     * @access public
     * @return integer
     */
    public function getInstructorHours() {
        return $this->_InstructorHours;
    }

    /**
     * _AeroActor::setInstructorHours
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setInstructorHours($value) {
        if ($value !== null) {
            $this->_InstructorHours = (int)$value;
        }
    }

    // }}}
    // PublicHours string property + getter/setter {{{

    /**
     * PublicHours int property
     *
     * @access private
     * @var integer
     */
    private $_PublicHours = 0;

    /**
     * _AeroActor::getPublicHours
     *
     * @access public
     * @return integer
     */
    public function getPublicHours() {
        return $this->_PublicHours;
    }

    /**
     * _AeroActor::setPublicHours
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPublicHours($value) {
        if ($value !== null) {
            $this->_PublicHours = (int)$value;
        }
    }

    // }}}
    // CommercialHours string property + getter/setter {{{

    /**
     * CommercialHours int property
     *
     * @access private
     * @var integer
     */
    private $_CommercialHours = 0;

    /**
     * _AeroActor::getCommercialHours
     *
     * @access public
     * @return integer
     */
    public function getCommercialHours() {
        return $this->_CommercialHours;
    }

    /**
     * _AeroActor::setCommercialHours
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCommercialHours($value) {
        if ($value !== null) {
            $this->_CommercialHours = (int)$value;
        }
    }

    // }}}
    // VLAEHours string property + getter/setter {{{

    /**
     * VLAEHours int property
     *
     * @access private
     * @var integer
     */
    private $_VLAEHours = 0;

    /**
     * _AeroActor::getVLAEHours
     *
     * @access public
     * @return integer
     */
    public function getVLAEHours() {
        return $this->_VLAEHours;
    }

    /**
     * _AeroActor::setVLAEHours
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setVLAEHours($value) {
        if ($value !== null) {
            $this->_VLAEHours = (int)$value;
        }
    }

    // }}}
    // AuthorizedFlyType one to many relation + getter/setter {{{

    /**
     * AuthorizedFlyType *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_AuthorizedFlyTypeCollection = false;

    /**
     * _AeroActor::getAuthorizedFlyTypeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getAuthorizedFlyTypeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('AeroActor');
            return $mapper->getManyToMany($this->getId(),
                'AuthorizedFlyType', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_AuthorizedFlyTypeCollection) {
            $mapper = Mapper::singleton('AeroActor');
            $this->_AuthorizedFlyTypeCollection = $mapper->getManyToMany($this->getId(),
                'AuthorizedFlyType');
        }
        return $this->_AuthorizedFlyTypeCollection;
    }

    /**
     * _AeroActor::getAuthorizedFlyTypeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getAuthorizedFlyTypeCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getAuthorizedFlyTypeCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_AuthorizedFlyTypeCollection) {
            $mapper = Mapper::singleton('AeroActor');
            return $mapper->getManyToManyIds($this->getId(), 'AuthorizedFlyType');
        }
        return $this->_AuthorizedFlyTypeCollection->getItemIds();
    }

    /**
     * _AeroActor::setAuthorizedFlyTypeCollectionIds
     *
     * @access public
     * @return array
     */
    public function setAuthorizedFlyTypeCollectionIds($itemIds) {
        $this->_AuthorizedFlyTypeCollection = new Collection('AuthorizedFlyType');
        foreach ($itemIds as $id) {
            $this->_AuthorizedFlyTypeCollection->setItem($id);
        }
    }

    /**
     * _AeroActor::setAuthorizedFlyTypeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setAuthorizedFlyTypeCollection($value) {
        $this->_AuthorizedFlyTypeCollection = $value;
    }

    /**
     * _AeroActor::AuthorizedFlyTypeCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function AuthorizedFlyTypeCollectionIsLoaded() {
        return ($this->_AuthorizedFlyTypeCollection !== false);
    }

    // }}}
    // Licence one to many relation + getter/setter {{{

    /**
     * Licence *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LicenceCollection = false;

    /**
     * _AeroActor::getLicenceCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLicenceCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('AeroActor');
            return $mapper->getManyToMany($this->getId(),
                'Licence', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LicenceCollection) {
            $mapper = Mapper::singleton('AeroActor');
            $this->_LicenceCollection = $mapper->getManyToMany($this->getId(),
                'Licence');
        }
        return $this->_LicenceCollection;
    }

    /**
     * _AeroActor::getLicenceCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLicenceCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getLicenceCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_LicenceCollection) {
            $mapper = Mapper::singleton('AeroActor');
            return $mapper->getManyToManyIds($this->getId(), 'Licence');
        }
        return $this->_LicenceCollection->getItemIds();
    }

    /**
     * _AeroActor::setLicenceCollectionIds
     *
     * @access public
     * @return array
     */
    public function setLicenceCollectionIds($itemIds) {
        $this->_LicenceCollection = new Collection('Licence');
        foreach ($itemIds as $id) {
            $this->_LicenceCollection->setItem($id);
        }
    }

    /**
     * _AeroActor::setLicenceCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLicenceCollection($value) {
        $this->_LicenceCollection = $value;
    }

    /**
     * _AeroActor::LicenceCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function LicenceCollectionIsLoaded() {
        return ($this->_LicenceCollection !== false);
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
    public static function getProperties($ownOnly = false) {
        $return = array(
            'Weight' => Object::TYPE_FLOAT,
            'Cost' => Object::TYPE_FLOAT,
            'IFRLanding' => Object::TYPE_INT,
            'PilotHours' => Object::TYPE_INT,
            'PilotHoursBiEngine' => Object::TYPE_INT,
            'CoPilotHours' => Object::TYPE_INT,
            'CoPilotHoursBiEngine' => Object::TYPE_INT,
            'PilotHoursNight' => Object::TYPE_INT,
            'PilotHoursBiEngineNight' => Object::TYPE_INT,
            'CoPilotHoursNight' => Object::TYPE_INT,
            'CoPilotHoursBiEngineNight' => Object::TYPE_INT,
            'PilotHoursIFR' => Object::TYPE_INT,
            'CoPilotHoursIFR' => Object::TYPE_INT,
            'StudentHours' => Object::TYPE_INT,
            'InstructorHours' => Object::TYPE_INT,
            'PublicHours' => Object::TYPE_INT,
            'CommercialHours' => Object::TYPE_INT,
            'VLAEHours' => Object::TYPE_INT);
        return $ownOnly?$return:array_merge(parent::getProperties(), $return);
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
    public static function getLinks($ownOnly = false) {
        $return = array(
            'AuthorizedFlyType'=>array(
                'linkClass'     => 'FlyType',
                'field'         => 'ToAeroActor',
                'linkTable'     => 'aacFlyType',
                'linkField'     => 'FromFlyType',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'Licence'=>array(
                'linkClass'     => 'Licence',
                'field'         => 'ToAeroActor',
                'linkTable'     => 'aacLicence',
                'linkField'     => 'FromLicence',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ));
        return $ownOnly?$return:array_merge(parent::getLinks(), $return);
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
        return array_merge(parent::getUniqueProperties(), $return);
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
        return array_merge(parent::getEmptyForDeleteProperties(), $return);
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
    public static function getMapping($ownOnly = false) {
        $return = array();
        return $ownOnly?$return:array_merge(parent::getMapping(), $return);
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
    // getParentClassName() {{{

    /**
     * Retourne le nom de la première classe parente
     *
     * @static
     * @access public
     * @return string
     */
    public static function getParentClassName() {
        return 'Actor';
    }

    // }}}
}

?>