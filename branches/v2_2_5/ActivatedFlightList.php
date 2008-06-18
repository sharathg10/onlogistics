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
require_once('Objects/Task.inc.php');
require_once('Objects/ActivatedChainTask.php');
require_once('Objects/ActivatedChainTaskDetail.const.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(), array('showErrorDialog'=>true, 'debug'=>false));
$ProfileId = $auth->getProfile();
$UserConnectedActorId = $auth->getActorId();

$FilterComponentArray = array();  // Tableau de filtres
$CustomerSelectFilter = array('Generic' => 0);  // On ne propose pas les Actor generiques
$InstructorSelectFilter = array('Generic' => 0);
$ConcreteProductArrayFilter = array('Product.ClassName'=>'AeroProduct');
//Database::connection()->debug = true;

if ($ProfileId == UserAccount::PROFILE_AERO_CUSTOMER) {
	$FilterComponentArray2 = array();  // Tableau de filtres
	$FilterComponentArray2[] = SearchTools::NewFilterComponent('Owner', 'ActivatedOperation.ConcreteProduct.Owner',
												  'Equals', $UserConnectedActorId, 1);
	$FilterComponentArray2[] = SearchTools::NewFilterComponent('Customer',
												  'ActivatedOperation.ActivatedChain.CommandItem().Command.Customer',
										    	  'Equals', $UserConnectedActorId, 1, 'ActivatedChainTask');
	$FilterComponentArray[] = SearchTools::FilterAssembler($FilterComponentArray2, 'Or'); //  Ceci est aussi un FilterComponent
	// Si un AeroCustomer connecte, que lui ds le select
	$CustomerSelectFilter = array_merge($CustomerSelectFilter, array('Id' => array($UserConnectedActorId)));
}
if ($ProfileId == UserAccount::PROFILE_AERO_INSTRUCTOR) {
	$FilterComponentArray2 = array();  // Tableau de filtres
	$FilterComponentArray2[] = SearchTools::NewFilterComponent('RealActor', 'ActivatedOperation.ActivatedChain.CommandItem().Command.Instructor',
												  'Equals', $UserConnectedActorId, 1, 'ActivatedChainTask');
	$FilterComponentArray2[] = SearchTools::NewFilterComponent('Instructor',
												  'ActivatedOperation.ActivatedChain.CommandItem().Command.Customer.Instructor',
										    	  'Equals', $UserConnectedActorId, 1, 'ActivatedChainTask');
	$FilterComponentArray[] = SearchTools::FilterAssembler($FilterComponentArray2, 'Or'); //  Ceci est aussi un FilterComponent
	// Si un AeroInstructor connecte, que lui ds le select
	$InstructorSelectFilter = array_merge($InstructorSelectFilter, array('Id' => array($UserConnectedActorId)));
}

/*  Contruction du formulaire de recherche */
$form = new SearchForm('ActivatedChainTask');
$AeroConcreteProductArray = SearchTools::CreateArrayIDFromCollection('AeroConcreteProduct',
														$ConcreteProductArrayFilter,
														_('Select an airplane'));
$form->addElement('select', 'AeroConcreteProduct', _('Airplane matriculation'),
				  array($AeroConcreteProductArray),
				  array('Path' => 'ActivatedOperation.ConcreteProduct'));

if($ProfileId == UserAccount::PROFILE_AERO_CUSTOMER) {
    $AeroCustomerArray = SearchTools::CreateArrayIDFromCollection('AeroCustomer', $CustomerSelectFilter);
    $form->addElement('hidden', 'Customer', $UserConnectedActorId, array(), array('Disable'=>true));
    $form->addElement('select', 'CustomerSelect', _('Customer'), array($AeroCustomerArray, 'disabled'),
				  array('Path' => 'ActivatedOperation.ActivatedChain.CommandItem().Command.Customer',
				        'Disable' => true));

} else {
    $AeroCustomerArray = SearchTools::CreateArrayIDFromCollection('AeroCustomer', $CustomerSelectFilter, _('Select a customer'));
    $form->addElement('select', 'Customer', _('Customer'), array($AeroCustomerArray),
				  array('Path' => 'ActivatedOperation.ActivatedChain.CommandItem().Command.Customer'));

}



$AeroInstructorArray = SearchTools::CreateArrayIDFromCollection('AeroInstructor', $InstructorSelectFilter, _('Select an instructor'));
$form->addElement('select', 'Instructor', _('Instructor'), array($AeroInstructorArray),
				  array('Path' => 'ActivatedOperation.ActivatedChain.CommandItem().Command.Instructor'));

$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
				  array('',
				  		'onClick="$(\\\'Date1\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(array('Name'   => 'BeginDate',
								 'Path' => 'Begin'),
						   array('Name'   => 'EndDate',
								 'Path' => 'Begin'),
						   array('EndDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')),
					   			 'BeginDate' => array('Y'=>date('Y')))
						  );
