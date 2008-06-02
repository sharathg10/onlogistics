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

class ActorDetail extends Object {
    
    // Constructeur {{{

    /**
     * ActorDetail::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // IsInternalAffectation string property + getter/setter {{{

    /**
     * IsInternalAffectation int property
     *
     * @access private
     * @var integer
     */
    private $_IsInternalAffectation = 1;

    /**
     * ActorDetail::getIsInternalAffectation
     *
     * @access public
     * @return integer
     */
    public function getIsInternalAffectation() {
        return $this->_IsInternalAffectation;
    }

    /**
     * ActorDetail::setIsInternalAffectation
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setIsInternalAffectation($value) {
        if ($value !== null) {
            $this->_IsInternalAffectation = (int)$value;
        }
    }

    // }}}
    // InternalAffectation foreignkey property + getter/setter {{{

    /**
     * InternalAffectation foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_InternalAffectation = false;

    /**
     * ActorDetail::getInternalAffectation
     *
     * @access public
     * @return object Actor
     */
    public function getInternalAffectation() {
        if (is_int($this->_InternalAffectation) && $this->_InternalAffectation > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_InternalAffectation = $mapper->load(
                array('Id'=>$this->_InternalAffectation));
        }
        return $this->_InternalAffectation;
    }

    /**
     * ActorDetail::getInternalAffectationId
     *
     * @access public
     * @return integer
     */
    public function getInternalAffectationId() {
        if ($this->_InternalAffectation instanceof Actor) {
            return $this->_InternalAffectation->getId();
        }
        return (int)$this->_InternalAffectation;
    }

    /**
     * ActorDetail::setInternalAffectation
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setInternalAffectation($value) {
        if (is_numeric($value)) {
            $this->_InternalAffectation = (int)$value;
        } else {
            $this->_InternalAffectation = $value;
        }
    }

    // }}}
    // Signatory foreignkey property + getter/setter {{{

    /**
     * Signatory foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Signatory = false;

    /**
     * ActorDetail::getSignatory
     *
     * @access public
     * @return object Actor
     */
    public function getSignatory() {
        if (is_int($this->_Signatory) && $this->_Signatory > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Signatory = $mapper->load(
                array('Id'=>$this->_Signatory));
        }
        return $this->_Signatory;
    }

    /**
     * ActorDetail::getSignatoryId
     *
     * @access public
     * @return integer
     */
    public function getSignatoryId() {
        if ($this->_Signatory instanceof Actor) {
            return $this->_Signatory->getId();
        }
        return (int)$this->_Signatory;
    }

    /**
     * ActorDetail::setSignatory
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setSignatory($value) {
        if (is_numeric($value)) {
            $this->_Signatory = (int)$value;
        } else {
            $this->_Signatory = $value;
        }
    }

    // }}}
    // BusinessProvider foreignkey property + getter/setter {{{

    /**
     * BusinessProvider foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_BusinessProvider = false;

    /**
     * ActorDetail::getBusinessProvider
     *
     * @access public
     * @return object Actor
     */
    public function getBusinessProvider() {
        if (is_int($this->_BusinessProvider) && $this->_BusinessProvider > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_BusinessProvider = $mapper->load(
                array('Id'=>$this->_BusinessProvider));
        }
        return $this->_BusinessProvider;
    }

    /**
     * ActorDetail::getBusinessProviderId
     *
     * @access public
     * @return integer
     */
    public function getBusinessProviderId() {
        if ($this->_BusinessProvider instanceof Actor) {
            return $this->_BusinessProvider->getId();
        }
        return (int)$this->_BusinessProvider;
    }

    /**
     * ActorDetail::setBusinessProvider
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setBusinessProvider($value) {
        if (is_numeric($value)) {
            $this->_BusinessProvider = (int)$value;
        } else {
            $this->_BusinessProvider = $value;
        }
    }

    // }}}
    // Actor one to one relation getter {{{
    /**
     * ActorDetail::getActor
     *
     * @access public
     * @return object Actor
     */
    public function getActor() {
        $mapper = Mapper::singleton('Actor');
        return $mapper->load(array('ActorDetail'=>$this->getId()));
    }

    /**
     * ActorDetail::getActorId
     *
     * @access public
     * @return integer
     */
    public function getActorId() {
        $return = $this->getActor();
        if ($return instanceof Actor) {
            return $return->getId();
        }
        return 0;
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
        return 'ActorDetail';
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
            'IsInternalAffectation' => Object::TYPE_BOOL,
            'InternalAffectation' => 'Actor',
            'Signatory' => 'Actor',
            'BusinessProvider' => 'Actor');
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