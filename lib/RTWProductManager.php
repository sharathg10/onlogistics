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
 * RTWProductManager.
 * Classe gerant la creation des produits pour le contexte pret a porter.
 */
class RTWProductManager extends RTWManager
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
        $productCol = Object::loadCollection('RTWProduct', new FilterComponent(
            new FilterRule('Model', FilterRule::OPERATOR_EQUALS, $model->getId()),
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
            $product = Object::load('RTWProduct', array(
                'Model' => $model->getId(),
                'Size'  => $sizeId
            ));
            if (!($product instanceof Product)) {
                $product = new RTWProduct();
                $product->generateId();
                self::setProductDefaults($product);
                $product->setSize($sizeId);
                // affecte le produit à la chaine
                self::createAffectation($product, 'lc');
                $oldReference = false;
            } else {
                $oldReference = $product->getBaseReference();
            }
            $product->setModel($model);
            $product->setName($model->getDescription());
            // construit la ref produit
            $size = Object::load('RTWSize', $sizeId);
            if ($size instanceof RTWSize) {
                $ref = sprintf('%s-%07d-%s', $model->getStyleNumber(), $product->getId(), $size->toString());
            } else {
                $ref = sprintf('%s-%07d-T00', $model->getStyleNumber(), $product->getId());
            }
            $product->setBaseReference($ref);
            // assigne le supplier via ActorProduct
            $apData = array(
                'Supplier_ID'        => $model->getManufacturerId(),
                'Supplier_Reference' => $ref,
                'Supplier_Price'     => 0
            );
            self::createActorProduct($product, $apData);
            // Creation de la nomenclature
            self::createNomenclature($product, $oldReference);
            $product->save();
        } 
    }

    // }}}
    // RTWProductManager::createNomenclature() {{{

    /**
     * Cree la nomenclature pour le RTWProduct passe en parametre.
     * 
     * @param object $product        une instance de RTWProduct
     *
     * @return void
     * @access protected
     * @throw  Exception
     * @static
     */
    protected static function createNomenclature($product, $oldReference)
    {
        $nomenclature = Object::load('Nomenclature', array('Product'=>$product->getId()));
        if (!($nomenclature instanceof Nomenclature)) {
            $nomenclature = new Nomenclature();
            $nomenclature->setBeginDate(date('Y-m-d 00:00:00'));
            $nextYear  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y") + 1);
            $nomenclature->setEndDate(date('Y-m-d 00:00:00', $nextYear));
            $nomenclature->setVersion('1.0');
            $nomenclature->setProduct($product);
        } else {
            // supprime tous les composants de la nomenclature
            $cpnMapper = Mapper::singleton('Component');
            $cpnMapper->delete($nomenclature->getComponentCollectionIds());
        }
        $model = $product->getModel();
        $nomenclature->save();
        // Le Component de niveau 0
        $component = new Component();
		$component->setNomenclature($nomenclature);
		$component->setProduct($product);
		$component->setQuantity(1);
		$component->setLevel(0);
		$component->save();
		// Les Component de niveau 1 (pas d'autre niveau d'ailleurs)
        $attributes = RTWModel::getMaterialProperties(true);
        foreach ($attributes as $attrName => $label) {
            $getter = 'get' . $attrName;
        	$pdt = $model->$getter();
            $getterNom = 'get' . $attrName . 'Nomenclature';
        	if ($model->$getterNom() && $pdt instanceof Product) {
                $qtyGetter = 'get' . $attrName . 'Quantity';
        	    $compt = new Component();
        		$compt->setNomenclature($nomenclature);
        		$compt->setProduct($pdt);
        		$compt->setQuantity($model->$qtyGetter());
        		$compt->setLevel(1);
        		$compt->setParent($component);
        		$compt->save();
        	}
        }
        // duplique la chaine fabrication et remplace ce qui est nomenclature
        $ref = 'FABRICATION';
        $chain = Object::load('Chain', array('Reference'=>$ref));
        if (!($chain instanceof Chain)) {
            throw new Exception(sprintf(
                _('You must create a chain with reference "%s"'),
                $ref
            ));
        }
        include_once 'Objects/Task.const.php';
        $newRef   = $product->getBaseReference();
        $newDesc  = $chain->getDescription() . ' ' . $newRef;
        if ($oldReference) {
            $newChain = Object::load('Chain', array('Reference' => $oldReference));
        } else {
            $newChain = false;
        }
        if (!($newChain instanceof Chain)) {
            include_once 'DuplicateChain.php';
            $newChain = duplicateChain($chain, $newRef, $newDesc);
        } else {
            $newChain->setReference($newRef);
            $newChain->setDescription($newDesc);
        }
        // au cas ou...
        $newChain->setAutoAssignTo(Chain::AUTOASSIGN_NONE);
        $opeCol = $newChain->getChainOperationCollection();
        foreach ($opeCol as $ope) {
            $taskCol = $ope->getChainTaskCollection();
            foreach ($taskCol as $task) {
                $taskId = $task->getTaskId();
                if ($taskId == TASK_INTERNAL_STOCK_EXIT) {
                    $col = $component->getComponentCollection();
                    $task->setComponentCollection($col);
                    $task->save();
                } else if ($taskId == TASK_INTERNAL_STOCK_ENTRY) {
                    $col = $product->getComponentCollection(array('Level'=>0));
                    $task->setComponentCollection($col);
                    $task->save();
                } else if ($taskId == TASK_SUIVI_MATIERE) {
                    $task->setComponent($component);
                    $task->save();
                }
            }
        }
        $newChain->save();
        self::createAffectation($product, $newChain->getReference());
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
    protected static function setProductDefaults($product)
    {
        $defaults = array(
            'SellUnitType'     => 1,
            'SellUnitQuantity' => 1,
            'ProductType'      => PRODUCT_TYPE_RTWPRODUCT,
            'Affected'         => 1,
            'Owner'            => Auth::singleton()->getActorId()
        );
        foreach ($defaults as $k=>$v) {
            $setter = 'set' . $k;
            $product->$setter($v);
        }
        // TVA 19.6 by default
        $tva = Object::load('TVA', array('Rate' => 19.6));
        if ($tva instanceof TVA) {
            $product->setTVA($tva);
        }
    }

    // }}}
}

?>
