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
require_once('InvoiceItemTools.php');
require_once('Objects/ActivatedMovement.php');
require_once('Objects/Command.const.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_COMMERCIAL,
                           UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES,
                           UserAccount::PROFILE_ACCOUNTANT));

SearchTools::ProlongDataInSession();  // prolonge les datas du form de recherche en session

$retURL = isset($_REQUEST['returnURL'])?$_REQUEST['returnURL']:'CommandList.php';

$InvoiceMapper = Mapper::singleton('Invoice');
$Invoice = $InvoiceMapper->load(array('Id' => $_GET['InvoiceId']));
if (!($Invoice instanceof Invoice)) {
	Template::errorDialog(_('Invoice was not found in the database.'), $retURL);
	exit;
}
if (isset($_REQUEST['EditComment'])) {
	$comment = get_magic_quotes_gpc()? trim(mysql_real_escape_string($_REQUEST['IAEComment'])):
            trim($_REQUEST['IAEComment']);
	$Invoice->setComment($comment);
    saveInstance($Invoice, $retURL);
}


$InvoiceItemCollection = $Invoice->getInvoiceItemCollection();
$ProductCommand = $Invoice->getCommand();
$CmdType = $ProductCommand->getType();

$sp = $ProductCommand->getSupplierCustomer();
$hasTVA = $Invoice->hasTVA(); // $sp->getHasTVA();
$cur = $ProductCommand->getCurrency();
$curStr = $cur instanceof Currency?$cur->getSymbol():'&euro;';

//pour l'affichage du grid
$grid = new Grid();
$grid->paged = false;
$grid->displayCancelFilter = false;
$grid->withNoCheckBox = true;
$grid->withNoSortableColumn = true;

$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%Reference%'));
$grid->NewColumn('FieldMapper', _('Designation'), array('Macro' => '%Name%'));
$grid->NewColumn('MultiPrice', _('Unit price excl. VAT'),
        array('Enabled' => (!($ProductCommand instanceof CourseCommand)),
              'Method' => 'getUnitPriceHT'));
$grid->NewColumn('FieldMapper', _('% VAT'),
        array('Enabled' => ($hasTVA), 'Macro' => '%TVA.Rate|formatnumber%'));
$grid->NewColumn('FieldMapper', _('Qty'), array('Macro' => '%quantity%'));
$grid->NewColumn('InvoiceDetailPromotion', _('Offer on sale'),
        array('Enabled' => ($CmdType == Command::TYPE_CUSTOMER),
              'customer'=>$ProductCommand->getCustomer(), 'retURL'=>$retURL));
$grid->NewColumn('FieldMapper', _('Discount'), array('Macro' => "%Handing|formathanding@$curStr%"));
$grid->NewColumn('InvoiceProductPrice', _('Turnover excl. VAT'),
        array('Enabled' => (!($ProductCommand instanceof CourseCommand))));
$grid->NewColumn('MultiPrice', _('Total amount incl. VAT'),
        array('Enabled' => ($ProductCommand instanceof CourseCommand),
              'Method' => 'getUnitPriceHT'));
$grid->NewColumn(
        'FieldMapperWithTranslationExpression', _('Deliv.'),
        array('Macro' => '%ActivatedMovement.State%',
	          'TranslationMap' => array(
                    ActivatedMovement::CREE => A_NO . '<input type="hidden" name="HiddenActMvtState[]" value="%ActivatedMovement.State%">',
	  				ActivatedMovement::ACM_EXECUTE_TOTALEMENT => A_YES . '<input type="hidden" name="HiddenActMvtState[]" value="%ActivatedMovement.State%">',
					ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT => _('Yes (Partial)') . '<input type="hidden" name="HiddenActMvtState[]" value="%ActivatedMovement.State%">'),
	          'DefaultValue' => 'N/A')
);

$InvoiceItemGrid = $grid->render($InvoiceItemCollection, false);

$NoInvoice = 0;
// La cde a un accompte; les factures de cette cde
if ($ProductCommand->getInstallment() != 0) {
	$InvoiceCollection = $InvoiceMapper->loadCollection(
            array('Command' => $ProductCommand->getId()));
	if ($InvoiceCollection->getCount() == 1) { // 1 seule facture
		$NoInvoice = 1;
	}
	else { //au moins 2 factures
		$InvoiceCollection->sort('DocumentNo');
		$InvoiceOne = $InvoiceCollection->getItem(0);  // la 1ere facture de la cde
		if (($InvoiceOne->getId() == $_GET['InvoiceId'])) {
		    $NoInvoice = 1;
		}
	}
}

