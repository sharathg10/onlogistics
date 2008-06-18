<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * $Source: /home/cvs/codegen/codegentemplates.py,v $
 *
 * Ceci est un fichier généré, NE PAS EDITER.
 *
 * @copyright 2002-2006 ATEOR - All rights reserved
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