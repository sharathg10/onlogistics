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

class _ActivatedChainOperation extends Object {
    // class constants {{{

    const FACTURED_NONE = 0;
    const FACTURED_FULL = 1;
    const FACTURED_PARTIAL = 2;

    // }}}
    // Constructeur {{{

    /**
     * _ActivatedChainOperation::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Actor foreignkey property + getter/setter {{{

    /**
     * Actor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Actor = false;

    /**
     * _ActivatedChainOperation::getActor
     *
     * @access public
     * @return object Actor
     */
    public function getActor() {
        if (is_int($this->_Actor) && $this->_Actor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Actor = $mapper->load(
                array('Id'=>$this->_Actor));
        }
        return $this->_Actor;
    }

    /**
     * _ActivatedChainOperation::getActorId
     *
     * @access public
     * @return integer
     */
    public function getActorId() {
        if ($this->_Actor instanceof Actor) {
            return $this->_Actor->getId();
        }
        return (int)$this->_Actor;
    }

    /**
     * _ActivatedChainOperation::setActor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setActor($value) {
        if (is_numeric($value)) {
            $this->_Actor = (int)$value;
        } else {
            $this->_Actor = $value;
        }
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
     * _ActivatedChainOperation::getOperation
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
     * _ActivatedChainOperation::getOperationId
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
     * _ActivatedChainOperation::setOperation
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
    // Ghost foreignkey property + getter/setter {{{

    /**
     * Ghost foreignkey
     *
     * @access private
     * @var mixed object ActivatedChainOperation or integer
     */
    private $_Ghost = false;

    /**
     * _ActivatedChainOperation::getGhost
     *
     * @access public
     * @return object ActivatedChainOperation
     */
    public function getGhost() {
        if (is_int($this->_Ghost) && $this->_Ghost > 0) {
            $mapper = Mapper::singleton('ActivatedChainOperation');
            $this->_Ghost = $mapper->load(
                array('Id'=>$this->_Ghost));
        }
        return $this->_Ghost;
    }

    /**
     * _ActivatedChainOperation::getGhostId
     *
     * @access public
     * @return integer
     */
    public function getGhostId() {
        if ($this->_Ghost instanceof ActivatedChainOperation) {
            return $this->_Ghost->getId();
        }
        return (int)$this->_Ghost;
    }

    /**
     * _ActivatedChainOperation::setGhost
     *
     * @access public
     * @param object ActivatedChainOperation $value
     * @return void
     */
    public function setGhost($value) {
        if (is_numeric($value)) {
            $this->_Ghost = (int)$value;
        } else {
            $this->_Ghost = $value;
        }
    }

    // }}}
    // ActivatedChain foreignkey property + getter/setter {{{

    /**
     * ActivatedChain foreignkey
     *
     * @access private
     * @var mixed object ActivatedChain or integer
     */
    private $_ActivatedChain = false;

    /**
     * _ActivatedChainOperation::getActivatedChain
     *
     * @access public
     * @return object ActivatedChain
     */
    public function getActivatedChain() {
        if (is_int($this->_ActivatedChain) && $this->_ActivatedChain > 0) {
            $mapper = Mapper::singleton('ActivatedChain');
            $this->_ActivatedChain = $mapper->load(
                array('Id'=>$this->_ActivatedChain));
        }
        return $this->_ActivatedChain;
    }

    /**
     * _ActivatedChainOperation::getActivatedChainId
     *
     * @access public
     * @return integer
     */
    public function getActivatedChainId() {
        if ($this->_ActivatedChain instanceof ActivatedChain) {
            return $this->_ActivatedChain->getId();
        }
        return (int)$this->_ActivatedChain;
    }

    /**
     * _ActivatedChainOperation::setActivatedChain
     *
     * @access public
     * @param object ActivatedChain $value
     * @return void
     */
    public function setActivatedChain($value) {
        if (is_numeric($value)) {
            $this->_ActivatedChain = (int)$value;
        } else {
            $this->_ActivatedChain = $value;
        }
    }

    // }}}
    // OwnerWorkerOrder foreignkey property + getter/setter {{{

    /**
     * OwnerWorkerOrder foreignkey
     *
     * @access private
     * @var mixed object WorkOrder or integer
     */
    private $_OwnerWorkerOrder = false;

