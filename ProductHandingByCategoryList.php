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

define('ITEMS_PER_PAGE', 200);
//Database::connection()->debug = true;

$auth = Auth::Singleton();
$auth->checkProfiles();

/*  Contruction du formulaire de recherche */
$form = new SearchForm('ProductHandingByCategory');

$form->addElement('text', 'Product', _('Product reference'),
		array(), array('Path' => 'Product.BaseReference'));

$catList = SearchTools::createArrayIDFromCollection('Category', array(),
    	_('Select one or more categories'));
$form->addElement('select', 'Type', _('Customer categories'),
	    array($catList, 'multiple size="6"'),
	    array('Path' => 'Category().Id', 'Operator'=>'In'));

$form->addAction(
    array(
        'URL'=>'ProductHandingByCategoryAddEdit.php?retURL=' .
            'ProductHandingByCategoryList.php'
    )
);


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
	/*  Construction du filtre  */
    $filter = $form->buildFilterComponentArray();
    $filter = SearchTools::filterAssembler($filter);

	$grid = new Grid();
	$grid->itemPerPage = ITEMS_PER_PAGE;
	// actions
	$grid->NewAction('AddEdit',
        array(
            'Action' => 'Add',
            'EntityType' => 'ProductHandingByCategory',
            'Query' => 'retURL=ProductHandingByCategoryList.php'
        )
    );
	$grid->NewAction('Delete',
        	 array('TransmitedArrayName' => 'phcIDs',
        		   'EntityType' => 'ProductHandingByCategory'));

	$grid->NewColumn('FieldMapper', _('Product'),
			array('Macro' => '%Product.BaseReference%'));
    $grid->NewColumn('FieldMapper', _('Customer categories'),
            array('Macro' => '%CategoryCollection%', 'Sortable' => false));
	$grid->NewColumn('FieldMapper', _('Discount'),
        	array('Macro' => '<a href="ProductHandingByCategoryAddEditE2.php?phcID=%Id%'.
                    '&retURL=ProductHandingByCategoryList.php">
                    %Handing|formatnumber% %Currency.Symbol|default@&#37;%</a>',
                  'SortField' => 'Handing')
    );
	$form->displayResult($grid, true, $filter);
}

else {   //  on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}

?>