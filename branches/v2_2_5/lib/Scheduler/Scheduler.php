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

require_once('PlanningTools.php');
require_once('Objects/ActivatedChainTask.php');
require_once('Objects/ActivatedChain.php');
require_once('Objects/Chain.php');
require_once('SchedulerDumpTools.php');
require_once('ExceptionCodes.php');

define('SCHEDULE_DEBUG', false);

/**
 * Scheduler
 *
 **/
class Scheduler{
	/**
	 * Constructor
	 *
	 * @access protected
	 */
	function Scheduler(){
	}

	/**
	 * @access private
	 * @var object
	 */

	var $activatedChain = false;

	/**
	 * @access private
	 * @var object
	 */
	var $pivotTask = false;

	/**
	 * @access private
	 * @var boolean
	 */
	var $withUnavailabilities = false;

	/**
	 * @access private
	 * @var string
	 */
	var $fullErrorMessage = false;

	/**
	 * Gère les contraintes sur des tâches données contenues dans un tableau
	 * $constraints.
     *
	 * @param ActivatedChainTask $task La tâche à gérer
	 * @param array $constraints Tableau contenant des dates prédéfinies pour
     * certaines tâches
	 * @return boolean TRUE si la tâche possède une contrainte et FALSE sinon.
	 */
	function HandleTaskContraints($task, $constraints){
		$id = $task->getId();
		if (isset($constraints[$id])){
			if (SCHEDULE_DEBUG){
				echo '[Scheduler::HandleTaskContraints] ' .
                    'Found contraint for task n°' . $task->getId() . ': ' .
                    $constraints[$id]['Begin'] . ' - ' .
                    $constraints[$id]['End'] . '<br />';
			}
			$task->SetBegin($constraints[$id]['Begin']);
			$task->SetEnd($constraints[$id]['End']);
			return true;
		}
		return false;
	}

	/**
	 * Plannifie une chaine activée
	 *
	 * @param ActivatedChain $instance La chaine à planifier
	 * @param array $constraints Liste de contraintes de dates pour certaines
	 * tâches. Ce tableau contient des tableaux indexés sur l'identifiant de
	 * la tâche concernée. Chaque tableau inclu contient deux valeurs indexées
	 * sur Begin et End contenant les bornes à appliquer à la tâche.
	 * Si une entrée correspondante à une tâche est trouvée, aucune
	 * vérification de disponibilité d'acteur n'est effectuée. Cette fonction
	 * est utilisée lors de la replanification de chaines massifiées.
	 * @return mixed TRUE en cas de succès et une exception en cas d'erreur.
	 */
	function ScheduleActivatedChain($instance, $wishedStartDate,
        $wishedEndDate, $constraints = array()){
		if (!($instance instanceof ActivatedChain)){
		    $this->fullErrorMessage = _('Chain is not activated.') . ' (Erreur 001)';
			return new Exception(EXCEP_SCHEDULER_001, 1);
		}
		$this->activatedChain = $instance;
		$this->pivotTask = $this->activatedChain->GetPivotTask();
        if (!($this->pivotTask instanceof ActivatedChainTask)){
            $this->fullErrorMessage = _('Chain does not have deadline task') . ' (Erreur 002)';
			return new Exception(EXCEP_SCHEDULER_002, 2);
		}
        // gestion spécifique des chaines de type cours
        if ($this->activatedChain->getType() == Chain::CHAIN_TYPE_COURSE) {
        	//$this->withUnavailabilities = 1;
            $task = $this->pivotTask->getTask();
            if ($task->getId() != TASK_FLY) {
                // la chaine n'est pas valide
                $this->fullErrorMessage = _('A class chain must have as deadline task the flight task.') . ' (Erreur 003)';
    			return new Exception(EXCEP_SCHEDULER_003, 3);
            }
            // les ressources ont déjà été controlées et les dates
            // correspondent forcément aux dates souhaitées
            $this->pivotTask->setBegin($wishedStartDate);
            $this->pivotTask->setEnd($wishedEndDate);
        } else {
    		if (false === $this->HandleTaskContraints($this->pivotTask,
    			$constraints)){
    			$planningTools = $this->_GetPlanningToolsForThatOperation(
					$this->pivotTask->getActivatedOperation());
    			if (Tools::isException($planningTools)){
    				return $planningTools;
    			}
    			/**
    			 * Planification de la tâche pivot.
    			 */
    			$scheduleResult = $this->SchedulePivotTask($wishedStartDate,
                    $wishedEndDate, $planningTools);
    			if (Tools::isException($scheduleResult)){
    				return $scheduleResult;
    			}
    		}
        }
		$this->activatedChain->SetBeginDate($this->pivotTask->GetBegin());
		$this->activatedChain->SetEndDate($this->pivotTask->GetEnd());
		if (SCHEDULE_DEBUG){
			echo __LINE__ . ' pre _SchedulePostPivotTasks<br />';
		}
		$scheduleResult = $this->_SchedulePostPivotTasks($constraints);
		if (Tools::isException($scheduleResult)){
			return $scheduleResult;
		}
		if (SCHEDULE_DEBUG){
			echo '<pre>';
			echo 'Pivot Start: ' . $this->pivotTask->GetBegin() . '<br />';
			echo '<pre>Pivot End: ' . $this->pivotTask->GetEnd() . '<br />';
			echo '</pre>';
			DumpActivatedChainSchedule($this->activatedChain);
			echo __LINE__ . ' post _SchedulePostPivotTasks<br />';
		}
		$scheduleResult = $this->_SchedulePrePivotTasks($constraints);
		if (DateTimeTools::MysqlDateToTimeStamp(
            $instance->GetBeginDate()) < time()){
                $this->fullErrorMessage = _('Schedule cannot be completed: wished date is too near.') . ' (Erreur 004)';
			return new Exception(EXCEP_SCHEDULER_004, 4);
		}
		if (Tools::isException($scheduleResult)){
			return $scheduleResult;
		}
		if (SCHEDULE_DEBUG){
			DumpActivatedChainSchedule($this->activatedChain);
		}
		return true;
	}

