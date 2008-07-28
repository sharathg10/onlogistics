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
 * RTWModel class
 *
 * Class containing addon methods.
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
    public static function getMaterialProperties($onlyForNomenclature=false) {

        $return = array(
            'Material1'     => _('Material 1'),
            'Material2'     => _('Material 2'),
            'Accessory1'    => _('Accessory 1'),
            'Accessory2'    => _('Accessory 2'),
        );
        if (!$onlyForNomenclature) {
            $return += array(
                'Thread1' => _('Thread 1'),
                'Thread2' => _('Thread 2')
            );
        }
        $return += array(
            'Selvedge'      => _('Selvedge'),
            'Lining'        => _('Lining'),
            'Insole'        => _('Insole'),
            'UnderSole'     => _('Under-sole'),
            'MediaPlanta'   => _('Media planta'),
            'Lagrima'       => _('Lagrima'),
            'Sole'          => _('Sole'),
            'HeelReference' => _('Heel reference'),
            'HeelCovering'  => _('Heel covering'),
            'Bamboo'        => _('Bamboo'),
            'Box'           => _('Box'),
            'HandBag'       => _('Hand bag'),
        );
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
    // RTWModel::getLegalMentions() {{{

    /**
     *
     * @access public
     * @return void
     */
    public function getLegalMentions($asHtml = false)
    {
        $nl    = $asHtml ? '<br/>' : "\n";
        $lines = array();
        $upper = array();
        $mats  = array('Material1', 'Material2', 'Accessory1', 'Accessory2');
        foreach ($mats as $mat) {
            $getter = 'get' . $mat;
            if (($matObj = $this->$getter()) instanceof RTWMaterial) {
                $upper[] = '    - ' . $matObj->toStringForCustoms();
            }
        }
        if (count($upper)) {
            $lines[] = _('Upper') . ": $nl" . implode($nl, $upper);
        }
        $lines[] = ($heelCov = $this->getHeelCovering()) instanceof RTWMaterial ? 
            _('Heel covering') . ': ' . $heelCov->toStringForCustoms() : null;
        $lines[] = ($lining = $this->getLining()) instanceof RTWMaterial ? 
            _('Lining') . ': ' . $lining->toStringForCustoms() : null;
        $lines[] = ($sole = $this->getSole()) instanceof RTWMaterial ? 
            _('Sole') . ': ' . $sole->toStringForCustoms() : null;
        $lines = array_filter($lines);
        return implode($nl, $lines);
    }

    // }}}

}

?>
