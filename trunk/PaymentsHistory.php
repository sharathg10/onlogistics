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
 * @version   SVN: $Id: PaymentCommandList.php 272 2008-11-26 14:59:17Z izimobil $
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
$form = new SearchForm('Command');

$form->addElement('text', 'Customer', _('Customer'), array(),
        array('Path' => 'SupplierCustomer.Customer.Name'));
$form->addElement('text', 'Supplier', _('Supplier'), array(),
        array('Path' => 'SupplierCustomer.Supplier.Name'));
$form->addElement('text', 'CommandNo', _('Order number'), array(),
        array('Path' => 'CommandNo'));
/*$form->addElement('text', 'DocumentNo', _('Reference'), array(),
    array('Path' => 'PaymentCollection.DocumentNo'));

/*$form->addElement('checkbox', 'DateOrder1', _('Filter by date of issue'),
        array('', 'onclick="$(\\\'Date1\\\').style.'
                . 'display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name' => 'StartEditionDate', 'Path' => 'PaymentCollection.Date'),
        array('Name' => 'EndEditionDate', 'Path' => 'PaymentCollection.Date'),
        array('EndEditionDate' => array(
                'd' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
              'StartEditionDate' => array('Y' => date('Y')))
          );*/

/*  Affichage du Grid  */

$FilterComponentArray = array_merge($FilterComponentArray, $form->buildFilterComponentArray());
$Filter = SearchTools::filterAssembler($FilterComponentArray);

$CommandMapper = Mapper::singleton('Command') ;
$CommandCollection = $CommandMapper->loadCollection($Filter);
$PaymentCollection = new Collection();
$PaymentCollection->acceptDuplicate = true;

if (!Tools::isEmptyObject($CommandCollection)) {
    $count = $CommandCollection->getCount();
    for($i = 0; $i < $count; $i++){
        $item = $CommandCollection->getItem($i);
        //recupere la collection des payments pour chaque commande
        $PaymtCollection = $item->getPaymentCollection(1);
        if (!Tools::isEmptyObject($PaymtCollection)) {
            $jcount = $PaymtCollection->getCount();
            for($j = 0; $j < $jcount; $j++){
                $itemj = $PaymtCollection->getItem($j);
                if($itemj->getCommand() == FALSE ) $itemj->setCommand($item);
                $PaymentCollection->SetItem($itemj);
            }
        }
        unset($PaymtCollection);
        unset($item);
    }
}

if (true === $form->displayGrid()) {
    $grid = new Grid();
    $grid->itemPerPage = 50;

//$phpself = $_SERVER['PHP_SELF'].'?&returnURL='.$retURL;
//$grid->NewAction('Cancel', array('Caption'=>_('State of orders'), 'ReturnURL'=>$retURL));

$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%DocumentNo%'));

$grid->NewColumn('FieldMapper', _('Command'),
    array('Macro' => '%Command.CommandNo%', 'Sortable' => false));

$grid->NewColumn('FieldMapper', _('Invoice'),
    array('Macro' => '%InvoiceCollection.Invoice.DocumentNo%', 'Sortable' => false));

$grid->NewColumn('FieldMapper', _('Date'),
    array('Macro' => '%Date|formatdate@DATE_SHORT%'));

//$grid->NewColumn('FieldMapper', _('Expected Date'),
//    array('Macro' => '%Expected Date|formatdate@DATE_SHORT%'));

$grid->NewColumn('FieldMapper', _('Paid'), array('Macro' => '%TotalPriceTTC%'));

$grid->NewColumn('FieldMapperWithTranslation', _('Means of payment'),
        array('Macro' => '%Modality%',
              'TranslationMap' => array(-1=>_('N/A')) + TermsOfPaymentItem::getPaymentModalityConstArray()));

$grid->NewColumn('FieldMapper', _('Bank'),
        array('Macro' => '%ActorBankDetail.BankName|default%'));
//$grid->NewColumn('MultiPrice', _('Total amount incl. VAT'),
//        array('Method' => 'getTotalPriceTTC'));


    $order = array('Date' => SORT_DESC);
    $result = $grid->render($PaymentCollection);
    Template::page(
        _('List of payments for selected invoices'),
        $form->render().'</form>'. $result
    );

} // fin DisplayGrid

else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}
?>
