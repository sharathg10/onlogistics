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

define('SupplierCustomer::NET', 0);
define('SupplierCustomer::FIN_DE_MOIS', 1);
define('SupplierCustomer::FIN_DE_MOIS_SUIVANT', 2);

/**
 * Options de payment
 * @access public
 * @return void 
 **/
function getOptionNameArray(){
	return array(SupplierCustomer::NET => _('Net'),
				 SupplierCustomer::FIN_DE_MOIS => _('End of month'),
				 SupplierCustomer::FIN_DE_MOIS_SUIVANT => _('End of next month'));
}

/**
 *
 * @access public
 * @return void 
 **/
function getOptionName($optionId){
	$array = getOptionNameArray();
	if (isset($array[$optionId])) {
	    return $array[$optionId];
	}	
	return false;
}

/**
 *
 * @access public
 * @return void 
 **/
function getOptionNameAsOptions($selected=false){
	$options = array();
	$array = getOptionNameArray();
	foreach($array as $val=>$label){
		$sel = $selected==$val?' selected':'';
		$options[] = sprintf('<option value="%s"%s>%s</option>', 
			$val, $sel, $label);
	}
	return $options;
}

define('SupplierCustomer::CHEQUE', 0);
define('SupplierCustomer::ESPECE', 1);
define('SupplierCustomer::CB', 2);
define('SupplierCustomer::TRAITE', 3);
define('SupplierCustomer::VIREMENT', 4);
define('SupplierCustomer::AVOIR', 5);
define('SupplierCustomer::BILLET_ORDRE', 6);


/**
 *
 * @access public
 * @return void 
 **/
function getModalityNameArray(){
	return array(SupplierCustomer::CB => _('Credit card'), 
				 SupplierCustomer::CHEQUE => _('Check'), 
				 SupplierCustomer::ESPECE => _('Cash'), 
				 SupplierCustomer::TRAITE => _('Draft'),
				 SupplierCustomer::VIREMENT => _('Transfer'), 
				 SupplierCustomer::AVOIR => _('Credit note'), 
				 SupplierCustomer::BILLET_ORDRE => _('promissory note'));
}

/**
 *
 * @access public
 * @return void 
 **/
function getModalityName($modalityId){
	$array = getModalityNameArray();
	if (isset($array[$modalityId])) {
	    return $array[$modalityId];
	}	
	return false;
}

/**
 *
 * @access public
 * @return void 
 **/
function getModalityNameAsOptions($selected=false){
	$options = array();
	$array = getModalityNameArray();
	foreach($array as $val=>$label){
		$sel = $selected==$val?' selected':'';
		$options[] = sprintf('<option value="%s"%s>%s</option>', $val, $sel, $label);
	}
	return $options;
}

?>