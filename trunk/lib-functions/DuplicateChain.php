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

function duplicateChain($chain, $newRef=null, $newDesc=null)
{
    $newChain = Tools::duplicateObject($chain);
    // assign la nouvelle référence et nom
    if ($newRef !== null) {
        $newChain->setReference($newRef);
    }
    if ($newDesc !== null) {
        $newChain->setDescription($newDesc);
    }
    // gestion des opérations et des tâches de la chaine
    $opeCol = $chain->getChainOperationCollection();
    $count  = $opeCol->getCount();
    for ($i=0; $i<$count; $i++) {
        $ope = $opeCol->getItem($i);
        $newOpe = Tools::duplicateObject($ope);
        $newOpe->setChain($newChain->getId());
        $newOpe->save();
        $taskCol = $ope->getChainTaskCollection();
        $jcount  = $taskCol->getCount();
        for ($j=0; $j<$jcount; $j++) {
            $task = $taskCol->getItem($j);
            $newTask = Tools::duplicateObject($task);
            $newTask->setOperation($newOpe->getId());
            // ActorSiteTransition
            $taskAST = $task->getActorSiteTransition();
            if ($taskAST instanceof ActorSiteTransition) {
                $newTaskAST = Tools::duplicateObject($taskAST);
                $newTaskAST->save();
                $newTask->setActorSiteTransition($newTaskAST);
            }
            // DepartureInstant, ArrivalInstant
            $taskDI = $task->getDepartureInstant();
            if ($taskDI instanceof DepartureInstant) {
                $newTaskDI = Tools::duplicateObject($taskDI);
                $newTaskDI->save();
                $newTask->setDepartureInstant($newTaskDI);
            }

            $taskAI = $task->getArrivalInstant();
            if ($taskAI instanceof ArrivalInstant) {
                $newTaskAI = Tools::duplicateObject($taskAI);
                $newTaskAI->save();
                $newTask->setArrivalInstant($newTaskAI);
            };
            // sauvegarde
            $newTask->save();
            if ($task->getId() == $chain->getPivotTaskId()) {
                $newChain->setPivotTask($newTask);
            }
        }
    }
    // date de création
    $newChain->setCreatedDate(date('Y-m-d H:i:s', time()));
    // ActorSiteTransition
    $chainAST = $chain->getSiteTransition();
    if ($chainAST instanceof ActorSiteTransition) {
        $newChainAST = Tools::duplicateObject($chainAST);
        $newChainAST->save();
        $newChain->setSiteTransition($newChainAST);
    }
    // PivotTask
    $newChain->save();
    return $newChain;
}
