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

class ActivatedChainTask extends _ActivatedChainTask {
    // Constructeur {{{

    /**
     * ActivatedChainTask::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // ActivatedChainTask::getNextTask() {{{

    /**
     * Renvoie la tâche suivante de la tâche courante, en pouvant préciser un
     * type de TriggerMode particulier
     *
     * @param array $validStates Tableau contenant les modes de déclenchements
     * valides pour la récupération de la tâche précédente.
     * @return ActivatedChainTask La tâche suivante ou FALSE s'il s'agit de la
     * dernière tâche, ou si aucune tâche correspondant au modes valides
     * n'est disponible.
     */
    public function getNextTask($validStates = array(), $acceptanceRule = '') {
        require_once('Task.inc.php');
        if (empty($validStates)) {
            $validStates = array(ActivatedChainTask::TRIGGERMODE_MANUAL, ActivatedChainTask::TRIGGERMODE_AUTO,
                ActivatedChainTask::TRIGGERMODE_TEMP);
        }
        $result = false;
        $findNext = false;
        $OwnerOperation = $this->GetActivatedOperation();
        $OwnerOperationTaskCollection =
            $OwnerOperation->GetActivatedChainTaskCollection();
        $OwnerOperationTaskCollection->Sort('Order');
        if ($this->GetOrder() < $OwnerOperationTaskCollection->GetCount()-1) {
            $result = $OwnerOperationTaskCollection->GetItem(
                $this->GetOrder() + 1);
        } else {
            $NextOperation = $OwnerOperation->GetNextOperation();
            if ($NextOperation instanceof ActivatedChainOperation) {
                $NextOperationTaskCollection =
                    $NextOperation->GetActivatedChainTaskCollection();
                $NextOperationTaskCollection->Sort('Order');
                if (false != $NextOperationTaskCollection) {
                    $result = $NextOperationTaskCollection->GetItem(0);
                }
            }
        }
        if (false != $result) {
            $findNext = false;
            if (false == in_array($result->GetTriggerMode(), $validStates)) {
                $findNext = true;
            } else {
                if (!empty($acceptanceRule)) {
                    assert(function_exists($acceptanceRule));

                    if (!$acceptanceRule($result)) {
                        $findNext = true;
                    }
                }
            }
        }
        if (true == $findNext) {
            $tmp = $result;

            unset($result);

            $result = $tmp->GetNextTask($validStates, $acceptanceRule);
        }
        return $result;
    }

    // }}}
    // ActivatedChainTask::getPreviousTask() {{{

    /**
     * Renvoie la tâche précédente de la tâche courante, en pouvant préciser
     * un type de TriggerMode particulier
     *
     * @param array $validStates Tableau contenant les modes de déclenchements
     * valides pour la récupération de la tâche précédente.
     * @return ActivatedChainTask La tâche précédente ou FALSE s'il s'agit de
     * la première tâche, ou si aucune  tâche correspondant au modes valides
     * n'est disponible.
     */
    public function getPreviousTask($validStates = array(), $acceptanceRule = '') {
        require_once('Task.inc.php');
        if (empty($validStates)) {
            $validStates = array(ActivatedChainTask::TRIGGERMODE_MANUAL, ActivatedChainTask::TRIGGERMODE_AUTO,
                ActivatedChainTask::TRIGGERMODE_TEMP);
        }
        $result = false;
        $OwnerOperation = $this->GetActivatedOperation();
        if ($this->GetOrder() > 0) {
            $OwnerOperationTaskCollection =
                $OwnerOperation->GetActivatedChainTaskCollection();
            $OwnerOperationTaskCollection->Sort('Order');
            $result = $OwnerOperationTaskCollection->GetItem(
                $this->GetOrder() - 1);
        } else {
            $PreviousOperation = $OwnerOperation->GetPreviousOperation();
            if ($PreviousOperation instanceof ActivatedChainOperation) {
                $PreviousOperationTaskCollection =
                    $PreviousOperation->GetActivatedChainTaskCollection();
                Assert($PreviousOperationTaskCollection);
                $PreviousOperationTaskCollection->Sort('Order');
                $result = $PreviousOperationTaskCollection->GetItem(
                    $PreviousOperationTaskCollection->GetCount() - 1);
            }
        }

        if (false != $result) {
            if (false == in_array($result->GetTriggerMode(), $validStates)) {
                $result = $result->GetPreviousTask($validStates,
                    $acceptanceRule);
            } else {
                require_once("Objects/Task.inc.php");
                if (!empty($acceptanceRule)) {
                    assert(function_exists($acceptanceRule));
                    if (!$acceptanceRule($result)) {
                        $result = $result->GetPreviousTask($validStates,
                            $acceptanceRule);
                    }
                }
            }
        }
        return $result;
    }

