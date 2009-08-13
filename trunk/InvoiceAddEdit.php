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
require_once('Objects/Command.php');
require_once('Objects/Command.const.php');
require_once('ProductCommandTools.php');
require_once('InvoiceItemTools.php');
require_once('Objects/ActivatedMovement.php');
require_once('Objects/LocationExecutedMovement.php');
require_once('Objects/TVA.inc.php');
require_once('LangTools.php');
require_once('AlertSender.php');
//Database::connection()->debug = 1;

$auth = Auth::Singleton();
$auth->checkProfiles(
        array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
              UserAccount::PROFILE_AERO_ADMIN_VENTES));

SearchTools::prolongDataInSession();
$InvoiceMapper = Mapper::singleton('Invoice');

$retURL = isset($_GET['returnURL'])?$_GET['returnURL']:'CommandList.php';

// On va creer une facture
if (isset($_REQUEST['FormSubmitted']) && $_REQUEST['FormSubmitted'] == 'true') {
    $errorUrl = 'InvoiceAddEdit.php?CommandId=' . $_POST['HiddenCommandID'];
    if (empty($_POST['gridItems'])) {
        Template::errorDialog(
                _('Billing impossible.'), $errorUrl);
        exit;
    }
    /*  Ouverture de la transaction  */
    Database::connection()->startTrans();

    $Command = Object::load('Command', $_POST['HiddenCommandID']);
    // Creation de l'Invoice pour commencer
    $Invoice = createInvoice($_POST['HiddenCommandID'], $errorUrl);
    saveInstance($Invoice, $errorUrl); // necessaire ici

    $InvoiceItemCollection = createInvoiceItems(
            $Invoice, $Command, $_POST['gridItems'], $_POST['hdg'],
            $_POST['ProductId'], $_POST['qty'], $_POST['HiddenTvaId'],
            $_POST['PriceHT'], $_POST['PdtCmdItemId']);
    $Invoice->setInvoiceItemCollection($InvoiceItemCollection);
    $Invoice->setTotalPriceHT($_POST['totalpriceHT']);
	$Invoice->setTotalPriceTTC($_POST['totalpriceTTC']);
	if(isset($_POST['IAEComment'])) {
        $Invoice->setComment($_POST['IAEComment']);
    }
	$Invoice->setTvaSurtaxRate($_POST['tvaSurtaxRate']);
	$Invoice->setFodecTaxRate($_POST['fodecTaxRate']);
	$Invoice->setTaxStamp($_POST['taxStamp']);

    if (isset($_POST['Instalment']) && $_POST['Instalment'] != 0) {
        $PaidInstalment = I18N::extractNumber($_POST['Instalment']);
        if ($Invoice->getTotalPriceTTC() <= $PaidInstalment) {
            //TotalPriceTTC de la facture est < a l'accompte
            $Invoice->setToPay(0);
        } else {
            $Invoice->setToPay($Invoice->getTotalPriceTTC() - $PaidInstalment);
        }
    } else {
        $Invoice->setToPay($Invoice->getTotalPriceTTC());
    }
    // calcule la commission du commercial
    $Invoice->updateCommercialCommission();

    // on maj les DeliveryOrder de la commande s'ils existent
    updateMixedObjects($Command, $Invoice, $errorUrl);
    saveInstance($Invoice, $errorUrl);

    // mise à jour de l'encours courant
    require_once('ProductCommandTools.php');
    $Alert = commandBlockage($Command, $Invoice);

    /*  Commit de la transaction  */
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_ERROR_IMPOSSIBLE_ACTION, $_GET['returnURL']);
        exit;
    }
    Database::connection()->completeTrans();
	// Seulement apres la transaction, on envoie l'alerte si necessaire
	if (!Tools::isEmptyObject($Alert)) {
	    $Alert->send();
	}
	$spc = $Invoice->getSupplierCustomer();
	$url = 'InvoiceCommandList.php?CommandId=' . $_POST['HiddenCommandID']
	       . '&returnURL=' . $retURL;


    /*  Redirect vers la liste des factures de la commande  */
    $result = true;
    if ($_POST['HiddenCmdType'] != 'Client') {
        // Commande fourniseur: pas d'impression ni d'envoi par mail
        Tools::redirectTo($url);
	    exit;
    } else if (Tools::isEmptyObject($spc)) {
        // Commande client
	    Tools::redirectTo($url . '&InvoiceId=' . $Invoice->getId() . '&print=1');
	    exit;
	} else if ($spc->getInvoiceByMail() == SupplierCustomer::INVOICE_BY_MAIL_NONE) {
	    // Pas d'envoi de mail: simplement edition de la facture
	    Tools::redirectTo($url . '&InvoiceId=' . $Invoice->getId() . '&print=1');
	    exit;
    } else if ($spc->getInvoiceByMail() == SupplierCustomer::INVOICE_BY_MAIL_ALERT) {
	    // Envoi de mail: simplement une alerte: la facture est disponible
        // Si pas d'envoi de mail car pas de userAccount parametre pour
        // les recevoir: cas gere via l'exception retournee par MailTools::send()
        $result = AlertSender::send_ALERT_INVOICE_TO_DOWNLOAD($Invoice, $auth->getUser());
	} else if ($spc->getInvoiceByMail() == SupplierCustomer::INVOICE_BY_MAIL_YES) {
	    // Envoi de mail: la facture en piece jointe
        // Si pas d'envoi de mail car pas de userAccount parametre pour
        // les recevoir: cas gere via l'exception retournee par MailTools::send()
        $result = AlertSender::send_ALERT_INVOICE_BY_MAIL($Invoice, $auth->getUser());
	}
    if (Tools::isException($result)) {
        Template::errorDialog(
            _('Invoice could not be sent by email because no user ')
            . _('is set to receive it.'),
            $url
        );
    } else {
        Template::infoDialog(_('Invoice was successfully sent by e-mail.'), $url);
    }
	exit;
} //fin du traitement


