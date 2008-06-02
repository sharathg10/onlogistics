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
require_once('Objects/Operation.const.php');
require_once('Objects/ActivatedChainTask.php');

$auth = Auth::Singleton();
$auth->checkProfiles();

define('FAKE_INDEX', '##');
define('TITLE_MONTH', _('Monthly schedule'));
define('TITLE_WEEK', _('Weekly schedule'));
define('TITLE_DAY', _('Daily schedule'));
define('DEFAULT_START_HOUR', 8);
define('DEFAULT_END_HOUR', 19);

define('BACKGROUND_DEFAULT', '#efeffa');
define('BACKGROUND_GREEN', '#39cb29');
define('BACKGROUND_ORANGE', '#ff9900');
define('BACKGROUND_RED', '#ff0000');

$profileId = $auth->getProfile();
$connectedActorId = $auth->getActorId();
$unavailabilityFilter = array();

// Gestion du context metier
$consultingContext = in_array('consulting',
    Preferences::get('TradeContext', array()));

SearchTools::ProlongDataInSession();
//  Recupere l'année le mois et le jour du planning
$year = (!empty($_GET['y']))?$_GET['y']:date('Y');
$month = (!empty($_GET['m']))?$_GET['m']:date('n');
$day = (!empty($_GET['d']))?$_GET['d']:date('d');

// Recupere le format de planning
$planningFormat = (!empty($_GET['f']))?$_GET['f']:'Weekly';

// titre et plage de dates du planning {{{
switch ($planningFormat){
    case 'Monthly':
    $pageTitle = TITLE_MONTH;
    $beginDateTS = mktime(0, 0, 0, $month, 1, $year);
    $endDateTS = mktime(0, 0, 0, $month+1, 1, $year);
    break;
    case 'Weekly':
    $pageTitle = TITLE_WEEK;
    $beginDateTS = mktime(0, 0, 0, $month, $day-7, $year);
    $endDateTS = mktime(0, 0, 0, $month, $day+7, $year);
    break;
    case 'Daily':
    $pageTitle = TITLE_DAY;
    $beginDateTS = mktime(0, 0, 0, $month, $day, $year);
    $endDateTS = mktime(0, 0, 0, $month, $day+1, $year);
    break;
}
$beginDate = DateTimeTools::timeStampToMySQLDate($beginDateTS);
$endDate = DateTimeTools::timeStampToMySQLDate($endDateTS);
// }}}

// Contruction du formulaire de recherche {{{
// Tous les criteres ont 'Disable' => true:
// Construction des filters resultant traitee de facon custom
$form = new SearchForm('Unavailability');

