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
require_once('SQLRequest.php');
$Auth = Auth::Singleton();
$Auth->checkProfiles();
$displayGrid =  0;
if (isset($_GET['printId']) && $_GET['printId'] > 0) {
	$Instalment = Object::load('Instalment', $_GET['printId']);
	if (Tools::isEmptyObject($Instalment)) {
	    Template::errorDialog(E_MSG_TRY_AGAIN, 'javascript:window.close()',
                BASE_POPUP_TEMPLATE);
	  	exit;
	}
}

//Database::connection()->debug = true;
$pageTitle = _('Expected Instalments');


/*  Contruction du formulaire de recherche  */
$form = new SearchForm();

$form->addElement('text', 'Command', _('Command number'), array(),
        array('Path' => 'Command.CommandNo'));
$form->addElement('text', 'Customer', _('Customer'), array(),
		array('Path' => 'SupplierCustomer.Customer.Name'));
$form->addElement('text', 'Supplier', _('Supplier'), array(),
		array('Path' => 'SupplierCustomer.Supplier.Name'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
        array('', 'onclick="$(\\\'Date1\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name'   => 'StartDate',
			  'Format' => array('minYear' => date('Y') - 5, 'maxYear' => date('Y')),
			  'Path' => 'InstalmentDate'),
		array('Name'   => 'EndDate',
			  'Format' => array('minYear' => date('Y') - 5, 'maxYear' => date('Y')),
			  'Path' => 'InstalmentDate'),
		array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
			  'StartDate' => array('Y' => date('Y')))
          );

//$form->addAction(array('URL' => 'InstalmentAdd.php'));


if(isset($_REQUEST['CommandId'])) {
    $Command = Object::load('Command', $_REQUEST['CommandId']);
    $form->setDefaultValues(array('Command' => $Command->getCommandNo()));
    $_REQUEST['Command'] = $Command->getCommandNo() ;
    $displayGrid = 1 ;
}


/*  Affichage du Grid  */
if (true === $form->DisplayGrid($displayGrid)) {

	// si recherche lancee sans critere de date, il faut tuer la var de session
    // pour la case a cocher
	if (isset($_POST['formSubmitted']) && !isset($_POST['DateOrder1'])) {
	    unset($_SESSION['DateOrder1']);
	}

	/*  Construction du filtre  */
    SearchTools::inputDataInSession();
    $Collection = new Collection();

    $reqCustomer = "" ;
    $reqSupplier = "" ;
    $reqCommand = "" ;
    $reqStartDate = "" ;
    $reqEndDate = "" ;
    if(isset( $_REQUEST['Customer'] )) $reqCustomer = $_REQUEST['Customer'] ;
    if(isset( $_REQUEST['Supplier'] )) $reqSupplier = $_REQUEST['Supplier'] ;
    if(isset( $_REQUEST['Command'] )) $reqCommand = $_REQUEST['Command'] ;
    if(isset( $_REQUEST['StartDate'] )) $reqStartDate = $_REQUEST['StartDate'] ;
    if(isset( $_REQUEST['EndDate'] )) $reqEndDate = $_REQUEST['EndDate'] ;

    $rs = request_CommandsWithInstalments( 
        $reqCustomer, $reqSupplier, $reqCommand, $reqStartDate, $reqEndDate);
    $i = 0;

    while (!$rs->EOF){
        $pq = 'Instalment'.$i;
        $$pq = Object::load('ExpectedInstalment');
        $$pq->setId($rs->fields['CommandId']);
        $$pq->setTotalPriceTTC($rs->fields['Instalment']);
        $$pq->setDate($rs->fields['InstalmentDate']);
        $$pq->setPercentTotal($rs->fields['PercentTotal']);
        $$pq->setCommandTotal($rs->fields['CommandTotal']);
        $$pq->setCommand($rs->fields['CommandId']);

        if( ($$pq->Command->getTotalInstalments() < $$pq->getTotalPriceTTC()) &&
            (Tools::isEmptyObject($$pq->Command->getInvoiceCollection())) )
        {
                $Collection->setItem($$pq);
        }

    	$rs->moveNext();
    	$i++;
    	unset($$pq);
    }

    if (!($Collection instanceof Collection)) {
    	die(E_ERROR_GENERIC);
    }

    $grid = new Grid();
    $grid->displayCancelFilter = false;
//    $grid->withNoCheckBox = true;
    $grid->withNoSortableColumn = true;

    $grid->NewAction('Print', array());
    $grid->NewAction('Export', array('FileName'=>'ExpectedInstalments'));
    $grid->NewAction('Redirect', array('Caption' => _('Pay this Instalment'),
        'URL' => 'InstalmentAdd.php?CommandId=%d',
    'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
    UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL)));

	/*  Colonnes du grid  */
	$grid->NewColumn('FieldMapper', _('Date'),
            array('Macro' => '%Date|formatdate@DATE_SHORT%'));
	$grid->NewColumn('FieldMapper', _('Customer'),
            array('Macro' => '%Command.SupplierCustomer.Customer.Name%'));
	$grid->NewColumn('FieldMapper', _('Supplier'),
            array('Macro' => '%Command.SupplierCustomer.Supplier.Name%'));
	$grid->NewColumn('FieldMapperWithTranslation', _('Order'),
        array('Macro' => '%Command.CommandNo%' , '0' => 'N/A',
            'TranslationMap' => array(0 => _('N/A'))));
	$grid->NewColumn('MultiPrice', _('Instalments Paid'),
            array('Macro' => '%Command.TotalInstalments%'));
    $grid->NewColumn('MultiPrice', _('Order Total'), array('Method' => 'getCommandTotal'));
	$grid->NewColumn('FieldMapper', _('Percentage'),
            array('Macro' => '%PercentTotal%'));
    $grid->NewColumn('MultiPrice', _('Amount'), array('Method' => 'getTotalPriceTTC'));

    $form->setDisplayForm(false);  // Par defaut, on n'affiche pas le form de rech
    $form->setItemsCollection($Collection);
    $form->displayResult($grid, true, array(), array(), $pageTitle);

} else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}

?>
