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

require_once('CommandPotentialActors.php');
require_once('lib/ExceptionCodes.php');

/**
 * @return void
 **/
function includeRequirements() {
    require_once('Objects/Command.const.php');
    require_once('Objects/ProductCommand.inc.php');
    require_once('Objects/ProductCommand.php');
    require_once('Objects/TVA.inc.php');
}

/**
  * Insere les quantites saisies en session si necessaire
  * @param 
  * @return void
  **/
function insertQtiesIntoSession() {
    if (isset($_REQUEST['gridItems'])) {
        $catalogQties =  (isset($_SESSION['catalogQties']))?
            $_SESSION['catalogQties']:array();
        $session = Session::Singleton();
        // Si ce sont des qtes d'UE, on prend la partie entiere car on ne peut
        // saisir des float non entiers, et pas de controle js ds le catalog
     	foreach ($_REQUEST['gridItems'] as $id) {
     	    if (isset($_REQUEST['qty_' . $id]) 
     	    && I18N::validateNumber($_REQUEST['qty_' . $id])
     	    && I18N::extractNumber($_REQUEST['qty_' . $id]) > 0) {
     	         $catalogQties[$id] = (Preferences::get('ProductCommandUEQty'))?
     	              floor($_REQUEST['qty_' . $id]):$_REQUEST['qty_' . $id];
     	    }
     	    
     	}
     	$session->register('catalogQties', $catalogQties, 2);
     }
} 

/**
 * Retourne l'URL de retour
 *
 * @return string
 */
function getReturnURL($commandType) {
    $from = isset($_REQUEST['from']) ? $_REQUEST['from'] : false;
    switch ($from) {
    case 'optimappro':
        $returnURL = 'SupplyingOptimization.php?formSubmitted=1';
        break;
    case 'estimate':
        $returnURL = 'EstimateList.php?dummy';
        break;
    default:
        if ($commandType == Command::TYPE_SUPPLIER) {
            $returnURL = 'SupplierCatalog.php?dummy';
        } else {
            $ctx = Preferences::get('TradeContext', array());
            if (in_array('readytowear', $ctx)) {
                $returnURL = 'dispatcher.php?entity=RTWModel&altname=RTWModelForCatalog'; 
            } else if (in_array('readytowear2', $ctx)) {
                $returnURL = 'dispatcher.php?entity=ProductModel&altname=ProductModelForCatalog'; 
            } else {
                $returnURL = 'CustomerCatalog.php?dummy';
            }
        }
    }
    return $returnURL;
}

/**
 *
 * @access public
 * @return void
 **/
