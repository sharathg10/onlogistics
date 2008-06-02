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

class ExecutedMovement extends _ExecutedMovement {
    // Constructeur {{{

    /**
     * ExecutedMovement::__construct()
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
     * methode addons
     * retourne la collection de Location à partir de ExecutedMovement
     * @return object Location Collection Content of value
     * @access public
     */
    function getLocationCollection()
    {
        $LEMCollection = $this->getLocationExecutedMovementCollection();
        if (LocationExecutedMovementCollection instanceof Collection) {
            $LocationCollection = new Collection();
            $count = $LEMCollection->getCount();
            for($i = 0; $i < $count; $i++) {
                $item = $LEMCollection->getItem($i);
                $LocationCollection->setItem($item->getLocation());
                unset($item);
            }
            return $LocationCollection;
        }
        return false;
    }

/**
 * Met a jour l'attribut SellUnitVirtualQuantity du Product commande et envoie 1 alerte si besoin
 * Cette methode est utilisee UNIQUEMENT en cas de mouvement NON PREVU (pas d'ActivatedMovement associe)
 * ou de mouvement DIFFERENT de l'ActivatedMovement prevu associe!!
 * En cas de mvt prevu:
 * La QV du produit commande est MAJ uniquemt qd le mvt est totalement execute.
 * La QV des produits de substitution est MAJ a chaque mvt sur un pdt qui n'est pas celui commande.
 * @access public
 * @return mixed : 0 si pas d'alerte sur le stock
 *                    array(MailTitle, MailBody) si une alerte est a envoyer
 **/
    function setProductVirtualQuantity() {
        require_once('Alert.const.php');
        $AlertArray = array(); // contiendra si necessaire les alertes a envoyer
        $EntrieExit = Tools::getValueFromMacro($this, '%Type.EntrieExit%');
        $ActivatedMovement = $this->getActivatedMovement();
        // Pas d'ActivatedMovement associe
        if (Tools::isEmptyObject($ActivatedMovement, 'ActivatedMovement')) {
            // le produit reellement impacte
            $Product = $this->getRealProduct();
            $initVirtualQuantity = $Product->getSellUnitVirtualQuantity();
            if ($EntrieExit == 0) { // entree
                $Product->setSellUnitVirtualQuantity($initVirtualQuantity + $this->getRealQuantity());
                }
            else {                  // sortie
                $Product->setSellUnitVirtualQuantity($initVirtualQuantity - $this->getRealQuantity());
                if ($initVirtualQuantity - $this->getRealQuantity() <= 0) {
                    $AlertArray[] = Object::load('Alert', ALERT_STOCK_QV_REACH_ZERO);
                    }
                elseif ($initVirtualQuantity - $this->getRealQuantity() <= $Product->getSellUnitMinimumStoredQuantity()) {
                    $AlertArray[] = Object::load('Alert', ALERT_STOCK_QV_MINI);
                    }
                }
            }
        else { // il existe un ActivatedMovement associe
            $Quantity = Tools::getValueFromMacro($ActivatedMovement, '%Quantity%');
            $ProductCommandItem = $ActivatedMovement->getProductCommandItem();
            $Product = $ActivatedMovement->getProduct();    // produit commande
            $initVirtualQuantity = $Product->getSellUnitVirtualQuantity();

            /* si le mvt est totalement execute, et qte de Product_commande mvtee != Qte commandee,
            * on met a jour la Qte virtuelle du Product commande */
            if (ExecutedMovement::EXECUTE_TOTALEMENT == $this->getState()
                    && ($this->getOrderedProductMovedQuantity() != $Quantity)) {
                if ($EntrieExit == 0) { // entree
                    $Product->setSellUnitVirtualQuantity($initVirtualQuantity + ($this->getOrderedProductMovedQuantity() - $Quantity));
                    if ($initVirtualQuantity + ($this->getOrderedProductMovedQuantity() - $Quantity) <= 0) {
                        $AlertArray[] = array(ALERT_STOCK_QV_REACH_ZERO, $Product);
                    }
                    elseif ($initVirtualQuantity + $this->getOrderedProductMovedQuantity() <= $Product->getSellUnitMinimumStoredQuantity()) {
                        $AlertArray[] = array(ALERT_STOCK_QV_MINI, $Product);
                    }
                }
                else {                  // sortie
                    $Product->setSellUnitVirtualQuantity($initVirtualQuantity - ($this->getOrderedProductMovedQuantity() - $Quantity));
                }
            }

            // Il faut s'occuper maintenant des autres Products (non commandes),
            // mais seulement les derniers mouvementes!!
            $LEMMapper = Mapper::singleton('LocationExecutedMovement');
            // Filter: On ne prend que les LEM correspondant au dernier mvt effectue pour l'EXM
            $Filter = array('ExecutedMovement' => $this->getId(), 'Date' => $this->getEndDate());
            $LEMCollection = $LEMMapper->loadCollection($Filter);
            $OtherProductQuantityInEXM = array();   // Qte des autres Produits mouvementees dans ce EXM

            if (!Tools::isEmptyObject($LEMCollection)) {
                $OrderedProductId = $Product->getId();  // Product commande
                for($i = 0; $i < $LEMCollection->getCount(); $i++) {
                    $LEM = $LEMCollection->getItem($i);
                    $LEMProductId = $LEM->getProductId();
                    // si produit mouvemente != produit commande
                    if ($OrderedProductId != $LEMProductId) {
                        $OtherProductQuantityInEXM[$LEMProductId] =
                                    (!isset($OtherProductQuantityInEXM[$LEMProductId]))?$LEM->getQuantity():
                                    $OtherProductQuantityInEXM[$LEMProductId] + $LEM->getQuantity();
                    }
                    unset($LEM);
                }
            }
            // s'il y a, lors des derniers mvts, des LEM avec un Product != celui commande
            if (count($OtherProductQuantityInEXM) > 0) {
                foreach($OtherProductQuantityInEXM as $key => $MovedQuantity) {
                    $RealProduct = Object::load('Product', $key);
                    $initRealProductVirtualQuantity = $RealProduct->getSellUnitVirtualQuantity();

                    if ($EntrieExit == 0) { // entree
                        $RealProduct->setSellUnitVirtualQuantity($initRealProductVirtualQuantity + $MovedQuantity);
                    }
                    else {  // sortie
                        $RealProduct->setSellUnitVirtualQuantity($initRealProductVirtualQuantity - $MovedQuantity);
                        if ($initRealProductVirtualQuantity - $MovedQuantity <= 0) {
                            $AlertArray[] = array(ALERT_STOCK_QV_REACH_ZERO, $RealProduct);
                        }
                        elseif ($initRealProductVirtualQuantity -
                                $MovedQuantity <= $RealProduct->getSellUnitMinimumStoredQuantity()) {
                            $AlertArray[] = array(ALERT_STOCK_QV_MINI, $RealProduct);
                        }
                    }
                    $RealProduct->save();
                    unset($RealProduct);
                }
            }
        }
        $Product->save();

        return $AlertArray;
    }


