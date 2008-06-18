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
require_once('Objects/DocumentModel.php');
require_once('Objects/DocumentModel.inc.php');
require_once('Objects/Property.php');
require_once('Objects/DocumentModelProperty.php');

define('MAXNUMBERDOCPRINTED', 10);
    
$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW), 
    array('showErrorDialog' => true, 'debug' => false));

if(isset($_REQUEST['domId'])) {
    $query = '?domId='.$_REQUEST['domId'];
    $_SESSION['domId'] = $_REQUEST['domId'];
} else {
    $query = '';
}


/*  Si on a clique sur OK apres saisie  */
if ((isset($_REQUEST['formSubmitted']) && $_REQUEST['formSubmitted'] == 1)) {
	if (isset($_REQUEST['domId'])) {  // Modification
	    $DocumentModel = Object::load('DocumentModel', $_REQUEST['domId']);
		if (Tools::isEmptyObject($DocumentModel)) {
		    Template::errorDialog(E_MSG_TRY_AGAIN, 'DocumentModelList.php');
    		Exit;
		}
	}
	else {    // Creation
		$DocumentModel = Object::load('DocumentModel');  
	}
	
	/*  Ouverture d'une transaction  car on peut etre amene a modifier 2 modeles 
	* d'un coup, si celui par defaut change  **/ 
	Database::connection()->startTrans();
	$DocumentModel->BecomeDefault();  // Remplace le Modele par defaut si besoin par celui-la
    FormTools::autoHandlePostData($_POST, $DocumentModel, 'DocumentModel');
    if(isset($_REQUEST['LogoType']) && is_array($_REQUEST['LogoType'])) {
        $logoType = isset($_REQUEST['LogoType'][0]) ? 
            $_REQUEST['LogoType'][0] : 0;
        $DocumentModel->setLogoType($logoType);
        $Actor = isset($_REQUEST['LogoType'][1]) ?
            Object::load('Actor', $_REQUEST['LogoType'][1]) : 0;
        $DocumentModel->setActor($Actor);
    }

    saveInstance($DocumentModel, 'DocumentModelList.php');

	/*
	 * SC personnalisation des documents :
     * Enregistrement des propriétés modifiés dans les popups
     */
	$DomPropMapper = Mapper::singleton('DocumentModelProperty');
    if(isset($_SESSION['domPropToRemove'])) {
	    $DomPropMapper->delete($_SESSION['domPropToRemove']);
	    unset($_SESSION['domPropToRemove']);
	}
	if(isset($_SESSION['domPropCol'])) {
	    $DomMapper = Mapper::singleton('DocumentModel');
	    $Dom = $DomMapper->load(array('id' => $_SESSION['domId']));
	    $Dom->SetDocumentModelPropertyCollection($_SESSION['domPropCol']);
        saveInstance($Dom, 'DocumentModelList.php');

	    $count = $_SESSION['domPropCol']->getcount();
	    if ($count >= 1) {
	        $itemIds = $_SESSION['domPropCol']->getItemIds();
            for ($i=0 ; $i<$count ; $i++) {
	            // enregistrement des objects de la collection en session :
	            // cela enregistre les nouvelles property et celles modifiées
	            $domProperty = $_SESSION['domPropCol']->getItem($i);
	            $domProperty->setId($itemIds[$i]);
                saveInstance($domProperty, 'DocumentModelList');
	        }
	    }
        if(isset($_SESSION['domPropCol'])) {
	    unset($_SESSION['domPropCol']);
        }
	}

	/*  Commit de la transaction  */
    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        die('erreur sql');
    }
    Database::connection()->completeTrans();
    //die();
    Tools::redirectTo('DocumentModelList.php?' . SID);
	exit;
}


/*  Formulaire */
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('DocumentModelAddEdit', 'post', $_SERVER['PHP_SELF'].$query);
$form->updateAttributes(
	array('onsubmit' => "return checkBeforeSubmit(document.forms[0].elements['DocumentModel_DocType'].value);"));


$form->addElement('text', 'DocumentModel_Name', _('Name'), 'size="80"');
$form->addElement('select', 'DocumentModel_DocType', _('Document type'), getDocumentTypeArray());
// Les boutons radio pour l'attribut Default sons geres ds le template

