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

class _AeroConcreteProduct extends ConcreteProduct {
    // class constants {{{

    const LITRE = 1;
    const GALLON = 2;
    const KILOGRAMME = 0;
    const PERCENT = 3;

    // }}}
    // Constructeur {{{

    /**
     * _AeroConcreteProduct::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // MaxWeightOnTakeOff float property + getter/setter {{{

    /**
     * MaxWeightOnTakeOff float property
     *
     * @access private
     * @var float
     */
    private $_MaxWeightOnTakeOff = 0;

    /**
     * _AeroConcreteProduct::getMaxWeightOnTakeOff
     *
     * @access public
     * @return float
     */
    public function getMaxWeightOnTakeOff() {
        return $this->_MaxWeightOnTakeOff;
    }

    /**
     * _AeroConcreteProduct::setMaxWeightOnTakeOff
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaxWeightOnTakeOff($value) {
        if ($value !== null) {
            $this->_MaxWeightOnTakeOff = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // MaxWeightBySeat float property + getter/setter {{{

    /**
     * MaxWeightBySeat float property
     *
     * @access private
     * @var float
     */
    private $_MaxWeightBySeat = 0;

    /**
     * _AeroConcreteProduct::getMaxWeightBySeat
     *
     * @access public
     * @return float
     */
    public function getMaxWeightBySeat() {
        return $this->_MaxWeightBySeat;
    }

    /**
     * _AeroConcreteProduct::setMaxWeightBySeat
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaxWeightBySeat($value) {
        if ($value !== null) {
            $this->_MaxWeightBySeat = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // RealLandingSinceNew float property + getter/setter {{{

    /**
     * RealLandingSinceNew float property
     *
     * @access private
     * @var float
     */
    private $_RealLandingSinceNew = 0;

    /**
     * _AeroConcreteProduct::getRealLandingSinceNew
     *
     * @access public
     * @return float
     */
    public function getRealLandingSinceNew() {
        return $this->_RealLandingSinceNew;
    }

    /**
     * _AeroConcreteProduct::setRealLandingSinceNew
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealLandingSinceNew($value) {
        if ($value !== null) {
            $this->_RealLandingSinceNew = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // RealLandingSinceOverall float property + getter/setter {{{

    /**
     * RealLandingSinceOverall float property
     *
     * @access private
     * @var float
     */
    private $_RealLandingSinceOverall = 0;

    /**
     * _AeroConcreteProduct::getRealLandingSinceOverall
     *
     * @access public
     * @return float
     */
    public function getRealLandingSinceOverall() {
        return $this->_RealLandingSinceOverall;
    }

    /**
     * _AeroConcreteProduct::setRealLandingSinceOverall
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealLandingSinceOverall($value) {
        if ($value !== null) {
            $this->_RealLandingSinceOverall = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // RealLandingSinceRepared float property + getter/setter {{{

    /**
     * RealLandingSinceRepared float property
     *
     * @access private
     * @var float
     */
    private $_RealLandingSinceRepared = 0;

    /**
     * _AeroConcreteProduct::getRealLandingSinceRepared
     *
     * @access public
     * @return float
     */
    public function getRealLandingSinceRepared() {
        return $this->_RealLandingSinceRepared;
    }

    /**
     * _AeroConcreteProduct::setRealLandingSinceRepared
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealLandingSinceRepared($value) {
        if ($value !== null) {
            $this->_RealLandingSinceRepared = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // RealCycleSinceNew float property + getter/setter {{{

    /**
     * RealCycleSinceNew float property
     *
     * @access private
     * @var float
     */
    private $_RealCycleSinceNew = 0;

    /**
     * _AeroConcreteProduct::getRealCycleSinceNew
     *
     * @access public
     * @return float
     */
    public function getRealCycleSinceNew() {
        return $this->_RealCycleSinceNew;
    }

    /**
     * _AeroConcreteProduct::setRealCycleSinceNew
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealCycleSinceNew($value) {
        if ($value !== null) {
            $this->_RealCycleSinceNew = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // RealCycleSinceOverall float property + getter/setter {{{

    /**
     * RealCycleSinceOverall float property
     *
     * @access private
     * @var float
     */
    private $_RealCycleSinceOverall = 0;

