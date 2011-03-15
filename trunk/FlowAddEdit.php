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
require_once('Objects/Flow.php');
require_once('Objects/FlowItem.php');
require_once('Objects/FlowType.php');
require_once('Objects/Currency.php');
require_once('Objects/ActorBankDetail.php');
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once('HTML/QuickForm.php');
require_once('AccountTools.php');
require_once('FormatNumber.php');

//Database::connection()->debug = true;
define('E_EXIST_FLOW', _('A expense/receipt with this number already exists.'));

$auth = Auth::Singleton();
$auth->checkProfiles(
    array(
        UserAccount::PROFILE_ADMIN,
        UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES,
        UserAccount::PROFILE_ACCOUNTANT
    )
);

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'FlowList.php';
$flowID = isset($_REQUEST['flowID'])?$_REQUEST['flowID']:0;

// Formulaire
$smarty = new Template();

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('FlowAddEdit', 'post');

if ($flowID > 0) {
    // on est en mode édition
    $mapper = Mapper::singleton('Flow');
    $flow = $mapper->load(array('Id' => $flowID));
} elseif (isset($_SESSION['flow'])) {
    // on a déjà instancié l'objet
    $flow = $_SESSION['flow'];
} else {
    // on est en mode ajout
    $flow = new Flow();
}
Session::register('flow', $flow, 1);

//  Si on a clique sur OK apres saisie  ou confirme la saisie
if (isset($_POST['Ok'])) {

    Database::connection()->startTrans();

    if($_POST['Flow_TotalTTC'] == 0 && $flow->getId() > 0) {
        // Il n'y a rien a enregistrer; si mode edition, on supprime le flow
        deleteInstance($flow, 'FlowAddEdit.php?flowID=' . $flow->getId());
    } elseif($_POST['Flow_TotalTTC'] > 0) {
        // vérifie la somme payé
        if($_POST['Unused_ToPay']<0) {
            Template::errorDialog(
                _('Total paid exceeds remaining to pay amount.'),
                'FlowAddEdit.php?flowID=' . $flow->getId());
            exit();
        }

        // Valeur initiale, avant saisie
        $initPaid = $flow->getPaid();
        $initABD = $flow->getActorBankDetail();
        // On incremente ou decremente en banque, selon que charge ou recette
        $coef = (Tools::getValueFromMacro($flow, '%FlowType.Type%') == FlowType::CHARGE)?-1:1;

        // On remplit l'objet Flow
        FormTools::autoHandlePostData($_POST, $flow);
        // on additionne le total règlé à ce qui a déjà été payé
        $flow->setPaid($initPaid + I18n::extractNumber($_POST['Flow_Paid']));

        $flow->setPaymentDate(DateTimeTools::QuickFormDateToMySQL('Flow_PaymentDate'));
        $flow->setEditionDate(DateTimeTools::QuickFormDateToMySQL('Flow_EditionDate'));

        // On verifie que le flow_Number n'est pas déjà utilisé
        $flowMapper = Mapper::singleton('Flow');
        $flowTest = $flowMapper->load(array('Number'=>$_POST['Flow_Number']));
        if($flowTest instanceof Flow && $flow->getId() != $flowTest->getId()) {
            $flowTypeItemArray = array();
            foreach ($_POST['FlowItem_FlowTypeItem_ID'] as $key=>$value) {
                $flowTypeItemArray[$value] = $_POST['FlowItem_TotalHT'][$key];
            }
            Session::register('flowTypeItem', $flowTypeItemArray, 1);
            Template::errorDialog(E_EXIST_FLOW, basename($_SERVER['PHP_SELF']));
            exit;
        }

        $abd = $flow->getActorBankDetail();
        // Si changement d'ActorBankDetail en mode edition
        if ($initABD instanceof ActorBankDetail && $initABD->getId() != $abd->getId()) {
            $initABD->setAmount($initABD->getAmount() - $coef * ($initPaid));
            saveInstance($initABD, basename($_SERVER['PHP_SELF']));
        }
        if (!Tools::isEmptyObject($abd)) {
            $abd->setAmount($abd->getAmount() + $coef * ($flow->getPaid() - $initPaid));
            saveInstance($abd, basename($_SERVER['PHP_SELF']));
        }
        saveInstance($flow, basename($_SERVER['PHP_SELF']));

        // on sauve les FlowItems
        $flowItemMapper = Mapper::singleton('FlowItem');
        foreach ($_POST['FlowTypeItem_Name'] as $key=>$value) {
            $flowItem = $flowItemMapper->load(
                array('Flow'=>$flow->getId(),
                      'Type'=>$_POST['FlowItem_FlowTypeItem_ID'][$key]));
            if(!($flowItem instanceof FlowItem)) {
                if($_POST['FlowItem_TotalHT'][$key]==0) {
                    // on n'enregistre pas les lignes nulles
                    continue;
                }
                $flowItem = new FlowItem();
                $flowItem->setFlow($flow);
                $flowItem->setType($_POST['FlowItem_FlowTypeItem_ID'][$key]);
            } else {
                if($_POST['FlowItem_TotalHT'][$key]==0) {
                    // on supprime les lignes devenue nulles
                    deleteInstance($flowItem, 'FlowAddEdit.php?flowID=' . $flow->getId());
                    continue;
                }
            }
            $flowItem->setTotalHT(troncature($_POST['FlowItem_TotalHT'][$key]));
            $flowItem->setTVA($_POST['FlowItem_TVA_ID'][$key]);
            $flowItem->setHanding($_POST['FlowItem_Handing'][$key]);
            saveInstance($flowItem, basename($_SERVER['PHP_SELF']));
            unset($flowItem);
        }
    }
    // vérification du prévisionnel
    $filter = array(
        SearchTools::NewFilterComponent('BeginDate', '',
            'GreaterThanOrEquals', $flow->getPaymentDate(), 1),
        SearchTools::NewFilterComponent('_EndDate', 'EndDate', /*prefix necessaire*/
            'LowerThanOrEquals', $flow->getPaymentDate(), 1),
        SearchTools::NewFilterComponent('ForecastFlow', 'FlowTypeItem().FlowType.Id',
            'Equals', $flow->getFlowTypeId(), 1, 'ForecastFlow'),
        SearchTools::NewFilterComponent('Currency', '',
            'Equals', $flow->getCurrency(), 1));

    $forecastFlow = Object::load('ForecastFlow',
        SearchTools::filterAssembler($filter));
    if($forecastFlow instanceof ForecastFlow) {
        $flowType = $flow->getFlowType();
        $method = false;
        if($flowType->getType() == FlowType::CHARGE &&
        $flow->getTotalTTC()>$forecastFlow->getAmount()) {
            $method = 'send_ALERT_FORECAST_EXPENSE_OVER_THE_BORD';
        } else if($flowType->getType() == FlowType::RECETTE &&
        $flow->getTotalTTC()< $forecastFlow->getAmount()) {
            $method = 'send_ALERT_FORECAST_RECEIPT_OVER_THE_BORD';
        }
        if($method) {

            require_once('lib/AlertSender.php');
            $params = array(
                'flowtype' => $flowType->getName(),
                'number' => $flow->getNumber(),
                'flow' => $flow->getName(),
                'pieceno' => $flow->getPieceNo(),
                'total' => $flow->getTotalTTC(),
                'forecast' => $forecastFlow->getAmount());
            $return = call_user_func(array('AlertSender', $method), $params);
        }
    }

    //  Commit de la transaction
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
    }
    Database::connection()->completeTrans();

    Tools::redirectTo($retURL);
    exit;
}

