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

class _ActorSiteTransition extends Object {
    
    // Constructeur {{{

    /**
     * _ActorSiteTransition::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // DepartureZone foreignkey property + getter/setter {{{

    /**
     * DepartureZone foreignkey
     *
     * @access private
     * @var mixed object Zone or integer
     */
    private $_DepartureZone = false;

    /**
     * _ActorSiteTransition::getDepartureZone
     *
     * @access public
     * @return object Zone
     */
    public function getDepartureZone() {
        if (is_int($this->_DepartureZone) && $this->_DepartureZone > 0) {
            $mapper = Mapper::singleton('Zone');
            $this->_DepartureZone = $mapper->load(
                array('Id'=>$this->_DepartureZone));
        }
        return $this->_DepartureZone;
    }

    /**
     * _ActorSiteTransition::getDepartureZoneId
     *
     * @access public
     * @return integer
     */
    public function getDepartureZoneId() {
        if ($this->_DepartureZone instanceof Zone) {
            return $this->_DepartureZone->getId();
        }
        return (int)$this->_DepartureZone;
    }

    /**
     * _ActorSiteTransition::setDepartureZone
     *
     * @access public
     * @param object Zone $value
     * @return void
     */
    public function setDepartureZone($value) {
        if (is_numeric($value)) {
            $this->_DepartureZone = (int)$value;
        } else {
            $this->_DepartureZone = $value;
        }
    }

    // }}}
    // DepartureActor foreignkey property + getter/setter {{{

    /**
     * DepartureActor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_DepartureActor = false;

    /**
     * _ActorSiteTransition::getDepartureActor
     *
     * @access public
     * @return object Actor
     */
    public function getDepartureActor() {
        if (is_int($this->_DepartureActor) && $this->_DepartureActor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_DepartureActor = $mapper->load(
                array('Id'=>$this->_DepartureActor));
        }
        return $this->_DepartureActor;
    }

    /**
     * _ActorSiteTransition::getDepartureActorId
     *
     * @access public
     * @return integer
     */
    public function getDepartureActorId() {
        if ($this->_DepartureActor instanceof Actor) {
            return $this->_DepartureActor->getId();
        }
        return (int)$this->_DepartureActor;
    }

    /**
     * _ActorSiteTransition::setDepartureActor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setDepartureActor($value) {
        if (is_numeric($value)) {
            $this->_DepartureActor = (int)$value;
        } else {
            $this->_DepartureActor = $value;
        }
    }

    // }}}
    // DepartureSite foreignkey property + getter/setter {{{

    /**
     * DepartureSite foreignkey
     *
     * @access private
     * @var mixed object Site or integer
     */
    private $_DepartureSite = false;

    /**
     * _ActorSiteTransition::getDepartureSite
     *
     * @access public
     * @return object Site
     */
    public function getDepartureSite() {
        if (is_int($this->_DepartureSite) && $this->_DepartureSite > 0) {
            $mapper = Mapper::singleton('Site');
            $this->_DepartureSite = $mapper->load(
                array('Id'=>$this->_DepartureSite));
        }
        return $this->_DepartureSite;
    }

    /**
     * _ActorSiteTransition::getDepartureSiteId
     *
     * @access public
     * @return integer
     */
    public function getDepartureSiteId() {
        if ($this->_DepartureSite instanceof Site) {
            return $this->_DepartureSite->getId();
        }
        return (int)$this->_DepartureSite;
    }

    /**
     * _ActorSiteTransition::setDepartureSite
     *
     * @access public
     * @param object Site $value
     * @return void
     */
    public function setDepartureSite($value) {
        if (is_numeric($value)) {
            $this->_DepartureSite = (int)$value;
        } else {
            $this->_DepartureSite = $value;
        }
    }

    // }}}
    // ArrivalZone foreignkey property + getter/setter {{{

    /**
     * ArrivalZone foreignkey
     *
     * @access private
     * @var mixed object Zone or integer
     */
    private $_ArrivalZone = false;

