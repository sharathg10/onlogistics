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

require_once 'RTWManager.php';

/**
 * RTWMaterialManager.
 * Classe gerant la creation des mat. 1eres pour le contexte pret a porter.
 */
class RTWMaterialManager extends RTWManager
{
    // RTWMaterialManager::createMaterials() {{{

    /**
     * Cree les RTWMaterial necessaires a partir des donnees passees en 
     * parametre.
     * 
     * @param object $material instance de RTWMaterial
     * @param array  $values   tableau des donnees post
     *
     * @return void
     * @access public
     * @throw  Exception
     * @static
     */
    public static function createMaterials(&$material, $values)
    {
        $prefix = $material->getMaterialType() == RTWMaterial::TYPE_RAW_MATERIAL ? "2" : "3";
        $ref = sprintf($prefix . '%07d', $material->getId());
        $material->setBaseReference($ref);
        self::setProductDefaults($material);
        self::createActorProduct($material, $values);
        self::createAffectation($material, 'af');
        $material->save();
    }

    // }}}
    // RTWManager::setProductDefaults() {{{

    /**
     * Renseigne les valeurs par defaut du produit nouvellement cree.
     * 
     * @param object $product une instance de RTWProduct
     *
     * @return void
     * @access public
     * @throw  Exception
     * @static
     */
    protected static function setProductDefaults($product)
    {
        // XXX je suis vraiment pas sur de tout ca...
        $defaults = array(
            'SellUnitType'     => SELLUNITTYPE_UB,
            'BuyUnitType'      => SELLUNITTYPE_UB,
            'SellUnitQuantity' => 1,
            'ProductType'      => PRODUCT_TYPE_RTWMATERIAL,
            'Affected'         => 1
        );
        if ($product->getMaterialType() == RTWMaterial::TYPE_RAW_MATERIAL) {
            $defaults['SellUnitType'] = SELLUNITTYPE_MT;
            $defaults['BuyUnitType']  = SELLUNITTYPE_MT;
        }
        foreach ($defaults as $k=>$v) {
            $setter = 'set' . $k;
            if (method_exists($product, $setter)) {
                $product->$setter($v);
            }
        }
    }

    // }}}
}

?>
