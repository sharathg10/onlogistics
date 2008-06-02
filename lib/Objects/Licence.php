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

class Licence extends Object {
    // class constants {{{

    const TOBECHECKED_NEVER = 0;
    const TOBECHECKED_ALERT = 1;
    const TOBECHECKED_ALERT_COMMAND = 2;

    // }}}
    // Constructeur {{{

    /**
     * Licence::__construct()
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
     * Licence::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * Licence::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Number string property + getter/setter {{{

    /**
     * Number string property
     *
     * @access private
     * @var string
     */
    private $_Number = '';

    /**
     * Licence::getNumber
     *
     * @access public
     * @return string
     */
    public function getNumber() {
        return $this->_Number;
    }

    /**
     * Licence::setNumber
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setNumber($value) {
        $this->_Number = $value;
    }

    // }}}
    // BeginDate datetime property + getter/setter {{{

    /**
     * BeginDate int property
     *
     * @access private
     * @var string
     */
    private $_BeginDate = 0;

    /**
     * Licence::getBeginDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getBeginDate($format = false) {
        return $this->dateFormat($this->_BeginDate, $format);
    }

    /**
     * Licence::setBeginDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBeginDate($value) {
        $this->_BeginDate = $value;
    }

    // }}}
    // EndDate datetime property + getter/setter {{{

    /**
     * EndDate int property
     *
     * @access private
     * @var string
     */
    private $_EndDate = 0;

    /**
     * Licence::getEndDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEndDate($format = false) {
        return $this->dateFormat($this->_EndDate, $format);
    }

    /**
     * Licence::setEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndDate($value) {
        $this->_EndDate = $value;
    }

    // }}}
    // Duration string property + getter/setter {{{

    /**
     * Duration int property
     *
     * @access private
     * @var integer
     */
    private $_Duration = 0;

    /**
     * Licence::getDuration
     *
     * @access public
     * @return integer
     */
    public function getDuration() {
        return $this->_Duration;
    }

    /**
     * Licence::setDuration
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDuration($value) {
        if ($value !== null) {
            $this->_Duration = (int)$value;
        }
    }

    // }}}
    // DurationType string property + getter/setter {{{

    /**
     * DurationType int property
     *
     * @access private
     * @var integer
     */
    private $_DurationType = 0;

    /**
     * Licence::getDurationType
     *
     * @access public
     * @return integer
     */
    public function getDurationType() {
        return $this->_DurationType;
    }

    /**
     * Licence::setDurationType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDurationType($value) {
        if ($value !== null) {
            $this->_DurationType = (int)$value;
        }
    }

    // }}}
    // AlertDateType string property + getter/setter {{{

    /**
     * AlertDateType int property
     *
     * @access private
     * @var integer
     */
    private $_AlertDateType = 0;

    /**
     * Licence::getAlertDateType
     *
     * @access public
     * @return integer
     */
    public function getAlertDateType() {
        return $this->_AlertDateType;
    }

    /**
     * Licence::setAlertDateType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setAlertDateType($value) {
        if ($value !== null) {
            $this->_AlertDateType = (int)$value;
        }
    }

    // }}}
    // DelayForAlert string property + getter/setter {{{

    /**
     * DelayForAlert int property
     *
     * @access private
     * @var integer
     */
    private $_DelayForAlert = 0;

    /**
     * Licence::getDelayForAlert
     *
     * @access public
     * @return integer
     */
    public function getDelayForAlert() {
        return $this->_DelayForAlert;
    }

    /**
     * Licence::setDelayForAlert
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDelayForAlert($value) {
        if ($value !== null) {
            $this->_DelayForAlert = (int)$value;
        }
    }

    // }}}
    // ToBeChecked const property + getter/setter/getToBeCheckedConstArray {{{

    /**
     * ToBeChecked int property
     *
     * @access private
     * @var integer
     */
    private $_ToBeChecked = 0;

    /**
     * Licence::getToBeChecked
     *
     * @access public
     * @return integer
     */
    public function getToBeChecked() {
        return $this->_ToBeChecked;
    }

    /**
     * Licence::setToBeChecked
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setToBeChecked($value) {
        if ($value !== null) {
            $this->_ToBeChecked = (int)$value;
        }
    }

    /**
     * Licence::getToBeCheckedConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getToBeCheckedConstArray($keys = false) {
        $array = array(
            Licence::TOBECHECKED_NEVER => _("No control"), 
            Licence::TOBECHECKED_ALERT => _("Alert mailing control"), 
            Licence::TOBECHECKED_ALERT_COMMAND => _("Alert sending control and order")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // LicenceType foreignkey property + getter/setter {{{

    /**
     * LicenceType foreignkey
     *
     * @access private
     * @var mixed object LicenceType or integer
     */
    private $_LicenceType = false;

    /**
     * Licence::getLicenceType
     *
     * @access public
     * @return object LicenceType
     */
    public function getLicenceType() {
        if (is_int($this->_LicenceType) && $this->_LicenceType > 0) {
            $mapper = Mapper::singleton('LicenceType');
            $this->_LicenceType = $mapper->load(
                array('Id'=>$this->_LicenceType));
        }
        return $this->_LicenceType;
    }

    /**
     * Licence::getLicenceTypeId
     *
     * @access public
     * @return integer
     */
    public function getLicenceTypeId() {
        if ($this->_LicenceType instanceof LicenceType) {
            return $this->_LicenceType->getId();
        }
        return (int)$this->_LicenceType;
    }

    /**
     * Licence::setLicenceType
     *
     * @access public
     * @param object LicenceType $value
     * @return void
     */
    public function setLicenceType($value) {
        if (is_numeric($value)) {
            $this->_LicenceType = (int)$value;
        } else {
            $this->_LicenceType = $value;
        }
    }

    // }}}
    // Rating one to many relation + getter/setter {{{

    /**
     * Rating 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_RatingCollection = false;

    /**
     * Licence::getRatingCollection
     *
     * @access public
     * @return object Collection
     */
    public function getRatingCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Licence');
            return $mapper->getOneToMany($this->getId(),
                'Rating', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_RatingCollection) {
            $mapper = Mapper::singleton('Licence');
            $this->_RatingCollection = $mapper->getOneToMany($this->getId(),
                'Rating');
        }
        return $this->_RatingCollection;
    }

    /**
     * Licence::getRatingCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getRatingCollectionIds($filter = array()) {
        $col = $this->getRatingCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * Licence::setRatingCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setRatingCollection($value) {
        $this->_RatingCollection = $value;
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
        return 'Licence';
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
            'Number' => Object::TYPE_STRING,
            'BeginDate' => Object::TYPE_DATETIME,
            'EndDate' => Object::TYPE_DATETIME,
            'Duration' => Object::TYPE_INT,
            'DurationType' => Object::TYPE_INT,
            'AlertDateType' => Object::TYPE_INT,
            'DelayForAlert' => Object::TYPE_INT,
            'ToBeChecked' => Object::TYPE_CONST,
            'LicenceType' => 'LicenceType');
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
            'Rating'=>array(
                'linkClass'     => 'Rating',
                'field'         => 'Licence',
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