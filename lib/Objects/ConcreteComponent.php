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

class ConcreteComponent extends _ConcreteComponent {
    // Constructeur {{{

    /**
     * ConcreteComponent::__construct()
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
     * Remplit cptHead en cascade si le CP choisi resulte d'un assemblage
     * Recursion
     *
     * @access public
     * @return object Collection of ConcreteComponent
     **/
    function getChildrenConcreteComponents() {
        $return = new Collection();
        $mapper = Mapper::singleton('ConcreteComponent');
        $coll = $mapper->loadCollection(
                array('Parent' => $this->getConcreteProductId()));
        $count = $coll->getCount();

        for($i = 0; $i < $count; $i++) {
            $ccp = $coll->getItem($i);
            $return->setItem($ccp);
            $return = $return->merge($ccp->getChildrenConcreteComponents());
        }
        return $return;
    }

    /**
     * Remplit cptHead en cascade si le CP choisi resulte d'un assemblage
     * Recursion
     *
     * @param integer $headId un Id de ConcreteProduct
     * @access public
     * @return void
     **/
    function updateChildsHead($headCP) {
        $idArray = array();
        $mapper = Mapper::singleton('ConcreteComponent');
        $coll = $this->getChildrenConcreteComponents();
        if (Tools::isEmptyObject($coll)) {
            return;
        }
        $count = $coll->getCount();
        for($i = 0; $i < $count; $i++) {
            $ccp = $coll->getItem($i);
            $idArray[] = $ccp->getConcreteProductId();
        }
        // TODO: tester, puis rendre recursif!!!!
        $headCP->addRemoveChild($idArray);
        $headCP->save();
    }

}

?>