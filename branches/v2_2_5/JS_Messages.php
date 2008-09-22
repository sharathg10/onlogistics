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

define('IGNORE_SESSION_TIMEOUT', true);
define('SKIP_CONNECTION', true);
// nécéssaires pour messages génériques
require_once('config.inc.php');

// ajouter les messages ici
$msgs = array();

$msgs['E_Error_Title'] = E_ERROR_TITLE;
$msgs['A_Delete'] = A_DELETE;

$msgs['ChainEdit_0'] = _('Please select a task type.');
$msgs['ChainEdit_1'] = '';
$msgs['ChainEdit_2'] = _('All operations must be assigned to an actor');
$msgs['ChainEdit_3'] = _('All tasks must have a duration');
$msgs['ChainEdit_4'] = _('You must put a deadline on one of the tasks.');

$msgs['ActorSitePopulateTools_0'] = _('Please select an actor.');
$msgs['ClientCatalog_0'] = _('Please select a customer first.');
$msgs['SupplierCatalog_0'] = _('Please select a supplier first.');
$msgs['checkForm_0'] = E_VALIDATE_FORM;
$msgs['checkForm_1'] = E_VALIDATE_FIELD;
$msgs['checkForm_2'] = E_VALIDATE_IS_REQUIRED;
$msgs['checkForm_3'] = E_VALIDATE_IS_INT;
$msgs['checkForm_4'] = _('must be "HH:MM"');
$msgs['checkForm_5'] = E_VALIDATE_IS_DECIMAL;
$msgs['OperationTaskPopulateTools_0'] = _('No operation available');
$msgs['OperationTaskPopulateTools_1'] = _('No task available');
$msgs['TimeTools_0'] = _('d');
$msgs['TimeTools_1'] = _('h');
$msgs['TimeTools_2'] = _('min');

$msgs['TChainTask_0'] = _('Amount');
$msgs['TChainTask_1'] = _('Instructions');

$msgs['ChainDef_0'] = _('Message obsolete');
$msgs['ChainDef_1'] = _('Message obsolete');

$msgs['ChainCommand_0'] = _('Are you sure you want to delete this item ?');
$msgs['ChainCommand_1'] = _('Please fill all required fields (emphasised by an asterisk).');

$msgs['AlertAddEdit_0'] = _('name');
$msgs['AlertAddEdit_1'] = _('subject');
$msgs['AlertAddEdit_2'] = _('body');

$msgs['AeroConcreteProductAddEdit_0'] = _('serial/lot number');
$msgs['AeroConcreteProductAddEdit_1'] = _('empty weight/mass');
$msgs['AeroConcreteProductAddEdit_2'] = _('maximum weight on lift-off');
$msgs['AeroConcreteProductAddEdit_3'] = _('maximum weight by seat');
$msgs['AeroConcreteProductAddEdit_4'] = _('Selling price excl. VAT');
$msgs['AeroConcreteProductAddEdit_5'] = _('Purchase price excl. VAT');
$msgs['AeroConcreteProductAddEdit_6'] = _('hours real potential since new (hours)');
$msgs['AeroConcreteProductAddEdit_7'] = _('hours real potential since new (minutes)');
$msgs['AeroConcreteProductAddEdit_8'] = _('cycle real potential since new');
$msgs['AeroConcreteProductAddEdit_9'] = _('landing real potential since new');
$msgs['AeroConcreteProductAddEdit_10'] = _('hours real potential since full overhaul (hours)');
$msgs['AeroConcreteProductAddEdit_11'] = _('hours real potential since full overhaul (minutes)');
$msgs['AeroConcreteProductAddEdit_12'] = _('cycle real potential since full overhaul');
$msgs['AeroConcreteProductAddEdit_13'] = _('landing real potential since full overhaul');
$msgs['AeroConcreteProductAddEdit_14'] = _('hours real potential since reparation (hours)');
$msgs['AeroConcreteProductAddEdit_15'] = _('hours real potential since reparation (minutes)');
$msgs['AeroConcreteProductAddEdit_16'] = _('cycle real potential since reparation');
$msgs['AeroConcreteProductAddEdit_17'] = _('Landing real potential since reparation');

