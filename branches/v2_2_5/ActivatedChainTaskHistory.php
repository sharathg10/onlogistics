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
$auth = Auth::Singleton();
$auth->checkProfiles();
$ProfileId = $auth->getProfile();
$UserConnectedActorId = $auth->getActorId();

// Gestion du context metier
$tradeContext = Preferences::get('TradeContext', array());
$consultingContext = in_array('consulting', $tradeContext);
$FilterComponentArray = array();  // Tableau de filtres

//Database::connection()->debug = true;

if ($ProfileId == UserAccount::PROFILE_AERO_OPERATOR) {
	$FilterComponentArray[] = SearchTools::NewFilterComponent(
            'RealActor', 'ActivatedOperation.RealActor',
			'Equals', $UserConnectedActorId, 1);
}
if ($ProfileId == UserAccount::PROFILE_AERO_CUSTOMER) {
    $FilterComponentArray2 = array();  // Tableau de filtres
	$FilterComponentArray2[] = SearchTools::NewFilterComponent(
            'RealActor', 'ActivatedOperation.RealActor',
			'Equals', $UserConnectedActorId, 1);
	$FilterComponentArray2[] = SearchTools::NewFilterComponent('Customer',
            'ActivatedOperation.ActivatedChain.CommandItem().Command.Customer',
            'Equals', $UserConnectedActorId, 1, 'ActivatedChainTask');
    //  Ceci est aussi un FilterComponent
	$Filter2 = SearchTools::filterAssembler($FilterComponentArray2, 'Or');
	$FilterComponentArray[] = $Filter2;
}

if ($ProfileId == UserAccount::PROFILE_AERO_INSTRUCTOR) {
	$filter = new FilterComponent();
	$filter->setItem(new FilterRule('ActivatedOperation.RealActor',
            FilterRule::OPERATOR_EQUALS,
			$UserConnectedActorId));
	$filter->operator = FilterComponent::OPERATOR_OR;
	$filter->setItem(new FilterRule(
            'ActivatedOperation.RealActor.Instructor',
			FilterRule::OPERATOR_EQUALS,
			$UserConnectedActorId));
	$FilterComponentArray[] = $filter;
}

if ($consultingContext) {
    $filterComponentArray[] = SearchTools::NewFilterComponent('OperationType',
        'ActivatedOperation.Operation.Type', 'Equals', Operation::OPERATION_TYPE_CONS, 1);
}

/*  Contruction du formulaire de recherche */
$form = new SearchForm('ActivatedChainTask');
$form->addElement('text', 'Command', _('Order'), array(),
        array('Path' => 'ActivatedOperation.ActivatedChain.CommandItem().Command.CommandNo'));
$tskFilter = ($consultingContext)?array('Id' => getConsultingTaskIds()):array();
$tskFilter['ToBeValidated'] = 1;
$TaskArray = SearchTools::createArrayIDFromCollection(
        'Task', $tskFilter, _('Select a task'));
$form->addElement('select', 'Task', _('Task'), array($TaskArray));
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
        array('',
	       'onClick="$(\\\'Date1\\\').style.display'
            . '=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name' => 'BeginDate', 'Path' => 'Begin'),
		array('Name'   => 'EndDate', 'Path' => 'Begin'),
        array('EndDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')),
        	  'BeginDate' => array('Y'=>date('Y')))
        );
// ActivatedChainTask finies, correspondant a une Task validable
$FilterComponentArray[] = SearchTools::NewFilterComponent(
        'State', '', 'Equals', ActivatedChainTask::STATE_FINISHED, 1);
$FilterComponentArray[] = SearchTools::NewFilterComponent(
        'ToBeValidated', 'Task.ToBeValidated', 'Equals', 1, 1);


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
    // Evite les interaction entre $_POST et $_SESSION
	SearchTools::cleanCheckBoxDataSession('DateOrder1');

	/*  Construction du filtre  */
    $FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray());
	$Filter = SearchTools::filterAssembler($FilterComponentArray);

	$grid = new Grid();
	$grid->itemPerPage = 300;
	$grid->NewAction('Print', array());
	$grid->NewAction('Export', array('FileName' => 'Taches'));
    $grid->NewColumn('ActivatedChainTaskHistory', _('Task'));
	$grid->NewColumn('FieldMapper', _('Order'),
			 array('Macro' => '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.CommandNo%',
			 	   'Sortable' => false));
	$grid->NewColumn('FieldMapper', _('Beginning date'),
            array('Macro' => '%Begin|formatdate%'));
	$grid->NewColumn('FieldMapper', _('User'),
            array('Macro' => '%ValidationUser.Identity%'));

	$Order = array('Begin' => SORT_DESC, 'Task.Name' => SORT_ASC);

	$form->displayResult($grid, true, $Filter, $Order);
} // fin FormSubmitted

else { // on n'affiche que le formulaire de recherche, pas le Grid
	Template::page('', $form->render() . '</form>');
}
?>