	/**
	 * Planification des tâches post pivot. Cette méthode est appellée par
     * ScheduleActivatedChain.
	 *
	 * @param ActivatedChain $ActivatedChain La chaîne activée à planifier
	 * @return boolean TRUE si la planification a réussie et une exception en
     * cas d'erreur.
	 * @access protected
	 * @see ScheduleActivatedChain
	 */
	function _SchedulePostPivotTasks($constraints){
		if (SCHEDULE_DEBUG){
			echo __LINE__ . '<br />';
		}
		/**
		 * Ces variables contiennent au fur et à mesure de la planification le
         * début et la fin de la chaîne
		 */
		$chainSchedulingEnd = DateTimeTools::MysqlDateToTimeStamp(
            $this->pivotTask->GetEnd());
		/**
		 * Planification post-pivot
		 */
		$currentTask = $this->pivotTask;
		$previousTask = $this->pivotTask;
		if (SCHEDULE_DEBUG){
			echo __LINE__ . '<br />';
		}
		while ($currentTask = $currentTask->GetNextTask()){
			if (false === $this->HandleTaskContraints($currentTask,
                $constraints)){
				$planningTools = $this->_GetPlanningToolsForThatOperation(
                    $currentTask->GetActivatedOperation());
				if (Tools::isException($planningTools)){
					return $planningTools;
				}

				if ($currentTask->GetTriggerMode() == ActivatedChainTask::TRIGGERMODE_TEMP){
					if (SCHEDULE_DEBUG){
						echo 'TriggerMode = TRIGGERMODE_TEMP<br />';
					}
					$scheduleResult = $this->ScheduleASynchronousTask(
                        $currentTask,
                        DateTimeTools::MysqlDateToTimeStamp(
                            $previousTask->GetEnd()),
                        $planningTools);
					if (Tools::isException($scheduleResult)){
						return $scheduleResult;
					}
				} elseif
                    (($currentTask->GetTriggerMode() == ActivatedChainTask::TRIGGERMODE_MANUAL) ||
                    ($currentTask->GetTriggerMode() == ActivatedChainTask::TRIGGERMODE_AUTO)){
					$DepartureInstant = $currentTask->GetDepartureInstant();
					$ArrivalInstant = $currentTask->GetArrivalInstant();

					if (false == is_subclass_of(
                        $DepartureInstant, 'AbstractInstant')){
						if (false == is_subclass_of($ArrivalInstant,
                            'AbstractInstant')){
							/**
							 * Si les deux bornes ne sont pas figées,
                             * on utilise la méthode de planification classique
							 */
							$scheduleResult =
                                $this->ScheduleSynchronousTaskForward(
                                    $currentTask, $planningTools);
							if (Tools::isException($scheduleResult)){
								return $scheduleResult;
							}
						}else{
							die('[Scheduler] not yet implemented: ' .
                            'DepartInstant = NULL && Arrival Instant <> NULL');
						}
					}else{
						/**
						 * Planification des tâches à heure fixe
						 */
						if (false == is_subclass_of($ArrivalInstant,
                            'AbstractInstant')){
							die('[Scheduler] not yet implemented: ' .
                            'DepartInstant <> NULL && Arrival Instant = NULL');
						}else{
							$taskStart = $DepartureInstant->GetNearestOccurence(
                                DateTimeTools::MysqlDateToTimeStamp(
                                    $previousTask->GetEnd()));
							if (false == $taskStart){
							    $this->fullErrorMessage = _('No valid occurrence found for the beginning of fixed time task: ') . $currentTask->toString() . ' (Erreur 007)';
								return new Exception(EXCEP_SCHEDULER_007, 7);
							}
							$currentTask->SetBegin(
                                DateTimeTools::timeStampToMySQLDate($taskStart));
							$taskEnd = $ArrivalInstant->GetNearestOccurence(
                                $taskStart);
							if (false == $taskEnd){
							    $this->fullErrorMessage = _('No valid occurrence found for the end of fixed time task: ') . $currentTask->toString() . ' (Erreur 008)';
								return new Exception(EXCEP_SCHEDULER_008, 8);
							}
							$currentTask->SetEnd(
                                DateTimeTools::timeStampToMySQLDate($taskEnd));
						}
					}
				}else{
				    $this->fullErrorMessage = _('Wrong trigger mode: ') .
                        (int)$currentTask->GetTriggerMode() . ' (Erreur 006)';
					return new Exception(EXCEP_SCHEDULER_006, 6);
				}
			}
			$newLimit = DateTimeTools::MysqlDateToTimeStamp(
                $currentTask->GetEnd());
			if ($chainSchedulingEnd < $newLimit){
				$chainSchedulingEnd = $newLimit;
			}
			// $this->PropagateSchedulingToGhost($currentTask);
			$previousTask = $currentTask;
		}
		Assert($chainSchedulingEnd != 0);
		$this->activatedChain->SetEndDate(DateTimeTools::timeStampToMySQLDate(
            $chainSchedulingEnd));
		return true;
	}

