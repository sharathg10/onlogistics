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

class CurrencyConverterAddEdit extends GenericAddEdit {
    
    // CurrencyConverterAddEdit::onBeforeDisplay() {{{
    /**
     * onBeforeDisplay 
     * 
     * @access protected
     * @return void
     */
    protected function onBeforeDisplay() {
        $this->addJSRequirements('js/includes/CurrencyConverterAddEdit.js');
        //$this->form->updateAttributes(array('onsubmit'=>'return checkBeforeSubmit();'));
        // euro par default
        $this->formDefaults['CurrencyConverter_Currency_ID'] = 1;
        $this->form->setDefaults($this->formDefaults);
    }
    // }}}
    // CurrencyConverterAddEdit::onBeforeHandlePostData() {{{

    /**
     * Verifications avant enregistrement des saisies en base
     *
     * @access public
     * @return void
     */
    public function onBeforeHandlePostData() {
        //Database::connection()->debug=true;        
        // Verification qu'un taux de change n'existe pas deja sur une periode 
        // qui chevauche celle selectionnee, pour les devises selectionnees
        $beginDate = $_REQUEST['CurrencyConverter_BeginDate'];
        $endDate = $_REQUEST['CurrencyConverter_EndDate'];
        
        $mapper = Mapper::singleton('CurrencyConverter');
        $filter = array(
            SearchTools::NewFilterComponent(
                'BeginDate', '', 'LowerThanOrEquals', $endDate, 1),
            SearchTools::NewFilterComponent(
                'EndDate', '', 'GreaterThanOrEquals', $beginDate, 1),
            SearchTools::NewFilterComponent(
                'Id', '', 'NotEquals', $this->objID, 1)
        );
        
        $cur1Filter = array(
            SearchTools::NewFilterComponent(
                'FromCurrency', '', 'Equals', $_REQUEST['CurrencyConverter_FromCurrency_ID'], 1),
            SearchTools::NewFilterComponent(
                'ToCurrency', '', 'Equals', $_REQUEST['CurrencyConverter_ToCurrency_ID'], 1)
        );
        $cur1Filter = SearchTools::filterAssembler($cur1Filter);
                
        $cur2Filter = array(
            SearchTools::NewFilterComponent(
                'ToCurrency', '', 'Equals', $_REQUEST['CurrencyConverter_FromCurrency_ID'], 1),
            SearchTools::NewFilterComponent(
                'FromCurrency', '', 'Equals', $_REQUEST['CurrencyConverter_ToCurrency_ID'], 1)
        );
        $cur2Filter = SearchTools::filterAssembler($cur2Filter);
        
        $curFilter = SearchTools::filterAssembler(
                array($cur1Filter, $cur2Filter), FilterComponent::OPERATOR_OR);
        
        $filter[] = $curFilter;
        $filter = SearchTools::filterAssembler($filter);
        $converter = $mapper->load($filter);        
        
        if($converter instanceof CurrencyConverter) {
            Template::errorDialog(_('A exchange rate already exists for selected currencies and period.'), $this->url);
            exit();
        }
    }

    // }}}
    
 /*   protected function onFinish() {
        //exit(0);
    }*/
}

?>