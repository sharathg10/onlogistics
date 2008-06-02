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

require_once("config.inc.php");

/**
 * Authentification
 */
$session = Session::Singleton();
$auth = Auth::Singleton();
// a changer PERM_CHAINLIST (qd scenario auth sera développé)
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW), 
    array('showErrorDialog' => true));

SearchTools::ProlongDataInSession(); // prolonge les datas en session

$retURL = isset($_REQUEST["retURL"])?$_REQUEST["retURL"]:'AffectationList.php';

if (!isset($_REQUEST["pclId"])) {
    Template::errorDialog(_("Please select products to unassign from chain"), $retURL);
} 

$url = 'pclId[]=' . implode('&pclId[]=', $_REQUEST["pclId"]);

if (!isset ($_REQUEST['OK'])) {
    $title = _("Are you sure you want to unassign selected products ?");
    $message = _("Are you sure you want to unassign selected products from chain ?");
    $okLink = "UnAffectProductFromChain.php?OK=1&" . $url;
    $cancelLink = "AffectationList.php";
    Template::confirmDialog($message, $okLink, $cancelLink);
    Exit;
} 


$productMapper = Mapper::singleton('Product');


$pclMapper = Mapper::singleton('ProductChainLink');

$pclCollection = $pclMapper->loadCollection(array('Id'=>$_REQUEST['pclId'])); 
// parcours de la collection de produits
$pdtArray = array();
$pdtNameArray = array();
$chnArray = array();

for($i = 0; $i < $pclCollection->GetCount(); $i++) {
    unset($pcl);
    $pcl = $pclCollection->GetItem($i);
    $aProduct = $pcl->GetProduct();
    if ($aProduct instanceof Product) {
        $pdtArray[] = $aProduct;
        $pdtNameArray[$aProduct->GetId()] = $aProduct->GetName();
        $aChain = $pcl->GetChain();
        $chnArray[$aChain->GetId()] = $aChain->GetReference();
        $pclMapper->delete($pcl->GetId()); 
        // on regarde si le produit n'est plus affecté à aucune chaine
    } 
} 

for($i = 0; $i < count($pdtArray); $i++) {
	$product = $pdtArray[$i];
    $ids = $product->getProductChainLinkCollectionIds();
    if (false == $ids || (is_array($ids) && count($ids) == 0)) {
		$affected = 0;
        $product->setAffected($affected);
        saveInstance($product, $retURL);
    } 
} // for

if (count($pdtArray) == 0) {
    Template::infoDialog('<p>'._('None of selected products were assign to a chain.').'</p>', $retURL);
    Exit;
} else {
    Template::infoDialog(_('Product(s)') . ' <b><ul><li>'
         . implode('</li><li>', $pdtNameArray)
         . '</li></ul></b> ' . _('was (were) unassigned from chain(s)') 
         . ' <b><ul><li>'
         . implode('</li><li>', $chnArray) . '</li></ul></b>', $retURL);
    Exit;
} 

?>
