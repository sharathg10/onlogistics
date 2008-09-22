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

define('SKIP_CONNECTION', true);
define('MAPPER_CACHE_DISABLED', true);

$only_class = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : false;
$only_dsn   = isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : false;

require_once 'config.inc.php';
foreach($GLOBALS['DSNS'] as $dsn_name) {
    if ($only_dsn && $dsn_name != $only_dsn) {
        continue;
    }
    $dsn = constant($dsn_name);
    if (substr_count($dsn, '/') == 4) {
        // XXX compte qui n'a pas de base propre: les crons ne sont pas
        // executées
        continue;
    }
    Database::connection($dsn);
    I18N::setLocale('en_GB');

    $xml = simplexml_load_file('config/onlogistics.xml');
    foreach ($xml->entity as $entity) {
        $eAttrs = $entity->attributes();
        $eName  = (String)$eAttrs['name'];
        if ($only_class && $only_class != $eName) {
            continue;
        }
        $eTable = (String)$eAttrs['tablename'];
        $props  = array();
        foreach ($entity->property as $property) {
            $pAttrs = $property->attributes();
            $pI18n  = isset($pAttrs['i18n']) ? intval($pAttrs['i18n']) : 0;
            $pName  = (String)$pAttrs['name'];
            if ($pI18n) {
                $props[] = $pName;
            }
        }
        if (!empty($props)) {
            $col = Object::loadCollection($eName);
            $count = $col->getCount();
            foreach ($props as $prop) {
                printf("-> Processing %s::%s\n", $eName, $prop);
                for ($i=0; $i<$count; $i++) {
                    $item = $col->getItem($i);
                    try {
                        $item->save();
                    } catch (Exception $exc) {
                        printf(
                            "[EEE] %s [%s::Id: %s on %s]'\n",
                            $exc->getMessage(),
                            $eName, $item->getId(),
                            $dsn_name
                        );
                    }
                    $getter = 'get'.$prop;
                    $setter = 'set'.$prop;

                    $value = $item->$getter();
                    foreach(array_keys(I18n::getSupportedLocales()) as $locCode) {
                        I18N::setLocale($locCode);
                        $i18n_value = ($value == '')?'':_($value);
                        //printf("   + %s = %s\n", $locCode, $value);
                        $item->$setter($i18n_value);
                    }
                    I18N::setLocale('en_GB');
                }
                try {
                    if (isset($item)) {
                        $item->save();
                    }
                } catch (Exception $exc) {
                    printf(
                        "[EEE] %s [%s::Id: %s on %s]'\n",
                        $exc->getMessage(),
                        $eName, $item->getId(),
                        $dsn_name
                    );
                }
                $alterSQL = sprintf(
                    'ALTER TABLE `%s` CHANGE COLUMN `_%s` `_%s` INT(11) NOT NULL DEFAULT 0;',
                    $eTable, $prop, $prop
                );
                printf("   + sql query: %s\n", _($alterSQL));
                Database::connection()->execute($alterSQL);
            }
        }
    }
}

?>
