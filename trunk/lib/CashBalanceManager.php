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

class CashBalanceManager {
    // CashBalanceManager::properties {{{
    
    /**
     * currency 
     * 
     * @var int
     * @access public
     */
    public $currency = false;
    
    /**
     * toCurrency devise vers laquelle convertir
     * 
     * @var object Currency
     * @access public
     */
    public $toCurrency = false;
    
    /**
     * convertertRate 
     * 
     * @var float
     * @access public
     */
    public $convertertRate = 1;
    
    /**
     * beginDate 
     * 
     * @var array
     * @access public
     */
    public $beginDate = false;
    
    /**
     * endDate 
     * 
     * @var array
     * @access public
     */
    public $endDate = false;

    /**
     * accountingType 
     * 
     * @var mixed
     * @access public
     */
    public $accountingType = false;
    
    /**
     * cashBalance 
     * 
     * @var array
     * @access public
     */
    public $cashBalance = array();

    /**
     * totals 
     * 
     * tableau qui contient le total réel et prévisionnel 
     *
     * @var array
     * @access public
     */
    public $totals = array();

    static $dataID = 0;
    // }}}
    // CashBalance::__construct() {{{
    
    /**
     * __construct
     *
     * les paramètre sont:
     *
     * - currency : id de la devise
     * - beginDate : date de début array('d'=>1, 'm'=>1, 'Y'=>2007) 
     * - endDate : date de début array('d'=>31, 'm'=>7, 'Y'=>2007) 
     * 
     * @param mixed $params 
     * @access public
     * @return void
     */
    public function __construct($params) {
        $this->currency = $params['currency'];
        if (isset($params['toCurrency']) && $params['toCurrency'] != '##' 
            && $params['toCurrency'] != $params['currency']) {
            $this->toCurrency = Object::load('Currency', $params['toCurrency']);
        }
        $this->beginDate = $params['beginDate'];
        $this->endDate = $params['endDate'];
        $this->accountingType = $params['accountingType'];
    }

    // }}}
    // CashBalance::process() {{{

    /**
     * process 
     *
     * Récupère la balance, construit un tableau de la forme
     *
     * <code>
     * array(
     *     0 => 'label',
     *     1 => total,
     *     2 => prévisionnel,
     *     3 => childArray()
     * );
     * </code>
     *
     * 
     * @param mixed $nocache 
     * @access public
     * @return void
     */
    public function process($nocache=false) {
        if(isset($_SESSION['CashBalance']) && !$nocache) {
            $this->cashBalance = unserialize($_SESSION['CashBalance']);
            Session::prolong('CashBalance');
            return true;
        }

        $monthsArray = I18N::getMonthesArray();
        $continue = true;
        $beginDate = $this->beginDate;
        $endDate = $this->endDate;
        while($continue) {
            if($beginDate['m'] == $this->endDate['m'] && $beginDate['Y']==$this->endDate['Y']) {
                $continue = false;
                $startTime = mktime(
                    0, 0, 0, $beginDate['m'], $beginDate['d'], $beginDate['Y']);
                $endTime = mktime(
                    23, 59, 59, $this->endDate['m'], $this->endDate['d'], $this->endDate['Y']);
            } else {
                $startTime = mktime(
                    0, 0, 0, $beginDate['m'], $beginDate['d'], $beginDate['Y']);
                $endTime = mktime(
                    23, 59, 59, $beginDate['m'], date('t', $startTime), $beginDate['Y']);
            }
            $date1 = date('Y-m-d H:i:s', $startTime);
            $date2 = date('Y-m-d H:i:s', $endTime);

            $header = $monthsArray[$beginDate['m']] . ' ' . $beginDate['Y']; 
            $this->totals[$header] = array('total'=>0, 'forecast'=>0);
            
            $flowCategories = Object::loadCollection('FlowCategory', array('Parent' => 0), 
                array('DisplayOrder'=>SORT_ASC));
            foreach($flowCategories as $flowCat) {
                list($this->cashBalance[$header][], $this->totals[$header]) = $flowCat->getCashBalance(
                    array('beginDate'=>$date1, 'endDate'=>$date2, 'currency'=>$this->currency,
                    'accountingType' => $this->accountingType), $this->totals[$header]);
            }

            // les flowTypes sans category
            $filter = array('FlowCategory' => 0);
            if($this->accountingType) {
                $filter['AccountingType'] = $this->accountingType;
            }
            $flowTypes = Object::loadCollection('FlowType', $filter, 
                array('Name'=>SORT_ASC));
            foreach($flowTypes as $flowType) {
                list($this->cashBalance[$header][], $this->totals[$header]) = $flowType->getCashBalance(
                    array('beginDate'=>$date1, 'endDate'=>$date2, 'currency'=>$this->currency,
                    'accountingType' => $this->accountingType), $this->totals[$header]);
            }

            $beginDate['d'] = 1;
            $beginDate['m'] = $beginDate['m'] + 1;
            if($beginDate['m']==13) {
                $beginDate['m'] = 1;
                $beginDate['Y'] = $beginDate['Y'] + 1;
            }
        }
        // Traitement visiblement separe pour les totaux du bas qui sont calcules par ailleurs...
        // Dangereux, puisque peuvent ne pas correspondre au listing...
        // On applique le taux de conversion (vaut 1 si pas de conversion demandee)
        foreach (array_keys($this->totals) as $key) {
            $this->totals[$key]['total'] = round($this->totals[$key]['total'] * $this->convertertRate, 2);
            $this->totals[$key]['forecast'] = round($this->totals[$key]['forecast'] * $this->convertertRate, 2);
        }
        
        $this->manageDistributionKey();
        Session::register('CashBalance', serialize($this->cashBalance), 2);
        return true;
    }

