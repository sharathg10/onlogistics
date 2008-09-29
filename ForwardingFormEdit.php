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
require_once('lib/Objects/MovementType.const.php');
require_once('ExecutedMovementTools.php');
require_once('lib/Objects/Actor.inc.php');
// }}}
// sessions + auth {{{
$session = Session::singleton();
$auth = Auth::Singleton();
$auth->checkProfiles(array(
    UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, 
    UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR,
    UserAccount::PROFILE_TRANSPORTEUR));

$lemIDs = SearchTools::requestOrSessionExist('lemIDs');
// }}}
// impression du BE {{{
if(isset($_REQUEST['print']) && $_REQUEST['print']==1) {
    if(isset($_REQUEST['showMsg']) && $_REQUEST['showMsg']==1) {
        Template::infoDialog(_('The document was created.'), 
            'ForwardingFormEdit.php?print=1&doc=' . $_REQUEST['doc'],
            BASE_POPUP_TEMPLATE);
        exit();
    }
    if (!isset($_REQUEST['doc'])) {
        Template::errorDialog(E_MSG_TRY_AGAIN, 'javascript:window.close()', 
            BASE_POPUP_TEMPLATE);
    	exit();
    }
    $forwardingForm = Object::load('ForwardingForm', $_REQUEST['doc']);
    if (!($forwardingForm instanceof ForwardingForm)) {
        Template::errorDialog(E_MSG_TRY_AGAIN, 'javascript:window.close()', 
            BASE_POPUP_TEMPLATE);
    	exit();
    }
    require_once('GenerateDocument.php');
    $reedit = isset($_REQUEST['reedit']) ? $_REQUEST['reedit'] : 0;
    generateDocument($forwardingForm, $reedit);
    exit();
}
// }}}
// création du BE {{{
if (isset($_REQUEST['FormSubmitted']) && $_REQUEST['FormSubmitted'] == 'true') {
    // si pas de lem, inutile d'aller plus loin
    if(!$lemIDs) {
        Template::errorDialog(E_MSG_TRY_AGAIN, 
            'javascript:window.close()', BASE_POPUP_TEMPLATE);
        exit();
    }
    require_once('InvoiceItemTools.php');
    Database::connection()->startTrans();
    // recherche du supplier customer {{{
    if(!isset($_SESSION['supplierCustomer'])) {
        $supCust = Object::load('SupplierCustomer', array(
            'Supplier'=>$auth->getActorId(),
            'Customer'=>$_POST['cmdDestinator']));
        if (!($supCust instanceof SupplierCustomer)) {
            $customer = Object::load('Actor', $_POST['cmdDestinator']);
            $supCust = Object::load('SupplierCustomer');
            $supCust->setSupplier($auth->getActor());
            $supCust->setCustomer($customer);
        }
        $session->register('supplierCustomer', $supCust, 2);
    } else {
        $supCust = $_SESSION['supplierCustomer'];
    }
    // }}}
    // création du ForwardingForm {{{
    if(!isset($_SESSION['forwardingForm'])) {
        $forwardingForm = Object::load('ForwardingForm');
        $forwardingForm->setEditionDate(date('Y-m-d H:i:s'));
        $forwardingForm->setCommandNo($_POST['CommandNo']);
        $forwardingForm->setTransporter($_POST['Transporter']);
        $forwardingForm->setConveyorDepartureSite($_POST['ConveyorDepartureSite']);
        $forwardingForm->setConveyorArrivalSite($_POST['ConveyorArrivalSite']);
        $forwardingForm->setSupplierCustomer($supCust);
        $forwardingForm->setDestinatorSite($_POST['cmdDestinatorSite']);
        $forwardingForm->setDocumentModel($forwardingForm->findDocumentModel());

        $session->register('forwardingForm', $forwardingForm, 2);
    } else {
        $forwardingForm = $_SESSION['forwardingForm'];
    }
    $forwardingForm->setDocumentNo(
        generateDocumentNo('BE', 'ForwardingForm', $forwardingForm->generateId()));
    // }}}
    // création des ForwardingFormPacking à partir des Product {{{
    if(isset($_POST['Product_ID']) && (!isset($_REQUEST['force']) || !$_REQUEST['force'])) {
        $date = date('Y-m-d H:i:s');
        // recherche de l'emplacement de destination des pdt consigné {{{
        $site = $forwardingForm->getConveyorArrivalSite();
        $site = $site instanceof Site ? $site : $_POST['cmdDestinatorSite'];
        $locationCol = Object::loadCollection('Location',
            array('Store.StorageSite' => $site));
        if($locationCol->getCount() == 0) {
            Template::confirmDialog(
                _('No location was found for this addressee, deposited packings will not be taken into account, continue ?'),
                $_SERVER['PHP_SELF'] . '?FormSubmitted=true&force=1',
                $_SERVER['PHP_SELF'], BASE_POPUP_TEMPLATE);
            exit();
        }
        $locationEntry = $locationCol->getItem(0);
        // }}}
        // création des ForwardingFormPacking et des mouvements {{{
        foreach($_POST['Product'] as $key=>$pdtId) {
            // création ForwardingFormPacking {{{
            $product = Object::load('Product', $pdtId);
            $TotalQty = 0;
            if (isset($_POST['ProductQty']) && is_array($_POST['ProductQty'])) {
                foreach($_POST['ProductQty'][$key] as $qty) {
                    $TotalQty += $qty;
                }
            }
            $o = new ForwardingFormPacking();
            $o->setProduct($product);
            $o->setCoverType(0);
            $o->setQuantity($TotalQty);
            $o->setForwardingForm($forwardingForm);
            saveInstance($o, 'javascript:window.close()');
            // }}}
            // création exm de sortie {{{
            $EXM_Out = new ExecutedMovement();
            $EXM_Out->setStartDate($date);
            $EXM_Out->setEndDate($date);
            $EXM_Out->setRealProduct($product);
            $EXM_Out->setState(ExecutedMovement::EXECUTE_TOTALEMENT);
            $EXM_Out->setType(SORTIE_DEPLACEMT);
            $EXM_Out->setRealQuantity($TotalQty);
            $EXM_Out->setProductVirtualQuantity();
            saveInstance($EXM_Out, 'javascript:window.close()');
            // }}}
            // création exm d'entré {{{
            $EXM_In = new ExecutedMovement();
            $EXM_In->setStartDate($date);
            $EXM_In->setEndDate($date);
            $EXM_In->setRealProduct($product);
            $EXM_In->setState(ExecutedMovement::EXECUTE_TOTALEMENT);
            $EXM_In->setType(ENTREE_DEPLACEMT);
            $EXM_In->setRealQuantity($TotalQty);
            $EXM_In->setProductVirtualQuantity();
            saveInstance($EXM_In, 'javascript:window.close()');
            // }}}
            // création des lem de sortie et d'entrée, maj des lpq {{{
            if (!isset($_POST['ProductLocation']) || !is_array($_POST['ProductLocation'])) {
                continue;
            }
            foreach($_POST['ProductLocation'][$key] as $index=>$locationExit) {
                $qty = $_POST['ProductQty'][$key][$index];
                if($qty==0) {
                    continue;
                }
                // création lem de sortie {{{
                $LEM_out = new LocationExecutedMovement();
                $LEM_out->setExecutedMovement($EXM_Out);
                $LEM_out->setDate($date);
                $LEM_out->setQuantity($qty);
                $LEM_out->setLocation($locationExit);
                $LEM_out->setProduct($product);
                $LEM_out->setForwardingForm($forwardingForm);
                saveInstance($LEM_out, 'javascript:window.close()');
                // }}}
                // création lem d'entré {{{
                $LEM_In = new LocationExecutedMovement();
                $LEM_In->setExecutedMovement($EXM_In);
                $LEM_In->setDate($date);
                $LEM_In->setQuantity($qty);
                $LEM_In->setLocation($locationEntry);
                $LEM_In->setProduct($product);
                $LEM_In->setForwardingForm($forwardingForm);
                saveInstance($LEM_In, 'javascript:window.close()');
                // }}}
                // maj LPQ {{{
                $LPQ_out = Object::load('LocationProductQuantities', array(
                    'Location'=>$locationExit, 
                    'Product'=>$product->getId()));
                $LPQ_in = getLocationProductQuantities(
                    $product, $locationEntry, ENTREE, 'DocumentList.php');
                updateLPQQuantity($LPQ_out, $qty, SORTIE, 'DocumentList.php');
                updateLPQQuantity($LPQ_in, $qty, ENTREE, 'DocumentList.php');
                // }}}
            }
            // }}}
        }
        // }}}
    }
    // }}}
    // création des ForwardingFormPacking à partir des CoverType {{{
    if(isset($_POST['CoverType_ID'])) {
        foreach($_POST['CoverType_ID'] as $key) {
            $o = new ForwardingFormPacking();
            $o->setCoverType($_POST['CoverType'][$key]);
            $o->setQuantity($_POST['CoverTypeQty'][$key]);
            $o->setForwardingForm($forwardingForm);
            saveInstance($o, 'javascript:window.close()');
        }
    }
    // }}}
    // màj des LocationExecutedMovement {{{
    foreach ($lemIDs as $lemID) {
        $lem = Object::load('LocationExecutedMovement', $lemID);
        $lem->setForwardingForm($forwardingForm);
        saveInstance($lem, 'javascript:window.close()');
    }
    // }}}
    //  Commit de la transaction {{{
    saveInstance($supCust, 'javascript:window.close()');
    saveInstance($forwardingForm, 'javascript:window.close()');
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_ERROR_IMPOSSIBLE_ACTION, $_GET['returnURL']);
        exit;
    }
    Database::connection()->completeTrans();
    unset($_SESSION['forwardingForm']);
    unset($_SESSION['supplierCustomer']);
    unset($_SESSION['lemIDs']);
    // }}}
    // impression du document
    Tools::redirectTo('ForwardingFormEdit.php?showMsg=1&print=1&doc=' .
        $forwardingForm->getId());
    exit();
}
// }}}
// vérification des lem sélectionnés {{{

