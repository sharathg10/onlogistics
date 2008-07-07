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
 * PriceByCurrency class
 *
 */
class PriceByCurrency extends Object {
    
    // Constructeur {{{

    /**
     * PriceByCurrency::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // RecommendedPrice float property + getter/setter {{{

    /**
     * RecommendedPrice float property
     *
     * @access private
     * @var float
     */
    private $_RecommendedPrice = 0;

    /**
     * PriceByCurrency::getRecommendedPrice
     *
     * @access public
     * @return float
     */
    public function getRecommendedPrice() {
        return $this->_RecommendedPrice;
    }

    /**
     * PriceByCurrency::setRecommendedPrice
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setRecommendedPrice($value) {
        if ($value !== null) {
            $this->_RecommendedPrice = round(I18N::extractNumber($value), 2);
        }
    }

    // }}}
    // Price float property + getter/setter {{{

    /**
     * Price float property
     *
     * @access private
     * @var float
     */
    private $_Price = 0;

    /**
     * PriceByCurrency::getPrice
     *
     * @access public
     * @return float
     */
    public function getPrice() {
        return $this->_Price;
    }

    /**
     * PriceByCurrency::setPrice
     *
     * @access public
     * @param float $value
     * @return void
     */
    public function setPrice($value) {
        if ($value !== null) {
            $this->_Price = round(I18N::extractNumber($value), 2);
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
     * PriceByCurrency::getCurrency
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
     * PriceByCurrency::getCurrencyId
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
     * PriceByCurrency::setCurrency
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
    // Product foreignkey property + getter/setter {{{

    /**
     * Product foreignkey
     *
     * @access private
     * @var mixed object Product or integer
     */
    private $_Product = false;

    /**
     * PriceByCurrency::getProduct
     *
     * @access public
     * @return object Product
     */
    public function getProduct() {
        if (is_int($this->_Product) && $this->_Product > 0) {
            $mapper = Mapper::singleton('Product');
            $this->_Product = $mapper->load(
                array('Id'=>$this->_Product));
        }
        return $this->_Product;
    }

    /**
     * PriceByCurrency::getProductId
     *
     * @access public
     * @return integer
     */
    public function getProductId() {
        if ($this->_Product instanceof Product) {
            return $this->_Product->getId();
        }
        return (int)$this->_Product;
    }

    /**
     * PriceByCurrency::setProduct
     *
     * @access public
     * @param object Product $value
     * @return void
     */
    public function setProduct($value) {
        if (is_numeric($value)) {
            $this->_Product = (int)$value;
        } else {
            $this->_Product = $value;
        }
    }

    // }}}
    // ActorProduct foreignkey property + getter/setter {{{

    /**
     * ActorProduct foreignkey
     *
     * @access private
     * @var mixed object ActorProduct or integer
     */
    private $_ActorProduct = false;

    /**
     * PriceByCurrency::getActorProduct
     *
     * @access public
     * @return object ActorProduct
     */
    public function getActorProduct() {
        if (is_int($this->_ActorProduct) && $this->_ActorProduct > 0) {
            $mapper = Mapper::singleton('ActorProduct');
            $this->_ActorProduct = $mapper->load(
                array('Id'=>$this->_ActorProduct));
        }
        return $this->_ActorProduct;
    }

    /**
     * PriceByCurrency::getActorProductId
     *
     * @access public
     * @return integer
     */
    public function getActorProductId() {
        if ($this->_ActorProduct instanceof ActorProduct) {
            return $this->_ActorProduct->getId();
        }
        return (int)$this->_ActorProduct;
    }

    /**
     * PriceByCurrency::setActorProduct
     *
     * @access public
     * @param object ActorProduct $value
     * @return void
     */
    public function setActorProduct($value) {
        if (is_numeric($value)) {
            $this->_ActorProduct = (int)$value;
        } else {
            $this->_ActorProduct = $value;
        }
    }

    // }}}
    // PricingZone foreignkey property + getter/setter {{{

    /**
     * PricingZone foreignkey
     *
     * @access private
     * @var mixed object PricingZone or integer
     */
    private $_PricingZone = false;

    /**
     * PriceByCurrency::getPricingZone
     *
     * @access public
     * @return object PricingZone
     */
    public function getPricingZone() {
        if (is_int($this->_PricingZone) && $this->_PricingZone > 0) {
            $mapper = Mapper::singleton('PricingZone');
            $this->_PricingZone = $mapper->load(
                array('Id'=>$this->_PricingZone));
        }
        return $this->_PricingZone;
    }

    /**
     * PriceByCurrency::getPricingZoneId
     *
     * @access public
     * @return integer
     */
    public function getPricingZoneId() {
        if ($this->_PricingZone instanceof PricingZone) {
            return $this->_PricingZone->getId();
        }
        return (int)$this->_PricingZone;
    }

    /**
     * PriceByCurrency::setPricingZone
     *
     * @access public
     * @param object PricingZone $value
     * @return void
     */
    public function setPricingZone($value) {
        if (is_numeric($value)) {
            $this->_PricingZone = (int)$value;
        } else {
            $this->_PricingZone = $value;
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
        return 'PriceByCurrency';
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
        return _('Prix');
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
            'RecommendedPrice' => Object::TYPE_DECIMAL,
            'Price' => Object::TYPE_DECIMAL,
            'Currency' => 'Currency',
            'Product' => 'Product',
            'ActorProduct' => 'ActorProduct',
            'PricingZone' => 'PricingZone');
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
            'RecommendedPrice'=>array(
                'label'        => _('Recommended price'),
                'shortlabel'   => _('Recommended price'),
                'usedby'       => array('grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 2
            ),
            'Price'=>array(
                'label'        => _('Price'),
                'shortlabel'   => _('Price'),
                'usedby'       => array('grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => '',
                'dec_num'      => 2
            ),
            'Currency'=>array(
                'label'        => _('Currency'),
                'shortlabel'   => _('Currency'),
                'usedby'       => array('grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Product'=>array(
                'label'        => _('Product'),
                'shortlabel'   => _('Product'),
                'usedby'       => array('grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ActorProduct'=>array(
                'label'        => _('ActorProduct'),
                'shortlabel'   => _('ActorProduct'),
                'usedby'       => array('grid', 'searchform'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'PricingZone'=>array(
                'label'        => _('Pricing zone'),
                'shortlabel'   => _('Pricing zone'),
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