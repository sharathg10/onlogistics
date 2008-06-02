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
require_once('TreeMenu.php');
$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

$errorUrl = 'NomenclatureConcreteProductList.php';
$error = false;
if (!isset($_REQUEST['cpId'])) {
    $error = true;
}
else {
    $cp = Object::load('ConcreteProduct', $_REQUEST['cpId']);
    if (Tools::isEmptyObject($cp)) {
        $error = true;
    }
    else {  // Erreur si pas deja affecte a un Component de niveau 0
        $nomId = Tools::getValueFromMacro($cp, '%Component.Nomenclature.Id%');
        $Nomenclature = Object::load('Nomenclature', $nomId);
    }
}
if ($error || Tools::isEmptyObject($Nomenclature)) {
    Template::errorDialog(E_MSG_TRY_AGAIN, $errorUrl);
  	exit;
}

$bygroup = (isset($_REQUEST['byGroup']) && $_REQUEST['byGroup'] == 1);
$viewType = ($bygroup)?_('sets'):_('components');
$getItemsMethod = ($bygroup)?'getGroupTreeItems':'getTreeItems';

$pageTitle = sprintf(
    _('Nomenclature "%s" version "%s": view by %s'),
    Tools::getValueFromMacro($Nomenclature, '%Product.BaseReference%'),
    $Nomenclature->getVersion(),
    $viewType
);

// Pour gerer correctement les redirections
$_SESSION['arboURL'] = $_SERVER['PHP_SELF'] . '?cpId=' . $_REQUEST['cpId'];
$_SESSION['arboURL'] .= ($bygroup)?'&byGroup=1':'';
SearchTools::ProlongDataInSession();

$TreeItems = $Nomenclature->$getItemsMethod($_REQUEST['cpId']);
$TreeMenu = new TreeMenu();
$TreeMenu->_items = array($TreeItems);

$smarty = new Template();
$smarty->assign('returnURL', $errorUrl);
$smarty->assign('tree', $TreeMenu->render());

$pageContent = $smarty->fetch('Nomenclature/ComponentsTree.html');
Template::page($pageTitle, $pageContent, $TreeMenu->getJSrequirements());
?>