    // }}}
    // ActivatedChainTask::toString() {{{

    /**
     *
     * @return string
     */
    public function toString() {
        $Task = $this->getTask();
        $taskType = method_exists($Task, 'toString')?$Task->toString():'';
        return sprintf('Task %s n°%d, order = %d (%s %0.2f&euro;)',
            $taskType, $this->GetId(), $this->GetOrder(),
            I18N::formatDuration($this->GetDuration()),
            $this->GetCost());
    }

    // }}}
    // ActivatedChainTask::getToStringAttribute() {{{

    /**
     * Retourne le nom des attributs représentant l'objet, pointés par toString()
     *
     * @static
     * @return array of strings
     * @access public
     */
    public function getToStringAttribute() {
        return array('Task', 'Order', 'Duration', 'Cost');
    }

    // }}}
    // ActivatedChainTask::GetMassifiedTaskDuration() {{{

    /**
     * Renvoie la durée effective d'une tâche en tenant compte de la
     * massification éventuelle.
     *
     * @return integer Nombre de seconde correspondant à la durée effective
     * de la tâche.
     */
    public function GetMassifiedTaskDuration() {
        $GhostTask = $this->GetGhost();
        if ($GhostTask instanceof ActivatedChainTask) {
            // Il existe une tâche de massification, on prends sa durée
            if ($GhostTask->GetDuration() != 0) {
                return $GhostTask->GetDuration();
            }
        }
        // Sinon il s'agit d'une tâche non massifiée, on prends sa durée
        return $this->GetDuration();
    }

    // }}}
    // ActivatedChainTask::GetMassifiedInteruptible() {{{

    /**
     * ActivatedChainTask::GetMassifiedInteruptible()
     *
     * @return
     */
    public function GetMassifiedInteruptible() {
        $GhostTask = $this->GetGhost();
        if ($GhostTask instanceof ActivatedChainTask) {
             // Il existe une tâche de massification, on prends son état
            return $GhostTask->GetInteruptible();
        }
        // Sinon il s'agit d'une tâche non massifiée, on prends son état
        return $this->GetInteruptible();
    }

    // }}}
    // ActivatedChainTask::GetMassifiedTriggerDelta() {{{

    /**
     * ActivatedChainTask::GetMassifiedTriggerDelta()
     *
     * @return
     */
    public function GetMassifiedTriggerDelta() {
        $GhostTask = $this->GetGhost();
        if ($GhostTask instanceof ActivatedChainTask) {
             // Il existe une tâche de massification, on prends son delta
            return $GhostTask->GetTriggerDelta();
        }
        // Sinon il s'agit d'une tâche non massifiée
        return $this->GetTriggerDelta();
    }

    // }}}
    // ActivatedChainTask::GetPreviousPrincipalTask() {{{

    /**
     * ActivatedChainTask::GetPreviousPrincipalTask()
     *
     * @return
     */
    public function GetPreviousPrincipalTask() {
        return $this->GetPreviousTask(array(ActivatedChainTask::TRIGGERMODE_MANUAL),
            'isPrincipalTask');
    }

    // }}}
    // ActivatedChainTask::GetPreviousMainTask() {{{

    /**
     * ActivatedChainTask::GetPreviousMainTask()
     *
     * @param boolean $TaskType
     * @return
     */
    public function GetPreviousMainTask($TaskType = false) {
        require_once("Objects/Task.inc.php");
        $CurrentTask = $this->GetPreviousTask();
        while (false != $CurrentTask) {
            if (false == IsEditionTask($CurrentTask)) {
                if ((false == $TaskType) ||
                    $TaskType == $CurrentTask->GetTaskId()) {
                    return $CurrentTask;
                }
            }
            unset($PrevTask);
            $PrevTask = $CurrentTask->GetPreviousTask();
            unset($CurrentTask);
            $CurrentTask = $PrevTask;
        }
        return false;
    }

    // }}}
    // ActivatedChaintask::GetNextMainTask() {{{

