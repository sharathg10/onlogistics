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
 * RTWModel class
 *
 * Classe contenant des méthodes additionnelles
 */
class RTWModel extends _RTWModel {
    // Constructeur {{{

    /**
     * RTWModel::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // RTWModel::canBeDeleted() {{{

    /**
     * Retourne true si l'objet peut être détruit en base de donnees.
     * Un RTWColor ne doit pas etre lie a un RTWMaterial
     *
     * @access public
     * @return boolean
     */
    public function canBeDeleted() {
        try {
            parent::canBeDeleted();
            $pdtCol = $this->getRTWProductCollection();
            foreach ($pdtCol as $pdt) {
                if (!$pdt->isDeletable(false)) {
                    throw new Exception('');
                }
            }
        } catch (Exception $exc) {
            throw new Exception(_('This model can not be modified because it is already used in one or more orders'));
        }
        return true;
    }

    // }}}
    // RTWModel::getMaterialProperties() {{{

    /**
     * Retourne les proprietes RTWMaterial
     *
     * @access public
     * @return boolean
     */
    public function getMaterialProperties($onlyForNomenclature=false) {

        $return = array(
            'HeelReference' => _('Heel reference'),
            'Sole'          => _('Sole'),
            'Box'           => _('Box'),
            'HandBag'       => _('Hand bag'),
            'Material1'     => _('Material 1'),
            'Material2'     => _('Material 2'),
            'Accessory1'    => _('Accessory 1'),
            'Accessory2'    => _('Accessory 2'),
            'Lining'        => _('Lining'),
            'Insole'        => _('Insole'),
            'UnderSole'     => _('Under-sole'),
            'Lagrima'       => _('Lagrima'),
            'HeelCovering'  => _('Heel covering'),
            'Selvedge'      => _('Selvedge'),
            'Bamboo'        => _('Bamboo'),
        );
        if (!$onlyForNomenclature) {
            $return['Thread1'] = _('Thread 1');
            $return['Thread2'] = _('Thread 2');
        }
        return $return;
    }
    // }}}
    // RTWModel::toString() {{{

    /**
     * Retourne le nom presse + style number du modele.
     *
     * @access public
     * @return string
     */
    function toString() {
        if (($pressName = $this->getPressName()) instanceof RTWPressName) {
            return $pressName->getName();
        }
        return '';
    }

    // }}}
    // RTWModel::getToStringAttribute() {{{

    /**
     * Retourne le nom presse + style number du modele.
     *
     * @access public
     * @return string
     */
    function getToStringAttribute() {
        return array('PressName');
    }

    // }}}

}

?>
