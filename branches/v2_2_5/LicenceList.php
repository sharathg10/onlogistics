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
require_once('Objects/Licence.php');

$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_ADMIN_VENTES,
	          			   UserAccount::PROFILE_AERO_INSTRUCTOR, UserAccount::PROFILE_AERO_CUSTOMER,
                           UserAccount::PROFILE_DIR_COMMERCIAL));
/**
 * Messages d'erreur
 **/
define('E_NO_ACTOR', sprintf(E_MSG_MUST_SELECT_A, _('actor')));
define('E_BAD_ACTOR', sprintf(E_MSG_MUST_SELECT_A, _('aircraft actor')));

/**
 * Cleanage de session
 **/
$sname = SearchTools::getGridItemsSessionName();
unset($_SESSION[$sname], $_SESSION['lic']);

/**
 * Pour prolonger la session du formulaire de recherche d'actorlist
 **/
SearchTools::prolongDataInSession();

/**
 * Variables passées par l'action du grid actorlist
 **/
$retURL = 'ActorList.php';
if (!isset($_REQUEST['actorID'])) {
    Template::errorDialog(E_NO_ACTOR, $retURL);
    exit;
}

/**
 * On charge l'acteur
 **/
$ActorMapper = Mapper::singleton('Actor');
$actor = $ActorMapper->load(array('Id'=>$_REQUEST['actorID']));

/**
 * On vérifie que c'est bien un acteur aeronautique
 **/
if (!($actor instanceof AeroActor)) {
    Template::errorDialog(E_BAD_ACTOR, $retURL);
    exit;
}
$title = sprintf(_('List of licenses for actor "%s".'), $actor->getName());
$licenceIds = $actor->getLicenceCollectionIds();

/*  Contruction du formulaire de recherche */
$form = new SearchForm('Licence');
// On fait suivre ce qui passe dans l'URL
$form->buildHiddenField(array('actorID' => $_REQUEST['actorID']));

$LicenceTypeArray = SearchTools::createArrayIDFromCollection('LicenceType', array(),
    _('Select one or more types'));
$form->addElement('select', 'LicenceType', _('Type'),
        array($LicenceTypeArray, 'multiple size="3"'));
// Nom de chps != Path, car interraction avec ActorList
$form->addElement('text', 'LicenceName', _('Name'), array(), array('Path' => 'Name'));
$form->addBlankElement();
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
    array('', 'onclick="$(\\\'Date1\\\').style.display=this.checked?\\\'block'
        . '\\\':\\\'none\\\';"'));
$form->addDate2DateElement(
        array('Name'   => 'StartDate', 'Path' => 'EndDate'),
		array('Name'   => 'EndDate',  'Path' => 'EndDate'),
		array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
			  'StartDate' => array('Y' => date('Y')))
);

$form->addAction(array('URL' => 'LicenceAddEdit.php?retURL='
        . $_SERVER['PHP_SELF']. '&amp;actorID=' . $actor->getId()));

/*  Affichage du Grid  */
if (true === $form->DisplayGrid()) {
	// Filtre par defaut: les licences de l'Actor selectionne
	$FilterComponentArray = array(); // Tableau de filtres
	$FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Id', '', 'In', $licenceIds, 1);

	$FilterComponentArray = array_merge($FilterComponentArray,
            $form->buildFilterComponentArray());
	$filter = SearchTools::filterAssembler($FilterComponentArray);

	$grid = new Grid();

	$grid->NewColumn('FieldMapper',
                   _('Name'),
                   array('Macro' =>'<a href="LicenceAddEdit.php?flcID=%Id%&actorID='
                   . $actor->getId().'&retURL='.$_SERVER['PHP_SELF'].'">%Name%</a>')
    );
	$grid->NewColumn('FieldMapper', _('Type'), array('Macro' =>'%Licencetype.Name%',
	       'Sortable' => false));
	$grid->NewColumn('FieldMapper', _('Number'), array('Macro' =>'%Number%'));
	$grid->NewColumn('FieldMapper', _('Validity'),
            array('Macro' =>_('from') . ' %BeginDate|formatdate@DATE_SHORT% ' .
	                    	_('at') . ' %EndDate|formatdate@DATE_SHORT%'));
	$grid->NewAction(
		'AddEdit',
		array(
            'Action'=>'Add',
            'EntityType' => 'Licence',
            'Query'=>'retURL=' . $_SERVER['PHP_SELF'] . '&amp;actorID=' . $actor->getId()
		)
	);
	$grid->NewAction(
		'Delete',
		array(
			'TransmitedArrayName'=>'flcIDs',
			'EntityType'=>'Licence',
	        'Query'=>sprintf('retURL=%s&amp;actorID=%s', $_SERVER['PHP_SELF'],
	            $actor->getId())
		)
	);
	$grid->NewAction('Redirect', array('Caption' => 'Annuler', 'URL' => $retURL));

	$order = array('Number' => SORT_ASC);
	$form->displayResult($grid, true, $filter, $order, $title);
}

else {   //  on n'affiche que le formulaire de recherche, pas le Grid
    Template::page($title, $form->render() . '</form>');
}

?>