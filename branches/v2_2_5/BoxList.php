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

$Auth = Auth::Singleton();
$Auth->checkProfiles();

//Database::connection()->debug=1;
// message de suppression
define('I_BOX_DELETE',
    _('You are about to ungroup all parcels, continue ?'));

/*  Contruction du formulaire de recherche  */
$form = new SearchForm('Box');

$form->addElement('text', 'CommandNo', _('Order number'), array(),
        array('Path' => 'ActivatedChain.CommandItem().Command.CommandNo'));
$form->addElement('text', 'Reference', _('Regrouping reference (parcel, pallet, etc...)'), array(),
		array('Path' => 'ParentBox.Reference'));
$form->addElement('text', 'BaseReference', _('Product reference'), array(),
		array('Path' => 'Reference'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
		array('', 'onClick="$(\\\'Date1\\\').style.'
                . 'display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name'   => 'StartDate',
			  'Format' => array('minYear'   => date('Y') - 5, 'maxYear'   => date('Y')),
			  'Path' => 'ParentBox.Date'),
		array('Name'   => 'EndDate',
			  'Format' => array('minYear'   => date('Y') - 5, 'maxYear'   => date('Y')),
			  'Path' => 'ParentBox.Date'),
        array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
        	  'StartDate' => array('Y' => date('Y')))
        );


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {

	// si recherche lancee sans critere de date, il faut tuer la var de session
    // pour la case a cocher
	if (isset($_POST['formSubmitted']) && !isset($_POST['DateOrder1'])) {
	    unset($_SESSION['DateOrder1']);
	}

	// Filtre par defaut: on n'affiche que les Box ayant une ParentBox
	$FilterComponentArray = array(SearchTools::NewFilterComponent(
            'ParentBox', '', 'GreaterThan', 0, 1));
	$FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray());
	//  Construction du filtre
	$Filter = SearchTools::filterAssembler($FilterComponentArray);

	$grid = new Grid();
	$grid->itemPerPage = 100;

	$grid->NewAction('Delete', array('EntityType' => 'Box',
            'Caption' => _('Cancel regrouping'),
            'TransmitedArrayName' => 'boxIDs',
            'ConfirmMessage' => I_BOX_DELETE));
	$grid->NewAction('Redirect', array('Caption' => _('Print packing list'),
            'TargetPopup' => true,
			'TransmitedArrayName' => 'boxId',
			'URL' => 'PackingListEdit.php?boxId=%d&'. SID));
	$grid->NewAction('Print');
	$grid->NewAction('Export', array('FileName' => 'PackingLists'));

	/*  Colonnes du grid  */
	$grid->NewColumn('FieldMapper', _('Order'),
			array('Macro' => '%ActivatedChain.CommandItem[0].Command.CommandNo%'));
	$grid->NewColumn('FieldMapper', _('Regrouping reference'),
            array('Macro' => '%ParentBox.Reference%'));
	$grid->NewColumn('FieldMapperWithTranslation', _('Content reference'),
			array('Macro' => '%Reference%', 'TranslationMap' => array('0' => 'N/A')));

    $Filter = isset($Filter)?$Filter:array();
	// Pas gerable par le fw
	/*'ActivatedChain.CommandItem[0].Command.WishedStartDate' => SORT_DESC,
	  'ActivatedChainTask.Begin' => SORT_DESC*/
	$Order = array('ParentBox.Reference');

	$form->displayResult($grid, true, $Filter);
} // fin FormSubmitted

else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}
?>
