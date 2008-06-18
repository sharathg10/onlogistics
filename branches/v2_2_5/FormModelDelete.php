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

$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

define('I_DELETED', _('Select form models were successfully deleted.'));
define('I_NOT_DELETED_ENTITY', _('The following form model could not be deleted because it is linked to an action: %s'));
define('I_NOT_DELETED_ENTITYS',  _('The following form models could not be deleted because they are linked to an action: %s'));

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'FormModelList.php';

//Les paramétres ne sont pas correct
if (!isset($_REQUEST['IDs'])) {
	Template::errorDialog(E_ERROR_IMPOSSIBLE_ACTION, $retURL);
	exit;
}

if (!is_array($_REQUEST['IDs'])) {
    $_REQUEST['IDs'] = array($_REQUEST['IDs']);
}

$objectMapper = Mapper::singleton('FormModel');
$objectCol = $objectMapper->loadCollection(
						array('Id'=>$_REQUEST['IDs']));

//pour les objet non supprimées
$notDeletedEntity = array();

//On demarre la transaction 
Database::connection()->startTrans();
$count = $objectCol->getCount();
for($i=0 ; $i<$count ; $i++){
    $object = $objectCol->getItem($i);
	/*
	Ne sont supprimable que les formModel liés
	à aucune action
	*/
	$actionCol = $object->getActionCollection();
    if($actionCol->getCount()==0) {
        //on peut supprimer
        $objectMapper->delete($object->getId());
	} else {
	    //ajout dans le tableau des non suprimées
        $notDeletedEntity[] = $object->getName();
	}
}

//On commite
if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
	Database::connection()->rollbackTrans();
	Template::errorDialog(E_ERROR_IMPOSSIBLE_ACTION, $retURL);
	Exit;
}
Database::connection()->completeTrans();

// redirige vers un message d'info
if (count($notDeletedEntity) == 1) {
    $msg = sprintf(I_NOT_DELETED_ENTITY, $notDeletedEntity[0]);
} else if (count($notDeletedEntity) > 1) {
    $str = "<ul><li>" . implode("</li><li>", $notDeletedEntity) . "</li></ul>"; 
    $msg = sprintf(I_NOT_DELETED_ENTITYS, $str);
} else {
    $msg = I_DELETED;
}
Template::infoDialog($msg, $retURL);
?>
