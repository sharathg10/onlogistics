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

function IsCommandCompatibleWithWorkOrder($Object, $WorkOrder) {
    $TotalVolume = 0;
    $TotalLM = 0;
    $TotalWeight = 0;
    $TotalDistance = 0;
    $TotalDuration = 0;
    if ($Object instanceof Collection) {
        // on récupère les infos de commande, il peut y avoir plusieurs 
        // chaines differentes
        $processedChains = array();
        for($i = 0; $i < $Object->GetCount(); $i++) {
            unset($Instance);
            $ActivatedChainOperation = $Object->GetItem($i);

            if ($ActivatedChainOperation->getMassified()) {
                
                $ActivatedChainOperationMapper = 
                    &Mapper::singleton('ActivatedChainOperation');
                $MassifiedOperations = 
                    $ActivatedChainOperationMapper->loadCollection(
                        array('Ghost' => $ActivatedChainOperation->getId()));
                for($j = 0; $j < $MassifiedOperations->getCount(); $j++) {
                    unset($curOperation);
                    $curOperation = $MassifiedOperations->getItem($j);
                    if (false == in_array($curOperation->getActivatedChainId(),
                        $processedChains)) {
                        $TotalVolume += Tools::getValueFromMacro(
                            $curOperation, 
                            "%ActivatedChain.CommandItem()[0].Command.TotalVolume%");
                        $TotalLM += Tools::getValueFromMacro(
                            $curOperation, 
                            "%ActivatedChain.CommandItem()[0].Command.TotalLM%");
                        $TotalWeight += Tools::getValueFromMacro(
                            $curOperation, 
                            "%ActivatedChain.CommandItem()[0].Command.TotalWeight%");
                        $TotalDistance += Tools::getValueFromMacro(
                            $curOperation, 
                            "%ActivatedChain.TotalDistance%");
                        $TotalDuration += Tools::getValueFromMacro(
                            $curOperation, 
                            "%ActivatedChain.TotalDuration%");
                        $processedChains[] = $ActivatedChain->getId();
                    } 
                    $processedChains[] = $curOperation->getActivatedChainId();
                } // for
            } else {
                $CurrentChainId = Tools::getValueFromMacro(
                    $ActivatedChainOperation, 
                    "%ActivatedChain.Id%");
                if (false == in_array($CurrentChainId, $processedChains)) {
                    $TotalVolume += Tools::getValueFromMacro(
                        $ActivatedChainOperation, 
                        "%ActivatedChain.CommandItem()[0].Command.TotalVolume%");
                    $TotalLM += Tools::getValueFromMacro(
                        $ActivatedChainOperation, 
                        "%ActivatedChain.CommandItem()[0].Command.TotalLM%");
                    $TotalWeight += Tools::getValueFromMacro(
                        $ActivatedChainOperation, 
                        "%ActivatedChain.CommandItem()[0].Command.TotalWeight%");
                    $TotalDistance += Tools::getValueFromMacro(
                        $ActivatedChainOperation, 
                        "%ActivatedChain.TotalDistance%");
                    $TotalDuration += Tools::getValueFromMacro(
                        $ActivatedChainOperation, 
                        "%ActivatedChain.TotalDuration%");
                } 
                $processedChains[] = $CurrentChainId;
            } 
        } 
    } elseif ($Object instanceof ActivatedChain) {
        $TotalVolume = Tools::getValueFromMacro($Object, 
            "%CommandItem()[0].Command.TotalVolume%");
        $TotalLM = Tools::getValueFromMacro($Object, 
            "%CommandItem()[0].Command.TotalLM%");
        $TotalWeight = Tools::getValueFromMacro($Object, 
            "%CommandItem()[0].Command.TotalWeight%");
        $TotalDistance = Tools::getValueFromMacro($Object, "%TotalDistance%");
        $TotalDuration = Tools::getValueFromMacro($Object, "%TotalDuration%");
    } elseif ($Object instanceof Command) {
        $TotalVolume = Tools::getValueFromMacro($Object, "%TotalVolume%");
        $TotalLM = Tools::getValueFromMacro($Object, "%TotalLM%");
        $TotalWeight = Tools::getValueFromMacro($Object, "%TotalWeight%");
        $TotalDistance = Tools::getValueFromMacro($Object, 
            "%CommandItem()[0].ActivatedChain.TotalDistance%");
        $TotalDuration = Tools::getValueFromMacro($Object, 
            "%CommandItem()[0].ActivatedChain.TotalDuration%");
    } else {
        return new Exception(_("Wrong parameter provided, parameter should be an order, an activated chain or a collection of operations."));
    } 
    // Récupération des infos de l'OT : récupération des max autorisés
    if ($WorkOrder instanceof WorkOrder) {
        $WorkOrderMaxVolume = $WorkOrder->GetMaxVolume();
        $WorkOrderMaxLM = $WorkOrder->GetMaxLM();
        $WorkOrderMaxWeight = $WorkOrder->GetMaxWeigth();
        $WorkOrderMaxDistance = $WorkOrder->GetMaxDistance();
        $WorkOrderMaxDuration = DateTimeTools::TimeToTimeStamp(
            $WorkOrder->GetMaxDuration());
    } else {
        return new Exception(
            _("Wrong parameter provided, parameter should be a work order."));
    } 
    // Vérification de compatibilité
    if (!empty($WorkOrderMaxVolume) && ($TotalVolume > $WorkOrderMaxVolume)) {
        return new Exception(_("Total order volume exceeds maximum value allowed in work order."));
    } 
    if (!empty($WorkOrderMaxLM) && ($TotalLM > $WorkOrderMaxLM)) {
        return new Exception(_("Total order linear meters exceeds maximum value allowed in work order."));
    } 
    if (!empty($WorkOrderMaxWeight) && ($TotalWeight > $WorkOrderMaxWeight)) {
        return new Exception(_("Total order weight exceeds maximum value allowed in work order."));
    } 
    if (!empty($WorkOrderMaxDistance) && 
        ($TotalDistance > $WorkOrderMaxDistance)) {
        return new Exception(_("Total order distance exceeds maximum value allowed in work order."));
    } 
    if (!empty($WorkOrderMaxDuration) && 
        ($TotalDuration > $WorkOrderMaxDuration)) {
        return new Exception(_("Total order duration exceeds maximum value allowed in work order."));
    } 
    // pas d'exception, on renvoie true
    return true;
}

?>
