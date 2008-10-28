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
require_once('ActivatedMovementTools.php');
require_once('Objects/MovementType.const.php');
require_once('Objects/ActivatedMovement.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles();
$ProfileId = $Auth->getProfile();
$UserConnectedActorId = $Auth->getActorId();  // l'Actor relie a l'user connecte
$uac = $Auth->getUser();
$siteIds = $uac->getSiteCollectionIds();
// Suppression de cette var liee au mode de suivi
unset($_SESSION['LCPCollection']);
$FilterComponentArray = array(); // Tableau de filtres
$EditBL = "";

// Gestion de l'edition de BL si necessaire:
// ouverture d'un popup en arriere-plan, impression du contenu (pdf), et fermeture de ce popup
if ((isset($_REQUEST['editBL'])) && ($_REQUEST['editBL'] == 1) && (isset($_REQUEST['cmdId']))) {
	$EditBL = "
	<SCRIPT language=\"javascript\">
	function kill() {
		window.open(\"KillPopup.html\",'popback','width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no');
	}
	function TimeToKill(sec) {
		setTimeout(\"kill()\",sec*1000);
	}
	var w=window.open(\"DeliveryOrderEdit.php?Cmd=" . $_REQUEST['cmdId']
        . "\",\"popback\",\"width=800,height=600,toolbars=no,scrollbars=no,menubars=no,status=no\");
	w.blur();
	TimeToKill(12);
	</SCRIPT>";
}
//  Suppression des LPQ en session s'il y en a
unset($_SESSION['LPQCollection']);

/*  Contruction du formulaire de recherche */
$form = new SearchForm('ActivatedMovement');

$form->customizationEnabled = true;  // les criteres de recherches sont desactivables
$form->hiddenCriteriaByDefault = array('Expeditor', 'Destinator', 'Supplier');

$movmentTypeSelectOnly = array('Id' => array(SORTIE_NORMALE,ENTREE_NORMALE,
                                             SORTIE_INTERNE,ENTREE_INTERNE));
$mvtKind = SearchTools::createArrayIDFromCollection('MovementType', $movmentTypeSelectOnly,
        _('Select one or more movement types'));

$form->addElement('select', 'Type', _('Movement type'),
        array($mvtKind, 'multiple size="4"'));
$form->addElement('text', 'Command', _('Order number'), array(),
        array('Path'=>'ProductCommand.CommandNo'));
$form->addElement('text', 'Reference', _('Reference'), array(),
        array('Path' => 'Product.BaseReference'));
$form->addElement('text', 'Name', _('Designation'), array(),
        array('Path' => 'Product.Name'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
        array('', 'onClick="$(\\\'Date1\\\').style.'
                . 'display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
        array('Name' => 'StartDate', 'Path' => 'StartDate'),
		array('Name' => 'EndDate', 'Path' => 'StartDate'),
        array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
        	  'StartDate' => array('Y' => date('Y')))
);

$form->addElement('text', 'Expeditor', _('Expeditor'), array(),
        array('Path'=>'ProductCommand.Expeditor.Name'));
$form->addElement('text', 'Destinator', _('Destinator'), array(),
        array('Path'=>'ProductCommand.Destinator.Name'));
$form->addElement('text', 'Supplier', _('Supplier'), array(),
        array('Path'=>'Product.ActorProduct().Actor.Name'));

/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
    /*  Construction du filtre  */
    // Filtre par defaut
	// La recherche est restreinte aux ACM susceptibles d'être affichés:
    // Ceux a l'état A faire (0) ou En cours (1) ou Partiel (3) ou Bloquée (4),
    // non lies a un devis.
	$FilterComponentArray[] = SearchTools::newFilterComponent(
        'State', "", 'NotEquals', ActivatedMovement::ACM_EXECUTE_TOTALEMENT, 1);
	$FilterComponentArray[] = SearchTools::newFilterComponent(
        'ProductCommand.IsEstimate', "", 'Equals', 0, 1);
    //Si Gestionnaire de stock GS connecte, ne voit que les ACM t.q.:
    // ACM.ACK.ACO.Actor=GS.Actor
    if ($ProfileId == UserAccount::PROFILE_GESTIONNAIRE_STOCK) {
        $FilterComponentArray[] = SearchTools::newFilterComponent(
                'Actor', 'ActivatedChainTask.ActivatedOperation.Actor',
                'Equals', $UserConnectedActorId, 1);
        if (!empty($siteIds)) {
            $FilterComponentArray[] = SearchTools::newFilterComponent(
                'Site', 'ProductCommand.DestinatorSite.Id',
                'In', $siteIds, 1);
        }
    }
    $FilterComponentArray = array_merge(
            $FilterComponentArray, $form->buildFilterComponentArray());
    // Création du filtre complet
	$Filter = SearchTools::filterAssembler($FilterComponentArray);
    $Order = array('StartDate' => SORT_ASC, 'ProductCommand.CommandNo' => SORT_ASC, 'Product.Name' => SORT_ASC);

    //$acmCol = Object::loadCollection('ActivatedMovement', $Filter);
    // Filtre sur les sites autorises pour le user connecte:
    // seulement pour les sorties de stock
    // Remarque: si root connecte, $siteIds est vide => pas de filtre en plus
/*    if(!empty($siteIds)) {
        $torm = array(); // id des acm à ne pas afficher
        $count = $acmCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $filterCmpnts = array();
            $acm = $acmCol->getItem($i);
            if (Tools::getValueFromMacro($acm, '%Type.EntrieExit%') == MovementType::TYPE_ENTRY) {
                continue;
            }
            $filterCmpnts[] = SearchTools::NewFilterComponent(
                'StorageSite', 'Location.Activated', 'Equals', 1, 1);
            $filterCmpnts[] = SearchTools::NewFilterComponent(
                'StorageSite', 'Location.Store.StorageSite', 'In', $siteIds, 1);
            $filterCmpnts[] = SearchTools::NewFilterComponent(
                'Product', '', 'Equals', $acm->getProductId(), 1);
            $filterCmpnts[] = SearchTools::NewFilterComponent(
                'RealQuantity', '', 'NotEquals', 0, 1);
            $filter = SearchTools::filterAssembler($filterCmpnts);
            $lpqCol = Object::loadCollection('LocationProductQuantities', $filter);
            if (Tools::isEmptyObject($lpqCol)) {
                $torm[] = $i;
            }
        }
        $acmCol->removeItem($torm);
    }*/

	$grid = new Grid();
	$grid->customizationEnabled = true;
	$grid->itemPerPage = 50;
	$grid->javascriptFormOwnerName = 'ActivatedMovementList';  // Pour ne pas avoir d'erreur js
	//$grid->withNoCheckBox = true;  // Commente pour l'execution par lot
    
	$grid->NewAction('Redirect', array('Caption' => _('Execute several movements'),  	 
                  'Profiles' => array(UserAccount::PROFILE_ADMIN,
                                UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                                UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR), 	 
                  'TransmitedArrayName' => 'acmId', 	 
                  'URL' => 'ActivatedMovementWithPrevisionExecuteSeveral.php?returnURL=ActivatedMovementList.php') 	 
            );
	$grid->NewAction('Print', array());
    if (in_array('readytowear', Preferences::get('TradeContext', array()))) {
        $grid->NewAction('ActivatedMovementListExport', array(
            'Caption'  => _('Export'),
            'Filter'   => $Filter,
            'Order'    => $Order,
            'FileName' => 'MvtsAttendus',
            'Profiles' => array(
                UserAccount::PROFILE_ADMIN,
                UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_GESTIONNAIRE_STOCK,
                UserAccount::PROFILE_SUPPLIER_CONSIGNE
            )
        ));
    } else {
	    $grid->NewAction('Export', array(
            'FileName' => 'MvtsAttendus',
            'Profiles' => array(
                UserAccount::PROFILE_ADMIN,
                UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_GESTIONNAIRE_STOCK,
                UserAccount::PROFILE_SUPPLIER_CONSIGNE
            )
        ));
    }

	$grid->NewColumn('FieldMapper', _('Order'),
            array('Macro' => '%ProductCommand.CommandNo%'));
	$grid->NewColumn('FieldMapper', _('Movement type'),array('Macro' => '%Type%'));
	$expeditorCol = $grid->NewColumn('FieldMapper', _('Expeditor'), array('Macro' => '%ProductCommand.Expeditor.Name%'));
	$destinatorCol = $grid->NewColumn('FieldMapper', _('Destinator'), array('Macro' => '%ProductCommand.Destinator.Name%'));
	$grid->NewColumn('ActivatedMovementReference', _('Reference'),
            array('Sortable' => false));
	$grid->NewColumn('FieldMapper', _('Designation'), array('Macro'=>'%Product.Name%'));
	$grid->NewColumn('ActivatedMovementSupplierReference', _('Purchase reference'),
            array('Sortable' => false));
	$grid->NewColumn('ActivatedMovementQuantities', _('Qty'),array('Sortable' => false));
	$grid->NewColumn('ActivatedMovementList', _('Site'),
            array('Level' => 'Site', 'Sortable' => false));
	$grid->NewColumn('ActivatedMovementList', _('Store'),
            array('Level' => 'Store', 'Sortable' => false));
	$grid->NewColumn('ActivatedMovementList', _('Location'), array('Sortable' => false));
    $grid->NewColumn('FieldMapper', _('Beginning date'), array('Macro' => '%StartDate|formatdate%'));

    $grid->hiddenColumnsByDefault = array($destinatorCol->index, $expeditorCol->index);

	/* Mise en commentaire provisoire, tant que pas cable avec la plannification
	$grid->NewColumn('FieldMapper', 'Date fin', array('Macro' => '%EndDate|formatdate%'));  */

    $execAction = in_array($ProfileId, array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
        UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR))?
        '<a href="ActivatedMovementAddWithPrevision.php?returnURL='
        . 'ActivatedMovementList.php&amp;ActivatedMvtId=%%Id%%" title="'
        . _('Execute this movement') . '">%s</a>':'%s';

    $grid->NewColumn('FieldMapperWithTranslationExpression', _('State'),
            array('Macro' => '%State%', 'TranslationMap' => array (
                0 => sprintf($execAction, _('To do')),
                1 => _('In progress'),
                3 => sprintf($execAction, _('Partial')),
                4 => _('Locked'))));


    //$form->setItemsCollection($acmCol);
    $form->displayResult($grid, true, $Filter, $Order, '', array(),
            array('beforeForm' => $EditBL));
} // fin FormSubmitted

else { // on n'affiche que le formulaire de recherche, pas le Grid
	Template::page('', $EditBL . $form->render() . '</form>');
}
?>
