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
require_once('Objects/Command.php');
require_once('Objects/Command.const.php');
//Database::connection()->debug = true;

$auth = Auth::Singleton();
$auth->checkProfiles();

$ProfileId = $auth->getProfile();
$UserConnectedActorId = $auth->getActorId();

$printInvoice = '';
if ((isset($_REQUEST['InvoiceId']))) {
    $Id = (is_array($_REQUEST['InvoiceId']))?$_REQUEST['InvoiceId'][0]:$_REQUEST['InvoiceId'];
	$print = (isset($_REQUEST['print']))?$_REQUEST['print']:0;
	$printInvoice =  "
	<script type=\"text/javascript\">
	<!--
    // <![CDATA[
	function kill() {
		window.open(\"KillPopup.html\",'popback','width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no');
	}
	function TimeToKill(sec) {
		setTimeout(\"kill()\",sec*1000);
	}
	function printInvoice() {
	   var w=window.open(\"EditInvoice.php?InvoiceId=" . $Id ."&print=".$print."\",\"popback\",\"width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no\");
	   w.blur();
	}
	connect(window, 'onload', printInvoice);
    // ]]>
    -->
	</script>";
}

$filterArray = array(); // Tableau de filtres
// on affiche pas les devis
$filterArray[] = SearchTools::NewFilterComponent('IsEstimate', '', 'Equals', 0, 1);
// Restriction aux commandes dont l'acteur de l'utilisateur connecte est Command_Customer
if($ProfileId == UserAccount::PROFILE_CLIENT_TRANSPORT) {
    $filterArray[] = SearchTools::NewFilterComponent(
            'Customer', '', 'Equals', $UserConnectedActorId, 1);
}
elseif ($ProfileId == UserAccount::PROFILE_DIR_COMMERCIAL){
    // restriction aux commandes client
    $filterArray[] = SearchTools::NewFilterComponent(
            'Expeditor', '', 'Equals', $auth->getActorId(), 1);
}
$smarty = new Template();

/*  Contruction du formulaire de recherche */
$form = new SearchForm('ChainCommand');
$form->addElement('text', 'CommandNo', _('Order number'));
$form->addElement('text', 'Customer', _('Customer'), array(),
        array('Path' => 'Customer.Name'));
$form->addElement('text', 'Expeditor', _('Shipper'), array(),
        array('Path' => 'Expeditor.Name'));
$form->addElement('text', 'Destinator', _('Addressee'), array(),
        array('Path' => 'Destinator.Name'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
        array('', 'onClick="$(\\\'Date1\\\').style.'
            . 'display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name'   => 'StartDate', 'Path' => 'WishedStartDate'),
	    array('Name'   => 'EndDate', 'Path' => 'WishedStartDate'),
	    array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
   			  'StartDate' => array('Y' => date('Y')))
);
$CommandStateArray =
    array('##'=>_('Select one or more states')) +
        Command::getStateConstArray();
$form->addElement('select', 'State', _('State'), array($CommandStateArray,
        'multiple="multiple" size="5"'));


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
	// Si recherche lancee sans critere de date, il faut tuer la var de session
    // pour la case a cocher
	if (isset($_REQUEST['formSubmitted']) && !isset($_REQUEST['DateOrder1'])) {
	    unset($_SESSION['DateOrder1']);
	}
	/*  Construction du filtre  */
	// 0. Filtre par defaut
	$filterArray[] = SearchTools::NewFilterComponent('CommandDate', '',
            'GreaterThan', '2004-02-17 00:00:00', 1);
    $filterArray = array_merge($filterArray,
            $form->buildFilterComponentArray());
    // Création du filtre complet
	$Filter = SearchTools::filterAssembler($filterArray);

	define('COMMAND_EVENT_LIST_ITEMPERPAGE', 50);
	$grid = new Grid();
	$grid->itemPerPage = COMMAND_EVENT_LIST_ITEMPERPAGE;
	$grid->setNbSubGridColumns(3);  // mettre ici: [nb de colonnes du SubGrid]

	$grid->NewAction('Redirect', array(
        'Caption' => A_DELETE,
        'URL' => 'CommandDelete.php?CommandId=%d&returnURL=' . $_SERVER['PHP_SELF'],
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                            UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL)));

    $grid->NewAction('Redirect', array(
		'Caption' => _('Charge'),
		'URL' => 'InvoiceChainCommandList.php?CommandId=%d&returnURL=' .
		         $_SERVER['PHP_SELF'],
		'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_ADMIN_VENTES)));

	$grid->NewAction('Redirect', array(
		'Caption' => _('Show invoices'),
		'URL' => 'InvoiceCommandList.php?CommandId=%d&returnURL=' . $_SERVER['PHP_SELF']));

	$grid->NewAction('Redirect', array(
		'Caption' => _('Show payments'),
		'URL' => 'PaymentCommandList.php?cmdId=%d&returnURL=' . $_SERVER['PHP_SELF']));

    $grid->NewAction('Redirect', array(
        'Caption' => _('Receipt'),
        'Title' => _('Print order receipt'),
        'TargetPopup' => true,
        'URL' => 'CommandReceiptEdit.php?cmdId=%d',
        'ReturnURL' => 'javascript:window.close();'));

	$grid->NewAction('Redirect', array(
        'Caption' => _('Shipment'),
        'Title' => _('Shipment informations'),
        'URL' => 'CommandExpeditionDetailAddEdit.php?cmdID=%d&retURL=' .
                 $_SERVER['PHP_SELF'],
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                            UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL)));

	$grid->NewAction('Print');
	$grid->NewAction('Export', array('FileName' => 'Commandes'));


    $grid->NewColumn('FieldMapper', _('number '), array('Macro' => '%CommandNo%'));
    $grid->NewColumn('FieldMapper', _('Date'),
        array('Macro' => '%WishedStartDate|formatdate@DATE_SHORT%'));
    $grid->NewColumn('FieldMapper', _('Shipper'),
        array('Macro' => '%Expeditor.Name%'));
    $grid->NewColumn('FieldMapper', _('Addressee'),
        array('Macro' => '%Destinator.Name%'));
    $grid->NewColumn('FieldMapper', _('Addressee site'),
        array('Macro' => '%DestinatorSite.Name%'));
    $grid->NewColumn('FieldMapperWithTranslation', _('State'),
        array('Macro' => '%State%','TranslationMap' => $ShortCommandStateArray));
    $grid->NewColumn('FieldMapper', _('Customer'),
        array('Macro' => '%Customer.Name%'));
    $grid->NewColumn('MultiPrice', _('Amount incl. VAT'),
        array('Method' => 'getTotalPriceTTC', 'Sortable' => false));
    $grid->NewColumn('ChainCommandItemList',
        array(_('Parcel'), _('Type'), _('Weight'), _('Quantity')),
        array('Sortable' => false));

	$Order = array('WishedStartDate' => SORT_DESC);

	$form->displayResult($grid, true, $Filter, $Order, '', array(),
						 array('beforeForm' => $printInvoice));
} // fin DisplayGrid

else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $printInvoice . $form->render() . '</form>');
}
?>
