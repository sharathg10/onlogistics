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

class CommandPotentialActors {

    var $_chainCollection = false;
    var $_departureActors = array();
    var $_arrivalActors = array();
    var $_proceed = false;

    function CommandPotentialActors($ChainCollection)
    {
        $this->_chainCollection = $ChainCollection;
    } 

    function setProductCollection($ChainCollection)
    {
        $this->_chainCollection = $ChainCollection;
        $this->_proceed = false;
    } 

    function ProcessCommonActorIdTuples()
    {
        $this->_departureActors = array();
        $this->_arrivalActors = array();
        $ActorTuples = array();

        if ($this->_chainCollection instanceof Collection) {
            for($j = 0; $j < $this->_chainCollection->GetCount(); $j++) {
                unset($Chain);
                $Chain = $this->_chainCollection->GetItem($j);
                $SiteTransition = $Chain->getSiteTransition();
                $ActorTuples = array_merge($ActorTuples, $this->getActorTuples($SiteTransition));
            } 
        } 
        if (is_array($ActorTuples)) {
            foreach($ActorTuples as $tuple) {
                $anArray = explode(':', $tuple);
                $this->_departureActors[$anArray[0]][] = $anArray[1];
                $this->_arrivalActors[$anArray[1]][] = $anArray[0];
            } 
        } 
        $this->_proceed = true;
    } 

    /**
     * 
     * @access public 
     * @return void 
     */
    function getActorTuples($SiteTransition)
    {
        $ActorTuples = array();
        $depActors = array();
        $arrActors = array();
        $DepartureActor = $SiteTransition->getDepartureActor();
        $ArrivalActor = $SiteTransition->getArrivalActor();
        if ($DepartureActor->isGeneric()) {
            $children = $DepartureActor->getChildren();
            for($i = 0; $i < $children->getCount(); $i++) {
                $child = $children->getItem($i);
                $depActors[] = $child->getId();
                unset($child);
            } // for
            unset($children);
        } else {
            $depActors[] = $DepartureActor->getId();
        } 
        if ($ArrivalActor->isGeneric()) {
            $children = $ArrivalActor->getChildren();
            for($i = 0; $i < $children->getCount(); $i++) {
                $child = $children->getItem($i);
                $arrActors[] = $child->getId();
                unset($child);
            } 
            unset($children);
        } else {
            $arrActors[] = $ArrivalActor->getId();
        } 

        foreach($depActors as $depActor) {
            foreach($arrActors as $arrActor) {
                $ActorTuples[] = $depActor . ':' . $arrActor;
            } 
        } 
        foreach($arrActors as $arrActor) {
            foreach($depActors as $depActor) {
                $ActorTuples[] = $depActor . ':' . $arrActor;
            } 
        } 
        return array_unique($ActorTuples);
    } 

    /**
     * retourne la liste des acteurs de départ potentiels
     */
    function getPotentialDepartureActors()
    {
        if (!$this->_proceed) {
            $this->ProcessCommonActorIdTuples();
        } 
        if (isset($this->_departureActors) && is_array($this->_departureActors)) {
            return array_keys($this->_departureActors);
        } 
        return array();
    } 

    /**
     * retourne la liste des acteurs d'arrivée potentiels
     */
    function getPotentialArrivalActors()
    {
        if (!$this->_proceed) {
            $this->ProcessCommonActorIdTuples();
        } 
        return array_keys($this->_arrivalActors);
    } 

    /**
     * retourne la liste des acteurs d'arrivée possible en fonction de l'acteur de départ
     */
    function getArrivalActorsFromDeparture($key)
    {
        if (!$this->_proceed) {
            $this->ProcessCommonActorIdTuples();
        } 

        $ArrActorsArray = $this->_departureActors[$key];
        return array_values($ArrActorsArray);
    } 

    /**
     * retourne la liste des acteurs de départ possible en fonction de l'acteur d'arrivée
     */
    function getDepartureActorsFromArrival($key)
    {
        if (!$this->_proceed) {
            $this->ProcessCommonActorIdTuples();
        } 
        $DepActorsArray = $this->_arrivalActors[$key];
        return array_values($DepActorsArray);
    } 

    /**
     */
    function hasCommonActors()
    {
        if (!$this->_proceed) {
            $this->ProcessCommonActorIdTuples();
        } 
        return (!empty($this->_arrivalActors) && !empty($this->_departureActors));
    } 
} 

?>