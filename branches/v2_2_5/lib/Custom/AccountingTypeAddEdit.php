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

define('I_NOT_DELETED_ACCOUNTINGTYPE', _('The following accounting type could not be deleted because it is associated either to a customer or to an account: %s'));
define('I_NOT_DELETED_ACCOUNTINGTYPES', _('The following accounting types could not be deleted because they are associated either to a customer or to an account: %s'));

/**
 * AccountingTypeAddEdit
 *
 */
class AccountingTypeAddEdit extends GenericAddEdit {

    private $_notDeletedAccountingType = array();

    // AccountingTypeAddEdit::__construct() {{{

    /**
     * Constructor
     *
     * @param array $params
     * @access public
     */
    public function __construct($params) {
        parent::__construct($params);
    }

    // }}}
    // AccountingTypeAddEdit::additionalFormContent() {{{

    /**
     * additionalContent 
     * 
     * @access public
     * @return void
     */
    public function additionalFormContent() {
        if($this->object->getId() > 0) {
            $grid = new Grid();
            $grid->withNoCheckBox = true;
            $grid->withNoSortableColumn = true;
            $grid->newColumn('FieldMapper', _('Name'), array('Macro'=>'%Name%'));
            return "<tr><th colspan=\"4\">" .
                _('List of actors associated to the accounting model') .
                "</th><tr>\n" .
                "<tr><td colspan=\"4\">\n" .
                $grid->render('Actor', false,
                    array('AccountingType' => $this->object->getId()),
                    array('Name' => SORT_ASC),
                    'GridLite.html') .
                "</td></tr>";
        }
    }

    // }}}
    // AccountingTypeAddEdit::onBeforeDelete() {{{

    /**
     * onBeforeDelete 
     * 
     * @access protected
     * @return void
     */
    protected function onBeforeDelete() {
        $accountingTypeMapper = Mapper::singleton('AccountingType');
        $accountingTypeCol = $accountingTypeMapper->loadCollection(
			array('Id' => $this->objID));
        //verifie que l'accountingType n'est lié à aucun Account
        $accountMapper = Mapper::singleton('Account');
        $accountCol = $accountMapper->loadCollection();
        $jcount = $accountCol->getcount();
					  
        //pour la vérification dans la boucle
        $actorMapper = Mapper::singleton('Actor');

        $okForDelete = array();
        $count = $accountingTypeCol->getCount();
        for($i=0 ; $i<$count ; $i++){
            $delete = true;
            $accountingType = $accountingTypeCol->getItem($i);
	        //Vérifie q'un accountingType n'est pas lié à un acteur
	        $actorCol = $actorMapper->loadCollection(
                array('AccountingType'=>$accountingType->getId()));
    
            for($j=0 ; $j<$jcount ; $j++) {
                $account = $accountCol->getItem($j);
                $actCol = $account->getAccountingTypeCollectionIds();
                if(in_array($accountingType->getId(), $actCol)) {
                    $delete = false;
                }
            }
    
	        if(Tools::isEmptyObject($actorCol) && $delete) {
                //on peut supprimer l'accountingType
                $okForDelete[] = $accountingType->getId();
            } else {
	            //ajout de l'accounting dans le tableau des non suprimées
                $this->_notDeletedAccountingType[] = $accountingType->getType();
	        }
        }
        $this->objID = $okForDelete;
    }

    // }}}
    // AccountingTypeAddEdit::onAfterDelete() {{{

    /**
     * onAfterDelete 
     * 
     * @access protected
     * @return void
     */
    protected function onAfterDelete() {
        // redirige vers un message d'info
        $msg = false;
        if (count($this->_notDeletedAccountingType) == 1) {
            $msg = sprintf(I_NOT_DELETED_ACCOUNTINGTYPE, $this->_notDeletedAccountingType[0]);
        } else if (count($this->_notDeletedAccountingType) > 1) {
            $str = "<ul><li>" . implode("</li><li>", $this->_notDeletedAccountingType) . "</li></ul>"; 
            $msg = sprintf(I_NOT_DELETED_ACCOUNTINGTYPES, $str);
        }

        if($msg) {
            Template::infoDialog($msg, $this->guessReturnURL());
            exit();
        }
    }

    // }}}
    // AccountingTypeAddEdit::onBeforeHandlePostData() {{{
    
    protected function onBeforeHandlePostData() {
        if(isset($_POST['AccountingType_MainModel']) && $_POST['AccountingType_MainModel']==1) {
            $o = Object::load('AccountingType', array('MainModel'=>true));
            if($o instanceof AccountingType) {
                $o->setMainModel(false);
                $o->save();
            }
        }
    } 
    
    // }}}
}

?>