$msgs['ActorAddEdit_0'] = _('name');
$msgs['ActorAddEdit_1'] = _('Number of days');
$msgs['ActorAddEdit_2'] = _('Maximum outstanding debts');
$msgs['ActorAddEdit_3'] = _('piloting hours');
$msgs['ActorAddEdit_4'] = _('Instructor hours of flight');
$msgs['ActorAddEdit_5'] = _('Commandant hours of flight');
$msgs['ActorAddEdit_6'] = _('Student hours of flight');
$msgs['ActorAddEdit_7'] = _('Dual hours of flight');
$msgs['ActorAddEdit_8'] = _('Nightly hours of flight');
$msgs['ActorAddEdit_9'] = _('Public transport hours of flight');
$msgs['ActorAddEdit_10'] = _('VLAE hours');
$msgs['ActorAddEdit_11'] = _('Hours on single-engined');
$msgs['ActorAddEdit_12'] = _('Hours on twin-engined');
$msgs['ActorAddEdit_13'] = _('commercial hours');
$msgs['ActorAddEdit_14'] = _('Please select an actor category in order to make your choice effective.');
$msgs['ActorAddEdit_15'] = E_ERROR_TITLE . ': ' . _('the database ');
$msgs['ActorAddEdit_16'] = _(' have to contain needed actors');

$msgs['ActorBankDetailAddEdit_0'] = _('name');
$msgs['ActorBankDetailAddEdit_1'] = _('IBAN number');

$msgs['CurrencyConverterAddEdit_0'] = _('You have to select two different currencies.');

$msgs['FlightEdit_0'] = _('You must select a flight purpose.');

$msgs['PaymentToHaveList_0'] = _('You must select a credit note (and only one).');

$msgs['OperationTolerance_0'] = _('Warning: modifications will not be effective until you click the "Ok" button.');

$msgs['LocationConcreteProductList_0'] = _('You provided twice the same SN, please correct.');
$msgs['LocationConcreteProductList_1'] = _('Please provide a SN.');
$msgs['LocationConcreteProductList_2'] = _('You provided less serial numbers than expected.');
$msgs['LocationConcreteProductList_3'] = _('A provided quantity exceeds available quantity in stock.');
$msgs['LocationConcreteProductList_4'] = _('Please provide a positive quantity.');
$msgs['LocationConcreteProductList_5'] = _('Quantity provided is different from the one expected.');
$msgs['LocationConcreteProductList_6'] = _('Please provide a date in the following format: dd/mm/yyyy');

$msgs['LicenceAddEdit_0'] = _('name');
$msgs['LicenceAddEdit_1'] = _('License type');

$msgs['ProductQuantityByCategoryList_0'] = _('category');
$msgs['ProductQuantityByCategoryList_1'] = _('Minimum quantity');

$msgs['InvoiceCommandList_0'] = _('Please select an invoice (and only one).');
$msgs['InvoiceCommandList_1'] = _('Payment was already done.');
$msgs['InvoiceCommandList_2'] = _('Payment cannot be negative.');
$msgs['InvoiceCommandList_3'] = _('You provided a lower amount than the one expected.');
$msgs['InvoiceCommandList_4'] = _('You provided a greater amount than the one expected.');
$msgs['InvoiceCommandList_5'] = _('Error: you are using a credit note, so you must provided an amount that is lower or equals to the amount of this credit note.');
$msgs['InvoiceCommandList_6'] = _('Error: you are using credit note as payment mode, please select a credit note.');
$msgs['InvoiceCommandList_7'] = _('Error: please select an invoice.');

$msgs['InvoiceAddEdit_0'] = _('Percent must be an integer between 0 and 100.');
$msgs['InvoiceAddEdit_1'] = _('Discount cannot be greater than the product price.');
$msgs['InvoiceAddEdit_2'] = _('Delivery, packing and insurance charges will be taken into account.');
$msgs['InvoiceAddEdit_3'] = _('Delivery, packing and insurance charges will not be taken into account.');
$msgs['InvoiceAddEdit_4'] = _('Some wrong data was encountered (%, e, /).');
$msgs['InvoiceAddEdit_5'] = _('Do you want to issue this invoice ?');

