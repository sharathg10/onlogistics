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

$auth = Auth::Singleton();
$auth->checkProfiles();
// on force la suppression de la var ajouté en session par FlowAddEdit car le 
// server Ajax fait un prolongdatainsession
Session::unregister('flow');
//  Contruction du formulaire de recherche
$form = new SearchForm('Flow');
$form->addElement('text', 'Number', _('Number'));
$flowTypes = SearchTools::createArrayIDFromCollection(
        'FlowType', array('InvoiceType'=>0),
        _('Select one or more types'));
$form->addElement('select', 'FlowType', _('Model'),
        array($flowTypes, 'multiple size="3"'), array('Operator'=>'Like'));

$form->addElement('text', 'Name', _('Name'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by payment date'),
    array('', 'onClick="$(\\\'Date1\\\').'
        . 'style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));
$form->addDate2DateElement(
    array('Name'=>'StartDate', 'Path'=>'PaymentDate'),
    array('Name'=>'EndDate', 'Path'=>'PaymentDate'),
    array(
        'EndDate'=>array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')),
        'StartDate'=>array('Y'=>date('Y'))
    )
);
$form->addAction(array('URL'=>'FlowAddEdit.php'));
//  Affichage du Grid
if (true === $form->displayGrid()) {
    // Si recherche lancee sans critere de date, il faut tuer la var de session
    // pour la case a cocher
    if (isset($_POST['formSubmitted']) && !isset($_POST['DateOrder1'])) {
        unset($_SESSION['DateOrder1']);
    }

    $filter = SearchTools::filterAssembler($form->buildFilterComponentArray());
    $grid = new Grid();
    $grid->itemPerPage = 500;

    // actions
    $grid->NewAction('AddEdit', array('Action'=>'Add', 'EntityType'=>'Flow'));
    $grid->NewAction('Delete', array('EntityType'=>'Flow',
        'TransmitedArrayName'=>'flowIDs'));
    $grid->NewAction('Export', array('FileName'=>'Flows'));
    $grid->NewAction('Print');

    // colonnes
    $grid->NewColumn('FieldMapper', _('Number'),
        array('Macro'=>'<a href="FlowAddEdit.php?flowID=%Id%&retURL='
            . $_SERVER['PHP_SELF'].'">%Number%</a>'));
    $grid->NewColumn('FieldMapper', _('Name'),
        array('Macro'=>'%Name%'));
    $grid->NewColumn('FieldMapper', _('Model'),
        array('Macro'=>'%FlowType.Name%'));
    $grid->NewColumn('FieldMapper', _('Registration date'),
        array('Macro'=>'%EditionDate|formatdate@DATE_SHORT%'));
    // XXX utiliser format currency ??
    $grid->NewColumn('FieldMapper', _('Amount incl. VAT'),
        array('Macro'=>'%TotalTTC|formatnumber% %Currency.Symbol%'));
    $grid->NewColumn('FieldMapper', _('Payment date'),
        array('Macro'=>'%PaymentDate|formatdate@DATE_SHORT%'));

    $form->displayResult($grid, true, $filter, array('Name'=>SORT_ASC));
} else {
    // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}
?>
