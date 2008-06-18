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

class Container extends Object {
    // class constants {{{

    const COVER_P_NONE = 0;
    const COVER_P_SIMPLE = 1;
    const COVER_P_DOUBLE = 2;
    const COVER_K_NONE = 0;
    const COVER_K_WOOD = 1;
    const COVER_K_PAPERBOARD = 2;
    const COVER_K_METAL = 3;
    const COVER_G_NONE = 0;
    const COVER_G_I = 1;
    const COVER_G_II = 2;
    const COVER_G_III = 3;

    // }}}
    // Constructeur {{{

    /**
     * Container::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Reference string property + getter/setter {{{

    /**
     * Reference string property
     *
     * @access private
     * @var string
     */
    private $_Reference = '';

    /**
     * Container::getReference
     *
     * @access public
     * @return string
     */
    public function getReference() {
        return $this->_Reference;
    }

    /**
     * Container::setReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setReference($value) {
        $this->_Reference = $value;
    }

    // }}}
    // SupplierReference string property + getter/setter {{{

    /**
     * SupplierReference string property
     *
     * @access private
     * @var string
     */
    private $_SupplierReference = '';

    /**
     * Container::getSupplierReference
     *
     * @access public
     * @return string
     */
    public function getSupplierReference() {
        return $this->_SupplierReference;
    }

    /**
     * Container::setSupplierReference
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setSupplierReference($value) {
        $this->_SupplierReference = $value;
    }

    // }}}
    // CoverType foreignkey property + getter/setter {{{

    /**
     * CoverType foreignkey
     *
     * @access private
     * @var mixed object CoverType or integer
     */
    private $_CoverType = false;

    /**
     * Container::getCoverType
     *
     * @access public
     * @return object CoverType
     */
    public function getCoverType() {
        if (is_int($this->_CoverType) && $this->_CoverType > 0) {
            $mapper = Mapper::singleton('CoverType');
            $this->_CoverType = $mapper->load(
                array('Id'=>$this->_CoverType));
        }
        return $this->_CoverType;
    }

    /**
     * Container::getCoverTypeId
     *
     * @access public
     * @return integer
     */
    public function getCoverTypeId() {
        if ($this->_CoverType instanceof CoverType) {
            return $this->_CoverType->getId();
        }
        return (int)$this->_CoverType;
    }

    /**
     * Container::setCoverType
     *
     * @access public
     * @param object CoverType $value
     * @return void
     */
    public function setCoverType($value) {
        if (is_numeric($value)) {
            $this->_CoverType = (int)$value;
        } else {
            $this->_CoverType = $value;
        }
    }

    // }}}
    // CoverProperty const property + getter/setter/getCoverPropertyConstArray {{{

    /**
     * CoverProperty int property
     *
     * @access private
     * @var integer
     */
    private $_CoverProperty = 0;

    /**
     * Container::getCoverProperty
     *
     * @access public
     * @return integer
     */
    public function getCoverProperty() {
        return $this->_CoverProperty;
    }

    /**
     * Container::setCoverProperty
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoverProperty($value) {
        if ($value !== null) {
            $this->_CoverProperty = (int)$value;
        }
    }

    /**
     * Container::getCoverPropertyConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getCoverPropertyConstArray($keys = false) {
        $array = array(
            Container::COVER_P_NONE => _("N/A"), 
            Container::COVER_P_SIMPLE => _("SIMPLE COVER"), 
            Container::COVER_P_DOUBLE => _("DOUBLE FOLD")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // CoverKind const property + getter/setter/getCoverKindConstArray {{{

    /**
     * CoverKind int property
     *
     * @access private
     * @var integer
     */
    private $_CoverKind = 0;

    /**
     * Container::getCoverKind
     *
     * @access public
     * @return integer
     */
    public function getCoverKind() {
        return $this->_CoverKind;
    }

    /**
     * Container::setCoverKind
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoverKind($value) {
        if ($value !== null) {
            $this->_CoverKind = (int)$value;
        }
    }

    /**
     * Container::getCoverKindConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getCoverKindConstArray($keys = false) {
        $array = array(
            Container::COVER_K_NONE => _("N/A"), 
            Container::COVER_K_WOOD => _("WOOD"), 
            Container::COVER_K_PAPERBOARD => _("CARDBOARD"), 
            Container::COVER_K_METAL => _("METALLIC")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // CoverGroup const property + getter/setter/getCoverGroupConstArray {{{

    /**
     * CoverGroup int property
     *
     * @access private
     * @var integer
     */
    private $_CoverGroup = 0;

    /**
     * Container::getCoverGroup
     *
     * @access public
     * @return integer
     */
    public function getCoverGroup() {
        return $this->_CoverGroup;
    }