$msgs['InstallmentAdd_0'] = _('Instalment must be greater than zero.');
$msgs['InstallmentAdd_1'] = _('Instalment cannot be greater than total incl. VAT.');

$msgs['HelpPageAddEdit_0'] = _('name');
$msgs['HelpPageAddEdit_1'] = _('corresponding file');
$msgs['HelpPageAddEdit_2'] = _('body');

$msgs['ActivatedMovementAdd_0'] = _('You must provide SN/Lot informations.');
$msgs['ActivatedMovementAdd_1'] = _('Provided quantity is greater than stock quantity.');

$msgs['ProductCommand_0'] = _('Provided discounts have a wrong format (%, e, /)');
$msgs['ProductCommand_1'] = _('You must provide a quantity greater than zero for each order item.');
$msgs['ProductCommand_2'] = _('Instalment must be greater than zero.');
$msgs['ProductCommand_3'] = _('Instalment cannot be greater than total incl. VAT.');
$msgs['ProductCommand_4'] = _('Percent must be an integer between 0 and 100.');
$msgs['ProductCommand_5'] = _('Discount cannot be greater than the product price.');
$msgs['ProductCommand_6'] = _('Percent must be an integer between 0 and 100.');
$msgs['ProductCommand_7'] = _('Please select at least one item.');
$msgs['ProductCommand_8'] = _('You cannot delete all items.');
$msgs['ProductCommand_9'] = _('Dates for scheduled order items should match the format: "dd/mm/yy hh:mm"');
$msgs['ProductCommand_10'] = _('A scheduled order must have at least two different dates.');
$msgs['ProductCommand_11'] = _('You must provide a wished date for your order.');
$msgs['ProductCommand_12'] = _('You chose to provide a date range so please provide a wished end date.');
$msgs['ProductCommand_13'] = _('You provide a wrong quantity for a product selled by ');
$msgs['ProductCommand_14'] = _('Amount excl. VAT is lower than minimum amount to order');
$msgs['ProductCommand_15'] = _('Please correct or contact customer department.');
$msgs['ProductCommand_16'] = _('Packaging quantity have to be an integer. Please correct.');
$msgs['ProductCommand_17'] = _('No expeditor site defined, please add one.');
$msgs['ProductCommand_18'] = _('Destinator does not have a delivery site, please add one.');

$msgs['WorkOrder_0'] = _('Please provide a work order name.');
$msgs['WorkOrder_1'] = _('Please provide non negatives values');

$msgs['UnavailabilityAddEdit_0'] = _('beginning date');
$msgs['UnavailabilityAddEdit_1'] = _('end date');

$msgs['SiteAddEdit_0'] = _('zip code');
$msgs['SiteAddEdit_1'] = _('country');
$msgs['SiteAddEdit_2'] = _('Site type could not be modified because it has stores in it.');
$msgs['SiteAddEdit_3'] = _('name');
$msgs['SiteAddEdit_4'] = _('A site already exists with this name, please correct.');
$msgs['SiteAddEdit_5'] = E_VALIDATE_FIELD . ' "'._('Zip code').'" or "'._('City').'" ' . E_VALIDATE_IS_REQUIRED . '. ' . _('Please correct.');

$msgs['SupplyingOptimization_0'] = _('number of weeks in history');
$msgs['SupplyingOptimization_1'] = _('number of weeks of forecast');
$msgs['SupplyingOptimization_2'] = _('default delivery within');
$msgs['SupplyingOptimization_3'] = _('Please select a valid date.');

$msgs['ProductHandingByCategoryAddEdit_0'] = _('Discount to apply');
$msgs['ProductHandingByCategoryAddEdit_1'] = _('categories');

