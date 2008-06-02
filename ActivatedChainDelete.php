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

$auth = Auth::Singleton();

$profileID = $auth->getProfile();
$auth->checkProfiles(
	array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ACTOR,
          UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES));

Database::connection()->startTrans();

/**
 * Gestion des droits: si User connecte n'est pas Pilot, il ne peut supprimer
 * que les ach resultant d'une commande passee par l'Actor attache au User
 * connecte.
 **/
$userConnectedActorID = $auth->getActorId();
// contient les No de Command liees aux ActivatedChain qu'on n'a pas le droit
// de supprimer
$cmdNoArray = array();

$achMapper = Mapper::singleton('ActivatedChain');
$acoMapper = Mapper::singleton('ActivatedChainOperation');
$actMapper = Mapper::singleton('ActivatedChainTask');

foreach($_REQUEST['chnId'] as $chnID) {
    // recuperation de ses Operations liées
    $ach = $achMapper->load(array('Id' => $chnID));
    $IdActivatedChain = $ach->getId();
    if (!isset($IdActivatedChain)) {
        Break;
    }
    /*  Gestion des droits  */
    if (!in_array($profileID,
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW))) {
        $CommandCustomerId = Tools::getValueFromMacro($ach,
			"%CommandItem()[0].Command.Customer.Id%");
		// on n'a pas le droit de supprimer cette ActivatedChain
        if ($CommandCustomerId != $userConnectedActorID) {
            $cmdNoArray[] = Tools::getValueFromMacro($ach,
				"%CommandItem()[0].Command.CommandNo%");;
            continue;
        }
    }

    $acoCollection = $ach->getActivatedChainOperationCollection();
    if ($acoCollection instanceof Collection) {
        for($i = 0; $i < $acoCollection->getCount();$i++) {
            $aco  = $acoCollection->getItem($i);
            $opID = $aco->getid();
            if (isset($opID)) {
                $actCollection = $aco->getActivatedChainTaskCollection();
                if ($actCollection instanceof Collection) {
                    // Suppression des taches liées
                    for($j = 0; $j < $actCollection->getCount();$j++) {
                        $act = $actCollection->getItem($j);
                        $taskID = $act->getId();
                        if (isset($taskID)) {
                            $actMapper->delete($taskID);
                        } // une tache au moins
                    }
                } // collection de taches
                $acoMapper->delete($opID);
            } // une operation au moins
        }
    } // collection d'operation
    $achMapper->delete($chnID);
}

if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    Database::connection()->rollbackTrans();
    Template::errorDialog(
        _('Selected chains could not be deleted.'), 'ActivatedChainList.php');
    exit;
}
Database::connection()->completeTrans();

if (count($cmdNoArray) > 0) {
	// si des ActivatedChain n'ont pu être supprimee, faute des bons droits
    $commands = implode('<br>&nbsp;&nbsp;-&nbsp;', $cmdNoArray);
    Template::errorDialog(
		sprintf(_('The chains related to the following orders cannot be deleted: <br>&nbsp;&nbsp;-&nbsp;%s'), $commands),
        'ActivatedChainList.php');
} else {
    Tools::redirectTo('ActivatedChainList.php');
}

?>