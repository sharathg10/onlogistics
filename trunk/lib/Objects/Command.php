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

class Command extends _Command {
    // Constructeur {{{

    /**
     * Command::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Command::getTotalSurface() {{{

    /**
     * getTotalSurface()
     *
     * @return float
     */
    function getTotalSurface()
    {
        $collection = $this->getCommandItemCollection();
        $surface = 0;
        // pour chaque item calcul de la surface
        if (false != $collection) {
            $count = $collection->getcount();
            for ($i=0; $i<$count; $i++) {
                $item = $collection->getItem($i);
                // somme du volume de chaque item
                $surface += $item->getSurface();
            }
        }
        return $surface;
    }

    // }}}
    // Command::getTotalVolume() {{{

    /**
     * getTotalVolume()
     *
     * @return
     */
    function getTotalVolume()
    {
        $collection = $this->getCommandItemCollection();
        $totvolume = 0;
        // pour chaque item calcul du volume
        if (false != $collection) {
            $count = $collection->getcount();
            for ($i=0; $i<$count; $i++) {
                $item = $collection->getItem($i);
                // somme du volume de chaque item
                $totvolume = $totvolume + ($item->getVolume());
            }
        }
        return $totvolume;
    }

    // }}}
    // Command::getTotalWeight() {{{

    /**
     * getTotalWeight()
     *
     * @return
     */
    function getTotalWeight()
    {
        $collection = $this->getCommandItemCollection();

        $totweight = 0;
        if (false != $collection) {
            $count = $collection->getcount();
            for ($i=0; $i<$count; $i++) {
                $item = $collection->getItem($i);
                // somme du volume de chaque item
                $totweight = $totweight +
                    ($item->getQuantity() * $item->getWeight());
            }
        }
        return $totweight;
    }

    // }}}
    // Command::getTotalQuantity() {{{

    /**
     * getTotalQuantity()
     *
     * @return
     */
    function getTotalQuantity()
    {
        $collection = $this->getCommandItemCollection();
        $totQuantity = 0;
        if (false != $collection) {
            $count = $collection->getcount();
            for ($i=0; $i<$count; $i++) {
                $item = $collection->getItem($i);
                // somme du volume de chaque item
                $totQuantity = $totQuantity + ($item->getQuantity());
            }
        }
        return $totQuantity;
    }

    // }}}
    // Command::getTotalLM() {{{

    /**
     * getTotalLM()
     *
     * @return
     */
    function getTotalLM()
    {
        require_once('MLcompute.php');
        $collection = $this->getCommandItemCollection();
        $totLM = 0;
        if (false != $collection) {
            $count = $collection->getcount();
            for ($i=0; $i<$count; $i++) {
                $item = $collection->getItem($i);
                $length = $item->getLength();
                $width = $item->getwidth();
                $Height = $item->getHeight();
                $gerbability = $item->getGerbability();
                $quantity = $item->getQuantity();
                $masterdim = $item->getMasterDimension();
                $totLM += MLcompute($length, $width, $Height,
                    $masterdim, $gerbability, $quantity);
            }
        }
        return $totLM;
    }

    // }}}
    // Command::generateCommandNo() {{{

    /**
     * Génère le numéros de commande, en se basant sur la référence de la
     * chaîne activée et sur un numéro incrémental.
     *
     * @access public
     * @param object Chain un objet chaine
     * @return string le numéro de commande
     */
    public function generateCommandNo($chain)
    {
        $chain->setCommandSequence($chain->getCommandSequence() + 1);
        $chain->save();
        $serial = $chain->getReference() . $chain->getCommandSequence();
        if ($this->getIsEstimate()) {
            $serial = 'D_'.$serial;
        }
        $commandMapper = Mapper::singleton('Command');
        // on appelle la methode récursivement jusqu'à trouver un numéro unique
        if ($commandMapper->alreadyExists(array('CommandNo'=>$serial))) {
            $serial = $this->generateCommandNo($chain);
        } else {
            $this->setCommandNo($serial);
        }
        return $serial;
    }

    // }}}
    // Command::UpdateState() {{{

