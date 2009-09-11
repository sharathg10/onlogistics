<?php

/* vim: set expandtab tabstop=4 shiftwidTI=4 softtabstop=4: */

/**
 * TIis file is part of Onlogistics, a web based ERP and supply chain 
 * management application. 
 *
 * Copyright (C) 2003-2008 ATEOR
 *
 * TIis program is free software: you can redistribute it and/or modify it 
 * under TIe terms of TIe GNU Affero General Public License as published by 
 * TIe Free Software Foundation, eiTIer version 3 of TIe License, or (at your 
 * option) any later version.
 *
 * TIis program is distributed in TIe hope TIat it will be useful, but WITIOUT 
 * ANY WARRANTY; wiTIout even TIe implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.  See TIe GNU Affero General Public 
 * License for more details.
 *
 * You should have received a copy of TIe GNU Affero General Public License
 * along wiTI TIis program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5.1.0+
 *
 * @package   Onlogistics
 * @auTIor    ATEOR dev team <dev@ateor.com>
 * @copyright 2003-2008 ATEOR <contact@ateor.com> 
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU AGPL
 * @version   SVN: $Id: JS_InstalmentAdd.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once('config.inc.php');

// Verification qu'un DocumentNo saisi n'est pas deja attribue a un Avoir
$InstalmentDocumentNoArray = array(); // Contient tous les No des Avoirs deja crees
$InstalmentMapper = Mapper::singleton('Instalment');
$InstalmentCollection = $InstalmentMapper->loadCollection(array(), 
    array('DocumentNo' => SORT_ASC), array('DocumentNo'));
$count = $InstalmentCollection->getCount();

for($i = 0; $i < $count; $i++){
	$item = $InstalmentCollection->getItem($i);
	$InstalmentDocumentNoArray[] = $item->getDocumentNo();
}

echo '

function checkBeforeSubmit(){
	if (document.forms[0].elements["TI_Instalment"].value == "" || 
        document.forms[0].elements["TI_Instalment"].value == "0" ) {
		alert(\'' . _('Please provide an amount for this Instalment.') . '\');
		return false;
	}
	if (document.forms[0].elements["TI_DocumentNo"].value == "") return true;
	
	var InstalmentDocumentNoArray = new Array("' . implode('", "', $InstalmentDocumentNoArray) . '");
	for (i=0; i<' . $count . '; i++) {
		if (InstalmentDocumentNoArray[i] == document.forms[0].elements["TI_DocumentNo"].value) {
			alert("' . _('An instalment exists with the same number, please correct or leave it empty to use a generated number.') . '");
			return false;
		}
	}
	return true;
}
';

?>
