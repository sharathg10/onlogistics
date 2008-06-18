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
require_once('GenerateDocument.php');

// Ne marche pas sous ie si on décommente l'auth?
$auth = Auth::singleton();

$errorBody = _('Error: delivery order could not be printed.');

if (!isset($_REQUEST['reedit']) && !isset($_REQUEST['Cmd'])
    && !isset($_REQUEST['LEM'])) {
	// Si edition et pas de commmande passee ds l'url
    Template::errorDialog($errorBody, 'javascript:window.close();', BASE_POPUP_TEMPLATE);
    exit;
}
if (isset($_REQUEST['reedit']) && empty($_REQUEST['idBL'])) {
	// Si reedition et pas de idBL
    Template::errorDialog(_('Please select a delivery order.'), 'javascript:window.close();', BASE_POPUP_TEMPLATE);
    exit;
}


$idBL = isset($_REQUEST['idBL'])?$_REQUEST['idBL']:0;
$DeliveryOrder = Object::load('DeliveryOrder', $idBL);

if (!$idBL) {
    if (isset($_REQUEST['LEM'])) {
        $LEM = Object::load('LocationExecutedMovement', $_REQUEST['LEM']);
        $ExecutedMovement = $LEM->getExecutedMovement();
        if (Tools::isEmptyObject($ExecutedMovement)) {
            Template::errorDialog($errorBody, 'javascript:window.close();', BASE_POPUP_TEMPLATE);
            exit;
        }
        // Pour editer un BL, il faut que ce soit une sortie, et prevu,
        // et qu'il n'y ait pas deja 1 BL edite pour ce mvt
        if (false == $LEM->isBLEditionPossible()) {
            Template::errorDialog(
                _('A delivery order can only be printed for an issue related to an expected movement and when no other delivery order was printed for this movement.'), 
                'javascript:window.close();', BASE_POPUP_TEMPLATE);
            exit;
        }
        $CommandId = Tools::getValueFromMacro($ExecutedMovement,
            '%ActivatedMovement.ProductCommandItem.Command.id%');
    } else {
        $CommandId = $_REQUEST['Cmd'];
    }
    $Command = Object::load('ProductCommand', $CommandId);
    if (Tools::isEmptyObject($Command)) {
        Template::errorDialog($errorBody, 'javascript:window.close();', BASE_POPUP_TEMPLATE);
        exit;
    }
    $DeliveryOrderId = $DeliveryOrder->generateId();
    if (isset($_REQUEST['LEM'])) {
        $EditionDate = $LEM->getDate();
    } else {
	    $EditionDate = date('Y-m-d H:i:s');
    }
    $DeliveryOrder->setEditionDate($EditionDate);
    require_once('InvoiceItemTools.php');
    $DocumentNo = generateDocumentNo('', 'AbstractDocument', $DeliveryOrderId);
    $DeliveryOrder->setDocumentNo($DocumentNo);
    $DeliveryOrder->setCommand($Command);
    $DocumentModel = $DeliveryOrder->FindDocumentModel();
    if (!(false == $DocumentModel)) {
        $DeliveryOrder->setDocumentModel($DocumentModel);
    }
}

// affichage du document
generateDocument($DeliveryOrder, $idBL>0);

if (!$idBL) {
    // Si edition du document au format pdf est OK
    saveInstance($DeliveryOrder, 'javascript:window.close();');
}

?>
