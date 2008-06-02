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
require_once('Objects/Chain.php');
require_once('Objects/ProductChainLink.php');

$session = Session::Singleton();
$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

SearchTools::ProlongDataInSession();

if (!isset($_REQUEST['ChnId']) && !isset($_SESSION['ChnId'])) {
    Template::errorDialog(_('Please select a chain.'), 'ProductList.php');
    exit;
}

$ChnID = isset($_REQUEST['ChnId'])?$_REQUEST['ChnId']:$_SESSION['ChnId'];
$session->register('ChnId', $ChnID, 2);

if (!$_SESSION['ProductId']) {
    /**
     * Impossible de trouver les ids des produits dans la session, on redirige
     * vers la liste des produtis
     */
    Tools::redirectTo('ProductList.php?' . SID);
    exit;
}
$session->prolong('ProductId', 1);

// mapper de productChainLink
$pclMapper = Mapper::singleton('ProductChainLink');

// tableau des acteurs de début et de fins des chaines liées aux produits 
// sélectionnés
$actorTuples = array();
$existingLinkCol = $pclMapper->loadCollection(
    array('Product'=>$_SESSION['ProductId']));
$count = $existingLinkCol->getCount();
for ($i=0; $i<$count; $i++) {
    $existingLink = $existingLinkCol->getItem($i);
    $pdtID = $existingLink->getProductId();
    if (!isset($actorTuples[$pdtID])) {
        $actorTuples[$pdtID] = array();
    }
    $actorTuples[$pdtID][] = array(
        Tools::getValueFromMacro($existingLink, '%Chain.SiteTransition.DepartureActorId%'),  
        Tools::getValueFromMacro($existingLink, '%Chain.SiteTransition.ArrivalActorId%')
    );
}

// collection de chaines
$mapper = Mapper::singleton('Chain');
$chainCol = $mapper->loadCollection(array('Id' => $ChnID));
$count = $chainCol->getCount();

// collection de produits
$pdtMapper = Mapper::singleton('Product');
$pdtCol = $pdtMapper->loadCollection(array('Id' => $_SESSION['ProductId']));
$jcount = $pdtCol->getCount();

// tabeaux de messages
$affected    = array();
$notAffected = array();
$affectationErrors = array();

Database::connection()->startTrans();

for($i = 0; $i < $count; $i++) {
    $chain = $chainCol->getItem($i);
    $chainID = $chain->getId();
    $chainRef = $chain->getReference();
    $ast = $chain->getSiteTransition();
    if ($ast instanceof ActorSiteTransition) {
        $dActor = $ast->getDepartureActorId();
        $aActor = $ast->getArrivalActorId();
    } else {
        $dActor = $aActor = 0;
    }
    if ($chain->getState() < Chain::CHAIN_STATE_BUILT) {
        Template::errorDialog(sprintf(
            _('Selected products could not be affected to chain "%s": chain is not constructed.'),
            $chain->getReference()), 'ProductList.php');
        exit;
    }

    for($j = 0; $j < $jcount; $j++){
        $pdt = $pdtCol->getItem($j);
        $pdtID = $pdt->getId();
        $pdtRef = $pdt->getBaseReference();
        $alreadyExists = $pclMapper->alreadyExists(
                array('Product' => $pdtID, 'Chain' => $chainID));
        if ($alreadyExists) {
            if (!isset($notAffected[$pdtRef])) {
                $notAffected[$pdtRef] = array();
            }
            $notAffected[$pdtRef][] = $chainRef;
            continue;
        }
        if (!isset($actorTuples[$pdtID])) {
            $actorTuples[$pdtID] = array();
        }
        $currentTuple = array($dActor, $aActor);
        if (in_array($currentTuple, $actorTuples[$pdtID])) {
            if (!isset($affectationErrors[$pdtRef])) {
                $affectationErrors[$pdtRef] = array();
            }
            $affectationErrors[$pdtRef][] = $chainRef;
            continue;
        }
        $pcl = new ProductChainLink();
        $pcl->setProduct($pdtID);
        $pcl->setChain($chainID);
        saveInstance($pcl, 'ProductList.php');
        $pdt->setAffected(true);
        saveInstance($pdt, 'ProductList.php');
        if (!isset($affected[$pdtRef])) {
            $affected[$pdtRef] = array();
        }
        $affected[$pdtRef][] = $chainRef;
        $actorTuples[$pdtID][] = array($aActor, $dActor);
    }

}
Database::connection()->completeTrans();

$msg = '';
if (!empty($affectationErrors)) {
    $msg .= '<p>' ._('The following assignments were ignored because a chain with the same departure and arrival actors is already assigned to selected product(s).').':<ul>';
    foreach($affectationErrors as $pdtRef => $chainArray) {
        foreach($chainArray as $chainRef) {
    	    $msg .= '<li>'.sprintf(_('product %s to chain %s'), $pdtRef, $chainRef);
        }
    }
    $msg .= '</ul></p>';
}
if (!empty($notAffected)) {
    $msg .= '<p>' ._('The following assignments were ignored because they already exist.').':<ul>';
    foreach($notAffected as $pdtRef => $chainArray) {
        foreach($chainArray as $chainRef) {
    	    $msg .= '<li>'.sprintf(_('product %s to chain %s'), $pdtRef, $chainRef);
        }
    }
    $msg .= '</ul></p>';
}
if (!empty($affected)) {
    $msg .= '<p>'._('The following assignments were successfully saved').':<ul>';
    foreach($affected as $pdtRef => $chainArray) {
        foreach($chainArray as $chainRef) {
    	    $msg .= '<li>'.sprintf(_('product %s to chain %s'), $pdtRef, $chainRef);
        }
    }
    $msg .= '</ul></p>';
}
unset($_SESSION['ChnId'], $_SESSION['ProductId']);
Template::infoDialog($msg, 'ProductList.php');
exit();

?>
