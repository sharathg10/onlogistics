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
require_once('Objects/Command.const.php');

define('ITEMS_PER_PAGE', 100);
//Database::connection()->debug = true;

$auth = Auth::Singleton();
$auth->checkProfiles();

SearchTools::cleanDataSession('ccp');

/*  Gestion des droits  */
$filterArray = array(); // Tableau de filtres
if ($auth->getProfile() == UserAccount::PROFILE_AERO_CUSTOMER) {
	// Restriction aux commandes dont il est customer
	$filterArray[] = SearchTools::NewFilterComponent('Customer', '', 'Equals', $auth->getActorId(), 1);
}
elseif ($auth->getProfile() == UserAccount::PROFILE_DIR_COMMERCIAL){
    // Restriction aux commandes client
    $filterArray[] = SearchTools::NewFilterComponent('Expeditor', '', 'Equals', $auth->getActorId(), 1);
}


/*  Contruction du formulaire de recherche */
$form = new SearchForm('CourseCommand');
$form->addElement('text', 'CommandNo', _('Order number'), array(),
				  array('Path' => 'CommandNo'));
$form->addElement('text', 'AeroCustomer', _('Customer'), array(),
				  array('Path' => 'Customer.Name'));
$form->addElement('text', 'AeroInstructor', _('Instructor name'), array(),
				  array('Path' => 'Instructor.Name'));
$form->addElement('text', 'AeroConcreteProduct', _('Matriculation'), array(),
				  array('Path' => 'AeroConcreteProduct.Immatriculation'));
$FlyTypeArray = SearchTools::CreateArrayIDFromCollection('FlyType', array(),
											_('Select one or more types'));
$form->addElement('select', 'FlyType', _('Airplane type'),
				  array($FlyTypeArray, 'multiple size="5"'));
$form->addElement('select', 'State', _('State'),
				  array($CourseStateArray, 'multiple size="5"'), array());
$form->addBlankElement();
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
				  array('', 'onClick="$(\\\'Date1\\\').' .
				  'style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
		array('Name'   => 'StartDate',
			  'Path' => 'WishedStartDate'),
		array('Name'   => 'EndDate',
			  'Path' => 'WishedEndDate'),
		array(
			'EndDate' => array('d' => date('d'), 'm' => date('m'),
							   'Y' => date('Y')),
			'StartDate' => array('Y' => date('Y'))
		)
	);


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
	/*  Construction du filtre  */
	$filterArray = array_merge($filterArray, $form->buildFilterComponentArray());
	$filter = SearchTools::FilterAssembler($filterArray); // Création du filtre complet


	$grid = new Grid();
	$grid->itemPerPage = ITEMS_PER_PAGE;
	// actions
	$grid->NewAction('Redirect',
        array(
            'Caption' => A_DELETE,
            'URL' => 'CommandDelete.php?CommandId=%d&returnURL=' .
                $_SERVER['PHP_SELF'],
            'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_ADMIN_VENTES,
                    UserAccount::PROFILE_DIR_COMMERCIAL)
        )
	);
	$grid->NewAction('Redirect',
        array(
            'Caption' => _('Unlock'),
            'URL' => 'CommandUnlock.php?CommandId=%d',
            'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_ADMIN_VENTES,
                    UserAccount::PROFILE_DIR_COMMERCIAL)
        )
	);
	$grid->NewAction('Redirect',
        array(
            'Caption' => _('Invoice details'),
            'URL' => 'CourseInvoiceDetail.php?cmdId=%d'
        )
    );
	$grid->NewAction('Redirect',
        array(
            'Caption' => _('Charge'),
            'URL' => 'InvoiceAddEdit.php?CommandId=%d&returnURL=' .
                $_SERVER['PHP_SELF'],
            'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES)
        )
    );

	$grid->NewAction('Redirect',
        array(
            'Caption' => _('Show invoices'),
            'URL' => 'InvoiceCommandList.php?CommandId=%d&returnURL=' .
                $_SERVER['PHP_SELF'],
            'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES,
                UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_DIR_COMMERCIAL)
        )
    );
	// colonnesCommand.WishedStartDate
	$grid->NewColumn('FieldMapper', _('Order number'), array('Macro'=>'%CommandNo%'));
	$grid->NewColumn('FieldMapper', _('Wished date'),
        		array('Macro' => '%WishedStartDate|formatdate%'));
	$grid->NewColumn('FieldMapper', _('Customer'),
        		array('Macro'=>'%Customer.Name%', 'Sortable'=>false));
	$grid->NewColumn('FieldMapperWithTranslation', _('Instructor'),
        		array('Macro' => '%Instructor.Name%', 'Sortable'=>false,
                      'TranslationMap'=>array(0=>'')));
	$grid->NewColumn('FieldMapper', _('Airplane type'),
        		array('Macro' => '%FlyType.Name%', 'Sortable'=>false));
	$grid->NewColumn('FieldMapper', _('Matr.'),
				array('Macro' => '%AeroConcreteProduct.Immatriculation%','Sortable'=>false));
	$grid->NewColumn('FieldMapper', _('Flight duration').' *',
				array('Macro'=>'%Duration|hundredthofhour%', 'Sortable'=>false));
	$grid->NewColumn('CourseCommandListRealDuration', _('Actual duration').' *',
        		array('Sortable'=>false));
	$grid->NewColumn('CourseCommandListInvoiceTotalTTC', _('Amount incl. VAT'),
        		array('Sortable'=>false));
	$grid->NewColumn('FieldMapperWithTranslation', _('State'),
        		array('Macro' => '%State%', 'TranslationMap'=>$ShortCourseStateArray,
            		  'Sortable'=>false));

	$form->displayResult($grid, true, $filter, array('WishedStartDate' => SORT_DESC), '', array(),
						 array('beforeForm' => '* ' . _('Hundredth of hours') . '<br /><br />'));
}
else {
	//  on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}
?>