/*  1ere arrivee sur la page: affichage de l'ecran de facturation  */
if (empty($_GET['CommandId'])) {
    Template::errorDialog(_('Please select an order for billing.'),
            $_GET['returnURL']);
    exit;
}
// Si n commandes selectionnees, on enregistre les factures en boucle
if (is_array($_GET['CommandId']) && count($_GET['CommandId']) > 1) {
    //Database::connection()->debug = true;
    chargeSeveralCommands($_GET['CommandId']);
    exit;
}
$Id = $_GET['CommandId'][0];  // Une seule Commande selectionnee
$Command = Object::load('Command', $Id);
$CommandItemCollection = $Command->getCommandItemCollection();
if ($Command->getState() == Command::BLOCAGE_CDE) {
    Template::errorDialog(
            _('Order is locked because outstanding debts amount exceeds its maximum value.'),
			$_GET['returnURL']);
    exit;
}
// La commande doit etre facturable
if (!$Command->isBillable()) {
    Template::errorDialog(_('This order is not billable.'), $_GET['returnURL']);
    exit;
}

$Expeditor = $Command->getExpeditor();
$Destinator = $Command->getDestinator();
$CmdType = $Command->getType();

$sp = $Command->getSupplierCustomer();
$hasTVA = (($sp instanceof SupplierCustomer && $sp->getHasTVA()) || $Command->isWithTVA());
// Gestion de la tva surtaxee
$hasTvaSurtax = $sp->getHasTvaSurtax();
$tvaSurtaxRate = ($hasTvaSurtax)?Preferences::get('TvaSurtax', 0):0;

$currency = $Command->getCurrency();
$curStr = $currency instanceof Currency?$currency->getSymbol():'&euro;';

$LemCommandItemCollection = new Collection();

