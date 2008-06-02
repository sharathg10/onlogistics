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

class WeeklyPlanning extends _WeeklyPlanning {
    // Constructeur {{{

    /**
     * WeeklyPlanning::__construct()
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
     *
     * @access public
     * @return object dailyplanning
     * @param $date une date MySQL ou un timestamp
     **/
    function getDailyPlanningForDate($date){
        if (!is_int($date)) {
            $date = DateTimeTools::MySQLDateToTimeStamp($date);
        }
        $day = date('l', $date);
        $accessor = 'get' . $day;
        if (method_exists($this, $accessor)) {
            return $this->$accessor();
        }
        return false;
    }  
    
    /**
     * Assigne les variables aux template planning
     * 
     * @access public
     * @return void 
     **/
    function renderTemplate($smarty){
        $smarty->assign("WeeklyPlanning_Id", $this->getId());
        $monday = $this->getMonday();
        if ($monday instanceof DailyPlanning) {
            $smarty->assign("Monday_Id", $monday->getId());
            $smarty->assign("Monday_Start", $monday->getStart());
            $smarty->assign("Monday_Pause", $monday->getPause());
            $smarty->assign("Monday_Restart", $monday->getRestart());
            $smarty->assign("Monday_End", $monday->getEnd());
        } 
        $tuesday = $this->getTuesday();
        if ($tuesday instanceof DailyPlanning) {
            $smarty->assign("Tuesday_Id", $tuesday->getId());
            $smarty->assign("Tuesday_Start", $tuesday->getStart());
            $smarty->assign("Tuesday_Pause", $tuesday->getPause());
            $smarty->assign("Tuesday_Restart", $tuesday->getRestart());
            $smarty->assign("Tuesday_End", $tuesday->getEnd());
        } 
        $wednesday = $this->getWednesday();
        if ($wednesday instanceof DailyPlanning) {
            $smarty->assign("Wednesday_Id", $wednesday->getId());
            $smarty->assign("Wednesday_Start", $wednesday->getStart());
            $smarty->assign("Wednesday_Pause", $wednesday->getPause());
            $smarty->assign("Wednesday_Restart", $wednesday->getRestart());
            $smarty->assign("Wednesday_End", $wednesday->getEnd());
        } 
        $thursday = $this->getThursday();
        if ($thursday instanceof DailyPlanning) {
            $smarty->assign("Thursday_Id", $thursday->getId());
            $smarty->assign("Thursday_Start", $thursday->getStart());
            $smarty->assign("Thursday_Pause", $thursday->getPause());
            $smarty->assign("Thursday_Restart", $thursday->getRestart());
            $smarty->assign("Thursday_End", $thursday->getEnd());
        } 
        $friday = $this->getFriday();
        if ($friday instanceof DailyPlanning) {
            $smarty->assign("Friday_Id", $friday->getId());
            $smarty->assign("Friday_Start", $friday->getStart());
            $smarty->assign("Friday_Pause", $friday->getPause());
            $smarty->assign("Friday_Restart", $friday->getRestart());
            $smarty->assign("Friday_End", $friday->getEnd());
        } 
        $saturday = $this->getSaturday();
        if ($saturday instanceof DailyPlanning) {
            $smarty->assign("Saturday_Id", $saturday->getId());
            $smarty->assign("Saturday_Start", $saturday->getStart());
            $smarty->assign("Saturday_Pause", $saturday->getPause());
            $smarty->assign("Saturday_Restart", $saturday->getRestart());
            $smarty->assign("Saturday_End", $saturday->getEnd());
        } 
        $sunday = $this->getSunday();
        if ($sunday instanceof DailyPlanning) {
            $smarty->assign("Sunday_Id", $sunday->getId());
            $smarty->assign("Sunday_Start", $sunday->getStart());
            $smarty->assign("Sunday_Pause", $sunday->getPause());
            $smarty->assign("Sunday_Restart", $sunday->getRestart());
            $smarty->assign("Sunday_End", $sunday->getEnd());
        }
        $ucol = $this->getUnavailabilityCollection(array(), 
            array('BeginDate'=>SORT_ASC));
        if (!Tools::isEmptyObject($ucol)) {
            $count = $ucol->getCount();
            $uarray = array();
            for($i=0; $i<$count; $i++){
                $u = $ucol->getItem($i);
                $cmd = $u->getCommand();
                $commandNo = 'N/A';
                if ($cmd instanceof Command) {
                    $commandNo = $cmd->getCommandNo();
                }
                $uarray[] = array(
                    'id'=>$u->getId(),
                    'purpose'=>$u->getPurpose(),
                    'beginDate'=>I18N::formatDate($u->getBeginDate()),
                    'endDate'=>I18N::formatDate($u->getEndDate()),
                    'commandNo'=>$commandNo
                );
            }
            $smarty->assign("UnavailabilityList", $uarray);
        }
        $smarty->assign("PHP_SELF", $_SERVER['PHP_SELF']);
    }

    /**
     * Crée un planning vide, le sauve et le retourne.
     *
     * @access public
     * @return object WeeklyPlanning
     */
    function createEmptyPlanning() {
        require_once('Objects/DailyPlanning.php');
        $planning = new WeeklyPlanning();
        $setters  = array(
            'setMonday', 'setTuesday', 'setWednesday', 'setThursday', 
            'setFriday', 'setSaturday', 'setSunday'
        );
        foreach($setters as $setter) {
            $day = new DailyPlanning();
            $day->save();
            $planning->$setter($day);
            unset($day);
        }
        $planning->save();
        return $planning;
    }

}

?>