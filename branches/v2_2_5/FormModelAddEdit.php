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
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once('HTML/QuickForm.php');
require_once('Objects/FormModel.php');
require_once('TreeMenu.php');

$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'FormModelList.php';
$fmID = isset($_REQUEST['fmID'])?$_REQUEST['fmID']:0;

if ($fmID > 0) {
    // mode édition
    $formModelMapper = Mapper::singleton('FormModel');
    $formModel = $formModelMapper->load(array('Id'=>$fmID));
} elseif(isset($_SESSION['FormModel'])) {
    $formModel = $_SESSION['FormModel'];
} else {
    // mode ajout
    $formModel = new FormModel();
    $formModel->generateId();
}
Session::register('FormModel', $formModel, 3);

//  Si on a clique sur OK apres saisie  ou confirme la saisie

if (isset($_POST['Ok']) || isset($_POST['update'])) {
    Database::connection()->startTrans();
    FormTools::autoHandlePostData($_POST, $formModel);
    saveInstance($formModel, $retURL);
    $fmID = $formModel->getId();
    //  Commit de la transaction
    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
    }
    Database::connection()->completeTrans();
    
    if(isset($_POST['update']) && $_POST['update']==0) {
        Tools::redirectTo($retURL);
        exit;
    } else {
        Tools::redirectTo('FormModelAddEdit.php?fmID='.$fmID);
        exit();
    }
}

// Formulaire
$smarty = new Template();

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('FormModelAddEdit', 'post');

// champs cachés
$form->addElement('hidden', 'retURL', $retURL);
$form->addElement('hidden', 'fmID', $fmID);
$form->addElement('hidden', 'update', 0);

// champs du formulaire
$form->addElement('text', 'FormModel_Name', _('Name'), 
    'style="width:100%;" id="FormModelName"');

$radio = array();
$radio[] = HTML_QuickForm::createElement('radio', null, null, _('yes'), 1, 
    'id="FormModelActivChecked"');
$radio[] = HTML_QuickForm::createElement('radio', null, null, _('no'), 0, 
    'id="FormModelActivUnchecked"');
$form->addGroup($radio,'FormModel_Activ', _('Active').' : ', '&nbsp;');

$actionTypeArray = array('##'=>_('Select an item')) + FormModel::getActionTypeConstArray();
$form->addElement('select', 'FormModel_ActionType', _('Action type'),
    $actionTypeArray, 'id="FormModelActionTypeSelect"');

// valeurs par défaut pour le mode édition
if ($formModel->getId() > 0) {
    $defaultValues = FormTools::getDefaultValues($form, $formModel);
} else {
    $defaultValues['FormModel_Activ'] = 1;
    $defaultValues['FormModel_ActionType'] = '##';
}
$form->setDefaults($defaultValues);


// Validation du formulaire
$form->addRule('FormModel_Name', _('You must provide a form name.'),
    'required', '', 'client');
$form->setJsWarnings(E_VALIDATE_FORM.' : ',_('Please correct.'));

$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());
$smarty->assign('retURL', $retURL);

$TreeItems = array();
if($fmID > 0) {
    $TreeItems = $formModel->getTreeViewStructure();
}

$TreeMenu = new TreeMenu('js/includes/ttm_formModel_tpl.js');
//$TreeMenu->_templateFile = 'FormTemplate';
$TreeMenu->_items = array($TreeItems);
$smarty->assign('tree', $TreeMenu->render());

$pageTitle = _('Add or update form model.');
$pageContent = $smarty->fetch('CommercialForm/FormModelAddEdit.html');

Template::page($pageTitle, $pageContent, $TreeMenu->getJSrequirements());
?>