    /**
     * Retourne la date a partir de laquelle il faut regarder s'il faut editer un BL
     * @access public
     * @param $DateSup: date apres laquelle les LocationExecutedMovements ne
     * conviennent pas pour une edition: date par defaut
     * @return string : soit une date, soit "0"
     **/
    function searchDateForEditBL($DateSup='') {

        if (Tools::getValueFromMacro($this, '%Type.Foreseeable%') != 1) {
            die(_('Error: movement is without forecast, delivery order can not be edited'));
        } else {
        // Si un BL anterieur a deja ete edite pour la commande liee au ExecutedMovt,
        // on recupere sa date.
            $DeliveryOrderMapper = Mapper::singleton('DeliveryOrder');

            if ($DateSup == '') {    // si edition, et non pas reedition
                $DeliveryOrderCollection =
                        $DeliveryOrderMapper->loadCollection(
                            array('Command'=> Tools::getValueFromMacro(
                                    $this, '%ActivatedMovement.ProductCommand.Id%')),
                            array('EditionDate' => SORT_DESC), 1);
                if (($DeliveryOrderCollection instanceof Collection)
                        && ($DeliveryOrderCollection->getCount() == 1)) {
                    $DeliveryOrder = $DeliveryOrderCollection->getItem(0);
                    return $DeliveryOrder->getEditionDate();
                    }
                }
            else {                    // si reedition
                $DeliveryOrderCollection =
                        $DeliveryOrderMapper->loadCollection(
                            array('Command' => Tools::getValueFromMacro(
                                    $this, '%ActivatedMovement.ProductCommand.Id%')),
                            array('EditionDate' => SORT_DESC));

                if (($DeliveryOrderCollection instanceof Collection)
                        && ($DeliveryOrderCollection->getCount() > 0)) {
                    for($i = 0; $i < $DeliveryOrderCollection->getCount(); $i++) {
                        $DeliveryOrder = $DeliveryOrderCollection->getItem($i);
                        if ($DateSup > $DeliveryOrder->getEditionDate()) {
                            return $DeliveryOrder->getEditionDate();
                            } // on retourne la premiere date d'edition de BL
                        }     // inferieure a celle du BL a reediter
                    }
                }
            }
        return "0";    // si pas de BL anterieur trouve
    }

