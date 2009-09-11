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

define('ALERT_STOCK_QV_MINI', 5000);
define('ALERT_STOCK_QR_MINI', 10000);
define('ALERT_STOCK_QV_REACH_ZERO', 15000);
define('ALERT_STOCK_QR_REACH_ZERO', 20000);
define('ALERT_PARTIAL_ENTRY', 25000);
define('ALERT_PARTIAL_EXIT', 30000);
define('ALERT_CLIENT_COMMAND_INCUR_OVER', 35000);
define('ALERT_SUPPLIER_COMMAND_INCUR_OVER', 40000);
define('ALERT_CLIENT_INVOICE_INCUR_OVER', 45000);
define('ALERT_SUPPLIER_INVOICE_INCUR_OVER', 50000);
define('ALERT_CLIENT_COMMAND_RECEIPT', 55000);
define('ALERT_ESTIMATE_RECEIPT', 55001);
define('ALERT_SUPPLIER_COMMAND_RECEIPT', 60000);
define('ALERT_CLIENT_LATE_PAYMENT', 65000);
define('ALERT_SUPPLIER_LATE_PAYMENT', 70000);
define('ALERT_MOVEMENT_CANCELLED', 75000);
define('ALERT_PARTIAL_MOVEMENT', 80000);
define('ALERT_CHAIN_COMMAND_RECEIPT', 85000);
define('ALERT_PLANIFICATION_CHAIN', 90000);
define('ALERT_COMMAND_DELETE', 95000);
define('ALERT_REINTEGRATION_STOCK', 100000);
define('ALERT_COURSE_COMMAND_RECEIPT', 100001);
define('ALERT_TEN_DAYS_SINCE_LAST_FLY', 100002);
define('ALERT_POTENTIAL_OVER', 100003);
define('ALERT_MOVEMENT_QTY_OVER', 100004);
define('ALERT_MOVEMENT_NON_FORESEEABLE', 100005);
define('ALERT_INSUFFICIENT_STOCK', 100006);
define('ALERT_REINTEGRATION_STOCK_FACTURED', 100007);
define('ALERT_PRODUCT_CHANGED', 100008);
define('ALERT_LICENCE_OUT_OF_DATE', 100010);
define('ALERT_LICENCE_OUT_OF_DATE_ADMIN', 100011);
define('ALERT_CUSTOMER_SITUATION', 100012);
define('ALERT_MAX_MEETING_DATE_EXCEEDED', 100013);
define('ALERT_PRODUCTION_TASK_VALIDATION', 100014);
define('ALERT_INVOICE_TO_DOWNLOAD', 100015);
define('ALERT_INVOICE_BY_MAIL', 100016);
define('ALERT_CUSTOMER_WITHOUT_ORDER_SINCE_THIRTY_DAYS', 100017);
define('ALERT_FORECAST_EXPENSE_OVER_THE_BORD', 100018);
define('ALERT_FORECAST_RECEIPT_OVER_THE_BORD', 100019);
define('ALERT_GED_DOCUMENT_UPLOADED', 100020);
define('ALERT_GED_DOCUMENT_UPDATED', 100021);
define('ALERT_GED_DOCUMENT_DELETED', 100022);
define('ALERT_GED_DOCUMENT_ASSIGNED', 100023);
define('ALERT_GED_DOCUMENT_UNASSIGNED', 100024);
define('ALERT_GED_ACK_OUT_OF_DATE', 100025);

/*
 * Retourne les subject et body par defaut des alertes
 *
 * @param integer $alertId Id d'Alerte: optionnel
 * @return mixed
 */
