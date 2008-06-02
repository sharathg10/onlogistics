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

class LocationExecutedMovement extends _LocationExecutedMovement {
    // Constructeur {{{

    /**
     * LocationExecutedMovement::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}

    // LocationExecutedMovement::CreateInventoryDetail() {{{

    /**
     * Cree le InventoryDetail correspondant si c'est un mvt d'inventaire
     * @access public
     * @param $Inventory Object Inventory
     * @return void
     */
    public function createInventoryDetail($Inventory) {

        require_once('Objects/MovementType.const.php');
        $MovementTypeId = Tools::getValueFromMacro($this, '%ExecutedMovement.Type.Id%');
        if ($MovementTypeId != SORTIE_INVENT && $MovementTypeId != ENTREE_INVENT) {  // si pas INVENTAIRE
            return 0;
        } // else...
        $Product = $this->getProduct();
        $InventoryDetail = Object::load('InventoryDetail');
        $InventoryDetail->setProduct($Product);
        $InventoryDetail->setProductReference($Product->getBaseReference());
        $InventoryDetail->setLocation($this->getLocation());
        $InventoryDetail->setLocationName(Tools::getValueFromMacro($this, '%Location.Name%'));

        $ActorProductMapper = Mapper::singleton('ActorProduct');
        $ActorProduct = $ActorProductMapper->load(
            array(
                'Priority' => 1,
                'Product'  => $this->getProductId()
            )
        );
        $site  = $Inventory->getStorageSite();
        $owner = false;
        if ($site instanceof StorageSite) {
            $owner = $site->getStockOwner();
        }
        if (!($owner instanceof Actor)) {
            $owner = $site->getOwner();
        }
        if (!Tools::isEmptyObject($ActorProduct) && !Tools::isEmptyObject($owner)) {
            $pbc = $ActorProduct->getPriceByCurrencyForInventory($owner);
            if ($pbc instanceof PriceByCurrency && $pbc->getPrice() > 0) {
                $InventoryDetail->setBuyingPriceHT($pbc->getPrice());
                $cur = $pbc->getCurrency();
                $InventoryDetail->setCurrency($cur->getSymbol());
            }
        }

        $lpqMapper = Mapper::singleton('LocationProductQuantities');
        $lpq = $lpqMapper->load(
                    array('Location'=>$this->getLocationId(),
                          'Product' =>$this->getProductId()));
        if (!($lpq instanceof LocationProductQuantities) || $lpq->getId() == 0) {
            return false;
        }
        $coef = ($MovementTypeId == ENTREE_INVENT)?1:-1;
        // qte a augmenter ou diminuer selon si entree ou sortie
        $InventoryDetail->setQuantity($lpq->getRealQuantity() + ($coef * $this->getQuantity()));
        $InventoryDetail->setInventory($Inventory);
        $InventoryDetail->save();
        return $InventoryDetail;
    }

    // }}}
    // LocationExecutedMovement::isCancellable() {{{

