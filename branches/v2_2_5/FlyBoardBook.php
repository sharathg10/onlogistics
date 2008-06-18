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
require_once ('Objects/ActivatedChainTaskDetail.php');
require_once('Objects/ActivatedChainTaskDetail.const.php');

$auth = Auth::Singleton();
$auth->checkProfiles();
$ProfileId = $auth->getProfile();
$UserConnectedActorId = $auth->getActorId();

$FilterComponentArray = array();  // Tableau de filtres
// Filtre pour le select du form
$ConcretPdttSelectFilter = array('Product.ClassName'=>'AeroProduct'); 
//Database::connection()->debug = true;

if ($ProfileId == UserAccount::PROFILE_AERO_CUSTOMER) {
	$FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Customer', 'ActivatedOperation.RealConcreteProduct.Owner', 
			'Equals', $UserConnectedActorId, 1);
	$ConcretPdttSelectFilter = array('Owner' => $UserConnectedActorId);							
}

/*  Contruction du formulaire de recherche */ 
$form = new SearchForm('ActivatedChainTask');
$AeroConcreteProductArray = SearchTools::CreateArrayIDFromCollection('AeroConcreteProduct', 
        $ConcretPdttSelectFilter, _('Select an airplane'));
$form->addElement('select', 'AeroConcreteProduct', _('Airplane matriculation'), 
				  array($AeroConcreteProductArray), 
				  array('Path' => 'ActivatedOperation.RealConcreteProduct'));			

$form->addElement('checkbox', 'DateOrder1', _('Filter by date'), 
        array('', 'onClick="$(\\\'Date1\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(array('Name'   => 'BeginDate', 
								 'Path' => 'Begin'),
						   array('Name'   => 'EndDate', 
								 'Path' => 'Begin'),
						   array('EndDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')), 
					   			'BeginDate' => array('Y'=>date('Y')))
						  );
$form->addElement('checkbox', 'ConcreteProductView', _('Display airplane hours of flight'), 
        array('', 'onclick="$(\\\'ConcreteProductDetail\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'), 
		array('Disable' => true));

// ActivatedChainTask de type VOL, et finies
$FilterComponentArray[] = SearchTools::NewFilterComponent('Task', '', 'Equals', TASK_FLY, 1);
$FilterComponentArray[] = SearchTools::NewFilterComponent('State', '', 'Equals', ActivatedChainTask::STATE_FINISHED, 1);


/*  Affichage du Grid  */
if (true === $form->DisplayGrid()) {

	/*  Construction du filtre  */  
    $FilterComponentArray = array_merge($FilterComponentArray, 
                                        $form->BuildFilterComponentArray());
	$Filter = SearchTools::FilterAssembler($FilterComponentArray); // Création du filtre complet
	
	// Affichage des donnees sur le ConcreteProduct
	if (isset($_POST['ConcreteProductView']) && $_POST['AeroConcreteProduct'] !='##') {
	    $AeroConcreteProduct = Object::load('AeroConcreteProduct', 
														 $_POST['AeroConcreteProduct']);
		$ConcreteProductDetail = $AeroConcreteProduct->displayDetail();
		$begin = (isset($_POST['BeginDate']))?
                DateTimeTools::QuickFormDateToMySQL('BeginDate') . ' 00:00:00':
                '2005-01-01 00:00:00';
		$end = (isset($_POST['EndDate']))?
                DateTimeTools::QuickFormDateToMySQL('EndDate') . ' 23:59:59':
                date('Y-m-d H:i:00');
		$ConcreteProductDetail .= $AeroConcreteProduct->displayCarburantDetail($begin, $end);
	}
	else {
		$ConcreteProductDetail = '<div id="ConcreteProductDetail"></div><div id="carburantDetail"></div>';
	}
	
    // si recherche lancee sans critere de date, il faut tuer la var de 
    // session pour la case a cocher
	if (isset($_POST['formSubmitted']) && !isset($_POST['DateOrder1'])) {
	    unset($_SESSION['DateOrder1']);
	}
	
	$grid = new Grid();
	$grid->itemPerPage = 100;
	$grid->withNoCheckBox = true;

	$grid->NewAction('Print');
	$grid->NewAction('Export', array('FileName' => 'FlightNotebook'));
 	
	$grid->NewColumn('FieldMapper', _('Date'), 
            array('Macro' => '%ActivatedChainTaskDetail.TakeOff|formatdate@DATE_SHORT%'));
	$grid->NewColumn('FieldMapper', _('Matr.'), 
		    array('Macro' => '%ActivatedOperation.RealConcreteProduct.Immatriculation%'));
	$grid->NewColumn('PiloteCopiloteName', _('Pilot'), array('role' => _('Pilot')));
	$grid->NewColumn('PiloteCopiloteName', _('Co-pilot'), array('role' => _('Co-pilot')));
	$grid->NewColumn('FieldMapper', _('Lift-off'), 
            array('Macro' => '%ActivatedChainTaskDetail.TakeOff|formatdate@TIME_SHORT%'));
	$grid->NewColumn('FieldMapper', _('Landing'), 
            array('Macro' => '%ActivatedChainTaskDetail.Landing|formatdate@TIME_SHORT%'));
	$grid->NewColumn('FieldMapper', _('Duration'), 
            array('Macro' => '%ActivatedChainTaskDetail.TechnicalHour%'));
	$grid->NewColumn('FieldMapperWithTranslation', _('Nature'), 
			array('Macro' => '%ActivatedChainTaskDetail.Nature%', 
				  'TranslationMap' => ActivatedChainTaskDetail::getNatureConstArray()));
	$grid->NewColumn('FlightPreparationQty', _('Fuel'), 
            array('liquid' => 'CarburantAdded'));
	$grid->NewColumn('FlightPreparationQty', _('Oil'), array('liquid' => 'OilAdded'));
	$grid->NewColumn('FieldMapperWithTranslationExpression', _('Night'), 
        array(
            'Macro' => '%ActivatedChainTaskDetail.MoteurType%', 
            'TranslationMap'=> array(
                'Mono'=>'%ActivatedChainTaskDetail.PilotHoursNight%', 
                'Bi'=>'%ActivatedChainTaskDetail.PilotHoursBiEngineNight%'
            )
        )
    );
    $grid->NewColumn('FieldMapper', _('Public transport'), 
            array('Macro' => '%ActivatedChainTaskDetail.PublicHours%'));
	$grid->NewColumn('FieldMapper', _('VLAE'), 
            array('Macro' => '%ActivatedChainTaskDetail.VLAEHours%'));
	
	$grid->withNoSortableColumn = true;
	$Order = array('ActivatedChainTaskDetail.EngineOn' => SORT_ASC);
	
	$form->displayResult($grid, true, $Filter, $Order, '', array(),
						 array('between' => $ConcreteProductDetail));
}	
else { // on n'affiche que le formulaire de recherche, pas le Grid
	$emptyLayer = '<div id="ConcreteProductDetail"></div><div id="carburantDetail"></div>';
    Template::page('', $form->render() . $emptyLayer . '</form>');
}
?>
