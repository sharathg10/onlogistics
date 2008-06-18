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
require_once('Objects/MovementType.const.php');

$auth = Auth::singleton();

$mapper = Mapper::singleton('ActivatedMovement');
$acmColl = $mapper->loadCollection(
        array('Type' => array(ENTREE_INTERNE, SORTIE_INTERNE)));
for($i = 0; $i < $acmColl->getCount(); $i++){
    $acm = $acmColl->getItem($i);
    $mvtType = $acm->getTypeId();

    $ack = $acm->getActivatedChainTask();
    if (!($ack instanceof ActivatedChainTask)) {
        $quantity = 0;
        continue;
    }
    $cCollection = $ack->getComponentCollection(
            array('Product' => $acm->getProductId()));
    //La liste retourne un productcommanditem unique
    $component = $cCollection->getItem(0);
    if($component instanceof Component) {
        if ($mvtType == ENTREE_INTERNE) {
            $qty = 1;
            $method = 'getPreviousTaskFromRule';
        } else {
            $qty = $component->getQuantity(true);
            $method = 'getNextTaskFromRule';
        }
        $assemblyAck = $ack->$method('isAssemblyTask');
        if ($assemblyAck) {
            $quantity = $qty * $assemblyAck->getAssembledQuantity();
        }
    } else {
        // Recuperation de la quantite via le commanditem
        $productCommand = $acm->getProductCommand();
        $pciCollection = $productCommand->getCommandItemCollection(
                array('Product' => $acm->getProductId()));
        // La liste retourne un commanditem unique
        $pci = $pciCollection->getItem(0);
        if ($pci instanceof CommandItem) {
            $quantity = $pci->getQuantity();
        }
    }
    $acm->setQuantity($quantity);
    try {
        saveInstance($acm, null, false);
    } catch(Exception $e) {
        echo $e->getMessage();
        exit();
    }
    echo $quantity . '<br>';
}
echo 'Fin du traitement.'

?>
