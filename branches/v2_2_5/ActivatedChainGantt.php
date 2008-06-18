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

$auth = Auth::singleton();

if (isset($_REQUEST['chnId'])) {
    $achMapper = Mapper::singleton('ActivatedChain');
    $ach = $achMapper->load(array('Id' => $_REQUEST['chnId']));
} else {
    Template::errorDialog(_('Please select a chain'), 'home.php');
    exit;
}

if (isset($_REQUEST['manual'])) {
    ob_start();
    dumpActivatedChainSchedule($ach);
    $content = ob_get_contents();
    ob_end_clean();
    Template::page(_('Gantt diagram of a scheduled chain'), $content);
    exit;
} else {
    $content = '<table width="100%" height="100%">
    	<tr>
    	<td valign="top" align="center">
    		<img src="GanttDiagram.php?chnId=' . $ach->getId() . '&showGantt=1" alt="Gantt" />
    	</td>
    </tr>
    </table>';
    Template::page(_('Gantt diagram of a scheduled chain'), $content);
}

?>
