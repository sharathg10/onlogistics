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

class UserAccountAddEdit extends GenericAddEdit {
    // UserAccountAddEdit::__construct() {{{

    /**
     * __construct 
     * 
     * @param mixed $params 
     * @access public
     * @return void
     */
	public function __construct($params) {
        $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
        $params['use_session'] = true;
		parent::__construct($params);
        $this->addJSRequirements(
            'JS_AjaxTools.php');
    }

    // }}}
    // UserAccountAddEdit::handleLogin() {{{

    /**
     * Methode qui gère l'affichage du input password
     *
     * @param  string $data
     * @access public
     * @return void
     */
    public function handleLogin($data) {
        $errorMsg = _('A user with the username "%s" already exists, please correct.');
        $existing = Object::load('UserAccount', array('Login'=>$data));
        if ($existing instanceof UserAccount && 
            $existing->getId() != $this->object->getId()) {
            Template::errorDialog(sprintf($errorMsg, $data), $this->url);
            exit(1);
        }
        $this->object->setLogin($data);
    }

    // }}}
    // UserAccountAddEdit::onBeforeDelete() {{{

    /**
     * Vérifie que l'acteur sélectionné est ok pour être supprimé.
     *
     * @access public
     * @return void
     */
    public function onBeforeDelete() {
        $ids = $this->objID;
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $auth = Auth::singleton();
        foreach ($ids as $id) {
            // on ne peut supprimer l'utilisateur connecté
            if ($id == $auth->getUserId()) {
                Template::errorDialog(
                    _('You cannot delete the connected user.'), 
                    $this->guessReturnURL()
                );
                exit(1);
            } else if ($id == ROOT_USERID) {
                Template::errorDialog(
                    _('You cannot delete the root user.'), 
                    $this->guessReturnURL()
                );
                exit(1);
            }
        }
        return true;
    }

    // }}}
    // UserAccountAddEdit::onBeforeHandlePostData() {{{

    /**
     * Vérifie que l'acteur sélectionné est ok pour les profils de type client 
     * aéronautique.
     *
     * @access public
     * @return void
     */
    public function onBeforeHandlePostData() {
        // si profile client aeronautique l'acteur doit être un AeroCustomer
        if($_POST['UserAccount_Profile'] == UserAccount::PROFILE_AERO_CUSTOMER) {
            $actorTest = Object::load('Actor', $_POST['UserAccount_Actor_ID']);
            if(!($actorTest instanceof AeroCustomer)) {
                Template::errorDialog(_('a user with a aeronautical profile must have a related actor whose type is aeronautical.'), $this->url);
                exit();
            }
        }
    }

    // }}}
    // UserAccountAddEdit::renderActor() {{{

    /**
     * renderActor 
     * 
     * @access public
     * @return void
     */
    public function renderActor() {
        $elt = HTML_QuickForm::createElement('select', 'UserAccount_Actor_ID', _('Actor'), 
            SearchTools::createArrayIDFromCollection('Actor'), 'id="UserAccount_Actor_ID" onchange="fw.ajax.updateSelect(\'UserAccount_Actor_ID\', \'__advmultiselectUserAccount_Site_IDs\', \'Site\', \'Owner\');"');
        $this->form->addElement($elt);
        $this->formDefaults['UserAccount_Actor_ID'] = $this->object->getActorId();
    }

    // }}}
    // UserAccountAddEdit::renderSite() {{{

    /**
     * renderSite 
     * 
     * @access public
     * @return void
     */
    public function getFilterForSite() {
        return array('Owner'=>$this->object->getActorId());
    }

    // }}}
}

?>
