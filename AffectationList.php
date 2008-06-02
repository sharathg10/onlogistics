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

define('AFFECTATIONLIST_ITEMPERPAGE', 300);

require_once('config.inc.php');

$auth = Auth::Singleton();
$auth->checkProfiles();

/*  Contruction du formulaire de recherche */
$form = new SearchForm('ProductChainLink');
$form->addElement('text', 'BaseReferencePn', _('Reference'), array(),
        array('Path' => 'Product.BaseReference'));
$form->addElement('text', 'Name', _('Designation'), array('size="70" '),
        array('Path' => 'Product.Name'));
$form->addElement('text', 'Reference', _('Chain reference'),
        array('size="8" maxlength="15"'), array('Path' => 'Chain.Reference'));


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
    /*  Construction du filtre  */
    $FilterComponentArray = $form->buildFilterComponentArray();
    $Filter = SearchTools::filterAssembler($FilterComponentArray);

    $grid = new Grid();
    $grid->itemPerPage = AFFECTATIONLIST_ITEMPERPAGE;

    $grid->NewAction ('Redirect', array(
            'Caption' => _('Unassign'),
            'Title' => _('Unassign product from chain'),
            'TransmitedArrayName' => 'pclId',
            'URL' => 'UnAffectProductFromChain.php')
    );

    $grid->NewColumn('FieldMapper', _('Reference'),
            array('Macro' => '%Product.BaseReference%'));
    $grid->NewColumn('FieldMapper', _('Designation'),
            array('Macro' => '%Product.Name%', 'Sortable' => false));
    $grid->NewColumn('FieldMapper', _('Selling unit'),
            array('Macro' => '%Product.SellUnitType.LongName%', 'Sortable' => false));
    $grid->NewColumn('FieldMapper', _('Chain'), array('Macro' => '%Chain.Reference%'));

    $Order = array('Product' => SORT_ASC);

    $form->displayResult($grid, true, $Filter, $Order);
}
else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}

?>