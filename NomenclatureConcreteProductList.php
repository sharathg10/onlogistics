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
//Database::connection()->debug = true;

/*  Contruction du formulaire de recherche  */
$form = new SearchForm('ConcreteProduct');

$form->addElement('text', 'BaseReference', _('Product reference'), array(),
				  array('Path' => 'Product.BaseReference'));
$form->addElement('text', 'SerialNumber', _('SN/Lot'));

/*  Affichage du Grid  */
if (true === $form->displayGrid()) {

	/*  Construction du filtre  */
	$FilterComponentArray = array();
    // Les CP lies a un Product lie a un Component de Level 0
    // Attention: ne pas oublier le param 'ConcreteProduct', ici!
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Level', 'Product.Component().Level', 'Equals', 0, 1, 'ConcreteProduct');
	$FilterComponentArray = array_merge($FilterComponentArray,
	       $form->buildFilterComponentArray());
	$Filter = SearchTools::filterAssembler($FilterComponentArray);

	$grid = new Grid();
	$grid->itemPerPage = 100;

	$grid->NewAction('Redirect', array('Caption' => _('Assign to a nomenclature'),
        	'URL' => 'NomenclatureAffect.php?cpId=%d'));
	$grid->NewAction('Redirect', array('Caption' => _('Show by components'),
            'URL' => 'ConcreteComponentsTree.php?cpId=%d'));
    $grid->NewAction('Redirect', array('Caption' => _('Show by sets'),
            'URL' => 'ConcreteComponentsTree.php?cpId=%d&byGroup=1'));

	$grid->NewColumn('FieldMapper', _('Product reference'),
            array('Macro' => '%Product.BaseReference%'));
	$grid->NewColumn('FieldMapper', _('SN/Lot'), array('Macro' => '%SerialNumber%'));
	$grid->NewColumn('FieldMapper', _('Designation'), array('Macro' => '%Product.Name%'));
	$grid->NewColumn('FieldMapperWithTranslation', _('Nomenclature version'),
            array('Macro' => '%Component.Nomenclature.Version%',
                  'TranslationMap' => array ('0' => '-')));

    $order = array('SerialNumber' => SORT_ASC);
	$form->displayResult($grid, true, $Filter, $order);
}

else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}
?>