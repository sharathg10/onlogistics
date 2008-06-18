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
// Pour la mise en session
require_once('Objects/ProductSubstitution.php');
$auth = Auth::Singleton();

SearchTools::prolongDataInSession();  // prolonge les datas en session
$confirmMsge = '';
$ProductSubstitutionMapper = Mapper::singleton('ProductSubstitution');
$pdtId = isset($_REQUEST['pdtId'])?$_REQUEST['pdtId']:0;

// Si le formulaire a ete envoye pour modifier ou ajouter des liens entre
// Product (clic sur OK)
if (isset($_REQUEST['actionId']) && $_REQUEST['actionId'] == -1
        && isset($_POST['HiddenPdtSubstId']) && !isset($_REQUEST['x'])) {
	// -1: MAJ des types de lien; 0: suppression
	// utilise que si ajout de Product
	$PdtSubstCollectionForSession = new Collection();
    if (isset($_SESSION['pdtproduct']) && $_SESSION['pdtproduct'] instanceof Product) {
        $FromProduct = $_SESSION['pdtproduct'];
    } else {
        $FromProduct = Object::load('Product', $pdtId);
    }
    if (!($FromProduct instanceof Product)) {
        Template::errorDialog(E_NO_ITEM_FOUND, "javascript:window.close();");
        exit(1);
    }

	foreach($_POST['HiddenPdtSubstId'] as $i => $pdtSubstId) {

		if ($pdtSubstId > 0) {  //  Si modif d'un pdtSubst deja existant
			$ProductSubstitution = Object::load('ProductSubstitution', $pdtSubstId);
		}
		else {                  // Sinon, c'est un ajout
			$ProductSubstitution = Object::load('ProductSubstitution');
			$ByProduct = Object::load('Product', $_POST['HiddenPdtId'][$i]);
			$ProductSubstitution->setFromProduct($FromProduct);
			$ProductSubstitution->setByProduct($ByProduct);
		}
		if ($_POST['Interchangeable'][$i] <> -1) { // si un type de lien selectionne
			// Ces 3 lignes pour ne pas casser le lien dans le bon sens ds ce cas
			if ($ProductSubstitution->getFromProductId() <> $pdtId
                    && $_POST['Interchangeable'][$i] == 0) {
				$ProductSubstitution->setByProduct($ProductSubstitution->getFromProduct());
				$ProductSubstitution->setFromProduct($FromProduct);
			}
		    $ProductSubstitution->setInterchangeable($_POST['Interchangeable'][$i]);
			// si le Product existe deja, on peut sauver en bado
			if ($pdtId > 0) {
                saveInstance($ProductSubstitution, 'javascript:window.close();');
			}
			else{
				$PdtSubstCollectionForSession->setItem($ProductSubstitution);
			}
		}
		unset($ProductSubstitution);
	}
	if (isset($PdtSubstCollectionForSession)
            && $PdtSubstCollectionForSession->getCount() > 0) {
		$session = Session::Singleton();
		$PdtSubstCollectionSerialized = serialize($PdtSubstCollectionForSession);
		$session->register('PdtSubstCollection', $PdtSubstCollectionSerialized, 5);
		$_SESSION['PdtSubstCollection'] = $PdtSubstCollectionSerialized;
	}
	$confirmMsge = _('Your modifications were successful, you can close this popup window.');
}

if (isset($_POST['ProductSelect']) && isset($_REQUEST['x'])) { // si ajout de liens
	$ProductSubstitutionCollection = new Collection();
	foreach($_POST['ProductSelect'] as $i => $ProductId) {
		$Collection = $ProductSubstitutionMapper->loadCollection(
                array('FromProduct.Id' => $pdtId, 'ByProduct.Id' => $ProductId));
		if ($Collection->getCount() > 0) {
		    continue;      // pour ne pas afficher 2 fois la meme ligne
		}
        if (isset($_SESSION['pdtproduct']) && $_SESSION['pdtproduct'] instanceof Product) {
            $FromProduct = $_SESSION['pdtproduct'];
        } else {
            $FromProduct = Object::load('Product', $pdtId);
        }
        if (!($FromProduct instanceof Product)) {
            Template::errorDialog(E_NO_ITEM_FOUND, "javascript:window.close();");
            exit(1);
        }
		$ByProduct = Object::load('Product', $ProductId);
		$ProductSubstitution = Object::load('ProductSubstitution');
		$ProductSubstitution->setFromProduct($FromProduct);
		$ProductSubstitution->setByProduct($ByProduct);
		$ProductSubstitution->setInterchangeable(-1);
		$ProductSubstitutionCollection->setItem($ProductSubstitution);
		unset($ProductSubstitution);
	}
}

/* filtre: soit relation avec FromProduct=$_REQUEST['pdtId']
* 		   soit relation avec ByProduct=$_REQUEST['pdtId'] ET Interchangeable=1  */
$FilterComponent = new FilterComponent();
$FilterComponent->setItem(
        new FilterRule('ByProduct',
                FilterRule::OPERATOR_EQUALS,
             	$pdtId));
