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
 * RTWMaterial class
 *
 * Classe contenant des m�thodes additionnelles
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
        $ret = $this->getName();
        if ($this->getColor() instanceof RTWColor) {
            $ret .= ' ' . $this->getColor()->getName();
        }
        $ref = $this->getReferenceByActor();
        if (!empty($ref)) {
            $ret .= ' / ' . $ref;
        }
        return $ret;
    }

    // }}}

}

?>