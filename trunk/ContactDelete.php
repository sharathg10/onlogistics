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
// fichiers requis par les sessions
require_once('ActorAddEditTools.php');
includeSessionRequirements();
require_once('Objects/Contact.php');

/**
 * Session et authentification
 **/
$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, 
          UserAccount::PROFILE_DIR_COMMERCIAL, UserAccount::PROFILE_GED_PROJECT_MANAGER));

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'home.php';
$origRetURL = isset($_REQUEST['origRetURL'])?$_REQUEST['origRetURL']:'home.php';

$session = Session::Singleton();

/**
 * Messages
 **/
$errorBody  = _('Contact %s cannot be deleted');

/**
 * Si le contact est passé en paramètre on le charge sinon on en construit un
 * nouveau, le tout via objectLoader.
 **/
$contactID = isset($_REQUEST['ctcId'])?$_REQUEST['ctcId']:false;
// Si un site est en session, on va chercher le contact dans la collection
// car il n'a peut-être pas encore été sauvé
$contactCollectionIndex = false;
if (isset($_SESSION['site'])) {
    $contactCollection = $_SESSION['site']->getContactCollection();
    $count = $contactCollection->getCount();
    for($i = 0; $i < $count; $i++){
    	$item = $contactCollection->getItem($i);
        if ($item->getId() == $contactID) {
            $contact = $item;
            $contactCollectionIndex = $i;
            break;
        }
    }
} else {
    $contact = Object::load('Contact', $contactID);
}
/**
 * On check si le contact est bien chargé, et on renvoie vers un dialogue
 * d'erreur au cas où.
 **/
if (Tools::isException($contact)) {
    Template::errorDialog($contact->getMessage(), sprintf("%s?retURL=%s", $retURL, $origRetURL));
	exit;
}
/**
 * Si un site est en session, $contactCollectionIndex est défini,
 *  on supprime le site de cette collection
 *
 **/
if ($contactCollectionIndex !== false) {
    $contactCollection->removeItem($contactCollectionIndex);
}

/**
 * On demarre une transaction
 **/
Database::connection()->startTrans();

/**
 * Si un site est en session on lui enlève ce contact
 **/
if (isset($_SESSION['site'])) {
    $site = $_SESSION['site'];
	$site->removeContact($contact->getId());
}
//$mapper = Mapper::singleton('Contact');
//$mapper->delete($contact->getId());

/**
 * On commite la transaction,
 * si la transaction a échouée on redirige vers un message d'erreur
 **/

if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
	Database::connection()->rollbackTrans();
	Template::errorDialog($errorBody, sprintf("%s?retURL=%s", $retURL, $origRetURL));
	Exit;
}
Database::connection()->completeTrans();
Tools::redirectTo(sprintf("%s?retURL=%s", $retURL, $origRetURL));
exit;
?>