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
require_once('Objects/ParagraphModel.php');
require_once('Objects/LinkFormModelParagraphModel.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

define('E_ERROR_P_LINK', _('This paragraph is already linked to the form.'));
define('E_ERROR_P_ORDER', 
    _('A paragraph already exists at this position, please modify the order.'));
define('E_ERROR_P_NO_SELECT', 
    _('Please select a paragraph or create a new.'));

if(!isset($_SESSION['FormModel'])) {
    Template::errorDialog(E_MSG_TRY_AGAIN, basename($_SERVER['PHP_SELF']), BASE_POPUP_TEMPLATE);
    exit();
}
$formModel = $_SESSION['FormModel'];
$retURL = basename($_SERVER['PHP_SELF']);

if (isset($_POST['Ok'])) {
    $linkFormModelParagraphModelMapper = Mapper::singleton('LinkFormModelParagraphModel');
    //créer le paragraph si nécéssaire
    if($_POST['FormModel_ParagraphModel_ID'] == '##') {
        if(isset($_POST['FormModel_ParagraphModel_Title'])) {
            // si le titre taper correspond à un paragraphe existant on le reutilise
            $paragraphModelMapper = Mapper::singleton('ParagraphModel');
            $paragraphTest = $paragraphModelMapper->load(
                array('Title'=>$_POST['FormModel_ParagraphModel_Title']));
            if($paragraphTest instanceof ParagraphModel) {
                $paragraphModelId = $paragraphTest->getId();
            } else {
                Database::connection()->startTrans();
                $paragraphModel = new ParagraphModel();
                $paragraphModel->setTitle($_POST['FormModel_ParagraphModel_Title']);
                saveInstance($paragraphModel, $retURL);
                //  Commit de la transaction
                if (Database::connection()->HasFailedTrans()) {
                    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
                    Database::connection()->rollbackTrans();
                    Template::errorDialog(E_ERROR_SAVING, $retURL, BASE_POPUP_TEMPLATE);
                    exit();
                }
                Database::connection()->completeTrans();
                $paragraphModelId = $paragraphModel->getId();
            }
        } else {
            Template::errorDialog(E_ERROR_P_NO_SELECT, $retURL, BASE_POPUP_TEMPLATE);
            exit();
        }
    } else {
        $paragraphModelId = $_POST['FormModel_ParagraphModel_ID'];
    }
    
    // verifier que le paragraph n'est pas déjà lié au formulaire
    $object = $linkFormModelParagraphModelMapper->load(
                    array('FormModel'=>$formModel->getId(),
                          'ParagraphModel'=>$paragraphModelId));
    if($object instanceof LinkFormModelParagraphModel) {
        Template::errorDialog(E_ERROR_P_LINK, $retURL, BASE_POPUP_TEMPLATE);
        exit();
    }
    // verifier qu'un paragraph n'existe pas déjà avec cette ordre pour ce formModel
    $object = $linkFormModelParagraphModelMapper->load(
                    array('FormModel'=>$formModel->getId(),
                          'ParagraphOrder'=>$_POST['ParagraphOrder']));
    if($object instanceof LinkFormModelParagraphModel) {
        Template::errorDialog(E_ERROR_P_ORDER, $retURL, BASE_POPUP_TEMPLATE);
        exit();
    }
    $linkFormModelParagraphModel = new LinkFormModelParagraphModel();
    $linkFormModelParagraphModel->setFormModel($formModel);
    $linkFormModelParagraphModel->setParagraphModel($paragraphModelId);
    $linkFormModelParagraphModel->setParagraphOrder($_POST['ParagraphOrder']);
    
    Database::connection()->startTrans();
    saveInstance($linkFormModelParagraphModel, $retURL);

    //  Commit de la transaction
    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_ERROR_SAVING, $retURL, BASE_POPUP_TEMPLATE);
        exit();
    }
    Database::connection()->completeTrans();
}
Session::register('FormModel', $formModel);

// Formulaire
$smarty = new Template();

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('ParagraphModelSelect', 'post');

// champs du formulaire
$paragraphList = SearchTools::CreateArrayIDFromCollection('ParagraphModel', array(), 
    _('None'), 'Title');
$form->addElement('select', 'FormModel_ParagraphModel_ID', 
    _('Select an existing paragraph'), 
    $paragraphList, 'style="width:100%;"');
$form->addElement('text', 'FormModel_ParagraphModel_Title', 
    _('Add a paragraph'), 'style="width:100%;"');
$form->addElement('text', 'ParagraphOrder', _('Paragraph order'));

// Validation du formulaire
$form->addRule('ParagraphOrder', _('You must set the priority order of the paragraph'), 
    'required', 'numeric', 'client');
$form->setJsWarnings(E_VALIDATE_FORM.' : ',_('Please correct.'));

$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());
$smarty->assign('retURL', $retURL);

$pageContent = $smarty->fetch('CommercialForm/ParagraphModelSelect.html');
$pageTitle = _('Add a paragraph to a form model.');

Template::page('', $pageTitle .  $pageContent . '</form>', array(), array(),
    BASE_POPUP_TEMPLATE);


?>