    // }}}
    // CashBalance::formatCashBalance() {{{

    /**
     * formatCashBalance 
     *
     * Met en forme une partie des données pour export en csv ou html
     * 
     * @param array $data données
     * @param int $month mois
     * @param array $dest destination
     * @param int $level niveau (pour indentation des labels)
     * @param bool $forHtml true pour ajouter du formattage pour le html
     * @static
     * @access public
     * @return void
     */
    public function formatCashBalance($data, $header, &$dest, $level=0, $forHtml=false) {
        $key = $data[4];

        $padding = '';
        if(!$forHtml) {
            $padding = '&nbsp;';
            for($i=0 ; $i<$level ; $i++) {
                $padding .= '&nbsp;&nbsp;';
            }
        }

        $dest[$key]['name'] = $padding . $data[0];
        //echo '<br>' . $data[1] . ' => ' . round($data[1] * $this->convertertRate, 2);
        // On applique le taux de conversion (1 si pas de conversion de devise)
        $dest[$key][$header]['total'] = round($data[1] * $this->convertertRate, 2);
        $dest[$key][$header]['forecast'] = round($data[2] * $this->convertertRate, 2);
        if(isset($data[3])) {
            if(!$forHtml) {
                $dest[$key]['name'] = '<b><i>' . $dest[$key]['name'] . '</i></b>';
            }
            foreach($data[3] as $child) {
                CashBalanceManager::formatCashBalance($child, $header, $dest, $level+1, $forHtml);
            }
        } elseif(!$forHtml) {
            $dest[$key]['name'] = $padding . '- ' . $data[0];
        }
        $dest[$key][_('Total')]['total'] = 0;
        $dest[$key][_('Total')]['forecast'] = 0;
    }

    // }}}
    // CashBalance::toHTML() {{{

    /**
     * toHTML
     *
     * retourne la balance mise en forme en html.
     * 
     * @access public
     * @return string
     */
    public function toHTML() {
        $array = $this->extractData();
        $smarty = new Template();
        $smarty->assign('data', $array['data']);
        $smarty->assign('totals', $this->totals);
        $smarty->assign('headers', $array['headers']);
        $content = $smarty->fetch('CashBalance/CashBalance.html');
        return $content;
    }

    // }}}
    // CashBalance::extractData() {{{

    /**
     * extractData
     *
     * retourne un tableau dont les clé sont 'headers' et 'data' avec les 
     * en-tête et données de la balance. 
     * 
     * @param bool $tocvs true pour ne pas générer de mise en page (pour 
     * utiliser les données dans un export csv)
     * @access public
     * @return array()
     */
    public function extractData($tocvs=false) {
        $headers = array();
        $data = array();
        foreach($this->cashBalance as $head=>$values) {
            $headers[] = $head;
            foreach($values as $detail) {
                $this->formatCashBalance($detail, $head, $data, 0, $tocvs);
            }
        }
        $headers[] = _('Total');
        $data = $this->cleanData($headers, $data);
        return array('headers'=>$headers, 'data'=>$data);
    }

    // }}}
    // CashBalance::toCSV() {{{
    
