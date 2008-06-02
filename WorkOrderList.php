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
require_once('Objects/WorkOrder.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles();
$ProfileId = $Auth->getProfile();
$UserConnectedActor = $Auth->getActor();
//Database::connection()->debug = true;

$FilterComponentArray = array(); // Tableau de filtres

if (in_array($ProfileId, array(UserAccount::PROFILE_ACTOR, UserAccount::PROFILE_SUPERVISOR, UserAccount::PROFILE_ADMIN_VENTES,
    UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_TRANSPORTEUR))) {
    // si pas pilote, impossible de saisir un Actor
	$ActorNameFieldType = array('readonly' => 'readonly');
	// Un User ne voit que les OT de son Actor
	$FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Actor', '', 'Equals', $Auth->getActorId(), 1);
}
elseif (in_array($ProfileId, array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW))) {
	$ActorNameFieldType = array();
}
/*  recup des OT qui ont 1 operation liee a une commande passee par l'actor lie
    au UserAccount connecte	 */
if ($ProfileId == UserAccount::PROFILE_ADMIN_VENTES || $ProfileId == UserAccount::PROFILE_AERO_ADMIN_VENTES) {
	$WorkOrderArrayId = $UserConnectedActor->getWorkOrderCollection();
	if (count($WorkOrderArrayId) > 0) {
		$FilterComponentArray[] = SearchTools::NewFilterComponent(
                'Id','','In', $WorkOrderArrayId, 1);
	}
}
/*  Si on vient de ActivatedChainOperationList.php : on transmet ds l'url les id
    de ActivatedChainOperations */
$query = UrlTools::buildURLFromRequest('SelectedOperations');

/*  Contruction du formulaire de recherche  */
$form = new SearchForm('WorkOrder');
$form->setQuickFormAttributes(array('action' => $_SERVER['PHP_SELF']. '?' . $query));

$form->addElement('text', 'Name', _('Work order name'));
$form->addElement('text', 'ActorName', _('Assigned actor'), $ActorNameFieldType,
        array('Path' => 'Actor.Name'));
$form->addElement('text', 'CommandNo', _('Order number'), array(),
		array('Path' => 'ActivatedChainOperation().ActivatedChain.CommandItem()'
                . '.Command.CommandNo'));
$form->addElement('checkbox', 'State', _('Closed'), array(), array('Path' => ''));
$form->addElement('checkbox', 'STATE_TOFILL', _('Not closed'), array(),
        array('Path' => 'State', 'Operator' => 'NotEquals'));
$form->addBlankElement();
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
    array('', 'onClick="$(\\\'Date1\\\').style.display='
            . 'this.checked?\\\'block\\\':\\\'none\\\';"'));

$form->addDate2DateElement(array('Name'   => 'ValidityStart'),
						   array('Name'   => 'ValidityEnd'),
						   array('ValidityStart' => array('Y'=>date('Y')),
					   			 'ValidityEnd' => array('d'=>date('d'),
                                                        'm'=>date('m'),
                                                        'Y'=>date('Y')))
						  );
$form->addAction(array('URL' => 'WorkOrder.php?action=add',
		               'Profiles' =>
        array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ACTOR, UserAccount::PROFILE_SUPERVISOR,
			  UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_TRANSPORTEUR)));

$hiddenFields = '';
// pour faire passer les ids de activatedChain si besoin
$hiddenFields .= UrlTools::buildHiddenFieldsFromURL('ChnId');
// idem pour les ids de activatedChainOpe si besoin
$hiddenFields .= UrlTools::buildHiddenFieldsFromURL('SelectedOperations');
$form->addSmartyValues(array('hiddenFields' => $hiddenFields));

/*  Si on vient de ActivatedChainOperationList.php : on n'affiche pas le form
* et on n'affiche que les OT non clotures
* et idem si on vient de WOCloture.php, suite a une affectation  */
if (isset($_REQUEST['SelectedOperations']) || isset($_REQUEST['from'])) {
	$form->setDisplayForm(false);
	$FilterComponentArray[] = SearchTools::NewFilterComponent(
            'State1', 'State', 'Equals', WorkOrder::WORK_ORDER_STATE_TOFILL, 1);
}
$pageContent = $form->render();


