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
require_once('ActivateChain.php');
require_once('Quantificator/CourseCommandQuantificator.php');
require_once('Scheduler/Scheduler.php');
require_once('AlertSender.php');
require_once('ProductCommandTools.php');
require_once('CourseCommandTools.php');
require_once('Objects/Alert.const.php');
require_once('Objects/CourseCommand.php');
require_once('Objects/CommandItem.php');
require_once('Objects/AeroCustomer.php');
require_once('Objects/AeroInstructor.php');
require_once('Objects/Licence.php');
require_once('Objects/Rating.php');
require_once('Objects/FlyType.php');
require_once('CourseCommandValidator.php');
require_once('Objects/Product.php');
require_once('LangTools.php');

/**
 * Session et authentification
 */
$session = Session::Singleton();

$auth = Auth::Singleton();
$auth->checkProfiles();
$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'CourseCommand.php';


/**
 * messages d'info et d'erreur
 *
 **/

define('I_SUCCESS', _('Your booking is scheduled on %s'));
define('I_INSTRUCTOR_DIFFERENT', _('Wished instructor is not available, class will be given by %s.'));
define('I_INSTRUCTOR_DEFAULT', _('Class will be given by %s.'));
define('E_CUSTOMER_NOT_FOUND', _('Customer not found in the database.'));
define('E_TRANS_FAILED', _('Database transaction failed.'));
define('E_NOFLYTYPE_FOUND',  _('No airplane of this type was found for customer "%s". Operation aborted.'));

/**
 * Assignation des variables au formulaire avec smarty
 */
$smarty = new Template();
$smarty->assign('FormAction', $_SERVER['PHP_SELF']);
$smarty->assign('retURL', $retURL);

if (isset($_REQUEST['Cancel'])) {
	if (isset($_REQUEST['customerID'])) {
	    unset($_REQUEST['customerID']);
	}
    cleanSessionData();
}

/**
 * Gestion du client
 *
 **/
$user = $auth->getUser();
if (isset($_SESSION['customer'])) {
    $customer = $_SESSION['customer'];
} else if (isset($_REQUEST['customerID'])) {
    // on charge le client
    $mapper = Mapper::singleton('AeroCustomer');
    $customer = $mapper->load(array('Id'=>$_REQUEST['customerID']));
} else if ($user->getProfile() == UserAccount::PROFILE_AERO_CUSTOMER) {
    // le client est l'acteur de l'utilisateur connecté
    $customer = $user->getActor();
} else {
    // on redirige vers la page de choix du client
    $cOptions = FormTools::writeOptionsFromObject('AeroCustomer', 0,
        array('Generic'=>0, 'Active'=>1));
    $smarty->assign('CustomerOptions', implode("\n\t", $cOptions));
    Template::page('', $smarty->fetch('Command/CourseCommandPre.html'));
	exit;
}
// on vérifie que le client est bien chargé
if (!($customer instanceof AeroCustomer)) {
    Template::errorDialog(E_CUSTOMER_NOT_FOUND, $retURL);
    exit;
}

if (isset($_SESSION['coursecommand'])) {
    $coursecommand = $_SESSION['coursecommand'];
} else {
    $coursecommand = new CourseCommand();
}

// on met le client en session pour 3 pages
$session->register('customer', $customer, 3);
// on met le client en session pour 3 pages
$session->register('coursecommand', $coursecommand, 3);

/**
 * Traitement de l'envoi du formulaire
 */
