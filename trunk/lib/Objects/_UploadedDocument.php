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

class _UploadedDocument extends Object {
    
    // Constructeur {{{

    /**
     * _UploadedDocument::__construct()
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
     * _UploadedDocument::getName
     *
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_Name;
    }

    /**
     * _UploadedDocument::setName
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setName($value) {
        $this->_Name = $value;
    }

    // }}}
    // Type foreignkey property + getter/setter {{{

    /**
     * Type foreignkey
     *
     * @access private
     * @var mixed object UploadedDocumentType or integer
     */
    private $_Type = false;

    /**
     * _UploadedDocument::getType
     *
     * @access public
     * @return object UploadedDocumentType
     */
    public function getType() {
        if (is_int($this->_Type) && $this->_Type > 0) {
            $mapper = Mapper::singleton('UploadedDocumentType');
            $this->_Type = $mapper->load(
                array('Id'=>$this->_Type));
        }
        return $this->_Type;
    }

    /**
     * _UploadedDocument::getTypeId
     *
     * @access public
     * @return integer
     */
    public function getTypeId() {
        if ($this->_Type instanceof UploadedDocumentType) {
            return $this->_Type->getId();
        }
        return (int)$this->_Type;
    }

    /**
     * _UploadedDocument::setType
     *
     * @access public
     * @param object UploadedDocumentType $value
     * @return void
     */
    public function setType($value) {
        if (is_numeric($value)) {
            $this->_Type = (int)$value;
        } else {
            $this->_Type = $value;
        }
    }

    // }}}
    // Comment string property + getter/setter {{{

    /**
     * Comment string property
     *
     * @access private
     * @var string
     */
    private $_Comment = '';

    /**
     * _UploadedDocument::getComment
     *
     * @access public
     * @return string
     */
    public function getComment() {
        return $this->_Comment;
    }

    /**
     * _UploadedDocument::setComment
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setComment($value) {
        $this->_Comment = $value;
    }

    // }}}
    // MimeType foreignkey property + getter/setter {{{

    /**
     * MimeType foreignkey
     *
     * @access private
     * @var mixed object MimeType or integer
     */
    private $_MimeType = false;

    /**
     * _UploadedDocument::getMimeType
     *
     * @access public
     * @return object MimeType
     */
    public function getMimeType() {
        if (is_int($this->_MimeType) && $this->_MimeType > 0) {
            $mapper = Mapper::singleton('MimeType');
            $this->_MimeType = $mapper->load(
                array('Id'=>$this->_MimeType));
        }
        return $this->_MimeType;
    }

    /**
     * _UploadedDocument::getMimeTypeId
     *
     * @access public
     * @return integer
     */
    public function getMimeTypeId() {
        if ($this->_MimeType instanceof MimeType) {
            return $this->_MimeType->getId();
        }
        return (int)$this->_MimeType;
    }

    /**
     * _UploadedDocument::setMimeType
     *
     * @access public
     * @param object MimeType $value
     * @return void
     */
    public function setMimeType($value) {
        if (is_numeric($value)) {
            $this->_MimeType = (int)$value;
        } else {
            $this->_MimeType = $value;
        }
    }

    // }}}
    // Customer foreignkey property + getter/setter {{{

    /**
     * Customer foreignkey
     *
     * @access private
     * @var mixed object Actor or integer
     */
    private $_Customer = false;

    /**
     * _UploadedDocument::getCustomer
     *
     * @access public
     * @return object Actor
     */
    public function getCustomer() {
        if (is_int($this->_Customer) && $this->_Customer > 0) {
            $mapper = Mapper::singleton('Actor');
            $this->_Customer = $mapper->load(
                array('Id'=>$this->_Customer));
        }
        return $this->_Customer;
    }

    /**
     * _UploadedDocument::getCustomerId
     *
     * @access public
     * @return integer
     */
    public function getCustomerId() {
        if ($this->_Customer instanceof Actor) {
            return $this->_Customer->getId();
        }
        return (int)$this->_Customer;
    }

    /**
     * _UploadedDocument::setCustomer
     *
     * @access public
     * @param object Actor $value
     * @return void
     */
    public function setCustomer($value) {
        if (is_numeric($value)) {
            $this->_Customer = (int)$value;
        } else {
            $this->_Customer = $value;
        }
    }

    // }}}
    // ActivatedChainTask foreignkey property + getter/setter {{{

    /**
     * ActivatedChainTask foreignkey
     *
     * @access private
     * @var mixed object ActivatedChainTask or integer
     */
    private $_ActivatedChainTask = false;

    /**
     * _UploadedDocument::getActivatedChainTask
     *
     * @access public
     * @return object ActivatedChainTask
     */
    public function getActivatedChainTask() {
        if (is_int($this->_ActivatedChainTask) && $this->_ActivatedChainTask > 0) {
            $mapper = Mapper::singleton('ActivatedChainTask');
            $this->_ActivatedChainTask = $mapper->load(
                array('Id'=>$this->_ActivatedChainTask));
        }
        return $this->_ActivatedChainTask;
    }

