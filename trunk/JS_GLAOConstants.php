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

require_once("config.inc.php");
require_once('Objects/Task.inc.php');

?>

var GLAO = new GLAO();
function GLAO(){
	this.isTransportTask = GLAO_isTransportTask;
	this.isProductionTask = GLAO_isProductionTask;
	this.isInternalStockTask = GLAO_isInternalStockTask;
	this.isChainActivationTask = GLAO_isChainActivationTask;
}

function GLAO_isTransportTask(taskIndex){
	return (taskIndex == <?php echo TASK_GROUND_TRANSPORT; ?>) 
		|| (taskIndex == <?php echo TASK_SEA_TRANSPORT; ?>) 
		|| (taskIndex == <?php echo TASK_INLAND_WATERWAY_TRANSPORT; ?>) 
		|| (taskIndex == <?php echo TASK_RAILWAY_TRANSPORT; ?>) 
		|| (taskIndex == <?php echo TASK_AIR_TRANSPORT; ?>);
}

function GLAO_isProductionTask(taskIndex){
    return (taskIndex == <?php echo TASK_ASSEMBLY; ?>)
        || (taskIndex == <?php echo TASK_SUIVI_MATIERE; ?>);
}

function GLAO_isInternalStockTask(taskIndex){
	return (taskIndex == <?php echo TASK_INTERNAL_STOCK_ENTRY; ?>) 
		|| (taskIndex == <?php echo TASK_INTERNAL_STOCK_EXIT; ?>);
}

function GLAO_isChainActivationTask(taskIndex){
	return (taskIndex == <?php echo TASK_ACTIVATION; ?>);
}
