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

if (isset($_GET['estId'])) {
    $commandReceipt = false;
    $command = Object::load('Command', $_GET['estId']);
} else if (isset($_GET['id'])) {
    $commandReceipt = Object::load('CommandReceipt', $_GET['id']);
    $command = $commandReceipt->getCommand();
    if(!($command instanceof Command)) {
        Template::errorDialog(
            _('Please select a document related to an order in order to print the receipt.'), 
            'javascript:window.close();',
            BASE_POPUP_TEMPLATE
        );
        exit(1);
    }
} else {
    Template::errorDialog(
        E_MSG_TRY_AGAIN,
        'javascript:window.close();',
        BASE_POPUP_TEMPLATE
    );
    exit(1);
}

if (!$command->getIsEstimate()) {
    Template::errorDialog(
        _('Selected item is not an estimate.'), 
        'javascript:window.close();',
        BASE_POPUP_TEMPLATE
    );
    exit(1);
}

if (!$commandReceipt) {
    $commandReceipt = Object::load('CommandReceipt', array(
        'Command'    => $command->getId(),
        'DocumentNo' => $command->getCommandNo()
    ));
}
if (!($commandReceipt instanceof CommandReceipt)) {
    $commandReceipt = new CommandReceipt();
    $commandReceipt->setCommand($command);
    $commandReceipt->setCommandType($command->getType());
    $commandReceipt->setDocumentNo($command->getCommandNo());
    $commandReceipt->setSupplierCustomer($command->getSupplierCustomer());
    $commandReceipt->setCurrency($command->getCurrency());
    $commandReceipt->setEditionDate(date('Y-m-d H:i:s'));
    if (($dmodel = $commandReceipt->findDocumentModel())) {
        $commandReceipt->setDocumentModel($dmodel);
    }
    $commandReceipt->save();
}
if ($command instanceof ProductCommand) {
    $doc = new CommandEstimateReceiptGenerator($commandReceipt);
} else if ($command instanceof ChainCommand) {
    $doc = new ChainCommandEstimateReceiptGenerator($commandReceipt);
} else {
    Template::errorDialog(
        _('Receipt reprinting is impossible for this type of document.'), 
        'javascript:window.close();',
        BASE_POPUP_TEMPLATE
    );
    exit(1);
}
// document non sauve en base
$pdf = $doc->render();
$pdf->output('doc.pdf', 'I');

?>