    /**
     * ActivatedChainTask::GetNextMainTask()
     *
     * @param boolean $TaskType
     * @return
     */
    public function GetNextMainTask($TaskType = false) {
        require_once("Objects/Task.inc.php");
        $CurrentTask = $this->GetNextTask();
        while (false != $CurrentTask) {
            if (false == IsEditionTask($CurrentTask)) {
                if ((false == $TaskType) ||
                    $TaskType == $CurrentTask->GetTaskId()) {
                    return $CurrentTask;
                }
            }
            unset($NextTask);
            $NextTask = $CurrentTask->GetNextTask();
            unset($CurrentTask);
            $CurrentTask = $NextTask;
        }
        return false;
    }

    // }}}
    // ActivatedChainTask::GetNextTaskFromRule() {{{

    /**
     * ActivatedChainTask::GetNextTaskFromRule()
     *
     * @param string $acceptanceRule
     * @return
     */
    public function GetNextTaskFromRule($acceptanceRule = '') {
        return $this->GetNextTask(array(), $acceptanceRule);
    }

    // }}}
    // ActivatedChainTask::GetPreviousTaskFromRule() {{{

    /**
     * ActivatedChainTask::GetPreviousTaskFromRule()
     *
     * @param string $acceptanceRule
     * @return
     */
    public function GetPreviousTaskFromRule($acceptanceRule = '') {
        return $this->GetPreviousTask(array(), $acceptanceRule);
    }

    // }}}
    // ActivatedChainTask;;GetNextTransportTask() {{{

    /**
     * ActivatedChainTask::GetNextTranportTask()
     *
     * @return
     */
    public function GetNextTranportTask() {
        return $this->GetNextTaskFromRule('IsTransportTask');
    }

    // }}}
    // ActivatedChainTask::getName() {{{

    /**
     * Methode addon qui renvoie le nom du modèle de la tache
     *
     * @access public
     * @return void
     */
    public function getName() {
        $task = $this->GetTask();
        return $task->getName();
    }

    // }}}
    // ActivatedChainTask::updateRealConcreteProduct() {{{

    /**
     * Met a jour le RealConcreteProduct de l'ACO associee lorsque c'est une
     * ActivatedChainTask de type VOL, Sinon retourne FALSE.
     * Si ce n'est pas le ConcreteProduct prevu a la Commande, on met aussi a jour ce dernier.
     * Si un des potentiels des ConcreteProduct devient <0, on ne met rien a jour
     * et on retourne FALSE.
     * A appeler dans une transaction
     * @access public
     * @return boolean
     */
    public function updateRealConcreteProduct(){
        if ('VOL' != $this->getName()) {
            return false;
        }
        $ActivatedChainOperation = $this->getActivatedOperation();
        $ConcretePdt = $ActivatedChainOperation->getRealConcreteProduct();
        $ActivatedChainTaskDetail = $this->getActivatedChainTaskDetail();
        if (Tools::isEmptyObject($ActivatedChainTaskDetail)) {
            return false;
        }
        $technicalHour = $ActivatedChainTaskDetail->getTechnicalHour();
        $cycleCellule = $ActivatedChainTaskDetail->getCycleCellule();
        $landingNumber = $ActivatedChainTaskDetail->getLandingNumber();

        // Les attributs, selon s'ils sont impactes par TechnicalHour,
        // CycleCellule, ou LandingNumber
        $UpdatedByTHattributes = array('RealHourSinceNew', 'RealHourSinceOverall',
                                       'RealHourSinceRepared');
        $UpdatedByCCattributes = array('RealCycleSinceNew', 'RealCycleSinceOverall',
                                       'RealCycleSinceRepared');
        // LN: Landing Number
        $UpdatedByLNattributes = array('RealLandingSinceNew', 'RealLandingSinceOverall',
                                       'RealLandingSinceRepared');
        /* MAJ potentiels reels du ConcretePdt et de son contenu */
        $updated = $ConcretePdt->updatePotentials(
                array(array('attributes' => $UpdatedByTHattributes,
                            'value' => $technicalHour),
                      array('attributes' => $UpdatedByCCattributes,
                            'value' => $cycleCellule),
                      array('attributes' => $UpdatedByLNattributes,
                            'value' => $landingNumber)
                    )
                );
        if (true !== $updated) {
            return $updated;
        }

        // Duree prevue a comparer avec la duree reelle (TechnicalHour)
        $duration = Tools::getValueFromMacro($this,
                '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Duration%');
        $initialDuration = DateTimeTools::getHundredthsOfHour($duration);

        /* MAJ potentiels virtuels du ConcretePdt et de son contenu  */
        // On regarde si l'appareil utilise est celui prevu
        $initialConcretePdtId = Tools::getValueFromMacro($this,
                '%ActivatedOperation.ConcreteProduct.Id%');
        $virtualAttributes = array('VirtualHourSinceNew', 'VirtualHourSinceOverall');

        if ($ConcretePdt->getId() == $initialConcretePdtId) {  // C'est le meme
            $updated = $ConcretePdt->updatePotentials(
                    array(0 => array('attributes' => $virtualAttributes,
                                     'value' => $technicalHour - $initialDuration)
                        )
                    );
            if (true !== $updated) {
                return $updated;
            }
        }
        else {  // Ce n'est pas le ConcreteProduct prevu: il faut mettre les 2 a jour
            $initialConcretePdt = Object::load('AeroConcreteProduct',
                                                            $initialConcretePdtId);
            // Si un potentiel se retrouve negatif, on annule tout
            $initCPupdated = $initialConcretePdt->updatePotentials(
                    array(array('attributes' => $virtualAttributes,
                                     'value' => -$initialDuration))
                    );
            $CPupdated = $ConcretePdt->updatePotentials(
                    array(array('attributes' => $virtualAttributes,
                                     'value' => $technicalHour))
                    );
            if (true !== $initCPupdated || true !== $CPupdated) {
                return max((int)$initCPupdated, (int)$CPupdated);
            }
        }
        return true;
    }

