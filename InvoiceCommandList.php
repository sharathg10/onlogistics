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
require_once('Objects/Command.const.php');
require_once('AccountTools.php');

$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_CLIENT_TRANSPORT,
          UserAccount::PROFILE_DIR_COMMERCIAL));

SearchTools::prolongDataInSession();
//Database::connection()->debug = true;
$retURL = isset($_REQUEST['returnURL'])?$_REQUEST['returnURL']:'CommandList.php';
$redirectURL = 'InvoiceCommandList.php?CommandId=' . $_REQUEST['CommandId']
        . '&returnURL=' . $retURL;
$EditInvoice = "";

//on vient de valider une facture depuis InvoiceAddEdit, on
//ouvre le pdf mais on ne lance pas l'impression
if(isset($_REQUEST['InvoiceId']) && isset($_REQUEST['print'])
&& $_REQUEST['print']==1) {
    $EditInvoice = '<script type="text/javascript">
            window.open("EditInvoice.php?print=0&InvoiceId=' . $_REQUEST['InvoiceId'] . '",'
            . '"popback","width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no");
        </script>';
}


if (empty($_REQUEST['CommandId'])) {
    Template::errorDialog(_('Please select an order first.'), $retURL);
    exit;
}
$Command = Object::load('Command', $_REQUEST['CommandId']);
$SupplierCustomer = $Command->getSupplierCustomer();

$InvoiceMapper = Mapper::singleton('Invoice');
$InvoiceCollection = $InvoiceMapper->loadCollection(
        array('Command' => $_REQUEST['CommandId']));
if ($InvoiceCollection->getCount() == 0) {
    Template::errorDialog(_('No invoices related to this order.'), $retURL);
    exit;
}

// Reimpression d'une facture
if (isset($_POST['imprimer_x']) || isset($_POST['impression'])) {
    if (!isset($_POST['gridItems'])) {
        Template::errorDialog(_('Please select an invoice.'), $redirectURL);
        exit;
    }
    else {
        $InvoiceId = $_POST['gridItems'][0];
        // Ouverture d'un popup en arriere-plan, impression du contenu (pdf),
        // et fermeture de ce popup
        $EditInvoice = "
        <script language=\"javascript\">
        function kill() {
            window.open(\"KillPopup.html\",'popback','width=800,height=600,"
            . "toolbars=no,scrollbars=no,menubars=no,status=no');
        }
        function TimeToKill(sec) {
            setTimeout(\"kill()\",sec*1000);
        }
            var w=window.open(\"EditInvoice.php?InvoiceId=" . $InvoiceId ."&print=1\","
            . "\"popback\",\"width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no\");
            w.blur();
            //TimeToKill(22);
        </script>";
    }
}

// On n'a pas cliquer pour supprimer une facture, mais pour effectuer un reglemt
else if (isset($_REQUEST['FormSubmitted']) && $_REQUEST['FormSubmitted'] == 'true')  {
    // Valide le formulaire : creation d'un paiement pour factures selectionnees
    if (!isset($_POST['gridItems'])) {
        Template::errorDialog(_('Please select an invoice.'), $redirectURL);
        exit;
    }
    Database::connection()->startTrans();

    // Remplace la virgule par le point avant d'inserer les donnees numeriques en base
    $_POST['invoicepriceTTC'] = I18N::extractNumber($_POST['invoicepriceTTC']);
    $_POST['RemainingPaymentPriceTTC'] = I18N::extractNumber($_POST['RemainingPaymentPriceTTC']);
    //$_POST['ToPayTTC'] = I18N::extractNumber($_POST['ToPayTTC']);
    $_POST['ToHaveRemainingTTC'] = I18N::extractNumber($_POST['ToHaveRemainingTTC']);

    $Payment = Object::load('Payment');

    $Payment->setDate(date('Y-m-d H-i-s'));
    $Payment->setTotalPriceTTC($_POST['invoicepriceTTC']);
    $Payment->setModality($_POST['Modality']);
    if (isset($_POST['reference'])) {
        $Payment->setReference($_POST['reference']);
    }

    // Gestion du ActorBankDetail
    if ($_POST['Modality'] != TermsOfPaymentItem::ASSETS && $_POST['ActorBankDetail'] != 0) {
        // On incremente ou decremente en banque, selon que cmde Supplier ou Customer
        $coef = ($Command->getType() == Command::TYPE_SUPPLIER)?-1:1;
        $abd = Object::load('ActorBankDetail', $_POST['ActorBankDetail']);
        $abd->setAmount($abd->getAmount() + $coef * $Payment->getTotalPriceTTC());
        saveInstance($abd, $redirectURL);
        $Payment->setActorBankDetail($abd);
    }

    saveInstance($Payment, $redirectURL);

    reset($_POST['gridItems']);
    reset($_POST['NoLine']);
    $NewNoLineArray = array();

    foreach($_POST['NoLine'] as $val) {
        if ($val != '') {
            $NewNoLineArray[] = $val;
        }
    }

    $ToHaveTTC = $_POST['ToHaveRemainingTTC']; // montant d'avoir disponible (au max)
    $usedToHaveTTC = 0;                        // montant d'avoir utilise
    $TotalToPay = $_POST['invoicepriceTTC'];   // montant regle
    $ToHave = Object::load('ToHave', $_POST['selectedToHaveId']);
    $TotalPayed = 0;

    while(list($key, $val) = each ($_POST['gridItems'])) { // les factures cochees
        $cle = $NewNoLineArray[$key];
        $_POST['ToPayTTC'][$cle] = I18N::extractNumber($_POST['ToPayTTC'][$cle]);
        $Invoice = Object::load('Invoice', $val);
        if ($TotalToPay < $_POST['ToPayTTC'][$cle]) {
            // si on regle un montant < le ToPay de la facture
            $Invoice->setToPay($Invoice->getToPay() - $TotalToPay);
            // somme affectee a cette facture : utile pour le InvoicePayment a creer
            $Payed = $TotalToPay;
            $TotalToPay = 0;
        }
        if ($TotalToPay >= $_POST['ToPayTTC'][$cle]) {
            // si on regle un montant > le ToPay de la facture
            $Invoice->setToPay(0);
            // somme affectee a cette facture : utile pour le InvoicePayment a creer
            $Payed = $_POST['ToPayTTC'][$cle];
            $TotalToPay = $TotalToPay - $_POST['ToPayTTC'][$cle];
        }
        saveInstance($Invoice, $redirectURL);
        // classe de lien entre Invoice et Payment
        $InvoicePayment = Object::load('InvoicePayment');
        $InvoicePayment->setInvoice($Invoice);
        $InvoicePayment->setPayment($Payment);
        $InvoicePayment->setPriceTTC($Payed);
        $TotalPayed += $Payed;

        if ($_POST['selectedToHaveId'] > 0 && $ToHaveTTC > 0) {
            // A SAUVER ??? pas de chps prevu ds le SC...
            $ToHaveUsedForInvoicePmt = min($ToHaveTTC, $Payed);
            $usedToHaveTTC += $ToHaveUsedForInvoicePmt;
            $ToHaveTTC -= $ToHaveUsedForInvoicePmt;
            $InvoicePayment->setToHave($_POST['selectedToHaveId']);
        }
        saveInstance($InvoicePayment, $redirectURL);

        unset($Invoice, $InvoicePayment, $ToHaveUsedForInvoicePmt);
    } // while

    // MAJ du SupplierCustomer si utilisation d'un avoir
    if ($usedToHaveTTC > 0) {
        $ToHave->setRemainingTTC($ToHave->getRemainingTTC() - $usedToHaveTTC);
        saveInstance($ToHave, $redirectURL);
        $SupplierCustomer->setToHaveTTC($SupplierCustomer->getToHaveTTC() - $usedToHaveTTC);
    } else {
        $SupplierCustomer->setUpdateIncur($SupplierCustomer->getUpdateIncur() - $TotalPayed);
    }
    saveInstance($SupplierCustomer, $redirectURL);

    //mise a jour de l'etat de la commande
    $Remaining = $_POST['RemainingPaymentPriceTTC'] - $_POST['invoicepriceTTC'];

    // Si montant regle < montant qui est du,
    // ou si toutes les lignes de cmde ne sont pas facturees
    if ($Remaining != 0
            || ($Command instanceof ProductCommand && $Command->isFactured() == 0)) {
        $Command->setState(Command::REGLEMT_PARTIEL);
    }
    else {
        $Command->setState(Command::REGLEMT_TOTAL);
    }
    saveInstance($Command, $redirectURL);
    //mise a jour de l'encours courant
    require_once('ProductCommandTools.php');
    commandDeBlockage($Command, $Payment->getTotalPriceTTC());

    /**  commit de la transaction  **/
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        die('erreur sql');
    }
    Database::connection()->completeTrans();

    Tools::redirectTo($retURL);
    exit;
}


// Affichage
$grid = new Grid();
$grid->paged = false;
$grid->displayCancelFilter = false;
$grid->withNoSortableColumn = true;

// Les chps hidden servent a afficher le titre dans le popup des Avoirs
if($Command instanceof ChainCommand) {
    $detailUrl = 'ChainCommandInvoiceDetail.php';
} elseif ($Command instanceof CourseCommand) {
  	$detailUrl = 'CourseInvoiceDetail.php';
} else {
    $detailUrl = 'InvoiceDetail.php';
}

$grid->NewAction('Delete', array('TransmitedArrayName' => 'docId',
        'EntityType' => 'Invoice',
        'Query'=>'cmdID=' . $_REQUEST['CommandId'] . '&retURL=' . $retURL,
        'ReturnURL' => $redirectURL,
        'Profiles' => array(UserAccount::PROFILE_ROOT))); // ROOT only

$grid->NewColumn('FieldMapper', _('Invoice number'), array(
        'Macro' => '<a href="'.$detailUrl.'?cmdId='
        . $_REQUEST['CommandId'] . '&InvoiceId=%Id%&returnURL='.$retURL.'">%DocumentNo%</a>
        <input type="hidden" name="DocumentNo[]" value="%DocumentNo%" />
        <input type="hidden" name="SelectedDocumentNo[]" value="" />
        <input type="hidden" name="PriceTTC[]" value="%TotalPriceTTC%" />
        <input type="hidden" name="ToPayTTC[]" value="%ToPay%" />'
         ));
$grid->NewColumn('FieldMapper', _('Order'), array('Macro' => '%Command.CommandNo%'));
$grid->NewColumn('FieldMapper', _('Edition date'),
        array('Macro' => '%EditionDate|formatdate@DATE_SHORT%'));
$grid->NewColumn('FieldMapperWithTranslation', _('Deadline for payment'),
        array('Macro' => '%PaymentDate|formatdate@DATE_SHORT%',
              'TranslationMap' => array('00/00/00'=>'N/A')));
$grid->NewColumn('MultiPrice', _('Amount incl. VAT'), array('Method'=>'getTotalPriceTTC'));
$grid->NewColumn('MultiPrice', _('Amount to pay incl. VAT'),  array('Method'=>'getToPay'));

if ($grid->isPendingAction()) {
    $collection = false;
    $dispatchResult = $grid->dispatchAction($InvoiceCollection);
    if (Tools::isException($dispatchResult)) {
        Template::errorDialog($dispatchResult->getMessage(), $redirectURL, BASE_POPUP_TEMPLATE);
        exit;
    } else {
        Template::page(_('List of invoices'), $dispatchResult);
    }
}
$InvoiceCommandListGrid = $grid->render(
        $InvoiceCollection, false, array(), array('EditionDate' => SORT_ASC),
        'Invoice/InvoiceCommandListGrid.html');
$JSRequirements = array('js/lib-functions/FormatNumber.js',
        'js/includes/InvoiceCommandList.js');

$cur = $Command->getCurrency();
$curStr = $cur instanceof Currency?$cur->getSymbol():'&euro;';

$CommandTotalPriceTTC = $Command->getTotalPriceTTC();

// Calcul de ce qui reste a regler (soit total de ce qui reste a regle): (nelle methode)
$RemainingPaymentPriceTTC = 0;  // ce qui reste a regler
$InvoiceTotalPriceTTC = 0;      // ce qui est facture
$HasPayment = 0;
if ($InvoiceCollection instanceof Collection && ($InvoiceCollection->getCount() > 0) ) {
    for($i = 0; $i < $InvoiceCollection->getCount(); $i++){
        $item = $InvoiceCollection->getItem($i);
        $RemainingPaymentPriceTTC += $item->getToPay();
        $InvoiceTotalPriceTTC += $item->getTotalPriceTTC();
        //recupere la collection des payments pour la facture associee
        $PaymentCollection = $item->getPaymentCollection();
        if ($PaymentCollection instanceof Collection
                && ($PaymentCollection->getCount() > 0)) {
            $HasPayment = 1;
        }
    }
}

if (Tools::isException($InvoiceCommandListGrid)) {
    Template::errorDialog($InvoiceCommandListGrid->getMessage(), $_SERVER['PHP_SELF']);
    exit;
}
else {
    //Affichage du formulaire avec smarty
    $smarty = new Template();

    // On determine s'il y a des Avoirs utilisables pour regler
    $SupplierCustomer = $Command->getSupplierCustomer();
    $smarty->assign('SupplierCustomerId', $SupplierCustomer->getId());
    $ToHaveExist = ($SupplierCustomer->getToHaveTTC() == 0)?'Aucun':'Oui';

    $smarty->assign('returnURL', $retURL);
    $smarty->assign('CommandId', $_REQUEST['CommandId']);
    $smarty->assign('Currency', $curStr);
    $smarty->assign('InvoiceTotalPriceTTC',
            I18N::formatNumber($InvoiceTotalPriceTTC));
    $smarty->assign('RemainingPaymentPriceTTC',
            I18N::formatNumber($RemainingPaymentPriceTTC));
    $smarty->assign('InvoiceCommandListGrid', $InvoiceCommandListGrid);
    $CommandState = $Command->getState();
    $smarty->assign('HiddenCommandState', $CommandState);
    $modalities = TermsOfPaymentItem::getPaymentModalityConstArray();
    $smarty->assign('ModalityList',
            join("\n\t\t", FormTools::writeOptionsFromArray($modalities)));
    $smarty->assign('CmdType', $Command->getType());  // Le type de la commande
    $smarty->assign('ToHaveExist', $ToHaveExist);

    // Gestion des ActorBankDetail: ceux lies au DataBaseOwner
    $ActorBankDetailList = getActorBankDetailList($Command->getActorBankDetailId());
    $smarty->assign('ActorBankDetailList', $ActorBankDetailList);

    if ($HasPayment == 1) {
        $smarty->assign('PaymentList', 1);
    }
    $smarty->assign('isCommercialConnected',
            (in_array(
                    $auth->getProfile(),
                    array(UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_DIR_COMMERCIAL)))?
            1:0);
    $smarty->assign('$isClientTransporteurConnected',
            ($auth->getProfile()==UserAccount::PROFILE_CLIENT_TRANSPORT)?1:0);

    Template::page(
        _('List of invoices and execution of a payment'),
        $EditInvoice.$smarty->fetch('Invoice/InvoiceCommandList.html'),
        $JSRequirements
    );
}
?>
