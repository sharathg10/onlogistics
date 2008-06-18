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
$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

SearchTools::prolongDataInSession();

// variables
$errorBody = E_ERROR_IMPOSSIBLE_ACTION;
$body = _('Alerts were successfully modified for user "%s".');
$retURL = 'dispatcher.php?entity=UserAccount';

/**
 * Si on n'a pas les bons paramètres, on renvoie une erreur
 **/
if (!isset($_REQUEST['uacId'])) {
	Template::errorDialog($errorBody, $retURL);
	exit;
}
// Si pas d'alerte passee ds l'url, c'est une desaffectation totale
$alertIds = isset($_REQUEST['alertIds'])?$_REQUEST['alertIds']:array();

// On charge le UserAccount
$uacMapper = Mapper::singleton('UserAccount');
$uac = $uacMapper->load(array('Id' => $_REQUEST['uacId']));

// Demarre la transaction
Database::connection()->startTrans();

$uac->setAlertCollectionIds($alertIds);
saveInstance($uac, $retURL);

// Pour l'affichage suite a l'operation effectuee
$alertsAffected = array();
foreach($alertIds as $alertId) {
	$alert = Object::load('Alert', $alertId);
	$alertsAffected[] = $alert->getName();
}

// Commit
if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
	Database::connection()->rollbackTrans();
	Template::errorDialog($errorBody, $retURL);
	exit;
}
Database::connection()->completeTrans();

/**
 * Tout est OK.
 * On informe l'utilisateur
 **/
if (count($alertsAffected) == 0) {
    $body = _('User "%s" was configured to receive no alerts.');
	$body = sprintf($body, $uac->getIdentity());

} else if (count($alertsAffected) == 1) {
	$body = _('User "%s" was configured to receive alert "%s"');
    $body = sprintf($body, $uac->getIdentity(), $alertsAffected[0]);
} else {
	$body = _('User "%s" was configured to receive the following alerts: %s');
    $body = sprintf($body, $uac->getIdentity(), join(', ', $alertsAffected));
}

Template::infoDialog(sprintf($body, $uac->getIdentity()), $retURL);
exit;

?>
