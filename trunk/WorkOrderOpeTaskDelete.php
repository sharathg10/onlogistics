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

require_once("config.inc.php");
require_once("WorkOrderAuth.php");
$Auth = WorkOrderAuth::Singleton();

$Auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ACTOR, UserAccount::PROFILE_SUPERVISOR, 
	    UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_TRANSPORTEUR), 
    array('showErrorDialog'=>true, 'debug'=>false));
$UserConnectedActorId = $Auth->GetActorId(); // Id de l'actor lie au User connecte

SearchTools::ProlongDataInSession();  // prolonge les datas en session

$WorkOrder = Object::load("WorkOrder", $_REQUEST['OtId']);
// Un User ne peut modifier que les OT de son Actor, sauf s'il est pilote
if ((in_array($Auth->getProfile(), array(UserAccount::PROFILE_ACTOR, UserAccount::PROFILE_SUPERVISOR,
                        				   UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_TRANSPORTEUR))) 
			  && $UserConnectedActorId != $WorkOrder->GetActorId()){
    Template::errorDialog(_("You are not allowed to modify this work order."), 'WorkOrderList.php');
}

//$OtName = $_REQUEST['OtName'];

require_once("WorkOrderAssign.php");
WorkOrderAssign($_REQUEST['OtId'], $_REQUEST['Id'], $_REQUEST['choice'], false);
// retour

Tools::redirectTo('WorkOrderOpeTaskList.php?choice=' . $_REQUEST['choice'] . '&OtId=' . 
    $_REQUEST['OtId'] . '&returnURL='.$_REQUEST["returnURL"] . "&".SID);
Exit;

?>