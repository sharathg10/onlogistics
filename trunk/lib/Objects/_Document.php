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
 * _Document class
 *
 */
class _Document extends Object {
    // class constants {{{

    const TYPE_TXT = 0;
    const TYPE_PDF = 1;
    const TYPE_CSV = 2;

    // }}}
    // Constructeur {{{

    /**
     * _Document::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Type const property + getter/setter/getTypeConstArray {{{

    /**
     * Type int property
     *
     * @access private
     * @var integer
     */
    private $_Type = 0;

    /**
     * _Document::getType
     *
     * @access public
     * @return integer
     */
    public function getType() {
        return $this->_Type;
    }

    /**
     * _Document::setType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setType($value) {
        if ($value !== null) {
            $this->_Type = (int)$value;
        }
    }

    /**
     * _Document::getTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getTypeConstArray($keys = false) {
        $array = array(
            _Document::TYPE_TXT => _("text/plain"), 
            _Document::TYPE_PDF => _("application/pdf"), 
            _Document::TYPE_CSV => _("text/csv")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // Data string property + getter/setter {{{

    /**
     * Data string property
     *
     * @access private
     * @var string
     */
    private $_Data = '';

    /**
     * _Document::getData
     *
     * @access public
     * @return string
     */
    public function getData() {
        return $this->_Data;
    }

    /**
     * _Document::setData
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setData($value) {
        $this->_Data = $value;
    }

    // }}}
    // AbstractDocument one to one relation getter {{{
    /**
     * _Document::getAbstractDocument
     *
     * @access public
     * @return object AbstractDocument
     */
    public function getAbstractDocument() {
        $mapper = Mapper::singleton('AbstractDocument');
        return $mapper->load(array('Document'=>$this->getId()));
    }

    /**
     * _Document::getAbstractDocumentId
     *
     * @access public
     * @return integer
     */
    public function getAbstractDocumentId() {
        $return = $this->getAbstractDocument();
        if ($return instanceof AbstractDocument) {
            return $return->getId();
        }
        return 0;
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
        return 'Document';
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
            'Type' => Object::TYPE_CONST,
            'Data' => Object::TYPE_LONGTEXT);
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