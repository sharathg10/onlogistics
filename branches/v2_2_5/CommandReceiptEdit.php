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
require_once('DocumentGenerator.php');

if(isset($_GET['cmdId'])) {
    $command = Object::load('Command', $_GET['cmdId']);
} elseif(isset($_GET['id'])) {
    $abstractDoc = Object::load('AbstractDocument', $_GET['id']);
    $command = $abstractDoc->getCommand();
    if(!($command instanceof Command)) {
        Template::errorDialog(
            _('Please select a document related to an order in order to print the receipt.'), 
            'javascript:window.close();', BASE_POPUP_TEMPLATE);
        exit();
    }
} else {
    Template::errorDialog(E_MSG_TRY_AGAIN, 'javascript:window.close();', BASE_POPUP_TEMPLATE);
    exit();
}

if($command instanceof ProductCommand) {
    $doc = new CommandReceipt($command);
} elseif($command instanceof ChainCommand) {
    $doc = new ChainCommandReceipt($command);
} else {
    Template::errorDialog(
        _('Receipt reprinting is impossible for this type of document.'), 
        'javascript:window.close();', BASE_POPUP_TEMPLATE);
    exit();
}
// document non sauve en base
$pdf = $doc->render();
$pdf->output('doc.pdf', 'I');
?>
