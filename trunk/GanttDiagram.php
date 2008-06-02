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
require_once('jpgraph/src/jpgraph.php');
require_once('jpgraph/src/jpgraph_gantt.php');
require_once('Scheduler/Scheduler.php');

if (!empty($_REQUEST['chnId'])) {
    $achMapper = Mapper::singleton('ActivatedChain');
    $ach = $achMapper->load(array('Id' => $_REQUEST['chnId']));
} else {
    Template::errorDialog(_('Please select a chain'), 'home.php');
    exit;
}

$scheduler = new Scheduler();
$cmdItemCol = $ach->getCommandItemCollection();
$cmdItem = $cmdItemCol->getItem(0);
$cmd = $cmdItem->getCommand();

$graph = new GanttGraph(0,0,'auto');
$graph->setColor(array(240, 240, 240));
$graph->setShadow();
$graph->setBox();
if ($cmd instanceof Command) {
    $dest = $cmd->getDestinator();
    $destName = $dest instanceof Actor ? $dest->getName() : '';
    $graph->title->set(sprintf(_('Schedule for chain "%s" of order "%s", customer "%s".'),
        $ach->getReference(), $cmd->getCommandNo(), $destName));
} else {
    $graph->title->set(sprintf(_('Schedule for chain "%s".'),
        $ach->getReference()));
}

$pivotTask = $ach->getPivotTask();
$lastPosition = 0;
$lastOperationPosition = 0;
$isPivotTask = 0;
$acoCol = $ach->getActivatedChainOperationCollection();
$acoCol->sort('Order');

$data = array();
$constrains = array();
$count = $acoCol->getCount();
for($i = 0; $i < $count; $i++) {
    // on traite les opérations
    $aco = $acoCol->getItem($i);
    $acoActor = $aco->getActor();

    $data[] = array($lastPosition, ACTYPE_GROUP, $aco->getName(),
        $aco->getBegin(), $aco->getEnd(),
        '(' . _('Actor') . ' : ' . $acoActor->getName() . ')');
    if ($i > 0) {
        //$constrains[] = array($lastOperationPosition, $lastPosition, CONSTRAIN_ENDSTART);
    }

    $ackCol = $aco->getActivatedChainTaskCollection();
    $ackCol->sort('Order');

    $lastPosition++;
    $jcount = $ackCol->getCount();
    for($j = 0; $j < $jcount; $j++) {
        $ack = $ackCol->getItem($j);
        $beginDate = $ack->getBegin();
        $endDate = $ack->getEnd();
        $caption = sprintf(
            _('%s %s from %s to %s'),
            $ack->getName(),
            $isPivotTask?_('[Deadline]'):'',
            I18N::formatDate($beginDate) ,
            I18N::formatDate($endDate)
        );
        $isPivotTask = (serialize($ack) == serialize($pivotTask));
        $activity = new GanttBar($lastPosition, $caption, $ack->getBegin(), $endDate);

        switch ($ack->getTriggerMode()) {
            case ActivatedChainTask::TRIGGERMODE_MANUAL:
            case ActivatedChainTask::TRIGGERMODE_AUTO:
                $activity->setPattern(BAND_RDIAG, 'blue');
                $activity->setFillColor('maroon');
                break;
            case ActivatedChainTask::TRIGGERMODE_TEMP: ;
                $activity->setPattern(BAND_RDIAG, 'yellow');
                $activity->setFillColor('yellow');
                break;
        }

        if (true == $isPivotTask) {
            $activity->setPattern(BAND_RDIAG, 'yellow');
            $activity->setFillColor('red');
            if ($cmd->getCadenced()) {
                // pour une commande cadencée on prend la date du commanditem 
                // lié à la chaine activée
                $cmi = $ach->getCommandItemCollection()->getItem(0);
                $wishedDate = $cmi->getWishedDate();
            } else {
                $wishedDate = DateTimeTools::timeStampToMySQLDate(
                    $scheduler->getReferenceDate(
				        $cmd->getWishedStartDate(), $cmd->getWishedEndDate()
                    )
                );
            }
			$milestone = new MileStone(++$lastPosition, _('Wished date'),
				$wishedDate, I18N::formatDate($wishedDate));
            $graph->Add($milestone);
            $vline = new GanttVLine($wishedDate);
            $graph->Add($vline);
        }
        $graph->Add($activity);
        $endDate = $ack->getEnd();
        $lastPosition++;
    }
	$lastOperationPosition++;
}

// ajuster les paramètres du graphe si la chaine s'étale sur plusieurs jours
$duration = $ach->getEndDate('timestamp') - $ach->getBeginDate('timestamp');
if ($duration > (DateTimeTools::ONE_DAY * 30 * 3)) { // sur plus de 3 mois
    $headers  = GANTT_HWEEK | GANTT_HMONTH | GANTT_HYEAR;
    $interval = 1;
} else if ($duration > (DateTimeTools::ONE_DAY * 30)) { // sur plus d'un mois
    $headers  = GANTT_HDAY | GANTT_HWEEK | GANTT_HMONTH | GANTT_HYEAR;
    $interval = 1;
} else if ($duration > DateTimeTools::ONE_WEEK) { // sur plusieurs semaines
    $headers  = GANTT_HDAY | GANTT_HWEEK | GANTT_HMONTH;
    $interval = 1;
} else if ($duration > DateTimeTools::ONE_DAY) { // sur plusieurs jours
    $headers  = GANTT_HHOUR | GANTT_HDAY | GANTT_HWEEK;
    $interval = 4;
} else { // sur plusieurs heures
    $headers  = GANTT_HHOUR | GANTT_HDAY;
    $interval = 1;
}

$graph->showHeaders($headers);

$graph->scale->week->setStyle(WEEKSTYLE_FIRSTDAY);
$graph->scale->week->setFont(FF_FONT0);

$graph->scale->day->setBackgroundColor('darkorange');
$graph->scale->day->setFont(FF_FONT0);
$graph->scale->day->setStyle(DAYSTYLE_SHORTDAYDATE1);
// Setup hour format
$graph->scale->hour->setIntervall($interval);
$graph->scale->hour->setBackgroundColor('lightyellow');
$graph->scale->hour->setFont(FF_FONT0);
$graph->scale->hour->setStyle(HOURSTYLE_HM24);
$graph->scale->hour->grid->setColor('gray:0.8');
// Add the specified activities
$graph->createSimple($data, $constrains);
// .. and stroke the graph
$graph->stroke();

?>
