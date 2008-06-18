<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * $Source: /home/cvs/codegen/codegentemplates.py,v $
 *
 * Ceci est un fichier généré, NE PAS EDITER.
 *
 * @copyright 2002-2006 ATEOR - All rights reserved
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
    // CommercialName string property + getter/setter {{{

    /**
     * CommercialName string property
     *
     * @access private
     * @var string
     */
    private $_CommercialName = '';

    /**
     * _RTWMaterial::getCommercialName
     *
     * @access public
     * @return string
     */
    public function getCommercialName() {
        return $this->_CommercialName;
    }

    /**
     * _RTWMaterial::setCommercialName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCommercialName($value) {
        $this->_CommercialName = $value;
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
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
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
            _RTWMaterial::TYPE_BAMBOO => _("Bamboo")
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
            'CommercialName' => Object::TYPE_STRING,
            'MaterialType' => Object::TYPE_CONST,
            'Color' => 'RTWColor',
            'Origin' => Object::TYPE_STRING);
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
                'field'         => 'Accessory1',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_7'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Accessory2',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_8'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Lining',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_9'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Insole',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_10'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'UnderSole',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_11'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Lagrima',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_12'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'HeelCovering',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_13'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Selvedge',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_14'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Thread1',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_15'=>array(
                'linkClass'     => 'RTWModel',
                'field'         => 'Thread2',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'RTWModel_16'=>array(
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
        return array('add', 'edit', 'del', 'grid', 'searchform');
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
        $return = array(
            'CommercialName'=>array(
                'label'        => _('Commercial designation'),
                'shortlabel'   => _('Commercial designation'),
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
        return 'Product';
    }

    // }}}
}

?>