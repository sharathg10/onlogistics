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
require_once('SQLRequest.php');

$auth = Auth::Singleton();
$auth->checkProfiles();
//Database::connection()->debug = true;
$profileID = $auth->getProfile();
$userConnectedActorID = $auth->getActorId();
$filterComponentArray = array();
// filtre par défaut, on affiche pas les operations qui sont liées à des devis
$filterComponentArray[] = SearchTools::newFilterComponent(
    'IsEstimate',
    'ActivatedChain.CommandItem().Command.IsEstimate',
    'Equals',
    0,
    true,
    'ActivatedChainOperation'
);

// Gestion des droits
if ($profileID == UserAccount::PROFILE_GESTIONNAIRE_STOCK || $profileID == UserAccount::PROFILE_TRANSPORTEUR) {
	// Un User ne voit que les OT de son Actor
	$filterComponentArray[] = SearchTools::NewFilterComponent(
            'Actor', '', 'Equals', $userConnectedActorID, 1);
}
if ($profileID == UserAccount::PROFILE_ACTOR) {
    // recup des bonnes ActivatedChain
    $sql = request_ActivatedChainList($userConnectedActorID, $profileID);
    $chainIDs = executeSQLforCollection('ActivatedChain', $sql);
    if (count($chainIDs) > 0) {
		$filterComponentArray[] = SearchTools::NewFilterComponent(
                'Id', '', 'In', $chainIDs, 1);
    }
}

/**
 * si on vient de ActivatedChainList, pour voir le detail des operations
 * On n'affiche pas le moteur de recherche, et le filtre est sur l'Id de
 * ActivatedChain
 */
if (!empty($_REQUEST['ChnId'])) {
    if ($profileID == UserAccount::PROFILE_ACTOR
    && count(array_diff($_REQUEST['ChnId'], $chainIDs)) > 0) {
        Template::errorDialog(
            _('You are not allowed to execute this action.'),
            'ActivatedChainList.php'
        );
        exit;
    }
	$filterComponentArray[] = SearchTools::NewFilterComponent(
            'ActivatedChain', '', 'In', $_REQUEST['ChnId'], 1);
}

/*  Contruction du formulaire de recherche */
$form = new SearchForm('ActivatedChainOperation');
$opArray = SearchTools::createArrayIDFromCollection(
        'Operation', array(), _('Operations to regroup'), 'Name');
$actArray = SearchTools::createArrayIDFromCollection(
        'Actor', array(), _('Actors assigned to operations'), 'Name');
$zoneArray = SearchTools::createArrayIDFromCollection(
        'Zone', array(), _('All zones'));

$form->addElement('select', 'Operation', _('Operations'), array($opArray));
$form->addElement('select', 'Actor', _('Assigned actors'),
        array($actArray, 'multiple="multiple" size="4"'));
//$form->AddBlankElement(); // pour la mise en page

