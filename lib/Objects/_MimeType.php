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

class _MimeType extends Object {
    
    // Constructeur {{{

    /**
     * _MimeType::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Extension string property + getter/setter {{{

    /**
     * Extension string property
     *
     * @access private
     * @var string
     */
    private $_Extension = '';

    /**
     * _MimeType::getExtension
     *
     * @access public
     * @return string
     */
    public function getExtension() {
        return $this->_Extension;
    }

    /**
     * _MimeType::setExtension
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setExtension($value) {
        $this->_Extension = $value;
    }

    // }}}
    // ContentType string property + getter/setter {{{

    /**
     * ContentType string property
     *
     * @access private
     * @var string
     */
    private $_ContentType = '';

    /**
     * _MimeType::getContentType
     *
     * @access public
     * @return string
     */
    public function getContentType() {
        return $this->_ContentType;
    }

    /**
     * _MimeType::setContentType
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setContentType($value) {
        $this->_ContentType = $value;
    }

    // }}}
    // DisplayName string property + getter/setter {{{

    /**
     * DisplayName string property
     *
     * @access private
     * @var string
     */
    private $_DisplayName = '';

    /**
     * _MimeType::getDisplayName
     *
     * @access public
     * @return string
     */
    public function getDisplayName() {
        return $this->_DisplayName;
    }

    /**
     * _MimeType::setDisplayName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDisplayName($value) {
        $this->_DisplayName = $value;
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
        return 'MimeType';
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
    public static function getProperties() {
        $return = array(
            'Extension' => Object::TYPE_STRING,
            'ContentType' => Object::TYPE_STRING,
            'DisplayName' => Object::TYPE_STRING);
        return $return;
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
    public static function getLinks() {
        $return = array(
            'UploadedDocument'=>array(
                'linkClass'     => 'UploadedDocument',
                'field'         => 'MimeType',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ));
        return $return;
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
        return $return;
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
        return $return;
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
    public static function getMapping() {
        $return = array();
        return $return;
    }

    // }}}
}

?>