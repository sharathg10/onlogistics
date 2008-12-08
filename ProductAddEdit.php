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
require_once('Objects/SellUnitType.const.php');

// A appeler avant le démarrage de la en session
includeSessionRequirements();

//Database::connection()->debug = 1;
// Authentification
$session = Session::singleton();
$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_OPERATOR));

SearchTools::prolongDataInSession();  // prolonge les données en session

// nettoyage de session de la page ActorProductAddEdit
unset($_SESSION['pdtActorProduct']);

// Messages
$productError = _('Product was not found in the database.');
$productSavedError = _('Product cannot be saved');
$productSaved = _('Product "%s" was successfully modified.');
$titre = _('Add or update product');
$productAlreadyExist = _('A product already exists with this reference "%s", please correct.');

// Determiner le mode ajout ou edition ?
$pdtId = isset($_REQUEST['pdtId'])?$_REQUEST['pdtId']:0;
$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'ProductList.php';

// Variables utilisees plus bas
$productMapper = Mapper::singleton('Product');

if (isset($_SESSION['pdtproduct'])) {
    $product = $_SESSION['pdtproduct'];
    $pdtId = $product->getId();
} else if ($pdtId == 0) {  // si mode ajout le product n'existe pas, on le crée
	$product = new Product();
    $product->generateId();
} else {
    // Chargement du produit en bdd
    $product = $productMapper->load(array('Id' => $pdtId));
}
if (!($product instanceof Product)) {
    Template::errorDialog($productError, $retURL);
    exit;
}

if (isset($_REQUEST['ChangeProductType']) &&
    $_REQUEST['ChangeProductType'] != 0) {
    SearchTools::inputDataInSession(0, 'pdt', false);
    $product->setProductType($_REQUEST['ProductType']);
}

$smarty = new Template();
if ($product->getProductTypeId() == PRODUCT_TYPE_AERONAUTIQUE) {
    $product = $product->mutate('AeroProduct');
    $flyTypeOptions = FormTools::writeOptionsFromObject('FlyType',
        $product->getFlyTypeId());
    $smarty->assign('isAero', 1);
    $smarty->assign('flytypeOptions', implode("\n", $flyTypeOptions));
} else if ($product->getProductTypeId() == PRODUCT_TYPE_RTWPRODUCT) {
    $product = $product->mutate('RTWProduct');
} else if ($product->getProductTypeId() == PRODUCT_TYPE_RTWMATERIAL) {
    $product = $product->mutate('RTWMaterial');
} else {
    if (get_class($product) != 'Product') {
        $product = $product->mutate('Product');
    }
}

$session->register('pdtproduct', $product, 2);

// Soumission du formulaire et sauvegarde des données.
if ((isset($_POST['formSubmitted']) && $_POST['formSubmitted'] != 0)
    || (isset($_POST['ActorProductAction']) && $_POST['ActorProductAction'] != '0')) {
    // Mise en session des saisies
	// params: 0 pour ne pas conserver en session les cases cochees ds
    // ProductList.php
    // 'pdt': pour affecter ce prefixe aux noms de var de session crees
    SearchTools::inputDataInSession(0, 'pdt', false);


    // si un Product existe deja avec ce BaseReference
    $ref = isset($_POST['BaseReference'])?$_POST['BaseReference']:'';
    if ($ref != $product->getBaseReference()
        && $productMapper->alreadyExists(array('BaseReference'=>$ref)))
    {
        Template::infoDialog(sprintf($productAlreadyExist, $ref),
            $_SERVER['PHP_SELF']);
        exit;
    }
    // En mode edition, modif  du tracingMode impossible si Product implique
    // dans une Nomenclature
    if ($product->getId() > 0 && $_POST['TracingMode'] != $product->getTracingMode()
            && !Tools::isEmptyObject($product->getComponentCollection(
                    array(), array(), array('Product')))) {
        Template::errorDialog(_('You cannot change tracing mode for a product involved in a nomenclature'),
                $_SERVER['PHP_SELF']);
        exit;
    }
    
	$product->setProductType(intval($_REQUEST['ProductType']));
	$redirect = isset($_POST['ActorProductAction']) && $_POST['ActorProductAction'] != '0';
	
    // gestion des actions de redirection avant la transaction
    if ($redirect) {
        $url = 'ActorProductAddEdit.php';
        if ($_POST['ActorProductAction'] == 'edit') {
            $url .= '?apID=' . $_POST['ActorProductID'];
        } else if ($_POST['ActorProductAction'] == 'delete') {
            $url .= '?apID=' . $_POST['ActorProductID'] . '&delete=1';
        }
        Tools::redirectTo($url);
        exit;
    }

    // on demarre la transaction
    Database::connection()->startTrans();

    // sauvegarde des infos du produit
    saveProductProperties($product);
    // Gestion des couples ActorProduct
    saveActorProduct($product, $redirect?false:true);
    // Gestion des eventuels ProductSubstitutions a creer
    saveProductSubstitution($product);
    // Gestion des infos du site web
    saveProductDetailsForWebSite($product);
    // sauvegarde du produit
    saveInstance($product, $retURL);

    // On commite la transaction,
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog($productSavedError, $retURL);
        exit;
    }
	Database::connection()->completeTrans();
    // Suppression des var en session
    SearchTools::cleanDataSession('pdt');
    // message d'info
    Template::infoDialog(sprintf($productSaved, $product->getBaseReference()), $retURL);
    exit;
}

