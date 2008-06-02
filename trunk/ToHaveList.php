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
$Auth = Auth::Singleton();
$Auth->checkProfiles();
// Si on doit imprimer un Avoir
$printToHave = '';
if (isset($_GET['printId']) && $_GET['printId'] > 0) {
	$ToHave = Object::load('ToHave', $_GET['printId']);
	if (Tools::isEmptyObject($ToHave)) {
	    Template::errorDialog(E_MSG_TRY_AGAIN, 'javascript:window.close()',
                BASE_POPUP_TEMPLATE);
	  	exit;
	}
	$printToHave = "
	<SCRIPT language=\"javascript\">
	var w=window.open(\"ToHaveEdit.php?thId=" . $_GET['printId']
        . "\",\"popup\",\"width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no\");
	</SCRIPT>";
}


/*  Contruction du formulaire de recherche  */
$form = new SearchForm('ToHave');

$form->addElement('text', 'DocumentNo', _('Credit note number'));
$form->addElement('select', 'Type', _('Type'), array(
    array('##'=>MSG_SELECT_AN_ELEMENT)+ ToHave::getTypeConstArray()));
$form->addElement('text', 'InvoiceNo', _('Invoice number'), array(),
        array('Path' => 'InvoicePayment().Invoice.DocumentNo'));
$form->addElement('text', 'Customer', _('Customer'), array(),
		array('Path' => 'SupplierCustomer.Customer.Name'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
        array('', 'onclick="$(\\\'Date1\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name'   => 'StartDate',
			  'Format' => array('minYear' => date('Y') - 5, 'maxYear' => date('Y')),
			  'Path' => 'EditionDate'),
		array('Name'   => 'EndDate',
			  'Format' => array('minYear' => date('Y') - 5, 'maxYear' => date('Y')),
			  'Path' => 'EditionDate'),
		array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
			  'StartDate' => array('Y' => date('Y')))
						  );
$form->addAction(array('URL' => 'ToHaveAdd.php'));

/*  Affichage du Grid  */
if (true === $form->DisplayGrid()) {

	// si recherche lancee sans critere de date, il faut tuer la var de session
    // pour la case a cocher
	if (isset($_POST['formSubmitted']) && !isset($_POST['DateOrder1'])) {
	    unset($_SESSION['DateOrder1']);
	}

	/*  Construction du filtre  */
	$Filter = SearchTools::filterAssembler($form->buildFilterComponentArray());

	$grid = new Grid();
	$grid->itemPerPage = 100;

	$grid->NewAction('AddEdit', array('Action' => 'Add', 'URL' => 'ToHaveAdd.php'));

	$grid->NewAction('Delete', array('EntityType' => 'ToHave',
	       'TransmitedArrayName' => 'thId'));
	$grid->NewAction('Redirect', array('Caption' => _('Details'),
            'Title' => _('Show details'),
            'URL' => 'ToHavePaymentList.php?thId=%d'));

	$grid->NewAction('Redirect', array('Caption' => _('Print a credit note'),
            'TargetPopup' => true,
            'TransmitedArrayName' => 'thId',
            'URL' => 'ToHaveEdit.php?thId=%d'));
	$grid->NewAction('Print');
	$grid->NewAction('Export', array('FileName' => 'Avoirs'));

	/*  Colonnes du grid  */
	$grid->NewColumn('FieldMapper', _('Customer'),
            array('Macro' => '%SupplierCustomer.Customer.Name%'));
	$grid->NewColumn('FieldMapper', _('Supplier'),
            array('Macro' => '%SupplierCustomer.Supplier.Name%'));
	$grid->NewColumn('FieldMapper', _('Date'),
            array('Macro' => '%EditionDate|formatdate@DATE_SHORT%'));
	$grid->NewColumn('FieldMapper', _('Credit note number'), array('Macro' => '%DocumentNo%'));
	$grid->NewColumn('MultiPrice', _('Amount incl. VAT'),
            array('Method' => 'getTotalPriceTTC'));
	$grid->NewColumn('MultiPrice', _('Remaining amount incl. VAT'),
            array('Method' => 'getRemainingTTC'));

    $Filter = isset($Filter)?$Filter:array();
	$Order = array('SupplierCustomer.Customer.Name' => SORT_ASC,
	       'EditionDate' => SORT_ASC);

	$form->displayResult($grid, true, $Filter, $Order, '', array(),
	       array('beforeForm' => $printToHave));
}

else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $printToHave . $form->render() . '</form>');
}
?>
