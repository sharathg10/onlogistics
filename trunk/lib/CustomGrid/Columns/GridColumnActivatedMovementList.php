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

class GridColumnActivatedMovementList extends AbstractGridColumn {
    /**
     * Constructor
     */
    function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
        if (isset($params['Level'])) {
            $this->_level = $params['Level'];
        }
    }
    /*
     * @access private
     */
    private $_level = 'Location';

    public function render($object) {
        $storeNameArray = array();
        switch($this->_level){
            case 'Site':
                $macro = '%Location.Store.StorageSite.Name%';
                break;
            case 'Store':
                $macro = '%Location.Store.Name%';
                break;
            default: // Location
                $macro = '%Location.Name%';
        } // switch
        $ProductId = $object->getProductId();
        
        require_once('ExecutionTools.php');
        $Filter = getLpqFilter($ProductId);
        $LPQMapper = Mapper::singleton('LocationProductQuantities');
        $LPQCollection = $LPQMapper->loadCollection($Filter);

        if (!Tools::isEmptyObject($LPQCollection)) {
            $count = $LPQCollection->getCount();
            for($j = 0; $j < $count; $j++) {
                $item = $LPQCollection->getItem($j);
/*                if (0 == $item->getRealQuantity()) {
                    unset($item);
                    continue; // si qte nulle, on n'affiche pas la location
                } */
                $storeNameArray[] = Tools::getValueFromMacro($item, $macro);
                unset($item);
            }
        }
        /*  Suppression des doublons et affichage ds une string  */
        return implode(", ", array_unique($storeNameArray));
    }
}

?>