$FilterComponent->operator = FilterComponent::OPERATOR_AND;
$FilterComponent->setItem(new FilterRule(
        'Interchangeable',
                FilterRule::OPERATOR_EQUALS,
                1));

$rule = new FilterRule(
        'FromProduct',
		FilterRule::OPERATOR_EQUALS,
		$pdtId
);
// le filtre qui sera finalement passe a loadObjectCollection()
$filter = new FilterComponent();
$filter->setItem($rule);
$filter->operator = FilterComponent::OPERATOR_OR;
$filter->setItem($FilterComponent);

$smarty = new Template();
require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('pdtSubstForm', 'post', $_SERVER['PHP_SELF']);

require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);

$form->addElement('hidden','pdtId', $pdtId);

// Construction de la liste de selection des Products, qui doivent
// être <> ceux ds le Grid
// tableau des id des Products a ne pas afficher ds le select
$pdtIdArrayToErase = array($pdtId);
$PdtSubstCollection = $ProductSubstitutionMapper->loadCollection($filter);
if ($PdtSubstCollection instanceof Collection && $PdtSubstCollection->getCount() > 0 ) {
	for($i = 0; $i < $PdtSubstCollection->getCount(); $i++){
    	$item = $PdtSubstCollection->getItem($i);
		$pdtIdArrayToErase[] = ($pdtId == $item->getFromProductId())?
                $item->getByProductId():$item->getFromProductId();
	}
}

$ProductMapper = Mapper::singleton('Product');
$ProductCollection = $ProductMapper->loadCollection(
        array(), array('BaseReference'=>SORT_ASC), array('BaseReference'));

$ProductRefArray = array();
for($i = 0; $i < $ProductCollection->getCount(); $i++){
   	$item = $ProductCollection->getItem($i);
    // si produit est bien a afficher ds le select
	if (!in_array($item->getId(), $pdtIdArrayToErase)) {
	    $ProductRefArray[$item->getId()] = $item->getBaseReference();
	}
	unset($item);
	}
$form->addElement('select', 'ProductSelect', _('Manage product substitutions: '),
        $ProductRefArray,
		'multiple size="1" OnMouseOver="this.size=20" OnMouseOut="this.size=1"');

$form->accept($renderer); // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$Product = Object::load('Product', $pdtId);
$pdtBaseReference = Tools::isEmptyObject($Product)?
        _('[Products being created]'):$Product->getBaseReference();
$smarty->assign('ProductReference', $pdtBaseReference);

$template = 'Product/ProductSubstitutionList.html';
$formContent = $smarty->fetch($template);

$grid = new Grid();
$grid->itemPerPage = 100;
$grid->paged = false;
$grid->displayCancelFilter = false;

if ($pdtId != 0) {  // si lors d'un ajout de Product, on supprime cette action
    $grid->NewAction('Delete', array('TransmitedArrayName' => 'PdtSubstId',
            'EntityType' => 'ProductSubstitution',
            'Query' => 'pdtId=' . $pdtId,
			'GridInPopup' => true));
}

$grid->NewAction('Submit');
$grid->NewAction('Close');

$grid->NewColumn('ProductSubstitutionProduct', _('Reference'),
    array('Sortable' => false, 'Macro' => '%ByProduct.BaseReference%'));
$grid->NewColumn('ProductSubstitutionLink', _('Link'), array('Sortable' => false));


if ($grid->isPendingAction()) {
	$PdtSubstCollection = false;
    $grid->setMapper($ProductSubstitutionMapper);
    $dispatchResult = $grid->DispatchAction($PdtSubstCollection);
    if (Tools::isException($dispatchResult)) {
        Template::errorDialog($dispatchResult->getMessage(),
                'ProductSubstitutionList.php?pdtId=' . $pdtId, BASE_POPUP_TEMPLATE);
    	}
	}
else {
	if (isset($ProductSubstitutionCollection)
            && $ProductSubstitutionCollection->getCount()> 0) { // si ajout de liens
		for($i = 0; $i < $ProductSubstitutionCollection->getCount(); $i++){
	   		$item = $ProductSubstitutionCollection->getItem($i);
			$PdtSubstCollection->setItem($item);
			unset($item);
		}
	}
  	$result = $grid->render($PdtSubstCollection, true, $filter,
            array('FromProduct.BaseReference' => SORT_ASC));
	$content = $formContent . '<br>' . $result."<input type=\"hidden\" name=\"pdtId\" value=\""
            . $pdtId . "\"></form><br /><b>".$confirmMsge.'</b>';
    Template::page(
        _('List of exchangeable products'),
        $content,
		array(),
        array(),
        BASE_POPUP_TEMPLATE
    );
  	}
?>
