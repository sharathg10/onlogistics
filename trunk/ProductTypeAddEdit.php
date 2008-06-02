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
require_once('Objects/ProductType.php');
require_once('Objects/Property.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

$session = Session::Singleton();

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'ProductTypeList.php';

// Messages
$errorBody  = _('Product type cannot be saved.');
$okTitle    = I_CONFIRM_DO;
$okBody     = _('Product type "%s" has been successfully saved.');
$pageTitle  = _('Add or update product types');
$errorPdtTypeName = _('A product type with the name provided already exists.');

// Si l'id est passe en paramètre on la charge sinon on instancie un nouvel
// objet
if(isset($_REQUEST['prtId'])) {
	$prtID = $_REQUEST['prtId'];
	$productType = Object::load('ProductType', $prtID);
} elseif (isset($_SESSION['ProductType'])
            && $_SESSION['ProductType'] instanceof ProductType) {
    $productType = $_SESSION['ProductType'];
} else {
    $prtID = false;
	$productType = Object::load('ProductType', $prtID);
}

// On check si l'objet est bien chargé, et on renvoie vers un dialogue
// d'erreur au cas où.
if (Tools::isException($productType)) {
    Template::errorDialog($productType->getMessage(), $retURL);
    exit;
}
// On le met en session pour 2 pages
$session->register('ProductType', $productType, 2);
// Traitement de l'envoi du formulaire
if (isset($_POST['formSubmitted'])) {
    // check sur le nom du ProductType
    $pdtTypeMapper = Mapper::singleton('ProductType');
    $obj = $pdtTypeMapper->load(array('Name'=>$_POST['ProductType_Name']));
    if($obj instanceof ProductType && $obj->getId() != $productType->getId()) {
        Template::errorDialog($errorPdtTypeName, $_SERVER['PHP_SELF']);
        exit();
}
    // On demarre une transaction
    Database::connection()->startTrans();
    FormTools::autoHandlePostData($_POST, $productType);
	// On ajoute les propriétés
	if (isset($_REQUEST['Property_Ids']) && is_array($_REQUEST['Property_Ids'])) {
		$propertyMapper = Mapper::singleton('Property');
		// on réinitialise les propriétés du type de produit
	    for($i = 0; $i < count($_REQUEST['Property_Ids']); $i++){
	    	$prop = Object::load('Property', $_REQUEST['Property_Ids'][$i]);
			if (Tools::isException($prop)) {
			    $prop = Object::load('Property', false);
			}
            $propname = $_REQUEST['Property_Name'][$i];
            if (!preg_match('/^[A-Za-z0-9]+$/', $propname)) {
                Template::errorDialog(
                    sprintf(_('Wrong name for property "%s"'), $propname),
                            $_SERVER['PHP_SELF']
                );
                exit;
            }
			$prop->setName($propname);
			$prop->setDisplayName($_REQUEST['Property_DisplayName'][$i]);
			$prop->setType($_REQUEST['Property_Type'][$i]);
            saveInstance($prop, $_SERVER['PHP_SELF']);
			$productType->addProperty($prop);

	    }
	}
    if (Tools::isException($productType)) {
        Template::errorDialog($productType->getMessage(), $retURL);
        exit;
    }
	if ($_REQUEST['PropertyToAdd']) {
		$newProperty = Object::load('Property');
		$newProperty->setName(sprintf("untitled%s",
                count($productType->getPropertyArray(false)) + 1));
	} else {
        saveInstance($productType, $_SERVER['PHP_SELF']);
        if (Database::connection()->hasFailedTrans()) {
            trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
            Database::connection()->rollbackTrans();
            Template::errorDialog($errorBody, $retURL);
            exit;
        }
        Database::connection()->completeTrans();
        if (isset($_SESSION['ProductType'])) {
            unset($_SESSION['ProductType']);
        }
        Tools::redirectTo('ProductTypeList.php');
        exit;
	}
}

// Assignation des variables au formulaire avec smarty
$smarty = new Template();
$smarty->assign('FormAction', $_SERVER['PHP_SELF']);
$smarty->assign('retURL', $retURL);
// On assigne l'objet à la page
$smarty->assign('ProductType', $productType);
$smarty->assign('GenericProductTypeDisabled',
        $productType->getGeneric()?'disabled':"");

$gptOptions = FormTools::writeOptionsFromObject('ProductType',
        $productType->getGenericProductTypeId(), array('Generic'=>1));
$smarty->assign('GenericProductTypeOptions', implode("\n", $gptOptions));

$genericProperties = $productType->getGenericPropertyArray();
$propertyList = $productType->getPropertyArray(false);
if (isset($newProperty)) {
    $propertyList[] = $newProperty;
}
$smarty->assign('GenericPropertyList', array_values($genericProperties));
$smarty->assign('PropertyList', array_values($propertyList));
$smarty->assign('TypeList', Property::getTypeConstArray());

$pageContent = $smarty->fetch('ProductType/ProductTypeAddEdit.html');
Template::page($pageTitle, $pageContent);

?>
