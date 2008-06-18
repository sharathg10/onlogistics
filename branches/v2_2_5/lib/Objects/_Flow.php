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

class _Flow extends Object {
    
    // Constructeur {{{

    /**
     * _Flow::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Name string property + getter/setter {{{

    /**
     * Name string property
     *
     * @access private
     * @var string
     */
    private $_Name = '';

    /**
     * _Flow::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _Flow::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Handing string property + getter/setter {{{

    /**
     * Handing string property
     *
     * @access private
     * @var string
     */
    private $_Handing = '0';

    /**
     * _Flow::getHanding
     *
     * @access public
     * @return string
     */
    public function getHanding() {
        return $this->_Handing;
    }

    /**
     * _Flow::setHanding
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setHanding($value) {
        $this->_Handing = $value;
    }

    // }}}
    // Number string property + getter/setter {{{

    /**
     * Number string property
     *
     * @access private
     * @var string
     */
    private $_Number = '';

    /**
     * _Flow::getNumber
     *
     * @access public
     * @return string
     */
    public function getNumber() {
        return $this->_Number;
    }

    /**
     * _Flow::setNumber
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setNumber($value) {
        $this->_Number = $value;
    }

    // }}}
    // PieceNo int property + getter/setter {{{

    /**
     * PieceNo int property
     *
     * @access private
     * @var integer
     */
    private $_PieceNo = null;

    /**
     * _Flow::getPieceNo
     *
     * @access public
     * @return integer
     */
    public function getPieceNo() {
        return $this->_PieceNo;
    }

    /**
     * _Flow::setPieceNo
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setPieceNo($value) {
        $this->_PieceNo = ($value===null || $value === '')?null:(int)$value;
    }

    // }}}
    // FlowType foreignkey property + getter/setter {{{

    /**
     * FlowType foreignkey
     *
     * @access private
     * @var mixed object FlowType or integer
     */
    private $_FlowType = false;

    /**
     * _Flow::getFlowType
     *
     * @access public
     * @return object FlowType
     */
    public function getFlowType() {
        if (is_int($this->_FlowType) && $this->_FlowType > 0) {
            $mapper = Mapper::singleton('FlowType');
            $this->_FlowType = $mapper->load(
                array('Id'=>$this->_FlowType));
        }
        return $this->_FlowType;
    }

    /**
     * _Flow::getFlowTypeId
     *
     * @access public
     * @return integer
     */
    public function getFlowTypeId() {
        if ($this->_FlowType instanceof FlowType) {
            return $this->_FlowType->getId();
        }
        return (int)$this->_FlowType;
    }

    /**
     * _Flow::setFlowType
     *
     * @access public
     * @param object FlowType $value
     * @return void
     */
    public function setFlowType($value) {
        if (is_numeric($value)) {
            $this->_FlowType = (int)$value;
        } else {
            $this->_FlowType = $value;
        }
    }

    // }}}
    // TotalTTC float property + getter/setter {{{

    /**
     * TotalTTC float property
     *
     * @access private
     * @var float
     */
    private $_TotalTTC = 0;

    /**
     * _Flow::getTotalTTC
     *
     * @access public
     * @return float
     */
    public function getTotalTTC() {
        return $this->_TotalTTC;
    }

    /**
     * _Flow::setTotalTTC
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setTotalTTC($value) {
        if ($value !== null) {
            $this->_TotalTTC = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Paid float property + getter/setter {{{

    /**
     * Paid float property
     *
     * @access private
     * @var float
     */
    private $_Paid = 0;

    /**
     * _Flow::getPaid
     *
     * @access public
     * @return float
     */
    public function getPaid() {
        return $this->_Paid;
    }

    /**
     * _Flow::setPaid
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPaid($value) {
        if ($value !== null) {
            $this->_Paid = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Currency foreignkey property + getter/setter {{{

    /**
     * Currency foreignkey
     *
     * @access private
     * @var mixed object Currency or integer
     */
    private $_Currency = false;

