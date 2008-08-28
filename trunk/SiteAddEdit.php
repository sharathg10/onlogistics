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
require_once('Objects/Country.php');

$auth = Auth::Singleton();
$ProfileId = $auth->getProfile();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_AERO_CUSTOMER,
          UserAccount::PROFILE_CUSTOMER, UserAccount::PROFILE_OWNER_CUSTOMER,
          UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_DIR_COMMERCIAL, UserAccount::PROFILE_GED_PROJECT_MANAGER));

$retURL = isset($_REQUEST['retURL'])&&!empty($_REQUEST['retURL'])?
    $_REQUEST['retURL']:'ActorAddEdit.php';

$session = Session::Singleton();
SearchTools::ProlongDataInSession(1);
$smarty = new Template();
$siteMapper = Mapper::singleton('Site');

/**
 * Messages
 */
$errorBody = _('Site cannot be saved');
$okTitle = I_CONFIRM_DO;
$okBody = _('Site "%s" was successfully saved.');
$pageTitle = _('Add or update site');
$choiceDialogTitle = _('More than one result match.');
$choiceDialogBody = _('More than one result match, please select a result:');
$siteAlreadyExist = _('A site already exists with the name "%s", please correct.');

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

/**
 * On check si l'acteur est bien chargé, et on renvoie vers un dialogue
 * d'erreur au cas où.
 */
if (!$actor || Tools::isException($actor)) {
    $msg = $actor?$actor->getMessage():_('Actor was not found in the database.');
    Template::errorDialog($msg, $retURL);
    exit;
}

// mise en session (ou prolongement de celle-ci) de l'acteur
$session->register('actor', $actor, 3);

/**
 * Si le site est passé en paramètre on le charge sinon on en construit un
 * nouveau, le tout via objectLoader.
 */
$siteID = isset($_REQUEST['sitId'])?$_REQUEST['sitId']:false;
$site = false;
// Si un acteur est en session, on va chercher le site dans la collection
// car il n'a peut-être pas encore été sauvé

if ($siteID) {
    $siteCollection = $_SESSION['actor']->getSiteCollection();
    $site = getItemInCollection($siteCollection, $siteID);
    if (false == $site) {
        // site pas trouvé
        $site = new Site();
    }
} else if (isset($_SESSION['site'])) {
    // cas d'un site en session
    $site = $_SESSION['site'];
} else {
    $site = new Site();
}


/**
 * On check si le site est bien chargé, et on renvoie vers un dialogue
 * d'erreur au cas où.
 */
if (false == $site || Tools::isException($site)) {
    $msg = Tools::isException($site)?$site->getMessage():_('Site was not found in the database');
    Template::errorDialog($msg, $retURL);
    exit;
}

// on lui attribue l'actor comme owner
$site->setOwner($_SESSION['actor']);

/**
 * On met le site en session
 */
$session->register('site', $site, 3);

// Ne pas traduire: sert aux getter de Planning
$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',
    'Saturday', 'Sunday');
/**
 * Traitement de l'envoi du formulaire
 */
if(isset($_POST['CountryCity_Id'])) {
    $_REQUEST['Site_CountryCity_Id'] = $_POST['CountryCity_Id'];
}
$mapper = Mapper::singleton('Site');

