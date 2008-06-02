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

$mapper = Mapper::singleton('Operation');
$collection  = $mapper->loadCollection();

// construction de la collection des tolérances
/**
 * @access public 
 * @return void 
 */

echo 'var OperationToleranceCollection = new TCollection ();';
$count = $collection->getCount();
for ($i = 0; $i < $count; $i++){
    $op = $collection->getItem($i);
    tolerance($op->getId(), $op->getFrontTolerance(), 
		$op->getEndTolerance(), $op->getTotalTolerance());
}

/**
 * tolerance
 * 
 * @param  $opId --> id de l'operation courrante
 * @param  $FrontTol --> tolerance avant
 * @param  $EndTol --> tolerance après
 * @param  $TotalTol --> tolerance total 
 * 
 * construit l'objet JS Operation' . $opId . '
 * et l'ajoute à la collection OperationToleranceCollection
 */

function tolerance($opId, $frontTol, $endTol, $totalTol){ 
    // construction de l'object Tolerance
    echo "var Operation" . $opId . " = new TOperationTolerance ();\n";
    echo "Operation" . $opId . ".setOperationId('" . $opId . "');\n";
    echo "Operation" . $opId . ".setFrontTolerance('" . $frontTol . "');\n";
    echo "Operation" . $opId . ".setEndTolerance('" . $endTol . "');\n";
    echo "Operation" . $opId . ".setTotalTolerance('" . $totalTol . "');\n"; 
    // ajout de l'objet à la collection
    echo "OperationToleranceCollection.addItem(Operation" . $opId . ");\n";
	
}

?>