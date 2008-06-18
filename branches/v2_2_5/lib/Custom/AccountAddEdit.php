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

require_once('Objects/FlowType.php');

/**
 * constantes
 */
define('I_NOT_DELETED_ACCOUNT', _('The following account could not be deleted because it is associated either to a VAT or an accounting type or an event type: %s'));

define('I_NOT_DELETED_ACCOUNTS',  _('The following accounts could not be deleted because they are associated either to a VAT or an accounting type or an event type: %s'));

/**
 * AccountAddEdit
 *
 */
class AccountAddEdit extends GenericAddEdit {

    private $_chargeSelectName = 'advmultiselectAccount_FlowTypeItem_Charges';
    private $_recetteSelectName = 'advmultiselectAccount_FlowTypeItem_Recettes';
    private $_notDeletedAccount = array();

    // UsserAccountAddEdit::__construct() {{{

    /**
     * Constructor
     *
     * @param array $params
     * @access public
     */
    public function __construct($params) {
        parent::__construct($params);
        $this->addJSRequirements(
            'js/includes/AccountAddEdit.js'
        );
    }

    // }}}
    // AccountAddEdit::renderFlowTypeItem() {{{
    
    /**
     * renderFlowTypeItem 
     * 
     * @access public
     * @return void
     */
    public function renderFlowTypeItem() {
        // select des charges
        $elt = HTML_QuickForm::createElement(
            'advmultiselect', $this->_chargeSelectName,
            array(_('Expense types'), _('Available expense types'), _('assigned')),
            SearchTools::createArrayIDFromCollection('FlowTypeItem',
                array('FlowType.Type'=>FlowType::CHARGE)),
            array('size'=>8, 'style'=>'width:100%;'));
        $this->form->addElement($elt);
        // select des recettes
        $elt = HTML_QuickForm::createElement(
            'advmultiselect', $this->_recetteSelectName,
            array(_('Receipt types'), _('Available receipt types'), _('assigned')),
            SearchTools::createArrayIDFromCollection('FlowTypeItem',
                array('FlowType.Type'=>FlowType::RECETTE)),
            array('size'=>8, 'style'=>'width:400px;'));
        $this->form->addElement($elt);

        // valeurs par défaut en mode édition
        $this->formDefaults[$this->_chargeSelectName] = 
            $this->object->getFlowTypeItemCollectionIds(array('FlowType.Type'=>FlowType::CHARGE));
        $this->formDefaults[$this->_recetteSelectName] = 
            $this->object->getFlowTypeItemCollectionIds(array('FlowType.Type'=>FlowType::RECETTE));
    }

    // }}}
    // AccountAddEdit::onAfterHandlePostData() {{{
    
    /**
     * onBeforeHandlePostData 
     * 
     * @access public
     * @return void
     */
    public function onAfterHandlePostData() {
        if(isset($_POST['Account_BreakdownType']) 
        && $_POST['Account_BreakdownType'] != Account::BREAKDOWN_TVA) {
            $this->object->setTVA(false);
        }
        // on récupère les flowtypeItem séparé en charges / recettes
        $detailCharges = !empty($_POST[$this->_chargeSelectName])?
            $_POST[$this->_chargeSelectName]:array();
        $detailRecettes = !empty($_POST[$this->_recetteSelectName])?
            $_POST[$this->_recetteSelectName]:array();
        unset($_POST[$this->_chargeSelectName], 
            $_POST[$this->_recetteSelectName]);
        $flowTypeItems = array_merge($detailCharges, $detailRecettes);
        $this->object->setFlowTypeItemCollectionIds($flowTypeItems);

        // A partir des Account_FlowTypeItem on crée les Account_FlowType
        $accountFlowType = array();
        foreach ($flowTypeItems as $id) {
            $flowTypeItem = Object::load('FlowTypeItem', $id);
            $flowTypeId = $flowTypeItem->getFlowTypeId();
            if(!in_array($flowTypeId, $accountFlowType)) {
                $accountFlowType[] = $flowTypeId;
            }
        }
        $this->object->setFlowTypeCollectionIds($accountFlowType);
    }

    // }}}
    // AccountAddEdit::onBeforeDisplay() {{{
    
    public function onBeforeDisplay() {
        $defaultCurrency = Object::load('Currency', array('Name'=>'Euro'));
        $this->formDefaults['Account_Currency_ID'] =  $this->object->getId() ? 
            $this->object->getCurrencyId() : $defaultCurrency->getId();
        $this->form->setDefaults($this->formDefaults);
    }
    
    // }}}
    // AccountAddEdit::renderBreakdownType() {{{
    
    public function renderBreakdownType() {
        $label = _('Type of breaking down');
        $ename = 'Account_BreakdownType';
        $elt = HTML_QuickForm::createElement('select', $ename, $label,
            Account::getBreakdownTypeConstArray(), 
            'id="'.$ename.'" onChange="updateTVASelect();" style="width:100%;"');
        $elt->setAttribute('class', $elt->getAttribute('class') . ' required_element');
        $this->form->addElement($elt);
            
        $msg = sprintf(E_VALIDATE_FIELD . ' "%s" ' . E_VALIDATE_IS_REQUIRED, $label);
        $this->form->addRule($ename, $msg, 'required', '', 'client');
        $this->form->addRule($ename, $msg, 'required');
        
        $this->formDefaults[$ename] = 
            $this->object->getBreakdownType();
    }
    
    // }}}
    // AccountAddEdit::renderOwner() {{{

    public function renderOwner() {
        $label = _('Linked to the accounting plan of');
        $ename = 'Account_Owner_ID';
        if($this->auth->getProfile()==UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT) {
            $arr = SearchTools::createArrayIDFromCollection(
                'Actor', array('Id'=>$this->auth->getActorId()));
        } else {
            $arr = SearchTools::createArrayIDFromCollection(
                'Actor', array('Generic'=>0));
        }
        $elt = HTML_QuickForm::createElement('select', $ename, 
            $label, $arr, 'id="'.$ename.'" style="width:100%;"');
        $elt->setAttribute('class', $elt->getAttribute('class') . ' required_element');
        $this->form->addElement($elt);
            
        $msg = sprintf(E_VALIDATE_FIELD . ' "%s" ' . E_VALIDATE_IS_REQUIRED, $label);
        $this->form->addRule($ename, $msg, 'required', '', 'client');
        $this->form->addRule($ename, $msg, 'required');

        $owner = $this->object->getOwner();
        $owner = $owner instanceof Actor ? 
            $owner : $this->auth->getDatabaseOwner();
        $this->formDefaults[$ename] = $owner->getId();
    }

    // }}}
}

?>
