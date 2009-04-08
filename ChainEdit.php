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
require_once('Objects/Chain.php');
require_once('Objects/ChainTask.php');
require_once('Objects/Chain.inc.php');
require_once('Objects/Task.const.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
//Database::connection()->debug = true;
SearchTools::prolongDataInSession();

$chnID = isset($_REQUEST['chnId'])?$_REQUEST['chnId']:0;
$onErrorUrl = 'ChainEdit.php?chnId='.$chnID;
$sid = defined('SID')?SID:'';

if($chnID > 0) {
    $ChainMapper = Mapper::singleton('Chain');
    $Chain = $ChainMapper->load(array('id' => (int)$chnID));
} elseif(isset($_REQUEST['nomId'])) {
    $Chain = false;
    $nom = Object::load('Nomenclature', $_REQUEST['nomId']);
    if($nom instanceof Nomenclature) {
        $Chain = $nom->buildChain();
    }
} else {
    $Chain = false;
}

if (false == $Chain) {
    die(_('Chain cannot be created.'));
}

if (isset($_REQUEST['FormSubmitted'])) {
    Database::connection()->startTrans();

    require_once('ChainTools.php');
    deleteChainTaskAndOperation($Chain);
    $Tasks = array();
    $Operations = array();
    $Actors = array();
    $Instructions = array();
    $DurationType = array();
    $KilometerNumber = array();
    $CostType = array();
    $RessourceGroup = array();
    $Cost = array();
    $InteruptibleTask = array();
    $TriggerMode = array();
    $TriggerDelta = array();

    $DepartureActor = array();
    $DepartureSite = array();
    $AbstractDepartureType = array();
    $DepartureWeeklyInstantDay = array();
    $DepartureWeeklyInstantTime = array();
    $DepartureInstantDate = array();

    $ArrivalActor = array();
    $ArrivalSite = array();
    $AbstractArrivalType = array();
    $ArrivalWeeklyInstantDay = array();
    $ArrivalWeeklyInstantTime = array();
    $ArrivalInstantDate = array();

    $AutoAlert = array();
    $AlertedUsers = array();

	$ChainDepartureActor = array();
    $ChainDepartureSite = array();
	$ChainArrivalActor = array();
    $ChainArrivalSite = array();
	$ChainToActivate = array();
	$HasProductCommandType = array();
	$ProductCommandType = array();
	$WishedDateType = array();
	$Delta = array();
	$Components = array();
	$Component = array();
	$ComponentQuantityRatio = array();
	$ActivationPerSupplier = array();

    $ChainId = $_REQUEST['chnId'];

    foreach($_REQUEST as $key => $value) {
        if (preg_match('/^Task([0-9]+)_([0-9]+)$/', $key, $tokens)) {
            if (!isset($Tasks[$tokens[1]])) {
                $Tasks[$tokens[1]] = array();
            }
            $Tasks[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation Operations
         */(preg_match('/^Operation([0-9]+)$/', $key, $tokens)) {
            $Operations[$tokens[1]] = $value;
        } elseif
        /**
         * récuperation Acteur de l'opération
         */(preg_match('/^Operation([0-9]+)Actor$/', $key, $tokens)) {
             $Actors[$tokens[1]] = $value;
             if(!$value) {
                 Template::errorDialog(_('All operations must be assigned to an actor'),
                        $onErrorUrl);
                 exit;
             }
        } elseif
        /**
         * récupération des taches
         */(preg_match('/^Task([0-9]+)_([0-9]+)Instructions$/', $key, $tokens)) {
            if (!isset($Instructions[$tokens[1]])) {
                $Instructions[$tokens[1]] = array();
            }
            $Instructions[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation DurationType
         */(preg_match('/^Task([0-9]+)_([0-9]+)DurationType$/', $key, $tokens)) {
            if (!isset($DurationType[$tokens[1]])) {
                $DurationType[$tokens[1]] = array();
            }
            $DurationType[$tokens[1]][$tokens[2]] = $value;
        } elseif

        /**
         * récuperation KilometerNumber
         */(preg_match('/^Task([0-9]+)_([0-9]+)KilometerNumber$/', $key, $tokens)) {
            if (!isset($KilometerNumber[$tokens[1]])) {
                $KilometerNumber[$tokens[1]] = array();
            }
            $KilometerNumber[$tokens[1]][$tokens[2]] = $value;
        } elseif

        /**
         * récuperation Task duration
         */(preg_match('/^Task([0-9]+)_([0-9]+)Duration$/', $key, $tokens)) {
             if(!$value) {
                 Template::errorDialog(_('All tasks must have a duration'), $onErrorUrl);
                 exit;
            }
            if (!isset($Duration[$tokens[1]])) {
                $Duration[$tokens[1]] = array();
            }
            $Duration[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récupération des type de cout des tasks
         */(preg_match('/^Task([0-9]+)_([0-9]+)CostType$/', $key, $tokens)) {
            if (!isset($CostType[$tokens[1]])) {
                $CostType[$tokens[1]] = array();
            }
            $CostType[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récupération du modèle de ressource
         */(preg_match('/^Task([0-9]+)_([0-9]+)RessourceGroup$/', $key, $tokens)) {
            if (!isset($RessourceGroup[$tokens[1]])) {
                $RessourceGroup[$tokens[1]] = array();
            }
            $RessourceGroup[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récupération du  cout des tasks
         */(preg_match('/^Task([0-9]+)_([0-9]+)Cost$/', $key, $tokens)) {
            if (!isset($Cost[$tokens[1]])) {
                $Cost[$tokens[1]] = array();
            }
            $Cost[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation Interuptibletask
         */(preg_match('/^Task([0-9]+)_([0-9]+)InteruptibleTask$/', $key, $tokens)) {
            if (!isset($InteruptibleTask[$tokens[1]])) {
                $InteruptibleTask[$tokens[1]] = array();
            }
            $InteruptibleTask[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation TriggerMode
         */(preg_match('/^Task([0-9]+)_([0-9]+)TriggerMode$/', $key, $tokens)) {
            if (!isset($TriggerMode[$tokens[1]])) {
                $TriggerMode[$tokens[1]] = array();
            }
            $TriggerMode[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation TriggerDelta
         */(preg_match('/^Task([0-9]+)_([0-9]+)TriggerDelta$/', $key, $tokens)) {
            if (!isset($TriggerDelta[$tokens[1]])) {
                $TriggerDelta[$tokens[1]] = array();
            }
            $TriggerDelta[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation DepartureActor
         */(preg_match('/^Task([0-9]+)_([0-9]+)DepartureActor$/', $key, $tokens)) {
            if (!isset($DepartureActor[$tokens[1]])) {
                $DepartureActor[$tokens[1]] = array();
            }
            $DepartureActor[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation DepartureZone
         */(preg_match('/^Task([0-9]+)_([0-9]+)DepartureZone$/', $key, $tokens)) {
            if (!isset($DepartureActor[$tokens[1]])) {
                $DepartureZone[$tokens[1]] = array();
            }
            $DepartureZone[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation DepartureSite
         */(preg_match('/^Task([0-9]+)_([0-9]+)DepartureSite$/', $key, $tokens)) {
            if (!isset($DepartureSite[$tokens[1]])) {
                $DepartureSite[$tokens[1]] = array();
            }
            $DepartureSite[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation ArrivalActor
         */(preg_match('/^Task([0-9]+)_([0-9]+)ArrivalActor$/', $key, $tokens)) {
            if (!isset($ArrivalActor[$tokens[1]])) {
                $ArrivalActor[$tokens[1]] = array();
            }
            $ArrivalActor[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation ArrivalZone
         */(preg_match('/^Task([0-9]+)_([0-9]+)ArrivalZone$/', $key, $tokens)) {
            if (!isset($ArrivalZone[$tokens[1]])) {
                $ArrivalZone[$tokens[1]] = array();
            }
            $ArrivalZone[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation ArrivalSite
         */(preg_match('/^Task([0-9]+)_([0-9]+)ArrivalSite$/', $key, $tokens)) {
            if (!isset($ArrivalSite[$tokens[1]])) {
                $ArrivalSite[$tokens[1]] = array();
            }
            $ArrivalSite[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation AbstractDepartureType
         */(preg_match('/^Task([0-9]+)_([0-9]+)AbstractDepartureType$/', $key, $tokens)) {
            if (!isset($AbstractDepartureType[$tokens[1]])) {
                $AbstractDepartureType[$tokens[1]] = array();
            }
            $AbstractDepartureType[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation DepartureWeeklyInstantDay
         */(preg_match('/^Task([0-9]+)_([0-9]+)DepartureWeeklyInstantDay$/', $key, $tokens)) {
            if (!isset($DepartureWeeklyInstantDay[$tokens[1]])) {
                $DepartureWeeklyInstantDay[$tokens[1]] = array();
            }
            $DepartureWeeklyInstantDay[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation DepartureWeeklyInstantTime
         */(preg_match('/^Task([0-9]+)_([0-9]+)DepartureWeeklyInstantTime$/', $key, $tokens)) {
            if (!isset($DepartureWeeklyInstantTime[$tokens[1]])) {
                $DepartureWeeklyInstantTime[$tokens[1]] = array();
            }
            $DepartureWeeklyInstantTime[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation DepartureInstantDate
         */(preg_match('/^Task([0-9]+)_([0-9]+)DepartureInstantDate$/', $key, $tokens)) {
            if (!isset($DepartureInstantDate[$tokens[1]])) {
                $DepartureInstantDate[$tokens[1]] = array();
            }
            $DepartureInstantDate[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation AbstractArrivalType
         */(preg_match('/^Task([0-9]+)_([0-9]+)AbstractArrivalType$/', $key, $tokens)) {
            if (!isset($AbstractArrivalType[$tokens[1]])) {
                $AbstractArrivalType[$tokens[1]] = array();
            }
            $AbstractArrivalType[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation ArrivalWeeklyInstantDay
         */(preg_match('/^Task([0-9]+)_([0-9]+)ArrivalWeeklyInstantDay$/', $key, $tokens)) {
            if (!isset($ArrivalWeeklyInstantDay[$tokens[1]])) {
                $ArrivalWeeklyInstantDay[$tokens[1]] = array();
            }
            $ArrivalWeeklyInstantDay[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation ArrivalWeeklyInstantTime
         */(preg_match('/^Task([0-9]+)_([0-9]+)ArrivalWeeklyInstantTime$/', $key, $tokens)) {
            if (!isset($ArrivalWeeklyInstantTime[$tokens[1]])) {
                $ArrivalWeeklyInstantTime[$tokens[1]] = array();
            }
            $ArrivalWeeklyInstantTime[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation ArrivalInstantDate
         */(preg_match('/^Task([0-9]+)_([0-9]+)ArrivalInstantDate$/', $key, $tokens)) {
            if (!isset($ArrivalInstantDate[$tokens[1]])) {
                $ArrivalInstantDate[$tokens[1]] = array();
            }
            $ArrivalInstantDate[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation AutoAlert
         */(preg_match('/^Task([0-9]+)_([0-9]+)AutoAlert$/', $key, $tokens)) {
            if (!isset($AutoAlert[$tokens[1]])) {
                $AutoAlert[$tokens[1]] = array();
            }
            $AutoAlert[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation AlertedUsers
         */
        (preg_match('/^Task([0-9]+)_([0-9]+)AlertedUsers$/', $key, $tokens)) {
            if (!isset($AlertedUsers[$tokens[1]])) {
                $AlertedUsers[$tokens[1]] = array();
            }
            $AlertedUsers[$tokens[1]][$tokens[2]] = explode("|", $value);
        }
	// Pour les Task d'activation
		elseif
        /**
         * récuperation ChainToActivate
         */(preg_match('/^Task([0-9]+)_([0-9]+)ChainToActivate$/', $key, $tokens)) {
            if (!isset($ChainToActivate[$tokens[1]])) {
                $ChainToActivate[$tokens[1]] = array();
            }
            $ChainToActivate[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation ChainToActivate
         */(preg_match('/^Task([0-9]+)_([0-9]+)HasProductCommandType$/', $key, $tokens)) {
            if (!isset($HasProductCommandType[$tokens[1]])) {
                $HasProductCommandType[$tokens[1]] = array();
            }
            $HasProductCommandType[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation ChainToActivate
         */(preg_match('/^Task([0-9]+)_([0-9]+)ProductCommandType$/', $key, $tokens)) {
            if (!isset($ProductCommandType[$tokens[1]])) {
                $ProductCommandType[$tokens[1]] = array();
            }
            $ProductCommandType[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation DepartureActor
         */(preg_match('/^Task([0-9]+)_([0-9]+)ChainDepartureActor$/', $key, $tokens)) {
            if (!isset($ChainDepartureActor[$tokens[1]])) {
                $ChainDepartureActor[$tokens[1]] = array();
            }
            $ChainDepartureActor[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation DepartureSite
         */(preg_match('/^Task([0-9]+)_([0-9]+)ChainDepartureSite$/', $key, $tokens)) {
            if (!isset($ChainDepartureSite[$tokens[1]])) {
                $ChainDepartureSite[$tokens[1]] = array();
            }
            $ChainDepartureSite[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation ArrivalActor
         */(preg_match('/^Task([0-9]+)_([0-9]+)ChainArrivalActor$/', $key, $tokens)) {
            if (!isset($ChainArrivalActor[$tokens[1]])) {
                $ChainArrivalActor[$tokens[1]] = array();
            }
            $ChainArrivalActor[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation ArrivalSite
         */(preg_match('/^Task([0-9]+)_([0-9]+)ChainArrivalSite$/', $key, $tokens)) {
            if (!isset($ChainArrivalSite[$tokens[1]])) {
                $ChainArrivalSite[$tokens[1]] = array();
            }
            $ChainArrivalSite[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation WishedDateType
         */(preg_match('/^Task([0-9]+)_([0-9]+)WishedDateType$/', $key, $tokens)) {
            if (!isset($WishedDateType[$tokens[1]])) {
                $WishedDateType[$tokens[1]] = array();
            }
            $WishedDateType[$tokens[1]][$tokens[2]] = $value;
        } elseif
        /**
         * récuperation Delta
         */(preg_match('/^Task([0-9]+)_([0-9]+)Delta$/', $key, $tokens)) {
            if (!isset($Delta[$tokens[1]])) {
                $Delta[$tokens[1]] = array();
            }
            $Delta[$tokens[1]][$tokens[2]] = $value;
        }
		elseif (preg_match('/^Task([0-9]+)_([0-9]+)Components$/', $key, $tokens)) {
            if (!isset($Components[$tokens[1]])) {
                $Components[$tokens[1]] = array();
            }
            $Components[$tokens[1]][$tokens[2]] = explode('|', $value);
        }
		elseif (preg_match('/^Task([0-9]+)_([0-9]+)Component$/', $key, $tokens)) {
            if (!isset($Component[$tokens[1]])) {
                $Component[$tokens[1]] = array();
            }
            $Component[$tokens[1]][$tokens[2]] = $value;
        }
		elseif (preg_match('/^Task([0-9]+)_([0-9]+)ComponentQuantityRatio$/', $key, $tokens)) {
            if (!isset($ComponentQuantityRatio[$tokens[1]])) {
                $ComponentQuantityRatio[$tokens[1]] = array();
            }
            $ComponentQuantityRatio[$tokens[1]][$tokens[2]] = $value;
        }
		elseif (preg_match('/^Task([0-9]+)_([0-9]+)ActivationPerSupplier/', $key, $tokens)) {
            if (!isset($ActivationPerSupplier[$tokens[1]])) {
                $ActivationPerSupplier[$tokens[1]] = array();
            }
            $ActivationPerSupplier[$tokens[1]][$tokens[2]] = $value;
        }
    }

    /**
     * Traitement d'Operation :
     * recupération des informations opértaions du tableau operation
     * pour enregistrement
     */
    require_once('Objects/ChainOperation.php');
    require_once('Objects/ActorSiteTransition.php');
    require_once('Objects/AbstractInstant.php');
    require_once('Objects/Instant.php');
    require_once('Objects/WeeklyInstant.php');
    require_once('Objects/DailyInstant.php');
    // $nboperation = 0;

    $PivotTask = false;
    foreach ($Operations as $key => $value) {
        unset($soperation);
        $soperation = new ChainOperation();
        $soperation->setChain($_REQUEST['chnId']);
        $soperation->setOperation($value);
        $soperation->setActor($Actors[$key]);
        $soperation->setOrder($key);
        saveInstance($soperation, $onErrorUrl);
        $listchainop[$key] = $soperation->getId();
        /**
         * Traitement des taches :
         * recupération des informations taches du tableau tache
         * pour enregistrement
         */
        foreach ($Tasks[$key] as $taskkey => $taskvalue) {
            $typeArray = array(
                TASK_ACTIVATION,
                TASK_GENERIC_ACTIVATION,
                TASK_INTERNAL_STOCK_ENTRY,
                TASK_INTERNAL_STOCK_EXIT
            );
			// Si ce n'est pas une tache d'activation
			if (!in_array($Tasks[$key][$taskkey], $typeArray)) {
			    unset($ActorSiteTransition);
	            $ActorSiteTransition = new ActorSiteTransition();

	            $ActorSiteTransition->setDepartureZone($DepartureZone[$key][$taskkey]);
	            $ActorSiteTransition->setDepartureActor($DepartureActor[$key][$taskkey]);
	            $ActorSiteTransition->setDepartureSite($DepartureSite[$key][$taskkey]);
	            $ActorSiteTransition->setArrivalZone($ArrivalZone[$key][$taskkey]);
	            $ActorSiteTransition->setArrivalActor($ArrivalActor[$key][$taskkey]);
	            $ActorSiteTransition->setArrivalSite($ArrivalSite[$key][$taskkey]);

                saveInstance($ActorSiteTransition, $onErrorUrl);

	            $DepartureInstant = false;
	            if ($AbstractDepartureType[$key][$taskkey] == INSTANT) {
	                $DepartureInstant = new Instant();
	                $DepartureInstant->setDate($DepartureInstantDate[$key][$taskkey]);
	            } else if ($AbstractDepartureType[$key][$taskkey] == WEEKLY_INSTANT) {
	                $DepartureInstant = new WeeklyInstant();
	                $DepartureInstant->setDay($DepartureWeeklyInstantDay[$key][$taskkey]);
	                $DepartureInstant->setTime($DepartureWeeklyInstantTime[$key][$taskkey]);
	            } else if ($AbstractDepartureType[$key][$taskkey] == DAILY_INSTANT) {
	                $DepartureInstant = new DailyInstant();
	                $DepartureInstant->setTime($DepartureWeeklyInstantTime[$key][$taskkey]);
	            }
	            if (false != $DepartureInstant) {
                    saveInstance($DepartureInstant, $onErrorUrl);
	                $TDepartureInstant = $DepartureInstant->getId();
	            }

	            unset($ArrivalInstant);
	            $ArrivalInstant = false;
	            if ($AbstractArrivalType[$key][$taskkey] == INSTANT) {
	                $ArrivalInstant = new Instant();
	                $ArrivalInstant->setDate($ArrivalInstantDate[$key][$taskkey]);
	            } else if ($AbstractArrivalType[$key][$taskkey] == WEEKLY_INSTANT) {
	                $ArrivalInstant = new WeeklyInstant();
	                $ArrivalInstant->setDay($ArrivalWeeklyInstantDay[$key][$taskkey]);
	                $ArrivalInstant->setTime($ArrivalWeeklyInstantTime[$key][$taskkey]);
	            } else if ($AbstractArrivalType[$key][$taskkey] == DAILY_INSTANT) {
	                $ArrivalInstant = new DailyInstant();
	                $ArrivalInstant->setTime($ArrivalWeeklyInstantTime[$key][$taskkey]);
	            }
	            if (false != $ArrivalInstant) {
                    saveInstance($ArrivalInstant, $onErrorUrl);
	                $TArrivalInstant = $ArrivalInstant->getId();
	            }
			}


            unset($stask);
            $stask = new ChainTask();

            $stask->setDuration($Duration[$key][$taskkey]);
            $stask->setDurationType($DurationType[$key][$taskkey]);
            $stask->setOrder($taskkey);
            $stask->setTask($Tasks[$key][$taskkey]);
			$stask->setCostType($CostType[$key][$taskkey]);
			$stask->setOperation($listchainop[$key]);

            // echo '<pre>TriggerDelta = '.$TriggerDelta[$key][$taskkey].'</pre>';

			$stask->setRessourceGroup($RessourceGroup[$key][$taskkey]);
			// Si ce n'est pas une tache d'activation ou de stock interne
			if (!in_array($Tasks[$key][$taskkey], $typeArray)) {
				$stask->setCost($Cost[$key][$taskkey]);
            	$stask->setCostType($CostType[$key][$taskkey]);
	            $stask->setKilometerNumber($KilometerNumber[$key][$taskkey]);
	            $stask->setInteruptible($InteruptibleTask[$key][$taskkey]);
				$stask->setTriggerMode($TriggerMode[$key][$taskkey]);
            	$stask->setTriggerDelta($TriggerDelta[$key][$taskkey]);
            	$stask->setInstructions(urldecode($Instructions[$key][$taskkey]));
	            $stask->setActorSiteTransition($ActorSiteTransition);
	            if (false != $DepartureInstant) {
	                $stask->setDepartureInstant($TDepartureInstant);
	            }
	            if (false != $ArrivalInstant) {
	                $stask->setArrivalInstant($TArrivalInstant);
	            }
            	$stask->setAutoAlert($AutoAlert[$key][$taskkey]);
				if ($stask->getAutoAlert() != 0) {
	                $UserAccountMapper = Mapper::singleton('UserAccount');
	                $UserAccountCollection = $UserAccountMapper->loadCollection(
                            array('Id' => $AlertedUsers[$key][$taskkey]));
	                $stask->setUserAccountCollection($UserAccountCollection);
	            }
            	$stask->setComponent($Component[$key][$taskkey]);
			}
			// Si c'est une tache d'activation
			else {
                // ActorSiteTransition: ici l'acteur de départ/arrivée est
                // l'acteur de l'opération
                $actor = $soperation->getActor();
                if ($actor instanceof Actor) {
                    $msite = $actor->getMainSite();
                } else {
                    $actor = 0;
                    $msite = 0;
                }
                $ActorSiteTransition = new ActorSiteTransition();
	            $ActorSiteTransition->setDepartureActor($actor);
	            $ActorSiteTransition->setDepartureSite($msite);
	            $ActorSiteTransition->setArrivalActor($actor);
	            $ActorSiteTransition->setArrivalSite($msite);
                saveInstance($ActorSiteTransition, $onErrorUrl);
                $stask->setActorSiteTransition($ActorSiteTransition);

                $stask->setCostType(ChainTask::COSTTYPE_FORFAIT);
				$stask->setInteruptible(1);
				$stask->setTriggerMode(ChainTask::TRIGGERMODE_MANUAL);
				$stask->setChainToActivate($ChainToActivate[$key][$taskkey]);
				$stask->setProductCommandType($ProductCommandType[$key][$taskkey]);
				$stask->setDepartureActor($ChainDepartureActor[$key][$taskkey]);
				$stask->setDepartureSite($ChainDepartureSite[$key][$taskkey]);
				$stask->setArrivalActor($ChainArrivalActor[$key][$taskkey]);
				$stask->setArrivalSite($ChainArrivalSite[$key][$taskkey]);
				$stask->setWishedDateType($WishedDateType[$key][$taskkey]);
				$stask->setDelta($Delta[$key][$taskkey]);
                if (is_array($Components[$key][$taskkey])) {
                    $cpnMapper = Mapper::singleton('Component');
                    $col = new Collection();
                    foreach($Components[$key][$taskkey] as $id){
                        $cpn = $cpnMapper->load(array('Id'=>$id));
                        if ($cpn instanceof Component) {
                            $col->setItem($cpn);
                        }
                    }
                }
                $stask->setComponentCollection($col);
                $stask->setComponentQuantityRatio($ComponentQuantityRatio[$key][$taskkey]);
                $stask->setActivationPerSupplier($ActivationPerSupplier[$key][$taskkey]);
			}
            saveInstance($stask, $onErrorUrl);
            if ($key == $_REQUEST['PivotOp'] && $taskkey == $_REQUEST['PivotTask']) {
                $PivotTask = $stask->getId();
            }
        } // fin foreach $Tasks[$key]
    } // fin foreach $Operations

    // checks
    if(!$PivotTask) {
        Template::errorDialog(_('You must put a deadline on one of the tasks.'),
                $onErrorUrl);
        exit;
    }

    // Modifie la date pivot dans la chaine
    $Chain->setPivotTask($PivotTask);
    $Chain->setPivotDateType($_REQUEST['PivotDateType']);
    $Chain->setState(Chain::CHAIN_STATE_BUILT);
    saveInstance($Chain, $onErrorUrl);

    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(_('Chain cannot be saved.'), 'ChainEdit.php?chnId=' . $_REQUEST['chnId']);
        exit;
    }
    Database::connection()->completeTrans();

    Template::infoDialog(
        _('Chain, operations and tasks were successfully saved.'),
        'dispatcher.php?entity=Chain'
    );
    exit;
}

$Smarty = new Template();
$Smarty->assign('Reference', $Chain->getReference());
$Smarty->assign('Description', $Chain->getDescription());
//$Smarty->assign('chnId', $chnID);

require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once ('HTML/QuickForm.php');

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($Smarty);
$form = new HTML_QuickForm('ChainBuild', 'post', $_SERVER['PHP_SELF'], '', 'id="ChainBuild" ');

$form->addElement('hidden', 'chnId', $chnID);
$form->addElement('hidden', 'PivotOp', '');
$form->addElement('hidden', 'PivotTask', '');
$form->addElement('hidden', 'PivotDateType', '');

$form->accept($renderer); // affecte au form le renderer personnalise
$Smarty->assign('form', $renderer->toArray());
$query = $chnID > 0 ? 'ChnId='.$chnID : 'nomId='.$_REQUEST['nomId'];
Template::page(
    _('Chain building'),
    $Smarty->fetch('Chain/ChainEdit.html'),
    array(
        'js/dynapi/src/dynapi.js',
	    'JS_GLAOConstants.php', // objet js permettant de déterminer le type de tache
	    'js/lib-functions/TimeTools.js',
	    'js/lib/TCollection.js', // gestion d'une collection d'objet js
	    'js/lib-functions/ComboBox.js',
	    'js/lib-functions/CheckAlertedActor.js',
	    'js/lib/TChainActorList.js', // pour garder la liste des acteurs de la chaine
	    'js/lib-functions/OperationTaskPopulateTools.js',
	    'JS_OperationTask.php',
	    'js/lib-functions/ActorSitePopulateTools.js',
	    'JS_ActorSiteList.php?withoutSite=1&dumpUsers=1&' . $sid,
	    'js/lib-functions/ActorCityNamePopulateTools.js',
	    'JS_ActorCityNameList.php',
	    'js/lib/TChainOperation.js', // Collection des operations de la chaine
	    'js/lib/TChainTask.js',  // pour paramètres d'une tâche de la chaine'
	    'js/lib/TBlockList.js', // BlockList d'une opération'
	    'js/lib/TOperationBlockList.js', // pour paramètres d'une opération
	    'JS_ChainEditSettings.php?' . $query, // charge les paramètres de la chaine
	    'js/includes/ChainEdit.js' // gère l'affichage de la chaine et les actions
	)
);
?>