function checkBeforeDisplay($commandType) {
    $returnURL = getReturnURL($commandType);
    $pdtVarName = getProductSessionVarName($commandType);
    if (empty($_SESSION[$pdtVarName])) {
        Template::errorDialog(_('Please select at least one item.'), $returnURL);
        exit;
    }
    
    if ($commandType == Command::TYPE_SUPPLIER) {
        // Recup du supplier en session et prolongement de la var de session associee
        // pour eviter sa perte en cas de rafraichissements successifs de la page
        if (isset($_SESSION['supplier']) && $_SESSION['supplier'] > 0) {
            $session = Session::Singleton();
            $session->prolong('supplier', 2);
        } else {
            Template::errorDialog(E_MSG_SELECT_A_SUPLIER, $returnURL);
            exit;
        }
    }
    elseif ($commandType == Command::TYPE_CUSTOMER) {
        // Recup du supplier en session et prolongement de la var de session associee
        // pour eviter sa perte en cas de rafraichissements successifs de la page
        if (isset($_SESSION['customer']) && $_SESSION['customer'] > 0) {
            $session = Session::Singleton();
            $session->prolong('customer', 2);
        } else {
            Template::errorDialog(_('Please select a customer.'), $returnURL);
            exit;
        }
        
        // Si preference ProductCommandUEQty activee, on verifie que tous les products sont ok:
        // SellUnitType pas dans {UR, UC} ni une unite de mesure
        // Et si sut = UB, il y a une verif de plus a faire
        if (Preferences::get('ProductCommandUEQty')) {
            require_once('Objects/SellUnitType.const.php');
            $productMapper = Mapper::singleton('Product');
            $productColl = $productMapper->loadCollection(
                array('Id' => $_SESSION[$pdtVarName], 
                      'SellUnitType' => array(SELLUNITTYPE_UB, SELLUNITTYPE_UE)));
            // Au moins un des products n'a pas le bon SellUnitType
            if ($productColl->getCount() < count($_SESSION[$pdtVarName])) {
                Template::errorDialog(
                    _('Selling unit of all selected products have to be base unit or packaging unit.'), 
                    $returnURL);
                exit;
            }
            foreach ($productColl as $item) {
                if ($item->getSellUnitTypeId() == SELLUNITTYPE_UB && $item->getUnitNumberInPackaging() == 0) {
                    Template::errorDialog(
                        _('For all selected products, if selling unit is base unit, number of base unit in packaging can\'t be zero.'), 
                        $returnURL);
                    exit;
                }
            }
        }
    }

    // On verifie si un des product n'a pas ete desactive ou desaffecte
    // XXX à refaire
    /*
    $ProductCollection = $ProductMapper->loadCollection(
            array('Id' => $_SESSION[$pdtVarName], 'Activated' => 1, 'Affected' => 1),
            array('BaseReference' => SORT_ASC));

    $pdtCount = $ProductCollection->getCount();
    if (count($_SESSION[$pdtVarName]) > $pdtCount) {
        Template::errorDialog(
            _('Un ou plusieurs des produits commandés a été désactivé ou désaffecté d\'une chaîne.')
            . _('<br>Vous ne pouvez pas les commander...'), $returnURL);
        exit;
    }
     */
    return true;
}

/**
 * Effectue les controles sur les saisies
 * @access public
 * @return void
 **/
function checkBeforeSubmit($commandType) {
    // Verification que le nb de produits a commander est > 0
    if (0 == array_sum($_REQUEST['qty'])) {
        Template::errorDialog(_('You must select at least one product to order.'),
                       getErrorURL($commandType));
        exit;
    }
    // Verification de la date
    if (empty($_REQUEST['StartDate'])) {
        Template::errorDialog(E_MSG_CHOICE_DATE, getErrorURL($commandType));
        exit;
    }
    $WishedEndDate = ($_REQUEST['WishedDate'] == 1)?$_REQUEST['EndDate']:'NULL';
    if ($WishedEndDate != 'NULL' && $_REQUEST['StartDate'] > $WishedEndDate) {
        Template::errorDialog(E_MSG_CHECK_DATE, getErrorURL($commandType));
        exit;
    }
    // Verification du No de Commande et Facture saisis
    if ($_REQUEST['cmdNumber']) {
        $cmdMapper = Mapper::singleton('Command');
        if ($cmdMapper->alreadyExists(array('CommandNo' => $_REQUEST['cmdNumber']))) {
            Template::errorDialog(
                _('Provided order number is already allocated, please correct.'),
                getErrorURL($commandType));
            exit;
        }
    }
    // Remarque: la verif sur les qtes mini commandables se fait "plus loin"
    // dans CommandManager::addCommandItem()
}

/**
 * Retourne la bonne url en cas d'erreur, pour conserver les saisies
 * @param integer $cmdType
 * @return string
 **/
function getErrorURL($cmdType) {
    // Les chps de saisie (hors grid, geres via ajax et session:
    $fields = array('cmdNumber', 'cmdIncoterm', 'cmdExpeditor', 'cmdDestinator',
        'cmdExpeditorSite', 'cmdDestinatorSite', 'cmdProjectManager',
        'cmdCommercial', 'Port', 'Emballage','Assurance', 'Installment',
        'GlobalHanding', 'cmdComment', 'cadencedOrder', 'cadencedOrderCB',
        'WishedDate', 'StartDate', 'EndDate', 'from', 'isEstimate');
    $stringToPass = UrlTools::buildURLFromRequest($fields, false);// not$mochikitFormatted
    $url = ($cmdType == Command::TYPE_SUPPLIER)?'ProductCommandSupplier.php?':'ProductCommand.php?';
    return $url . $stringToPass;
}

