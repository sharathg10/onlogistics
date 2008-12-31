<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IMPORTANT: This is a generated file, please do not edit.
 *
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

/**
 * Box class
 *
 * Class containing addon methods.
 */
class Box extends _Box {
    // Constructeur {{{

    /**
     * Box::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // getExecutionDate() {{{

    /**
     * Retourne la date de début de l'ACK ou de l'ACH s'il s'agit d'une box de
     * niveau 1.
     *
     * @access public
     * @return string
     */
    function getExecutionDate($format='localedate_short'){
        if ($this->getLevel() == 1) {
            $ach = $this->getActivatedChain();
            return $ach instanceof ActivatedChain?
                $ach->getBeginDate($format):'N/A';
        }
        $ackCol = $this->getActivatedChainTaskCollection();
        if (!Tools::isEmptyObject($ackCol)) {
            $ack = $ackCol->getItem(0);
            return $ack->getBegin($format);
        }
        return _('N/A');
    }

    // }}}
    // getSellUnitType() {{{

    /**
     * Retourne l'unité de regroupement du Box.
     *
     * @access public
     * @return object SellUnitType
     */
    function getSellUnitType(){
        // TODO
        return false;
    }

    // }}}
    // getProductTypeCollection() {{{

    /**
     * Retourne la collection de types de produits du Box.
     *
     * @access public
     * @return object SellUnitType
     */
    function getProductTypeCollection(){
        $col = new Collection('', false);
        $boxcol = $this->getBoxCollection();
        $boxcount = $boxcol->getCount();
        // box contenant
        if ($boxcount > 0) {
            for($i = 0; $i < $boxcount; $i++){
                $box = $boxcol->getItem($i);
                $col = $col->merge($box->getProductTypeCollection());
            } // for
        }
        // box de niveau 1
        $cmi = $this->getCommandItem();
        if ($cmi instanceof CommandItem) {
            $col->setItem($cmi->getProductType());
        }
        return $col;
    }

    // }}}
    // getChildrenBoxes() {{{

    /**
     * Retourne toutes les box inférieures dans la hiérarchie, si un level est
     * précisé seuls les box de ce level seront retournées.
     *
     * @access public
     * @param int $level
     * @return object Collection
     **/
    function getChildrenBoxes($level = false){
        $return = new Collection();
        $filter = array();
        if ($level > 0) {
            $filter = array('Level'=>$level);
        }
        $boxes = $this->getBoxCollection($filter);
        $count = $boxes->getCount();
        for($i = 0; $i < $count; $i++){
            $box = $boxes->getItem($i);
            $return->setItem($box);
            $return = $return->merge($box->getChildrenBoxes($level));
        }
        return $return;
    }

    // }}}
    // createPackingList() {{{

    /**
     * Cree la PackingList associee si elle n'existe pas deja
     * @access public
     * @return object PackingList
     **/
    function createPackingList() {
        $PackingList = $this->getPackingList();
        if (!Tools::isEmptyObject($PackingList)) {
            return $PackingList;
        }

        $PackingList = Object::load('PackingList');
        $PackingList->generateId();
        $PackingList->setDocumentNo($PackingList->getId());
        $PackingList->setEditionDate(date('Y-m-d H:i:s'));
        $DocumentModel = $PackingList->FindDocumentModel();
        if (!(false == $DocumentModel)) {
            $PackingList->setDocumentModel($DocumentModel);
        }
        $PackingList->setBox($this);
        $SupplierCustomerMapper = Mapper::singleton('SupplierCustomer');
        $SupplierCustomer = $SupplierCustomerMapper->load(
                    array('Supplier' => $this->getExpeditorId(),
                          'customer' => $this->getDestinatorId()));
        if (Tools::isEmptyObject($SupplierCustomer)) {
            $SupplierCustomer = Object::load('SupplierCustomer');
            $SupplierCustomer->setSupplier($this->getExpeditor());
            $SupplierCustomer->setCustomer($this->getDestinator());
            $SupplierCustomer->save();
        }
        $PackingList->setSupplierCustomer($SupplierCustomer);
        $PackingList->save();
        $this->setPackingList($PackingList);
        $this->save();
        return $PackingList;
    }

