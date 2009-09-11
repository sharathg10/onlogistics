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

$Auth = Auth::Singleton();
$Auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_GESTIONNAIRE_STOCK,
          UserAccount::PROFILE_SUPPLIER_CONSIGNE, UserAccount::PROFILE_OPERATOR));
$ProfileId = $Auth->getProfile();
// on recupere l'Actor relie a l'user connecte
$UserConnectedActorId = $Auth->getActorId();
//Database::connection()->debug = true;

// Traitement des infos du REQUEST/SESSION
$returnUrl = $_REQUEST['returnURL'] . '?stoId=' . $_REQUEST['stoId'];

if (empty($_REQUEST['StoreId'])) {
    Template::errorDialog(I_NEED_SELECT_ITEM, $returnUrl);
    exit;
}
// pour la gestion des droits
$Store = Object::load('Store', $_REQUEST['StoreId']);
$pageTitle = sprintf(_('Stock state: location > reference, store (%s)'),
                    $Store->getName());

if (($ProfileId == UserAccount::PROFILE_SUPPLIER_CONSIGNE &&
$UserConnectedActorId != $Store->getStockOwnerId()) ||
(in_array($ProfileId, array(UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR)) &&
$UserConnectedActorId != Tools::getValueFromMacro($Store, '%StorageSite.Owner.Id%'))) {
    Template::errorDialog(
        _("You are not allowed to view the locations of this store."),
        $returnUrl);
    exit;
}

// Attention: pour les champs du form, on garde les memes Name que dans
// StockStorageSiteList, pour conserver les saisies
$form = new SearchForm();
$form->addElement('text', 'BaseReference', _('Reference'),
    array('Value' => $_SESSION['BaseReference']), /* pour conserver les saisies */
    array('Disable'=>true));
$form->addElement('text', 'PdtName', _('Designation'),
    array('Value' => $_SESSION['PdtName']), array('Disable'=>true));

if (in_array($Auth->getProfile(), array(UserAccount::PROFILE_OWNER_CUSTOMER, UserAccount::PROFILE_SUPPLIER_CONSIGNE))) {
    $owners = SearchTools::createArrayIDFromCollection(
            'Actor', array('Id' => $Auth->getActorId()));
}else {
    $owners = SearchTools::createArrayIDFromCollection(
            'Actor', array('Active' => 1),_('Select an actor'));
}
$form->addElement('select', 'Owner', _('Product owner'), array($owners),
        array('Disable' => true));
$form->addElement('text', 'Location', _('Location'), array(),
        array('Disable' => true));

$form->addElement('hidden', 'returnURL', $_REQUEST['returnURL'],
            array(), array('Disable' => true));
$form->addElement('hidden', 'StoreId', $_REQUEST['StoreId'],
            array(), array('Disable' => true));
$form->addElement('hidden', 'stoId', $_REQUEST['stoId'],
            array(), array('Disable' => true));
// Pour la persistence des saisies
if (isset($_SESSION['SerialNumber'])) {
    $form->addElement('hidden', 'SerialNumber', $_SESSION['SerialNumber'],
            array(), array('Disable' => true));
}
// On doit forcer la valeur par defaut pour le select du owner
$defaultValues = $form->getDefaultValues();
$form->setDefaultValues(array_merge($defaultValues, array('Owner' => $_SESSION['Owner'])));

/*  Affichage du Grid  */
if (true === $form->displayGrid(1)) {
    // Necessaire, car pas d'appel a SearchForm::buildFilterComponentArray()
    SearchTools::inputDataInSession();
    $Collection = new Collection();
    $SQLRequest = request_ProductByLocationList($_REQUEST['StoreId']);
    $rs = executeSQL($SQLRequest);
    $i = 0;

    while (!$rs->EOF){
    	$pq = 'Location'.$i;
    	$$pq = Object::load('Location');
    	$$pq->setId($rs->fields['lpqLocation']);
    	$$pq->setName($rs->fields['locName']);

    	$Collection->setItem($$pq);
    	$rs->moveNext();
    	$i++;
    	unset($$pq);
    }
    if (!($Collection instanceof Collection)) {
    	die(E_ERROR_GENERIC);
    }

    $grid = new Grid();
    $grid->displayCancelFilter = false;
    $grid->withNoCheckBox = true;
    $grid->withNoSortableColumn = true;

    $grid->NewAction('Print', array());
    $grid->NewAction('Export', array('FileName'=>'StockByLocation'));
    $grid->NewAction('Cancel', array('Caption' => _('Back to list of stores'),
            'ReturnURL' => $returnUrl));

    $grid->NewColumn('FieldMapper', _('Locations'), array('Macro' => '%Name%'));
    $grid->NewColumn(
        'ProductByLocationList',
        '<table style="width: 100%;"><thead><tr>' .
            '<th style="width: 50%">'._('Reference').'</th>' .
            '<th style="width: 50%">'._('Quantity').'</th>' .
            '</tr></thead></table>');

    $form->setDisplayForm(false);  // Par defaut, on n'affiche pas le form de rech
    $form->setItemsCollection($Collection);
    $form->displayResult($grid, true, array(), array(), $pageTitle);
}

?>
