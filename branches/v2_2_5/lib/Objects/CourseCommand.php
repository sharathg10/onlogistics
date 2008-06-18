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

class CourseCommand extends _CourseCommand {
    // Constructeur {{{

    /**
     * CourseCommand::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}

    /**
     * CourseCommand::findInstructor()
     * Retourne un instructeur compatible avec la commande. 
     * On essaie d'abord de voir si celui choisi est ok, sinon on prend celui
     * du client s'il est compatible. Sinon on retourne false.
     * ATTENTION: cette méthode affecte aussi l'instructeur à la commande
     *
     * @access public
     * @return boolean 
     **/
    function findInstructor(){
        $flytype = $this->getFlyType();
        if (!($flytype instanceof FlyType)) {
            return false;
        }
        // début moins tolérance
        $start = DateTimeTools::MySQLDateToTimeStamp(
            $this->getWishedStartDate()) - $this->_getTolerance();
        $end = DateTimeTools::MySQLDateToTimeStamp(
            $this->getWishedEndDate());
        
        $inst = $this->getInstructor();
        if ($inst instanceof AeroInstructor) {
            // on essaie de voir s'il est ok pour la commande
            if ($inst->canFlyWithType($flytype) && 
                $inst->isAvailableFor($start, $end)) {
                // l'instructeur est celui souhaité
                $this->setIsWishedInstructor(true);
                return $inst;
            }
        }
        // sinon on prend le premier instructeur disponible
        $mapper = Mapper::singleton('AeroInstructor');
        $custID = $this->getCustomerID();
        $instCol = $mapper->loadCollection();
        $count = $instCol->getCount();
        for($i=0; $i<$count; $i++){
            $inst = $instCol->getItem($i);
            // on essaie de voir s'il est ok pour la commande
            if ($inst->canFlyWithType($flytype) && 
                $inst->isAvailableFor($start, $end)) {
                // l'instructeur n'est pas celui souhaité
                $this->setInstructor($inst);
                if ($custID != $inst->getId()) {
                    $this->setIsWishedInstructor(false);
                }
                
                return $inst;
            }
        }
        return false;
    }
    
    /**
     * CourseCommand::findAeroConcreteProduct()
     * Retourne un hélicoptère (AeroConcreteProduct) compatible avec la commande, 
     * c'est à dire disponible pour le créneau choisi.
     * ATTENTION: cette méthode affecte aussi l'helico à la commande
     *
     * @access public
     * @return object AeroConcreteProduct 
     **/
    function findAeroConcreteProduct(){
        require_once('Objects/ConcreteProduct.php');
        //require_once('ConcreteProduct.const.php');
        $flytype = $this->getFlyType();
        if (!($flytype instanceof FlyType)) {
            return -1;
        }
        $start = DateTimeTools::MySQLDateToTimeStamp(
                $this->getWishedStartDate()) - $this->_getTolerance();
        $end = DateTimeTools::MySQLDateToTimeStamp($this->getWishedEndDate());
        $ccpCollection = $flytype->getAeroConcreteProductCollection(
                array('State'=>ConcreteProduct::EN_MARCHE));
        $count = $ccpCollection->getCount();
        if ($count == 0) {
            return -1;
        }
        for($i = 0; $i < $count; $i++){
            $ccp = $ccpCollection->getItem($i);
            if ($ccp->isAvailableFor($start, $end)) {
                $this->setAeroConcreteProduct($ccp);
                return $ccp;
            }
        }
        return false;
    }
    
    /**
     * CourseCommand::findChain()
     * SC: Sélection de la chaîne de type 'Cours' dont l'acteur de fin est 
     * soit le client, soit l’acteur générique auquel le client est affecté.
     * Si plusieurs chaînes correspondent à ce critère, prendre la première.
     * Si aucune chaîne ne correspond à ce critère, afficher un message 
     * d'erreur et envoyer une alerte au Décisionnaire.
     *
     * @access public
     * @return mixed un objet Chain ou une exception 
     **/
    function findChain(){
        require_once('Objects/Chain.php');
        $customer = $this->getCustomer();
        if (!($customer instanceof AeroCustomer)) {
            return new Exception(_('There is no customer for this order.'));
        }
        $filter = new FilterComponent(
            new FilterComponent(
                new FilterRule(
                    'Type',
                    FilterRule::OPERATOR_EQUALS,
                    Chain::CHAIN_TYPE_COURSE
                )                  
            ),
            FilterComponent::OPERATOR_AND,
            new FilterComponent(
                FilterComponent::OPERATOR_OR,
                new FilterRule(
                    'SiteTransition.ArrivalActor',
                    FilterRule::OPERATOR_EQUALS,
                    $customer->getId()
                ),
                new FilterRule(
                    'SiteTransition.ArrivalActor',
                    FilterRule::OPERATOR_EQUALS,
                    $customer->getGenericActorId()
                )              
            )
        );
        $mapper = Mapper::singleton('Chain');
        $chain = $mapper->load($filter);
        if (!($chain instanceof Chain)) {
            return new Exception(
                _('Order cannot be completed, customer is not affected to any chain. Please contact your admin.'));
        }
        return $chain;
    }
    
    /**
     * Retourne la tolerance de l'opération de vol
     * 
     * @access public
     * @return string time 
     **/
    function _getTolerance(){
        require_once('Objects/Operation.const.php');
        $mapper = Mapper::singleton('Operation');
        $ope = $mapper->load(array('Id'=>OPERATION_VOL));
        if ($ope instanceof Operation) {
            return DateTimeTools::TimeToTimeStamp($ope->getFrontTolerance());
        }
        return 0;
    }

}

?>