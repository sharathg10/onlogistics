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

class ActivatedChainIterator {
    // properties {{{

    /**
     * ActivatedChain instance
     *
     * @var    object ActivatedChain $activatedChain
     * @access protected
     */
    protected $activatedChain;

    // }}}
    // constructor {{{

    /**
     * Constructeur
     *
     * @param  object ActivatedChain $ach
     * @access public
     */
    function __construct($ach)
    {
        $this->activatedChain = $ach;
    }

    // }}}
    // ActivatedChainIterator::execute() {{{

    /**
     * Parcours les opérations de la chaine et appelle la méthode
     * processOperation() sur chaque opération.
     * 
     * @param  const $order SORT_ASC ou SORT_DESC
     * @param  mixed $limitToAco permet de passer un tableau d'ids ou un id
     * d'aco pour ne traiter que certaines opérations
     * @param  mixed $limitToAck permet de passer un tableau d'ids ou un id
     * d'ack pour ne traiter que certaines tâches
     * @return void
     */
    public function execute($order=SORT_ASC, $limitToAco=false, $limitToAck=false)
    {
        $filter = $limitToAco?array('Id'=>$limitToAco):array();
        $acoCol = $this->activatedChain->getActivatedChainOperationCollection(
            $filter,
            array('Order'=>$order)
        );
        $count = $acoCol->getCount();
        for($i = 0; $i < $count; $i++) {
            $aco = $acoCol->getItem($i);
            $this->processOperation($aco, $order, $limitToAck);
        } 
        $this->activatedChain->save();
        return true;
    }

    // }}}
    // ActivatedChainIterator::processOperation() {{{

    /**
     * Sauve l'opération passée en paramètre.
     * 
     * @param  object ActivatedChainOperation $aco
     * @param  const $order SORT_ASC ou SORT_DESC
     * @param  mixed $limitToAck permet de passer un tableau d'ids d'ack ou un 
     * id pour ne traiter que certaines tâches
     * @return void
     */
    protected function processOperation($aco, $order=SORT_ASC, $limitToAck=false)
    {
        $filter = $limitToAck?array('Id'=>$limitToAck):array();
        $ackCol = $aco->getActivatedChainTaskCollection(
            $filter,
            array('Order'=>$order)
        );
        $count = $ackCol->getCount();
	    for($i = 0; $i < $count; $i++) {
	        $ack = $ackCol->getItem($i);
	        $this->processTask($ack);
		}
        $aco->save();
    }

    // }}}
    // ActivatedChainIterator::processTask() {{{

    /**
     * Sauve la tâche passée en paramètre.
     * 
     * @param  object ActivatedChainTask $ack
     * @return void
     */
    protected function processTask($ack)
    {
        $ack->save();
    }

    // }}}
}

?>
