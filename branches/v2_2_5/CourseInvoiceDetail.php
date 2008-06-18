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
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_ADMIN_VENTES,
						   UserAccount::PROFILE_AERO_CUSTOMER, UserAccount::PROFILE_DIR_COMMERCIAL));

SearchTools::prolongDataInSession();  // prolonge les datas du form de recherche en session
$returnURL = 'CourseCommandList.php';
$errorBody = _('No invoice issued for this order.');

$CourseCommand = Object::load('CourseCommand', $_REQUEST['cmdId']);
if (Tools::isEmptyObject($CourseCommand) || !$CourseCommand->hasBeenFactured() ) {
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

$InvoiceItemCollection = $Invoice->getInvoiceItemCollection();

//pour l'affichage du grid
$grid = new Grid();
$grid->paged = false;
$grid->displayCancelFilter = false;
$grid->withNoCheckBox = true;
$grid->withNoSortableColumn = true;

$grid->NewColumn('FieldMapper', _('Service'), array('Macro' => '%Prestation.Name%'));
//$grid->NewColumn('MultiPrice', _('PU HT'), array('Method' => 'getUnitPriceHT'));
$grid->NewColumn('FieldMapper', _('% VAT'), array('Macro' => '%TVA.Rate|formatnumber%'));
$grid->NewColumn('FieldMapper', _('Number of hours'), array('Macro' => '%quantity|formatnumber%'));
//$grid->NewColumn('InvoiceProductPrice', _('Turnover excl. VAT'));
$grid->NewColumn('MultiPrice', _('Turnover excl. VAT'), array('Method' => 'getUnitPriceHT'));
$InvoiceItemGrid = $grid->render($InvoiceItemCollection, false);


//Affichage du formulaire avec smarty
$Smarty = new Template();
$Smarty->assign('CommandNo', $CourseCommand->getCommandNo());
$Smarty->assign('InvoiceNo', $Invoice->getDocumentNo());
$Smarty->assign('GlobalHanding', I18N::formatNumber($Invoice->getGlobalHanding()));
$Smarty->assign('TotalPriceTTC', I18N::formatNumber($Invoice->getTotalPriceTTC()));
$Smarty->assign('TotalPriceHT',  I18N::formatNumber($Invoice->getTotalPriceHT()));
$Smarty->assign('TVA', I18N::formatNumber($Invoice->getTotalPriceTTC() - $Invoice->getTotalPriceHT()));
$Smarty->assign('cmdId', $_REQUEST['cmdId']);
$Smarty->assign('InvoiceId', $Invoice->getId());
$Smarty->assign('isCourseCommand', 'yes');
$Smarty->assign('print', 0);  // tjs edition, pas reedition
$Smarty->assign('IAEComment', $Invoice->getComment());

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

$Smarty->assign('formAction', $_SERVER['PHP_SELF']);
$Smarty->assign('InvoiceItemGrid', $InvoiceItemGrid);
$Smarty->assign('returnURL', $returnURL);

Template::page(_('Invoice details'), $Smarty->fetch('Invoice/InvoiceDetail.html'));

?>