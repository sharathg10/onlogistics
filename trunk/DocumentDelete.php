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
require_once('ExecutedMovementTools.php');
require_once('Objects/MovementType.const.php');

if(!isset($_REQUEST['id'])) {
    Template::errorDialog(E_MSG_TRY_AGAIN, 'DocumentList.php');
    exit();
}

Database::connection()->startTrans();

foreach ($_REQUEST['id'] as $id) {
    $doc = Object::load('ForwardingForm', $id);
    $FFP_products = array();
    $ffpCol = $doc->getForwardingFormPackingCollection(
        array('CoverType'=>0));
    $count = $ffpCol->getCount();
    for($i=0 ; $i<$count ; $i++) {
        $ffp = $ffpCol->getItem($i);
        $FFP_products[] = $ffp->getProductId();
    }
    
    $lemCol = $doc->getLocationExecutedMovementCollection();
    $exmCol = new Collection();
    $exmCol->acceptDuplicate = false;
    $count = $lemCol->getCount();
    for ($i=0 ; $i<$count ; $i++) {
        $lem = $lemCol->getItem($i);
        // si le lem est associé à un produit associé à un des ForwardingFormPacking
        if(in_array($lem->getProductId(), $FFP_products)) {
            // on remet à jour les LPQ
            $exm = $lem->getExecutedMovement();
            $exmCol->setItem($exm);
            $mvtType = Tools::getValueFromMacro($exm, 
                '%Type.EntrieExit%');
            $lpq = getLocationProductQuantities($lem->getProduct(), 
                $lem->getLocation(), $mvtType, 'DocumentList.php');
            updateLPQQuantity($lpq, $lem->getQuantity(), 
                $mvtType==ENTREE?SORTIE:ENTREE, 'DocumentList.php');
        } else {
            $lem->setForwardingForm(0);
            saveInstance($lem, 'DocumentList.php');
        }
    }
    // suppression des EXM si nécéssaire (et donc LEM)
    $count = $exmCol->getCount();
    for($i=0 ; $i<$count ; $i++) {
        $exm = $exmCol->getItem($i);
        deleteInstance($exm, 'DocumentList.php');
    }
    deleteInstance($doc, 'DocumentList.php');
}

if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
	Database::connection()->rollbackTrans();
    Template::errorDialog(E_ERROR_SQL, 'DocumentList.php');
	Exit;
}
Database::connection()->completeTrans();

Tools::redirectTo('DocumentList.php');
?>
