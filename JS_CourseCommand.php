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


if (!isset($_GET['canChangeInstructor'])) {
    exit;
}

/**
 * Authentification
 */
$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_CUSTOMER, UserAccount::PROFILE_AERO_ADMIN_VENTES),
    array('showErrorDialog' => true));

$mapper = Mapper::singleton('AeroInstructor');
$instructors = $mapper->loadCollection(array('Active'=>1),
    array('Name'=>SORT_ASC));

$count = $instructors->getCount();
$hashTable = array();
for($i = 0; $i < $count; $i++){
	$inst = $instructors->getItem($i);

    $ftypes = $inst->getFlyTypeCollection();
    $ids = $ftypes->getItemIds();
    foreach($ids as $id){
        if (!isset($hashTable[$id])) {
            $hashTable[$id] = array();
        }
        $hashTable[$id][] = array($inst->getId(), $inst->getName());
    }
}
$sel = isset($_GET['selected'])?$_GET['selected']:0;
echo "hashTable = new Array();\n";
foreach($hashTable as $ftype=>$instArray){
	echo "hashTable[" . $ftype . "] = new Array();\n";
    $i = 0;
    foreach($instArray as $inst){
        $selected = $inst[0]==$sel?1:0;
        $option = "new Option('".$inst[1]."', '".$inst[0]."', ".$selected.")";
    	echo "hashTable[" . $ftype . "][" . $i . "] = " . $option . ";\n";
        $i++;
    }
}
echo "selected=" . $sel . ";";

?>

function updateInstructorSelect(flytype) {
	if (!flytype) {
		var ftypeSelect = document.forms[0].elements['FlyType'];
		flytype = ftypeSelect.value;
	}
	var instructorSelect = document.forms[0].elements['Instructor'];
	var instructorID = document.forms[0].elements['InstructorID'];
	instructorSelect.length = 1;
	if (flytype == 0) {
		return false;
	}
	if (!hashTable[flytype]) {
		return false;
	} else {
		for(var i=0; i < hashTable[flytype].length; i++) {
			instructorSelect.options[i+1] = hashTable[flytype][i];
		}
	}
	instructorID.value = instructorSelect.value;
}

connect(window, 'onload', function () {
	updateInstructorSelect();
});

requiredFields = new Array(
	new Array('WishedStartDate', REQUIRED_AND_NOT_ZERO, NONE, '<?php echo _('Wished time:');?>')
);
