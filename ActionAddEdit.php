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
require_once('TreeMenu.php');
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once('HTML/QuickForm.php');
require_once('Objects/FormModel.php');
require_once('Objects/Action.php');
require_once('AlertSender.php');

$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, 
    UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_DIR_COMMERCIAL));

SearchTools::ProlongDataInSession();

$actionID = isset($_REQUEST['aID'])?$_REQUEST['aID']:0;

if ($actionID > 0) {
    // mode édition 
    $actionMapper = Mapper::singleton('Action');
    $action = $actionMapper->load(array('Id'=>$actionID));
    $formModel = $action->getFormModel();
    if($formModel instanceof FormModel) {
        $_REQUEST['FormModel'] = $formModel->getId();
        $_REQUEST['ActionType'] = $formModel->getActionType();
    }
} else {
    // mode ajout
    $action = new Action();
}

$actorId = isset($_REQUEST['actorID'])?$_REQUEST['actorID']:0;
$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'ActionList.php';
$errorURL = 'ActionAddEdit.php?aID=' . $actionID .'&actorID=' . $actorId;
if (isset($_POST['formSubmitted'])) {
    // ouvertue transaction
    Database::connection()->startTrans();
    FormTools::autoHandlePostData($_POST, $action);
    //$action->setActionDate(date('Y-m-d h:i:s'));
    saveInstance($action, $errorURL);
    
    // création des RealAnswer
    $questions = array();
    foreach ($_POST as $key=>$value) {
        if(ereg("(question_)([[:alnum:]]+)", $key, $regs)) {
            $questions[$regs[2]] = $value;
        }
    }
    
    $RealAnswerMapper = Mapper::singleton('RealAnswer');
    foreach ($questions as $questionId=>$answer) {
        $answer = is_array($answer)?$answer:array($answer);
        foreach ($answer as $answerValue) {
            $filter = array('Action'=>$action->getId(),
                'Question'=>$questionId);
            $RealAnswer = $RealAnswerMapper->load($filter);
            if(!($RealAnswer instanceof RealAnswer)) {
                $RealAnswer = Object::load('RealAnswer');
                $RealAnswer->setAction($action);
                $question = Object::load('Question', $questionId); 
                $RealAnswer->setQuestion($question);
            }
            $answerModelTest = Object::load('AnswerModel', $answerValue);
            if(!($answerModelTest instanceof AnswerModel)) {
                $RealAnswer->setValue(stripcslashes($answerValue));
            } else {
                $RealAnswer->setAnswerModel($answerValue);
            }
            saveInstance($RealAnswer, $errorURL);
        }
    }
    
    // création d'une nouvelle action si celle ci passe à l'état terminé
    if($action->getState() == Action::ACTION_STATE_DO) {
        $nbSecondsPerWeek = 7*24*60*60;
        $frequency = Tools::getValueFromMacro($action, 
            '%Actor.CustomerProperties.PersonalFrequency.Frequency%');
        if($frequency > 0) { 
            $wishedDate = DateTimeTools::DateModeler($action->getActionDate(), 
            $frequency*$nbSecondsPerWeek);
            $nextAction = new Action();
            $nextAction->setCommercial($action->getCommercial());
            $nextAction->setActor($action->getActor());
            $nextAction->setFormModel($action->getFormModel());
            $nextAction->setWishedDate($wishedDate);
            $nextAction->setActionDate($wishedDate);
            $nextAction->setType($action->getType());
            $nextAction->setState(Action::ACTION_STATE_TODO);
            saveInstance($nextAction, $errorURL);
        }
    }
    
    //  Commit de la transaction
    if (Database::connection()->HasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
    }
    Database::connection()->completeTrans();
    
    if(isset($_POST['updateCustomerAlert']) && $_POST['updateCustomerAlert']==1) {
        /* envoie du mail d'alert */
        AlertSender::send_ALERT_CUSTOMER_SITUATION($action);
        
    }
    Tools::redirectTo($retURL);
    exit();
}

