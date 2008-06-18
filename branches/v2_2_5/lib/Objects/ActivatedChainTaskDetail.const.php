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

require_once ('Objects/ActivatedChainTaskDetail.php');
require_once ('Objects/AeroConcreteProduct.php');

/**
 * Retourne les types de place lors d'un vol pour un instructeur
 * @access public
 * @return array 
 **/
function getInstructorSeatTypeArray(){
	return array(ActivatedChainTaskDetail::AUCUN => _('None'),
				 ActivatedChainTaskDetail::PILOTE_INSTRUCTEUR => _('Pilot (IP)'));
}

/**
 * Retourne les types de place lors d'un vol pour un client
 * @access public
 * @return array 
 **/
function getCustomerSeatTypeArray(){
	return array(ActivatedChainTaskDetail::AUCUN => _('None'), 
				 ActivatedChainTaskDetail::PILOTE_ELEVE => _('Pilot (EP)'), 
				 ActivatedChainTaskDetail::COPILOTE => _('Co-pilot student (CP)'));
}

/**
 * Retourne les types de place de pilote lors d'un vol
 * @access public
 * @return array 
 **/
function getPilotSeatArray(){
	return array(ActivatedChainTaskDetail::PILOTE, ActivatedChainTaskDetail::PILOTE_ELEVE, ActivatedChainTaskDetail::PILOTE_INSTRUCTEUR);
}


$volumeUnitConversions = array(
		AeroConcreteProduct::KILOGRAMME => array(AeroConcreteProduct::LITRE => 1.3888),
		AeroConcreteProduct::GALLON => array(AeroConcreteProduct::LITRE => 3.7854),
		AeroConcreteProduct::LITRE => array(AeroConcreteProduct::KILOGRAMME => 0.72,
					   AeroConcreteProduct::GALLON => 0.2642),
		AeroConcreteProduct::PERCENT => array(AeroConcreteProduct::LITRE => 2)
);

/**
 * Convertit les unites de volume
 * 
 * @param float $value : valeur a convertir
 * @param integer $inUnit : unite en entree
 * @param integer $outUnit : unite en sortie
 * Ces parametres peuvent prendre comme valeur: AeroConcreteProduct::KILOGRAMME, AeroConcreteProduct::LITRE, AeroConcreteProduct::GALLON, PERCENT...
 * @access public
 * @return float 
 **/
function convertVolume($value, $inUnit, $outUnit=AeroConcreteProduct::LITRE) {
	global $volumeUnitConversions;
	// Pas besoin de conversion dans ce cas
	if ($inUnit == $outUnit) {
	    return $value;
	}
	$conversions = $volumeUnitConversions[(int)$inUnit];
	if (isset($conversions[(int)$outUnit])) {
	    return round($value * $conversions[(int)$outUnit], 2);
	}
	else {
		Template::errorDialog('Undefined conversion.', 'home.php');
		exit;
	}
}

?>
