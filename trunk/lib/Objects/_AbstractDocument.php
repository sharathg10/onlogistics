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
 * _AbstractDocument class
 *
 */
class _AbstractDocument extends Object {
    // class constants {{{

    const TYPE_SUPPLIER_PRODUCT = 1;
    const TYPE_SUPPLIER_TRANSPORT = 2;
    const TYPE_SUPPLIER_COURSE = 3;
    const TYPE_SUPPLIER_PRESTATION = 4;
    const TYPE_CUSTOMER_PRODUCT = 5;
    const TYPE_CUSTOMER_TRANSPORT = 6;
    const TYPE_CUSTOMER_COURSE = 7;
    const TYPE_CUSTOMER_PRESTATION = 8;

    // }}}
    // Constructeur {{{

    /**
     * _AbstractDocument::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // DocumentNo string property + getter/setter {{{

    /**
     * DocumentNo string property
     *
     * @access private
     * @var string
     */
    private $_DocumentNo = '';

    /**
     * _AbstractDocument::getDocumentNo
     *
     * @access public
     * @return string
     */
    public function getDocumentNo() {
        return $this->_DocumentNo;
    }

    /**
     * _AbstractDocument::setDocumentNo
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setDocumentNo($value) {
        $this->_DocumentNo = $value;
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
     * _AbstractDocument::getEditionDate
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
     * _AbstractDocument::setEditionDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setEditionDate($value) {
        $this->_EditionDate = $value;
    }

    // }}}
    // Command foreignkey property + getter/setter {{{

    /**
     * Command foreignkey
     *
     * @access private
     * @var mixed object Command or integer
     */
    private $_Command = false;

    /**
     * _AbstractDocument::getCommand
     *
     * @access public
     * @return object Command
     */
    public function getCommand() {
        if (is_int($this->_Command) && $this->_Command > 0) {
            $mapper = Mapper::singleton('Command');
            $this->_Command = $mapper->load(
                array('Id'=>$this->_Command));
        }
        return $this->_Command;
    }

    /**
     * _AbstractDocument::getCommandId
     *
     * @access public
     * @return integer
     */
    public function getCommandId() {
        if ($this->_Command instanceof Command) {
            return $this->_Command->getId();
        }
        return (int)$this->_Command;
    }

    /**
     * _AbstractDocument::setCommand
     *
     * @access public
     * @param object Command $value
     * @return void
     */
    public function setCommand($value) {
        if (is_numeric($value)) {
            $this->_Command = (int)$value;
        } else {
            $this->_Command = $value;
        }
    }

    // }}}
    // CommandType const property + getter/setter/getCommandTypeConstArray {{{

    /**
     * CommandType int property
     *
     * @access private
     * @var integer
     */
    private $_CommandType = 0;

    /**
     * _AbstractDocument::getCommandType
     *
     * @access public
     * @return integer
     */
    public function getCommandType() {
        return $this->_CommandType;
    }

    /**
     * _AbstractDocument::setCommandType
     *
     * @access public
     * @param integer $value
     * @return void
     */
    public function setCommandType($value) {
        if ($value !== null) {
            $this->_CommandType = (int)$value;
        }
    }

    /**
     * _AbstractDocument::getCommandTypeConstArray
     * Retourne un tableau associatif avec la valeur de la constante en
     * clef et sa représentation textuelle en valeur.
     * Si $keys vaut true, seules les clefs sont retournées
     *
     * @access public
     * @static
     * @param boolean $keys
     * @return array
     */
    public static function getCommandTypeConstArray($keys = false) {
        $array = array(
            _AbstractDocument::TYPE_SUPPLIER_PRODUCT => _("Supplier order"), 
            _AbstractDocument::TYPE_SUPPLIER_TRANSPORT => _("Carriage service"), 
            _AbstractDocument::TYPE_SUPPLIER_COURSE => _("Class booking"), 
            _AbstractDocument::TYPE_SUPPLIER_PRESTATION => _("Service order"), 
            _AbstractDocument::TYPE_CUSTOMER_PRODUCT => _("Customer order"), 
            _AbstractDocument::TYPE_CUSTOMER_TRANSPORT => _("Carriage service"), 
            _AbstractDocument::TYPE_CUSTOMER_COURSE => _("Class booking"), 
            _AbstractDocument::TYPE_CUSTOMER_PRESTATION => _("Service order")
        );
        asort($array);
        return $keys?array_keys($array):$array;
    }

