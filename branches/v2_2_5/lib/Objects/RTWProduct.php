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
 * RTWProduct class
 *
 * Class containing addon methods.
 */
class RTWProduct extends _RTWProduct {
    // Constructeur {{{

    /**
     * RTWProduct::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // RTWProduct::toString() {{{

    /**
     * Retourne le nom du produit, les informations de ses options et sa
     * taille.

     * @access public
     * @return string
     */
    function toString() {
        $sep   = '/';
        $ret   = $this->getName();
        $model = $this->getModel();
        if (!($model instanceof RTWModel)) {
            return $ret;
        }
        $attrs = array('Material1', 'Material2', 'Material3', 'Accessory1', 'Accessory2', 'Accessory3');
        foreach ($attrs as $attr) {
            $getter = 'get' . $attr;
            if (($obj = $model->$getter()) instanceof RTWMaterial) {
                $ret .= " $sep " . $obj->toString();
            }
        }
        if (($size = $this->getSize()) instanceof RTWSize) {
            $ret .= " $sep " . _('Size') . ': ' . $size->toString();
        }
        return $ret;
    }

    // }}}
    // RTWProduct::getProperty() {{{

    /**
     *
     * @access public
     * @return void
     */
    public function getProperty($getter)
    {
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        if (($model = $this->getModel()) instanceof RTWModel) {
            if (method_exists($model, $getter)) {
                $ret = $model->$getter();
                if ($ret instanceof Object) {
                    return $ret->toString();
                }
                return $ret;
            }
        }
        return parent::getProperty($getter);
    }

    // }}}
    // RTWProduct::setProperty() {{{

    /**
     *
     * @access public
     * @return void
     */
    public function setProperty($setter, $prop_value)
    {
        if (method_exists($this, $setter)) {
            $this->$setter($prop_value);
            return;
        }
        if (($model = $this->getModel()) instanceof RTWModel) {
            if (method_exists($model, $setter)) {
                $model->$setter($prop_value);
                $model->save();
                return;
            }
        }
        return parent::setProperty($setter, $prop_value);
    }

    // }}}
    // RTWProduct::generateEAN13Code() {{{

    /**
     * Generates the EAN13 code for the current product.
     *
     * @access public
     * @return void
     */
    public function generateEAN13Code()
    {
        include_once 'EAN13Tools.php';
        $this->setEAN13Code(generateEAN13Code());
    }

    // }}}

}

?>
