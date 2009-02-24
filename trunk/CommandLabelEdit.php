<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of Onlogistics, a web based ERP and supply chain 
 * management application. 
 *
 * Copyright (C) 2003-2009 ATEOR
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
 * @version   SVN: $Id: config.inc.php 45 2008-06-19 08:15:01Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 2.2.6
 * @filesource
 */

require_once 'config.inc.php';
require_once 'DocumentGenerator.php';

$auth = Auth::singleton();
$auth->checkProfiles(array(
    UserAccount::PROFILE_ADMIN,
    UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
    UserAccount::PROFILE_ADMIN_VENTES,
    UserAccount::PROFILE_AERO_ADMIN_VENTES,
    UserAccount::PROFILE_GESTIONNAIRE_STOCK,
));

$mvtInfo = array();

if(is_array($_REQUEST['mvtId'])) {
    foreach ($_REQUEST['mvtId'] as $id) {
        $mvt = Object::load('ActivatedMovement', $id);
        $mvt_date = $mvt->getStartDate();

        $cmd = Object::load('ProductCommand', $mvt->getProductCommandId() );
        $cmd_ref = $cmd->getCommandNo();

        $cmd_supplier = $cmd->getExpeditor();
        $cmd_supplier = $cmd_supplier->getName();

        $cmd_customer= $cmd->getDestinator();
        $cmd_customer= $cmd_customer->getName();

        $mvtInfo[] = array($mvt_date, $cmd_supplier, $cmd_customer, $cmd_ref);
    }
} else {
        $mvt = Object::load('ActivatedMovement', $mvtId);
        $mvt_date = $mvt->getStartDate();

        $cmd = Object::load('ProductCommand', $mvt->getProductCommandId() );
        $cmd_ref = $cmd->getCommandNo();

        $cmd_supplier = $cmd->getExpeditor();
        $cmd_supplier = $cmd_supplier->getName();

        $cmd_customer= $cmd->getDestinator();
        $cmd_customer= $cmd_customer->getName();

        $mvtInfo[] = array($mvt_date, $cmd_supplier, $cmd_customer, $cmd_ref);
}

if (!count($mvtInfo)) {
    Template::errorDialog(E_NO_RECORD_FOUND);
    exit(1);
}

// $mvtInfo = array_unique($mvtInfo); 
// doesn't work on array containing arrays
// so we must serialize each element of the array 

foreach ($mvtInfo as $myArray) {
    $tmpArray[] = serialize($myArray);
}

// then apply array_unique
$tmpArray = array_unique($tmpArray);

// unset final array, and fill it again with each element unserialized
// ... 
unset ($mvtInfo) ;
foreach ($tmpArray as $myArray) {
    $mvtInfo[] = unserialize($myArray);
}

$gen = new MovementLabelGenerator($mvtInfo);
$pdf = $gen->render();
$pdf->output();

?>