	function _SchedulePrePivotTasks($constraints){
		/**
		 * Ces variables contiennent au fur et à mesure de la planification le
         * début et la fin de la chaîne
		 */		
		$chainSchedulingStart = DateTimeTools::MySQLDateToTimeStamp(
            $this->pivotTask->GetBegin());
		/**
		 * Planification post-pivot
		 */
		$currentTask = $this->pivotTask;
		$nextTask = $this->pivotTask;
		while ($currentTask = $currentTask->GetPreviousTask()){
			if (false === $this->HandleTaskContraints($currentTask,
                $constraints)){
				if (SCHEDULE_DEBUG){
					echo '_SchedulePrePivotTasks: ' .
                        $currentTask->toString() . '<br />';
				}
				$planningTools = $this->_GetPlanningToolsForThatOperation(
                    $currentTask->GetActivatedOperation());
				if (Tools::isException($planningTools)){
					return $planningTools;
				}
				if ($currentTask->GetTriggerMode() == ActivatedChainTask::TRIGGERMODE_TEMP){
					$scheduleResult = $this->ScheduleASynchronousTask(
                        $currentTask,
                        DateTimeTools::MySQLDateToTimeStamp(
                            $nextTask->GetBegin()),
                        $planningTools);
					if (Tools::isException($scheduleResult)){
						return $scheduleResult;
					}
				}elseif (
                    ($currentTask->GetTriggerMode() == ActivatedChainTask::TRIGGERMODE_MANUAL) ||
                    ($currentTask->GetTriggerMode() == ActivatedChainTask::TRIGGERMODE_AUTO)){
					$DepartureInstant = $currentTask->GetDepartureInstant();
					$ArrivalInstant = $currentTask->GetArrivalInstant();
					if (false == is_subclass_of($DepartureInstant,
                        'AbstractInstant')){
						if (false == is_subclass_of($ArrivalInstant,
                            'AbstractInstant')){
							/**
							 * Si les deux bornes ne sont pas figées, on
                             * utilise la méthode de planification classique
							 */
							$scheduleResult =
                                $this->ScheduleSynchronousTaskBackward(
                                    $currentTask, $planningTools);
							if (Tools::isException($scheduleResult)){
								return $scheduleResult;
							}
						}else{
							die('[Scheduler] not yet implemented: ' .
                            'DepartInstant = NULL && Arrival Instant <> NULL');
						}
					}else{
						/**
						 * Planification des tâches à heure fixe
						 */
						if (false == is_subclass_of($ArrivalInstant,
                            'AbstractInstant')){
							die('[Scheduler] not yet implemented: ' .
                            'DepartInstant <> NULL && Arrival Instant = NULL');
						}else{
							$taskEnd = $ArrivalInstant->GetNearestOccurence(
                                DateTimeTools::MySQLDateToTimeStamp(
                                    $nextTask->GetBegin()), false);
							if (false == $taskEnd){
							    $this->fullErrorMessage = _('No valid occurrence found for the beginning of fixed time task: ') . $currentTask->toString() . ' (Erreur 007)';
								return new Exception(EXCEP_SCHEDULER_007, 7);
							}
							$currentTask->SetEnd(
                                DateTimeTools::timeStampToMySQLDate($taskEnd));
							$taskStart = $DepartureInstant->GetNearestOccurence(
                                $taskEnd, false);
							if (false == $taskStart){
							    $this->fullErrorMessage = _('No valid occurrence found for the end of fixed time task: ') . $currentTask->toString() . ' (Erreur 008)';
								return new Exception(EXCEP_SCHEDULER_008, 8);
							}
							$currentTask->SetBegin(
                                DateTimeTools::timeStampToMySQLDate($taskStart));
						}
					}
				}else{
				    $this->fullErrorMessage = _('Wrong trigger mode: ') .
                        (int)$currentTask->GetTriggerMode() . ' (Erreur 006)';
					return new Exception(EXCEP_SCHEDULER_006, 6);
				}
			}
			if ($chainSchedulingStart >
                DateTimeTools::MysqlDateToTimeStamp($currentTask->GetBegin())){
				$chainSchedulingStart = DateTimeTools::MysqlDateToTimeStamp(
                    $currentTask->GetBegin());
			}
			$nextTask = $currentTask;
		}
		$this->activatedChain->SetBeginDate(
            DateTimeTools::timeStampToMySQLDate($chainSchedulingStart));
		return true;
	}

