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
 * @version   SVN: $Id: migrate_help_data.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

define('SKIP_CONNECTION', true);
define('MAPPER_CACHE_DISABLED', true);

error_reporting(E_ALL);

require_once 'config.inc.php';

if (php_sapi_name() != 'cli') {
    fwrite('stderr', "This script must be called from commandline.\n");
    exit(1);
}
if (!defined('DSN_MALOLES')) {
    fwrite('stderr', "DSN_MALOLES is not defined.\n");
    exit(1);
}

define('SEASON_ID', 546);

Database::connection(DSN_MALOLES);
Database::connection()->startTrans();

$products = Object::loadCollection('RTWProduct', array('Model.Season.Id' => SEASON_ID));
foreach ($products as $product) {
    $product->generateEAN13Code();
    echo $product->getEAN13Code() . "\n";
    $product->save();
}
Database::connection()->completeTrans();

?>