    // }}}
    // DocumentModel foreignkey property + getter/setter {{{

    /**
     * DocumentModel foreignkey
     *
     * @access private
     * @var mixed object DocumentModel or integer
     */
    private $_DocumentModel = false;

    /**
     * _AbstractDocument::getDocumentModel
     *
     * @access public
     * @return object DocumentModel
     */
    public function getDocumentModel() {
        if (is_int($this->_DocumentModel) && $this->_DocumentModel > 0) {
            $mapper = Mapper::singleton('DocumentModel');
            $this->_DocumentModel = $mapper->load(
                array('Id'=>$this->_DocumentModel));
        }
        return $this->_DocumentModel;
    }

    /**
     * _AbstractDocument::getDocumentModelId
     *
     * @access public
     * @return integer
     */
    public function getDocumentModelId() {
        if ($this->_DocumentModel instanceof DocumentModel) {
            return $this->_DocumentModel->getId();
        }
        return (int)$this->_DocumentModel;
    }

    /**
     * _AbstractDocument::setDocumentModel
     *
     * @access public
     * @param object DocumentModel $value
     * @return void
     */
    public function setDocumentModel($value) {
        if (is_numeric($value)) {
            $this->_DocumentModel = (int)$value;
        } else {
            $this->_DocumentModel = $value;
        }
    }

    // }}}
    // SupplierCustomer foreignkey property + getter/setter {{{

    /**
     * SupplierCustomer foreignkey
     *
     * @access private
     * @var mixed object SupplierCustomer or integer
     */
    private $_SupplierCustomer = false;

    /**
     * _AbstractDocument::getSupplierCustomer
     *
     * @access public
     * @return object SupplierCustomer
     */
    public function getSupplierCustomer() {
        if (is_int($this->_SupplierCustomer) && $this->_SupplierCustomer > 0) {
            $mapper = Mapper::singleton('SupplierCustomer');
            $this->_SupplierCustomer = $mapper->load(
                array('Id'=>$this->_SupplierCustomer));
        }
        return $this->_SupplierCustomer;
    }

    /**
     * _AbstractDocument::getSupplierCustomerId
     *
     * @access public
     * @return integer
     */
    public function getSupplierCustomerId() {
        if ($this->_SupplierCustomer instanceof SupplierCustomer) {
            return $this->_SupplierCustomer->getId();
        }
        return (int)$this->_SupplierCustomer;
    }

