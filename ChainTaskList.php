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

// authentification
$session = Session::singleton();
$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_TRANSPORTEUR, UserAccount::PROFILE_DIR_COMMERCIAL));

if (!($actID = SearchTools::requestOrSessionExist('actID'))) {
    Template::errorDialog(E_ERROR_SESSION, 'ActorList.php');
    exit(1);
}
$session->register('actID', $actID, 3);

SearchTools::prolongDataInSession();

$filters = array();
$filters[] = SearchTools::NewFilterComponent('Actor', 'Operation.Actor', 'Equals', $actID, 1);
// DepartureInstant > 0 ou ArrivalInstant > 0
$rule1 = new FilterRule(
    'DepartureInstant',
    FilterRule::OPERATOR_GREATER_THAN,
    0
);
$rule2 = new FilterRule(
    'ArrivalInstant',
    FilterRule::OPERATOR_GREATER_THAN,
    0
);
$component = new FilterComponent();
$component->operator = FilterComponent::OPERATOR_OR;
$component->setItem($rule1);
$component->setItem($rule2);
$filters[] = $component;
if ($auth->getProfile() == UserAccount::PROFILE_TRANSPORTEUR) {
    // pour les transporteurs, filtre en plus sur l'acteur du user connecté
    $filters[] = SearchTools::NewFilterComponent('Actor', 'Operation.Actor', 'Equals',
        $auth->getActorId());
}

// création du grid
$grid = new Grid();
$grid->itemPerPage = 30;

// actions du grid {{{
$grid->NewAction('Cancel', array('ReturnURL'=>'ActorList.php'));
// }}}

// colonnes du grid {{{
$grid->NewColumn('FieldMapper', _('Chain'),
    array('Macro'=>'%Operation.Chain.Reference%'));
$grid->NewColumn('FieldMapper', _('Task'),
    array('Macro' => '<a href="ChainTaskAddEdit.php?ctkID=%Id%">%Task.Name%</a>'));
$grid->NewColumn('FieldMapper', _('Actor'),
    array('Macro'=>'%Operation.Actor%'));
$grid->NewColumn('AbstractInstant', _('Departure date'), array('Type'=>'Departure'));
$grid->NewColumn('AbstractInstant', _('Arrival date'), array('Type'=>'Arrival'));
// }}}

// affichage de la page
$filter = SearchTools::filterAssembler($filters);
Template::pageWithGrid($grid, 'ChainTask',
    _('List of fixed date task or weekly task'),
    $filter, array('Task.Name'=>SORT_ASC));

?>