	/**
	 * Scheduler::_GetPlanningToolsForThatOperation()
	 * Retourne le planniong associé à l'acteur de l'opération
	 *
	 * @param $operation
	 * @return
	 **/
	function _GetPlanningToolsForThatOperation($operation){
		$mapper = Mapper::singleton('WeeklyPlanning');
		$planningID = Tools::getValueFromMacro($operation,
			'%Actor.MainSite.Planning.Id%');
		$planning = $mapper->load(array('Id'=>$planningID));
		if (Tools::isEmptyObject($planning)){
		    $this->fullErrorMessage = _('Schedule was not found for actor associated to operation ') . $operation->toString() . ' (Erreur 009)';
			return new Exception(EXCEP_SCHEDULER_009, 9);
		}
		return new PlanningTools($planning);
	}

	/**
	 * Planifie une tâche temporelle
	 *
	 * @param ActivatedChainTask Instance de la tâche temporelle à planifier
	 * @param integer $milestone Borne temporaire de référence pour le début.
	 * de la tâche. Cette borne sera pondérée par le delta de déclenchement
     * puis la tâche sera planifiée.
	 * @param PlanningTools $planningTools Utilitaire d'accès aux informations
     * du planning
	 * @return boolean TRUE si la planification s'est effectuée sans problème
     * et une Exception sinon
	 */
	function ScheduleASynchronousTask($task, $milestone, $planningTools){
		if (SCHEDULE_DEBUG){
			echo '<pre>TriggerDelta =  ' .
                I18N::formatDuration($task->GetTriggerDelta()) .
                '</pre>';
		}
		$task->SetBegin(DateTimeTools::timeStampToMySQLDate(
                $milestone + $task->GetTriggerDelta()));
		$scheduledRange = $this->ScheduleInteruptibleTaskForward(
            $task->GetMassifiedTaskDuration(),
            DateTimeTools::MysqlDateToTimeStamp($task->GetBegin()),
            $planningTools);
		if (Tools::isException($scheduledRange)){
			return $scheduledRange;
		}
		$task->SetEnd(DateTimeTools::timeStampToMySQLDate($scheduledRange['End']));
		return true;
	}

