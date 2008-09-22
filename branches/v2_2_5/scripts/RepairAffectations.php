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
 * @version   SVN: $Id: RepairOccupiedLocation.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

define('SKIP_CONNECTION', true);
define('MAPPER_CACHE_DISABLED', true);

require_once('config.inc.php');
require_once('lib/SQLRequest.php');
require_once('lib/Objects/MovementType.const.php');


if (count($_SERVER['argv']) < 3) {
    echo 'SVP veuillez passer un DSN et une liste d\'ids de produits.';
    exit;
}

foreach($GLOBALS['DSNS'] as $dsn_name) {
    if ($dsn_name != $_SERVER['argv'][1]) {
        continue;
    }
    $dsn = constant($dsn_name);
    if (substr_count($dsn, '/') == 4) {
        // XXX compte qui n'a pas de base propre: les crons ne sont pas
        // executées
        continue;
    }
    Database::connection($dsn);
    $pdtIds = array_slice($_SERVER['argv'], 2);
    foreach ($pdtIds as $id) {
        $product = Object::load('Product', $id);
        // duplique la chaine fabrication et remplace ce qui est nomenclature
        $ref = 'FABRICATION';
        $chain = Object::load('Chain', array('Reference'=>$ref));
        if (!($chain instanceof Chain)) {
            printf(
                _('You must create a chain with reference "%s"'),
                $ref
            );
            exit(1);
        }
        $nomenclature = Object::load('Nomenclature', array('Product'=>$product->getId()));
        $coll = $nomenclature->getComponentCollection(array('Level' => 0));
        $component = $coll->getItem(0);
        include_once 'DuplicateChain.php';
        include_once 'Objects/Task.const.php';
        $newRef   = $product->getBaseReference();
        $newDesc  = $chain->getDescription() . ' ' . $newRef;
        $newChain = duplicateChain($chain, $newRef, $newDesc);
        $opeCol = $newChain->getChainOperationCollection();
        foreach ($opeCol as $ope) {
            $taskCol = $ope->getChainTaskCollection();
            foreach ($taskCol as $task) {
                $taskId = $task->getTaskId();
                if ($taskId == TASK_INTERNAL_STOCK_EXIT) {
                    $col = $component->getComponentCollection();
                    $task->setComponentCollection($col);
                    $task->save();
                } else if ($taskId == TASK_INTERNAL_STOCK_ENTRY) {
                    $col = $product->getComponentCollection(array('Level'=>0));
                    $task->setComponentCollection($col);
                    $task->save();
                } else if ($taskId == TASK_SUIVI_MATIERE) {
                    $task->setComponent($component);
                    $task->save();
                }
            }
        }
        $newChain->save();
        $ref = $newChain->getReference();
        if ($ref == 'lc') {
            $chainCol = Object::loadCollection(
                'Chain',
                array('AutoAssignTo' => Chain::AUTOASSIGN_PRODUCTS)
            );
        } else if ($ref == 'af') {
            $chainCol = Object::loadCollection(
                'Chain',
                array('AutoAssignTo' => Chain::AUTOASSIGN_MATERIALS)
            );
        } else {
            $chainCol = Object::loadCollection(
                'Chain',
                array('Reference' => $ref)
            );
        }
        foreach ($chainCol as $chain) {
            $pcl = new ProductChainLink();
            $pcl->setChain($chain);
            $pcl->setProduct($product);
            $pcl->save();
        }
    }
}


?>
