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

require_once('Objects/WeeklyPlanning.php');

define('FORWARD', 1);
define('BACKWARD', 2);

class PlanningTools {
    var $_WeeklyPlanning = null;

    /**
     * Initialize a new instance of WeeklyPlanning
     * 
     * @access public 
     */
    function PlanningTools($WeeklyPlanning) {
        $this->_WeeklyPlanning = $WeeklyPlanning;
    } 

    /**
     * 
     * @access private 
     * @return array 
     * @param timestamp $date 
     * @param object $planningInstance 
     */
    function getNextAvailableRange($d, $withIndisponibilities=false) {
        $accessor = 'get' . date('l', $d);
        $absdate = mktime(0, 0, 0, date('m', $d), date('d', $d),date('Y', $d));
        $i = 0;

        while ($i < 8) {
            $p = $this->_WeeklyPlanning->$accessor();
            $rangelength = $this->getPlanningRangeLength($p); 
            for($j = 0; $j < $rangelength; $j++) {
                $range = $this->getPlanningRange($p, $j, $rangelength);
                if (false == $range) {
                    continue;
                }					
                $res['Start'] = $absdate + $range['Start'];
                $res['End'] = $absdate + $range['End'];
                if ($res['Start'] > $d) {
                    return $res;
                } 
                if ($res['End'] > $d) {
                    return array('Start' => $d, 'End' => $res['End']);
                } 
            }
            // Va au jour suivant
            $absdate += DateTimeTools::ONE_DAY;
            $accessor = 'get' . date('l', $absdate); 
            $i++;
        } 
        return false;
    } 

    /**
     * 
     * @access private 
     * @return array 
     * @param timestamp $d
     * @param object $planningInstance 
     */
    function getPreviousAvailableRange($d, $withIndisponibilities=false) {
        $firstrange = false;
        $accessor = 'get' . date('l', $d);
        $absdate = mktime(0, 0, 0, date('m', $d), date('d', $d),date('Y', $d));
        $i = 0;
        while ((false == $firstrange) && ($i < 8)) {
            $p = $this->_WeeklyPlanning->$accessor();
            $rangelength = $this->getPlanningRangeLength($p);
            for($j = $rangelength-1; (false == $firstrange) && ($j >= 0); $j--) {
                $range = $this->getPlanningRange($p, $j, $rangelength);
                if (false == $range) {
                    continue;
                }
               	$ret['Start'] = $absdate + $range['Start'];
                $ret['End'] = $absdate + $range['End'];
                if ($ret['Start'] < $d && $ret['End'] >= $d) {
                    $firstrange = array(
                        'Start'=>$ret['Start'], 
                        'End'=>$d
                    );
                    break;
                } else if ($ret['End'] <= $d) {
                    $firstrange = array(
                        'Start' => $ret['Start'],
                        'End' => $ret['End']
                    );
                    break;
                }
            } 
            // Va au jour précédent
            $absdate -= DateTimeTools::ONE_DAY;
            $accessor = 'get' . date('l', $absdate); 
            $i++;
        } 
        return $firstrange;
    } 

    /**
     * Renvoie le nombre de plage horaires du planning journalier
     * 
     * @access public 
     * @return void 
     */
    function getPlanningRangeLength($dp) {
        if (!($dp instanceof DailyPlanning)) {
            return 0;
        } 
        if (0 == $dp->getPause() &&  0 == $dp->getRestart()) {
            if (0 == $dp->getStart() &&  0 == $dp->getEnd()) {
                return 0;
            } 
            return 1;
        } 
        return 2;
    }

    /**
     * 
     * @access public 
     * @return void 
     */
    function getPlanningRange($dp, $index, $rlength) {
        if ($rlength == 1) {
            if ($index == 0) {
                $result = array(
                    'Start' => DateTimeTools::TimeToTimeStamp($dp->getStart()),
                    'End' => DateTimeTools::TimeToTimeStamp($dp->getEnd()));
            } else {
                return false;
            } 
        } else if ($rlength == 2) {
            switch ($index) {
                case 0:
                    $result = array(
                        'Start' => DateTimeTools::TimeToTimeStamp($dp->getStart()),
                        'End' => DateTimeTools::TimeToTimeStamp($dp->getPause()));
                    break;
                case 1:
                    $result = array(
                        'Start' => DateTimeTools::TimeToTimeStamp($dp->getRestart()),
                        'End' => DateTimeTools::TimeToTimeStamp($dp->getEnd()));
                    break;
                default:
                    return false;
            } 
        } else {
            return false;
        } 
        return $result;
    } 
    
