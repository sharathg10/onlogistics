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
}


/*  Si on a clique sur OK apres saisie ou confirme la saisie */
if ((isset($_REQUEST['formSubmitted']) && $_REQUEST['formSubmitted'] == 1)) {
	// Modification ou Creation
	if (!isset($CountryCity)) {
	    $CountryCity = Object::load('CountryCity');
	}

	Database::connection()->startTrans();
	FormTools::autoHandlePostData($_REQUEST, $CountryCity, 'CountryCity');

	// Verification des saisies
	$test = $CountryCity->alreadyExistInCountry(
	       $_REQUEST['CountryCity_CityName_Name'], $_REQUEST['CountryCity_Zip_Code']);
	$query = UrlTools::buildURLFromRequest(array('ccId', 'CountryCity_Country_ID',
            'CountryCity_CityName_Department_State_ID',
            'CountryCity_CityName_Department_ID',
            'CountryCity_CityName_Name', 'CountryCity_Zip_Code'));
	if (true === $test && !isset($_REQUEST['confirm'])) {
	    Template::errorDialog(
    		    _('A city with name and zip code provided already exists in this country. Please correct.'),
    			$action . '?' . $query);
    	exit;
    } elseif ((! false == $test) && !isset($_REQUEST['confirm'])) {
        $errorMsg = sprintf(
            _('The following cities already exist in this country: <ul>%s</ul>Continue ?'), 
            $test);
 		Template::confirmDialog($errorMsg, $action . '?' . $query .
                '&formSubmitted=1&confirm=1', $action . '?' . $query);
		exit;
	}
	$datas = array('Name' => $_REQUEST['CountryCity_CityName_Name'],
				   'ZipCode' => $_REQUEST['CountryCity_Zip_Code'],
				   'Department' => $_REQUEST['CountryCity_CityName_Department_ID'],
				   'State' => $_REQUEST['CountryCity_CityName_Department_State_ID']);
	$CountryCity->setCountry($_REQUEST['CountryCity_Country_ID']);

	// Sauve aussi les Zip et CityName si necessaire
	$CountryCity->saveAll($datas);

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
$form = new HTML_QuickForm('CountryCityAddEdit', 'post', $action);

$CountryArray = SearchTools::createArrayIDFromCollection('Country');
$form->addElement('select', 'CountryCity_Country_ID', _('Country'), $CountryArray,
        'onchange="fw.ajax.updateSelect(\'country\', \'state\', \'State\', \'Country\');'
        . 'fw.ajax.updateSelect(\'country\', \'department\', \'Department\', \'Country\');'
        . 'return false;" class="searchField" id="country"');
$form->addElement('text', 'CountryCity_CityName_Name', _('Name'), 'style="width:100%"');
$form->addElement('text', 'CountryCity_Zip_Code', _('Zip code'), 'style="width:100%"');
$form->addElement('hidden', 'ccId', isset($_REQUEST['ccId'])?$_REQUEST['ccId']:0, 'id="ccId"');
$form->addElement('hidden', 'stateId',
        isset($Department)?$Department->getStateId():0, 'id="stateId"');
$form->addElement('hidden', 'departmentId',
        isset($Department)?$DepartmentId:0, 'id="departmentId"');


/*  Si Edition d'un CountryCity existant  */
if (isset($CountryCity)) {
	$defaultValues = FormTools::getDefaultValues($form, $CountryCity);
}
else {
	$defaultValues = SearchTools::createDefaultValueArray();
}
$form->setDefaults($defaultValues);  // Le form avec les valeurs par defaut

/*  Validation du formulaire */
$form->addRule('CountryCity_CityName_Name',
        _('Please provide a name.'), 'required', '', 'client');
$form->setJsWarnings(E_VALIDATE_FORM.' : ',_('Please correct.'));

$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$pageContent = $smarty->fetch('CountryCity/CountryCityAddEdit.html');

$action = (isset($_REQUEST['ccId']))?_('Update'):_('Create');
Template::page(
    $action . _('city'),
    $pageContent,
    array('JS_AjaxTools.php', 'js/includes/CountryCityAddEdit.js')
);

?>
