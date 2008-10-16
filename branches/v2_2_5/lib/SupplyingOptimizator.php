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

class SupplyingOptimizator { 
    // properties {{{
    /**
     *
     * @var    integer $_passedWeekNumber nb de semaine d'historique
     * @access private
     */
    private $_passedWeekNumber = 6;
    /**
     *
     * @var    integer $_futureWeekNumber nb de semaine a afficher
     * @access private
     */
    private $_futureWeekNumber = 6;
    /**
     *
     * @var    integer $_deliveryDelay delai de livraison
     * @access private
     */
    private $_deliveryDelay = 7;
    /**
     *
     * @var    boolean avec ou sans extrapolation par rapport a l'historique
     * @access private
     */
    private $_withExtrapolation = 0;
    /**
     *
     * @var    integer fournisseur selectionne
     * @access private
     */
    private $_supplierId = 0;
    private $_modelIds = 0;
    /**
     *
     * @var    integer $_customerId l'actor connecte, par defaut
     * @access private
     */
    private $_customerId = 0;
    /**
     *
     * @var    array of integer $_tsArray les lundis 00:00 de chaque semaine
     * @access private
     */
    private $_tsArray = array();
    /**
     *
     * @var    array of integer $_collectedData les donnees collectees par getData()
     * @access private
     */
    private $_collectedData = array();
    /**
     *
     * @var    integer $wishedStartDates
     * Les dates souhaitees par semaine, tenant compte du delai de livraison
     * @access public
     */
    public $wishedStartDates = array();
    // }}}
    // SupplyingOptimizator::__construct() {{{

    /**
     * Constructeur.
     *
     * @param mixed $params: tableau des differents parametres qui sont:
     * - modifiables sur la version desktop
     * - stokkees en preferences pour la version web
     * Seul SupplierId est requis
     * @return void
     */
    public function __construct($params=array()) {
        require_once('RPCTools.php');
        $auth = Auth::Singleton();
        $userId = $auth->getUserId();
        $this->_passedWeekNumber = (isset($params['passedWeekNumber']))?
            $params['passedWeekNumber']:
            PreferencesByUser::get('PassedWeekNumber', 6, $userId);
        $this->_futureWeekNumber = (isset($params['futureWeekNumber']))?
            $params['futureWeekNumber']:
            PreferencesByUser::get('FutureWeekNumber', 6, $userId);
        $defaultDeliveryDelay = (isset($params['defaultDeliveryDelay']))?
            $params['defaultDeliveryDelay']:
            PreferencesByUser::get('DefaultDeliveryDelay', 7, $userId);

// TODO: blinder avec EXC??? si supplierId non fourni!!!!!
        $this->_supplierId = $params['supplierId'];
        if (isset($params['customerId'])) {
            $this->_customerId = $params['customerId'];
        }else {  // l'actor connecte par defaut
            $this->_customerId = $auth->getActorId();
        }
        $this->_modelIds = isset($params['modelIds']) ? $params['modelIds'] : array();
        $supplierCustomer = Object::load(
                'SupplierCustomer',
                array('Customer' => $this->_customerId,
                      'Supplier' => $this->_supplierId));
        if (!Tools::isEmptyObject($supplierCustomer)
                    && !is_null($supplierCustomer->getTotalDeliveryDay())) {
            $this->_deliveryDelay = $supplierCustomer->getTotalDeliveryDay();
        }
        else {
            $this->_deliveryDelay = $defaultDeliveryDelay;
        }

        $this->_withExtrapolation = (isset($params['withExtrapolation']))?
            $params['withExtrapolation']:
            PreferencesByUser::get('WithExtrapolation', 0, $userId);


        // certaines requetes se servent de ca. A refactorer??
        $_SESSION['passedWeekNumber'] = $this->_passedWeekNumber;
        $_SESSION['futureWeekNumber'] = $this->_futureWeekNumber;
        $_SESSION['supplierId'] = $this->_supplierId;  // pour ol-desktop
        // Les lundis 00:00 de chaque semaine
        $this->_tsArray = getWeekTimeStamps();

        $wishedStartDate = array();
        for ($j=0; $j <= $this->_futureWeekNumber; $j++) {
            // si la date souhaitee obtenue est < date courante, on prend la date courante
            $timestamp = (strtotime("-". $this->_deliveryDelay." day", $this->_tsArray[$j])>time())?
                    strtotime("-". $this->_deliveryDelay . " day", $this->_tsArray[$j]):time();
            $wishedStartDate[$j] = DateTimeTools::timeStampToMySQLDate($timestamp);
        }
        $this->wishedStartDates = $wishedStartDate;
    }