    /**
     * _Flow::getCurrency
     *
     * @access public
     * @return object Currency
     */
    public function getCurrency() {
        if (is_int($this->_Currency) && $this->_Currency > 0) {
            $mapper = Mapper::singleton('Currency');
            $this->_Currency = $mapper->load(
                array('Id'=>$this->_Currency));
        }
        return $this->_Currency;
    }

    /**
     * _Flow::getCurrencyId
     *
     * @access public
     * @return integer
     */
    public function getCurrencyId() {
        if ($this->_Currency instanceof Currency) {
            return $this->_Currency->getId();
        }
        return (int)$this->_Currency;
    }

    /**
     * _Flow::setCurrency
     *
     * @access public
     * @param object Currency $value
     * @return void
     */
    public function setCurrency($value) {
        if (is_numeric($value)) {
            $this->_Currency = (int)$value;
        } else {
            $this->_Currency = $value;
        }
    }

    // }}}
    // PaymentDate datetime property + getter/setter {{{

    /**
     * PaymentDate int property
     *
     * @access private
     * @var string
     */
    private $_PaymentDate = 0;

    /**
     * _Flow::getPaymentDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getPaymentDate($format = false) {
        return $this->dateFormat($this->_PaymentDate, $format);
    }

    /**
     * _Flow::setPaymentDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setPaymentDate($value) {
        $this->_PaymentDate = $value;
    }

    // }}}
    // EditionDate datetime property + getter/setter {{{

    /**
     * EditionDate int property
     *
     * @access private
     * @var string
     */
    private $_EditionDate = 0;

    /**
     * _Flow::getEditionDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEditionDate($format = false) {
        return $this->dateFormat($this->_EditionDate, $format);
    }

    /**
     * _Flow::setEditionDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEditionDate($value) {
        $this->_EditionDate = $value;
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
     * _Flow::getActorBankDetail
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
     * _Flow::getActorBankDetailId
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
     * _Flow::setActorBankDetail
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
    // FlowItem one to many relation + getter/setter {{{

    /**
     * FlowItem 1..* relation
     *
     * @access private
     * @var Collection
     */
    private $_FlowItemCollection = false;

    /**
     * _Flow::getFlowItemCollection
     *
     * @access public
     * @return object Collection
     */
    public function getFlowItemCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('Flow');
            return $mapper->getOneToMany($this->getId(),
                'FlowItem', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_FlowItemCollection) {
            $mapper = Mapper::singleton('Flow');
            $this->_FlowItemCollection = $mapper->getOneToMany($this->getId(),
                'FlowItem');
        }
        return $this->_FlowItemCollection;
    }

    /**
     * _Flow::getFlowItemCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getFlowItemCollectionIds($filter = array()) {
        $col = $this->getFlowItemCollection($filter, array(), array('Id'));
        return $col instanceof Collection?$col->getItemIds():array();
    }

    /**
     * _Flow::setFlowItemCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setFlowItemCollection($value) {
        $this->_FlowItemCollection = $value;
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
        return 'Flow';
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
            'Name' => Object::TYPE_STRING,
            'Handing' => Object::TYPE_STRING,
            'Number' => Object::TYPE_STRING,
            'PieceNo' => Object::TYPE_INT,
            'FlowType' => 'FlowType',
            'TotalTTC' => Object::TYPE_DECIMAL,
            'Paid' => Object::TYPE_DECIMAL,
            'Currency' => 'Currency',
            'PaymentDate' => Object::TYPE_DATETIME,
            'EditionDate' => Object::TYPE_DATETIME,
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
            'FlowItem'=>array(
                'linkClass'     => 'FlowItem',
                'field'         => 'Flow',
                'ondelete'      => 'cascade',
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
        return array('grid', 'searchform');
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
            'Name'=>array(
                'label'        => _('name'),
                'shortlabel'   => _('name'),
                'usedby'       => array('grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Number'=>array(
                'label'        => _('number'),
                'shortlabel'   => _('number'),
                'usedby'       => array('grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'PaymentDate'=>array(
                'label'        => _('Payment date'),
                'shortlabel'   => _('Payment date'),
                'usedby'       => array('grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ));
        return $return;
    }

    // }}}
}

?>