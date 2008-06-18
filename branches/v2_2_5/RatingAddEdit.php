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
// pour les sessions
require_once('Objects/Licence.php');
require_once('Objects/LicenceType.php');
require_once('Objects/Rating.php');
require_once('Objects/RatingType.php');
require_once('Objects/FlyType.php');
require_once('LangTools.php');

/**
 * Messages d'erreur
 **/
define('E_TITLE', 'Erreur.');
define('E_NO_RATING', _('No qualification selected.'));
$pageTitle = _('Add or update skill');

/**
 * Authentification
 * Permissions: Admin aero, admin ventes aero
 **/
$session = Session::singleton();
$auth = Auth::singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_AERO_CUSTOMER, UserAccount::PROFILE_DIR_COMMERCIAL)
);

/**
 * Pour prolonger la session du formulaire de recherche d'actorlist
 **/
SearchTools::ProlongDataInSession();

/**
 * on checke que l'acteur soit bien passé en paramètres
 **/
if (!isset($_REQUEST['actorID'])) {
    Template::errorDialog(E_ERROR_IN_EXEC, 'ActorList.php');
    exit;
}
/**
 * on checke que la licence soit bien passé en paramètre
 **/
if (!isset($_SESSION['lic'])) {
    Template::errorDialog(E_NO_LICENCE, 'ActorList.php');
    exit;
}

$lic = $_SESSION['lic'];
// l'url de retour
$retURL = sprintf('LicenceAddEdit.php?actorID=%s', $_REQUEST['actorID']);

/**
 * On charge la licence ou on prend celle qui est en session, ou on en crée
 * une nouvelle
 **/
$mapper = Mapper::singleton('Rating');
if (isset($_REQUEST['ratID']) && false != $_REQUEST['ratID']) {
    $rat = $mapper->load(array('Id'=>$_REQUEST['ratID']));
} else if (isset($_SESSION['rat'])) {
    $rat = $_SESSION['rat'];
} else {
    $rat = new Rating();
}

$ratID = $rat instanceof Rating?$rat->getId():$_REQUEST['ratID'];
// verifions que le rating n'est pas dans la collection
// s'il y est il faut prendre celui là
$ratCollection = $lic->getRatingCollection();
$count = $ratCollection->getCount();
for($i=0; $i<$count; $i++){
    $item = $ratCollection->getItem($i);
    if ($item->getId() == $ratID) {
        $rat = $item;
        break;
    }
}


$session->register('rat', $rat, 2);

/**
 * On vérifie que c'est bien une licence ;)
 **/
if (!($rat instanceof Rating)) {
    Template::errorDialog(E_NO_RATING, $retURL);
    exit;
}

/**
 * nous servira à déterminer s'il s'agit d'un ajout de licence, auquel cas,
 * il faudra l'ajouter aux licences de l'acteur
 **/
$isNewRating = $rat->getId() == 0;

/**
 * Traitement de l'envoi du formulaire
 */
if (isset($_POST['formSubmitted'])) {

	// gérer les duration/date sur les données POST
    // on calcule la date de fin en faisant:
    // date de début + (durée * typedurée (mois ou jours))
    if ($_POST['Rating_BeginDate'] != 0) {
        $d = DateTimeTools::MySQLDateToTimeStamp(
            $_POST['Rating_BeginDate']) +
            $_POST['Rating_Duration'] * 86400 *
            ($_POST['Rating_DurationType']>0?31:1);
        $_POST['Rating_EndDate'] = DateTimeTools::timeStampToMySQLDate($d);
    } else {
        $_POST['Rating_EndDate'] = 0;
    }

    // On remplit l'objet
    FormTools::autoHandlePostData($_POST, $rat);
    // si on est dans le cas d'une nouvelle licence, il faut l'ajouter à la
    // collection de licences de l'acteur
    if ($isNewRating) {
        $rat->setId($mapper->generateId());
        $ratCollection = $lic->getRatingCollection();
        $ratCollection->setItem($rat);
    }
    unset($_SESSION['rat']);
    // Redirection vers la liste des licences
    Tools::redirectTo($retURL);
    exit;
}

/**
 * Assignation des variables au formulaire avec smarty
 */
$smarty = new Template();
$smarty->assign('FormAction', $_SERVER['PHP_SELF']);
$smarty->assign('retURL', $retURL);
$smarty->assign('actorID', $_REQUEST['actorID']);
$smarty->assign('Duration', range(0, 500));
$smarty->assign('DurationType', array(0=>'jour(s)', 1=>'mois'));

/**
 * On assigne les propriétés de l'objet à la page
 */
$properties = $rat->getProperties();
foreach($properties as $name=>$type){
    if (is_string($type)) {
        $getter = 'get' . $name . 'Id';
        $opts = FormTools::writeOptionsFromObject($type, $rat->$getter());
        $smarty->assign('Rating_' . $name . '_Options', implode("\n", $opts));
    } else {
        $getter = 'get' . $name;
    	$smarty->assign('Rating_' . $name, $rat->$getter());
    }
}
$smarty->assign('Rating_BeginDate_Display',
    I18N::formatDate($rat->getBeginDate()));
/**
 * On affiche la page
 **/
$pageContent = $smarty->fetch('Rating/RatingAddEdit.html');

$js = array(
    'js/lib-functions/checkForm.js',
    'js/includes/RatingAddEdit.js',
	'js/jscalendar/calendar.js',
	getJSCalendarLangFile(),
	'js/jscalendar/calendar-setup.js'
);
Template::page($pageTitle, $pageContent, $js);
?>