if (isset($_POST['formSubmitted']) && !isset($_REQUEST['Site_CountryCity_Id'])) {
    /* Controle:
        - pas deja un Site avec ce Name en base
        - pas deja un Site avec ce Name dans $actor->getSiteCollection() (en session)
    */
    $name = $_POST['Site_Name'];
    $siteTest = $_SESSION['actor']->getSiteCollection()->getItemByObjectProperty(
            'Name', $name);
    if (($name != $site->getName())
    && ($mapper->alreadyExists(array('Name' => $name)) || !Tools::isEmptyObject($siteTest))) {
        //  On remplit l'objet site pour conserver les saisies
/*        FormTools::autoHandlePostData($_POST, $_SESSION['site'], 'Site');
        $_SESSION['site']->setName('');
echo $_SESSION['site']->getCountryCity();*/
        Template::infoDialog(sprintf($siteAlreadyExist, $name), $_SERVER['PHP_SELF']);
        exit;
    }
    //  On remplit l'objet site
    FormTools::autoHandlePostData($_POST, $_SESSION['site'], 'Site');
    if (Tools::isException($_SESSION['site'])) {
        Template::errorDialog($_SESSION['site']->getMessage(), $retURL);
        exit;
    }
    // type de site
    if (isset($_REQUEST['IsInvoiceAddress']) &&
            isset($_REQUEST['IsDeliveryAddress'])) {
        $_SESSION['site']->setType(Site::SITE_TYPE_FACTURATION_LIVRAISON);
    } else if (isset($_REQUEST['IsInvoiceAddress'])) {
        $_SESSION['site']->setType(Site::SITE_TYPE_FACTURATION);
    } else if (isset($_REQUEST['IsDeliveryAddress'])) {
        $_SESSION['site']->setType(Site::SITE_TYPE_LIVRAISON);
    } else {
        $_SESSION['site']->setType(Site::SITE_TYPE_QUELCONQUE);
    }

    /**
     * Il faut mettre le country/zip/city en session, car ils ne sont pas
     * encore assignés au site, on attend la vérification pour les assigner
     */
    $cityname = isset($_REQUEST['Site_CountryCity_CityName_Name'])?$_REQUEST['Site_CountryCity_CityName_Name']:'';
    $session->register('cityname', $cityname, 3);

    $ctryname = isset($_REQUEST['Site_CountryCity_Country_Name'])?$_REQUEST['Site_CountryCity_Country_Name']:'';
    $session->register('ctryname', $ctryname, 3);

    $zipcode = isset($_REQUEST['Site_CountryCity_Zip_Code'])?$_REQUEST['Site_CountryCity_Zip_Code']:'';
    $session->register('zipcode', $zipcode, 3);

    /**
     * On remplit l'objet Planning
     */
	$planning = $_SESSION['site']->getPlanning(false);
    if (!($planning instanceof WeeklyPlanning)) {
        $planning = new WeeklyPlanning();
    }
    FormTools::autoHandlePostData($_POST, $planning);
    // les daily plannings
    foreach($days as $day) {
        $getter = 'get' . $day;
        $dailyPlanning = $planning->$getter();
        if (!($dailyPlanning instanceof DailyPlanning)) {
            $dailyPlanning = new DailyPlanning();
        }
        $dailyPlanning->setStart($_POST[$day . '_DailyPlanning_StartHour'] . ':' . $_POST[$day . '_DailyPlanning_StartMinute']);
        $dailyPlanning->setPause($_POST[$day . '_DailyPlanning_PauseHour'] . ':' . $_POST[$day . '_DailyPlanning_PauseMinute']);
        $dailyPlanning->setRestart($_POST[$day . '_DailyPlanning_RestartHour'] . ':' . $_POST[$day . '_DailyPlanning_RestartMinute']);
        $dailyPlanning->setEnd($_POST[$day . '_DailyPlanning_EndHour'] . ':' . $_POST[$day . '_DailyPlanning_EndMinute']);
        $setter = sprintf('set%s', $day);
        $planning->$setter($dailyPlanning);
        unset($dailyPlanning, $dailyPlanningID, $day);
    }
    $_SESSION['site']->setPlanning($planning);
    if (isset($_REQUEST['IsStorageSite'])) {
        if (get_class($_SESSION['site']) == 'Site') {
            $_SESSION['site'] = $_SESSION['site']->mutate('StorageSite');
        }
        $_SESSION['site']->setStockOwner($_REQUEST['StockOwner_Id']);
    } else {
        if (get_class($_SESSION['site']) == 'StorageSite') {
            $_SESSION['site'] = $_SESSION['site']->mutate('Site');
        }
    }
    $self = basename($_SERVER['PHP_SELF']);
    if (isset($_REQUEST['UnavailabilityToAdd']) &&
        $_REQUEST['UnavailabilityToAdd'] != -1)
    {
        Tools::redirectTo(sprintf("UnavailabilityAddEdit.php?unaId=%s&retURL=%s",
                $_REQUEST['UnavailabilityToAdd'], $self));
        exit;
    } else if (isset($_REQUEST['UnavailabilityToDelete']) &&
        $_REQUEST['UnavailabilityToDelete'] != -1)
    {
        Tools::redirectTo(sprintf("UnavailabilityAddEdit.php?unaId=%s&retURL=%s" .
                '&unavailability_delete=1',
                $_REQUEST['UnavailabilityToDelete'], $self));
        exit;
    }
    /**
     * on gère les actions possibles
     */
    if (isset($_REQUEST['ContactToAdd']) && $_REQUEST['ContactToAdd'] != -1) {
        // le formulaire a été soumis avec la variable 'addContact' à true
        // on redirige vers le formulaire d'ajout de contact
        Tools::redirectTo(sprintf("ContactAddEdit.php?ctcId=%s&retURL=%s&origRetURL=%s",
                $_REQUEST['ContactToAdd'], $self, $retURL));
        exit;
    } else if (isset($_REQUEST['ContactToDelete']) && $_REQUEST['ContactToDelete'] != -1) {
        Tools::redirectTo(sprintf("ContactDelete.php?ctcId=%s&retURL=%s&origRetURL=%s",
                $_REQUEST['ContactToDelete'], $self, $retURL));
        exit;
    }
}
if (isset($_POST['formSubmitted'])) {
    // tuple country/city/zip
    if (isset($_REQUEST['Site_CountryCity_Id'])) {
        $match = Object::load('CountryCity',
            $_REQUEST['Site_CountryCity_Id']);

        $_SESSION['site']->setCountryCity($match);
    } else {
        require_once('CityZipCountryHelper.php');
        $helper = new CityZipCountryHelper($smarty, $_SESSION['zipcode'],
            $_SESSION['ctryname'], $_SESSION['cityname']);
        $match = $helper->findExactMatch();
        if (Tools::isException($match)) {
            Template::errorDialog($match->getMessage(), 'SiteAddEdit.php');
            exit;
        }
        if (false == $match) {
            $matches = $helper->findMatches();
            return $helper->showSuggestions($matches, 'SiteAddEdit.php', $retURL);
        }
        $_SESSION['site']->setCountryCity($match);
    }
    $siteCollection = $_SESSION['actor']->getSiteCollection();
    if ($site->getId() == false) {
        $_SESSION['site']->setId($mapper->generateId());
        $siteCollection->setItem($_SESSION['site']);
    } else {
        replaceItemInCollection($siteCollection, $_SESSION['site']);
    }
    $validationLink = $retURL;
    if (isset($_SESSION['asPopup']) && isset($_SESSION['widgetName'])) {
        $validationLink = sprintf(
            'JS_SiteAddEdit.php?sitName=%s&sitId=%s&widgetName=%s',
            $_SESSION['site']->getName(), $_SESSION['site']->getId(),
            $_SESSION['widgetName']);
        // il faut sauver l'acteur
        if (false != $actor) {
            saveAll($actor);
            unset($_SESSION['actor']);
        }
        // cleanage des sessions
        unset($_SESSION['asPopup'], $_SESSION['widgetName']);
    }
    /**
     * On cleane la session
     */
    unset($_SESSION['site'], $_SESSION['cityname'], $_SESSION['ctryname'],
          $_SESSION['zipcode']);

    Tools::redirectTo($validationLink);
    exit;
}