    // }}}
    // ActivatedChainTask::reUpdateReakConcreteProduct() {{{

    /**
     * Met a jour le RealConcreteProduct de l'ACO associee lorsque c'est une
     * ActivatedChainTask de type VOL, Sinon retourne FALSE.
     * A utiliser lors d'une modification d'une precedente validation de vol.
     * @param $initConcretePdt correspond au RealConcreteProduct de l'ACO associee
     * @param $AeroConcretePdt le AeroConcreteProduct finalement utilise
     * @param $initActivatedChainTaskDetail le ACKDetail initial, contenant
     * les donnees deja validees
     * @access public
     * @return boolean
     */
    public function reUpdateRealConcreteProduct($initConcretePdt, $AeroConcretePdt,
                                         $initActivatedChainTaskDetail) {
        if ('VOL' != $this->getName()) {
            return false;
        }
        $initTechnicalHour = $initActivatedChainTaskDetail->getTechnicalHour();
        $initCycleCellule = $initActivatedChainTaskDetail->getCycleCellule();
        $initLandingNumber = $initActivatedChainTaskDetail->getLandingNumber();

        $ActivatedChainTaskDetail = $this->getActivatedChainTaskDetail();
        $technicalHour = $ActivatedChainTaskDetail->getTechnicalHour();
        $cycleCellule = $ActivatedChainTaskDetail->getCycleCellule();
        $landingNumber = $ActivatedChainTaskDetail->getLandingNumber();
        $THDifference = $technicalHour - $initTechnicalHour;
        $CCDifference = $cycleCellule - $initCycleCellule;
        $LNDifference = $landingNumber - $initLandingNumber;
        // Les attributs, selon s'ils sont impactes par TechnicalHour,
        // CycleCellule, ou LandingNumber
        $UpdatedByTHattributes = array('RealHourSinceNew', 'RealHourSinceOverall',
                                       'RealHourSinceRepared', 'VirtualHourSinceNew',
                                       'VirtualHourSinceOverall');
        $UpdatedByCCattributes = array('RealCycleSinceNew', 'RealCycleSinceOverall',
                                       'RealCycleSinceRepared');
        $UpdatedByLNattributes = array('RealLandingSinceNew', 'RealLandingSinceOverall',
                                       'RealLandingSinceRepared');
        // Si on ne change pas de ConcreteProduct
        if ($initConcretePdt->getId() == $AeroConcretePdt->getId()) {
            $updated = $AeroConcretePdt->updatePotentials(
                    array(array('attributes' => $UpdatedByTHattributes,
                                     'value' => $THDifference),
                          array('attributes' => $UpdatedByCCattributes,
                                     'value' => $CCDifference),
                          array('attributes' => $UpdatedByLNattributes,
                                     'value' => $LNDifference))
                    );
            if (true !== $updated) {
                return $updated;
            }
        }
        else { //  Si on change de ConcreteProduct
            // MAJ de l'ancien ConcreteProduct
            $updated = $initConcretePdt->updatePotentials(
                    array(array('attributes' => $UpdatedByTHattributes,
                                     'value' => -$initTechnicalHour),
                          array('attributes' => $UpdatedByCCattributes,
                                     'value' => -$initCycleCellule),
                          array('attributes' => $UpdatedByLNattributes,
                                     'value' => -$initLandingNumber))
                    );
            if (true !== $updated) {
                return $updated;
            }

            // MAJ du nouveau ConcreteProduct
            $updated = $AeroConcretePdt->updatePotentials(
                    array(array('attributes' => $UpdatedByTHattributes,
                                     'value' => $technicalHour),
                          array('attributes' => $UpdatedByCCattributes,
                                     'value' => $cycleCellule),
                          array('attributes' => $UpdatedByLNattributes,
                                     'value' => $landingNumber))
                    );
            if (true !== $updated) { // impossible normalement, car on ajoute (+)
                return $updated;
            }
        }
        return true;
    }