    // }}}
    // SupplyingOptimizator::getData() {{{

    /**
     * Effectue les requetes de recuperation des donnees
     *
     * @return boolean false si pas de donnees exploitables en base
     */
    public function getData() {
        require_once('SQLRequest.php');
        $return = array();
        /*Retourne pour les Product ayant un fournisseur donne, pour les semaines
         * passees prises en compte, et celles à venir sur lesquelles on fait des
         * previsions, la qte commandee (commandes client uniquement)
         * Sert aussi a determiner les Product a prendre en compte: ceux au moins
         * commandes 1 fois ds ces semaines passees ou pour ces semaines futures*/
        $orderedQtyArray = array();
        $orderedQtyArrayInEstimate = array();
        $internalEntrieQtyArray = $internalExitQtyArray = array();
        $withInternalExit = false;

        for ($j=-$this->_passedWeekNumber+1;$j<=$this->_futureWeekNumber;$j++) {
            // 604800: Nb de sec dans une semaine
            $TimeStampEnd = $this->_tsArray[$j] + 604800;
            //  resultset suite a req SQL
            $rs = getOrderedQtyPerWeekForSupplier($this->_customerId,
                    $this->_supplierId, $this->_tsArray[$j], $TimeStampEnd, $this->_modelIds);

            while ($rs && !$rs->EOF){
                if ($j == 0) { // patch moche pour forcer l'obtention d'un dico, pas une liste!!
                    $orderedQtyArray[$rs->fields['pdtId']]["##"] = 0;
                }
                if (isset($orderedQtyArray[$rs->fields['pdtId']][strval($j)])) {
                    $orderedQtyArray[$rs->fields['pdtId']][strval($j)] +=
                        floatval($rs->fields['Qty']);
                } else {
                    $orderedQtyArray[$rs->fields['pdtId']][strval($j)] =
                        floatval($rs->fields['Qty']);
                }
                if ($rs->fields['isEstimate'] == 1) {
                    if (isset($orderedQtyArrayInEstimate[$rs->fields['pdtId']][strval($j)])) {
                        $orderedQtyArrayInEstimate[$rs->fields['pdtId']][strval($j)] +=
                            floatval($rs->fields['Qty']);
                    } else {
                        $orderedQtyArrayInEstimate[$rs->fields['pdtId']][strval($j)] =
                            floatval($rs->fields['Qty']);
                    }
                }
                $rs->moveNext();  // strval(): necessaire, sinon, si seulemt la cle 0,
            }                     // ça donne une liste, et pas un dictionnaire!!
            

            // Si Qtes commandees par des Clients pour les semaines a venir,
            // on cherche si des livraisons ont ete faites en avance, et on retranche les qtes
            // N'influera pas le calcul des moyennes de conso, car ds le futur
            if ($j >= 1) {
                $rs2 = getDeliveredQtyPerWeekForSupplier($this->_customerId,
                        $this->_supplierId, $this->_tsArray[$j], $TimeStampEnd, $this->_modelIds);
                while ($rs2 && !$rs2->EOF){
                    if (isset($orderedQtyArray[$rs2->fields['pdtId']][strval($j)])) {
                        $orderedQtyArray[$rs2->fields['pdtId']][strval($j)] -=
                            floatval($rs2->fields['realQty']);
                    }
                    $rs2->moveNext();
                }
                unset($rs2);
            }

            // Mouvements internes
            // Remarque: pas le meme comportement selon l'indice de semaine:
            // Si $week<1 => on ne recupere que les sorties internes (toutes: livrees ou non)
            // Si $week>=1 => on recupere les entrees et sorties internes *non encore livrees*
            $rs3 = getInternalActivatedMovementPerWeek(
                    $this->_supplierId, $this->_tsArray[$j], $TimeStampEnd, $j, $this->_modelIds);
            while ($rs3 && !$rs3->EOF) {
                $qty = floatval($rs3->fields['qty']);
                if ($qty > 0) {
                    $internalEntrieQtyArray[$rs3->fields['pdtId']][strval($j)] = $qty;
                    // Initialisation
                    $internalExitQtyArray[$rs3->fields['pdtId']][strval($j)] = 0;
                }elseif ($qty < 0) {
                    $internalExitQtyArray[$rs3->fields['pdtId']][strval($j)] = abs($qty);
                    $withInternalExit = true;
                }
                // Si qty = 0, on ne fait rien
                $rs3->moveNext();
            }
            unset($rs3);
        }
        $weekEndTimestamp = getWeekEndTimeStamp(); // fin de semaine courante

        // Commandes client en retard ou a mvter avant fin de semaine courante:
        // reverse pour l'affichage seulement, sur la semaine 0
        // N'influera pas le calcul des moyennes de conso
        // On ne peut passer un array de pdtIds ici, car si des pdts n'ont ete commandes
        // que pour une date < au creneau de passe etudie!!
        // On considere que ces qtes vont sortir de stock d'ici a fin de semaine,
        // donc sert aussi à estimer le stock en fin de semaine 0
        $rs = getLateOrderedQty($this->_supplierId, $this->_customerId, $weekEndTimestamp, $this->_modelIds);
        $orderedQtyBeforeEndOfWeek = array();
        while ($rs && !$rs->EOF) {
            // initialisation
            if (!isset($orderedQtyArray[$rs->fields['pdtId']])) {
                $orderedQtyArray[$rs->fields['pdtId']] = array();
            }
            $orderedQtyBeforeEndOfWeek[$rs->fields['pdtId']] = floatval($rs->fields['qty']);
            $rs->moveNext();
        }
        // Pas de donnees a exploiter dans ce cas
        if ((count($orderedQtyArray) == 0 && count($orderedQtyArrayInEstimate) == 0) 
            && $withInternalExit === false) {
            return false;
        }

        // $productIdArray: on va y appliquer saisonalites, promos
        $productIdArray = array_keys($orderedQtyArray);
        $internalACMProductIdArray = array_keys($internalExitQtyArray);
        $allProductIdArray = array_merge($productIdArray, $internalACMProductIdArray);
        $return['internalEntrieQtyArray'] = $internalEntrieQtyArray;
        $return['internalExitQtyArray'] = $internalExitQtyArray;

        /* Pour les Product concernes, pour les semaines a traiter,
         * la qte commandee pour reappro (commandes fournisseur uniquement)*/
        $waitedQtyArray = array();

        for ($j=1;$j<=$this->_futureWeekNumber;$j++) {
            //  resultset suite a req SQL, sur $allProductIdArray!!
            $rs = getWaitedQtyPerWeek($allProductIdArray, $this->_customerId,
                    $this->_tsArray[$j+1], $this->_tsArray[$j]);
            while ($rs && !$rs->EOF){
                $waitedQtyArray[$rs->fields['pdtId']][$j] = floatval($rs->fields['qty']);
                $rs->moveNext();
            }
        }

        // Les entrees normales prevues avant fin de semaine courante,
        // et non encore effectuees
        // On considere que tout ça va rentrer en semaine 0!!
        $rs = getWaitedQtyPerWeek($allProductIdArray,
                $this->_customerId, $weekEndTimestamp);
        $waitedQtyBeforeEndOfWeek = array();
        while ($rs && !$rs->EOF) {
            $waitedQtyBeforeEndOfWeek[$rs->fields['pdtId']] = floatval($rs->fields['qty']);
            $rs->moveNext();
        }

        // On en profite pour initialiser à 0 $waitedQtyArray[$pdtId][0]
        foreach($allProductIdArray as $pdtId) {
            $waitedQtyArray[$pdtId][0] = 0;
            for ($i=1; $i <= $this->_futureWeekNumber; $i++) {
                if (!isset($waitedQtyArray[$pdtId][$i])) {
                    $waitedQtyArray[$pdtId][$i] = 0;
                }
            }
            // On ajoute pour la semaine 0 les livraisons attendues a livrer avant
            // fin de semaine 0
            if (isset($waitedQtyBeforeEndOfWeek[$pdtId])) {
                $waitedQtyArray[$pdtId][0] += $waitedQtyBeforeEndOfWeek[$pdtId];
            }
            if (!isset($orderedQtyBeforeEndOfWeek[$pdtId])) {
                $orderedQtyBeforeEndOfWeek[$pdtId] = 0;
            }
        }

        /* Pour chacun des Product concernes, la quantite reelle en stock prevue
         * en fin de la semaine courante:
         * Qte reelle en stock actuellement - qtes sorties + qtes entrees
         * (d'ici a la fin de semaine) */
        $realStockAtEndOfWeek = array();
        $orderedQty = array();  // sorties de stock attendues d'ici a fin de semaine
        $waitedQty = array();   // entrees en stock attendues d'ici a fin de semaine
        $internalQty = array(); // entrees - sorties internes attendues d'ici a fin de semaine
        foreach($allProductIdArray as $pdtId) {
            $orderedQty[$pdtId] = $waitedQty[$pdtId] = $internalQty[$pdtId] = 0;
		}

        /* Mouvements internes: ACM d'entree et sortie en retard:
         * reporte tout ca pour la semaine 1
        */
        $internalExitQtyBeforeEndOfWeek = array();
        $rs = getInternalMvtQtyBeforeEndOfWeek($allProductIdArray, $weekEndTimestamp);
        while ($rs && !$rs->EOF) {
            $qty = floatval($rs->fields['qty']);
            if ($qty > 0) {  // Entree interne
                // Reporte sur semaine 0
                $waitedQtyArray[$rs->fields['pdtId']][0] += $qty;
            }
            else {  // Sortie interne
                $internalExitQtyBeforeEndOfWeek[$rs->fields['pdtId']] = abs($qty);
            }
            $rs->moveNext();
        }

        $return['waitedQtyArray'] = $waitedQtyArray;
        $return['orderedQtyArray'] = $orderedQtyArray;
        $return['orderedQtyArrayInEstimate'] = $orderedQtyArrayInEstimate;

        if ($this->_withExtrapolation == 1) {
            /* Pour chacun des Product concernes,
             * pour les semaines a traiter la somme des impacts de:
             *   - la Promotion qui, chaque semaine, a le ApproIpactRate le plus fort
             *   - la Saisonalite eventuelle oubien 0 par defaut */
             // Uniquement sur $productIdArray (pas prise en compte des mvts internes)
            $promoImpactRate = getProductPromoImpactPerWeek($productIdArray);
            $impactPerWeek = getPromoSaisonalityImpactPerWeek(
                    $productIdArray, $promoImpactRate);
            $return['promoSaisonImpactPerWeek'] = $impactPerWeek;
        }


        /* Pour chacun des Product concernes, les infos necessaires sur tous ses
         * Product pour le calcul des stats, et l'affichage des resultats */
        $productInfoList = array();
        // Pour trier par BaseReference, vu que cote python, on manipule des
        // dictionnaires (non ordonnes)
        $productIdSorted = array();
        $models = array();
        // Stock a l'instant now
        $rs = getProductInfoList($allProductIdArray, $this->_supplierId);
        while ($rs && !$rs->EOF){
            $stockQty[$rs->fields['pdtId']] = floatval($rs->fields['QR']);
            if (!isset($internalExitQtyBeforeEndOfWeek[$rs->fields['pdtId']])) {
                $internalExitQtyBeforeEndOfWeek[$rs->fields['pdtId']] = 0;
            }
            $orderedQty = $internalExitQtyBeforeEndOfWeek[$rs->fields['pdtId']]
                    + $orderedQtyBeforeEndOfWeek[$rs->fields['pdtId']];
            $productInfoList[$rs->fields['pdtId']] =
                    array('BaseReference' => strval($rs->fields['pdtBaseReference']),
                          'Name' => $rs->fields['pdtName'],
                          'MiniStoredQty' => floatval($rs->fields['MiniStoredQty']),
                          'BuyUnitQty' => floatval($rs->fields['BuyUnitQty']),
                          'QV' => floatval($rs->fields['QV']),
                          'QR' => floatval($rs->fields['QR']),
                          'exitQtyBeforeEndOfWeek' => $orderedQty,
                          'supplierRef' => strval($rs->fields['supplierRef']));
            $productIdSorted[] = intval($rs->fields['pdtId']);
            $models[$rs->fields['pdtId']] = $rs->fields['pdtModel'];
            $rs->moveNext();
        }
        $return['productInfoList'] = $productInfoList;
        // Pas utile pour la version web
        $return['productIdSorted'] = $productIdSorted;
        $return['models'] = $models;

        foreach($allProductIdArray as $pdtId) {
            /*  Recuperation de la Qte en stock en fin de semaine 0  */
            $realStockAtEndOfWeek[$pdtId] =
                $stockQty[$pdtId] - $orderedQtyBeforeEndOfWeek[$pdtId]
                + $waitedQtyArray[$pdtId][0] - $internalExitQtyBeforeEndOfWeek[$pdtId];
        }
        $return['realStockAtEndOfWeek'] = $realStockAtEndOfWeek;
        $this->_collectedData = $return;
        return $return;
    }

