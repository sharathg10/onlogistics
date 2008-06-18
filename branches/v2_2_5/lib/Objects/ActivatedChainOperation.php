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

class ActivatedChainOperation extends _ActivatedChainOperation {
    // Constructeur {{{

    /**
     * ActivatedChainOperation::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // ActivatedChainOperation::GetPreviousOperation() {{{

    /**
     * Renvoie l'opération précédant cette opération
     *
     * @return ActivatedChainOperation L'instance précédente de
     * ActivatedChainOperation ou FALSE s'il n'en existe pas.
     */
    public function GetPreviousOperation() {
        $OwnerChain = $this->GetActivatedChain();
        Assert($OwnerChain instanceof ActivatedChain);
        if ($this->GetOrder() > 0) {
            $OwnerChainOperationCollection =
                $OwnerChain->GetActivatedChainOperationCollection();
            $OwnerChainOperationCollection->Sort('Order');
            return $OwnerChainOperationCollection->GetItem(
                $this->GetOrder() - 1);
        }
        return false;
    }

    // }}}
    // ActivatedChainOperation::GetNextOperation() {{{

    /**
     * Renvoie l'opération suivant cette opération
     *
     * @return ActivatedChainOperation L'instance suivante de
     * ActivatedChainOperation ou FALSE s'il n'en existe pas.
     */
    public function GetNextOperation() {
        $OwnerChain = $this->GetActivatedChain();
        $OwnerChainOperationCollection =
            $OwnerChain->GetActivatedChainOperationCollection();
        $OwnerChainOperationCollection->Sort('Order');
        if ($this->GetOrder() < $OwnerChainOperationCollection->GetCount()) {
            return $OwnerChainOperationCollection->GetItem(
                $this->GetOrder() + 1);
        }
        return false;
    }

    // }}}
    // ActivatedChainOperation::GetBegin() {{{

    /**
     * Renvoie le début de la planification pour l'opération courante
     *
     * @return integer Timestamp début de la planification de l'opération
     */
    public function GetBegin() {
        $FirstTask = $this->GetFirstTask();
        if (!($FirstTask instanceof ActivatedChainTask)) {
            return false;
        }
        return $FirstTask->GetBegin();
    }

    // }}}
    // ActivatedChainOperation::GetEnd() {{{

    /**
     * Renvoie la fin de la planification pour l'opération courante
     *
     * @return integer Timestamp correspondant à la fin de la planification de
     * l'opération
     */
    public function GetEnd() {
        $LastTask = $this->GetLastTask();
        if (!($LastTask instanceof ActivatedChainTask)) {
            return false;
        }
        return $LastTask->GetEnd();
    }

    // }}}
    // ActivatedChainOperation::GetStartActor() {{{

    /**
     * Renvoie l'acteur de départ (CountryCity) pour l'opération courante
     *
     * @return object Une instance de CountryCity
     */
    public function GetStartActor() {
        $FirstTask = $this->GetFirstTask();
        if (!($FirstTask instanceof ActivatedChainTask)) {
            return false;
        }
        $ActorSiteTransition = $FirstTask->GetActorSiteTransition();
        if (!($ActorSiteTransition instanceof ActorSiteTransition)) {
            return false;
        }
        if ($ActorSiteTransition->GetDepartureActor()) {
            return $ActorSiteTransition->GetDepartureActor();
        }
        return false;
    }

    // }}}
    // ActivatedChainOperation::GetEndActor() {{{

    /**
     * Renvoie l'acteur d'arrivée (CountryCity) pour l'opération courante
     *
     * @return object Une instance de CountryCity
     */
    public function GetEndActor() {
        $LastTask = $this->GetLastTask();
        if (!($LastTask instanceof ActivatedChainTask)) {
            return false;
        }
        $ActorSiteTransition = $LastTask->GetActorSiteTransition();
        if (!($ActorSiteTransition instanceof ActorSiteTransition)) {
            return false;
        }
        if ($ActorSiteTransition->GetArrivalActor()) {
            return $ActorSiteTransition->GetArrivalActor();
        }
        return false;
    }

    // }}}
    // ActivatedChainOperation::getName() {{{

