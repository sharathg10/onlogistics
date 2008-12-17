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

class GridColumnCommandItemList extends SubGridColumn {
    /**
     * Permet d'afficher le detail des Command
     * 
     * @access private 
     */
    /**
     * Constructor
     * 
     * @access protected 
     */
    public function __construct($title = array(), $params = array()) {
        if (isset($params['PackagingUnitQty'])) {
            $this->_packagingUnitQty = $params['PackagingUnitQty'];
        }
        parent::__construct($title, $params);
    } 

    protected $gridInstance = false;
    // True s'il faut afficher la colonne en plus pour les qtes d'UV
    private $_packagingUnitQty= false;

    public function render($object) {
        $subGrid = new SubGrid();
        $subGrid->NewColumn('ProductReference', '', array('cmdType' => $object->getType()));
        // Qte UE si preference activee
        if ($this->_packagingUnitQty) {
            $subGrid->NewColumn('FieldMapper', '', 
                array('Macro' => '%PackagingUnitQuantity|default@N/A%'));
            // Pour le lazy-loading
            $fields = array('Command', 'PackagingUnitQuantity', 'Quantity', 'Product', 'WishedDate');
        } else {
            $fields = array('Command', 'Quantity', 'Product', 'WishedDate');
        }
        // Qte UV
        $subGrid->NewColumn('FieldMapper', '', 
            array('Macro' => '%Quantity%%Product.MeasuringUnit%'));
        if ($object->getCadenced()) {
            $subGrid->NewColumn('FieldMapper', 'Wished date', 
                array('Macro' => '%WishedDate|formatdate@DATE_SHORT%'));
        } else {
            $subGrid->NewColumn('FieldMapper', 'wished date', 
                array('Macro' => '%Command.WishedStartDate|formatdate@DATE_SHORT%'));
        }
        $mapper = Mapper::singleton('ProductCommandItem');
        
        $col = $mapper->loadCollection(
            array('Command' => $object->GetId()), 
            array('Product.BaseReference'=>SORT_ASC), 
            $fields
        );
        return $subGrid->render($col);
    } 
} 

?>