/*  Fonctionalite de PEAR::QuickForm: hierselect: 2 select interdependants, via JS  */
$ActorArray = SearchTools::CreateArrayIDFromCollection('Actor');
$ActorArray = array('0' => _('Select an actor')) + $ActorArray; // car ## provoque une erreur js...
foreach($ActorArray as $key => $value){  // pour eviter erreur JS si des Names avec des '"'
	$ActorArray[$key] = str_replace('"', '\"', $ActorArray[$key]);
}

$empty = array('0' => _('Select a fixed actor'));
$ActorSelect = array(DocumentModel::NO_LOGO => $empty, DocumentModel::EXPEDITOR => $empty, 
					 DocumentModel::DESTINATOR => $empty, DocumentModel::ONE_ACTOR => $ActorArray);
$LogoTypeHierSelect = $form->addElement('hierselect','LogoType', _('Logo type'));
$options = array(DocumentModel::getLogoTypeConstArray(), $ActorSelect);
$LogoTypeHierSelect->setOptions($options);
$form->addElement('textarea', 'DocumentModel_Footer', _('Footer'), ' cols="80" rows="5"');


/*  Si Edition d'un modele existant  */
$number = 1; //nombre de doc à imprimer
if (isset($_REQUEST['domId'])) {
	$DocumentModel = Object::load('DocumentModel', $_REQUEST['domId']);
	if (Tools::isEmptyObject($DocumentModel)) {
	    Template::errorDialog(E_MSG_TRY_AGAIN, 'DocumentModelList.php');
   		Exit;
	}
    foreach($DocumentModel->getProperties() as $name => $class) {
	    $getter = 'get' . $name;
	    $val = $DocumentModel->$getter();
		$defaultValues['DocumentModel_'.$name] = $DocumentModel->$getter();
	}
	
	$defaultValues['LogoType'] = array($DocumentModel->getLogoType(), $DocumentModel->getActorId());
	$form->setDefaults($defaultValues);  // affiche le form avec les valeurs par defaut
	
	$DefaultTrue = ($DocumentModel->getDefault() == 1)?'checked':'';
	$DefaultFalse = ($DocumentModel->getDefault() == 0)?'checked':'';
	$smarty->assign('DefaultTrue', $DefaultTrue);
	$smarty->assign('DefaultFalse', $DefaultFalse);
	
	$DefaultDuplicataTrue = ($DocumentModel->getDisplayDuplicata() == 1)?'checked':'';
	$DefaultDuplicataFalse = ($DocumentModel->getDisplayDuplicata() == 0)?'checked':'';
	$smarty->assign('DefaultDuplicataTrue', $DefaultDuplicataTrue);
	$smarty->assign('DefaultDuplicataFalse', $DefaultDuplicataFalse);
	
	$DefaultTotalWeightTrue = ($DocumentModel->getDisplayTotalWeight())?
	   'checked':'';
	$DefaultTotalWeightFalse = (!$DocumentModel->getDisplayTotalWeight())?
	   'checked':'';
	$smarty->assign('DefaultTotalWeightTrue', $DefaultTotalWeightTrue);
	$smarty->assign('DefaultTotalWeightFalse', $DefaultTotalWeightFalse);
    
    $DefaultProductDetailTrue = ($DocumentModel->getDisplayProductDetail())?
	   'checked':'';
	$DefaultProductDetailFalse = (!$DocumentModel->getDisplayProductDetail())?
	   'checked':'';
	$smarty->assign('DefaultProductDetailTrue', $DefaultProductDetailTrue);
	$smarty->assign('DefaultProductDetailFalse', $DefaultProductDetailFalse);
	//nombre de doc à imprimer
	$number = $DocumentModel->getNumber();
}
$numberArray = array();
for ($i=1 ; $i<MAXNUMBERDOCPRINTED+1 ; $i++) {
    $numberArray[$i] = $i;
}
$form->addElement('select', 'DocumentModel_Number', 
    _('Number of copies printed automatically in desktop applications'), 
    $numberArray, $number);

$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$pageContent = $smarty->fetch('DocumentModelAddEdit.html');

$action = (isset($_REQUEST['domId']))?_('Update form model'):_('Add a form model');
$domId = (isset($_REQUEST['domId']))?$_REQUEST['domId']:0;
$js=array('JS_DocumentModel.php?domId='.$domId);

Template::page($action, $pageContent, $js);
exit;

?>
