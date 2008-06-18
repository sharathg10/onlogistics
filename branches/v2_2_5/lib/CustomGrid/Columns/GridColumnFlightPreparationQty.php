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

class GridColumnFlightPreparationQty extends AbstractGridColumn {
    
	 var $_liquid;
	
    /**
     * Constructor
     * 
     * @access protected 
     */
    function GridColumnFlightPreparationQty($title = '', $params = array()) {
        parent::__construct($title, $params);
		if (isset($params['liquid'])) {
            $this->_liquid = $params['liquid'];
        } 
    } 

    function Render($object) {
		require_once('Objects/ActivatedChainTask.php');
		$FlightPreparationTask = $object->getPreviousTask(array(ActivatedChainTask::TRIGGERMODE_AUTO, ActivatedChainTask::TRIGGERMODE_MANUAL,
						 								   ActivatedChainTask::TRIGGERMODE_TEMP), 'isFlightPreparationTask');
		if (Tools::isEmptyObject($FlightPreparationTask)) {
		    return _('N/A');
		}
		$actDetail = $FlightPreparationTask->getActivatedChainTaskDetail();
		if (Tools::isEmptyObject($actDetail)) {
		    return _('N/A');
		}
		$qtySetter = 'get' . $this->_liquid;
		$quantity = $actDetail->$qtySetter();
		return $quantity . ' L';
    } 
} 

?>