$form->addElement('checkbox', 'ConcreteProductView', _('Display airplane details'),
			array('', 'onclick="$(\\\'ConcreteProductDetail\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'),
			array('Disable' => true));
$form->addElement('checkbox', 'CustomerView', _('Display customer details'),
				  array('', 'onclick="$(\\\'customerDetail\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'),
				  array('Disable' => true));
$form->addElement('checkbox', 'InstructorView', _('Display instructor details'),
			array('', 'onclick="$(\\\'instructorDetail\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'),
			array('Disable' => true));



// ActivatedChainTask de type VOL, et a faire
$FilterComponentArray[] = SearchTools::NewFilterComponent('Task', '', 'Equals', TASK_FLY, 1);
$FilterComponentArray[] = SearchTools::NewFilterComponent('State', '', 'Equals', ActivatedChainTask::STATE_TODO, 1);


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
    // Evite les interaction entre $_POST et $_SESSION
	SearchTools::cleanCheckBoxDataSession('DateOrder1');

	/*  Construction du filtre  */
    $FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray());
	$Filter = SearchTools::filterAssembler($FilterComponentArray);

	// Affichage des donnees sur le ConcreteProduct
	if (isset($_POST['ConcreteProductView']) && $_POST['AeroConcreteProduct'] !='##') {
        $AeroConcreteProduct = Object::load('AeroConcreteProduct',
	           $_POST['AeroConcreteProduct']);
		$ConcreteProductDetail = $AeroConcreteProduct->displayDetail();
	}
	else {
		$ConcreteProductDetail = '<div id="ConcreteProductDetail"></div>';
	}
	$actorsDetail = '';
	// Affichage des donnees sur le Customer
	if (isset($_POST['CustomerView']) && $_POST['Customer'] !='##') {
	    $AeroCustomer = Object::load('AeroCustomer', $_POST['Customer']);
		$actorsDetail = $AeroCustomer->displayDetail();
	}
	else {
		$actorsDetail = '<div id="customerDetail"></div>';
	}
	// Affichage des donnees sur le Customer
	if (isset($_POST['InstructorView']) && $_POST['Instructor'] !='##') {
	    $AeroInstructor = Object::load('AeroInstructor', $_POST['Instructor']);
		$actorsDetail .= $AeroInstructor->displayDetail();
	}
	else {
		$actorsDetail .= '<div id="instructorDetail"></div>';
	}

	$grid = new Grid();
	$grid->itemPerPage = 100;
	$grid->withNoCheckBox = true;

	$grid->NewAction('Print', array());
	$grid->NewAction('Export', array('FileName' => 'FlightNotebook'));

	$grid->NewColumn('FieldMapper', _('Date'),
				array('Macro' => '%Begin|formatdate%'));
	$grid->NewColumn('FieldMapper', _('Airplane type'),
					 array('Macro' => '%ActivatedOperation.ConcreteProduct.Product.FlyType.Name%'));
	$grid->NewColumn('FieldMapper', _('Matriculation'),
					 array('Macro' => '%ActivatedOperation.ConcreteProduct.Immatriculation%'));
	$grid->NewColumn('FieldMapper', _('Duration'),
				array('Macro' => '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Duration|formatduration%',
					  'Sortable' => false));
	$grid->NewColumn('FieldMapper', _('Customer'),
				array('Macro' => '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Customer.Name%',
					  'Sortable' => false));
	$grid->NewColumn('FieldMapperWithTranslation', _('Instructor'),
				array('Macro' => '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Instructor.Name%',
                      'TranslationMap' => array(0=>''),
					  'Sortable' => false));
	$grid->NewColumn('FieldMapper', _('Order'),
				array('Macro' => '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.CommandNo%',
					  'Sortable' => false));

//	$grid->withNoSortableColumn = true;
	$Order = array('Begin' => SORT_ASC);

	$additionalContent = array('between' => $ConcreteProductDetail . $actorsDetail);
	$form->displayResult($grid, true, $Filter, $Order, '', array(), $additionalContent);
} // fin FormSubmitted

else { // on n'affiche que le formulaire de recherche, pas le Grid
	$emptyLayer = '<div id="ConcreteProductDetail"></div>
				   <div id="customerDetail"></div><div id="instructorDetail"></div>';
    Template::page('', $form->render() . $emptyLayer . '</form>');
}
?>