    /**
     * Methode addon qui renvoie le nom du modèle de l'opération
     * @access public
     * @return void
     */
    public function getName() {
        $operation = $this->GetOperation();
        return $operation->getName();
    }

    // }}}
    // ActivatedChainOperation::getPackingNumberAbdWeight() {{{

    /**
     * Methode addon qui renvoie le nombre total de colis de la commande
     * associee a l'operation
     * @access public
     * @return array Tableau associatif:
     * array('PackingNumber' => $PackingNumber,
     *         'PackingWeight' => $PackingWeight,
     *         'isForecast' =>$isForecast);
     */
    public function getPackingNumberAndWeight() {
        require_once('Objects/WorkOrder.php');
        require_once('Objects/Command.const.php');
        $isForecast = false;  // Ce n'est pas une prevision
        $Filter = array();
        $CommandId = Tools::getValueFromMacro($this, '%ActivatedChain.CommandItem()[#].Command.Id%');
        $Command = Object::load('Command', $CommandId);
        
        if (in_array($Command->getState(), array(Command::ACTIVEE, Command::BLOCAGE_CDE)) ) {
            $isForecast = true;  // C'est une prevision
        }
        $PackingNumber = $PackingWeight = 0;
        $WorkOrder = $this->getOwnerWorkerOrder();
        if (WorkOrder::WORK_ORDER_STATE_FULL == $WorkOrder->getState()) {
            // si le WO a ete cloture, on doit recuperer la date de cloture
            // pour filtrer les LocationExecutedMovt
            $dateInf = $WorkOrder->getClotureDate();
        }
        else {
            // Toute date de LEM sera forcement > a ceci
            $dateInf = '0000-00-00 00:00:00';
        }
        // si l'aco posséde une tache génératrice de box
        $ackCol = $this->getActivatedChainTaskCollection();
        $ackColCount = $ackCol->getCount();
        for ($i=0 ; $i<$ackColCount ; $i++) {
            $ack = $ackCol->getItem($i);
            
            if($boxCol= $ack->getGroupableBoxCollection()) {
                $boxCount = $boxCol->getCount();
                for ($j=0 ; $j<$boxCount ; $j++) {
                    $box = $boxCol->getItem($j);
                    $parentBox = $box->getParentBox();
                    $PackingNumber ++;
                    $PackingWeight += $parentBox->getWeight();
                }    
            }
        }
        // si les infos n'ont pas été récupérées à partir dex box
        if($PackingNumber == 0) {
            $ach = $this->getActivatedChain();
            $CommandItemCollection = $ach->getCommandItemCollection();
            $count = $CommandItemCollection->getCount();
            for($i = 0; $i < $count; $i++) {
                $CommandItem = $CommandItemCollection->getItem($i);
                /* Patch en attendant mieux, pour eviter un plantage!!!!! */
                //if (!method_exists($CommandItem, 'getActivatedMovement')) {
                if($CommandItem instanceof ChainCommandItem || $CommandItem instanceof ProductCommandItem) {
                    $PackingNumber += $CommandItem->getQuantity();
                    $PackingWeight += $CommandItem->getWeight();
                    continue;
                }
                /* End Patch */
                // On prend les infos dans CommandItem: ce sont des previsions
                if ($isForecast) {
                    $Product =  $CommandItem->getProduct();
                    $PackingNumber += $Product->PackagingUnitNumber($CommandItem->getQuantity());
                    $PackingWeight += $CommandItem->getQuantity() * $Product->getSellUnitWeight();
                    unset($Product);
                }
                else {
                    $ActivatedMovement = $CommandItem->getActivatedMovement();
                    $ExecutedMovement = $ActivatedMovement->getExecutedMovement();
                    if (!Tools::isEmptyObject($ExecutedMovement)) {
                        // Calcul de la Qte mouvementee:
                        // Les LEM non annules, non annulateurs
                        // et dont date du LEM >= date de cloture de l'OT
                        $filter = new FilterComponent();
                        $filter->setItem(new FilterRule('Cancelled',
                                                      FilterRule::OPERATOR_EQUALS,
                                                     0));
                         $filter->setItem(new FilterRule('CancelledMovement',
                                                      FilterRule::OPERATOR_EQUALS,
                                                     0));
                        $filter->setItem(new FilterRule('Date',
                                                         FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                                                        $dateInf));
                         $filter->operator = FilterComponent::OPERATOR_AND;
                        $LEMCollection = $ExecutedMovement->getLocationExecutedMovementCollection($filter);
                        if (!Tools::isEmptyObject($LEMCollection)) {
                            for($j = 0; $j < $LEMCollection->getCount(); $j++){
                                $LEM = $LEMCollection->getItem($j);
                                $Product =  $LEM->getProduct();
                                $PackingNumber += $Product->PackagingUnitNumber($LEM->getQuantity());
                                $PackingWeight += $LEM->getQuantity() * $Product->getSellUnitWeight();
                                unset($LEM, $Product);
                            }
                        }
                        else continue;
                    }
                    unset($ActivatedMovement, $ExecutedMovement, $LEMCollection);
                }
    
                unset($CommandItem);
            }
        }
        return array('PackingNumber' => $PackingNumber,
                     'PackingWeight' => $PackingWeight,
                     'isForecast' => $isForecast);
    }

