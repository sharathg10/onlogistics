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

class GridColumnActivatedMovementReference extends AbstractGridColumn {
    /**
     * Constructor
     * 
     * @access protected 
     */
    function GridColumnActivatedMovementReference($title = '', $params = array())
    {
        parent::__construct($title, $params);
    } 

    function Render($object)
    {
        require_once('Objects/MovementType.const.php');
        $EntrieExit = Tools::getValueFromMacro($object, '%Type.EntrieExit%');
        $Product = $object->getProduct();
        $ProductId = $Product->getId();

        $return = '<a href="javascript:void(0);" title="' . 
            $Product->getName() . '">';
        $return .= $Product->getBaseReference()
         . '</a><input type="hidden" name="Product_Id[]" value="' . $ProductId . '">';
        if ($EntrieExit == MovementType::TYPE_ENTRY) {
            return $return;
        } else { 
            // si sortie, gestion des produits de substitution actives, et 
            // s'ils sont en stock ds un site du bon Actor
//            $Product = Object::load('Product', $ProductId);
            if ($Product instanceof Product && $Product->getRealQuantity() == 0) { 
                // si qte en stock = 0, on doit regarder les pdts de subst
                $PdtSubstitutionCollection = $Product->getProductSubstitutionCollection();
                
                if (Tools::isEmptyObject($PdtSubstitutionCollection)) {
                    return $return; // si pas de pdt de substitution
                }
                else {
                    $alert = "<a href=\"javascript:void(0);\" title=\"" 
                     . _('This product is replaceable.') . ".\""
                     . " style=\"text-decoration:none\"><font color=\"#FF0000\"><b>*</b></font></a>";
                    $count = $PdtSubstitutionCollection->getCount();
                    for($i = 0; $i < $count; $i++) {
                        $item = $PdtSubstitutionCollection->getItem($i);
                        $SubstitutProductId = $item->getSubstitutionForProduct($ProductId);

                        if ((int)$SubstitutProductId > 0) { 
                            // reste a voir si active, et en stock
                            $SubstitutProduct = Object::load('Product', $SubstitutProductId);
                            if (1 == $SubstitutProduct->getActivated()) { 
                                // si active, reste a voir si en stock
                                //  Selon le profile du UserAccount connecte, 
                                // tous les sites ne conviennent pas 
                                $Auth = Auth::Singleton();
                                $SiteOwner = (in_array($Auth->getProfile(),
                                        array(UserAccount::PROFILE_OPERATOR, UserAccount::PROFILE_GESTIONNAIRE_STOCK)))?$Auth->getActorId():0;
                                if ($SubstitutProduct->getRealQuantity(0, $SiteOwner) > 0) {
                                    return ($return . $alert);
                                } 
                            } 
                        } 
                        unset($item);
                    } // for
                    return $return; // s'il y a des pdt de substitution, mais pas en stock
                }
            } else { // si qte en stock > 0
                return $return;
            } 
        } 
    } 
} 

?>
