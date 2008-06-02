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
require_once('Objects/Unavailability.php');
require_once('Objects/WeeklyPlanning.php');
require_once('LangTools.php');

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'home.php';

/**
 * Gestion de la page précédente
 **/
$planning = false;
if ($retURL == 'SiteAddEdit.php') {
    // on inclue les fichiers de session relatifs à ActorAddEdit
    require_once('ActorAddEditTools.php');
    includeSessionRequirements();
    $session = Session::singleton();
    if ($_SESSION['site'] && $_SESSION['site'] instanceof Site) {
        $planning = $_SESSION['site']->getPlanning();
        if($planning->getId()==0) {
            $planning->generateId();
            saveInstance($planning, $retURL);
        }
    }    
} else if ($retURL == 'ConcreteProductAddEdit.php') {
    // on inclue les fichiers de session relatifs à ConcreteProductAddEdit
    require_once('ConcreteProductAddEditTools.php');
    includeSessionRequirements();
    $session = Session::singleton();
    if ($_SESSION['ccp'] && $_SESSION['ccp'] instanceof ConcreteProduct) {
        $planning = $_SESSION['ccp']->getWeeklyPlanning();
    }
}
SearchTools::ProlongDataInSession(1);
/**
 * Session et authentification
 */
$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_CUSTOMER,
          UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_DIR_COMMERCIAL, UserAccount::PROFILE_GED_PROJECT_MANAGER));

/**
 * Messages
 */
$errorTitle = 'Erreur fatale.';
$pageTitle  = _('Add or update unavailability');

/**
 * Si l'id est passée en paramètre on la charge sinon
 * on instancie un nouvel objet
 *
 */
$unaId = isset($_REQUEST['unaId'])?$_REQUEST['unaId']:false;

/**
 * On check si l'objet est bien chargé, et on renvoie vers un dialogue
 * d'erreur au cas où.
 */
if (false != $unaId) {
    if (false != $planning) {
        $unavCollection = $planning->getUnavailabilityCollection();
        $count = $unavCollection->getCount();
        for($i=0; $i<$count; $i++){
            $item = $unavCollection->getItem($i);
            if ($item->getId() == $unaId) {
                break;
            }
        }
        $unav = $item;
    } else {
        $unav  = Object::load('Unavailability', $unaId);
    }
} else {
    $unav = new Unavailability();
}

if (Tools::isException($unav)) {
    Template::errorDialog($unav->getMessage(), $retURL);
    exit;
}

$mapper = Mapper::singleton('Unavailability');
if (isset($_REQUEST['unavailability_delete'])) {
    if (false != $planning) {
        $unavCollection = $planning->getUnavailabilityCollection();
        $count = $unavCollection->getCount();
        for($i=0; $i<$count; $i++){
            $item = $unavCollection->getItem($i);
            if ($item->getId() == $unav->getId()) {
                $unavCollection->removeItem($i);
                break;
            }
        }
    }
    $mapper->delete($unav->getId());
    Tools::redirectTo($retURL);
    exit;
}
/**
 * Traitement de l'envoi du formulaire
 */
if (isset($_POST['formSubmitted'])) {
    if($_POST['Unavailability_BeginDate'] > $_POST['Unavailability_EndDate'] ) {
        Template::errorDialog(_('Wrong dates provided'), 'UnavailabilityAddEdit.php?retURL=' . $retURL);
        exit();
    }
    /**
     * On remplit l'objet
     */
    FormTools::autoHandlePostData($_POST, $unav);
    if (false != $planning) {
        $unavCollection = $planning->getUnavailabilityCollection();
        if ($unav->getId() == 0) {
            $unav->setId($mapper->generateId());
        }
        $unav->setWeeklyPlanning($planning);
        $unavCollection->acceptDuplicate = false;
        $unavCollection->setItem($unav);
    }
    saveInstance($unav, $retURL);
    Tools::redirectTo($retURL);
    exit;
}

/**
 * Assignation des variables au formulaire avec smarty
 */
$smarty = new Template();
$smarty->assign('FormAction', $_SERVER['PHP_SELF']);
$smarty->assign('retURL', $retURL);

// On assigne les propriétés de l'objet à la page
$smarty->assign('Unavailability_Id', $unav->getId());
$smarty->assign('Unavailability_Purpose', $unav->getPurpose());
$smarty->assign('Unavailability_BeginDate', $unav->getBeginDate());
$smarty->assign('Unavailability_EndDate', $unav->getEndDate());
$smarty->assign('Unavailability_BeginDate_Display',
    I18N::formatDate($unav->getBeginDate()));
$smarty->assign('Unavailability_EndDate_Display',
    I18N::formatDate($unav->getEndDate()));

$smarty->assign('Unavailability_WeeklyPlanning', $unav->getWeeklyPlanningId());

/**
 * Et on affiche la page
 **/
$template = 'Unavailability/UnavailabilityAddEdit.html';
$pageContent = $smarty->fetch($template);

$js = array(
    'js/lib-functions/checkForm.js',
    'js/includes/UnavailabilityAddEdit.js',
	'js/jscalendar/calendar.js',
	getJSCalendarLangFile(),
	'js/jscalendar/calendar-setup.js'
);
Template::page($pageTitle, $pageContent, $js);
?>