    // }}}
    // ActivatedChainTask::flightInvoice() {{{

    /**
     * Cree une facture pour un vol.
     *
     * @return object Alert or false
     */
    public function flightInvoice(){
        $CommercialDuration = Tools::getValueFromMacro($this,
                '%ActivatedChainTaskDetail.RealCommercialDuration%');
        // Cout lie a la prestation
        $PrestationId = Tools::getValueFromMacro($this, '%ActivatedOperation.Operation.Prestation.Id%');
        $Prestation = Object::load('Prestation', $PrestationId);

        $params = array('time' => $CommercialDuration/100);
        // pour les coûts liés aux acteurs
        $InstructorId = Tools::getValueFromMacro($this, '%ActivatedOperation.realActor.Id%');
        $CommandId = Tools::getValueFromMacro($this,
            '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Id%');
        $CourseCommand = Object::load('CourseCommand', $CommandId);
        $CustomerId = $CourseCommand->getCustomerId();
        $actorIds = array($InstructorId, $CustomerId);
        // coût des products
        $ProductId = Tools::getValueFromMacro($this, '%ActivatedOperation.RealConcreteProduct.Product.Id%');
        $productIds = array($ProductId => 1);
        // coût des concreteproducts
        $AeroConcretePdtId = Tools::getValueFromMacro($this, '%ActivatedOperation.RealConcreteProduct.Id%');
        $concreteProductIds = array($AeroConcretePdtId => 1);
        // coût des flytype
        $FlyTypeId = Tools::getValueFromMacro($this, '%ActivatedOperation.RealConcreteProduct.Product.FlyType.Id%');
        $flyTypeIds = array($FlyTypeId=>1);
        $totalHT = $Prestation->getPrestationPrice($params,
            $concreteProductIds, $flyTypeIds, $productIds, $actorIds);

        $tva = $Prestation->getTVA();
        $TVARate = 0;
        if ($tva instanceof TVA) {
            $TVARate = $tva->getRate();
        }

        $Invoice = Object::load('Invoice');
        $InvoiceMapper = Mapper::singleton('Invoice');
        $InvoiceId = $InvoiceMapper->generateId();
        $Invoice->SetId($InvoiceId);

        require_once('InvoiceItemTools.php');
        $Invoice->setDocumentNo(GenerateDocumentNo('FC', 'AbstractDocument', $InvoiceId)); // facture client
        $Invoice->setEditionDate(date("Y-m-d H-i-s"));
        $Invoice->setCommand($CourseCommand);
        $Invoice->setCommandType($CourseCommand->getInvoiceCommandType());
        $Invoice->setAccountingTypeActor($CourseCommand->getDestinatorId());
        $Invoice->setSupplierCustomer($CourseCommand->getSupplierCustomer());
        $Invoice->setCurrency($CourseCommand->getCurrency());
        $Invoice->setTotalPriceHT($totalHT);
        $Invoice->setTotalPriceTTC($totalHT * (1 + $TVARate/100));
        //on renseigne la date de reglement
        SavePaymentDate($Invoice, $CourseCommand);
        $Invoice->setToPay($totalHT * (1 + $TVARate/100));
        // mise à jour de l'encours courant
        require_once('ProductCommandTools.php');
        $Alert = CommandBlockage($CourseCommand, $Invoice);
        $Invoice->save();
        $InvoiceItem = Object::load('InvoiceItem');
        $InvoiceItem->setQuantity($CommercialDuration / 100);  /// des Heures, pas des 100e d'H
        $InvoiceItem->setTva($tva);
        $InvoiceItem->setUnitPriceHT($totalHT);
        $InvoiceItem->setInvoice($Invoice);
        $InvoiceItem->setPrestation($Prestation);
        $InvoiceItem->save();
        return $Alert;
    }

