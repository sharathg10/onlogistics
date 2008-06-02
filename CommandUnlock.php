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

require_once('config.inc.php');
require_once('Objects/Command.const.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL));

SearchTools::prolongDataInSession();  // prolonge les datas du form de recherche en session

$CommandMapper = Mapper::singleton('Command');
$Command = Object::load('Command', $_REQUEST['CommandId']);
$retURL = 'CommandList.php';

if (Tools::isEmptyObject($Command) || $Command->getState() != Command::BLOCAGE_CDE) {
    Template::errorDialog(_('Selected order must be in locked state.'), $retURL);
   	exit;
}
// La commande ne peut etre cloturee
if ($Command->getClosed()) {
    Template::errorDialog(_('You cannot unlock a closed order'), $retURL);
    exit;
}
if (!isset($_GET['ok']) ) {
    Template::confirmDialog(
        _('Are you sure you want to unlock this order ?'),
        $_SERVER['PHP_SELF'].'?ok=1&CommandId='.$_REQUEST['CommandId'], $retURL);
	exit;
}

//  Debloque la commande et tous ses ActivatedMovements associes
$Command->blockDeblock(0);

// Redirect vers la liste des commandes
Tools::redirectTo($retURL);
exit;
?>