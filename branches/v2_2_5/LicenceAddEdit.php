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
 * Authentification
 * Permissions: Admin aero, admin ventes aero
 **/
$session = Session::singleton();
$auth = Auth::singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_AERO_CUSTOMER, UserAccount::PROFILE_DIR_COMMERCIAL)
);

$pageTitle = _('Add or update licence');
$mapper = Mapper::singleton('Licence');

/**
 * Pour prolonger la session du formulaire de recherche d'actorlist
 **/
SearchTools::prolongDataInSession();

/**
 * on checke que l'acteur soit bien passé en paramètres
 **/
if (!isset($_REQUEST['actorID'])) {
    Template::errorDialog(E_MSG_TRY_AGAIN, 'ActorList.php');
    exit;
}
$actorMapper = Mapper::singleton('Actor');
$actor = $actorMapper->load(array('Id'=>$_REQUEST['actorID']));
$retURL = 'LicenceList.php?actorID=' . $_REQUEST['actorID'];

/**
 * On charge la licence ou on prend celle en session, ou on en crée une nouvelle
 **/
if (isset($_REQUEST['flcID']) && false != $_REQUEST['flcID']) {
    $lic = $mapper->load(array('Id'=>$_REQUEST['flcID']));
} else if (isset($_SESSION['lic'])) {
    $lic = $_SESSION['lic'];
} else {
    $lic = new Licence();
}
/**
 * On vérifie que c'est bien une licence ;)
 **/
if (!($lic instanceof Licence)) {
    Template::errorDialog(sprintf(E_MSG_MUST_SELECT_A, _('License')), $retURL);
    exit;
}

$session->register('lic', $lic, 2);
/**
 * nous servira à déterminer s'il s'agit d'un ajout de licence, auquel cas,
 * il faudra l'ajouter aux licences de l'acteur
 **/
$isNewLicence = !$lic->hasBeenInitialized;

/**
 * Cleanage de session
 **/
$sname = SearchTools::getGridItemsSessionName();
if (isset($_SESSION[$sname])) {
    unset($_SESSION[$sname]);
}
if (isset($_SESSION['rat'])) {
    unset($_SESSION['rat']);
}
/**
 * Le grid des qualifications
 **/
$grid = new Grid();
$grid->displayCancelFilter = false;
$grid->withNoSortableColumn = true;
// actions
$grid->NewAction(
    'AddEdit',
    array(
        'Action'=>'Add',
        'EntityType' => 'Rating',
        'Query'=>'actorID=' . $actor->getId()
    )
);
$grid->NewAction(
	'Delete',
	array(
		'TransmitedArrayName'=>'ratIDs',
		'EntityType'=>'Rating',
        'Query'=>'actorID=' . $actor->getId()
	)
);
// Colonnes
$grid->NewColumn('FieldMapper',
                 _('Type'),
                 array('Macro' =>'<a href="RatingAddEdit.php?ratID=%Id%&actorID='.$actor->getId().'&retURL='.$_SERVER['PHP_SELF'].'">%Type.Name%</a>')
                );
$grid->NewColumn('FieldMapper', _('Airplane type'), array('Macro' =>'%FlyType.Name%'));
$grid->NewColumn('FieldMapper', _('Validity'),
    array('Macro' => _('from') . ' %BeginDate|formatdate@DATE_SHORT% ' .
                     _('at') . ' %EndDate|formatdate@DATE_SHORT%'));

$ratCollection = $lic->getRatingCollection();
/**
 * Traitement de l'envoi du formulaire
 */
if (isset($_POST['formSubmitted'])) {
    $errorUrl = $_SERVER['PHP_SELF'] . '?actorID=' . $actor->getId();
    // gérer les duration/date sur les données POST
    // on calcule la date de fin en faisant:
    // date de début + (durée * typedurée (mois ou jours))
    if ($_POST['Licence_BeginDate'] != 0) {
        $d = DateTimeTools::MySQLDateToTimeStamp(
            $_POST['Licence_BeginDate']) +
            $_POST['Licence_Duration'] * 86400 *
            ($_POST['Licence_DurationType']>0?31:1);
        $_POST['Licence_EndDate'] = DateTimeTools::timeStampToMySQLDate($d);
    } else {
        $_POST['Licence_EndDate'] = 0;
    }

	if ($_POST['DelayForAlertType'] == 0) {
	    $_POST['Licence_DelayForAlert'] = $_POST['DelayForAlertNber'];
	}
	else {
		$_POST['Licence_DelayForAlert'] = $_POST['DelayForAlertNber'] * 31;
	}
	// On remplit l'objet
    FormTools::autoHandlePostData($_POST, $lic);

    if ($grid->isPendingAction()) {
        $res = $grid->dispatchAction($ratCollection);
        if (Tools::isException($res)) {
            Template::errorDialog(E_ERROR_IN_EXEC . ': ' . $res->getMessage(),
                $errorUrl);
            exit;
        }
    }
    Database::connection()->startTrans();
    saveInstance($lic, $errorUrl);
    $count = $ratCollection->getCount();
    for($i=0; $i<$count; $i++){
        $rat = $ratCollection->getItem($i);
        $rat->setLicence($lic->getId());
        saveInstance($rat, $errorUrl);
    }
    // si on est dans le cas d'une nouvelle licence, il faut l'ajouter à la
    // collection de licences de l'acteur
    if ($isNewLicence) {
        $licCollection = $actor->getLicenceCollection();
        $licCollection->setItem($lic);
        saveInstance($actor, $errorUrl);
    }

    /**
     * On commite la transaction,
     * si la transaction a réussi, on redirige vers un message d'information
     * sinon vers un message d'erreur
     */
    if (Database::connection()->hasFailedTrans()) {
        if (DEV_VERSION) {
            trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        }
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_MSG_TRY_AGAIN, $retURL);
        exit;
    }

    Database::connection()->completeTrans();
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
$smarty->assign('actorID', $actor->getId());
$LicenceTypeArray = SearchTools::CreateArrayIDFromCollection('LicenceType', array(), _('Select a type'));
$smarty->assign('LicenceTypeArray', $LicenceTypeArray);
$smarty->assign('Duration', range(0, 500));
$smarty->assign('DurationType', array(0=>'jour(s)', 1=>'mois'));
$delayForAlertNber = ($lic->getDelayForAlert() % 31 == 0)?$lic->getDelayForAlert() / 31
		:$lic->getDelayForAlert();
$smarty->assign('DelayForAlertNber', $delayForAlertNber);
$smarty->assign('Licence::TOBECHECKED_NEVER', 0);
$smarty->assign('Licence::TOBECHECKED_ALERT', 1);
$smarty->assign('Licence::TOBECHECKED_ALERT_COMMAND', 2);

/**
 * On assigne les propriétés de l'objet à la page
 */
$properties = $lic->getProperties();
foreach($properties as $name=>$type){
    $getter = (is_string($type))?'get' . $name . 'Id':'get' . $name;
	$smarty->assign('Licence_' . $name, $lic->$getter());
}
$smarty->assign('Licence_BeginDate_Display',
    I18N::formatDate($lic->getBeginDate()));
$result = $grid->render($ratCollection, false);
$smarty->assign('RatingGrid', $result);

/**
 * On affiche la page
 **/
$pageContent = $smarty->fetch('Licence/LicenceAddEdit.html');

$js = array(
    'js/lib-functions/checkForm.js',
    'js/includes/LicenceAddEdit.js',
	'js/jscalendar/calendar.js',
	getJSCalendarLangFile(),
	'js/jscalendar/calendar-setup.js'
);
Template::page($pageTitle, $pageContent, $js);
?>
