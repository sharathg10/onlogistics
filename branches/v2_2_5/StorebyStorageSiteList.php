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
require_once('Objects/Store.php');

define('STORE_LIST_ITEMPERPAGE', 50);

$Auth = Auth::Singleton();
$Auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_GESTIONNAIRE_STOCK,
          UserAccount::PROFILE_SUPPLIER_CONSIGNE, UserAccount::PROFILE_OPERATOR));

$ProfileId = $Auth->getProfile();
// on recupere l'Actor relie a l'user connecte
$UserConnectedActorId = $Auth->getActorId();
//Database::connection()->debug = true;

// Nettoyage session
unset($_SESSION['inStoreAE']);

$retURL = isset($_GET['returnURL'])?$_GET['returnURL']:'home.php';

if (empty($_REQUEST['stoId'])) {  // Traitement des infos du REQUEST/SESSION
    Template::errorDialog(_('Please select a site.'), $retURL);
    exit;
}
$FilterComponentArray = array();  // Tableau de filtres

// pour le title de la page et la gestion des droits
$StorageSite = Object::load('StorageSite', $_REQUEST['stoId']);
if (in_array($ProfileId, array(UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR))
    && $UserConnectedActorId != $StorageSite->getOwnerId()) {
    Template::errorDialog(
            _('You are not allowed to view the stores of this site.'),
            $_GET['returnURL']);
    exit;
}

$pageTitle = _('Site') . ' ('.$StorageSite->getName().')';

if ($ProfileId == UserAccount::PROFILE_SUPPLIER_CONSIGNE) {
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'StockOwner', '', 'Equals', $UserConnectedActorId, 1);
}
$FilterComponentArray[] = SearchTools::NewFilterComponent(
            'StorageSite', '', 'Equals', $_REQUEST['stoId'], 1);

// Pour chaque elemt du form, on force avec les saisies du form de StockStorageSiteList
if (isset($_SESSION['BaseReference']) && !empty($_SESSION['BaseReference']) && empty($_POST['BaseReference'])) {
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
        'BaseReference', 'Location().LocationProductQuantities().Product.BaseReference',
        'Like', $_SESSION['BaseReference'], 1, 'Store');
}
if (isset($_SESSION['PdtName']) && !empty($_SESSION['PdtName']) && empty($_POST['PdtName'])) {
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
        'PdtName', 'Location().LocationProductQuantities().Product.Name',
        'Like', $_SESSION['PdtName'], 1, 'Store');
}
if (isset($_SESSION['SerialNumber']) && !empty($_SESSION['SerialNumber']) && empty($_POST['SerialNumber'])) {
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
        'SerialNumber', 'Location().LocationConcreteProduct().ConcreteProduct.SerialNumber',
        'Like', $_SESSION['SerialNumber'], 1, 'Store');
}
if (isset($_SESSION['Owner']) && $_SESSION['Owner'] != '##' &&
(!isset($_POST['Owner']) || $_POST['Owner'] == '##')) {
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
        'Owner', 'Location().LocationProductQuantities().Product.Owner',
        'Like', $_SESSION['Owner'], 1, 'Store');
}

// Attention: pour les champs du form, on garde les memes Name que dans
// StockStorageSiteList, pour conserver les saisies
$form = new SearchForm('Store');
$array = (isset($_SESSION['BaseReference']))?
    array('Value' => $_SESSION['BaseReference']):array();
$form->addElement('text', 'BaseReference', _('Reference'),
    $array, /* pour conserver les saisies */
    array('Path' => 'Location().LocationProductQuantities().Product.BaseReference'));
$array = (isset($_SESSION['PdtName']))?
    array('Value' => $_SESSION['PdtName']):array();
$form->addElement('text', 'PdtName', _('Designation'),
    $array,
    array('Path' => 'Location().LocationProductQuantities().Product.Name'));
$array = (isset($_SESSION['SerialNumber']))?
    array('Value' => $_SESSION['SerialNumber']):array();
$form->addElement('text', 'SerialNumber', _('SN / Lot'),
    $array,
    array('Path' => 'Location().LocationConcreteProduct().ConcreteProduct.SerialNumber'));

if (in_array($Auth->getProfile(), array(UserAccount::PROFILE_OWNER_CUSTOMER, UserAccount::PROFILE_SUPPLIER_CONSIGNE))) {
    $owners = SearchTools::createArrayIDFromCollection(
            'Actor', array('Id' => $Auth->getActorId()));
}else {
    $owners = SearchTools::createArrayIDFromCollection(
            'Actor', array('Active' => 1),_('Select an actor'));
}
$form->addElement('select', 'Owner', _('Product owner'), array($owners),
    array('Path' => 'Location().LocationProductQuantities().Product.Owner'));

$form->addElement('hidden', 'stoId', $_REQUEST['stoId'], array(), array('Disable'=>true));

// On doit forcer la valeur par defaut pour le select du owner
if (isset($_SESSION['Owner'])) {
    $defaultValues = $form->getDefaultValues();
    $form->setDefaultValues(array_merge($defaultValues, array('Owner' => $_SESSION['Owner'])));
}

