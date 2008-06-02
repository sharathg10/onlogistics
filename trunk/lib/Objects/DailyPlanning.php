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

class DailyPlanning extends _DailyPlanning {
    // Constructeur {{{

    /**
     * DailyPlanning::__construct()
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
     * @return void 
     **/
    function getGraph($d = 0, $wplanning = false, $title = ''){
        require_once('jpgraph/src/jpgraph.php');
        require_once('jpgraph/src/jpgraph_gantt.php');
        require_once('PlanningTools.php');
        
        $td = DateTimeTools::MySQLDateToTimeStamp($d);
        $absdate = mktime(0, 0, 0, date('m', $td), date('d', $td),date('Y', $td));
        $mabsdate = DateTimeTools::timeStampToMySQLDate($absdate);
        $mabsdate1day = DateTimeTools::timeStampToMySQLDate($absdate + DateTimeTools::ONE_DAY-1);
        $graph = new GanttGraph();
        $graph->SetMarginColor('black:1.7');
        $graph->SetColor('white');
        $graph->SetBackgroundGradient('white','lightgray',GRAD_LEFT_REFLECTION,BGRAD_MARGIN);
        $graph->SetShadow();
        $graph->SetBox();
        $graph->ShowHeaders(GANTT_HDAY | GANTT_HHOUR);
        
        $graph->title->Set($title);
        $graph->subtitle->Set(_('(in green: available time slots)'));
        
        // Setup day format
        $graph->scale->day->SetBackgroundColor('darkgray:1.5');
        $graph->scale->day->SetFont(FF_FONT0);
        $graph->scale->day->SetStyle(DAYSTYLE_LONGDAYDATE1);
        // Setup hour format
        $graph->scale->hour->SetIntervall(1);
        $graph->scale->hour->SetBackgroundColor('lightgray:1.5');
        $graph->scale->hour->SetFont(FF_FONT0);
        $graph->scale->hour->SetStyle(HOURSTYLE_HM24);
        $graph->scale->hour->grid->SetColor('gray:0.8');
        
        $mstart = DateTimeTools::MySQLDateAdd($mabsdate, $this->getStart());
        $mend = DateTimeTools::MySQLDateAdd($mabsdate, $this->getPause());
        $astart = DateTimeTools::MySQLDateAdd($mabsdate, $this->getRestart());
        $aend = DateTimeTools::MySQLDateAdd($mabsdate, $this->getEnd());
        $length = PlanningTools::getPlanningRangeLength($this);
        if ($length == 1) {
            $pbar = new GanttBar(0, 'Planning', $mstart, $aend);
            $pbar->SetFillColor('green');
            $graph->Add($pbar);
        } else if ($length == 2) {
            $pbar1 = new GanttBar(0, 'Planning', $mstart, $mend);
            $pbar2 = new GanttBar(0, 'Planning', $astart, $aend);
            $pbar1->SetFillColor('green');
            $pbar2->SetFillColor('green');
            $graph->Add($pbar1);
            $graph->Add($pbar2);
        } else {
            return false;
        }
        
        // indispos
        if ($wplanning) {
            $col = PlanningTools::getUnavailabilityCollectionForDate($absdate, 
                $wplanning);
            $count = $col->getCount();
            for($i=0; $i<$count; $i++){
                $unav = $col->getItem($i);
                $ustart = $unav->getBeginDate();
                if ($ustart < $mabsdate) {
                    $ustart = $mabsdate;
                }
                $uend = $unav->getEndDate();
                if ($uend > $mabsdate1day) {
                    $uend = $mabsdate1day;
                }
                $pbar = new GanttBar(0, '', $ustart, $uend);
                $pbar->setPattern(BAND_SOLID, 'white');
                $graph->Add($pbar);
            }
        }
        return $graph;
    }

}

?>