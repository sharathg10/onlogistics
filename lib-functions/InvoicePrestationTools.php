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

require_once('ProductCommandTools.php');

// includeSessionRequirements() {{{
/**
 *
 *
 * @access public
 * @return void
 **/
function includeSessionRequirements() {
    require_once('Objects/Actor.php');
    require_once('Objects/Customer.php');
    require_once('Objects/Supplier.php');
    require_once('Objects/AeroCustomer.php');
    require_once('Objects/AeroSupplier.php');
    require_once('Objects/AeroOperator.php');
    require_once('Objects/AeroInstructor.php');
    require_once('Objects/LocationExecutedMovement.php');
    require_once('Objects/MovementType.php');
    require_once('Objects/ExecutedMovement.php');
    require_once('Objects/ActivatedMovement.php');
    require_once('Objects/ActivatedChainOperation.php');
    require_once('Objects/ActivatedChainTask.php');
    require_once('Objects/ActivatedChain.php');
    require_once('Objects/Operation.php');
    require_once('Objects/ProductCommand.php');
    require_once('Objects/ChainCommand.php');
    require_once('Objects/ChainCommandItem.php');
    require_once('Objects/ProductCommandItem.php');
    require_once('Objects/CourseCommand.php');
    require_once('Objects/PrestationCommand.php');
//    require_once('Objects/SupplierCustomer.php');
}

// }}}
// checkDataBeforeSubmit() {{{

/**
 * Effectue les controles sur les saisies
 * @access public
 * @return void
 */
function checkDataBeforeSubmit() {
	// Verification que le nb de prestations a facturer est > 0
    if (0 == array_sum($_REQUEST['qty'])) {
    //if (count($_REQUEST['gridItems']) < 0) {
        Template::errorDialog(_('Please select at least one service to charge.'),
                getPrestationErrorURL());
        exit;
    }
    // Verification de la date
    if (empty($_REQUEST['StartDate'])) {
        Template::errorDialog(E_MSG_CHOICE_DATE, getPrestationErrorURL());
        exit;
    }
    // Verification du No de Commande et Facture saisis
    if ($_REQUEST['cmdNumber']) {
        $cmdMapper = Mapper::singleton('Command');
        if ($cmdMapper->alreadyExists(array('CommandNo' => $_REQUEST['cmdNumber']))) {
            Template::errorDialog(
                _('Provided order number is already allocated, please correct.'),
                getPrestationErrorURL());
            exit;
        }
    }
    if ($_REQUEST['invoiceDocumentNo']) {
        $cmdMapper = Mapper::singleton('Invoice');
        if ($cmdMapper->alreadyExists(array('DocumentNo' => $_REQUEST['invoiceDocumentNo']))) {
            Template::errorDialog(
                _('A document with the same number already exists, please correct.'),
                getPrestationErrorURL()
            );

            exit;
        }
    }
    // vérifier que expeditorSite est renseigné
    if (empty($_REQUEST['SupplierSite'])
        || strcmp($_REQUEST['SupplierSite'], '##')==0) {
        Template::errorDialog(_('Please select a supplier billing site.'),
                       getPrestationErrorURL());
        exit;
    }
    // vérifier que destinatorSite est renseigné
    if (empty($_REQUEST['CustomerSite'])
        || strcmp($_REQUEST['CustomerSite'], '##')==0) {
        Template::errorDialog(_('Please select a customer billing site.'),
                getPrestationErrorURL());
        exit;
    }
}

// }}}
// getPrestationErrorURL() {{{

/**
 * Retourne la bonne url en cas d'erreur, pour conserver les saisies
 * @access public
 * @return string
 */
function getPrestationErrorURL(){
	// Les chps de saisie:
    $fields = array('cmdNumber', 'invoiceDocumentNo', 'SupplierSite',
         'CustomerSite', 'Commercial', 'Port', 'Emballage', 'Assurance',
         'Instalment', 'InstalmentModality', 'GlobalHanding', 'cmdComment');
    $stringToPass = UrlTools::buildURLFromRequest($fields);
    return 'PrestationInvoiceAddEdit.php?' . $stringToPass;
}

// }}}
// getJSforInvoice() {{{

/**
 * Gestion de l'edition de facture si necessaire:
 * Ouverture d'un popup en arriere-plan, impression du contenu (pdf),
 * et fermeture de ce popup
 *
 * @access public
 * @return string
 */
function getJSforInvoice() {
    if (!isset($_REQUEST['InvoiceId'])) {
    	return '';
    }
    $Id = (is_array($_REQUEST['InvoiceId']))?
            $_REQUEST['InvoiceId'][0]:$_REQUEST['InvoiceId'];
	$print = (isset($_REQUEST['print']))?$_REQUEST['print']:0;
	$EditInvoice = "
	<SCRIPT language=\"javascript\">
	function kill() {
		window.open(\"KillPopup.html\",'popback','width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no');
	}
	function TimeToKill(sec) {
		setTimeout(\"kill()\",sec*1000);
	}
	var w=window.open(\"EditInvoice.php?InvoiceId=" . $Id ."&print=".$print."\",\"popback\",\"width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no\");
	w.blur();
	//TimeToKill(22);
	</SCRIPT>";
	return $EditInvoice;
}

// }}}
?>