    // }}}
    // SupplyingOptimizator::renderData() {{{

    /**
     * Met en forme les donnees collectees et effectue qques calculs
     *
     * @return array or boolean false si rien a afficher
     */
    public function renderData() {
        $data = $this->_collectedData;
        /* Qtes commandees (sorties) par semaine ecoulee, et pour les semaines futures,
         pour le Supplier choisi
         Servira a comparer les previsions de conso avec les qtes deja commandees
         Sert aussi a determiner les Product a prendre en compte:
         ceux au moins commandes 1 fois ds/pour les semaines passees ou futures etudiees */
        $OrderedQtyPerWeek = $data['orderedQtyArray'];
        $OrderedQtyPerWeekInEstimate = $data['orderedQtyArrayInEstimate'];
        // Qtes commandees pour reappro (entrees) par semaine
        $WaitedQtyPerWeek = $data['waitedQtyArray'];
        $Models = $data['models'];

        $ProductIdArray = array();
        $AllProductIdArray = array();
        // Contient aussi les Product impliques dans des ACM internes
        $AllProductIdArray = array_keys($WaitedQtyPerWeek);

        if (!empty($OrderedQtyPerWeek)) {
            $ProductIdArray = array_keys($OrderedQtyPerWeek);
            // On complete le tableau avec des 0 pour les semaines ou pas de qte commandee
            foreach($OrderedQtyPerWeek as $pdtId=>$qties) {
                for($i = -$this->_passedWeekNumber + 1; $i <= $this->_futureWeekNumber; $i++){
                    if (!isset($qties[$i])) {
                        $OrderedQtyPerWeek[$pdtId][$i] = 0;
                    }
                }
            }
        }
        $Week0StockQty = array();
        $StockQty = array();
        $Week0StockQty = $data['realStockAtEndOfWeek'];
        foreach($AllProductIdArray as $pdtId) {
            $StockQty[$pdtId] = array(0 => $Week0StockQty[$pdtId]);
        }

        $ProductInfoList = $data['productInfoList'];
        // Mouvements internes
        $internalEntrieQtyArray = $data['internalEntrieQtyArray'];
        $internalExitQtyArray = $data['internalExitQtyArray'];
        // Initialisation a 0
        foreach($internalExitQtyArray as $pdtId=>$qties) {
            if (!isset($internalEntrieQtyArray[$pdtId])) {
                $internalEntrieQtyArray[$pdtId] = array();
            }
            for($i = -$this->_passedWeekNumber + 1; $i <= $this->_futureWeekNumber; $i++) {
                if (!isset($qties[$i])) {
                    $internalExitQtyArray[$pdtId][$i] = 0;
                }
                if (!isset($internalEntrieQtyArray[$pdtId][$i])) {
                    $internalEntrieQtyArray[$pdtId][$i] = 0;
                }
            }
        }
        //$data['productIdSorted']; pas utilise pour la version web

        /*
         * Calculs
         */
        $QtyToOrder = array();

        // Si on tient compte de l'historique, des saisonalites, et promos
        if ($this->_withExtrapolation) {
            # Recuperation des impacts resultant des promo ET des saisonnalites
            # Somme: (Impacts Promotions + Impacts Saisonalites + 100) par semaine
            $ImpactPerWeek = $data['promoSaisonImpactPerWeek'];
            $FreeQuantity = array();
            $InternalExitQuantity = array();
        }

        foreach($AllProductIdArray as $pdtId) {
            $QtyToOrder[$pdtId] = array();
            $MiniStoredQty = $ProductInfoList[$pdtId]['MiniStoredQty'];
            $totalQtyToOrder = 0;  # Qte a reapprovisionner
            $totalOrderedQty = 0;  # Qte commandee (commandes client)
            // Si on tient compte de l'historique, des saisonalites, et promos
            // Ca ne concerne pas les Product impliques seulement dans des ACM internes
            if ($this->_withExtrapolation) {
                $FreeQuantity[$pdtId] = array();
                $InternalExitQuantity[$pdtId] = array();

                // Calcul des quantites 'brutes' commandees les dernieres semaines,
                // independamment des promo ET des saisonnalites
                for($i = -$this->_passedWeekNumber + 1; $i <=0 ; $i++) {
                    if (isset($OrderedQtyPerWeek[$pdtId])) {
                        $FreeQuantity[$pdtId][$i] =
                            ($OrderedQtyPerWeek[$pdtId][$i] * 100) / $ImpactPerWeek[$pdtId][$i];
                    }
                    if (isset($internalExitQtyArray[$pdtId])) {
                        // Pour faire la moyenne glissante(, puis le sup!!!)
                        $InternalExitQuantity[$pdtId][$i] = $internalExitQtyArray[$pdtId][$i];
                    }
                }
            }
            if (!isset($OrderedQtyPerWeek[$pdtId])) {
                $OrderedQtyPerWeek[$pdtId] = array();
            }
            // initialisation: la semaine 0 n'est pas a prevoir, traitee a part
            $QtyToOrder[$pdtId][0] = 0;
            if ($MiniStoredQty - $StockQty[$pdtId][0] > 0) {
                $QtyToOrder[$pdtId][0] = $MiniStoredQty - $StockQty[$pdtId][0];
                // pour ne pas supprimer les produits a l'affichage
                $totalQtyToOrder = $QtyToOrder[$pdtId][0];
            }

            for($i = 1; $i <=$this->_futureWeekNumber ; $i++) {
                // Si on tient compte de l'historique, des saisonalites, et promos
                if ($this->_withExtrapolation && isset($ProductIdArray[$pdtId])) {
                    
                    # Calcul des previsions de Qtes brutes pour les semaines
                    # a venir a partir du calcul des moyennes des quantites
                    # 'brutes' commandees les X dernieres semaines, pour chaque semaine a venir
                    $FreeQuantity[$pdtId][$i] =
                        array_sum($FreeQuantity[$pdtId]) / count($FreeQuantity[$pdtId]);
                    unset($FreeQuantity[$pdtId][$i - $this->_passedWeekNumber]);

                    // Calcul des previsions de consommation pour les semaines a venir,
                    // en appliquant les impacts des Promo/Saisonality
                    $OrderedQtyPerWeek[$pdtId][$i] = max(
                        $OrderedQtyPerWeek[$pdtId][$i],
                        $FreeQuantity[$pdtId][$i] * $ImpactPerWeek[$pdtId][$i] / 100
                    );
                }
                // Mouvements internes
                if (isset($internalExitQtyArray[$pdtId])) {  // AllProductIdArray
                    // WaitedQtyPerWeek[$pdtId] existe deja (construit cote serveur)
                    if (!isset($OrderedQtyPerWeek[$pdtId][$i])) {
                        $OrderedQtyPerWeek[$pdtId][$i] = 0;
                    }
                    // Si avec historique (extrapolation), on fait les moyennes glissantes, puis
                    // le sup avec la qte prevue a sortir en interne
                    if ($this->_withExtrapolation) {
                        $InternalExitQuantity[$pdtId][$i] =
                            array_sum($InternalExitQuantity[$pdtId]) / count($InternalExitQuantity[$pdtId]);
                        unset($InternalExitQuantity[$pdtId][$i - $this->_passedWeekNumber]);
                        $internalExitQtyArray[$pdtId][$i] = max(
                            $internalExitQtyArray[$pdtId][$i],
                            $InternalExitQuantity[$pdtId][$i]
                        );
                    }
                    $OrderedQtyPerWeek[$pdtId][$i] += $internalExitQtyArray[$pdtId][$i];
                    # Seulement pour les semaines >=1
                    $WaitedQtyPerWeek[$pdtId][$i] += $internalEntrieQtyArray[$pdtId][$i];
                }
//$WaitedQtyPerWeek[$pdtId][0] += $internalEntrieQtyArray[$pdtId][0];

                // Calcul du stock reel previsionnel en fin des semaines a venir
                // Et deduction de la quantite a commander pour reapprovisionnement, par semaine
                $StockQty[$pdtId][$i] = $StockQty[$pdtId][$i-1] - ceil($OrderedQtyPerWeek[$pdtId][$i])
                    + $WaitedQtyPerWeek[$pdtId][$i] + $QtyToOrder[$pdtId][$i-1];
//                echo "\n Stock prevu pour " + $pdtId + " à la fin de semaine " + $i + ': ' + str(StockQty[$pdtId][$i])
//                echo "\n Detail calcul: " + str(StockQty[$pdtId][str(i-1)]) + " - " + str(ceil(OrderedQtyPerWeek[$pdtId][$i])) + " + " + str(WaitedQtyPerWeek[$pdtId][$i]) + " + " + str(QtyToOrder[$pdtId][str(i-1)])
                $QtyToOrder[$pdtId][$i] = max($MiniStoredQty - $StockQty[$pdtId][$i], 0);

                $totalOrderedQty += $OrderedQtyPerWeek[$pdtId][$i];
                $totalQtyToOrder += $QtyToOrder[$pdtId][$i] ;
            }
            if ($totalQtyToOrder == 0) { // && $totalOrderedQty == 0) {
                //echo "\n On supprime l'entrée %s car Qte commandee=Qte pour reappro=0, et pas de sortie interne" % (str(x))
                unset($OrderedQtyPerWeek[$pdtId], $WaitedQtyPerWeek[$pdtId],
                    $QtyToOrder[$pdtId], $ProductInfoList[$pdtId], $Models[$pdtId]);
                $AllProductIdArray = array_diff($AllProductIdArray, array($pdtId));
            }
        }
        // Si pas de qtes commandees
        if ((empty($OrderedQtyPerWeek) || empty($ProductInfoList)) && empty($internalExitQtyArray)) {
            return false;
        }

        // Recuperation des infos sur des Product de Substitution en stock
        ///$substitutionInfoList = self.safeExecute('optimAppro.getSubstitutionInfoList', AllProductIdArray)
            
        $substitutionInfoList = SupplyingOptimizator::getSubstitutionInfoList(
                $AllProductIdArray, $this->_supplierId);
        // Plus logique de stocker cette info dans $OrderedQtyPerWeek
        foreach($ProductInfoList as $pdtId=>$infos) {
            $OrderedQtyPerWeek[$pdtId][0] = $infos['exitQtyBeforeEndOfWeek'];
        }
        return array(
                'OrderedQtyPerWeek' => $OrderedQtyPerWeek,
                'OrderedQtyPerWeekInEstimate' => $OrderedQtyPerWeekInEstimate,
                'WaitedQtyPerWeek' =>$WaitedQtyPerWeek,
                'QtyToOrder' => $QtyToOrder,
                'ProductInfoList' => $ProductInfoList,
                'SubstitutionInfoList' => $substitutionInfoList,
                'Models' => $Models
        );
    }

