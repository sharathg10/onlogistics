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

class _Prestation extends Object {
    // class constants {{{

    const PRESTATION_TYPE_MAINTENANCE = 0;
    const PRESTATION_TYPE_COURSE = 1;
    const PRESTATION_TYPE_HIRING = 2;
    const PRESTATION_TYPE_STOCKAGE = 3;
    const PERIODICITY_DAY = 1;
    const PERIODICITY_MONTH = 2;
    const PRESTATION_TOLERANCE_TYPE_HOUR = 1;
    const PRESTATION_TOLERANCE_TYPE_DAY = 2;

    // }}}
    // Constructeur {{{

    /**
     * _Prestation::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Name string property + getter/setter {{{

    /**
     * Name string property
     *
     * @access private
     * @var string
     */
    private $_Name = '';

    /**
     * _Prestation::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _Prestation::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
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
     * _Prestation::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * _Prestation::setType
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
     * _Prestation::getTypeConstArray
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
            _Prestation::PRESTATION_TYPE_MAINTENANCE => _("Maintenance"), 
            _Prestation::PRESTATION_TYPE_COURSE => _("Class"), 
            _Prestation::PRESTATION_TYPE_HIRING => _("Rental"), 
            _Prestation::PRESTATION_TYPE_STOCKAGE => _("Storage")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Operation foreignkey property + getter/setter {{{

    /**
     * Operation foreignkey
     *
     * @access private
     * @var mixed object Operation or integer
     */
    private $_Operation = false;

    /**
     * _Prestation::getOperation
     *
     * @access public
     * @return object Operation
     */
    public function getOperation() {
        if (is_int($this->_Operation) && $this->_Operation > 0) {
            $mapper = Mapper::singleton('Operation');
            $this->_Operation = $mapper->load(
                array('Id'=>$this->_Operation));
        }
        return $this->_Operation;
    }

    /**
     * _Prestation::getOperationId
     *
     * @access public
     * @return integer
     */
    public function getOperationId() {
        if ($this->_Operation instanceof Operation) {
            return $this->_Operation->getId();
        }
        return (int)$this->_Operation;
    }

    /**
     * _Prestation::setOperation
     *
     * @access public
     * @param object Operation $value
     * @return void
     */
    public function setOperation($value) {
        if (is_numeric($value)) {
            $this->_Operation = (int)$value;
        } else {
            $this->_Operation = $value;
        }
    }

    // }}}
    // TVA foreignkey property + getter/setter {{{

    /**
     * TVA foreignkey
     *
     * @access private
     * @var mixed object TVA or integer
     */
    private $_TVA = false;

    /**
     * _Prestation::getTVA
     *
     * @access public
     * @return object TVA
     */
    public function getTVA() {
        if (is_int($this->_TVA) && $this->_TVA > 0) {
            $mapper = Mapper::singleton('TVA');
            $this->_TVA = $mapper->load(
                array('Id'=>$this->_TVA));
        }
        return $this->_TVA;
    }

    /**
     * _Prestation::getTVAId
     *
     * @access public
     * @return integer
     */
    public function getTVAId() {
        if ($this->_TVA instanceof TVA) {
            return $this->_TVA->getId();
        }
        return (int)$this->_TVA;
    }

    /**
     * _Prestation::setTVA
     *
     * @access public
     * @param object TVA $value
     * @return void
     */
    public function setTVA($value) {
        if (is_numeric($value)) {
            $this->_TVA = (int)$value;
        } else {
            $this->_TVA = $value;
        }
    }

    // }}}
    // Facturable string property + getter/setter {{{

    /**
     * Facturable int property
     *
     * @access private
     * @var integer
     */
    private $_Facturable = true;

    /**
     * _Prestation::getFacturable
     *
     * @access public
     * @return integer
     */
    public function getFacturable() {
        return $this->_Facturable;
    }

    /**
     * _Prestation::setFacturable
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setFacturable($value) {
        if ($value !== null) {
            $this->_Facturable = (int)$value;
        }
    }

    // }}}
    // Active string property + getter/setter {{{

    /**
     * Active int property
     *
     * @access private
     * @var integer
     */
    private $_Active = true;

    /**
     * _Prestation::getActive
     *
     * @access public
     * @return integer
     */
    public function getActive() {
        return $this->_Active;
    }

    /**
     * _Prestation::setActive
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setActive($value) {
        if ($value !== null) {
            $this->_Active = (int)$value;
        }
    }

    // }}}
    // Periodicity const property + getter/setter/getPeriodicityConstArray {{{

    /**
     * Periodicity int property
     *
     * @access private
     * @var integer
     */
    private $_Periodicity = 1;

    /**
     * _Prestation::getPeriodicity
     *
     * @access public
     * @return integer
     */
    public function getPeriodicity() {
        return $this->_Periodicity;
    }

