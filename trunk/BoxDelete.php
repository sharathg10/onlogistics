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
require_once('Objects/ActivatedChainTask.php');

//Database::connection()->debug = 1;

// messages

// url de retour
$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'BoxList.php';

// Gestion des droits
$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, 
    UserAccount::PROFILE_TRANSPORTEUR, UserAccount::PROFILE_GESTIONNAIRE_STOCK));

// prolonge les recherches du moteur de recherche
SearchTools::ProlongDataInSession();

// si aucun id n'est passé en paramètre, message d'erreur
// ici on a des ids de *ChildBoxes*
if (!isset($_REQUEST['boxIDs'])) {
    Template::errorDialog(I_NEED_SELECT_ITEM, $retURL);
    exit;
}

// on charge la collection des box à partir des IDs passés en paramètre
$mapper = Mapper::singleton('Box');
$col = $mapper->loadCollection(array('Id'=>$_REQUEST['boxIDs']));

// si la collection est vide, message d'erreur
if (Tools::isEmptyObject($col)) {
    Template::errorDialog(E_NO_ITEM_FOUND, $retURL);
    exit;
}

// construction de la collection de parentBox
// note: 
//  $boxMapper->loadCollection(array('ParentBox.Id'=>$_REQUEST['boxIDs']))
// ne marche pas, d'ou cette boucle
$parentBoxes = new Collection();
$count = $col->getCount();
for($i = 0; $i < $count; $i++){
    $box = $col->getItem($i);
    if (in_array($box->getParentBoxId(), $parentBoxes->getItemIds())) {
        continue;
    }
    $parentBox = $box->getParentBox();
    $parentBoxes->setItem($parentBox);
}

// on demarre une transaction 
Database::connection()->startTrans();

// on parcours les parentBoxes
$false = false;
$count = $parentBoxes->getCount();
for($i = 0; $i < $count; $i++){
    $parentBox = $parentBoxes->getItem($i);
    // on parcours les ack liées et on passe leur état à "en cours"
    $ackCollection = $parentBox->getActivatedChainTaskCollection();      
    if (!Tools::isEmptyObject($ackCollection)) {
        $colCount = $ackCollection->getCount();
        for($k = 0; $k < $colCount; $k++){
            $ack = $ackCollection->getItem($k);
            $ack->setState(ActivatedChainTask::STATE_IN_PROGRESS);
            saveInstance($ack, $retURL);
        }
    }
    // suppression de la packinglist liée
    $pkl = $parentBox->getPackingList();
    if ($pkl instanceof PackingList) {
        deleteInstance($pkl, $retURL);
    }    
    // suppression de la box parente
    deleteInstance($parentBox, $retURL);
}

// On commite
if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
	Database::connection()->rollbackTrans();
	Template::errorDialog(E_ERROR_IN_EXEC, $retURL);
	Exit;
}
Database::connection()->completeTrans();

// Tout est OK. On retourne à la liste 
Tools::redirectTo($retURL);
exit;

?>
