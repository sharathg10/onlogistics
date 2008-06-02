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
require_once('DateWidget.php');
require_once('Objects/ChainTask.php');
require_once('Objects/ActivatedChain.php');
require_once('Objects/ActivatedChainTask.php');
require_once('ChainActivationTaskDetailTools.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

$Smarty = new Template();
$session = Session::Singleton();
SearchTools::prolongDataInSession();

$TriggerDeltaWidget = HourWidgetRender(3, '00');
$FixedDateDeparture = DateWidgetRender(1);
$FixedDateArrival = DateWidgetRender(2);
$FixedHourDeparture = HourWidgetRender(1);
$FixedHourArrival = HourWidgetRender(2);

require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once('HTML/QuickForm.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($Smarty);
$form = new HTML_QuickForm('TaskDetails', 'post', $_SERVER['PHP_SELF']);

$taskId = isset($_REQUEST['taskID'])?$_REQUEST['taskID']:'';
$NomArrayId = getNomenclatureArray($taskId);
$form->addElement('select', 'Nomenclature', _('Nomenclature'), $NomArrayId,
        'onChange="fw.ajax.updateSelect(\'Nomenclature\', \'Component\', \'Component\','
        . ' \'Nomenclature\');" id="Nomenclature" style="width: 100%"');
$select = $form->addElement('select', 'Component', _('Linked component'), array(),
        'id="Component" style="width: 100%"');
$select->addOption('Aucun', 0);

$rsgArray = SearchTools::createArrayIDFromCollection(
        'RessourceGroup', array(), _('Select an item'));
$form->addElement('select', 'RessourceGroup', _('Resource model'),
        $rsgArray, 'id="RessourceGroup" style="width: 100%"');
$form->accept($renderer); // affecte au form le renderer personnalise
$Smarty->assign('form', $renderer->toArray());

$Smarty->assign('TriggerDeltaWidget', $TriggerDeltaWidget);
$Smarty->assign('DepartureFixedDate', $FixedDateDeparture);
$Smarty->assign('ArrivalFixedDate', $FixedDateArrival);
$Smarty->assign('DepartureFixedTime', $FixedHourDeparture);
$Smarty->assign('ArrivalFixedTime', $FixedHourArrival);

$Smarty->assign('PIVOTTASK_BEGIN', ActivatedChain::PIVOTTASK_BEGIN);
$Smarty->assign('PIVOTTASK_END', ActivatedChain::PIVOTTASK_END);

$Smarty->assign('DURATIONTYPE_FORFAIT', ChainTask::DURATIONTYPE_FORFAIT);
$Smarty->assign('DURATIONTYPE_KG', ChainTask::DURATIONTYPE_KG);
$Smarty->assign('DURATIONTYPE_METER', ChainTask::DURATIONTYPE_METER);
$Smarty->assign('DURATIONTYPE_LM', ChainTask::DURATIONTYPE_LM);
$Smarty->assign('DURATIONTYPE_QUANTITY', ChainTask::DURATIONTYPE_QUANTITY);
$Smarty->assign('DURATIONTYPE_KM', ChainTask::DURATIONTYPE_KM);

$Smarty->assign('COSTTYPE_FORFAIT', ChainTask::COSTTYPE_FORFAIT);
$Smarty->assign('COSTTYPE_HOURLY', ChainTask::COSTTYPE_HOURLY);
$Smarty->assign('COSTTYPE_KG', ChainTask::COSTTYPE_KG);
$Smarty->assign('COSTTYPE_CUBEMETTER', ChainTask::COSTTYPE_CUBEMETTER);
$Smarty->assign('COSTTYPE_LM', ChainTask::COSTTYPE_LM);
$Smarty->assign('COSTTYPE_QUANTITY', ChainTask::COSTTYPE_QUANTITY);
$Smarty->assign('COSTTYPE_KM', ChainTask::COSTTYPE_KM);

$Smarty->assign('TRIGGERMODE_MANUAL', ChainTask::TRIGGERMODE_MANUAL);
$Smarty->assign('TRIGGERMODE_AUTO', ChainTask::TRIGGERMODE_AUTO);
$Smarty->assign('TRIGGERMODE_TEMP', ChainTask::TRIGGERMODE_TEMP);

$zoneOptions = FormTools::writeOptionsFromObject('Zone', 0,
        array(), array('Name' => SORT_ASC));
$Smarty->assign('zoneOptions', join("\n\t\t", $zoneOptions));


$Smarty->assign('DisplayAlertSendParameters',1);


Template::page(
    _('Task details'),
    $Smarty->fetch('Chain/TaskDetails.html'),
    array(
        'js/dynapi/src/dynapi.js',
        'JS_AjaxTools.php',
		'JS_GLAOConstants.php',
        'js/lib/TCollection.js',
        'js/lib/TZone.js',
        'js/lib/TLocation.js',
        /*'js/includes/Zone_Location.js',
        'JS_Zone_Location.php?' . SID,*/
        'js/lib-functions/ComboBox.js',
        'js/includes/WidgetDateTools.js',
        'js/lib-functions/ActorSitePopulateTools.js',
        'JS_ActorSiteList.php?dumpUsers=1&' . SID,
        'js/includes/ChainTaskDetails.js',/*
        'JS_ZoneActorSite.php?' . SID,
        'js/includes/ChainTaskDetailsRS.js'*/
    ),
    array(),
    BASE_POPUP_TEMPLATE
);

?>