$lemArray = array();
$cancelledLEM = array();
$forwardingFormArray = array();
$entryLEM = array();
$CmdNo = array();
// check qu'aucun lem n'est déjà associé à un ForwardingForm
// et qu'aucun lem n'est annulé
foreach ($lemIDs as $lemID) {
    $lem = Object::load('LocationExecutedMovement', $lemID);
    $forwardingForm = $lem->getForwardingForm();
    if($forwardingForm instanceof ForwardingForm) {
        $lemArray[] = $lemID;
        if(!in_array($forwardingForm->getDocumentNo(), $forwardingFormArray)) {
            $forwardingFormArray[] = $forwardingForm->getDocumentNo();
        }
    }
    if($lem->getCancelled()) {
        $cancelledLEM[] = $lemID;
    }
    if(!Tools::getValueFromMacro($lem, '%ExecutedMovement.Type.EntrieExit%')) {
        $entryLEM[] = $lemID;
    }
    $cmd = Tools::getValueFromMacro($lem, '%ExecutedMovement.ActivatedMovement.ProductCommand.CommandNo%');
    if($cmd!='0') {
        $CmdNo[] = $cmd;
    }
}
if(!empty($lemArray)) {
    $msg = _('Movements number %s already appear on forwarding forms number %s. Please correct your selection.');
    Template::errorDialog(
        sprintf($msg, implode(', ', $lemArray), implode(', ', $forwardingFormArray)),
        'javascript:window.close();', BASE_POPUP_TEMPLATE);
    exit();
}
if (!empty($cancelledLEM)) {
	$msg = _('Movements %s were cancelled and cannot appear in forwarding form. Please correct your selection.');
    Template::errorDialog(
	   sprintf($msg, implode(', ', $cancelledLEM)),
	   'javascript:window.close();', BASE_POPUP_TEMPLATE);
	exit();
}
if (!empty($entryLEM)) {
    Template::errorDialog(
	   _('You did not select stock entries, forwarding form printing is impossible. Please correct your selection.'),
	   'javascript:window.close();', BASE_POPUP_TEMPLATE);
	exit();
}
// on stocke les lemID en session
$session->register('lemIDs', $lemIDs, 2);
// }}}
// construction du formualire {{{
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once ('HTML/QuickForm.php');

