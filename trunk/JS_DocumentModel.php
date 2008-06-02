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

$DoctModelNameArray = array();  // contient les noms de DocomentModel
$defaultModelArray = array();
$DMMapper = Mapper::singleton('DocumentModel');
$DMCollection = $DMMapper->loadCollection(array(), array(), array('Name', 'DocType', 'Default'));
$count = $DMCollection->getCount();

for($i = 0; $i < $count; $i++){
	$item = $DMCollection->getItem($i);
	if ($_REQUEST['domId'] != $item->getId()) {
	    $DoctModelNameArray[] = $item->getName();
	}
	if (1 == $item->getDefault() && $item->getId() != $_REQUEST['domId']) {
	    $defaultModelArray[] = $item->getDocType();
	}
}

$nbDocModelName = count($DoctModelNameArray);
$nbDefaultDocModel = count($defaultModelArray);

echo '
function checkBeforeSubmit(docType){
	if (document.forms[0].elements["DocumentModel_Name"].value == "") {
		alert(\'' . _('Please provide a name.') . '\');
		return false;
	}
	var DoctModelNameArray = new Array("' . implode('", "', $DoctModelNameArray) . '");
	for (i=0; i<' . $nbDocModelName . '; i++) {
		if (DoctModelNameArray[i] == document.forms[0].elements["DocumentModel_Name"].value) {
			alert(\'' . _('A model with the name provided already exists, please correct.') . '\');
			return false;
		}
	}

	if (document.forms[0].elements["DocumentModel_Default"][1].checked) {
		var DefaultDocModelArray = new Array("' . implode('", "', $defaultModelArray) . '");
		for (i=0; i<' . $nbDefaultDocModel . '; i++) {
			if (DefaultDocModelArray[i] == docType) {
				alert(\'' . _('A default model is already set for selected document, please correct.') . '\');
				return false;
			}
		}
	}
	return true;
}
';

?>