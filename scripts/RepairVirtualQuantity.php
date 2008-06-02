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
require_once('SQLRequest.php');
require_once('Objects/ActivatedMovement.php');
require_once('Objects/MovementType.const.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
// Seule dalhia autorisee
if ($Auth->getIdentity() != 'dalhia') {
    Template::errorDialog('Erreur: vous n\'avez pas accès à ce script!!', 'home.php');
}
//Database::connection()->debug = true;


// Traitement des Product impliques dans des Command non entierement livrees

// Recuperation des Qtes
$rs = getQuantities();
$msge = '';

// Doit-on sauver ou non les QV?
$save = isset($_GET['save'])?$_GET['save']:0;

$outQty = $inQty = $outExmQty = $inExmQty = array();
$pdtIdArray = array();  // Les Products concernes

while (!$rs->EOF) {
	$pdtId = intval($rs->fields['pdtId']);
	if (!isset($pdtIdArray[intval($rs->fields['pdtId'])])) {
	    $pdtIdArray[] = intval($rs->fields['pdtId']);
	}
	
	// Si sortie de stock
	if (intval($rs->fields['mvtId']) == SORTIE_NORMALE) {
		// Sorties prevues
		$outQty[$pdtId] = (isset($outQty[$pdtId]))?$outQty[$pdtId]:0;
		$outQty[$pdtId] += floatval($rs->fields['aciQty']);
		// Si le mvt est partiellement execute, on tient compte des qtes deja mvtees
		if (!is_null($rs->fields['deliveredQty'])) {
			$outExmQty[$pdtId] = (isset($outExmQty[$pdtId]))?$outExmQty[$pdtId]:0;
	        $outExmQty[$pdtId] += floatval($rs->fields['deliveredQty']);
	    }
    }
	
	else {  // ENTREE_NORMALE
		// Entrees prevues
		$inQty[$pdtId] = (isset($inQty[$pdtId]))?$inQty[$pdtId]:0;
		$inQty[$pdtId] += floatval($rs->fields['aciQty']);
		// Si le mvt est partiellement execute, on tient compte des qtes deja mvtees
		if (!is_null($rs->fields['deliveredQty'])) {
			$inExmQty[$pdtId] = (isset($inExmQty[$pdtId]))?$inExmQty[$pdtId]:0;
	        $inExmQty[$pdtId] += floatval($rs->fields['deliveredQty']);
	    }
	}
	$rs->moveNext();
}

$count = count($pdtIdArray);
for($i = 0; $i < $count; $i++){
	$pdtId = $pdtIdArray[$i];
	$outQty[$pdtId] = (isset($outQty[$pdtId]))?$outQty[$pdtId]:0;
	$outExmQty[$pdtId] = (isset($outExmQty[$pdtId]))?$outExmQty[$pdtId]:0;
	$inQty[$pdtId] = (isset($inQty[$pdtId]))?$inQty[$pdtId]:0;
	$inExmQty[$pdtId] = (isset($inExmQty[$pdtId]))?$inExmQty[$pdtId]:0;
	$Product = Object::load('Product', $pdtId);
	$QV = $Product->getSellUnitVirtualQuantity();
	$QR = $Product->getRealQuantity();
	$newQV = $QR + $inQty[$pdtId] - $inExmQty[$pdtId] - $outQty[$pdtId] + $outExmQty[$pdtId];
	if ($newQV != $QV) {
	    echo 'Product ' . $Product->getBaseReference() . '(id=' . $pdtId . '): '
			. 'ancienne QV: ' . $QV . ' et la nouvelle: ' . $newQV . '<br>';
		if ($save == 1) {
		    $Product->setSellUnitVirtualQuantity($newQV);
			$Product->save();
		}
	}
}


// Traitement des autres Product
$FilterComponent = SearchTools::NewFilterComponent('Id', '', 'NotIn', $pdtIdArray, 1);
$ProductMapper = Mapper::singleton('Product');

$ProductCollection = $ProductMapper->loadCollection($FilterComponent);
$count = $ProductCollection->getCount();
for($i = 0; $i < $count; $i++) {
	$Product = $ProductCollection->getItem($i);
	$QV = $Product->getSellUnitVirtualQuantity();
	$QR = $Product->getRealQuantity();
	if ($QR != $QV) {
	    echo 'Product ' . $Product->getBaseReference() . ' (id=' . $Product->getId() . '): '
			. 'ancienne QV: ' . $QV . ' et la nouvelle: ' . $QR . ' (=QV)<br>';
		if ($save == 1) {
		    $Product->setSellUnitVirtualQuantity($newQV);
			$Product->save();
			$msge = '<font color="red"><b>Les données ont été sauvées en base.</b></font>';
		}
	}
}

echo '<hr><b>Fin d\'exécution du script.</b><br>';
echo $msge;
?>
