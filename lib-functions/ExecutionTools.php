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

function isLPQActivated($LPQCollection) {

    $DisabledLocationNameArray = array();
    for($i = 0; $i < $LPQCollection->getCount(); $i++){
         $item = $LPQCollection->getItem($i);
         if (0 == $item->GetActivated()) {  // si desactive
             $DisabledLocationNameArray[] = Tools::getValueFromMacro($item, '%Location.Name%');
         }
    }
    return $DisabledLocationNameArray;
}


/**
 * Retourne la collection des LPQ pour lesquels une qte >0 a ete saisie.
 * @access public
 * @return void
 **/
function SubmittedLPQCollection() {
    $LocationPdtQtiesCollection = new Collection();
    while(list($key, $val) = each ($_POST["lpqId"])) {
        /*  $_POST["lpqId"] > 0 prouve que le LPQ existe deja en base; sinon, controle inutile  */
        if ($_POST["lpqId"] > 0 && isset($_POST["QuantityArray"][$key]) && 0 < $_POST["QuantityArray"][$key]) {  // si qte >0 a ete saisie
            $LocationProductQuantities = Object::load("LocationProductQuantities", $val);
            $LocationPdtQtiesCollection->SetItem($LocationProductQuantities);
        }
        unset($LocationProductQuantities);
    }
    return $LocationPdtQtiesCollection;
}

/**
 * Affiche un message d'erreur si une qte >0 a ete saisie pour un LPQ finalement desactive
 * @access public
 * @return void
 **/
function ActivatedLPQControl() {
    $LPQCollection = SubmittedLPQCollection();
    $DisabledLocationNameArray = isLPQActivated($LPQCollection);
    $htmlListLocationName = "";
    if (count($DisabledLocationNameArray) > 0) {
        for ($i=0; $i<count($DisabledLocationNameArray);$i++) {
            $htmlListLocationName .= "<li>" . $DisabledLocationNameArray[$i] . "</li>";
        }
        $WithWithout = (isset($_POST["ActivatedMvtId"]))?"With":"Without";
        $MovementInfo = (isset($_POST["ActivatedMvtId"]))?"ActivatedMvt=".$_POST["ActivatedMvtId"]:"MvtType=".$_POST["MvtType"];
        Template::errorDialog(
            _("Movement could not be executed because the following locations were temporary deactivated for inventory") .
            ".<ul>".$htmlListLocationName."</ul>",
            "ActivatedMovementAdd".$WithWithout."Prevision.php?".$MovementInfo."&product=".$_POST["Product_id"]."&Comment=".htmlentities(urlencode($_POST["Comment"])));
        exit;
    }
}

/**
 * Effectue des controles sur les saisies
 * @access public
 * @var string $cancelLink : lien de retour
 * @var boolean $foreseeable : avec/sans prevision
 * @return void
 **/
