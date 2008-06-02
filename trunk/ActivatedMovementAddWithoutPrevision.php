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
require_once('Objects/MovementType.const.php');
require_once('ActivatedMovementTools.php');

$Auth = Auth::Singleton();
$Auth->checkProfiles();
$uac = $Auth->getUser();
$siteIds = $uac->getSiteCollectionIds();
$ProfileId = $Auth->getProfile();
$UserConnectedActorId = $Auth->getActorId();
//Database::connection()->debug = true;
$MovementTypeMapper = Mapper::singleton('MovementType');
$ProductMapper = Mapper::singleton('Product');
$retURL = 'ActivatedMovementAddWithoutPrevision.php';

////////// On a selectionné produit & MvtType:  affichage du GRID /////////////
// On a tapé une ref dans le champs text...
if((isset($_REQUEST['productText']) && $_REQUEST['productText'] != '')) {
    $productMapper = Mapper::singleton('Product');
    $product = $productMapper->load(array('BaseReference'=>$_REQUEST['productText']));
    if(!($product instanceof Product)) {
        Template::errorDialog(
            sprintf(_('Unknown reference "%s", please try again.'),
                    $_REQUEST['productText']), $retURL);
        exit();

    }
    $_REQUEST['product'] = $product->getId();
}
if ((isset($_REQUEST['product']) && ($_REQUEST['product'] != 0))
            && isset($_REQUEST['MvtType']) && $_REQUEST['MvtType'] != 0) {

	$MovementType = $MovementTypeMapper->load(array('Id' => $_REQUEST['MvtType']));
	$MvtTypeEntrieExit = $MovementType -> getEntrieExit(); // 0:ENTREE, 1:SORTIE

	// Gestion des emplacements autorises pour ce produit TO DO
	// on recupere les id et les noms des emplacements deja affectes pour le produit
	$LPQMapper = Mapper::singleton('LocationProductQuantities');
	$filter = array('Product' => $_REQUEST['product'],
        'Location.Activated' => 1, 'Activated' => 1);
    if(!empty($siteIds)) {
        $filter = array_merge(
            $filter,
            array('Location.Store.StorageSite'=>$siteIds));
    } elseif (!in_array($ProfileId,
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW))) {
		$filter = array_merge(
            $filter,
            array('Location.Store.StorageSite.Owner' => $UserConnectedActorId));
	}
	$LPQCollection = $LPQMapper->loadCollection(
            $filter, array('Location.Name' => SORT_ASC));

	// Si entree ou si deplacement, on construit le select sur les Location
	if ($MvtTypeEntrieExit == ENTREE) {
		if (isset($_SESSION['LPQCollection'])) {
		    $LPQCollectionForSession = unserialize($_SESSION['LPQCollection']);
			for ($i=0;$i<$LPQCollectionForSession->getCount();$i++) {
				$LPQCollection->setItem($LPQCollectionForSession->getItem($i));
			}
		}
		$HTMLLocationSelect = getHTMLLocationSelect(
                $LPQCollection, $ProfileId, $UserConnectedActorId, $siteIds);
	}

	/*  Creation du grid  */
	$Product = $ProductMapper->load(array('id' => $_REQUEST['product']));
	$grid = executionGrid($Product->getTracingMode(), $MvtTypeEntrieExit, $LPQCollection);

	if ($grid->isPendingAction()) {
		$LPQtyCollection = false;
		$grid->setMapper($LPQMapper);
		$dispatchResult = $grid->dispatchAction($LPQtyCollection);
	}
	else {
		$ActivatedMvtGrid = $grid->render($LPQCollection);
	}


	//Affichage du formulaire avec smarty
	$Smarty = new Template();
	$Smarty->assign('FormAction', 'ActivatedMovementAddWithoutPrevisionToBeExecuted.php');
	$Smarty->assign('ActivatedMvtGrid', $ActivatedMvtGrid);
	$Smarty->assign('monSelectMvtType', $_REQUEST['MvtType']);
	$Smarty->assign('MvtTypeEntrieExit', $MvtTypeEntrieExit);
	$Smarty->assign('TracingMode', $Product->getTracingMode());
	if ($Product->getTracingMode() > 0) {
        $tmArray = Product::getTracingModeConstArray();
	    $Smarty->assign('TracingModeName', $tmArray[$Product->getTracingMode()]);
	}

	if ($MvtTypeEntrieExit == ENTREE) {  // pour le select sur les Locations
		$Smarty->assign('HTMLLocationSelect', $HTMLLocationSelect);
	}

	$Smarty->assign('MvtTypeName', $MovementType->getName());
	$Smarty->assign('ProductName', $Product->getBaseReference());
	$Smarty->assign('monSelectProduct', $_REQUEST['product']);
	if (isset($_REQUEST['Comment'])) {  // en cas de retour apres msge d'erreur
		$Smarty->assign('Comment', stripcslashes(stripcslashes(
                html_entity_decode(urldecode($_REQUEST['Comment'])))));
	}
	else {
		$Smarty->assign('Comment', '');
	}
    Template::page(
        '',
        $Smarty->fetch('ActivatedMovement/ActivatedMovementAddWithoutPrevision.html'),
	    array('js/includes/ActivatedMovementAdd.js')
    );
}