    /**
     * toCSV 
     *
     * exporte les données au format csv.
     * 
     * @param string $delim separateur de colonnes
     * @param string $nl separateur de lignes
     * @access public
     * @return string
     */
    public function toCSV($delim=';', $nl="\n") {
        $array = $this->extractData(true);
        $csvData = '';
        //en-tête
        $csvData = _('label');
        foreach($array['headers'] as $k=>$v) {
            $csvData .= $delim . $v;
        }

        //données
        foreach($array['data'] as $values) {
            $csvData .= $nl . $values['name'];
            foreach($array['headers'] as $k=>$v) {
                $csvData .= $delim . $values[$v]['total'];
                if ($values[$v]['forecast'] > 0) {
                    $csvData .= '(' . $values[$v]['forecast'] . ')';
                }
            }
        }
        // Totaux
        $totals = $forecast = array();
        foreach($this->totals as $values) {
            $totals[] = $values['total'];
            $forecast[] = (int)($values['forecast']);            
        }
        $csvData .= $nl . $nl . _('Result before taxes');
        $csvData .= $delim . implode($delim, $forecast);
        $csvData .= $nl . _('Result before cumuled taxes');
        for ($i=0; $i<count($forecast); $i++) {
            $csvData .= $delim . array_sum(array_slice($forecast, 0, $i + 1));
        }
        
        $csvData .= $nl . _('Month balance');
        $csvData .= $delim . implode($delim, $totals);
        $csvData .= $nl . _('Real treasury');
        for ($i=0; $i<count($totals); $i++) {
            $csvData .= $delim . array_sum(array_slice($totals, 0, $i + 1));
        }
        return $csvData;
    }
    
    // }}}
    // CashBalanceManager::cleanData() {{{
    
    /**
     * cleanData 
     * 
     * Retourne un tableau sans les lignes qui ont le total ET le 
     * prévisionnel à 0
     *
     * @param array $headers 
     * @param array $data 
     * @access public
     * @return void
     */
    public function cleanData($headers, $data) {
        $cleanData = array();
        $cleanHeader = array();
        foreach($data as $k=>$v) {
            $total = $forecast = 0;
            foreach($headers as $i=>$month) {
                if($v[$month]['total'] != 0 || $v[$month]['forecast'] != 0) {
                    $total += $v[$month]['total'];
                    $forecast += $v[$month]['forecast'];
                }
            }
            if($total !=0 || $forecast != 0) {
                $cleanData[$k] = $v;
                $cleanData[$k][_('Total')]['total'] = $total;
                $cleanData[$k][_('Total')]['forecast'] = $forecast;
            }
        }
        //return array('headers'=>$cleanHeader, 'data'=>$cleanData);
        return $cleanData;
    }

    // }}}
    // CashBalanceManager::manageDistributionKey() {{{

    /**
     * manageDistributionKey 
     * 
     * Ajoute des données tenant compte des clés de répartition et des totaux du 
     * modèle principale. 
     * Pour chaque modèle "non principal" (MainModel=0) on 
     * ajoute une ligne avec le total du modèle principale * le pourcentage 
     * indiqué dans la clé de répartition du modèle secondaire.
     *
     * @access public
     * @return void
     */
    public function manageDistributionKey() {
        $mapper = Mapper::singleton('AccountingType');
        $filter = array('MainModel'=>false);
        if($this->accountingType) {
            $filter['Id']=$this->accountingType;
        }
        $accTypeCol = $mapper->loadCollection($filter);
        $mainAccountingType = $mapper->load(array('MainModel'=>true));
        if(!($mainAccountingType instanceof AccountingType)) {
            return false;
        }
        $result = array();
        foreach($accTypeCol as $accountingType) {
            $distributionKey = $accountingType->getDistributionKey();
            if($distributionKey <= 0 || $distributionKey>=100) {
                return false;
            }
            $cm = new CashBalanceManager(array(
                'beginDate' => $this->beginDate,
                'endDate' => $this->endDate,
                'currency' => $this->currency,
                'accountingType' => $mainAccountingType->getId()));

            $cm->process(true);
            foreach($cm->totals as $key=>$data) {
                $this->cashBalance[$key][] = array(
                    0 => $mainAccountingType->getType() . ' (' . $distributionKey . '%)',
                    1 => $data['total'] * $distributionKey/100,
                    2 => $data['forecast'] * $distributionKey/100,
                    4 => 'DistKey_' . $accountingType->getId());
            }
        }
        return true;
    }

    // }}}
    // CashBalanceManager::checkConverter() {{{

