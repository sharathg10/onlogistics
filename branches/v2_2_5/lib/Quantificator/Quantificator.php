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

require_once('ActivatedChainIterator.php');
require_once('Objects/Task.inc.php');
require_once('Objects/ActivatedChainTask.php');
require_once('Objects/RessourceRessourceGroup.php');
require_once('FormatNumber.php');

/**
 * Quantificator
 * Classe de base des autres quantificateurs.
 * 
 * @package    lib
 * @subpackage Quantificator
 */
class Quantificator extends ActivatedChainIterator
{
    // properties {{{

    /**
     * Command instance
     *
     * @var    object Command $command
     * @access protected
     */
    protected $command;

    /**
     * Determine si les calculs doivent être faits sur les données réelles (après 
     * validation des taches).
     *
     * @var    boolean real
     * @access protected
     */
    protected $real;

    // }}}
    // constructor {{{

    /**
     * Constructeur
     *
     * @access public
     */
    function __construct($ach, $cmd, $real=false)
    {
        parent::__construct($ach);
        $this->command = $cmd;
        $this->real = $real;
    }

    // }}}
    // Quantificator::processTask() {{{

    /**
     * Quantifie la tache en cours (prix et durée).
     *
     * @access public
     * @param  object ActivatedChainTask $ack
     * @return void
     */
    protected function processTask($ack)
    {
        // durée
		$mult = $this->getDurationMultiplicator($ack->getDurationType(), $ack);
        $ack->setDuration(ceil($mult * $ack->getRawDuration()));
        // coût
        $rsg = $ack->getRessourceGroup();
        $logger = Tools::loggerFactory();
        if ($rsg instanceof RessourceGroup) {
            // si on gère les ressources via ActivityBasedcosting
            $cost = 0;
            $rrgCol = $rsg->getRessourceRessourceGroupCollection();
            $count = $rrgCol->getCount();
            for ($i=0; $i<$count; $i++) {
                $rrg = $rrgCol->getItem($i);
                $res = $rrg->getRessource();
                // type de coût, on recherche le multiplicateur
		        $mult = $this->getCostMultiplicator($res->getCostType(), $ack, true);
                $product = $res->getProduct();
                if ($product instanceof Product) {
                    // on a une ressource produit... on va chercher le prix d'achat
                    $resCost = $product->getUVPrice() * $res->getQuantity();
                } else {
                    $resCost = $res->getCost();
                }
                // taux d'utilisation dans la tache, un pourcentage
                $cost += $resCost * $mult * ($rrg->getRate()/100);
            }
        } else {
            // quantification classique du coût
		    $mult = $this->getCostMultiplicator($ack->getCostType(), $ack);
            $cost = $mult * $ack->getRawCost();
        }
        $setter = $this->real?'setRealCost':'setCost';
        $ack->$setter(troncature($cost));
        // passe la tâche à avec prévision
        $ack->setWithForecast(true);
        return parent::processTask($ack);
    } 

    // }}}
     // Quantificator::getDurationMultiplicator() {{{

    /**
     * Retourne le multiplicateur pour le calcul de la durée en fonction
     * du type de durée passé en paramètre.
     * Méthode surchargée dans les classes filles.
     *
     * @access protected
     * @param  int $durationType
     * @return mixed
     */
    protected function getDurationMultiplicator($durationType, $ack)
    {
        return 1;
    }

    // }}}
    // Quantificator::getCostMultiplicator() {{{

    /**
     * Retourne le multiplicateur pour le calcul du coût en fonction du type
     * de coût passé en paramètre.
     *
     * @access protected
     * @param  int $costType
     * @param  object ActivatedChainTask $ack
     * @param  boolean $forRessource si on veut le multiplicateur pour une 
     * ressource ou pour une quantification classique (false par défaut).
     * @return mixed
     */
    protected function getCostMultiplicator($costType, $ack, $forRessource=false)
    {
        return 1;
    } 

    // }}}
}

?>