    /**
     * Retourne les LocationExecutedMovement a prendre en compte pour editer un BL
     * On ne prend pas les LEM annulateurs
     * @access public
     * @param $DateSup: date apres laquelle les LocationExecutedMovements ne
     *        conviennent pas:
     *          - pour une edition: date par defaut
     *          - pour une reedition: EditionDate du BL qu'on veut reediter
     * @param boolean $withoutCancelled, si true les LEM qui ont été annulés ne
     *        seront pas pris en compte. DEPRECATED
     * @return LocationExecutedMovement collection
     **/
    function getLocationExecutedMovementForBL($DateSup='', $withoutCancelled=false){

        if ($DateSup == '') {
            $DateSup = date('Y-m-d H:i:s');    // date du jour
        }
        if (Tools::getValueFromMacro($this, '%Type.Foreseeable%') != 1) {
            Template::errorDialog(
                _('An error occurred: the delivery order will not be printed because movement in progress is not expected.'), 
                'home.php');
            exit;
        } else {
            // si $DateInf=0: il n'existe pas de BL pour l'instant, donc tous
            // les LocExMovt "comptent"
            $DateInf = $this->searchDateForEditBL($DateSup);

            $filter = new FilterComponent();
            $filter->setItem(new FilterRule('Cancelled',
                    FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                    0));
            $filter->setItem(new FilterRule('Date',
                    FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                    $DateSup));
            $filter->setItem(new FilterRule('Date',
                    FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                    $DateInf));
            $filter->operator = FilterComponent::OPERATOR_AND;

            $LEMCollection = $this->getLocationExecutedMovementCollection($filter);

            /*if (!Tools::isEmptyObject($LEMCollection)) {
                // tableau des Id des Items a supprimer si anterieurs a la date
                // $Date
                $ArrayToDelete = array();
                $count = $LEMCollection->getCount();
                for($i = 0; $i < $count; $i++){
                    $LEM = $LEMCollection->getItem($i);
                    $lemDate = $LEM->getDate();
                    /* en reedition dateinf peut etre egale à zero, mais il peut y
                    // avoir des bl edites aprés, il ne faut compter leurs lem
                    if($DateInf == "0") {
                        if($lemDate > $DateSup || ($withoutCancelled && $LEM->getCancelled() > 0)) {
                            $ArrayToDelete[] = $i;
                        }
                    } else {
                        if($lemDate <= $DateInf || $lemDate > $DateSup
                                || ($withoutCancelled && $LEM->getCancelled() > 0)) {
                            $ArrayToDelete[] = $i;
                        }
                    }
                }
                if (count($ArrayToDelete) > 0) {
                    $LEMCollection->removeItem($ArrayToDelete);
                }
            }*/
        }
        return $LEMCollection;
    }  // end function


    /**
     * Retourne les LocationExecutedMovement anterieurs a la date passee en param
     * @access public
     * @param $DateSup: date apres laquelle les LocationExecutedMovements ne
     * conviennent pas pour une reedition: EditionDate du BL qu'on veut reediter
     * @return LocationExecutedMovement collection
     **/
    function getLocationExecutedMovementAtThisTime($DateSup='') {
        $LEMMapper = Mapper::singleton('LocationExecutedMovement');
        $filter = new FilterComponent();
        $filter->setItem(new FilterRule('ExecutedMovement',
                FilterRule::OPERATOR_EQUALS,
                $this->getId()));
        if ($DateSup != '') {
            $filter->setItem(new FilterRule('Date',
                    FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                    $DateSup));
            $filter->operator = FilterComponent::OPERATOR_AND;
        }
        $LEMCollection =  $LEMMapper->loadCollection($filter);
        return $LEMCollection;
    }  // end function


    /**
     * Retourne la quantite mouvementee de Product, qte non reintegree,
     * pour un Produuit donne
     * @access public
     * @param $Product Object Product
     * @param $isFactured integer: si -1, on n'en tient pas compte,
     *                                si >= 0, filtre supplementaire
     * @return float
     **/
    function getProductMovedQuantity($Product, $isFactured=-1) {
        $LEMMapper = Mapper::singleton('LocationExecutedMovement');
        $ProductId = $Product->getId();

        $FilterComponentArray = array(); // Tableau de filtres
        $FilterComponentArray[] = SearchTools::newFilterComponent(
                'ExecutedMovement', '', 'Equals', $this->getId(), 1);
        $FilterComponentArray[] = SearchTools::newFilterComponent(
                'Product', '', 'Equals', $ProductId, 1);
        if ($isFactured == 0) {
            $FilterComponentArray[] = SearchTools::newFilterComponent(
                    'InvoiceItem', '', 'Equals', 0, 1);
        }elseif ($isFactured == 1) {
            $FilterComponentArray[] = SearchTools::newFilterComponent(
                    'InvoiceItem', '', 'NotEquals', 0, 1);
        }
        $filter = SearchTools::filterAssembler($FilterComponentArray);

        $LEMCollection = $LEMMapper->loadCollection($filter);
        $ProductMovedQuantity = 0;

        if (!Tools::isEmptyObject($LEMCollection)) {
            $count = $LEMCollection->getCount();
            for($i = 0; $i < $count; $i++){
                $LocationExecutedMovement = $LEMCollection->getItem($i);
                // si annulateur, on decremente
                if ($LocationExecutedMovement->getCancelled() > 0) {
                    $ProductMovedQuantity -= $LocationExecutedMovement->getQuantity();
                }
                else {
                    $ProductMovedQuantity += $LocationExecutedMovement->getQuantity();
                }
            }
        }
        return $ProductMovedQuantity;
    }

