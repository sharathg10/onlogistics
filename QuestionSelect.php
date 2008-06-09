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
require_once('Objects/Question.php');
require_once('Objects/LinkParagraphModelQuestion.php');
require_once('Objects/Theme.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

define('E_ERROR_Q_LINK', _('This question is already linked to a paragraph.'));
define('E_ERROR_Q_ORDER', 
    _('An answer already exists at this position, please modify the order.'));

if(!isset($_SESSION['FormModel'])) {
    Template::errorDialog(E_ERROR_SESSION, basename($_SERVER['PHP_SELF']), BASE_POPUP_TEMPLATE);
    exit();
}
$formModel = $_SESSION['FormModel'];
$retURL = basename($_SERVER['PHP_SELF']);


if (isset($_POST['Ok'])) {
    if(!isset($_POST['QuestionID']) || $_POST['QuestionID']==0 
        || $_POST['QuestionID']=='##') {
        Template::errorDialog(_('You must select a question'), 
            $retURL, BASE_POPUP_TEMPLATE);
        exit();
    }
    if(!isset($_POST['ParagraphModelID']) || $_POST['ParagraphModelID']==0 
        || $_POST['ParagraphModelID']=='##') {
        Template::errorDialog(_('You must select a paragraph'), 
            $retURL, BASE_POPUP_TEMPLATE);
        exit();
    }
    // verifier que la question n'est pas déjà lié au paragraph
    $linkParagraphModelQuestionMapper = Mapper::singleton('LinkParagraphModelQuestion');
    $object = $linkParagraphModelQuestionMapper->load(
                    array('Question' => $_POST['QuestionID'],
                          'ParagraphModel' => $_POST['ParagraphModelID']));
    if($object instanceof LinkParagraphModelQuestion) {
        Template::errorDialog(E_ERROR_Q_LINK, $retURL, BASE_POPUP_TEMPLATE);
        exit();
    }
    // verifier que l'ordre de la question n'est pas utilisé dans ce paragraphe
    $object = $linkParagraphModelQuestionMapper->load(
                    array('QuestionOrder' => $_POST['QuestionOrder'],
                          'ParagraphModel' => $_POST['ParagraphModelID']));
    if($object instanceof LinkParagraphModelQuestion) {
        Template::errorDialog(E_ERROR_Q_ORDER, $retURL, BASE_POPUP_TEMPLATE);
        exit();
    }    
    $linkParagraphModelQuestion = new LinkParagraphModelQuestion();
    $linkParagraphModelQuestion->setParagraphModel($_POST['ParagraphModelID']);
    $linkParagraphModelQuestion->setQuestion($_POST['QuestionID']);
    $linkParagraphModelQuestion->setQuestionOrder($_POST['QuestionOrder']);
    
    Database::connection()->startTrans();
    saveInstance($linkParagraphModelQuestion, $retURL);

    //  Commit de la transaction
    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_ERROR_SAVING, $retURL, BASE_POPUP_TEMPLATE);
		exit();
    }
    Database::connection()->completeTrans();
    $_SESSION['FormModel'] = $formModel;
}
// Formulaire
$smarty = new Template();

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('QuestionSelect', 'post');

// champs du formulaire
$paragraphCol = $_SESSION['FormModel']->getParagraphModelCollection();
$paragraphList = SearchTools::createArrayIdFromCollection('ParagraphModel', 
    array('Id'=>$paragraphCol->getItemIds()),
    _('Select a paragraph'), 'Title');
$form->addElement('select', 'ParagraphModelID', _('Move answer in next paragraph'),
    $paragraphList, 'id="ParagraphModelID" style="width: 100%;"');
$themeList = SearchTools::CreateArrayIDFromCollection('Theme', array(), 
    _('Select a theme'), 'Name');
$form->addElement('select', 'ThemeID', 
    _('Select question theme to display'),
    $themeList, 'id="ThemeID" onchange="displayQuestionSelect();return false;" style="width:100%;"');

$categoryList = SearchTools::CreateArrayIDFromCollection('Category', array(), 
    _('Select a category'), 'Name');
$form->addElement('select', 'CategoryID', 
    _('Select concerned customers category'),
    $categoryList, 'id="CategoryID" onchange="displayQuestionSelect();return false;" style="width:100%;"');

$form->addElement('select', 'QuestionID', _('Select question to display'),
                  array(), 'id="QuestionID" style="width:100%;"');
$form->addElement('text', 'QuestionOrder', _('question order in the paragraph'));

// Validation du formulaire
$form->addRule('ParagraphModelID', _('Please select a paragraph'),
    'required', 'numeric', 'client');
$form->addRule('QuestionID', _('Please select a question'),
    'required', 'numeric', 'client');
$form->addRule('QuestionOrder', _('Please provide the answer order.'), 
    'required', 'numeric', 'client');
$form->setJsWarnings(E_VALIDATE_FORM.' : ',_('Please correct.'));

$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());
$smarty->assign('retURL', $retURL);

$pageContent = $smarty->fetch('CommercialForm/QuestionSelect.html');
$pageTitle = _('Add an answer to a form model.');
$js = array('JS_AjaxTools.php', 'js/includes/QuestionSelect.js');

Template::ajaxPage($pageTitle, $pageTitle .  $pageContent . '</form>', $js,
    array(), BASE_POPUP_TEMPLATE);

?>
