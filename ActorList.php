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
require_once('Objects/Actor.inc.php');

$auth = Auth::Singleton();
$auth->checkProfiles();
// Database::connection()->debug = true;
$FilterComponentArray = array();  // Tableau de filtres
$FilterComponentArray[] = SearchTools::NewFilterComponent('Generic', '', 'NotEquals', 1, 1);

$zoneFilter = array();  // Tableau de filtres
$zoneFilter[] = SearchTools::NewFilterComponent(
    'DeliveryZone', 'Site().CountryCity.Zone.Name', 'Like', '', 0, 'Actor');
$zoneFilter[] = SearchTools::NewFilterComponent(
    'DeliveryZone', 'Site().Zone.Name', 'Like', '', 0, 'Actor');
//  Ceci est aussi un FilterComponent
$FilterComponentArray[] = SearchTools::FilterAssembler($zoneFilter, 'Or');

/**
 * Filtres sur l'user connecté
 **/
$profileID = $auth->getProfile();
$actorID   = $auth->getActorId();
$userID    = $auth->getUserId();

if ($profileID == UserAccount::PROFILE_COMMERCIAL) {
    $cpn = SearchTools::NewFilterComponent('Commercial', '', 'Equals', $userID, 1);
	$FilterComponentArray[] = $cpn;
}else if ($profileID == UserAccount::PROFILE_AERO_CUSTOMER) {
    $cpn = SearchTools::NewFilterComponent('Id', '', 'Equals', $actorID, 1);
    $FilterComponentArray[] = $cpn;
}else if ($profileID == UserAccount::PROFILE_AERO_INSTRUCTOR) {
    $cpn = new FilterComponent();
    $cpn->operator = FilterComponent::OPERATOR_OR;
    $cpn->setItem(SearchTools::NewFilterComponent('Id', '', 'Equals', $actorID, 1));
    $cpn->setItem(SearchTools::NewFilterComponent('Instructor', '', 'Equals', $actorID, 1));
    $FilterComponentArray[] = $cpn;
}else if ($profileID == UserAccount::PROFILE_DIR_COMMERCIAL) {
    $cpn = SearchTools::NewFilterComponent('ClassName', '', 'In', array('Customer', 'AeroCustomer'), 1);
    $FilterComponentArray[] = $cpn;
}

// Profiles ayant des droits max sur cet ecran:
$fullRightsProfiles = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, 
                            UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES,
                            UserAccount::PROFILE_AERO_OPERATOR, UserAccount::PROFILE_DIR_COMMERCIAL,
                            UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_AERO_CUSTOMER,
                            UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_GED_PROJECT_MANAGER);
$withFullRights = in_array($profileID, $fullRightsProfiles);
$withCommRights = in_array($profileID, array(UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_DIR_COMMERCIAL));

/*  Contruction du formulaire de recherche */
$form = new SearchForm('Actor');

$form->customizationEnabled = true;  // les criteres de recherches sont desactivables
$form->hiddenCriteriaByDefault = array('Category', 'Code', 'TvaRate', 'Siret');

$form->addElement('text', 'Name', _('Name'));
$form->addElement('text', 'CityName', _('Main site city'), array(),
        array('Path' => 'MainSite.CountryCity.CityName.Name'));
$form->addElement('text', 'DeliveryZone', _('Delivery zone'), array(),
        array('Disable' => true));
$form->addElement('text', 'ZipCode', _('Main site zip code'),
        array('size="8" maxlength="15"'),
        array('Path' => 'MainSite.CountryCity.Zip.Code'));
$form->addElement('text', 'Commercial', _('Salesman'), array(),
        array('Path' => 'Commercial.Identity'));
$form->addBlankElement();
$form->addElement('select', 'ClassName', _('Type'),
        array($classNameDict, 'multiple size="3"'),
        array('Operator' => 'Like'));
$catArray = Object::loadCollection('Category', array(), array('Name'=>SORT_ASC))->toArray();
$catArray = array('##' => _('All')) + $catArray;
$form->addElement('select', 'Category', _('Category'),
        array($catArray, 'multiple size="3"'),
        array('Operator' => 'Like'));

$form->addElement('checkbox', 'Active', _('Active'));
$form->addElement('checkbox', 'Active_No', _('Inactive'), array(),
        array('Path' => 'Active', 'Operator' => 'NotEquals'));

$form->addElement('text', 'Code', _('Code'));
$form->addElement('text', 'Siret', _('Siret'));
$tvaArray = SearchTools::createArrayIDFromCollection('TVA',
        array(), MSG_SELECT_AN_ELEMENT);
