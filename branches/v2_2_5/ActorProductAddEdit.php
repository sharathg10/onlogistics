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
require_once('ProductAddEditTools.php');
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once('HTML/QuickForm.php');
require_once('Objects/SellUnitType.const.php');
require_once('FormatNumber.php');

// à appeler avant le démarrage de la en session
includeSessionRequirements();

// session et authentification
$session = Session::singleton();
$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_OPERATOR),
    array('showErrorDialog' => true));

// prolonge les données en session
SearchTools::prolongDataInSession();

// si le Product ou la collection de pbc n'est pas en session
// => message d'erreur et retour vers la liste des produits
if (!isset($_SESSION['pdtproduct'])) {
    Template::errorDialog(E_ERROR_SESSION, 'ProductList.php');
    exit;
}

// raccourcis
$product = $_SESSION['pdtproduct'];
$retURL  = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'ProductAddEdit.php';

// chargement ou création de l'ActorProduct
$apID = isset($_REQUEST['apID'])?$_REQUEST['apID']:0;
$apCollection = $product->getActorProductCollection();

$errorURL = $_SERVER['PHP_SELF'];
if ($apID > 0) {
    // mode edition
    // on le prend dans la collection de ActorProduct du produit
    $ap = $apCollection->getItemById($apID);
    // Pour l'afficher dans la liste meme s'il est desactive
    $supplier = $ap->getActor();
    $errorURL .= '?apID=' . $apID;
} else {
    if (isset($_SESSION['pdtActorProduct'])) {
        $ap = $_SESSION['pdtActorProduct'];
    } else {
        // mode ajout, on crée l'actorProduct et on lui génère un id
        $ap = new ActorProduct();
        $ap->generateId();
    }
}

// prolonge la session pour 2 pages
$session->register('pdtproduct', $product, 2);
$session->register('pdtActorProduct', $ap, 2);

// premier passage du delete
if (isset($_GET['delete'])) {
    $msg = sprintf(
        _('Are you sure you want to delete supplier "%s" ?'),
        Tools::getValueFromMacro($ap, '%Actor.Name%')
    );
    $okLink = 'ActorProductAddEdit.php?apID=' . $apID . '&forcedelete=1';
    Template::confirmDialog($msg, $okLink, $retURL);
    exit;
}
// deuxieme passage après confirmation
if (isset($_GET['forcedelete'])) {
    $apCollection->removeItemById($apID);
    $product->setActorProductCollection($apCollection);
    Tools::redirectTo($retURL);
    exit;
}

// traitement du formulaire
if (isset($_POST['Ok'])) {
    // on rempli l'objet
    FormTools::autoHandlePostData($_POST, $ap);
    // si prioritaire est checké, il faut vérifier qu'il n'y a pas déjà un
    // couple avec ce produit prioritaire
    if (isset($_POST['ActorProduct_Priority'])) {
        $count = $apCollection->getCount();
        for ($i=0; $i<$count; $i++) {
            $item = $apCollection->getItem($i);
            if ($item->getId() != $ap->getId() && $item->getPriority() == 1) {
                $ap->setPriority(0);
                $msg = _('Supplier "%s" is already taking precedence for ordering this product.');
                Template::errorDialog(
                    sprintf($msg, Tools::getValueFromMacro($item, '%Actor.Name%')),
                    $errorURL
                );
                exit(1);
            }
        }
    } else {
        $ap->setPriority(0);
    }
    if ($apID == 0) {
        $apCollection->setItem($ap);
    }
    $product->setActorProductCollection($apCollection);
    // nettoyage de la session
    unset($_SESSION['pdtActorProduct']);
    // on ne sauve pas, on redirige vers ProductAddEdit
    Tools::redirectTo($retURL);
    exit(0);
}

// traitement du template
$smarty = new Template();
$smarty->assign('retURL', $retURL);
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('FlowAddEdit', 'post');
$form->addElement('hidden', 'retURL', $retURL);
$form->addElement('hidden', 'apID', $apID);
$form->addElement('hidden', 'ActorProduct_Product_ID', $product->getId());

$supplierArray = SearchTools::createArrayIDFromCollection(array('Supplier', 'AeroSupplier'),
    array('Active' => 1), '', 'Name');
// Pour l'afficher dans la liste meme s'il est desactive
if (isset($supplier) && !$supplier->getActive()) {
    $additionalActor = array($supplier->getId() => $supplier->getName());
    $supplierArray = $supplierArray + $additionalActor;
    asort($supplierArray);
}
$form->addElement('select', 'ActorProduct_Actor_ID',
    _('Supplier'), $supplierArray, 'style="width:100%;"');
$form->addElement('text', 'ActorProduct_AssociatedProductReference',
    _('Purchase reference'), 'style="width:100%;"');
$unitTypeArray = SearchTools::CreateArrayIDFromCollection('SellUnitType', array(), '',
    'LongName');
$form->addElement('select', 'ActorProduct_BuyUnitType_ID',
    _('Purchase unit'), $unitTypeArray);
$form->addElement('text', 'ActorProduct_BuyUnitQuantity',
    _('Purchase unit quantity'), 'size="5"');
$form->addElement('checkbox', 'ActorProduct_Priority',
    _('Takes precedence for order'));

// valeurs par défaut
$defaultValues = FormTools::getDefaultValues($form, $ap);
if (isset($defaultValues['ActorProduct_BuyUnitType_ID'])
 && $defaultValues['ActorProduct_BuyUnitType_ID'] == '') {
    $defaultValues['ActorProduct_BuyUnitType_ID'] = SELLUNITTYPE_UB;
}

$form->setDefaults($defaultValues);

// Validation du formulaire
$form->addRule('ActorProduct_Actor_ID',
    _('Please select a supplier.'), 'nonzero', '', 'client');
$form->addRule('ActorProduct_BuyUnitType_ID',
    _('Please select a purchase unit.'), 'nonzero', '', 'client');
$form->addRule('ActorProduct_BuyUnitQuantity',
    _('Please select a purchase unit quantity.'), 'required', '',
    'client');
$form->addRule('ActorProduct_BuyUnitQuantity',
    _('Field "purchase unit quantity" must be an integer or a float.'),
    'numeric', '', 'client');
$form->setJsWarnings(E_VALIDATE_FORM.' : ',_('Please correct.'));

$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());

$pageTitle = _('Add or update supplier for product %s.');
$pageContent = $smarty->fetch('Product/ActorProductAddEdit.html');

Template::page(sprintf($pageTitle, $product->getBaseReference()), $pageContent);

?>
