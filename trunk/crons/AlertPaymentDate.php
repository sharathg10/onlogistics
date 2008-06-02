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

require_once('SQLRequest.php');
require_once('AlertSender.php');

$mapper = Mapper::singleton('Actor');

// on ne charge que les Customer et AeroCustomer
$filter = new FilterComponent(
    new FilterRule(
        'ClassName', 
        FilterRule::OPERATOR_EQUALS, 
        'Customer'
    ),
    new FilterRule(
        'ClassName', 
        FilterRule::OPERATOR_EQUALS, 
        'AeroCustomer'
    )
);
$filter->operator = FilterComponent::OPERATOR_OR;

$col = $mapper->loadCollection($filter, array(), array('Name'));
$cnt = $col->getCount();

for($i = 0; $i < $cnt; $i++) {
	$act = $col->getItem($i);
	$sql = Request_AlertPaymentDate($act->getId());
	$rs  = ExecuteSQL($sql);
    
    if ($rs && !$rs->EOF) {
        $body = "Client " . $act->getName() . ":\n";
        while (!$rs->EOF) {
            $body .= sprintf(
                "\t- "._('Order %s, invoice %s: deadline for payment exceeded (%s)')."\n", 
                $rs->fields['abcCommandNo'], 
                $rs->fields['adcDocumentNo'], 
                I18N::formatDate($rs->fields['adcPaymentDate'], I18N::DATE_SHORT)
            );
            $rs->moveNext();
        }
        $rs->close();
        
        // On envoie l' alerte
        AlertSender::send_ALERT_CLIENT_LATE_PAYMENT($act->getName(), $body); 
    }
}

?>