    /**
     * _ActivatedChainOperation::getOwnerWorkerOrder
     *
     * @access public
     * @return object WorkOrder
     */
    public function getOwnerWorkerOrder() {
        if (is_int($this->_OwnerWorkerOrder) && $this->_OwnerWorkerOrder > 0) {
            $mapper = Mapper::singleton('WorkOrder');
            $this->_OwnerWorkerOrder = $mapper->load(
                array('Id'=>$this->_OwnerWorkerOrder));
        }
        return $this->_OwnerWorkerOrder;
    }

    /**
     * _ActivatedChainOperation::getOwnerWorkerOrderId
     *
     * @access public
     * @return integer
     */
    public function getOwnerWorkerOrderId() {
        if ($this->_OwnerWorkerOrder instanceof WorkOrder) {
            return $this->_OwnerWorkerOrder->getId();
        }
        return (int)$this->_OwnerWorkerOrder;
    }

    /**
     * _ActivatedChainOperation::setOwnerWorkerOrder
     *
     * @access public
     * @param object WorkOrder $value
     * @return void
     */
    public function setOwnerWorkerOrder($value) {
        if (is_numeric($value)) {
            $this->_OwnerWorkerOrder = (int)$value;
        } else {
            $this->_OwnerWorkerOrder = $value;
        }
    }

    // }}}
    // RealActor foreignkey property + getter/setter {{{

    /**
     * RealActor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_RealActor = false;

    /**
     * _ActivatedChainOperation::getRealActor
     *
     * @access public
     * @return object Actor
     */
    public function getRealActor() {
        if (is_int($this->_RealActor) && $this->_RealActor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_RealActor = $mapper->load(
                array('Id'=>$this->_RealActor));
        }
        return $this->_RealActor;
    }

    /**
     * _ActivatedChainOperation::getRealActorId
     *
     * @access public
     * @return integer
     */
    public function getRealActorId() {
        if ($this->_RealActor instanceof Actor) {
            return $this->_RealActor->getId();
        }
        return (int)$this->_RealActor;
    }

    /**
     * _ActivatedChainOperation::setRealActor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setRealActor($value) {
        if (is_numeric($value)) {
            $this->_RealActor = (int)$value;
        } else {
            $this->_RealActor = $value;
        }
    }

    // }}}
    // ConcreteProduct foreignkey property + getter/setter {{{

    /**
     * ConcreteProduct foreignkey
     *
     * @access private
     * @var mixed object ConcreteProduct or integer
     */
    private $_ConcreteProduct = false;

    /**
     * _ActivatedChainOperation::getConcreteProduct
     *
     * @access public
     * @return object ConcreteProduct
     */
    public function getConcreteProduct() {
        if (is_int($this->_ConcreteProduct) && $this->_ConcreteProduct > 0) {
            $mapper = Mapper::singleton('ConcreteProduct');
            $this->_ConcreteProduct = $mapper->load(
                array('Id'=>$this->_ConcreteProduct));
        }
        return $this->_ConcreteProduct;
    }

    /**
     * _ActivatedChainOperation::getConcreteProductId
     *
     * @access public
     * @return integer
     */
    public function getConcreteProductId() {
        if ($this->_ConcreteProduct instanceof ConcreteProduct) {
            return $this->_ConcreteProduct->getId();
        }
        return (int)$this->_ConcreteProduct;
    }

    /**
     * _ActivatedChainOperation::setConcreteProduct
     *
     * @access public
     * @param object ConcreteProduct $value
     * @return void
     */
    public function setConcreteProduct($value) {
        if (is_numeric($value)) {
            $this->_ConcreteProduct = (int)$value;
        } else {
            $this->_ConcreteProduct = $value;
        }
    }

    // }}}
    // RealConcreteProduct foreignkey property + getter/setter {{{

    /**
     * RealConcreteProduct foreignkey
     *
     * @access private
     * @var mixed object ConcreteProduct or integer
     */
    private $_RealConcreteProduct = false;

    /**
     * _ActivatedChainOperation::getRealConcreteProduct
     *
     * @access public
     * @return object ConcreteProduct
     */
    public function getRealConcreteProduct() {
        if (is_int($this->_RealConcreteProduct) && $this->_RealConcreteProduct > 0) {
            $mapper = Mapper::singleton('ConcreteProduct');
            $this->_RealConcreteProduct = $mapper->load(
                array('Id'=>$this->_RealConcreteProduct));
        }
        return $this->_RealConcreteProduct;
    }

    /**
     * _ActivatedChainOperation::getRealConcreteProductId
     *
     * @access public
     * @return integer
     */
    public function getRealConcreteProductId() {
        if ($this->_RealConcreteProduct instanceof ConcreteProduct) {
            return $this->_RealConcreteProduct->getId();
        }
        return (int)$this->_RealConcreteProduct;
    }

