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

function cleanActorAddEditSessionData(){
	$sessionNames = array('actor', 'supplierCustomer', 'site', 'contact', 
	       'ActorBankDetail', 'actorDetail');
	foreach($_SESSION as $key=>$val){
        // cleanage des sessions dans sessionNames et des tempids
        if (in_array($key, $sessionNames) || substr($key, -6) == 'tempId') {
		    unset($_SESSION[$key]);
        }
	}
}

/**
 * saveAll()
 * Sauve toute l'acteur et tout ses objets liés, pratique pour gérer
 * l'annulation.
 *
 * @access public
 * @param object Actor l'acteur en session
 * @return void
 **/
function saveAll($actor){
    //Database::connection()->debug = true;
    $url = 'ActorList.php';
    $actor = $_SESSION['actor'];
	Database::connection()->startTrans();

	if (isset($_REQUEST['SupplierCustomer_Type'])
            && $_REQUEST['SupplierCustomer_Type'] != -1) {
		saveInstance($_SESSION['supplierCustomer'], $url);
	}
	else {
	    /* on peut venir de ProductCommand.php auquel cas cette
	    variable n'est pas renseigné */
	    if(isset($_SESSION['supplierCustomer'])) {
            deleteInstance($_SESSION['supplierCustomer'], $url);
	    }
	}
	if (isset($_SESSION['CustomerProperties'])) {
	    saveInstance($_SESSION['CustomerProperties'], $url);
        $actor->setCustomerProperties($_SESSION['CustomerProperties']);
        // Prochaine visite: Si une frequence est definie, mais qu'il n'y a pas
        // de prochaine visite de prevue, il faut en creer une
        // frequency = nb de semaines
        $frequency = Tools::getValueFromMacro($_SESSION['CustomerProperties'],
                '%PersonalFrequency.Frequency%');
        if ($frequency != 0) {
            $action = $actor->getNextMeetingAction();
            if (Tools::isEmptyObject($action)) {
                $action = new Action();
                $action->setType(FormModel::ACTION_TYPE_MEETING);
                $action->setState(Action::ACTION_STATE_TODO);
                $nbSecPerWeek = 7*24*60*60;
                $action->setWishedDate(
                    DateTimeTools::DateModeler(date('Y-m-d H:i:s'), $frequency*$nbSecPerWeek));
                $action->setActor($actor);
                $action->setCommercial($actor->getCommercialId());
                $formModelMapper = Mapper::singleton('FormModel');
                $FormModel = $formModelMapper->load(
                        array('ActionType' => FormModel::ACTION_TYPE_MEETING));
                if (!Tools::isEmptyObject($FormModel)) {
                    $action->setFormModel($FormModel);
                }
                saveInstance($action, $url);
            }
        }

	}

    $siteCollection = $actor->getSiteCollection();
    $count = $siteCollection->getCount();
    // suppression des sites eventuels
    foreach ($siteCollection->removedItems as $siteID) {
        $site = Object::load('Site', $siteID);
        if ($site instanceof Site) {
            deleteInstance($site, $url);
        }
    } 
    for($i = 0; $i < $count; $i++){
        // sauvegarde du site/contacts/adresse/planning/dailyplannings
    	$site = $siteCollection->getItem($i);
        $planning = $site->getPlanning();
        $monday = $planning->getMonday();
        saveInstance($monday, $url);
        $tuesday = $planning->getTuesday();
        saveInstance($tuesday, $url);
        $wednesday = $planning->getWednesday();
        saveInstance($wednesday, $url);
        $thursday = $planning->getThursday();
        saveInstance($thursday, $url);
        $friday = $planning->getFriday();
        saveInstance($friday, $url);
        $saturday = $planning->getSaturday();
        saveInstance($saturday, $url);
        $sunday = $planning->getSunday();
        saveInstance($sunday, $url);
        $ucol = $planning->getUnavailabilityCollection();
        $jcount = $ucol->getCount();
        for($j=0; $j<$jcount; $j++){
            $u = $ucol->getItem($j);
            saveInstance($u, $url);
        }
        saveInstance($planning, $url);
        $contactCollection = $site->getContactCollection();
        $jcount = $contactCollection->getCount();
        for($j = 0; $j < $jcount; $j++){
        	$contact = $contactCollection->getItem($j);
            saveInstance($contact, $url);
        }
        saveInstance($site, $url);
    }
    // ActorBankDetailCollection
    $abdCollection = $actor->getActorBankDetailCollection();
    // suppression des abd eventuels
    foreach ($abdCollection->removedItems as $abdID) {
        $abd = Object::load('ActorBankDetail', $abdID);
        if ($abd instanceof ActorBankDetail) {
            deleteInstance($abd);
        }
    } 
    $count = $abdCollection->getCount();
    for($i = 0; $i < $count; $i++){
    	$abd = $abdCollection->getItem($i);
    	saveInstance($abd, $url);
    }
    if(isset($_REQUEST['MainSite_ID'])) {
        $actor->setMainSite($_REQUEST['MainSite_ID']);
    }
    
    // Consulting context: gestion du ActorDetail
    if (isset($_SESSION['actorDetail'])) {
        saveInstance($_SESSION['actorDetail'], $url);
        $_SESSION['actor']->setActorDetail($_SESSION['actorDetail']);
    }

    saveInstance($actor, $url);

    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        return false;
    }
    Database::connection()->completeTrans();
}

