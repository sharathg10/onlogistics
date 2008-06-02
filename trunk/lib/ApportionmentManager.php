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

require_once('config.inc.php');
require_once('Objects/Account.php');
require_once('FormatNumber.php');

/**
 * Classe de ventilation des factures et des frais/recettes externes.
 *
 * DOC {{{
 *
 * Description
 * ===========
 * Cette classe permet de ventiler les factures internes à onlogistics ainsi
 * que les charges et recettes externes (Flow et FlowItem) dans les comptes (Account)
 * comptables.
 *
 * La classe exporte 3 méthodes publiques:
 *   - process()
 *     Lance le processus de ventilation.
 *     Elle accepte 2 arguments:
 *       * fromDate: une date mysql style "2006-03-27 10:00:00"
 *       * toDate:   une date mysql style "2006-03-27 10:00:00"
 *     Si fromDate est null et que toDate est renseigné, la ventilation est
 *     effectuée pour tous les éléments qui sont antérieurs à toDate.
 *     Si au contraire fromDate est renseigné et toDate non, seuls les
 *     éléments ultérieurs à fromDate sont ventilés.
 *     Si aucune date n'est précisée, tous les éléments sans exception sont
 *     ventilés.
 *
 *   - toArray()
 *     Retourne les données de ventilation sous la forme de tableau.
 *     Pour la description de ce tableau voir plus bas.
 *     Elle accepte un argument boolean withDetail (par défaut à true) qui
 *     permet de spécifier si on veut le tableau de ventilation avec le détail
 *     des items ou juste le total des comptes.
 *
 *   - toCSV()
 *     Retourne les données de ventilation sous la forme d'une chaine au
 *     format csv (comma separated values).
 *     Elle accepte un argument boolean withDetail (par défaut à true) qui
 *     permet de spécifier si on veut le tableau de ventilation avec le détail
 *     des items ou juste le total des comptes. Elle accepte aussi 2 arguments
 *     chaines (delim et nl) qui permettent respectivement de spécifier le
 *     séparateur de colonnes (par défaut le point-virgule) et le caractère de
 *     retour à la ligne (par défaut \n).
 *
 *
 * Structure du tableau de ventilation:
 * ====================================
 * {
 *   '512'=>[
 *       {'Number'=>'1234', 'Detail'=>'Carburant' 'Amount'=>'120.12'},
 *       {'Number'=>'1234', 'Detail'=>'Entretien' 'Amount'=>'120.12'},
 *       {'Number'=>'1235', 'Amount'=>'55.12'},
 *   ],
 *   '512_total'=>175.24,
 *   '512_currency'=>'Euro',
 *   '540'=>[
 *       {'Number'=>'6502', 'Name'=>'Durand', 'Amount'=>'12.34'},
 *       {'Number'=>'6503', 'Amount'=>'90.45'},
 *   ],
 *   '540_total'=>102.79,
 *   '540_currency'=>'Dollar US',
 *   etc...
 * }
 * Ici, les cléfs 512, 512_total et 512_currency représentent respectivement:
 * le numéro du compte, le total ventilé pour le compte et la devise.
 *
 *
 * Exemples:
 * =========
 * 1. export csv de la ventilation de toutes les factures et charges/recettes
 * <code>
 * require_once('ApportionmentManager.php');
 * $manager = new ApportionmentManager();
 * $manager->process();
 * $csvdata = $manager->toCSV();
 * header('Content-Type: text/csv');
 * echo $csvdata;
 * </code>
 *
 * 2. Récupération d'un tableau de ventilation des éléments depuis 2 mois
 * <code>
 * require_once('ApportionmentManager.php');
 * $twomonth_before = mktime(0, 0, 0, date("m")-2, date("d"),  date("Y"));
 * $manager = new ApportionmentManager();
 * $manager->process(date('Y-m-d H:i:s', $twomonth_before));
 * $result  = $manager->toArray();
 * print_r($result);
 * </code>
 *
 * NOTES:
 * Régles pour définir si une ope est un debit ou un credit
 *
 * - Charges (flow de type charge + factures fournisseurs):
 *     - comptes TTC: credit
 *     - comptes HT et TVA: debit
 * 
 * - Recettes (flow de type recette + factures clients):
 *     - comptes TTC: debit
 *     - comptes HT et TVA: credit
 * }}}
 *
 * @access public
 */
class ApportionmentManager {

    /**
     * Tableau de ventilation (pour le format, voir la doc de la classe).
     *
     * @var array _apportionmentArray
     * @access private
     */
    var $_apportionmentArray = array();

