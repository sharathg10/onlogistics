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
    I18N::setLocale('en_GB');

    $mapper = Mapper::singleton('TVA');
    $coll = $mapper->loadCollection();
    for ($i=0; $i<$coll->getCount(); $i++) {
        $tva = $coll->getItem($i);
        $categ = $tva->getCategory('en_GB');
        switch ($categ) {
            case 'NORMAL':
                $tva->setType(TVA::TYPE_OTHER);
                break;
            case 'DELIVERY EXPENSES':
                $tva->setType(TVA::TYPE_DELIVERY_EXPENSES);
                break;
            case 'PACKING':
                $tva->setType(TVA::TYPE_PACKING);
                break;
            case 'INSURANCE':
                $tva->setType(TVA::TYPE_INSURANCE);
                break;
            // Mis en commentaire car fait planter le script, vu qu'il y a des tva loufoques
            // et TVA.Type est UNIQUE
            /*default:
                $tva->setType(TVA::TYPE_OTHER);
                break;*/
        }
        try {
            $tva->save();
        } catch (Exception $exc) {
            echo $exc->getMessage() . '; DSN: ' . $dsn_name . ' tva: ' . $tva->getCategory() . "\n";
        }
    }
}

?>