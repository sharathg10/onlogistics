<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is a generated file, please do not edit.
 *
 * This file is part of onlogistics application.
 * Copyright (C) 2003-2008 ATEOR
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
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
 * @version   CVS: $Id$
 * @link      http://www.onlogistics.com
 * @link      http://www.onlogistics.org
 * @since     File available since release 0.1.0
 * @filesource
 * $Source: /home/cvs/codegen/codegentemplates.py,v $
 */

/**
 * Component class
 *
 * Classe contenant des méthodes additionnelles
 */
class Component extends _Component {
    // Constructeur {{{

    /**
     * Component::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Component::getQuantity() {{{

    /**
     * Surchargee pour retourner la quantite en prenant en compte ou pas le 
     * pourcentage de gaspillage.
     *
     * @param bool $withPercentWasted prendre en compte le % de gaspillage ou 
     *             non (false par defaut).
     *
     * @access public
     * @return integer
     */
    function getQuantity($withPercentWasted = false) {
        $qty = parent::getQuantity();
        if ($withPercentWasted && $this->getPercentWasted() > 0) {
            return $qty + ($qty * ($this->getPercentWasted() / 100));
        }
        return $qty;
    }

    // }}}
    // Component::getConcreteQuantity() {{{

    /**
     * Retourne la somme des Quantity des ConcreteComponent
     * lies au Component, et ayant pour Head le ConcreteProduct d'id $headId
     *
     * @param integer $headId
     * @access public
     * @return integer
     */
    function getConcreteQuantity($headId) {
        // Si mode de suivi est: TODO

        // Si mode de suivi est SN
        $headCP = Object::load('ConcreteProduct', $headId);
        $CPCollection = $headCP->getConcreteProductCollection(
                array('Component' => $this->getId()));

        if (Tools::isEmptyObject($CPCollection)) {
            return 0;
        }
        else {
            return $CPCollection->getCount();
        }
    }

    // }}}
    // Component::getConcreteProducts() {{{

    /**
     * Retourne les ConcreteProduct lies au Component,
	 * et ayant pour Head le ConcreteProduct d'id $headId
     *
	 * @param integer $headId
     * @access public
     * @return object Collection
     */
    function getConcreteProducts($headId) {
        require_once('Objects/Product.php');
        require_once('Objects/Task.const.php');
        require_once('Objects/ActivatedChainTask.php');
        $tracingMode = Tools::getValueFromMacro($this, '%Product.TracingMode%');
        // Si pas de mode de suivi, ca n'a pas de sens
        $CPCollection = new Collection();
        $headCP = Object::load('ConcreteProduct', $headId);

        // Si Level 0, c'est forcement le CP d'Id $headId!!
        if ($this->getLevel() == 0) {
            $CPCollection->setItem($headCP);
        }

        elseif ($tracingMode == Product::TRACINGMODE_SN) {
            $CPCollection = $headCP->getConcreteProductCollection(
                    array('Component' => $this->getId()));
        }
        elseif ($tracingMode == Product::TRACINGMODE_LOT) {
            // Le Level est  > 0
            $parentComponent = $this->getParent();
            // Lot => pas de la nomencl. pieces, mais de l'assemblage
            $CPCollection1 = $headCP->getConcreteProductCollection(
                    array('Product' => $this->getProductId()),
                    array('SerialNumber' => SORT_ASC));
            $ackMapper = Mapper::singleton('ActivatedChainTask');
            $ackCollection = $ackMapper->loadCollection(
                    array('Component' => $parentComponent->getId(),
                          'State' => STATE_FINISHED/*,
                          'AssembledRealQuantity' > 0*/));
            if (!Tools::isEmptyObject($ackCollection) && !Tools::isEmptyObject($CPCollection1)) {
                $count = $ackCollection->getCount();
                for($i = 0; $i < $count; $i++) {
                	$ack = $ackCollection->getItem($i);
                    $ccpCollection = $ack->getConcreteComponentCollection();
                    // Parcourir cette collection, recupérer les CP de chaque item,
                    $ccpCount = $ccpCollection->getCount();
                    for($j = 0; $j < $ccpCount; $j++) {
                    	$ccp = $ccpCollection->getItem($j);
                        $cp = $ccp->getConcreteProduct();
                        if (!in_array($cp->getId(), $CPCollection1->getItemIds())) {
                            continue;
                        }
                        $CPCollection->setItem($cp);
                    }
                }
            }
        }
        return $CPCollection;
    }

    // }}}
    // Component::getConcreteProductList() {{{

	/**
     * Retourne la string listant les ConcreteProduct lies au Component,
	 * et ayant pour Head le ConcreteProduct d'id $headId
     *
	 * @param integer $headId
     * @access public
     * @return string
     */
    function getConcreteProductList($headId) {
        $cpCollection = $this->getConcreteProducts($headId);
        $ret = '';
		$padding = '';
		$count = $cpCollection->getCount();
		for($i = 0; $i < $count; $i++){
			$cp = $cpCollection->getItem($i);
			$ret .= $padding . $cp->getSerialNumber();
			$padding = ', ';
		}
		return $ret;

    }

