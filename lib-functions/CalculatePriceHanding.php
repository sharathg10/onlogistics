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

function CalculatePriceHanding($handingType, $price, $quantity, $handing){
	switch($handingType){
		case 'currency': 
			return HandingCurrency($price, $quantity, $handing);
		case 'percent': 
			return HandingPercent($price, $quantity, $handing);
		case 'frac': 
			return HandingFrac($price, $quantity, $handing);
		default:
			return $price * $quantity;
	}
}
 

/**
 * HandingCurrency() 
 * remise de type euro * 
 * 
 * @param  $price 
 * @param  $quantity 
 * @param  $handing 
 * @return 
 */
function HandingCurrency($price, $quantity, $handing)
{
    return (($price - $handing) * $quantity);
} 

/**
 * HandingFrac()
 * remise de type fraction
 * 
 * @param $price
 * @param $quantity
 * @param $handing
 * @return 
 **/
function HandingFrac($price, $quantity, $handing)
{
    $handingFracArray = explode('/', $handing);
    $handingFrac1 = $handingFracArray[0];
    $handingFrac2 = $handingFracArray[1];

    if ($handingFrac2 != 0) {
        return ($price * ($quantity - floor($quantity * 
            ($handingFrac1 / $handingFrac2))));
    } else {
        return ($price * $quantity);
    } 
} 

/**
 * HandingPercent()
 * remise de type pourcentage
 * 
 * @param $price
 * @param $quantity
 * @param $handing
 * @return 
 **/
function HandingPercent($price, $quantity, $handing)
{
    $handingPercent = str_replace('%', '', $handing);
    return ($price * (1 - $handingPercent / 100) * $quantity);
} 

function getHandingType($handing) {
    if (strpos($handing, '/') !== false) {
        $type = 'frac';
    } elseif (strpos($handing, '%') !== false) {
        $type = 'percent';
    } elseif (is_numeric($handing)) {
        $type = 'currency';
    } else {
        $type = 'N/A';
    }
    return $type;
}
?>
