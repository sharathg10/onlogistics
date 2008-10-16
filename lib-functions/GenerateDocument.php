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

function generateDocument($document, $reedit=0, $output='I') {
    require_once('DocumentGenerator.php');
    $context = Preferences::get('TradeContext', array());
    if ($document instanceof Invoice) {
        $cmd = $document->getCommand();
        if ($cmd instanceof CourseCommand) {
    	    $generator_name = 'CourseCommandInvoiceGenerator';
        } elseif ($cmd instanceof ChainCommand) {
            $generator_name = 'ChainCommandInvoiceGenerator';
        } elseif ($cmd instanceof PrestationCommand) {
            $generator_name = 'PrestationInvoiceGenerator';
        } else {
            if (in_array('readytowear', $context) && $cmd->getType() == Command::TYPE_CUSTOMER) {
                $generator_name = 'RTWInvoiceGenerator';
            } else {
                $generator_name = 'InvoiceGenerator';
            }
        }
    } elseif ($document instanceof Collection) { // Facturation multiple
        $generator_name = 'InvoiceCollectionGenerator';
        $count = $document->getCount(); // Collection d'Invoice
        for($i = 0; $i < $count; $i++) {
            $invoice = $document->getItem($i);
            $pdfDoc = $invoice->getPDFDocument();
            // On sauve le doc pdf en base si besoin
            if (Tools::isEmptyObject($pdfDoc)) {
                Database::connection()->startTrans();
                // pour sauvegarde du doc en base
                $generator = new InvoiceGenerator($invoice, true);
        	    $pdf = $generator->render();
                $data = $pdf->output('', 'S');
                $pdfDoc = new PDFDocument();
                $pdfDoc->setData($data);
                $pdfDoc->save();
                $invoice->setPDFDocument($pdfDoc->getId());
                $invoice->save();
                Database::connection()->completeTrans();
            }
        }
        // pour affichage sur navigateur
        $generator = new $generator_name($document, $reedit);
	    $pdf = $generator->render();
        $name = $reedit?'first_reedition.pdf':'original.pdf';
        return $pdf->output($name, $output);
    } else if (get_class($document) == 'DeliveryOrder') { 
        $cmd = $document->getCommand();
        if (in_array('readytowear', $context) && $cmd->getType() == Command::TYPE_CUSTOMER) {
            $generator_name = 'RTWDeliveryOrderGenerator';
        } else {
            $generator_name = 'DeliveryOrderGenerator';
        }
    } else if (get_class($document) == 'CommandReceipt') { 
        $cmd = $document->getCommand();
        if ($cmd instanceof ChainCommand) {
            $generator_name = 'ChainCommandReceiptGenerator';
        } else {
            if (in_array('readytowear', $context) && $cmd->getType() == Command::TYPE_CUSTOMER) {
                $generator_name = 'RTWCommandReceiptGenerator';
            } else {
                $generator_name = 'CommandReceiptGenerator';
            }
        }
    } else if (get_class($document) == 'Estimate') { 
        $cmd = $document->getCommand();
        if ($cmd instanceof ChainCommand) {
            $generator_name = 'ChainCommandEstimateGenerator';
        } else {
            if (in_array('readytowear', $context) && $cmd->getType() == Command::TYPE_CUSTOMER) {
                $generator_name = 'RTWEstimateGenerator';
            } else {
                $generator_name = 'EstimateGenerator';
            }
        }
    } else {
        $generator_name = get_class($document) . 'Generator';
    }
    if (class_exists($generator_name)) {
        $pdfDoc = $document->getPDFDocument();
        // Si le pdf existe deja en base
        if (!Tools::isEmptyObject($pdfDoc)) {
            Tools::redirectTo('DocumentReedit.php?id=' . $document->getId());
            exit();
        }
        Database::connection()->startTrans();
        // pour sauvegarde du doc en base
        $generator = new $generator_name($document, true);
	    $pdf = $generator->render();
        $data =  $pdf->output('', 'S');
        $pdfDoc = new PDFDocument();
        $pdfDoc->setData($data);
        $pdfDoc->save();
        $document->setPDFDocument($pdfDoc->getId());
        $document->save();
        Database::connection()->completeTrans();
        // pour affichage sur navigateur
        $generator = new $generator_name($document, $reedit);
	    $pdf = $generator->render();
        $name = $reedit?'first_reedition.pdf':'original.pdf';
        return $pdf->output($name, $output);
    }
    return '';
}

?>
