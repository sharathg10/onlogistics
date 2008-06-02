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

define('SKIP_CONNECTION', true);
define('MAPPER_CACHE_DISABLED', true);

require_once('config.inc.php');
require_once('lib/SQLRequest.php');
require_once('lib/Objects/MovementType.const.php');


if (count($_SERVER['argv']) != 3) {
    echo 'SVP veuillez passer un creneau de dates (AAAA-MM-JJ) en paramËtres.';
    exit;
}

function searchOL($OLarray, $locId, $pdtId) {
    foreach ($OLarray as $key => $val) {
    	if ($val['locId'] == $locId && $val['pdtId'] == $pdtId) {
    	    return $key;
    	}elseif ($val['locId'] > $locId || ($val['locId'] == $locId && $val['pdtId'] > $pdtId)) {
    	    return false;  // Car tries
    	}
    }
    return false;
}

foreach($GLOBALS['DSNS'] as $dsn_name) {
    $dsn = constant($dsn_name);
    if (substr_count($dsn, '/') == 4) {
        // XXX compte qui n'a pas de base propre: les crons ne sont pas
        // executÈes
        continue;
    }
    Database::connection($dsn);
    //Database::connection()->debug = true;
    // Verif du creneau de dates passe en param

    $begin = $_SERVER['argv'][1] . ' 00:00:00';
    // Attention, en ce moment,  la tache cron s'execute a 20h
    // Il faut verifier qu'il n'y a pas de LEM entre cette heure et 23:59:59
    $end = $_SERVER['argv'][2] . ' 23:59:59';

    // Verif que pour le creneau choisi, il n'existe AUCUN OL
    $olMapper = Mapper::Singleton('OccupiedLocation');
    $filterCompArray = array(
        SearchTools::NewFilterComponent('CreationDate', '', 'GreaterThanOrEquals', $begin, 1),
        SearchTools::NewFilterComponent('CreationDate', '', 'LowerThanOrEquals', $end, 1));

    $filter = SearchTools::filterAssembler($filterCompArray);
    $testColl = $olMapper->loadCollection($filter, array(), array('Id'));
    if ($testColl->getCount() > 0) {
        printf("Il existe des OccupiedLocations, rien n'a √©t√© fait sur la base $dsn_name\n");
        continue;
    }
    // On peut commencer le traitement
    $lemMapper = Mapper::Singleton('LocationExecutedMovement');
    $currentBegin = $begin;
    while ($currentBegin < $end) {
        // (86400 = nb de secondes ds un jour) - NE GERE PAS LE PASSAGE heure ete/hiver!!
        $currentBeginTS = DateTimeTools::mySQLDateToTimeStamp($currentBegin);
        $dayBefore = date('Y-m-d', $currentBeginTS - 1);  // La veille
        $currentEnd = date('Y-m-d 23:59:59', $currentBeginTS);
        //echo '###### $currentBegin=' . $currentBegin . ' et $currentEnd=' . $currentEnd . ' ######';    
        // Les OL de la veille sur lesquels on va se baser
        $dayBeforeOLarray = array();
        $rs = request_getLastDayOLs($dayBefore);
        while (!$rs->EOF) {
            $dayBeforeOLarray[] = array(
                'locId' => $rs->fields['locId'], 
                'pdtId' => $rs->fields['pdtId'], 
                'qty' => $rs->fields['qty']
            );
    	    $rs->moveNext();
        }
        $filterCompArray = array(
            SearchTools::NewFilterComponent('Date', '', 'GreaterThanOrEquals', $currentBegin, 1),
            SearchTools::NewFilterComponent('Date', '', 'LowerThanOrEquals', $currentEnd, 1),
            SearchTools::NewFilterComponent('Cancelled', '', 'Equals', 0, 1)
        );
        $filter = SearchTools::filterAssembler($filterCompArray);
        $lemColl = $lemMapper->loadCollection(
            $filter, array(), array('Location', 'Product', 'Quantity', 'ExecutedMovement'));
        // TODO: GESTION DES ANNULATIONS / REINTEGRATIONS!!!!!
        $lemcount = $lemColl->getCount();
        for ($i=0; $i<$lemcount; $i++) {
            $lem = $lemColl->getItem($i);
            // 1: sortie; 0: entree
            $entrieExit = Tools::getValueFromMacro($lem, '%ExecutedMovement.Type.EntrieExit%');
            $coef = ($entrieExit)?-1:1;
            $olKey = searchOL($dayBeforeOLarray, $lem->getLocationId(), $lem->getProductId());
            // Pas de OL trouve, ca doit etre un entree
            if ($olKey === false) {
                if ($entrieExit == 0) {
                    printf("bizarre car pas d OL et sortie de stock: lemId={$lem->getId()} sur base $dsn_name\n");
                    continue;
                }
                // Il faut creer le OL dans ce cas
                $dayBeforeOLarray[] = array(
                    'locId' => $lem->getLocationId(), 
                    'pdtId' => $lem->getProductId(), 
                    'qty' => $lem->getQuantity()
                );
            } else {
                // Le OL existe
                $dayBeforeOLarray[$olKey]['qty'] += $coef * $lem->getQuantity();
                // Si la qte devient nulle, on supprime le OL
                if ($dayBeforeOLarray[$olKey]['qty'] == 0) {
                    unset($dayBeforeOLarray[$olKey]);
                }
            }
        }
        // Reste a sauver les OL pour le jour en cours
        foreach ($dayBeforeOLarray as $val) {
            $ol = new OccupiedLocation;
            //$ol->generateId();
            $ol->setLocation($val['locId']);
            $ol->setProduct($val['pdtId']);
            $ol->setQuantity($val['qty']);
            $ol->setCreationDate(date('Y-m-d', $currentBeginTS));
            $ol->setValidityDate(date('Y-m-d', $currentBeginTS));
            $ol->save();
            unset($ol);
        }
        $currentBegin = date('Y-m-d 00:00:00', $currentBeginTS + 86400); // Le lendemain, 00:00:00
    }
}


?>