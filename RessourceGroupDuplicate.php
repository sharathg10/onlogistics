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

//Database::connection()->debug = true;

$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN,
    UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
    
SearchTools::prolongDataInSession();

$retURL = isset($_REQUEST['retURL']) ? 
    $_REQUEST['retURL'] : 'dispatcher.php?entity=RessourceGroup';

if (!isset($_REQUEST['rsgID'])) {
    Template::errorDialog(I_NEED_SINGLE_ITEM, $retURL);
    exit;
}

$rsg = Object::load('RessourceGroup', $_REQUEST['rsgID']);

if (isset($_POST['ok'])) {
    Database::connection()->startTrans();

    $newRsg = Tools::duplicateObject($rsg);
    // Assigne le nouveau nom
    $newRsg->setName($_POST['rsgName']);
    // copie des RessourceRessourceGroup associés
    $rrgCol = Object::loadCollection(
        'RessourceRessourceGroup',
        array('RessourceGroup'=>$rsg->getId())
    );
    $count = $rrgCol->getCount();
    for ($i=0; $i<$count; $i++) {
        $rrg = $rrgCol->getItem($i);
        $newRrg = Tools::duplicateObject($rrg);
        $newRrg->setRessourceGroup($newRsg->getId());
        $newRrg->save();
    }
    // copie des ProductRessourceGroup associés
    // TODO
    saveInstance($newRsg, $retURL);
    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
    }
    Database::connection()->completeTrans();
    Tools::redirectTo($retURL);
}

$smarty = new Template();
$smarty->assign('formAction', $_SERVER['PHP_SELF']);
$smarty->assign('rsgID', $_REQUEST['rsgID']);
$smarty->assign('retURL', $retURL);
$smarty->assign('rsgName', $rsg->getName() . '_copy');
$content = $smarty->fetch('RessourceGroup/RessourceGroupDuplicate.html');

Template::page(
    sprintf(_('Resource model "%s" copy'), $rsg->getName()),
    $content
);

?>
