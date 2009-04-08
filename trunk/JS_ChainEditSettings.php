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
require_once('Objects/Task.inc.php');
require_once('Objects/Chain.php');
require_once('Objects/Chain.inc.php');
require_once('Objects/ChainTask.php');

function TaskDurationTypeToStr($durationType)
{
    switch ($durationType) {
        case ChainTask::DURATIONTYPE_FORFAIT: return _('fixed price');
        case ChainTask::DURATIONTYPE_KG: return _('Kg');
        case ChainTask::DURATIONTYPE_METER: return _('by cube meter');
        case ChainTask::DURATIONTYPE_LM: return _('by linear meter');
        case ChainTask::DURATIONTYPE_QUANTITY: return _('by unit');
        case ChainTask::DURATIONTYPE_KM: return _('by kilometer');
        default: return 'N/A';
    } // switch
}

function TaskCostTypeToStr($costType)
{
    switch ($costType) {
        case ChainTask::COSTTYPE_FORFAIT: return _('fixed price');
        case ChainTask::COSTTYPE_HOURLY: return _('By hour');
        case ChainTask::COSTTYPE_KG: return _('Kg');
        case ChainTask::COSTTYPE_CUBEMETTER: return _('by cube meter');
        case ChainTask::COSTTYPE_LM: return _('by linear meter');
        case ChainTask::COSTTYPE_QUANTITY: return _('by unit');
        case ChainTask::COSTTYPE_KM: return _('by kilometer');
        default: return 'N/A';
    } // switch
}

