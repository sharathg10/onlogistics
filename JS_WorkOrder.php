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

$selectedJob = isset($_REQUEST['selectedJob'])?$_REQUEST['selectedJob']:0;
$selectedActor = isset($_REQUEST['selectedActor'])?$_REQUEST['selectedActor']:0;

header('Content-Type: text/plain');

echo 'connect(window, \'onload\', function() {
    fw.dom.selectOptionByValue(\'job\', ' . $selectedJob . ');';

// La seconde instruction attend la fn de la 1ere
if ($selectedActor > 0) {  // Mode edit
    echo '
    var dd = fw.ajax.updateSelectCustom(\'job\', \'actor\', \'Actor\', \'Job\', \'job_getActorCollection\');
    var myCallback = function () {
        fw.dom.selectOptionByValue(\'actor\', ' . $selectedActor . ');
    }
    dd.addCallback(myCallback);';
}
else {
    echo 'fw.ajax.updateSelectCustom(\'job\', \'actor\', \'Actor\', \'Job\', \'job_getActorCollection\');';
}

echo '
});

/*
 * Juste pour verifier qu\'un nom a bien ete saisi
 */
function validation() {
    if (document.forms[0].elements["name"].value == "") {
		alert(WorkOrder_0);
		return false;
	}
	var posVals = new Array(\'maxvol\', \'maxweigth\', \'maxLM\', \'maxdistance\');
	var count = posVals.length;
	for (i=0 ; i<4 ; i++) {
	    if(document.forms[0].elements[posVals[i]].value < 0) {
	        alert(WorkOrder_1);
	        return false;
	    }
	}
	return true;
}
';

?>
