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

$auth = Auth::singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_OPERATOR, UserAccount::PROFILE_AERO_INSTRUCTOR));

$pdtsNotDeleted = _('The following products could not be deleted, they were just deactivated: <ul><li>%s</li></ul>');
$retURL = 'ProductList.php';

SearchTools::prolongDataInSession();  // prolonge les datas en session

$productId = $_REQUEST['ProductId'];
$DeletableProductId = array();   // contiendra les id des Product a seulement desactiver

Database::connection()->startTrans();
$notDeletedProducts = array();

$ProductMapper = Mapper::singleton('Product');
if (is_array($productId)) {
	foreach($productId as $i => $pdtId) {
			$Product = $ProductMapper->load(array('Id' => $pdtId));
			if (true == $Product->isDeletable()) {  //  Product supprimable
			    $DeletableProductId[] = $pdtId;
			}
			else {
				$Product->setActivated(0);   // on le desactive
                saveInstance($Product, $retURL);
				$notDeletedProducts[] = $Product->getBaseReference();
			}
	}

	if (count($DeletableProductId) > 0) {
	    if (!$ProductMapper->delete($DeletableProductId)) {
	        Template::errorDialog(E_ERROR_IN_EXEC, $retURL);
	    }
	}

	// il faut aussi supprimer les liaisons ProductChainLink
    $ProductChainLinkMapper = Mapper::singleton('ProductChainLink');
    $ProductChainLinkCollection = $ProductChainLinkMapper->loadCollection(
            array('Product' => $productId));
	for($i = 0; $i < $ProductChainLinkCollection->getCount(); $i++){
		unset($ProductChainLink);
		$ProductChainLink = $ProductChainLinkCollection->getItem($i);
        deleteInstance($ProductChainLink, $retURL);
	}
	// et les liaisons ActorProduct
    $APMapper = Mapper::singleton('ActorProduct');
    $APCollection = $APMapper->loadCollection(array('Product' => $productId));
	for($i = 0; $i < $APCollection->getCount(); $i++){
		$APLink = $APCollection->getItem($i);
        deleteInstance($APLink, $retURL);
		unset($APLink);
	}
}

if (Database::connection()->hasFailedTrans()) {
    Database::connection()->rollbackTrans();
    Template::errorDialog(E_ERROR_IN_EXEC, $retURL);
    exit;
}else{
    Database::connection()->completeTrans();
}

if (!empty($notDeletedProducts)) {
    $msg = sprintf($pdtsNotDeleted, implode('</li><li>', $notDeletedProducts));
    Template::infoDialog($msg, $retURL);
	exit;
}

Tools::redirectTo($retURL);

?>