    /**
     * Verifie qu'un et un seul CurrencyConverter est defini, en cas de 
     * conversion de devise necessaire Si plusieurs, ou si un seul dont le creneau 
     * ne correspond pas, affichage d'un message
     * 
     * @access public
     * @return void  ///integer code erreur (0 pas de CConverter, -1: plus d'1 trouve...)
     */
    public function checkConverter() {
        $return = false;
        // Attention, $this->beginDate et endDate sont des array... :((
        $cmBeginDate = $this->beginDate;
        $cmBeginDate =  $cmBeginDate['Y'] .'-' . sprintf("%02.0f", $cmBeginDate['m']) 
                .'-' . sprintf("%02.0f", $cmBeginDate['d']);
        $cmEndDate = $this->endDate;
        $cmEndDate =  $cmEndDate['Y'] .'-' . sprintf("%02.0f", $cmEndDate['m']) 
                .'-' . sprintf("%02.0f", $cmEndDate['d']);
        $mapper = Mapper::singleton('CurrencyConverter');
        $filter = array(
            SearchTools::NewFilterComponent(
                'BeginDate', '', 'LowerThanOrEquals', $cmEndDate, 1),
            SearchTools::NewFilterComponent(
                'EndDate', '', 'GreaterThanOrEquals', $cmBeginDate, 1)
        );
        
        $cur1Filter = array(
            SearchTools::NewFilterComponent(
                'FromCurrency', '', 'Equals', $this->currency, 1),
            SearchTools::NewFilterComponent(
                'ToCurrency', '', 'Equals', $this->toCurrency->getId(), 1)
        );
        $cur1Filter = SearchTools::filterAssembler($cur1Filter);
                
        $cur2Filter = array(
            SearchTools::NewFilterComponent(
                'ToCurrency', '', 'Equals', $this->currency, 1),
            SearchTools::NewFilterComponent(
                'FromCurrency', '', 'Equals', $this->toCurrency->getId(), 1)
        );
        $cur2Filter = SearchTools::filterAssembler($cur2Filter);
        
        $curFilter = SearchTools::filterAssembler(
                array($cur1Filter, $cur2Filter), FilterComponent::OPERATOR_OR);
        
        $filter[] = $curFilter;
        $filter = SearchTools::filterAssembler($filter);
        $converterColl = $mapper->loadCollection($filter, array('BeginDate' => SORT_ASC));
        $url = 'CashBalance.php';
        $msg = '';
        if ($converterColl->getCount() == 0) {
            Template::errorDialog(
                _('No exchange rate exists for selected currencies and period. Please define this exchange rate, or change currency.'), 
                $url);
            exit;
        } elseif ($converterColl->getCount() == 1) {
            $converter = $converterColl->getItem(0);
        } else { // Plus d'un trouve => code erreur
            // On affichera du coup les resultats calcules sur le creneau delimite
            // par l'intersection du 1er CurrencyConverter trouve avec le creneau 
            // selectionne.
            $converter = $converterColl->getItem(0);
            $msg = _('More than one exchange rate exist for selected currencies and period.');
        }
        // TODO: si besoin, reduire le creneau + affichage erreur!!!!
        
        if ($converter->getFromCurrencyId() == $this->currency) {
            $this->convertertRate = $converter->getRate();
        } else {
            $this->convertertRate = round(1 / $converter->getRate(), 6);
        }
        $beginDate = (strcmp($cmBeginDate, $converter->getBeginDate()) < 0)?
            $converter->getBeginDate():$cmBeginDate;
        $endDate = (strcmp($cmEndDate, $converter->getEndDate()) < 0)?
            $cmEndDate:$converter->getEndDate();
        if ($beginDate > $cmBeginDate || $endDate < $cmEndDate) {
            $msg .= ' ' . _('The results will be calculated for the period from %s to %s.');
            $beginDateArray = explode('-', $beginDate);
            $url .= '?bDate[Y]=' . $beginDateArray[0] . '&bDate[m]=' . ltrim($beginDateArray[1], '0') 
                 . '&bDate[d]=' . ltrim($beginDateArray[2], '0');
            $endDateArray = explode('-', $endDate);
            $url .= '&eDate[Y]=' . $endDateArray[0] . '&eDate[m]=' . ltrim($endDateArray[1], '0') 
                 . '&eDate[d]=' . ltrim($endDateArray[2], '0') . '&Currency=' . $_REQUEST['Currency']
                 . '&CurrencyConverter=' . $_REQUEST['CurrencyConverter'] 
                 . '&AccountingType=' . urlencode($_REQUEST['AccountingType']);
            if (isset($_REQUEST['Export'])) {
                $url .= '&Export=1';
            } else {
                $url .= '&formSubmitted=1';
            }
            Template::errorDialog(
                sprintf(
                    $msg, 
                    I18N::formatDate($beginDate, 'd/m/Y'), 
                    I18N::formatDate($endDate, 'd/m/Y')), 
                $url);
            exit;
        }
        
        return $return;
    }

    // }}}
}

?>
