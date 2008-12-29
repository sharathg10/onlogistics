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

class CommandItem extends _CommandItem {
    // Constructeur {{{

    /**
     * CommandItem::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // handingType() {{{
    
    /**
     * CommandItem::HandingType()
     * Retourne la devise de la remise
     * 
     * @return string
     **/
    function handingType() {
        if (ereg("/", $this->getHanding())) {
            $Type = "frac";
        } elseif (ereg("%", $this->getHanding())) {
            $Type = "percent";
        } elseif (ereg("[0-9]", $this->getHanding())) {
            $Type = "currency";
        } else {
            $Type = "N/A";
        }
        return $Type;
    }

    // }}}
    // getDisplayedHanding() {{{
    
    /**
     * Le montant ou taux de la remise a afficher (soit avec % soit €, soit x/y)
     * 
     * @access public
     * @return string
     **/
    function getDisplayedHanding() {
        if ('frac' == $this->HandingType()) {
            return $this->getHanding();
        } else if ('currency' == $this->HandingType()) {
            return I18N::formatNumber($this->getHanding());
        } else if ('percent' == $this->HandingType()) {
            $Handing = substr($this->getHanding(), 0, strlen($this->getHanding())-1);
            return I18N::formatPercent($Handing);
        }
        return '';
    }

    // }}}
    // getTotalHT() {{{

    /**
     * Retourne le total HT du commanditem, cad qté fois prix HT.
     * 
     * @access public
     * @param boolean $asString si true retourne le prix sous forme de chaine
     * @return mixed float ou string 
     **/
    function getTotalHT($asString = false){
        require_once('FormatNumber.php');
        require_once("CalculatePriceHanding.php");
        $res = CalculatePriceHanding($this->HandingType(), $this->getPriceHT(), 
            $this->getQuantity(), $this->getHanding());
        $res = troncature($res);
        if (!$asString) {
            return floatval($res);
        }
        // troncature à 2 décimales
        return I18N::formatNumber($res);
    }

    // }}}
    // getTotalTTC() {{{
    
    /**
     * Retourne le total HT du commanditem, cad total HT fois TVA.
     *
     * @access public
     * @param boolean $asString si true retourne le prix sous forme de chaine
     * @return void 
     **/
    function getTotalTTC($asString = false){
        require_once('FormatNumber.php');
        $res = $this->getTotalHT();
        $tva = $this->getTVA();
        if (!Tools::isEmptyObject($tva)) {
            $res = $res + ($res * ($tva->getRate()/100));
        }
        $res = troncature($res);
        // troncature à 2 décimales
        if (!$asString) {
            return floatval($res);
        }
        return I18N::formatNumber($res);
    }

    // }}}
    // getSurface() {{{
 
    /**
     * Retourne la surface du commanditem
     *
     * @param int $qty optional: if not given the quantity is the ordered qty
     * 
     * @access public
     * @return float 
     */
    function getSurface($qty = false) {
        if ($qty === false) {
            $qty = $this->getQuantity();
        }
        return $qty * $this->getWidth() * $this->getLength();
    }

    // }}}
    // getVolume() {{{

    /**
     * Retourne le volume du commanditem
     *
     * @param int $qty optional: if not given the quantity is the ordered qty
     * 
     * @access public
     * @return float 
     */
    function getVolume($qty = false) {
        if ($qty === false) {
            $qty = $this->getQuantity();
        }
        return $qty * $this->getWidth() * $this->getLength() 
            * $this->getHeight();
    }

    // }}}
    // getRealTvaRate() {{{

    /**
     * Retourne le taux de TVA tenant eventuellement compte de la tva surtaxee
     * (Utilise au moment de la facturation uniquement)
     * @access public
     * @return float
     */
    function getRealTvaRate() {
        $tva = $this->getTVA();
        if (!($tva instanceof TVA)) {
            return 0;
        }
        $sp = $this->getCommand()->getSupplierCustomer();
        $hasTvaSurtax = ($sp instanceof SupplierCustomer && $sp->getHasTvaSurtax());
        
        $tvaSurtaxRate = ($hasTvaSurtax)?Preferences::get('TvaSurtax', 0):0;
        return $tva->getRealTvaRate($tvaSurtaxRate);
    }

    // }}}
}

?>
