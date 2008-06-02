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

class Operation extends Object {
    // class constants {{{

    const OPERATION_TYPE_DIVERS = 0;
    const OPERATION_TYPE_PROD = 1;
    const OPERATION_TYPE_CONS = 2;

    // }}}
    // Constructeur {{{

    /**
     * Operation::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Name i18n_string property + getter/setter {{{

    /**
     * Name foreignkey
     *
     * @access private
     * @var mixed object I18nString or integer
     */
    private $_Name = 0;

    /**
     * Operation::getName
     *
     * @access public
     * @param string $locale optional, default is the current locale code
     * @param boolean $useDefaultLocaleIfEmpty determine if the getter must
     * return the translation in the DEFAULT_LOCALE if no translation is found
     * in the current locale.
     * @return string
     */
    public function getName($locale=false, $defaultLocaleIfEmpty=true) {
        $locale = $locale !== false ? $locale : I18N::getLocaleCode();
        if (is_int($this->_Name) && $this->_Name > 0) {
            $this->_Name = Object::load('I18nString', $this->_Name);
        }
        $ret = null;
        if ($this->_Name instanceof I18nString) {
            $getter = 'getStringValue_' . $locale;
            $ret = $this->_Name->$getter();
            if ($ret == null && $defaultLocaleIfEmpty) {
                $getter = 'getStringValue_' . LOCALE_DEFAULT;
                $ret = $this->_Name->$getter();
            }
        }
        return $ret;
    }

    /**
     * Operation::getNameId
     *
     * @access public
     * @return integer
     */
    public function getNameId() {
        if ($this->_Name instanceof I18nString) {
            return $this->_Name->getId();
        }
        return (int)$this->_Name;
    }

    /**
     * Operation::setName
     *
     * @access public
     * @param string $value
     * @param string $locale optional, default is the current locale code
     * @return void
     */
    public function setName($value, $locale=false) {
        if (is_numeric($value)) {
            $this->_Name = (int)$value;
        } else if ($value instanceof I18nString) {
            $this->_Name = $value;
        } else {
            $locale = $locale !== false ? $locale : I18N::getLocaleCode();
            if (!($this->_Name instanceof I18nString)) {
                $this->_Name = Object::load('I18nString', $this->_Name);
                if (!($this->_Name instanceof I18nString)) {
                    $this->_Name = new I18nString();
                }
            }
            $setter = 'setStringValue_'.$locale;
            $this->_Name->$setter($value);
            $this->_Name->save();
        }
    }

    // }}}
    // Symbol string property + getter/setter {{{

    /**
     * Symbol string property
     *
     * @access private
     * @var string
     */
    private $_Symbol = '';

    /**
     * Operation::getSymbol
     *
     * @access public
     * @return string
     */
    public function getSymbol() {
        return $this->_Symbol;
    }

    /**
     * Operation::setSymbol
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setSymbol($value) {
        $this->_Symbol = $value;
    }

    // }}}
    // FrontTolerance datetime property + getter/setter {{{

    /**
     * FrontTolerance int property
     *
     * @access private
     * @var string
     */
    private $_FrontTolerance = 0;

    /**
     * Operation::getFrontTolerance
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getFrontTolerance($format = false) {
        return $this->dateFormat($this->_FrontTolerance, $format);
    }

    /**
     * Operation::setFrontTolerance
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setFrontTolerance($value) {
        $this->_FrontTolerance = $value;
    }

    // }}}
    // EndTolerance datetime property + getter/setter {{{

    /**
     * EndTolerance int property
     *
     * @access private
     * @var string
     */
    private $_EndTolerance = 0;

    /**
     * Operation::getEndTolerance
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEndTolerance($format = false) {
        return $this->dateFormat($this->_EndTolerance, $format);
    }

    /**
     * Operation::setEndTolerance
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndTolerance($value) {
        $this->_EndTolerance = $value;
    }

    // }}}
    // TotalTolerance datetime property + getter/setter {{{

    /**
     * TotalTolerance int property
     *
     * @access private
     * @var string
     */
    private $_TotalTolerance = 0;

    /**
     * Operation::getTotalTolerance
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getTotalTolerance($format = false) {
        return $this->dateFormat($this->_TotalTolerance, $format);
    }

    /**
     * Operation::setTotalTolerance
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setTotalTolerance($value) {
        $this->_TotalTolerance = $value;
    }

    // }}}
    // IsConcreteProductNeeded string property + getter/setter {{{

    /**
     * IsConcreteProductNeeded int property
     *
     * @access private
     * @var integer
     */
    private $_IsConcreteProductNeeded = false;

    /**
     * Operation::getIsConcreteProductNeeded
     *
     * @access public
     * @return integer
     */
    public function getIsConcreteProductNeeded() {
        return $this->_IsConcreteProductNeeded;
    }

