<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IMPORTANT: This is a generated file, please do not edit.
 *
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

/**
 * _RTWMaterial class
 *
 */
class _RTWMaterial extends Product {
    // class constants {{{

    const TYPE_RAW_MATERIAL = 0;
    const TYPE_ACCESSORY = 1;
    const TYPE_PACKAGING = 2;
    const TYPE_THREAD = 3;
    const TYPE_HEEL = 4;
    const TYPE_BAMBOO = 5;
    const TYPE_SOLE = 6;

    // }}}
    // Constructeur {{{

    /**
     * _RTWMaterial::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // ScientificName string property + getter/setter {{{

    /**
     * ScientificName string property
     *
     * @access private
     * @var string
     */
    private $_ScientificName = '';

    /**
     * _RTWMaterial::getScientificName
     *
     * @access public
     * @return string
     */
    public function getScientificName() {
        return $this->_ScientificName;
    }

    /**
     * _RTWMaterial::setScientificName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setScientificName($value) {
        $this->_ScientificName = $value;
    }

    // }}}
    // MaterialType const property + getter/setter/getMaterialTypeConstArray {{{

    /**
     * MaterialType int property
     *
     * @access private
     * @var integer
     */
    private $_MaterialType = 0;

    /**
     * _RTWMaterial::getMaterialType
     *
     * @access public
     * @return integer
     */
    public function getMaterialType() {
        return $this->_MaterialType;
    }

    /**
     * _RTWMaterial::setMaterialType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setMaterialType($value) {
        if ($value !== null) {
            $this->_MaterialType = (int)$value;
        }
    }

    /**
     * _RTWMaterial::getMaterialTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa repr�sentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retourn�es
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getMaterialTypeConstArray($keys = false) {
        $array = array(
            _RTWMaterial::TYPE_RAW_MATERIAL => _("Raw material"), 
            _RTWMaterial::TYPE_ACCESSORY => _("Accessory"), 
            _RTWMaterial::TYPE_PACKAGING => _("Packaging"), 
            _RTWMaterial::TYPE_THREAD => _("Thread"), 
            _RTWMaterial::TYPE_HEEL => _("Heel"), 
            _RTWMaterial::TYPE_BAMBOO => _("Bamboo"), 
            _RTWMaterial::TYPE_SOLE => _("Sole")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Color foreignkey property + getter/setter {{{

    /**
     * Color foreignkey
     *
     * @access private
     * @var mixed object RTWColor or integer
     */
    private $_Color = false;

    /**
     * _RTWMaterial::getColor
     *
     * @access public
     * @return object RTWColor
     */
    public function getColor() {
        if (is_int($this->_Color) && $this->_Color > 0) {
            $mapper = Mapper::singleton('RTWColor');
            $this->_Color = $mapper->load(
                array('Id'=>$this->_Color));
        }
        return $this->_Color;
    }

    /**
     * _RTWMaterial::getColorId
     *
     * @access public
     * @return integer
     */
    public function getColorId() {
        if ($this->_Color instanceof RTWColor) {
            return $this->_Color->getId();
        }
        return (int)$this->_Color;
    }

    /**
     * _RTWMaterial::setColor
     *
     * @access public
     * @param object RTWColor $value
     * @return void
     */
    public function setColor($value) {
        if (is_numeric($value)) {
            $this->_Color = (int)$value;
        } else {
            $this->_Color = $value;
        }
    }

    // }}}
    // Origin string property + getter/setter {{{

    /**
     * Origin string property
     *
     * @access private
     * @var string
     */
    private $_Origin = '';

    /**
     * _RTWMaterial::getOrigin
     *
     * @access public
     * @return string
     */
    public function getOrigin() {
        return $this->_Origin;
    }

    /**
     * _RTWMaterial::setOrigin
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setOrigin($value) {
        $this->_Origin = $value;
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
        return 'Product';
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
        return _('Materials');
    }

    // }}}
    // getProperties() {{{

    /**
     * Retourne le tableau des propri�t�s.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getProperties($ownOnly = false) {
        $return = array(
            'ScientificName' => Object::TYPE_STRING,
            'MaterialType' => Object::TYPE_CONST,
            'Color' => 'RTWColor',
            'Origin' => Object::TYPE_STRING);
        return $ownOnly?$return:array_merge(parent::getProperties(), $return);
    }

    // }}}
    // getLinks() {{{

    /**
     * Retourne le tableau des entit�s li�es.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getLinks($ownOnly = false) {
        $return = array(
            'RTWModel'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'HeelReference',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_1'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Sole',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_2'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Box',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_3'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'HandBag',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_4'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Material1',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_5'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Material2',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_6'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Material3',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_7'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Accessory1',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_8'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Accessory2',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_9'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Accessory3',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_10'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Lining',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_11'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Insole',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_12'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'UnderSole',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_13'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'MediaPlanta',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_14'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Lagrima',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_15'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'HeelCovering',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_16'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Selvedge',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_17'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Thread1',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_18'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Thread2',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_19'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Bamboo',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ));
        return $ownOnly?$return:array_merge(parent::getLinks(), $return);
    }

    // }}}
    // getUniqueProperties() {{{

    /**
     * Retourne le tableau des propri�t�s qui ne peuvent prendre la m�me valeur
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
     * Retourne le tableau des propri�t�s doivent �tre "vides" (0 ou '') pour
     * qu'une occurrence puisse �tre supprim�e en base de donn�es.
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
     * Retourne le tableau des "fonctionalit�s" pour l'objet en cours.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getFeatures() {
        return array('add', 'edit', 'del', 'grid', 'searchform');
    }

    // }}}
    // getMapping() {{{

    /**
     * Retourne le mapping n�cessaires aux composants g�n�riques.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getMapping($ownOnly = false) {
        $return = array(
            'ScientificName'=>array(
                'label'        => _('Scientific terminology'),
                'shortlabel'   => _('Scientific terminology'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'MaterialType'=>array(
                'label'        => _('MaterialType'),
                'shortlabel'   => _('MaterialType'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Color'=>array(
                'label'        => _('Color'),
                'shortlabel'   => _('Color'),
                'usedby'       => array('grid', 'addedit', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Origin'=>array(
                'label'        => _('origin'),
                'shortlabel'   => _('origin'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $ownOnly?$return:array_merge(parent::getMapping(), $return);
    }

    // }}}
    // useInheritance() {{{

    /**
     * D�termine si l'entit� est une entit� qui utilise l'h�ritage.
     * (classe parente ou classe fille). Ceci afin de differencier les entit�s
     * dans le mapper car classes filles et parentes sont mapp�es dans la m�me
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
     * Retourne le nom de la premi�re classe parente
     *
     * @static
     * @access public
     * @return string
     */
    public static function getParentClassName() {
        return 'Product';
    }

    // }}}
}

?>