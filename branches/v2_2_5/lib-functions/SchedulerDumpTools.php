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

function DumpActivatedChainSchedule($ActivatedChain){
	$PivotTask = $ActivatedChain->GetPivotTask();
	$TaskPosition = 0;
	echo '<pre>' . "\n";
	echo "Start on " . $ActivatedChain->GetBeginDate() . ", ends on " . 
        $ActivatedChain->GetEndDate();
	$ChainCommandItemCollection = $ActivatedChain->GetCommandItemCollection();
	if(!Tools::isEmptyObject($ChainCommandItemCollection)){
		$ChainCommandItem = $ChainCommandItemCollection->GetItem(0);
		$ChainCommand = $ChainCommandItem->GetCommand();
		if ($ChainCommand instanceof Command){
			echo ", Wished Date = " . 
                DateTimeTools::timeStampToMySQLDate(
                    Scheduler::GetReferenceDate(
                        $ChainCommand->GetWishedStartDate(), 
                        $ChainCommand->GetWishedEndDate()
                    ), 'Y-m-d H:i'
                ) . "\n";
		}
	}
	echo "\n";
	$OperationCollection = $ActivatedChain->GetActivatedChainOperationCollection();
	$taskCost = 0;
	if (false != $OperationCollection){
		$OperationCollection->Sort('Order');
		for($i = 0; $i < $OperationCollection->GetCount(); $i++){
			$Operation = $OperationCollection->GetItem($i);
			if ($Operation == null){
				continue;
			}
			$temp_op = $Operation->getOperation();
			DumpActivatedChainSeparator();
			printf("[%04d] Operation n°%04d : %-81s |                Debut |                  Fin |\n", $Operation->GetId(), $Operation->GetOrder(), $temp_op->getName());
			$TaskCollection = $Operation->GetActivatedChainTaskCollection();
			if (false != $TaskCollection){
				$TaskCollection->Sort('Order');
				for($j = 0; $j < $TaskCollection->GetCount(); $j++){
					$Task = $TaskCollection->GetItem($j);
					DumpActivatedChainTaskSchedule($Task);
					$taskCost += $Task->GetCost();
				}
			}
		}
		DumpActivatedChainSeparator();
	}
	echo 'Cost: ' . $taskCost . ' €';
	echo '</pre>';
}

function DumpActivatedChainTaskSchedule($Task){
	$caption = sprintf('[%04d]%s [%s] ', $Task->GetId(), 
        ($Task->GetMassified()?'[M]':'   '), 
        $Task->getWithForecast()?'Forecast':'        ');
	$temp_task = $Task->getTask();
	$caption .= ($temp_task instanceof Task)?$temp_task->getName():'N/A';
	switch ((int)$Task->GetTriggerMode()){
		case ActivatedChainTask::TRIGGERMODE_MANUAL:
			break;
		case ActivatedChainTask::TRIGGERMODE_AUTO: $caption .= ' [A]';
			break;
		case ActivatedChainTask::TRIGGERMODE_TEMP: $caption .= sprintf(' [T:%s]', 
                I18N::formatDuration($Task->GetTriggerDelta()));
			break;
		default:
			$caption = '';
	}
	$caption = sprintf('%-60s (%7s raw: %7s) %6d €', $caption,
		I18N::formatDuration($Task->GetMassifiedTaskDuration()),
		I18N::formatDuration($Task->GetRawDuration()),
		$Task->GetCost()
		);
	printf(" \-- %s%-" . ($Task->GetMassified()?84:87) . 
        "s n°%04s | %20s | %20s |\n", 
        $Task->GetMassified()?'[G]':'   ', 
        $caption, $Task->GetOrder(), $Task->GetBegin(), $Task->GetEnd()); 
	// $endDate = $Task->GetEnd();
	$Ghost = $Task->GetGhost();
	if ($Ghost instanceof ActivatedChainTask && ($Ghost->GetMassified() == true)){
		DumpActivatedChainTaskSchedule($Ghost);
	}
}

function DumpActivatedChainSeparator(){
	echo "----------------------------------------------------------------" . 
        "-----------------------------------------------------------------" . 
        "--------------------------\n";
}

/**
 * Utilise DateTimeTools::timeStampToMySQLDate pour retourner une fourchette 
 * de date de la forme [Y-m-d H:i ; Y-m-d H:i] à partir d'un tableau 
 * de deux timestamp unix.
 * <code>
 * dateRangeToHumanStr(array('Start'=>$timestampDebut, 'End'=>$timestampFin));
 * </code>
 * 
 * @param array $range tableau de timestamp
 * @param string $format format des dates à retourner
 * @return string
 */
function dateRangeToHumanStr($range, $format='Y-m-d H:i') {
    return '[' . DateTimeTools::timeStampToMySQLDate($range['Start'], $format) . 
           ' ; ' . DateTimeTools::timeStampToMySQLDate($range['End'], $format) . ']';
}

?>