for($i = 0; $i < $CommandItemCollection->getCount(); $i++) {
    $CommandItem = $CommandItemCollection->getItem($i);
    if (!method_exists($CommandItem, 'getActivatedmovement')) {
        continue;
    }

    $AMovement = $CommandItem->getActivatedmovement();

    if(!($AMovement instanceof ActivatedMovement)) {
        // tout ce qui suit est basé sur l'ACM.
        continue;
    }
    // si un ActivatedMovement a deja ete facture
    if ($AMovement->getHasBeenFactured() == 1) {
        continue;
    }

	//  Etat ActivatedMovement::CREE: on prend l'info ds CommandItem
	if (!in_array($AMovement->getState(),
                array(ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT, ActivatedMovement::ACM_EXECUTE_TOTALEMENT))) {
        $LemCommandItemCollection->setItem($CommandItem);
   	}

    // Si un activatedMovement a ete passe en execution:
    // l'info est ds le LEM non factures
    else {
        $EXMovement = $AMovement->getExecutedMovement();
        $LEMCollection = $EXMovement->getLocationExecutedMovementCollection(
				array('InvoiceItem' => 0));

        if (!Tools::isEmptyObject($LEMCollection)) {
			$ProductIdArray = array();  // tableau d'Id de Product
			$lemCount = $LEMCollection->getCount();
			for($j = 0;$j < $lemCount; $j++) {
                $LEM = $LEMCollection->getItem($j);
                if (0 < $LEM->getCancelled()) { // si mvt annulateur
                    continue;
                }
				$Product = $LEM->getProduct();
				$ProductId = $Product->getId();
				// si ce Product deja traite (1 invoiceItem par Product)
				// Note au 30/08/2007: il ne faudrait pas add les qty ds ce cas??
				// Fait ainsi pour la facturation multiple
				if (in_array($ProductId, $ProductIdArray)) {
				    continue;
				}

				$ProductIdArray[] = $ProductId;
                // Qte mvtee, non reintegree, et non facturee
				$Quantity = $EXMovement->getProductMovedQuantity($Product, 0);
				if ($Quantity == 0) {  // si tout a ete reintegre
				    continue;
				}

				$LemCommandItem = Object::load('ProductCommandItem');
		        $LemCommandItem->setProduct($Product);
		        $LemCommandItem->setCommand($Command);
		        $LemCommandItem->setActivatedMovement($AMovement);
				$LemCommandItem->setQuantity($Quantity);
		        $LemCommandItem->setHanding($CommandItem->getHanding());
                // si c'est le produit commande
				if ($ProductId == $CommandItem->getProductId()) {
				    $LemCommandItem->setPriceHT($CommandItem->getPriceHT());
                    if ($hasTVA) {
                        $LemCommandItem->setTVA($CommandItem->getTVA());
                    }
					$LemCommandItem->setPromotion($CommandItem->getPromotion());
				} else {
                    // on va chercher le prix ds la table Product
                    // (ou ActorProduct si cmde fournisseur)
					$ProductPrice = ($CmdType == Command::TYPE_CUSTOMER)?
                        $Product->getPriceByActor($Destinator):
                        $Product->getUVPrice($Expeditor);

					$LemCommandItem->setPriceHT($ProductPrice);
					if ($hasTVA) {
					    $LemCommandItem->setTVA($Product->getTVA());
					}

					// Si commande client, il peut y avoir une Promotion et
                    // Destinator est le Customer
					if ($CmdType == Command::TYPE_CUSTOMER) {
					    $Promotion = $Product->getPromotion($Destinator,
                            								 $LEM->getDate());
						if (!Tools::isEmptyObject($Promotion)) {
						    $LemCommandItem->setPromotion($Promotion);
						}
					}
				}
				$LemCommandItemCollection->setItem($LemCommandItem);
				unset($LemCommandItem, $Product);
			}
		}
	}
    unset($CommandItem, $AMovement, $EXMovement);
} // for

// si il n'y a plus de lignes a facturer on redirige vers la liste
// des factures de la commande
if ($LemCommandItemCollection->getCount() == 0) {
    Tools::redirectTo('InvoiceCommandList.php?CommandId=' . $Id . '&returnURL=' . $retURL);
    exit;
}
// Pour l'affichage du detail par taux de tva
$tvaRateArray = array();
if ($hasTVA) {
    $count = $LemCommandItemCollection->getCount();
    for($i = 0; $i < $count; $i++){
    	$CommandItem = $LemCommandItemCollection->getItem($i);
        $tvaRate = $CommandItem->getRealTvaRate();
        if (!in_array($tvaRate, $tvaRateArray) && $tvaRate != 0) {
            $tvaRateArray[I18N::formatNumber($tvaRate)] = $tvaRate;
        }
    }
}

// Check des avoirs de la commande ( Instalment )

$HasInstalment = 0 ;
$TotalInstalments = $Command->getTotalInstalments() ;
if ($TotalInstalments > 0 ) $HasInstalment = 1;

$NoInvoice = 0;
// si cette cde a deja des factures
$InvoiceCollection = $InvoiceMapper->loadCollection(array('Command' => $Id));
if ($InvoiceCollection->getCount() == 0) { // pas de factures
    $NoInvoice = 1;
}

// pour l'affichage du grid
$grid = new Grid();

$grid->NewColumn('InvoiceAddEditBaseReference', _('Reference'),
        array('cmdType' => $CmdType));
// Si commande client, on affiche aussi la ref. client si elle existe
if ($CmdType == Command::TYPE_CUSTOMER) {
    $grid->NewColumn('CallBack', _('Customer reference'),
        array('Func' => 'getReferenceByActor', 'Args' => array($Destinator),
              'Macro' => 'Product'));
}
$grid->NewColumn('FieldMapper', _('Designation'), array('Macro' => '%Product.Name%'));
$grid->NewColumn('MultiPrice', _('Basis excl. VAT'), array('Method' => 'getPriceHT'));

