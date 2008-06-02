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
require_once('Objects/WorkOrder.php');
require_once('WorkOrderAssign.php');
$Auth = Auth::Singleton();
$Auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ACTOR,
        UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_TRANSPORTEUR));
$ActorId = $Auth->getActorId();   // Un User ne peut supprimer que les OT de son Actor

$UserAccount = $Auth->getUser();
$ProfileId = $UserAccount->getProfile();

SearchTools::prolongDataInSession();  // prolonge les datas en session

// recuperation de l'OT
$ListWkoId = $_REQUEST['WkoId'];

// pour chaque OT
foreach($ListWkoId as $WkoId) {
	$WorkOrder = Object::load('WorkOrder', $WkoId);
	// Verif si l'OT appartient bien au user connecte ou si un pilote est
    // connecte et si OT non cloture
	if ((!in_array($ProfileId, array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW))
        && $ActorId != $WorkOrder->getActorId())
	|| WorkOrder::WORK_ORDER_STATE_TOFILL != $WorkOrder->getState()) {
	    continue;
	}

	/** Ouverture de la transaction  **/
	Database::connection()->startTrans();

    // recuperation de ses Operations liées
    $mapper = Mapper::singleton('ActivatedChainOperation');
    unassigne(HANDLER_OPERATION, $WkoId, $mapper);
    // recuperation de ses taches liées
    $mapper = Mapper::singleton('ActivatedChainTask');
    unassigne(HANDLER_TASK, $WkoId, $mapper);
    // Suppression de l'OT
    $WorkOrder->delete();
	unset($WorkOrder);

	/** commit de la transaction **/
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_MSG_TRY_AGAIN, 'WorkOrderList.php');
        exit;
    }
	Database::connection()->completeTrans();
}
// retour

Tools::redirectTo('WorkOrderList.php');
exit;

/**
 * @access public
 * @return void
 */
function unassigne($choice, $WkoId, $mapper)
{
	$Collection = $mapper->loadCollection(array('OwnerWorkerorder' => $WkoId));
    for($i = 0; $i < $Collection->getCount();$i++) {
        $item = $Collection->getItem($i);
        $Id[] = $item->getId();
    }
    // desaffectation de chaque liaison Operation/taches
    if (isset($Id) && count($Id) > 0) {
		workOrderAssign($WkoId, $Id, $choice, false);
    }
}

?>