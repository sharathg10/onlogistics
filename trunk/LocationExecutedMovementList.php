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
require_once('Objects/MovementType.const.php');
require_once('Objects/SellUnitType.const.php');

$Auth = Auth::Singleton();
$ProfileId = $Auth->getProfile();
$Auth->checkProfiles();
$uac = $Auth->getUser();
$siteIds = $uac->getSiteCollectionIds();

// Suppression de cette var liee au mode de suivi
unset($_SESSION['LCPCollection']);

$smarty = new Template();
$session = Session::Singleton();

$grid = new Grid();
// Pour ne pas avoir d'erreur js liee a customisation searchForms
$grid->javascriptFormOwnerName = 'LocationExecutedMovementList';
$grid->customizationEnabled = true;
$grid->setNbSubGridColumns(2);
$grid->itemPerPage = 50;

$grid->NewColumn('FieldMapperWithTranslationExpression', _('Movement'),
        array('Macro' =>'%Cancelled%',
              'TranslationMap' => array (-1 => '<a href="javascript:void(0);" '
                . 'title="%ExecutedMovement.Comment%">%Id%</a><span title="'
                . _('This movement was cancelled.') . '" class="rouge">*</span>'),
               'DefaultValue' => '<a href="javascript:void(0);" '
                . 'title="%ExecutedMovement.Comment%">%Id%</a>',
                'SortField' => 'Id'));
$grid->NewColumn('FieldMapper', 'Date', array('Macro' => '%Date|formatdate%'));
$FilterComponentArray = array(); // Tableau de filtres

// Place ici, pour eviter error si on vient de ActivatedMovtAddWithPrevision.php
$form = new SearchForm('LocationExecutedMovement');

$form->customizationEnabled = true;  // les criteres de recherches sont desactivables
$form->hiddenCriteriaByDefault = array('Commercial', 'CommandDate');

