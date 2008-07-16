<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IMPORTANT: This is a generated file, please do not edit.
 *
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

/**
 * FlowCategory class
 *
 * Class containing addon methods.
 */
class FlowCategory extends _FlowCategory {
    // Constructeur {{{

    /**
     * FlowCategory::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // FlowCategory::getTreeItems() {{{

    /**
     * getTreeItems 
     *
     * retourne l'arbo utiliser par tigra tree menu pour représenter le treeview
     *
     * @access public
     * @return void
     */
    public function getTreeItems() {
        $return = array();
        $return[0] = $this->getName();
        $return[1] = 'dispatcher.php?entity=FlowCategory&action=edit&objID=' . $this->getId();
        $children = $this->getFlowCategoryCollection(array(), array('DisplayOrder'=>SORT_ASC));
        $index = 2;
        foreach($children as $key=>$child) {
            $return[$index] = $child->getTreeItems();
            $index++;
        }
        $children = $this->getFlowTypeCollection(array(), array('Name'=>SORT_ASC));
        foreach($children as $key=>$child) {
            $return[$index] = array(
                0 => $child->getName(),
                1 => 'dispatcher.php?entity=FlowType&action=edit&objID=' . $child->getId());
            $index++;
        }
        return $return;
    } 

    // }}}
    // FlowCategory::getCashBalance() {{{ 

    /**
     * getCashBalance 
     * 
     * @param array $params 
     * @access public
     * @return void
     */
    public function getCashBalance($params=array(), $totals=array()) {
        $return = array();
        $return[0] = $this->getName();
        $return[1] = 0;
        $return[2] = 0;
        $return[3] = array();
        $return[4] = 'FlowCategory_' . $this->getId();
        $sum = 0;
        
        // FlowType
        $filter = array();
        if(isset($params['accountingType']) && $params['accountingType']) {
            $filter['AccountingType'] = $params['accountingType'];
        }
        $flowTypes = $this->getFlowTypeCollection($filter, array('Name'=>SORT_ASC));
        foreach($flowTypes as $flowType) {
            list($result, $totals) = $flowType->getCashBalance($params, $totals);
            $return[1] += $result[1];
            $return[2] += $result[2];
            $return[3][] = $result;
        }
        // FlowCategory fille
        $children = $this->getFlowCategoryCollection(array(), array('DisplayOrder'=>SORT_ASC));
        foreach($children as $child) {
            list($result, $totals) = $child->getCashBalance($params, $totals);
            $return[1] += $result[1];
            $return[2] += $result[2];
            $return[3][] = $result;
        }
        return array($return, $totals);
    }

    // }}}


}

?>