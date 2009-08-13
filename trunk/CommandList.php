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
$UserConnectedActorId = $auth->getActorId();
$uac = $auth->getUser();
$siteIds = $uac->getSiteCollectionIds();

// contexte metier
$consultingContext = in_array('consulting', Preferences::get('TradeContext', array()));

/*  Gestion des droits  */
$FilterComponentArray = array(); // Tableau de filtres
// on n'affiche pas les devis
$FilterComponentArray[] = SearchTools::newFilterComponent('IsEstimate', '', 'Equals', 0, 1);
if(!empty($siteIds)) {
    $filterCmpnt = array();
    $filterCmpnt[] = SearchTools::newFilterComponent('DestinatorSite', '', 'In', $siteIds, 1);
    $filterCmpnt[] = SearchTools::newFilterComponent('ExpeditorSite', '', 'In', $siteIds, 1);
    $FilterComponentArray[] = SearchTools::filterAssembler($filterCmpnt, FilterComponent::OPERATOR_OR);
}
if ($ProfileId == UserAccount::PROFILE_CUSTOMER || $ProfileId == UserAccount::PROFILE_AERO_CUSTOMER) {
    // restriction aux commandes qui ont pour destinataire l'acteur connecte
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Destinator', '', 'Equals', $UserConnectedActorId, 1);
}
elseif ($ProfileId == UserAccount::PROFILE_COMMERCIAL){
    // restriction aux commandes qui ont pour commercial l'user connecte
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Commercial', '', 'Equals', $auth->getUserId(), 1);
}
elseif ($ProfileId == UserAccount::PROFILE_RTW_SUPPLIER){
    // restriction aux commandes qui ont pour fournisseur l'acteur connecte
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Type', '', 'Equals', Command::TYPE_SUPPLIER, 1);
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Expeditor', '', 'Equals', $auth->getActorId(), 1);
}
elseif ($ProfileId == UserAccount::PROFILE_DIR_COMMERCIAL){
    // restriction aux commandes client
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Expeditor', '', 'Equals', $auth->getActorId(), 1);
}
elseif ($ProfileId == UserAccount::PROFILE_ADMIN_VENTES || $ProfileId == UserAccount::PROFILE_AERO_ADMIN_VENTES){
    // restriction aux commandes enregistrées par son acteur
    $rule1 = new FilterRule('Expeditor',
                            FilterRule::OPERATOR_EQUALS,
                            $auth->getActorId());
    $component1 = new FilterComponent();
    $component1->SetItem($rule1);
    $rule2 = new FilterRule('Destinator',
                            FilterRule::OPERATOR_EQUALS,
                            $auth->getActorId());
    $component2 = new FilterComponent();
    $component2->SetItem($rule2);
    //$rule3 = new FilterRule('CommandItem.ActivatedChain.Owner',
    // FilterRule::OPERATOR_EQUALS, $auth->getActorId());
    //$component3 = new FilterComponent();
    //$component3->SetItem($rule3);
    $rule4 = new FilterRule('Customer',
                            FilterRule::OPERATOR_EQUALS,
                            $auth->getActorId());
    $component4 = new FilterComponent();
    $component4->SetItem($rule4);
    $FilterComponentArray[] = new FilterComponent($rule1, $rule2, /*$rule3, */$rule4,
                                                  FilterComponent::OPERATOR_OR);
}

$smarty = new Template();

/*  Contruction du formulaire de recherche */
$form = new SearchForm('ProductCommand');

$form->customizationEnabled = true;  // les criteres de recherches sont desactivables
$form->hiddenCriteriaByDefault = array('Commercial', 'CommandDate');