if(isset($_REQUEST['ActionType']) && isset($_REQUEST['FormModel']) && $_REQUEST['FormModel']!='##') {
    // affichage du formulaire
    $TreeItems = array();
    if($_REQUEST['FormModel'] > 0) {
        $formModel = Object::load('FormModel', $_REQUEST['FormModel']);
        $TreeItems = $formModel->getTreeViewStructure(false);
    }
    
    $smarty = new Template();
    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form = new HTML_QuickForm('FormModelAddEdit', 'post');

    // champs cachés
    $form->addElement('hidden', 'Action_FormModel_ID', $formModel->getId());
    $form->addElement('hidden', 'Action_ActionDate', date('Y-m-d H:i:s', time()));
    $form->addElement('hidden', 'Action_Type', $formModel->getActionType());
    $form->addElement('hidden', 'Action_Actor_ID', $actorId);
    $form->addElement('hidden', 'Action_Commercial_ID', $auth->getActorId());
    $form->addElement('hidden', 'aID', $actionID);
    $form->addElement('hidden', 'retURL', $retURL);
    
    $actionStateArray = Action::getStateConstArray();
    $form->addElement('select', 'Action_State', _('Action status'),
        $actionStateArray, 'onChange="synchronizeActionStates(this)"');
    $form->addElement('textarea', 'Action_Comment', _('Comment'),
            'style="width:100%;"');
    // valeurs par défaut pour le mode édition
    $answers = array();
    if ($action->getId() > 0) {
        $defaultValues = FormTools::getDefaultValues($form, $action);
        /* si ActionDate = 'OOOO-OO-00 00:00:00' c'est que l'action a été crée
        à la validation de la précédenrte, il faut positionner la date à la date
        du jour lors de l'édition */
        if($action->getActionDate()=='0000-00-00 00:00:00') {
            $defaultValues['Action_ActionDate'] = date('Y-m-d h:i:s', time());
        }
        $realAnswerCollection = $action->getRealAnswerCollection();
        $count = $realAnswerCollection->getCount();
        for ($i=0 ; $i<$count ; $i++) {
            $realAnswer = $realAnswerCollection->getItem($i);
            $key = 'question_'.$realAnswer->getQuestionId();
            if(isset($answers[$key]))  {
                if(!is_array($answers[$key])) {
                    $answers[$key] = array($answers[$key]);
                }
                $answers[$key][] = $realAnswer->getAnswerModelId();
            } else {
                if($realAnswer->getAnswerModelId()) {
                    $answers[$key] = $realAnswer->getAnswerModelId();
                } else {
                    $answers[$key] = $realAnswer->getValue();
                }
            }
        }
    } else {        
        $defaultValues['Action_State'] = 0;
    }
    
    $form->setDefaults($defaultValues);
    
    $form->addRule('Action_State', _('Please provide a status.'),
    'nonzero', '', 'client');
    
    $form->accept($renderer);
    $smarty->assign('form', $renderer->toArray());
    $smarty->assign('retURL', $retURL);
    
    //$TreeItems = array();
    
    $TreeMenu = new TreeMenu('js/includes/ttm_formModel_tpl.js');
    $TreeMenu->_items = array($TreeItems);
    $smarty->assign('tree', $TreeMenu->render());
    $smarty->assign('formulaire', $formModel->getEditableFormModel($answers));
    $smarty->assign('updateCustomerAlert', 0);
    
    $actor = Object::load('Actor', $actorId);
    $pageTitle = sprintf(_('Add/Update action for actor "%s"'), $actor->getName());
    $pageContent = $smarty->fetch('Action/ActionAddEdit.html');
    
    $js = array('js/includes/ActionAddEdit.js');
    $js = array_merge($js, $TreeMenu->getJSrequirements());
    
    Template::page($pageTitle, $pageContent, $js);
} elseif (isset($_REQUEST['fromActorAddEdit']) && $_REQUEST['fromActorAddEdit']==1) {
    /*
    Le client à été passé manuellement en alert, 
    une action à été créé, il faut forcer la saisie
    du commentaire.
    */
    $smarty = new Template();
    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
    $form = new HTML_QuickForm('FormModelAddEdit', 'post');
    
    $form->addElement('hidden', 'aID', $actionID);
    $form->addElement('hidden', 'retURL', $retURL);
    
    $form->addElement('hidden', 'updateCustomerAlert', 1);
    $form->addElement('textarea', 'Action_Comment', _('Comment'),
        'style="width:100%;"');
    $actionStateArray = Action::getStateConstArray();
    $form->addElement('select', 'Action_State', _('Action status'),
        $actionStateArray, 'disabled');
        
    $defaultValues = FormTools::getDefaultValues($form, $action);
    $form->setDefaults($defaultValues);
    
    $form->addRule('Action_Comment', _('Please provide a comment.'),
    'required', '', 'client');
    
    $form->accept($renderer);
    $smarty->assign('form', $renderer->toArray());
    $smarty->assign('retURL', $retURL);
    $smarty->assign('updateCustomerAlert', 1);
    
    $actor = Object::load('Actor', $actorId);
    $pageTitle = sprintf(_('Client %s has been put in alert state.'), $actor->getName());
    $pageContent = $smarty->fetch('Action/ActionAddEdit.html');
    
    $js = array('js/includes/ActionAddEdit.js');
    Template::page($pageTitle, $pageContent, $js);
} else {
    // affichage du select des type d'action
    // puis du select des formModel correspondant
    require_once('HTML/QuickForm/Renderer/Default.php');
	$form = new HTML_QuickForm('ActionAddEdit', 'post', $_SERVER['PHP_SELF']);
	$renderer = new HTML_QuickForm_Renderer_Default();
	if($actorId==0) {
	    $title = "";
	    $filter = array('Generic'=>0);
	    if($auth->getProfile()==UserAccount::PROFILE_COMMERCIAL) {
	       $filter['Commercial'] = $auth->getActorId();
	    }
	    $actorArray = SearchTools::CreateArrayIDFromCollection('Actor', $filter, 
	       _('Select an actor'));
	    $form->addElement('select', 'actorID', '', $actorArray, 
    						  'onChange="this.form.submit()"');
	} else {
	    $selectedActor = Object::load('Actor', $actorId);
	    $title = _('Add an action for actor: ') 
	       . $selectedActor->getName();
        $form->addElement('hidden', 'actorID', $actorId);
    	// recupere les infos sur les ActionType, pour le SELECT
    	$actionTypeArray = array(0=>_('Select an action type')) + 
    	    FormModel::getActionTypeConstArray();
        unset($actionTypeArray[FormModel::ACTION_TYPE_UPDATE_POTENTIAL_KO],
              $actionTypeArray[FormModel::ACTION_TYPE_UPDATE_POTENTIAL_OK]);
    	
        $form->addElement('select', 'ActionType', '', $actionTypeArray, 
    						  'onChange="this.form.submit()"');
    		
    	if(isset($_REQUEST['ActionType'])) {
    		$form ->setDefaults(array('ActionType'=> $_REQUEST['ActionType']));
    		
    		// on récupère les formModel à afficher
    		$formModelArray = SearchTools::CreateArrayIDFromCollection('FormModel', array('ActionType'=>$_REQUEST['ActionType']), 
    		  _('Select a form'));
    		$form->addElement('select', 'FormModel', '', $formModelArray, 
    						  'onChange="this.form.ActionType.disabled=false;this.form.submit()"');
    	}
	}

	$form->accept($renderer);			  // affecte au form le renderer personnalise
	$monformHTML = $renderer->toHtml();   // recup le HTML du form
	$monformHTML = str_replace ('</form>' , "" , $monformHTML); 
    Template::page('', $title . $monformHTML . '</form>');
}
?>
