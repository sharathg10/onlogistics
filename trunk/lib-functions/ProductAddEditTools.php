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

require_once('FormatNumber.php');

/**
 * includeSessionRequirements()
 * Fait les require_once nécessaires à la mise en session dans ProductAddEdit
 *
 * @access public
 * @return void
 **/
function includeSessionRequirements(){
    require_once('Objects/Product.php');
    require_once('Objects/ActorProduct.php');
    require_once('Objects/ProductSubstitution.php');
    require_once('Objects/ProductType.inc.php');
    require_once('Objects/Property.inc.php');
    require_once('Objects/AeroProduct.php');
    require_once('Objects/ProductType.php');
    require_once('Objects/Property.php');
    require_once('Objects/PropertyValue.php');
    require_once('Objects/Supplier.php');
    require_once('Objects/SellUnitType.php');
    require_once('Objects/TVA.php');
    require_once('Objects/Container.php');
    require_once('Objects/FlyType.php');
    require_once('Objects/Currency.php');
    require_once('Objects/PriceByCurrency.php');
//    require_once('Objects/Image.php');
}

 /**
 * saveProductProperties()
 * sauve les informations du produit passé en paramètre avec les données POST
 * du formulaire.
 *
 * @access public
 * @param  object Product
 * @return void
 **/
function saveProductProperties($product){
    // Attributs pour lesquels on doit gerer les saisies de la forme
    // xx,yyy ou xx.yyy (3 decimales)
    $decimalAttributes = array(
        'Volume',
        'SellUnitLength',
        'SellUnitWidth',
        'SellUnitHeight',
        'SellUnitWeight'
    );

    foreach($product->getAllClassProperties() as $field => $type) {
		if ($field == 'ProductType') {
		    continue;
		}
        $setter = 'set' . $field;
        if (isset($_POST[$field])) {
			if (in_array($field, $decimalAttributes)) {
                $product->$setter(troncature($_POST[$field], 3));
			} else {
                // XXX stripslashes ??
                $product->$setter(stripslashes($_POST[$field]));
            }
        }
    }
    if (!isset($_POST['Millesime'])) { // si case Millesime non cochee
        $product->setYear(null);
    }
    // bug #2487: supprimer les propertyvalue résiduelles après un changement
    // de type de produit
    $pdtID = $product->getId();
    if ($pdtID > 0) {
        require_once('SQLRequest.php');
        $pdtType = $product->getProductType();
        if ($pdtType instanceof ProductType) {
            $pdtTypeIdArray = array($pdtType->getId());
            $genericPType = $pdtType->getGenericProductType();
            if ($genericPType instanceof ProductType) {
                $pdtTypeIdArray[] = $genericPType->getId();
            }
            request_cleanPropertyValues($pdtID, $pdtTypeIdArray);       
        }
    }
}

/**
 * saveProductPriceByCurrency()
 * crée et sauve les PriceByCurrency pour le produit passé en
 * paramètre à partir des données POST du formulaire.
 *
 * @access public
 * @param  object Product
 * @return void
 **/
function saveProductPriceByCurrency($product){
    require_once('Objects/PriceByCurrency.php');
    if (!isset($_POST['Currencies']) || !is_array($_POST['Currencies'])) {
        return false;
    }
    $mapper = Mapper::singleton('PriceByCurrency');
    foreach($_POST['Currencies'] as $id){
    	$pbc = $mapper->load(
            array('Product'=>$product->getId(), 'Currency'=>$id));
        // si ce couple n'existe pas on le crée
        if (!($pbc instanceof PriceByCurrency)) {
            $pbc = new PriceByCurrency();
            $pbc->setProduct($product->getId());
            $pbc->setCurrency($id);
        }
        // $_POST['Currency_' . $id] est le champs contenant le prix
        $pbc->setPrice(troncature($_POST['Price_' . $id]));
        saveInstance($pbc, 'ProductList.php');
    }
    return true;
}

/**
 * saveActorProduct()
 * Sauve les ActorProduct et leurs PriceByCurrency liés au produit passé en
 * paramètre.
 *
 * @access public
 * @param  object Product
 * @return void
 **/
function saveActorProduct($product, $save=false){
    $apCollection = $product->getActorProductCollection();
    $count = $apCollection->getcount();
    for ($i=0; $i<$count; $i++) {
        $ap = $apCollection->getItem($i);
        $ap->setProduct($product->getId());
        if ($save) {
            saveInstance($ap, 'ProductList.php');
        }
        $pbcCollection = $ap->getPriceByCurrencyCollection();
        $jcount = $pbcCollection->getCount();
        for ($j=0; $j<$jcount; $j++) {
            $pbc = $pbcCollection->getItem($j);
            if ($save) {
                saveInstance($pbc, 'ProductList.php');
            }
        }
    }
    foreach($apCollection->removedItems as $id) {
        $inst = Object::load('ActorProduct', $id);
        if ($inst instanceof ActorProduct) {
            deleteInstance($inst, 'ProductList.php');
        }
    }
}

/**
 * saveProductSubstitution()
 * crée et sauve les éventuelles substitutions pour le produit passé en
 * paramètre à partir des données de session.
 *
 * @access public
 * @param  object Product
 * @return void
 **/
function saveProductSubstitution($product){
    if (!isset($_SESSION['PdtSubstCollection'])) {
        return false;
    }
    $mapper = Mapper::singleton('ProductSubstitution');
    $col = unserialize($_SESSION['PdtSubstCollection']);
    $count = $col->getCount();
    if ($count > 0) {
        for($i = 0; $i < $count; $i++) {
            $item = $col->getItem($i);
            $item->setFromProduct($product);
            saveInstance($item, 'ProductList.php');
        }
    }
    return true;
}

function saveProductDetailsForWebSite($product) {
    // gestion de l'image
    if (isset($_POST['NoImage']) && $_POST['NoImage'] == 1) {
        $no_image = 0;
        $product->setImage($no_image);
        $imageMapper = Mapper::singleton('Image');
        $img = $imageMapper->load(
            array('UUID'=>md5($product->getBaseReference())));
        if ($img instanceof Image) {
            deleteInstance($img, 'ProductList.php');
        }
    } else if (isset($_FILES['Product_Image_File']) &&
        !empty($_FILES['Product_Image_File']['name'])) {
		// gestion de l'upload de l'image
        $man = new ImageManager('Product_Image_File');
		$man->resize();
        $img = $man->dbstore(true, $product->getBaseReference());
		$product->setImage($product->getBaseReference());
	}
	// gestion de la description
	if(!empty($_POST['Product_Description'])) {
	    $product->setDescription($_POST['Product_Description']);
	}
}
?>
