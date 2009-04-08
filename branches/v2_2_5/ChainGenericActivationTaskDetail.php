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
 * @version   SVN: $Id: ChainActivationTaskDetail.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once('config.inc.php');
require_once('Objects/ChainTask.php');
require_once('SQLRequest.php');
require_once('ChainActivationTaskDetailTools.php');
require_once('Objects/ActivatedChain.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

// Pour le remote scripting: Si je selectionne un Actor fixe,
// ca restreint les Sites
SajaxTools::activeSajax(array('getActorSites', 'getChainType', 'getComponentOptions'));
SearchTools::prolongDataInSession();

$Smarty = new Template();
$session = Session::Singleton();

//$isActivationTask = isset($_REQUEST['activationTask']);

require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($Smarty);

require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('TaskDetails', 'post', $_SERVER['PHP_SELF']);

$ActorArrayId = SearchTools::createArrayIDFromCollection('Actor', array('Generic'=>0));
// Les boutons radio sont geres dans le template
$form->addElement('select', 'ChainDepartureActor', '', $ActorArrayId,
    'onchange="displayActorSites(\'Departure\')" '
    . 'id="ChainDepartureActor"');

$form->addElement('select', 'ChainArrivalActor', '', $ActorArrayId,
    'onchange="displayActorSites(\'Arrival\')" '
    . 'id="ChainArrivalActor"');

$form->addElement('text', 'Delta', 'X=');                   
// Sert a ne pas sauver en base: ActorSiteTransition, DepartureInstant et ArrivalInstant

$radio = array(
    HTML_QuickForm::createElement('radio', null, null, _('yes'), 1),
    HTML_QuickForm::createElement('radio', null, null, _('no'), 0));
$form->addGroup($radio, 'ComponentQuantityRatio',
    _('Take into account volume of nomenclature'), '&nbsp;');
$form->addElement('text', 'TaskDurationHour', '', 'size=3');
$form->addElement('text', 'TaskDurationMinute', '', 'size=3');
$form->addElement('select', 'DurationType', _('Calculation mode'),
    ChainTask::getDurationTypeConstArray());

$rsgArray = SearchTools::createArrayIDFromCollection(
        'RessourceGroup', array(), _('Select an item'));
$form->addElement('select', 'RessourceGroup', _('Resource model'),
        $rsgArray, 'id="RessourceGroup" style="width: 100%"');
$form->accept($renderer); // affecte au form le renderer personnalise
$Smarty->assign('form', $renderer->toArray());

$Smarty->assign('sajaxJS', sajax_get_javascript());

$Smarty->assign('WISHED_START_DATE_TYPE_BEGIN_TASK_PLUS_X', ChainTask::WISHED_START_DATE_TYPE_BEGIN_TASK_PLUS_X);
$Smarty->assign('WISHED_START_DATE_TYPE_BEGIN_TASK_MINUS_X', ChainTask::WISHED_START_DATE_TYPE_BEGIN_TASK_MINUS_X);
$Smarty->assign('WISHED_START_DATE_TYPE_COMMAND_PLUS_X', ChainTask::WISHED_START_DATE_TYPE_COMMAND_PLUS_X);
$Smarty->assign('WISHED_START_DATE_TYPE_COMMAND_MINUS_X', ChainTask::WISHED_START_DATE_TYPE_COMMAND_MINUS_X);

// Les valeurs par defaut des champs du form sont gerees via JS

Template::ajaxPage(
    _('Task details'),
    $Smarty->fetch('Chain/ChainGenericActivationTaskDetail.html'),
    array(
        'js/dynapi/src/dynapi.js',
		'JS_GLAOConstants.php',
		'js/lib/TCollection.js',
		'js/lib-functions/ComboBox.js',
        'js/includes/ChainGenericActivationTaskDetail.js'
    ),
    array(),
    BASE_POPUP_TEMPLATE
);

?>