/**
 * Persistence des widgets de date
 *
 */
function restoreDates($smarty, $dateType, $startDate, $endDate) {
    $smarty->assign('WishedDate', $dateType);
    $startDate = $startDate?$startDate:0;
    $smarty->assign('WishedStartDate', $startDate);
    $smarty->assign('FWishedStartDate', I18N::formatDate($startDate));
    $endDate = $endDate?$endDate:0;
    $smarty->assign('WishedEndDate', $endDate);
    $smarty->assign('FWishedEndDate', I18N::formatDate($endDate));
}

/**
 * Retourne le nom de la variable en session contenant les Product selectionnes
 * @return array of integer
 **/
function getProductSessionVarName($cmdType) {
    return 'pdt';
    //$type = $cmdType==Command::TYPE_SUPPLIER?'Supplier':'Customer';
    //return SearchTools::getGridItemsSessionName($type . 'Catalog.php');
}

/**
 * Suppression des produits selectionnes si necessaire
 *
 * @param object $pdtCollection Collection de Products
 * @param integer $commandType
 * @return void
 **/
function deleteProducts($pdtCollection, $commandType) {
    if (isset($_REQUEST['suppr_x']) && isset($_REQUEST['gridItems'])) {
        // On trie a l'envers pour respecter les index suite a suppression!!!
        rsort($_REQUEST['gridItems']);
        $lastPdtId = 0;
        // Tri pour correspondre avec $_REQUEST
        $pdtCollection->sort('BaseReference');
        foreach($_REQUEST['gridItems'] as $pdtId) {
            // Il faut aussi nettoyer $_REQUEST pour qty et hdg
            $pdtIndex = $pdtCollection->getIndexById($pdtId);
            $pdtCollection->removeItemById($pdtId);
            // Ce test si plusieurs ids identiques (si commande cadencee)
            $pdtIndex = ($pdtId == $lastPdtId)?$pdtIndex + 1:$pdtIndex;
            unset($_REQUEST['qty'][$pdtIndex], $_REQUEST['hdg'][$pdtIndex]);
            $lastPdtId = $pdtId;
        }
        // Pour reaffecter les cles
        $_SESSION['qty'] = array_values($_REQUEST['qty']);
        $_SESSION['hdg'] = array_values($_REQUEST['hdg']);
        removeProductInSession($commandType);
    }
}

/**
 * Suppression des produits selectionnes si necessaire
 *
 * @param object $pdtCollection Collection de Products
 * @param integer $commandType
 * @return void
 **/
function addProduct($pdtVarName) {
    if (isset($_POST['AddCommandItem'])) {
        $hdgArray = array();
        $qtyArray = array();
        $done = false;
        foreach($_POST['hiddenIds'] as $key=>$pdtId) {
            $hdgArray[] = $_REQUEST['hdg'][$key];
            $qtyArray[] = $_REQUEST['qty'][$key];
            if ($pdtId == $_REQUEST['ProductToAdd'] && !$done) {
                $hdgArray[] = '';
                $qtyArray[] = 1;
                $done = true;
            }
        }
        $_REQUEST['hdg'] = $hdgArray;
        $_REQUEST['qty'] = $qtyArray;
        $_SESSION[$pdtVarName][] = $_REQUEST['ProductToAdd'];
        $_SESSION['hdg'] = $hdgArray;
        $_SESSION['qty'] = $qtyArray;
    }

}

/**
 * Supprime en session les Product supprimes dans le bon de Commande
 * @return void
 **/