    // }}}
    // ActivatedChainOperation::getActivatedMovementCollection() {{{

    /**
     * Renvoie la Collection des ActivatedMovements lies
     * @access public
     * @return object Collection
     */
    public function getActivatedMovementCollection() {
        $Collection = new Collection(); // La collection qui sera retournee
        $CommandId = Tools::getValueFromMacro(
                $this, '%ActivatedChain.CommandItem()[#].Command.Id%');
        $Command = Object::load('Command', $CommandId);

        $CommandItemCollection = $Command->getCommandItemCollection();
        $count = $CommandItemCollection->getCount();
        for($i = 0; $i < $count; $i++) {
            $CommandItem = $CommandItemCollection->getItem($i);
            if (!method_exists($CommandItem, 'getActivatedMovement')) {
                continue;
            }

            $ActivatedMovement = $CommandItem->getActivatedMovement();
            if (!empty($ActivatedMovement)) {
                $Collection->setItem($ActivatedMovement);	
            }
            
            unset($ActivatedMovement, $CommandItem);
        }
        return $Collection;
    }

    // }}}
    // ActivatedChainOperation::getACODuration() {{{

    /**
     * Calcul la durée de l'ACO en sommant les ACK.Duration.
     *
     * @return int la durée de l'ACO en secondes
     */
    public function getACODuration() {
        //calcul durée de l'aco
        $duration = 0;
        $ackCol = $this->getActivatedChainTaskCollection();
        $count = $ackCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $ack = $ackCol->getItem($i);
            $duration += $ack->getDuration();
        }
        return $duration;
    }

    // }}}
    // ActivatedChainOperation::findPrestation() {{{
    
    /**
     * findPrestation 
     * 
     * @param mixed $customerId 
     * @access public
     * @return void
     */
    public function findPrestation($customerId) {
        $filter = array(
            SearchTools::newFilterComponent('Operation', '', 'Equals', $this->getOperationId(), 1),
            SearchTools::newFilterComponent('Actor', 'PrestationCustomer().Actor.Id', 'Equals', $customerId, 1, 'Prestation'),
            SearchTools::newFilterComponent('Active', 'Active', 'Equals', 1, 1),
            SearchTools::newFilterComponent('Facturable', 'Facturable', 'Equals', 1, 1));
        $filter = SearchTools::filterAssembler($filter);
        $mapper = Mapper::singleton('Prestation');
        $prestation = $mapper->load($filter);
        return $prestation;
    }
    
    // }}}
    // ActivatedChainOperation::dupplicate() {{{     
     
    /**     
     * Permet de creer une nouvelle ActivatedChainOperation qui est un      
     * duplicat, ainsi qu'un duplicat de chacune des ses taches.    
     * @access public   
     * @return void     
     */     
    public function dupplicate() {   
        $ACOMapper = Mapper::singleton('ActivatedChainOperation');
        // le dupplicat
        $NewActivatedChainOperation = Object::load('ActivatedChainOperation');
        $AttributeListForNewValue = array('Id', 'Ghost', 'OwnerWorkerOrder', 
            'FirstTask', 'LastTask', 'Massified', 'State', 'OrderInWorkOrder');
        foreach($this->getProperties() as $field => $type) {
            if (!in_array($field, $AttributeListForNewValue)) {
                $setter = 'set' . $field;
                $getter = 'get' . $field;
                if (method_exists($this, $setter)) {
                    $NewActivatedChainOperation->$setter($this->$getter());
                }
            }
        }
        $NewActivatedChainOperation->setId($ACOMapper->generateId());
        $NewActivatedChainOperation->setOrderInWorkOrder(0);
        $NewActivatedChainOperation->setMassified(0);
        $NewActivatedChainOperation->setState(0);
        
        $ActivatedChainTaskCollection = $this->getActivatedChainTaskCollection();
        if ($ActivatedChainTaskCollection instanceof Collection && 
            ($ActivatedChainTaskCollection->getCount() > 0)) {
            for($i = 0; $i < $ActivatedChainTaskCollection->getCount(); $i++){
                $ActivatedChainTask = $ActivatedChainTaskCollection->getItem($i);
                $NewActivatedChainTask = $ActivatedChainTask->dupplicate(
                    $NewActivatedChainOperation);
                if ($this->getFirstTaskID() == $ActivatedChainTask->getId()) {
                    $NewActivatedChainOperation->setFirstTask($NewActivatedChainTask);
                }
                if ($this->getLastTaskID() == $ActivatedChainTask->getId()) {
                    $NewActivatedChainOperation->setLastTask($NewActivatedChainTask);
                }
                unset($ActivatedChainTask, $NewActivatedChainTask);
            }
        }
        $NewActivatedChainOperation->save();
        return $NewActivatedChainOperation;
    }  // end function
    
    // }}}
    // ActivatedChainOperation::deleteInvoice() {{{     
     
    /**     
     * Fait les MAJ necessaires lorsque on supprime une facture associee
     * @access public   
     * @return void     
     */     
    public function updateWhenDeleteInvoice() {   
        require_once('Objects/Task.inc.php');
        $this->setPrestationFactured(false);
        $this->setPrestationCommandDate('0000-00-00 00:00:00');
        $this->save();

        // on passe également les box à non facturé si il y a une ack de 
        // regroupement précédent une aco de transport, ou les lem à non 
        // facturé si il y a une tache de sortie de stock
        $ackTransport = $this->getLastTask();
        if(!($ackTransport instanceof ActivatedChainTask)) {
            continue;
        }
        if(isTransportTask($ackTransport->getTaskId())) {
            $ackTransport = $ackTransport->getPreviousTask(array(), 'isTransportTask');
        }
        // cherche une tache de regroupement ou de sortie de stock avant le transport
        if($ack = $ackTransport->getPreviousTask(array(), 'isGroupingOrStockExitTask')) {
            if(isGroupingTask($ack->getTask())) {
                $filter = array(
                    SearchTools::newFilterComponent('ActivatedChainTask', 'ActivatedChainTask().Id',
                        'Equals', $ack->getId(), 1, 'Box'),
                    SearchTools::newFilterComponent('PrestationFactured', '', 'Equals', 1, 1));
                $filter = SearchTools::filterAssembler($filter);
                $boxCol = Object::loadCollection('Box', $filter);
                foreach($boxCol as $box) {
                    $box->setPrestationFactured(false);
                    // et le $box->setInvoicePrestation(0) est fait par le fw
                    $box->save();
                }
            } elseif(getTaskId($ack) == TASK_STOCK_EXIT) {
                $filter = array(
                    SearchTools::newFilterComponent('LEM',
                        'ExecutedMovement.ActivatedMovement.ActivatedChainTask.Id',
                        'Equals', $ack->getId(), 1),
                    SearchTools::newFilterComponent('TransportPrestationFactured', '',
                        'Equals', 1, 1)
                    );
                $filter = SearchTools::filterAssembler($filter);
                $lemCol = Object::loadCollection('LocationExecutedMovement', $filter, 
                        array(), array(), 0, 1, false, true);  // NO CACHE!!!!!
                foreach($lemCol as $lem) {
                    $lem->setTransportPrestationFactured(false);
                    $lem->save();
                }
            }
        }
    }  // end function
    
    // }}}

}

?>