    /**
     * Function creee pour WOOpeTaskList.php, pour afficher les creneaux de
     * date possibles en tenant compte de WishedStartDate et WishedEndDate de 
     * la commande
     * 
     * @access private 
     * @return array 
     * @param datetime $date 
     * @param time $begin : provient de WishedStartDate
     * @param time $end : provient de WishedEndDate
     */
    function getDayRange($date, $begin, $end) {
        $accessor = 'get' . date('l', $date); 
        // c'est un DailyPlanning
        $planning = $this->_WeeklyPlanning->$accessor(); 
        // nbre de crenaux ds la journee
        $rangeLength = $this->getPlanningRangeLength($planning);
        /*  traitement de l'horaire de debut  */
        if ($begin > $planning->getEnd()) {
            $startTime = 'Impossible';
        } elseif ($begin > $planning->getRestart()) {
            $startTime = $begin;
            $rangeLength = 1;
        } elseif ($begin > $planning->getPause()) {
            $startTime = $planning->getRestart();
            $rangeLength = 1;
        } else {
            $startTime = ($begin > $planning->getStart())?
                $begin:$planning->getStart();
        } 

        /*  traitement de l'horaire de fin  */
        if ($end < $planning->getStart()) {
            $endTime = 'Impossible';
        } elseif ($end < $planning->getPause()) {
            $endTime = $end;
            $rangeLength = 1;
        } elseif ($end < $planning->getRestart()) {
            $endTime = $planning->getPause();
            $rangeLength = 1;
        } else {
            $endTime = ($end < $planning->getEnd())?$end:$planning->getEnd();
        } 
        // affichage du nom du jour en francais
        $dayRangeToDisplay = I18N::formatDate($date, "%A") . ': ';

        if ($startTime == 'Impossible' || $endTime == 'Impossible') {
            return $dayRangeToDisplay . _('Delivery impossible this day.');
        } 
        if (false != $planning && $rangeLength == 1) {
            // si une seule plage horaire
            $dayRangeToDisplay .= DateTimeTools::RangeDisplay(substr($startTime, 0, 5),
                substr($endTime, 0, 5));
            return $dayRangeToDisplay;
        } 
        if (false != $planning && $rangeLength == 2) {
            $dayRangeToDisplay .= DateTimeTools::RangeDisplay(substr($startTime, 0, 5),
                substr($planning->getPause(), 0, 5)) . _(' and ');
            $dayRangeToDisplay .= DateTimeTools::RangeDisplay(
                substr($planning->getRestart(), 0, 5), 
                substr($endTime, 0, 5));
            return $dayRangeToDisplay;
        } 
        return false;
    } 
    
