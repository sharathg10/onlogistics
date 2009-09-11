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
require_once('Objects/Command.php');
require_once('Objects/Command.const.php');

$auth = Auth::Singleton();
//Database::connection()->debug = true;
$auth->checkProfiles();
$ProfileId = $auth->getProfile();

/*  Gestion des droits  */
$FilterComponentArray = array(); // Tableau de filtres
$restrictedProfiles = array(UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES,
        UserAccount::PROFILE_ACCOUNTANT);
if (in_array($ProfileId, $restrictedProfiles)) {
    // Restriction aux factures de commandes qui ont pour fournisseur ou customer
    // l'Actor du user connecte
    $rule1 = new FilterRule('SupplierCustomer.Supplier',
                            FilterRule::OPERATOR_EQUALS,
                            $auth->getActorId());
    $rule2 = new FilterRule('SupplierCustomer.Customer',
                            FilterRule::OPERATOR_EQUALS,
                            $auth->getActorId());
    $FilterComponentArray[] = new FilterComponent($rule1, $rule2,
            FilterComponent::OPERATOR_OR);
}


/*  Contruction du formulaire de recherche */
$form = new SearchForm('Invoice');

$form->addElement('text', 'Customer', _('Customer'), array(),
        array('Path' => 'SupplierCustomer.Customer.Name'));
$form->addElement('text', 'Supplier', _('Supplier'), array(),
        array('Path' => 'SupplierCustomer.Supplier.Name'));
$form->addElement('text', 'CommandNo', _('Order number'), array(),
        array('Path' => 'Command.CommandNo'));
$form->addElement('text', 'DocumentNo', _('Invoice number'));
$form->addElement('text', 'PaymentReference', _('Payment reference'), array(),
        array('Path' => 'InvoicePayment().Payment.Reference'));

$currencyArray = SearchTools::CreateArrayIDFromCollection('Currency', array(),
    '', 'ShortName');
$form->addElement('select', 'Currency', _('Currency'), array($currencyArray));

$form->addElement('checkbox', 'DateOrder1', _('Filter by date of issue'),
        array('', 'onclick="$(\\\'Date1\\\').style.'
                . 'display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name' => 'StartEditionDate', 'Path' => 'EditionDate'),
        array('Name' => 'EndEditionDate', 'Path' => 'EditionDate'),
        array('EndEditionDate' => array(
                'd' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
              'StartEditionDate' => array('Y' => date('Y')))
);
$form->addBlankElement();
$form->addElement('checkbox', 'DateOrder2', _('Filter by expiration date'),
        array('', 'onclick="$(\\\'Date2\\\').style.'
                . 'display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name' => 'StartPaymentDate', 'Path' => 'PaymentDate'),
        array('Name' => 'EndPaymentDate', 'Path' => 'PaymentDate'),
        array('EndPaymentDate' => array(
                'd' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
              'StartPaymentDate' => array('Y' => date('Y')))
);

/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
    // Si recherche lancee sans critere de date, il faut tuer la var de session
    // pour les cases a cocher
    if (isset($_REQUEST['formSubmitted']) && !isset($_REQUEST['DateOrder1'])) {
        unset($_SESSION['DateOrder1']);
    }
    if (isset($_REQUEST['formSubmitted']) && !isset($_REQUEST['DateOrder2'])) {
        unset($_SESSION['DateOrder2']);
    }
    /*  Construction du filtre  */
    $FilterComponentArray = array_merge($FilterComponentArray,
           $form->buildFilterComponentArray());
    $Filter = SearchTools::filterAssembler($FilterComponentArray);
    $returnURL = basename($_SERVER['PHP_SELF']);

    $grid = new Grid();
    $grid->itemPerPage = 50;

    $grid->NewAction('Redirect', array('Caption' => _('Reprint'),
            'Title' => _('Print invoice copy'),
            'TargetPopup' => true,
	       	'URL' => 'DocumentReedit.php?id=%d'));

    $grid->NewAction('Redirect', array(
        'Caption' => _('Details'),
        'Title' => _('Invoice details'),
        'URL' => 'InvoiceDetail.php?InvoiceId=%d&amp;returnURL=' . $returnURL));

    $grid->NewAction('Redirect', array(
        'Caption' => _('Payments'),
        'Title' => _('List of payments'),
        'TransmitedArrayName' => 'invId',
        'URL' => 'InvoicePaymentList.php'));

    $grid->NewAction('Print');
    $grid->NewAction('Export', array('FileName' => 'Factures'));

    $grid->NewColumn('FieldMapper', _('Invoice'), array('Macro' => '%DocumentNo%'));
    $grid->NewColumn('FieldMapperWithTranslation', _('Deadline'),
            array('Macro' => '%PaymentDate|formatdate@DATE_SHORT%',
                   'TranslationMap'=>array('00/00/0000'=>'')));
    $grid->NewColumn('FieldMapper', _('Edition date'),
            array('Macro' => '%EditionDate|formatdate@DATE_SHORT%'));
    $grid->NewColumn('FieldMapper', _('Order'),
            array('Macro' => '%Command.CommandNo%'));
    $grid->NewColumn('FieldMapper', _('Supplier'),
            array('Macro' => '%SupplierCustomer.Supplier.Name%'));
    $grid->NewColumn('FieldMapper', _('Customer'),
            array('Macro' => '%SupplierCustomer.Customer.Name%'));
    $grid->NewColumn('FieldMapper', _('Amount excl. VAT'),
            array('Macro' => '%TotalPriceHT|formatnumber%'));
    $grid->NewColumn('ToHaveTVA', _('VAT total'), array('Sortable' => false));
    $grid->NewColumn('FieldMapper', _('Amount incl. VAT'),
            array('Macro' => '%TotalPriceTTC|formatnumber%'));
    $grid->NewColumn('FieldMapper', _('Remaining to pay'),
            array('Macro' => '%ToPay|formatnumber%'));

    $Order = array('EditionDate' => SORT_ASC);

    $form->displayResult($grid, true, $Filter, $Order);
} // fin DisplayGrid

else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}
?>
