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

require_once('Quantificator.php');

/** 
 * Quantificateur pour les taches d'assemblages.
 * Contrairement aux autres quantificateurs celui-ci parcours les taches dans 
 * l'ordre inverse.
 * Il a pour but d'assigner une quantité attendue aux taches d'assemblages de 
 * la chaine activée.
 * 
 * @package    lib
 * @subpackage Quantificator
 */
class AssemblyQuantificator extends Quantificator
{
    // properties {{{

    /**
     *
     * @var    integer $quantity
     * @access protected
     */
    protected $quantity = 1;

    // }}} 
    // AssemblyQuantificator::execute() {{{

    /**
     * AssemblyQuantificator::execute()
     * 
     * @return boolean
     */
    public function execute($order=SORT_ASC, $limitToAco=false, $limitToAck=false)
    {
        // on ne traite que les commandes de produits
        if (!($this->command instanceof ProductCommand)) {
            return false;
        }
        // si aucune tache d'assemblage, pas la peine d'aller plus loin
        // s'il y a plus d'une tache d'assemblage c'est la *dernière* qui est 
        // retournée dans la variable $ack (car on a passé le paramètre 
        // $reverseOrder à true)
        if (!($ack = $this->activatedChain->hasTaskOfType(
            array(TASK_INTERNAL_STOCK_EXIT, TASK_ASSEMBLY, TASK_SUIVI_MATIERE), true)))
        {
            return false;
        }
        // flag déterminant si un produit compatible a été trouvé
        $found = false;
        // le composant lié à la tache d'assemblage
        $cpn = $ack->getComponent();
        if ($cpn instanceof Component) {
            // la collection de commanditems de la chaine
		    $cmiCol = $this->activatedChain->getCommandItemCollection();
            // pour chaque commanditem on récupère le produit et on vérifie s'il 
            // est compatible avec l'assemblage et si on en trouve un compatible on 
            // assigne la quantité commandée de commanditem comme qté de référence 
            if ($cmiCol instanceof Collection) {
                $count = $cmiCol->getCount();
                for($i = 0; $i < $count; $i++) {
                    $cmi = $cmiCol->getItem($i);
                    $pdt = $cmi->getProduct();
                    if (!($pdt instanceof Product)) {
                        continue;
                    }
                    // le produit correspond t-il au composant à la nomenclature 
                    // de la tache d'assemblage ?
                    if ($cpn->getProductId() == $pdt->getId()) {
                        $found = true;
                        $this->quantity = $cmi->getQuantity();
                        // pas la peine de continuer on a notre produit
                        break;
                    }
                } 
            } 
        }
        // si aucun produit n'est valide pour l'assemblage -> on sort
        //if (!$found) {
        //    return false;
        //}
        // on appelle la boucle du quantificateur dans le sens inverse
        return parent::execute(SORT_DESC);
    }

    // }}}
    // AssemblyQuantificator::processTask() {{{
    
    /**
     * AssemblyQuantificator::processTask()
     * 
     * Determine la quantité de la tache d'assemblage en cours.
     * Note importante: les taches sont parcourues dans l'ordre *inverse* de 
     * leur ordre dans la chaine.
     * 
     * @access public 
     * @param object ActivatedChainTask la tache en cours
     * @return void
     */
    protected function processTask($ack)
    {
        if (in_array($ack->getTaskId(), array(TASK_ASSEMBLY, TASK_SUIVI_MATIERE))) {
            // pour chaque tache d'assemblage on récupère la quantité courante
            $cpn = $ack->getComponent();
            $currentQty = $cpn->getQuantityInHead() * $this->quantity;
            $ack->setAssembledQuantity($currentQty);
        } else if ($ack->getTaskId() == TASK_INTERNAL_STOCK_EXIT &&
            Tools::getValueFromMacro($ack, 
                '%RessourceGroup.AddNomenclatureCosts%') == '1') {
            // tache sortie interne, calcul du coût uniquement si spécifié dans 
            // le modèle de ressource
            $cpnCol = $ack->getComponentCollection();
            $count = $cpnCol->getCount();
            $cost  = 0;
            for ($i=0; $i<$count; $i++) {
                $cpn = $cpnCol->getItem($i);
                $currentQty = $cpn->getQuantityInHead(true) * $this->quantity;
                $pdt = $cpn->getProduct();
                // on ajoute le prix 
                if ($pdt instanceof Product) {
                    $cost += $currentQty * $pdt->getUVPrice();
                }
            }
            $ack->setCost($ack->getCost() + $cost);
        }
    }

    // }}}
}

?>