function WriteLoadSettingsFunction($Chain)
{
    echo("function LoadSettings() {\n");
    echo "  var anOperation;\n";
    echo "  var aTask;\n";
    $typeArray = array(
        TASK_ACTIVATION,
        TASK_GENERIC_ACTIVATION,
        TASK_INTERNAL_STOCK_ENTRY,
        TASK_INTERNAL_STOCK_EXIT
    );
    if ($Chain != false && $Chain->getId() > 0) {
        $ChainOperationCollection = $Chain->GetChainOperationCollection();
        $ChainOperationCollection->Sort('Order');
        $count = $ChainOperationCollection->GetCount();
        for($i = 0; ($ChainOperationCollection != false) && ($i < $count); $i++) {
            $ChainOperation = $ChainOperationCollection->GetItem($i);
            echo "\n";
            echo "  // Operation " . ($i + 1) . "\n";
            if ($i != 0) {
                echo("  anOperation = OperationBlockList.addOperation(" . ($i - 1) . ");\n");
            } else {
                // La première opération est ajoutée automatiquement, utilisons la directement.
                echo "  anOperation = OperationBlockList.getItem(0);\n";
            }

            echo "  anOperation.setActorId(" . $ChainOperation->GetActorId() . ");\n";
            // echo "  anOperation.setOperationId(" . $ChainOperation->GetOrder() . ");\n";
            echo "  anOperation.setOperationId(" . $ChainOperation->GetOperationId() . ");\n";
            echo "\n";

            $TaskCollection = $ChainOperation->GetChainTaskCollection();

            if (false != $TaskCollection) {
                $TaskCollection->Sort('Order');
                $jcount = $TaskCollection->GetCount();
                for($j = 0; $j < $jcount; $j++) {
                    $Task = $TaskCollection->GetItem($j);
                    echo "  // Task " . ($i + 1) . "." . ($j + 1) . "\n";

                    if ($j == 0) {
                        // La première tâche est ajoutée automatiquement, utilisons la directement.
                        echo("  aTask = anOperation.getItem(" . $j . ");\n");
                    } else {
                        echo("  aTask = anOperation.addTask(" . ($j - 1) . ");\n");
                    }
                    // Tache Pivot
                    // echo("  aTask.setId(" . $Task->GetOrder() . ");\n");
                    echo("  aTask.setId(" . $Task->GetTaskId() . ");\n");
                    echo("  aTask.setDuration(" . $Task->GetDuration() . ");\n");
                    echo("  aTask.setDurationType(" . $Task->GetDurationType() . ");\n");
                    echo("  aTask.setDurationTypeLabel(" .
                        JsTools::JSQuoteString(TaskDurationTypeToStr($Task->GetDurationType())) . ");\n");
                    echo("  aTask.setKilometerNumber(" . $Task->GetKilometerNumber() . ");\n");
                    echo("  aTask.setCost(" . $Task->GetCost() . ");\n");
                    echo("  aTask.setCostType(" . $Task->GetCostType() . ");\n");
                    echo("  aTask.setRessourceGroup(" . $Task->GetRessourceGroupId() . ");\n");
                    echo("  aTask.setCostTypeLabel(" .
                        JsTools::JSQuoteString(TaskCostTypeToStr($Task->GetCostType())) . ");\n");
                    $ActorSiteTransition = $Task->GetActorSiteTransition();
                    if (false != $ActorSiteTransition) {
                        echo("  aTask.setDepartureZone(" .
                            $ActorSiteTransition->GetDepartureZoneID() . ");\n");
                        echo("  aTask.setDepartureActor(" .
                            $ActorSiteTransition->GetDepartureActorID() . ");\n");
                        echo("  aTask.setDepartureSite(" .
                            $ActorSiteTransition->GetDepartureSiteID() . ");\n");
                        echo("  aTask.setArrivalZone(" .
                            $ActorSiteTransition->GetArrivalZoneID() . ");\n");
                        echo("  aTask.setArrivalActor(" .
                            $ActorSiteTransition->GetArrivalActorID() . ");\n");
                        echo("  aTask.setArrivalSite(" .
                            $ActorSiteTransition->GetArrivalSiteID() . ");\n");
                    }
                    $AbstractInstantMapper = Mapper::singleton('AbstractInstant');

                    $DepartureInstant = $AbstractInstantMapper->load(array('Id' => $Task->GetDepartureInstantId()));
                    if (!Tools::isEmptyObject($DepartureInstant) && !Tools::isException($DepartureInstant)) {
                        $DepartureAbstractInstantType = get_class($DepartureInstant);
                        if ($DepartureAbstractInstantType == 'Instant') {
                            echo("  aTask.setAbstractDepartureType(" . INSTANT . ");\n");
                            echo("  aTask.setDepartureInstantDate('" . $DepartureInstant->GetDate() . "');\n");
                        } else if ($DepartureAbstractInstantType == "WeeklyInstant") {
                            echo("  aTask.setAbstractDepartureType(" . WEEKLY_INSTANT . ");\n");
                            $DepartureInstantMapper = Mapper::singleton('WeeklyInstant');

                            $DepartureInstant = $DepartureInstantMapper->load(array('Id' => $Task->GetDepartureInstantId()));
                            echo("  aTask.setDepartureWeeklyInstantDay(" . $DepartureInstant->GetDay() . ");\n");
                            echo("  aTask.setDepartureWeeklyInstantTime('" . $DepartureInstant->GetTime() . "');\n");
                        } else if ($DepartureAbstractInstantType == "DailyInstant") {
                            echo("  aTask.setAbstractDepartureType(" . DAILY_INSTANT . ");\n");
                            $DepartureInstantMapper = Mapper::singleton('DailyInstant');
                            $DepartureInstant = $DepartureInstantMapper->load(array('Id' => $Task->GetDepartureInstantId()));
                            echo("  aTask.setDepartureWeeklyInstantTime('" . $DepartureInstant->GetTime() . "');\n");
                        }
                    }

                    $ArrivalAbstractInstant = $AbstractInstantMapper->load(array('Id' => $Task->GetArrivalInstantId()));
                    if (!Tools::isEmptyObject($ArrivalAbstractInstant) && !Tools::isException($ArrivalAbstractInstant)) {
                        $ArrivalAbstractInstantType = get_class($ArrivalAbstractInstant);
                        if ($ArrivalAbstractInstantType == 'Instant') {
                            echo("  aTask.setAbstractArrivalType(" . INSTANT . ");\n");
                            $ArrivalInstantMapper = Mapper::singleton('Instant');
                            $ArrivalInstant = $ArrivalInstantMapper->load(array('Id' => $Task->GetArrivalInstantId()));
                            echo("  aTask.setArrivalInstantDate('" . $ArrivalInstant->GetDate() . "');\n");
                        } else if ($ArrivalAbstractInstantType == "WeeklyInstant") {
                            echo("  aTask.setAbstractArrivalType(" . WEEKLY_INSTANT . ");\n");
                            $ArrivalInstantMapper = Mapper::singleton('WeeklyInstant');
                            $ArrivalInstant = $ArrivalInstantMapper->load(array('Id' => $Task->GetArrivalInstantId()));
                            echo("  aTask.setArrivalWeeklyInstantDay(" . $ArrivalInstant->GetDay() . ");\n");
                            echo("  aTask.setArrivalWeeklyInstantTime('" . $ArrivalInstant->GetTime() . "');\n");
                        } else if ($ArrivalAbstractInstantType == "DailyInstant") {
                            echo("  aTask.setAbstractArrivalType(" . DAILY_INSTANT . ");\n");
                            $ArrivalInstantMapper = Mapper::singleton('DailyInstant');
                            $ArrivalInstant = $ArrivalInstantMapper->load(array('Id' => $Task->GetArrivalInstantId()));
                            echo("  aTask.setArrivalWeeklyInstantTime('" . $ArrivalInstant->GetTime() . "');\n");
                        }
                    }
                    // ---------------------
                    echo("  aTask.setInteruptibleTask(" . $Task->GetInteruptible() . ");\n");
                    echo("  aTask.setTriggerMode(" . $Task->GetTriggerMode() . ");\n");
                    echo("  aTask.setTriggerDelta(" . $Task->GetTriggerDelta() . ");\n");
                    echo("  aTask.setInstructions(" . JsTools::JSQuoteString(urlencode($Task->GetInstructions())) . ");\n");

                    /**
                     * Récupération des utilisateurs qui doivent être alertés
                     */
                    $AlertedUsersCollection = $Task->GetUserAccountCollection();
                    $Padding = '';
                    $ReturnedString = "";
                    if (!Tools::isEmptyObject($AlertedUsersCollection)) {
                        for($j = 0; $j < $AlertedUsersCollection->getCount(); $j++) {
                            unset($AlertedUsersItem);
                            $AlertedUsersItem = $AlertedUsersCollection->getItem($j);
                            $ReturnedString .= $Padding . $AlertedUsersItem->GetId();
                            $Padding = '|';
                        } // for
                        echo("  aTask.setAlertedUsers(" . JsTools::JSQuoteString($ReturnedString) . ");\n");
                        // récupération de la checkbox
                        echo("  aTask.setAutoAlert(" . JsTools::JSQuoteString($Task->GetAutoAlert()) . ");\n");
                    }

					// Si tache d'Activation

					echo("  aTask.setChainToActivate(" . $Task->getChainToActivateId() . ");\n");
                    if (in_array($Task->getTaskId(), $typeArray)) {
                        $chToActivate = $Task->getChainToActivate();
                        $chToActivateType = $chToActivate instanceof Chain?$chToActivate->getType():-1;
                        $chToActivateType = $chToActivateType==0;
    					echo("  aTask.setHasProductCommandType(" . $chToActivateType . ");\n");
    					echo("  aTask.setProductCommandType(" . $Task->getProductCommandType() . ");\n");

                        echo("  aTask.setChainDepartureActor(" . $Task->getDepartureActorId() . ");\n");
                        echo("  aTask.setChainDepartureSite(" . $Task->getDepartureSiteId() . ");\n");
                        echo("  aTask.setChainArrivalActor(" . $Task->getArrivalActorId() . ");\n");
                        echo("  aTask.setChainArrivalSite(" . $Task->getArrivalSiteId() . ");\n");
    					echo("  aTask.setWishedDateType(" . $Task->getWishedDateType() . ");\n");
    					echo("  aTask.setDelta(" . $Task->getDelta() . ");\n");
                        // nomenclature
                        $components = $Task->getComponentCollection();
                        $kcount = $components->getCount();
                        $nomenclature = 0;
                        for($k = 0; $k < $kcount; $k++){
                        	$cpn = $components->getItem($k);
                            echo("  aTask._componentArray[$k] = " . $cpn->getId() . ";\n");
                            if ($nomenclature == 0) {
                                $nomenclature = $cpn->getNomenclatureId();
                            }
                        } // for
                        echo("  aTask.setNomenclature(" . $nomenclature . ");\n");
                        // ratio
                        $ratio = $Task->getComponentQuantityRatio();
                        echo("  aTask.setComponentQuantityRatio(" . $ratio . ");\n");
                        //
                        $aps = $Task->getActivationPerSupplier();
                        echo("  aTask.setActivationPerSupplier(" . $aps . ");\n");
                    } else {
                        $cpn = $Task->getComponent();
                        if ($cpn instanceof Component) {
                            echo("  aTask.setComponent(" . $cpn->getId() . ");\n");
                            echo("  aTask.setNomenclature(" . $cpn->getNomenclatureId() . ");\n");
                        }
                    }
                } // /boucle sur les Task
            }
            echo "\n";
            echo "  anOperation.expand();\n";
        }
        echo "\n";
        $PivotTask = $Chain->GetPivotTask();


        if (!Tools::isEmptyObject($PivotTask) && false != $PivotTask->GetOperation()) {
            $PivotOperation = $PivotTask->GetOperation();
            echo("  OperationBlockList.setPivotObject(OperationBlockList.getItem(" . $PivotOperation->getOrder() . ").getItem(" . $PivotTask->GetOrder() . ") );\n");
        } else {
            // echo "alert('no Pivot');";
        }
        echo "  OperationBlockList.setPivotDate(" . $Chain->GetPivotDateType() . ");\n";
    }
    echo "}";
}

if (isset($_REQUEST['ChnId'])) {
    $ChainMapper = Mapper::singleton('Chain');
    $Chain = $ChainMapper->load(array('Id' => $_REQUEST['ChnId']));
    WriteLoadSettingsFunction($Chain);
} elseif (isset($_REQUEST['nomId'])) {
    $nom = Object::load('Nomenclature', $_REQUEST['nomId']);
    $Chain = $nom->buildChain();
    WriteLoadSettingsFunction($Chain);
}

?>
