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
//Database::connection()->debug= true;
/**
 * Session et authentification
 **/
$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_DIR_COMMERCIAL));

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'ActorList.php';

/**
 * Messages
 **/
$errorTitle = E_ERROR_TITLE;
$errorBody  = _('Selected actors cannot be deleted');
$noActorSelected = _('Please select at least one item.');
$actorsNotDeleted = _('The following actors could not be deleted, they were just deactivated: <ul><li>%s</li></ul>');
$permissionDenied = _('The following actors could not be deleted because you are not allowed to do so: <ul><li>%s</li></ul>');
$actorsNotTouched = _('The following actors cannot be deleted nor deactivated: <ul><li>%s</li></ul>');
$severalPdtsWithoutSupplier = _('Several active products have no main supplier.');
$pdtsWithoutSupplier = _('The following active products have no main supplier: <ul><li>%s</li></ul>');

/**
 * Début de la transaction
 **/
Database::connection()->startTrans();

if (!isset($_REQUEST['actId'])) {
    Template::errorDialog($noActorSelected, $retURL);
	exit;
}

$notDeletedActors = array();
$notDeletedActorsBecauseOfPerms = array();
$intactActors = array();  // Acteurs ne pouvant etre supprimes ou desacives
$activePdtWithoutActiveMainSupplier = array();
$severalPdtsWithoutActiveMainSupplier = false;
$mapper = Mapper::singleton('Actor');
$actorCol = $mapper->loadCollection(array('Id'=>$_REQUEST['actId']));
$count = $actorCol->getCount();

for($i = 0; $i < $count; $i++) {
	$actor = $actorCol->getItem($i);

    if ($auth->getProfile() == UserAccount::PROFILE_COMMERCIAL) {
        // blindage sur le commercial, il ne peut supprimer que les acteurs
        // dont il est commercial
        if ($actor->getCommercialId() != $auth->getUserId()) {
            $notDeletedActorsBecauseOfPerms[] = $actor->getName();
            continue;
        }
    }
    // l'acteur n'est pas supprimable, ni desactivable
    if ((!Tools::isEmptyObject($actor->getStorageSiteCollection()))
            || !Tools::isEmptyObject($actor->getStoreCollection())) {
        $intactActors[] = $actor->getName();
    } elseif (!$actor->isDeletable()) {
        // l'acteur n'est pas supprimable, on le désactive uniquement
        $actor->setActive(false);
        $notDeletedActors[] = $actor->getName();
        saveInstance($actor, $retURL);
        $pdtColl = $actor->isMainSupplier(true);
        foreach($pdtColl as $pdt) {
            $activePdtWithoutActiveMainSupplier[] = $pdt->getBaseReference();
            // Si plus de 10 Products dans ce cas, on ne les liste pas
            if (count($activePdtWithoutActiveMainSupplier) > 10) {
                $severalPdtsWithoutActiveMainSupplier = true;
                break;
            }
        }
        $actor->removeMainSupplierLinks(); // Met les ActorProduct.Priority a 0
    } else {
        // on supprime ses sites
        $siteCol = $actor->getSiteCollection();
        $jcount = $siteCol->getCount();
        for ($j=0; $j<$jcount; $j++) {
            $site = $siteCol->getItem($j);
            try {
                deleteInstance($site, $retURL);
            } catch (Exception $exc) {
                // l'acteur n'est pas supprimable, ni desactivable
                $intactActors[] = $actor->getName() . ' (' . $exc->getMessage() . ')';
                // passe à l'acteur suivant
                continue 2;
            }
        }
        // supprime l'acteur
        deleteInstance($actor, $retURL);
    }
}

if (Database::connection()->hasFailedTrans()) {
    trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    Database::connection()->rollbackTrans();
    Template::errorDialog($errorBody, $retURL);
	exit;
}
Database::connection()->completeTrans();

/**
 * Si tous les acteurs n'ont pas pu être supprimés, on en informe l'utilisateur
 * sinon on redirige vers la liste des acteurs.
 **/
$msg = '';
if (!empty($notDeletedActors)) {
    $msg = sprintf($actorsNotDeleted, implode('</li><li>', $notDeletedActors));
}
if (!empty($notDeletedActorsBecauseOfPerms)) {
    $msg .= sprintf($permissionDenied,
        implode('</li><li>', $notDeletedActorsBecauseOfPerms));
}
if (!empty($intactActors)) {
    $msg .= sprintf($actorsNotTouched, implode('</li><li>', $intactActors));
}
if ($severalPdtsWithoutActiveMainSupplier) {
    $msg .= '<br>' . $severalPdtsWithoutSupplier;
}
elseif (!empty($activePdtWithoutActiveMainSupplier)) {
    $msg .= sprintf($pdtsWithoutSupplier,
        implode('</li><li>', $activePdtWithoutActiveMainSupplier));
}

if (!empty($msg)) {
    Template::infoDialog($msg, $retURL);
	exit;
}

Tools::redirectTo($retURL);

?>