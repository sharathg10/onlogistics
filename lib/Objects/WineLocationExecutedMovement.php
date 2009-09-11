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
 * @version   SVN: $Id: LocationExecutedMovement.php 206 2008-10-02 14:45:37Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

class WineLocationExecutedMovement extends LocationExecutedMovement {
    // class constants {{{

    const TYPE_PERIODICAL= 0;
    const TYPE_SELECTED = 1;

    // }}}
    // Constructeur {{{

    /**
     * LocationExecutedMovement::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // DCA foreignkey property + getter/setter {{{

    /**
     * ForwardingForm foreignkey
     *
     * @access private
     * @var mixed object ForwardingForm or integer
     */
    private $_DCA = false;

    /**
     * _LocationExecutedMovement::getForwardingForm
     *
     * @access public
     * @return object ForwardingForm
     */
    public function getDCA() {
        if (is_int($this->_DCA) && $this->_DCA> 0) {
            if($this->_DCAType == WineLocationExecutedMovement::TYPE_PERIODICAL) {
                $mapper = Mapper::singleton('WineDCAPeriodical');
            } else {
                $mapper = Mapper::singleton('WineDCAHeader');
            }

            $this->_DCA = $mapper->load(array('Id'=>$this->_DCA));
        }
        return $this->_DCA;
    }

    /**
     * _LocationExecutedMovement::getForwardingFormId
     *
     * @access public
     * @return integer
     */
    public function getDCAId() {
        if ($this->_DCA instanceof WineDCAHeader
            || $this->_DCA instanceof WineDCAPeriodical) {
            return $this->_DCA->getId();
        }
        return (int)$this->_DCA;
    }

    /**
     * _LocationExecutedMovement::setForwardingForm
     *
     * @access public
     * @param object ForwardingForm $value
     * @return void
     */
    public function setDCA($value) {
        if (is_numeric($value)) {
            $this->_DCA = (int)$value;
        } else {
            $this->_DCA = $value;
        }
    }

    // }}}
    // DCAType const property + getter/setter/getDCATypeConstArray {{{

    /**
     * EntrieExit int property
     *
     * @access private
     * @var integer
     */
    private $_DCAType= 0;

    /**
     * MovementType::getEntrieExit
     *
     * @access public
     * @return integer
     */
    public function getDCAType() {
        return $this->_DCAType;
    }

    /**
     * MovementType::setEntrieExit
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setDCAType($value) {
        if ($value !== null) {
            $this->_DCAType= (int)$value;
        }
    }

    /**
     * MovementType::getEntrieExitConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getDCATypeConstArray($keys = false) {
        $array = array(
            WineLocationExecutedMovement::TYPE_PERIODICAL=> _("Periodical"), 
            WineLocationExecutedMovement::TYPE_SELECTED=> _("Selected Items")
        );
        asort($array);
        return $keys?array_keys($array):$array;
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
        return 'LocationExecutedMovement';
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
        return _('LocationExecutedMovement');
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

        $WineDCAClass = 'WineDCAHeader' ;
//        if ($this->_DCAType == WineLocationExecutedMovement::TYPE_PERIODICAL) 
//            $WineDCAClass = 'WineDCAPeriodical' ;

        $return = array(
            'DCA' => $WineDCAClass,
            'DCAType' => Object::TYPE_CONST );
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
        return array('add', 'edit', 'del', 'grid', 'searchform');
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
        $return = array(
            'DCA'=>array(
                'label'        => _('DCA'),
                'shortlabel'   => _('DCA'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'DCAType'=>array(
                'label'        => _('DCA Type'),
                'shortlabel'   => _('DCA Type'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            )
        );
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
        return false;
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
        return false;
    }

    // }}}
}

?>
