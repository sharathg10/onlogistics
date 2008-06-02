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

require_once('config.inc.php');
$Auth = Auth::Singleton();

// recuperation de l'OT
$WkoId = $_REQUEST['WkoId'];
$WorkOrderMapper = Mapper::singleton('WorkOrder');
$WorkOrder = $WorkOrderMapper->load(array('Id' => $WkoId));
// recuperation des OP
$SelectedOperations = $_REQUEST['SelectedOperations'];
$ACOMapper = Mapper::singleton('ActivatedChainOperation');
$ACOCollection = $ACOMapper->loadCollection(array('Id' => $SelectedOperations));

require_once('IsCommandCompatibleWithWorkOrder.php');
$IsCompatible = IsCommandCompatibleWithWorkOrder($ACOCollection, $WorkOrder);
if (true === $IsCompatible) {
    for($i = 0; $i < $ACOCollection->getCount(); $i++) {
        unset($ActivatedChainOperation);
        $ActivatedChainOperation = $ACOCollection->getItem($i); 
        // affecter l'OT à l'OP
        $ActivatedChainOperation->setOwnerWorkerOrder($WkoId);
        saveInstance($ActivatedChainOperation, $_SERVER['PHP_SELF']);
        // recuperer ses taches
        $ActivatedChainTaskCollection = $ActivatedChainOperation->getActivatedChainTaskCollection();
        // affecter l'OT à ses taches
        for($j = 0; $j < $ActivatedChainTaskCollection->getCount();$j++) {
            unset($ActivatedChainTask);
            $ActivatedChainTask = $ActivatedChainTaskCollection->getItem($j);
            $ActivatedChainTask->setOwnerWorkerOrder($WkoId);
            saveInstance($ActivatedChainTask, $_SERVER['PHP_SELF']);
        } 
    } 
} elseif (Tools::isException($IsCompatible)) {
    Template::errorDialog($IsCompatible->getMessage(), $_SERVER['HTTP_REFERER']);
	Exit;
} 
// retour
// '&from=affect' servira a l'issue de WorkOrderCloture, a rediriger correctement
Tools::redirectTo('WorkOrderOpeTaskList.php?OtId='.$WkoId.'&returnURL=WorkOrderList.php&from=affect');
Exit;

?>