    // }}}
    // SupplyingOptimizator::getSubstitutionInfoList() {{{

    /**
     * Retourne les infos a afficher en cas de substitution possible
     * @param $ProductIdArray array of integer (Ids de Product)
     * @param $supplierId integer (utile seulemt pour la version desktop)
     * @access public
     * @static
     * @return array of strings
     **/
    public static function getSubstitutionInfoList($ProductIdArray, $supplierId) {
        $SubstitutionInfo = array();  // Contiendra les resultats

        foreach($ProductIdArray as $pdtId) {
            $Product = Object::load('Product', $pdtId);

            // Retourne une collection de Product en stock, et actives
            $substitutCollection = $Product->getProductCollectionForSubstitution(
                    0, 0, $supplierId, 0);
            if (Tools::isEmptyObject($substitutCollection)) {
                $SubstitutionInfo[$pdtId] = '';
                continue;
            }
            $SubstitutionInfo[$pdtId] = ' (';
            $count = $substitutCollection->getCount();
            for($i = 0; $i < $count; $i++) {
                $SubstitutionInfo[$pdtId] .=
                    $substitutCollection->getItem($i)->getBaseReference()
                    . ($i == $count - 1)?')':', ';
            }
        }
        return $SubstitutionInfo;
    }
    // }}}
}

?>