/**
 *
 * @access public
 * @return void
 **/
function includeSessionRequirements(){
    require_once('Objects/Actor.php');
    require_once('Objects/ActorDetail.php');
    require_once('Objects/Customer.php');
    require_once('Objects/Supplier.php');
    require_once('Objects/AeroCustomer.php');
    require_once('Objects/AeroSupplier.php');
    require_once('Objects/AeroOperator.php');
    require_once('Objects/AeroInstructor.php');
    require_once('Objects/Site.php');
    require_once('Objects/StorageSite.php');
    require_once('Objects/Zip.php');
    require_once('Objects/CityName.php');
    require_once('Objects/Incoterm.php');
    require_once('Objects/Job.php');
    require_once('Objects/Contact.php');
    require_once('Objects/Category.php');
    require_once('Objects/CountryCity.php');
    require_once('Objects/WeeklyPlanning.php');
    require_once('Objects/Unavailability.php');
    require_once('Objects/DailyPlanning.php');
    require_once('Objects/SupplierCustomer.php');
    require_once('Objects/CommunicationModality.php');
    require_once('Objects/Zone.php');
    require_once('Objects/ActorBankDetail.php');
    require_once('Objects/Currency.php');
    require_once('Objects/PricingZone.php');
    require_once('Objects/ActorBankDetail.php');
    require_once('Objects/CustomerProperties.php');
    require_once('Objects/CustomerFrequency.php');
    require_once('Objects/CustomerPotential.php');
    require_once('Objects/CustomerSituation.php');
    require_once('Objects/AccountingType.php');
}

/**
 * ::generateTempId()
 *
 *
 * @access public
 * @param object
 * @return void
 **/
function generateTempId($object){
	$key = get_class($object) . '_tempID';
    if (isset($_SESSION[$key])) {
        return ++$_SESSION[$key];
    } else {
        $_SESSION[$key] = 1;
        return $_SESSION[$key];
    }
}
/**
 * getItemInCollection()
 *
 *
 * @access public
 * @return void
 **/
function getItemInCollection($collection, $itemID){
    $index = -1;
    $count = $collection->getCount();
    for($i = 0; $i < $count; $i++){
    	$c_item = $collection->getItem($i);
        if ($c_item->getId() == $itemID) {
            return $c_item;
        }
    }
}

/**
 * replaceItemInCollection()
 *
 *
 * @access public
 * @return void
 **/
function replaceItemInCollection($collection, $item){
    $index = -1;
    $count = $collection->getCount();
    for($i = 0; $i < $count; $i++){
    	$c_item = $collection->getItem($i);
        if ($c_item->getId() == $item->getId()) {
            $index = $i;
        }
    }
    if ($index != -1) {
        $collection->setItem($item, $index);
    }
}

/**
 * Pour chaque item du tableau $_POST, cette fonction vérifie s'il existe un
 * champs style 'NomDeChamps_Hours' ou 'NomDeChamps_Minutes' et converti ceux-ci
 * en centièmes d'heures dans $_POST['NomDeChamps'].
 *
 * @access public
 * @return void
 **/
function convertHoursAndMinutesToHundredthOfHours(){
	if (!isset($_POST)) {
	    return;
	}
    $handled = array();
    foreach($_POST as $key=>$val){
        $pos_hour = strpos($key, '_Hours');
        $pos_min  = strpos($key, '_Minutes');
        if (false === $pos_hour && false === $pos_min) {
            continue;
        }
        $prefix = substr($key, 0, strrpos($key, '_'));
        if (isset($handled[$prefix])) {
            continue;
        }
        $hour_prefix = $prefix . '_Hours';
        $min_prefix  = $prefix . '_Minutes';
        $hours = isset($_POST[$hour_prefix])?$_POST[$hour_prefix]:'00';
        $mins  = isset($_POST[$min_prefix])?$_POST[$min_prefix]:'00';
        $value = DateTimeTools::getHundredthsOfHour($hours . ':' . $mins);

        $_POST[$prefix] = $value;
        $handled[$prefix] = true;
    }
}

/**
 * Assigne des variables smarty ayant pour nom des noms d'attribut
 * d'un objet, lorsqu'on ne sait pas quelle est sa classe, donc
 * s'il possède bien ces attributs
 * @param $smarty object Smarty
 * @param $instance object
 * @param $attribute mixed : nom ou tableau de noms d'attributs
 * @return void 
 **/
function assignObjectAttributes($smarty, $instance, $attribute=array()){
    if (!is_array($attribute)) {
        $attribute = array($attribute);
    }
    for($i=0;$i<count($attribute);$i++){
        $getter = 'get'.$attribute[$i];
        if (method_exists($instance, $getter)) {
            $smarty->assign($attribute[$i], $instance->$getter());
        }
    }
}

?>