/*  Affichage du Grid  */
if (isset($_REQUEST['SelectedOperations']) || true === $form->displayGrid()) {
	// Evite les interaction entre $_POST et $_SESSION
	SearchTools::cleanYesNoDataSession('State', 'STATE_TOFILL');
	SearchTools::cleanCheckBoxDataSession('DateOrder1');

	/*  Construction du filtre  */
    $FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray());
	$Filter = SearchTools::filterAssembler($FilterComponentArray);
	SearchTools::saveLastEntitySearched();  // car pas d'appel a displayResult()

	$grid = new Grid();
	$grid->itemPerPage = 100;
	$grid->withNoSortableColumn = true;

	$grid->NewAction('AddEdit', array(
            'Action' => 'Add',
			'URL' => 'WorkOrder.php?action=add',
			'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ACTOR,
                                UserAccount::PROFILE_SUPERVISOR, UserAccount::PROFILE_GESTIONNAIRE_STOCK,
								UserAccount::PROFILE_TRANSPORTEUR)));

	/*  Si on vient de ActivatedChainOperationList.php  */
	if (isset($_REQUEST['SelectedOperations']) && is_array($_REQUEST['SelectedOperations'])){
		$grid->NewAction('Redirect',
                array('Caption' => _('Assign'),
                      'Title' => _('Assign selected items'),
					  'URL' => 'WorkOrderAffectOperations.php?WkoId=%d' . $query,
					  'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                                          UserAccount::PROFILE_ACTOR, UserAccount::PROFILE_GESTIONNAIRE_STOCK,
										  UserAccount::PROFILE_TRANSPORTEUR)));
	}

	$grid->NewAction('Delete',
            array('EntityType' => 'WorkOrder',
				  'TransmitedArrayName' => 'WkoId',
				  'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ACTOR,
									  UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_TRANSPORTEUR)));
	$grid->NewAction('Redirect',
            array('Caption' => _('Operations'),
                  'Title' => _('Display operations'),
				  'URL' => 'WorkOrderOpeTaskList.php?OtId=%d&amp;returnURL=WorkOrderList.php',
				  'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ACTOR,
                                      UserAccount::PROFILE_SUPERVISOR, UserAccount::PROFILE_ADMIN_VENTES,
                                      UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_TRANSPORTEUR,
									  UserAccount::PROFILE_GESTIONNAIRE_STOCK)));

    /*  Colonnes du grid  */
	$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%Id|formatserial6%'));
	$grid->NewColumn('FieldMapper', _('Name'),
            array('Macro' => '<a href="WorkOrder.php?OTid=%Id%&amp;action=edit">%Name%</a>'));

	$grid->NewColumn('FieldMapperWithTranslation', _('Closed'),
        array('Macro' => '%State%',
            'TranslationMap' => array(
                WorkOrder::WORK_ORDER_STATE_FULL => A_YES,
                WorkOrder::WORK_ORDER_STATE_TOFILL => A_NO
                )));
	$grid->NewColumn('FieldMapper', _('Actor'),
            array('Macro' => '%actor.name%'));
	$grid->NewColumn('FieldMapperWithTranslation', _('Validity begin'),
            array('Macro' => '%ValidityStart|formatdate%',
                  'TranslationMap' => array('00/00/00&nbsp;00:00' => _('N/A'))));
	$grid->NewColumn('FieldMapperWithTranslation', _('Validity end'),
            array('Macro' => '%ValidityEnd|formatdate%',
                  'TranslationMap' => array('00/00/00&nbsp;00:00' => _('N/A'))));
	$pageIndex = isset($_REQUEST['PageIndex'])?$_REQUEST['PageIndex']:false;
	$WorkOrderMapper = Mapper::singleton('WorkOrder');
    $Filter = isset($Filter)?$Filter:array();
	$Order = array('Id' => SORT_DESC);

	if ($grid->isPendingAction()){
		$Collection = false;
		$grid->setMapper($WorkOrderMapper);
		$dispatchResult = $grid->dispatchAction($Collection);
		if (Tools::isException($dispatchResult)){
			Template::errorDialog($dispatchResult->getMessage(),
                    'WorkOrderList.php'.'?'.$query);
		}
	}
	else{
	    $render = $grid->render($WorkOrderMapper, true, $Filter, $Order);
        Template::page(_('Work order management'), $pageContent . $render . '</form>');
	}
} // fin FormSubmitted
// On force le pagetitle au cas ou on vienne de ACOList, pour affecter
// un OT existant
else { // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page(_('Work order management'), $pageContent . '</form>');
}
?>