    /**
     * _AeroConcreteProduct::getRealCycleSinceOverall
     *
     * @access public
     * @return float
     */
    public function getRealCycleSinceOverall() {
        return $this->_RealCycleSinceOverall;
    }

    /**
     * _AeroConcreteProduct::setRealCycleSinceOverall
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealCycleSinceOverall($value) {
        if ($value !== null) {
            $this->_RealCycleSinceOverall = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // RealCycleSinceRepared float property + getter/setter {{{

    /**
     * RealCycleSinceRepared float property
     *
     * @access private
     * @var float
     */
    private $_RealCycleSinceRepared = 0;

    /**
     * _AeroConcreteProduct::getRealCycleSinceRepared
     *
     * @access public
     * @return float
     */
    public function getRealCycleSinceRepared() {
        return $this->_RealCycleSinceRepared;
    }

    /**
     * _AeroConcreteProduct::setRealCycleSinceRepared
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRealCycleSinceRepared($value) {
        if ($value !== null) {
            $this->_RealCycleSinceRepared = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // TankCapacity float property + getter/setter {{{

    /**
     * TankCapacity float property
     *
     * @access private
     * @var float
     */
    private $_TankCapacity = 0;

    /**
     * _AeroConcreteProduct::getTankCapacity
     *
     * @access public
     * @return float
     */
    public function getTankCapacity() {
        return $this->_TankCapacity;
    }

    /**
     * _AeroConcreteProduct::setTankCapacity
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTankCapacity($value) {
        if ($value !== null) {
            $this->_TankCapacity = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // TankUnitType const property + getter/setter/getTankUnitTypeConstArray {{{

    /**
     * TankUnitType int property
     *
     * @access private
     * @var integer
     */
    private $_TankUnitType = 0;

    /**
     * _AeroConcreteProduct::getTankUnitType
     *
     * @access public
     * @return integer
     */
    public function getTankUnitType() {
        return $this->_TankUnitType;
    }

    /**
     * _AeroConcreteProduct::setTankUnitType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTankUnitType($value) {
        if ($value !== null) {
            $this->_TankUnitType = (int)$value;
        }
    }

    /**
     * _AeroConcreteProduct::getTankUnitTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getTankUnitTypeConstArray($keys = false) {
        $array = array(
            _AeroConcreteProduct::LITRE => _("L"), 
            _AeroConcreteProduct::GALLON => _("Gallon"), 
            _AeroConcreteProduct::KILOGRAMME => _("Kg"), 
            _AeroConcreteProduct::PERCENT => _("%")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // CourseCommand one to many relation + getter/setter {{{

    /**
     * CourseCommand 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CourseCommandCollection = false;

    /**
     * _AeroConcreteProduct::getCourseCommandCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCourseCommandCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('AeroConcreteProduct');
            return $mapper->getOneToMany($this->getId(),
                'CourseCommand', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CourseCommandCollection) {
            $mapper = Mapper::singleton('AeroConcreteProduct');
            $this->_CourseCommandCollection = $mapper->getOneToMany($this->getId(),
                'CourseCommand');
        }
        return $this->_CourseCommandCollection;
    }

    /**
     * _AeroConcreteProduct::getCourseCommandCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCourseCommandCollectionIds($filter = array()) {
        $col = $this->getCourseCommandCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _AeroConcreteProduct::setCourseCommandCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCourseCommandCollection($value) {
        $this->_CourseCommandCollection = $value;
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
        return 'ConcreteProduct';
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
            'MaxWeightOnTakeOff' => Object::TYPE_DECIMAL,
            'MaxWeightBySeat' => Object::TYPE_DECIMAL,
            'RealLandingSinceNew' => Object::TYPE_DECIMAL,
            'RealLandingSinceOverall' => Object::TYPE_DECIMAL,
            'RealLandingSinceRepared' => Object::TYPE_DECIMAL,
            'RealCycleSinceNew' => Object::TYPE_DECIMAL,
            'RealCycleSinceOverall' => Object::TYPE_DECIMAL,
            'RealCycleSinceRepared' => Object::TYPE_DECIMAL,
            'TankCapacity' => Object::TYPE_DECIMAL,
            'TankUnitType' => Object::TYPE_CONST);
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
            'CourseCommand'=>array(
                'linkClass'     => 'CourseCommand',
                'field'         => 'AeroConcreteProduct',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
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
        return 'ConcreteProduct';
    }

    // }}}
}

?>