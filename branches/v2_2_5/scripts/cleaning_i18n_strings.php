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

require_once 'config.inc.php';

foreach($GLOBALS['DSNS'] as $dsn_name) {
    $dsn = constant($dsn_name);
    if (substr_count($dsn, '/') == 4) {
        // XXX compte qui n'a pas de base propre: les crons ne sont pas
        // executées
        continue;
    }
    Database::connection($dsn);

    $xml = simplexml_load_file('config/onlogistics.xml');
    $i18nStringUsedIds = array();
    foreach ($xml->entity as $entity) {
        $eAttrs = $entity->attributes();
        $eName  = (String)$eAttrs['name'];
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
                $getter = 'get' . $prop . 'Id';
                foreach($col as $item) {
                    $i18nStringUsedIds[] = $item->$getter();
                }
            }
        }
    }
    $sql = 'delete from I18nString where _Id not in ('.implode(',', $i18nStringUsedIds).')';
    Database::connection()->execute($sql);
}
?>
