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

//Database::connection()->debug = true;

$auth = Auth::Singleton();
$auth->checkProfiles();
$profileID = $auth->getProfile();
$actorID = $auth->getActorId();

// Gestion du context metier
$tradeContext = Preferences::get('TradeContext', array());
$aeroContext = in_array('aero', $tradeContext);
$consultingContext = in_array('consulting', $tradeContext);

$filterComponentArray = array();  // Tableau de filtres
// filtre par défaut, on affiche pas les taches qui sont liées à des devis
$filterComponentArray[] = SearchTools::newFilterComponent(
    'IsEstimate',
    'ActivatedOperation.ActivatedChain.CommandItem().Command.IsEstimate',
    'Equals',
    0,
    true,
    'ActivatedChainTask'
);


if ($profileID == UserAccount::PROFILE_AERO_OPERATOR) {
	$filterComponentArray[] = SearchTools::NewFilterComponent(
            'Actor', 'ActivatedOperation.Actor', 'Equals', $actorID, 1);
}
// remarque: 6eme param obligatoire ici, car relation 1..*}
if ($profileID == UserAccount::PROFILE_AERO_CUSTOMER) {
	$filterComponentArray[] = SearchTools::NewFilterComponent('Customer',
        'ActivatedOperation.ActivatedChain.CommandItem().Command.Customer',
        'Equals', $actorID, 1, 'ActivatedChainTask');
}
if ($profileID == UserAccount::PROFILE_AERO_INSTRUCTOR) {
    $filter = new FilterComponent();
	$filter->setItem(new FilterRule('ActivatedOperation.RealActor',
            FilterRule::OPERATOR_EQUALS, $actorID));
	$filter->operator = FilterComponent::OPERATOR_OR;
	$filterItem = SearchTools::NewFilterComponent('Customer',
            'ActivatedOperation.ActivatedChain.CommandItem().Command.Instructor',
			'Equals', $actorID, 1, 'ActivatedChainTask');
	$filter->setItem($filterItem);
	$filterComponentArray[] = $filter;
}
if ($consultingContext) {
    $filterComponentArray[] = SearchTools::NewFilterComponent('OperationType',
        'ActivatedOperation.Operation.Type', 'Equals', Operation::OPERATION_TYPE_CONS, 1);
}

/*  Contruction du formulaire de recherche */
$form = new SearchForm('ActivatedChainTask');
$form->addElement('text', 'Command', _('Order'), array(),
        array('Path' => 'ActivatedOperation.ActivatedChain.CommandItem().Command.CommandNo'));
if (!$consultingContext) {
    $form->addElement('text', 'Reference', _('Component reference'), array(),
        array('Path' => 'Component.Product.BaseReference'));
}
$tskFilter = ($consultingContext)?array('Id' => getConsultingTaskIds()):array();
$tskArray = SearchTools::createArrayIDFromCollection(
    'Task', $tskFilter, _('Select a task'));
$form->addElement('select', 'Task', _('Task'), array($tskArray));
$opeFilter = ($consultingContext)?array('Type' => array(Operation::OPERATION_TYPE_CONS)):array();
$operationArray = SearchTools::createArrayIDFromCollection(
    'Operation', $opeFilter, _('Select an operation'));
$form->addElement('select', 'OperationName', _('Operation'), array($operationArray),
    array('Path' => 'ActivatedOperation.Operation'));
$customerArray = SearchTools::createArrayIDFromCollection(
    'Customer', array('Generic' => 0), _('Select a customer'));
$form->addElement('select', 'Customer', _('Customer'), array($customerArray),
    array('Path' => 'ActivatedOperation.ActivatedChain.CommandItem().Command.Destinator'));

if ($consultingContext) {
    $pmFilter = array('Generic' => 0);
    if ($profileID == UserAccount::PROFILE_GED_PROJECT_MANAGER) {
        $pmFilter['Id'] = $auth->getUser()->getActorId();
        $firstItem = '';
    } else {
        $firstItem = _('Select a project manager');
    }
    $pmArray = SearchTools::createArrayIDFromCollection(
        'ProjectManager', $pmFilter, $firstItem);
    $form->addElement('select', 'ProjectManager', _('Project manager'),
        array($pmArray),
        array('Path' => 'ActivatedOperation.Actor')
    );
    $form->addElement('text', 'Signatory', _('Signatory'), array(),
        array(
            'Path' => 'ActivatedOperation.ActivatedChain.CommandItem().Command.Destinator.ActorDetail.Signatory',
            'Operator' => 'Like'
        )
    );
}
if ($aeroContext) {
    $form->addElement('text', 'Immatriculation', _('Airplane'), array(),
        array('Path' => 'ActivatedOperation.ConcreteProduct.Immatriculation'));
}

