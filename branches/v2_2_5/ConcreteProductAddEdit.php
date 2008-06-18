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
require_once ('Objects/ConcreteProduct.php');
require_once ('Objects/AeroConcreteProduct.php');
require_once('ConcreteProductAddEditTools.php');
// requis pour les sessions
includeSessionRequirements();

//require_once('Objects/ConcreteProduct.const.php');
require_once('Objects/ProductType.inc.php');
require_once('LangTools.php');

// Database::connection()->debug = true;
/**
 * Authentification
 */
$session = Session::Singleton();
$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_OPERATOR, UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_AERO_CUSTOMER));

// prolonge les datas en session pour le formulaire de recherche
SearchTools::prolongDataInSession();

/**
 * Messages
 */
$ccpIdError = _('Item with ID "%s" was not found in the database.');
$pdtIdError = _('Product related to the selected item was not found in the database.');
$ccpAlreadyExists = _('An item with the SN/Lot "%s" already exists.');
$accessDenied = _('You don\'t have enough permissions for this item.');
$ccpSavedError = _('Item cannot be saved.');
$fieldRequired = E_VALIDATE_FIELD . " %s " . E_VALIDATE_IS_REQUIRED;
$noTracingModeError = _('Product related to selected item must have a tracking mode (SN or Lot).');
$title = _('Element related to product %s (%s). Tracking mode: %s.');

/**
 * Variables
 */
$ccpId = isset($_REQUEST['ccpId'])?$_REQUEST['ccpId']:0;
$pdtId = isset($_REQUEST['pdtId'])?$_REQUEST['pdtId']:0;
if (isset($_REQUEST['retURL'])) {
    $retURL = $_REQUEST['retURL'];
} else {
    $retURL = $pdtId?'ProductList.php':'ConcreteProductList.php';
}

$ccp_Mapper = Mapper::singleton('ConcreteProduct');

/**
 * Chargement de l'item
 */
if (!isset($_SESSION['ccp']) || false == $_SESSION['ccp']) {
    if ($ccpId) {
        $ccp = $ccp_Mapper->load(array('Id' => $ccpId));
    } else {
        // mode ajout
        if (!$pdtId) {
            Template::errorDialog($pdtIdError, $retURL);
            exit;
        }
        $pdtMapper = Mapper::singleton('Product');
        $tmpProduct = $pdtMapper->load(array('Id'=>$pdtId));
        $cls = $tmpProduct instanceof AeroProduct?'AeroConcreteProduct':'ConcreteProduct';
        $ccp = new $cls();
        $ccp->setProduct($pdtId);
    }
} else {
    $ccp = $_SESSION['ccp'];
}

$product = $ccp->getProduct();
$productTracingMode = $product->getTracingMode();
if (!in_array($productTracingMode, array(Product::TRACINGMODE_SN, Product::TRACINGMODE_LOT))) {
    Template::errorDialog($noTracingModeError, $retURL);
    exit;
}

// s'assurer que l'utiliseur à le droit d'éditer ce produit
if ($auth->getProfile() == UserAccount::PROFILE_AERO_CUSTOMER &&
    $ccp->getOwnerId() != $auth->getActorId()) {
    Template::errorDialog($accessDenied, $retURL);
    exit;
}

$session->register('ccp', $ccp, 3);
$clsname = get_class($ccp);

/**
 * Soumission du formulaire et sauvegarde des données.
 */