// si pas de produit & MvtType selectionne, on n'affiche pas le GRID mais le form
else {
	// Suppression de cette var liee au mode de suivi
	if (isset($_SESSION['LCPCollection'])) {
	    unset($_SESSION['LCPCollection']);
	}

	require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
    require_once('HTML/QuickForm.php');
	$form = new HTML_QuickForm('monForm', 'post', $_SERVER['PHP_SELF']);
    unset($form->_attributes['name']);  // xhtml compliant
	$smarty = new Template();
    $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
	// recupere les infos sur les MovementType, pour le SELECT
	$MovementTypeCollection = $MovementTypeMapper->loadCollection(
            array('Foreseeable' => array(-1, 0)), array('Name' => SORT_ASC));

	$MovementTypeNamesArray = array();
	for($i = 0; $i < $MovementTypeCollection->getCount(); $i++){
    	$item = $MovementTypeCollection->getItem($i);
		if ($item->getId() == SORTIE_DEPLACEMT) {
			unset($item);
			continue;
		}
		elseif ($item->getId() == ENTREE_DEPLACEMT) {
		   	$MovementTypeNamesArray[$item->getId()] = _('Position change');
		}
		else {
			$MovementTypeNamesArray[$item->getId()] = $item->getName();
		}
		unset($item);
	}
	$InitValueArrayMvtType = array(0=>_('Select a movement type'));
	// NE PAS UTILISER Array_merge :cf DOC PHP!!!
	$MovementTypeNamesArray = $InitValueArrayMvtType + $MovementTypeNamesArray;

	if (!isset($_REQUEST['MvtType'])) {  // si pas de type de mvt
		///$disabled = '';				 // le select ne doit pas etre disabled
		$form->addElement('select', 'MvtType', '', $MovementTypeNamesArray,
		      'onchange="this.form.submit()"');
	    $inputRef = '';
	}

	else {
        $form->updateAttributes(array('onsubmit' => "this.MvtType.disabled=false;"));
		$form->addElement('select', 'MvtType', '', $MovementTypeNamesArray,
                'onChange="this.form.submit()" disabled="disabled"');
		$form ->setDefaults(array('MvtType'=> $_REQUEST['MvtType']));
		$MovementType = $MovementTypeMapper->load(
                array('Id' => $_REQUEST['MvtType']));

		// on recupere les infos sur les produits pour les afficher dans le SELECT
		require_once('SQLRequest.php');
		// si sortie ou deplacement
		if (SORTIE == $MovementType->GetEntrieExit()
                || ENTREE_DEPLACEMT == $_REQUEST['MvtType']) {
			// Si pas pilote, filtre sur les Product vus
			$SitOwnerId = (in_array(
                $ProfileId,
                array(UserAccount::PROFILE_ADMIN,
                      UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)))?
                    0:$UserConnectedActorId;
			$ProductRefArray = array(0=>_('Select a product'))
							+ StockProductArrayForSelect(-1, $SitOwnerId, 1);
			$ProductNameArray = array(0=>_('Select a designation'))
							+ StockProductArrayForSelect(-1, $SitOwnerId, 1, 'Name');
		}
		else {
			$ProductRefArray = array(0=>_('Select a reference'))
                    + ProductArrayForSelect();
			$ProductNameArray = array(0=>_('Select a designation'))
                    + ProductArrayForSelect('Name');
		}

		$form->addElement('select', 'product', '', $ProductRefArray,
				'id="productSelect" onchange="this.form.MvtType.disabled=false;this.form.submit()"');
	    $form->addElement('select', 'productName', '', $ProductNameArray,
				'onchange="fw.dom.selectOptionByValue(\'productSelect\',this.value);"');
	    $form->addElement('text', 'productText');
        $form->addElement('submit', 'ok', 'ok');
	}
////////
    // Suppression des LPQ en session s'il y en a
    unset($_SESSION['LPQCollection']);
    $form->accept($renderer);
    $smarty->assign('form', $renderer->toArray());
    $pageTitle = _('Execution of an unexpected movement');
    $pageContent = $smarty->fetch('ActivatedMovement/ACMWithoutPrevProductSelect.html');
    Template::page(
        $pageTitle,
        $pageContent,
        array('js/lib-functions/ActivatedMvtWithoutPrev.js')
    );
}

?>