/*  Affichage du Grid  */
if (true === $form->displayGrid(1)) {
    $FilterComponentArray = array_merge(
            $form->buildFilterComponentArray(), $FilterComponentArray);
	$filter = SearchTools::filterAssembler($FilterComponentArray);

    $grid = new Grid();
    $grid->itemPerPage = STORE_LIST_ITEMPERPAGE;
    $grid->displayCancelFilter = false;

    $grid->NewAction('AddEdit',
    	    array('Action' => 'Add',
    			  'EntityType' => 'Store',
    			  'Query' => 'returnURL=StorebyStorageSiteList.php&amp;stoId='
    			  			. $_REQUEST['stoId'].'&amp;StoreId=0',
    	          'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
    	            				  UserAccount::PROFILE_GESTIONNAIRE_STOCK)
    	    )
    	);

    $grid->NewAction('Delete',
        array(
            'TransmitedArrayName' => 'StoreId',
            'EntityType' => 'Store',
            'ReturnURL' => basename($_SERVER['PHP_SELF']).'?stoId='.$_REQUEST['stoId'],
            'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_GESTIONNAIRE_STOCK),
            'Query' => 'returnURL=StorebyStorageSiteList.php?stoId=' . $_REQUEST['stoId']
        )
    );


    $grid->NewAction('Redirect',
        array(
            'Caption' => A_ACTIVATE,
            'Title' => _('Activate store'),
            'TransmitedArrayName' => 'StoreId',
            'URL' => 'StoreActiveDesactive.php?action=reac&returnURL='
                . 'StorebyStorageSiteList.php?stoId=' . $_REQUEST['stoId']
        )
    );

    $grid->NewAction('Redirect',
        array(
            'Caption' => _('Deactivate stores'),
            'Title' => _('Deactivate store and its locations'),
            'TransmitedArrayName' => 'StoreId',
            'URL' => 'StoreActiveDesactive.php?action=desac&returnURL='
                . 'StorebyStorageSiteList.php?stoId=' . $_REQUEST['stoId']
        )
    );

    $grid->NewAction('Redirect',
        array(
            'Caption' => _('Stock by location'),
            'Title' => _('Stock sorted by location'),
            'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR,
                UserAccount::PROFILE_AERO_OPERATOR, UserAccount::PROFILE_SUPPLIER_CONSIGNE),
            'URL' => 'ProductByLocationList.php?StoreId=%d&returnURL=' .
                'StorebyStorageSiteList.php&amp;stoId='.$_REQUEST['stoId']
        )
    );

    $grid->NewAction('Redirect',
        array(
            'Caption' => _('Stock by reference'),
            'Title' => _('Stock ordered by reference'),
            'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR,
                UserAccount::PROFILE_AERO_OPERATOR, UserAccount::PROFILE_SUPPLIER_CONSIGNE),
            'URL' => 'LocationByProductList.php?StoreId=%d&returnURL=' .
                    'StorebyStorageSiteList.php&amp;stoId='.$_REQUEST['stoId']
        )
    );

    $grid->NewAction('Redirect',
        array(
            'Caption' => _('Show SN/Lot'),
            'Title' => _('SN/Lot in stock by location'),
            'Profiles' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
                    UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR,
                    UserAccount::PROFILE_AERO_OPERATOR, UserAccount::PROFILE_SUPPLIER_CONSIGNE),
            'URL' => 'DetailedLocationByProductList.php?StoreId=%d&returnURL=' .
                    'StorebyStorageSiteList.php&amp;stoId='.$_REQUEST['stoId']
        )
    );

    $grid->NewAction('Cancel',
        array(
            'Caption' => _('Back to list of sites'),
            'ReturnURL' => 'StockStorageSiteList.php'
        )
    );

    // TODO: faire un gridColumn adapté pour gestion des droits
    $editMacro = !in_array($ProfileId, array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
            UserAccount::PROFILE_GESTIONNAIRE_STOCK))?
            '%Name%':'<a href="StoreAddEdit.php?StoreId=%Id%&amp;returnURL'
            .'=StorebyStorageSiteList.php&amp;stoId='. $_REQUEST['stoId'].'">%Name%</a>';
    $grid->NewColumn('FieldMapper', _('Store'), array('Macro' => $editMacro,
            'SortField' => 'Name'));
    $grid->NewColumn('FieldMapper', _('Owner'),
        array('Macro' => '%StockOwner.Name%', 'Sortable' => false));
    $grid->NewColumn('FieldMapperWithTranslation', _('Active'),
            array('Macro' => '%Activated%',
                  'TranslationMap' => array(0=>A_NO, 1=>A_YES)));

    $order = array('Name' => SORT_ASC);
    $form->setDisplayForm(false);  // Par defaut, on n'affiche pas le form de rech
    $form->displayResult($grid, true, $filter, $order, $pageTitle);
}

?>