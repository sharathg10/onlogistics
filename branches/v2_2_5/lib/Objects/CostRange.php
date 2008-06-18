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

class CostRange extends _CostRange {
    // Constructeur {{{

    /**
     * CostRange::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // CostRange::calculPrice() {{{

    /**
     * Retourne le prix en fonction du CostType et des paramètres à prendre en 
     * compte: poids, volume, temps et quantité.
     * les paramètres sont passés dans un tableau de la forme:
     * <code>
     * array(
     *      'weight'   => 3, 
     *      'volume'   => 6, 
     *      'time'     => 4, 
     *      'quantity' => 12)
     * </code>
     *
     * @param array $params les paramètres
     * @param float $coeff Coefficient à appliquer au cout du CostRange
     * @return float
     */
    public function calculPrice($params=array(), $coeff=1) {
        $costTypesFixed = array(CostRange::TYPE_FIXED, CostRange::TYPE_FIXED_WEIGHT_RANGE, 
            CostRange::TYPE_FIXED_VOLUME_RANGE, CostRange::TYPE_FIXED_QUANTITY);
        if(in_array($this->getCostType(), $costTypesFixed)) {
            return $this->getCost() * $coeff;
        } 

        $costTypesHour = array(CostRange::TYPE_HOUR, CostRange::TYPE_HOUR_WEIGHT_RANGE, 
            CostRange::TYPE_HOUR_VOLUME_RANGE);
        if(in_array($this->getCostType(), $costTypesHour)) {
            $time = !empty($params['time'])?$params['time']:0;
            return $time * $this->getCost() * $coeff;
        } 

        if($this->getCostType() == CostRange::TYPE_AMOUNT_WEIGHT_RANGE) {
            $weight = !empty($params['weight'])?$params['weight']:0;
            return $weight * $this->getCost() * $coeff;
        } 

        if($this->getCostType() == CostRange::TYPE_AMOUNT_VOLUME_RANGE) {
            $volume = !empty($params['volume'])?$params['volume']:0;
            return $volume * $this->getCost() * $coeff;
        } 

        if($this->getCostType() == CostRange::TYPE_UNIT || $this->getCostType() == CostRange::TYPE_AMOUNT) {
            $quantity = !empty($params['quantity'])?$params['quantity']:0;
            return $quantity * $this->getCost() * $coeff;
        }

        if($this->getCostType() == CostRange::TYPE_UNIT_FOR_QUANTITY) {
            $quantity = !empty($params['quantity'])?$params['quantity']:0;
            return $quantity * ($this->getCost() * $coeff) / $this->getEndRange();
        }
        
        if($this->getCostType() == CostRange::TYPE_UNIT_BY_RANGE_10) {
            $weight = !empty($params['weight'])?$params['weight']:0;
            $weight = 10 * ceil($weight / 10);
            return $weight * $this->getCost() * $coeff;
        }
        
        if($this->getCostType() == CostRange::TYPE_UNIT_BY_RANGE_100) {
            $weight = !empty($params['weight'])?$params['weight']:0;
            $weight = 100 * ceil($weight / 100);
            return $weight * $this->getCost() * $coeff;
        }
    }

    // }}}
    // CostRange::canBeSaved() {{{

    /**
     * canBeSaved 
     * 
     * @access public
     * @return void
     */
    public function canBeSaved() {
        parent::canBeSaved();
        // pour un prix à l'unité pour une quantité, il faut saisir une qty dans 
        // EndRange
        if($this->getCostType() == CostRange::TYPE_UNIT_FOR_QUANTITY) {
            $endRange = $this->getEndRange();
            if($endRange==0) {
                throw new Exception(
                    _('You must provide a quantity in "Upper bound" field to a price by unit for a quantity.'));
            }
        }
        // pour prix fixe, il ne faut pas de magasin
        if($this->getCostType() == CostRange::TYPE_FIXED) {
            $store = $this->getStore();
            if($store instanceof Store) {
                throw new Exception(
                    _('You cannot define a fixed price for a store.')
                );
            }
        }
        // pour un prix avec type de produits il ne faut pas de zones
        /*if($this->getProductTypeId()>0 && ($this->getDepartureZoneId()>0 || $this->getArrivalZoneId()>0)) {
            throw new Exception(
                _('You cannot define a price for a product type in a zone.'));
        }*/       
        if(!$this->_checkCostType()) {
            throw new Exception(_('A service can only have one fixed price.'));
        }
        if(!$this->_checkRange()) {
            throw new Exception(_('Ranges that you are defining are in conflict.'));
        }
    }
    
    // }}}
    // CostRange::_checkCostType() {{{
    