    /**
     * Retourne la quantite mouvemente de Product commande, non reintegree
     * @access public
     * @return float
     **/
    function getOrderedProductMovedQuantity() {
        // Product commande
        $OrderedProductId = Tools::getValueFromMacro($this, '%ActivatedMovement.Product.Id%');
        $Product = Object::load('Product', $OrderedProductId);
        return $this->getProductMovedQuantity($Product);
    }


    /**
     * Retourne la collection des LEM de livraison (c a d qui ne sont pas
     * des reintegrations ou annulations)
     * @access public
     * @param $dateSup string Date au format 'Y-m-d H:i:s': borne superieure (facultative)
     * @return object LocationExecutedMovementCollection
     */
    function getDeliveredLocationExecutedMovtCollection($dateSup="") {
        $LEMMapper = Mapper::singleton('LocationExecutedMovement');
        $filter = new FilterComponent();
        $filter->setItem(new FilterRule('ExecutedMovement',
                                      FilterRule::OPERATOR_EQUALS,
                                     $this->getId()));
         $filter->setItem(new FilterRule('CancelledMovement',
                                      FilterRule::OPERATOR_EQUALS,
                                     0));
        if ($dateSup != "") {
            $filter->setItem(new FilterRule('Date',
                                             FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                                            $dateSup));
        }
         $filter->operator = FilterComponent::OPERATOR_AND;
         $LEMCollection =  $LEMMapper->loadCollection($filter);
         return $LEMCollection;
    }

    /**
     * Retourne la collection des LEM annulateurs (c a d qui sont des reintegrations
     * ou annulations)
     * @access public
     * @param $dateSup string Date au format "Y-m-d H:i:s": borne superieure (facultative)
     * @return object LocationExecutedMovementCollection
     */
    function getCancellerLocationExecutedMovtCollection($dateSup="") {
        $LEMMapper = Mapper::singleton('LocationExecutedMovement');
        $filter = new FilterComponent();
        $filter->setItem(new FilterRule('ExecutedMovement',
                FilterRule::OPERATOR_EQUALS,
                $this->getId()));
         $filter->setItem(new FilterRule('CancelledMovement',
                FilterRule::OPERATOR_GREATER_THAN,
                0));
        if ($dateSup != "") {
            $filter->setItem(new FilterRule('Date',
                FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                $dateSup));
        }
         $filter->operator = FilterComponent::OPERATOR_AND;

        $LEMCollection =  $LEMMapper->loadCollection($filter);
        return $LEMCollection;
    }

    /**
     * Retourne la qte livree et la qte reintegree jusqu'a une date donnee
     * @access public
     * @param $dateSup string Date au format "Y-m-d H:i:s": borne superieure (facultative)
     * @return array of integer
     **/
    function getProductQtyCancelledAndDelivered($dateSup="") {
        $DeliveredQuantity = $CancelledQuantity = 0;
        $LEMCollection = $this->getLocationExecutedMovementAtThisTime($dateSup);
        if (!Tools::isEmptyObject($LEMCollection)) {
            $count = $LEMCollection->getCount();
            for($i = 0; $i < $count; $i++){
                $LocationExecutedMovement = $LEMCollection->getItem($i);
                // si pas mvt annulateur
                if ($LocationExecutedMovement->getCancelledMovementId() == 0) {
                    $DeliveredQuantity += $LocationExecutedMovement->getQuantity();
                }
                else {  // si mvt annulateur
                    $CancelledQuantity += $LocationExecutedMovement->getQuantity();
                }
            }
        }
        return array($DeliveredQuantity, $CancelledQuantity);
    }

    /**
     * Pour gérer le fait qu'on peut avoir des mouvements internes avec ou sans
     * prévision: teste s'il existe un ACM associé.
     *
     * @access public
     * @return boolean
     */
    function isForeseeable() {
        if (!in_array($this->getTypeId(), array(ENTREE_INTERNE, SORTIE_INTERNE))) {
            return Tools::getValueFromMacro($this, '%Type.Foreseeable%');
        }
        return (!Tools::isEmptyObject($this->getActivatedMovement()));
    }

}

?>