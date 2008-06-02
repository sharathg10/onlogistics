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

require_once('Objects/Command.const.php');
require_once('Objects/Operation.const.php');
require_once('Objects/Task.const.php');

class GridColumnCourseCommandListRealDuration extends AbstractGridColumn {
    /**
     * Constructor
     * 
     * @access protected
     * @return void 
     **/
    function GridColumnCourseCommandListRealDuration($title = '', 
        $params = array()){
    	parent::__construct($title, $params);
    }
    
    /**
     *
     * @access public
     * @return void 
     **/
    function render($object){
        // ne pas aller plus loin si l'état est 
        if ($object->getState() < Command::LIV_COMPLETE) {
            return _('N/A');
        }
    	$cmiCollection = $object->getCommandItemCollection();
        $cmi = $cmiCollection->getItem(0); // il n'y en a qu'un
        $ach = $cmi->getActivatedChain();
        $acoCollection = $ach->getActivatedChainOperationCollection(
            array('Operation.Id'=>array(OPERATION_VOL))
        );
        if (Tools::isEmptyObject($acoCollection)) {
            return _('N/A');
        }
        $count = $acoCollection->getCount();
        for($i=0; $i<$count; $i++){
            $aco = $acoCollection->getItem($i);
            $ackCollection = $aco->getActivatedChainTaskCollection(
                array('Task.Id'=>array(TASK_FLY))
            );
            if (Tools::isEmptyObject($ackCollection)) {
                return _('N/A');
            }
            $jcount = $ackCollection->getCount();
            for($j=0; $j<$jcount; $j++){
                $ack = $ackCollection->getItem($j);
                $ackDetail = $ack->getActivatedChainTaskDetail();
                if ($ackDetail instanceof ActivatedChainTaskDetail) {
                    $ret = $ackDetail->getRealCommercialDuration();
                    return I18N::formatNumber($ret);
                }
            }
        }
        return _('N/A');
    }
    
}

?>