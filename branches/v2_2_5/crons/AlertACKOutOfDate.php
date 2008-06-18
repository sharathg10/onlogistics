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

require_once('AlertSender.php');

$FilterComponentArray = array();
$FilterComponentArray[] = SearchTools::NewFilterComponent(
            'End', '', 'LowerThan', date('Y-m-d H:i:s', mktime()), 1);
$filter = SearchTools::filterAssembler($FilterComponentArray);
$ackMapper = Mapper::singleton('ActivatedChainTask');
$ackColl = $ackMapper->loadCollection(
    $filter, array(), array('Task', 'End', 'ActivatedOperation'));

$count = $ackColl->getCount();
// Il y aura un mail de construit par commande
$commandDataArray = array();


for($i = 0; $i < $count; $i++) {
	$ack = $ackColl->getItem($i);
    $commandId = Tools::getValueFromMacro(
        $ack,
        '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Id%'
    );
	if (!isset($commandDataArray[$commandId])) {
	    $commandDataArray[$commandId] = '';
	}
	$commandDataArray[$commandId] .= '
        <tr>
    		<td>' . $ack->getActivatedOperation()->getName() . '</td>
    		<td>' . $ack->getName() . '</td>
    		<td>' . $ack->getEnd() . '</td>
    	</tr>';
}


// On envoit les alertes
foreach ($commandDataArray as $cmdId => $data) {
    $result = AlertSender::send_ALERT_GED_ACK_OUT_OF_DATE($cmdId, $data);
}


?>