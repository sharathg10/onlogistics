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
 * I18nString class
 *
 */
class I18nString extends Object {
    
    // Constructeur {{{

    /**
     * I18nString::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // StringValue_en_GB string property + getter/setter {{{

    /**
     * StringValue_en_GB string property
     *
     * @access private
     * @var string
     */
    private $_StringValue_en_GB = '';

    /**
     * I18nString::getStringValue_en_GB
     *
     * @access public
     * @return string
     */
    public function getStringValue_en_GB() {
        return $this->_StringValue_en_GB;
    }

    /**
     * I18nString::setStringValue_en_GB
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStringValue_en_GB($value) {
        $this->_StringValue_en_GB = $value;
    }

    // }}}
    // StringValue_fr_FR string property + getter/setter {{{

    /**
     * StringValue_fr_FR string property
     *
     * @access private
     * @var string
     */
    private $_StringValue_fr_FR = '';

    /**
     * I18nString::getStringValue_fr_FR
     *
     * @access public
     * @return string
     */
    public function getStringValue_fr_FR() {
        return $this->_StringValue_fr_FR;
    }

    /**
     * I18nString::setStringValue_fr_FR
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStringValue_fr_FR($value) {
        $this->_StringValue_fr_FR = $value;
    }

    // }}}
    // StringValue_de_DE string property + getter/setter {{{

    /**
     * StringValue_de_DE string property
     *
     * @access private
     * @var string
     */
    private $_StringValue_de_DE = '';

    /**
     * I18nString::getStringValue_de_DE
     *
     * @access public
     * @return string
     */
    public function getStringValue_de_DE() {
        return $this->_StringValue_de_DE;
    }

    /**
     * I18nString::setStringValue_de_DE
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStringValue_de_DE($value) {
        $this->_StringValue_de_DE = $value;
    }

    // }}}
    // StringValue_nl_NL string property + getter/setter {{{

    /**
     * StringValue_nl_NL string property
     *
     * @access private
     * @var string
     */
    private $_StringValue_nl_NL = '';

    /**
     * I18nString::getStringValue_nl_NL
     *
     * @access public
     * @return string
     */
    public function getStringValue_nl_NL() {
        return $this->_StringValue_nl_NL;
    }

    /**
     * I18nString::setStringValue_nl_NL
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStringValue_nl_NL($value) {
        $this->_StringValue_nl_NL = $value;
    }

    // }}}
    // StringValue_tr_TR string property + getter/setter {{{

    /**
     * StringValue_tr_TR string property
     *
     * @access private
     * @var string
     */
    private $_StringValue_tr_TR = '';

    /**
     * I18nString::getStringValue_tr_TR
     *
     * @access public
     * @return string
     */
    public function getStringValue_tr_TR() {
        return $this->_StringValue_tr_TR;
    }

    /**
     * I18nString::setStringValue_tr_TR
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStringValue_tr_TR($value) {
        $this->_StringValue_tr_TR = $value;
    }

    // }}}
    // StringValue_pl_PL string property + getter/setter {{{

    /**
     * StringValue_pl_PL string property
     *
     * @access private
     * @var string
     */
    private $_StringValue_pl_PL = '';

    /**
     * I18nString::getStringValue_pl_PL
     *
     * @access public
     * @return string
     */
    public function getStringValue_pl_PL() {
        return $this->_StringValue_pl_PL;
    }

    /**
     * I18nString::setStringValue_pl_PL
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setStringValue_pl_PL($value) {
        $this->_StringValue_pl_PL = $value;
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
        return 'I18nString';
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
            'StringValue_en_GB' => Object::TYPE_TEXT,
            'StringValue_fr_FR' => Object::TYPE_TEXT,
            'StringValue_de_DE' => Object::TYPE_TEXT,
            'StringValue_nl_NL' => Object::TYPE_TEXT,
            'StringValue_tr_TR' => Object::TYPE_TEXT,
            'StringValue_pl_PL' => Object::TYPE_TEXT);
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