// champs cachés
$form->addElement('hidden', 'retURL', $retURL);
$form->addElement('hidden', 'flowID', $flowID, 'id="flowId"');

// champs du formulaire
$form->addElement('text', 'Flow_PieceNo', _('Piece no'), 'style="width:100%;" class="ReadOnlyField"');
$form->addElement('text', 'Flow_Name', _('Name'), 'style="width:100%;"');
$form->addElement('text', 'Flow_Number', _('Number'), 'style="width:100%;"');
$flowtypeArray = SearchTools::CreateArrayIDFromCollection('FlowType', array('InvoiceType'=>0),
        _('Select a type'), 'Name');
$form->addElement('select', 'Flow_FlowType_ID', _('Expenses/Receipts type'),
    $flowtypeArray, 'id="flowTypeId" onchange=" ' .
        'displayFlowTypeItems();changeActorBankDetail();"'.
        ' style="width:100%;" id="flowTypeId"');
$form->addElement('text', 'Unused_TotalHT', _('Amount excl. VAT'),
    'style="width:96%;" class="ReadOnlyField" readonly="readonly" id="UnusedTotalHT"');
$form->addElement('text', 'Unused_TotalTVA', _('VAT total'),
    'style="width:96%;" class="ReadOnlyField" readonly="readonly" id="UnusedTotalTVA"');
$form->addElement('text', 'Flow_TotalTTC', _('Amount incl. VAT'),
    'style="width:96%;" class="ReadOnlyField" readonly="readonly"');
$form->addElement('text', 'Flow_Paid', _('Total paid'),
    'style="width:100%;" onKeyUp="updateToPay();" id="FlowPaid"');
$form->addElement('text', 'Flow_Handing', _('Global discount'),
    'style="width:100%;" onKeyUp="updateTotal();" id="FlowHanding"');
$form->addElement('text', 'Unused_ToPay', _('Remaining to pay'),
    'style="width:96%;" class="ReadOnlyField" readonly="readonly"');
