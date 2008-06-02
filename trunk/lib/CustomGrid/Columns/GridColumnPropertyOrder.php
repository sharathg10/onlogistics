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

class GridColumnPropertyOrder extends AbstractGridColumn {

    /**
     * Constructor
     * 
     * @access protected 
     */
    function GridColumnPropertyOrder($title = '', $params = array()) {
        parent::__construct($title, $params);
    } 

    function Render($object) {
        $order = 0;
    	$domPropertyMapper = Mapper::singleton('DocumentModelProperty');
    	
    	if(isset($_SESSION['domId'])) {
    	    /*Pour le grid 1*/
    	    if($object instanceof Property) {
    	        if(isset($_SESSION['domPropCol'])) {    	            
    	            $count = $_SESSION['domPropCol']->getCount();
    	            for ($i=0 ; $i<$count ; $i++) {
    	                $domProperty = $_SESSION['domPropCol']->getItem($i);
    	                if($domProperty->getPropertyId()!=$object->getId()) {
    	                    unset($domProperty);
    	                    continue;
    	                }    	                
    	                $order = $domProperty->getOrder();
    	                break;
    	            }
    	        } else {
        	        $domProperty = $domPropertyMapper->load(
                	           array('Property'=>$object->getId(), 
                	                 'PropertyType'=>0,
                	                 'DocumentModel'=>$_SESSION['domId']));
                    if(!Tools::isEmptyObject($domProperty)) {
                        $order = $domProperty->getOrder();
                    }
    	        }
                return '<input type="text" size="4" '.
                 'name="DocumentModelProperty_Order['.$object->getId().']" '.
                 'value="'.$order.'" />';
    	    }

    	    /*Pour le grid 2*/
    	    elseif ($object instanceof DocumentModelProperty) {
    	        if(isset($_SESSION['domPropCol'])) {
    	            $count = $_SESSION['domPropCol']->getCount();
    	            for ($i=0 ; $i<$count ; $i++) {
    	                $domProperty = $_SESSION['domPropCol']->getItem($i);
    	                if($domProperty->getPropertyType()!=$object->getId()) {
    	                    unset($domProperty);
    	                    continue;
    	                }
    	                $order = $domProperty->getOrder();
    	                break;
    	            }
    	        } else {
    	            $domProperty = $domPropertyMapper->load(
        	          array('PropertyType'=>$object->getPropertyType(), 
        	                'DocumentModel'=>$_SESSION['domId']));
        	        if(!Tools::isEmptyObject($domProperty)) {
        	            $order = $domProperty->getOrder();
        	        }
    	        }
    	        
    	        return '<input type="text" size="4" '.
                 'name="DocumentModelCell_Order['.$object->getPropertyType().']" '.
                 'value="'.$order.'" />';
    	    }
    	    
    	}
    }
} 

?>