    // }}}
    // getCommandCollection() {{{

    /**
     * Retourne la collection des Command liees, via le path:
     * Box > ActivatedChain > CommandItem > Command
     * Si la Box le permet
     * @access public
     * @return Object Collection
     */
    function getCommandCollection() {
        $CommandCollection = new Collection();  // La collection retournee
        // Ne marche pas a cause de la recursivite...
        $CommandCollection->acceptDuplicate = false;

        // Si une Box est creee lors d'une Command
        $CommandId = Tools::getValueFromMacro($this, '%CommandItem.Command.Id%');
        if (!in_array($CommandId, array('N/A', '0'), true)) {
            $Command = Object::load('Command', $CommandId);
            $CommandCollection->setItem($Command);
            return $CommandCollection;
        }
        // Sinon, on passe via les ACK
        $ACKCollection = $this->getActivatedChainTaskCollection(
                array(), array(), array('ActivatedOperation'));
        $ACKCount = $ACKCollection->getCount();
        for($j = 0; $j < $ACKCount; $j++) {
            $ACK = $ACKCollection->getItem($j);
            $CommandId = Tools::getValueFromMacro(
                    $ACK,
                    '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Id%');

            if (in_array($CommandId, array('N/A', '0'), true)) {
                continue;
            }
            $Command = Object::load('Command', $CommandId);
            $CommandCollection->setItem($Command);
        }

        // On descend dans les Box contenues
        $BoxCollection = $this->getBoxCollection();
        $count = $BoxCollection->getCount();
        for($i = 0; $i < $count; $i++) {
            $Box = $BoxCollection->getItem($i);
            $CmdCollection = $Box->getCommandCollection();
            $CommandCollection = $CommandCollection->merge($CmdCollection);
            unset($Box, $Command);
        }
        // Ajoute car acceptDuplicate=false est inoperant ici...
        $Ids = array_unique($CommandCollection->getItemIds());
        $CommandMapper = Mapper::singleton('Command');
        $CommandCollection = $CommandMapper->loadCollection(array('Id' => $Ids));
        return $CommandCollection;
    }

    // }}}
    // getCommand() {{{

    /**
     * Retourne, si la Box le permet la Command liee, si elle n'est liee qu'a une seule Command
     * @access public
     * @return void
     */
    function getCommand(){
        $CommandCollection = $this->getCommandCollection();

        if ($CommandCollection->getCount() == 1) {
            $Command = $CommandCollection->getItem(0);
            return $Command;
        }
        return false;
    }

    // }}}
    // getDataForDocument() {{{

    /**
     * Retourne les donnees pour les etiquettes ou la packinglist.
     *
     * @access public
     * @param int $level
     * @return object Collection
     */
    function getDataForDocument()
    {
        if ($this->getParentBoxId()) {
            return array();
        }
        $return = array(
            'reference'      => $this->getReference(),
            'expeditor'      => $this->getExpeditor(),
            'expeditorSite'  => $this->getExpeditorSite(),
            'destinator'     => $this->getDestinator(),
            'destinatorSite' => $this->getDestinatorSite(),
        );
        $boxes = $this->getBoxCollection();
        $children = array();
        foreach ($boxes as $box) {
            $cmi   = $box->getCommandItem();
            if (!$cmi instanceof CommandItem) {
                continue;
            }
            $cmiId = $cmi->getId();
            if (isset($children[$cmiId])) {
                $children[$cmiId]['quantity'] += 1;
                continue;
            }
            $children[$cmiId] = array(
                'cmi'       => $cmi,
                'quantity'  => 1,
                'reference' => $box->getReference(),
            );
        }
        $return['children'] = $children;
        return $return;
    }

    // }}}
}

?>
