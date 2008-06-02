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

class WorkOrder extends _WorkOrder {
    // Constructeur {{{

    /**
     * WorkOrder::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    
    /**
     * Retourne la Collection des Command reliees a un OT 
     * @return float Content of value
     * @access public
     */
    function getCommandCollection(){
        $CommandCollection = new Collection();
        $CommandCollection->acceptDuplicate = false;  // pas de doublon
        $this->_ActivatedChainOperationCollectionLoaded = FALSE;
        $ActivatedChainOpeCollection = $this->getActivatedChainOperationCollection();
        for($i = 0; $i < $ActivatedChainOpeCollection->getCount(); $i++){
            $ActivatedChainOperation = $ActivatedChainOpeCollection->getItem($i);
            $CommandId = Tools::getValueFromMacro($ActivatedChainOperation, '%ActivatedChain.CommandItem[0].Command.Id%');
            $Command = Object::load('Command', $CommandId);  // 1 seule commande liee a 1 ActivatedChain
            $CommandCollection->setItem($Command);
            unset($ActivatedChainOperation, $Command);
        }
        return $CommandCollection;
    }
    
    /**
     * Retourne la collection des ActivatedChainOperation liees au WO, dont l'ActivatedMovement
     * n'est pas execute totalement. 
     * @return boolean
     * @access public
     */
    function getOperationCollectionToDupplicate(){
        require_once('Objects/ActivatedChainOperation.inc.php');
        require_once('Objects/ActivatedMovement.php');
        $ACOToDupplicateCollection = new Collection();
        $ACOToDupplicateCollection->acceptDuplicate = false;  // pour ne pas avoir de doublon ds cette collection
        // l'attribut de tri a pour unique but de forcer le chargemt en base
        $ACOCollection = $this->getActivatedChainOperationCollection(array(), array('Id' => SORT_ASC));

        if ($ACOCollection instanceof Collection && ($ACOCollection->getCount() > 0)) {
            for($i = 0; $i < $ACOCollection->getCount(); $i++){
                $ACO = $ACOCollection->getItem($i);  // c'est une ActivatedChainOperation
                if (! IsTransportOperation($ACO)) {   // si ce n'est pas un transport
                    continue;
                }
                else {
                    $Chain = $ACO->getActivatedChain();
                    $CommandItemCollection = $Chain->getCommandItemCollection();
                    
                    for($j = 0; $j < $CommandItemCollection->getCount(); $j++){
                        $CommandItem = $CommandItemCollection->getItem($j);
                        // Seule ProductCommandItem possede un ActivatedMovement, pas CommandItem
                        if (!($CommandItem instanceof ProductCommandItem)) {
                            continue;
                        }
                        $ActivatedMovementState = Tools::getValueFromMacro($CommandItem, '%ActivatedMovement.State%');
                        if (in_array($ActivatedMovementState, array(ActivatedMovement::CREE, ActivatedMovement::ACM_EN_COURS, 
                                                                    ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT, ActivatedMovement::BLOQUE))) {
                            /*  Une livraison au moins n'est pas totale => il faudra duppliquer l'operation  */
                            $ACOToDupplicateCollection->setItem($ACO);
                        }
                    }
                
                }
            }
        }
        else return new Exception('Erreur' . ': ' . _('No operation found.'));
        
        return $ACOToDupplicateCollection;
    }
    
    /**
     * Retourne le nbre total de colis et le poids total de ces colis
     * @access public
     * @return array Tableau associatif:
     * array('PackingNumber' => $PackingNumber, 
     *         'PackingWeight' => $PackingWeight, 
     *         'isForecast' =>$isForecast);
     **/
    function getTotalPackingNumberAndWeight() {
        require_once('Objects/ActivatedChainOperation.inc.php');
        $isForecast = false;  // Ce n'est pas une prevision
        $TotalPackingNumber = $TotalPackingWeight = 0;
        $ACOCollection = $this->getActivatedChainOperationCollection();
        for($i = 0; $i < $ACOCollection->getCount(); $i++) {
            $ACOperation = $ACOCollection->getItem($i);
            if (! IsTransportOperation($ACOperation)) {  // si ce n'est pas un transport
                unset($ACOperation);
                continue;
            }
            else {
                $PackingInfos = $ACOperation->getPackingNumberAndWeight();
                $TotalPackingNumber += $PackingInfos['PackingNumber'];
                $TotalPackingWeight += $PackingInfos['PackingWeight'];
                $isForecast = ($isForecast)?true:$PackingInfos['isForecast']; 
                unset($ACOperation);
            }
        }
        return array('PackingNumber' => $TotalPackingNumber, 
                     'PackingWeight' => $TotalPackingWeight,
                     'isForecast' => $isForecast);
    }

}

?>