    /**
     * Container::setCoverGroup
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCoverGroup($value) {
        if ($value !== null) {
            $this->_CoverGroup = (int)$value;
        }
    }

    /**
     * Container::getCoverGroupConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getCoverGroupConstArray($keys = false) {
        $array = array(
            Container::COVER_G_NONE => _("N/A"), 
            Container::COVER_G_I => _("Group I"), 
            Container::COVER_G_II => _("GROUP II"), 
            Container::COVER_G_III => _("GROUP III")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // MaxAuthorizedWeight float property + getter/setter {{{

    /**
     * MaxAuthorizedWeight float property
     *
     * @access private
     * @var float
     */
    private $_MaxAuthorizedWeight = 0;

    /**
     * Container::getMaxAuthorizedWeight
     *
     * @access public
     * @return float
     */
    public function getMaxAuthorizedWeight() {
        return $this->_MaxAuthorizedWeight;
    }

    /**
     * Container::setMaxAuthorizedWeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setMaxAuthorizedWeight($value) {
        if ($value !== null) {
            $this->_MaxAuthorizedWeight = I18N::extractNumber($value);
        }
    }

    // }}}
    // Weight float property + getter/setter {{{

    /**
     * Weight float property
     *
     * @access private
     * @var float
     */
    private $_Weight = 0;

    /**
     * Container::getWeight
     *
     * @access public
     * @return float
     */
    public function getWeight() {
        return $this->_Weight;
    }

    /**
     * Container::setWeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setWeight($value) {
        if ($value !== null) {
            $this->_Weight = I18N::extractNumber($value);
        }
    }

    // }}}
    // ExternalLength float property + getter/setter {{{

    /**
     * ExternalLength float property
     *
     * @access private
     * @var float
     */
    private $_ExternalLength = 0;

    /**
     * Container::getExternalLength
     *
     * @access public
     * @return float
     */
    public function getExternalLength() {
        return $this->_ExternalLength;
    }

    /**
     * Container::setExternalLength
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setExternalLength($value) {
        if ($value !== null) {
            $this->_ExternalLength = I18N::extractNumber($value);
        }
    }

    // }}}
    // ExternalWidth float property + getter/setter {{{

    /**
     * ExternalWidth float property
     *
     * @access private
     * @var float
     */
    private $_ExternalWidth = 0;

    /**
     * Container::getExternalWidth
     *
     * @access public
     * @return float
     */
    public function getExternalWidth() {
        return $this->_ExternalWidth;
    }

    /**
     * Container::setExternalWidth
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setExternalWidth($value) {
        if ($value !== null) {
            $this->_ExternalWidth = I18N::extractNumber($value);
        }
    }

    // }}}
    // ExternalHeight float property + getter/setter {{{

    /**
     * ExternalHeight float property
     *
     * @access private
     * @var float
     */
    private $_ExternalHeight = 0;

    /**
     * Container::getExternalHeight
     *
     * @access public
     * @return float
     */
    public function getExternalHeight() {
        return $this->_ExternalHeight;
    }

    /**
     * Container::setExternalHeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setExternalHeight($value) {
        if ($value !== null) {
            $this->_ExternalHeight = I18N::extractNumber($value);
        }
    }

    // }}}
    // InternalLength float property + getter/setter {{{

    /**
     * InternalLength float property
     *
     * @access private
     * @var float
     */
    private $_InternalLength = 0;

    /**
     * Container::getInternalLength
     *
     * @access public
     * @return float
     */
    public function getInternalLength() {
        return $this->_InternalLength;
    }

    /**
     * Container::setInternalLength
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setInternalLength($value) {
        if ($value !== null) {
            $this->_InternalLength = I18N::extractNumber($value);
        }
    }

    // }}}
    // InternalWidth float property + getter/setter {{{

    /**
     * InternalWidth float property
     *
     * @access private
     * @var float
     */
    private $_InternalWidth = 0;

    /**
     * Container::getInternalWidth
     *
     * @access public
     * @return float
     */
    public function getInternalWidth() {
        return $this->_InternalWidth;
    }

    /**
     * Container::setInternalWidth
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setInternalWidth($value) {
        if ($value !== null) {
            $this->_InternalWidth = I18N::extractNumber($value);
        }
    }

    // }}}
    // InternalHeight float property + getter/setter {{{

    /**
     * InternalHeight float property
     *
     * @access private
     * @var float
     */
    private $_InternalHeight = 0;

    /**
     * Container::getInternalHeight
     *
     * @access public
     * @return float
     */
    public function getInternalHeight() {
        return $this->_InternalHeight;
    }

    /**
     * Container::setInternalHeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setInternalHeight($value) {
        if ($value !== null) {
            $this->_InternalHeight = I18N::extractNumber($value);
        }
    }

    // }}}
    // Volume float property + getter/setter {{{

    /**
     * Volume float property
     *
     * @access private
     * @var float
     */
    private $_Volume = 0;

