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
require_once('Objects/ActivatedChain.php');
require_once('SQLRequest.php');

define('ACTIVATEDCHAINLIST_ITEMPERPAGE', 150);

$auth = Auth::Singleton();
$auth->checkProfiles();
$tradeContext = Preferences::get('TradeContext', array());
$consultingContext = in_array('consulting', $tradeContext);

$profileID = $auth->getProfile();
$userConnectedActorID = $auth->getActorId();
$FilterComponentArray = array(); // XOXO

if (!in_array($profileID,
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW))) {
	$sql = request_ActivatedChainList($userConnectedActorID, $profileID);
    $filter = new FilterComponent();
    // Un User ne voit que les Machins de son Actor
    $filter->setItem(new FilterRule('Actor',
            FilterRule::OPERATOR_EQUALS,
            ExecuteSQLforCollection('ActivatedChain', $sql)
            )
        );
    $filter->operator = FilterComponent::OPERATOR_AND;
    $filterComponentArray[] = $filter;
}
/*  Contruction du formulaire de recherche */
$form = new SearchForm('ActivatedChain');
$form->addElement('text', 'CommandNum', _('Order number'), array(),
        		  array('Path' => 'CommandItem().Command.CommandNo'));

if (!$consultingContext) {
    $form->addElement('text', 'Reference', _('Reference'));
    $expLabel  = _('Expeditor');
    $destLabel = _('Addressee');
} else {
    $expLabel  = _('Supplier');
    $destLabel = _('Customer');
}

$form->addElement('text', 'Expeditor', $expLabel, array(),
    array('Path' => 'CommandItem().Command.Expeditor.Name'));

$form->addElement('text', 'Destinator', $destLabel, array(),
    array('Path' => 'CommandItem().Command.Destinator.Name'));

$form->addElement('checkbox', 'DateOrder1', _('Filter by beginning date'),
    array('', 'onClick="$(\\\'Date1\\\')'
            . '.style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
    array('Name' => 'StartBeginDate', 'Path' => 'BeginDate'),
    array('Name' => 'EndBeginDate', 'Path' => 'BeginDate'),
    array('EndBeginDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
          'StartBeginDate' => array('Y' => date('Y')))
    );


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {

	$grid = new Grid();
	$grid->itemPerPage = ACTIVATEDCHAINLIST_ITEMPERPAGE;

	// Actions
	$grid->NewAction(
			'Redirect', array('Caption' => _('Operations'),
			'Title' => _('Display operations'),
	        'TransmitedArrayName' => 'ChnId',
	        'URL' => 'ActivatedChainOperationList.php',
            'Profiles' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                                UserAccount::PROFILE_ACTOR,UserAccount::PROFILE_ADMIN_VENTES,
                                UserAccount::PROFILE_AERO_ADMIN_VENTES))
	    );
	$grid->NewAction(
			'Redirect', array('Caption' => _('Schedule'),
			'Title' => _('Display schedule (Gantt diagram)'),
	        'URL' => 'ActivatedChainGantt.php?chnId=%d')
	    );
	$grid->NewAction('Export', array('FileName' => 'Chaines'));
	// Colonnes
	$grid->NewColumn(
		'FieldMapper',
		_('Order'),
		array('Macro'=>'%CommandItem()[0].Command.CommandNo%', 'Sortable' => false)
	);
	$grid->NewColumn('FieldMapper', _('Chain reference'), array('Macro' => '%Reference%'));
	$grid->NewColumn(
		'FieldMapper', _('Description'),
	    array(
			'Macro' =>'<a href="ActivatedChainOperationList.php?ChnId[]=%ID%">%DESCRIPTION%</a>',
			'SortField' => 'DESCRIPTION'
		)
	);
	$grid->NewColumn(
		'FieldMapper',
		_('Activated on'), array('Macro' => '%CommandItem()[0].Command.CommandDate|formatdate%',
	    'Sortable' => false)
	);
	// date/heure de debut de la chaine (quand elle a deja ete planifiee)
	$grid->NewColumn(
		'FieldMapperWithTranslation', _('Beginning'), array('Macro' => '%BeginDate|formatdate%',
	    'TranslationMap' => array(0 => 'N/A'), 'SortField' => 'BeginDate')
	);
	$grid->withNoSortableColumn = true;

	$FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray());
    $filter = SearchTools::FilterAssembler($FilterComponentArray);
	$order = array('BeginDate' => SORT_DESC);

	$form->displayResult($grid, true, $filter, $order);
} // fin affichage Grid

else {  // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}

?>