if (isset($_REQUEST['exmId'])) {  // si on vient de ActivatedMovtAddWithPrevision.php
    $grid->paged = false;
    $grid->displayCancelFilter = false;
    $grid->withNoCheckBox = true;
    $grid->NewAction('Close');
} else {  // Affichage du form de recherche, ...
    /*  Contruction du formulaire de recherche  */
    $form->addElement('text', 'CommandNo', _('Order number'), array(),
        array('Path' => 'ExecutedMovement.ActivatedMovement.ProductCommand.CommandNo'));
    $form->addElement('text', 'Product', _('Reference'), array(),
            array('Path' => 'Product.BaseReference'));
    $form->addElement('text', 'Location', _('Location'), array(),
            array('Path' => 'Location.Name'));
    $form->addElement('text', 'ProductName', _('Designation'), array(),
            array('Path' => 'Product.Name'));
    $form->addElement('text', 'Store', _('Store'), array(),
            array('Path' => 'Location.Store.Name'));
    $form->addElement('text', 'StorageSite', _('Site'), array(),
            array('Path' => 'Location.Store.StorageSite.Name'));
    $form->addElement('text', 'StockOwner', _('Stock owner'), array(),
        array('Path' => 'Location.Store.StockOwner.Name'));
    $PdtOwnerFilter = array('Active' => 1);
    // Si Client proprietaire ou fournisseur, ne voit pas les autres acteurs
    // Le filtre resultant est 'automatique'
    if ($ProfileId == UserAccount::PROFILE_OWNER_CUSTOMER ||
        $ProfileId == UserAccount::PROFILE_SUPPLIER_CONSIGNE) {
        $PdtOwnerFilter[] = array('Id' => $Auth->getActorId());
        $blankItem = '';
    } else {
        $blankItem = _('Select an actor');
    }
    $form->addElement('select', 'ProductOwner', _('Product owner'), array(
        SearchTools::createArrayIDFromCollection('Actor', $PdtOwnerFilter,
            $blankItem)), array('Path' => 'Product.Owner')
    );
    $MovementTypeInitialArray = array('##' => _('Select one or more types'));
    // Les 3 types d'annulation de LEM:
    $CancellationMovemtTypeArray = array(1001 => _('Cancellation'),
            1002 => _('Customer refusal'), 1003 => _('Impossible delivery'));
    $MovementTypeArray = $MovementTypeInitialArray + $MovementTypeArray
            + $CancellationMovemtTypeArray;
    // le FilterComponent ne sera pas construit automatiqt, mais gere à part
    $form->addElement('select', 'Type', _('Movement type'),
            array($MovementTypeArray, 'multiple size="5"'),
            array('Path' => 'ExecutedMovement.Type', 'Disable' => true));
    $form->addElement('checkbox', 'Cancelled', _('Cancelled'), array(),
            array('Path' => "", 'Value' => '-1'));
    $form->addElement('checkbox', 'NotCancelled', _('Not cancelled'), array(),
            array('Path' => 'Cancelled',
                  'Operator' => 'GreaterThanOrEquals',  // NotEquals // -1
                  'Value' => '0'));
    $form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
            array('', 'onClick="$(\\\'Date1\\\').'
            . 'style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));
    $form->addDate2DateElement(
            array('Name'   => 'Start', 'Path' => 'Date'),
            array('Name'   => 'End', 'Path' => 'Date'),
            array('End' => array('d'=>date('d'),'m'=>date('m'),'Y'=>date('Y')),
                  'Start' => array('Y'=>date('Y'))));

    // Si nouvelle recherche
    if (isset($_REQUEST['actionId']) && $_REQUEST['actionId'] == -1) {
        unset($_SESSION['lemFirstArrival']);
    }
    // Si 1ere arrivee sur cet ecran, filtre par defaut: dernieres 24h
    if ($form->isFirstArrival() || isset($_SESSION['lemFirstArrival'])) {
        // Nettoie les donnees du form de provenance si besoin
        SearchTools::cleanDataSession('noPrefix');
        $form->setDisplayForm(false);
        $FilterComponentArray[] = SearchTools::NewFilterComponent('Date', "",
                'GreaterThan', date('Y-m-d H:i:s', strtotime ("-1 days")), 1);
        if ($ProfileId == UserAccount::PROFILE_OWNER_CUSTOMER ||
            $ProfileId == UserAccount::PROFILE_SUPPLIER_CONSIGNE) {
            $FilterComponentArray[] = SearchTools::NewFilterComponent(
                'Product.Owner','Product.Owner', 'Equals', $Auth->getActorId(), 1);
        }
        // Mise en session  sert, suite a un msge d'erreur, a revenir sur
        // le bon grid filtre
        $session = Session::Singleton();
        $session->register('lemFirstArrival', 1, 3); // En session pour 3 pages
        // XXX obligé de faire ça ici pour que LastEntitySearched soit mis en 
        // session (cf. SearchForm.php ligne 1043)
        $_REQUEST['formSubmitted'] = true;
    }
}

/*  Affichage du Grid  */
if (isset($_REQUEST['exmId']) || true === $form->displayGrid() || $form->isFirstArrival()) {
    if(!empty($siteIds)) {
        $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'StorageSite', 'Location.Store.StorageSite.Id',
                'In', $siteIds, 1);
    }
    /*  Gestion des droits  */
    if ($ProfileId == UserAccount::PROFILE_GESTIONNAIRE_STOCK) {
        $FilterComponentArray[] = SearchTools::NewFilterComponent(
                'SiteOwner', 'Location.Store.StorageSite.Owner.Id',
                'Equals', $Auth->getActorId(), 1);
    }
    elseif ($ProfileId == UserAccount::PROFILE_SUPPLIER_CONSIGNE) {
        $FilterComponentArray[] = SearchTools::NewFilterComponent(
                'StockOwner', 'Location.Store.StockOwner.Id',
                'Equals', $Auth->getActorId(), 1);
    }
    elseif ($ProfileId == UserAccount::PROFILE_ADMIN_VENTES ||
        $ProfileId == UserAccount::PROFILE_AERO_ADMIN_VENTES) {
        // Recupere les LocationExecutedMovement dont l'expeditor de la cde
        // correspondante egal l'acteur du user connecté ou le destinator de
        // la cde correspondante egal l'acteur du user connecté ou le owner de
        // la chaine correspondante activée egal l'acteur du user connecté ou
        // le customer de la cde correspondante egal l'acteur du user connecté
        $rule1 = new FilterRule(
                'ExecutedMovement.ActivatedMovement.ProductCommand.Expeditor',
                FilterRule::OPERATOR_EQUALS, $Auth->getActorId());
        $component1 = new FilterComponent();
        $component1->setItem($rule1);

        $rule2 = new FilterRule(
                'ExecutedMovement.ActivatedMovement.ProductCommand.Destinator',
                FilterRule::OPERATOR_EQUALS, $Auth->getActorId());
        $component2 = new FilterComponent();
        $component2->setItem($rule2);

        $rule3 = new FilterRule(
                'ExecutedMovement.ActivatedMovement.ProductCommandItem.ActivatedChain.Owner',
                FilterRule::OPERATOR_EQUALS, $Auth->getActorId());
        $component3 = new FilterComponent();
        $component3->setItem($rule3);

        $rule4 = new FilterRule(
                'ExecutedMovement.ActivatedMovement.ProductCommand.Customer',
                FilterRule::OPERATOR_EQUALS,
                $Auth->getActorId());
        $component4 = new FilterComponent();
        $component4->setItem($rule4);

        $FilterComponentArray[] = new FilterComponent(
                $rule1, $rule2, $rule3, $rule4, FilterComponent::OPERATOR_OR);
    }

    // si recherche lancee sans critere de date, il faut tuer la var de session
    // pour la case a cocher
    if (isset($_REQUEST['formSubmitted']) && !isset($_REQUEST['DateOrder1'])) {
        unset($_SESSION['DateOrder1']);
    }

    /*  Necessaire car un checkbox envoie tjs une valeur!!! */
    if (isset($_REQUEST['Cancelled'])) {
        unset($_SESSION['NotCancelled']);
    }
    elseif (isset($_REQUEST['NotCancelled'])) {
        unset($_SESSION['Cancelled']);
    }
    else {
        unset($_SESSION['Cancelled'], $_SESSION['NotCancelled']);
    }

    /*  Construction du filtre  */
    //  Filtre sur le type de mvt: particulier, car ds le select, on a 2 types
    // d'infos differents
    if (SearchTools::requestOrSessionExist('Type')!= false
            && !in_array('##', SearchTools::requestOrSessionExist('Type'))) {
        $MovtTypeArray = SearchTools::requestOrSessionExist('Type');
        $FilterComponent = new FilterComponent();
        foreach($MovtTypeArray as $key => $value) {
            if (intval($value) < 1000) {  // si vraiment un MovementType
                $FilterComponent1 = new FilterComponent();
                $FilterComponent1->setItem(new FilterRule(
                        'ExecutedMovement.Type',
                        FilterRule::OPERATOR_EQUALS,
                        intval($value)));
                $FilterComponent1->setItem(new FilterRule(
                        'Cancelled',
                        FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                        0));
                $FilterComponent1->operator = FilterComponent::OPERATOR_AND;
                $FilterComponent->setItem($FilterComponent1);
            }
            else {  // si un des 3 types d'annulation de LEM
                $FilterComponent->setItem(new FilterRule(
                        'Cancelled',
                        FilterRule::OPERATOR_EQUALS,
                        intval($value - 1000)));
            }
            $FilterComponent->operator = FilterComponent::OPERATOR_OR;
        }
        $FilterComponentArray[] = $FilterComponent;
    }

    $grid->NewColumn('FieldMapper', _('Reference'),
            array('Macro' =>'%Product.BaseReference%'));
    $grid->NewColumn('FieldMapper', _('Designation'), array('Macro' =>'%Product.Name%'));
    $grid->NewColumn('FieldMapper', _('Type'), array('Macro' =>'%Name%',
            'Sortable' => false));
    // si on vient de ActivatedMovtAddWithPrevision.php, on affiche le type d'UV
    if (isset($_REQUEST['exmId'])) {
        $grid->NewColumn('FieldMapper', _('Selling unit'),
                array('Macro' =>'%Product.SellUnitQuantity% %Product.SellUnitType.ShortName%',
                      'Sortable' => false));
    }
    else {
        $grid->NewAction('Redirect',
                array('Caption' => _('Delivery order'),
                      'Title' => _('Print delivery order'),
                      'TargetPopup' => true,
                      'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                                          UserAccount::PROFILE_GESTIONNAIRE_STOCK),
                      'ReturnURL' => 'javascript:window.close();',
                      'URL' => 'DeliveryOrderEdit.php?LEM=%d'));

        $grid->NewAction('Redirect',
                array('Caption'=> _('Forwarding form'),
                      'Title'=> _('Print forwarding form'),
                      'TargetPopup' => true,
                      'TransmitedArrayName'=>'lemIDs',
                      'URL'=>'ForwardingFormEdit.php',
                      'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                                          UserAccount::PROFILE_GESTIONNAIRE_STOCK)));

        $grid->NewAction('Redirect',
                array('Caption' => A_CANCEL,
                      'Title' => _('Cancel movement'),
                      'TransmitedArrayName' => 'LEM',
                      'URL' => 'LocationExecutedMovementDelete.php?LEM=%d&'. SID,
                      'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                                          UserAccount::PROFILE_GESTIONNAIRE_STOCK)));
        // Pour tous les Profiles
        $grid->NewAction('Print');
        $grid->NewAction('Export', array('FileName' => _('Executed movements'),
                'FirstArrival' => $form->isFirstArrival()));

        $grid->NewColumn('FieldMapperWithTranslationExpression', _('Order'),
                array('Macro' =>'%ExecutedMovement.ActivatedMovement.ProductCommand.CommandNo%',
                'TranslationMap' => array ('N/A' => '%ForwardingForm.CommandNo%',
                '0' => '%ForwardingForm.CommandNo%')));
        $grid->NewColumn('FieldMapperWithTranslation', _('Forwarding form'),
                array('Macro' =>'%ForwardingForm.DocumentNo%',
                      'TranslationMap'=>array(0=>'')));
        $grid->NewColumn('FieldMapperWithTranslation', _('Delivery order'),
                array('Macro' =>'%DeliveryOrder.DocumentNo%',
                      'TranslationMap'=>array(0=>'')));
    }
    $grid->NewColumn('FieldMapperWithTranslationExpression', _('Qty'),
            array('Macro' =>'%Product.SellUnitType.Id%',
                  'TranslationMap' => array(
                        SELLUNITTYPE_UE => '%Quantity|formatnumber@3@1%',
                        SELLUNITTYPE_UC => '%Quantity|formatnumber@3@1%',
                        SELLUNITTYPE_UB => '%Quantity|formatnumber@3@1%',
                        SELLUNITTYPE_UR => '%Quantity|formatnumber@3@1%'),
                  'DefaultValue' => '%Quantity|formatnumber@3@1% %Product.SellUnitType.ShortName%',
                  'DataType'=>'numeric'));

    $columns = array(_('SN/Lots'), _('Quantity'));
    $grid->NewColumn('LEMSerialNumberList', $columns, array('Sortable' => false));

    $grid->NewColumn('FieldMapper', _('Site'), array('Macro' =>'%Location.Store.StorageSite.Name%'));
    $grid->NewColumn('FieldMapper', _('Store'), array('Macro' =>'%Location.Store.Name%'));
    $grid->NewColumn('FieldMapper', _('Location'), array('Macro' =>'%Location.Name%'));

    if (isset($_REQUEST['exmId'])) {  // si on vient de ActivatedMovtAddWithPrevision.php
       $grid->withNoSortableColumn = true; // met chaque colonne a NON SORTABLE
       $LEMMapper = Mapper::singleton('LocationExecutedMovement');
       $result = $grid->render($LEMMapper, false,
               array('ExecutedMovement' => $_REQUEST['exmId']),
               array('Date' => SORT_DESC, 'Id' => SORT_DESC));
       Template::page(
           _('List of partial movements executed.'),
           '<form>'.$result.'</form>',
           array(), array(), BASE_POPUP_TEMPLATE
       );
    }

    else {
        $FilterComponentArray = array_merge($FilterComponentArray,
                $form->buildFilterComponentArray());
        // Creation du filtre complet
        $filter = SearchTools::filterAssembler($FilterComponentArray);
        $order = array('Date' => SORT_DESC);
        $form->displayResult($grid, true, $filter, $order);
    }
}  // fin DisplayGrid

else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}

?>
