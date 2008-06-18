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

function parseConfigFile($fname) {
    $fname = dirname(__FILE__) . '/' . $fname;
    if (!file_exists($fname) || !is_readable($fname)) {
        throw new Exception("$fname must exists and must be readable.");
    }
    if (!($fp = fopen($fname, 'r'))) {
        throw new Exception("$fname cannot be opened.");
    }
    $i = 1;
    while($line = fgets($fp)){
        $line = trim($line);
        if(!empty($line) && !preg_match('/^[;\#]/', $line)){
            @list($const, $val) = explode('=', $line);
            if(isset($const) && isset($val)){
                $val = explode('#', $val);
                $val = trim($val[0]);
                if (strtolower($val) == 'true') {
                    $val = true;
                } else if(strtolower($val) == 'false') {
                    $val = false;
                }
                if(!defined($const)){
                    define("$const", $val);
                }
                if (strpos($const,'DSN_') === 0) {
                    $GLOBALS['DSNS'][] = $const;
                }
            } else {
                throw new Exception("Syntax error in $fname on line $i.");
            }
        }
        $i++;
    }
    return true;
}

/** 
 * Inclus le fichier de configuration du framework.
 *
 * @return boolean true if bootstraping is ok or throw an exception
 * @throws Exception
 */
function bootstrap() {
    // define the project root directory
    define('PROJECT_ROOT', dirname(__FILE__));
    if (!defined('FRAMEWORK_ROOT')) {
        throw new Exception('You must define the path to the framework '
            . '(FRAMEWORK_ROOT constant) in your config file.');
    }
    if (!file_exists(FRAMEWORK_ROOT . '/framework.inc.php')) {
        throw new Exception('framework config file not found, please verify '
            . 'that FRAMEWORK_ROOT constant points to a valid path.');
    }
    include_once(FRAMEWORK_ROOT . '/framework.inc.php');
}

?>