function getAlertContent($alertId=0) {
    $alertContent = array();
    $alertContent[ALERT_STOCK_QV_MINI] = array(
        'subject' => _('Reference') . ' {$ProductBaseReference}. '
            . _('Virtual stock reached'). ' {$ProductMinimumStock}.',
        'body' => _('Virtual stock for product') . ' {$ProductBaseReference} '
            . _('reached is minimum level, which is') .': {$ProductMinimumStock}.' . "\n"
            . _('Please very next scheduled supplying.'). "\n\n"
            . _('Product designation').': {$ProductName}' . "\n"
            . _('Product supplier') . ': {$ProductSupplierName}'
    );

    $alertContent[ALERT_STOCK_QR_MINI] = array(
        'subject' => _('Reference') . ' {$ProductBaseReference}. '._('Actual stock reaches')
            . ' {$ProductMinimumStock}.',
        'body' => _('Stock quantity of product ref.') . ' {$ProductBaseReference} '
            . _('reached is minimum level, which is') . ': {$ProductMinimumStock}.' . "\n"
            . _('Please very next scheduled supplying.'). "\n\n"
            . _('Product designation').': {$ProductName}' . "\n"
            . _('Product supplier') . ': {$ProductSupplierName}'
    );

    $alertContent[ALERT_STOCK_QV_REACH_ZERO] = array(
        'subject' => _('Reference') . ' {$ProductBaseReference}. '
            ._('Virtual stock reaches zero.'),
        'body' => _('Virtual stock for product') . ' {$ProductBaseReference} '
            . _('reached zero.'). "\n\n"
            . _('Please very next scheduled supplying.'). "\n\n"
            . _('Product designation').': {$ProductName}' . "\n"
            . _('Product supplier') . ': {$ProductSupplierName}'
    );

    $alertContent[ALERT_STOCK_QR_REACH_ZERO] = array(
        'subject' => _('Reference') . ' {$ProducttBaseReference} '._('Stock reaches zero.'),
        'body' => _('Stock quantity of product ref.') . ' {$ProductBaseReference} '
            . _('reached its minimum level, which is zero.'). "\n\n"
            . _('Please very next scheduled supplying.'). "\n\n"
            . _('Product designation') . ': {$ProductName}' . "\n"
            . _('Product supplier') . ': {$ProductSupplierName}'
    );

    $alertContent[ALERT_PARTIAL_ENTRY] = array(
        'subject' => _('Partial import. Order') . ' {$Numcde}. ' . _('Ref.')
            . ' {$ProductBaseReference} : {$lemQuantity}/{$cdmQuantity}.',
        'body' => _('Order') . ' {$Numcde} : '
            . _('Partial movement, for reference')
            . ' {$ProductBaseReference}, ' . _('There was') . ' {$lemQuantity} '
            . _('moved units for') . ' {$cdmQuantity} ' . _('expected') . '.'
    );

    $alertContent[ALERT_PARTIAL_EXIT] = array(
        'subject' => _('Partial issue. Order') . ' {$Numcde}. ' . _('Ref.')
            . ' {$ProductBaseReference} : {$lemQuantity}/{$cdmQuantity}.',
        'body' => _('Order').' {$Numcde} : '
            . _('Partial movement, for reference')
            . ' {$ProductBaseReference}, {$lemQuantity} '
            . _('moved units for') .' {$cdmQuantity} ' . _('expected') . '.'
    );

    $alertContent[ALERT_CLIENT_COMMAND_INCUR_OVER] = array(
        'subject' => _('Order') . ' {$Numcde} , ' . _('customer') . ' {$ActorName}, '
            .  _('Outstanding debts amount exceeded'),
        'body' => _('Order') . ' {$Numcde} , ' . _('customer') . ' {$ActorName}, '
            . _('Outstanding debts amount exceeded'). "\n"
            . _('Allowed outstanding debts amount') . ': {$MaximumIncurse} {$Currency}.' . "\n"
            . _('Current outstanding debts value with this order')
            . ' : {$UpdateIncurseWithCommand} {$Currency}.'
    );

    $alertContent[ALERT_SUPPLIER_COMMAND_INCUR_OVER] = array(
        'subject' => _('Order') . ' {$Numcde} , ' . _('supplier')
            . ' {$ActorName}, ' . _('Outstanding debts amount exceeded'),
        'body' => _('Order') . ' {$Numcde} , ' . _('supplier')
            . ' {$ActorName}, ' . _('Outstanding debts amount exceeded'). "\n"
            . _('Allowed outstanding debts amount') . ': {$MaximumIncurse} {$Currency}.' . "\n"
            . _('Current outstanding debts value with this order')
            . ' : {$UpdateIncurseWithCommand} {$Currency}.'
    );

    $alertContent[ALERT_CLIENT_INVOICE_INCUR_OVER] = array(
        'subject' => _('Order') . ' {$Numcde} , ' . _('customer')
            . ' {$CustomerName}, '. _('allowed outstanding debts amount exceeded'),
        'body' => _('Customer') . ' {$CustomerName}, ' . _('Order')
            . ' {$Numcde} , ' . _('Invoice') . ' {$NumInvoice} '
            . _('Outstanding debts amount exceeded') . "\n"
            . _('Allowed outstanding debts amount').': {$MaximumIncurse} {$Currency}.'. "\n"
            . _('Current outstanding debts value with this invoice')
            . ' : {$UpdateIncurseWithCommand} {$Currency}.'
    );

    $alertContent[ALERT_SUPPLIER_INVOICE_INCUR_OVER] = array(
        'subject' => _('Order') . ' {$Numcde} , ' . _('supplier')
            . ' {$SupplierName}, ' . _('Outstanding debts amount exceeded'),
        'body' => _('Supplier') . ' {$SupplierName}, ' . _('Order')
            . ' {$Numcde} , ' . _('Invoice') . ' {$NumInvoice} '
            . _('Outstanding debts amount exceeded'). "\n"
            . _('Allowed outstanding debts amount').' : {$MaximumIncurse}€' . "\n"
            . _('Current outstanding debts value with this invoice').' : {$UpdateIncurse} €'
    );

    $alertContent[ALERT_CLIENT_COMMAND_RECEIPT] = array(
        'subject' => _('Order receipt') . ' {$NumCde}',
        'body' => '{$body}'
    );

    $alertContent[ALERT_ESTIMATE_RECEIPT] = array(
        'subject' => _('Estimate') . ' {$NumCde}',
        'body' => '{$body}'
    );

    $alertContent[ALERT_SUPPLIER_COMMAND_RECEIPT] = array(
        'subject' => _('Order receipt') . ' {$NumCde}',
        'body' => '{$body}'
    );

    $alertContent[ALERT_CLIENT_LATE_PAYMENT] = array(
        'subject' => _('Deadline for payment exceeded: customer') . ' {$CustomerName}.',
        'body' => '{$body}'
    );

    $alertContent[ALERT_SUPPLIER_LATE_PAYMENT] = array(
        'subject' => _('Deadline for payment exceeded: order') . ' {$NumCde}, '
            . _('Supplier') . ' {$SuppierName}',
        'body' => _('Order') . ' {$NumCde},  ' . _('Invoice')
            . ' {$NumInvoice},  , ' . _('Supplier') . ' {$SuppierName}, '
            . _('Payment date limit exceeded.') . "\n"
            . _('Expected payment date') . ' : 16/06/04 18:08.'
    );

    $alertContent[ALERT_MOVEMENT_CANCELLED] = array(
        'subject' => _('Reference') . ' {$ProductBaseReference}: '
            . _('Cancellation of movement') . '  {$MvtTypeName} ' . _('from')
            . ' {$Quantity}.',
        'body' => '{$ProductName}:  ' . _('Movement')
            . ' {$LocationExecutedMovtId} ' . _('has been cancelled')
    );

    $alertContent[ALERT_PARTIAL_MOVEMENT] = array(
        'subject' => _('Partial. Order') . ' {$NumCde}. ' . _('Ref.')
            . ' {$ProductBaseReference}: {$TotalRealQuantity}/{$EnvisagedQuantity}.',
        'body' => _('Order') . ' {$NumCde}: '
            . _('partial movement, for reference')
            . ' {$ProductBaseReference} ,  {$TotalRealQuantity} '
            . _('moved units for').' {$EnvisagedQuantity} '
            ._('expected') . '.' . "\n"
            . '{$PartialMvtBodyAddon}' . "\n\n"
            . '{$Comment}'
    );

    $alertContent[ALERT_CHAIN_COMMAND_RECEIPT] = array(
        'subject' => _(' Carriage order receipt').' {$commandCommandNo}',
        'body' => '<h4>OnLogistics: '._('Carriage order').'</h4>
    <hr />
    <table width="100%">
      <tr>
        <td>
    		'._('Collection address').'<br /><br />
    		<b>{$expeditorName}</b><br />
    		{$expeditorStreetNo} {$expeditorStreetType} {$expeditorStreetName}
    		{$expeditorStreetAddons}<br />
    		{$expeditorZip}<br />
    		{$expeditorCity} - {$expeditorCountry}<br />
    		{$expeditorPhone}
    	</td>
    	<td>
    	    '._('Delivery address').'<br /><br />
    		<b>{$destinatorName}</b><br />
    		{$destinatorStreetNo} {$destinatorStreetType} {$destinatorStreetName}
    		{$destinatorStreetAddons}<br />
    		{$destinatorZip}<br />
    		{$destinatorCity} - {$destinatorCountry}<br />
    		{$destinatorPhone}
    	</td>
      </tr>
    </table>
    <p>
        '._('Madam, Sir').',<br /><br />
    '._('On').' {$commandDate}, '._('The following carriage service was booked, here is its description:').':
    </p>
    <ul>
    <li>'._('Customer name').': <b>{$customerName}</b></li>
    <li>'._('Order number').': <b>{$commandCommandNo}</b></li>
    <li>'._('Wished date').' {$commandDateType}: <b>{$commandWishedDate}</b></li>
    <li>'._('Incoterm').': <b>{$commandIncoterm}</b></li>
    <li>'._('Imputation number or account number').': <b>{$commandInputationNo}</b></li>
    <li>'._('Amount to recover on delivery').': <b>{$commandDeliveryPayment} €</b></li>
    </ul>
    <p>
    '._('Details of parcels to deliver').':
    </p>
    <table border="1" width="100%">
    	<tr>
    		<th>'._('Parcel type').'</th>
    		<th>'._('Quantity').'</th>
    		<th>'._('Unit weight (kg)').'</th>
    		<th>'._('Dimensions').'</th>
    		<th>'._('Stackable ratio').'</th>
    		<th>'._('Priority dimension').'</th>
    	</tr>
    	{$commandContent}
    </table>
    <table border="1" width="100%">
    	<tr>
    		<td><b>'._('Global discount').': </b>{$commandHanding} %</td>
    		<td><b>'._('Packing').': </b>{$commandPacking} €</td>
    		<td><b>'._('Insurance').': </b>{$commandInsurance} €</td>
    		<td><b>'._('VAT').': </b>{$commandTVA} €</td>
    		<td><b>'._('Amount excl. VAT').': </b>{$commandTotalPriceHT} €</td>
    		<td><b>'._('Amount incl. VAT').': </b>{$commandTotalPriceTTC} €</td>
    		<td><b>'._('Instalment').':</b> {$commandInstalment} €</td>
    		<td><b>'._('To pay').':</b> {$commandToPay} €</td>
    	</tr>
    	<tr>
    		<td colspan="7">
    			<b>'._('Comment').': {$commandComment}</b>
    		</td>
    	</tr>
    </table>

    <p>
    '._('Let us know if we can be of further assistance.<br/>The transport department.')
     . '</p>'
    );

    $alertContent[ALERT_PLANIFICATION_CHAIN] = array(
        'subject' => _('Chain schedule error').' "{$ChainReference}"',
        'body' => '{$Error}'
    );

    $alertContent[ALERT_COMMAND_DELETE] = array(
        'subject' => _('Order deletion').' {$Numcde}',
        'body' => _('Order').' {$Numcde} '
            . _('have just been deleted by the user').' {$UserName}'
    );

    $alertContent[ALERT_REINTEGRATION_STOCK] = array(
        'subject' => '{$NumCde} , ' . _('Reference')
            . ' {$ProductBaseReference} : {$CancellationType} ' . _('from') . ' {$Quantity}.',
        'body' => _('Order') . ' {$NumCde} , ' . _('Reference')
            . ' {$ProductBaseReference} : {$CancellationType} ' . _('from')
            . ' {$Quantity}.' . "\n"  . '{$Comment}.'
    );

    $alertContent[ALERT_COURSE_COMMAND_RECEIPT] = array(
        'subject' => _('Confirmation of your booking of the') .' {$CommandDate}, '
            . _('ref.') . ': {$CommandCommandNo}',
        'body' => _('Madam, Sir') . ",\n\n"
            . _('Your booking of the') . ' {$CommandDate} '
            . _('was successful.') . "\n\n"
            . _('Booking number') . ': {$CommandCommandNo}' . "\n\n"
            . _('Summary of your booking') . ':' . "\n"
            . '---------------------------------------' . "\n\n"
            . _('Customer') . ': {$CommandCustomer}' . "\n"
            . _('Wished time:') . ' {$CommandWishedStartDate} '
            . _('from') . ' {$CommandBeginHour} ' . _('to') . ' {$CommandEndHour}' . "\n"
            . _('Wished airplane').': {$CommandFlyType}' . "\n"
            . _('Assigned instructor').': {$CommandInstructor}' . "\n\n"
            . _('Comment').': {$CommandComment}'
            . "\n\n". _('Yours sincerely,') . "\n\n"
            . _('The department of sales')
    );

    $alertContent[ALERT_TEN_DAYS_SINCE_LAST_FLY] = array(
        'subject' => _('Last class was ten days ago.'),
        'body' => _('Customer').': {$AeroCustomerName}' . "\n"
            . _('Last flight date') . ': {$LastFlyDate}' . "\n\n"
            . _('Your last class was ten days ago, please plan a test for next class.')
    );

    $alertContent[ALERT_POTENTIAL_OVER] = array(
        'subject' => '{$Immatriculation}: '._('potential exceeded'),
        'body' => _('Airplane').' {$Immatriculation}' . "\n"
            . _('PN') . ': {$BaseReference}' . "\n"
            . _('SN/Lot') . ': {$SerialNumber}' . "\n\n"
            . _('Actual potential') . ': {$RealHourSinceOverall}' . "\n"
            . _('Virtual potential') . ': {$VirtualHourSinceOverall}' . "\n\n"
            . _('Maintenance services to do according to potential')
            . ':' . "\n" . '{$Prestations}'
    );

    $alertContent[ALERT_MOVEMENT_QTY_OVER] = array(
        'subject' => _('Order').' {$NumCde}: '
            . _('Moved quantity is greater than the one ordered'),
        'body' => _('Moved quantity for product reference')
            . ' {$ProductBaseReference} ' . _('exceeds expected quantity')
            . ': {$TotalRealQuantity} ' . _('moved units for')
            . ' {$EnvisagedQuantity} ' . _('expected.')
    );

    $alertContent[ALERT_MOVEMENT_NON_FORESEEABLE] = array(
        'subject' => _('Reference').' {$ProductBaseReference}: {$MvtTypeName}',
        'body' => _('Product designation').' : {$ProductName}' . "\n"
            . _('Moved quantity').': {$RealQuantity}' . "\n"
            . _('Comment').': {$Comment}'
    );

    $alertContent[ALERT_INSUFFICIENT_STOCK] = array(
        'subject' => _('Reference') . ' {$ProductBaseReference}: '
            . _('insufficient amount of stock for order.'),
        'body' => _('Stock quantity of product ref.') . ' {$ProductBaseReference} '
            ._('is insufficient for the order') . ' {$NumCde}.' . "\n\n"
            . _('Product designation').': {$ProductName}' . "\n\n"
            . _('Product supplier').': {$ProductSupplierName}'
    );

    $alertContent[ALERT_REINTEGRATION_STOCK_FACTURED] = array(
        'subject' => _('Order') . ' {$NumCde} : '
            . _('Reinstatement after billing, please control customer credit notes.'),
        'body' => _('For order') . ' {$NumCde}, '
            . _('a stock reinstatement was done but order was already charged.') . "\n"
            . _('Please verify if a customer credit note should be issued.')
    );

    $alertContent[ALERT_PRODUCT_CHANGED] = array(
        'subject' => _('Order').' {$NumCde} : ' . _('Reference substitution'),
        'body' => _('Expected reference') . ' {$OrderedBaseReference} '
            . _('has been replaced by') . ' {$ProductBaseReference}.'
    );

    $alertContent[ALERT_LICENCE_OUT_OF_DATE] = array(
        'subject' => _('License or qualification date expired for') . ' {$ActorName}',
        'body' => _('The following license or qualification dates expired for')
            . ' {$ActorName}:' . "\n\n" . '{$Body}'
    );

    $alertContent[ALERT_LICENCE_OUT_OF_DATE_ADMIN] = array(
        'subject' => _('License or qualification date expired'),
        'body' => _('The following license or qualification dates expired:').'
    {$Body}'
    );

    $alertContent[ALERT_CUSTOMER_SITUATION] = array(
        'subject' => _('Customer') . ' : {$actorName} : ' . _('Manually put in alert state'),
        'body' => _('Customer') . ' {$actorName} '
            . _('was set in alert by user')
            . ' {$userAccountName} ' . _('On') . ' {$date} ' . _('to')
            . ' {$heure}.<br><br>' . _('Comment') . ' : {$actionComment}'
    );

    $alertContent[ALERT_MAX_MEETING_DATE_EXCEEDED] = array(
        'subject' => _('Customer') . ' : {$actorName} : ' . _('Deadline for visit exceeded'),
        'body' => _('limit date for customer visit') . ' {$actorName} '
            . _('settled on') . ' {$date} ' . _('exceeded.')
    );

    $alertContent[ALERT_PRODUCTION_TASK_VALIDATION] = array(
        'subject' => _('Error during production task validation.'),
        'body' => _('Date') . ': {$date} - ' . _('User') . ': {$userAccountName}.'
            . "\n" . '{$msg}'
    );

    $alertContent[ALERT_INVOICE_TO_DOWNLOAD] = array(
        'subject' => _('Invoice') . ' {$numInvoice}',
        'body' => _('Madam, Sir') . ',' . "\n\n"
        . _('Invoice number ') . '{$numInvoice}'
        . _(' is available for printing at this address: https://www.onlogistics.com.') . "\n"
        . _('Feel free to contact us for any further informations.') . "\n"
        . _('Thanks for using our services.') . "\n\n"
        . '{$userAccountName}'
    );

    $alertContent[ALERT_INVOICE_BY_MAIL] = array(
        'subject' => _('Invoice') . ' {$numInvoice}',
        'body' => _('Madam, Sir') . ',' . "\n\n"
        . _('Please find enclosed the invoice number ')
        . '{$numInvoice}' . '.'
        . "\n" . _('Thanks for using our services.') . "\n\n"
        . '{$userAccountName}'
    );

    $alertContent[ALERT_CUSTOMER_WITHOUT_ORDER_SINCE_THIRTY_DAYS] = array(
        'subject' => _('Customers without order since 30 days'),
        'body' => _('The following customers did not place an order since 30 days: {$customerList}')
    );

    $alertContent[ALERT_FORECAST_EXPENSE_OVER_THE_BORD] = array(
        'subject' => _('Forecast of expenses'),
        'body' => _('The total of the following expense is greater than its forecast:') . "\n\n"
            . _('Model: {$flowtype}') . "\n"
            . _('Number: {$number}') . "\n"
            . _('Name: {$flow}') . "\n"
            . _('Piece no: {$pieceno}') . "\n"
            . _('Total: {$total}') . "\n"
            . _('Forecast: {$forecast}')
    );

    $alertContent[ALERT_FORECAST_RECEIPT_OVER_THE_BORD] = array(
        'subject' => _('Forecast of receipts'),
        'body' => _('The total of the following receipt is lower than its forecast:') . "\n\n"
            . _('Model: {$flowtype}') . "\n"
            . _('Number: {$number}') . "\n"
            . _('Name: {$flow}') . "\n"
            . _('Piece no: {$pieceno}') . "\n"
            . _('Total: {$total}') . "\n"
            . _('Forecast: {$forecast}')
    );

    $alertContent[ALERT_GED_DOCUMENT_UPLOADED] = array(
        'subject' => _('Document upload confirmation'),
        'body'    => _('Document "{$name}" was uploaded by user "{$user}" on {$date}')
    );

    $alertContent[ALERT_GED_DOCUMENT_UPDATED] = array(
        'subject' => _('Document update confirmation'),
        'body'    => _('Document "{$name}" was updated by user "{$user}" on {$date}')
    );

    $alertContent[ALERT_GED_DOCUMENT_DELETED] = array(
        'subject' => _('Document delete confirmation'),
        'body'    => _('Document "{$name}" was deleted by user "{$user}" on {$date}')
    );

    $alertContent[ALERT_GED_DOCUMENT_ASSIGNED] = array(
        'subject' => _('Document assignment confirmation'),
        'body'    => _('Document "{$name}" was assigned to task "{$task}" of order "{$order}" by user "{$user}" on {$date}')
    );

    $alertContent[ALERT_GED_DOCUMENT_UNASSIGNED] = array(
        'subject' => _('Document unassignment confirmation'),
        'body'    => _('Document "{$name}" was unassigned from its task by user "{$user}" on {$date}')
    );

    $alertContent[ALERT_GED_ACK_OUT_OF_DATE] = array(
        'subject' => _('Overdue tasks for customer {$customerName}, order {$commandNo}'),
        'body'    => '<p>' . _('The following tasks are out of date for this customer:</p> 
              <table border="1" width="100%">
            	<tr>
            		<th>'._('Operation').'</th>
            		<th>'._('Task').'</th>
            		<th>'._('Scheduled end').'</th>
            	</tr>
            	{$commandContent}
              </table>')
    );
    
    return $alertId == 0?$alertContent:$alertContent[$alertId];
}
?>