// On n'affiche pas les memes criteres de recherche si on vient de l'onglet
// 'Taches' oubien 'Vol'
if (SearchTools::requestOrSessionExist('from', 'tasks')) {
    // onglet Taches
    if (!$consultingContext) {
        $userAccountFilter = array('Profile' => UserAccount::PROFILE_OPERATOR);
        if ($profileId == UserAccount::PROFILE_OPERATOR) {
            $userAccountFilter['Id'] = $auth->getUserId();
        }
        $firstItem = ($profileId == UserAccount::PROFILE_OPERATOR)?
                '':_('Select an operator');
        $userAccountArray = SearchTools::CreateArrayIDFromCollection('UserAccount',
                $userAccountFilter, $firstItem);
        $disabled = ($profileId == UserAccount::PROFILE_OPERATOR)?'disabled':'';
        $form->addElement('select', 'Operator', _('Operator'),
                array($userAccountArray, $disabled), array('Disable' => true));
    }
    $form->addElement('text', 'Command', _('Order'),
            array(), array('Disable'=>true));
    if ($consultingContext) {
        $pmFilter = array('Generic' => 0);
        if ($profileId == UserAccount::PROFILE_GED_PROJECT_MANAGER) {
            $pmFilter['Id'] = $auth->getUser()->getActorId();
            $firstItem = '';
        } else {
            $firstItem = _('Select a project manager');
        }
        $pmArray = SearchTools::createArrayIDFromCollection(
            'ProjectManager', $pmFilter, $firstItem);
        $form->addElement('select', 'ProjectManager', _('Project manager'),
            array($pmArray),
            array('Disable' => true)
        );
        $customerArray = SearchTools::createArrayIDFromCollection(
            'Customer', array('Generic' => 0), _('Select a customer'));
        $form->addElement('select', 'Customer', _('Customer'),
            array($customerArray),
            array('Disable' => true)
        );
        $form->addElement('text', 'Signatory', _('Signatory'),
            array(),
            array('Disable' => true)
        );
    }
    $operationType = ($consultingContext)?
            Operation::OPERATION_TYPE_CONS:Operation::OPERATION_TYPE_PROD;
    $operationNameArray = SearchTools::createArrayIDFromCollection('Operation',
            array('Type' => $operationType),
            _('Select an operation'), 'Name');
    $form->addElement('select', 'Operation', _('Operation'),
            array($operationNameArray), array('Disable' => true));
    $form->buildHiddenField(array('from' => 'tasks'));
    $form->addElement('text', 'BaseReference', _('Product'),
            array(), array('Disable'=>true));
} else {
    // onglet vol
    //  Critère 1 : Liste déroulante des AeroCustomer
    $aeroCustomerFilter = array();
    if($profileId != UserAccount::PROFILE_AERO_CUSTOMER) {
        $aeroCustomerFilter = array('Active' => 1, 'Generic' => 0);

        if($profileId == UserAccount::PROFILE_AERO_INSTRUCTOR) {
            $aeroCustomerFilter['Instructor'] = $connectedActorId;
        }
        $style = 'multiple size="3"';
    }
    else {
        $aeroCustomerFilter = array('Id' => $connectedActorId);
        $style = 'disabled';
    }
    $aeroCustomerArray = SearchTools::CreateArrayIDFromCollection('AeroCustomer',
            $aeroCustomerFilter, _('Select a customer.'));
    $form->addElement('select', 'Customer2', _('Customer'),
            array($aeroCustomerArray, $style),
            array('Disable' => true)); // 'WeeklyPlanning.MainSite.Actor'


    //  Critère 2 : Liste déroulante des AeroInstructor
    $aeroInstructorFilter = array();
    if($profileId != UserAccount::PROFILE_AERO_INSTRUCTOR) {
        $aeroInstructorFilter = array('Active' => 1, 'Generic' => 0);
        $style = 'multiple size="3"';
    }
    else {
        $aeroInstructorFilter = array('Id' => $connectedActorId);
        $style = 'disabled';
    }
    $aeroInstructorArray = SearchTools::CreateArrayIDFromCollection('AeroInstructor',
            $aeroInstructorFilter, _('Select an instructor'));
    $form->addElement('select', 'Instructor', _('Instructor'),
            array($aeroInstructorArray, $style),
            array('Disable' => true)); // 'WeeklyPlanning.MainSite.Actor'


    //  Critère 3 : Liste déroulantes des immatriculations
    $cpFilter = array('Product.ClassName' => 'AeroProduct');

    if($profileId == UserAccount::PROFILE_AERO_CUSTOMER) {
        $cpFilter['Owner'] = $connectedActorId;
    }
    $immatriculationArray = SearchTools::CreateArrayIDFromCollection(
            'ConcreteProduct', $cpFilter, _('Select a matriculation'));
    $style = (count($immatriculationArray) > 1)?'multiple size="3"':'disabled';
    $form->addElement('select', 'ConcreteProduct', _('Matriculation'),
            array($immatriculationArray, $style),
            array('Disable' => true));
} // Fin construction form de recherche
// }}}