function checkBeforeMovement($cancelLink, $foreseeable=true){
    //on verifie qu'il y a des emplacements
    // Si pas d'emplacement utilise, message different selon si entree ou sortie
    if (!isset($_POST['lpqId'])) {
        $errorMsge = ($_POST['MvtTypeEntrieExit'] == ENTREE)?
            _('You must add a location.'):
            _('You can\'t validate movement without using a location.');
       Template::errorDialog($errorMsge, $cancelLink);
       exit;
    }
    // Controle  validite des champs saisis par emplacement : des entiers
    // Gestion des formats de nombres suivant les locales
    $_POST['QuantityArray'] = array_map(array('I18N', 'extractNumber'), $_POST['QuantityArray']);
    
    reset($_POST['QuantityArray']);

    // Nombre d'emplacements qui ont donne lieu a une saisie de qte
    // Utilise pour les mvts sans prevision, car tout mvt avec Qte nulle est refuse
    $filledQuantity = 0;

    while(list($key, $val) = each ($_POST['QuantityArray'])) {
        if (!is_numeric($val)){
            Template::errorDialog(
                _('Movement could not be executed because you provided wrong quantities.'),
                $cancelLink . '&Product=' . $_POST['Product_id']
            );
            exit;
        } else if ($val > 0) {  // si une qte valide est saisie
            $filledQuantity = $filledQuantity + 1; // utile pour refuser les mvts avec une qte nulle
        }
    }
    // Pour un mvt sans prevision,
    // si au moins une qte saisie, avec Qte > 0, On accepte le mvt, sinon, refus
    if (false == $foreseeable && $filledQuantity == 0) {
        Template::errorDialog(
            _('Movement could not be executed because quantity provided is null.'),
            $cancelLink
        );
        exit;
    }
    reset($_POST['QuantityArray']);

    // Si pas de mode de suivi, pas de controle supplementaire
    // Idem si qte mouvementee est nulle
    if ($_REQUEST['TracingMode'] == 0 || $filledQuantity == 0) {
        return true;
    }
    // Controle sur les SN/LOTS: controle sur les quantites par Location
    // Collection des LocationConcreteProduct provenant des saisies effectuees
    $LCPCollection = $_SESSION['LCPCollection'];

    if (Tools::isEmptyObject($LCPCollection)) {
        Template::errorDialog(_('Wrong SN/Lot provided, please correct.'), $cancelLink);
        exit;
    }
    $TotalRealQuantity = array_sum($_POST['QuantityArray']);
    $count = $LCPCollection->getCount();
    $CPnumber = 0;  // Nbre reel de ConcreteProduct saisis
    $ProductQtyPerLocation = array();
    $ConcretePdtQtyPerLocation = array();  // Qtes mouvementees par Location

    for($i = 0; $i < count($_POST['lpqId']); $i++){
        if ($_POST['QuantityArray'][$i] <= 0) {
            continue;
        }
        $LocationId = $_POST['locId'][$i];
        $ProductQtyPerLocation[$LocationId] = $_POST['QuantityArray'][$i];
    }

    // SN
    if ($_REQUEST['TracingMode'] == Product::TRACINGMODE_SN) {
        // Check Nb global de SN saisis OK, puis
        // Check SN saisis sont dans les bons LCP
        for($i = 0; $i <$count ; $i++) {
            $LCP = $LCPCollection->getItem($i);
            $ConcreteProduct = $LCP->getConcreteProduct();
            if ($ConcreteProduct == false) {  // Pas de saisie de SN pour cette ligne
                continue;
            }
            $LocationId = $LCP->getLocationId();
            $ConcretePdtQtyPerLocation[$LocationId] = isset($ConcretePdtQtyPerLocation[$LocationId])?
                                                    $ConcretePdtQtyPerLocation[$LocationId] + 1:1;
            $CPnumber++;
        }

        if ($CPnumber != $TotalRealQuantity) {
            Template::errorDialog(
                _('SN number does not match provided quantities for product by location.'),
                $cancelLink
            );
            exit;
        }
    }

    //LOT
    elseif ($_REQUEST['TracingMode'] == Product::TRACINGMODE_LOT) {
        // Verifie que les saisies correspondent au nbre de lots annonce
        // (Nb total de lots differents utilises)
        if (checkLotNumber() == false) {
            Template::errorDialog(
                _('Lots number does not match provided total lot number by location.'),
                $cancelLink
            );
            exit;
        }
        // Check SN saisis sont dans les bons LCP

        for($i = 0; $i <$count ; $i++){
            $LCP = $LCPCollection->getItem($i);
            $LocationId = $LCP->getLocationId();
            $ConcretePdtQtyPerLocation[$LocationId] =
                    isset($ConcretePdtQtyPerLocation[$LocationId])?
                    $ConcretePdtQtyPerLocation[$LocationId] + $LCP->getQuantity():$LCP->getQuantity();
        }
    }

    $tracingMode = ($_REQUEST['TracingMode'] == Product::TRACINGMODE_LOT)?_('lots'):_('SN');
    foreach ($ProductQtyPerLocation as $key => $value) {
        if (!isset($ConcretePdtQtyPerLocation[$key]) || $value != $ConcretePdtQtyPerLocation[$key]) {
            Template::errorDialog(
                sprintf(_('Provided %s do not match provided quantities for product by location.'),
                    $tracingMode),
                $cancelLink
            );
            exit;
        }
    }

}


/**
 * Effectue des controles pour executer des mvts par lot
 * @var string $cancelLink : lien de retour
 * @var boolean $foreseeable : avec/sans prevision
 * @return void
 **/