    /**
     * _Prestation::setPeriodicity
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPeriodicity($value) {
        if ($value !== null) {
            $this->_Periodicity = (int)$value;
        }
    }

    /**
     * _Prestation::getPeriodicityConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getPeriodicityConstArray($keys = false) {
        $array = array(
            _Prestation::PERIODICITY_DAY => _("by day"), 
            _Prestation::PERIODICITY_MONTH => _("price/number of days")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // MovementType one to many relation + getter/setter {{{

    /**
     * MovementType *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_MovementTypeCollection = false;

    /**
     * _Prestation::getMovementTypeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getMovementTypeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Prestation');
            return $mapper->getManyToMany($this->getId(),
                'MovementType', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_MovementTypeCollection) {
            $mapper = Mapper::singleton('Prestation');
            $this->_MovementTypeCollection = $mapper->getManyToMany($this->getId(),
                'MovementType');
        }
        return $this->_MovementTypeCollection;
    }

    /**
     * _Prestation::getMovementTypeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getMovementTypeCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getMovementTypeCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_MovementTypeCollection) {
            $mapper = Mapper::singleton('Prestation');
            return $mapper->getManyToManyIds($this->getId(), 'MovementType');
        }
        return $this->_MovementTypeCollection->getItemIds();
    }

    /**
     * _Prestation::setMovementTypeCollectionIds
     *
     * @access public
     * @return array
     */
    public function setMovementTypeCollectionIds($itemIds) {
        $this->_MovementTypeCollection = new Collection('MovementType');
        foreach ($itemIds as $id) {
            $this->_MovementTypeCollection->setItem($id);
        }
    }

    /**
     * _Prestation::setMovementTypeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setMovementTypeCollection($value) {
        $this->_MovementTypeCollection = $value;
    }

    /**
     * _Prestation::MovementTypeCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function MovementTypeCollectionIsLoaded() {
        return ($this->_MovementTypeCollection !== false);
    }

    // }}}
    // Potential float property + getter/setter {{{

    /**
     * Potential float property
     *
     * @access private
     * @var float
     */
    private $_Potential = 0;

    /**
     * _Prestation::getPotential
     *
     * @access public
     * @return float
     */
    public function getPotential() {
        return $this->_Potential;
    }

    /**
     * _Prestation::setPotential
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPotential($value) {
        if ($value !== null) {
            $this->_Potential = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // PotentialDate datetime property + getter/setter {{{

    /**
     * PotentialDate int property
     *
     * @access private
     * @var string
     */
    private $_PotentialDate = 0;

    /**
     * _Prestation::getPotentialDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getPotentialDate($format = false) {
        return $this->dateFormat($this->_PotentialDate, $format);
    }

    /**
     * _Prestation::setPotentialDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPotentialDate($value) {
        $this->_PotentialDate = $value;
    }

    // }}}
    // Tolerance string property + getter/setter {{{

    /**
     * Tolerance int property
     *
     * @access private
     * @var integer
     */
    private $_Tolerance = 0;

    /**
     * _Prestation::getTolerance
     *
     * @access public
     * @return integer
     */
    public function getTolerance() {
        return $this->_Tolerance;
    }

    /**
     * _Prestation::setTolerance
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTolerance($value) {
        if ($value !== null) {
            $this->_Tolerance = (int)$value;
        }
    }

    // }}}
    // ToleranceType const property + getter/setter/getToleranceTypeConstArray {{{

    /**
     * ToleranceType int property
     *
     * @access private
     * @var integer
     */
    private $_ToleranceType = 0;

    /**
     * _Prestation::getToleranceType
     *
     * @access public
     * @return integer
     */
    public function getToleranceType() {
        return $this->_ToleranceType;
    }

    /**
     * _Prestation::setToleranceType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setToleranceType($value) {
        if ($value !== null) {
            $this->_ToleranceType = (int)$value;
        }
    }

    /**
     * _Prestation::getToleranceTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getToleranceTypeConstArray($keys = false) {
        $array = array(
            _Prestation::PRESTATION_TOLERANCE_TYPE_HOUR => _("Hours"), 
            _Prestation::PRESTATION_TOLERANCE_TYPE_DAY => _("Days")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // FreePeriod string property + getter/setter {{{

    /**
     * FreePeriod int property
     *
     * @access private
     * @var integer
     */
    private $_FreePeriod = 0;

    /**
     * _Prestation::getFreePeriod
     *
     * @access public
     * @return integer
     */
    public function getFreePeriod() {
        return $this->_FreePeriod;
    }

    /**
     * _Prestation::setFreePeriod
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setFreePeriod($value) {
        if ($value !== null) {
            $this->_FreePeriod = (int)$value;
        }
    }

    // }}}
    // Comment string property + getter/setter {{{

    /**
     * Comment string property
     *
     * @access private
     * @var string
     */
    private $_Comment = '';

