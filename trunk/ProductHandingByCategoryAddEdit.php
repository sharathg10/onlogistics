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

// nombre d'items du grid sur les products
define('PHC_ITEMPERPAGE', 200);

// authentification
$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
        UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES));

// prolonger les données du formulaire de recherche
// Interfere avec le SearchForm de ce script: a voir...
//SearchTools::ProlongDataInSession();

// url de retour
$retURL = isset($_REQUEST['retURL'])?
        $_REQUEST['retURL']:'ProductHandingByCategoryList.php';
$pageTitle = _('select one or more products');

/*  Contruction du formulaire de recherche */
$form = new SearchForm('Product');

$form->addElement('text', 'BaseReference', _('Product reference'));
$form->addElement('text', 'Name', _('Designation'));

$form->addAction(
    array(
        'URL'=>$retURL,
        'Caption' => _('Back')
    )
);

/*  Affichage du Grid  */
if (true === $form->DisplayGrid()) {
	/*  Construction du filtre  */
	// Attention: param 1: conservation des items selectionnes
	// successivement si plusieurs recherches
    $filter = $form->buildFilterComponentArray(1);
    $filter = SearchTools::filterAssembler($filter);

	$grid = new Grid();
	// actions
	$grid->NewAction('Redirect',
					 array(
						 'Caption' => A_VALIDATE,
						 'TransmitedArrayName' => 'pdtId',
				         'URL' => 'ProductHandingByCategoryAddEditE2.php?retURL='.$retURL
					 )
					);
	$grid->NewAction('Cancel', array('ReturnURL' => $retURL));

	$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%BaseReference%'));
	$grid->NewColumn('FieldMapper', _('Designation'), array('Macro' => '%Name%'));
    $grid->itemPerPage = PHC_ITEMPERPAGE;

	// Boucle: une colonne par devise (Currency)
	$mapper = Mapper::singleton('Currency');
	$CurrencyColl = $mapper->loadCollection();
	$count = $CurrencyColl->getCount();
	for($i = 0; $i < $count; $i++) {
		$Currency = $CurrencyColl->getItem($i);
		$grid->NewColumn('ProductHandingByCategoryAddEdit',
						 _('Selling price') . ' ' . $Currency->getSymbol() . ' ' . _('excl. VAT'),
						 array('CurrencyId' => $Currency->getId()));
	}

	$form->displayResult($grid, true, $filter, array(), $pageTitle);
}

else {   //  on n'affiche que le formulaire de recherche, pas le Grid
    Template::page($pageTitle, $form->render() . '</form>');
}
?>
