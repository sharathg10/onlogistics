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
require_once('ActorAddEditTools.php');
includeSessionRequirements(); // fichiers requis par les sessions

$auth = Auth::Singleton();
$ProfileId = $auth->getProfile();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_AERO_CUSTOMER,
          UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_DIR_COMMERCIAL, UserAccount::PROFILE_GED_PROJECT_MANAGER));

$retURL = isset($_REQUEST['retURL'])&&!empty($_REQUEST['retURL'])?
    $_REQUEST['retURL']:'ActorAddEdit.php';

$session = Session::Singleton();
SearchTools::ProlongDataInSession(1);

$pageTitle = _('Add or update bank');

/**
 * Construction de l'acteur.
 * si celui-ci est passé en paramètre ou est en session.
 */
$actorID = isset($_REQUEST['actId'])?$_REQUEST['actId']:false;
if (false != $actorID) {
    // cas d'un id passé
    $actor = Object::load('Actor', $actorID);
} else if (isset($_SESSION['actor']) && $_SESSION['actor'] instanceof Actor) {
    // acteur en session
    $actor = $_SESSION['actor'];
} else {
    // on devrait pas être ici...
    $actor = false;
}

// Check si l'acteur est bien chargé
if (!$actor || Tools::isException($actor)) {
    $msg = $actor?$actor->getMessage():_('Actor was not found in the database.');
    Template::errorDialog($msg, $retURL);
    exit;
}
$session->register('actor', $actor, 3);

// Si l'ActorBankDetail est passé en param on le charge sinon on en construit un new
$abdId = isset($_REQUEST['abdId'])?$_REQUEST['abdId']:false;
$abd = false;
// Si un acteur est en session, on va chercher le ActorBankDetail dans la collection
// car il n'a peut-être pas encore été sauvé
if ($abdId) {
    $abdCollection = $_SESSION['actor']->getActorBankDetailCollection();
    $abd = $abdCollection->getItemById($abdId);
    if (false == $abd) {  // ActorBankDetail pas trouvé
        $abd = new ActorBankDetail();
    }
} else if (isset($_SESSION['ActorBankDetail'])) {
    // Cas d'un ActorBankDetail en session
    $abd = $_SESSION['ActorBankDetail'];
} else {
    $abd = new ActorBankDetail();
}

/**
 * On check si le ActorBankDetail est bien charge, et on renvoit vers un dialogue
 * d'erreur au cas ou.
 */
if (false == $abd || Tools::isException($abd)) {
    $msg = Tools::isException($abd)?$abd->getMessage():_('Bank was not found in the database.');
    Template::errorDialog($msg, $retURL);
    exit;
}

$abd->setActor($_SESSION['actor']);
$session->register('ActorBankDetail', $abd, 3);

/**
 * Traitement de l'envoi du formulaire
 */
if (isset($_POST['formSubmitted'])) {
    // On remplit l'objet ActorBankDetail
    FormTools::autoHandlePostData($_POST, $_SESSION['ActorBankDetail'], 'ActorBankDetail');
	if (Tools::isException($_SESSION['ActorBankDetail'])) {
        Template::errorDialog($_SESSION['ActorBankDetail']->getMessage(), $retURL);
        exit;
    }

    $abdCollection = $_SESSION['actor']->getActorBankDetailCollection();
    if ($abd->getId() == false) {
        $mapper = Mapper::singleton('ActorBankDetail');
        $_SESSION['ActorBankDetail']->setId($mapper->generateId());
        $abdCollection->setItem($_SESSION['ActorBankDetail']);
    } else {
        replaceItemInCollection($abdCollection, $_SESSION['ActorBankDetail']);
    }

    unset($_SESSION['ActorBankDetail']);
    Tools::redirectTo($retURL);
    exit;
}


/**
 * Assignation des variables au formulaire avec smarty
 */
$smarty = new Template();
$smarty->assign('retURL', $retURL);
// assign plutot que register_object... (erreur?)
$smarty->assign('ActorBankDetail', $_SESSION['ActorBankDetail']);  // register_object
$smarty->assign('IsActive', ($abd->getActive() == 1)?'checked':' ');
$smarty->assign('IsNotActive', ($abd->getActive() == 1)?' ':'checked');
// On n'affiche tous les champs que si l'Actor est DataBaseOwner
$smarty->assign('isDBO', ($_SESSION['actor']->getDatabaseOwner() == 1)?'1':'0');

$streetTypeSelected = $abd->getBankAddressStreetType();
$StreetTypes = ActorBankDetail::getBankAddressStreetTypeConstArray();
$StreetTypeList = FormTools::writeOptionsFromArray($StreetTypes, $streetTypeSelected);
$smarty->assign('StreetTypeList', join("\n\t\t", $StreetTypeList));

$currencySelected = $_SESSION['ActorBankDetail']->getCurrencyId();
$currencyOptions = FormTools::writeOptionsFromObject('Currency', $currencySelected,
        array(), array('Name' => SORT_ASC));
$smarty->assign('CurrencyList', join("\n\t\t", $currencyOptions));


$pageContent = $smarty->fetch('Actor/ActorBankDetailAddEdit.html');

Template::page($pageTitle, $pageContent, array('js/lib-functions/checkForm.js'));

?>
