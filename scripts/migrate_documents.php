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
define('SMARTY_COMPILE_DIR', '/tmp/');

require_once 'config.inc.php';

// check that the ARCHIVED_DOCUMENTS_DIR is defined first
if (!defined('ARCHIVED_DOCUMENTS_DIR')) {
    trigger_error(
        "You must set the ARCHIVED_DOCUMENTS_DIR constant in your project.conf file.",
        E_USER_ERROR
    );
}

$sql     = 'SELECT _Id, _Type, _Data from Document;';
$typemap = array('txt', 'pdf', 'csv');
$basedir = ARCHIVED_DOCUMENTS_DIR . DIRECTORY_SEPARATOR . ENVIRONMENT;

foreach($GLOBALS['DSNS'] as $dsn_name) {
    $realm = strtolower(substr($dsn_name, 4));
    $dsn = constant($dsn_name);
    if (substr_count($dsn, '/') == 4) {
        continue;
    }
    Database::connection($dsn)->connect();
    Database::connection()->startTrans();
    $rs = Database::connection()->execute($sql);
    if (false === $rs) {
        continue;
    }
    $basedir_with_realm = $basedir . DIRECTORY_SEPARATOR . $realm;

    if (!is_dir($basedir_with_realm)) {
        if (!@mkdir($basedir_with_realm, 0777, true)) {
            trigger_error(
                "Cannot create ${basedir_with_realm}, make sure ARCHIVED_DOCUMENTS_DIR is writable.",
                E_USER_ERROR
            );
        }
    }

    while (!$rs->EOF) {
        $file = $basedir_with_realm . DIRECTORY_SEPARATOR . $rs->fields['_Id'];
        if (file_exists($file)) {
            continue;
        }
        if (isset($typemap[$rs->fields['_Type']])) {
            $file .= '.' . $typemap[$rs->fields['_Type']];
        }
        $result = file_put_contents($file, $rs->fields['_Data']);
        if (!$result) {
            trigger_error('Cannot write ' . $file, E_USER_WARNING);
        } else {
            // that's the real thing, we remove this space consuming data from 
            // the db since we've written the file already
            Database::connection()->execute(
                'UPDATE Document SET _Data=NULL where _Id=' . $rs->fields['_Id']
            );
        }
        $rs->MoveNext();
    }

    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    	Database::connection()->rollbackTrans();
    	exit;
    }
    Database::connection()->completeTrans();
}

?>
