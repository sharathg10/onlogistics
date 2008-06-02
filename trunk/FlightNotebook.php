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
require_once ('Objects/ActivatedChainTaskDetail.php');
require_once('Objects/ActivatedChainTask.php');
require_once('Objects/ActivatedChainTaskDetail.const.php');

$auth = Auth::Singleton();
$auth->checkProfiles();
$ProfileId = $auth->getProfile();

$UserConnectedActorId = $auth->getActorId();
$FilterComponentArray = array();  // Tableau de filtres
$CustomerSelectFilter = array('Generic' => 0);  // On ne propose pas les Actor generiques
$InstructorSelectFilter = array('Generic' => 0);

if ($ProfileId == UserAccount::PROFILE_AERO_CUSTOMER) {
	$FilterComponentArray[] = SearchTools::NewFilterComponent('Customer',
            'ActivatedOperation.ActivatedChain.CommandItem().Command.Customer',
            'Equals', $UserConnectedActorId, 1, 'ActivatedChainTask');
	// Si un AeroCustomer connecte, que lui ds le select
	$CustomerSelectFilter = array_merge($CustomerSelectFilter,
            array('Id' => array($UserConnectedActorId)));
    $AeroCustomer = $auth->getActor();
}

if ($ProfileId == UserAccount::PROFILE_AERO_INSTRUCTOR) {
	$FilterComponentArray2 = array();  // Tableau de filtres
	$FilterComponentArray2[] = SearchTools::NewFilterComponent('RealActor',
        'ActivatedOperation.RealActor', 'Equals', $UserConnectedActorId, 1);
	$FilterComponentArray2[] = SearchTools::NewFilterComponent('Instructor',
        'ActivatedOperation.ActivatedChain.CommandItem().Command.Customer.Instructor',
        'Equals', $UserConnectedActorId, 1, 'ActivatedChainTask');
    //  Ceci est aussi un FilterComponent
	$FilterComponentArray[] = SearchTools::FilterAssembler($FilterComponentArray2, 'Or');
	// Si un AeroInstructor connecte, que lui ds le select
	$InstructorSelectFilter = array_merge($InstructorSelectFilter,
            array('Id' => array($UserConnectedActorId)));
    $AeroInstructor = $auth->getActor();
}

/*  Contruction du formulaire de recherche */
$form = new SearchForm('ActivatedChainTask');

if ($ProfileId != UserAccount::PROFILE_AERO_CUSTOMER) {
    $AeroCustomerArray = SearchTools::CreateArrayIDFromCollection('AeroCustomer',
        $CustomerSelectFilter, _('Select a customer'));
    $form->addElement('select', 'Customer', _('Customer'),
        array($AeroCustomerArray),
        array('Path' => 'ActivatedOperation.ActivatedChain.CommandItem().Command.Customer'));

    $AeroInstructorArray = SearchTools::CreateArrayIDFromCollection('AeroInstructor',
        $InstructorSelectFilter, _('Select an instructor'));
    $form->addElement('select', 'Instructor', _('Instructor'),
        array($AeroInstructorArray),
        array('Path' => 'ActivatedOperation.RealActor'));
}

$form->addElement('checkbox', 'CustomerView', _('Display customer hours of flight'),
				  array('', 'onclick="$(\\\'customerDetail\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';
				  		$(\\\'customerStat\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'),
				  array('Disable' => true));
$form->addElement('checkbox', 'InstructorView', _('Display instructor hours of flight'),
			array('', 'onclick="$(\\\'instructorDetail\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';
				   $(\\\'instructorStat\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'),
			array('Disable' => true));

$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
				  array('',
				  		'onClick="$(\\\'Date1\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name' => 'BeginDate', 'Path' => 'Begin'),
	    array('Name' => 'EndDate', 'Path' => 'Begin'),
	    array('EndDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')),
   			  'BeginDate' => array('Y'=>date('Y')))
);
// ActivatedChainTask de type VOL, et finies
$FilterComponentArray[] = SearchTools::NewFilterComponent('Task', '', 'Equals', TASK_FLY, 1);
$FilterComponentArray[] = SearchTools::NewFilterComponent('State', '', 'Equals', ActivatedChainTask::STATE_FINISHED, 1);


