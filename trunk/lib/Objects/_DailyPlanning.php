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

class _DailyPlanning extends Object {
    
    // Constructeur {{{

    /**
     * _DailyPlanning::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Start datetime property + getter/setter {{{

    /**
     * Start int property
     *
     * @access private
     * @var string
     */
    private $_Start = 0;

    /**
     * _DailyPlanning::getStart
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getStart($format = false) {
        return $this->dateFormat($this->_Start, $format);
    }

    /**
     * _DailyPlanning::setStart
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStart($value) {
        $this->_Start = $value;
    }

    // }}}
    // Pause datetime property + getter/setter {{{

    /**
     * Pause int property
     *
     * @access private
     * @var string
     */
    private $_Pause = 0;

    /**
     * _DailyPlanning::getPause
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getPause($format = false) {
        return $this->dateFormat($this->_Pause, $format);
    }

    /**
     * _DailyPlanning::setPause
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPause($value) {
        $this->_Pause = $value;
    }

    // }}}
    // Restart datetime property + getter/setter {{{

    /**
     * Restart int property
     *
     * @access private
     * @var string
     */
    private $_Restart = 0;

    /**
     * _DailyPlanning::getRestart
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getRestart($format = false) {
        return $this->dateFormat($this->_Restart, $format);
    }

    /**
     * _DailyPlanning::setRestart
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setRestart($value) {
        $this->_Restart = $value;
    }

    // }}}
    // End datetime property + getter/setter {{{

    /**
     * End int property
     *
     * @access private
     * @var string
     */
    private $_End = 0;

    /**
     * _DailyPlanning::getEnd
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEnd($format = false) {
        return $this->dateFormat($this->_End, $format);
    }

    /**
     * _DailyPlanning::setEnd
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEnd($value) {
        $this->_End = $value;
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
        return 'DailyPlanning';
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
            'Start' => Object::TYPE_TIME,
            'Pause' => Object::TYPE_TIME,
            'Restart' => Object::TYPE_TIME,
            'End' => Object::TYPE_TIME);
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
            'WeeklyPlanning'=>array(
                'linkClass'     => 'WeeklyPlanning',
                'field'         => 'Monday',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'WeeklyPlanning_1'=>array(
                'linkClass'     => 'WeeklyPlanning',
                'field'         => 'Tuesday',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'WeeklyPlanning_2'=>array(
                'linkClass'     => 'WeeklyPlanning',
                'field'         => 'Wednesday',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'WeeklyPlanning_3'=>array(
                'linkClass'     => 'WeeklyPlanning',
                'field'         => 'Thursday',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'WeeklyPlanning_4'=>array(
                'linkClass'     => 'WeeklyPlanning',
                'field'         => 'Friday',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'WeeklyPlanning_5'=>array(
                'linkClass'     => 'WeeklyPlanning',
                'field'         => 'Saturday',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'WeeklyPlanning_6'=>array(
                'linkClass'     => 'WeeklyPlanning',
                'field'         => 'Sunday',
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