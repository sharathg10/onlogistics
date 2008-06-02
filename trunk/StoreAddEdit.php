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

$Auth = Auth::Singleton();
$Auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_GESTIONNAIRE_STOCK));
$ProfileId = $Auth->getProfile();
$UserConnectedActorId = $Auth->getActorId();   // L'Actor relie a l'user connecte
// Blindage
if (!isset($_REQUEST['stoId'])) {
    Template::errorDialog(E_ERROR_IN_EXEC, 'StockStorageSiteList.php');
	exit;
}
if (!isset($_REQUEST['StoreId'])) {
    Template::errorDialog(_('No store selected.'),
            'StorebyStorageSiteList.php?stoId='.$_REQUEST['stoId']);
	exit;
}

$Store = Object::load('Store', $_REQUEST['StoreId']);
if ($ProfileId == UserAccount::PROFILE_GESTIONNAIRE_STOCK && $_REQUEST['StoreId'] > 0
        && ($UserConnectedActorId != Tools::getValueFromMacro($Store, '%StorageSite.Owner.Id%'))) {
	// pour etre sur que le user connecte a bien les droits d'editer ce Store
    Template::errorDialog(_('You are not allowed to modify this store.'),
            'StorebyStorageSiteList.php?stoId='.$_REQUEST['stoId']);
	exit;
}


$Smarty = new Template();
$StorageSite = Object::load('StorageSite', $_REQUEST['stoId']);
$Smarty->assign('StorageSiteName', $StorageSite->getName());
$action = _('Update');

// Si demande d'ajout de Store, le template est modifie
if ($_REQUEST['StoreId'] == 0 && !isset($_REQUEST['Ok'])) {
    $Smarty->assign('AddStore', 1);
	$action = _('Create');
}
$Smarty->assign('action', $action . _(' of '));
$StorageSite = Object::load('StorageSite', $_REQUEST['stoId']);
$LocationMapper = Mapper::singleton('Location');

$query = 'returnURL=StorebyStorageSiteList.php&amp;stoId=' . $_REQUEST['stoId']; // var pour url

//  Si envoi du formulaire: info concernant seulement le Store
// le hidden inStoreAE sert a dectecter si on ne vient pas de StorebyStorageSiteList
if (SearchTools::requestOrSessionExist('inStoreAE') &&
(SearchTools::requestOrSessionExist('actionId', '0')
|| SearchTools::requestOrSessionExist('actionId', '2')
|| SearchTools::requestOrSessionExist('Ok', 'Ok'))) {
    $errorURL = 'StoreAddEdit.php?'.$query . '&amp;StoreId='. $Store->getId();
	if (empty($_REQUEST['StoreName']) || $_REQUEST['StockOwner'] == '##') {
	    Template::errorDialog(
            _('Please select an owner and provide a store name.'),
            $errorURL);
		exit;
	}

	if ($_REQUEST['StoreName'] != $Store->getName()) {
		if (in_array($_REQUEST['StoreName'], $StorageSite->getStoreNameArray())) {
		    Template::errorDialog(
                _('A store with the name provided already exists in this site.'),
                $errorURL);
			exit;
		}
	    $Store->setName($_REQUEST['StoreName']);
	}
	if ($_REQUEST['StockOwner'] != $Store->getStockOwnerId()) {
		$Owner = Object::load('Actor', $_REQUEST['StockOwner']);
	    $Store->setStockOwner($Owner);
	}
	$Store->setStorageSite($StorageSite);
	$Store->setActivated($_REQUEST['Activated'], true);  // 2eme param: update les Locations associes aussi
    saveInstance($Store, $errorURL);
}

$LocationCollection = $LocationMapper->loadCollection(
        array('Store' => $Store->getId()), array('Name' => SORT_ASC));