function removeProductInSession($cmdType) {
    $_SESSION[getProductSessionVarName($cmdType)] =
            array_diff($_SESSION[getProductSessionVarName($cmdType)], $_REQUEST['gridItems']);
}

/**
 * Renvoie les options expéditeurs
 *
 * @param  Object Collection $pdtCol
 * @param  Object Customer $customer
 * @return array
 */
function getExpeditorList($pdtCol, $customer) {
    try {
        $chainCol = getCommandChainCollection($pdtCol, false, $customer);
        $cpa = new CommandPotentialActors($chainCol);
        return $cpa->getPotentialDepartureActors();
    } catch (Exception $exc) {
        throw $exc;
    }
}

/**
 * Renvoie les id des Actor destinataires possibles
 *
 * @param  Object Collection $pdtCol
 * @param  Object Supplier $supplier
 * @return array
 */
function getDestinatorList($pdtCol, $supplier) {
    try {
        $chainCol = getCommandChainCollection($pdtCol, $supplier, false);
        $cpa = new CommandPotentialActors($chainCol);
        return $cpa->getPotentialArrivalActors();
    } catch (Exception $exc) {
        throw $exc;
    }
}

/**
 *
 * @return object Collection of chain
 */
function getCommandChainCollection($pdtCol, $supplier=false, $customer=false) {
    // activation n chaine
    try {
        $chainProductMap = getChainProductMap($pdtCol, $supplier, $customer);
        $chainIDs = array_keys($chainProductMap);
    } catch (Exception $exc) {
        throw $exc;
    }
    return Object::loadCollection('Chain', array('Id'=>$chainIDs));
}

/**
 * Retourne un tableau des chaines activables pour les produits commandés.
 * Le tableau est de la forme:
 *
 * array(
 *     'ChainID1'=>array(pdtID1, pdtID2, ...),
 *     'ChainID2'=>array(pdtID3, pdtID4, ...)
 * )
 *
 * @access public
 * @param  Object Collection la collection de produits de la commande
 * @param  Object Customer $cust
 * @param  Object Supplier $sup
 * @return array
 * @throw  Exception
 */
function getChainProductMap($pdtCol, $sup, $cust) {
    $multipleChains = Preferences::get('CommandActivateMultipleChains');
    $chainProductMap  = array();
    $assignedProducts = array();
    $count = $pdtCol->getCount();
    for($i = 0; $i < $count; $i++) {
        $pdt = $pdtCol->getItem($i);
        $pdtID = $pdt->getId();
        $chnCol = $pdt->getChainCollection();
        $jcount = $chnCol->getCount();
        for($j = 0; $j < $jcount; $j++) {
            $chn = $chnCol->getItem($j);
            $chnID = $chn->getId();
            $ast  = $chn->getSiteTransition();
            if (!($ast instanceof ActorSiteTransition)) {
                continue;
            }
            $cond = true;
            $exp  = $ast->getDepartureActorId();
            $dest = $ast->getArrivalActorId();
            if ($sup && $cust) { // rech exacte après selection du dest/exp
                $cond = ($exp==$sup->getId() || $exp==$sup->getGenericActorID())
                     && ($dest==$cust->getId() || $dest==$cust->getGenericActorID());
            } else if ($sup) {
                $cond = $exp==$sup->getId() || $exp==$sup->getGenericActorID();
            } else if ($cust) {
                $cond = $dest==$cust->getId() || $dest==$cust->getGenericActorID();
            }
            if (!$cond) {
                continue;
            }
            if (!isset($chainProductMap[$chnID])) {
                $chainProductMap[$chnID] = array();
            }
            $chainProductMap[$chnID][] = $pdtID;
            $assignedProducts[] = $pdtID;
        }
    }
    // si aucune chaine n'a été trouvée
    if (count($chainProductMap) == 0) {
        throw new Exception(EXCEP_COMMAND_015, 15);
    }
    // si tous les produits n'ont pu être assignés à une chaine
    $pdtIds = $pdtCol->getItemIds();
    $diff = array_diff($pdtIds, $assignedProducts);
    if (count($diff) > 0) {
        throw new Exception(EXCEP_COMMAND_015, 15);
    }
    return $chainProductMap;
}

