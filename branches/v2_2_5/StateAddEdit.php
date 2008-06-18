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
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));
//Database::connection()->debug = true;

SearchTools::prolongDataInSession();

$action = basename($_SERVER['PHP_SELF']) . '?';

if (isset($_REQUEST['ccId'])) {
	$CountryCity = Object::load('CountryCity', $_REQUEST['ccId']);
	if (Tools::isEmptyObject($CountryCity)) {
	    Template::errorDialog(E_MSG_TRY_AGAIN, 'CountryCityList.php');
   		exit;
	}
	$StateId = Tools::getValueFromMacro($CountryCity, '%CityName.Department.State.Id%');
	$State = Object::load('State', $StateId);
}


/*  Si on a clique sur OK apres saisie  */
if ((isset($_REQUEST['formSubmitted']) && $_REQUEST['formSubmitted'] == 1)) {
	// Modification ou Creation
	if (!isset($State)) {
	    $State = Object::load('State');
	}

	Database::connection()->startTrans();
	FormTools::autoHandlePostData($_POST, $State, 'State');
	
	$query = UrlTools::buildURLFromRequest(
            array('ccId', 'State_Country_ID', 'State_Name', 'State_Number'));

	// Verification que le nom n'est pas deja utilise
	// pour une autre region du meme pays
	if (!$State->isNameCorrect()) {
	    Template::errorDialog(_('A district already exists with the name and number provided, please correct.'),
                $action . $query);
    	exit;
	}

    saveInstance($State, $action . $query);

	/*  Commit de la transaction  */
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        die('erreur sql');
    }
    Database::connection()->completeTrans();
	Tools::redirectTo('CountryCityList.php');
	exit;
}


/*  Formulaire */
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
require_once ('HTML/QuickForm.php');
$query = (isset($_REQUEST['ccId']))?'ccId='.$_REQUEST['ccId']:'';
$form = new HTML_QuickForm('StateAddEdit', 'post', $action . $query);

$CountryArray = SearchTools::createArrayIDFromCollection('Country');
$form->addElement('select', 'State_Country_ID', _('Country'), $CountryArray,
        array('style="width:100%"'));
$form->addElement('text', 'State_Name', _('Name'), 'style="width:100%"');
$form->addElement('text', 'State_Number', _('Number'), 'style="width:100%"');

/*  Si Edition d'une region existant  */
if (isset($State)) {
    foreach($State->getProperties() as $name => $class) {
	    $getter = (is_string($class))?'get' . $name . 'Id':'get' . $name;
		$Id = (is_string($class))?'_ID':'';
	    $val = $State->$getter();
		$defaultValues['State_' . $name . $Id] = $State->$getter();
	}
}
else {
	$defaultValues = SearchTools::createDefaultValueArray();
}
$form->setDefaults($defaultValues);  // Le form avec les valeurs par defaut

/*  Validation du formulaire */
$form->addRule('State_Name', _('Please provide a name.'), 'required', '', 'client');
$form->setJsWarnings(_('Error: '), _('Please correct.'));

$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$pageContent = $smarty->fetch('CountryCity/StateAddEdit.html');

$action = (isset($_REQUEST['ccId']))?_('Update'):_('Create');

Template::page($action . _(' of a district'), $pageContent);

?>