    // ApportionmentManager::__construct() {{{

    /**
     * Constructeur.
     *
     * @access protected
     */
    function ApportionmentManager() {
    }

    // }}}
    // ApportionmentManager::process() {{{

    /**
     * Lance la ventilation pour la période passée en paramètre (si présente)
     * et pour les comptes dont le breakdownType est un de ceux passé en
     * paramètre (si present).
     *
     * @param string $fromDate une date mysql style "2006-03-27 10:00:00"
     * @param string $toDate   une date mysql style "2006-03-27 10:00:00"
     * @param array $forBreakdownTypes les breakdownType à ventillé
     * @access protected
     */
    function process($fromDate = false, $toDate = false, $accountingType=false, 
        $forBreakdownTypes=array()) {
        // la collection des comptes comptables
        $filter = array();
        if($accountingType) {
            $filter = SearchTools::NewFilterComponent('AccountingType', 'AccountingType().Id',
                'Equals', $accountingType, 1, 'Account');
                //array('AccountingType' => $accountingType);
        }
        $accMapper = Mapper::singleton('Account');
        $accCol = $accMapper->loadCollection($filter);
        $count  = $accCol->getCount();

        // la collection de factures
        $invCol = $this->_getItemCollection('Invoice', $fromDate, $toDate);
        $invColCount = $invCol->getCount();
    
        // la collection de charges/recettes
        $flowCol = $this->_getItemCollection('Flow', $fromDate, $toDate);
        $flowColCount = $flowCol->getCount();

        // pour chaque compte on ventile les éléments Invoice et Flow
        for ($i=0; $i<$count; $i++) {
            $account = $accCol->getItem($i);
            // si il y a des breakdownType passés en paramètres et que celui
            // du compte n'en fait pas parti, on passe au compte suivant
            if(!empty($forBreakdownTypes) &&
               !in_array($account->getBreakdownType(), $forBreakdownTypes)) {
                continue;
            }
            // construction du tableau AccountData pour le passer aux fonctions
            // _process(), plus efficace qu'un objet, car les getters ne sont
            // fait qu'une fois
            $accountData = array();
            $accountData['Number'] = $account->getNumber();
            $accountData['CurrencyId'] = $account->getCurrencyId();
            $accountData['TVAId'] = $account->getTVAId();
            $accountData['BreakdownType'] = $account->getBreakdownType();
            // paramètres nécéssaires à la ventillation des factures
            if($invColCount > 0) {
                $accountData['AccountingTypeIds'] =
                    $account->getAccountingTypeCollectionIds();
                // récup des FlowType speciaux
                $accountData['InvoicesTypes'] = 
                    $account->getInvoicesTypes();
                $accountData['BreakdownParts'] = array();
                // récup des FlowTypeItem speciaux
                foreach ($accountData['InvoicesTypes'] as $invoiceType) {
                    $accountData['BreakdownParts'][$invoiceType] =
                        $account->getBreakdownParts($invoiceType);
                }
            }
            // paramètres nécéssaires à la ventillation des charges/recettes
            if($flowColCount > 0) {
                $accountData['FlowTypeIds'] =
                    $account->getFlowTypeCollectionIds();
                $accountData['FlowTypeItemIds'] =
                    $account->getFlowTypeItemCollectionIds();
            }
            // ventilation des factures existantes
            for ($j=0; $j<$invColCount; $j++) {
                $inv = $invCol->getItem($j);
                $this->_processInvoice($inv, $accountData);
            }
            // ventilation des charges et recettes externes
            for ($j=0; $j<$flowColCount; $j++) {
                $flow = $flowCol->getItem($j);
                $this->_processFlow($flow, $accountData);
            }
        }
    }

    // }}}
    // ApportionmentManager::toArray() {{{

    /**
     * Retourne le tableau de ventilation. Si withDetail est à true, le détail
     * des items est retourné pour chaque compte sinon seuls les totaux de
     * chaque compte sont retournés.
     * Pour une description de ce tableau, se référer à la doc de la classe.
     *
     * @param boolean $withDetail
     * @return array
     */
    function toArray($withDetail=true) {
        // il faut trier le tableau qui contient le détail et les totaux en
        // fonction du paramètre withDetail
        $array = array();
        foreach ($this->_apportionmentArray as $key=>$val) {
            if ($withDetail) {
                if (is_array($val)) {
                    $array[$key] = $val;
                }
            } else {
                if (strpos($key, '_total')) {
                    // on ne doit retourner que le total de chaque compte
                    // on supprime le suffixe "_total"
                    $key = substr($key, 0, -6);
                    $array[$key]['total'] = $val;
                } elseif(is_array($val)) {
                    // on récupère dans la première facture du compte le type 
                    // (débit ou crédit)
                    $array[$key]['type'] = $val[0]['Type'];
                }
            }

        }
        return $array;
    }