/**
 * Assignation des variables au formulaire avec smarty
 */
$smarty->assign('FormAction', $_SERVER['SCRIPT_NAME']);
$smarty->assign('retURL', $retURL);

// Zone
$zoneId = $_SESSION['site']->getZoneId();
$zoneOptions  = FormTools::writeOptionsFromObject('Zone', $zoneId, array(),
        array('Name' => SORT_ASC));
$smarty->assign('ZoneList', join("\n\t\t", $zoneOptions));

/**
 * On assigne le site et son countryCity
 */
 // assign plutot que register_object, car la ligne suivante ds le template
 // provoque une erreur:
 // {if $Site->getName() != ""}{$Site->getName()}{else}{Actor->getName}{/if}
$smarty->assign('Site', $_SESSION['site']);  // register_object
$smarty->register_object('Actor', $_SESSION['actor']);
if (isset($_REQUEST['IsStorageSite']) || get_class($_SESSION['site']) == 'StorageSite') {
    $smarty->assign('IsStorageSite', 'checked');
}
// Un storageSite en mode Edition, contenant au moins 1 Store ne peut pas
// devenir un Site
if (get_class($_SESSION['site']) == 'StorageSite'
&& !Tools::isEmptyObject(
$_SESSION['site']->getStoreCollection(array(), array(), array('Id')))) {
    $smarty->assign('IsStorageSiteDisabled', 'true');
}else {
    $smarty->assign('IsStorageSiteDisabled', 'false');
}
$siteType = $_SESSION['site']->getType();
if ($siteType == Site::SITE_TYPE_LIVRAISON || $siteType == Site::SITE_TYPE_FACTURATION_LIVRAISON) {
    $smarty->assign('IsDeliveryAddress', 'checked');
}
if ($siteType == Site::SITE_TYPE_FACTURATION || $siteType == Site::SITE_TYPE_FACTURATION_LIVRAISON) {
    $smarty->assign('IsInvoiceAddress', 'checked');
}