$smarty = new Template();
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('ForwardingFormEdit.php', 'post');

// champs cachés
$form->addElement('hidden', 'productSelectUpdate', '');
$form->addElement('hidden', 'SiteOwner', $auth->getActorId(), 'id="SiteOwner"');
$form->addElement('hidden', 'FormSubmitted', 0, 'id="FormSubmitted"');

// destinataire
$actorArray = SearchTools::createArrayIDFromCollection('Actor', 
    array('Active'=>1, 'Generic'=>0), _('Select addressee'), 'Name', array('Name'=>SORT_ASC)
);
$form->addElement('select', 'cmdDestinator', _('Addressee'),
    $actorArray, 'onchange="fw.ajax.updateSelect(\'cmdDestinator\', ' .
    '\'destinatorSite\', \'Site\', \'Owner\', true);" ' . 
    'style="width:100%" id="cmdDestinator"'
);
// site destinataire
$form->addElement('select', 'cmdDestinatorSite', _('Addressee site'), array(),
    'id="destinatorSite" style="width:100%;"');

// numéro de commande
if(count($CmdNo) > 0) {
    $CmdNo = $CmdNo[0];
    $opts = 'class="ReadOnlyField" readonly style="width:98%;"';
} else {
    $CmdNo = '';
    $opts = 'style="width:100%;"';
}
$form->addElement('text', 'CommandNo', _('Order number'), $opts);

