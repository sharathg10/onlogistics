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

class AccountingType extends Object {
    
    // Constructeur {{{

    /**
     * AccountingType::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Type string property + getter/setter {{{

    /**
     * Type string property
     *
     * @access private
     * @var string
     */
    private $_Type = '';

    /**
     * AccountingType::getType
     *
     * @access public
     * @return string
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * AccountingType::setType
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setType($value) {
        $this->_Type = $value;
    }

    // }}}
    // MainModel string property + getter/setter {{{

    /**
     * MainModel int property
     *
     * @access private
     * @var integer
     */
    private $_MainModel = 0;

    /**
     * AccountingType::getMainModel
     *
     * @access public
     * @return integer
     */
    public function getMainModel() {
        return $this->_MainModel;
    }

    /**
     * AccountingType::setMainModel
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setMainModel($value) {
        if ($value !== null) {
            $this->_MainModel = (int)$value;
        }
    }

    // }}}
    // DistributionKey float property + getter/setter {{{

    /**
     * DistributionKey float property
     *
     * @access private
     * @var float
     */
    private $_DistributionKey = null;

    /**
     * AccountingType::getDistributionKey
     *
     * @access public
     * @return float
     */
    public function getDistributionKey() {
        return $this->_DistributionKey;
    }

    /**
     * AccountingType::setDistributionKey
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setDistributionKey($value) {
        $this->_DistributionKey = ($value===null || $value === '')?null:I18N::extractNumber($value);
    }

    // }}}
    // ActorBankDetail foreignkey property + getter/setter {{{

    /**
     * ActorBankDetail foreignkey
     *
     * @access private
     * @var mixed object ActorBankDetail or integer
     */
    private $_ActorBankDetail = false;

    /**
     * AccountingType::getActorBankDetail
     *
     * @access public
     * @return object ActorBankDetail
     */
    public function getActorBankDetail() {
        if (is_int($this->_ActorBankDetail) && $this->_ActorBankDetail > 0) {
            $mapper = Mapper::singleton('ActorBankDetail');
            $this->_ActorBankDetail = $mapper->load(
                array('Id'=>$this->_ActorBankDetail));
        }
        return $this->_ActorBankDetail;
    }

    /**
     * AccountingType::getActorBankDetailId
     *
     * @access public
     * @return integer
     */
    public function getActorBankDetailId() {
        if ($this->_ActorBankDetail instanceof ActorBankDetail) {
            return $this->_ActorBankDetail->getId();
        }
        return (int)$this->_ActorBankDetail;
    }

    /**
     * AccountingType::setActorBankDetail
     *
     * @access public
     * @param object ActorBankDetail $value
     * @return void
     */
    public function setActorBankDetail($value) {
        if (is_numeric($value)) {
            $this->_ActorBankDetail = (int)$value;
        } else {
            $this->_ActorBankDetail = $value;
        }
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
        return 'AccountingType';
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
        return _('Accounting model');
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
            'Type' => Object::TYPE_STRING,
            'MainModel' => Object::TYPE_BOOL,
            'DistributionKey' => Object::TYPE_FLOAT,
            'ActorBankDetail' => 'ActorBankDetail');
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
            'Actor'=>array(
                'linkClass'     => 'Actor',
                'field'         => 'AccountingType',
                'ondelete'      => 'nullify',
                'multiplicity'  => 'onetomany'
            ),
            'FlowType'=>array(
                'linkClass'     => 'FlowType',
                'field'         => 'AccountingType',
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
        $return = array('Type');
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
        return array('grid', 'add', 'edit', 'del', 'searchform');
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
            'Type'=>array(
                'label'        => _('Name'),
                'shortlabel'   => _('Name'),
                'usedby'       => array('addedit', 'grid', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'MainModel'=>array(
                'label'        => _('Main model'),
                'shortlabel'   => _('Main model'),
                'usedby'       => array('addedit', 'grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'DistributionKey'=>array(
                'label'        => _('Distribution key'),
                'shortlabel'   => _('Distribution key'),
                'usedby'       => array('addedit', 'grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ActorBankDetail'=>array(
                'label'        => _('Bank'),
                'shortlabel'   => _('Bank'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
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
        return $this->getType();
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
        return 'Type';
    }

    // }}}
}

?>