    /**
     * _ActivatedChainOperation::setRealConcreteProduct
     *
     * @access public
     * @param object ConcreteProduct $value
     * @return void
     */
    public function setRealConcreteProduct($value) {
        if (is_numeric($value)) {
            $this->_RealConcreteProduct = (int)$value;
        } else {
            $this->_RealConcreteProduct = $value;
        }
    }

    // }}}
    // FirstTask foreignkey property + getter/setter {{{

    /**
     * FirstTask foreignkey
     *
     * @access private
     * @var mixed object ActivatedChainTask or integer
     */
    private $_FirstTask = false;

    /**
     * _ActivatedChainOperation::getFirstTask
     *
     * @access public
     * @return object ActivatedChainTask
     */
    public function getFirstTask() {
        if (is_int($this->_FirstTask) && $this->_FirstTask > 0) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_FirstTask = $mapper->load(
                array('Id'=>$this->_FirstTask));
        }
        return $this->_FirstTask;
    }

    /**
     * _ActivatedChainOperation::getFirstTaskId
     *
     * @access public
     * @return integer
     */
    public function getFirstTaskId() {
        if ($this->_FirstTask instanceof ActivatedChainTask) {
            return $this->_FirstTask->getId();
        }
        return (int)$this->_FirstTask;
    }

    /**
     * _ActivatedChainOperation::setFirstTask
     *
     * @access public
     * @param object ActivatedChainTask $value
     * @return void
     */
    public function setFirstTask($value) {
        if (is_numeric($value)) {
            $this->_FirstTask = (int)$value;
        } else {
            $this->_FirstTask = $value;
        }
    }

    // }}}
    // LastTask foreignkey property + getter/setter {{{

    /**
     * LastTask foreignkey
     *
     * @access private
     * @var mixed object ActivatedChainTask or integer
     */
    private $_LastTask = false;

    /**
     * _ActivatedChainOperation::getLastTask
     *
     * @access public
     * @return object ActivatedChainTask
     */
    public function getLastTask() {
        if (is_int($this->_LastTask) && $this->_LastTask > 0) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_LastTask = $mapper->load(
                array('Id'=>$this->_LastTask));
        }
        return $this->_LastTask;
    }

    /**
     * _ActivatedChainOperation::getLastTaskId
     *
     * @access public
     * @return integer
     */
    public function getLastTaskId() {
        if ($this->_LastTask instanceof ActivatedChainTask) {
            return $this->_LastTask->getId();
        }
        return (int)$this->_LastTask;
    }

    /**
     * _ActivatedChainOperation::setLastTask
     *
     * @access public
     * @param object ActivatedChainTask $value
     * @return void
     */
    public function setLastTask($value) {
        if (is_numeric($value)) {
            $this->_LastTask = (int)$value;
        } else {
            $this->_LastTask = $value;
        }
    }

    // }}}
    // Order string property + getter/setter {{{

    /**
     * Order int property
     *
     * @access private
     * @var integer
     */
    private $_Order = 0;

    /**
     * _ActivatedChainOperation::getOrder
     *
     * @access public
     * @return integer
     */
    public function getOrder() {
        return $this->_Order;
    }

    /**
     * _ActivatedChainOperation::setOrder
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setOrder($value) {
        if ($value !== null) {
            $this->_Order = (int)$value;
        }
    }

    // }}}
    // OrderInWorkOrder string property + getter/setter {{{

    /**
     * OrderInWorkOrder int property
     *
     * @access private
     * @var integer
     */
    private $_OrderInWorkOrder = 0;

    /**
     * _ActivatedChainOperation::getOrderInWorkOrder
     *
     * @access public
     * @return integer
     */
    public function getOrderInWorkOrder() {
        return $this->_OrderInWorkOrder;
    }

    /**
     * _ActivatedChainOperation::setOrderInWorkOrder
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setOrderInWorkOrder($value) {
        if ($value !== null) {
            $this->_OrderInWorkOrder = (int)$value;
        }
    }

    // }}}
    // TaskCount string property + getter/setter {{{

    /**
     * TaskCount int property
     *
     * @access private
     * @var integer
     */
    private $_TaskCount = 0;

    /**
     * _ActivatedChainOperation::getTaskCount
     *
     * @access public
     * @return integer
     */
    public function getTaskCount() {
        return $this->_TaskCount;
    }

    /**
     * _ActivatedChainOperation::setTaskCount
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setTaskCount($value) {
        if ($value !== null) {
            $this->_TaskCount = (int)$value;
        }
    }

    // }}}
    // Massified string property + getter/setter {{{

    /**
     * Massified int property
     *
     * @access private
     * @var integer
     */
    private $_Massified = 0;

    /**
     * _ActivatedChainOperation::getMassified
     *
     * @access public
     * @return integer
     */
    public function getMassified() {
        return $this->_Massified;
    }

    /**
     * _ActivatedChainOperation::setMassified
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setMassified($value) {
        if ($value !== null) {
            $this->_Massified = (int)$value;
        }
    }

    // }}}
    // State string property + getter/setter {{{

    /**
     * State int property
     *
     * @access private
     * @var integer
     */
    private $_State = 0;

    /**
     * _ActivatedChainOperation::getState
     *
     * @access public
     * @return integer
     */
    public function getState() {
        return $this->_State;
    }

    /**
     * _ActivatedChainOperation::setState
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setState($value) {
        if ($value !== null) {
            $this->_State = (int)$value;
        }
    }

    // }}}
    // PrestationFactured const property + getter/setter/getPrestationFacturedConstArray {{{

    /**
     * PrestationFactured int property
     *
     * @access private
     * @var integer
     */
    private $_PrestationFactured = 0;

    /**
     * _ActivatedChainOperation::getPrestationFactured
     *
     * @access public
     * @return integer
     */
    public function getPrestationFactured() {
        return $this->_PrestationFactured;
    }

    /**
     * _ActivatedChainOperation::setPrestationFactured
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPrestationFactured($value) {
        if ($value !== null) {
            $this->_PrestationFactured = (int)$value;
        }
    }

    /**
     * _ActivatedChainOperation::getPrestationFacturedConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getPrestationFacturedConstArray($keys = false) {
        $array = array(
            _ActivatedChainOperation::FACTURED_NONE => _("Not charged"), 
            _ActivatedChainOperation::FACTURED_FULL => _("Completely charged"), 
            _ActivatedChainOperation::FACTURED_PARTIAL => _("Partially charged")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // PrestationCommandDate datetime property + getter/setter {{{

    /**
     * PrestationCommandDate int property
     *
     * @access private
     * @var string
     */
    private $_PrestationCommandDate = 0;

    /**
     * _ActivatedChainOperation::getPrestationCommandDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getPrestationCommandDate($format = false) {
        return $this->dateFormat($this->_PrestationCommandDate, $format);
    }

    /**
     * _ActivatedChainOperation::setPrestationCommandDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPrestationCommandDate($value) {
        $this->_PrestationCommandDate = $value;
    }

    // }}}
    // InvoiceItem foreignkey property + getter/setter {{{

    /**
     * InvoiceItem foreignkey
     *
     * @access private
     * @var mixed object InvoiceItem or integer
     */
    private $_InvoiceItem = false;

    /**
     * _ActivatedChainOperation::getInvoiceItem
     *
     * @access public
     * @return object InvoiceItem
     */
    public function getInvoiceItem() {
        if (is_int($this->_InvoiceItem) && $this->_InvoiceItem > 0) {
            $mapper = Mapper::singleton('InvoiceItem');
            $this->_InvoiceItem = $mapper->load(
                array('Id'=>$this->_InvoiceItem));
        }
        return $this->_InvoiceItem;
    }

    /**
     * _ActivatedChainOperation::getInvoiceItemId
     *
     * @access public
     * @return integer
     */
    public function getInvoiceItemId() {
        if ($this->_InvoiceItem instanceof InvoiceItem) {
            return $this->_InvoiceItem->getId();
        }
        return (int)$this->_InvoiceItem;
    }

    /**
     * _ActivatedChainOperation::setInvoiceItem
     *
     * @access public
     * @param object InvoiceItem $value
     * @return void
     */
    public function setInvoiceItem($value) {
        if (is_numeric($value)) {
            $this->_InvoiceItem = (int)$value;
        } else {
            $this->_InvoiceItem = $value;
        }
    }

    // }}}
    // InvoicePrestation foreignkey property + getter/setter {{{

    /**
     * InvoicePrestation foreignkey
     *
     * @access private
     * @var mixed object Invoice or integer
     */
    private $_InvoicePrestation = 0;

    /**
     * _ActivatedChainOperation::getInvoicePrestation
     *
     * @access public
     * @return object Invoice
     */
    public function getInvoicePrestation() {
        if (is_int($this->_InvoicePrestation) && $this->_InvoicePrestation > 0) {
            $mapper = Mapper::singleton('Invoice');
            $this->_InvoicePrestation = $mapper->load(
                array('Id'=>$this->_InvoicePrestation));
        }
        return $this->_InvoicePrestation;
    }

    /**
     * _ActivatedChainOperation::getInvoicePrestationId
     *
     * @access public
     * @return integer
     */
    public function getInvoicePrestationId() {
        if ($this->_InvoicePrestation instanceof Invoice) {
            return $this->_InvoicePrestation->getId();
        }
        return (int)$this->_InvoicePrestation;
    }

    /**
     * _ActivatedChainOperation::setInvoicePrestation
     *
     * @access public
     * @param object Invoice $value
     * @return void
     */
    public function setInvoicePrestation($value) {
        if (is_numeric($value)) {
            $this->_InvoicePrestation = (int)$value;
        } else {
            $this->_InvoicePrestation = $value;
        }
    }

    // }}}
    // ActivatedChainTask one to many relation + getter/setter {{{

    /**
     * ActivatedChainTask 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_ActivatedChainTaskCollection = false;

    /**
     * _ActivatedChainOperation::getActivatedChainTaskCollection
     *
     * @access public
     * @return object Collection
     */
    public function getActivatedChainTaskCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChainOperation');
            return $mapper->getOneToMany($this->getId(),
                'ActivatedChainTask', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_ActivatedChainTaskCollection) {
            $mapper = Mapper::singleton('ActivatedChainOperation');
            $this->_ActivatedChainTaskCollection = $mapper->getOneToMany($this->getId(),
                'ActivatedChainTask');
        }
        return $this->_ActivatedChainTaskCollection;
    }

    /**
     * _ActivatedChainOperation::getActivatedChainTaskCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getActivatedChainTaskCollectionIds($filter = array()) {
        $col = $this->getActivatedChainTaskCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ActivatedChainOperation::setActivatedChainTaskCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setActivatedChainTaskCollection($value) {
        $this->_ActivatedChainTaskCollection = $value;
    }

    // }}}
    // Unavailability one to many relation + getter/setter {{{

    /**
     * Unavailability 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_UnavailabilityCollection = false;

    /**
     * _ActivatedChainOperation::getUnavailabilityCollection
     *
     * @access public
     * @return object Collection
     */
    public function getUnavailabilityCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ActivatedChainOperation');
            return $mapper->getOneToMany($this->getId(),
                'Unavailability', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_UnavailabilityCollection) {
            $mapper = Mapper::singleton('ActivatedChainOperation');
            $this->_UnavailabilityCollection = $mapper->getOneToMany($this->getId(),
                'Unavailability');
        }
        return $this->_UnavailabilityCollection;
    }

    /**
     * _ActivatedChainOperation::getUnavailabilityCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getUnavailabilityCollectionIds($filter = array()) {
        $col = $this->getUnavailabilityCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ActivatedChainOperation::setUnavailabilityCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setUnavailabilityCollection($value) {
        $this->_UnavailabilityCollection = $value;
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
        return 'ActivatedChainOperation';
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
            'Actor' => 'Actor',
            'Operation' => 'Operation',
            'Ghost' => 'ActivatedChainOperation',
            'ActivatedChain' => 'ActivatedChain',
            'OwnerWorkerOrder' => 'WorkOrder',
            'RealActor' => 'Actor',
            'ConcreteProduct' => 'ConcreteProduct',
            'RealConcreteProduct' => 'ConcreteProduct',
            'FirstTask' => 'ActivatedChainTask',
            'LastTask' => 'ActivatedChainTask',
            'Order' => Object::TYPE_INT,
            'OrderInWorkOrder' => Object::TYPE_INT,
            'TaskCount' => Object::TYPE_INT,
            'Massified' => Object::TYPE_INT,
            'State' => Object::TYPE_INT,
            'PrestationFactured' => Object::TYPE_CONST,
            'PrestationCommandDate' => Object::TYPE_DATETIME,
            'InvoiceItem' => 'InvoiceItem',
            'InvoicePrestation' => 'Invoice');
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
            'ActivatedChainOperation'=>array(
                'linkClass'     => 'ActivatedChainOperation',
                'field'         => 'Ghost',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'ActivatedChainTask'=>array(
                'linkClass'     => 'ActivatedChainTask',
                'field'         => 'ActivatedOperation',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetomany'
            ),
            'Unavailability'=>array(
                'linkClass'     => 'Unavailability',
                'field'         => 'ActivatedChainOperation',
                'ondelete'      => 'cascade',
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
}

?>