$form->addElement('text', 'Unused_GlonalHandingAmount', _('Global discount amount'),
    'style="width:96%;" class="ReadOnlyField" readonly="readonly"');

$alreadyPaid = 0;
if ($flow->getId() > 0 || $flow->getNumber()!='') {
    $alreadyPaid = $flow->getPaid();
}
$form->addElement('hidden', 'Already_Paid', $alreadyPaid, 'id="AlreadyPaid"');
$currencyArray = SearchTools::CreateArrayIDFromCollection('Currency', array(), '', 'Name');
$form->addElement('select', 'Flow_Currency_ID', _('Currency'),
    $currencyArray, 'style="width:100%;"');
$dateFormat = array();
$dateFormat['format']  = I18N::getHTMLSelectDateFormat();
$dateFormat['minYear'] = date("Y");
$dateFormat['maxYear'] = date("Y") + 10;
$form->addElement('date', 'Flow_PaymentDate', _('Payment date'), $dateFormat);
$topArray = array(0 => _('N/A')) + TermsOfPaymentItem::getPaymentModalityConstArray();
$form->addElement('select', 'Flow_TermsOfPayment', _('Terms of payment'), $topArray, 'style="width:100%"');
$form->addElement('date', 'Flow_EditionDate', _('Expense/receipt issue date'), $dateFormat);

// valeurs par défaut pour le mode édition
if ($flow->getId() > 0 || $flow->getNumber()!='') {
    $defaultValues = FormTools::getDefaultValues($form, $flow);
    $defaultValues['Flow_PaymentDate'] = DateTimeTools::mySQLToQuickFormDate(
        $flow->getPaymentDate());
    $defaultValues['Flow_TermsOfPayment'] = $flow->getTermsOfPayment();
    $defaultValues['Flow_EditionDate'] = DateTimeTools::mySQLToQuickFormDate(
        $flow->getEditionDate());
    $defaultValues['Flow_Paid'] = 0;
    $defaultValues['Flow_Handing'] = $flow->getHanding();
    $defaultValues['Unused_GlonalHandingAmount'] = $flow->getDiscountAmount();
    $defaultValues['Flow_PieceNo'] = $flow->getPieceNo();
} else {
    require_once('SQLRequest.php');

    $curMapper = Mapper::singleton('Currency');
    $currency = $curMapper->load(array('Name'=>'Euro'));
    $defaultValues['Flow_Currency_ID'] = $currency->getId();
    $defaultValues['Flow_EditionDate'] = $defaultValues['Flow_PaymentDate'] =
        DateTimeTools::mySQLToQuickFormDate(date('Y-m-d h:i:s'));
    $defaultValues['Flow_TermsOfPayment'] = 0;
    $defaultValues['Flow_Paid'] = 0;
    $defaultValues['Flow_Handing'] = 0;
    $rs = request_flowLastPieceNo();
    $defaultValues['Flow_PieceNo'] = $rs->fields[0]+1;
}

$form->setDefaults($defaultValues);

// Validation du formulaire
$form->addRule('Flow_Number', _('Please provide a number.'),
    'required', '', 'client');
$form->addRule('Flow_FlowType',
    _('You must select an expense/receipt type.'),
    'nonzero', '', 'client');
$form->addRule('Flow_TotalHT',
    _('Field "Total excl. VAT" must be a float.'),
    'numeric', '', 'client');
$form->addRule('Flow_Paid',
    _('Field "Amount already paid" must be a float.'),
    'float', '', 'client');
$form->setJsWarnings(E_VALIDATE_FORM.' : ',_('Please correct.'));

$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());
$smarty->assign('retURL', $retURL);

// Gestion des ActorBankDetail: ceux lies au DataBaseOwner
// Le ActorBankDetail selectionne ds le champs select:
// Flow.ActorBankDetail, ou sinon Flow.FlowType.ActorBankDetail, ou sinon aucun
if ($flow->getActorBankDetailId() > 0) {
    $defaultABD = $flow->getActorBankDetailId();
}elseif (Tools::getValueFromMacro($flow, '%FlowType.ActorBankDetail.Id%') > 0) {
    $defaultABD = Tools::getValueFromMacro($flow, '%FlowType.ActorBankDetail.Id%');
}else $defaultABD = 0;

$ActorBankDetailList = getActorBankDetailList($defaultABD);
$smarty->assign('ActorBankDetailList', $ActorBankDetailList);

$pageTitle = _('Add or update expense or receipt');
$pageContent = $smarty->fetch('Flow/FlowAddEdit.html');
$js = array('js/lib-functions/FormatNumber.js',
    'js/includes/FlowAddEdit.js', 'JS_FlowAddEdit.php');

Template::ajaxPage($pageTitle, $pageContent, $js);

?>