    // }}}
    // ApportionmentManager::toCSV() {{{

    /**
     * Retourne une chaîne au format csv (comma separated values) représentant
     * le tableau de ventilation.
     *
     * @param boolean $withDetail
     * @param string $delim: optionnel un caractère délimiteur (défaut: ";")
     * @param string $nl: optionnel le caractère retour de ligne (défaut: "\n")
     * @return string
     */
    function toCSV($withDetail=true, $delim=';', $nl="\n") {
        $array = $this->toArray($withDetail);
        $csvData = '';
        if ($withDetail) {
            // en-tête
            $csvData = _('Account number') . $delim
                     . _('Date') . $delim
                     . _('Invoice number or expense/receipt') . $delim
                     . _('Expense/Receipt line') . $delim
                     . _('Name') .$delim
                     . _('Type') . $delim
                     . _('Amount') . $delim
                     . _('Payment date') . $delim
                     . _('Currency');
            // données
            foreach ($array as $key=>$vals) {
                foreach ($vals as $val) {
                    $detail = isset($val['Detail'])?$val['Detail']:'';
                    $name = isset($val['Name'])?$val['Name']:'';
                    $csvData .= $nl . $key . $delim . $val['Date'] . $delim
                        . $val['Number'] . $delim
                        . $detail . $delim
                        . $name . $delim
                        . $val['Type'] . $delim
                        . $val['Amount'] . $delim
                        . $val['PaymentDate'] . $delim
                        . $this->_apportionmentArray[$key . '_currency'];
                }
            }
        } else {
            // en-tête
            $csvData = _('Account number') . $delim
                . _('Type') . $delim
                . _('Total amount') . $delim
                . _('Currency');
            // données
            foreach ($array as $key=>$val) {
                $csvData .= $nl . $key . $delim . $val['type'] . $delim
                    . $val['total'] . $delim
                    . $this->_apportionmentArray[$key . '_currency'];
            }
        }
        return $csvData;
    }
    
    // }}}
    // ApportionmentManager::_processInvoice() {{{