// Pour l'affichage du detail par taux de tva
$tvaRateArray = ($hasTVA)?$Invoice->getTVADetail():array();
// Formatage pour l'affichage
foreach($tvaRateArray as $key => $value) {
	$tvaRateArray[I18N::formatNumber($key)] = I18N::formatNumber($value);
	unset($tvaRateArray[$key]);
}

//Affichage du formulaire avec smarty
$Smarty = new Template();
$Smarty->assign('CommandNo', $ProductCommand->getCommandNo());
$Smarty->assign('Currency', $curStr);
$Smarty->assign('InvoiceNo', $Invoice->getDocumentNo());
$Smarty->assign('Port', I18N::formatNumber($Invoice->getPort()));
$Smarty->assign('Packing', I18N::formatNumber($Invoice->getPacking()));
$Smarty->assign('Insurance', I18N::formatNumber($Invoice->getInsurance()));
$Smarty->assign('CustomerRemExcep',
        I18N::formatNumber($ProductCommand->getCustomerRemExcep()));
$Smarty->assign('GlobalHanding', I18N::formatNumber($Invoice->getGlobalHanding()));
$hbr = $ProductCommand->getHandingByRangePercent();
if ($hbr > 0) {
    $Smarty->assign('HandingByRangePercent', sprintf(I_COMMAND_HANDING, $hbr));
}
$totalHT = $Invoice->getTotalPriceHT();
$Smarty->assign('TotalPriceTTC', I18N::formatNumber($Invoice->getTotalPriceTTC()));
$Smarty->assign('TotalPriceHT', I18N::formatNumber($totalHT));
$Smarty->assign('tvaRateArray', $tvaRateArray);
$Smarty->assign('TVA', empty($tvaRateArray)?'0,00':'');
$Smarty->assign('cmdId', $ProductCommand->getId());
$Smarty->assign('InvoiceId', $_GET['InvoiceId']);
$Smarty->assign('IAEComment', $Invoice->getComment());

$handingDetail = $Invoice->getHandingDetail();
$Smarty->assign('TotalPriceHTBeforeDiscount', I18N::formatNumber($handingDetail['ht']));
$Smarty->assign('GlobalDiscount', I18N::formatNumber($handingDetail['handing']));
if ($NoInvoice == 1) { //un accompte sur cette facture
	$Smarty->assign('HasInvoice', 1);
	$Smarty->assign('Installment', I18N::formatNumber($ProductCommand->getInstallment()));
	$ToPay = max(0, $Invoice->getTotalPriceTTC() - $ProductCommand->getInstallment()); // tjs >=0
	$Smarty->assign('ToPay', I18N::formatNumber($ToPay));
}

// Gestion de la tva surtaxee
$tvaSurtaxRateFormated = I18N::formatNumber($Invoice->getTvaSurtaxRate());
$Smarty->assign('TvaSurtaxRate', $tvaSurtaxRateFormated);

// Gestion de la taxe Fodec
$fodecTaxRate = $Invoice->getFodecTaxRate();
$fodecTaxRateFormated = ((int)$fodecTaxRate == 0)?0:I18N::formatNumber($fodecTaxRate);
$Smarty->assign('FodecTaxRate', $fodecTaxRateFormated);
$Smarty->assign('FodecTax', I18N::formatNumber($totalHT * ($fodecTaxRate) / 100));
// Gestion du timbre fiscal
$taxStampFormated = I18N::formatNumber($Invoice->getTaxStamp());
$Smarty->assign('TaxStamp', $taxStampFormated);

$Smarty->assign('InvoiceItemGrid', $InvoiceItemGrid);
$Smarty->assign('$formAction', $_SERVER['PHP_SELF']);
$Smarty->assign('returnURL',
        (isset($_REQUEST['cmdId']))?'InvoiceCommandList.php?CommandId='
                . $_REQUEST['cmdId'].'&returnURL='.$retURL:$retURL);

Template::page(
    _('Invoice details'),
    $Smarty->fetch('Invoice/InvoiceDetail.html')
);
?>