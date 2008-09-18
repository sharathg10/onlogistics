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

/**
 * Return the list of available actor classes.
 *
 * @return array
 */ 
function getClassNameList()
{
    return array(
        'Actor'          => _('Actor'),
        'Customer'       => _('Customer'),
        'Supplier'       => _('Supplier'),
        'ProjectManager' => _('Project manager'),
        'AeroCustomer'   => _('Customer'),
        'AeroSupplier'   => _('Supplier'),
        'AeroInstructor' => _('Instructor'),
        'AeroOperator'   => _('Operator'),
    );
}

/**
 * retourne un tableau avec les types de société possibles.
 * 
 * @return array 
 */
function getCompanyTypes(){
	return array(_('SA'), _('SARL'), _('SNC'), _('SAS'), _('EURL'));
}

/**
 * retourne un tableau d'options avec les types de société possibles.
 * 
 * @access public
 * @return array 
 */
function getCompanyTypesAsOptions($sel=0){
	$ret = array("\n<option value=\"\">" . _('None') . "</option>");
	foreach(getCompanyTypes() as $type){
		$selected = $sel==$type?' selected':'';
		$ret[] = sprintf("\n<option value=\"%s\"%s>%s</option>", 
			$type, $selected, $type);
	}
	return $ret;
}

function getCarrierActorCollection() {
    $returnCol = new Collection();
    $actorCol = Object::loadCollection('Actor', array(
        'Active'=>1, 
        'Generic'=>0));
    $count = $actorCol->getCount();
    for($i=0 ; $i<$count ; $i++) {
        $actor = $actorCol->getItem($i);
        $jobCol = $actor->getJobCollection(array('Name'=>'CARRIER'));
        if($jobCol->getCount()>0) {
            $returnCol->setItem($actor);
        }
    }
    return $returnCol;
}

?>