    // }}}
    // Component::getTreeItems() {{{

    /**
     * Retourne un tableau representant la structure en arbre
     * Nomenclature modele - Recursion
     *
     * @access public
     * @param boolean $withLink liens vers ComponentAddEdit ssi
     * Nomenclature->isUsed() == false
     * @param boolean $recursive appel recursif ou non
     * @return array of strings
     */
    function getTreeItems($withLink=false, $recursive=true) {
        $Product = $this->getProduct();
        $tracingModeArray = Product::getTracingModeConstArray();
        $info = $Product->getBaseReference() . ' | ' . $Product->getName();
        $info .= ($Product->getTracingMode() == 0)?' ':
                ' (' . $tracingModeArray[$Product->getTracingMode()] . ') ';
        $info .= ($this->getLevel() == 0)?
                '- ' . _('Version') . ' ' . Tools::getValueFromMacro($this, '%Nomenclature.Version%'):
                '- ' . _('Qty') . ' ' . $this->getQuantity();
        $url =  ($withLink)?'ComponentAddEdit.php?&cmpId=' . $this->getId() : 0;
        $return = array($info, $url);
        if (!$recursive) {
            return $return;
        }

        $coll = $this->getComponentCollection(
                        array(), array('Product.Basereference' => SORT_ASC));
        if(!Tools::isEmptyObject($coll)) {
            $count = $coll->getCount();
            for($i = 0; $i < $count; $i++) {
                $cmp = $coll->getItem($i);
                $return[] = $cmp->getTreeItems($withLink);
            }
        }
        return $return;
    }

    // }}}
    // Component::getPieceTreeItems() {{{

    /**
     * Retourne un tableau representant la structure en arbre
     * Nomenclature pieces - Recursion
     *
     * @access public
     * @param boolean $headId id du CP racine de la nomenclature pieces
     * @param boolean $recursive appel recursif ou non
     * @return array of strings
     */
    function getPieceTreeItems($headId, $recursive=true) {
        $Product = $this->getProduct();
        $headCP = Object::load('ConcreteProduct', $headId);
        $tracingModeArray = Product::getTracingModeConstArray();
        $info = $Product->getBaseReference() . ' | ' . $Product->getName();
        $info .= ($Product->getTracingMode() == 0)?' ':
                ' (' . $tracingModeArray[$Product->getTracingMode()] . ') ';
        $info .= ($this->getLevel() == 0)?
                '- ' . _('Version') . ' ' . Tools::getValueFromMacro($this, '%Nomenclature.Version%')
                . ' # ' . $headCP->getSerialNumber():
                '- ' . _('Qty') . ' ' . $this->getQuantity() . ' # '
                . $this->getConcreteProductList($headId);
        // ComponentRedirect pour selectionner un Parent si besoin
        $url =  ($this->getLevel() != 0)?'ComponentRedirect.php?cmpId='
                . $this->getId() . '&cpId=' . $_REQUEST['cpId']:0;
        $return = array($info, $url);
        if (!$recursive) {
            return $return;
        }

        $coll = $this->getComponentCollection(
                        array(), array('Product.Basereference' => SORT_ASC));
        if(!Tools::isEmptyObject($coll)) {
            $count = $coll->getCount();
            for($i = 0; $i < $count; $i++) {
                $cmp = $coll->getItem($i);
                $return[] = $cmp->getPieceTreeItems($headId);
            }
        }
        return $return;
    }

    // }}}
    // Component::getQuantityInHead() {{{

    /**
     * Retourne la quantité de composant dans le head de la comenclature.
     *
     * @access public
     * @return integer
     */
    function getQuantityInHead() {
        $parent = $this->getParent();
        $qty = $this->getQuantity(true);
        while($parent) {
            $qty *= $parent->getQuantity(true);
            $parent = $parent->getParent();
        }
        return $qty;
    }

    // }}}
    // Component::getSerialNumber() {{{

    /**
     * Utilise uniquement dans AssemblyDetail.php, pour faciliter l'affichage
     * dans un grid
     *
     * @access public
     * @return string or false
     */
    function getSerialNumber() {
    	if (property_exists($this, 'SerialNumber')) {
    	    return $this->SerialNumber;
    	}
    	else return false;
    }

    // }}}
    // Component::toString() {{{

    /**
     * Component::toString()
     *
     * @access public
     * @return void
     */
    function toString(){
        return sprintf("%s: %s", $this->getProduct()->getBaseReference(),
                $this->getProduct()->getName());
    }

    // }}}
    // Component::getToStringAttribute() {{{

