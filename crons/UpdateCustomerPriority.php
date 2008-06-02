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

if (php_sapi_name() != 'cli') {
    exit(1);
}

$actorMapper = Mapper::singleton('Actor');
// Collection des Actor a traiter
$customerCollection = $actorMapper->loadCollection(
		array('Active' => 1, 'ClassName' => array('AeroCustomer', 'Customer')));
$count = $customerCollection->getCount();
$nowWeek = date('W');

for($i = 0; $i < $count; $i++) {
	$Actor = $customerCollection->getItem($i);
	$CustomerProperties = $Actor->getCustomerProperties();
	if (Tools::isEmptyObject($CustomerProperties)) {
        continue;  // Rien a updater dans ce cas
    }
    // C'est la premiere Action de type MEETING trouvee la plus urgente:
    // celle qui nous interesse
    $action = $Actor->getNextMeetingAction();
    if (Tools::isEmptyObject($action)) {
        $priorityLevel = CustomerProperties::PRIORITY_EXTRALOW;
    }
    else {
        $nextMeetingWeek = $action->getWishedDate('W');
        $frequency = Tools::getValueFromMacro($Actor,
                '%CustomerProperties.PersonalFrequency.Frequency%');
        if ($nowWeek > $nextMeetingWeek) {  // date depassee
            $priorityLevel = CustomerProperties::PRIORITY_HIGH;
        }
        elseif ($nowWeek == $nextMeetingWeek) {  // semaine en cours
            $priorityLevel = CustomerProperties::PRIORITY_CURRENT;
        }
        elseif ($nextMeetingWeek == 0) {  // date a fixer
            $priorityLevel = CustomerProperties::PRIORITY_EXTRALOW;
        }
        else {
            if ($frequency == 0) { // Pas de frequence definie: regle de gestion
                $priorityLevel = CustomerProperties::PRIORITY_LOW;
            }
            elseif ($nowWeek < $nextMeetingWeek - $frequency) {
                $priorityLevel = CustomerProperties::PRIORITY_LOW;
            }
            // sinon: $nextMeetingWeek - $frequency <= $nowWeek < $nextMeetingWeek
            else {
                $priorityLevel = CustomerProperties::PRIORITY_AVERAGE;
            }
        }
    }
    $CustomerProperties->setPriorityLevel($priorityLevel);
    $CustomerProperties->save();
}

?>
