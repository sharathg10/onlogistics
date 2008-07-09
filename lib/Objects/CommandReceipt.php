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
 * @version   SVN: $Id: SiteAddEdit.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * CommandReceipt class
 *
 */
class CommandReceipt extends AbstractDocument {
    
    // Constructeur {{{

    /**
     * CommandReceipt::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // getTableName() {{{

    /**
     * Retourne le nom de la table sql correspondante
     *
     * @static
     * @access public
     * @return string
     */
    public static function getTableName() {
        return 'AbstractDocument';
    }

    // }}}
    // getObjectLabel() {{{

    /**
     * Retourne le "label" de la classe.
     *
     * @static
     * @access public
     * @return string
     */
    public static function getObjectLabel() {
        return _('None');
    }

    // }}}
    // getProperties() {{{

    /**
     * Retourne le tableau des propriétés.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getProperties($ownOnly = false) {
        $return = array();
        return $ownOnly?$return:array_merge(parent::getProperties(), $return);
    }

    // }}}
    // getLinks() {{{

    /**
     * Retourne le tableau des entités liées.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getLinks($ownOnly = false) {
        $return = array();
        return $ownOnly?$return:array_merge(parent::getLinks(), $return);
    }

    // }}}
    // getUniqueProperties() {{{

    /**
     * Retourne le tableau des propriétés qui ne peuvent prendre la même valeur
     * pour 2 occurrences.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getUniqueProperties() {
        $return = array();
        return array_merge(parent::getUniqueProperties(), $return);
    }

    // }}}
    // getEmptyForDeleteProperties() {{{

    /**
     * Retourne le tableau des propriétés doivent être "vides" (0 ou '') pour
     * qu'une occurrence puisse être supprimée en base de données.
     *
     * @static
     * @access public
     * @return array
     */
    public static function getEmptyForDeleteProperties() {
        $return = array();
        return array_merge(parent::getEmptyForDeleteProperties(), $return);
    }

    // }}}
    // getFeatures() {{{

    /**
     * Retourne le tableau des "fonctionalités" pour l'objet en cours.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getFeatures() {
        return array();
    }

    // }}}
    // getMapping() {{{

    /**
     * Retourne le mapping nécessaires aux composants génériques.
     * Voir Object pour documentation.
     *
     * @static
     * @access public
     * @return array
     * @see Object.php
     */
    public static function getMapping($ownOnly = false) {
        $return = array();
        return $ownOnly?$return:array_merge(parent::getMapping(), $return);
    }

    // }}}
    // useInheritance() {{{

    /**
     * Détermine si l'entité est une entité qui utilise l'héritage.
     * (classe parente ou classe fille). Ceci afin de differencier les entités
     * dans le mapper car classes filles et parentes sont mappées dans la même
     * table.
     *
     * @static
     * @access public
     * @return bool
     */
    public static function useInheritance() {
        return true;
    }

    // }}}
    // getParentClassName() {{{

    /**
     * Retourne le nom de la première classe parente
     *
     * @static
     * @access public
     * @return string
     */
    public static function getParentClassName() {
        return 'AbstractDocument';
    }

    // }}}
}

?>