    /**
     * _ActorSiteTransition::getArrivalZone
     *
     * @access public
     * @return object Zone
     */
    public function getArrivalZone() {
        if (is_int($this->_ArrivalZone) && $this->_ArrivalZone > 0) {
            $mapper = Mapper::singleton('Zone');
            $this->_ArrivalZone = $mapper->load(
                array('Id'=>$this->_ArrivalZone));
        }
        return $this->_ArrivalZone;
    }

    /**
     * _ActorSiteTransition::getArrivalZoneId
     *
     * @access public
     * @return integer
     */
    public function getArrivalZoneId() {
        if ($this->_ArrivalZone instanceof Zone) {
            return $this->_ArrivalZone->getId();
        }
        return (int)$this->_ArrivalZone;
    }

    /**
     * _ActorSiteTransition::setArrivalZone
     *
     * @access public
     * @param object Zone $value
     * @return void
     */
    public function setArrivalZone($value) {
        if (is_numeric($value)) {
            $this->_ArrivalZone = (int)$value;
        } else {
            $this->_ArrivalZone = $value;
        }
    }

    // }}}
    // ArrivalActor foreignkey property + getter/setter {{{

    /**
     * ArrivalActor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_ArrivalActor = false;

    /**
     * _ActorSiteTransition::getArrivalActor
     *
     * @access public
     * @return object Actor
     */
    public function getArrivalActor() {
        if (is_int($this->_ArrivalActor) && $this->_ArrivalActor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_ArrivalActor = $mapper->load(
                array('Id'=>$this->_ArrivalActor));
        }
        return $this->_ArrivalActor;
    }

    /**
     * _ActorSiteTransition::getArrivalActorId
     *
     * @access public
     * @return integer
     */
    public function getArrivalActorId() {
        if ($this->_ArrivalActor instanceof Actor) {
            return $this->_ArrivalActor->getId();
        }
        return (int)$this->_ArrivalActor;
    }

    /**
     * _ActorSiteTransition::setArrivalActor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setArrivalActor($value) {
        if (is_numeric($value)) {
            $this->_ArrivalActor = (int)$value;
        } else {
            $this->_ArrivalActor = $value;
        }
    }

    // }}}
    // ArrivalSite foreignkey property + getter/setter {{{

    /**
     * ArrivalSite foreignkey
     *
     * @access private
     * @var mixed object Site or integer
     */
    private $_ArrivalSite = false;

    /**
     * _ActorSiteTransition::getArrivalSite
     *
     * @access public
     * @return object Site
     */
    public function getArrivalSite() {
        if (is_int($this->_ArrivalSite) && $this->_ArrivalSite > 0) {
            $mapper = Mapper::singleton('Site');
            $this->_ArrivalSite = $mapper->load(
                array('Id'=>$this->_ArrivalSite));
        }
        return $this->_ArrivalSite;
    }

    /**
     * _ActorSiteTransition::getArrivalSiteId
     *
     * @access public
     * @return integer
     */
    public function getArrivalSiteId() {
        if ($this->_ArrivalSite instanceof Site) {
            return $this->_ArrivalSite->getId();
        }
        return (int)$this->_ArrivalSite;
    }

    /**
     * _ActorSiteTransition::setArrivalSite
     *
     * @access public
     * @param object Site $value
     * @return void
     */
    public function setArrivalSite($value) {
        if (is_numeric($value)) {
            $this->_ArrivalSite = (int)$value;
        } else {
            $this->_ArrivalSite = $value;
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
        return 'ActorSiteTransition';
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
            'DepartureZone' => 'Zone',
            'DepartureActor' => 'Actor',
            'DepartureSite' => 'Site',
            'ArrivalZone' => 'Zone',
            'ArrivalActor' => 'Actor',
            'ArrivalSite' => 'Site');
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
            'ActivatedChain'=>array(
                'linkClass'     => 'ActivatedChain',
                'field'         => 'SiteTransition',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'ActorSiteTransition',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Chain'=>array(
                'linkClass'     => 'Chain',
                'field'         => 'SiteTransition',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainTask'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'ActorSiteTransition',
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