    /**
     *
     * @access public
     * @return void 
     **/
    function getUnavailabilityCollectionForDate($date, $wplanning=false){
        // quelques conversions
        $d1 = DateTimeTools::timeStampToMySQLDate($date);
        $d2 = DateTimeTools::timeStampToMySQLDate($date + DateTimeTools::ONE_DAY);
        // on récupère la collection d'indisponibilités filtrée avec la date
        // passée en paramètre.
        // construction du filtre
        $filter = new FilterComponent();
        $filter->operator = FilterComponent::OPERATOR_OR; 
        // le filtre est: la date de début ou de fin de l'indisponibilité doit
        // être supérieure à la date et inférieure à la date + 1 jour, ou 
        // alors la date de début de l'indisponibilité doit être inférieure à 
        // la date et la date de fin supérieure à la date.
        // component 1
        $filter->setItem(
            new FilterRule(
                'BeginDate',
                FilterRule::OPERATOR_BETWEEN,
                array($d1, $d2)
            )
        );
        $filter->setItem(
            new FilterRule(
                'EndDate',
                FilterRule::OPERATOR_BETWEEN,
                array($d1, $d2)
            )
        );
        // chargement de la collection
		
        return $wplanning->getUnavailabilityCollection($filter);    	
    }    

// ----------------------------------------------------------------------------
// LES FONCTIONS CI-DESSOUS NE SONT PAS ENCORE UTILISEES
// ----------------------------------------------------------------------------

        
    /**
     * 
     * @access private 
     * @return array 
     * @param timestamp $date 
     * @param object $planningInstance 
     */
    function getNextAvailableRange_new($d, $withIndisponibilities=false) {
        $accessor = 'get' . date('l', $d);
        $absdate = mktime(0, 0, 0, date('m', $d), date('d', $d),date('Y', $d));
        $i = 0;

        while ($i < 8) {
            $p = $this->_WeeklyPlanning->$accessor();
            if ($p instanceof DailyPlanning) {
                for($j = 0; $j < $this->getPlanningRangeLength($p); $j++) {
					if ($withIndisponibilities) {
	                    $ranges = $this->getAvailablePlanningRanges(
	                        $this->getPlanningRange($p, $j), $absdate, FORWARD);
	                    if (false == $ranges) {
	                        continue;
	                    }
					} else {
						$ranges = array($this->getPlanningRange($p, $j));
					}
                    foreach($ranges as $r){
						if (false == $r) {
						    continue;
						}					
                        $res['Start'] = $absdate + $r['Start'];
                        $res['End'] = $absdate + $r['End'];
                        if ($res['Start'] >= $d) {
                            return $res;
                        } 
                        if ($res['End'] >= $d) {
							$res = array('Start' => $d, 'End' => $res['End']);
                            return $res;
                        } 
                    }
                } 
            } 
            // Va au jour suivant
            $absdate += DateTimeTools::ONE_DAY;
            $accessor = 'get' . date('l', $absdate); 
            $i++;
        } 
        return false;
    } 

    /**
     * 
     * @access private 
     * @return array 
     * @param timestamp $d
     * @param object $planningInstance 
     */
    function getPreviousAvailableRange_new($d, $withIndisponibilities=false) {
        $firstrange = false;
        $accessor = 'get' . date('l', $d);
        $absdate = mktime(0, 0, 0, date('m', $d), date('d', $d),date('Y', $d));
        $i = 0;
        while ((false == $firstrange) && ($i < 8)) {
            $p = $this->_WeeklyPlanning->$accessor();
            if (false != $p) {
                for($j = $this->getPlanningRangeLength($p)-1;
                    (false == $firstrange) && ($j >= 0); $j--) {
					if ($withIndisponibilities) {
	                    $ranges = $this->getAvailablePlanningRanges(
	                        $this->getPlanningRange($p, $j), $absdate, BACKWARD);
	                    if (false == $ranges) {
	                        continue;
	                    }                 
	                    $ranges = array_reverse($ranges);
					} else {
						$ranges = array($this->getPlanningRange($p, $j));
					}
                    foreach($ranges as $r){
						if (false == $r) {
						    continue;
						}
                    	$ret['Start'] = $absdate + $r['Start'];
                        $ret['End'] = $absdate + $r['End'];
                        if ($ret['Start'] < $d && $ret['End'] >= $d) {
                            $firstrange = array(
                                'Start'=>$ret['Start'], 
                                'End'=>$d
                            );
                            break;
                        } else if ($ret['End'] <= $d) {
                            $firstrange = array(
                                'Start' => $ret['Start'],
                                'End' => $ret['End']
                            );
                            break;
                        }                        
                    }
                    
 
                } 
            } 
            // Va au jour précédent
            $absdate -= DateTimeTools::ONE_DAY;
            $accessor = 'get' . date('l', $absdate); 
            $i++;
        } 
        return $firstrange;
    } 
    