	/**
	 * Planifie une tâche non temporelle post-pivot.
	 *
	 * @param ActivatedChainTask $task La tâche à planifier.
	 * @param PlanningTools $planningTools Utilitaire de gestion du planning
     * de l'acteur effecteur de la tâche
	 * @return boolean TRUE si la planification s'est bien passée et une
     *  exception sinon
	 */
	function ScheduleSynchronousTaskForward($task, $planningTools){
		$previousSynchronousTask = $task->GetPreviousTask(
            array(ActivatedChainTask::TRIGGERMODE_MANUAL, ActivatedChainTask::TRIGGERMODE_AUTO));
		if (!Assert($previousSynchronousTask instanceof ActivatedChainTask)){
		    $this->fullErrorMessage = 'Unable to find previous task of ' .
                $task->toString() . ' (Erreur 010)';
			return new Exception(EXCEP_SCHEDULER_010, 10);
		}else{
			Assert($previousSynchronousTask->GetEnd() != 0);
			if (true == $task->GetMassifiedInteruptible()){
				$scheduledRange = $this->ScheduleInteruptibleTaskForward(
                    $task->GetMassifiedTaskDuration(),
                    DateTimeTools::MysqlDateToTimeStamp(
                        $previousSynchronousTask->GetEnd()),
                    $planningTools);
			}else{
				$scheduledRange = $this->ScheduleNonInteruptibleTaskForward(
                    $task->GetMassifiedTaskDuration(),
                    DateTimeTools::MysqlDateToTimeStamp(
                        $previousSynchronousTask->GetEnd()),
                    $planningTools);
			}
			if (Tools::isException($scheduledRange)){
				return $scheduledRange;
			}
			$task->SetBegin(DateTimeTools::timeStampToMySQLDate(
                $scheduledRange['Start']));
			$task->SetEnd(DateTimeTools::timeStampToMySQLDate(
                $scheduledRange['End']));
		}
		return true;
	}