/**
 * Retourne le SupplierCustomer correspondant au 2 acteurs passés en paramètre.
 * S'il est introuvable, il est créé avec les valeurs par défaut.
 *
 * @param object Actor $expeditor
 * @param object Actor $destinator
 * @return object SupplierCustomer
 */
function findSupplierCustomer($expeditor, $destinator, $hasTVA=false) {
    if ($expeditor instanceof Actor) $expeditor  = $expeditor->getId();
    if ($destinator instanceof Actor) $destinator = $destinator->getId();
    $spcMapper = Mapper::singleton('SupplierCustomer');
    $spc = $spcMapper->load(
        array(
            'Supplier' => $expeditor,
            'Customer' => $destinator
        )
    );
    if (!($spc instanceof SupplierCustomer)) {
        // le couple n'a pas été trouvé on en crée un par défaut
        require_once('Objects/SupplierCustomer.php');
        $spc = new SupplierCustomer();
        // conditions de paiement par defaut
        $top = Object::load('TermsOfPayment', 1);
        if ($top instanceof TermsOfPayment) {
            $spc->setTermsOfPayment($top);
        }
        $spc->setHasTVA($hasTVA);
        $spc->setSupplier($expeditor);
        $spc->setCustomer($destinator);
        $spc->save();
    }
    return $spc;
}

/**
 * Crée la commande, les commanditems, active et planifie la chaine, envoie les
 * mails crée les mouvements etc... à l'aide du CommandManager
 *
 * @param int $commandType le type de commande
 * @param object Collection la collection des produits commandés
 * @param int $curId l'id de la devise pour la commande en cours
 * @param boolean $isEstimate mettre à true si la commande est un devis $fromEstimateId
 * @param int $fromEstimateId !=0 si la commande est un passage de devis a cmde
 * @return object Command la commande
 */