    /**
     * UpdateState()
     * Methode Addon pour mettre a jour l'etat de la commande en fonction
     * de l'etat de ses ActivatedMovement, suite a un movt
     *
     * @return boolean
     */
    function UpdateState()  {
        require_once('Objects/Command.const.php');
        require_once('Objects/ActivatedMovement.php');
        $CommandType = $this->getType();  // commande client ou fournisseur?
        if (get_class($this) == 'ProductCommand') {
            // si etat initial= Command::LIV_PARTIELLE
            if ($CommandType == Command::TYPE_CUSTOMER && Command::LIV_PARTIELLE == $this->getState()) {
                return (0);  // pas de mise a jour necessaire ds ce cas
            }
            // si reintegration, en fait
            elseif ($CommandType == Command::TYPE_CUSTOMER && Command::LIV_COMPLETE == $this->getState()) {
                $State = Command::LIV_PARTIELLE;
            }
            else {
                $HasCreatedActivatedMovement = 0;
                $HasPartialActivatedMovement = 0;
                $HasCompleteActivatedMovement = 0;
                $ProductCommandItemCollection = $this->getCommandItemCollection();
                if (false != $ProductCommandItemCollection) {
                    $count = $ProductCommandItemCollection->getcount();
                    for ($i=0; $i<$count; $i++) {
                        $item = $ProductCommandItemCollection->getItem($i);
                        $acm = $item->getActivatedMovement();
                        if (Tools::isEmptyObject($acm)) {
                            continue;
                        }
                        if (($acm->getState() != ActivatedMovement::ACM_EXECUTE_TOTALEMENT)
                        && ($acm->getState() != ActivatedMovement::CREE)) {
                            $State = ($CommandType == Command::TYPE_CUSTOMER)?
                                    Command::PREP_PARTIELLE:Command::LIV_PARTIELLE;
                            $this->setState($State);
                            return $State;
                        }
                        elseif ($acm->getState() == ActivatedMovement::ACM_EXECUTE_TOTALEMENT) {
                            $HasCompleteActivatedMovement ++;
                        }
                        elseif ($acm->getState() == ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT) {
                            $HasPartialActivatedMovement ++;
                        }
                        elseif ($acm->getState() == ActivatedMovement::CREE) {
                            $HasCreatedActivatedMovement ++;
                        }
                    }

                    // si on passe ici, il n'y a pas de Mvt partiel ou en cours
                    if (($HasCreatedActivatedMovement == 0) && ($HasCompleteActivatedMovement > 0)) {
                        $State = ($CommandType == Command::TYPE_CUSTOMER)?
                            Command::PREP_COMPLETE : Command::LIV_COMPLETE;
                    }
                    elseif (($HasCreatedActivatedMovement > 0) && ($HasCompleteActivatedMovement > 0)) {
                        $State = ($CommandType == Command::TYPE_CUSTOMER)?
                            Command::PREP_PARTIELLE : Command::LIV_PARTIELLE;
                    }
                }
            }
            if (isset($State)) {
                $this->setState($State);
                return($State);
            }
            else return (0);
        }
    }     // endFunction

    // }}}
    // Command::UpdateStateWhenFinishWorkOrder() {{{

    /**
     * UpdateStateWhenFinishWorkOrder()
     * Methode Addon pour mettre a jour l'etat de la commande lors de la cloture
     * d'un OT en fonction de l'etat de ses ActivatedMovement, et des
     * ActivatedChainoperation associees
     *
     * @return integer
     */
    function UpdateStateWhenFinishWorkOrder() {
        require_once('Objects/Command.const.php');
        require_once('Objects/ActivatedMovement.php');
        require_once('Objects/WorkOrder.php');
        require_once('Objects/ActivatedChainOperation.inc.php');
        if (get_class($this) == 'ProductCommand') {
            $ProductCommandItemCollection = $this->getCommandItemCollection();
            $LIV_PARTIELLE = 0;  // initialisation

            if (!Tools::isEmptyObject($ProductCommandItemCollection)) {
                $count = $ProductCommandItemCollection->getcount();
                for ($i=0; $i<$count; $i++) {
                       $item = $ProductCommandItemCollection->getItem($i);
                       $ActivatedMovement = $item->getActivatedMovement();

                       if (in_array($ActivatedMovement->getState(), array(ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT, ActivatedMovement::CREE, ActivatedMovement::BLOQUE))) {
                        if (in_array($this->getState(), array(Command::PREP_COMPLETE, Command::PREP_PARTIELLE, Command::ACTIVEE))) {
                            return($this->getState());   // pas de modif ds ce cas!!
                        }
                        $LIV_PARTIELLE = 1;
                        continue;
                       }
                    $ActivatedChain = $item->getActivatedChain();
                    $ACOCollection = $ActivatedChain->getActivatedChainOperationCollection();
                    // Si $LIV_PARTIELLE = 1, pas besoin de passer ici
                    if (!Tools::isEmptyObject($ACOCollection) && $LIV_PARTIELLE == 0) {
                        $jcount = $ACOCollection->getCount();
                        for ($j = 0;$j < $jcount;$j++) {
                            $ACO = $ACOCollection->getItem($j);
                            if (!IsTransportOperation($ACO)) {
                                continue;
                            }
                            $WorkOrder = $ACO->getOwnerWorkerOrder();

                            // si pas relie a un OT ou a un OT non cloture
                            if (Tools::isEmptyObject($WorkOrder) ||
                                (!Tools::isEmptyObject($WorkOrder) && WorkOrder::WORK_ORDER_STATE_TOFILL == $WorkOrder->getState())) {
                                $this->setState(Command::LIV_PARTIELLE);
                                return(Command::LIV_PARTIELLE);
                            }
                            unset($ACO, $WorkOrder);
                        }  // for
                    }
                    unset($item, $ActivatedMovement, $ActivatedChain, $ACOCollection);
                   } // for

                if ($LIV_PARTIELLE == 1) {
                    $this->setState(Command::LIV_PARTIELLE);  // car tous les mvts ne sont pas executes totalemt
                    return(Command::LIV_PARTIELLE);
                }
                $this->setState(Command::LIV_COMPLETE);
                return(Command::LIV_COMPLETE);
            }
            else return (0);
           }
    }