$countryCity = false;
$countryCity = $_SESSION['site']->getCountryCity();
if (!($countryCity instanceof CountryCity)) {
    $cityname = isset($_SESSION['cityname'])?$_SESSION['cityname']:'';
    $zipcode = isset($_SESSION['zipcode'])?$_SESSION['zipcode']:'';
} else {
    $ctn = $countryCity->getCityName();
    $zip = $countryCity->getZip();
    $cityname = ($ctn instanceof CityName)?$ctn->getName():'';
    $zipcode = ($zip instanceof Zip)?$zip->getCode():'';
}
$smarty->assign('CityName_Name', $cityname);
$smarty->assign('Zip_Code', $zipcode);

/**
 * reste à assigner les propriétés multiples,
 * ici les contacts et les plannings
 */
$contactCollection = $_SESSION['site']->getContactCollection();
$contactList = array();
for($i = 0; $i < $contactCollection->getCount(); $i++) {
    $contact = $contactCollection->getItem($i);
    $contactList[] = $contact;
    unset($contact);
} // for
$smarty->assign('ContactList', $contactList);

$weeklyPlanning = $_SESSION['site']->getPlanning(false);

if (!($weeklyPlanning instanceof WeeklyPlanning)) {
    $weeklyPlanning = Object::load('WeeklyPlanning');
}
$weeklyPlanning->renderTemplate($smarty);

/**
 * Et les propriétés qui doivent être affichées sous forme de select
 */
$actorSelected = method_exists($_SESSION['site'], 'getStockOwnerID')?$_SESSION['site']->getStockOwnerID():0;
$ownerOptions = FormTools::writeOptionsFromObject('Actor', $actorSelected, array(), array('Name' => SORT_ASC));
$smarty->assign('OwnerList', join("\n\t\t", $ownerOptions));

$streetType  = $site->getStreetType();
$streetTypes = Site::getStreetTypeConstArray();
$StreetTypeList = FormTools::writeOptionsFromArray($streetTypes, $streetType);
$smarty->assign('StreetTypeList', join("\n\t\t", $StreetTypeList));
// France par defaut si pas de coutry défini
$siteCountry = Tools::getValueFromMacro($_SESSION['site'], '%CountryCity.Country.Id%');
$defaultCountry = isset($_SESSION['ctryname']) && $_SESSION['ctryname'] > 0?$_SESSION['ctryname']:7;
$siteCountry = $siteCountry > 0?$siteCountry:$defaultCountry;
$coutryOptions = FormTools::writeOptionsFromObject('Country', $siteCountry,
        array(), array('Name' => SORT_ASC));
$smarty->assign('CountryList', join("\n\t\t", $coutryOptions));

$commModOptions = getCommunicationModalityModesAsOptions();
$smarty->assign('CommModeList', join("\n\t\t", $commModOptions));

if (isset($_REQUEST['asPopup'])) {
    $asPopup = $_REQUEST['asPopup'];
    $session->register('asPopup', $asPopup, 3);
}
if (isset($_REQUEST['widgetName'])) {
    $widgetName = $_REQUEST['widgetName'];
    $session->register('widgetName', $widgetName, 3);
}

/**
 * Et on affiche la page
 */
$pageContent = $smarty->fetch('Site/SiteAddEdit.html');
$js = array('js/lib-functions/checkForm.js', 'js/includes/SiteAddEdit.js');

$tpl = isset($_SESSION['asPopup'])?BASE_POPUP_TEMPLATE:BASE_TEMPLATE;
Template::ajaxPage($pageTitle, $pageContent, $js, array(), $tpl);

?>
