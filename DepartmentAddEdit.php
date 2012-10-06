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

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_DIR_COMMERCIAL,UserAccount::PROFILE_COMMERCIAL));

SearchTools::prolongDataInSession();

$action = basename($_SERVER['PHP_SELF']);

if (isset($_REQUEST['ccId']) && $_REQUEST['ccId'] > 0) {
	$CountryCity = Object::load('CountryCity', $_REQUEST['ccId']);
	if (Tools::isEmptyObject($CountryCity)) {
	    Template::errorDialog(E_MSG_TRY_AGAIN, 'CountryCityList.php');
   		exit;
	}
	$DepartmentId = Tools::getValueFromMacro($CountryCity, '%CityName.Department.Id%');
	$Department = Object::load('Department', $DepartmentId);
	// Une ville n'est pas forcement liee a un Department
	if (Tools::isEmptyObject($Department)) {
	    $Department = Object::load('Department');
	}
}


/*  Si on a clique sur OK apres saisie  */
if ((isset($_REQUEST['formSubmitted']) && $_REQUEST['formSubmitted'] == 1)) {
	// Modification ou Creation
	if (!isset($Department)) {
	    $Department = Object::load('Department');
	}

	Database::connection()->startTrans();
	FormTools::autoHandlePostData($_POST, $Department, 'Department');

	// Verification que le nom n'est pas deja utilise
	// pour une autre region du meme pays
	if (!$Department->isNameCorrect()) {
		$query = UrlTools::buildURLFromRequest(
                array('ccId', 'Department_Country_ID', 'Department_State_ID',
					  'Department_Name', 'Department_Number'));
	    Template::errorDialog(
            _('A department already have the name and number provided, please correct.'),
            $action . '?' . $query);
    	exit;
	}

    saveInstance($Department, $action);

	/*  Commit de la transaction  */
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        die(E_ERROR_SQL);
    }
    Database::connection()->completeTrans();
	Tools::redirectTo('CountryCityList.php?' . SID);
	exit;
}


/*  Formulaire */
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('DepartmentAddEdit', 'post', $action);

$CountryArray = SearchTools::createArrayIDFromCollection('Country');
$form->addElement('select', 'Department_Country_ID', _('Country'),
        $CountryArray,
        'onchange="fw.ajax.updateSelect(\'country\', \'state\', \'State\', \'Country\');" '
            . 'style="width:100%" id="country"');
$form->addElement('text', 'Department_Name', _('Name'), 'style="width:100%"');
$form->addElement('text', 'Department_Number', _('Number'), 'style="width:100%"');
$form->addElement('hidden', 'ccId', isset($_REQUEST['ccId'])?$_REQUEST['ccId']:0, 'id="ccId"');
$form->addElement('hidden', 'stateId',
        isset($Department)?$Department->getStateId():0, 'id="stateId"');

/*  Si Edition d'un Department existant  */
if (isset($Department)) {
    foreach($Department->getProperties() as $name => $class) {
	    $getter = (is_string($class))?'get' . $name . 'Id':'get' . $name;
		$Id  = is_string($class)?'_ID':'';
	    $val = $Department->$getter();
		$defaultValues['Department_' . $name . $Id] = $Department->$getter();
	}
}
else {
	$defaultValues = SearchTools::createDefaultValueArray();
}
$form->setDefaults($defaultValues);  // affiche le form avec les valeurs par defaut

/*  Validation du formulaire */
$form->addRule('Department_Name',
        _('Please provide a name.'), 'required', '', 'client');
$form->setJsWarnings(_('Error: '),_('Please correct.'));

$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$pageContent = $smarty->fetch('CountryCity/DepartmentAddEdit.html');

$action = (isset($_REQUEST['ccId']))?
        _('Update department'):_('Add a department');

Template::page($action, $pageContent,
        array('JS_AjaxTools.php', 'js/includes/DepartmentAddEdit.js'));
exit;

?>