$form->addElement('text', 'CommandNo', _('Order number'));
if ($consultingContext) {
    $pmFilter = array('Generic' => 0);
    if ($ProfileId == UserAccount::PROFILE_GED_PROJECT_MANAGER) {
        $pmFilter['Id'] = $auth->getUser()->getActorId();
        $firstItem = '';
    } else {
        $firstItem = _('Select a project manager');
    }
    $pmArray = SearchTools::createArrayIDFromCollection(
        'ProjectManager', $pmFilter, $firstItem);
    $form->addElement('select', 'ProjectManager', _('Project manager'), array($pmArray));
}
if ($ProfileId != UserAccount::PROFILE_RTW_SUPPLIER) {
    // Le profile client ne voit pas les ref d'achat
    if ($ProfileId != UserAccount::PROFILE_CUSTOMER){
        $form->addElement('text', 'SupplierReference', _('Purchase reference'), array(),
            array('Path' => 'CommandItem().Product.ActorProduct().AssociatedProductReference'));
    }
    $form->addElement('text', 'Name', _('Product designation'), array(),
        array('Path' => 'CommandItem().Product.Name'));
    $form->addElement('text', 'BaseReference', _('Product reference'), array(),
        array('Path' => 'CommandItem().Product.BaseReference'));
    $form->addElement('text', 'Expeditor', _('Shipper'), array(),
        array('Path' => 'Expeditor.Name'));
    $form->addElement('text', 'Destinator', _('Addressee'), array(),
        array('Path' => 'Destinator.Name'));
    $form->addElement('text', 'InvoiceNo', _('Document number (invoice, delivery order, etc...)'),
        array(), array('Path' => 'AbstractDocument().DocumentNo'));

    $form->addElement('checkbox', 'Closed', _('Closed'));
    $form->addElement('checkbox', 'NotClosed', _('Not closed'), array(),
        array('Path' => 'Closed', 'Operator' => 'NotEquals'));
    $CommandStateArray = array('##'=>_('Select one or more states'))
        + Command::getStateConstArray();
    $form->addElement('select', 'State', _('State'),
        array($CommandStateArray, 'multiple="multiple" size="5"'));

    $form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
        array('', 'onclick="$(\\\'Date1\\\').style.'
                . 'display=this.checked?\\\'block\\\':\\\'none\\\';"'));

    $form->addDate2DateElement(
        array('Name' => 'StartDate', 'Path' => 'WishedStartDate'),
        array('Name' => 'EndDate', 'Path' => 'WishedStartDate'),
        array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
              'StartDate' => array('Y' => date('Y')))
      );
      
    $form->addElement('text', 'Commercial', _('Salesman'), array(),
        array('Path' => 'Commercial.Name'));
        
    $form->addElement('checkbox', 'DateOrder2', _('Filter by creation date'),
        array('', 'onclick="$(\\\'Date2\\\').style.'
                . 'display=this.checked?\\\'block\\\':\\\'none\\\';"'));

    $form->addDate2DateElement(
        array('Name' => 'StartCommandDate', 'Path' => 'CommandDate'),
        array('Name' => 'EndCommandDate', 'Path' => 'CommandDate'),
        array('EndCommandDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
              'StartCommandDate' => array('Y' => date('Y')))
      );        
} else {
    $form->addElement('text', 'StyleNumber', _('Style number'), array(), array(
        'Path' => 'CommandItem().Product@RTWProduct.Model.StyleNumber'
    ));
}


$defaultValues = $form->getDefaultValues();
// Valeur par defaut: checked pour Non cloture
// array_merge pour ne pas ecraser les defaultvalues issues du date2dateElement
$form->setDefaultValues(array_merge($defaultValues, array('NotClosed' => 1)));

