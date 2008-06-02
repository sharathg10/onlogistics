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

require_once 'Objects/ProductType.inc.php';
require_once 'Objects/SellUnitType.const.php';

/**
 * RTWManager.
 * Classe gerant la creation des produits pour le contexte pret a porter.
 */
class RTWManager
{
    // RTWManager::createAffectation() {{{

    /**
     * Affecte le produit $product à la chaine parametree.
     * 
     * @param object $product une instance de RTWProduct
     *
     * @return void
     * @access public
     * @throw  Exception
     * @static
     */
    protected static function createAffectation($product, $chainRef)
    {
        $chain = Object::load('Chain', array('Reference' => $chainRef));
        if (!($chain instanceof Chain)) {
            throw new Exception(sprintf(
                _('You must create a chain with reference "%s"'),
                $chainRef
            ));
        }
        $pcl = new ProductChainLink();
        $pcl->setChain($chain);
        $pcl->setProduct($product);
        $pcl->save();
    }

    // }}}
    // RTWManager::createActorProduct() {{{

    /**
     * Cree l'actorProduct correspondant aux données passées.
     * 
     * @param object $product instance de Product
     * @param array  $values  tableau des donnees post
     *
     * @return void
     * @access protected
     * @throw  Exception
     * @static
     */
    protected static function createActorProduct(&$product, $values)
    {
        $supplierId = isset($values['Supplier_ID']) ? 
            $values['Supplier_ID'] : false;
        if (false == $supplierId) {
            return;
        }
        $ref   = isset($values['Supplier_Reference']) ? 
            $values['Supplier_Reference'] : '';
        $price = isset($values['Supplier_Price']) ? 
            $values['Supplier_Price'] : 0;
        $ap = new ActorProduct();
        $ap->setActor($supplierId);
        $ap->setProduct($product);
        $ap->setAssociatedProductReference($ref);
        $ap->setBuyUnitType(1);
        $ap->setBuyUnitQuantity(1);
        $ap->setPriority(1);
        $ap->save();
        // prix
        $pbc = new PriceByCurrency();
        $pbc->setCurrency(1);
        $pbc->setPrice($price);
        $pbc->setActorProduct($ap);
        $pbc->save();
        $product->setActorProduct($ap);
    }

    // }}}
}

?>