    /**
     * Retourne true si le LEM est annulable, et false sinon
     * @access public
     * @return array(boolean, string)
     */
    public function isCancellable(){
        require_once('Objects/MovementType.const.php');
        require_once('Objects/ActivatedMovement.php');
        $exm = $this->getExecutedMovement();
        $MovementTypeId = $exm->getTypeId();
        $MovementType = $exm->getType();
        $EntrieExit = $MovementType->getEntrieExit();
        $foreseeable = $exm->isForeseeable();
        $ErrorMsgeBegin = _('This movement cannot be cancelled');

        /*  Pour tous ces types de mvt: annulation refusee  */
        if (in_array($MovementTypeId, array(ENTREE_NORMALE, SORTIE_INVENT, ENTREE_INVENT, SORTIE_DEPLACEMT, ENTREE_DEPLACEMT))
            || $this->getCancelledMovementId() > 0) {
            return array(false, _('This type of movement cannot be cancelled'));
        }

        /*  Si le LEM est lie a un mvt en cours: annulation refusee  */
        if (ActivatedMovement::ACM_EN_COURS == Tools::getValueFromMacro($exm, '%ActivatedMovement.State%')) {
            return array(false, $ErrorMsgeBegin . _(' linked to a movement in progress.'));
        }

        /*  Si Qte reintegree = Qte sortie, reintegration refusee  */
        if (1 == $MovementType->getForeseeable() && 0 == $exm->getRealQuantity()) {
            return array(false, $ErrorMsgeBegin . _(', because all issued products have been reinstated.'));
        }
        /*  Si mvt non prevu deja annule  */
        if ((!$foreseeable) && $this->getCancelled() == -1) {
            return array(false, $ErrorMsgeBegin . _(', because it has already been cancelled.'));
        }
        /*  Si Product ou Location desactive, annulation refusee  */
        if (0 == Tools::getValueFromMacro($this, '%Product.Activated%') || 0 == Tools::getValueFromMacro($this, '%Location.Activated%')) {
            return array(false, $ErrorMsgeBegin . _(', because location or product was deactived.'));
        }
        /*  si on annule une entree, on doit faire une sortie: le LPQ doit encore exister, etre acive, et avec une Qte suffisante  */
        $LocationId = $this->getLocationId();
        $ProductId = $this->getProductId();
        $LocationPdtQuantitiesMapper = Mapper::singleton('LocationProductQuantities');
        $LocationPdtQuantities = $LocationPdtQuantitiesMapper->load(array('Location' => $LocationId, 'Product' => $ProductId));

        if (($EntrieExit == ENTREE && (Tools::isEmptyObject($LocationPdtQuantities)
            || $LocationPdtQuantities->getRealQuantity() < $this->getQuantity()))
            || (!Tools::isEmptyObject($LocationPdtQuantities) && 0 == $LocationPdtQuantities->getActivated())) {
            return array(false, $ErrorMsgeBegin . _(', because location was deactived or does not contain anymore this product in sufficient quantity.'));
        }

        /* si prestation facturée */
        if($this->getPrestationFactured()) {
            $invoice = Tools::getValueFromMacro($this,
                '%InvoiceItem.Invoice.DocumentNo%');
            return array(false, $ErrorMsgeBegin . ', ' .
                _('it is linked to invoice ') . $invoice);
        }
        return array(true, 'Tout est OK');
    }

    // }}}
    // LocationExecutedMovement::createCancellerMovement() {{{

    /**
     * Cree un LEM annulateur et gere les LEMConcreteProduct associes
     * @access public
     * @return object LocationExecutedMovement
     */
    public function createCancellerMovement() {
        $CancellerLEM = Object::load('LocationExecutedMovement');
        $CancellerLEM->setDate(date('Y-m-d H:i:s', time()));
        $CancellerLEM->setExecutedMovement($this->getExecutedMovement());
        $CancellerLEM->setProduct($this->getProduct());
        $CancellerLEM->setCancelledMovement($this);
        $CancellerLEM->setCancelled(1);
        // Par defaut:
    	$CancellerLEM->setQuantity($this->getQuantity());
    	$CancellerLEM->setLocation($this->getLocation());
        $CancellerLEM->save();

        // On s'occupe des LEMConcreteProduct si besoin
        $LEMCPCollection = $this->getLEMConcreteProductCollection();
        // Pas de mode de suivi
        if (Tools::isEmptyObject($LEMCPCollection)) {
            return $CancellerLEM;
        }
        $count = $LEMCPCollection->getCount();
        for($i = 0; $i < $count; $i++){
            $LEMCP = $LEMCPCollection->getItem($i);
            $CancellerLEMCP = Object::load('LEMConcreteProduct');
            $CancellerLEMCP->setConcreteProduct($LEMCP->getConcreteProduct());
            $CancellerLEMCP->setLocationExecutedMovement($CancellerLEM);
            $CancellerLEMCP->setQuantity($LEMCP->getQuantity()); // SI SANS PREV. !
            $CancellerLEMCP->setCancelled(LEMConcreteProduct::LEMCP_CANCELLER);  // car annulateur
            $CancellerLEMCP->setCancelledLEMConcreteProduct($LEMCP);
            $CancellerLEMCP->save();
            unset($LEMCP, $CancellerLEMCP);
        }

        return $CancellerLEM;
    }

    // }}}
    // LocationExecutedMovement::getMawQuantityForCancellation() {{{

    /**
     * Qte max de Product qu'il est possile de reintegrer en stock oubien d'annuler
     * @access public
     * @return void
     */
    public function getMaxQuantityForCancellation(){
        require_once('Objects/MovementType.const.php');
        $MovementTypeId = Tools::getValueFromMacro($this, '%ExecutedMovement.Type.Id%');

        if ($MovementTypeId != SORTIE_NORMALE) {
               return $this->getQuantity();
        }
        $MovementType = Object::load('MovementType', $MovementTypeId);
        $EntrieExit = Tools::getValueFromMacro($this, '%ExecutedMovement.Type.EntrieExit%');

        $LocationExecutedMovtMapper = Mapper::singleton('LocationExecutedMovement');
        $LocationExecutedMovtCollection = $LocationExecutedMovtMapper->loadCollection(array('CancelledMovement' => $this->getId()));

        $Quantity = $this->getQuantity();
        for($i = 0; $i < $LocationExecutedMovtCollection->getCount(); $i++){
            $item = $LocationExecutedMovtCollection->getItem($i);
            $Quantity -= $item->getQuantity();
        }
        return $Quantity;
    }