$form->addElement('select', 'TvaRate', _('TVA'), array($tvaArray), array('Path' => 'TVA.Rate'));

// Filtre resultant du checkbox suivant pas gere par SearchForm,
// mais par une creation de filtre particuliere:
$form->addElement('checkbox', 'Incur', _('Outstanding debts maximum amount exceeded'), '',
        array('Disable' => true));

if ($withCommRights) {
    $form->addBlankElement();
    $priorityLevelArray['##'] = _('Select one or more orders');
    for($i = 1; $i < 12; $i++) {
        $priorityLevelArray[$i] = $i;
    }
    $form->addElement('select', 'PriorityLevel', _('Priority order'),
            array($priorityLevelArray, 'multiple size="3"'),
            array('Path' => 'CustomerProperties.PriorityLevel'));
    $custSituationArray = SearchTools::createArrayIDFromCollection('CustomerSituation', array(),
            _('Select one or more situations'));
    $form->addElement('select', 'Situation', _('Situation'),
            array($custSituationArray, 'multiple size="3"'),
            array('Path' => 'CustomerProperties.Situation'));
    $form->addBlankElement();
    $form->addElement('checkbox', 'DateOrder1', _('Next visit date'),
        array('', 'onClick="$(\\\'Date1\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'));
    $form->addDate2DateElement(
            array('Name' => 'StartDate', 'Path' => 'Action().WishedDate'),
			array('Name' => 'EndDate', 'Path' => 'Action().WishedDate'),
			array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
				  'StartDate' => array('Y' => date('Y'))));
}

$form->setDefaultValues(array('Active' => 1));  // Valeur par defaut (checked)

$form->addAction(array('URL' => 'ActorAddEdit.php',
					   'Profiles' => $fullRightsProfiles));
