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
$auth->checkProfiles(
    array(
        UserAccount::PROFILE_ADMIN,
        UserAccount::PROFILE_ADMIN_VENTES,
        UserAccount::PROFILE_AERO_ADMIN_VENTES
    )
);

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'FlowList.php';

// Si on n'a pas les bons paramètres, on renvoit une erreur
if (!isset($_REQUEST['flowIDs'])) {
    Template::errorDialog(E_ERROR_IMPOSSIBLE_ACTION, $retURL);
    exit;
}

// On demarre la transaction
Database::connection()->startTrans();

// On supprime les éléments sélectionnes
if (!is_array($_REQUEST['flowIDs'])) {
    $_REQUEST['flowIDs'] = array($_REQUEST['flowIDs']);
}
$flowCollection = Object::loadCollection('Flow', array(
    'Id'=>$_REQUEST['flowIDs']), array('PieceNo'=>SORT_DESC));
require_once('SQLRequest.php');

$rs = request_flowLastPieceNo();
$maxPieceNo = $rs->fields[0];
$notDeletedFlow = array();
for($i = 0; $i < count($_REQUEST['flowIDs']); $i++) {
    $flow = $flowCollection->getItem($i);
    if($flow->getPieceNo() < $maxPieceNo) {
        $notDeletedFlow[] = $flow->getNumber();
        continue;
    }
    $maxPieceNo = $maxPieceNo-1;
        //Object::load('Flow', $_REQUEST['flowIDs'][$i]);
    $abd = $flow->getActorBankDetail();
    if (!Tools::isEmptyObject($abd)) {
        // On incremente ou decremente en banque, selon que charge ou recette
        $coef = (Tools::getValueFromMacro($flow, '%FlowType.Type%') == FlowType::CHARGE)?1:-1;
        $abd->setAmount($abd->getAmount() + $coef * $flow->getPaid());
        saveInstance($abd, $retURL);
    }
    deleteInstance($flow, $retURL);
}

// On commite
if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    Database::connection()->rollbackTrans();
    Template::errorDialog(E_ERROR_IMPOSSIBLE_ACTION, $retURL);
    exit;
}
Database::connection()->completeTrans();

if(!empty($notDeletedFlow)) {
    $str = "<ul><li>" . implode("</li><li>", $notDeletedFlow) . "</li></ul>"; 
    $msg = sprintf(_('The following expenses or receipts could not be deleted: %s'), $str);
    Template::infoDialog($msg, $retURL);
    exit();
}

// Tout est OK, On retourne à la liste
Tools::redirectTo($retURL);
?>
