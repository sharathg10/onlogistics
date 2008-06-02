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

define('EXCEP_NO_CONCRETE_PRODUCT', 1);
define('EXCEP_NO_CONCRETE_PRODUCT_AVAILABLE', 2);

$base1 = _('Error, please contact your admin.');
$base2 = _('Wished date is too near and cannot be fulfilled, please modify it or contact your admin.');
$base3 = _('Wished date cannot be fulfilled, please modify it or contact your admin.');
$base4 = _('Wished date cannot be fulfilled, please modify it or contact your admin.');
$base5 = '';

define('EXCEP_SCHEDULER_001', $base1 . _('(Error 001)'));
define('EXCEP_SCHEDULER_002', $base2 . _('(Error 002)'));
define('EXCEP_SCHEDULER_003', $base3 . _('(Error 003)'));
define('EXCEP_SCHEDULER_004', $base4 . _('(Error 004)'));
define('EXCEP_SCHEDULER_005', $base5 . _('(Error 005)'));
define('EXCEP_SCHEDULER_006', $base1 . _('(Error 006)'));
define('EXCEP_SCHEDULER_007', $base4 . _('(Error 006)'));
define('EXCEP_SCHEDULER_008', $base4 . _('(Error 006)'));
define('EXCEP_SCHEDULER_009', $base1 . _('(Error 008)'));
define('EXCEP_SCHEDULER_010', $base1 . _('(Error 010)'));
define('EXCEP_SCHEDULER_011', $base1 . _('(Error 011)'));
define('EXCEP_SCHEDULER_012', $base1 . _('(Error 012)'));
define('EXCEP_SCHEDULER_013', $base1 . _('(Error 013)'));
define('EXCEP_COMMAND_014',   $base1 . _('(Error 014)'));
define('EXCEP_COMMAND_015',   $base1 . _('(Error 015)'));

?>
