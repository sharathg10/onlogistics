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

require_once ('Objects/Command.php');

$ShortCommandStateArray = array(Command::ACTIVEE => _('Activated'), 
							   	Command::PREP_PARTIELLE => _('Partially prepared'),
							   	Command::PREP_COMPLETE => _('Completely prepared'),
							  	Command::LIV_PARTIELLE => _('Partially delivered'),
							   	Command::LIV_COMPLETE => _('Completely delivered'),
							   	Command::FACT_PARTIELLE => _('Partially charged'),
							   	Command::FACT_COMPLETE => _('Completely charged'),
							   	Command::REGLEMT_PARTIEL => _('Partial payment'),
							   	Command::REGLEMT_TOTAL => _('Total payment'),
							   	Command::BLOCAGE_CDE => _('Locked order'));

$CourseStateArray = array('##' => _('Select one or more states'),
						  Command::ACTIVEE => _('Activated'), 
						  Command::PREP_PARTIELLE => _('Partially prepared'),
						  Command::PREP_COMPLETE => _('Completely prepared'),
						  Command::LIV_COMPLETE => _('Completed flights'),
						  Command::FACT_PARTIELLE => _('Partially charged'),
						  Command::FACT_COMPLETE => _('Completely charged'),
						  Command::REGLEMT_PARTIEL => _('Partially paid'),
						  Command::REGLEMT_TOTAL => _('Completely paid'),
						  Command::BLOCAGE_CDE => _('Locked'));
							
$ShortCourseStateArray = array('##' => _('Select one or more states'),
							   Command::ACTIVEE => _('Activated'), 
							   Command::PREP_PARTIELLE => _('Partially prepared'),
							   Command::PREP_COMPLETE => _('Completely prepared'),
							   Command::LIV_COMPLETE => _('Completed flight'),
							   Command::FACT_PARTIELLE => _('Partially charged'),
							   Command::FACT_COMPLETE => _('Completely charged'),
							   Command::REGLEMT_PARTIEL => _('Partial payment'),
							   Command::REGLEMT_TOTAL => _('Total payment'),
							   Command::BLOCAGE_CDE => _('Locked order'));

$PrestationStateArray = array('##' => _('Select one or more states'),
						  Command::ACTIVEE => _('Activated'), 
						  Command::FACT_PARTIELLE => _('Partially charged'),
						  Command::FACT_COMPLETE => _('Completely charged'),
						  Command::REGLEMT_PARTIEL => _('Partially paid'),
						  Command::REGLEMT_TOTAL => _('Completely paid'));
                          			                                
define('DATE_TYPE_DELIVERY', 0);   // date de type livraison
define('DATE_TYPE_ENLEVEMENT', 1); // date de type enlèvement

// mutualisation messages
define('I_COMMAND_OK', _('Your %s was successfully saved with reference: "%s"'));
define('I_COMMAND_HANDING', _('Given the amount of your order, a discount of %s %% has been applied.'));

/**
 * Retourne l'état de la commande ou 'inconnu'
 * 
 * @access public
 * @param $style (etat long ou court, facultatif)
 * @return string 
 **/
function getCommandStateToString($command, $style='short'){
	$CommandStateArray = $command->getStateConstArray();
	$ShortCommandStateArray = array(
		Command::ACTIVEE => _('Activated'), 
		Command::PREP_PARTIELLE => _('Partially prepared'),
		Command::PREP_COMPLETE => _('Completely prepared'),
		Command::LIV_PARTIELLE => _('Partially delivered'),
		Command::LIV_COMPLETE => _('Completely delivered'),
		Command::FACT_PARTIELLE => _('Partially charged'),
		Command::FACT_COMPLETE => _('Completely charged'),
		Command::REGLEMT_PARTIEL => _('Partial payment'),
		Command::REGLEMT_TOTAL => _('Total payment'),
		Command::BLOCAGE_CDE => _('Locked order')
	);
	$array = $style=='short'?$ShortCommandStateArray:$CommandStateArray;
	if (array_key_exists($command->getState(), $array)) {
	    return $array[$command->getState()];
	}
	return _('Unknown');
}

?>
