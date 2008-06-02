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
require_once('Objects/ProductHandingByCategory.php');

// authentification
$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
    					   UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_AERO_ADMIN_VENTES));
//Database::connection()->debug = true;
define('I_PAGE_TITLE', _('Add or update discount by actor category'));
define('E_ALREADY_EXISTS',
    _("A discount already exists for the following product/category pairs: %s."));

// Prolonger les données en session
SearchTools::ProlongDataInSession(1);

// url de retour
$retURL = isset($_REQUEST['retURL'])?
        $_REQUEST['retURL']:'ProductHandingByCategoryAddEditE2.php';

$action = ($_SESSION['phcID'] == 0)?'add':'edit';

// récupération de l'id de la remise
$phcID = isset($_SESSION['phcID'])?
        ((is_array($_SESSION['phcID']))?$_SESSION['phcID'][0]:$_SESSION['phcID']):0;

if ($action == 'edit') {
    $phbc = Object::load('ProductHandingByCategory', $phcID);
    $phbc->setHanding($_SESSION['handing']);
}
else {
    $pdtIdsSessionName = SearchTools::getGridItemsSessionName(
        'ProductHandingByCategoryAddEdit');
    $productIds = $_SESSION[$pdtIdsSessionName];
}

// Enregistrement des modifications
if(isset($_REQUEST['actionId']) && $_REQUEST['actionId'] == 0) {
    $errorDatas = array();  // Contiendra les infos sur les PHC impossibles a creer
	$now = date('Y-m-d H:i:s');
    // Démarrage de la transaction
    Database::connection()->startTrans();

    // On vérifie qu'une remise n'existe pas déjà pour les couples produit/cat.
    // dans le cas d'un ajout uniquement

	// Si mode edition, select simple, avec 1 seul Product
	if ($action == 'edit') {
	    if ($cats = request_ProductHandingByCategoryAlreadyExists(
			        $phcID, $phbc->getProductId(), $_SESSION['CategoryIds'])) {
	        $catIds = array_diff($_SESSION['CategoryIds'], array_keys($cats));
	        $phbc->setCategoryCollectionIds($catIds);
			$errorDatas[$phbc->getProductId()] = $cats;
	    }
	    // sauvegarde de la remise
        $phbc->setUpdateDate($now);
        saveInstance($phbc, $retURL);
	}
	// Select multiple sur les Product: on cree autant de PHC que de Product selectionnes
	else {
        foreach($productIds as $pdtId) {
			$phbc = Object::load('ProductHandingByCategory');
			if ($cats = request_ProductHandingByCategoryAlreadyExists(
				        $phcID, $pdtId, $_SESSION['CategoryIds'])) {
		        $catIds = array_diff($_SESSION['CategoryIds'], array_keys($cats));
				$errorDatas[$pdtId] = $cats;
		    }
			else {
				$catIds = $_SESSION['CategoryIds'];
			}
			$phbc->setCategoryCollectionIds($catIds);
			$Product = Object::load('Product', $pdtId);
			$phbc->setProduct($Product);
			$phbc->setHanding($_SESSION['handing']);
			$phbc->setType($_SESSION['type']);
			$phbc->setCurrency($_SESSION['currency']);
			$phbc->setUpdateDate($now);

		    // sauvegarde de la remise
            $phbc->setUpdateDate($now);
            saveInstance($phbc, $retURL);
			unset($phbc, $cats);
		}
	}

    // commit de la transaction
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(Database::connection()->errorMsg(), $retURL);
        exit;
    }
    Database::connection()->completeTrans();
    unset($_SESSION['CategoryIds'], $_SESSION['phcID'], $_SESSION['handing']);
    if (isset($pdtIdsSessionName)) {
        unset($_SESSION[$pdtIdsSessionName]);
    }

    if (count($errorDatas) > 0) {
        // il faut afficher un message d'info
		$errorProductIds = array_keys($errorDatas);
		$errorCategoryIds = array_values($errorDatas);
		$displayErrors = array();  // Pour lister a l'ecran les PHC posant pb

		foreach($errorDatas as $pdtId => $categ) {
			$pdt = Object::load('Product', $pdtId);
			$displayErrors[] = $pdt->getBaseReference() . ' | "' . implode('", "', $categ) .'"';
			unset($pdt);
		}
		$msg = sprintf(E_ALREADY_EXISTS, '<ul><li>' .
            implode('</li><li>', $displayErrors) . '</li></ul>');
        Template::errorDialog($msg, $retURL);
    }
	else {  // sinon redirection
        unset($_SESSION['LastEntitySearched']);
        Tools::redirectTo('ProductHandingByCategoryList.php');
    }
    exit;
}


/*  Affichage du Grid  */
$collection = new Collection();

if ($action == 'edit') {
    $collection->setItem($phbc);
}
else {
    foreach($productIds as $pdtId) {
    	$phbc = Object::load('ProductHandingByCategory');
        $phbc->setHanding($_SESSION['handing']);
        $phbc->setProduct($pdtId);
        $collection->setItem($phbc);
        unset($phbc);
    }
}

$grid = new Grid();
$grid->withNoCheckBox = true;
$grid->paged = false;
$grid->displayCancelFilter = false;

// actions
$grid->NewAction('SubmitWithIndex', array('Caption' => A_VALIDATE));
$grid->NewAction('Cancel', array('ReturnURL' => $retURL . '?phcID=' . $phcID));

$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%Product.BaseReference%'));
$grid->NewColumn('FieldMapper', _('Designation'), array('Macro' => '%Product.Name%'));

if ($grid->isPendingAction()) {
    $none = false;
    $grid->dispatchAction($none);
}


// Boucle: une colonne par devise (Currency)
$mapper = Mapper::singleton('Currency');
$CurrencyColl = $mapper->loadCollection();
$count = $CurrencyColl->getCount();
for($i = 0; $i < $count; $i++) {
	$Currency = $CurrencyColl->getItem($i);
	$grid->NewColumn('ProductHandingByCategoryAddEdit',
						_('Selling price') . ' ' . $Currency->getSymbol() . ' ' . _('excl. VAT'),
						array('CurrencyId' => $Currency->getId(),
                        'handing' => $_SESSION['handing'],
                        'handingType'=> $_SESSION['type'],
                        'handingCurrency'=>$_SESSION['currency']));
}

// Recuperation des Name de Category impactees
$names = '';
$categoryMapper = Mapper::singleton('Category');
$categoryCollection = $categoryMapper->loadCollection(
        array('Id' => $_SESSION['CategoryIds']));
$count = $categoryCollection->getCount();
$virgule = false;
for($i = 0; $i < $count; $i++){
	$category = $categoryCollection->getItem($i);
    if($virgule) {
        $names .= ', ';
    } else {
        $virgule = true;
    }
    $names .= $category->getName();
}

$detail = '<form><b>' . _('Summary of references for which discount of ');
$detail .= I18N::formatNumber($_SESSION['handing']);
if($_SESSION['type']==ProductHandingByCategory::TYPE_PERCENT) {
    $detail .= '%';
} else {
    $cur = $mapper->load(array('Id'=>$_SESSION['currency']));
    $detail .= ' ' . $cur->getName();
}
$detail .= ' ' . _('is applied') . '<br />';
$detail .= _('Concerned categories are:') . ' : ' . $names . '.</b><br /><br />';

$result = $grid->render($collection, false, array(),
                        array('Product.BaseReference' => SORT_ASC));

Template::page(I_PAGE_TITLE, $detail . $result . '</form>');

?>
