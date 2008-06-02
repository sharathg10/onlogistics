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

class ProductCommandItem extends _ProductCommandItem {
    // Constructeur {{{

    /**
     * ProductCommandItem::__construct()
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
     * Addons ProductCommandItem
     * pour assurer la compatibilité au niveau des addons de command
     */

    /**
     *
     * @access public
     * @return void
     */
    function getWidth()
    {
        $Product = $this->GetProduct();
        return $Product->GetSellUnitWidth();
    }

    /**
     *
     * @access public
     * @return void
     */
    function getLength()
    {
        $Product = $this->GetProduct();
        return $Product->GetSellUnitLength();
    }

    /**
     *
     * @access public
     * @return void
     */
    function getHeight()
    {
        $Product = $this->GetProduct();
        return $Product->GetSellUnitHeight();
    }

    /**
     *
     * @access public
     * @return void
     */
    function getWeight()
    {
        $Product = $this->GetProduct();
        return $Product->GetSellUnitWeight();
    }

    /**
     *
     * @access public
     * @return void
     */
    function getGerbability()
    {
        $Product = $this->GetProduct();
        return $Product->GetSellUnitGerbability();
    }

    /**
     *
     * @access public
     * @return void
     */
    function getMasterDimension()
    {
        $Product = $this->GetProduct();
        return $Product->GetSellUnitMasterDimension();
    }

    /**
     * Retourne le volume du produit lié
     *
     * @access public
     * @return float
     **/
    function getVolume(){
        $product = $this->getProduct();
        $volume  = $product->getVolume();
        if (0 == $volume) {
            $volume = $product->getSellUnitWidth() *
                $product->getSellUnitLength() *
                $product->getSellUnitHeight();
        }
        return $this->getQuantity() * $volume;
    }

    /**
     * Retourne le producttype du produit lié
     *
     * @access public
     * @return float
     **/
    function getProductType(){
        $product = $this->getProduct();
        if ($product instanceof Product) {
            return $product->getProductType();
        }
        $return = false;
        return $return;
    }

    /**
     * Methode AddOn pour creer un activatedMovement et mettre a jour
     * la qte virtuelle en stock de product associe
      * retourne un tableau de strings vide si pas d'alerte qd on met a jour la qte virtuelle
      * ou contenant le body du ou des mails d'alerte a envoyer
     *
     * @access public
     * @return array
     */
    function CreateActivatedMovement() {
        require_once('Objects/Task.const.php');
        require_once("Objects/Command.php");
        require_once("Objects/Command.const.php");
        require_once('MovementType.const.php');
        $Command = $this->getCommand();


        $CommandType = $Command->GetType();
        $ActivatedChain = $this->getActivatedChain();

        // Dans ce cas, on ne cree pas d'ActivatedMovement et tout ce qui s'en suit (MAJ qtes...)
        if (($CommandType == Command::TYPE_SUPPLIER && !$ActivatedChain->hasTaskOfType(TASK_STOCK_ENTRY))
         || ($CommandType == Command::TYPE_CUSTOMER && !$ActivatedChain->hasTaskOfType(TASK_STOCK_EXIT))) {
            return;
        }


        require_once('Objects/ActivatedMovement.php');
        $ActivatedMovement = new ActivatedMovement();

        /* Mise en commentaire provisoire, tant que pas cable avec la plannification
        $ActivatedMovement->SetEndDate(Tools::getValueFromMacro($this, "%Command.WishedEndDate%")); */
        $ActivatedMovement->setState(0);
        // id du Product commande
        $CommandItemProductID = Tools::getValueFromMacro($this, "%Product.Id%");
        $MvtTypeMapper = Mapper::singleton('MovementType');

        // role Fournisseur => entree
        if ($CommandType == Command::TYPE_SUPPLIER) {
            $mvtActivatedChainTask = $ActivatedChain->hasTaskOfType(TASK_STOCK_ENTRY);
            $MovementType = $MvtTypeMapper->load(array('Id'=>ENTREE_NORMALE));
        }
        // role Client => sortie
        elseif ($CommandType == Command::TYPE_CUSTOMER) {
            $mvtActivatedChainTask = $ActivatedChain->hasTaskOfType(TASK_STOCK_EXIT);
            $MovementType = $MvtTypeMapper->load(array('Id'=>SORTIE_NORMALE));
        }
        else {
            trigger_error('ProductCommand' . $Command->getCommandNo()
                    . ' avec le Type invalide suivant: ' . $CommandType, E_USER_ERROR);
            Template::errorDialog(E_MSG_TRY_AGAIN, 'home.php');
            exit;  // stoppe la transaction
            //return false;
        }

        $ActivatedMovement->setStartDate($mvtActivatedChainTask->getBegin());
        $ActivatedMovement->setEndDate($mvtActivatedChainTask->getEnd());
        $ActivatedMovement->setActivatedChainTask($mvtActivatedChainTask);
        $ActivatedMovement->setType($MovementType);
        $ActivatedMovement->setQuantity($this->getQuantity());
        $ActivatedMovement->setProductCommandItem($this);
        $ActivatedMovement->setProduct($this->getProduct());
        $ActivatedMovement->setProductCommand($this->getCommand());
        $AlertMailData = $ActivatedMovement->setProductVirtualQuantity(); // mise a jour de la qte virtuelle
        $ActivatedMovement->save();

        $this->setActivatedMovement($ActivatedMovement);
        return ($AlertMailData);
    }

}

?>