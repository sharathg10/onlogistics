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

class AeroCustomer extends AeroActor {
    
    // Constructeur {{{

    /**
     * AeroCustomer::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Trainee string property + getter/setter {{{

    /**
     * Trainee int property
     *
     * @access private
     * @var integer
     */
    private $_Trainee = 0;

    /**
     * AeroCustomer::getTrainee
     *
     * @access public
     * @return integer
     */
    public function getTrainee() {
        return $this->_Trainee;
    }

    /**
     * AeroCustomer::setTrainee
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTrainee($value) {
        if ($value !== null) {
            $this->_Trainee = (int)$value;
        }
    }

    // }}}
    // SoloFly string property + getter/setter {{{

    /**
     * SoloFly int property
     *
     * @access private
     * @var integer
     */
    private $_SoloFly = 0;

    /**
     * AeroCustomer::getSoloFly
     *
     * @access public
     * @return integer
     */
    public function getSoloFly() {
        return $this->_SoloFly;
    }

    /**
     * AeroCustomer::setSoloFly
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setSoloFly($value) {
        if ($value !== null) {
            $this->_SoloFly = (int)$value;
        }
    }

    // }}}
    // LastFlyDate datetime property + getter/setter {{{

    /**
     * LastFlyDate int property
     *
     * @access private
     * @var string
     */
    private $_LastFlyDate = 0;

    /**
     * AeroCustomer::getLastFlyDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getLastFlyDate($format = false) {
        return $this->dateFormat($this->_LastFlyDate, $format);
    }

    /**
     * AeroCustomer::setLastFlyDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setLastFlyDate($value) {
        $this->_LastFlyDate = $value;
    }

    // }}}
    // Instructor foreignkey property + getter/setter {{{

    /**
     * Instructor foreignkey
     *
     * @access private
     * @var mixed object AeroInstructor or integer
     */
    private $_Instructor = false;

    /**
     * AeroCustomer::getInstructor
     *
     * @access public
     * @return object AeroInstructor
     */
    public function getInstructor() {
        if (is_int($this->_Instructor) && $this->_Instructor > 0) {
            $mapper = Mapper::singleton('AeroInstructor');
            $this->_Instructor = $mapper->load(
                array('Id'=>$this->_Instructor));
        }
        return $this->_Instructor;
    }

    /**
     * AeroCustomer::getInstructorId
     *
     * @access public
     * @return integer
     */
    public function getInstructorId() {
        if ($this->_Instructor instanceof AeroInstructor) {
            return $this->_Instructor->getId();
        }
        return (int)$this->_Instructor;
    }

    /**
     * AeroCustomer::setInstructor
     *
     * @access public
     * @param object AeroInstructor $value
     * @return void
     */
    public function setInstructor($value) {
        if (is_numeric($value)) {
            $this->_Instructor = (int)$value;
        } else {
            $this->_Instructor = $value;
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
            'Trainee' => Object::TYPE_BOOL,
            'SoloFly' => Object::TYPE_BOOL,
            'LastFlyDate' => Object::TYPE_DATETIME,
            'Instructor' => 'AeroInstructor');
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
        $return = array();
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
        return 'AeroActor';
    }

    // }}}
}

?>