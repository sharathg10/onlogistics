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
 * @version   SVN: $Id: RTWProductManager.php 287 2008-12-10 17:36:46Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once 'AbstractProductManager.php';

/**
 * RTWProductManager.
 * Classe gerant la creation des produits pour le contexte pret a porter.
 */
class ProductManager extends AbstractProductManager
{
    // RTWProductManager::createProducts() {{{

    /**
     * Cree la totalité des produits pour un modèle donné selon ltailles disponibles.
     * 
     * @param object $model une instance de RTWModel
     *
     * @return object la collection de produits.
     * @access public
     * @throw  Exception
     * @static
     */
    public static function createProducts($model)
    {
        $sizeIds = $model->getSizeCollectionIds();
        if (empty($sizeIds)) {
            throw new Exception(_('No products were created because you did not select any available size.'));
        }
        // supprimer les produits pour lesquels on a viré des tailles
        $productCol = Object::loadCollection('Product', new FilterComponent(
            new FilterRule('ProductModel', FilterRule::OPERATOR_EQUALS, $model->getId()),
            new FilterRule('Size', FilterRule::OPERATOR_NOT_IN, $sizeIds)
        ));
        foreach ($productCol as $p) {
            $c = Object::load('Chain', array('Reference' => $p->getBaseReference()));
            if ($c instanceof Chain) {
                $c->delete();
            }
            $p->delete();
        }
        foreach ($sizeIds as $sizeId) {
            $product = Object::load('Product', array(
                'ProductModel' => $model->getId(),
                'Size'  => $sizeId
            ));
            if (!($product instanceof Product)) {
                $product = new Product();
                $product->generateId();
                self::setProductDefaults($product, $model);
                $product->setSize($sizeId);
                // affecte le produit à la chaine
                self::createAffectation($product, 'lc');
                $oldReference = false;
            } else {
                $oldReference = $product->getBaseReference();
            }
            $product->setProductModel($model);
            $product->setName($model->getDescription());
            // construit la ref produit
            $size = Object::load('RTWSize', $sizeId);
            if ($size instanceof RTWSize) {
                $ref = sprintf('%s-%s', $model->getBaseReference(), $size->toString());
            } else {
                $ref = sprintf('%s-T00', $model->getBaseReference());
            }
            $product->setBaseReference($ref);
            // assigne le supplier via ActorProduct
            $apData = array(
                'Supplier_ID'        => $model->getManufacturerId(),
                'Supplier_Reference' => $ref,
                'Supplier_Price'     => 0
            );
            self::createActorProduct($product, $apData);
            $product->save();
        } 
    }

    // }}}
    // RTWProductManager::setProductDefaults() {{{

    /**
     * Retourne un tableau propriete=>valeur des valeurs par defaut a 
     * renseigner pour le produit nouvellement cree.
     * 
     * @return array
     * @access protected
     * @static
     */
    protected static function setProductDefaults($product, $model)
    {
        $defaults = array(
            'SellUnitType'     => 1,
            'SellUnitQuantity' => 1,
            'ProductType'      => $model->getProductTypeId(),
            'TVA'              => $model->getTVAId(),
            'Affected'         => 1,
            'Owner'            => $model->getOwnerId(),
        );
        foreach ($defaults as $k=>$v) {
            $setter = 'set' . $k;
            $product->$setter($v);
        }
    }

    // }}}
}

?>
