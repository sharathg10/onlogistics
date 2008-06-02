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

class ActorProductAddEdit extends GenericAddEdit {

    // ActorProductAddEdit::renderActor() {{{
    /**
     * @access public
     * @return void
     */
    public function renderActor() {
        $elt = HTML_QuickForm::createElement(
                'select', 'ActorProduct_Actor_ID', _('Customer'),
                SearchTools::createArrayIDFromCollection(
                        'Actor',
                        array('ClassName' => array('Customer', 'AeroCustomer'),
                              'Active' => 1), MSG_SELECT_AN_ELEMENT),
                        'id="ActorProduct_Actor_ID" width="100%"');
        $elt->setAttribute('class', 'select required_element');
        $this->form->addElement($elt);
        $this->formDefaults['ActorProduct_Actor_ID'] = $this->object->getActorId();
        // ajoute une validation numérique (!= '##')
        $msg = sprintf(
                E_VALIDATE_FIELD . ' "%s" ' . E_VALIDATE_IS_REQUIRED,
                _('Customer'));
        $this->form->addRule('ActorProduct_Actor_ID', $msg, 'numeric', '', 'client');
        // Juste pour afficher l'asterisque:
        $this->form->addRule('ActorProduct_Actor_ID', $msg, 'required', '', 'client');
    }
    // }}}
    // ActorProductAddEdit::renderProduct() {{{

    /**
     * @access public
     * @return void
     */
    public function renderProduct() {
        $elt = HTML_QuickForm::createElement(
                'select', 'ActorProduct_Product_ID', _('Product'),
                SearchTools::createArrayIDFromCollection(
                        'Product',
                        array('Activated' => 1), MSG_SELECT_AN_ELEMENT, 'BaseReference'),
                        'id="ActorProduct_Product_ID"');
        $elt->setAttribute('class', 'select required_element');
        $this->form->addElement($elt);
        $this->formDefaults['ActorProduct_Product_ID'] = $this->object->getProductId();
        // ajoute une validation numérique (!= '##')
        $msg = sprintf(
                E_VALIDATE_FIELD . ' "%s" ' . E_VALIDATE_IS_REQUIRED,
                _('Product'));
        $this->form->addRule('ActorProduct_Product_ID', $msg, 'numeric', '', 'client');
        // Juste pour afficher l'asterisque:
        $this->form->addRule('ActorProduct_Product_ID', $msg, 'required', '', 'client');
    }

    // }}}
    // ActorProductAddEdit::onAfterHandlePostData() {{{

    /**
     * Appelée avant sauvegarde:
     * test l'unicité du couple d'attributs (Actor, Product)
     *
     * @access public
     * @return void
     */
    public function onAfterHandlePostData()
    {
        $test = Object::load(
                'ActorProduct',
                array('Actor' => $_POST['ActorProduct_Actor_ID'],
                      'Product' => $_POST['ActorProduct_Product_ID']));
        if ($test instanceof ActorProduct && $test->getId() != $this->objID) {
            Template::errorDialog(
                _('This product has already a reference for the selected customer, please correct.'),
                $this->url
            );
            exit();
        }
    }

    // }}}
    // ActorProductAddEdit::onBeforeDelete() {{{

    /**
     * onBeforeDelete
     *
     * @access protected
     * @return void

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

    // }}}*/
}

?>