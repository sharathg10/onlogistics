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

class _CourseCommand extends Command {
    
    // Constructeur {{{

    /**
     * _CourseCommand::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // WarrantyEndDate datetime property + getter/setter {{{

    /**
     * WarrantyEndDate int property
     *
     * @access private
     * @var string
     */
    private $_WarrantyEndDate = 0;

    /**
     * _CourseCommand::getWarrantyEndDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getWarrantyEndDate($format = false) {
        return $this->dateFormat($this->_WarrantyEndDate, $format);
    }

    /**
     * _CourseCommand::setWarrantyEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setWarrantyEndDate($value) {
        $this->_WarrantyEndDate = $value;
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
     * _CourseCommand::getSoloFly
     *
     * @access public
     * @return integer
     */
    public function getSoloFly() {
        return $this->_SoloFly;
    }

    /**
     * _CourseCommand::setSoloFly
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
    // IsWishedInstructor string property + getter/setter {{{

    /**
     * IsWishedInstructor int property
     *
     * @access private
     * @var integer
     */
    private $_IsWishedInstructor = 0;

    /**
     * _CourseCommand::getIsWishedInstructor
     *
     * @access public
     * @return integer
     */
    public function getIsWishedInstructor() {
        return $this->_IsWishedInstructor;
    }

    /**
     * _CourseCommand::setIsWishedInstructor
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setIsWishedInstructor($value) {
        if ($value !== null) {
            $this->_IsWishedInstructor = (int)$value;
        }
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
     * _CourseCommand::getInstructor
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
     * _CourseCommand::getInstructorId
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
     * _CourseCommand::setInstructor
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
    // AeroConcreteProduct foreignkey property + getter/setter {{{

    /**
     * AeroConcreteProduct foreignkey
     *
     * @access private
     * @var mixed object AeroConcreteProduct or integer
     */
    private $_AeroConcreteProduct = false;

    /**
     * _CourseCommand::getAeroConcreteProduct
     *
     * @access public
     * @return object AeroConcreteProduct
     */
    public function getAeroConcreteProduct() {
        if (is_int($this->_AeroConcreteProduct) && $this->_AeroConcreteProduct > 0) {
            $mapper = Mapper::singleton('AeroConcreteProduct');
            $this->_AeroConcreteProduct = $mapper->load(
                array('Id'=>$this->_AeroConcreteProduct));
        }
        return $this->_AeroConcreteProduct;
    }

    /**
     * _CourseCommand::getAeroConcreteProductId
     *
     * @access public
     * @return integer
     */
    public function getAeroConcreteProductId() {
        if ($this->_AeroConcreteProduct instanceof AeroConcreteProduct) {
            return $this->_AeroConcreteProduct->getId();
        }
        return (int)$this->_AeroConcreteProduct;
    }

    /**
     * _CourseCommand::setAeroConcreteProduct
     *
     * @access public
     * @param object AeroConcreteProduct $value
     * @return void
     */
    public function setAeroConcreteProduct($value) {
        if (is_numeric($value)) {
            $this->_AeroConcreteProduct = (int)$value;
        } else {
            $this->_AeroConcreteProduct = $value;
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
     * _CourseCommand::getFlyType
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
     * _CourseCommand::getFlyTypeId
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
     * _CourseCommand::setFlyType
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
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'Command';
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
            'WarrantyEndDate' => Object::TYPE_DATETIME,
            'SoloFly' => Object::TYPE_BOOL,
            'IsWishedInstructor' => Object::TYPE_BOOL,
            'Instructor' => 'AeroInstructor',
            'AeroConcreteProduct' => 'AeroConcreteProduct',
            'FlyType' => 'FlyType');
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
        return 'Command';
    }

    // }}}
}

?>