    // }}}
    // LocationExecutedMovement::getCancelledQuantity() {{{

    /**
     * Si LEM annule, ou reintegre, donne la qte d'UV reintegree en stock
     *
     * @param $dateSup si renseigne, limite sup dans le temps: sert a la REedition
     * de BL, pour editer le BL initial, sans les reintegrations ulterieures
     * @access public
     * @return integer
     */
    public function getCancelledQuantity($dateSup=0) {
        require_once('Objects/MovementType.const.php');

        $Quantity = 0;
        // si le mvt n'a pas subi de reintegration / annulation,
        // et un mvt annulateur ne peut etre annule
        if (0 <= $this->getCancelled()) {
            return 0;
        }

        $exm = $this->getExecutedMovement();
        // si mvt non prevu, on ne peut annuler partiellement
        // utilisation de getForeseeable() car un mvt interne peut être avec
        // ou sans prévision
        if (!$exm->isForeseeable()) {
            return $this->getQuantity();
        }
        else {
            if ($dateSup != 0) {
                $filter = new FilterComponent();
                $filter->setItem(new FilterRule('CancelledMovement',
                        FilterRule::OPERATOR_EQUALS,
                        $this->getId()));
                $filter->setItem(new FilterRule('Date',
                        FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                        $dateSup));
                $filter->operator = FilterComponent::OPERATOR_AND;
            }
            else {
                $filter = array('CancelledMovement' => $this->getId());
            }
            $LEMMapper = Mapper::singleton('LocationExecutedMovement');
            $LEMCollection = $LEMMapper->loadCollection($filter);
            for ($i=0;$i<$LEMCollection->getCount();$i++) {
                $LEM = $LEMCollection->getItem($i);
                $Quantity += $LEM->getQuantity();
            }
        }
        return $Quantity;
    }

    // }}}
    // LocationExecutedMovement::isBLEditionPossible() {{{

    /**
     * Retourne true ou false selon qu'un BL a deja ete edite pour ce LEM ou pas
     * @access public
     * @return boolean
     */
    public function isBLEditionPossible() {
        require_once('Objects/MovementType.const.php');
        // entree ou sortie
        $entrieExit = Tools::getValueFromMacro($this, '%ExecutedMovement.Type.EntrieExit%');
        // prevu ou pas
        $foreseeable = Tools::getValueFromMacro($this, '%ExecutedMovement.Type.Foreseeable%');
        // pour editer un BL, il faut que ce soit une sortie, et prevu
        if ($entrieExit <> SORTIE || $foreseeable != 1) {
            return false;
        }

        $CommandId = Tools::getValueFromMacro($this,
                '%ExecutedMovement.ActivatedMovement.ProductCommand.Id%');
        $DeliveryOrderMapper = Mapper::singleton('DeliveryOrder');

        $filter = new FilterComponent();
        $filter->setItem(new FilterRule('Command',
                                      FilterRule::OPERATOR_EQUALS,
                                     $CommandId));
        $filter->setItem(new FilterRule('EditionDate',
                                             FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                                            $this->getDate()));
        $filter->operator = FilterComponent::OPERATOR_AND;

        $DeliveryOrderCollection = $DeliveryOrderMapper->loadCollection($filter);
        if (Tools::isEmptyObject($DeliveryOrderCollection)) {
            return true;
        }
        return false;
    }

    // }}}
    // LocationExecutedMovement::Cancel() {{{

    /**
     * Annule un LEM et les LEMConcreteProduct associes
     * @access public
     * @return void
     */
    public function Cancel(){
        $this->setCancelled(-1);
        $LEMCPCollection = $this->getLEMConcreteProductCollection();
        // Pas de mode de suivi
        if (Tools::isEmptyObject($LEMCPCollection)) {
            return;
        }
        $count = $LEMCPCollection->getCount();
        for($i = 0; $i < $count; $i++){
            $LEMCP = $LEMCPCollection->getItem($i);
            $LEMCP->setCancelled(LEMConcreteProduct::LEMCP_CANCELLED);
            $LEMCP->save();
        }
    }

