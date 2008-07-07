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
 * DocumentAppendix class
 *
 */
class DocumentAppendix extends Object {
    
    // Constructeur {{{

    /**
     * DocumentAppendix::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Code string property + getter/setter {{{

    /**
     * Code string property
     *
     * @access private
     * @var string
     */
    private $_Code = '';

    /**
     * DocumentAppendix::getCode
     *
     * @access public
     * @return string
     */
    public function getCode() {
        return $this->_Code;
    }

    /**
     * DocumentAppendix::setCode
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCode($value) {
        $this->_Code = $value;
    }

    // }}}
    // Title string property + getter/setter {{{

    /**
     * Title string property
     *
     * @access private
     * @var string
     */
    private $_Title = '';

    /**
     * DocumentAppendix::getTitle
     *
     * @access public
     * @return string
     */
    public function getTitle() {
        return $this->_Title;
    }

    /**
     * DocumentAppendix::setTitle
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setTitle($value) {
        $this->_Title = $value;
    }

    // }}}
    // Body string property + getter/setter {{{

    /**
     * Body string property
     *
     * @access private
     * @var string
     */
    private $_Body = '';

    /**
     * DocumentAppendix::getBody
     *
     * @access public
     * @return string
     */
    public function getBody() {
        return $this->_Body;
    }

    /**
     * DocumentAppendix::setBody
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBody($value) {
        $this->_Body = $value;
    }

    // }}}
    // Image string property + getter/setter {{{

    /**
     * Image string property
     *
     * @access private
     * @var string
     */
    private $_Image = '';

    /**
     * DocumentAppendix::getImage
     *
     * @access public
     * @return string
     */
    public function getImage() {
        return $this->_Image;
    }

    /**
     * DocumentAppendix::setImage
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setImage($value) {
        $this->_Image = $value;
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
        return 'DocumentAppendix';
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
        return _('Document appendices');
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
            'Code' => Object::TYPE_STRING,
            'Title' => Object::TYPE_STRING,
            'Body' => Object::TYPE_TEXT,
            'Image' => Object::TYPE_IMAGE);
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
        $return = array();
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
        $return = array('Code');
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
        return array('grid', 'add', 'edit', 'del');
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
        $return = array(
            'Code'=>array(
                'label'        => _('Code'),
                'shortlabel'   => _('Code'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('Code')
            ),
            'Title'=>array(
                'label'        => _('Title'),
                'shortlabel'   => _('Title'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('Content')
            ),
            'Body'=>array(
                'label'        => _('Body'),
                'shortlabel'   => _('Body'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('Content')
            ),
            'Image'=>array(
                'label'        => _('Image'),
                'shortlabel'   => _('Image'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => _('Content')
            ));
        return $return;
    }

    // }}}
    // toString() {{{

    /**
     * Retourne la représentation texte de l'objet
     *
     * @access public
     * @return string
     */
    public function toString() {
        return $this->getCode();
    }

    // }}}
    // getToStringAttribute() {{{

    /**
     * Retourne le nom de l'attribut pointé par toString()
     *
     * @static
     * @access public
     * @return string
     */
    public function getToStringAttribute() {
        return 'Code';
    }

    // }}}
}

?>