if (isset($_POST['formSubmitted'])) {
    /**
     * Vérifications préalables
     *
     **/
    $requiredFields = array('SerialNumber'=>_('serial/lot number'));
    // Pour un AeroConcreteProduct, il faut saisir une immatriculation
    if ($ccp instanceof AeroConcreteProduct) {
        $requiredFields = $requiredFields + array('Immatriculation' => _('matriculation'));
    }
    foreach($requiredFields as $field => $name){
        $field = $clsname . '_' . $field;
        if (!isset($_REQUEST[$field]) || empty($_REQUEST[$field])) {
            Template::errorDialog(sprintf($fieldRequired, $name), $retURL);
            exit;
        }
    }

    /**
     * On contrôle que le SN n'existe pas déjà si le mode de suivi est SN et si
     * le sn est different du sn précédent (pour une edition).
     **/
    $sn = $_REQUEST[$clsname . '_SerialNumber']; // serial number
    if ($_SESSION['ccp']->getSerialNumber() != $sn
            && $ccp_Mapper->alreadyExists(array('SerialNumber' => $sn,
                                             'Product' => $product->getId())))
    {
        Template::errorDialog(sprintf($ccpAlreadyExists, $sn), $retURL);
        exit;
    }

    /**
     * on demarre la transaction
     */
    Database::connection()->startTrans();
    /**
     * on remplit le produit
     */
    // il faut convertir les champs heures en cent. d'heures
    $fields = array('RealHourSinceNew', 'RealHourSinceOverall', 'RealHourSinceRepared');
    foreach($fields as $field){
        $fname = $clsname . '_' . $field;
        $_POST[$fname.'_Hours'] = empty($_POST[$fname.'_Hours'])?
                '00':$_POST[$fname.'_Hours'];
        $_POST[$fname.'_Minutes'] = empty($_POST[$fname.'_Minutes'])?
                '00':$_POST[$fname.'_Minutes'];
        $time = $_POST[$fname.'_Hours'] . ':' . $_POST[$fname.'_Minutes'];
        $_POST[$fname] = DateTimeTools::getHundredthsOfHour($time);
        DateTimeTools::hundredthsOfHourToTime($_POST[$fname]);
    }
    FormTools::autoHandlePostData($_POST, $ccp);

    // son planning
    $planning = $ccp->getWeeklyPlanning();
    if (!($planning instanceof WeeklyPlanning)) {
        $planning = new WeeklyPlanning();
    }
    // Ne pas traduire: sert aux getter de Planning
    $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday',
                  'Friday', 'Saturday', 'Sunday');
    foreach($days as $day) {
        $getter = 'get' . $day;
        $dailyPlanning = $planning->$getter();
        if (!($dailyPlanning instanceof DailyPlanning)) {
            $dailyPlanning = new DailyPlanning();
        }
        $dailyPlanning->setStart($_POST[$day .
                '_DailyPlanning_StartHour'] . ':' .
                $_POST[$day . '_DailyPlanning_StartMinute']);
        $dailyPlanning->setPause($_POST[$day .
                '_DailyPlanning_PauseHour'] . ':' .
                $_POST[$day . '_DailyPlanning_PauseMinute']);
        $dailyPlanning->setRestart($_POST[$day .
                '_DailyPlanning_RestartHour'] . ':' .
                $_POST[$day . '_DailyPlanning_RestartMinute']);
        $dailyPlanning->setEnd($_POST[$day .
                '_DailyPlanning_EndHour'] . ':' .
                $_POST[$day . '_DailyPlanning_EndMinute']);
        $setter = sprintf('set%s', $day);
        $planning->$setter($dailyPlanning);
        saveInstance($dailyPlanning, $retURL);
    }
    $ccp->setWeeklyPlanning($planning);
    /**
     * on sauve les indisponibilités
     *
     **/
    $ucol = $planning->getUnavailabilityCollection();
    $count = $ucol->getCount();
    for($i=0; $i<$count; $i++){
        $u = $ucol->getItem($i);
        saveInstance($u, $retURL);
    }
    saveInstance($planning, $retURL);
    /**
     * on sauve le produit
     */
    saveInstance($ccp, $retURL);

    $self = basename($_SERVER['PHP_SELF']);
    if ($_REQUEST['UnavailabilityToAdd'] != -1) {
        Tools::redirectTo(sprintf("UnavailabilityAddEdit.php?unaId=%s&retURL=%s",
                $_REQUEST['UnavailabilityToAdd'], $self));
        exit;
    } else if ($_REQUEST['UnavailabilityToDelete'] != -1) {
        Tools::redirectTo(sprintf("UnavailabilityAddEdit.php?unaId=%s&retURL=%s" .
                '&unavailability_delete=1',
                $_REQUEST['UnavailabilityToDelete'], $self));
        exit;
    }
    /**
     * On commite la transaction,
     * si la transaction a réussi, on redirige vers un message d'information
     * sinon vers un message d'erreur
     */
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog($ccpSavedError, $retURL);
        Exit;
    }
    Database::connection()->completeTrans();
    /* Suppression des var en session  */
    SearchTools::cleanDataSession('ccp');
    Tools::redirectTo($retURL);
    exit;
}