    // méthodes privées
    /**
     * Ventile une charge/recette dans le compte en cours si celle ci est
     * compatible avec le compte.
     *
     * @param object Invoice $inv la facture à ventiler
     * @param array $accountData: un tableau des propriétés du compte, on passe
     * un tableau pour améliorer les perfs
     * @return void
     */
    function _processInvoice($inv, $accountData) {
        // vérifie d'abord si la facture est compatible avec le compte
        // elle est compatible si sa devise est la même que celle définie pour
        // le compte
        $cur = $inv->getCurrency();
        if (!$cur || $cur->getId() != $accountData['CurrencyId']) {
            return;
        }
        // on vérifie l'AccountingType
        $accTypeActorID = $inv->getAccountingTypeActorId();
        $actorMapper = Mapper::singleton('Actor');
        $accTypeActor = $actorMapper->load(array('Id'=>$accTypeActorID));
        $accTypeID = 0;
        if($accTypeActor instanceof Actor) {
            $accTypeID = $accTypeActor->getAccountingTypeId();
        }
        if (!in_array($accTypeID, $accountData['AccountingTypeIds'])) {
            // l'acteur ne correspond pas
            return;
        }
        
        // on vérifie que la facture correspond à un des FlowType special
        if(!in_array($inv->getCommandType(), $accountData['InvoicesTypes'])) {
            return;
        }
        
        require_once('Objects/TVA.inc.php');
        $TVARates = array();
        $TVAIds = array();
        $TVACategories = array(
            'INSURANCE' => TVA::TYPE_INSURANCE, 
            'DELIVERY EXPENSES' => TVA::TYPE_DELIVERY_EXPENSES, 
            'PACKING' => TVA::TYPE_PACKING);
        $tvaMapper = Mapper::singleton('TVA');
        foreach ($TVACategories as $category=>$value) {
            $tva = $tvaMapper->load(array('Type' => $value));
            if(!($tva instanceof TVA)) {
                $TVARates[$category] = 0;
                $TVAIds[$category] = 0;
                continue;
            }
            $TVARates[$category] = $tva->getRate();
            $TVAIds[$category] = $tva->getId();
        }
        // pour chaque FlowTypeItem de ce FlowType

        // si le FlowType à un FlowTypeItem ligne de facture
        // la facture est compatible on va maintenant ventiler ses InvoiceItems
        $amount = 0;
        if(in_array(FlowTypeItem::BREAKDOWN_INVOICE_ITEM, $accountData['BreakdownParts'][$inv->getCommandType()])) {
            // ventillation de la remise
            if ($accountData['BreakdownType'] == Account::BREAKDOWN_DISCOUNT) {
                $handing = $inv->getHandingDetail();
                $amount += $handing['handing'];
            } else {
                $invItemCol = $inv->getInvoiceItemCollection();
                $count = $invItemCol->getCount();
                for ($i=0; $i<$count; $i++) {
                    $invItem = $invItemCol->getItem($i);
                    if ($accountData['BreakdownType'] == Account::BREAKDOWN_TVA) {
                        if ($accountData['TVAId'] != $invItem->getTVAId()) {
                            // les taux de TVA ne correspondent pas
                            continue;
                        }
                        // ventilation de la tva
                        $amount += troncature($invItem->getTotalTVA());
                    } else if ($accountData['BreakdownType'] == Account::BREAKDOWN_TTC) {
                        // sinon ventilation du TTC
                        $amount += $invItem->getTotalPriceHT() + troncature($invItem->getTotalTVA());
                    } else {
                        // sinon ventilation du HT
                        $amount += $invItem->getTotalPriceHT();
                    }
                }
            }
        }
        // si le flowtype à un des flowtypeitem frais annexe
        // on ventille les frais annexes de la facture
        $annexesCharges = array(
            FlowTypeItem::BREAKDOWN_INSURANCE => array('TVACategory' => 'INSURANCE',
                                         'InvoiceProperty' => 'Insurance'),
            FlowTypeItem::BREAKDOWN_PACKING => array('TVACategory' => 'PACKING',
                                       'InvoiceProperty'=>'Packing'),
            FlowTypeItem::BREAKDOWN_PORT => array('TVACategory' => 'DELIVERY EXPENSES',
                                    'InvoiceProperty' => 'Port'));
        $detailArray = FlowTypeItem::getBreakdownPartConstArray();
        $detail = '';
        foreach ($annexesCharges as $key=>$value) {
            $price=0;
            if(in_array($key, $accountData['BreakdownParts'][$inv->getCommandType()])) {
                $getter = 'get' . $value['InvoiceProperty'];
                if ($accountData['BreakdownType'] == Account::BREAKDOWN_TVA) {
                    // ventilation de la tva
                    // vérifier que le taux correspond avec $accountData['TVAId'] ?
                    if ($accountData['TVAId'] != $TVAIds[$value['TVACategory']]) {
                        // les taux de TVA ne correspondent pas
                        continue;
                    }
                    $price = troncature($inv->$getter() * 
                        $TVARates[$value['TVACategory']] / 100);                    
                    if($price != 0) {
                        $detail .= ' TVA ' . strtolower($value['TVACategory']);
                    }
                } elseif ($accountData['BreakdownType'] == Account::BREAKDOWN_TTC) {
                    // sinon ventilation du TTC
                    $price = $inv->$getter() + 
                        troncature($inv->$getter() * 
                        $TVARates[$value['TVACategory']] / 100);
                    if($price != 0) {
                        $detail .= ' TTC ' . strtolower($value['TVACategory']);
                    }
                } elseif ($accountData['BreakdownType'] == Account::BREAKDOWN_HT) {
                    // sinon ventilation du HT
                    $price = $inv->$getter();
                    if($price != 0) {
                        $detail .= ' HT ' . strtolower($value['TVACategory']);
                    }
                }
                $amount += $price;
                
            }
        }
        if ($amount != 0) { 
            $number = $accountData['Number'];
            if (!isset($this->_apportionmentArray[$number])) {
                $this->_apportionmentArray[$number] = array();
            }
            if (!isset($this->_apportionmentArray[$number . '_total'])) {
                $this->_apportionmentArray[$number . '_total'] = 0;
            }
            require_once('Objects/Command.php');
            $cmd = $inv->getCommand();
            $sp = $cmd->getSupplierCustomer();
            $name = '';
            if ($sp instanceof SupplierCustomer) {
                switch ($cmd->getType()) {
                    case Command::TYPE_SUPPLIER:
                        $actor = $sp->getSupplier();
                        if ($actor instanceof Actor) {
                            $name = $actor->getName();
                        }
                        if ($accountData['BreakdownType'] == Account::BREAKDOWN_TTC) {
                            $type = _('credit');
                        } else {
                            $type = _('debit');
                        }
                        break;
                    case Command::TYPE_CUSTOMER:
                    case Command::TYPE_TRANSPORT:
                    case Command::TYPE_PRESTATION:
                    case Command::TYPE_COURSE:
                        $actor = $sp->getCustomer();
                        if ($actor instanceof Actor) {
                            $name = $actor->getName();
                        }
                        if ($accountData['BreakdownType'] == Account::BREAKDOWN_TTC) {
                            $type = _('debit');
                        } else {
                            $type = _('credit');
                        }
                        break;
                }
            }
            $dataArray = array(
                'Number' => $inv->getDocumentNo(),
                'Date'   => I18N::formatDate($inv->getEditionDate(), I18N::DATE_LONG),
                'PaymentDate'   => I18N::formatDate($inv->getPaymentDate(), I18N::DATE_LONG),
                'Name'   => $name,
                'Detail' => $detail,
                'Amount' => $amount,
                'Type'   =>$type
            );
            // on ajoute les données de ventilation au tableau
            $this->_apportionmentArray[$number][] = $dataArray;
            // on ajoute le total ventilé au total du compte
            $this->_apportionmentArray[$number . '_total'] += $amount;
            if (!isset($this->_apportionmentArray[$number . '_currency'])) {
                $this->_apportionmentArray[$number . '_currency'] = $cur->getShortName();
            }
        }
    }

