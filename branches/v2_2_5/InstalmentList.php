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
 * @version   SVN: $Id: InstalmentList.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once('config.inc.php');
$Auth = Auth::Singleton();
$Auth->checkProfiles();
// Si on doit imprimer un Avoir
$displayGrid =  0;
$printInstalment = '';
if (isset($_GET['printId']) && $_GET['printId'] > 0) {
	$Instalment = Object::load('Instalment', $_GET['printId']);
	if (Tools::isEmptyObject($Instalment)) {
	    Template::errorDialog(E_MSG_TRY_AGAIN, 'javascript:window.close()',
                BASE_POPUP_TEMPLATE);
	  	exit;
	}
	$printInstalment = "
	<SCRIPT language=\"javascript\">
	var w=window.open(\"InstalmentEdit.php?thId=" . $_GET['printId']
        . "\",\"popup\",\"width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no\");
	</SCRIPT>";
}

//Database::connection()->debug = true;

/*  Contruction du formulaire de recherche  */
$form = new SearchForm('Instalment');

$form->addElement('text', 'DocumentNo', _('Reference'));
$form->addElement('select', 'Modality', _('Modality'), array(
    array('##'=>MSG_SELECT_AN_ELEMENT)+ TermsOfPaymentItem::getPaymentModalityConstArray()));
$form->addElement('text', 'Command', _('Command number'), array(),
        array('Path' => 'Command.CommandNo'));
$form->addElement('text', 'Customer', _('Customer'), array(),
		array('Path' => 'Command.SupplierCustomer.Customer.Name'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
        array('', 'onclick="$(\\\'Date1\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name'   => 'StartDate',
			  'Format' => array('minYear' => date('Y') - 5, 'maxYear' => date('Y')),
			  'Path' => 'Date'),
		array('Name'   => 'EndDate',
			  'Format' => array('minYear' => date('Y') - 5, 'maxYear' => date('Y')),
			  'Path' => 'Date'),
		array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
			  'StartDate' => array('Y' => date('Y')))
						  );


if(isset($_REQUEST['CommandId'])) {
    $Command = Object::load('Command', $_REQUEST['CommandId']);
    $form->setDefaultValues(array('Command' => $Command->getCommandNo()));
    $displayGrid = 1 ;
    $form->addAction(array('URL' => 'InstalmentAdd.php'));
    $form->addElement('hidden', 'CommandId', 'pouet' );
    $form->setDefaults(array('CommandId'=> $_REQUEST['CommandId'])); 
}


/*  Affichage du Grid  */
if (true === $form->DisplayGrid($displayGrid)) {

	// si recherche lancee sans critere de date, il faut tuer la var de session
    // pour la case a cocher
	if (isset($_POST['formSubmitted']) && !isset($_POST['DateOrder1'])) {
	    unset($_SESSION['DateOrder1']);
	}

	/*  Construction du filtre  */
	$Filter = SearchTools::filterAssembler($form->buildFilterComponentArray());

	$grid = new Grid();
	$grid->itemPerPage = 100;

	$grid->NewAction('Delete', array('EntityType' => 'Instalment',
	       'TransmitedArrayName' => 'thId'));

	$grid->NewAction('Export', array('FileName' => 'Acomptes'));

    if(isset($_REQUEST['returnURL'])) {
        $grid->NewAction('Cancel', array('Caption'=>_('State of orders'), 
            'ReturnURL'=>$_REQUEST['returnURL']));
    }

	/*  Colonnes du grid  */
	$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%DocumentNo%'));
	$grid->NewColumn('FieldMapper', _('Date'),
            array('Macro' => '%Date|formatdate@DATE_SHORT%'));
	$grid->NewColumn('FieldMapperWithTranslation', _('Order'),
        array('Macro' => '%Command.CommandNo%' , '0' => 'N/A',
            'TranslationMap' => array(0 => _('N/A'))));
	$grid->NewColumn('FieldMapper', _('Customer'),
            array('Macro' => '%Command.SupplierCustomer.Customer.Name%'));
	$grid->NewColumn('FieldMapper', _('Supplier'),
            array('Macro' => '%Command.SupplierCustomer.Supplier.Name%'));
    $grid->NewColumn('MultiPrice', _('Amount'), array('Method' => 'getTotalPriceTTC'));

    $Filter = isset($Filter)?$Filter:array();
	$Order = array('Date' => SORT_ASC);

	$form->displayResult($grid, true, $Filter, $Order, '', array(),
	       array('beforeForm' => $printInstalment));
}

else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $printInstalment . $form->render() . '</form>');
}

?>
