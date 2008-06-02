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

class Task extends Object {
    
    // Constructeur {{{

    /**
     * Task::__construct()
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
     * Task::getName
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
     * Task::getNameId
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
     * Task::setName
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
     * Task::getSymbol
     *
     * @access public
     * @return string
     */
    public function getSymbol() {
        return $this->_Symbol;
    }

    /**
     * Task::setSymbol
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setSymbol($value) {
        $this->_Symbol = $value;
    }

    // }}}
    // Instructions string property + getter/setter {{{

    /**
     * Instructions string property
     *
     * @access private
     * @var string
     */
    private $_Instructions = '';

    /**
     * Task::getInstructions
     *
     * @access public
     * @return string
     */
    public function getInstructions() {
        return $this->_Instructions;
    }

    /**
     * Task::setInstructions
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setInstructions($value) {
        $this->_Instructions = $value;
    }

    // }}}
    // Duration float property + getter/setter {{{

    /**
     * Duration float property
     *
     * @access private
     * @var float
     */
    private $_Duration = 0;

    /**
     * Task::getDuration
     *
     * @access public
     * @return float
     */
    public function getDuration() {
        return $this->_Duration;
    }

    /**
     * Task::setDuration
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setDuration($value) {
        if ($value !== null) {
            $this->_Duration = I18N::extractNumber($value);
        }
    }

    // }}}
    // Cost float property + getter/setter {{{

    /**
     * Cost float property
     *
     * @access private
     * @var float
     */
    private $_Cost = 0;

    /**
     * Task::getCost
     *
     * @access public
     * @return float
     */
    public function getCost() {
        return $this->_Cost;
    }

    /**
     * Task::setCost
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setCost($value) {
        if ($value !== null) {
            $this->_Cost = I18N::extractNumber($value);
        }
    }

    // }}}
    // ToBeValidated string property + getter/setter {{{

    /**
     * ToBeValidated int property
     *
     * @access private
     * @var integer
     */
    private $_ToBeValidated = 0;

    /**
     * Task::getToBeValidated
     *
     * @access public
     * @return integer
     */
    public function getToBeValidated() {
        return $this->_ToBeValidated;
    }

    /**
     * Task::setToBeValidated
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setToBeValidated($value) {
        if ($value !== null) {
            $this->_ToBeValidated = (int)$value;
        }
    }

    // }}}
    // Type string property + getter/setter {{{

    /**
     * Type int property
     *
     * @access private
     * @var integer
     */
    private $_Type = 0;

    /**
     * Task::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * Task::setType
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

    // }}}
    // IsBoxCreator string property + getter/setter {{{

    /**
     * IsBoxCreator int property
     *
     * @access private
     * @var integer
     */
    private $_IsBoxCreator = 0;

    /**
     * Task::getIsBoxCreator
     *
     * @access public
     * @return integer
     */
    public function getIsBoxCreator() {
        return $this->_IsBoxCreator;
    }

    /**
     * Task::setIsBoxCreator
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setIsBoxCreator($value) {
        if ($value !== null) {
            $this->_IsBoxCreator = (int)$value;
        }
    }

    // }}}
    // Operation one to many relation + getter/setter {{{

    /**
     * Operation *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_OperationCollection = false;

    /**
     * Task::getOperationCollection
     *
     * @access public
     * @return object Collection
     */
    public function getOperationCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Task');
            return $mapper->getManyToMany($this->getId(),
                'Operation', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_OperationCollection) {
            $mapper = Mapper::singleton('Task');
            $this->_OperationCollection = $mapper->getManyToMany($this->getId(),
                'Operation');
        }
        return $this->_OperationCollection;
    }

    /**
     * Task::getOperationCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getOperationCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getOperationCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_OperationCollection) {
            $mapper = Mapper::singleton('Task');
            return $mapper->getManyToManyIds($this->getId(), 'Operation');
        }
        return $this->_OperationCollection->getItemIds();
    }

    /**
     * Task::setOperationCollectionIds
     *
     * @access public
     * @return array
     */
    public function setOperationCollectionIds($itemIds) {
        $this->_OperationCollection = new Collection('Operation');
        foreach ($itemIds as $id) {
            $this->_OperationCollection->setItem($id);
        }
    }

    /**
     * Task::setOperationCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setOperationCollection($value) {
        $this->_OperationCollection = $value;
    }

    /**
     * Task::OperationCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function OperationCollectionIsLoaded() {
        return ($this->_OperationCollection !== false);
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
        return 'Task';
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
        return _('Tasks');
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
            'Instructions' => Object::TYPE_STRING,
            'Duration' => Object::TYPE_FLOAT,
            'Cost' => Object::TYPE_FLOAT,
            'ToBeValidated' => Object::TYPE_BOOL,
            'Type' => Object::TYPE_INT,
            'IsBoxCreator' => Object::TYPE_BOOL);
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
            'Operation'=>array(
                'linkClass'     => 'Operation',
                'field'         => 'ToTask',
                'linkTable'     => 'oprTask',
                'linkField'     => 'FromOperation',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'ActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'Task',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ChainTask'=>array(
                'linkClass'     => 'ChainTask',
                'field'         => 'Task',
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
            'ToBeValidated'=>array(
                'label'        => _('To be validated'),
                'shortlabel'   => _('To be validated'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Operation'=>array(
                'label'        => _('Operation'),
                'shortlabel'   => _('Operation'),
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