//if ($profileID != UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW) {
    $form->addElement('text', 'Actor', _('Assigned actor'), array(),
            array('Path' => 'ActivatedOperation.Actor.Name'));
/*}
else {
	$form->addBlankElement();
}*/
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
        array('',
			  'onClick="$(\\\'Date1\\\').style.display'
              . '=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name'   => 'BeginDate', 'Path' => 'Begin'),
        array('Name'   => 'EndDate', 'Path' => 'Begin'),
        array('EndDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')),
        	  'BeginDate' => array('Y'=>date('Y'))));
$stateArray  = array('##'=>_('Select one or more states'));
$stateArray += ActivatedChainTask::getStateConstArray();
$form->addElement('select', 'State', _('State'), array($stateArray, 'multiple size="5"'));

// ActivatedChainTask non finies, correspondant a une Task validable
$filterComponentArray[] = SearchTools::NewFilterComponent(
        'ToBeValidated', 'Task.ToBeValidated', 'Equals', 1, 1);

$form->AddAction(
    array(
        'Caption' => _('Update potential'),
        'URL' => 'PotentialsEdit.php?retURL=' . basename($_SERVER['PHP_SELF']),
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_OPERATOR)
    )
);

/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
    // Evite les interaction entre $_POST et $_SESSION
	SearchTools::cleanCheckBoxDataSession('DateOrder1');
    // si on a pas sélectionné d'état, on affiche pas les états terminé
    if (!SearchTools::requestOrSessionExist('State', '##', 'NotEquals')) {
        $filterComponentArray[] = SearchTools::NewFilterComponent(
                'State', '', 'NotEquals', ActivatedChainTask::STATE_FINISHED, 1);
    }
	/*  Construction du filtre  */
    $filterComponentArray = array_merge($filterComponentArray,
            $form->buildFilterComponentArray());
    //  Création du filtre complet
	$filter = SearchTools::filterAssembler($filterComponentArray);

	$grid = new Grid();
	$grid->itemPerPage = 100;
    $grid->NewAction('Redirect',
        array(
            'Caption' => _('Start'),
            'Title' => _('Start task'),
            'URL' => 'ProductionTaskValidation.php?ackId=%d&StartTask=1'
        )
    );
    $grid->NewAction('Redirect',
        array(
            'Caption' => _('Pause'),
            'Title' => _('Pause task'),
            'URL' => 'ProductionTaskValidation.php?ackId=%d&StopTask=1'
        )
    );
    $grid->NewAction('Redirect',
        array(
            'Caption' => _('Restart'),
            'Title' => _('Restart task'),
            'URL' => 'ProductionTaskValidation.php?ackId=%d&ReStartTask=1'
        )
    );
    $grid->NewAction('Redirect',
        array(
            'Caption' => _('Finish'),
            'Title' => _('Finish task'),
            'URL' => 'ProductionTaskValidation.php?ackId=%d&FinishTask=1'
        )
    );
    $grid->NewAction('Redirect',
        array(
            'Caption' => _('Print log card'),
            'TargetPopup' => true,
            'URL' => 'LogCardEdit.php?ackId=%d&retURL='.$_SERVER['PHP_SELF']
        )
    );
	$grid->NewAction('Print', array());
	$grid->NewAction('Export', array('FileName' => 'Taches'));

	$grid->NewColumn('ActivatedChainTaskEdit', _('Task'));
	$grid->NewColumn('FieldMapper', _('Order'),
        array(
            'Macro' => '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.CommandNo%',
            'Sortable' => false
        )
    );
    if ($consultingContext) {
        $grid->NewColumn('FieldMapper', _('Customer'),
            array(
                'Macro' => '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Destinator.Name%',
                'Sortable' => false
            )
        );
    } else {
        $grid->NewColumn('FieldMapper', _('Parts quantity'),
            array('Macro' => '%RealQuantity%'));
    }
    $grid->NewColumn('FieldMapper', _('Beginning date'),
        array('Macro' => '%Begin|formatdate%'));
    $grid->NewColumn('FieldMapper', _('Limit date'),
        array('Macro' => '%End|formatdate%'));
	$grid->NewColumn('FieldMapper', _('Expected duration'),
        array('Macro' => '%Duration|formatduration%'));
	$grid->NewColumn('FieldMapper', _('Actor'),
        array('Macro' => '%ActivatedOperation.Actor.Name%'));
    $grid->NewColumn('FieldMapperWithTranslation', _('State'),
        array('Macro' => '%State%', 'TranslationMap' => $stateArray));

	$order = array('Begin' => SORT_ASC);
	$form->displayResult($grid, true, $filter, $order);
} // fin FormSubmitted

else { // on n'affiche que le formulaire de recherche, pas le Grid
	Template::page('', $form->render() . '</form>');
}
?>
