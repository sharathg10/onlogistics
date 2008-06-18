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

require_once('Objects/Contact.const.php');

$auth = Auth::Singleton();
$ProfileId = $auth -> getProfile();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_AERO_CUSTOMER,
          UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_DIR_COMMERCIAL, UserAccount::PROFILE_GED_PROJECT_MANAGER));

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'home.php';
$origRetURL = isset($_REQUEST['origRetURL'])?$_REQUEST['origRetURL']:'home.php';

$session = Session::Singleton();
SearchTools::ProlongDataInSession(1);
/**
 * Messages
 **/
$errorBody  = _('Contact cannot be saved');
$pageTitle  = _('Create/update contact');

$contactID = isset($_REQUEST['ctcId'])?$_REQUEST['ctcId']:false;
/**
 * Si le contact est passé en paramètre on le charge en prenant celui-ci dans
 * le collection de contacts du site (car il n'a peut-êtyre pas encore été
 * sauvé. Sinon on en construit un nouveau.
 **/
if (isset($_SESSION['site']) && $contactID) {
    $contactCollection = $_SESSION['site']->getContactCollection();
    $contact = getItemInCollection($contactCollection, $contactID);
}
if (!isset($contact) || false == $contact) {
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
 * Traitement de l'envoi du formulaire
 **/
if (isset($_POST['formSubmitted'])) {

	/**
	 * On rempli l'objet contact avec les données du formulaire
	 **/
	FormTools::autoHandlePostData($_POST, $contact);

	/**
	 * IMPORTANT: ici on teste si un site est en session et si c'est le
	 * cas on lui affecte le contact précédemment créé (si ajout: donc si pas
	 * de $contactID)
	 **/
	if (false == $contactID && isset($_SESSION['site'])) {
        $mapper = Mapper::singleton('Contact');
        $contact->setId($mapper->generateId());
		$_SESSION['site']->addContact($contact);
	}
	Tools::redirectTo(sprintf("%s?retURL=%s", $retURL, $origRetURL));
	exit;
}

/**
 * Assignation des variables au formulaire avec smarty
 **/
$smarty = new Template();
$smarty->assign('FormAction', $_SERVER['PHP_SELF']);
$smarty->assign('retURL', $retURL);
$smarty->assign('origRetURL', $origRetURL);
$smarty->assign('Contact', $contact);


$roleID      = $contact->getRoleId();
$roleOptions = FormTools::writeOptionsFromObject('ContactRole', $roleID, 
    array(), array(), 'toString', array(), true);
$smarty->assign('ContactRoleList', implode("\n\t\t", $roleOptions));

$cmdty = $contact->getCommunicationModality();
$commModOptions = getCommunicationModalityModesAsOptions($cmdty);
$smarty->assign('CommModeList', join("\n\t\t", $commModOptions));

/**
 * Et on affiche la page
 **/
$pageContent = $smarty->fetch('Contact/ContactAddEdit.html');
$baseTpl = isset($_SESSION['asPopup'])?BASE_POPUP_TEMPLATE:BASE_TEMPLATE;
$js = array('js/lib-functions/checkForm.js', 'js/includes/ContactAddEdit.js');
Template::page($pageTitle, $pageContent, $js, array(), $baseTpl);

?>