if (isset($_REQUEST['formSubmitted']) && !isset($_REQUEST['Cancel'])) {
    /**
     * On demarre une transaction
     */
	//Database::connection()->debug = true;
    Database::connection()->startTrans();
    // on rempli l'objet commande
    $coursecommand->setCommandDate($_REQUEST['Date']);
    $coursecommand->setCustomer($customer);
    $inst = isset($_REQUEST['Instructor'])?$_REQUEST['Instructor']:$_REQUEST['InstructorID'];
    $coursecommand->setInstructor($inst);
    $coursecommand->setFlyType($_REQUEST['FlyType']);
    $coursecommand->setSoloFly(!isset($_REQUEST['SoloFly'])||$_REQUEST['SoloFly']==0?false:true);
    $coursecommand->setComment($_REQUEST['Comment']);
    // dates
    $startdate = $_REQUEST['WishedStartDate'];
    $coursecommand->setWishedStartDate($startdate);
    // calcul de la durée
    $thour = isset($_REQUEST['Time_Hour'])?$_REQUEST['Time_Hour']:'00';
    $tmin  = isset($_REQUEST['Time_Minute'])?$_REQUEST['Time_Minute']:'00';
    $duration = $thour . ':' . $tmin;
    if ($duration == '00:00') {
        Template::errorDialog(_('Class duration cannot be null.'), $retURL);
        Exit;
    }
    $coursecommand->setDuration($duration);
    // calcul de la date de fin
    $enddate = DateTimeTools::MySQLDateAdd($startdate, $duration);
    $coursecommand->setWishedEndDate($enddate);

    // Validation
    $validator = new CourseCommandValidator($coursecommand);
    $result = $validator->validate();
    if ($result instanceof Exception && $result->getCode() == EXCEP_NO_CONCRETE_PRODUCT) {
        Template::errorDialog($result->getMessage(), 'CourseCommand.php');
        exit;
    }
    if ($result instanceof Exception
            && $result->getCode() == EXCEP_NO_CONCRETE_PRODUCT_AVAILABLE) {
        $msg = $result->getMessage();
        $ftype = $coursecommand->getFlyType();
        $wdate = $coursecommand->getWishedStartDate();
        $ccpCollection = $ftype->getAeroConcreteProductCollection();
        $count = $ccpCollection->getCount();
        for($i=0; $i<$count; $i++){
            $ccp = $ccpCollection->getItem($i);
            $wpl = $ccp->getWeeklyPlanning();
            $title = _('Airplane schedule') . ' ' . $ccp->getImmatriculation();
            $msg .= sprintf(
                '<p><img src="PlanningGantt.php?wplanning=%s&date=%s&title=%s" /></p>',
                $wpl->getId(), $wdate, urlencode($title));

        }
        Template::errorDialog($msg, $retURL);
        exit;
    }
    if (Tools::isException($result)) {
        Template::errorDialog($result->getMessage(), $retURL);
        exit;
    }

    $chn = $coursecommand->findChain();
    if (Tools::isException($chn)) {
        Template::errorDialog($chn->getMessage(), $retURL);
        exit;
    }
    // on attribue un numéro de commande
    $coursecommand->generateCommandNo($chn);
    // activation de la chaîne
    $ach = ActivateChain($chn, $coursecommand);
    if (Tools::isException($ach)) {
        Template::errorDialog($ach->getMessage(), $retURL);
        exit;
    }
    // quantification de la chaîne
    $quantificator = new CourseCommandQuantificator($ach, $coursecommand);
    $res = $quantificator->execute();
    if (Tools::isException($res)) {
        Template::errorDialog($res->getMessage(), $retURL);
        exit;
    }
    // planification de la chaine
    $scheduler = new Scheduler();
    $res = $scheduler->scheduleActivatedChain($ach, $startdate, $enddate);
    if (Tools::isException($res)) {
        Template::errorDialog($res->getMessage(), $retURL);
        exit;
    }
    // Mise a jour du potentiel virtuel du AeroConcreteProduct et
	// des AeroConcreteProduct le composant
    $ccp = $coursecommand->getAeroConcreteProduct();
	$ccp->updatePotentials(
	    array(
            array(
                'attributes' => array('VirtualHourSinceNew', 'VirtualHourSinceOverall'),
                'value' => DateTimeTools::getHundredthsOfHour($coursecommand->getDuration())
			)
        )
	);

    // sauvegarde de la chaine et de la commande
	require_once('ActivatedChainIterator.php');
	$iterator = new ActivatedChainIterator($ach);
	$iterator->execute();
    saveInstance($coursecommand, $retURL);

	// creation d'un commanditem 'fake'
	$commandItem = new CommandItem();
	$commandItem->setCommand($coursecommand);
	$commandItem->setActivatedChain($ach);
	$commandItem->setPriceHT($coursecommand->getTotalPriceHT());
    saveInstance($commandItem, $retURL);
    // toujours dans la série compatibilité, on renseigne l'exp/destinataire
    // même si ça n'a pas trop de sens amha...
    // expéditeur: acteur de début de la chaîne
    $siteTrans = $ach->getSiteTransition();
    if ($siteTrans instanceof ActorSiteTransition) {
        $coursecommand->setExpeditor($siteTrans->getDepartureActor());
        $coursecommand->setExpeditorSite($siteTrans->getDepartureSite());
    }
    // destinataire: client de la commande
    $coursecommand->setDestinator($customer);
    $coursecommand->setDestinatorSite($customer->getMainSite());
    $spc = findSupplierCustomer(
        $coursecommand->getExpeditor(),
        $coursecommand->getDestinator(),
        ($coursecommand->getTotalPriceTTC() > $coursecommand->getTotalPriceHT())
    );
    $coursecommand->setSupplierCustomer($spc);
    saveInstance($coursecommand, $retURL);
    // mise à jour des indisponibilités
    $ach->updateUnavailabilities($coursecommand);
    saveInstance($ach, $retURL);

    /**
     * On commite la transaction,
     * si la transaction a réussi, on redirige vers un message d'information
     * sinon vers un message d'erreur
     */
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_TRANS_FAILED, $retURL);
        Exit;
    }
    Database::connection()->completeTrans();

    // envoi du récépissé aux users paramétrés et au client de la commande
    $additionnalUsers = new Collection();
    $additionnalUsers->setItem($auth->getUser());
    AlertSender::send_ALERT_COURSE_COMMAND_RECEIPT($coursecommand, $additionnalUsers);

    // envoi des éventuelles alertes potentiel dépassé
    $ccp->sendPotentialOverAlert();

    // cleanage de la session
  	cleanSessionData();
    $msg = sprintf(I_SUCCESS, I18N::formatDate(
        $coursecommand->getWishedStartDate()));
    if (!$coursecommand->getSolofly() && !$coursecommand->getIsWishedInstructor()) {
        $inst = $coursecommand->getInstructor();
        // le message n'est pas le même suivant le choix de l'utilisateur, s'il
        // a sélectionné "indifferent" ou s'il a selectionné un instructeur qui
        // n'est pas disponible
        $inst_msg = isset($_REQUEST['Instructor'])&&$_REQUEST['Instructor']==0?
            I_INSTRUCTOR_DEFAULT:I_INSTRUCTOR_DIFFERENT;
        $msg .= '<br><br>' . sprintf($inst_msg, $inst->getName());
    }
    Template::infoDialog($msg, 'CourseCommand.php');
    exit;
}

