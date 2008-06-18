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
require_once('Objects/Product.php');

$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
          UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_SUPPLIER_CONSIGNE,
          UserAccount::PROFILE_OPERATOR, UserAccount::PROFILE_AERO_OPERATOR));

$pfID = $auth->getProfile();
$connectedActorId = $auth->getActorId();

// variables
define('GRID_ITEMPERPAGE', 300);
define('PAGE_TITLE', _('Details of SN/Lot by location'));
$retURL = isset($_REQUEST['returnURL'])?
    $_REQUEST['returnURL']:'StockStorageSiteList.php';

if (!isset($_REQUEST['stoId'])) {
    Template::errorDialog(E_ERROR_SESSION, $retURL);
    exit;
}
$retURL .= '?stoId='.$_REQUEST['stoId'];

if (!isset($_REQUEST['StoreId'])) {
    Template::errorDialog(_('Please select a store.'), $retURL);
    exit;
}
$store = Object::load('Store', $_REQUEST['StoreId']);
if (Tools::isEmptyObject($store)) {
    Template::errorDialog(E_ERROR_IN_EXEC, $retURL);
    exit;
}
// authentification
$lowProfiles = array(UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR,
                     UserAccount::PROFILE_AERO_OPERATOR);
if (($pfID == UserAccount::PROFILE_SUPPLIER_CONSIGNE && $connectedActorId != $store->getStockOwnerId())
    || (in_array($pfID, $lowProfiles) &&
    $connectedActorId != Tools::getValueFromMacro($store, '%StorageSite.Owner.Id%'))) {
        Template::errorDialog(
            _('You are not allowed to view the locations of this store.'),
            $retURL);
        exit;
}

// construction du moteur de recherche
$form = new SearchForm('LocationConcreteProduct');
$form->buildHiddenField(
    array(
        'StoreId'   => $_REQUEST['StoreId'],
        'stoId'     => $_REQUEST['stoId'],
        'returnURL' => $_REQUEST['returnURL']
    )
);
// Attention: pour les champs du form, on garde les memes Name que dans
// StockStorageSiteList, pour conserver les saisies
$form->addElement('text', 'BaseReference', _('Reference'), array(),
    	array('Path' => 'ConcreteProduct.Product.BaseReference'));
$form->addElement('text', 'PdtName', _('Designation'), array(),
    	array('Path' => 'ConcreteProduct.Product.Name'));
$form->addElement('text', 'SerialNumber', _('SN/Lot'), array(),
    	array('Path' => 'ConcreteProduct.SerialNumber'));
$form->addElement('text', 'LocationName', _('Location'), array(),
    	array('Path' => 'Location.Name'));
$form->addElement('text', 'CPOwner', _('SN/Lot owner'), array(),
    	array('Path' => 'ConcreteProduct.Owner.Name'));
$form->addElement('checkbox', 'DateOrder1', _('Filter by end of life date'),
        array('', 'onClick="$(\\\'Date1\\\').style.display'
                . '=this.checked?\\\'block\\\':\\\'none\\\';"'));
$form->addDate2DateElement(
        array('Name' => 'StartDate', 'Path' => 'ConcreteProduct.EndOfLifeDate'),
		array('Name' => 'EndDate', 'Path' => 'ConcreteProduct.EndOfLifeDate'),
		array('EndDate' => array('d' => date('d'), 'm' => date('m'), 'Y' => date('Y')),
			  'StartDate' => array('Y' => date('Y')))
		);

if (in_array($pfID, array(UserAccount::PROFILE_OWNER_CUSTOMER, UserAccount::PROFILE_SUPPLIER_CONSIGNE))) {
    $owners = SearchTools::createArrayIDFromCollection(
            'Actor', array('Id' => $connectedActorId));
}else {
    $owners = SearchTools::createArrayIDFromCollection(
            'Actor', array('Active' => 1), _('Select an actor'));
}
$form->addElement('select', 'Owner', _('Product Owner'), array($owners),
        array('Path' => 'ConcreteProduct.Product.Owner.Name'));

// On force les valeurs par defaut
//$defaultValues = SearchTools::dataInSessionToDisplay();
//$form->setDefaultValues($defaultValues);

if (true === $form->displayGrid()) {
    // constructeion de l'url de retour pour les actions
    $url = basename($_SERVER['PHP_SELF']) . '?returnURL=%s&amp;stoId=%s&amp;StoreId=%s';
    $url = sprintf($url, $_REQUEST['returnURL'], $_REQUEST['stoId'], $_REQUEST['StoreId']);

    $grid = new Grid();
    $grid->displayCancelFilter = false;
    $grid->withNoCheckBox = true;
    $grid->itemPerPage = GRID_ITEMPERPAGE;
    // colonnes
	$grid->NewColumn('FieldMapper', _('Reference'),
            array('Macro' => '%ConcreteProduct.Product.BaseReference%'));
	$grid->NewColumn('FieldMapperWithTranslationExpression',
        _('SN (red) or Lot (blue)'),
        array(
            'Macro' =>'%ConcreteProduct.Product.TracingMode%',
            'TranslationMap' => array(
        	    0 => '%ConcreteProduct.SerialNumber%',
                Product::TRACINGMODE_SN  =>
                    '<div style="color:red">%ConcreteProduct.SerialNumber%</div>',
                Product::TRACINGMODE_LOT =>
                    '<div style="color:blue">%ConcreteProduct.SerialNumber%</div>'
        	)
        )
    );
	$grid->NewColumn('FieldMapper', _('Location'), array('Macro' => '%Location.Name%'));
	$grid->NewColumn('FieldMapper', _('Quantity'),
            array('Macro' => '%Quantity|formatnumber@3@1%'));
	$grid->NewColumn('FieldMapper', _('Owner'),
            array('Macro' => '%ConcreteProduct.Owner.Name|default%'));
	$grid->NewColumn('FieldMapperWithTranslation', _('End of life date'),
            array('Macro' => '%ConcreteProduct.EndOfLifeDate|formatdate@DATE_SHORT%',
                  'TranslationMap' => array('00/00/00' => 'N/A')));

    // actions
    $grid->NewAction('Print', array());
	$grid->NewAction('Export', array('FileName'=>'SNLot', 'ReturnURL'=>$url));
    $grid->NewAction('Cancel',
        array('Caption' => _('Back to list of stores'), 'ReturnURL' => $retURL)
    );
    // filtre et ordre
    $filters = array();
    /*$filters[] = SearchTools::NewFilterComponent('TracingMode',
        'ConcreteProduct.Product.TracingMode', 'GreaterThan', '0', 1);*/
    $filters[] = SearchTools::NewFilterComponent('StoreId', 'Location.Store',
            'Equals', $_REQUEST['StoreId'], 1);
    $filters = array_merge($filters, $form->buildFilterComponentArray());
    $filter = SearchTools::filterAssembler($filters);
	$order = array('ConcreteProduct.Product.BaseReference' => SORT_ASC);

	// affichage
	$form->displayResult($grid, true, $filter, $order, PAGE_TITLE);
}
else {
    Template::page(PAGE_TITLE, $form->render() . '</form>');
}
?>