    // }}}
    // Command::getExecutedMovementCollection() {{{

   /*
    * Methode Addon pour recuperer tous les executedMovements concernant la Command
    * @return integer Content of value
    */
    function getExecutedMovementCollection()
    {
        $ExecutedMovementMapper = Mapper::singleton('ExecutedMovement');
        $ExecutedMovementCollection = new Collection();

        $CommandItemCollection = $this->getCommandItemCollection();
        if (($CommandItemCollection instanceof Collection) && ($CommandItemCollection->getCount() > 0)) {
            $count = $CommandItemCollection->getCount();
             for ($i = 0;$i < $count;$i++) {
                 $item = $CommandItemCollection->getItem($i);
                $ActivatedMovementId = $item->getActivatedMovementId();
                $ExecutedMovement = $ExecutedMovementMapper->load(array('ActivatedMovement' => $ActivatedMovementId));
                if (($ExecutedMovement instanceof ExecutedMovement) && $ExecutedMovement->getId() > 0) {
                    $ExecutedMovementCollection -> SetItem($ExecutedMovement);
                }
                unset($ActivatedMovement);
                unset($ExecutedMovement);
                }
            }
        return($ExecutedMovementCollection);
    }     // endFunction

    // }}}
    // Command::getDataForBL() {{{

    /**
     * Recupere les donnees a editer dans un BL si besoin a une date donnee
     * @access public
     * @param $DateSup: date apres laquelle les LocationExecutedMovements ne conviennent pas
     * pour une edition: date par defaut
     * @return array : tableau de strings
     */
    function getDataForBL($DateSup='') {
        require_once('Objects/ExecutedMovement.php');
        require_once('Objects/Property.inc.php');
        require_once('Objects/DeliveryOrder.php');
        require_once('FormatNumber.php');
        $EXMMapper = Mapper::singleton('ExecutedMovement');
        $ProductReference = array();   // Ref commandee
        $ProductName = array();        // Nom Product
        $OrderedQuantity = array();    // Qte commandee
        $ProductUV = array();          // SellUnitQuantity du Product commande/livre
        $DeliveredQuantity = array();  // Qte livree
        $RemainingQuantity = array();  // Qte restant a livrer
        $PackagingNumber = array();    // Nbre de colis (UE)
        $Weight = array();             // Poids des colis
        $returnData = array();

        $customsProperties = array();

        // recupere le document
        $abstractDocMapper = Mapper::singleton('AbstractDocument');
        $abstractDoc = $abstractDocMapper->load(
            array('Command' => $this->getId()));
        if (!($abstractDoc instanceof AbstractDocument)) {
            // le document n'est pas encore créé, on en créee un temporaire...
            $abstractDoc = new DeliveryOrder();
            $abstractDoc->setCommand($this);
        }
        // recupere le document model
        $dom = $abstractDoc->FindDocumentModel();
        $domPropertyCol = $dom->getDocumentModelPropertyCollection(
                array('PropertyType'=>0), array('Order'=>SORT_ASC));
        $numberOfDomProps = $domPropertyCol->getCount();

        $CommandItemCollection = $this->getCommandItemCollection();
        if (!Tools::isEmptyObject($CommandItemCollection)) {
            $count = $CommandItemCollection->getCount();
            for ($i = 0;$i < $count;$i++) {
                $item = $CommandItemCollection->getItem($i);
                $EXM = $EXMMapper->load(
                    array('ActivatedMovement' => $item->getActivatedMovementId()));
                // si pas d'exm, pas la peine d'aller plus loin
                if (!($EXM instanceof ExecutedMovement) || $EXM->getId()==0) {
                    continue;
                }
                // si pas de lem, pas la peine d'aller plus loin
                $LEMCollection = $EXM->getLocationExecutedMovementForBL($DateSup);
                if (Tools::isEmptyObject($LEMCollection, 'Collection')) {
                    continue;
                }
                $ProductRefForActivatedMovt = array();
                $ProductNameForActivatedMovt = array();
                $ProductUVForActivatedMovt = array();
                // Qte livree par ref, pour chaque ActivatedMovement
                $DeliveredQtyForActivatedMovt = array();
                $ProductIdArrayForActivatedMovt = array();
                $customsPropertiesArray = array();

                $OrderedProductId = Tools::getValueFromMacro($item, '%Product.Id%');
                $ProductRefForActivatedMovt[-1] = Tools::getValueFromMacro(
                        $item, '%Product.BaseReference%');
                $ProductNameForActivatedMovt[-1] = TextTools::truncateText(
                    Tools::getValueFromMacro($item, '%Product.Name%'),
                    64, '...'
                );
                $ProductUVForActivatedMovt[-1] = Tools::getValueFromMacro(
                        $item, '%Product.SellUnitQuantity%');
                // Qte livree de produit commande initialise a 0
                $DeliveredQtyForActivatedMovt[-1] = 0;
                // Qte reintegree de produit commande initialise a 0
                $CancelledQtyForActivatedMovt[-1] = 0;
                $customsPropertiesArray[-1] = '';
                // gestion des documentModelProperty pour le champ désignation
                for ($foo=0 ; $foo<$numberOfDomProps ; $foo++) {
                    $domProperty = $domPropertyCol->getItem($foo);
                    $property = $domProperty->getProperty();
                    $product = $item->getProduct();
                    if($product instanceof Product) {
                        $customsPropertiesArray[-1] .= ' '.
                            Tools::getValueFromMacro($product,
                            '%' . $property->getName() . '%');
                    }
                }

                $jcount = $LEMCollection->getCount();
                for($j = 0; $j < $jcount; $j++) {
                    $LEM = $LEMCollection->getItem($j);
                    $RealProduct = $LEM->getProduct();
                    $RealProductId = $LEM->getProductId();
                    $RealQuantity = $LEM->getQuantity()
                        - $LEM->getCancelledQuantity($DateSup);
                    $PackagingNumber[] = $RealProduct->packagingUnitNumber(
                        $RealQuantity);
                    $Weight[] = $RealQuantity * $RealProduct->getSellUnitWeight();
                    // si c'est le produit commande: indice -1 pour qu'il soit en 1er
                    if ($RealProductId == $OrderedProductId) {
                       $DeliveredQtyForActivatedMovt[-1] += $RealQuantity;
                    } else {
                        if (in_array($RealProductId,
                        $ProductIdArrayForActivatedMovt)) {
                            $DeliveredQtyForActivatedMovt[$RealProductId] +=
                                $RealQuantity;
                        } else {
                            $DeliveredQtyForActivatedMovt[$RealProductId] =
                                $RealQuantity;
                            $ProductIdArrayForActivatedMovt[] = $RealProductId;
                        }
                        $ProductRefForActivatedMovt[0] = _('replaced by');
                        $ProductNameForActivatedMovt[0] = "";
                        $ProductUVForActivatedMovt[0] = "";
                        $DeliveredQtyForActivatedMovt[0] = "";
                        $CancelledQtyForActivatedMovt[0] = "";
                        $ProductNameForActivatedMovt[$RealProductId] =
                                TextTools::truncateText($RealProduct->getName(), 64, '...');
                        $ProductUVForActivatedMovt[$RealProductId] =
                                $RealProduct->getSellUnitQuantity();
                        $ProductRefForActivatedMovt[$RealProductId] =
                                $RealProduct->getBaseReference();

                        for ($foo=0 ; $foo<$numberOfDomProps ; $foo++) {
                            $domProperty = $domPropertyCol->getItem($foo);
                            $property = $domProperty->getProperty();
                            $customsPropertiesArray[-1] .= ' '.
                                Tools::getValueFromMacro($RealProduct,
                                '%' . $property->getName() . '%');
                        }
                    }

                    unset($LEM, $RealProduct);
                }  // for

                ksort($DeliveredQtyForActivatedMovt);
                foreach($DeliveredQtyForActivatedMovt as $k=>$v) {
                    if(!is_numeric($v)) {
                        continue;
                    }
                    $DeliveredQtyForActivatedMovt[$k] = I18n::formatNumber($v, 3, true, true);
                }
                $DeliveredQuantity[] = implode("\n", $DeliveredQtyForActivatedMovt);

                // Collection des LEM effectues depuis le debut
                if($EXM->getState() == ExecutedMovement::EXECUTE_TOTALEMENT) {
                    $RemainingQuantity[] = 0;
                } else {
                    $ProductQtyCancelledAndDelivered =
                        $EXM->getProductQtyCancelledAndDelivered($DateSup);
                    $tmpQty = $item->getQuantity() -
                        $ProductQtyCancelledAndDelivered[0] +
                        $ProductQtyCancelledAndDelivered[1];
                    // pour gerer le cas ou la qte est < celle prevue
                    $RemainingQuantity[] = $tmpQty < 0 ? 0:$tmpQty;
                }

                ksort($ProductRefForActivatedMovt);
                ksort($ProductNameForActivatedMovt);
                ksort($ProductUVForActivatedMovt);
                $ProductReference[] = implode("\n", $ProductRefForActivatedMovt);
                $OrderedQuantity[] = $item->getQuantity();
                $ProductName[] = implode("\n", $ProductNameForActivatedMovt);
                $ProductUV[] = implode("\n", $ProductUVForActivatedMovt);
                $customsProperties[] = implode("\n", $customsPropertiesArray);

                unset($EXM, $item, $ProductRefForActivatedMovt,
                      $ProductNameForActivatedMovt, $ProductUVForActivatedMovt,
                      $DeliveredQtyForActivatedMovt, $ProductIdArrayForActivatedMovt,
                      $customsPropertiesArray);
            }    // fin de boucle sur les CommandItems
        }
        $count = count($ProductName);

        $mapper = Mapper::singleton('Product');
        // on reordonne les donnees pour les inserer ligne par ligne ds le doc pdf
        for ($i=0; $i<$count; $i++) {
            $pdt = $mapper->load(array('BaseReference'=>$ProductReference[$i]));
            $qtyaddon = '';
            if ($pdt instanceof Product) {
                $qtyaddon = $pdt->getMeasuringUnit();
            }
            $returnData[] = array($ProductReference[$i],
                      $ProductName[$i].$customsProperties[$i],
                      I18N::formatNumber($OrderedQuantity[$i], 3, true, true) . $qtyaddon,
                      $ProductUV[$i],
                      $DeliveredQuantity[$i] . $qtyaddon, // le format number est fait plus haut
                      I18N::formatNumber($RemainingQuantity[$i], 3, true, true));
        }
        $TotalPackagingNumber = array_sum($PackagingNumber);
        $TotalWeight = array_sum($Weight);
        $return = array($returnData, array($TotalPackagingNumber, $TotalWeight));
        return $return;
    }     // endFunction