/*  Si envoi du formulaire: info concernant seulement les Location  */
if (SearchTools::requestOrSessionExist('inStoreAE') &&
(SearchTools::requestOrSessionExist('actionId', '0')
|| SearchTools::requestOrSessionExist('actionId', '2'))) {
	$index = false;
	// si un Location d'Id 0
	if (isset($_REQUEST['LocationId']) && in_array(0, $_REQUEST['LocationId'])) {
	    $index = array_search(0, $_REQUEST['LocationId']); // indice nouveau Location
	    // si un nom a ete saisi
		if (!(false === $index) && !empty($_REQUEST['LocationName'][$index])) {
			if (in_array($_REQUEST['LocationName'][$index], $Store->getLocationNameArray())) {
				$queryAdd = '&amp;StoreName=' . $_REQUEST['StoreName'] . '&amp;StockOwner='
                        . $_REQUEST['StockOwner'] . '&amp;Activated=' . $_REQUEST['Activated'];
			    Template::errorDialog(
                        _('A location with the name provided already exists in this store.'),
                        'StoreAddEdit.php?actionId=0&amp;'.$query . '&amp;StoreId='
                        . $Store->getId().$queryAdd);
				exit;
			}
			$Location = Object::load('Location');
			$Location->setName($_REQUEST['LocationName'][$index]);
			$Location->setStore($Store);
			// Si le store est desactivé, tous ses Location le sont aussi
			$Location->setActivated($Store->getActivated());
            saveInstance($Location, $errorURL.'&amp;actionId=0');
			$LocationCollection->setItem($Location);
		}
	}
	unset($Location);
	if (isset($_REQUEST['LocationId'])) {
	    for ($i=0;$i<count($_REQUEST['LocationId']);$i++) {
			if (!($index === false) && $i == $index) {
			    continue;
			}
			$Location = Object::load('Location', $_REQUEST['LocationId'][$i]);
			if (Tools::isEmptyObject($Location) && $_REQUEST['LocationId'][$i] != 0) {
			    Template::errorDialog(
                        _('One of the location was not found in the database.'),
                        'StoreAddEdit.php?' . $query . '&amp;StoreId='. $Store->getId());
				exit;
			}
			if ($Location->getName() == $_REQUEST['LocationName'][$i]
                    || $_REQUEST['LocationName'][$i] == '') {
			    continue;
			}
			if (in_array($_REQUEST['LocationName'][$i], $Store->getLocationNameArray())) {
				Template::errorDialog(
                        sprintf(_('A location with the name provided "%s" already exists in this store.'),
                                $_REQUEST['LocationName'][$i]),
					    'StoreAddEdit.php?&amp;'.$query . '&amp;StoreId='. $Store->getId());
				exit;
			}
			$Location->setName($_REQUEST['LocationName'][$i]);
            saveInstance($Location, $errorURL);
		}
	}
	// Si clic sur Ajouter un emplacement, pour creer un item vide
	if (SearchTools::requestOrSessionExist('actionId', '0')) {
	    $Location = Object::load('Location');
		$Location->setStore($Store);
		$LocationCollection->setItem($Location);
	}
}

$query .= '&amp;StoreId='. $Store->getId();

/*  Contruction du formulaire  */
require_once('HTML/QuickForm.php');
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($Smarty);
$actorFilter = array('Active' => 1, 'Generic' => 0);
$ActorArray = SearchTools::createArrayIDFromCollection(
        'Actor', $actorFilter, MSG_SELECT_AN_ELEMENT);

$form = new HTML_QuickForm('StoreAddEdit', 'post', 'StoreAddEdit.php');
unset($form->_attributes['name']);  // xhtml compliant
$form->addElement('text', 'StoreName', '');
$form->addElement('select', 'StockOwner', '', $ActorArray);
$Smarty->assign('storeId', $Store->getId());
$Smarty->assign('activated', $Store->getActivated());

$form->addElement('hidden', 'StoreId', $Store->getId());
$form->addElement('hidden', 'stoId', $_REQUEST['stoId']);
$form->addElement('hidden', 'returnURL', 'StorebyStorageSiteList.php');

$StoreName = (isset($_REQUEST['StoreName']))?$_REQUEST['StoreName']:$Store->getName();
$StockOwnerId = (isset($_REQUEST['StockOwner']))?
        $_REQUEST['StockOwner']:$Store->getStockOwnerId();
$defaultValues = array('StockOwner' => $StockOwnerId, 'StoreName' => $StoreName);
$form->setDefaults($defaultValues);  // affiche le form avec les valeurs par defaut

$form->accept($renderer); 			 // affecte au form le renderer personnalise
$Smarty->assign('StorageSiteId', $_REQUEST['stoId']);
$Smarty->assign('form', $renderer->toArray());


/*  construction du grid  */
$grid = new Grid();
define('LOCATION_LIST_ITEMPERPAGE', 200);
$grid->itemPerPage = LOCATION_LIST_ITEMPERPAGE;
$grid->displayCancelFilter = false;

$grid->NewAction('SubmitWithIndex', array('Caption' => A_ADD,
        'Title' => _('Add a location')));
$grid->NewAction('Delete', array('TransmitedArrayName' => 'locId',
        'ReturnURL' => 'StoreAddEdit.php?' . $query,
        'EntityType' => 'Location',
        'Query' => $query));
$grid->NewAction('SubmitWithIndex');
$grid->NewAction('Cancel', array('ReturnURL' => 'StorebyStorageSiteList.php?stoId='
        . $_REQUEST['stoId']));

$grid->NewColumn('FieldMapper', _('Location name'),
        array('Macro' => '<input type="text" name="LocationName[]" size="15" value="%Name%" />
        			   <input type="hidden" name="LocationId[]" value="%Id%" />',
	          'Sortable' => false));

$result = (0 == $Store->getId())?"":$grid->render($LocationCollection, false);

if ($grid->isPendingAction()) {
	$LocationCollection = false;
    $grid->setMapper($LocationMapper);
    $dispatchResult = $grid->dispatchAction($LocationCollection);

    if (Tools::isException($dispatchResult)) {
        Template::errorDialog($dispatchResult->getMessage(), 'StoreAddEdit.php?' . $query);
    }
	else {
        Template::page(_('Update store'),
            $Smarty->fetch('Store/StoreAddEdit.html') . $result . '</form>');
	}
}
else {
    Template::page(
        $action . ' ' . _('store'),
        $Smarty->fetch('Store/StoreAddEdit.html') . $result . '</form>'
    );
}

?>
