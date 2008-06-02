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
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

$retUrl = 'NomenclatureConcreteProductList.php';
if (!isset($_REQUEST['cpId'])) {
    Template::errorDialog(E_MSG_TRY_AGAIN, $retUrl);
    exit;
}
$ConcreteProduct = Object::load('ConcreteProduct', $_REQUEST['cpId']);
if (Tools::isEmptyObject($ConcreteProduct)) {
    Template::errorDialog(E_MSG_TRY_AGAIN, $retUrl);
    exit;
}
if ($ConcreteProduct->getComponentId() > 0) {
    Template::errorDialog(_('This part is already linked to a nomenclature.'), $retUrl);
    exit;
}

// On verifie s'il existe bien une Nomenclature modele associee au CP
$NomenclatureMapper = Mapper::singleton('Nomenclature');
$Nomenclature = $NomenclatureMapper->load(
        array('Product' => $ConcreteProduct->getProductId()));
if (Tools::isEmptyObject($Nomenclature)) {
    Template::errorDialog(_('Associated nomenclature model could not be found.'), $retUrl);
    exit;
}
// Si non constructible, on refuse
// (Si contient des Components non suivis au SN)
if ($Nomenclature->getBuildable() == 0) {
    Template::errorDialog(_('Associated nomenclature is not "buildable".'), $retUrl);
    exit;
}

// C'est OK: on enregistre en base:
Database::connection()->startTrans();

$ComponentMapper = Mapper::singleton('Component');
$Component = $ComponentMapper->load(
        array('Nomenclature' => $Nomenclature->getId(), 'Level' => 0));
$ConcreteProduct->setComponent($Component);
$ConcreteProduct->addRemoveChild($_REQUEST['cpId']);
saveInstance($ConcreteProduct, $retUrl);

if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    Database::connection()->rollbackTrans();
    Template::errorDialog(E_MSG_TRY_AGAIN, $errorURL);
    Exit;
}
Database::connection()->completeTrans();

Template::infoDialog(_('Part has been successfully assigned.'), $retUrl);

?>
