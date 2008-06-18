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
require_once('Objects/Question.php');
require_once('Objects/LinkQuestionAnswerModel.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

define('E_ERROR_A_LINK', _('This answer is already linked to this question.'));
define('E_ERROR_A_ORDER', 
    _('An answer already exists at this position, please modify the order.'));

if(!isset($_SESSION['FormModel'])) {
    Template::errorDialog(E_MSG_TRY_AGAIN, basename($_SERVER['PHP_SELF']), BASE_POPUP_TEMPLATE);
    exit();
}
$formModel = $_SESSION['FormModel'];
$retURL = basename($_SERVER['PHP_SELF']);

//Database::connection()->debug = true;

if (isset($_POST['Ok'])) {
    // verifier que la réponse n'est pas déjà lié à la question
    $linkQuestionAnswerModelMapper = Mapper::singleton('LinkQuestionAnswerModel');
    $object = $linkQuestionAnswerModelMapper->load(
                    array('Question' => $_POST['QuestionID'],
                          'AnswerModel' => $_POST['AnswerModelID']));
    if($object instanceof LinkQuestionAnswerModel) {
        Template::errorDialog(E_ERROR_A_LINK, $retURL, BASE_POPUP_TEMPLATE);
        exit();
    }
    $object = $linkQuestionAnswerModelMapper->load(
                    array('Question' => $_POST['QuestionID'],
                          'AnswerOrder' => $_POST['AnswerOrder']));
    if($object instanceof LinkQuestionAnswerModel) {
        Template::errorDialog(E_ERROR_A_ORDER, $retURL, BASE_POPUP_TEMPLATE);
        exit();
    }
    
    $linkQuestionAnswerModel = new LinkQuestionAnswerModel();
    $linkQuestionAnswerModel->setAnswerModel($_POST['AnswerModelID']);
    $linkQuestionAnswerModel->setQuestion($_POST['QuestionID']);
    $linkQuestionAnswerModel->setAnswerOrder($_POST['AnswerOrder']);
    
    Database::connection()->startTrans();
    saveInstance($linkQuestionAnswerModel, $retURL);

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
$form = new HTML_QuickForm('AnswerModelSelect', 'post');

// champs du formulaire
$paragraphCol = $formModel->getParagraphModelCollection();
$fullQuestionCol = new Collection();
$icount = $paragraphCol->getCount();
for($i=0 ; $i<$icount ; $i++) {
    $paragraph = $paragraphCol->getItem($i);
    $questionCol = $paragraph->getQuestionCollection();
    $jcount = $questionCol->getCount();
    for($j=0 ; $j<$jcount ; $j++) {
        $question = $questionCol->getItem($j);
        $question->setText($paragraph->getTitle() . ' - ' . $question->getText());
        $fullQuestionCol->setItem($question);
        unset($question);
    }
}

$questionOptions = FormTools::writeOptionsFromCollection($fullQuestionCol, 0, 'getText');
$smarty->assign('questionOptions', implode("\n\t", $questionOptions));

$answerList = SearchTools::CreateArrayIDFromCollection('AnswerModel', array(), '', 'Value');
$form->addElement('select', 'AnswerModelID', 
    _('Select an answer'), $answerList, 
    'style="width:100%;"');

$form->addElement('text', 'AnswerOrder', _('Answer order'));

// Validation du formulaire
$form->addRule('QuestionID', _('Please select a question.'),
    'required', 'numeric', 'client');
$form->addRule('AnswerOrder', _('Please provide the answer order.'), 
    'required', 'numeric', 'client');
$form->setJsWarnings(E_VALIDATE_FORM.' : ',_('Please correct.'));

$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());
$smarty->assign('retURL', $retURL);

$pageContent = $smarty->fetch('CommercialForm/AnswerModelSelect.html');
$pageTitle = _('Add an answer to a form model.');

Template::page($pageTitle, $pageTitle .  $pageContent . '</form>',
    array(), array(), BASE_POPUP_TEMPLATE);


?>