    // }}}
    // Command::getDataForRTWBL() {{{

    /**
     * Recupere les donnees a editer dans un BL si besoin a une date donnee
     * @access public
     * @param $DateSup: date apres laquelle les LocationExecutedMovements ne conviennent pas
     * pour une edition: date par defaut
     * @return array : tableau de strings
     */
    function getDataForRTWBL($DateSup='') {
        $dataForBL = $this->getDataForBL($DateSup);
        $data      = $dataForBL[0];
        $ret  = array();
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }
            $rtwProduct = Object::load('RTWProduct', array('BaseReference' => $item[0]));
            if (!($rtwProduct instanceof RTWProduct)) { 
                continue;
            }
            if (!(($size = $rtwProduct->getSize()) instanceof RTWSize)) {
                $size = false;
            }
            $ref = $rtwProduct->getBaseReference();
            $model = $rtwProduct->getModel();
            if (!isset($ret[$model->getId()])) {
                $ret[$model->getId()] = array(
                    $ref,
                    // taille: qte
                    $rtwProduct->getName() . ($size ? "\n".$size->getName().": {$item[2]}" : ''),
                    // qte: sera incrementee
                    intval($item[2]),
                    // UV
                    $item[3],
                    // Qte livree: sera incrementee
                    intval($item[4]),
                    // Reste a livrer: sera incrementee
                    intval($item[5])
                );
            } else {
                $ret[$model->getId()][1] .= ($size ? ", ".$size->getName().": {$item[2]}" : '');
                $ret[$model->getId()][2] += intval($item[2]);
                $ret[$model->getId()][4] += intval($item[4]);
                $ret[$model->getId()][5] += intval($item[5]);
            }
        }
        foreach ($ret as $i=>&$array) {
            $model = Object::load('RTWModel', $i);
            $legalMentions = $model->getLegalMentions();
            if (!empty($legalMentions)) {
                $array[1] .= "\n\n" . $legalMentions;
            }
        }
        return array(array_values($ret), $dataForBL[1]);
    }

    // }}}
    // Command::getOtherPossibleMovement() {{{

    /**
     * Retourne true s'il existe des ActivatedMvmts de sortie de stock autres
     * que celui passe en parametre prevus pour cette commande en etat partiel
     * ou a faire avec une quantite en stock (pour le produit dans des
     * emplacements situes dans le meme site de stockage) > 0
     * Sert pour savoir si on force ou pas l'edition d'un BL
     * @param $acm : Object ActivatedMovement or default 0
     * @param $Site : Object Site
     * @access public
     * @return boolean
     */
    function getOtherPossibleMovement($acm=0, $Site) {
        require_once('Objects/ActivatedMovement.php');
        $acmId = ($acm instanceof ActivatedMovement)?$acm->getId():0;
        $ProductCommandItemCollection = $this->getCommandItemCollection();
        $count = $ProductCommandItemCollection->getCount();
        for($i = 0; $i < $count; $i++) {
            $ProductCommandItem = $ProductCommandItemCollection->getItem($i);
            $activatedMovtId = $ProductCommandItem->getActivatedMovementId();
             // l'ActivtdMovt passe en param ne convient pas
            if ($activatedMovtId > 0 && $activatedMovtId != $acmId) {
                $acm = $ProductCommandItem->getActivatedMovement();
                if (in_array($acm->getState(), array(ActivatedMovement::CREE, 
                    ActivatedMovement::ACM_EXECUTE_PARTIELLEMENT))) {
                    $Product = $ProductCommandItem->getProduct();
                    $siteID = $Site instanceof Site?$Site->getId():0;
                    $StockQuantity = $Product->getRealQuantity($siteID);
                    if ($StockQuantity > 0) {
                        return true;
                    }
                }
            }
            unset($ProductCommandItem);
        }  // for
    return false;
    }  // end function

    // }}}
    // Command::getPaymentCollection() {{{

    /**
     * Retourne la collection des reglements
     * @param $state integer: 0 par defaut: tous;
     *                           1: seulement ceux qui n'ont pas ete annules!
     * @access public
     * @return Payment Collection
     */
    function getPaymentCollection($state=0) {
        // la collection qui sera retournee
        $PaymentCollection = new Collection();
        $PaymentCollection->acceptDuplicate = false;
        $InvoiceMapper = Mapper::singleton('Invoice');

        $InvoiceCollection = $InvoiceMapper->loadCollection(
                                                    array('Command'=>$this->getId()));

        if (!Tools::isEmptyObject($InvoiceCollection)) {
            $count = $InvoiceCollection->getCount();
               for($i = 0; $i < $count; $i++){
                $item = $InvoiceCollection->getItem($i);
                //recupere la collection des payments pour la facture associee
                $PaymtCollection = $item->getPaymentCollection($state);
                if (!Tools::isEmptyObject($PaymtCollection)) {
                    $jcount = $PaymtCollection->getCount();
                    for($j = 0; $j < $jcount; $j++){
                        $Payment = $PaymtCollection->getItem($j);
                        $PaymentCollection->SetItem($Payment);
                        unset($Payment);
                    }
                }
                unset($item, $PaymtCollection);
            }
        }
        return $PaymentCollection;
    }

    // }}}
    // Command::BlockDeblock() {{{

    /**
     * Permet de bloquer ou debloquer la commande et tous ses
     * ActivatedMovements associes
     * @access public
     * @param integer $block : 1 pour bloquer (par defaut), et 0 pour debloquer
     * @return void
     */
    function BlockDeblock($block=1) {
        require_once('Objects/Command.const.php');
        require_once('Objects/ActivatedMovement.php');
        require_once('Objects/Task.const.php');

        /*  Ouverture de la transaction  */
        Database::connection()->startTrans();

        if ($block == 1) {
            $this->setState(Command::BLOCAGE_CDE);
        }
        else {
            $this->setState(Command::ACTIVEE);
        }
        // met a jour de l'etat de la commande
        $this->save();

        /* Reste a mettre a jour de l'etat des ACM s'il y en a  */
        $CommandItemCollection = $this->getCommandItemCollection();

        if (!Tools::isEmptyObject($CommandItemCollection)) {
            $count = $CommandItemCollection->getCount();
            for($i = 0; $i < $count; $i++) {
                $item = $CommandItemCollection->getItem($i);
                if (!method_exists($item, 'getActivatedMovement')) {
                    break;  // Si pas d'ACM (Cmde de cours, ...)
                }
                $ActivatedMovement = $item->getActivatedMovement();
                if (Tools::isEmptyObject($ActivatedMovement)) {
                    $ActivatedChain = $this->getActivatedChain();

                    // Dans ce cas il est normal qu'il n'y ai pas d'ACM
                    if (($this->getType() == Command::TYPE_SUPPLIER &&
                        !$ActivatedChain->hasTaskOfType(TASK_STOCK_ENTRY))
                        || ($this->getType() == Command::TYPE_CUSTOMER &&
                        !$ActivatedChain->hasTaskOfType(TASK_STOCK_EXIT))) {
                        break;
                    }
                    // sinon il y a une erreur
                    trigger_error(
                        'Un ProductCommandItem sans ACM dans Command.BlockDeblock()',
                        E_USER_WARNING
                    );
                    break;
                }
                if ($block == 1) {  // Blocage
                    if ($ActivatedMovement->getState() == ActivatedMovement::CREE) {
                        $ActivatedMovement->setState(ActivatedMovement::BLOQUE);
                        $ActivatedMovement->save();
                    }
                }
                else {  // Deblocage
                    $ActivatedMovement->setState(ActivatedMovement::CREE);
                    $ActivatedMovement->save();
                }
            }
        }

        if (Database::connection()->HasFailedTrans()) {
            Database::connection()->rollbackTrans();
            Template::errorDialog(E_MSG_TRY_AGAIN, 'CommandList.php');
        }
        /*  Commit de la transaction  */
        Database::connection()->completeTrans();
    }

    // }}}
    // Command::getSupplierCustomer() {{{

    /**
     * Retourne le couple SupplierCustomer de la commande telle que:
     * Supplier=$this->getExpeditor() et Customer=$this->getDestinator()
     *
     * Si le couple n'a pu être trouvé, il est créé à la volée avec des infos
     * par défaut.
     *
     * @access public
     * @return object SupplierCustomer
     */
    function getSupplierCustomer() {
        $spc = parent::getSupplierCustomer();
        if (!($spc instanceof SupplierCustomer)) {
            // le couple n'a pas été trouvé on en crée un par défaut
            require_once('Objects/SupplierCustomer.php');
            $spc = new SupplierCustomer();
            $spc->setModality(SupplierCustomer::CHEQUE);
            $spc->setTotalDays(30);
            $spc->setOption(SupplierCustomer::NET);
            $exp = $this->getExpeditor();
            $spc->setSupplier($exp);
            $dest = $this->getDestinator();
            $spc->setCustomer($dest);
            $hastva = ($this->getTotalPriceTTC()-$this->getTotalPriceHT() > 0);
            $spc->setHasTVA($hastva);
            if($this->hasBeenInitialized) {
                $spc->save();
            }
            $this->setSupplierCustomer($spc);
            if($this->hasBeenInitialized) {
                $this->save();
            }
        }
        return $spc;
    }

    // }}}
    // Command::getPassengerWeight() {{{

    /**
     * Command::getTotalWeight()
     * Retourne le poids total du client et de l'instructeur s'il existe
     *
     * @access public
     * @return float
     */
    function getPassengerWeight(){
        $cust = $this->getCustomer();
        if (!($cust instanceof AeroCustomer)) {
            return 0;
        }
        $inst = false;
        if (method_exists($this, 'getInstructor')) {
            $inst = $this->getInstructor();
        }
        return $cust->getWeight() + ($inst?$inst->getWeight():0);
    }

    // }}}
    // Command::hasBeenFactured() {{{

    /**
     * Retourne TRUE si la Commande a deja ete facturee, FALSE sinon
     * @access public
     * @return boolean
     */
    function hasBeenFactured(){
        require_once('Objects/Command.const.php');
        return in_array($this->getState(), array(Command::FACT_PARTIELLE, 
            Command::FACT_COMPLETE, Command::REGLEMT_PARTIEL, Command::REGLEMT_TOTAL));
    }

    // }}}
    // Command::getActivatedChainOperation() {{{

    /**
     * Retourne la premiere ACO liee a la Command, telle que
     * son Operation soit de type $OperationId
     * @param $OperationId integer Operation Type
     * @access public
     * @return void
     */
    function getActivatedChainOperation($OperationId){
        $CmdItemCollection = $this->getCommandItemCollection();
        for($i=0; $i<$CmdItemCollection->getCount(); $i++){
            $CommandItem = $CmdItemCollection->getItem($i);
            $ActivatedChain = $CommandItem->getActivatedChain();
            $ACOCollection = $ActivatedChain->getActivatedChainOperationCollection();
            for($i=0; $i<$ACOCollection->getCount(); $i++){
                $ACO = $ACOCollection->getItem($i);
                if ($ACO->getOperationId() == $OperationId) {
                    return $ACO;
                }
            }
        }
        return false;
    }

    // }}}
    // Command::htmlDump() {{{

    /**
     * Command::htmlDump()
     * Dumpe les données de la commande au format HTML
     *
     * @return string
     */
    function htmlDump() {
        $props = $this->getProperties();
        $name = get_class($this) . ': ' . $this->getCommandNo();
        $ret = "<h2>$name</h2>";
        $ret .= '<table border=1><tr><th>attribut</th><th>valeur</th></tr>';
        foreach($props as $name=>$type) {
            $getter = is_string($type)?'get' . $name . 'Id':'get' . $name;
            if (method_exists($this, $getter)) {
                $ret .= sprintf("<tr><td>%s</td><td>%s<td></tr>",
                    $name, $this->$getter());
            }
        }
        $cmiCol = $this->getCommandItemCollection();
        $count  = $cmiCol->getCount();
        $ret .= "</table><br><h4>CommandItems</h4>";
        for ($i=0; $i<$count; $i++) {
            $ret .= '<table border=1><tr><th>attribut</th><th>valeur</th></tr>';
            $cmi  = $cmiCol->getItem($i);
            $props = $cmi->getProperties();
            foreach($props as $name=>$type) {
                $getter = is_string($type)?'get' . $name . 'Id':'get' . $name;
                if (method_exists($cmi, $getter)) {
                    $ret .= sprintf("<tr><td>%s</td><td>%s<td></tr>",
                        $name, $cmi->$getter());
                }
            }
            $ret .= "</table>";
        }
        return $ret;
    }

    // }}}
    // Command::isWithTVA() {{{

    /**
     * Retourne true si un des CommandItem de la commande
     * a une TVA de définie, c'est à dire si le couple
     * supplierCustomer avait hasTVA=1 au moment du passage
     * de la commande.
     *
     * @access public
     * @return boolean
     */
    function isWithTVA() {

        $filter = SearchTools::NewFilterComponent(
           'TVA', '', 'NotEquals', 0, 1);
        $cmi = $this->getCommandItemCollectionIds($filter);
        if(count($cmi) > 0) {
            return true;
        }
        return false;
    }

    // }}}
    // Command::getInvoiceCommandType() {{{

    /**
     * Reourne le type précis de la commande, en fonction
     * de Command.Type et du SupplierCustomer.
     * @access public
     * @return int
     */
    function getInvoiceCommandType() {
        require_once('Objects/Invoice.php');

        if($this->getType() == Command::TYPE_SUPPLIER) {
            return AbstractDocument::TYPE_SUPPLIER_PRODUCT;
        }
        if($this->getType() == Command::TYPE_CUSTOMER) {
            return AbstractDocument::TYPE_CUSTOMER_PRODUCT;
        }

        /* dans les autres cas il faut définir si la commande
         est client ou fournisseur en fonction de son
         supplierCustomer :
         client si Command.SupplierCustomer.Supplier.Id = DatabaseOwner
         fournisseur si Command.SupplierCustomer.Customer.Id = DatabaseOwner */
        $supplierCustomer = $this->getSupplierCustomer();
        if($supplierCustomer->getSupplierId() == 1) {
            $cmdCustomer = true;
        } elseif ($supplierCustomer->getCustomerId() == 1) {
            $cmdCustomer = false;
        }

        switch ($this->getType()) {
        	case Command::TYPE_TRANSPORT:
                if($cmdCustomer) {
                    return AbstractDocument::TYPE_CUSTOMER_TRANSPORT;
                }
                return AbstractDocument::TYPE_SUPPLIER_TRANSPORT;
                break;
            case Command::TYPE_PRESTATION:
                if($cmdCustomer) {
                    return AbstractDocument::TYPE_CUSTOMER_PRESTATION;
                }
                return AbstractDocument::TYPE_SUPPLIER_PRESTATION;
                break;
            case Command::TYPE_COURSE:
                if($cmdCustomer) {
                    return AbstractDocument::TYPE_CUSTOMER_COURSE;
                }
                return AbstractDocument::TYPE_SUPPLIER_COURSE;
                break;
        	default:
        	    return $this->getType();
        		break;
        }
    }

    // }}}
    // Command::isBillable() {{{

    /**
     * Retourne true/false selon si la commande est facturable
     * @access public
     * @return boolean
     */
    public function isBillable() {
        if (!($this instanceof ProductCommand) || $this->getType() != Command::TYPE_CUSTOMER) {
            return true;
        }
        return (Tools::getValueFromMacro($this, '%SupplierCustomer.CustomerProductCommandBehaviour%') == 0);
    }

    // }}}
    // Command::setState() {{{

    /**
     * Surcharge pour gerer l'attribut Closed, pour les ProductCommand
     * seulement, pour l'instant
     * @param $state integer
     * @access public
     * @return void
     */
    public function setState($state) {
        if (!($this instanceof ProductCommand) || $this->getType() != Command::TYPE_CUSTOMER) {
            return parent::setState($state);
        }
        $behaviour = Tools::getValueFromMacro(
                $this, '%SupplierCustomer.CustomerProductCommandBehaviour%');
        // Remarque: si facturable, Closed est a passer a 0 forcement
        if ($behaviour == SupplierCustomer::WITH_INVOICE) {
            $this->setClosed(0);
        }
        // Passage obligatoire a Closed=1
        if (($state == Command::PREP_COMPLETE && $behaviour == SupplierCustomer::NO_INVOICE_CLOSED_AFTER_PREPARED)
        || ($state == Command::LIV_COMPLETE && $behaviour == SupplierCustomer::NO_INVOICE_CLOSED_AFTER_DELIVERED)) {
            $this->setClosed(1);
        }
        // Si reintegration en stock, retour a Closed=0
        if ($behaviour == SupplierCustomer::NO_INVOICE_CLOSED_AFTER_PREPARED
        && in_array($state, array(Command::PREP_PARTIELLE, Command::ACTIVEE))) {
            $this->setClosed(0);
        }

        // Si regression de l'etat Livr. complet, retour a Closed=0
        if ($behaviour == SupplierCustomer::NO_INVOICE_CLOSED_AFTER_DELIVERED
        && in_array($state, array(Command::LIV_PARTIELLE, Command::PREP_COMPLETE,
        Command::PREP_PARTIELLE, Command::ACTIVEE))) {
            $this->setClosed(0);
        }

        parent::setState($state);
    }

    // }}}
    // Command::findHandingByRangePercent() {{{

    /**
     * Trouve le pourcentage de remise par trannche correspondant à la commande.
     *
     * Cette remise est recherchée en fonction de:
     *   - la devise de la commande,
     *   - la catégorie du Command::Customer,
     *   - le montant total HT de la commande
     *
     * @access public
     * @return float
     */
    public function findHandingByRangePercent() {
        $catID = Tools::getValueFromMacro(
            $this,
            '%SupplierCustomer.Customer.Category.Id%'
        );
        if ($catID == 0) {
            // le client n'a pas de catégorie, pas la peine de continuer
            return 0;
        }
        $totalHT = $this->getTotalPriceHT();
        $filter = new FilterComponent(
            new FilterRule(
                'Currency',
                FilterRule::OPERATOR_EQUALS,
                $this->getCurrencyId()
            ),
            new FilterRule(
                'Category',
                FilterRule::OPERATOR_EQUALS,
                $catID
            ),
            new FilterRule(
                'Minimum',
                FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                $totalHT
            ),
            new FilterRule(
                'Maximum',
                FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                $totalHT
            )
        );
        $filter->operator = FilterComponent::OPERATOR_AND;
        $handing = Object::load('HandingByRange', $filter);
        return ($handing instanceof HandingByRange) ? $handing->getPercent() : 0;
    }

    // }}}

}

?>
