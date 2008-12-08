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
 * RTWMaterial class
 *
 * Class containing addon methods.
 */
class RTWMaterial extends _RTWMaterial {
    // Constructeur {{{

    /**
     * RTWMaterial::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // RTWMaterial::getToStringAttribute() {{{

    /**
     * Retourne le nom des attributs utilisés par la méthode toString()

     * @access public
     * @return array
     */
    function getToStringAttribute() {
        return array('Name', 'Color');
    }

    // }}}
    // RTWMaterial::toString() {{{

    /**
     * Retourne le nom du material et sa couleur.

     * @access public
     * @return string
     */
    function toString() {
        $ref = $this->getReferenceByActor();
        if (empty($ref)) {
            $ref = $this->getName();
        }
        if ($this->getColor() instanceof RTWColor) {
            $ref .= ' (' . $this->getColor()->getName() . ')';
        }
        return $ref;
    }

    // }}}
    // RTWMaterial::toStringForCustoms() {{{

    /**
     *
     * @access public
     * @return string
     */
    function toStringForCustoms() {
        $ret = $this->getScientificName();
        if (($origin = $this->getOrigin()) != '') {
            $ret .= " ($origin)";
        }
        return $ret;
    }

    // }}}
    // RTWMaterial::getCommercialNameAndColor() {{{

    /**
     * Retourne le nom commercial et la couleur.

     * @access public
     * @return string
     */
    function getCommercialNameAndColor() {
        $ref = $this->getName();
        if ($this->getColor() instanceof RTWColor) {
            $ref .= ' (' . $this->getColor()->getName() . ')';
        }
        return $ref;
    }

    // }}}
    // RTWMaterial::canBeDeleted() {{{

    /**
     * RTWSeason::canBeDeleted()
     * Retourne true si l'objet peut etre detruit en base de donnees.
     * Un RTWSeason ne doit pas etre lie a un RTWModel
     *
     * @access public
     * @return boolean
     */
    public function canBeDeleted() {
        $id = $this->getId();
        $filter = new FilterComponent(
            new FilterRule('HeelReference', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Sole', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Box', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('HandBag', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Material1', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Material2', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Material3', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Accessory1', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Accessory2', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Accessory3', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Lining', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Insole', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('UnderSole', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Lagrima', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('HeelCovering', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Selvedge', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Thread1', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Thread2', FilterRule::OPERATOR_EQUALS, $id),
            new FilterRule('Bamboo', FilterRule::OPERATOR_EQUALS, $id),
            FilterComponent::OPERATOR_OR
        );
        $testColl = Object::loadCollection('RTWModel', $filter);
        if ($testColl->getCount() > 0) {
            throw new Exception('This material is used in one or more models.');
        }
        return true;
    }

    // }}}

}

?>