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

function concreteProductExists($sn, $Product) {
    $msgStart = _('Improper SN "%s" provided: ');
    $msgEnd = _('Please correct.');
	$cpMapper = Mapper::singleton('ConcreteProduct');
	$testCP = $cpMapper->load(
            array('SerialNumber' => $sn, 'Product' => $Product->getId()));
	if (Tools::isEmptyObject($testCP)) {
	    return new Exception($msgStart . $msgEnd);
	}
    if (in_array($testCP->getState(), array(ConcreteProduct::EN_REPARATION, ConcreteProduct::AU_REBUS, ConcreteProduct::EN_LOCATION))) {
        return new Exception(
                $msgStart . _('provided SN state is ') . $testCP->getState() 
                . '. ' . $msgEnd);
    }
	$ccpMapper = Mapper::singleton('ConcreteComponent');
	$testCCP = $ccpMapper->load(array('ConcreteProduct' => $testCP->getId()));
	
    if (!Tools::isEmptyObject($testCP)) {
	    return new Exception($msgStart . 
                _('SN provided is already used in an assembly. ') . $msgEnd);
	}
    return true;
}

/**
 * Met les saisies en session en cas de retour apres erreur
 * @param integer $assemblyNb Nombre d'asemblages prevus
 * @return void 
 **/
function putInSession($assemblyNb){
	$session = Session::Singleton();
	unset($_SESSION['Quantity'], $_SESSION['realQuantity'], $_SESSION['SerialNumber']);
	$session->register('Quantity', $_REQUEST['Quantity'], 2);
	$session->register('realQuantity', $_REQUEST['realQuantity'], 2);
	$session->register('SerialNumber', $_REQUEST['SerialNumber'], 2);
	
	if (!isset($_REQUEST['SerialNumber_0'])) {
	    return 0;
	}
	for($i = 0; $i < $assemblyNb; $i++) {
		$inputName = 'SerialNumber_'. $i;
		unset($_SESSION[$inputName]);
		$session->register($inputName, $_REQUEST[$inputName], 2);
	}
}


?>
