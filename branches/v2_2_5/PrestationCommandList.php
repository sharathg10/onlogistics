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
require_once('Objects/PrestationCommand.php');
require_once('Objects/Command.const.php');
$auth = Auth::Singleton();
//Database::connection()->debug = true;
$auth->checkProfiles();

/*  Gestion des droits  */
$ProfileId = $auth->getProfile();
$FilterComponentArray = array(); // Tableau de filtres
if ($ProfileId == UserAccount::PROFILE_DIR_COMMERCIAL){
    // restriction aux commandes client
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Expeditor', '', 'Equals', $auth->getActorId(), 1);
}

/*  Contruction du formulaire de recherche */
$form = new SearchForm('PrestationCommand');

$form->addElement('text', 'CommandNo', _('Order number'));
$form->addElement('text', 'Destinator', _('Customer'), array(),
        array('Path' => 'Destinator.Name'));
$form->addElement('text', 'Expeditor', _('Supplier'), array(),
        array('Path' => 'Expeditor.Name'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
	    array('', 'onClick="$(\\\'Date1\\\').style.display'
            . '=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name' => 'StartDate', 'Path' => 'WishedStartDate'),
	    array('Name' => 'EndDate', 'Path' => 'WishedStartDate'),
	    array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
   			  'StartDate' => array('Y' => date('Y')))
);
$stateArray = array('##' => _('Select one or more states'))
        + PrestationCommand::getStateConstArray();
$form->addElement('select', 'State', _('State'), array($stateArray, 'multiple size="5"'));

$form->addAction(array('URL' => 'InvoicePrestationList.php',
					   'Caption' => _('Issue a service invoice'),
                       'Profile' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)));

/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
	// si recherche lancee sans critere de date, il faut tuer la var de session
    // pour la case a cocher
	if (isset($_REQUEST['formSubmitted']) && !isset($_REQUEST['DateOrder1'])) {
	    unset($_SESSION['DateOrder1']);
	}
	/*  Construction du filtre  */
	$FilterComponentArray = array_merge($FilterComponentArray,
                                        $form->buildFilterComponentArray());
    $Filter = SearchTools::FilterAssembler($FilterComponentArray); // Création du filtre complet

	$grid = new Grid();
	$grid->customizationEnabled = true;
    $grid->itemPerPage = 50;
    $grid->setNbSubGridColumns(1);  // mettre ici: [nb de colonnes du SubGrid]

    $grid->NewAction('Redirect', array(
        'Caption'=> A_ADD,
        'Title' => _('Issue a service invoice'),
        'URL' => 'InvoicePrestationList.php',
        'Profile' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)));

	$grid->NewAction('Redirect', array(
        'Caption' => A_DELETE,
        'Title' => _('Delete a service invoice'),
        'URL' => 'CommandDelete.php?CommandId=%d&returnURL=' . basename($_SERVER['PHP_SELF'])));

	$grid->NewAction('Redirect', array(
        'Caption' => _('Show invoices'),
        'Title' => _('Display list of invoices'),
        'URL' => 'InvoiceCommandList.php?CommandId=%d&returnURL=' . basename($_SERVER['PHP_SELF'])));

	$grid->NewAction('Redirect', array(
        'Caption' => _('List of payments'),
        'Title' => _('Display list of payments'),
        'URL' => 'PaymentCommandList.php?cmdId=%d&returnURL=' . basename($_SERVER['PHP_SELF'])));

	$grid->NewAction('Print');
	$grid->NewAction('Export', array('FileName' => 'Commandes'));

	$grid->NewColumn('FieldMapper', _('Order'), array('Macro' => '%CommandNo%'));
	$grid->NewColumn('FieldMapper', _('Date'),
            array('Macro' => '%WishedStartDate|formatdate@DATE_SHORT%'));
	$grid->NewColumn('FieldMapperWithTranslation', _('State'),
            array('Macro' => '%State%','TranslationMap' => $ShortCommandStateArray));
	$grid->NewColumn('MultiPrice', _('Amount incl. VAT'),
            array('Method' => 'getTotalPriceTTC', 'Sortable' => false));
	$grid->NewColumn('FieldMapper', _('Supplier'), array('Macro' => '%Expeditor%'));
	$grid->NewColumn('FieldMapper', _('Customer'), array('Macro' => '%Destinator%'));
	$grid->NewColumn('FieldMapper', _('Salesman'),
            array('Macro' => '%Commercial.Identity|default%'));
	$grid->NewColumn('PrestationCommandItemList', array(_('Service')),
            array('Sortable' => false));

	$Order = array('WishedStartDate' => SORT_DESC);

	$form->displayResult($grid, true, $Filter, $Order);
} // fin DisplayGrid

else { // on n'affiche que le formulaire de recherche, pas le Grid
	Template::page('', $form->render() . '</form>');
}
?>
