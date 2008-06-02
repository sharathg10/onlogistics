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

define('I_NOT_DELETED_ENTITY', _('The following product caracteristic could not be deleted because it is associated to a product or a seasonality: %s'));
define('I_NOT_DELETED_ENTITIES',  _('The following product caracteristics could not be deleted because they are associated to a product or a seasonality: %s'));
define('I_ALREADY_EXISTS',  _('A product caracteristic with this name and this type already exists, please correct.'));

class ProductKindAddEdit extends GenericAddEdit {
    
    private $_notDeletedEntity = array();
    
    // ProductKindAddEdit::__construct() {{{

    /**
     * __construct 
     * 
     * @param mixed $params 
     * @access public
     * @return void
     */
	public function __construct($params) {
		parent::__construct($params);
    }
    
    // }}}
    // GenericActorAddEdit::onAfterHandlePostData() {{{

    /**
     * Appelee avant sauvegarde pour verifier l'unicite du couple 
     * (Name, ProductKind)
     *
     * @access public
     * @return void
     */
    public function onAfterHandlePostData()
    {
        // checke que le nom n'est pas déjà utilisé
        $obj = Object::load('ProductKind', 
                array('Name' => $_POST['ProductKind_Name'], 
                      'ProductType' => $_POST['ProductKind_ProductType_ID']));
        if ($obj instanceof ProductKind && $obj->getId() != $this->objID) {
            Template::errorDialog(I_ALREADY_EXISTS, $this->url);
            exit(1);
        }
    }

    // }}}
    // ProductKindAddEdit::onBeforeDelete() {{{

    /**
     * Vérifie que la ProductKind n'est pas utilisee par:
     * une Saisonnality, une Promotion, une PropertyValue.
     *
     * @access public
     * @return void or boolean true
     */
    public function onBeforeDelete() {
        require_once('SQLRequest.php');
        $ids = $this->objID;
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $rs = request_usedProductKindIds();
        $usedProductKindIds = array();
        $okForDelete = array();
        
        while ($rs && !$rs->EOF) {
    		$usedProductKindIds[] = $rs->fields['pkId'];
    		$rs->moveNext();
    	}
    	
    	$this->objID = array_diff($ids, $usedProductKindIds);
    	$notOkForDelete = array_intersect($ids, $usedProductKindIds);
    	foreach ($notOkForDelete as $pkId) {
    		$ProductKind = Object::load('ProductKind', $pkId);
    		$this->_notDeletedEntity[] = $ProductKind->getName();
    	}
        return true;
    }

    // }}}
    // ProductKindAddEdit::onAfterDelete() {{{

    /**
     * onAfterDelete 
     * 
     * @access protected
     * @return void
     */
    protected function onAfterDelete() {
        // redirige vers un message d'info
        $msg = false;
        if (count($this->_notDeletedEntity) == 1) {
            $msg = sprintf(I_NOT_DELETED_ENTITY, $this->_notDeletedEntity[0]);
        } else if (count($this->_notDeletedEntity) > 1) {
            $str = "<ul><li>" . implode("</li><li>", $this->_notDeletedEntity) . "</li></ul>"; 
            $msg = sprintf(I_NOT_DELETED_ENTITIES, $str);
        }

        if($msg) {
            Template::infoDialog($msg, $this->guessReturnURL());
            exit();
        }
    }

    // }}}
}

?>