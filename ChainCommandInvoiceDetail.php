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

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES));

SearchTools::ProlongDataInSession();  // prolonge les datas du form de recherche en session
$returnURL = 'InvoiceChainCommandList.php';
$errorBody = _('No invoice issued for this order.');

$ChainCommand = Object::load('ChainCommand', $_REQUEST['cmdId']);
if (Tools::isEmptyObject($ChainCommand) || !$ChainCommand->hasBeenFactured() ) {
    Template::errorDialog($errorBody, $returnURL);
	exit;
}

$InvoiceMapper = Mapper::singleton('Invoice');
$Invoice = $InvoiceMapper->load(array('Command' => $_REQUEST['cmdId']));
if (!($Invoice instanceof Invoice)) {
	Template::errorDialog($errorBody, $returnURL);
	exit;
}
// On a modifie le commentaire
if (isset($_REQUEST['EditComment'])) {
	$comment = get_magic_quotes_gpc()? trim(mysql_real_escape_string($_REQUEST['IAEComment'])):
            trim($_REQUEST['IAEComment']);
	$Invoice->setComment($comment);
    saveInstance($Invoice, $returnURL);
}

$ChainCommandItemCollection = $ChainCommand->getCommandItemCollection();
$grid = new Grid();
$grid->paged = false;
$grid->displayCancelFilter = false;
$grid->withNoCheckBox = true;
$grid->withNoSortableColumn = true;

//Tableau de rappel du détail de la commande
$grid->NewColumn('FieldMapper', _('Parcel number'), array('Macro' => '%Id%'));
$grid->NewColumn('FieldMapper', _('Parcel type'), array('Macro' => '%CoverType.Name%'));
$grid->NewColumn('FieldMapper', _('Content'), array('Macro' => '%ProductType.Name%'));
$grid->NewColumn('FieldMapper', _('Weight'), array('Macro' => '%Weight%'));
$grid->NewColumn('FieldMapper', _('Dimensions'), array('Macro' => '%Height%*%Length%*%Width%'));
$grid->NewColumn('FieldMapper', _('Qty'), array('Macro' => '%quantity%'));

$InvoiceItemGrid = $grid->render($ChainCommandItemCollection, false, array(),
        array(), 'Invoice/InvoiceItemGrid.html');

//Affichage du formulaire avec smarty
$Smarty = new Template();
$Smarty->assign('CommandNo', $ChainCommand->getCommandNo());
$Smarty->assign('InvoiceNo', $Invoice->getDocumentNo());
$Smarty->assign('GlobalHanding', I18N::formatNumber($Invoice->getGlobalHanding()));
$Smarty->assign('TotalPriceTTC', I18N::formatNumber($Invoice->getTotalPriceTTC()));
$Smarty->assign('TotalPriceHT',  I18N::formatNumber($Invoice->getTotalPriceHT()));
$Smarty->assign('TVA',
        I18N::formatNumber($Invoice->getTotalPriceTTC() - $Invoice->getTotalPriceHT()));
$Smarty->assign('cmdId', $_REQUEST['cmdId']);
$Smarty->assign('InvoiceId', $Invoice->getId());
$Smarty->assign('IAEComment', $Invoice->getComment());
$Smarty->assign('print', 0);  // tjs edition, pas reedition
//devise
$cur = $ChainCommand->getCurrency();
$curStr = $cur instanceof Currency?$cur->getSymbol():'&euro;';
$Smarty->assign('Currency', $curStr);

// Gestion de la tva surtaxee
$tvaSurtaxRate = $Invoice->getTvaSurtaxRate();
$tvaSurtaxRateFormated = ((int)$tvaSurtaxRate == 0)?0:I18N::formatNumber($tvaSurtaxRate);
$Smarty->assign('TvaSurtaxRate', $tvaSurtaxRateFormated);
$totalHT = $Invoice->getTotalPriceHT();
// Gestion de la taxe Fodec
$fodecTaxRate = $Invoice->getFodecTaxRate();
$fodecTaxRateFormated = ((int)$fodecTaxRate == 0)?0:I18N::formatNumber($fodecTaxRate);
$Smarty->assign('FodecTaxRate', $fodecTaxRateFormated);
$Smarty->assign('FodecTax', I18N::formatNumber($totalHT * ($fodecTaxRate) / 100));
// Gestion du timbre fiscal
$taxStampFormated = I18N::formatNumber($Invoice->getTaxStamp());
$Smarty->assign('TaxStamp', $taxStampFormated);

$Smarty->assign('InvoiceItemGrid', $InvoiceItemGrid);
$Smarty->assign('formAction', $_SERVER['PHP_SELF']);
$Smarty->assign('returnURL',
        'InvoiceCommandList.php?CommandId=' . $_REQUEST['cmdId']
        . '&returnURL=ChainCommandList.php');

Template::page(_('Invoice details'), $Smarty->fetch('Invoice/InvoiceDetail.html'));
?>