    /**
     * Container::getVolume
     *
     * @access public
     * @return float
     */
    public function getVolume() {
        return $this->_Volume;
    }

    /**
     * Container::setVolume
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setVolume($value) {
        if ($value !== null) {
            $this->_Volume = I18N::extractNumber($value);
        }
    }

    // }}}
    // RecipientWeight float property + getter/setter {{{

    /**
     * RecipientWeight float property
     *
     * @access private
     * @var float
     */
    private $_RecipientWeight = 0;

    /**
     * Container::getRecipientWeight
     *
     * @access public
     * @return float
     */
    public function getRecipientWeight() {
        return $this->_RecipientWeight;
    }

    /**
     * Container::setRecipientWeight
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRecipientWeight($value) {
        if ($value !== null) {
            $this->_RecipientWeight = I18N::extractNumber($value);
        }
    }

    // }}}
    // AssemblyKind string property + getter/setter {{{

    /**
     * AssemblyKind int property
     *
     * @access private
     * @var integer
     */
    private $_AssemblyKind = 0;

    /**
     * Container::getAssemblyKind
     *
     * @access public
     * @return integer
     */
    public function getAssemblyKind() {
        return $this->_AssemblyKind;
    }

    /**
     * Container::setAssemblyKind
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setAssemblyKind($value) {
        if ($value !== null) {
            $this->_AssemblyKind = (int)$value;
        }
    }

    // }}}
    // ExternalContainer string property + getter/setter {{{

    /**
     * ExternalContainer string property
     *
     * @access private
     * @var string
     */
    private $_ExternalContainer = '';

    /**
     * Container::getExternalContainer
     *
     * @access public
     * @return string
     */
    public function getExternalContainer() {
        return $this->_ExternalContainer;
    }

    /**
     * Container::setExternalContainer
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setExternalContainer($value) {
        $this->_ExternalContainer = $value;
    }

    // }}}
    // InternalContainer string property + getter/setter {{{

    /**
     * InternalContainer string property
     *
     * @access private
     * @var string
     */
    private $_InternalContainer = '';

    /**
     * Container::getInternalContainer
     *
     * @access public
     * @return string
     */
    public function getInternalContainer() {
        return $this->_InternalContainer;
    }

    /**
     * Container::setInternalContainer
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setInternalContainer($value) {
        $this->_InternalContainer = $value;
    }

    // }}}
    // Protection string property + getter/setter {{{

    /**
     * Protection string property
     *
     * @access private
     * @var string
     */
    private $_Protection = '';

    /**
     * Container::getProtection
     *
     * @access public
     * @return string
     */
    public function getProtection() {
        return $this->_Protection;
    }

    /**
     * Container::setProtection
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setProtection($value) {
        $this->_Protection = $value;
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
        return 'Container';
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
            'Reference' => Object::TYPE_STRING,
            'SupplierReference' => Object::TYPE_STRING,
            'CoverType' => 'CoverType',
            'CoverProperty' => Object::TYPE_CONST,
            'CoverKind' => Object::TYPE_CONST,
            'CoverGroup' => Object::TYPE_CONST,
            'MaxAuthorizedWeight' => Object::TYPE_FLOAT,
            'Weight' => Object::TYPE_FLOAT,
            'ExternalLength' => Object::TYPE_FLOAT,
            'ExternalWidth' => Object::TYPE_FLOAT,
            'ExternalHeight' => Object::TYPE_FLOAT,
            'InternalLength' => Object::TYPE_FLOAT,
            'InternalWidth' => Object::TYPE_FLOAT,
            'InternalHeight' => Object::TYPE_FLOAT,
            'Volume' => Object::TYPE_FLOAT,
            'RecipientWeight' => Object::TYPE_FLOAT,
            'AssemblyKind' => Object::TYPE_INT,
            'ExternalContainer' => Object::TYPE_STRING,
            'InternalContainer' => Object::TYPE_STRING,
            'Protection' => Object::TYPE_STRING);
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
            'Product'=>array(
                'linkClass'     => 'Product',
                'field'         => 'ConditioningRecommended',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Product_1'=>array(
                'linkClass'     => 'Product',
                'field'         => 'PackagingRecommended',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Product_2'=>array(
                'linkClass'     => 'Product',
                'field'         => 'GroupingRecommended',
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
    // toString() {{{

    /**
     * Retourne la représentation texte de l'objet
     *
     * @access public
     * @return string
     */
    public function toString() {
        return $this->getReference();
    }

    // }}}
    // getToStringAttribute() {{{

    /**
     * Retourne le nom de l'attribut pointé par toString()
     *
     * @static
     * @access public
     * @return string
     */
    public function getToStringAttribute() {
        return 'Reference';
    }

    // }}}
}

?>