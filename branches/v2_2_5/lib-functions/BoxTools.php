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

require_once('Objects/Box.php');
require_once('Objects/Task.const.php');
require_once('Objects/SellUnitType.const.php');

/**
 * Crée les Box initiaux à la commande.
 * Les crée uniquement si la commande comporte une tâche créatrice de Box.
 * La référence est volontairement non renseignée, vu que ce sont des Box de 
 * niveau 1 qui sont créées.
 * 
 * @access public
 * @param object $activatedChain une chaîne activée
 * @param object $commandItem un (Chain/Product)CommandItem
 * @return boolean true si des boxes ont été crées et false sinon
 **/
function createInitialBoxes($activatedChain, $commandItem){
    if (!$activatedChain->hasBoxCreatorTask() || 
         $activatedChain->hasTaskOfType(TASK_STOCK_EXIT)) {
        // si la chaîne ne comporte pas de tache créatrice de Box, ou si elle 
        // comporte une tache de sortie de stock (les box seront créées à la  
        // validation de la tache), pas la peine d'aller plus loin. 
        // On ne crée aucun Box.
        return false;
    }
    if ($commandItem instanceof ProductCommandItem) {
        $product = $commandItem->getProduct();
        $covertype = false;
        $ref = $product->getBaseReference();
    } else {
        $covertype = $commandItem->getCoverTypeId();
        $ref = false;
    }
    // determiner le nombre de boxes à créer
    $boxnumArray = explode('.', strval($commandItem->getQuantity()));
    $boxnum = $boxnumArray[0];
    if (count($boxnumArray) == 2) { // qté décimale
        $boxnum++;
    }
    // création des entités
    for($i = 0; $i < $boxnum; $i++){
    	$box = new Box();
        $box->generateId();
        $box->setReference(false==$ref?$box->getId():$ref);
        $box->setLevel(1);
        $box->setActivatedChain($activatedChain);
        $box->setCommandItem($commandItem);
        // ATTENTION ceci est différent de $commandItem->getVolume()
        $box->setVolume($commandItem->getWeight()*$commandItem->getHeight()*$commandItem->getWidth());
        // si c'est le dernier, que l'uv est au poids et que la quantité est
        // décimale on met la quantité restante dans le dernier box
        if ($i == $boxnum -1 && isset($product) 
            && $product->getSellUnitTypeId() > SELLUNITTYPE_UR 
            && isset($boxnumArray[1]) && $boxnumArray[1] != 0)
        {
            $remainingQty = floatval('0.' . $boxnumArray[1]);
            $box->setWeight($commandItem->getWeight() * $remainingQty);
        } else {
            $box->setWeight($commandItem->getWeight());
        }
        if (false != $covertype) {
            $box->setCoverType($covertype);
        }
        $command = $commandItem->getCommand();
        if ($command instanceof Command) {
            $box->setExpeditor($command->getExpeditor());
            $box->setExpeditorSite($command->getExpeditorSite());
            $box->setDestinator($command->getDestinator());
            $box->setDestinatorSite($command->getDestinatorSite());
        }
        $box->save();
    }
    // retourne true si au moins une box a été créee
    return ($boxnum > 0);
}

?>