    /**
     * Operation::setIsConcreteProductNeeded
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setIsConcreteProductNeeded($value) {
        if ($value !== null) {
            $this->_IsConcreteProductNeeded = (int)$value;
        }
    }

    // }}}
    // Type const property + getter/setter/getTypeConstArray {{{

    /**
     * Type int property
     *
     * @access private
     * @var integer
     */
    private $_Type = 0;

    /**
     * Operation::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * Operation::setType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setType($value) {
        if ($value !== null) {
            $this->_Type = (int)$value;
        }
    }

    /**
     * Operation::getTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getTypeConstArray($keys = false) {
        $array = array(
            Operation::OPERATION_TYPE_DIVERS => _("Various"), 
            Operation::OPERATION_TYPE_PROD => _("Production"), 
            Operation::OPERATION_TYPE_CONS => _("Consulting")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Prestation foreignkey property + getter/setter {{{

    /**
     * Prestation foreignkey
     *
     * @access private
     * @var mixed object Prestation or integer
     */
    private $_Prestation = false;

    /**
     * Operation::getPrestation
     *
     * @access public
     * @return object Prestation
     */
    public function getPrestation() {
        if (is_int($this->_Prestation) && $this->_Prestation > 0) {
            $mapper = Mapper::singleton('Prestation');
            $this->_Prestation = $mapper->load(
                array('Id'=>$this->_Prestation));
        }
        return $this->_Prestation;
    }

    /**
     * Operation::getPrestationId
     *
     * @access public
     * @return integer
     */
    public function getPrestationId() {
        if ($this->_Prestation instanceof Prestation) {
            return $this->_Prestation->getId();
        }
        return (int)$this->_Prestation;
    }

    /**
     * Operation::setPrestation
     *
     * @access public
     * @param object Prestation $value
     * @return void
     */
    public function setPrestation($value) {
        if (is_numeric($value)) {
            $this->_Prestation = (int)$value;
        } else {
            $this->_Prestation = $value;
        }
    }

    // }}}
    // Task one to many relation + getter/setter {{{

    /**
     * Task *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_TaskCollection = false;

    /**
     * Operation::getTaskCollection
     *
     * @access public
     * @return object Collection
     */
    public function getTaskCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Operation');
            return $mapper->getManyToMany($this->getId(),
                'Task', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_TaskCollection) {
            $mapper = Mapper::singleton('Operation');
            $this->_TaskCollection = $mapper->getManyToMany($this->getId(),
                'Task');
        }
        return $this->_TaskCollection;
    }

    /**
     * Operation::getTaskCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getTaskCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getTaskCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_TaskCollection) {
            $mapper = Mapper::singleton('Operation');
            return $mapper->getManyToManyIds($this->getId(), 'Task');
        }
        return $this->_TaskCollection->getItemIds();
    }

    /**
     * Operation::setTaskCollectionIds
     *
     * @access public
     * @return array
     */
    public function setTaskCollectionIds($itemIds) {
        $this->_TaskCollection = new Collection('Task');
        foreach ($itemIds as $id) {
            $this->_TaskCollection->setItem($id);
        }
    }

    /**
     * Operation::setTaskCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setTaskCollection($value) {
        $this->_TaskCollection = $value;
    }

    /**
     * Operation::TaskCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function TaskCollectionIsLoaded() {
        return ($this->_TaskCollection !== false);
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
        return 'Operation';
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
        return _('Operations');
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
            'Name' => Object::TYPE_I18N_STRING,
            'Symbol' => Object::TYPE_STRING,
            'FrontTolerance' => Object::TYPE_TIME,
            'EndTolerance' => Object::TYPE_TIME,
            'TotalTolerance' => Object::TYPE_TIME,
            'IsConcreteProductNeeded' => Object::TYPE_BOOL,
            'Type' => Object::TYPE_CONST,
            'Prestation' => 'Prestation');
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
            'Task'=>array(
                'linkClass'     => 'Task',
                'field'         => 'FromOperation',
                'linkTable'     => 'oprTask',
                'linkField'     => 'ToTask',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ActivatedChainOperation'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'Operation',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainOperation'=>array(
                'linkClass'     => 'ChainOperation',
                'field'         => 'Operation',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Prestation'=>array(
                'linkClass'     => 'Prestation',
                'field'         => 'Operation',
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
        $return = array('Symbol');
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
        return array('searchform', 'grid', 'add', 'edit', 'del');
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
            'Name'=>array(
                'label'        => _('Name'),
                'shortlabel'   => _('Name'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Symbol'=>array(
                'label'        => _('Symbol'),
                'shortlabel'   => _('Symbol'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Type'=>array(
                'label'        => _('Type'),
                'shortlabel'   => _('Type'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Task'=>array(
                'label'        => _('Tasks'),
                'shortlabel'   => _('Tasks'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
}

?>