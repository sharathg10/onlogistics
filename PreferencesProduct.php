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
 * @version   SVN: $Id: PreferencesCommand.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once('config.inc.php');

$auth = Auth::singleton();
$auth->checkProfiles();

$confirmMessage = _('Preferences were successfully saved.');

// Le formulaire a ete poste
if (isset($_POST['Ok'])) {
    Database::connection()->startTrans();

    $ean13prefix   = isset($_POST['EAN13Prefix']) ? $_POST['EAN13Prefix'] : '';
    $ean13sequence = isset($_POST['EAN13Sequence']) ? $_POST['EAN13Sequence'] : 0;
    Preferences::set('EAN13Prefix', $ean13prefix);
    Preferences::set('EAN13Sequence', $ean13sequence);

    Preferences::save();

    // Gestion des erreurs
    if (Database::connection()->hasFailedTrans()) {
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_ERROR_SQL, $_SERVER['PHP_SELF']);
        exit;
    }
    // Commit de la transaction
    Database::connection()->completeTrans();

    Template::infoDialog($confirmMessage, $_SERVER['PHP_SELF']);
    exit(0);
}

$smarty = new Template();
$smarty->assign('EAN13Prefix', Preferences::get('EAN13Prefix'));
$smarty->assign('EAN13Sequence', Preferences::get('EAN13Sequence'));
Template::page('', $smarty->fetch('Preferences/PreferencesProduct.html'));

?>
