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

define('COMM_EMAIL', 1);
define('COMM_PHONE', 2);
define('COMM_FAX', 3);
define('COMM_MOBILE', 4);

/**
 * Retourne les modes de comm disponibles dans un tableau
 * 
 * @access public
 * @return array 
 **/
function getCommunicationModalityModes(){
	return array(
				COMM_EMAIL => _('Email'),
				COMM_PHONE => _('Phone'),
				COMM_FAX => _('Fax'),
				COMM_MOBILE => _('Mobile phone'),
			);
}

/**
 * Retourne les modes de comm sous forme d'un tableau d'options de select
 * 
 * @access public
 * @return void 
 **/
function getCommunicationModalityModesAsOptions($sel=0){
	$options = array("<option value=\"0\">" . _('None') . "</option>");
	foreach(getCommunicationModalityModes() as $val=>$name){
		$selected = $sel==$val?" selected":"";
		$options[] = sprintf('<option value="%s"%s>%s</option>', 
			$val, $selected, $name);
	}
	return $options;
}

?>