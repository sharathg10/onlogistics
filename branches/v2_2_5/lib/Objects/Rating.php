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

class Rating extends Object {
    
    // Constructeur {{{

    /**
     * Rating::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
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
     * Rating::getBeginDate
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
     * Rating::setBeginDate
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
     * Rating::getEndDate
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
     * Rating::setEndDate
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
     * Rating::getDuration
     *
     * @access public
     * @return integer
     */
    public function getDuration() {
        return $this->_Duration;
    }

    /**
     * Rating::setDuration
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
     * Rating::getDurationType
     *
     * @access public
     * @return integer
     */
    public function getDurationType() {
        return $this->_DurationType;
    }

    /**
     * Rating::setDurationType
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
    // Type foreignkey property + getter/setter {{{

    /**
     * Type foreignkey
     *
     * @access private
     * @var mixed object RatingType or integer
     */
    private $_Type = false;

    /**
     * Rating::getType
     *
     * @access public
     * @return object RatingType
     */
    public function getType() {
        if (is_int($this->_Type) && $this->_Type > 0) {
            $mapper = Mapper::singleton('RatingType');
            $this->_Type = $mapper->load(
                array('Id'=>$this->_Type));
        }
        return $this->_Type;
    }

    /**
     * Rating::getTypeId
     *
     * @access public
     * @return integer
     */
    public function getTypeId() {
        if ($this->_Type instanceof RatingType) {
            return $this->_Type->getId();
        }
        return (int)$this->_Type;
    }

    /**
     * Rating::setType
     *
     * @access public
     * @param object RatingType $value
     * @return void
     */
    public function setType($value) {
        if (is_numeric($value)) {
            $this->_Type = (int)$value;
        } else {
            $this->_Type = $value;
        }
    }

    // }}}
    // FlyType foreignkey property + getter/setter {{{

    /**
     * FlyType foreignkey
     *
     * @access private
     * @var mixed object FlyType or integer
     */
    private $_FlyType = false;

    /**
     * Rating::getFlyType
     *
     * @access public
     * @return object FlyType
     */
    public function getFlyType() {
        if (is_int($this->_FlyType) && $this->_FlyType > 0) {
            $mapper = Mapper::singleton('FlyType');
            $this->_FlyType = $mapper->load(
                array('Id'=>$this->_FlyType));
        }
        return $this->_FlyType;
    }

    /**
     * Rating::getFlyTypeId
     *
     * @access public
     * @return integer
     */
    public function getFlyTypeId() {
        if ($this->_FlyType instanceof FlyType) {
            return $this->_FlyType->getId();
        }
        return (int)$this->_FlyType;
    }

    /**
     * Rating::setFlyType
     *
     * @access public
     * @param object FlyType $value
     * @return void
     */
    public function setFlyType($value) {
        if (is_numeric($value)) {
            $this->_FlyType = (int)$value;
        } else {
            $this->_FlyType = $value;
        }
    }

    // }}}
    // Licence foreignkey property + getter/setter {{{

    /**
     * Licence foreignkey
     *
     * @access private
     * @var mixed object Licence or integer
     */
    private $_Licence = false;

    /**
     * Rating::getLicence
     *
     * @access public
     * @return object Licence
     */
    public function getLicence() {
        if (is_int($this->_Licence) && $this->_Licence > 0) {
            $mapper = Mapper::singleton('Licence');
            $this->_Licence = $mapper->load(
                array('Id'=>$this->_Licence));
        }
        return $this->_Licence;
    }

    /**
     * Rating::getLicenceId
     *
     * @access public
     * @return integer
     */
    public function getLicenceId() {
        if ($this->_Licence instanceof Licence) {
            return $this->_Licence->getId();
        }
        return (int)$this->_Licence;
    }

    /**
     * Rating::setLicence
     *
     * @access public
     * @param object Licence $value
     * @return void
     */
    public function setLicence($value) {
        if (is_numeric($value)) {
            $this->_Licence = (int)$value;
        } else {
            $this->_Licence = $value;
        }
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
        return 'Rating';
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
            'BeginDate' => Object::TYPE_DATETIME,
            'EndDate' => Object::TYPE_DATETIME,
            'Duration' => Object::TYPE_INT,
            'DurationType' => Object::TYPE_INT,
            'Type' => 'RatingType',
            'FlyType' => 'FlyType',
            'Licence' => 'Licence');
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
        $return = array();
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