// transporteur
$carrierCol = getCarrierActorCollection();
$carrierArray = array('##' => _('Select carrier'));
$count = $carrierCol->getCount();
for($i=0 ; $i<$count ; $i++) {
    $carrier = $carrierCol->getItem($i);
    $carrierArray[$carrier->getId()] = $carrier->toString();
}
$form->addElement('select', 'Transporter', _('Carrier'), $carrierArray, 
    'onchange="fw.ajax.updateSelect(\'CarrierSelect\', ' .
    '\'ConveyorDepartureSite\', \'Site\', \'Owner\');'.
    'fw.ajax.updateSelect(\'CarrierSelect\', \'ConveyorArrivalSite\', ' .
    '\'Site\', \'Owner\');" id="CarrierSelect" style="width:100%;"'
);
// sites transporteur
$form->addElement('select', 'ConveyorDepartureSite', 
    _('Carrier departure site'), $carrierArray, 'id="ConveyorDepartureSite" ' .
    'style="width:100%;" onchange="loadUsableLPQ();"'
);
$form->addElement('select', 'ConveyorArrivalSite', _('Carrier arrival site'), 
    $carrierArray, 'id="ConveyorArrivalSite" style="width:100%;"'
);
// valeurs par défaut
$form->setDefaults(array('CommandNo' => $CmdNo));
// }}}
// Affichage du formulaire {{{
$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());

$content = $smarty->fetch('ForwardingForm/ForwardingFormEdit.html');
$title = _('Print forwarding form');
$js = array('JS_AjaxTools.php', 'js/includes/ForwardingForm.js');
Template::page($title, $content, $js, array(), BASE_POPUP_TEMPLATE);
// }}}
?>