function checkBeforeSeveralMovements() {
    $cancelLink = 'ActivatedMovementList.php';
    // Messages d'erreur
    $e_noMovement = _('You have to select one or more movements.');
    $e_severalCommands = _('All selected movements have to be linked to the same command.');
    $e_withTracingMode = _('All selected movements have to be linked to a product whithout tracing mode.');
    $e_onlyNormalMovement = _('All selected movements have to be normal issue or entry.');
    $e_onlyCreated = _('All selected movement states have to be "Created".');
    $e_onlyOneLPQ = _('Each selected movement have to be execute on one and only one location.');
    $e_LpqBadQty = _('A selected movement can\'t be execute: insufficient stock');
    $e_LpqNotActivated = _('Movement could not be executed because the following location is temporary deactivated for inventory: %s');
    $e_onlyOneSite = _('All selected movements have to be executed in the same site.');
    
    //on verifie qu'il y a des acm postes
    if (!isset($_REQUEST['acmId'])) {
        Template::errorDialog($e_noMovement, $cancelLink);
        exit;
    }
    $cmdIdArray = array();  // Pour controler que les acm selectionnes sont lies a 1 seule cmde
    // Pour controler que les acm selectionnes sont lies a 1 storageSite, dans le cas des sorties
    $siteIdArray = array();  
    foreach ($_REQUEST['acmId'] as $acmId) {
        $acm = Object::load('ActivatedMovement', $acmId);
        
        $cmdIdArray[] = $acm->getProductCommandId();
        if (count(array_unique($cmdIdArray)) == 2) {
            Template::errorDialog($e_severalCommands, $cancelLink);
            exit;
        }
        // Controle: etat A faire ou Partiel only
        if ($acm->getState() != ActivatedMovement::CREE) {
            Template::errorDialog($e_onlyCreated, $cancelLink);
            exit;
        }
        // Controle: entree ou sortie normal only
        if (!in_array($acm->getTypeId(), array(SORTIE_NORMALE, ENTREE_NORMALE))) {
            Template::errorDialog($e_onlyNormalMovement, $cancelLink);
            exit;
        }
        // Controle: pas de tracingMode
        if ($acm->getProduct()->getTracingMode() > 0) {
            Template::errorDialog($e_withTracingMode, $cancelLink);
            exit;
        }
        // Si sortie, verif que 1 seul LPQ contient le Product, et en qte suffisante
        // Et tout doit se passer dans le MEME site
        $entrieExit = Tools::getValueFromMacro($acm, '%Type.EntrieExit%');
        if ($entrieExit == SORTIE) {
            $pdtId = $acm->getProductId();
            $Filter = getLpqFilter($pdtId);
            $LPQMapper = Mapper::singleton('LocationProductQuantities');
            $LPQCollection = $LPQMapper->loadCollection($Filter);
            if ($LPQCollection->getCount() != 1) {
                Template::errorDialog($e_onlyOneLPQ, $cancelLink);
                exit;
            }
            $lpq = $LPQCollection->getItem(0);
            if ($lpq->getRealQuantity() < $acm->getRemainingQuantity()) {
                Template::errorDialog($e_LpqBadQty, $cancelLink);
                exit;
            }
            // LE LPQ a ete desactive entre-temps
            if ($lpq->getActivated() == 0) {
                Template::errorDialog(
                        sprintf($e_LpqNotActivated, $lpq->getLocation()->getName()), 
                        $cancelLink);
                exit;
            }
            $siteIdArray[] = Tools::getValueFromMacro($lpq, '%Location.Store.StorageSite.Id%');
            if (count(array_unique($siteIdArray)) == 2) {
                Template::errorDialog($e_onlyOneSite, $cancelLink);
                exit;
            }
        }
        
    }
    
    return true;
}

/**
 * Retourne le filtre sur les LPQ, pour GridColumnActivatedMovementList 
 * et pour checkBeforeSeveralMovements()
 * 
 * @param int $ProductId
 * @return object Filter
 **/