$listHiddenFields = '<input type="hidden" name="ProductId[]" value="%Product.Id%">'
    . '<input type="hidden" name="PriceHT[]" value="%PriceHT%">'
	. '<input type="hidden" name="ActivatedMvt[]" value="%ActivatedMovementId%">'
	. '<input type="hidden" name="HiddenActMvtState[]" value="%ActivatedMovement.State%">'
	. '<input type="hidden" name="PdtCmdItemId[]" value="%Id%">';

if ($hasTVA) {
    $grid->NewColumn('FieldMapper', _('% VAT'),
            array('Macro' => '%RealTvaRate|formatnumber%'));
    $listHiddenFields .= '<input type="hidden" name="HiddenTVA[]" value="%RealTvaRate%">'
            . '<input type="hidden" name="HiddenTvaId[]" value="%TVA.Id%">'
            . '<input type="hidden" name="TTVA[]" value="0">';
} else {
    $listHiddenFields .= '<input type="hidden" name="HiddenTVA[]" value="0">'
            . '<input type="hidden" name="HiddenTvaId[]" value="0">'
            . '<input type="hidden" name="TTVA[]" value="0">';
}

$grid->NewColumn('FieldMapper', _('Qty'),
    array('Macro' => '<input type="text" name="qty[]" size="5" value="%quantity%"'
        . ' onKeyUp="RecalculateItemTotal(getItemIndex(this));RecalculateTotal();">'
        . '%Product.MeasuringUnit%'));
/*if ($CmdType == Command::TYPE_CUSTOMER) {
    $grid->NewColumn('ProductCommandPromotion', _('Offer on sale ') . $curStr,
        array('customer' => $Destinator, 'forInvoice' => 1));
}*/
$grid->NewColumn('ProductCommandPromotion', _('Offer on sale ') . $curStr,
        array('Enabled' => ($CmdType == Command::TYPE_CUSTOMER),
              'customer' => $Destinator, 'forInvoice' => 1));

// cette colonne contient les hiddens
$grid->NewColumn('FieldMapper', _('Disc.'),
        array('Macro' => '<input type="text" name="hdg[]" size="5" value="%Handing%" '
		  . 'onKeyUp="RecalculateItemTotal(getItemIndex(this));RecalculateTotal();">'
          . $listHiddenFields));
$grid->NewColumn('InvoiceProductPrice', _('Unit price excl. VAT'));

$grid->NewColumn('FieldMapperWithTranslation', _('Deliv.'),
    array('Macro' => '%ActivatedMovement.State%',
          'TranslationMap' => array(
	          ActivatedMovement::CREE => A_NO,
	          ActivatedMovement::ACM_EXECUTE_TOTALEMENT => A_YES,
	          ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT => _('Yes (Partial)')),
	          'DefaultValue' => _('N/A')));

$InvoiceItemGrid = $grid->render($LemCommandItemCollection, false,
        array(), array(), 'Invoice/InvoiceItemGrid.html');

// Affichage du formulaire avec smarty
$Smarty = new Template();
$Smarty->assign('returnURL', $retURL);
$Smarty->assign('CommandNo', $Command->getCommandNo());

$Smarty->assign('Currency', $curStr);

$Smarty->assign('CmdType', ($CmdType == Command::TYPE_CUSTOMER)?'Client':'Fournisseur');
$Smarty->assign('HiddenCommandID', $Command->getId());
$Smarty->assign('Port', $Command->getPort());
$Smarty->assign('Packing', $Command->getPacking());
$Smarty->assign('Insurance', $Command->getInsurance());
$Smarty->assign('GlobalHanding', $Command->getHanding());
$hbr = $Command->getHandingByRangePercent();
if ($hbr > 0) {
    $Smarty->assign('HandingByRangePercent', sprintf(I_COMMAND_HANDING, $hbr));
}
if ($CmdType == Command::TYPE_CUSTOMER) {  // Remise liee au client au moment de la commande
    $Smarty->assign('CustomerRemExcep',
        			I18N::formatNumber($Command->getCustomerRemExcep()));
}

