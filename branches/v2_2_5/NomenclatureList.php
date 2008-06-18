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

// En cas de Cancel sur NomenclatureAddEdit.php
unset($_SESSION['Nomenclature']);
//  Contruction du formulaire de recherche
$form = new SearchForm('Nomenclature');

$form->addElement('text', 'BaseReference', _('Product reference'), array(),
        array('Path' => 'Product.BaseReference'));
$form->addElement('text', 'Version', _('Nomenclature version'));
$form->addBlankElement();
$form->addElement('checkbox', 'DateOrder1', _('Filter by date of validity'),
        array('', 'onClick="$(\\\'Date1\\\').'
                . 'style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name'   => 'BeginDate',
              'Format' => array('minYear' => date('Y') - 5)),
        array('Name'   => 'EndDate',
              'Format' => array('minYear' => date('Y') - 5)),
        array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
              'StartDate' => array('Y' => date('Y')))
        );
$form->addAction(array('URL' => 'NomenclatureAddEdit.php'));

//  Affichage du Grid
if (true === $form->DisplayGrid()) {
    // Si recherche lancee sans critere de date, il faut tuer la var de session
    // pour la case a cocher
    if (isset($_POST['formSubmitted']) && !isset($_POST['DateOrder1'])) {
        unset($_SESSION['DateOrder1']);
    }

    $filter = SearchTools::filterAssembler($form->buildFilterComponentArray());

    $grid = new Grid();
    $grid->itemPerPage = 100;

    $grid->NewAction('AddEdit', array('Action' => 'Add',
            'EntityType' => 'Nomenclature'));
    $grid->NewAction('Delete', array('EntityType' => 'Nomenclature',
            'TransmitedArrayName' => 'nomId'));
    $grid->NewAction('Redirect', array('Caption' => _('Show by components'),
            'URL' => 'ComponentsTree.php?nomId=%d'));
    $grid->NewAction('Redirect', array('Caption' => _('Show by sets'),
            'URL' => 'ComponentsTree.php?nomId=%d&byGroup=1'));
    $grid->NewAction('Redirect', array('Caption' => _('Build chain'),
        'URL' => 'ChainEdit.php?nomId=%d', 
        'Profiles'=>array(UserAccount::PROFILE_ROOT)));
    $grid->NewAction('Redirect', array('Caption' => _('Copy'),
        'Title' => _('Copy nomenclature'),
        'URL' => 'NomenclatureDuplicate.php?nomID=%d'));

    $grid->NewColumn('FieldMapper', _('Product reference'),
            array('Macro' => '<a href="NomenclatureAddEdit.php?nomId=%Id%&retURL='
                    . $_SERVER['PHP_SELF'].'">%Product.BaseReference%</a>',
            'SortField' => 'Product.BaseReference'));
    $grid->NewColumn('FieldMapper', _('Designation'), array('Macro' => '%Product.Name%'));
    $grid->NewColumn('FieldMapper', _('Nomenclature version'), array('Macro' => '%Version%'));
    $grid->NewColumn('FieldMapper', _('Validity begin'),
            array('Macro' => '%BeginDate|formatdate@DATE_SHORT%'));
    $grid->NewColumn('FieldMapper', _('Validity end'),
            array('Macro' => '%EndDate|formatdate@DATE_SHORT%'));

    $order = array('Product.BaseReference' => SORT_ASC, 'EndDate' => SORT_DESC);

    $form->displayResult($grid, true, $filter, $order);
}

else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}
?>