$form->addAction(
    array(
        'Caption'=>_('Manage business events'),
        'URL' => 'ActionList.php',
        'Profiles' => array(UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_DIR_COMMERCIAL,
                            UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
    )
);
/**  Cleanage des infos de session pour actoraddedit  **/
require_once('ActorAddEditTools.php');
cleanActorAddEditSessionData();

/*  Affichage du Grid: Condition resultant aussi qu'on provienne de LicenceList.php */
if (true === $form->displayGrid()
	|| isset($_SESSION['LastEntitySearched']) && $_SESSION['LastEntitySearched'] == 'LicenceList') {

	// Evite les interaction entre $_POST et $_SESSION
	SearchTools::cleanYesNoDataSession('Active', 'Active_No');
	SearchTools::cleanCheckBoxDataSession(array('Incur', 'DateOrder1'));

	/*  Filtre sur l'encours depasse : but: arriver a la requete SQL suivante:
	*   SELECT actName FROM Actor A, SupplierCustomer SC
	* 	WHERE SC.spcCustomer=A.actId AND SC.spcSupplier=$UserConnectedActorId AND SC.spcUpdateIncur > SC.spcMaxIncur;  */
	$UserConnectedActorId = $auth->getActorId();
	if (SearchTools::RequestOrSessionExist('Incur', 1)) {
        $FilterComponentArray[] = new FilterComponent(
		    new FilterRule('Id', FilterRule::OPERATOR_EQUALS, '!SC._Customer'),
		    new FilterRule('!SC._Supplier', FilterRule::OPERATOR_EQUALS, $UserConnectedActorId),
            new FilterRule('!SC._UpdateIncur', FilterRule::OPERATOR_GREATER_THAN, '!SC._MaxIncur'),
            FilterComponent::OPERATOR_AND
        );
        $tables = array('SC'=>'SupplierCustomer');
    } else {
        $tables = array();
    }
	// On prend en compte seulement les Actions A FAIRE
    if (SearchTools::requestOrSessionExist('DateOrder1', 1)) {
	    $FilterComponentArray[] = SearchTools::NewFilterComponent(
                'ActionState', 'Action().State', 'Equals', Action::ACTION_STATE_TODO, 1, 'Actor');
	}
    /*  Construction du filtre  */
    $FilterComponentArray = array_merge($FilterComponentArray, $form->buildFilterComponentArray());
    $Filter = SearchTools::filterAssembler($FilterComponentArray, 'AND');
    $Filter->tables = $tables;

    $grid = new Grid();
    // Pour ne pas avoir d'erreur js liee a customisation searchForms
    $grid->javascriptFormOwnerName = 'ActorList';
    define('ACTORLIST_ITEMPERPAGE', 150);
    $grid->itemPerPage = ACTORLIST_ITEMPERPAGE;

    $grid->NewAction('AddEdit', array('Action' => 'Add',
									  'EntityType' => 'Actor',
									  'Profiles' => $fullRightsProfiles));

    $grid->NewAction('Delete',
            array('TransmitedArrayName' => 'actId',
                  'EntityType' => 'Actor',
                  'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                        UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES,
                        UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_DIR_COMMERCIAL)
                  )
    );

    $grid->NewAction('Redirect',
            array('Caption' => _('Carriage'),
	              'Title' => _('Manage carriage'),
                  'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                        UserAccount::PROFILE_TRANSPORTEUR, UserAccount::PROFILE_DIR_COMMERCIAL),
	              'URL' => 'ChainTaskList.php?actID=%d')
    );
    // Contexte metier: seulement si aero
    $tradeContext = Preferences::get('TradeContext');
    if (!is_null($tradeContext) && in_array('aero', $tradeContext)) {
        $grid->NewAction('Redirect',
            array(
                'Caption' => _('Licenses'),
                'Title' => _('Licenses management'),
                'URL' => 'LicenceList.php?actorID=%d',
                'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_ADMIN_VENTES,
                                    UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_AERO_CUSTOMER,
                                    UserAccount::PROFILE_DIR_COMMERCIAL)
            )
        );
    }

	$grid->NewAction('Redirect',
        array(
            'Caption' => _('Add event'),
            'Title' => _('Add business event'),
            'Profiles' => array(UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_DIR_COMMERCIAL,
                                UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
            'URL' => 'ActionAddEdit.php?actorID=%d')
    );
    $grid->NewAction('Redirect',
        array(
            'Caption' => _('Manage events'),
            'Title' => _('Manage business events'),
            'Profiles' => array(UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_DIR_COMMERCIAL,
                                UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
            'URL' => 'ActionList.php?actID=%d')
    );
    $grid->NewAction('Redirect',
            array('Caption' => _('Terms'),
                  'Title' => _('Manage supplier delivery within'),
				  'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
		          'URL' => 'SupplierDelayStock.php?actId=%d'));

	$grid->NewAction('Print');
	$grid->NewAction('Export', array('FileName' => 'Acteurs'));


    $grid->NewColumn('FieldMapper', _('Name'),
            array('Macro' => '<a href="ActorAddEdit.php?actId=%Id%">%Name%</a>',
                  'Enabled' => ($withFullRights),
			      'SortField' => 'Name'));
    $grid->NewColumn('FieldMapper', _('Name'), array('Macro' => '%Name%',
                  'Enabled' => (!$withFullRights)));
	$grid->NewColumn('FieldMapperWithTranslation', _('Salesman'),
            array('Macro' => '%Commercial.Identity%',
			      'TranslationMap' => array(0 => ''),
			      'Sortable' => false));
    $grid->NewColumn('ActorListIncur', _('Allowed outstanding debts'),
            array('Method'=>'getMaxIncur', 'Sortable'=>false));
    $grid->NewColumn('ActorListIncur', _('Current outstanding debts'),
            array('Method'=>'getUpdateIncur', 'Sortable'=>false));
    $grid->NewColumn('FieldMapper', _('Address'),
            array('Macro' => '%MainSite.FormatAddressInfos|default%', 'Sortable' => false));
    // Colonnes pour les commerciaux
    $grid->NewColumn('FieldMapper', _('Situation'),
            array('Macro' => '%CustomerProperties.Situation.Name|default%',
                  'Enabled' => ($withCommRights)));
    $grid->NewColumn('ActionWishedDate', _('Next visit'),
            array('Enabled' => ($withCommRights)));
    $grid->NewColumn('ActionWishedDate', _('Visit as soon as'),
            array('Limit' => 'begin', 'Enabled' => ($withCommRights)));
    $grid->NewColumn('ActionWishedDate', _('Deadline'),
            array('Limit' => 'end', 'Enabled' => ($withCommRights)));
    $grid->NewColumn('FieldMapper', _('Priority'),
            array('Macro' => '%CustomerProperties.PriorityLevel%',
                  'Enabled' => ($withCommRights)));

	$Order = array('Name' => SORT_ASC);

	$form->displayResult($grid, true, $Filter, $Order);
} // fin affichage Grid

else {  // on n'affiche que le formulaire de recherche, pas le Grid
    Template::page('', $form->render() . '</form>');
}
?>
