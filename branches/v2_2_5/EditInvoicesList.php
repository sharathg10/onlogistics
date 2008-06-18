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

session_cache_limiter('private');
require_once('config.inc.php');
require_once('DocumentGenerator.php');
require_once('Objects/Invoice.php');
require_once('Objects/InvoicesList.php');
require_once('Objects/SupplierCustomer.php');
require_once('Objects/Customer.php');

$auth = Auth::Singleton();

if (!isset($_REQUEST['ivId'])) {
    die('1 : '._('An error occurred, list of invoices could not be printed.'));
}

$full = !isset($_GET['full'])?false:$_GET['full'];
$invoiceMapper = Mapper::singleton('Invoice');

// on construit une InvoicesList non sauvegardée
$invoicesList = new InvoicesList();
$invoiceCol = new Collection();
$customerId = false;
foreach ($_REQUEST['ivId'] as $value) {
    $invoice = $invoiceMapper->load(array('Id'=>$value));
    $destinatorId = Tools::getValueFromMacro($invoice, '%Command.Destinator.Id%');
    if(!$customerId) {
        $customerId = $destinatorId;
    } elseif ($customerId != $destinatorId) {
        Template::errorDialog(
            _('All selected invoices must have the same customer'),
            'javascript:window.close();', BASE_POPUP_TEMPLATE);
        exit();
    }
    $invoiceCol->setItem($invoice);
}
$currency = $invoice->getCurrency();
$invoicesList->setInvoiceCollection($invoiceCol);
$actorMapper = Mapper::singleton('Actor');
// supplier customer
$customer = $actorMapper->load(array('Id' => $customerId));
$supplierCustomer = new SupplierCustomer();
$connectedUser = $auth->getUser();
$supplierCustomer->setSupplier($connectedUser->getActor());
$supplierCustomer->setCustomer($customer);
$invoicesList->setSupplierCustomer($supplierCustomer);
// dates
if (isset($_SESSION['beginDate']) && isset($_SESSION['endDate'])) {
    $invoicesList->setBeginDate($_SESSION['beginDate']);
    $invoicesList->setEndDate($_SESSION['endDate']);
}
// currency
$invoicesList->setCurrency($currency);
// documentModel
$documentModel = $invoicesList->FindDocumentModel();
if (!(false == $documentModel)) {
    $invoicesList->setDocumentModel($documentModel);
}

// generation du document
// document non sauvegarde en base
$generator = new InvoicesListGenerator($invoicesList, $full);
$pdf = $generator->render();
$pdf->output();

?>
