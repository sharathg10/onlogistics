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

require_once('FormatNumber.php');

/**
 * calcul le Metre lineaire
 * prend en paramètre :
 * 			$length : longueur du colis
 * 			$width : largeur du colis
 * 			$Height : hauteur du colis
 * 			$prioritydim :	dimension prioritaire du colis
 * 			$gerbability : gerbabilité du colis
 * 			$quantity : quantité de colis commandé
 * 
 * retourne le Metre lineaire (surface utilisé par la commande)
 * 
 * @access public 
 * @return void 
 */
function MLcompute($length, $width, $height, $prioritydim, $gerbability, $quantity)
{
    switch ($prioritydim) {
        case 0: // dimension prioritaire : non affectée
            $UnitSurface = $width * $length;
            break;
        case 1: // dimension prioritaire : longueur
            $UnitSurface = $width * $height;
            break;
        case 2: // dimension prioritaire : largeur
            $UnitSurface = $length * $height;
            break;
        case 3: // dimension priotitaire : hauteur
            $UnitSurface = $width * $length;
            break;
    } 
    // calcul du nombre de pile de produit en fonction de la gerbabilité :
	require_once('FormatNumber.php');
    $nb_column = $quantity / ($gerbability + 1); 
    // calcul de la surface utilisée par la commande :
    $total_ML = I18N::formatNumber($nb_column * $UnitSurface);

    return $total_ML;
} 

?>