    /**
     * _UploadedDocument::getActivatedChainTaskId
     *
     * @access public
     * @return integer
     */
    public function getActivatedChainTaskId() {
        if ($this->_ActivatedChainTask instanceof ActivatedChainTask) {
            return $this->_ActivatedChainTask->getId();
        }
        return (int)$this->_ActivatedChainTask;
    }

    /**
     * _UploadedDocument::setActivatedChainTask
     *
     * @access public
     * @param object ActivatedChainTask $value
     * @return void
     */
    public function setActivatedChainTask($value) {
        if (is_numeric($value)) {
            $this->_ActivatedChainTask = (int)$value;
        } else {
            $this->_ActivatedChainTask = $value;
        }
    }

    // }}}
    // UserAccount foreignkey property + getter/setter {{{

    /**
     * UserAccount foreignkey
     *
     * @access private
     * @var mixed object UserAccount or integer
     */
    private $_UserAccount = false;

    /**
     * _UploadedDocument::getUserAccount
     *
     * @access public
     * @return object UserAccount
     */
    public function getUserAccount() {
        if (is_int($this->_UserAccount) && $this->_UserAccount > 0) {
            $mapper = Mapper::singleton('UserAccount');
            $this->_UserAccount = $mapper->load(
                array('Id'=>$this->_UserAccount));
        }
        return $this->_UserAccount;
    }

    /**
     * _UploadedDocument::getUserAccountId
     *
     * @access public
     * @return integer
     */
    public function getUserAccountId() {
        if ($this->_UserAccount instanceof UserAccount) {
            return $this->_UserAccount->getId();
        }
        return (int)$this->_UserAccount;
    }

    /**
     * _UploadedDocument::setUserAccount
     *
     * @access public
     * @param object UserAccount $value
     * @return void
     */
    public function setUserAccount($value) {
        if (is_numeric($value)) {
            $this->_UserAccount = (int)$value;
        } else {
            $this->_UserAccount = $value;
        }
    }

    // }}}
    // CreationDate datetime property + getter/setter {{{

    /**
     * CreationDate int property
     *
     * @access private
     * @var string
     */
    private $_CreationDate = 0;

    /**
     * _UploadedDocument::getCreationDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getCreationDate($format = false) {
        return $this->dateFormat($this->_CreationDate, $format);
    }

    /**
     * _UploadedDocument::setCreationDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setCreationDate($value) {
        $this->_CreationDate = $value;
    }

    // }}}
    // LastModificationDate datetime property + getter/setter {{{

    /**
     * LastModificationDate int property
     *
     * @access private
     * @var string
     */
    private $_LastModificationDate = 0;

    /**
     * _UploadedDocument::getLastModificationDate
     * Retourne la date au format défini par $format,
     * si celui-ci n'est pas défini (false) le format
     * est celui retourné par la base de données.
     *
     * @access public
     * @param string format
     * @return mixed
     */
    public function getLastModificationDate($format = false) {
        return $this->dateFormat($this->_LastModificationDate, $format);
    }

    /**
     * _UploadedDocument::setLastModificationDate
     *
     * @access public
     * @param string $value
     * @return void
     */
    public function setLastModificationDate($value) {
        $this->_LastModificationDate = $value;
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
        return 'UploadedDocument';
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
        return _('Document');
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
            'Type' => 'UploadedDocumentType',
            'Comment' => Object::TYPE_TEXT,
            'MimeType' => 'MimeType',
            'Customer' => 'Actor',
            'ActivatedChainTask' => 'ActivatedChainTask',
            'UserAccount' => 'UserAccount',
            'CreationDate' => Object::TYPE_DATETIME,
            'LastModificationDate' => Object::TYPE_DATETIME);
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
            'Name'=>array(
                'label'        => _('Name'),
                'shortlabel'   => _('Name'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => true,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Type'=>array(
                'label'        => _('Document type'),
                'shortlabel'   => _('Document type'),
                'usedby'       => array('searchform', 'grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Comment'=>array(
                'label'        => _('Comment'),
                'shortlabel'   => _('Comment'),
                'usedby'       => array('addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'MimeType'=>array(
                'label'        => _('File type'),
                'shortlabel'   => _('File type'),
                'usedby'       => array('grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'Customer'=>array(
                'label'        => _('Customer'),
                'shortlabel'   => _('Customer'),
                'usedby'       => array('grid', 'addedit'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'ActivatedChainTask'=>array(
                'label'        => _('Task'),
                'shortlabel'   => _('Task'),
                'usedby'       => array('grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'CreationDate'=>array(
                'label'        => _('Upload date'),
                'shortlabel'   => _('Upload date'),
                'usedby'       => array('grid'),
                'required'     => false,
                'inplace_edit' => false,
                'add_button'   => false,
                'section'      => ''
            ),
            'LastModificationDate'=>array(
                'label'        => _('Last modification date'),
                'shortlabel'   => _('Last modification date'),
                'usedby'       => array('grid'),
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