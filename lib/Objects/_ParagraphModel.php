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

class _ParagraphModel extends Object {
    
    // Constructeur {{{

    /**
     * _ParagraphModel::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
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
     * _ParagraphModel::getTitle
     *
     * @access public
     * @return string
     */
    public function getTitle() {
        return $this->_Title;
    }

    /**
     * _ParagraphModel::setTitle
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setTitle($value) {
        $this->_Title = $value;
    }

    // }}}
    // LinkParagraphModelQuestion one to many relation + getter/setter {{{

    /**
     * LinkParagraphModelQuestion 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_LinkParagraphModelQuestionCollection = false;

    /**
     * _ParagraphModel::getLinkParagraphModelQuestionCollection
     *
     * @access public
     * @return object Collection
     */
    public function getLinkParagraphModelQuestionCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ParagraphModel');
            return $mapper->getOneToMany($this->getId(),
                'LinkParagraphModelQuestion', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_LinkParagraphModelQuestionCollection) {
            $mapper = Mapper::singleton('ParagraphModel');
            $this->_LinkParagraphModelQuestionCollection = $mapper->getOneToMany($this->getId(),
                'LinkParagraphModelQuestion');
        }
        return $this->_LinkParagraphModelQuestionCollection;
    }

    /**
     * _ParagraphModel::getLinkParagraphModelQuestionCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getLinkParagraphModelQuestionCollectionIds($filter = array()) {
        $col = $this->getLinkParagraphModelQuestionCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _ParagraphModel::setLinkParagraphModelQuestionCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setLinkParagraphModelQuestionCollection($value) {
        $this->_LinkParagraphModelQuestionCollection = $value;
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
        return 'ParagraphModel';
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
        return _('Paragraph model');
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
            'Title' => Object::TYPE_STRING);
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
            'LinkFormModelParagraphModel'=>array(
                'linkClass'     => 'LinkFormModelParagraphModel',
                'field'         => 'ParagraphModel',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'LinkParagraphModelQuestion'=>array(
                'linkClass'     => 'LinkParagraphModelQuestion',
                'field'         => 'ParagraphModel',
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
        $return = array('Title');
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
            'Title'=>array(
                'label'        => _('Title'),
                'shortlabel'   => _('Title'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
}

?>