if ($hasTVA) {
    $mapper = Mapper::singleton('TVA');
    $tvaCollection = $mapper->loadCollection(
        array('Type' => array(TVA::TYPE_DELIVERY_EXPENSES, TVA::TYPE_PACKING, TVA::TYPE_INSURANCE)));
    if (!Tools::isEmptyObject($tvaCollection)) {
        for($i = 0; $i < $tvaCollection->getCount(); $i++) {
    	    $tva = $tvaCollection->getItem($i);
            if (!in_array($tva->getRealTvaRate($tvaSurtaxRate), $tvaRateArray)) {
                $tvaRateArray[I18N::formatNumber($tva->getRealTvaRate($tvaSurtaxRate))] = 
                        $tva->getRealTvaRate($tvaSurtaxRate);
            }
        }
    }
    $Smarty->assign('port_tva_rate', getTVARateByCategory(TVA::TYPE_DELIVERY_EXPENSES, $tvaSurtaxRate));
    $Smarty->assign('packing_tva_rate', getTVARateByCategory(TVA::TYPE_PACKING, $tvaSurtaxRate));
    $Smarty->assign('insurance_tva_rate', getTVARateByCategory(TVA::TYPE_INSURANCE, $tvaSurtaxRate));
}

$Smarty->assign('tvaRateArray', $tvaRateArray);

// Gestion de la tva surtaxee
if ($hasTvaSurtax) {
    $tvaSurtaxRateFormated = I18N::formatNumber($tvaSurtaxRate);
    $Smarty->assign('TvaSurtaxRateFormated', $tvaSurtaxRateFormated);
    $Smarty->assign('TvaSurtaxRate', $tvaSurtaxRate);
} else {
    $Smarty->assign('TvaSurtaxRate', 0);
}

// Gestion de la taxe Fodec
$hasFodecTax = $sp->getHasFodecTax();
$fodecTaxRate = Preferences::get('FodecTax', 0);
if ($hasFodecTax) {
    $fodecTaxRateFormated = I18N::formatNumber($fodecTaxRate);
    $Smarty->assign('FodecTaxRateFormated', $fodecTaxRateFormated);
    $Smarty->assign('FodecTaxRate', $fodecTaxRate);
} else {
    $Smarty->assign('FodecTaxRate', 0);
}
// Gestion du timbre fiscal
$hasTaxStamp = $sp->getHasTaxStamp();
$taxStamp = Preferences::get('TaxStamp', 0);
if ($hasTaxStamp) {
    $taxStampFormated = I18N::formatNumber($taxStamp);
    $Smarty->assign('TaxStampFormated', $taxStampFormated);
    $Smarty->assign('TaxStamp', $taxStamp);
} else {
    $Smarty->assign('TaxStamp', 0);
}

$Smarty->assign('InvoiceItemGrid', $InvoiceItemGrid);

// L'encours
if ($sp instanceof SupplierCustomer) {
    $MaxIncur = (is_null($sp->getMaxIncur()))?
            _('Undefined'):I18N::formatNumber($sp->getMaxIncur());
    $Smarty->assign('MaxIncur', $MaxIncur);
    $Smarty->assign('UpdateIncur', I18N::formatNumber($sp->getUpdateIncur()));
	$Smarty->assign('ToHaveTTC', I18N::formatNumber($sp->getToHaveTTC()));
    if (($top = $Command->getTermsOfPayment()) instanceof TermsOfPayment) {
        $Smarty->assign('TermsOfPayment', $top->getName());
    }
} else {
    $Smarty->assign('UpdateIncur', '0,00');
	$Smarty->assign('ToHaveTTC', '0,00');
}

//pour les factures fournisseur d'une commande produit
if($Command instanceof ProductCommand && $Command->getType()==Command::TYPE_SUPPLIER) {
    $Smarty->assign('displayEditionDate', 1);
    $Smarty->assign('Invoice_EditionDate', date('Y-m-d h:i:s'));
    $Smarty->assign('Invoice_EditionDate_Format', date('d-m-Y h:i:s'));
}

if ($HasInstalment == 1) { // un acompte et pas de factures
    $Smarty->assign('HasInvoice', 1);
    $Smarty->assign('Instalment', $TotalInstalments);
}

Template::page(
    _('Invoice'),
    $Smarty->fetch('Invoice/InvoiceAddEdit.html'),
    array(
            'js/dynapi/src/dynapi.js',
            'js/lib-functions/FormatNumber.js',
            //'js/includes/ProductCommand.js',
            'js/includes/InvoiceAddEdit.js',
            'js/jscalendar/calendar.js',
            getJSCalendarLangFile(),
            'js/jscalendar/calendar-setup.js'
        )
);
?>