function getLpqFilter($ProductId) {
    $Auth = Auth::Singleton();
    $FilterComponentArray = array();
    if (in_array($Auth->getProfile(), array(UserAccount::PROFILE_OPERATOR, 
            UserAccount::PROFILE_GESTIONNAIRE_STOCK))) {
        /*$Filter = array(
                'Product' => $ProductId,
                'Location.Store.StorageSite.Owner' => $Auth->getActorId());*/
        $FilterComponentArray[] = SearchTools::NewFilterComponent(
            'Owner', 'Location.Store.StorageSite.Owner', 'Equals', $Auth->getActorId(), 1);
    }
    if ($Auth->getProfile() == UserAccount::PROFILE_GESTIONNAIRE_STOCK) {
        $uac = $Auth->getUser();
        $siteIds = $uac->getSiteCollectionIds();
        $FilterComponentArray[] = SearchTools::newFilterComponent(
                'Actor', 'Location.Store.StorageSite',
                'In', $siteIds, 1);
    } /*else {
        ///$Filter = array('Product' => $ProductId);
        $FilterComponentArray[] = SearchTools::NewFilterComponent(
                'Product', '', 'Equals', $ProductId, 1);
    }*/

    $FilterComponentArray[] = SearchTools::NewFilterComponent(
                'Product', '', 'Equals', $ProductId, 1);
    $FilterComponentArray[] = SearchTools::NewFilterComponent(
                'RealQuantity', '', 'NotEquals', 0, 1);
    $Filter = SearchTools::filterAssembler($FilterComponentArray);
    return $Filter;
}

/**
 * Verifie si les qtes sont suffisantes en stock, (pour une sortie)
 * @access public
 * @return void
 **/
function checkStockQty($cancelLink) {
    $LPQMapper = Mapper::singleton('LocationProductQuantities');
    reset($_POST['lpqId']);  // controle des quantites saisies par emplacement
    while(list($key, $val) = each ($_POST['lpqId'])) {
        $LPQ = $LPQMapper->load(array('Id' => $val ));
        if (isset($_POST['QuantityArray'][$key]) && $LPQ->getRealQuantity() < $_POST['QuantityArray'][$key]) {
            Template::errorDialog(
                _('Movement could not be executed because some quantity is insufficient for a location.'),
                $cancelLink
            );
            exit;
        }
        unset($LPQ);
    }
}

/**
 * Cree le ou les LEMConcreteProduct pour un LEM donne
 *
 * @param object $LEM LocationExecutedMovement
 * @access public
 * @return object LEMConcreteProduct Collection or boolean
 **/
/*function createLEMConcreteProduct($LEM) {
    $LEMCPCollection = new Collection();  // Collection qui sera retournee
    if ($_REQUEST['TracingMode'] == 0) { // Si pas de mode de suivi
        return $LEMCPCollection;
    }
    $CPMapper = Mapper::singleton('ConcreteProduct');
    $LocationId = $LEM->getLocationId();
    // Collection des LocationConcreteProduct provenant des saisies effectuees
    $LCPCollection = $_SESSION['LCPCollection'];

    $quantity = 1; // Par defaut, on prend: TracingMode=Product::TRACINGMODE_SN
    $count = $LCPCollection->getCount();

    for($i = 0; $i <$count ; $i++){
        $LCP = $LCPCollection->getItem($i);
        if ($LocationId != $LCP->getLocationId() || $LCP->getQuantity() == 0) {
            continue;
        }
        if ($_REQUEST['TracingMode'] == Product::TRACINGMODE_LOT) {
            $quantity = $LCP->getQuantity();
        }
        $LEMCP = Object::load('LEMConcreteProduct');

        $ConcreteProduct = $LCP->getConcreteProduct();

        // Si nouveau ConcreteProduct: il faut le sauver
        // la condition suivante ne suffit pas
        if ($ConcreteProduct->getId() == 0) {
            $testConcreteProduct = $CPMapper->load(
                            array('Product' => $ConcreteProduct->getProductId(),
                                  'SerialNumber' => $ConcreteProduct->getSerialNumber()));

            // Ce ConcreteProduct n'existe pas: il faut le creer
            if (Tools::isEmptyObject($testConcreteProduct)) {
                $ConcreteProduct->save();
            }
            else {
                $ConcreteProduct = $testConcreteProduct;
                $LCP->setConcreteProduct($testConcreteProduct);
            }
        }

        $LEMCP->setConcreteProduct($ConcreteProduct);
        $LEMCP->setLocationExecutedMovement($LEM);
        $LEMCP->setQuantity($quantity);
        $LEMCP->save();
        $LEMCPCollection->setItem($LEMCP);
    }
    return $LEMCPCollection;
}*/

