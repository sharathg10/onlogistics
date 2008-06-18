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

class CustomerFrequency extends Object {
    // class constants {{{

    const TYPE_FREQUENCY_LINEAR = 1;
    const TYPE_FREQUENCY_SEASONAL = 2;
    const TYPE_FREQUENCY_MANUAL = 3;

    // }}}
    // Constructeur {{{

    /**
     * CustomerFrequency::__construct()
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
     * CustomerFrequency::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * CustomerFrequency::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Frequency int property + getter/setter {{{

    /**
     * Frequency int property
     *
     * @access private
     * @var integer
     */
    private $_Frequency = null;

    /**
     * CustomerFrequency::getFrequency
     *
     * @access public
     * @return integer
     */
    public function getFrequency() {
        return $this->_Frequency;
    }

    /**
     * CustomerFrequency::setFrequency
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setFrequency($value) {
        $this->_Frequency = ($value===null || $value === '')?null:(int)$value;
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
     * CustomerFrequency::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * CustomerFrequency::setType
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
     * CustomerFrequency::getTypeConstArray
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
            CustomerFrequency::TYPE_FREQUENCY_LINEAR => _("Linear"), 
            CustomerFrequency::TYPE_FREQUENCY_SEASONAL => _("seasonal"), 
            CustomerFrequency::TYPE_FREQUENCY_MANUAL => _("Manual")
        );
        asort($array);
        return $keys?array_keys($array):$array;
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
     * CustomerFrequency::getBeginDate
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
     * CustomerFrequency::setBeginDate
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
     * CustomerFrequency::getEndDate
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
     * CustomerFrequency::setEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndDate($value) {
        $this->_EndDate = $value;
    }

    // }}}
    // Attractivity foreignkey property + getter/setter {{{

    /**
     * Attractivity foreignkey
     *
     * @access private
     * @var mixed object CustomerAttractivity or integer
     */
    private $_Attractivity = false;

    /**
     * CustomerFrequency::getAttractivity
     *
     * @access public
     * @return object CustomerAttractivity
     */
    public function getAttractivity() {
        if (is_int($this->_Attractivity) && $this->_Attractivity > 0) {
            $mapper = Mapper::singleton('CustomerAttractivity');
            $this->_Attractivity = $mapper->load(
                array('Id'=>$this->_Attractivity));
        }
        return $this->_Attractivity;
    }

    /**
     * CustomerFrequency::getAttractivityId
     *
     * @access public
     * @return integer
     */
    public function getAttractivityId() {
        if ($this->_Attractivity instanceof CustomerAttractivity) {
            return $this->_Attractivity->getId();
        }
        return (int)$this->_Attractivity;
    }

    /**
     * CustomerFrequency::setAttractivity
     *
     * @access public
     * @param object CustomerAttractivity $value
     * @return void
     */
    public function setAttractivity($value) {
        if (is_numeric($value)) {
            $this->_Attractivity = (int)$value;
        } else {
            $this->_Attractivity = $value;
        }
    }

    // }}}
    // Potential foreignkey property + getter/setter {{{

    /**
     * Potential foreignkey
     *
     * @access private
     * @var mixed object CustomerPotential or integer
     */
    private $_Potential = false;

    /**
     * CustomerFrequency::getPotential
     *
     * @access public
     * @return object CustomerPotential
     */
    public function getPotential() {
        if (is_int($this->_Potential) && $this->_Potential > 0) {
            $mapper = Mapper::singleton('CustomerPotential');
            $this->_Potential = $mapper->load(
                array('Id'=>$this->_Potential));
        }
        return $this->_Potential;
    }

    /**
     * CustomerFrequency::getPotentialId
     *
     * @access public
     * @return integer
     */
    public function getPotentialId() {
        if ($this->_Potential instanceof CustomerPotential) {
            return $this->_Potential->getId();
        }
        return (int)$this->_Potential;
    }

    /**
     * CustomerFrequency::setPotential
     *
     * @access public
     * @param object CustomerPotential $value
     * @return void
     */
    public function setPotential($value) {
        if (is_numeric($value)) {
            $this->_Potential = (int)$value;
        } else {
            $this->_Potential = $value;
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
        return 'CustomerFrequency';
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
            'Frequency' => Object::TYPE_INT,
            'Type' => Object::TYPE_CONST,
            'BeginDate' => Object::TYPE_DATE,
            'EndDate' => Object::TYPE_DATE,
            'Attractivity' => 'CustomerAttractivity',
            'Potential' => 'CustomerPotential');
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
                'field'         => 'PersonalFrequency',
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