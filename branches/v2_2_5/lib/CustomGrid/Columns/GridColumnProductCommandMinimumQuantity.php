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

require_once('Objects/ProductQuantityByCategory.php');

/**
 * Affiche la quantité minimum de commande pour le couple produit/client.
 *
 * @access public
 */
class GridColumnProductCommandMinimumQuantity extends AbstractGridColumn {
    /**
     * Object Customer
     *
     * @access protected
     */
    var $customer = false;

    /**
     * Constructor
     *
     * @access protected
     */
    function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
        if (isset($params['customer'])) {
            $this->customer = $params['customer'];
        }
    }

    public function render($object) {
        $return = '<div class="grid_formatnumber">';
        if ($this->customer instanceof Actor) {
            $pqcMapper = Mapper::singleton('ProductQuantityByCategory');
            $pqc = $pqcMapper->load(
                array(
                    'Product'  => $object->getId(),
                    'Category' => $this->customer->getCategoryId()
                )
            );
            if ($pqc instanceof ProductQuantityByCategory) {
                $qty = $pqc->getMinimumQuantity();
                if ($pqc->getMinimumQuantityType() == ProductQuantityByCategory::QTY_TYPE_MULTIPLE) {
                    return $return . '*' . (int)$qty . '</div>';
                }
                return $return
                    . I18N::formatNumber($qty, 3, true)
                    . '</div>';
            }
        }
        return $return . 1 . '</div>';
    }
}

?>
