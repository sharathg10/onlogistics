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
require_once('Objects/ConcreteComponent.php');
require_once('Objects/ConcreteProduct.php');  // define des cstes

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

$pageTitle = _('Add or update component part');
$query = 'cpId=' . $_REQUEST['cpId']. '&cmpId=' . $_REQUEST['cmpId']
		 . '&parId=' . $_REQUEST['parId'];
$returnURL = 'ComponentConcreteProduct.php?' . $query;
$errorURL = basename($_SERVER['PHP_SELF']) . '?' . $query;

SearchTools::prolongDataInSession();
//Database::connection()->debug = true;

if (isset($_REQUEST['ccmpId']) && $_REQUEST['ccmpId'] > 0) {
	$ConcreteComponent = Object::load('ConcreteComponent', $_REQUEST['ccmpId']);
    $initCPid = $ConcreteComponent->getConcreteProductId();
} else {  // Il s'agit d'un ajout
    $ConcreteComponent = new ConcreteComponent();
    $ConcreteComponent->setParent($_REQUEST['parId']);
}

if (!($ConcreteComponent instanceof ConcreteComponent)) {
    Template::errorDialog(E_MSG_TRY_AGAIN, $returnURL);
  	exit;
}

// Servira à déterminer s'il s'agit d'un ajout
$isNewConcreteComponent = ($ConcreteComponent->getId() == 0);
$Component = Object::load('Component', $_REQUEST['cmpId']);

//  Si on a clique sur OK apres saisie ou confirme la saisie
if (isset($_REQUEST['formSubmitted'])) {
	FormTools::autoHandlePostData($_REQUEST, $ConcreteComponent, 'ConcreteComponent');

	Database::connection()->startTrans();

    $cp = $ConcreteComponent->getConcreteProduct();
    // Le ConcreteProduct head
    $headCP = Object::load('ConcreteProduct', $_REQUEST['cpId']);

	if ($isNewConcreteComponent) {
		$mapper = Mapper::singleton('ConcreteComponent');
        $ConcreteComponent->setId($mapper->generateId());
    }
    // Rien a faire dans ce cas
    elseif ($initCPid == $ConcreteComponent->getConcreteProductId()) {
        Tools::redirectTo($returnURL);
	    exit;
    }
    else {  // Si edition, et modification du Concreteproduct lie
        $initCP = Object::load('ConcreteProduct', $initCPid);
        $nullComponent = new Component();
        $initCP->setComponent($nullComponent);  // setComponent(0): fatal error
        saveInstance($initCP, $returnURL);
        // Supprime une ligne de cptHead
        $headCP->addRemoveChild($initCPid, false);
        saveInstance($headCP, $returnURL);
    }

    saveInstance($ConcreteComponent, $returnURL);
    $headCP->addRemoveChild($cp->getId());  // remplit la table cptHead si besoin
    saveInstance($headCP, $returnURL);
    $cp->setComponent($Component);
    saveInstance($cp, $returnURL);

	/* Controle de la Qty:
	 * la somme des Quantity des ConcreteComponent lies au Component, et ayant
	 * pour Head le ConcreteProduct d'id $headId doit etre <= Component->getQty()
	 **/
	$newQty = $Component->getConcreteQuantity($_REQUEST['cpId']);
	if ($newQty > $Component->getQuantity()) {
	    Template::errorDialog(
                _('Quantity provided exceeds allowed quantity for this component.'),
				$errorURL);
  		exit;
	}
	// Remplissage de cptHead en cascade si le CP choisi resulte d'un assemblage
    // Recursion
	$ConcreteComponent->updateChildsHead($headCP);

	//  Si OK, commit de la transaction
	// Commit de la transaction, si elle a réussi
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog(E_MSG_TRY_AGAIN, $errorURL);
        exit;
    }
    Database::connection()->completeTrans();
	Tools::redirectTo($returnURL);
	exit;
}

/*  Formulaire */
$smarty = new Template();
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
require_once ('HTML/QuickForm.php');
$form = new HTML_QuickForm('ConcreteComponentAddEdit', 'post');

// Dans le Select, on propose les ConcreteProduct lies au Product,
// et non lies a une Nomenclature
$ProductId = $Component->getProductId();

$filter = array('Product' => $ProductId,  'Component' => array(0),
                'State' => array(ConcreteProduct::EN_MARCHE, ConcreteProduct::EN_STOCK));
$ConcreteProductArray = SearchTools::createArrayIDFromCollection(
        'ConcreteProduct', $filter, '', 'SerialNumber');
// Si mode edition, il faut proposer le CP actuel
if (!$isNewConcreteComponent) {
    $ConcreteProductArray += array(
            $ConcreteComponent->getConcreteProductId() =>
                    Tools::getValueFromMacro($ConcreteComponent,
                                      '%ConcreteProduct.SerialNumber%'));
}

$form->addElement('select', 'ConcreteComponent_ConcreteProduct_ID', _('Part (SN)'),
        $ConcreteProductArray, 'style="width:100%"');

// Ici, Product::TRACINGMODE_SN, => la quantity est forcement 1
$form->addElement('text', 'ConcreteComponent_Quantity', _('Quantity'),
        'style="width:100%;background-color:#E1E8EF;border:1px #000000 dotted;" readonly');
$form->addElement('hidden', 'ccmpId', $ConcreteComponent->getId());
$form->addElement('hidden', 'cmpId', $_REQUEST['cmpId']);
$form->addElement('hidden', 'cpId', $_REQUEST['cpId']);
$form->addElement('hidden', 'parId', $_REQUEST['parId']);

/*  Si Edition d'un ConcreteComponent existant  */
if (isset($ConcreteComponent)) {
	$defaultValues = FormTools::getDefaultValues($form, $ConcreteComponent);
}
else {
	$defaultValues = SearchTools::createDefaultValueArray();
}
$form->setDefaults($defaultValues);  // affiche le form avec les valeurs par defaut

$form->accept($renderer);  // affecte au form le renderer personnalise
$smarty->assign('form', $renderer->toArray());
$smarty->assign('returnURL', $returnURL);

$pageContent = $smarty->fetch('Nomenclature/ConcreteComponentAddEdit.html');

Template::page($pageTitle, $pageContent);

?>
