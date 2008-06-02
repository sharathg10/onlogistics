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

class ForecastFlow extends Object {
    
    // Constructeur {{{

    /**
     * ForecastFlow::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Description string property + getter/setter {{{

    /**
     * Description string property
     *
     * @access private
     * @var string
     */
    private $_Description = '';

    /**
     * ForecastFlow::getDescription
     *
     * @access public
     * @return string
     */
    public function getDescription() {
        return $this->_Description;
    }

    /**
     * ForecastFlow::setDescription
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDescription($value) {
        $this->_Description = $value;
    }

    // }}}
    // Amount float property + getter/setter {{{

    /**
     * Amount float property
     *
     * @access private
     * @var float
     */
    private $_Amount = null;

    /**
     * ForecastFlow::getAmount
     *
     * @access public
     * @return float
     */
    public function getAmount() {
        return $this->_Amount;
    }

    /**
     * ForecastFlow::setAmount
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setAmount($value) {
        $this->_Amount = ($value===null || $value === '')?
            null:round(I18N::extractNumber($value), 2);
    }

    // }}}
    // BeginDate datetime property + getter/setter {{{

    /**
     * BeginDate int property
     *
     * @access private
     * @var string
     */
    private $_BeginDate = 0;

    /**
     * ForecastFlow::getBeginDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getBeginDate($format = false) {
        return $this->dateFormat($this->_BeginDate, $format);
    }

    /**
     * ForecastFlow::setBeginDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setBeginDate($value) {
        $this->_BeginDate = $value;
    }

    // }}}
    // EndDate datetime property + getter/setter {{{

    /**
     * EndDate int property
     *
     * @access private
     * @var string
     */
    private $_EndDate = 0;

    /**
     * ForecastFlow::getEndDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getEndDate($format = false) {
        return $this->dateFormat($this->_EndDate, $format);
    }

    /**
     * ForecastFlow::setEndDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEndDate($value) {
        $this->_EndDate = $value;
    }

    // }}}
    // Active string property + getter/setter {{{

    /**
     * Active int property
     *
     * @access private
     * @var integer
     */
    private $_Active = 1;

    /**
     * ForecastFlow::getActive
     *
     * @access public
     * @return integer
     */
    public function getActive() {
        return $this->_Active;
    }

    /**
     * ForecastFlow::setActive
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setActive($value) {
        if ($value !== null) {
            $this->_Active = (int)$value;
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
     * ForecastFlow::getCurrency
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
     * ForecastFlow::getCurrencyId
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
     * ForecastFlow::setCurrency
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
    // FlowTypeItem one to many relation + getter/setter {{{

    /**
     * FlowTypeItem *..* relation
     *
     * @access private
     * @var Collection
     */
    private $_FlowTypeItemCollection = false;

    /**
     * ForecastFlow::getFlowTypeItemCollection
     *
     * @access public
     * @return object Collection
     */
    public function getFlowTypeItemCollection($filter = array(),
        $sortOrder = array(), $fields = array()) {
        // si un paramètre est passé on force le rechargement de la collection
        // on ne met en cache mémoire que les collections brutes
        if (!empty($filter) || !empty($sortOrder) || !empty($fields)) {
            $mapper = Mapper::singleton('ForecastFlow');
            return $mapper->getManyToMany($this->getId(),
                'FlowTypeItem', $filter, $sortOrder, $fields);
        }
        // si la collection n'est pas en mémoire on la charge
        if (false == $this->_FlowTypeItemCollection) {
            $mapper = Mapper::singleton('ForecastFlow');
            $this->_FlowTypeItemCollection = $mapper->getManyToMany($this->getId(),
                'FlowTypeItem');
        }
        return $this->_FlowTypeItemCollection;
    }

    /**
     * ForecastFlow::getFlowTypeItemCollectionIds
     *
     * @access public
     * @param $filter FilterComponent or array
     * @return array
     */
    public function getFlowTypeItemCollectionIds($filter = array()) {
        if (!empty($filter)) {
            $col = $this->getFlowTypeItemCollection($filter, array(), array('Id'));
            return $col instanceof Collection?$col->getItemIds():array();
        }
        if (false == $this->_FlowTypeItemCollection) {
            $mapper = Mapper::singleton('ForecastFlow');
            return $mapper->getManyToManyIds($this->getId(), 'FlowTypeItem');
        }
        return $this->_FlowTypeItemCollection->getItemIds();
    }

    /**
     * ForecastFlow::setFlowTypeItemCollectionIds
     *
     * @access public
     * @return array
     */
    public function setFlowTypeItemCollectionIds($itemIds) {
        $this->_FlowTypeItemCollection = new Collection('FlowTypeItem');
        foreach ($itemIds as $id) {
            $this->_FlowTypeItemCollection->setItem($id);
        }
    }

    /**
     * ForecastFlow::setFlowTypeItemCollection
     *
     * @access public
     * @param object Collection $value
     * @return void
     */
    public function setFlowTypeItemCollection($value) {
        $this->_FlowTypeItemCollection = $value;
    }

    /**
     * ForecastFlow::FlowTypeItemCollectionIsLoaded
     *
     * @access public
     * @return boolean
     */
    public function FlowTypeItemCollectionIsLoaded() {
        return ($this->_FlowTypeItemCollection !== false);
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
        return 'ForecastFlow';
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
        return _('Forecast flow');
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
            'Description' => Object::TYPE_STRING,
            'Amount' => Object::TYPE_DECIMAL,
            'BeginDate' => Object::TYPE_DATE,
            'EndDate' => Object::TYPE_DATE,
            'Active' => Object::TYPE_BOOL,
            'Currency' => 'Currency');
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
            'FlowTypeItem'=>array(
                'linkClass'     => 'FlowTypeItem',
                'field'         => 'FromForecastFlow',
                'linkTable'     => 'ForecastFlowFlowTypeItem',
                'linkField'     => 'ToFlowTypeItem',
                'multiplicity'  => 'manytomany',
                'bidirectional' => 0
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
        return array('searchform', 'grid', 'add', 'edit', 'del');
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
            'Description'=>array(
                'label'        => _('Description'),
                'shortlabel'   => _('Description'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Amount'=>array(
                'label'        => _('Amount'),
                'shortlabel'   => _('Amount'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 2
            ),
            'BeginDate'=>array(
                'label'        => _('Begin date'),
                'shortlabel'   => _('Begin date'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'EndDate'=>array(
                'label'        => _('End date'),
                'shortlabel'   => _('End date'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Active'=>array(
                'label'        => _('Active'),
                'shortlabel'   => _('Active'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Currency'=>array(
                'label'        => _('Currency'),
                'shortlabel'   => _('Currency'),
                'usedby'       => array('grid', 'addedit', 'searchform'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'FlowTypeItem'=>array(
                'label'        => _('Expenses and receipts models lines'),
                'shortlabel'   => _('Expenses and receipts models lines'),
                'usedby'       => array('addedit'),
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