function handleCommand($commandType, $pdtCollection, $curID, $isEstimate=false, $fromEstimateId=0) {
    require_once('CommandManager.php');
    $auth = Auth::singleton();
    $returnURL = getErrorURL($commandType);

    // on crée la commande à l'aide du CommandManager
    $cadencedOrder = isset($_REQUEST['cadencedOrder']) && $_REQUEST['cadencedOrder'] == 1;
    $manager = new CommandManager(array(
        'CommandType'        => 'ProductCommand',
        'ProductCommandType' => $commandType,
        'UseTransaction'     => true,
        'IsEstimate'         => $isEstimate,
        'FromEstimateId'     => $fromEstimateId
    ));
    // création de la commande
    $commID = (isset($_REQUEST['cmdCommercial']) && $_REQUEST['cmdCommercial'] != '##')?
        $_REQUEST['cmdCommercial']:0;

    $commandData = array(
            'CommandNo'       => $_REQUEST['cmdNumber'],
            'Type'            => $commandType,
            'Expeditor'       => isset($_REQUEST['cmdExpeditor'])?$_REQUEST['cmdExpeditor']:'',
            'Destinator'      => isset($_REQUEST['cmdDestinator'])?$_REQUEST['cmdDestinator']:'',
            'ExpeditorSite'   => isset($_REQUEST['cmdExpeditorSite'])?$_REQUEST['cmdExpeditorSite']:'',
            'DestinatorSite'  => isset($_REQUEST['cmdDestinatorSite'])?$_REQUEST['cmdDestinatorSite']:'',
            'Customer'        => $auth->getActorId(),
            'Incoterm'        => $_REQUEST['cmdIncoterm'],
            'WishedStartDate' => $_REQUEST['StartDate'],
            'WishedEndDate'   => $_REQUEST['WishedDate']==1?$_REQUEST['EndDate']:'NULL',
            'Comment'         => stripslashes($_REQUEST['cmdComment']),
            'CommandDate'     => date('Y-m-d H:i:s', time()),
            'Port'            => isset($_REQUEST['Port']) ? $_REQUEST['Port'] : 0,
            'Packing'         => isset($_REQUEST['Emballage']) ? $_REQUEST['Emballage'] : 0,
            'Insurance'       => isset($_REQUEST['Assurance']) ? $_REQUEST['Assurance'] : 0,
            'Installment'     => $_REQUEST['Installment'],
            'Handing'         => isset($_REQUEST['GlobalHanding']) ? $_REQUEST['GlobalHanding'] : 0,
            'TotalPriceHT'    => $_REQUEST['TotalHT'],
            'TotalPriceTTC'   => $_REQUEST['TotalTTC'],
            'Currency'        => $curID,
            'Commercial'      => $commID,
            'CustomerRemExc'  => $_REQUEST['HiddenRemExcep'],
            'Cadenced'        => $cadencedOrder
        );
    if (isset($_REQUEST['cmdProjectManager'])) {
        $commandData['ProjectManager'] = $_REQUEST['cmdProjectManager'];
    }
    // checks et formatage des floats
    try {
        $commandData = CommandManager::checkParams($commandData);
    }
    catch (Exception $e) {
        Tools::handleException($e, $returnURL);
    }
    Tools::handleException($commandData, $returnURL);
    $result = $manager->createCommand($commandData);
    Tools::handleException($result, $returnURL);
    // Creation des commandItems
    $promoIDs = (isset($_REQUEST['PromotionId']))?$_REQUEST['PromotionId']:array();
    $productIDs = (isset($_REQUEST['ProductId']))?$_REQUEST['ProductId']:array();
    // Qtes d'UE seulement pour les cmdes client ET si preference activee
    $packagingUnitQties = (isset($_REQUEST['ueQty']))?$_REQUEST['ueQty']:array();
    $count = $pdtCollection->getCount();
    for ($i=0; $i<$count; $i++) {
        $pdt = $pdtCollection->getItemById($productIDs[$i]);
        if (isset($promoIDs[$i]) && $promoIDs[$i] > 0) {
            // si reellement une Promotion
            $promo = Object::load('Promotion', $promoIDs[$i]);
            $promoID = $promo->getId();
        } else {
            $promoID = 0;
        }
        if ($cadencedOrder) {
            if (!empty($_REQUEST['CommandItemDate'][$i])) {
                list($date, $time) = explode(' ', $_REQUEST['CommandItemDate'][$i]);
                list($d, $m, $y) = explode('/', $date);
                $y = (strlen($y) == 2) ? '20'.$y : $y;
                $wishedDate = sprintf('%4d-%02d-%02d %s:00', $y, $m, $d, $time);
            } else {
                $wishedDate = $_REQUEST['StartDate'];
            }
        } else {
            $wishedDate = null;
        }
        
        $commandItemData = array(
                'Product'   => $pdt->getId(),
                'Quantity'  => $_REQUEST['qty'][$i],
                'Handing'   => isset($_REQUEST['hdg'][$i]) ? $_REQUEST['hdg'][$i] : 0,
                'Promotion' => $promoID,
                'PriceHT'   => $_REQUEST['HiddenPrice'][$i],
                'WishedDate'=> $wishedDate
            );
        if (isset($packagingUnitQties[$i])) {
            $commandItemData['PackagingUnitQuantity'] = $packagingUnitQties[$i];
        }
        // checks et formatage des floats
        try {
            $commandItemData = CommandManager::checkParams(
            $commandItemData,
            array(
                'Product'  => array('req'=>false, 'type'=>'int'),
                'Quantity' => array('req'=>true, 'type'=>'float'),
                'PackagingUnitQuantity' => array('req'=>false, 'type'=>'int'),
                'Discount' => array('req'=>false, 'type'=>'float'),
                'TVA'      => array('req'=>false, 'type'=>'float'),
                'PriceHT'  => array('req'=>true, 'type'=>'float')),
            true);
        }
        catch (Exception $e) {
            Tools::handleException($e, $returnURL);
        }
        Tools::handleException($commandItemData, $returnURL);
        $result = $manager->addCommandItem($commandItemData);
        Tools::handleException($result, $returnURL);
    }
    // Activation du processus
    $result = $manager->activateProcess();
    Tools::handleException($result, $returnURL);
    // validation de la commande
    $result = $manager->validateCommand();
    Tools::handleException($result, $returnURL);
    return $manager->command;
}

