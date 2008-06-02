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

require_once('Quantificator.php');

/** 
 * CourseCommandQuantificator class 
 * Quantificateur pour la commande cours
 * 
 * @package    lib
 * @subpackage Quantificator
 */
class CourseCommandQuantificator extends Quantificator {
    // CourseCommandQuantificator::processTask() {{{

    /**
     * Quantificator::ProcessTask()
     * 
     * @param $task
     * @return void
     */
    protected function processTask($task) {
        $taskID = $task->getTaskId();
		if ($taskID == TASK_FLY) {
            // la durée est celle définie dans la commande
            $duration = DateTimeTools::TimeToTimeStamp(
                $this->command->getDuration());
		    $task->SetDuration($duration);
            // le coût doit être récupéré via la prestation associée, on la  
            // récupère via l'opération 
            $costHT  = 0;
            $costTTC = 0;
            
            $aco = $task->getActivatedOperation();
            $ope = $aco->getOperation();
            $prest = $aco->findPrestation($aco->getActorId());
            if($prest instanceof Prestation) {
                $params = array('time'=>$duration/3600);
                // acteur à facturer
                $act = $aco->getActor(); // acteur de l'opération
                // concreteproduct ou product ou flytype à facturer
                $concreteProductIds = array();
                $productIds = array();

                $ccp = $aco->getConcreteProduct();
                $prices = $prest->getPrestationPrice($params, array($ccp->getId()=>array(
                    'id'=>$ccp->getId(), 'qty'=>1)));
                $tva = $prest->getTVA();
                foreach($prices as $crID=>$params) {
                    $costHT = $params['priceHT'];
                    break;
                }
                $costTTC = $costHT + (($costHT * $tva->getRate()) / 100);
                $this->command->setTotalPriceHT($costHT);
                $this->command->setTotalPriceTTC($costTTC);
                // cout de la tache 
                $task->setCost($costHT);
            }
		} else {
            // on calcule la durée et le coût de la tache normalement
    		parent::processTask($task);  
        }
    }

    // }}}
}

?>