    // }}}
    // ApportionmentManager::_processFlow() {{{

    /**
     * Ventile une charge/recette dans le compte en cours si celle ci est
     * compatible avec le compte.
     *
     * @param object Flow $flow la charge/recette à ventiler
     * @param array $accountData: un tableau des propriétés du compte, on passe
     * un tableau pour améliorer les perfs
     * @return void
     */
    function _processFlow($flow, $accountData) {
        // si la devise ne correspond pas on laisse tomber...
        if ($accountData['CurrencyId'] != $flow->getCurrencyId()) {
            return;
        }
        // de même si le flowtype du flow n'est pas un de ceux définis dans le
        // compte on ne fait rien
        if (!in_array($flow->getFlowTypeId(), $accountData['FlowTypeIds'])) {
            return;
        }
        // ok, le Flow est "ventilable"
        $number = $accountData['Number'];
        $cur    = $flow->getCurrency();
        if (!isset($this->_apportionmentArray[$number])) {
            $this->_apportionmentArray[$number] = array();
        }
        if (!isset($this->_apportionmentArray[$number . '_total'])) {
            $this->_apportionmentArray[$number . '_total'] = 0;
        }
        if ($accountData['BreakdownType'] == Account::BREAKDOWN_TTC) {
            // ventilation du TTC
            $amount = $flow->getTotalTTC();
            $type = ($flow->getFlowType()->getType() == FlowType::CHARGE) ? 
                _('credit') : _('debit');

            $dataArray = array(
                'Number' => $flow->getNumber(),
                'Amount' => $amount,
                'Name'   => $flow->getName(),
                'Type'   => $type,
                'PaymentDate' => I18N::formatDate($flow->getPaymentDate(), I18N::DATE_LONG),
                'Date'   => I18N::formatDate($flow->getEditionDate(), I18N::DATE_LONG));
            // on ajoute les données de ventilation au tableau passé
            $this->_apportionmentArray[$number][] = $dataArray;
            // on ajoute le total ventilé au total du compte
            $this->_apportionmentArray[$number . '_total'] += $amount;
        } elseif ($accountData['BreakdownType'] == Account::BREAKDOWN_DISCOUNT) {
            // ventilation de la remise
            $amount = $flow->getDiscountAmount();
            if($amount > 0) {
                $type = ($flow->getFlowType()->getType() == FlowType::CHARGE) ?
                    _('debit') : _('credit');
                $dataArray = array(
                    'Number' => $flow->getNumber(),
                    'Name'   => $flow->getName(),
                    'Amount' => $amount,
                    'Type'   => $type,
                    'PaymentDate' => I18N::formatDate($flow->getPaymentDate(), I18N::DATE_LONG),
                    'Date'   => I18N::formatDate($flow->getEditionDate(), I18N::DATE_LONG));
            }
            // on ajoute les données de ventilation au tableau passé
            $this->_apportionmentArray[$number][] = $dataArray;
            // on ajoute le total ventilé au total du compte
            $this->_apportionmentArray[$number . '_total'] += $amount;
        }else {
            // sinon ventilation de la TVA ou du HT portés par
            // les FlowItems
            $flowItemCol = $flow->getFlowItemCollection();
            $flowItemColCount = $flowItemCol->getCount();
            for ($k=0 ; $k<$flowItemColCount ; $k++) {
                $flowItem = $flowItemCol->getItem($k);
                $this->_processFlowItem($flowItem, $accountData, $flow);
            }
        }
        if (!isset($this->_apportionmentArray[$number . '_currency'])) {
            $this->_apportionmentArray[$number . '_currency'] = $cur->getShortName();
        }
    }

