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

class RessourceRessourceGroup extends Object {
    
    // Constructeur {{{

    /**
     * RessourceRessourceGroup::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Ressource foreignkey property + getter/setter {{{

    /**
     * Ressource foreignkey
     *
     * @access private
     * @var mixed object Ressource or integer
     */
    private $_Ressource = false;

    /**
     * RessourceRessourceGroup::getRessource
     *
     * @access public
     * @return object Ressource
     */
    public function getRessource() {
        if (is_int($this->_Ressource) && $this->_Ressource > 0) {
            $mapper = Mapper::singleton('Ressource');
            $this->_Ressource = $mapper->load(
                array('Id'=>$this->_Ressource));
        }
        return $this->_Ressource;
    }

    /**
     * RessourceRessourceGroup::getRessourceId
     *
     * @access public
     * @return integer
     */
    public function getRessourceId() {
        if ($this->_Ressource instanceof Ressource) {
            return $this->_Ressource->getId();
        }
        return (int)$this->_Ressource;
    }

    /**
     * RessourceRessourceGroup::setRessource
     *
     * @access public
     * @param object Ressource $value
     * @return void
     */
    public function setRessource($value) {
        if (is_numeric($value)) {
            $this->_Ressource = (int)$value;
        } else {
            $this->_Ressource = $value;
        }
    }

    // }}}
    // RessourceGroup foreignkey property + getter/setter {{{

    /**
     * RessourceGroup foreignkey
     *
     * @access private
     * @var mixed object RessourceGroup or integer
     */
    private $_RessourceGroup = false;

    /**
     * RessourceRessourceGroup::getRessourceGroup
     *
     * @access public
     * @return object RessourceGroup
     */
    public function getRessourceGroup() {
        if (is_int($this->_RessourceGroup) && $this->_RessourceGroup > 0) {
            $mapper = Mapper::singleton('RessourceGroup');
            $this->_RessourceGroup = $mapper->load(
                array('Id'=>$this->_RessourceGroup));
        }
        return $this->_RessourceGroup;
    }

    /**
     * RessourceRessourceGroup::getRessourceGroupId
     *
     * @access public
     * @return integer
     */
    public function getRessourceGroupId() {
        if ($this->_RessourceGroup instanceof RessourceGroup) {
            return $this->_RessourceGroup->getId();
        }
        return (int)$this->_RessourceGroup;
    }

    /**
     * RessourceRessourceGroup::setRessourceGroup
     *
     * @access public
     * @param object RessourceGroup $value
     * @return void
     */
    public function setRessourceGroup($value) {
        if (is_numeric($value)) {
            $this->_RessourceGroup = (int)$value;
        } else {
            $this->_RessourceGroup = $value;
        }
    }

    // }}}
    // Rate float property + getter/setter {{{

    /**
     * Rate float property
     *
     * @access private
     * @var float
     */
    private $_Rate = null;

    /**
     * RessourceRessourceGroup::getRate
     *
     * @access public
     * @return float
     */
    public function getRate() {
        return $this->_Rate;
    }

    /**
     * RessourceRessourceGroup::setRate
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRate($value) {
        $this->_Rate = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
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
        return 'RessourceRessourceGroup';
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
        return _('Resources');
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
            'Ressource' => 'Ressource',
            'RessourceGroup' => 'RessourceGroup',
            'Rate' => Object::TYPE_DECIMAL);
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
        $return = array(
            'Rate'=>array(
                'label'        => _('Rate'),
                'shortlabel'   => _('Rate'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 2
            ));
        return $return;
    }

    // }}}
}

?>