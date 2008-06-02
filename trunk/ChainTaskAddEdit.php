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
require_once('ChainTaskAddEditTools.php');
require_once('HTML/QuickForm.php');
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
// }}}

// auth et url de retour {{{
$session = Session::singleton();
$auth = Auth::singleton();
$auth->checkProfiles(
        array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_TRANSPORTEUR,
              UserAccount::PROFILE_DIR_COMMERCIAL));

// prolonge la session de l'acteur en mémoire
if (!($actID = SearchTools::RequestOrSessionExist('actID'))) {
    Template::errorDialog(E_ERROR_SESSION, 'ActorList.php');
    exit(1);
}
$session->register('actID', $actID, 3);

$retURL = isset($_REQUEST['retURL'])?
    $_REQUEST['retURL']:'ChainTaskList.php';
// }}}

// instanciation de la chaintask {{{
if (isset($_REQUEST['ctkID']) && $_REQUEST['ctkID'] > 0) {
    $mapper = Mapper::singleton('ChainTask');
    $ctk = $mapper->load(array('Id'=>$_REQUEST['ctkID']));
} else {
    $ctk = false;
}
// l'entité n'a pu être chargée...
if (!($ctk instanceof ChainTask)) {
    Template::errorDialog(E_ERROR_IN_EXEC, $retURL);
    exit(1);
}
$dInstant = $ctk->getDepartureInstant();
$aInstant = $ctk->getArrivalInstant();
$dInstantType = getInstantType($dInstant);
$aInstantType = getInstantType($aInstant);
// }}}

// traitement après soumission du formulaire {{{
if (isset($_POST['Ok'])) {
    Database::connection()->startTrans();
    saveInstants($ctk, $dInstant, 'Departure');
    saveInstants($ctk, $aInstant, 'Arrival');
    saveInstance($ctk, $retURL);

    if (Database::connection()->hasFailedTrans()) {
        $err = Database::connection()->errorMsg();
        trigger_error($err, E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_ERROR_SQL . '.<br/>' . $err, $retURL);
        exit;
    }
    Database::connection()->completeTrans();
    Tools::redirectTo($retURL);
    exit(0);
}
// }}}

// traitement du template avec smarty {{{
$smarty = new Template();
$smarty->assign('ctkID', $_REQUEST['ctkID']);
$smarty->assign('retURL', $retURL);
$smarty->assign('formAction', basename($_SERVER['PHP_SELF']));
$smarty->assign('ChainTask_Name', Tools::getValueFromMacro($ctk, '%Task.Name%'));
$smarty->assign('Chain_Name',
    Tools::getValueFromMacro($ctk, '%Operation.Chain.Reference%'));
$smarty->assign('dateFormat', I18N::getHTMLSelectDateFormat());
// durée
$duration = $ctk->getDuration();
$duration_hours = floor($duration/3600);
$duration_minutes = $duration/60 - $duration_hours*60;
$duration_string = $duration_hours . ' h.';
if ($duration_minutes > 0) {
    $duration_string .= ' et ' . $duration_minutes . ' min.';
}
$smarty->assign('ChainTask_ActualDuration', $duration_string);
$smarty->assign('ChainTask_Duration_Hour', $duration_hours);
$smarty->assign('ChainTask_Duration_Minute', $duration_minutes);
// instants
$smarty->assign('DepartureInstant_Type', $dInstantType);
$smarty->assign('ArrivalInstant_Type', $aInstantType);
assignInstants($smarty, $dInstant, 'Departure');
assignInstants($smarty, $aInstant, 'Arrival');
// }}}

// affichage de la page {{{
$pageTitle = _('Update fixed date task or weekly task');
$pageContent = $smarty->fetch('ChainTask/ChainTaskAddEdit.html');
$js = array('js/includes/ChainTaskAddEdit.js');
Template::page($pageTitle, $pageContent, $js);
// }}}

?>