$smarty->assign('customerID', $customer->getId());

$smarty->assign('CustomerName', $customer->getName());
$smarty->assign('CustomerSoloFly', $customer->getSoloFly());
$instructor = $customer->getInstructor();
if ($instructor instanceof AeroInstructor) {
    $smarty->assign('CustomerInstructor', $instructor->getName());
    $smarty->assign('InstructorSelectEnabled', 'disabled');
	$customerInstructorID = $instructor->getId();
    $smarty->assign('InstructorID', $customerInstructorID);
    $canChangeInstructor = false;
} else {
    $canChangeInstructor = true;
	$customerInstructorID = 0;
}

$spc = $customer->getSupplierCustomer();
if ($spc instanceof SupplierCustomer) {
    $smarty->assign('CustomerMaxIncur', $spc->getMaxIncur());
    $smarty->assign('CustomerCurrentIncur', $spc->getUpdateIncur());
}

// on récupère les flytypes de(s) licence(s) du client
$col = $customer->getFlyTypeCollection();
if (Tools::isEmptyObject($col)) {
    cleanSessionData();
	// pas $retURL, sinon, part en boucle si AERO_CUSTOMER
	$retURL = ($user->getProfile() == UserAccount::PROFILE_AERO_CUSTOMER)?'home.php':$retURL;
    Template::errorDialog(sprintf(E_NOFLYTYPE_FOUND, $customer->getName()),
        		   $retURL);
    Exit;
}
$col->sort('Name', SORT_ASC);
$flytypeOptions = FormTools::writeOptionsFromCollection($col,
    $coursecommand->getFlyTypeId());

$smarty->assign('FlyTypeOptions', implode("\n\t", $flytypeOptions));
$smarty->assign('WishedStartDate', $coursecommand->getWishedStartDate());
$smarty->assign('WishedStartDateDisplay',
    I18N::formatDate($coursecommand->getWishedStartDate()));
if ($coursecommand->getDuration()) {
    $smarty->assign('Duration', $coursecommand->getDuration());
}
$smarty->assign('SoloFly', $coursecommand->getSoloFly());
$smarty->assign('Comment', $coursecommand->getComment());

$instructorID = $coursecommand->getInstructorId();
$instructorID = $instructorID > 0?$instructorID:$customerInstructorID;
/**
 * On affiche la page
 **/
$pageContent = $smarty->fetch('Command/CourseCommand.html');
// variables GET pour le js généré par php
$requestParams  = 'canChangeInstructor=' . $canChangeInstructor;
$requestParams .= '&selected=' . $instructorID;
// includes js nécessaires
$js = array(
    'js/lib-functions/checkForm.js',
    'JS_CourseCommand.php?' . $requestParams,
	'js/jscalendar/calendar.js',
	getJSCalendarLangFile(),
	'js/jscalendar/calendar-setup.js'
    );

Template::page('', $pageContent, $js);
?>
