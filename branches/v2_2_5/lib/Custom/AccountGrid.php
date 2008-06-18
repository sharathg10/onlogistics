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

require_once('Objects/FlowType.php');

/**
 * AccountGrid
 * 
 */
class AccountGrid extends GenericGrid {
    // AccountGrid::__construct() {{{

    /**
     * Constructor
     *
     * @param array $params
     * @access public
     */
    public function __construct($params) {
        $params['itemsperpage'] = 200;
        parent::__construct($params);
    }

    // }}}
    // AccountGrid::renderSearchFormFlowType() {{{

    /**
     * renderSearchFormFlowType 
     * 
     * @access public
     * @return void
     */
    public function renderSearchFormFlowType() {
        $flowTypeArray_Charges = SearchTools::CreateArrayIDFromCollection(
            'FlowType', array('Type'=>FlowType::CHARGE), _('Select expenses'), 'Name');
        $flowTypeArray_Recettes = SearchTools::CreateArrayIDFromCollection(
            'FlowType', array('Type'=>FlowType::RECETTE), _('Select receipts'), 'Name');

        $this->searchForm->addBlankElement();
        $this->searchForm->addElement('select', 'FlowType_Charges', 
            _('Expenses'), array($flowTypeArray_Charges), 
            array('Path'=>'FlowType().Id'));
        $this->searchForm->addElement('select', 'FlowType_Recette', 
            _('Receipts'), array($flowTypeArray_Recettes), 
            array('Path'=>'FlowType().Id'));
    }

    // }}}
    // AccountGrid::renderSearchFormOwner() {{{
    
    public function renderSearchFormOwner() {
        if($this->auth->getProfile()==UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT) {
            $arr = SearchTools::CreateArrayIDFromCollection('Actor', 
                array('Id'=>$this->auth->getActorId()));
        } else {
            $arr = SearchTools::CreateArrayIDFromCollection('Actor', 
                array('Generic'=>0), MSG_SELECT_AN_ELEMENT);
        }
        $this->searchForm->addElement('select', 'Owner', 
            _('Linked to the accounting plan of'), 
            array($arr));
    }
    
    // }}}
}

?>
