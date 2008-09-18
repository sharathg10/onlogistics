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

class ChainCommand extends _ChainCommand {
    // Constructeur {{{

    /**
     * ChainCommand::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // ChainCommand::getSupplierCustomer() {{{

    /**
     * Retourne le couple SupplierCustomer de la commande telle que:
     * Supplier=DatabaseOwner et Customer=$this->getCustomer()
     *
     * Si le couple n'a pu être trouvé, il est créé à la volée avec des infos
     * par défaut.
     *
     * @access public
     * @return object SupplierCustomer
     */
    function getSupplierCustomer() {
        $spc = parent::getSupplierCustomer();
        if (!($spc instanceof SupplierCustomer)) {
            // le couple n'a pas été trouvé on en crée un par défaut
            require_once('Objects/SupplierCustomer.php');
            $spc = new SupplierCustomer();
            $sup = Auth::getDatabaseOwner();
            // conditions de paiement par defaut
            $top = Object::load('TermsOfPayment', 1);
            if ($top instanceof TermsOfPayment) {
                $spc->setTermsOfPayment($top);
            }
            $spc->setSupplier($sup);
            $cus = $this->getCustomer();
            $spc->setCustomer($cus);
            $hastva = ($this->getTotalPriceTTC()-$this->getTotalPriceHT() > 0);
            $spc->setHasTVA($hastva);
            if($this->hasBeenInitialized) {
                $spc->save();
            }
            $this->setSupplierCustomer($spc);
            if($this->hasBeenInitialized) {
                $this->save();
            }
        }
        return $spc;
    }
    // }}}
    // ChainCommand::hasAllGoupingTaskToDo() {{{

    /**
     * Retourne true si toutes les tâches de regroupement sont à STATE_TODO
     * @access public
     * @return boolean
     */
    public function hasAllGoupingTaskToDo() {
        // Base sur reel: y a t il eu des regroupements executes?
        // A ce stade, on ne vérifie pas qu'ils soient non facturée
        $chainCmdItemColl = $this->getCommandItemCollection();
        require_once('Objects/Task.inc.php');
        // Les ids de taches de transport
        $trsptTaskIds = array(TASK_GROUND_TRANSPORT, TASK_SEA_TRANSPORT,
            TASK_INLAND_WATERWAY_TRANSPORT, TASK_RAILWAY_TRANSPORT, TASK_AIR_TRANSPORT);
        $ackFilter = SearchTools::NewFilterComponent('Task', '', 'In', $trsptTaskIds, 1);

        foreach($chainCmdItemColl as $chainCmdItem) {
            $chain = $chainCmdItem->getActivatedChain();
            // Les taches de transport
            $trsptTaskColl = $chain->getActivatedChainTaskCollection($ackFilter);
            $ackCount = $trsptTaskColl->getCount();
            foreach($trsptTaskColl as $trsptTask) {
                // La tache precedente de regroupement
                $groupingTask = $trsptTask->getPreviousTaskFromRule('isGroupingTask');
                if ($groupingTask && $groupingTask->getstate() == ActivatedChainTask::STATE_TODO) {
                    continue;
                }
                // il y a au moins un regroupement d'execute pour cette cmde
                return false;
            }
        }
        return true;
    }

    // }}}

}

?>
