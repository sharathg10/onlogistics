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

class DangerousProductType extends Object {
    // class constants {{{

    const CLASS_NONE = 0;
    const NUMBER_41 = 1;
    const NUMBER_42 = 2;
    const NUMBER_43 = 3;
    const NUMBER_51 = 4;
    const NUMBER_52 = 5;
    const NUMBER_61 = 6;
    const NUMBER_62 = 7;

    // }}}
    // Constructeur {{{

    /**
     * DangerousProductType::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Class const property + getter/setter/getClassConstArray {{{

    /**
     * Class int property
     *
     * @access private
     * @var integer
     */
    private $_Class = 0;

    /**
     * DangerousProductType::getClass
     *
     * @access public
     * @return integer
     */
    public function getClass() {
        return $this->_Class;
    }

    /**
     * DangerousProductType::setClass
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setClass($value) {
        if ($value !== null) {
            $this->_Class = (int)$value;
        }
    }

    /**
     * DangerousProductType::getClassConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getClassConstArray($keys = false) {
        $array = array(
            DangerousProductType::CLASS_NONE => _("N/A")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Letter foreignkey property + getter/setter {{{

    /**
     * Letter foreignkey
     *
     * @access private
     * @var mixed object DangerousProductLetter or integer
     */
    private $_Letter = false;

    /**
     * DangerousProductType::getLetter
     *
     * @access public
     * @return object DangerousProductLetter
     */
    public function getLetter() {
        if (is_int($this->_Letter) && $this->_Letter > 0) {
            $mapper = Mapper::singleton('DangerousProductLetter');
            $this->_Letter = $mapper->load(
                array('Id'=>$this->_Letter));
        }
        return $this->_Letter;
    }

    /**
     * DangerousProductType::getLetterId
     *
     * @access public
     * @return integer
     */
    public function getLetterId() {
        if ($this->_Letter instanceof DangerousProductLetter) {
            return $this->_Letter->getId();
        }
        return (int)$this->_Letter;
    }

    /**
     * DangerousProductType::setLetter
     *
     * @access public
     * @param object DangerousProductLetter $value
     * @return void
     */
    public function setLetter($value) {
        if (is_numeric($value)) {
            $this->_Letter = (int)$value;
        } else {
            $this->_Letter = $value;
        }
    }

    // }}}
    // Group foreignkey property + getter/setter {{{

    /**
     * Group foreignkey
     *
     * @access private
     * @var mixed object DangerousProductGroup or integer
     */
    private $_Group = false;

    /**
     * DangerousProductType::getGroup
     *
     * @access public
     * @return object DangerousProductGroup
     */
    public function getGroup() {
        if (is_int($this->_Group) && $this->_Group > 0) {
            $mapper = Mapper::singleton('DangerousProductGroup');
            $this->_Group = $mapper->load(
                array('Id'=>$this->_Group));
        }
        return $this->_Group;
    }

    /**
     * DangerousProductType::getGroupId
     *
     * @access public
     * @return integer
     */
    public function getGroupId() {
        if ($this->_Group instanceof DangerousProductGroup) {
            return $this->_Group->getId();
        }
        return (int)$this->_Group;
    }

    /**
     * DangerousProductType::setGroup
     *
     * @access public
     * @param object DangerousProductGroup $value
     * @return void
     */
    public function setGroup($value) {
        if (is_numeric($value)) {
            $this->_Group = (int)$value;
        } else {
            $this->_Group = $value;
        }
    }

    // }}}
    // Number const property + getter/setter/getNumberConstArray {{{

    /**
     * Number int property
     *
     * @access private
     * @var integer
     */
    private $_Number = 0;

    /**
     * DangerousProductType::getNumber
     *
     * @access public
     * @return integer
     */
    public function getNumber() {
        return $this->_Number;
    }

    /**
     * DangerousProductType::setNumber
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setNumber($value) {
        if ($value !== null) {
            $this->_Number = (int)$value;
        }
    }

    /**
     * DangerousProductType::getNumberConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getNumberConstArray($keys = false) {
        $array = array(
            DangerousProductType::NUMBER_41 => _("CLASS 4.1: INFLAMMABLE SOLIDS"), 
            DangerousProductType::NUMBER_42 => _("CLASS 4.2: MATERIALS PRONE TO SPONTANEOUS INFLAMMATION"), 
            DangerousProductType::NUMBER_43 => _("CLASS 4.3: MATERIALS THAT EMIT INFLAMMABLE GAS WHEN IN CONTACT WITH WATER"), 
            DangerousProductType::NUMBER_51 => _("CLASS 5.1: COMBUSTIBLE MATERIALS"), 
            DangerousProductType::NUMBER_52 => _("CLASS 5.2: ORGANIC PEROXYDES"), 
            DangerousProductType::NUMBER_61 => _("CLASS 6.1: POISON MATERIALS"), 
            DangerousProductType::NUMBER_62 => _("CLASS 6.2: INFECTIOUS MATERIALS")
        );
        asort($array);
        return $keys?array_keys($array):$array;
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
        return 'DangerousProductType';
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
            'Class' => Object::TYPE_CONST,
            'Letter' => 'DangerousProductLetter',
            'Group' => 'DangerousProductGroup',
            'Number' => Object::TYPE_CONST);
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