/*
* Met a jour l'encours courant et selon la valeur de l'encours courant bloque les cdes
* et leurs ActivatedMovement associés
* Si encours depasse, besoin d'envoyer une alerte qui sera envoyee hors transaction
* @param $ProductCommand
* @param $TotalPriceTTC
* @param integer $PriceTTC : si renseigne, c'est ce montant qui met a jour
* $sc::UpdateIncur, et non pas $Invoice::TotalPriceTTC
* (dans le cas de suppression d'un Payment, par exple)
* @return object Alert or false
*/
function commandBlockage($ProductCommand, $Invoice, $PriceTTC=false) {
    require_once('Objects/Alert.const.php');

    $Expeditor = $ProductCommand->getExpeditor();   // on recupere le fournisseur
    $Destinator = $ProductCommand->getDestinator(); // on recupere le client
    $TotalPriceTTC = ($PriceTTC == false)?$Invoice->getTotalPriceTTC():$PriceTTC;

    $sc = $ProductCommand->getSupplierCustomer();
    $cur = $ProductCommand->getCurrency();
    $curStr = $cur instanceof Currency?$cur->getSymbol():'&euro;';

    $sc->setUpdateIncur($sc->getUpdateIncur() + $TotalPriceTTC);
    $sc->save();

    if (!(is_null($sc->getMaxIncur()))) {     // Si l'encours autorisé est defini
        if ($sc->getUpdateIncur() > $sc->getMaxIncur()) {
            //on bloque toutes les cdes activees et leurs ActivatedMovement
            blockDeblockage($Expeditor, $Destinator);

            $alert = Object::load('Alert', ALERT_CLIENT_INVOICE_INCUR_OVER);
            $alert->prepare(
                    array(
                        'Numcde' => $ProductCommand->getCommandNo(),
                        'CustomerName' => $Destinator->getName(),
                        'MaximumIncurse' => $sc->getMaxIncur(),
                        'NumInvoice' => $Invoice->getDocumentNo(),
                        'UpdateIncurseWithCommand' => $sc->getUpdateIncur(),
                        'Currency' => TextTools::entityDecode($curStr)
                     )
                );
            return $alert;
        }
    }
    return false;
}

/**
 *
 * @access public
 * @return void
 **/
function handleIncurUpdate($cmd, $curStr) {
    require_once('Objects/ActivatedMovement.php');
    require_once('AlertSender.php');
    $sc = $cmd->getSupplierCustomer();
    $actor = $cmd->getType()==Command::TYPE_SUPPLIER?$cmd->getExpeditor():$cmd->getDestinator();

    //gestion de l'acompte
    if ($cmd->getInstallment() != 0) {
        $sc->setUpdateIncur($sc->getUpdateIncur() - $cmd->getInstallment());
        $sc->save();
    }

    if (!is_null($sc->getMaxIncur())
            && (($sc->getUpdateIncur() + $cmd->getTotalPriceTTC()) > $sc->getMaxIncur())) {
        // Bloque la commande et tous ses ActivatedMovements associes
        $cmd->blockDeblock();
        // envoie de l'alerte
        if ($cmd->getType()==Command::TYPE_SUPPLIER) {
            CommandManager::$alertsToSend[] = array(
                'ALERT_SUPPLIER_COMMAND_INCUR_OVER',
                array(
                    $cmd, $curStr,
                    array($cmd->getExpeditorSiteId(), $cmd->getDestinatorSiteId())
                )
            );
        } else {
            CommandManager::$alertsToSend[] = array(
                'ALERT_CLIENT_COMMAND_INCUR_OVER',
                array(
                    $cmd, $curStr,
                    array($cmd->getExpeditorSiteId(), $cmd->getDestinatorSiteId())
                )
            );
        }
    }
}