/*  Affichage du Grid  */
if (true === $form->DisplayGrid()) {
	/*  Construction du filtre  */
    $FilterComponentArray = array_merge($FilterComponentArray,
                                        $form->buildFilterComponentArray());
	$Filter = SearchTools::FilterAssembler($FilterComponentArray); //  Création du filtre complet

	$actorsDetail = '';
    $AeroInstructor = $AeroCustomer = false;
    if (isset($_POST['Instructor']) && $_POST['Instructor'] !='##') {
	    $AeroInstructor = Object::load('AeroInstructor', $_POST['Instructor']);
    }
    if (isset($_POST['Customer']) && $_POST['Customer'] !='##') {
	    $AeroCustomer = Object::load('AeroCustomer', $_POST['Customer']);
    }
	// Affichage des donnees sur le Customer
	if (isset($_POST['CustomerView']) && $AeroCustomer instanceof AeroCustomer) {
		$actorsDetail = $AeroCustomer->displayDetail();
		$actorsDetail .= $AeroCustomer->displayStat($Filter);
	} else {
		$actorsDetail = '<div id="customerDetail"></div><div id="customerStat"></div>';
	}
	// Affichage des donnees sur le Customer
	if (isset($_POST['InstructorView']) && $AeroInstructor instanceof AeroInstructor) {
		$actorsDetail .= $AeroInstructor->displayDetail();
		$actorsDetail .= $AeroInstructor->displayStat($Filter);
	} else {
		$actorsDetail .= '<div id="instructorDetail"></div><div id="instructorStat"></div>';
	}

    // si recherche lancee sans critere de date, il faut tuer la var de session pour la case a cocher
	if (isset($_POST['formSubmitted']) && !isset($_POST['DateOrder1'])) {
	    unset($_SESSION['DateOrder1']);
	}

	$grid = new Grid();
	$grid->itemPerPage = 100;
	$grid->withNoCheckBox = true;
    $grid->withNoSortableColumn = true;

	$grid->NewAction('Print');
	$grid->NewAction('Export', array('FileName' => 'FlightNotebook'));

	$grid->NewColumn('FieldMapper', _('Date'),
        array('Macro' => '%ActivatedChainTaskDetail.TakeOff|formatdate@DATE_SHORT%'));
	$grid->NewColumn('FieldMapper', _('Airplane type'),
        array('Macro' => '%ActivatedOperation.RealConcreteProduct.Product.FlyType.Name%'));
	$grid->NewColumn('FieldMapper', _('Matr.'),
        array('Macro' => '%ActivatedOperation.RealConcreteProduct.Immatriculation%'));
    // Affiche ssi $AeroCustomer
    $grid->NewColumn('FieldMapperWithTranslation', _('On board role'),
            array(
                'Macro' => '%ActivatedChainTaskDetail.CustomerSeat%',
                'TranslationMap'=>getCustomerSeatTypeArray(),
                'Enabled' => ($AeroCustomer)
            )
    );
    // Affiche ssi $AeroInstructor
    $grid->NewColumn('FieldMapperWithTranslation', _('On board role'),
            array(
                'Macro' => '%ActivatedChainTaskDetail.InstructorSeat%',
                'TranslationMap'=>getInstructorSeatTypeArray(),
                'Enabled' => ($AeroInstructor)
            )
    );
	$grid->NewColumn('FieldMapperWithTranslation', _('Flight purpose'),
        array(
            'Macro' => '%ActivatedChainTaskDetail.Nature%',
			'TranslationMap' => ActivatedChainTaskDetail::getNatureConstArray()));
	$grid->NewColumn('FieldMapper', _('Engine'),
        	array('Macro' => '%ActivatedChainTaskDetail.MoteurType%'));

    // Heures de jour
	$col1 = $grid->NewColumn('FieldMapper', _('Double'),
        	array('Macro' => '%ActivatedChainTaskDetail.CoPilotHours%'));
	$col2 = $grid->NewColumn('FieldMapperWithTranslationExpression',
	        _('Commandant'),
	        array(
	            'Macro' => '%ActivatedChainTaskDetail.MoteurType%',
	            'TranslationMap'=> array(
	                'Mono'=>'%ActivatedChainTaskDetail.PilotHours%',
	                'Bi'=>'%ActivatedChainTaskDetail.PilotHoursBiEngine%'
	            )
	        )
    	);
	$col3 = $grid->NewColumn('FieldMapper', _('Co-pilot'),
        	array('Macro' => '%ActivatedChainTaskDetail.CoPilotHoursBiEngine%'));
    $grid->NewColumnGroup(_('Daily hours'), array($col1, $col2, $col3));

    // Heures de nuit
	$col1 = $grid->NewColumn('FieldMapper', _('Double'),
        array('Macro' => '%ActivatedChainTaskDetail.CoPilotHoursNight%'));
	$col2 = $grid->NewColumn('FieldMapperWithTranslationExpression',
        _('Commandant'),
        array(
            'Macro' => '%ActivatedChainTaskDetail.MoteurType%',
            'TranslationMap'=> array(
                'Mono'=>'%ActivatedChainTaskDetail.PilotHoursNight%',
                'Bi'=>'%ActivatedChainTaskDetail.PilotHoursBiEngineNight%'
            )
        )
    );
	$col3 = $grid->NewColumn('FieldMapper', _('Co-pilot'),
        array('Macro' => '%ActivatedChainTaskDetail.CoPilotHoursBiEngineNight%'));
    $grid->NewColumnGroup(_('Nightly hours'), array($col1, $col2, $col3));
    // Heures IFR
	$col1 = $grid->NewColumn('FieldMapper', _('Commandant'),
        array('Macro' => '%ActivatedChainTaskDetail.PilotHoursIFR%'));
	$col2 = $grid->NewColumn('FieldMapper', _('Co-pilot'),
        array('Macro' => '%ActivatedChainTaskDetail.CoPilotHoursIFR%'));
    $grid->NewColumnGroup('Heures IFR', array($col1, $col2));
    // aterrissage IFR
	$grid->NewColumn('FieldMapper', _('IFR landings'),
        array('Macro' => '%ActivatedChainTaskDetail.IFRLanding%'));

	$Order = array('ActivatedChainTaskDetail.TakeOff' => SORT_ASC);

	$form->displayResult($grid, true, $Filter, $Order, '', array(),
						 array('between' => $actorsDetail));
} // fin FormSubmitted

else { // on n'affiche que le formulaire de recherche, pas le Grid
	$emptyLayer = '<div id="customerDetail"></div><div id="instructorDetail"></div>
				   <div id="customerStat"></div><div id="instructorStat"></div>';
    Template::page('', $form->render() . $emptyLayer . '</form>');
}
?>