    /**
     * _AbstractDocument::setSupplierCustomer
     *
     * @access public
     * @param object SupplierCustomer $value
     * @return void
     */
    public function setSupplierCustomer($value) {
        if (is_numeric($value)) {
            $this->_SupplierCustomer = (int)$value;
        } else {
            $this->_SupplierCustomer = $value;
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
     * _AbstractDocument::getCurrency
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
     * _AbstractDocument::getCurrencyId
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
     * _AbstractDocument::setCurrency
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
    // AccountingTypeActor foreignkey property + getter/setter {{{

    /**
     * AccountingTypeActor foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_AccountingTypeActor = false;

    /**
     * _AbstractDocument::getAccountingTypeActor
     *
     * @access public
     * @return object Actor
     */
    public function getAccountingTypeActor() {
        if (is_int($this->_AccountingTypeActor) && $this->_AccountingTypeActor > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_AccountingTypeActor = $mapper->load(
                array('Id'=>$this->_AccountingTypeActor));
        }
        return $this->_AccountingTypeActor;
    }

    /**
     * _AbstractDocument::getAccountingTypeActorId
     *
     * @access public
     * @return integer
     */
    public function getAccountingTypeActorId() {
        if ($this->_AccountingTypeActor instanceof Actor) {
            return $this->_AccountingTypeActor->getId();
        }
        return (int)$this->_AccountingTypeActor;
    }

    /**
     * _AbstractDocument::setAccountingTypeActor
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setAccountingTypeActor($value) {
        if (is_numeric($value)) {
            $this->_AccountingTypeActor = (int)$value;
        } else {
            $this->_AccountingTypeActor = $value;
        }
    }

    // }}}
    // PDFDocument foreignkey property + getter/setter {{{

    /**
     * PDFDocument foreignkey
     *
     * @access private
     * @var mixed object PDFDocument or integer
     */
    private $_PDFDocument = false;

    /**
     * _AbstractDocument::getPDFDocument
     *
     * @access public
     * @return object PDFDocument
     */
    public function getPDFDocument() {
        if (is_int($this->_PDFDocument) && $this->_PDFDocument > 0) {
            $mapper = Mapper::singleton('PDFDocument');
            $this->_PDFDocument = $mapper->load(
                array('Id'=>$this->_PDFDocument));
        }
        return $this->_PDFDocument;
    }

    /**
     * _AbstractDocument::getPDFDocumentId
     *
     * @access public
     * @return integer
     */
    public function getPDFDocumentId() {
        if ($this->_PDFDocument instanceof PDFDocument) {
            return $this->_PDFDocument->getId();
        }
        return (int)$this->_PDFDocument;
    }

    /**
     * _AbstractDocument::setPDFDocument
     *
     * @access public
     * @param object PDFDocument $value
     * @return void
     */
    public function setPDFDocument($value) {
        if (is_numeric($value)) {
            $this->_PDFDocument = (int)$value;
        } else {
            $this->_PDFDocument = $value;
        }
    }

    // }}}
    // Locale string property + getter/setter {{{

    /**
     * Locale string property
     *
     * @access private
     * @var string
     */
    private $_Locale = '';

    /**
     * _AbstractDocument::getLocale
     *
     * @access public
     * @return string
     */
    public function getLocale() {
        return $this->_Locale;
    }

    /**
     * _AbstractDocument::setLocale
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setLocale($value) {
        $this->_Locale = $value;
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
        return 'AbstractDocument';
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
            'DocumentNo' => Object::TYPE_STRING,
            'EditionDate' => Object::TYPE_DATETIME,
            'Command' => 'Command',
            'CommandType' => Object::TYPE_CONST,
            'DocumentModel' => 'DocumentModel',
            'SupplierCustomer' => 'SupplierCustomer',
            'Currency' => 'Currency',
            'AccountingTypeActor' => 'Actor',
            'PDFDocument' => 'PDFDocument',
            'Locale' => Object::TYPE_STRING);
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
            'PDFDocument'=>array(
                'linkClass'     => 'PDFDocument',
                'field'         => 'AbstractDocument',
                'ondelete'      => 'cascade',
                'multiplicity'  => 'onetoone'
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
        return true;
    }

    // }}}
    // _AbstractDocument::mutate() {{{

    /**
     * "Mutation" d'un objet parent en classe fille et vice-versa.
     * Cela permet par exemple dans un formulaire de modifier la classe d'un
     * objet via un select.
     *
     * @access public
     * @param string type le type de l'objet vers lequel 'muter'
     * @return object
     **/
    public function mutate($type){
        // on instancie le bon objet
        require_once('Objects/' . $type . '.php');
        $mutant = new $type();
        if(!($mutant instanceof _AbstractDocument)) {
            trigger_error('Invalid classname provided.', E_USER_ERROR);
        }
        // propriétés fixes
        $mutant->hasBeenInitialized = $this->hasBeenInitialized;
        $mutant->readonly = $this->readonly;
        $mutant->setId($this->getId());
        // propriétés simples
        $properties = $this->getProperties();
        foreach($properties as $property=>$type){
            $getter = 'get' . $property;
            $setter = 'set' . $property;
            if (method_exists($mutant, $setter)) {
                $mutant->$setter($this->$getter());
            }
        }
        // relations
        $links = $this->getLinks();
        foreach($links as $property=>$data){
            $getter = 'get' . $property . 'Collection';
            $setter = 'set' . $property . 'Collection';
            if (method_exists($mutant, $setter)) {
                $mutant->$setter($this->$getter());
            }
        }
        return $mutant;
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
        return $this->getDocumentNo();
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
        return 'DocumentNo';
    }

    // }}}
}

?>