/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
    // Si recherche lancee sans critere de date, il faut tuer la var de session
    // pour la case a cocher
    if (isset($_REQUEST['formSubmitted']) && !isset($_REQUEST['DateOrder1'])) {
        unset($_SESSION['DateOrder1']);
    }
    if (isset($_REQUEST['formSubmitted']) && !isset($_REQUEST['DateOrder2'])) {
        unset($_SESSION['DateOrder2']);
    }
    // Evite les interaction entre $_POST et $_SESSION
	SearchTools::cleanYesNoDataSession('State', 'NotClosed');
	SearchTools::cleanCheckBoxDataSession(array('DateOrder1', 'DateOrder2', 'Closed', 'NotClosed'));

    /*  Construction du filtre  */
    $FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray());
    $Filter = SearchTools::filterAssembler($FilterComponentArray);
    $returnURL = basename($_SERVER['PHP_SELF']);

    define('COMMAND_EVENT_LIST_ITEMPERPAGE', 50);
    $grid = new Grid();
	$grid->customizationEnabled = true;
	$grid->javascriptFormOwnerName = 'CommandList';  // Pour ne pas avoir d'erreur js
    // mettre ici: [nb de colonnes du SubGrid] (pour affichage correct)
    $grid->setNbSubGridColumns(2);
    $grid->itemPerPage = COMMAND_EVENT_LIST_ITEMPERPAGE;

    $grid->NewAction('Redirect', array(
        'Caption' => A_DELETE,
        'URL' => 'CommandDelete.php?CommandId=%d&returnURL=' . $returnURL,
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                            UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL)));

    $grid->NewAction('Redirect', array(
        'Caption' => _('Unlock'),
        'URL' => 'CommandUnlock.php?CommandId=%d',
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                            UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL)));

    $grid->NewAction('Redirect', array(
        'Caption' => _('Charge'),
        'URL' => 'InvoiceAddEdit.php?returnURL=' . $returnURL,
        'TransmitedArrayName' => 'CommandId',
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                            UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES)));

    $grid->NewAction('Redirect', array(
        'Caption' => _('Show invoices'),
        'URL' => 'InvoiceCommandList.php?CommandId=%d&returnURL=' . $returnURL,
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                            UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_COMMERCIAL,
                            UserAccount::PROFILE_DIR_COMMERCIAL)));

    $grid->NewAction('Redirect', array(
        'Caption' => _('Add Instalment'),
        'Title' => _('Add instalment'),
        'URL' => 'InstalmentAdd.php?CommandId=%d&returnURL=' . $returnURL,
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                            UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL)));

    $grid->NewAction('Redirect', array(
        'Caption' => _('View Instalments'),
        'Title' => _('View instalment'),
        'URL' => 'ExpectedInstalmentList.php?CommandId=%d&returnURL=' . $returnURL,
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                            UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL)));

    $grid->NewAction('Redirect', array(
        'Caption' => _('Show payments'),
        'URL' => 'PaymentCommandList.php?cmdId=%d&returnURL=' . $returnURL,
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                            UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_COMMERCIAL,
                            UserAccount::PROFILE_DIR_COMMERCIAL)));


    $grid->NewAction('Redirect', array(
        'Caption' => _('Shipment'),
        'Title' => _('Shipment informations'),
        'URL' => 'CommandExpeditionDetailAddEdit.php?cmdID=%d&retURL=' . $returnURL,
        'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
                            UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_DIR_COMMERCIAL)));

    $grid->NewAction('Redirect', array(
        'Caption' => _('Receipt'),
        'Title' => _('Print order receipt'),
        'TargetPopup' => true,
	    'URL' => 'CommandReceiptEdit.php?cmdId=%d',
        'ReturnURL' => 'javascript:window.close();',
        'Profiles' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                            UserAccount::PROFILE_ACTOR,UserAccount::PROFILE_ADMIN_VENTES,
                            UserAccount::PROFILE_COMMERCIAL,UserAccount::PROFILE_CUSTOMER,
                            UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_DIR_COMMERCIAL)));

    $grid->NewAction('Redirect', array(
        'Caption' => _('Print product labels'),
        'Title' => _('Print product labels'),
        'TargetPopup' => true,
	    'URL' => 'ProductLabelEdit.php?cmdId=%d',
        'ReturnURL' => 'javascript:window.close();',
        'Profiles' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                            UserAccount::PROFILE_RTW_SUPPLIER)));

    $grid->NewAction('Redirect', array(
        'Caption' => _('Print worksheets'),
        'Title' => _('Print worksheets'),
        'TargetPopup' => true,
        'URL' => 'WorksheetEdit.php?cmdId=%d',
        'ReturnURL' => 'javascript:window.close();',
        'Profiles' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                            UserAccount::PROFILE_RTW_SUPPLIER)));

    $grid->NewAction('Print');
    $grid->NewAction('Export', array('FileName' => 'Commandes'));

    $grid->NewColumn('CommandProduct', _('Order'),
        array('Sortable' => false));

    $grid->NewColumn('FieldMapper', _('Date'),
        array('Macro' => '%CommandDate|formatdate@DATE_SHORT%'));

    $grid->NewColumn('FieldMapper', _('Wished Date'),
        array('Macro' => '%WishedDate|formatdate@DATE_SHORT%'));

    $grid->NewColumn('FieldMapper', _('Terms of payment'),
        array('Macro' => '%TermsOfPayment.Name%'));

    if ($ProfileId != UserAccount::PROFILE_RTW_SUPPLIER) {
        $grid->NewColumn('FieldMapperWithTranslation', _('State'),
            array('Macro' => '%State%','TranslationMap' => $ShortCommandStateArray));
        $grid->NewColumn('MultiPrice', _('Amount incl. VAT'),
            array('Method' => 'getTotalPriceTTC', 'Sortable' => false,
                  'DataType' => 'numeric'));
        $grid->NewColumn('FieldMapper', _('Shipper'),
            array('Macro' => '%Expeditor.Name%'));
        $grid->NewColumn('FieldMapper', _('Addressee'),
            array('Macro' => '%Destinator.Name%'));
        $grid->NewColumn('FieldMapper', _('Addressee site'),
            array('Macro' => '%DestinatorSite.Name%'));
        $grid->NewColumn('FieldMapper', _('Salesman'),
            array('Macro' => '%Commercial.Identity|default%'));
        // Si la preference est activee, on affiche une colonne de plus:
        // Si ce sont des qtes d'UE, et la colonne Quantity est renommee
        $qtyCaption = (Preferences::get('ProductCommandUEQty'))?
           _('Selling unit qty'):_('Quantity');
        $columns = (Preferences::get('ProductCommandUEQty'))?
            array(_('Product'), _('Qty pack.'), $qtyCaption, _('Date')):
            array(_('Product'), $qtyCaption, _('Date'));
        $grid->NewColumn('CommandItemList', $columns, 
            array('PackagingUnitQty' => Preferences::get('ProductCommandUEQty'), 
                  'Sortable' => false));
    }

    $order = array('WishedStartDate' => SORT_DESC);

    $form->displayResult($grid, true, $Filter, $order);
} // fin DisplayGrid

else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}
?>