    // }}}
    // ApportionmentManager::_processFlowItem() {{{

    /**
     * Ventile une ligne de charge/recette dans le compte en cours si celle ci est
     * compatible avec le compte.
     *
     * @param object FlowItem $flowItem la ligne de charge/recette à ventiler
     * @param array $accountData: un tableau des propriétés du compte, on passe
     * un tableau pour améliorer les perfs
     * @param object Flow $flow l'objet Flow actuel (pour ne pas le recharger)
     * @return void
     */
    function _processFlowItem($flowItem, $accountData, $flow) {
        // Si le flowTypeItem du flowItem n'est pas un de ceux définis dans le
        // compte on ne fait rien
        if (!in_array($flowItem->getTypeId(), $accountData['FlowTypeItemIds'])) {
            return;
        }
        $flowTypeItem = $flowItem->getType();
        // ok, le FlowItem est "ventilable"
        $number = $accountData['Number'];
        $cur    = $flow->getCurrency();
        if (!isset($this->_apportionmentArray[$number])) {
            $this->_apportionmentArray[$number] = array();
        }
        if (!isset($this->_apportionmentArray[$number . '_total'])) {
            $this->_apportionmentArray[$number . '_total'] = 0;
        }
        $flowType = $flow->getFlowType();
        $type = ($flowType->getType() == FlowType::CHARGE) ? 
            _('debit') : _('credit');
        $dataArray = array(
            'Number' => $flow->getNumber(),
            'Name'   => $flow->getName(),
            'Date'   => I18N::formatDate($flow->getEditionDate(), I18N::DATE_LONG),
            'PaymentDate'   => I18N::formatDate($flow->getPaymentDate(), I18N::DATE_LONG),
            'Detail' => $flowTypeItem->getName(),
            'Type'   => $type
        );
        if ($accountData['BreakdownType'] == Account::BREAKDOWN_TVA) {
            if ($accountData['TVAId'] != $flowItem->getTVAId()) {
                // les taux de TVA ne correspondent pas
                return;
            }
            // ventilation de la TVA
            $tva = $flowItem->getTVA();
            $amount = troncature($flowItem->getTotalHT() * $tva->getRate() / 100);
        } else {
            // sinon ventilation du HT
            $amount = $flowItem->getTotalHT();
        }
        $dataArray['Amount'] = $amount;
        // on ajoute les données de ventilation au tableau
        $this->_apportionmentArray[$number][] = $dataArray;
        // on ajoute le total ventilé au total du compte
        $this->_apportionmentArray[$number . '_total'] += $amount;
    }

    // }}}
    // ApportionmentManager::_getItemCollection() {{{

    /**
     * Retourne une collection de factures ou de charges/recettes (selon le
     * paramètre $cls) filtrée par date (si les paramètres de date sont
     * renseignés.)
     *
     * @access private
     * @param string $cls le nom de la classe
     * @param string $fromDate une date mysql style "2006-03-27 10:00:00"
     * @param string $toDate   une date mysql style "2006-03-27 10:00:00"
     * @return object Collection
     */
    function _getItemCollection($cls, $fromDate=false, $toDate=false) {
        // construction du filtre sur les dates
        if ($fromDate || $toDate) {
            $filter = new FilterComponent;
            $filter->operator = FilterComponent::OPERATOR_AND;
            if ($fromDate) {
                $filter->setItem(
                    new FilterRule(
                        'EditionDate',
                        FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                        $fromDate
                    )
                );
            }
            if ($toDate) {
                $filter->setItem(
                    new FilterRule(
                        'EditionDate',
                        FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                        $toDate
                    )
                );
            }
        } else {
            // pas de filtre
            $filter = array();
        }
        
        $mapper = Mapper::singleton($cls);
        return $mapper->loadCollection($filter);
    }

    // }}}
}

?>
