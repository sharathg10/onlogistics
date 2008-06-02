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

$auth = Auth::singleton();

$tendaysBefore = DateTimeTools::timeStampToMySQLDate(time()-864000);
$flt = new FilterComponent(
    FilterComponent::OPERATOR_AND,
    new FilterRule(
        'Trainee',
        FilterRule::OPERATOR_EQUALS,
        1
    ),
    new FilterRule(
        'LastFlyDate',
        FilterRule::OPERATOR_LOWER_THAN,
        $tendaysBefore
    )
);

$mapper = Mapper::singleton('AeroCustomer');
$col = $mapper->loadCollection($flt, array(), 
    array('Name','LastFlyDate', 'Instructor'));

$count = $col->getCount();
for($i=0; $i<$count; $i++){
    $cus = $col->getItem($i);
    $uacCol = false;
    $instID = $cus->getInstructorId();
    if ($instID > 0) {
        $mapper = Mapper::singleton('UserAccount');
        $uacCol = $mapper->loadCollection(
            array('Actor'=>$instID), array(), array('Identity', 'Email'));
    }
    $lastdate = $cus->getLastFlyDate()==0?_('N/A'):$cus->getLastFlyDate();
    AlertSender::send_ALERT_TEN_DAYS_SINCE_LAST_FLY(
        $cus->getName(),
        $lastdate,
        $uacCol
    );
}
?>