    // }}}
    // ActivatedChainTask::getRoleActorDuringFlight() {{{

    /**
     * Retourne le pilote oubien le copilote du vol, selon le param passe,
     * pour une tache de vol
     *
     * @param $role string: 'Pilote' ou 'Copilote'
     * @access public
     * @return void
     */
    public function getRoleActorDuringFlight($role){
        require_once("Objects/Task.inc.php");
          // ce doit etre un vol et on verifie le param passe
        if (!isFlightTask($this) || !in_array($role, array('Pilote', 'Copilote'))) {
            return false;
        }
        $roleSeatArray = ($role == 'Pilote')?getPilotSeatArray():array(ActivatedChainTaskDetail::COPILOTE);
        $actDetail = $this->getActivatedChainTaskDetail();
        // Si l'instructeur est pilote
        if (in_array($actDetail->getInstructorSeat(), $roleSeatArray)) {
            $InstructorId = Tools::getValueFromMacro($this, '%ActivatedOperation.realActor.id%');
            $Instructor = Object::load('AeroInstructor', $InstructorId);
            return $Instructor;
        }
        // Si le client est pilote
        elseif (in_array($actDetail->getCustomerSeat(), $roleSeatArray)) {
            $CommandId = Tools::getValueFromMacro($this, '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Id%');
            $Command = Object::load('CourseCommand', $CommandId);
            $Customer = $Command->getCustomer();
            return $Customer;
        }
        return false;
    }

    // }}}
    // ActivatedChainTask::getGroupableBoxCollection() {{{

    /**
     * Retourne toutes les Box qui peuvent être regroupées dans cette tâche, si
     * aucune Box n'est regroupable, ou si la tache courante n'est pas une tâche
     * créatrice de Box, la fonction retourne false.
     * Les box regroupables sont les box de toutes les taches précédant la tâche
     * en cours qui n'ont pas été regroupées (cad qui ont ParentBox à 0) + les
     * box de niveau 1 (non liées à une ACK) qui n'ont pas été regroupées.
     *
     * @access public
     * @return mixed object Collection (collection de Box) ou false
     */
    public function getGroupableBoxCollection(){
        // si on est pas dans une tâche créatrice de box on retourne false
        $task = $this->getTask();
        if (!$task->getIsBoxCreator()) {
            return false;
        }
        // on récupère l'id de la chaîne contenant la tâche
        $achID = Tools::getValueFromMacro($this, '%ActivatedOperation.ActivatedChain.Id%');
        // on merge toutes les box de niveau inférieur qui n'auraient pas été
        // déjà regroupées (qui ont ParentBox à 0) et qui sont liées à la
        // chaîne en cours
        $mapper = Mapper::singleton('Box');
        $filter = new FilterComponent(
            new FilterComponent(
                new FilterRule(
                    'ActivatedChain',
                    FilterRule::OPERATOR_EQUALS,
                    $achID
                )
            ),
            new FilterComponent(
                new FilterRule(
                    'Level',
                    FilterRule::OPERATOR_LOWER_THAN,
                    $this->getCurrentBoxLevel()
                )
            ),
            new FilterComponent(
                new FilterRule(
                    'ParentBox',
                    FilterRule::OPERATOR_EQUALS,
                    0
                )
            )
        );
        $filter->operator = FilterComponent::OPERATOR_AND;
        // chargement de la collection
        $col = $mapper->loadCollection($filter);
        // si la collection n'est pas vide on la retourne
        if ($col->getCount() > 0) {
            return $col;
        }
        return false;
    }

    // }}}
    // ActivatedChainTask::getGroupableBoxCollectionCount() {{{

    /**
     * Retourne le nombre de Box qui peuvent être regroupées dans cette tâche
     *
     * @access public
     * @return integer
     */
    public function getGroupableBoxCollectionCount(){
        require_once('SQLRequest.php');
        $achID = Tools::getValueFromMacro($this, '%ActivatedOperation.ActivatedChain.Id%');
        $previousTask = $this->getPreviousTaskFromRule('isBoxCreatorTask');
        $ret = request_groupableBoxCount($achID, ($previousTask instanceof ActivatedChainTask)?$previousTask->getId():0, $this->getCurrentBoxLevel());
        if ($ret) {
            return $ret->fields[0];
        }
        return 0;
    }

    // }}}
    // ActivatedChainTask::getCurrentBoxLevel() {{{

