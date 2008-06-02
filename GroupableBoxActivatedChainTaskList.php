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
require_once('Objects/Task.const.php');
require_once('Objects/ActivatedChainTask.php');

$auth = Auth::singleton();
$auth->checkProfiles();

$profileID = $auth->getProfile();
$actorID   = $auth->getActorId();

define('PAGE_SUBTITLE', '<br />'._('Please select the tasks you want to regroup then click the "Regroup" button').'<br/><br/>');
define('ITEMS_PER_PAGE', 25);

//Database::connection()->debug = true;

$form = new SearchForm('ActivatedChainTask');
// Critère 0 : Plage de date
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
    array('', 'onclick="$(\\\'Date1\\\').style.' .
    'display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
	    array('Name'   => 'BeginDate',
	          'Path' => 'Begin'),
	    array('Name'   => 'EndDate',
	    	  'Path' => 'Begin'),
	    array('EndDate' => array(
		            'd'=>date('d'),
		            'm'=>date('m'),
		            'Y'=>date('Y')
		        ),
	          'BeginDate' => array('Y'=>date('Y'))
	    	)
	);
//Critère 1 : Numéro de commande
$form->addElement('text', 'Command', _('Order'), array(),
		array('Path'=>'ActivatedOperation.ActivatedChain.CommandItem().Command.CommandNo')
	);

// Critère 2 : Commune de départ de la commande
$form->addElement('text', 'CityName1', _('Departure city'),
    array(),
    array(
        'Path'=>'ActivatedOperation.ActivatedChain.CommandItem().Command.' .
            'ExpeditorSite.CountryCity.CityName.Name'
    )
);
// Critère 3 : Commune d’arrivée de la commande
$form->addElement('text', 'CityName2', _('Arrival city'),
    array(),
    array(
        'Path'=>'ActivatedOperation.ActivatedChain.CommandItem().Command.' .
            'DestinatorSite.CountryCity.CityName.Name'
    )
);

// Critère 4 : Zone de départ  de la commande 
$dzArray = SearchTools::createArrayIDFromCollection('Zone', array(), _('Select a zone'));
$form->addElement('select', 'Zone1', _('Departure zone'),
    array($dzArray),
    array('Path'=>'ActivatedOperation.FirstTask.ActorSiteTransition.DepartureSite.Zone'));
// Critère 5 : Zone d’arrivée de la commande
$azArray = SearchTools::createArrayIDFromCollection('Zone', array(), _('Select a zone'));
$form->addElement('select', 'Zone2', _('Arrival zone'),
    array($azArray),
    array('Path'=>'ActivatedOperation.LastTask.ActorSiteTransition.ArrivalSite.Zone'));

// Filtres par défaut
$filterArray = array();
// ActivatedChainTask créatrices de box
$filterArray[] = SearchTools::NewFilterComponent(
    'IsBoxCreator', 'Task.IsBoxCreator', 'Equals', 1, 1);
$filterArray[] = SearchTools::NewFilterComponent(
    'Task', '', 'NotEquals', TASK_STOCK_EXIT, 1);
$filterArray[] = SearchTools::NewFilterComponent('State', '', 'In',
    	array(ActivatedChainTask::STATE_TODO, ActivatedChainTask::STATE_IN_PROGRESS), 1);
//Database::connection()->debug = true;
if ($profileID == UserAccount::PROFILE_GESTIONNAIRE_STOCK ||
    $profileID == UserAccount::PROFILE_TRANSPORTEUR) {
	$filterArray[] = SearchTools::NewFilterComponent('Actor', 'ActivatedOperation.Actor',
        	'Equals', $actorID, 1);
}


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
	/*  Construction du filtre  */
    $filterArray = array_merge($filterArray, $form->BuildFilterComponentArray());
    //  Création du filtre complet
	$filter = SearchTools::filterAssembler($filterArray);

    // si recherche lancee sans critere de date, il faut tuer la var de session pour la case a cocher
	if (isset($_POST['formSubmitted']) && !isset($_POST['DateOrder'])) {
	    unset($_SESSION['DateOrder']);
	}

	$grid = new Grid();
	$grid->itemPerPage = ITEMS_PER_PAGE;
 	// colonnes
	$grid->NewColumn('FieldMapper', _('Order'),
        array('Macro' => '%ActivatedOperation.ActivatedChain.CommandItem()[0]' .
            '.Command.CommandNo%', 'Sortable' => false));
	$grid->NewColumn('FieldMapper', _('Task'), array('Macro' => '%Task.Name%'));
    $grid->NewColumn('FieldMapper', _('Departure city'),
        array('Macro'=>'%ActivatedOperation.FirstTask.DepartureSite.CountryCity.CityName.Name|default%',
            'Sortable'=>false));
    $grid->NewColumn('FieldMapper', _('Arrival city'),
        array('Macro'=>'%ActivatedOperation.LastTask.ArrivalSite.CountryCity.CityName.Name|default%'));
	$grid->NewColumn('FieldMapper', _('Beginning date'),
        array('Macro' => '%Begin|formatdate%'));
	$grid->NewColumn('FieldMapper', _('Number of parcels'),
        array('Macro' => '%GroupableBoxCollectionCount%', 'Sortable' => false));
	$grid->NewColumn('FieldMapperWithTranslation', _('State'), array('Macro' => '%State%',
					 'TranslationMap' => ActivatedChainTask::getStateConstArray()));
	// actions
	$grid->NewAction('Redirect',
        array(
            'Caption' => _('Regroup'),
            'TransmitedArrayName' => 'ackIDs',
            'URL' => 'GroupableBoxList.php'
        )
    );

	$order = array('Begin' => SORT_ASC);

	$form->displayResult($grid, true, $filter, $order, '', array(),
						 array('between' => PAGE_SUBTITLE));
}
else { // on n'affiche que le formulaire de recherche, pas le Grid
	Template::page('', $form->render() . '</form>');
}

?>