// Champs cachés
$smarty->assign('pdtId', $pdtId);
$smarty->assign('formAction', $_SERVER['PHP_SELF']);
$smarty->assign('Image', $product->getImage());
if ($product->getId() == 0) {
    // on est en ajout de produit, donc activé par defaut
	$product->setActivated(1);
}

// Affichage des données du produit
$allProperties = $product->getAllClassProperties();
$excludes = array('Shape', 'PressName', 'Material1', 'Material2', 'Material3', 'Accessory1', 'Accessory2', 'Accessory3');
foreach($allProperties as $name => $class) {
    if (in_array($class, $excludes)) {
        continue;
    }
    unset($options, $filter);
    $getter = 'get' . $name;
    $val = $product->$getter();
    if ($class == Object::TYPE_CONST) { // si attribut de type const
        if (isset($_SESSION['pdt' . $name])) {
            $selectedVal = $_SESSION['pdt' . $name];
        } else {
            $selectedVal = $val;
        }
        if ($name == 'TracingMode' && $selectedVal != Product::TRACINGMODE_LOT) {
            $smarty->assign('TMDisabled', 'disabled');
        }
        $methodName = $getter . 'ConstArray';
        $array = $product->$methodName();
        $options = FormTools::writeOptionsFromArray($array, $selectedVal);
        $smarty->assign($name . 'Options', implode("\n", $options));
    } elseif (is_string($class)) { // si attribut de type Object
        $selectedVal = 0;
        if (isset($_SESSION['pdt' . $name])) { // si cet attribut vient d'etre modifie
            $selectedVal = $_SESSION['pdt' . $name];
        } elseif (method_exists($val, 'getId')) { // sinon, on recupere l'info en bado
            $mapper = Mapper::singleton($class);
            $selectedVal = $val->getId();
        }
        $filter = array();
        // pour SellUnitTypeInContainer il ne faut pas afficher toutes
        // les entrées, seulement UB et UC, on passe donc un filtre
        if ($name == 'SellUnitTypeInContainer') {
            $filter = array('Id' => array(SELLUNITTYPE_UB, SELLUNITTYPE_UC));
        }
        // pour les SellUnitType on met UB par défaut
        if ($name == 'SellUnitType' && !$selectedVal) {
            $selectedVal = SELLUNITTYPE_UB;
        }
        // pour les Owner on met le DataBaseOwner par défaut
        if ($name == 'Owner' && !$selectedVal) {
            $selectedVal = Auth::getDataBaseOwner()->getId();
        }
        $fields = ($class == 'ProductType' || $class == 'Actor')?
            array('Name'):array();
		$sort = ($class == 'ProductType' || $class == 'Actor')?
            array('Name' => SORT_ASC):array();
        $options = FormTools::WriteOptionsFromObject(
                $class, $selectedVal, $filter, $sort, 'toString', $fields);
        $smarty->assign($name . 'Options', implode("\n", $options));
    }

    if (isset($_SESSION['pdt' . $name])) {
        $smarty->assign($name, $_SESSION['pdt' . $name]);
    } else {
        $smarty->assign($name, $val);
    }
}