    /**
     * Retourne le niveau de box pour la tache courante si la tache est une
     * tâche créatrice de box et false sinon.
     * Ce niveau sera celui affecté aux boxes créées par la tâche courante.
     *
     * @access public
     * @return mixed integer le niveau de box ou false
     */
    public function getCurrentBoxLevel(){
        // si on est pas dans une tâche créatrice de box on retourne false
        $task = $this->getTask();
        if (!$task->getIsBoxCreator()) {
            return false;
        }
        // level par défaut: 2 soit celui juste au dessus des boxes générées
        // initialement.
        $level = 2;
        $pTask = clone $this; // copie
        while($pTask = $pTask->getPreviousTaskFromRule('isBoxCreatorTask')){
            $level++;
        }
        return $level;
    }

    // }}}
    // ActivatedChainTask::getCommandType() {{{

    /**
     * Retourne le type de commande à activer (pour une tache d'activation)
     *
     * @access public
     * @return mixed string ou boolean false
     */
    public function getCommandType(){
        require_once('Objects/Chain.php');
        $chain = $this->getChainToActivate();
        if ($chain instanceof Chain) {
            if ($chain->getType() == Chain::CHAIN_TYPE_PRODUCT) {
                return 'ProductCommand';
            }
            return 'ChainCommand';
        }
        if( $this->getTaskId() == TASK_GENERIC_ACTIVATION ) {
            return 'ProductCommand';
        }
        return false;
    }

    // }}}
    // ActivatedChainTask::getActivatedChain() {{{

    /**
     * Retourne l'ActivatedChain
     *
     * @access public
     * @return Objet ActivatedChain
     */
    public function getActivatedChain(){
        $ActivatedChainOperation = $this->getActivatedOperation();
        $ActivatedChain          = $ActivatedChainOperation->getActivatedChain();

        return $ActivatedChain;
    }

    // }}}
    // ActivatedChainTask::getDepartureInstant() {{{

    /*
     * surcharge de la méthode getDepartureInstant()
     * pour calculer le DepartureInstant en fonction
     * de l'ArrivalInstant et de la durée si nécéssaire
     *
     * @access public
     * @return Object Instant
     */
    public function getDepartureInstant() {

        // si DepartureInstant existe, on le retourne
        if(parent::getDepartureInstant() != false) {
            return parent::getDepartureInstant();
        }
        $arrivalInstant = parent::getArrivalInstant();
        if(false == is_subclass_of($arrivalInstant, 'AbstractInstant')
        || $this->getDuration() == false) {
            return false;
        }

        if($arrivalInstant instanceof Instant) {
            $instant = new Instant();
            $instant->setDate(DateTimeTools::DateModeler($arrivalInstant->getDate(),
                                          -$this->getDuration()));
            return $instant;
        } elseif ($arrivalInstant instanceof DailyInstant) {
            // XXX à vérifier... je sais pas trop ce qu'il faut faire dans ce
            // cas là...
            return $arrivalInstant;
        } elseif ($arrivalInstant instanceof WeeklyInstant) {
            $nbSndPerDay = 24*60*60;
            $time = $arrivalInstant->getTime();
            $day = $arrivalInstant->getDay();
            $timeTS = DateTimeTools::TimeToTimeStamp($time);
            $newTimeTS = $timeTS - $this->getDuration();
            if($newTimeTS >= 0) {
                // le jour ne change pas
                $newDay = $day;
            } else {
                $newDay = $day;
                while($newTimeTS < 0) {
                    $newDay = $newDay-1;
                    if($newDay==-1) {
                        $newDay == 6;
                    }
                    $newTimeTS += $nbSndPerDay;
                }
            }
            $newTime = array();
            $newTime['sec'] = $newTimeTS % 60;
            $newTime['min'] = ($newTimeTS / 60) % 60;
            $newTime['hour'] = ($newTimeTS / 3600) % 60;

            $weeklyInstant = new WeeklyInstant();
            $weeklyInstant->setTime(sprintf('%d:%d:%d', $newTime['hour'], $newTime['min'], $newTime['sec']));
            $weeklyInstant->setDay($newDay);
            return $weeklyInstant;
        }

        return false;
    }

    // }}}
    // ActivatedChainTask::getArrivalInstant() {{{

