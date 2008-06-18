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
require_once('Objects/Box.php');
require_once('Objects/ActivatedChainTask.php');

define('E_NO_ACK', _('Selected tasks could not be found in the database.'));

// auth
$auth = Auth::singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, 
    	UserAccount::PROFILE_TRANSPORTEUR, UserAccount::PROFILE_GESTIONNAIRE_STOCK));

SearchTools::ProlongDataInSession();

// url de retour
$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'GroupableBoxList.php';

// si aucune box n'a été sélectionnée -> message d'erreur
if (!isset($_REQUEST['gridItems'])) {
    Template::errorDialog(I_NEED_SELECT_ITEM, $retURL);
    exit;
}
if (!is_array($_REQUEST['gridItems'])) {
    $_REQUEST['gridItems'] = array($_REQUEST['gridItems']);
}

// si aucune box n'a été sélectionnée -> message d'erreur
if (!isset($_REQUEST['ackIDs'])) {
    Template::errorDialog(E_NO_ACK, $retURL);
    exit;
}

// demarrage de la transaction
Database::connection()->startTrans();

// on charge une collection de tâches
$ackMapper = Mapper::singleton('ActivatedChainTask');
$ackCol = $ackMapper->loadCollection(array('Id'=>$_REQUEST['ackIDs']));


/**
 * XXX DIRTY HACK
 * ici on va parcourir les taches passées en paramètre pour savoir si elles sont 
 * bien toutes impliquées dans le regroupement. En effet la cynétique ne permet 
 * pas vraiment de ne passer que des taches impliquées, ex:
 * 
 * ECRAN 1: on selectionne 2 taches (A et B)
 * ECRAN 2: 
 *      - on affiche *toutes* les box des taches A et B
 *      - on selectionne 2 boxes de la tache A
 * ECRAN 3: 
 *      Il faut donc enlever la tache B passée en paramètre, car aucune box lui 
 *      correspondant n'est présente...
 * 
 * Tant qu'on est dans la boucle, on en profite pour déterminer si on doit ou 
 * non editer une packinglist automatiquement, c'est le cas si la tache suivant 
 * la tache en cours est une tache d'édition de PL avec un mode de déclenchement 
 * (triggermode) automatique.
 **/
$toRemove = array();
$generatePL = false;
$count = $ackCol->getCount();
for($i = 0; $i < $count; $i++){
	$ack = $ackCol->getItem($i);
    $boxcol = $ack->getGroupableBoxCollection();
    if (!($boxcol instanceof Collection)) {
        $toRemove[] = $i;
        continue;
    }
    $boxids = $boxcol->getItemIds();
    $intersect = array_intersect($_REQUEST['gridItems'], $boxids);
    if (empty($intersect)) {
        $toRemove[] = $i;
    } else {
        $tsk = $ack->getNextTask(array(ActivatedChainTask::TRIGGERMODE_AUTO), 
            'isPackingListEditionTask');
        if ($tsk instanceof ActivatedChainTask) {
            $generatePL = true;
        }
    }
}

// supprimer les taches qui n'ont rien à faire ici
if (!empty($toRemove)) {
	$ackCol->removeItem($toRemove);
} // for

// on crée le box contenant
$parentBox = new Box();
$parentBox->generateId();
$ref = isset($_REQUEST['boxReference']) && !empty($_REQUEST['boxReference'])?
    $_REQUEST['boxReference']:$parentBox->getId();
$parentBox->setReference($ref);
if (isset($_REQUEST['boxDate'])) {
    $parentBox->setDate($_REQUEST['boxDate']);
}
if (isset($_REQUEST['boxComment'])) {
    $parentBox->setComment($_REQUEST['boxComment']);
}
if (isset($_REQUEST['boxCoverType'])) {
    $parentBox->setCoverType($_REQUEST['boxCoverType']);
}
if (isset($_REQUEST['boxVolume'])) {
    $parentBox->setVolume($_REQUEST['boxVolume']);
}
if (isset($_REQUEST['boxDimensions'])) {
    $parentBox->setDimensions($_REQUEST['boxDimensions']);
}
if (isset($_REQUEST['boxExpeditor'])) {
    $parentBox->setExpeditor($_REQUEST['boxExpeditor']);
}
if (isset($_REQUEST['boxExpeditorSite'])) {
    $parentBox->setExpeditorSite($_REQUEST['boxExpeditorSite']);
}
if (isset($_REQUEST['boxDestinator'])) {
    $parentBox->setDestinator($_REQUEST['boxDestinator']);
}
if (isset($_REQUEST['boxDestinatorSite'])) {
    $parentBox->setDestinatorSite($_REQUEST['boxDestinatorSite']);
}

// on charge la collection des boxes passées en paramètre
$mapper = Mapper::singleton('Box');
$col = $mapper->loadCollection(array('Id'=>$_REQUEST['gridItems']));
$higherLevel = $totalWeight = $totalVolume = 0;
$count = $col->getCount();

// pour chaque box on assigne notre parent box
for($i = 0; $i < $count; $i++){
	$box = $col->getItem($i);
    $box->setParentBox($parentBox->getId());
    saveInstance($box, $retURL);
    if ($box->getLevel() > $higherLevel) {
        $higherLevel = $box->getLevel();
    }
    $totalWeight += $box->getWeight();
	$totalVolume += $box->getVolume();
}
if (!isset($_REQUEST['boxVolume']) || $_REQUEST['boxVolume'] == false ) {
    $parentBox->setVolume($totalVolume);
}
// on assigne un level supérieur à la parent box
$parentBox->setActivatedChainTaskCollection($ackCol);
$parentBox->setLevel($higherLevel + 1);
$parentBox->setWeight($totalWeight);
saveInstance($parentBox, $retURL);

// commit de la transaction
Database::connection()->completeTrans();

// mise à jour de l'état des taches, "en cours" s'il reste des box à regrouper 
// pour la tache, "terminé" sinon
$count = $ackCol->getCount();
for($i = 0; $i < $count; $i++){
    $item = $ackCol->getItem($i);
    $col = $item->getGroupableBoxCollection();
    $item->setState($col instanceof Collection && $col->getCount() > 0?
        ActivatedChainTask::STATE_IN_PROGRESS : ActivatedChainTask::STATE_FINISHED);
    saveInstance($item, $retURL);
}

// génération de la packing list
$redirect = 'GroupableBoxActivatedChainTaskList.php';
if ($generatePL) {
	echo "
<script type=\"text/javascript\">
window.open(\"PackingListEdit.php?fromParent=1&boxId=" . $parentBox->getId() . 
    "\"," . "\"popback\", \"width=800, height=600, toolbars=no, " . 
    "scrollbars=no, menubars=no, status=no\");
location.href = \"".$redirect."\";
</script>";
} else {
    Tools::redirectTo($redirect);
}

?>