$ptype = $product->getProductType();
if ($ptype instanceof ProductType) {
    $genericPType = $ptype->getGenericProductType();
    $pdtProperties = Product::getPropertiesByContext();
    $dynproperties = $ptype->getPropertyArray();
    $properties = array();
    $excludes = array('Supplier', 'Price', 'SupplierReference', 'BuyUnitType',
        'Shape', 'PressName', 'Material1', 'Material2', 'Material3',
        'Accessory1', 'Accessory2', 'Accessory3');

    foreach($dynproperties as $name=>$property){
        if (!$property->isDynamic() || in_array($name, $excludes)) {
            continue;
        }
        $dname = $property->getDisplayName();
        $getter = 'get' . $name;
        $value = $product->$getter();
        if ($property->getType() == Property::OBJECT_TYPE) {
            if (is_object($value)) {
                $value = $value->getId();
            }
            $entity = isset($pdtProperties[$name])?
                $pdtProperties[$name]:$name;
            $filter=array();
            $attrs = call_user_func(array($entity, 'getProperties'));
            if(false !== ($p = array_search('ProductType', $attrs))) {
                /* TODO: il faudrait une recursion pour gerer n niveaux de 
                 * Producttypes generiques */
                if ($genericPType instanceof ProductType) {
                    $filter = array($p => array($ptype->getId(), $genericPType->getId()));
                } else {
                    $filter = array($p => $ptype->getId());
                }
            }
            $options = FormTools::WriteOptionsFromObject(
                $entity, $value, $filter);

            $widget = sprintf(
                '<select name="%s" style="width:100%%"><option value="0">N/A</option>%s</select>',
                $name, implode("\n", $options));
        } else {
            $widget = sprintf(
                '<input type="text" name="%s" value="%s" style="width:100%%" />',
                $name, $value);
        }

    	$properties[] = array(
            'name'=>$name,
            'displayName'=>$dname,
            'widget'=>$widget
        );
    }
    $smarty->assign('properties', $properties);
}

// Pour l'etat Active/Desactive
$smarty->assign('ActivatedOptions', array(1, 0));
$smarty->assign('ActivatedOptionsValues', array('Oui', 'Non'));

// fournisseurs
$apCollection = $product->getActorProductCollection();
$count = $apCollection->getcount();

$suppliers = array();
for ($i=0; $i<$count; $i++) {
    $ap = $apCollection->getItem($i);
    $apActor = $ap->getActor();
    $suppliers[] = array(
        'name'       => $apActor->getName(),
        'apid'       => $ap->getId(),
        'appriority' => $ap->getPriority()
    );
}
$smarty->assign('suppliers', $suppliers);

$smarty->assign('returnURL', 'ProductList.php');

// Année du produit vin
if (isset($_SESSION['pdtBaseReference'])) { // si retour pres erreur de saisie
    if (isset($_SESSION['pdtMillesime'])) {
        $smarty->assign('wineyear', $_SESSION['pdtYear'] . '-01-01');
        $smarty->assign('MillesimeChecked', 'checked');
    } else {
        $smarty->assign('MillesimeDisabled', 'disabled');
    }
} else {
    if (($product->getYear() > 0) || (isset($_SESSION['pdtMillesime']))) {
        $Year = (isset($_SESSION['pdtMillesime']))?$_SESSION['pdtYear']:$product->getYear();
        $smarty->assign('wineyear', $Year . '-01-01');
        $smarty->assign('MillesimeChecked', 'checked');
    } else {
        $smarty->assign('MillesimeDisabled', 'disabled');
    }
}

// Display du template
$pageContent = $smarty->fetch('Product/ProductAddEdit.html');
$js = array('js/lib-functions/checkForm.js', 'js/includes/ProductAddEdit.js');
Template::page($titre, $pageContent, $js);

?>