    /*
     * surcharge de la méthode getArrivalInstant()
     * pour calculer l'ArrivalInstant en fonction
     * du DepartureInstant et de la durée si nécéssaire
     *
     * @access public
     * @return Object Instant
     */
    public function getArrivalInstant() {
        // si ArrivalInstant est renseigné on le retourne
        if(parent::getArrivalInstant() != false) {
            return parent::getArrivalInstant();
        }

        // on essai de calculer ArrivalInstant en fonction de DepartureInstant
        // et de la durée de la tâche
        $departureInstant = parent::getDepartureInstant();
        if(false==is_subclass_of($departureInstant, 'AbstractInstant')
        || $this->getDuration()==false) {
            return false;
        }

        if($departureInstant instanceof Instant) {
            $instant = new Instant();
            $instant->setDate(DateTimeTools::DateModeler($departureInstant->getDate(),
                                          $this->getDuration()));
            return $instant;
        } elseif ($departureInstant instanceof DailyInstant) {
            $instant = new DailyInstant();
            $instant->setTime(DateTimeTools::timeStampToMySQLDate(
                DateTimeTools::timeToTimeStamp(
                    $departureInstant->getTime()) + $this->getDuration(),
                'H:i:s'
            ));
            return $instant;
        } elseif ($departureInstant instanceof WeeklyInstant) {
            $nbSndPerDay = 24*60*60;
            $time = $departureInstant->getTime();
            $day = $departureInstant->getDay();
            $timeTS = DateTimeTools::TimeToTimeStamp($time);
            $newTimeTS = $timeTS + $this->getDuration();
            if($newTimeTS < $nbSndPerDay) {
                // le jour ne change pas
                $newDay = $day;
            } elseif ($newTimeTS > $nbSndPerDay) {
                $newDay = $day;
                while($newTimeTS > $nbSndPerDay) {
                    $newDay = $newDay+1;
                    if($newDay==7) {
                        $newDay = 0;
                    }
                    $newTimeTS -= $nbSndPerDay;
                }
            }

            $newTime = array();
            $newTime['sec'] = $newTimeTS % 60;
            $newTime['min'] = ($newTimeTS / 60) % 60;
            $newTime['hour'] = ($newTimeTS / 3600) % 60;

            $weeklyInstant = new WeeklyInstant();
            $weeklyInstant->setTime(sprintf('%d:%d:%d', $newTime['hour'], $newTime['min'], $newTime['sec']));
            $weeklyInstant->setDay($newDay);
            return $weeklyInstant;
        }

        return false;
    }

    // }}}
    // ActivatedChainTask::getAssembledConcreteProductCollection() {{{

    /**
     * Retourne le type de commande à activer (pour une tache d'activation)
     * N'a un sens que pour les ack: TASK_ASSEMBLY, TASK_SUIVI_MATIERE
     * @access public
     * @return object Collection of ConcreteProduct
     */
    public function getAssembledConcreteProductCollection(){
        $return = new Collection();
        $return->acceptDuplicate = false;
        $ccpColl = $this->getConcreteComponentCollection();
        if (!Tools::isEmptyObject($ccpColl)) {
            $count = $ccpColl->getCount();
            for($i = 0; $i < $count; $i++){
                $item = $ccpColl->getItem($i);
                $return->setItem($item->getParent());
            }
        }
        return $return;
    }

    // }}}
    // ActivatedChainTask::dupplicate() {{{

    /**
     * Permet de creer une nouvelle ActivatedChainTask qui est un dupplicat
     * @access public
     * @param  $ActivatedChainOperation Object (ActivatedChainOperation);
     * le dupplicat d'operation, qui implique cette dupplication de tache
     * @return void
     */
    public function dupplicate($ActivatedChainOperation) {
        $ackkMapper = Mapper::singleton('ActivatedChainTask');
        $newActivatedChainTask = Object::load('ActivatedChainTask'); // le dupplicat
        $attributeListForNewValue = array('Id', 'ActivatedOperation',
            'OwnerWorkerOrder', 'Massified', 'DataProvider', 'UserAccount');
        foreach($this->getProperties() as $field => $type) {
            if (!in_array($field, $attributeListForNewValue)) {
                $setter = 'set' . $field;
                $getter = 'get' . $field;
                if (method_exists($this, $setter)) {
                    $newActivatedChainTask->$setter($this->$getter());
                }
            }
        }
        $newActivatedChainTask->setId($ackkMapper->generateId());
        $newActivatedChainTask->setActivatedOperation($ActivatedChainOperation);
        $newActivatedChainTask->setMassified(0);
        $newActivatedChainTask->save();
        return $newActivatedChainTask;
    }

    // }}}

}

?>
