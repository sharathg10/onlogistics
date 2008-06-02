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
require_once('GenerateDocument.php');
$auth = Auth::Singleton();
$errorBody = _('An error occurred, invoice could not be printed.');

if (!isset($_REQUEST['InvoiceId'])) {
    die('1 : ' . $errorBody);
}

SearchTools::prolongDataInSession();  // prolonge les datas du form de recherche en session


$invoiceMapper = Mapper::singleton('Invoice');
// Si facturation multiple
if (is_array($_REQUEST['InvoiceId']) && count($_REQUEST['InvoiceId']) > 1) {
    $invoiceColl = $invoiceMapper->loadCollection(array('Id' => $_REQUEST['InvoiceId']));
    generateDocument($invoiceColl, $_REQUEST['print']);
	exit;
}
// Sinon
// Attention, on peut aussi venir de la facturation multiple, mais avec 1 seule
// facture a editer
$invoiceId = (is_array($_REQUEST['InvoiceId']))?
    $_REQUEST['InvoiceId'][0]:$_REQUEST['InvoiceId'];
$Invoice = $invoiceMapper->load(array('Id' => $invoiceId));

if ($Invoice instanceof Invoice) {
	generateDocument($Invoice, $_REQUEST['print']);
	exit;
} else {
    die('2 : ' . $errorBody);
}

?>