    /**
     * PlanningTools::getAvailablePlanningRanges()
     * Retourne le créneau de date du planning d'un acteur en fonction des 
     * indisponibilités du planning à partir d'une date donnée et d'un créneau 
     * d'heures.
     * 
     * @access public 
     * @param array $range ex: array('Start'=>'08H00', 'End'=>'12:00')
     * @param string $date une date (sans les heures) style: '2005-04-01'
     * @return array ex: array('Start'=>'10H30', 'End'=>'12:00')
     */
    function getAvailablePlanningRanges($range, $date, $direction=FORWARD) {
        // quelques conversions
        $filterdate = DateTimeTools::timeStampToMySQLDate($date);
        $filterdate2 = DateTimeTools::timeStampToMySQLDate($date + DateTimeTools::ONE_DAY);
        // on récupère la collection d'indisponibilités filtrée avec la date
        // passée en paramètre.
        // construction du filtre
        $filter = new FilterComponent();
        $filter->operator = FilterComponent::OPERATOR_OR; 
        // le filtre est: la date de début ou de fin de l'indisponibilité doit
        // être supérieure à la date et inférieure à la date + 1 jour, ou 
        // alors la date de début de l'indisponibilité doit être inférieure à 
        // la date et la date de fin supérieure à la date.
        // component 1
        $component1 = new FilterComponent();
        $component1->operator = FilterComponent::OPERATOR_AND;
        $component1->setItem(
            new FilterRule(
                'BeginDate',
                FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                $filterdate
            )
        );
        $component1->setItem(
            new FilterRule(
                'BeginDate',
                FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                $filterdate2
            )
        );
        $filter->setItem($component1);
        // component 2
        $component2 = new FilterComponent();
        $component2->operator = FilterComponent::OPERATOR_AND;
        $component2->setItem(
            new FilterRule(
                'EndDate',
                FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                $filterdate
            )
        );
        $component2->setItem(
            new FilterRule(
                'EndDate',
                FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                $filterdate2
            )
        ); 
        $filter->setItem($component2);
        // chargement de la collection
		
        $col = $this->_WeeklyPlanning->getUnavailabilityCollection($filter);
        // si la collection est vide, c'est qu'il n'y a pas d'indisponibilités.
        // on retourne donc simplement le créneau tel quel.
        $count = $col->getCount();
        if ($count == 0) {
            return array($range);
        } 
        // sinon il faut traiter les indisponibilités...
        // on trie la collection par date de début
        $col->sort('BeginDate', SORT_ASC);
        
        // tableau des plages dispos
        $ranges = array();
        // date absolue + time
        $start = $date + $range['Start'];
        $end   = $date + $range['End'];
        $pendingrange = false;
        // pour chaque indisponibilité
        for($i = 0; $i < $count; $i++) {
            // une indisponibilité
            $u = $col->getItem($i);
            // on doit convertir en timestamp les dates mysql
            $ustart = DateTimeTools::MysqlDateToTimeStamp($u->getBeginDate());
            $uend   = DateTimeTools::MysqlDateToTimeStamp($u->getEndDate());
            // on stocke les heures uniquement dans les créneaux
            $ustarttime = $ustart - ($date);
            $uendtime   = $uend - ($date);
            if (false != $pendingrange) {
                $pendingrange['End'] = $ustarttime;
                $ranges[] = $pendingrange;
                $start = $ustart;
            }
            $pendingrange = false;
            if ($ustart < $start && $uend > $end) {
                // si l'indisponibilité est plus grande que le créneau
                //     Start |-----------------| End
                // Ustart |----------------------------| UEnd
                // pas la peine d'aller plus loin
                continue;
            }
            
            if ($ustart > $start && $uend < $end) {
                // si l'indisponibilité est comprise dans le créneau
                //     Start |-------------------| End
                //         Ustart |-------| UEnd
                $ranges[] = array('Start'=>$range['Start'],'End'=>$ustarttime);
                $start = $uend;
            } else if ($ustart > $start && $ustart < $end && $uend > $end) {
                // si l'indisponibilité est à cheval sur le créneau à droite
                //     Start |-------------------| End
                //         Ustart |---------------------------| UEnd
                $ranges[] = array('Start'=>$range['Start'],'End'=>$ustarttime);
                // pas la peine d'aller plus loin
                break;
            } else if ($uend > $start && $uend < $end) {
                // si l'indisponibilité est à cheval sur le créneau à gauche
                //     Start |-------------------| End
                // Ustart |-----------| UEnd
                // s'il y'a une indisponibilité suivante la fin est le début 
                // de celle-ci
                if ($i < $count - 1) {
                    $pendingrange = array('Start'=>$uendtime);
                    $start = $uend;
                } else {
                    $ranges[] = array('Start'=>$uendtime,'End'=>$range['End']);
                }
            }
        }
        return !empty($ranges)?$ranges:false;
    } 

} 

?>