    /**
     * Vérifie le CostType d'un CostRange.
     * 
     * @access private
     * @return boolean
     */
    private function _checkCostType() {
        $array = array(CostRange::TYPE_FIXED, CostRange::TYPE_HOUR, 
            CostRange::TYPE_UNIT, CostRange::TYPE_UNIT_FOR_QUANTITY);
        if(in_array($this->getCostType(), $array) ) {
            $filterArray = array();
            $filterArray[] = new FilterComponent(
                new FilterRule(
                    'CostType',
                    FilterRule::OPERATOR_EQUALS,
                    $this->getCostType()
                )
            );
            $filterArray[] = new FilterComponent(
                new FilterRule(
                    'DepartureZone',
                    FilterRule::OPERATOR_EQUALS,
                    $this->getDepartureZoneId()
                )
            );
            $filterArray[] = new FilterComponent(
                new FilterRule(
                    'ArrivalZone',
                    FilterRule::OPERATOR_EQUALS,
                    $this->getArrivalZoneId()
                )
            );
            $filterArray[] = new FilterComponent(
                new FilterRule(
                    'Store',
                    FilterRule::OPERATOR_EQUALS,
                    $this->getStore()
                )
            );
            $filterArray[] = new FilterComponent(
                new FilterRule(
                    'ProductType',
                    FilterRule::OPERATOR_EQUALS,
                    $this->getProductType()));
            $filterArray[] = new FilterComponent(
                new FilterRule(
                    'UnitType',
                    FilterRule::OPERATOR_EQUALS,
                    $this->getUnitType()));
            
            $assos = 'Prestation';
            $assosId = $this->getPrestationId();
            if($assosId==0) {
                $assos = 'PrestationCost';
                $assosId = $this->getPrestationCostId();
            }
            $filterArray[] = new FilterComponent(
                new FilterRule(
                    $assos,
                    FilterRule::OPERATOR_EQUALS,
                    $assosId));
            
            $filter = SearchTools::FilterAssembler($filterArray);
            $col = Object::loadCollection('CostRange', $filter, array(), array('Id'));
            if($col->getCount()>0) {
                return false;
            }
        }
        return true;
    }
    
    // }}}
    // CostRange::_checkRange() {{{
    
    /**
     * Vérifie qu'il n'y a pa s de conflit de tranches.
     * 
     * @access private
     * @return boolean
     */
    private function _checkRange() {
        $filterArray = array();
        //B<F & E>F => B<F<E
        $filterArray[] = new FilterComponent(
            new FilterRule(
                'BeginRange',
                FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                $this->getEndRange()
            ),
            new FilterRule(
                'EndRange',
                FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                $this->getEndRange()
            ),
            FilterComponent::OPERATOR_AND
        );
        //B<D & E>D => B<D<E
        $filterArray[] = new FilterComponent(
            new FilterRule(
                'BeginRange',
                FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                $this->getBeginRange()
            ),
            new FilterRule(
                'EndRange',
                FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                $this->getBeginRange()
            ),
            FilterComponent::OPERATOR_AND
        );
        // B>D & E<F
        $filterArray[] = new FilterComponent(
            new FilterRule(
                'BeginRange',
                FilterRule::OPERATOR_GREATER_THAN_OR_EQUALS,
                $this->getBeginRange()
            ),
            new FilterRule(
                'EndRange',
                FilterRule::OPERATOR_LOWER_THAN_OR_EQUALS,
                $this->getEndRange()
            ),
            FilterComponent::OPERATOR_AND
        );
        $filterCostRange = array();
        $filterCostRange[] = SearchTools::FilterAssembler($filterArray, 'Or');
        //ajout filtre pour le costType
        $filterCostRange[] = new FilterComponent(
            new FilterRule(
                'CostType',
                FilterRule::OPERATOR_EQUALS,
                $this->getCostType()
            )
        );
        // ajout filtres pour les zones
        $filterCostRange[] = new FilterComponent(
            new FilterRule(
                'DepartureZone',
                FilterRule::OPERATOR_EQUALS,
                $this->getDepartureZoneId()
            )
        );
        $filterCostRange[] = new FilterComponent(
            new FilterRule(
                'ArrivalZone',
                FilterRule::OPERATOR_EQUALS,
                $this->getArrivalZoneId()
            )
        );
        $filterCostRange[] = new FilterComponent(
            new FilterRule(
                'Store',
                FilterRule::OPERATOR_EQUALS,
                $this->getStoreId()
            )
        );
        $filterCostRange[] = new FilterComponent(
            new FilterRule(
                'ProductType',
                FilterRule::OPERATOR_EQUALS,
                $this->getProductTypeId()
            )
        );
        $filterCostRange[] = new FilterComponent(
            new FilterRule(
                'UnitType',
                FilterRule::OPERATOR_EQUALS,
                $this->getUnitTypeId()
            )
        );

        $assos = 'Prestation';
        $assosId = $this->getPrestationId();
        if($assosId==0) {
            $assos = 'PrestationCost';
            $assosId = $this->getPrestationCostId();
        }
        $filterCostRange[] = new FilterComponent(
            new FilterRule(
                $assos,
                FilterRule::OPERATOR_EQUALS,
                $assosId));

        //filtre complet
        $filter = SearchTools::FilterAssembler($filterCostRange);
        $col = Object::loadCollection('CostRange', $filter, array(), array('Id'));
        if($col->getCount() != 0) {
            return false;
        }
        return true;
    }
    
    // }}}
    // CostRange::findPrestation() {{{

    /**
     * Retourne la prestation directement associée ou celle associée au 
     * prestation cost
     * 
     * @access public
     * @return void
     */
    public function findPrestation() {
        $prestation = $this->getPrestation();
        if(!($prestation instanceof Prestation)) {
            $prestationCost = $this->getPrestationCost();
            $prestation = $prestationCost->getPrestation();
        }
        return $prestation;
    }

    // }}}

}

?>