    /**
     * _Prestation::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * _Prestation::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // CostRange one to many relation + getter/setter {{{

    /**
     * CostRange 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_CostRangeCollection = false;

    /**
     * _Prestation::getCostRangeCollection
     *
     * @access public
     * @return object Collection
     */
    public function getCostRangeCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Prestation');
            return $mapper->getOneToMany($this->getId(),
                'CostRange', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_CostRangeCollection) {
            $mapper = Mapper::singleton('Prestation');
            $this->_CostRangeCollection = $mapper->getOneToMany($this->getId(),
                'CostRange');
        }
        return $this->_CostRangeCollection;
    }

    /**
     * _Prestation::getCostRangeCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getCostRangeCollectionIds($filter = array()) {
        $col = $this->getCostRangeCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Prestation::setCostRangeCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setCostRangeCollection($value) {
        $this->_CostRangeCollection = $value;
    }

    // }}}
    // PrestationCost one to many relation + getter/setter {{{

    /**
     * PrestationCost 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_PrestationCostCollection = false;

    /**
     * _Prestation::getPrestationCostCollection
     *
     * @access public
     * @return object Collection
     */
    public function getPrestationCostCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Prestation');
            return $mapper->getOneToMany($this->getId(),
                'PrestationCost', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_PrestationCostCollection) {
            $mapper = Mapper::singleton('Prestation');
            $this->_PrestationCostCollection = $mapper->getOneToMany($this->getId(),
                'PrestationCost');
        }
        return $this->_PrestationCostCollection;
    }

    /**
     * _Prestation::getPrestationCostCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getPrestationCostCollectionIds($filter = array()) {
        $col = $this->getPrestationCostCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Prestation::setPrestationCostCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setPrestationCostCollection($value) {
        $this->_PrestationCostCollection = $value;
    }

    // }}}
    // PrestationCustomer one to many relation + getter/setter {{{

    /**
     * PrestationCustomer 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_PrestationCustomerCollection = false;

    /**
     * _Prestation::getPrestationCustomerCollection
     *
     * @access public
     * @return object Collection
     */
    public function getPrestationCustomerCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Prestation');
            return $mapper->getOneToMany($this->getId(),
                'PrestationCustomer', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_PrestationCustomerCollection) {
            $mapper = Mapper::singleton('Prestation');
            $this->_PrestationCustomerCollection = $mapper->getOneToMany($this->getId(),
                'PrestationCustomer');
        }
        return $this->_PrestationCustomerCollection;
    }

    /**
     * _Prestation::getPrestationCustomerCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getPrestationCustomerCollectionIds($filter = array()) {
        $col = $this->getPrestationCustomerCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Prestation::setPrestationCustomerCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setPrestationCustomerCollection($value) {
        $this->_PrestationCustomerCollection = $value;
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
        return 'Prestation';
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
        return _('Add/Update service');
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
            'Name' => Object::TYPE_STRING,
            'Type' => Object::TYPE_CONST,
            'Operation' => 'Operation',
            'TVA' => 'TVA',
            'Facturable' => Object::TYPE_BOOL,
            'Active' => Object::TYPE_BOOL,
            'Periodicity' => Object::TYPE_CONST,
            'Potential' => Object::TYPE_DECIMAL,
            'PotentialDate' => Object::TYPE_DATETIME,
            'Tolerance' => Object::TYPE_INT,
            'ToleranceType' => Object::TYPE_CONST,
            'FreePeriod' => Object::TYPE_INT,
            'Comment' => Object::TYPE_TEXT);
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
            'MovementType'=>array(
                'linkClass'     => 'MovementType',
                'field'         => 'FromPrestation',
                'linkTable'     => 'prsToMvtType',
                'linkField'     => 'ToMovementType',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
            ),
            'CostRange'=>array(
                'linkClass'     => 'CostRange',
                'field'         => 'Prestation',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'InvoiceItem'=>array(
                'linkClass'     => 'InvoiceItem',
                'field'         => 'Prestation',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'Operation'=>array(
                'linkClass'     => 'Operation',
                'field'         => 'Prestation',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'PrestationCost'=>array(
                'linkClass'     => 'PrestationCost',
                'field'         => 'Prestation',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'PrestationCustomer'=>array(
                'linkClass'     => 'PrestationCustomer',
                'field'         => 'Prestation',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'PrestationCommandItem'=>array(
                'linkClass'     => 'PrestationCommandItem',
                'field'         => 'Prestation',
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
        $return = array('Name');
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
        return array('grid', 'searchform', 'add', 'edit', 'del');
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
                'usedby'       => array('grid', 'addedit', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('General informations')
            ),
            'Type'=>array(
                'label'        => _('Type'),
                'shortlabel'   => _('Type'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('General informations')
            ),
            'Operation'=>array(
                'label'        => _('Operation'),
                'shortlabel'   => _('Operation'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('General informations')
            ),
            'TVA'=>array(
                'label'        => _('VAT'),
                'shortlabel'   => _('VAT'),
                'usedby'       => array('grid', 'searchform', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('General informations')
            ),
            'Facturable'=>array(
                'label'        => _('Chargeable'),
                'shortlabel'   => _('Chargeable'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('General informations')
            ),
            'Active'=>array(
                'label'        => _('Active'),
                'shortlabel'   => _('Active'),
                'usedby'       => array('searchform', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('General informations')
            ),
            'Periodicity'=>array(
                'label'        => _('Calculation mode'),
                'shortlabel'   => _('Calculation mode'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('General informations')
            ),
            'MovementType'=>array(
                'label'        => _('Movement type'),
                'shortlabel'   => _('Movement type'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('General informations')
            ));
        return $return;
    }

    // }}}
}

?>