/**
 * Verifie que les saisies correspondent au nbre de lots annonce
 * @access public
 * @return void
 **/
function checkLotNumber() {
    if (!isset($_POST['LotNumber'])) {
        return true;
    }
    $TotalLotNumber = array_sum($_POST['LotNumber']);
    $LCPCollection = $_SESSION['LCPCollection'];
    $count = $LCPCollection->getCount();
    $realTotalLotNumber = 0; // Nb de lots differents utilises
    for($i = 0; $i <$count ; $i++) {
        if ($LCPCollection->getItem($i)->getQuantity() > 0) {
            $realTotalLotNumber ++;
        }
    }
    if ($realTotalLotNumber != $TotalLotNumber) {
        return false;
    }
    return true;
}

/**
 * Creation/suppr/MAJ des LocationConcreteProduct si mode de suivi = SN ou lot
 *
 * @param object $lpq LocationProductQuantities
 * @param object $LEM LEMConcreteProduct Collection
 * @access public
 * @return void
 **/
function updateLocationConcreteProduct($lpq, $LEMCPCollection) {
    if (!($LEMCPCollection instanceof Collection)) {  // Si pas de mode de suivi
        return true;
    }
    $url = 'ActivatedMovementList.php';
    $coef = (($_POST['MvtTypeEntrieExit'] == ENTREE && !isChangeOfPosition())
                || (isset($_REQUEST['CancellationType']) && $_REQUEST['CancellationType'] != ""))?1:-1;

    $LocationId = $lpq->getLocationId();
    // Collection des LocationConcreteProduct provenant des saisies effectuees
    $LCPCollection = isset($_SESSION['LCPCollection'])?$_SESSION['LCPCollection']:
        new Collection();
    $LCPMapper = Mapper::singleton('LocationConcreteProduct');

    $LEMCPcount = $LEMCPCollection->getCount();

    for($j = 0; $j < $LEMCPcount; $j++) {
        $LEMCP = $LEMCPCollection->getItem($j);
        // On recalcule a chaque iteration, car on
        // peut supprimer des elemts de la collection
        for($i = 0; $i <$LCPCollection->getCount() ; $i++) {
            $LCP = $LCPCollection->getItem($i);

            if ($LocationId != $LCP->getLocationId() || $LCP->getQuantity() == 0
                    || $LCP->getConcreteProductId() != $LEMCP->getConcreteProductId()) {
                continue;
            }

            $testLCP = $LCPMapper->load(
                                array('ConcreteProduct' => $LCP->getConcreteProductId(),
                                      'Location' => $LCP->getLocationId()));

            // Si SORTIE ou chgt de position, le LCP doit exister, sinon, erreur
            if (Tools::isEmptyObject($testLCP) && ($_REQUEST['MvtTypeEntrieExit'] == SORTIE
                                        || isChangeOfPosition())) {
                return false;
            }
            elseif (!Tools::isEmptyObject($testLCP)) {
                $testLCP->setQuantity($testLCP->getQuantity() + ($coef * $LCP->getQuantity()));
                if ($testLCP->getQuantity() == 0) {
                    deleteInstance($testLCP, $url);
                } else {  // Entree ou Sortie sur LCP existant
                    saveInstance($testLCP, $url);
                }
            } else {  // ENTREE sur nouvel emplacement: creation de LCP
                saveInstance($LCP, $url);
            }
            unset($testLCP, $LCP);
            $LCPCollection->removeItem($i);
            $i = $i -1;
        }
        unset($LEMCP);
    }

    return true;
}

/**
 * Retourne true si chgt de position, false sinon
 * Utile car ce type de mvt est tres different des autres, puisque
 * il comprend une sortie, puis une entree!
 * @access public
 * @return void
 **/
function isChangeOfPosition() {
    require_once('Objects/MovementType.const.php');
    return (isset($_REQUEST['MvtType']) && ENTREE_DEPLACEMT == $_REQUEST['MvtType']);
}