/**
 * GUI
 */

/**
 * Champs cachés
 */
$smarty = new Template();
$smarty->assign('formAction', $_SERVER['PHP_SELF']);
$smarty->assign('retURL', $retURL);
$smarty->assign('ClassName', $clsname);

/**
 * Affichage des données de l'item
 */
$dates = array('BirthDate', 'OnServiceDate', 'WarrantyBeginDate',
               'WarrantyEndDate', 'EndOfLifeDate');
$hoh = array('RealHourSinceNew', 'RealHourSinceOverall',
             'RealHourSinceRepared', 'VirtualHourSinceNew', 'VirtualHourSinceOverall');
$props = $ccp->getProperties();
foreach($props as $name => $type) {
    if (is_string($type)) {
        // on ne traite que les propriétés simples
        continue;
    }

    $getter = 'get' . $name;
    $val = $ccp->$getter();
    if (in_array($name, $dates)) {
        if ($val != 0) {
            $smarty->assign($name . '_Display', I18N::formatDate($val, I18N::DATE_LONG));
        }

    }
    if (in_array($name, $hoh)) {
        $hhmm = explode(':', DateTimeTools::hundredthsOfHourToTime($val));
        $smarty->assign($name . '_Hours', $hhmm[0]);
        $smarty->assign($name . '_Minutes', $hhmm[1]);
    } else {
        $smarty->assign($name, $val);
    }
}
// cas particuliers: state, ...
//$smarty->assign('StateOptions', implode("\n", getStateOptions($ccp->getState())));
$smarty->assign('StateOptions',
            implode("\n", FormTools::writeOptionsfromArray(
                    ConcreteProduct::getStateConstArray(),
                    $ccp->getState())));
if ($clsname == 'AeroConcreteProduct') {
    //$smarty->assign('UnitOptions', implode("\n", getUnitOptions($ccp->getTankUnitType())));$ccp->getTankUnitType()
    $smarty->assign('UnitOptions',
            implode("\n", FormTools::writeOptionsfromArray(
                    AeroConcreteProduct::getTankUnitTypeConstArray(),
                    $ccp->getTankUnitType())));
}

// Recuperation de l etat actif ou non
$smarty->assign('active', $ccp->getActive());

// cas particulier: owner
$owneroptions = FormTools::writeOptionsFromObject('Actor', $ccp->getOwnerId(),
    array('Active'=>1), array('Name'=>SORT_ASC), 'getName', array('Name'));
$smarty->assign('OwnerOptions', implode("\n", $owneroptions));

// cas particulier: planning
$weeklyPlanning = $ccp->getWeeklyPlanning();
if (!($weeklyPlanning instanceof WeeklyPlanning)) {
    $weeklyPlanning = Object::load('WeeklyPlanning');
}
$weeklyPlanning->renderTemplate($smarty);
// indisponibilités
$unavailabilities = $weeklyPlanning->getUnavailabilityCollection();
$count = $unavailabilities->getCount();
for($i=0; $i<$count; $i++){
    $unav = $unavailabilities->getItem($i);

}
$tmArray = Product::getTracingModeConstArray();
$product = $ccp->getProduct();
$title = sprintf($title,
                 $product->getBaseReference(),
                 $product->getName(),
                 $tmArray[$product->getTracingMode()]);


// Si c'est un AeroProduct, on affiche le FlyType
if ($product instanceof AeroProduct) {
    $FlyType = $product->getFlyType();
    $title .= (Tools::isEmptyObject($FlyType))?
            '':_('Airplane type').' : ' . $FlyType->getName() . '.';
    $smarty->assign('isAero', 1);
}


/**
 * Display du template
 */
$template = 'ConcreteProduct/ConcreteProductAddEdit.html';
$pageContent = $smarty->fetch($template);

$js = array(
        'js/lib-functions/checkForm.js',
        'js/includes/' . $clsname . 'AddEdit.js',
        'js/jscalendar/calendar.js',
        getJSCalendarLangFile(),
        'js/jscalendar/calendar-setup.js'
        );
Template::page($title, $pageContent, $js);

?>