    // }}}
    // LocationExecutedMovement::createBoxes() {{{

    /**
     * Crée les Box lors de l'enregistrement du LEM, pour les sorties normales
     * de stock.
     *
     * @access public
     * @param boolean $isPartial true si le mouvement est une reprise de partiel
     * @return boolean true si des boxes ont été crées et false sinon
     */
    public function createBoxes() {
        require_once('Objects/Box.php');
        require_once('Objects/Task.const.php');
        require_once('Objects/ActivatedChainTask.php');
        require_once('Objects/SellUnitType.const.php');
        // determiner le nombre de boxes à créer
        $boxnum = ceil($this->getQuantity());
        if ($boxnum == 0) {
            return false;
        }
        $pdt = $this->getProduct();
        $exm = $this->getExecutedMovement();
        $acm = $exm->getActivatedMovement();
        $ack = $acm->getActivatedChainTask();
		if (!($ack instanceof ActivatedChainTask)) {
            trigger_error(
                    'Erreur en base de données: l\'ACM d\'id '
                        . $acm->getId() . 'n\'a pas d\'ACK associée.',
                     E_USER_WARNING);
            return false;
        }
        // on ne crée pas les boxes si pas de tache créatrice de box après la
        // tache en cours.
        if (!$ack->getNextTaskFromRule('isBoxCreatorTask')) {
            return false;
        }
        $cmi = $acm->getProductCommandItem();
        //$ach = $cmi->getActivatedChain();
        $ach = $ack->getActivatedChain();
        //$cmd = $cmi->getCommand();
        $cmd = $acm->getProductCommand();
        // création des entités
        for($i = 0; $i < $boxnum; $i++) {
            $box = new Box();
            $weight = 0;
            if ($cmi instanceof CommandItem) {
                $weight = $cmi->getWeight();
            }
            if ($pdt instanceof Product) {
                $box->setReference($pdt->getBaseReference());
                $box->setCoverType($pdt->getCoverType());
                $weight = $pdt->getSellUnitWeight();
            }
            $box->setCommandItem($cmi);
            $box->setActivatedChain($ach);
            $box->setLocationExecutedMovement($this);
            // volume et poids
            $box->setVolume($this->getVolume());
            // si c'est le dernier, que l'uv est au poids et que la quantité
            // est décimale on met la quantité restante dans le dernier box
            if ($i == $boxnum -1 && $pdt->getSellUnitTypeId() > SELLUNITTYPE_UR
                    && $boxnum - floor($boxnum) != 0)
            {
                $remainingQty = $boxnum - floor($boxnum);
                $box->setWeight($weight * $remainingQty);
            } else {
                $box->setWeight($weight);
            }
            // données expéditeur/destinataire
            if ($cmd instanceof Command) {
                $box->setExpeditor($cmd->getExpeditorId());
                $box->setExpeditorSite($cmd->getExpeditorSiteId());
                $box->setDestinator($cmd->getDestinatorId());
                $box->setDestinatorSite($cmd->getDestinatorSiteId());
            }
            // collection d'ack, il n'y en a qu'une
            $ackCol = new Collection();
            $ackCol->setItem($ack);
            $box->setActivatedChainTaskCollection($ackCol);
            $box->setLevel($ack->getCurrentBoxLevel());
            $box->save();
        }
        // il faut remettre l'état des taches de regroupement à ActivatedChainTask::STATE_TODO si
        // des box ont été créées pour gérer le cas du bug #0001555
        if ($boxnum > 0) {
            $ackCol = $ach->getActivatedChainTaskCollection(
                array('Task.Id'=>TASK_GROUP));
            $count = $ackCol->getCount();
            for($i = 0; $i < $count; $i++){
                $item = $ackCol->getItem($i);
                // les taches qui sont après la tache de stock et qui ont
                // leur état à terminé
                if ($item->getState() == ActivatedChainTask::STATE_FINISHED
                    && ($item->getId() > $ack->getId()))
                {
                    $item->setState(ActivatedChainTask::STATE_TODO);
                    $item->save();
                }
            }
        }
        // retourne true si au moins une box a été créee
        return ($boxnum > 0);
    }

    // }}}
    // LocationExecutedMovement::getVolume() {{{

    /**
     * Retourne le volume
     * @access public
     * @return float
     */
    public function getVolume() {
        $Product = $this->getProduct();
        $volume  = $Product->getVolume();
        if (0 == $volume) {
            $volume = $Product->getSellUnitWidth() *
                $Product->getSellUnitLength() * $Product->getSellUnitHeight();
        }
        return $this->getQuantity() * $volume;
    }