	function ScheduleSynchronousTaskBackward($task, $planningTools){
		$nextSynchronousTask = $task->GetNextTask(
            array(ActivatedChainTask::TRIGGERMODE_MANUAL, ActivatedChainTask::TRIGGERMODE_AUTO));
		if ($nextSynchronousTask != false){
			if (!Assert($nextSynchronousTask instanceof ActivatedChainTask)){
			    $this->fullErrorMessage = 'Unable to find previous task of ' .
                    $task->toString() . ' (Erreur 010)';
				return new Exception(EXCEP_SCHEDULER_010, 10);
			}else{
				$taskDuration = $task->GetMassifiedTaskDuration();
				if($taskDuration <= 0){
				    $this->fullErrorMessage = sprintf(
                        _('Task %s cannot be scheduled because its duration is undefined.'), 
                        $task->toString()) . ' (Erreur 010)';
					return new Exception(EXCEP_SCHEDULER_010, 10);
				}
				if (true == $task->GetMassifiedInteruptible()){
					$scheduledRange = $this->ScheduleInteruptibleTaskBackward(
                        $taskDuration,
                        DateTimeTools::MysqlDateToTimeStamp(
                            $nextSynchronousTask->GetBegin()),
                        $planningTools);
				}else{
					$scheduledRange = $this->ScheduleNonInteruptibleTaskBackward(
                        $taskDuration, DateTimeTools::MysqlDateToTimeStamp(
                            $nextSynchronousTask->GetBegin()),
                        $planningTools);
				}
				if (Tools::isException($scheduledRange)){
					return $scheduledRange;
				}
				$task->SetBegin(DateTimeTools::timeStampToMySQLDate(
                    $scheduledRange['Start']));
				$task->SetEnd(DateTimeTools::timeStampToMySQLDate(
                    $scheduledRange['End']));
			}
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Détermine la date de référence pour la planification de la tâche pivot
	 * dans la chaine
	 * @param string $rangeStart Date MySQL correspondant au début de
	 * l'intervalle de temps pour la date souhaitée au niveau de la commande.
     * YYYY-MM-DD HH:MM:SS
	 * @param string $rangeEnd Date MySQL correspondant à la fin de
	 * l'intervalle de temps pour la date souhaitée au niveau de la commande.
	 * YYYY-MM-DD HH:MM:SS. Si cette élément vaut 0000-00-00 00:00:00 ou 0,
     * le début de l'intervalle sera utilisé comme référence.
	 * @return integer TimeStamp correspondant à la date de référence.
	 */
	function GetReferenceDate($rangeStart, $rangeEnd){
		$rangeStartTS = DateTimeTools::MysqlDateToTimeStamp($rangeStart);
		if (($rangeEnd != '0000-00-00 00:00:00') && ($rangeEnd != 0) &&
            ($rangeEnd != -1)){
			$rangeEndTS = DateTimeTools::MysqlDateToTimeStamp($rangeEnd);
			$wishedDate = ($rangeEndTS - $rangeStartTS) / 2 + $rangeStartTS;
		}else{
			$wishedDate = $rangeStartTS;
		}
		return $wishedDate;
	}

	/**
	 * Plannifie la tâche pivot.
	 *
	 * @access public
	 * @param ActivatedChainTask $task Instance de ActivatedChainTask
     * correspondant à la tâche pivot
	 * @param integer $anchorType Borne référente pour la plannification.
	 * @param ChainCommand $ChainCommand Instance de ChainCommand
     * correspondant à la commande de la chaine en cours de planification
	 * @param WeeklyPlanning $WeeklyPlanning Planning de l'acteur effecteur de
     * la tâche pivot
	 * @return boolean TRUE si la planification s'est bien passée et
     * une Exception sinon
	 */
	function SchedulePivotTask($wishedStartDate, $wishedEndDate,
		$planningTools){
		$anchorType = $this->activatedChain->GetPivotDateType();
		$taskDuration = $this->pivotTask->GetMassifiedTaskDuration();
		if($taskDuration <= 0){
            $this->fullErrorMessage = sprintf(
                _('Task %s cannot be scheduled because its duration is undefined.'),
						$this->pivotTask->toString()) . ' (Erreur 010)';
			return new Exception(EXCEP_SCHEDULER_010, 10);
		}
		$wishedDate = $this->GetReferenceDate($wishedStartDate, $wishedEndDate);
		if (SCHEDULE_DEBUG){
			echo '$wishedDate  = ' .
                DateTimeTools::timeStampToMySQLDate($wishedDate) . '<br />';
		}
		$actorIsAvailable = $this->ActorAvailableForPivotTask($planningTools,
			$wishedDate);
		if (Tools::isException($actorIsAvailable)){
			return $actorIsAvailable;
		}
		if ($anchorType == ActivatedChain::PIVOTTASK_BEGIN){
			if ($this->pivotTask->GetMassifiedInteruptible() == true){
				$result = $this->ScheduleInteruptibleTaskForward(
                    $taskDuration, $wishedDate, $planningTools);
			}else{
				$result = $this->ScheduleNonInteruptibleTaskForward(
                    $taskDuration, $wishedDate, $planningTools);
			}
		}elseif ($anchorType == ActivatedChain::PIVOTTASK_END){
			if ($this->pivotTask->GetMassifiedInteruptible() == true){
				if (SCHEDULE_DEBUG){
					echo '$wishedDate = ' .
                        DateTimeTools::timeStampToMySQLDate($wishedDate) .
                        '<br />';
				}
				$result = $this->ScheduleInteruptibleTaskBackward(
                    $taskDuration, $wishedDate, $planningTools);
			}else{
				$result = $this->ScheduleNonInteruptibleTaskBackward(
                    $taskDuration, $wishedDate, $planningTools);
			}
		}else{
		    $this->fullErrorMessage = _('Wrong type of anchor for deadline task "') . $anchorType . '" !' . ' (Erreur 011)';
			return new Exception(EXCEP_SCHEDULER_011, 11);
		}

		if ((false == $result) || Tools::isException($result)){
			return $result;
		}
		if (SCHEDULE_DEBUG){
			echo 'PivotTask Valid Range: ' .
                dateRangeToHumanStr($result) . '<br />';
		}
		$this->pivotTask->SetBegin(DateTimeTools::timeStampToMySQLDate($result['Start']));
		$this->pivotTask->SetEnd(DateTimeTools::timeStampToMySQLDate($result['End']));
		return true;
	}

	/**
	 * Plannifie une tâche post-pivot interruptible de $taskDuration secondes
	 * à partir de la date $date en se basant sur les disponibilités de l'acteur.
	 *
	 * @param integer $taskDuration Nombre de secondes de la tâche
	 * @param integer $date TimeStamp correspondant à la date minimale pour
     * l'execution possible de la tâche
	 * @param PlanningTools $planningTools Utilitaire de gestion du planning
     * de l'acteur permettant de connaitre ses périodes chaumés.
	 * @return array Un tableau associatif à deux entrées: 'Start' et 'End'
     * auxquelles correspondent
	 * deux timestamp déterminant les bornes de l'intervalle temporel.
	 * Une exception en cas d'impossibilité de plannification.
	 */
	function ScheduleInteruptibleTaskForward($taskDuration, $date,
        $planningTools){
		$resultRange = array('Start' => $date, 'End' => $date);
        $i = 0;
		while (($taskDuration > 0) &&
            ($taskPieces = $this->SchedulePiecesForward(
                $taskDuration, $date, $planningTools))){
            $i++;
            if ($taskPieces['FitTime'] <= 0) {
                break;
            }
			$taskDuration -= $taskPieces['FitTime'];
			$resultRange['End'] = $taskPieces['End'];
			$date = $taskPieces['End'];
		}
		if ($taskDuration > 0){
            $this->fullErrorMessage = _('Could not schedule interruptible task of ') 
                . I18N::formatDuration($taskDuration) . _(' from ') 
                . DateTimeTools::timeStampToMySQLDate($date) . ' (Erreur 012)';
			return new Exception(EXCEP_SCHEDULER_012, 12);
		}
		return $resultRange;
	}

	function ScheduleInteruptibleTaskBackward($taskDuration, $date,
        $planningTools){
		$resultRange = array('Start' => $date, 'End' => $date);
		while (($taskDuration > 0) &&
            ($taskPieces = $this->SchedulePiecesBackward(
                $taskDuration, $date, $planningTools)) &&
            ($taskPieces['FitTime'] > 0)){
            if ($taskPieces['FitTime'] <= 0) {
                break;
            }
			$taskDuration -= $taskPieces['FitTime'];
			$resultRange['Start'] = $taskPieces['Start'];
			$date = $taskPieces['Start'];
		}
		if ($taskDuration > 0){
            $this->fullErrorMessage = _('Could not schedule interruptible task of ') 
                . I18N::formatDuration($taskDuration) . _(' from ') 
                . DateTimeTools::timeStampToMySQLDate($date) . ' (Erreur 012)';
			return new Exception(EXCEP_SCHEDULER_012, 12);
		}
		return $resultRange;
	}

	/**
	 * Plannification d'une partie d'une tâche interuptible à partir de la
	 * date $lastPause
	 * @param integer $timeToFitInSecond Nombre de secondes maximum à gérer
	 * @param integer $lastPause Borne temporelle minimale pour la plannif
	 * @param PlanningTools $planningTools Utilistaire d'accès aux
     * informations du planning de l'acteur effecteur.
	 * @return array Un tableau associatif à deux entrées: 'LastEnd' et
     * 'FitTime'. Le premier correspond à la fin de l'execution du morceau de
     * tâche et le second correspond au nombre de secondes qui ont étés
     * plannifiées.
	 */
	function SchedulePiecesForward($timeToFitInSecond, $lastPause,
        $planningTools){
		$taskPieces = false;
		$range = $planningTools->GetNextAvailableRange($lastPause,
			$this->withUnavailabilities);
		if ($range){
			if ($timeToFitInSecond <= $range['End'] - $range['Start']){
				$taskPieces['End'] = $range['Start'] + $timeToFitInSecond;
				$taskPieces['FitTime'] = $timeToFitInSecond;
			}else{
				$taskPieces['End'] = $range['End'];
				$taskPieces['FitTime'] = $range['End'] - $range['Start'];
			}
		}
		if (SCHEDULE_DEBUG){
			echo 'SchedulePiecesBackward: ' .
                dateRangeToHumanStr($range) . ', FitTime = ' .
                I18N::formatDuration($taskPieces['FitTime']) .
                '<br />';
		}
		return $taskPieces;
	}

	/**
	 * @access public
	 * @return void
	 */
	function SchedulePiecesBackward($timeToFitInSecond, $lastPause,
        $planningTools){
		$taskPieces = false;
		$range = $planningTools->GetPreviousAvailableRange($lastPause,
			$this->withUnavailabilities);
		if ($range){
			if ($timeToFitInSecond <= $range['End'] - $range['Start']){
				$taskPieces['Start'] = $range['End'] - $timeToFitInSecond;
				$taskPieces['FitTime'] = $timeToFitInSecond;
			}else{
				$taskPieces['Start'] = $range['Start'];
				$taskPieces['FitTime'] = $range['End'] - $range['Start'];
			}
		}
		if (SCHEDULE_DEBUG){
			echo 'SchedulePiecesBackward: ' .
                dateRangeToHumanStr($range) . ', FitTime = ' .
                I18N::formatDuration($taskPieces['FitTime']) .
                '<br />';
		}
		return $taskPieces;
	}

	/**
	 * Recherche un créneau valide pour l'execution d'une tâche non
	 * interuptible à partir d'une date et d'un planning de disponibilité.
	 *
	 * @param integer $taskDuration Durée de la tâche en secondes
	 * @param integer $date TimeStamp correspondant à la date minimale à
     * laquelle la tâche peut être executée
	 * @param PlanningTools $planningTools Utilitaire de gestion du planning
     * correspondant à l'acteur effecteur
	 * @return array Un tableau associatif à deux entrées: 'Start' et 'End'
     * correspondant aux bornes de la tâches  ou une exception détaillant la
	 * cause de l'impossibilité
	 */
	function ScheduleNonInteruptibleTaskForward($taskDuration, $date,
        $planningTools){
		// Si on a pas trouvé au bout d'une semaine,
		// aucune plage n'est disponible
		$dateMax = $date + DateTimeTools::ONE_WEEK + DateTimeTools::ONE_DAY;
		$curDate = $date;
		while ($curDate < $dateMax){
			$creneau = $planningTools->GetNextAvailableRange($curDate,
				$this->withUnavailabilities);
			if (false == $creneau){
			    $this->fullErrorMessage = __LINE__ . ' : ' . sprintf(
                    _('Actor does not have a time slot long enough to execute non interruptible task. (taskDuration = %s, date = %s)'),
					$taskDuration,
					DateTimeTools::timeStampToMySQLDate($date)) . ' (Erreur 013)';
				return new Exception(EXCEP_SCHEDULER_013, 13);
			}
			if (($creneau['End'] - $creneau['Start']) >= $taskDuration){
				return array('Start' => $creneau['Start'],
					'End' => $creneau['Start'] + $taskDuration);
			}
			Assert($creneau['End'] > $curDate);
			$curDate = $creneau['End'];
		}
		$this->fullErrorMessage = __LINE__ . ' : ' . sprintf(
            _('Actor does not have a time slot long enough to execute non interruptible task. (taskDuration = %s, date = %s)'),
					I18N::formatDuration($taskDuration),
					DateTimeTools::timeStampToMySQLDate($date)) . ' (Erreur 013)';
		return new Exception(EXCEP_SCHEDULER_013, 13);
	}

	/**
	 * @access public
	 * @return void
	 */
	function ScheduleNonInteruptibleTaskBackward($taskDuration, $date,
        $planningTools){
		$dateMin = $date - DateTimeTools::ONE_WEEK - DateTimeTools::ONE_DAY;
		$curDate = $date;
		while ($curDate > $dateMin){
			$creneau = $planningTools->GetPreviousAvailableRange($curDate,
				$this->withUnavailabilities);
			if (false == $creneau){
			    $this->fullErrorMessage = __LINE__ . ' : ' .
                    _('Actor does not have a time slot long enough to execute non interruptible task ') . 
                    ' (Erreur 013)';
				return new Exception(EXCEP_SCHEDULER_013, 13);
			}
			if (($creneau['End'] - $creneau['Start']) >= $taskDuration){
				return array('Start' => $creneau['End'] - $taskDuration,
					'End' => $creneau['End']);
			}
			Assert($creneau['Start'] < $curDate);
			$curDate = $creneau['Start'];
		}
		$this->fullErrorMessage = __LINE__ . ' : ' . 
            _('Actor does not have a time slot long enough to execute non interruptible task ') . 
            _('from ') .
            I18N::formatDuration($taskDuration) . ' (Erreur 013)';
		return new Exception(EXCEP_SCHEDULER_013, 13);
	}

	/**
	 * Permet de déterminer la disponibilité d'un acteur à une date donnée.
	 *
	 * @param PlanningTools $planningTools Instance de PlanningTools
     * correspondant au planning de l'acteur
	 * @param integer $wishedDate Date concernée (TIMESTAMP)
	 * @return boolean TRUE si la date correspond à une plage de validité du
     * planning et FALSE sinon
	 */
	function ActorAvailableForPivotTask($planningTools, $wishedDate){
		$range = $planningTools->GetNextAvailableRange($wishedDate,
			$this->withUnavailabilities);
		if (is_array($range) && ($wishedDate >= $range['Start'] &&
            $wishedDate <= $range['End'])){
			return true;
		}
		// var_dump( $planningTools);
		$this->fullErrorMessage = __LINE__ . ' : ' .
            _('Actor does not have a time slot long enough to execute deadline task ') .
            '(' . DateTimeTools::timeStampToMySQLDate($wishedDate) . '), ' .
            dateRangeToHumanStr($range) . ' (Erreur 013)';
		return new Exception(EXCEP_SCHEDULER_013, 13);
	}

	/**
	 * Retourne le message d'erreur complet correspondant
	 * à la dernière exception levée ou false si aucun.
	 *
	 * @access public
	 * @return string
	 */
	function getFullErrorMessage() {
	    return $this->fullErrorMessage;
	}
}

?>