// Prefixe "_" pour les interactions avec WorkOrderList.php
$form->addElement('text', '_CommandNo', _('Order'), array(),
        array('Path' => 'ActivatedChain.CommandItem().Command.CommandNo'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by beginning date'),
        array('', 'onclick="$(\\\'Date1\\\').'
                . 'style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
    array('Name' => 'StartBeginDate', 'Path' => 'FirstTask.Begin'),
    array('Name' => 'EndBeginDate',   'Path' => 'FirstTask.Begin'),
    array('EndBeginDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
          'StartBeginDate' => array('Y' => date('Y')))
    );
$form->addBlankElement(); // pour la mise en page
$form->addElement('checkbox', 'DateOrder2', _('Filter by end date'),
    	array('', 'onclick="$(\\\'Date2\\\').style.'
        . 'display=this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(
    array('Name' => 'StartEndDate', 'Path' => 'LastTask.End'),
    array('Name' => 'EndEndDate',   'Path' => 'LastTask.End'),
    array('EndEndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
          'StartEndDate' => array('Y' => date('Y')))
    );

// la recherche sur les zones est spéciales, on la gère à part.
$form->addElement('select', 'DepartureZone', _('Departure zone'), array($zoneArray),
    			  array('Disable'=>true));
$form->addElement('select', 'ArrivalZone', _('Arrival zone'), array($zoneArray),
    			  array('Disable'=>true));

/**
 * si on vient de ActivatedChainList, pour voir le detail des operations,
 * on n'affiche pas le moteur de recherche
 */
if (!empty($_REQUEST['ChnId'])) {
    $form->setDisplayForm(false);
}
$pageContent = $form->render();

/*  Affichage du Grid  */
if (!empty($_REQUEST['ChnId']) || true === $form->displayGrid()) {
    // Evite les interaction entre $_POST et $_SESSION
	SearchTools::cleanCheckBoxDataSession(array('DateOrder1', 'DateOrder2'));

    // Diferents types de transport:
    // Si recherche sur un tel type d'operation, on affiche des columns en +
    require_once('Objects/ActivatedChainOperation.inc.php');
    $tptArray = array(OPERATION_TRAE, OPERATION_TRFE, OPERATION_TRFL,
                      OPERATION_TRMA,OPERATION_TRTE);


    /*  Construction du filtre  */
    // On prend les opérations qui ne sont pas déjà dans un ordre de travail
    $filterComponentArray[] = SearchTools::NewFilterComponent(
        'OwnerWorkerOrder', '', 'Equals', 0, 1);
    if(isset($_POST['ArrivalZone']) && $_POST['ArrivalZone']!='##') {
        $filterComponentArray[] = new FilterComponent(
            new FilterRule(
                'LastTask.ActorSiteTransition.ArrivalSite.Zone',
                FilterRule::OPERATOR_EQUALS,
                $_POST['ArrivalZone']),
            new FilterRule(
                'LastTask.ActorSiteTransition.ArrivalSite.CountryCity.Zone',
                FilterRule::OPERATOR_EQUALS,
                $_POST['ArrivalZone']),
            FilterComponent::OPERATOR_OR);
    }
    if(isset($_POST['DepartureZone']) && $_POST['DepartureZone']!='##') {
        $filterComponentArray[] = new FilterComponent(
            new FilterRule(
                'FirstTask.ActorSiteTransition.DepartureSite.Zone',
                FilterRule::OPERATOR_EQUALS,
                $_POST['DepartureZone']),
            new FilterRule(
                'FirstTask.ActorSiteTransition.DepartureSite.CountryCity.Zone',
                FilterRule::OPERATOR_EQUALS,
                $_POST['DepartureZone']),
            FilterComponent::OPERATOR_OR);
    }
	$filterComponentArray = array_merge($filterComponentArray,
										$form->buildFilterComponentArray());
    // Création du filtre complet
    $Filter = SearchTools::filterAssembler($filterComponentArray);

    $grid = new Grid();
    $grid->itemPerPage = 50;
    // pour faire passer ds l'url les ids de activatedChain
    $chainIds = UrlTools::buildURLFromRequest('ChnId');
    $grid->NewAction('Redirect',
	        array(
				  'Caption' => _('Assign to a new work order'),
                  'URL' => 'WorkOrder.php?action=add' . $chainIds,
                  'TransmitedArrayName' => 'SelectedOperations',
		          'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                        UserAccount::PROFILE_ACTOR, UserAccount::PROFILE_GESTIONNAIRE_STOCK,
                        UserAccount::PROFILE_TRANSPORTEUR)
						)
					);
    $grid->NewAction('Redirect',
            array(
				  'Caption' => _('Assign to an existing work order'),
                  'URL' => 'WorkOrderList.php?' . $chainIds,
                  'TransmitedArrayName' => 'SelectedOperations',
		          'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                        UserAccount::PROFILE_ACTOR, UserAccount::PROFILE_GESTIONNAIRE_STOCK,
                        UserAccount::PROFILE_TRANSPORTEUR)
						)
					);

    $grid->NewColumn('FieldMapper', _('Order'),
	        array('Macro' => '%ActivatedChain.CommandItem()[0].Command.CommandNo%',
			      'Sortable' => false));
    $grid->NewColumn('FieldMapper', _('Operation'),
	        array('Macro' => '%Operation.name%', 'Sortable' => false));
    $grid->NewColumn('FieldMapperWithTranslation', _('Assigned actor'),
	        array('Macro' => '%Actor.Name%',
			      'TranslationMap' => array('' => _('N/A')), 'Sortable' => false));
    $grid->NewColumn('FieldMapperWithTranslation', _('Beginning'),
            array('Macro' => '%FirstTask.Begin|formatdate%',
	              'TranslationMap' => array(0 => _('N/A'))));
    $grid->NewColumn('FieldMapperWithTranslation', _('End'),
            array('Macro' => '%LastTask.End|formatdate%',
                  'TranslationMap' => array(0 => _('N/A'))));

    if (in_array(SearchTools::RequestOrSessionExist('Operation'), $tptArray)) {
        $colZone1 = $grid->NewColumn('FieldMapperWithTranslation', _('for departure'),
            array('Macro' => '%FirstTask.ActorSiteTransition.DepartureSite.Zone.Name%',
                  'TranslationMap' => array(0 => _('N/A')),
			      'Sortable' => false));
        $colZone2 = $grid->NewColumn('FieldMapperWithTranslation', _('for arrival'),
            array('Macro' => '%LastTask.ActorSiteTransition.ArrivalSite.Zone.Name%',
                  'TranslationMap' => array(0 => _('N/A')),
                  'Sortable' => false));
        $grid->newColumnGroup(_('Zone'), array($colZone1, $colZone2));
        $colCP1 = $grid->NewColumn('FieldMapper', _('for departure'),
            array('Macro' => '%FirstTask.ActorSiteTransition.DepartureSite.CountryCity.Zip.Code%',
                  'Sortable' => false));
        $colCP2 = $grid->NewColumn('FieldMapper', _('for arrival'),
            array('Macro' => '%LastTask.ActorSiteTransition.ArrivalSite.CountryCity.Zip.Code%',
				  'Sortable' => false));
        $grid->newColumnGroup(_('Zip code'), array($colCP1, $colCP2));
    }

    $grid->NewColumn('ACODuration', _('Duration (h)'), array('Sortable'=>false));
	// AVANT :
	//affiche aujourd'hui les ProductType associés à la chaîne modèle qui a été
	//activée (ActivatedChainOperation.ActivatedChain.Chain.ProductType)
/*    $grid->NewColumn('FieldMapper', 'Type de produits',
					 array('Macro' => '%ActivatedChain.ProductType()|htmlize%',
            			   'Sortable' => false));
*/
	//TODO :
	//Pour une commande de transport (ChainCommand) :
	//	ActivatedChainOperation.ActivatedChain.CommandItem.ProductType
    $grid->NewColumn('ACOProductTypeName', _('Product type'),
					 array('Sortable' => false));

	//Pour une commande de produit (ProductCommand) :
	//	ActivatedChainOperation.ActivatedChain.CommandItem.Product.ProductType
/*	    $grid->NewColumn('FieldMapper', 'Type de produits',
				array('Macro' => '%ActivatedChain.CommandItem()[0].Product.ProductType.name|htmlize%',
            		  'Sortable' => false));
*/
    $acoMapper = Mapper::singleton('ActivatedChainOperation');
    SearchTools::saveLastEntitySearched(); // car pas d'appel a displayResult()

	// construit des champs caches
    $hiddenChainFields = UrlTools::buildHiddenFieldsFromURL('ChnId');
	// construit les vars a passer ds l'url si besoin
    $query = UrlTools::buildURLFromRequest('ChnId');
    if ($grid->isPendingAction()) {
        $acoCollection = false;
        $grid->setMapper($acoMapper);
        $dispatchResult = $grid->dispatchAction($acoCollection);
        if (Tools::isException($dispatchResult)) {
            Template::errorDialog($dispatchResult->getMessage(),
                    'ActivatedChainOperationList.php?' . $query,
                    BASE_POPUP_TEMPLATE);
        } else {
            Template::page(_('List of activated operations'), $dispatchResult);
        }
    } else {
        $order = array('FirstTask.Begin' => SORT_ASC);
        $gridContent = $grid->render($acoMapper, true, $Filter, $order);
        Template::page('', $pageContent . $gridContent . $hiddenChainFields . '</form>');
    }
} else {
	// on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $pageContent . '</form>');
}

?>
