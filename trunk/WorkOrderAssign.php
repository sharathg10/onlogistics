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

define('HANDLER_TASK', 2);
define('HANDLER_OPERATION', 1);
/**
 * WorkOrderAssign()
 * 
 * @param  $OtId uid de l'ot
 * @param  $Id tableau d'index des éléments à traiter
 * @param  $choice HANDLER_TASK ou HANDLER_OPERATION
 * @param  $assign TRUE => demande d'affectation / FALSE => demande de désaffectation
 * @return 
 */
function WorkOrderAssign($OtId, $Id, $choice, $assign){
    if ($assign != true){
		$update = Object::load('WorkOrder');  /// $update = 0;  (modif)
    }else{
        $update = Object::load('WorkOrder', $OtId); /// $OtId;  (modif)
    }
	
    if ($choice == HANDLER_TASK){ // si c'est des taches:  desaffectation de chaque tache
        foreach($Id as $TaskId){
            $ActivatedTask = Object::load('ActivatedChainTask', $TaskId);
            $ActivatedTask->setOwnerWorkerOrder($update);
            saveInstance($ActivatedTask, $_SERVER['PHP_SELF']);
            // si son Op n'a plus de tache, on la desaffecte aussi.
            $ActivatedOperation = $ActivatedTask->getActivatedOperation();
            $Taskscollection = $ActivatedOperation->getActivatedChainTaskCollection();
            if(false != $Taskscollection){
                if ($Taskscollection->getCount() == 1){
                    $ActivatedOperation->setOwnerWorkerOrder($update);
                    saveInstance($ActivatedOperation, $_SERVER['PHP_SELF']);
                }
            }
        }
    }
	else{ // HANDLER_OPERATION : si c'est des OP, desafectation de chaque OP
        foreach($Id as $OpeId){
            $ActivatedOpe = Object::load('ActivatedChainOperation', $OpeId);
            $ActivatedOpe->setOwnerWorkerOrder($update);
			if ($assign != true) {    // si desaffectation
			    $ActivatedOpe->setOrderInWorkOrder(0);
			}
            saveInstance($ActivatedOpe, $_SERVER['PHP_SELF']);

			$ActivatedTaskCollId = $ActivatedOpe->getActivatedChainTaskCollectionIds();
/*          $ActivatedTaskColl = $ActivatedOpe->getActivatedChainTaskCollection();
            if(!Tools::isEmptyObject($ActivatedTaskColl)){  /// false != $ActivatedTaskColl
                for($i = 0 ; $i < $ActivatedTaskColl->getCount();$i++){
                    $ActivatedTask = $ActivatedTaskColl->getItem($i);*/
					
			if (!empty($ActivatedTaskCollId)) {
				for($i = 0 ; $i < count($ActivatedTaskCollId);$i++){
			    	$ActivatedTask = Object::load('ActivatedChainTask', 
															   $ActivatedTaskCollId[$i]);
                    $ActivatedTask->setOwnerWorkerOrder($update);  // desaffectation de chacune de ses taches
                    saveInstance($ActivatedTask, $_SERVER['PHP_SELF']);
					unset($ActivatedTask);
                }
            }
			unset($ActivatedOpe, $ActivatedTaskColl);
        }
    }
}
?>