$msgs['ProductAddEdit_0'] = _('reference');
$msgs['ProductAddEdit_1'] = _('product type');
$msgs['ProductAddEdit_2'] = _('selling unit qty');
$msgs['ProductAddEdit_3'] = _('selling unit minimum qty');
$msgs['ProductAddEdit_4'] = _('selling unit type');
$msgs['ProductAddEdit_5'] = _('selling unit length');
$msgs['ProductAddEdit_6'] = _('selling unit width');
$msgs['ProductAddEdit_7'] = _('selling unit height');
$msgs['ProductAddEdit_8'] = _('selling unit weight');
$msgs['ProductAddEdit_9'] = _('number of base units in packaging');
$msgs['ProductAddEdit_10'] = _('number of units in packing');
$msgs['ProductAddEdit_11'] = _('number of packing units in regrouping');
$msgs['ProductAddEdit_12'] = _('stackable ratio');
$msgs['ProductAddEdit_13'] = _('packaging stackable ratio');
$msgs['ProductAddEdit_14'] = _('packing stackable ratio');
$msgs['ProductAddEdit_15'] = _('regrouping stackable ratio');
$msgs['ProductAddEdit_16'] = _('Selling unit quantity field must be an integer');
$msgs['ProductAddEdit_17'] = _('Selling unit minimum quantity field must be an integer');
$msgs['ProductAddEdit_18'] = _('designation');

$msgs['PrestationAdd_0'] = _('Please provide a service name');
$msgs['PrestationAdd_1'] = _('Please select a service type');

$msgs['PotentialsEdit_0'] = _('potential since full overhaul (hours)');
$msgs['PotentialsEdit_1'] = _('potential since full overhaul (minutes)');
$msgs['PotentialsEdit_2'] = _('potential since reparation (hours)');
$msgs['PotentialsEdit_3'] = _('potential since reparation (minutes)');
$msgs['PotentialsEdit_4'] = _('number of landings since full overhaul');
$msgs['PotentialsEdit_5'] = _('number of landings since reparation');
$msgs['PotentialsEdit_6'] = _('number of cycles since full overhaul');
$msgs['PotentialsEdit_7'] = _('number of cycles since reparation');

$msgs['FlightPreparationEdit_0'] = _('Tank capacity is undefined.');

$msgs['ContactAddEdit_0'] = _('name');

$msgs['AssemblyEdit_0'] = _('You cannot provide a quantity that is superior to the one expected.');
$msgs['AssemblyEdit_1'] = _('Error: you provided twice the same SN/Lot for the same assembly.');
$msgs['AssemblyEdit_2'] = _('Error: you must a provide a SN/Lot for each assembled part.');
$msgs['AssemblyEdit_3'] = _('Error: you must a provide a different SN/Lot for each assembled part.');

$msgs['SpreadSheetAddEdit_0'] = _('name');
$msgs['SpreadSheetAddEdit_1'] = _('Please select a base entity first.');
$msgs['SpreadSheetAddEdit_2'] = _('The following fields are required for columns: ');
$msgs['SpreadSheetAddEdit_3'] = _('Are you sure you want to delete selected items ?');
$msgs['SpreadSheetAddEdit_4'] = _('Are you sure you want to delete selected item ?');
$msgs['SpreadSheetAddEdit_5'] = _('Column width cannot be less than 40.');
$msgs['SpreadSheetAddEdit_6'] = _('Name');
$msgs['SpreadSheetAddEdit_7'] = _('Descr.');
$msgs['SpreadSheetAddEdit_8'] = _('Default');
$msgs['SpreadSheetAddEdit_9'] = _('Width');
$msgs['SpreadSheetAddEdit_10'] = _('Required');
$msgs['SpreadSheetAddEdit_11'] = _('Index');

$msgs['StockAccountingExport_0'] = _('Stores');
$msgs['StockAccountingExport_1'] = _('Criterion');

$msgs['InvoicePrestationList_0'] = _('There is nothing to charge.');
$msgs['InvoicePrestationList_1'] = _('Please select at least one store.');

$msgs['ProductionTaskValidation_0'] = _("Are you sure you want to finish the task ?");

$msgs['CatalogAddEdit_0'] = _("name");

$msgs['ChainCommand_0'] = _('Your order must at least consist of one item.');
$msgs['ChainCommand_1'] = _('You must choose a date or a date range.');
$msgs['ChainCommand_2'] = _('You chose to recover a payment on delivery, but didn\'t provide an amount.');
$msgs['ChainCommand_3'] = _('Please provide a valid quantity for each order item.');
$msgs['ChainCommand_4'] = _('Please provide a valid weight for each order item.');
$msgs['ChainCommand_5'] = _('Please provide a valid width for each order item.');
$msgs['ChainCommand_6'] = _('Please provide a valid length for each order item.');
$msgs['ChainCommand_7'] = _('Please provide a valid height for each order item.');

