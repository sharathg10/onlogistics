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

class ActivatedMovement extends _ActivatedMovement {
    // Constructeur {{{

    /**
     * ActivatedMovement::__construct()
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
     * Met a jour l'attribut SellUnitVirtualQuantity du Product commande
     * retourne un tableau de strings vide si pas d'alerte
     * ou contenant le body du ou des mails d'alerte a envoyer
     * @access public
     * @return array contenant les donnees des mails d'alerte eventuels
     **/
    function setProductVirtualQuantity() {
        require_once('Alert.const.php');
        $AlertArray = array();
        $ProductCommandItem = $this->getProductCommandItem();
        // si pas de command item on récupère le product via acm::getProduct()
        if (!($ProductCommandItem instanceof CommandItem)) {
            $Product = $this->getProduct();
        } else {
            $Product = $ProductCommandItem->getProduct();
        }

        $initVirtualQuantity = $Product->getSellUnitVirtualQuantity();
        $EntrieExit = Tools::getValueFromMacro($this, '%Type.EntrieExit%');
        $qty = $this->getQuantity();

        if ($EntrieExit == 0) { // entree
            $Product->setSellUnitVirtualQuantity($initVirtualQuantity + $qty);
        } else {  // sortie
            $Product->setSellUnitVirtualQuantity($initVirtualQuantity - $qty);
            if ($initVirtualQuantity - $qty <= 0) {
                $AlertArray = array(ALERT_STOCK_QV_REACH_ZERO, $Product);
            } elseif ($initVirtualQuantity - $qty
                        <= $Product->getSellUnitMinimumStoredQuantity()) {
                $AlertArray = array(ALERT_STOCK_QV_MINI, $Product);
            }
            if ($Product->getRealQuantity() < $qty) {
                $AlertArray = array(ALERT_INSUFFICIENT_STOCK, $Product);
            }
        }
        $Product->save();
        return ($AlertArray);
    }

    /**
     * Methode AddOn pour creer un ExecutedMovement
     * avec les infos disponibles juste avant l'execution
     *
     * @param $TotalRealQuantity int : Qty reelle
     * @param $product_id int : Product reel
     * @param $State int : etat de l'Executed Movement,
     * par defaut  1: Mvt EXECUTE TOTALEMENT
     * sinon,      2: Mvt EXECUTE PARTIELLEMENT
     * @return Object ExecutedMovement
     * @access public
     */
    function CreateExecutedMovement($TotalRealQuantity, $product_id, $Comment="", $State=1){
        //
        require_once('Objects/ExecutedMovement.php');


        $ExecutedMovement = new ExecutedMovement();
        $ExecutedMovement->setStartDate(date("Y-m-d H-i-s"));
        $ExecutedMovement->setEndDate(date("Y-m-d H-i-s"));
        $ExecutedMovement->setState($State);      // etat EXECUTE ou PARTIEL
        $ExecutedMovement->setComment($Comment);
        $ExecutedMovement->setType($this->getType());

        // Produit prevu par l'ActivatedMovement
        /*
        $InitialProductId = Tools::getValueFromMacro($this, "%ProductCommandItem.Product.Id%");
        $ProductMapper = Mapper::singleton('Product');
        $Product = $ProductMapper->load(Array('Id' => $InitialProductId));
        */
        $ProductMapper = Mapper::singleton('Product');
        $Product = $ProductMapper->load(Array('Id' => $product_id));

        //
        $ExecutedMovement->setRealProduct($Product);
        //$ExecutedMovement->setRealQuantity(Tools::getValueFromMacro($this, "%ProductCommandItem.Quantity%"));
        $ExecutedMovement->setRealQuantity($TotalRealQuantity);
        $ExecutedMovement->setActivatedMovement($this);
        return $ExecutedMovement;
    }


    /**
     *
     * @access public
     * @return void
     * retourne l'executedMovement eventuel d'un ActivatedMovement
     **/
    function getExecutedmovement(){

        $exmMapper = Mapper::singleton('ExecutedMovement');
        $exm = $exmMapper->load(
            array("ActivatedMovement"=>$this->getId()));
        if ($exm instanceof ExecutedMovement && $exm->getId() > 0) {
            return $exm;
        }
        return false;
    }

    /**
     * Retourne true ou false si l'ACM est lie, via le EXM, ï¿½une collection
     * non vide de LEM tels que:
     * non annulateurs, ni annules, non factures, ayant une Quantity non nulle,
     * et qque soit le Product  mouvemente dans ces LEM
     * @access public
     * @return boolean
     **/
    function hasLEMnotFactured() {
        $EXMovement = $this->getExecutedMovement();
        $filterComponentArray = array();
        $filterComponentArray[] = SearchTools::newFilterComponent(
                'InvoiceItem', '', 'Equals', 0, 1);
        $filterComponentArray[] = SearchTools::newFilterComponent(
                'Cancelled', '', 'Equals', 0, 1);
        // ici, 'Quantity2' car il existe un input text du meme nom!!
        $filterComponentArray[] = SearchTools::newFilterComponent(
                'Quantity2', 'Quantity', 'NotEquals', 0, 1);
        $filter = SearchTools::filterAssembler($filterComponentArray);
        $LEMCollection = $EXMovement->getLocationExecutedMovementCollection($filter);

        return (!Tools::isEmptyObject($LEMCollection));
    }

    /**
     * Recupere la quantité restant a mouvementer, pour E/S normale
     *
     * @access public
     * @return int Quantity
     */
    function getRemainingQuantity() {
        $partialQuantity = 0;
        // Si l'ACM est au dela de l'etat ActivatedMovement::CREE
        if ($this->getState() != ActivatedMovement::CREE) {
            $EXMMapper = Mapper::singleton('ExecutedMovement');
            $PartialEXM = $EXMMapper->load(array('ActivatedMovement' => $this->getId()));
            if (!Tools::isEmptyObject($PartialEXM)) {
                $partialQuantity = $PartialEXM->getRealQuantity();
            }
        }
        
        return $this->getQuantity() - $partialQuantity;
    }

    /**
     * Recupere la quantité en suivant le bon chemin :
     * SI mouvement de stock interne alors:
     *      SI ACM.Component:
     *          SI ENTREE_INTERNE:
     *              qty = AssembledQty de la tache précédente d'assemblage
     *          SI MovementType::TYPE_EXIT INTERNE:
     *              qty = AssembledQty de la tache suivante d'assemblage * qty
     *                    de component
     *      SINON
     *          qty = ACM.ProductCommand.ProductCommandItem.Quantity
     * SINON:
     *      qty = ACM.ProductCommandItem.quantity
     *
     * @access public
     * @return int Quantity
     
    function getQuantity() {
        require_once('Objects/MovementType.const.php');
        $mvtTypeId = $this->getTypeId();
        $quantity = 0;
        // Si mouvement interne
        if($mvtTypeId == ENTREE_INTERNE || $mvtTypeId == SORTIE_INTERNE) {
            return parent::getQuantity();
        }
        else {
            // Retourne ACM.ProductCommandItem.quantity
            $quantity = Tools::getValueFromMacro($this, '%ProductCommandItem.Quantity%');
        }
        return $quantity;
    }
*/

}

?>