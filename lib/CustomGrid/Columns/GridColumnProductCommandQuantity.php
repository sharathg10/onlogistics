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
class GridColumnProductCommandQuantity extends AbstractGridColumn {
    /**
     * Object Customer
     *
     * @access private
     */
    private $_supplier = false;
    private $_supplierCustomer = false;
    private $_onlyBuyUnitQty = false;

    /**
     * Constructor
     *
     * @access protected
     */
    function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
        if (isset($params['supplier'])) {
            $this->_supplier = $params['supplier'];
        }
        if (isset($params['supplierCustomer'])) {
            $this->_supplierCustomer = $params['supplierCustomer'];
        }
        if (isset($params['onlyBuyUnitQty']) && $params['onlyBuyUnitQty']) {
            $this->_onlyBuyUnitQty = true;
        }
    }

    public function render($object) {
        // On est dans SupplierCatalog: on affiche seulement la qte dont la 
        // saisie doit etre un multiple
        if ($this->_onlyBuyUnitQty) {
            $buyUnitQty = $object->getBuyUnitQuantity($this->_supplier);
            $buyUnitQty = ($buyUnitQty > 0)?$buyUnitQty:1;
        	return ' (' . _('by') . ' ' . $buyUnitQty . ')';
        }
        
        // Les qty en session sont des UE (pas UV), et il faut aficher 
        // 2 champs text: pour le nb d'Uv et pour le nb d'UE
        // et on stocke dans des hidden les infos pour convertir les qtes d'UE 
        // saisies en nb d'UV
        if (Preferences::get('ProductCommandUEQty')) {
            $ueQty = (isset($_SESSION['catalogQties']) && isset($_SESSION['catalogQties'][$object->getId()]))?
                    $_SESSION['catalogQties'][$object->getId()]:1;
            $return = '<input type="text" name="ueQty[]" size="5" value="' 
                . $ueQty . '" onkeyup="getItemNbUV(getItemIndex(this));'
                .'RecalculateItemTotal(getItemIndex(this));RecalculateTotal();" />'
                .'&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="qty[]" '
                .' readonly="readonly" class="ReadOnlyField" size="5" />'
                . $object->getMeasuringUnit()
                . '<input type="hidden" name="sut[]" value="'
                . $object->getSellUnitTypeId() . '" />'
                . '<input type="hidden" name="suq[]" value="'
                . $object->getSellUnitQuantity() . '" />'
                . '<input type="hidden" name="unip[]" value="'
                . $object->getUnitNumberInPackaging() . '" />';
        }
        else {  // On n'affiche qu'une colonne, pour les UV
            $qty = (isset($_SESSION['catalogQties']) && isset($_SESSION['catalogQties'][$object->getId()]))?
                    $_SESSION['catalogQties'][$object->getId()]:1;
            $return = '<input type="text" name="qty[]" size="5" value="' . $qty . '" onkeyup="'
                .'RecalculateItemTotal(getItemIndex(this));RecalculateTotal();" />'
                . $object->getMeasuringUnit();
        }
            
        // Si on est dans ProductCommandSupplier
        if ($this->_supplier !== false) {
            $buyUnitQty = $object->getBuyUnitQuantity($this->_supplier);
            $return .= '<input type="hidden" name="HiddenBuyUnitQty[]" value="'
                . $buyUnitQty . '" />';
            if ($buyUnitQty > 0) {
                $return = $return . ' (' . _('by') . ' ' . $buyUnitQty . ')';
            }
        }
        
        return $return;
    }
}

?>