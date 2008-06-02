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

function getInvoiceFilter($form, $actorId)
{
    $FilterCmptArray = array();

    //si aucune devise sélectionné, la devise est l'euro
    if (SearchTools::RequestOrSessionExist('Currency') == false
            || SearchTools::RequestOrSessionExist('Currency') == '##') {
        $curMapper = Mapper::singleton('Currency');
        $cur = $curMapper->load(array('ShortName'=>'EUR'));
        $_REQUEST['Currency'] = $cur->getId();
    }
    
    // Filtre par defaut factures client
    //$FilterCmptArray[] = SearchTools::NewFilterComponent('Supplier', 'SupplierCustomer.Supplier.Id', 
    //                    'Equals', $actorId, 1);
    
    $FilterComponentArray[] = SearchTools::NewFilterComponent('ToPay', '', 'GreaterThan', 0);

    // Filtre lie aux criteres selectionnes
    $FilterCmptArray[] = SearchTools::NewFilterComponent('Currency', 'Currency', 'Equals');
        
    $dateFilterCmptArray = $form->getDate2DateFilterComponent(1,
            array('Name' => 'StartDate', 'Path' => 'PaymentDate'),
            array('Name' => 'EndDate', 'Path' => 'PaymentDate'));
    $FilterCmptArray = array_merge($FilterCmptArray, $dateFilterCmptArray);
    
    $filter = SearchTools::FilterAssembler($FilterCmptArray);
    
    return $filter;
}

/**
 * Construit le filtre nécessaire à la récupération 
 * des Flow en fonction du formulaire de recherche
 * 
 * @param object SearchForm $form le formulaire de recherche
 * @access public
 * @return array
 */
function getFlowFilter($form)
{
    $FilterCmptArray = array();
    
    // Filtre lie aux criteres selectionnes
    $FilterCmptArray[] = SearchTools::NewFilterComponent('Currency', 'Currency', 'Equals');
        
    $dateFilterCmptArray = $form->getDate2DateFilterComponent(1,
            array('Name' => 'StartDate', 'Path' => 'PaymentDate'),
            array('Name' => 'EndDate', 'Path' => 'PaymentDate'));
    $FilterCmptArray = array_merge($FilterCmptArray, $dateFilterCmptArray);

    $filter = SearchTools::FilterAssembler($FilterCmptArray);
    
    return $filter;
}
?>