$msgs['RessourceGroupAddEdit_0'] = _('Resource');
$msgs['RessourceGroupAddEdit_1'] = _('Rate');
$msgs['RessourceGroupAddEdit_2'] = _('Rate type');

$msgs['TaskDetails_0'] = _('[No city found]');
$msgs['TaskDetails_1'] = _('[No operation]');
$msgs['TaskDetails_2'] = _('[No task]');
$msgs['TaskDetails_3'] = _('A task with a fixed date can not be defined as deadline task.');
$msgs['TaskDetails_4'] = _('A deadline task is already defined, modify it ?');
$msgs['TaskDetails_5'] = _('All sites');
$msgs['TaskDetails_6'] = _('No site');

$msgs['ForwardingForm_0'] = _('Please select the addressee site');
$msgs['ForwardingForm_1'] = _('Please provide a positive quantity.');
$msgs['ForwardingForm_2'] = _('Provided quantities for the following product-location pairs exceed amount of stock');
$msgs['ForwardingForm_3'] = _('Carrier departure and arrival sites must be different.');

$msgs['GroupableBoxList_0'] = _('volume');
$msgs['GroupableBoxList_1'] = _('shipper');
$msgs['GroupableBoxList_2'] = _('shipper site');
$msgs['GroupableBoxList_3'] = _('addressee');
$msgs['GroupableBoxList_4'] = _('addressee site');
$msgs['GroupableBoxList_5'] = _('parcel type');

$msgs['CategoryAddEdit_0'] = _('A category has only one minimum amount by currency. Please correct.');
$msgs['CategoryAddEdit_1'] = _('Discounts by range percent, minimum and maximum must be floats. Please correct.');
$msgs['CategoryAddEdit_2'] = _('Discounts by range overlap. Please correct.');


$msgs['ZoneAddEdit_0'] = _('Please select at least one city.');
$msgs['ZoneAddEdit_1'] = _('Please select at least one site.');

$msgs['FlowTypeAddEdit_0'] = _('You must add expenses and receipts lines');

$msgs['ProductPriceAddEdit_0'] = _('Pricing zone');
$msgs['ProductPriceAddEdit_1'] = _('Currency');
$msgs['ProductPriceAddEdit_2'] = _('Recommended price');
$msgs['ProductPriceAddEdit_3'] = _('Price');
$msgs['ProductPriceAddEdit_4'] = _('N/A');
$msgs['ProductPriceAddEdit_5'] = _('This will discard the changes you made for this supplier, continue ?');

$msgs['TermsOfPaymentAddEdit_1']  = _('Percent of total amount incl. taxes');
$msgs['TermsOfPaymentAddEdit_2']  = _('Delay in days');
$msgs['TermsOfPaymentAddEdit_3']  = _('Modality');
$msgs['TermsOfPaymentAddEdit_4']  = _('Option');
$msgs['TermsOfPaymentAddEdit_5']  = _('Event');
$msgs['TermsOfPaymentAddEdit_6']  = _('To pay to');
$msgs['TermsOfPaymentAddEdit_7']  = _('Order supplier');
$msgs['TermsOfPaymentAddEdit_8']  = _('Percent of amount must be a float');
$msgs['TermsOfPaymentAddEdit_9']  = _('Payment delay must be an integer');
$msgs['TermsOfPaymentAddEdit_10'] = _('Percent of amount can not exceed 100%');
$msgs['TermsOfPaymentAddEdit_11'] = _('Percent of amount sum must be equals to 100%');


header('Content-Type: text/javascript');
// Pas d'utilisation de JsTools::JSQuoteString() ici, car inutilisable dans
// Login.php (le config.inc.php redirige JS_Messages.php vers Login.php)
foreach($msgs as $name=>$msg){
	echo 'var ' . $name . ' = ' . "'" . str_replace("'", "\'", $msg) . "'" . ";\n";
}
?>
