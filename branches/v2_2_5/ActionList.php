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
require_once('Objects/FormModel.php');
require_once('Objects/Action.php');
require_once('ActionTools.php');

$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
        UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_DIR_COMMERCIAL));

// Filtre pour le Grid selon le Profile connecte
$FilterComponentArray = array(); // Tableau de filtres
if($auth->getProfile()==UserAccount::PROFILE_COMMERCIAL) {
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Commercial', '', 'Equals', $auth->getUserId(), 1);
}
if (isset($_REQUEST['actID'])) {
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Actor', '', 'Equals', $_REQUEST['actID'], 1);
    // Remplissage du chps text correspondant au critere de recherche
    $actor = Object::load('Actor', $_REQUEST['actID']);
    $_REQUEST['Name'] = $actor->getName();
}


if(isset($_GET['exportStats'])) {
    $commId = ($auth->getProfile()==UserAccount::PROFILE_COMMERCIAL)?$auth->getUserId():0;
    $stats = new ActionStats($commId);
    $data = $stats->toCSV();
    header('Pragma: public');
    header('Content-Type: application/force-download');
    header('Content-Disposition: attachment;filename=ventilation.csv');
    echo $data;
    exit;
}

$form = new SearchForm('Action');

// champs de recherche du formulaire
$form->addElement('text', 'Name', _('Customer'), array(), array('Path'=>'Actor.Name'));
$actionTypeArray = FormModel::getActionTypeConstArray();
$form->addelement('select', 'Type', _('Action type'),
        array($actionTypeArray, ' multiple size="3"'));
$actionStateArray = Action::getStateConstArray();
$form->addelement('select', 'State', _('Status'),
        array($actionStateArray, ' multiple size="3"'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
        array('', 'onClick="$(\\\'Date1\\\').style.'
                . 'display=this.checked?\\\'block\\\':\\\'none\\\';"'));
$form->addDate2DateElement(
        array('Name' => 'StartDate', 'Path' => 'ActionDate'),
        array('Name' => 'EndDate', 'Path' => 'ActionDate'),
        array('StartDate' => array('Y' => date('Y'), 'm' => date('m')-1, 'd' => date('d')),
              'EndDate' => array('Y' => date('Y'), 'm' => date('m'), 'd' => date('d'))
              )
);

// actions du formulaire
$form->addAction(array('URL' => 'ActionAddEdit.php'));

if ($form->displayGrid() == true || isset($_REQUEST['actID'])) {
    $FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray(1));
    $filter = SearchTools::filterAssembler($FilterComponentArray);

	$grid = new Grid();
    $grid->itemPerPage = 200;

    // colonnes
    $grid->newColumn('FieldMapper', _('Customer'), array('Macro'=>'%Actor.Name%'));
    $grid->newColumn('FieldMapperWithTranslation', _('Type'),
    	array('Macro'=>'%Type%',
    	      'TranslationMap'=>FormModel::getActionTypeConstArray()));
    $grid->newColumn('FieldMapper', _('Action'),
    	array('Macro'=>'<a href="ActionAddEdit.php?aID=%Id%">%FormModel.Name%</a>'));
    $grid->newColumn('FieldMapper', _('Date'),
    	array('Macro'=>'%ActionDate|formatdate@DATE_SHORT%'));
    $grid->newColumn('FieldMapperWithTranslation', _('Status'),
    	array('Macro'=>'%State%', 'TranslationMap'=>Action::getStateConstArray()));

    //actions
    $grid->NewAction('AddEdit', array('Action' => 'Add', 'EntityType' => 'Action'));
    $grid->newAction('Delete', array('TransmitedArrayName'=>'aIDs',
            'EntityType'=>'Action',
            'Profile'=>array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                    UserAccount::PROFILE_DIR_COMMERCIAL)));
    $grid->NewAction('Redirect', array('URL'=>'ActionList.php?exportStats=1',
            'Caption'=>_('Statistics by action type')));
    $grid->NewAction('Print');
    $grid->NewAction('Export', array('FileName'=>'ActionsCommerciales'));
    $grid->NewAction('Cancel', array('ReturnURL'=>'ActorList.php'));
    $order = array('Actor.Name' => SORT_ASC);
    $form->displayResult($grid, true, $filter, $order);
}
else {
    Template::page('', $form->render() . '</form>');
}

?>