/**
 * Retourne true si reintegration en stock/annulation, false sinon
 * @access public
 * @return void
 **/
function isCancellation() {
    return (isset($_REQUEST['CancellationType']) && $_REQUEST['CancellationType'] != "");
}

/**
 * Effectue les controles sur les SN/Lots saisis si reintegration
 *
 * @access public
 * @param string $returnURL
 * @return object $LocationConcreteProductCollection
 **/
/*function lcpControlForCancellation($returnURL) {
    $errorMsge = array(
        1 => _('La saisie est incorrecte: un des SN/lot saisis n\'existe pas.'),
        2 => _('La saisie est incorrecte: Un des SN/lot saisis ne correspond pas au mouvement annulé.'),
        3 => _('La saisie est incorrecte: Une quantité saisie est trop importante.'),
        4 => _('La saisie est incorrecte: Un des SN/lot saisis est desactivé.')
    );
    $error = 0;
    $CPMapper = Mapper::singleton('ConcreteProduct');

    // Collection de LocationConcreteProduct qui sera mise en session
    $LCPCollection = new Collection();
    $LEM = Object::load('LocationExecutedMovement', $_REQUEST['LEM']);

    $LEMCPCollection = $LEM->getLEMConcreteProductCollection();
    // Pas de mode de suivi
    if (Tools::isEmptyObject($LEMCPCollection)) {
        return;
    }
    $count = $LEMCPCollection->getCount();
    // Contiendra les SN/No de lot possibles et les qtes max associees
    $waitedCPids = array();
    $returnedQty = array();  // Contiendra les Qtes reintegrees par No de lot

    for($i = 0; $i < $count; $i++){
        $LEMCP = $LEMCPCollection->getItem($i);
        $CPid = $LEMCP->getConcreteProductId();
        $qty = isset($waitedCPids[$CPid])?
                $waitedCPids[$CPid] + $LEMCP->getQuantity():$LEMCP->getQuantity();
        $waitedCPids[$CPid] = $qty;
    }

    for($i = 0; $i < count($_REQUEST['LocationId']); $i++) {
        if ($_REQUEST['SerialNumber'][$i] != '') {
            $testConcreteProduct = $CPMapper->load(
                array(
                    'Product' => $_REQUEST['Product_id'],
                    'SerialNumber' => $_REQUEST['SerialNumber'][$i]
                )
            );

            // Ce ConcreteProduct n'existe pas: erreur
            if (Tools::isEmptyObject($testConcreteProduct)) {
                $error = 1;
                break;
            }
            // Le SN/lot saisi existe mais ne correspond pas au mvt annule
            elseif (! in_array($testConcreteProduct->getId(), array_keys($waitedCPids))) {
                $error = 2;
                break;
            }
            // La Qte saisie est superieure au max possible (mode de suivi=LOT)
            elseif (isset($_REQUEST['Quantity'][$i]) && $_REQUEST['Quantity'][$i] > 0) {
                $CPid = $testConcreteProduct->getId();
                $qty = isset($returnedQty[$CPid])?
                        $returnedQty[$CPid] + $_REQUEST['Quantity'][$i]:$_REQUEST['Quantity'][$i];
                if ($qty > $waitedCPids[$testConcreteProduct->getId()]) {
                    $error = 3;
                    break;
                }
                $returnedQty[$CPid] = $qty;
            }
            // Le concreteProduct est desactive
            elseif($testConcreteProduct->getActive() == 0) {
                $error = 4;
                break;
            }

            // Sinon, tout est OK
            $LCP = Object::load('LocationConcreteProduct');
            $LCP->setConcreteProduct($testConcreteProduct);
            $Location = Object::load('Location', $_REQUEST['LocationId'][$i]);
            $LCP->setLocation($Location);
            $LCP->setId(0);
            $qty = (isset($_REQUEST['CPQuantity'][$i]))?$_REQUEST['CPQuantity'][$i]:1;
            $LCP->setQuantity($qty);
            $LCPCollection->setItem($LCP);
        }
    }
    if ($error > 0) {
        Template::errorDialog($errorMsge[$error], $returnURL, BASE_POPUP_TEMPLATE);
        Exit;
    }
    return $LCPCollection;
}*/

?>
