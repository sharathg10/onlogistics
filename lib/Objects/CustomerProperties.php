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

class CustomerProperties extends Object {
    // class constants {{{

    const PRIORITY_HIGH = 1;
    const PRIORITY_CURRENT = 2;
    const PRIORITY_AVERAGE = 3;
    const PRIORITY_LOW = 4;
    const PRIORITY_EXTRALOW = 5;

    // }}}
    // Constructeur {{{

    /**
     * CustomerProperties::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // NAFCode string property + getter/setter {{{

    /**
     * NAFCode string property
     *
     * @access private
     * @var string
     */
    private $_NAFCode = '';

    /**
     * CustomerProperties::getNAFCode
     *
     * @access public
     * @return string
     */
    public function getNAFCode() {
        return $this->_NAFCode;
    }

    /**
     * CustomerProperties::setNAFCode
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setNAFCode($value) {
        $this->_NAFCode = $value;
    }

    // }}}
    // PriorityLevel const property + getter/setter/getPriorityLevelConstArray {{{

    /**
     * PriorityLevel int property
     *
     * @access private
     * @var integer
     */
    private $_PriorityLevel = 1;

    /**
     * CustomerProperties::getPriorityLevel
     *
     * @access public
     * @return integer
     */
    public function getPriorityLevel() {
        return $this->_PriorityLevel;
    }

    /**
     * CustomerProperties::setPriorityLevel
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPriorityLevel($value) {
        if ($value !== null) {
            $this->_PriorityLevel = (int)$value;
        }
    }

    /**
     * CustomerProperties::getPriorityLevelConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getPriorityLevelConstArray($keys = false) {
        $array = array(
            CustomerProperties::PRIORITY_HIGH => _("Major emergency"), 
            CustomerProperties::PRIORITY_CURRENT => _("Common emergency"), 
            CustomerProperties::PRIORITY_AVERAGE => _("Medium emergency"), 
            CustomerProperties::PRIORITY_LOW => _("Low emergency"), 
            CustomerProperties::PRIORITY_EXTRALOW => _("Distant emergency")
        );
        asort($array);
        return $keys?array_keys($array):$array;
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
     * CustomerProperties::getPotential
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
     * CustomerProperties::getPotentialId
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
     * CustomerProperties::setPotential
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
    // Situation foreignkey property + getter/setter {{{

    /**
     * Situation foreignkey
     *
     * @access private
     * @var mixed object CustomerSituation or integer
     */
    private $_Situation = false;

    /**
     * CustomerProperties::getSituation
     *
     * @access public
     * @return object CustomerSituation
     */
    public function getSituation() {
        if (is_int($this->_Situation) && $this->_Situation > 0) {
            $mapper = Mapper::singleton('CustomerSituation');
            $this->_Situation = $mapper->load(
                array('Id'=>$this->_Situation));
        }
        return $this->_Situation;
    }

    /**
     * CustomerProperties::getSituationId
     *
     * @access public
     * @return integer
     */
    public function getSituationId() {
        if ($this->_Situation instanceof CustomerSituation) {
            return $this->_Situation->getId();
        }
        return (int)$this->_Situation;
    }

    /**
     * CustomerProperties::setSituation
     *
     * @access public
     * @param object CustomerSituation $value
     * @return void
     */
    public function setSituation($value) {
        if (is_numeric($value)) {
            $this->_Situation = (int)$value;
        } else {
            $this->_Situation = $value;
        }
    }

    // }}}
    // PersonalFrequency foreignkey property + getter/setter {{{

    /**
     * PersonalFrequency foreignkey
     *
     * @access private
     * @var mixed object CustomerFrequency or integer
     */
    private $_PersonalFrequency = false;

    /**
     * CustomerProperties::getPersonalFrequency
     *
     * @access public
     * @return object CustomerFrequency
     */
    public function getPersonalFrequency() {
        if (is_int($this->_PersonalFrequency) && $this->_PersonalFrequency > 0) {
            $mapper = Mapper::singleton('CustomerFrequency');
            $this->_PersonalFrequency = $mapper->load(
                array('Id'=>$this->_PersonalFrequency));
        }
        return $this->_PersonalFrequency;
    }

    /**
     * CustomerProperties::getPersonalFrequencyId
     *
     * @access public
     * @return integer
     */
    public function getPersonalFrequencyId() {
        if ($this->_PersonalFrequency instanceof CustomerFrequency) {
            return $this->_PersonalFrequency->getId();
        }
        return (int)$this->_PersonalFrequency;
    }

    /**
     * CustomerProperties::setPersonalFrequency
     *
     * @access public
     * @param object CustomerFrequency $value
     * @return void
     */
    public function setPersonalFrequency($value) {
        if (is_numeric($value)) {
            $this->_PersonalFrequency = (int)$value;
        } else {
            $this->_PersonalFrequency = $value;
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
        return 'CustomerProperties';
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
            'NAFCode' => Object::TYPE_STRING,
            'PriorityLevel' => Object::TYPE_CONST,
            'Potential' => 'CustomerPotential',
            'Situation' => 'CustomerSituation',
            'PersonalFrequency' => 'CustomerFrequency');
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
            'Actor'=>array(
                'linkClass'     => 'Actor',
                'field'         => 'CustomerProperties',
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