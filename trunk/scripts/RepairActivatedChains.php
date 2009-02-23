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

/**
 * Script qui parcours les chaines deja activees, descendantes de la chaine 
 * modele passee en parametre (ex: LC), et qui pour chaque chaine, ajoute une 
 * operation de regroupement composee de 2 taches (regroupement et edition 
 * packing list).
 *
 * Ce script va permettre de faire en sorte que les chaines activees par une 
 * chaine mal parametree a l'origine (c'etait le cas de la chaine LC) soient 
 * quand meme listees dans l'ecran de regroupement.
 *
 * Usage:
 * La ligne de commande ci-dessous n'execute pas reelement les requetes:
 * $ php scripts/RepairActivatedChains.php
 *
 * Pour executer pour de bon:
 * $ php scripts/RepairActivatedChains.php -f
 *
 */

error_reporting(E_ALL);

define('SKIP_CONNECTION', true);
define('MAPPER_CACHE_DISABLED', true);

require_once('config.inc.php');

Database::connection(DSN_MALOLES);

$chain = Object::load('Chain', array('Reference' => 'LC'));

if (!($chain instanceof Chain)) {
    echo "Error: Chain \"LC\" does not exists\n";
    exit(1);
}

$opeCol = $chain->getChainOperationCollection();
if (count($opeCol) != 3) {
    echo "Error: Chain \"LC\" must contain 3 operations\n";
    exit(1);
}

list(,$groupingOpe,) = $opeCol;
list($groupingTask, $packingListTask) = $groupingOpe->getChainTaskCollection();

$achCol = Object::loadCollection('ActivatedChain', array('Reference' => 'LC'));

Database::connection()->startTrans();
echo ">>> Transaction started\n";

foreach ($achCol as $ach) {
    $orderId = Tools::getValueFromMacro($ach, '%CommandItem()[0].Command.Id%');
    $acoCol  = $ach->getActivatedChainOperationCollection();

    if (!$orderId) {
        printf(">>> Skipping ActivatedChain with id \"%s\": no related order\n", $ach->getId());
        continue;
    }
    if (count($acoCol) == 3) {
        printf(">>> Skipping ActivatedChain with id \"%s\": seems already repaired\n", $ach->getId());
        continue;
    }
    if (count($acoCol) != 2) {
        printf(">>> Skipping ActivatedChain with id \"%s\": invalid structure", $ach->getId());
        continue;
    }

    printf(">>> Processing ActivatedChain with id \"%s\"\n", $ach->getId());

    list($firstOperation, $lastOperation) = $acoCol;
    list($firstTask1, $firstTask2) = $firstOperation->getActivatedChainTaskCollection();
    list($lastTask) = $lastOperation->getActivatedChainTaskCollection();

    $lastOperation->setOrder(2);
    $lastOperation->save();

    $newAco = new ActivatedChainOperation();
    $newAco->generateId();

    $newAch1 = new ActivatedChainTask();
    $newAch1->setOrder(0);
    $newAch1->setInteruptible(1);
    $newAch1->setRawDuration(600);
    $newAch1->setDuration(600);
    $newAch1->setWithForecast(1);
    $newAch1->setActivatedOperation($newAco->getId());
    $newAch1->setTask($groupingTask->getTaskId());
    $newAch1->setActorSiteTransition($firstTask2->getActorSiteTransitionId());
    $newAch1->setBegin($firstTask2->getEnd());
    $newAch1->setEnd(DateTimeTools::mySQLDateAdd($firstTask2->getEnd(), '00:10:00'));
    $newAch1->save();

    $newAch2 = new ActivatedChainTask();
    $newAch2->setOrder(1);
    $newAch2->setInteruptible(1);
    $newAch2->setRawDuration(600);
    $newAch2->setDuration(600);
    $newAch2->setWithForecast(1);
    $newAch2->setActivatedOperation($newAco->getId());
    $newAch2->setTask($packingListTask->getTaskId());
    $newAch2->setActorSiteTransition($firstTask2->getActorSiteTransitionId());
    $newAch2->setBegin($newAch1->getEnd());
    $newAch2->setEnd(DateTimeTools::mySQLDateAdd($newAch1->getEnd(), '00:10:00'));
    $newAch2->save();

    $lastTask->setBegin(DateTimeTools::mySQLDateAdd($lastTask->getBegin(), '00:20:00'));
    $lastTask->setEnd(DateTimeTools::mySQLDateAdd($lastTask->getEnd(), '00:20:00'));
    $lastTask->save();

    $newAco->setOperation($groupingOpe->getOperationId());
    $newAco->setActivatedChain($ach->getId());
    $newAco->setOrder(1);
    $newAco->setTaskCount(2);
    $newAco->setActor($firstOperation->getActorId());
    $newAco->setFirstTask($newAch1->getId());
    $newAco->setLastTask($newAch2->getId());
    $newAco->save();
}

if (isset($_SERVER['argv'][1]) && in_array($_SERVER['argv'][1], array('-f', '--force'))) {
    Database::connection()->completeTrans();
    echo ">>> Transaction commited\n";
} else {
    Database::connection()->rollbackTrans();
    echo ">>> Transaction rolled back\n";
}

?>