// Le formulaire de recherche a ete poste
// Construction des filtres custom
if (true === $form->displayGrid()) {
    // Met les criteres de recherche en session (habituellment fait
    // par $form->buildFilterComponentArray() )
    SearchTools::InputDataInSession(1, '', true);

    // construction des filtres et récupération des Unavailabilities {{{
    // Filtre par defaut sur les dates
    if((isset($_POST['Command']) && $_POST['Command']=='') || !empty($_GET['popup'])) {
        $filterDateCmp = array();
        $filterDateCmp[] = SearchTools::NewFilterComponent('', 'EndDate', 'GreaterThan',
            $beginDate, 1);
        $filterDateCmp[] = SearchTools::NewFilterComponent('', 'BeginDate', 'LowerThan',
            $endDate, 1);
        $unavailabilityFilter[] = SearchTools::FilterAssembler($filterDateCmp);
    }
    // Filtre sur Command.CommandNo, si critere utilise (onglet Taches)
    $unavailabilityFilter[] = SearchTools::NewFilterComponent('Command', 'Command.CommandNo', 'Like');
    $unavailabilityFilter[] = SearchTools::NewFilterComponent('ProjectManager', 'ActivatedChainOperation.Actor', 'Equals');
    $unavailabilityFilter[] = SearchTools::NewFilterComponent('Customer', 'Command.Destinator', 'Equals');
    $unavailabilityFilter[] = SearchTools::NewFilterComponent('Signatory', 'Command.Destinator.ActorDetail.Signatory', 'Like');
    $unavailabilityFilter[] = SearchTools::NewFilterComponent(
            'Operation', 'ActivatedChainOperation.Operation', 'Equals');
    $unavailabilityFilter[] = SearchTools::NewFilterComponent(
            'BaseReference', 'Command@ProductCommand.CommandItem().Product.BaseReference', 'Like', '', 0, 'Unavailability');
    $unavailabilityCol = array();  //

    $instructorIds = SearchTools::RequestOrSessionExist('Instructor');
    $cpIds = SearchTools::RequestOrSessionExist('ConcreteProduct');
    $customerIds = SearchTools::RequestOrSessionExist('Customer2');
    $operatorId = ($profileId==UserAccount::PROFILE_OPERATOR)?
        $auth->getUserId():SearchTools::RequestOrSessionExist('Operator');
    
    $weeklyPlanning = false;
    // Filtre sur les instructeurs si critere utilise
    if ($instructorIds !== false && !empty($instructorIds)) {
        $searchFilter = array();
        // S'il y a des clients ou immatriculations selectionnes
        if($customerIds !== false && !empty($customerIds)) {  // Filtre clients
            $searchFilter[] = SearchTools::NewFilterComponent(
                    'Customer', 'Command.Customer', 'In', $customerIds, 1);
        }
        if($cpIds !== false && !empty($cpIds)) {  // Filtre CP
            $searchFilter[] = SearchTools::NewFilterComponent(
                    'AeroCP', 'Command.AeroConcreteProduct', 'In', $cpIds, 1);
        }
        if (!empty($searchFilter)) {
            $unavailabilityFilter[] = SearchTools::FilterAssembler($searchFilter);
        }
        $filter = SearchTools::FilterAssembler($unavailabilityFilter);

        // Pour chaque instructeur
        if (is_array($instructorIds)) {
            foreach ($instructorIds as $InstructorId) {
                if($InstructorId == FAKE_INDEX) continue;
                // Recupere le weeklyPlanning de l'instructeur puis ses indisponibilites
                $actor = Object::load('Actor', $InstructorId);
                $weeklyPlanning = $actor->getWeeklyPlanning();
                $unavailabilityCol[] = $weeklyPlanning->getUnavailabilityCollection($filter);
            }
        }
    }
    // Filtre sur les ConcreteProduct si critere utilise
    elseif ($cpIds !== false && !empty($cpIds)) {
        // Si des clients sont selectionnes
        if($customerIds !== false && !empty($customerIds)) {
            $unavailabilityFilter[] = SearchTools::NewFilterComponent(
                    'Customer', 'Command.Customer', 'In', $customerIds, 1);
        }
        $filter = SearchTools::FilterAssembler($unavailabilityFilter);
        // Pour chaque cp
        foreach ($cpIds as $cpId) {
            if($cpId == FAKE_INDEX) continue;
            // Recupération du weeklyPlanning du cp puis de ses indisponibilites
            $concreteProduct = Object::load('ConcreteProduct', $cpId);
            $weeklyPlanning = $concreteProduct->getWeeklyPlanning();
            $unavailabilityCol[] = $weeklyPlanning->getUnavailabilityCollection($filter);
        }
    }
    // Filtre sur les Clients si critere utilise
    elseif ($customerIds !== false && !empty($customerIds)) {
        $filter = SearchTools::FilterAssembler($unavailabilityFilter); // filtre complet
        // Pour chaque customer
        foreach ($customerIds as $customerId) {
            if ($customerId == FAKE_INDEX)  continue;
            // Recupération du weeklyPlanning du client puis de ses indisponibilites
            $actor = Object::load('Actor', $customerId);
            $weeklyPlanning = $actor->getWeeklyPlanning();
            $unavailabilityCol[] = $weeklyPlanning->getUnavailabilityCollection($filter);
        }
    }
    // On est dans l'onglet Taches, et il y a des UserAccount selectionnes
    elseif ($operatorId !== false && $operatorId !== '##') {
        $filter = SearchTools::FilterAssembler($unavailabilityFilter); // filtre complet
        $userAccount = Object::load('UserAccount', $operatorId);
        $actor = $userAccount->getActor();
        $weeklyPlanning = $actor->getWeeklyPlanning();
        // Recupere la collection d'indisponibilites
        $unavailabilityCol[] = $weeklyPlanning->getUnavailabilityCollection($filter);
    }
    else {
        $filter = SearchTools::FilterAssembler($unavailabilityFilter);
        $unavailabilityMapper = Mapper::singleton('Unavailability');
        $unavailabilityCol[] = $unavailabilityMapper->loadCollection($filter);
    }
    // }}}

        Database::connection()->debug=false;
    // Merge les unavailabilityCollections obtenues {{{
    $count = count($unavailabilityCol);
    $uCollection = new Collection();
    $uCollection->acceptDuplicate = false;
    for ($i=0 ; $i<$count ; $i++) {
        $n = $unavailabilityCol[$i]->getCount();
        for($j=0 ; $j<$n ; $j++) {
            $uCollection->setItem($unavailabilityCol[$i]->getItem($j));
        }
    }
    // }}}

    // recherche de la date de la première Unavailability trouvée {{{
    $uCollection->sort('BeginDate');
    if(isset($_REQUEST['formSubmitted'])) {
        $firstEvent = $uCollection->getItem(0);
        if($firstEvent instanceof Unavailability) {
            $date = explode(' ', $firstEvent->getBeginDate());
            $dateToDisplay = explode('-', $date[0]);

            $year = $dateToDisplay[0];
            $month = $dateToDisplay[1];
            $day = $dateToDisplay[2];
        }
    }
    // }}}

    // Affichage du planning
    if(empty($_GET['popup'])) {
        $formContent = $form->render() . '</form>';

        //  Construction du planning
        $serSite = Mapper::singleton('Site');
        $pf = '&f='.$planningFormat;
        $events = array();
        $planningStartHour = $planningEndHour = 0;
        $dayDate = date('Y-m-d h:i:s');
        $taskDates = array();

        // Construit le tableau d'événements avec les Unavailability
        $count = $uCollection->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $unavailability = $uCollection->getItem($i);
            $purpose = $unavailability->getPurpose();
            $command = $unavailability->getCommand();

            $start = $unavailability->getBeginDate();
            $end = $unavailability->getEndDate();

            if($command instanceof Command) {
                $cmd = $command->getCommandNo();
                // on ajoute le nom de l'operation
                $cmd.= ' ' . Tools::getValueFromMacro($unavailability,
                    '%ActivatedChainOperation.Operation.Name%');
                // les indispo sont triées par date on stock pour chaque commande
                // la date de début de la première tâche
                if(!isset($taskDates[$command->getId()])) {
                    $taskDates[$command->getId()] = array(
                        'begin'=>I18N::formatDate($start, I18N::DATE_LONG),
                        'customerName'=>$command->getCustomer()->getName());
                }

                $ackState = Tools::getValueFromMacro($unavailability,
                    '%ActivatedChainOperation.ActivatedChainTask()[0].State%');
                if($ackState == ActivatedChainTask::STATE_FINISHED) {
                    continue;
                }
            }

            //$start = $unavailability->getBeginDate();
            //$end = $unavailability->getEndDate();
            $humanStart = I18N::formatDate($start, I18N::DATETIME_LONG);
            $humanEnd = I18N::formatDate($end, I18N::DATETIME_LONG);

            if(!empty($cmd)) {
                switch ($planningFormat) {
                    case 'Monthly':
                    if($command instanceof CourseCommand) {
                        $ccp = $command->getAeroConcreteProduct();
                        $cmd = _('Class');//get_class($command);
                    } elseif ($command instanceof ProductCommand) {
                        break;
                    } else {
                        $cmd = '';
                    }
                        break;
                    case 'Weekly':
                    if($command instanceof CourseCommand) {
                        $ccp = $command->getAeroConcreteProduct();
                        $cmd = _('Class - ') . $ccp->getImmatriculation();
                    } elseif ($command instanceof ProductCommand) {
                        $cmd .= '<br>' . _('Beginning: ') . $taskDates[$command->getId()]['begin'];
                        $cmd .= '<br>' . $taskDates[$command->getId()]['customerName'];
                    } else {
                        $cmd = '';
                    }
                        break;
                    case 'Daily':
                    if($command instanceof CourseCommand) {
                        $ccp = $command->getAeroConcreteProduct();
                        $actor = $command->getInstructor();
                        $instructor = _('No instructor') . ', ';
                        if($actor instanceof Actor) {
                            $instructor = _('instructor') . ': ' . $actor->getName(). ', ';
                        }
                        unset($actor);
                        $actor = $command->getCustomer();
                        $customer = '';
                        if($actor instanceof Actor) {
                            $customer = _('Student') . ': ' .$actor->getName(). ', ';
                        }
                        $cmd = _('Class, beginning') . ': ' . $command->getWishedStartDate('H:i') .
                            ', ' . _('duration') .  ': '  . $command->getDuration() . ', ' .
                            $instructor . $customer . _('airplane: ') .
                            $ccp->getImmatriculation();
                        unset($actor);
                    } elseif ($command instanceof ProductCommand) {
                        $cmd .= '<br>' . _('from') . ' : ' . $humanStart .
                            ' ' . _('to') . ' : ' . $humanEnd;
                    } else {
                        $cmd = '';
                    }
                    break;
                }
            } else {
                $cmd= '';
            }


            if(!empty($purpose)){
                switch ($planningFormat) {
                    case 'Monthly':
                        break;
                    case 'Weekly':
                        break;
                    case 'Daily':
                        $name = '';
                        if($weeklyPlanning instanceof WeeklyPlanning) {
                            $site = $serSite->load(
                                    array('Planning'=>$weeklyPlanning->getId()));
                            $actor = $site->getOwner();
                            $name = $actor->getName();
                        }
                        $purpose = '' . $name . '&nbsp;&nbsp;' . $purpose
                                . _(' from ') . $humanStart . _(' to ') . $humanEnd;
                        unset($actor);
                        break;
                }
                if($cmd != '') {
                    $purpose = '<br>'.$purpose;
                }
            }

            /*
            création d'une indisponibilité par jour pour tenir
            compte des horraires et we
            */

            $wp = $unavailability->getWeeklyPlanning();
            $unavailabilityDate = explode(' ', $start);
            $unavailabilityBeginHour = $unavailabilityDate[1];
            $unavailabilityBeginDate = $unavailabilityDate[0];
            $operationName = Tools::getValueFromMacro($unavailability,
                    '%ActivatedChainOperation.Operation.Name%');
            $operationName = ($operationName == 0)?'':' ' . $operationName;

            $unavailabilityDate = explode(' ', $end);
            $unavailabilityEndHour = $unavailabilityDate[1];
            $unavailabilityEndDate = $unavailabilityDate[0];

            $explodingDate = explode('-', $unavailabilityBeginDate);
            $time = mktime(0,0,0, $explodingDate[1], $explodingDate[2],$explodingDate[0]);
            $explodingDate = explode('-', $unavailabilityEndDate);
            $unavEndTime = mktime(0,0,0, $explodingDate[1], $explodingDate[2],$explodingDate[0]);

            while ($time <= $unavEndTime) {
                $date = date('Y-m-d h:i:s', $time);
                $explodingDate = explode(' ', $date);
                $currentHour = $explodingDate[1];
                $currentDate = $explodingDate[0];
                $explodingDate = explode('-', $currentDate);

                $dailyPlanning = $wp->getDailyPlanningForDate($date);

            	$startHour = $dailyPlanning->getStart();
            	$endHour = $dailyPlanning->getEnd();

            	if($startHour!='00:00:00' && $endHour!='00:00:00') {
            	    if($currentDate == $unavailabilityBeginDate) {
            	        $bd = $currentDate.' '.$unavailabilityBeginHour;
            	        if($currentDate == $unavailabilityEndDate) {
            	            $ed = $currentDate.' '.$unavailabilityEndHour;
            	        } else {
            	            $ed = $currentDate.' '.$endHour;
            	        }
            	    } elseif ($currentDate == $unavailabilityEndDate) {
            	        $bd = $currentDate.' '.$startHour;
            	        $ed = $currentDate.' '.$unavailabilityEndHour;
            	    } else {
            	        $bd = $currentDate.' '.$startHour;
            	        $ed = $currentDate.' '.$endHour;
            	    }

            	    // gestion de la couleur d'affichage de l'indisponibilité
            	    $background = BACKGROUND_DEFAULT;
            	    if($command instanceof Command) {
            	        $commandEndDate = explode(' ', $command->getWishedEndDate());
                        $commandStartDate = explode(' ', $command->getWishedStartDate());
            	        if($dayDate > $unavailabilityBeginDate) {
                	        // si la date courante est supérieur à la date de début
                	        // de l'indisponibilité, la commande apparait en rouge
                	        $background = BACKGROUND_RED;
                	    } elseif($ackState == ActivatedChainTask::STATE_IN_PROGRESS) {
                	        // si l'ack de l'aco est à l'état en cours, la commande
                	        // apparait en orange
                	        $background = BACKGROUND_ORANGE;
                        } elseif((($commandEndDate[0] != '0000-00-00')
                            && ($commandStartDate[0] <= $currentDate && $currentDate <= $commandEndDate[0]))
                            || ($currentDate == $commandStartDate[0])) {
                	        // si la date courante est comprise dans le
                	        // créneau de date de la commande ou si date
                	        // souhaité de la commande égale date de
                    	    // l'indisponibilité, la commande apparait en vert
                    	    $background = BACKGROUND_GREEN;
                	    }
            	    }

            	    $events[] = array(
                        'id'    => $unavailability->getId(),
                        'cmd'   => $unavailability->getCommandId(),
                        'start' => $bd,
                        'end'   => $ed,
                        'desc'  => $cmd.$purpose . $operationName, //# de command + type d'indisp
                        'background' => $background,
                        'onclick' => sprintf("window.open('UnavailabilityPlanning.php?popup=1&y=%d&m=%d&d=%d&format=daily', 'title', 'width=800,height=600,toolbars=no,scrollbars=yes,menubars=no,status=no');", $explodingDate[0], $explodingDate[1], $explodingDate[2])
                    );

                    $startHour = explode(':', $startHour);
                    $endHour = explode(':', $endHour);

                    if($planningFormat=='Daily') {
                        $date = $currentDate . ' 00:00:00';
                        if($date==$beginDate) {
                            $planningStartHour = $startHour[0];
            	            $planningEndHour = $endHour[0];
                        }
                    } else {
                        if($planningStartHour==0 && $planningEndHour==0) {
                	        $planningStartHour = $startHour[0];
                	        $planningEndHour = $endHour[0];
                	    } else {
                	        if($startHour[0] < $planningStartHour) {
                	            $planningStartHour = $startHour[0];
                	        }
                	        if($endHour[0] > $planningEndHour) {
                	            $planningEndHour = $endHour[0];
                	        }
                	    }
                    }

                    // si necessaire on augmente l'heure de fin
                    if($endHour[1]!='00') {
                        $planningEndHour +=1;
                    }

            	}
            	$time = mktime(0,0,0, $explodingDate[1],$explodingDate[2]+1, $explodingDate[0]);
            }
        }

        // Instancie le planning
        $lang = I18N::getLocaleCode();
        $planningStartHour = intval($planningStartHour);
        $planningEndHour = intval($planningEndHour);
        if($planningStartHour==0) {
            $planningStartHour = DEFAULT_START_HOUR;
        }
        if($planningEndHour==0) {
            $planningEndHour = DEFAULT_END_HOUR;
        }

        $planning = new Timetable(array(
            'format' => $planningFormat,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'lang' => $lang,
            'firsthour' => $planningStartHour,
            'lasthour' => $planningEndHour,
            'firstday' => 1
        ), array(
            'clickondayurl'=>'UnavailabilityPlanning.php?f=Daily&y=%d&m=%d&d=%d',
            'hoursinevents' => true
        ));

        // gestion de la couleurs des événements et ajout au planning
        // recherche de plusieurs evenements en vert avec le meme num de cmd,
        // dans ce cas seule l'unavalability d'id la plus elevé reste en vert.
        $backgroundedEvent = array();
        $backgroundedCmd = array();
        foreach ($events as $key=>$event) {
            if($event['background']==BACKGROUND_GREEN) {
                if(!in_array($event['cmd'], $backgroundedCmd)) {
                    $backgroundedCmd[] = $event['cmd'];
                    $backgroundedEvent[$event['cmd']] = $key;
                } else {
                    if($event['id'] > $backgroundedEvent[$event['cmd']]['id']) {
                        $events[$backgroundedEvent[$event['cmd']]]['background'] = BACKGROUND_DEFAULT;
                        $backgroundedEvent[$event['cmd']] = $key;
                    } else {
                        $event['background'] = BACKGROUND_DEFAULT;
                    }

                }
            }
            $planning->addEvent($event);
        }

        //permet d'imprimer
        $planning->addAction('Print',
            array(
                'caption' => A_PRINT,
                'image' => 'images/imprimer.gif'
            )
        );
        //permet de passer au mois, jour, semaine suivant
        $planning->addAction('Previous',
            array(
                'caption' => _('previous')
            )
        );
        //permet de passer au mois, jour, semaine précédent
        $planning->addAction('Next',
            array(
                'caption' => _('next')
            )
        );
        //permet de passer au planning mensuel
        $planning->addAction('Month',
            array(
                'caption' => _('Month'),
                'image' => 'images/month_on.gif'
            )
        );
        //permet de passer au planning hebdomadaire
        $planning->addAction('Week',
            array(
                'caption' => _('Week'),
                'image' => 'images/week_on.gif'
            )
        );
        //permet de passer au planning journalier
        $planning->addAction('Day',
            array(
                'caption' => _('Day'),
                'image' => 'images/day_on.gif'
            )
        );
        
        $planningContent = $planning->render();
        Template::page($pageTitle, $formContent . $planningContent);
    }


    else {    // Affiche un grid des indisponibilites dans un popup
        $grid = new Grid();
        $grid->itemPerPage = 100;
        $grid->withNoCheckBox = true;
        $grid->withNoSortableColumn = true;
        // Surligne en vert les lignes liees aux commandes dont
        // WishedStartDate =  date courante
        $grid->highlightCondition =
            array('Macro' => '%Command.WishedStartDate|formatdate@Y-m-d%',
                  'Operator' => '=',
                  'Value' => date('Y-m-d',mktime(0,0,0, $month, $day, $year)));

        $firstUnav = $uCollection->getItem(0);
        $cmd = $firstUnav->getCommand();

        //colonnes du grid
        $grid->NewColumn('UnavailabilitySiteOwnerName', _('Actor'),
                array('Sortable' => false));
        $grid->newColumn('FieldMapper', _('Beginning'), array('Macro'=>'%BeginDate|formatdate%'));
        $grid->newColumn('FieldMapper', _('End'), array('Macro'=>'%EndDate|formatdate%'));
        $grid->newColumn('FieldMapper', _('Type'), array('Macro'=>'%Purpose%'));
        $grid->newColumn('FieldMapper', _('Order'),
                array('Macro' => '%Command.commandNo%'));
        if($cmd instanceof CourseCommand) {
            $grid->newColumn('FieldMapper', _('Instructor'),
                    array('Macro' => '%Command.Instructor.Name%'));
            $grid->newColumn('FieldMapper', _('Customer'),
                    array('Macro' => '%Command.customer.Name%'));
            $grid->newColumn('FieldMapper', _('Airplane'),
                    array('Macro' => '%Command.AeroConcreteProduct.Immatriculation%'));
        } elseif ($cmd instanceof ProductCommand) {
            $grid->newColumn('FieldMapper', _('Customer'),
                    array('Macro' => '%Command.Destinator.Name%'));
            $grid->newColumn('FieldMapper', _('Operation'),
                array('Macro' => '%ActivatedChainOperation.Operation.Name%'));
            $grid->newColumn('ACHProductRefAndQty', array('Ref.', 'Qty.'));
        }
        //actions du grid
        $grid->NewAction('Close');
        $grid->NewAction('Print');

        $result = $grid->render($uCollection, true, array(),
                array('BeginDate' => SORT_ASC));
        $title = _('List of unavailabilities') . _(' for ') .
                date('d/m/Y', mktime(0,0,0, $month, $day, $year));
        // Affiche le grid dans un popup
        Template::page(
            _('List of unavailabilities'),
            $title . $result,
            array(),
            array(),
            BASE_POPUP_TEMPLATE
        );
    }
}
else {
    // on n'affiche que le formulaire de recherche
    Template::page($pageTitle, $form->render() . '</form>');
}
?>