    /**
     * Component::getToStringAttribute()
     * Retourne le nom des attributs représentant l'objet, pointés par toString()
     *
     * @static
     * @return array of strings
     * @access public
     */
    public function getToStringAttribute() {
        return array('Product');
    }

    // }}}
    // Component::buildChainOperation() {{{

    /**
     * Construit les opération d'assemblage contenant les taches de sortie 
     * interne, assemblage et entrée interne de manière récursive à partir de ce 
     * composant.
     * A appeler dans une transaction.
     *
     * @param Chain $chain la chaine dans laquelle ajouter les operation
     * @access public
     * @return int ordre de la ChainOperation suivante
     */
    public function buildChainOperation($chain, $order=1) {
        require_once('Objects/Operation.const.php');
        require_once('Objects/Task.const.php');
        
        $childComponentsCol = Object::loadCollection('Component', array(
            'Nomenclature' => $this->getNomenclatureId(),
            'Parent'       => $this->getId()
        ));
        $count = $childComponentsCol->getCount();
        if($count == 0) {
            // pas de composant fils, le composant sera géré par son père
            return $order;
        }
        
        // avant de sortir les composants fils, ont s'occupe des petits fils
        for($i=0 ; $i<$count ; $i++) {
            $child = $childComponentsCol->getItem($i);
            $order = $child->buildChainOperation($chain, $order);
        }

        /** 
         * création d'une ChainOperation liée à l'operation d'assemblage et 
         * ajout de cette ChainOperation à la Chain
         */
        $coCol = $chain->getChainOperationCollection();
        $chainOperation = new ChainOperation();
        $chainOperation->generateId();
        $chainOperation->setOperation(
            Object::load('Operation', OPERATION_ASSE)
        );
        //$chainOperation->setChain($chain);
        $coCol->setItem($chainOperation);
        $chainOperation->setOrder($order);

        /**
         * sortie interne des composants fils:
         * création d'une ChainTask liée à la tache sortie interne 
         * pour les composants à assembler et ajout de cette ChainTask à la 
         * ChainOperation
         */
        $ctCol = $chainOperation->getChainTaskCollection();
        $chainTaskExit = new ChainTask();
        $chainTaskExit->generateId();
        $chainTaskExit->setOrder(1);
        $chainTaskExit->setTask(
            Object::load('Task', TASK_INTERNAL_STOCK_EXIT)
        );
        //$chainTaskExit->setOperation($chainOperation);
        $ctCol->setItem($chainTaskExit);
        $chainTaskExit->setComponentCollection($childComponentsCol);
        
        /**
         * assemblage du composant:
         * création d'une ChainTask assemblage pour le composant et ajout de 
         * cette ChainTask à la ChainOperation
         */
        $chainTaskAssembly = new ChainTask();
        $chainTaskAssembly->generateId();
        $chainTaskAssembly->setOrder(2);
        $chainTaskAssembly->setTask(
            Object::load('Task', TASK_ASSEMBLY)
        );
        $ctCol->setItem($chainTaskAssembly);
        //$chainTaskAssembly->setOperation($chainOperation);
        $chainTaskAssembly->setComponent($this);
        
        /**
         * entrée interne du composant:
         * création d'une ChainTask liée à la tache entré interne pour le
         * composant assemblé et ajout de cette ChainTask à la ChainOperation
         */
        $chainTaskEntry = new ChainTask();
        $chainTaskEntry->generateId();
        $chainTaskEntry->setOrder(3);
        $chainTaskEntry->setTask(
            Object::load('Task', TASK_INTERNAL_STOCK_ENTRY)
        );
        $ctCol->setItem($chainTaskEntry);
        //$chainTaskEntry->setOperation($chainOperation);
        $collection = new Collection('Component');
        $collection->setItem($this);
        $chainTaskEntry->setComponentCollection($collection);
        
        return $order + 1;
    }

    // }}}
    // Component::duplicate() {{{

    /**
     * duplicate 
     *
     * copie un component ainsi que ces component fils (recursion)
     * 
     * @param mixed $nomID id de la nouvelle nomenclature 
     * @param mixed $retURL url de retour en cas d'erreur
     * @access public
     * @return Component le nouveau component
     */
    public function duplicate($nomID, $retURL) {
        $new = Tools::duplicateObject($this);
        $new->setNomenclature($nomID);

        //Component.ComponentGroup
        $compGroup = $this->getComponentGroup();
        if($compGroup instanceof ComponentGroup) {
            $newCompGroup = Tools::duplicateObject($compGroup);
            $newCompGroup->setNomenclature($nomID);
            saveInstance($newCompGroup, $retURL);
            $new->setComponentGroup($newCompGroup);
        }
        //Component.Component()
        $compCol = $this->getComponentCollection();
        foreach($compCol as $component) {
            $newComp = $component->duplicate($nomID, $retURL);
            $newComp->setParent($new);
            saveInstance($newComp, $retURL);
        }
        saveInstance($new, $retURL);
        return $new;
    }

    // }}}

}

?>