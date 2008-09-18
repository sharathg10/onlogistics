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
require_once('Objects/AbstractDocument.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles();
SearchTools::ProlongDataInSession();
$FilterComponentArray = array();
/*  Contruction du formulaire de recherche */
$form = new SearchForm('AbstractDocument');

$profileforFullList = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
        UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL);
if(in_array($Auth->getProfile(), $profileforFullList)) {
    $documentList = AbstractDocument::getDocumentList();
} else {
    $documentList = array(
        'PackingList'    => _('Packing list'),
        'DeliveryOrder'  => _('Delivery order'),
        'ForwardingForm' => _('Forwarding form')
    );
}
$form->addElement('select', 'ClassName', _('Document type'), array($documentList));
$form->AddBlankElement();  // pour la mise en page
$form->addElement('text', 'CommandNo', _('Order number'), array(),
        array('Path' => 'Command.CommandNo'));
$form->addElement('text', 'DocumentNo', _('Document number'));
$form->addElement('text', 'Destinator', _('Customer'), array(),
		array('Path' => 'Command.Destinator.Name'));
$form->addElement('text', 'Expeditor', _('Supplier'), array(),
		array('Path' => 'Command.Expeditor.Name'));
// $form->AddBlankElement();  // pour la mise en page
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
		array('', 'onclick="$(\\\'Date1\\\').'
              . 'style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));
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

if (in_array($Auth->getProfile(),
        array(UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES))) {
	$FilterComponentArray[] = new FilterComponent(
	   new FilterRule(
	       'SupplierCustomer.Supplier',
	       FilterRule::OPERATOR_EQUALS,
	       $Auth->getActorId()
	   ),
	   new FilterRule(
	       'SupplierCustomer.Customer',
	       FilterRule::OPERATOR_EQUALS,
	       $Auth->getActorId()
	   ),
	   FilterComponent::OPERATOR_OR
	);
}

/*  Affichage du Grid  */
if (true == $form->displayGrid()){// || $form->isFirstArrival()) {
	// si recherche lancee sans critere de date, il faut tuer la var de session
    // pour la case a cocher
	if (isset($_REQUEST['formSubmitted']) && !isset($_REQUEST['DateOrder1'])) {
	    unset($_SESSION['DateOrder1']);
	}
    $clsName = SearchTools::requestOrSessionExist('ClassName');
    if (in_array($clsName, array('DeliveryOrder', 'CommandReceipt', 'Invoice', 'Estimate'))) {
        $FilterComponentArray[] = new FilterComponent(
            new FilterRule('Command', FilterRule::OPERATOR_GREATER_THAN, 0)
        );
    }
	$grid = new Grid();
	$grid->itemPerPage = 50;

	$grid->NewAction('Redirect', array('Caption' => _('Reprinting'),
            'TargetPopup' => true,
        	'URL' => 'DocumentReedit.php?id=%d',
            'ReturnURL' => 'javascript:window.close();'));
    /*
    $grid->NewAction('Redirect', array(
            'Caption' => _('Order receipt'),
            'Title' => _('Print order receipt'),
            'TargetPopup' => true,
    	    'URL' => 'CommandReceiptEdit.php?id=%d',
            'ReturnURL' => 'javascript:window.close();',
            'Profiles' => $profileforFullList));
    */

    $grid->NewAction('Redirect', array(
            'Caption' => _('Send invoice'),
            'Title' => _('Send an invoice by email'),
    	    'URL' => 'InvoiceMail.php?invId=%d',
            'ReturnURL' => $_SERVER['PHP_SELF'],
            'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)));

    $grid->NewAction('Print');
	$grid->NewAction('Export', array('FileName' => 'ListeDocuments',
		'FirstArrival' => $form->IsFirstArrival()));

    $searchedDoc = SearchTools::requestOrSessionExist('ClassName');
    if($searchedDoc =='ForwardingForm') {
        $grid->NewAction('Delete', array(
            'Caption' => A_DELETE,
            'TransmitedArrayName'=>'id',
            'EntityType'=>'Document'));
    }
	$grid->NewColumn('FieldMapper', _('Document'), array('Macro' =>'%DocumentNo%'));
	$grid->NewColumn('FieldMapperWithTranslation', _('Order'),
	       array('Macro' =>'%Command.CommandNo%',
                 'TranslationMap' => array('0' => 'N/A')));
	$grid->NewColumn('FieldMapper', _('Date'),
            array('Macro' => '%EditionDate|formatdate%'));

	/*  Construction du filtre  */
	$FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray());
	$Filter = SearchTools::filterAssembler($FilterComponentArray);

	$Order = array('EditionDate' => SORT_DESC);

	$form->displayResult($grid, true, $Filter, $Order);
	Tools::redirectTo($_SERVER['PHP_SELF']);
    exit;
} // fin DisplayGrid

else { // on n'affiche que le formulaire de recherche, pas le Grid
	Template::page('', $form->render() . '</form>');
}
?>