    // }}}
    // LocationExecutedMovement::getWeight() {{{

    /**
     * Retourne le poids
     * @access public
     * @return void
     */
    public function getWeight() {
        $Product = $this->getProduct();
        return $this->getQuantity() * $Product->getSellUnitWeight();
    }

    // }}}
    // LocationExecutedMovement::getName() {{{

	/**
     * Retourne le nom du type de mouvement
     * Va chercher l'info a 2 endroit selon si mvt annulateur ou pas
     *
     * @access public
     * @return string
     */
    public function getName() {
    	if (0 >= $this->getCancelled()) { // Si ce n'est pas un mouvement annulateur
            return Tools::getValueFromMacro($this, '%ExecutedMovement.Type%');
        } else {
            $array = $this->getCancelledConstArray();
            return $array[$this->getCancelled()];
        }
    }

    // }}}
    // LocationExecutedMovement::findPrestation() {{{

    /**
     * findPrestation
     *
     * @param integer $customerId
     * @param mixed $prestationIds array of ids filtre supplementaire sur 
     * des ids de prestation
     * @access public
     * @return void
     */
    public function findPrestation($customerId=false, $prestationIds=array()) {
        $actorId = $customerId?
                $customerId:Tools::getValueFromMacro($this, '%Product.Owner.Id%');
        $mvtTypeId = Tools::getValueFromMacro($this,
                '%ExecutedMovement.Type.Id%');
        $filter = array(
            SearchTools::newFilterComponent('Active', '', 'Equals', 1, 1),
            SearchTools::newFilterComponent('Facturable', '', 'Equals', 1, 1),
            SearchTools::newFilterComponent('Actor',
                'PrestationCustomer().Actor', 'Equals', $actorId, 1, 'Prestation'),
            SearchTools::newFilterComponent('MovementType', 'MovementType().Id',
                'Equals', $mvtTypeId, 1, 'Prestation')
        );
        if (!empty($prestationIds)) {
            $filter[] = SearchTools::newFilterComponent('Id', '', 'In', $prestationIds, 1);
        }

        $filter = SearchTools::filterAssembler($filter);
        $mapper = Mapper::singleton('Prestation');
        $prsCol = $mapper->loadCollection($filter);
        return $prsCol;
    }

    // }}}
    // LocationExecutedMovement::getDeliveryOrder() {{{

    /**
     * Retourne le BL s'il y en a un associe
     * @access public
     * @return object
     */
    public function getDeliveryOrder() {
        require_once('Objects/MovementType.const.php');
        // entree ou sortie
        $entrieExit = Tools::getValueFromMacro($this, '%ExecutedMovement.Type.EntrieExit%');
        // prevu ou pas
        $foreseeable = Tools::getValueFromMacro($this, '%ExecutedMovement.Type.Foreseeable%');
        // Pour existance d'un BL, il faut que ce soit une sortie, et prevu
        if ($entrieExit <> SORTIE || $foreseeable != 1) {
            return false;
        }
        // Recherche du 1er BL pour cette commande, dont EditionDate >= LEM.Date
        $CommandId = Tools::getValueFromMacro($this,
                '%ExecutedMovement.ActivatedMovement.ProductCommand.Id%');
        $DeliveryOrderMapper = Mapper::singleton('DeliveryOrder');

        $filter = new FilterComponent();
        $filter->setItem(new FilterRule('Command',
                                      FilterRule::OPERATOR_EQUALS,
                                     $CommandId));
        $filter->setItem(new FilterRule('EditionDate',
                                             FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                                            $this->getDate()));
        $filter->operator = FilterComponent::OPERATOR_AND;

        $DeliveryOrderCollection = $DeliveryOrderMapper->loadCollection($filter);
        if (Tools::isEmptyObject($DeliveryOrderCollection)) {
            return false;  // Pas de BL
        }
        $DeliveryOrder = $DeliveryOrderCollection->getItem(0);
        if ($this->getCancelled() == -1) {
            $lemMapper = Mapper::singleton('LocationExecutedMovement');
            $cancelerLEM = $lemMapper->load(array('CancelledMovement' => $this->getId()));
            // Le LEM a ete annule *avant* edition du BL => pas de BL
            if ($cancelerLEM->getDate() < $DeliveryOrder->getEditionDate()) {
                return false;  // Pas de BL
            }
        }
        // Si LEM non annule, ou reintegre apres edition du BL, c'est ce BL ci
        return $DeliveryOrder;
    }

    // }}}

}

?>