/*
* Met a jour l'encours courant et selon la valeur de l'encours courant
* debloque les cdes et leurs ActivatedMovement associés
* @param $ProductCommand
* @param $TotalPriceTTC
* @return void
*/
function commandDeBlockage($cmd, $totalPriceTTC) {
    $sc = $cmd->getSupplierCustomer();
    if (!Tools::isEmptyObject($sc, 'SupplierCustomer')) {
        //$sc->setUpdateIncur($sc->getUpdateIncur() - $totalPriceTTC);
        //$sc->save();
        // Si l'encours autorisé est defini
        if (!(is_null($sc->getMaxIncur()))) {
            if ($sc->getUpdateIncur() <= $sc->getMaxIncur()) {
                // Si l'encours courant est inferieur a l'encours maximal autorisé,
                // on debloque les commandes bloquees et leurs
                // ActivatedMovements associes, ds la limite de
                // UpdateIncur - MaxIncur
                $exp = $cmd->getExpeditor();
                $des = $cmd->getDestinator();
                blockDeblockage($exp, $des, 0);
            }
        }
    }
}

/*
* Bloque ou debloque les commandes et leurs ActivatedMovement associés
* pour l'expediteur et le destinataire concernés.
* En cas de deblocage, debloque toutes les cdes bloquees, ds la limite
* de UpdateIncur - MaxIncur
* @param $Expeditor
* @param $Destinator
* @param $block integer: 1 pour bloquer (par defaut), et 0 pour debloquer
*/
function blockDeblockage($Expeditor, $Destinator, $block=1) {
    require_once('Objects/Command.const.php');

    $PdtCmdMapper = Mapper::singleton('ProductCommand');
    // On bloque des activees, ou on debloque des commandes bloquees
    $State = ($block == 1)?Command::ACTIVEE:Command::BLOCAGE_CDE;
    $PdtCmdCollection = $PdtCmdMapper->loadCollection(
            array('Expeditor' => $Expeditor,
                  'Destinator' => $Destinator,
                  'State' => $State),
            array('WishedStartDate' => SORT_ASC));
    $scMapper = Mapper::singleton('SupplierCustomer');
    $sc = $scMapper->load(array('Supplier' => $Expeditor,
                                            'Customer' => $Destinator));
    if (!Tools::isEmptyObject($PdtCmdCollection)) {
        $usableIncur = $sc->getMaxIncur() - $sc->getUpdateIncur();

        for($i = 0; $i < $PdtCmdCollection->getCount(); $i++) {
            $Command = $PdtCmdCollection->getItem($i);
            // Si deblocage, mais Encours insuffisant => on sort de la boucle
            if ($block == 0 && $Command->getTotalPriceTTC() > $usableIncur) {
                break;
            }
            $Command->blockDeblock($block);  // Bloque ou debloque la commande et ses ActivatedMovements associes
            $usableIncur = $usableIncur - $Command->getTotalPriceTTC();  // sert pour deblocage
            unset($Command);
        }
    }
}

/**
 * Pour debug
 * @param object $ActivatedChain
 * @return void
 **/
function debug($ActivatedChain) {
    $opeCol = $ActivatedChain->getActivatedChainOperationCollection();
    $c = $opeCol->getCount();
    for($i = 0; $i < $c; $i++) {
        $ope = $opeCol->getItem($i);
        $ackCol = $ope->getActivatedChainTaskCollection();
        $c2 = $ackCol->getCount();
        for($j = 0; $j < $c2; $j++) {
            $ack = $ackCol->getItem($j);
            $tsk = $ack->getTask();
        } // for
    } // for
    die();
}

?>
