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
require_once('Objects/ConcreteProduct.php');
//require_once('Objects/ConcreteProduct.const.php');

define('ITEMS_PER_PAGE', 100);
//Database::connection()->debug = true;

$auth = Auth::Singleton();
$auth->checkProfiles();

SearchTools::cleanDataSession('ccp');

/*  Contruction du formulaire de recherche */
$form = new SearchForm('ConcreteProduct');
$form->addElement('text', 'Product', _('Product reference'),
            array(), array('Path' => 'Product.BaseReference'));
$form->addElement('text', 'SerialNumber', _('Serial number or lot'));

$form->addElement('select', 'State', _('State'),
        array(ConcreteProduct::getStateConstArray(), 'multiple size="4"'),
        array('Operator' => 'Like'));
$form->addElement('text', 'Owner', _('Owner'),
        array(), array('Path' => 'Owner.Name'));

$form->addElement('checkbox', 'Active', _('Active'));
$form->addElement('checkbox', 'Active_No', _('Inactive'),
        array(), array('Path' => 'Active', 'Operator' => 'NotEquals'));
//$form->addElement('text', 'Immatriculation', 'Immatriculation');


    // Contexte metier: seulement si aero
    $tradeContext = Preferences::get('TradeContext');
    if (!is_null($tradeContext) && in_array('aero', $tradeContext)) {
        $form->AddAction(array('Caption' => _('Update potential'),
               'UseButton' => true,
               'URL' => 'PotentialsEdit.php?retURL=' . basename($_SERVER['PHP_SELF']),
               'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_OPERATOR)
               )
        );
    }

/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
    // Evite les interaction entre $_POST et $_SESSION
	SearchTools::cleanYesNoDataSession('Active', 'Active_No');

    /*  Construction du filtre  */
    $filters = $form->buildFilterComponentArray();
    if ($auth->getProfile() == UserAccount::PROFILE_AERO_CUSTOMER) {
        // le client n'a accès qu'aux ConcreteProducts dont il est Owner
        $filters[] = SearchTools::NewFilterComponent(
                'Owner', '', 'Equals', $auth->getActorId(), 1);
    }
    $filter = SearchTools::filterAssembler($filters);

    $grid = new Grid();
    $grid->itemPerPage = ITEMS_PER_PAGE;

    // actions
    $grid->NewAction('Delete', array('TransmitedArrayName' => 'ccpIds',
                                     'EntityType' => 'ConcreteProduct'));

    $grid->NewColumn('FieldMapper', _('Product'), array('Macro' => '%Product.BaseReference%'));
    $grid->NewColumn(
        'FieldMapper', _('SN/Lot'),
        array('Macro' => '<a href="ConcreteProductAddEdit.php?ccpId=%Id%&amp;retURL='.
                        'ConcreteProductList.php">%SerialNumber%</a>'
        )
    );
//  $grid->NewColumn('FieldMapper', 'Immatriculation', array('Macro'=>'%Immatriculation%'));

    $order = array('Product.BaseReference' => SORT_ASC, 'SerialNumber' => SORT_ASC);

    $form->displayResult($grid, true, $filter, $order);
} // fin FormSubmitted

else {   //  on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}

?>