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

class GenericActorAddEdit extends GenericAddEdit {
    // GenericActorAddEdit::__construct() {{{

    /**
     * Constructor
     *
     * @param array $params
     * @access public
     */
    public function __construct($params) {
        $params['title'] = _('Add or update generic actor');
        $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
        parent::__construct($params);
    }

    // }}}
    // GenericActorAddEdit::onBeforeHandlePostData() {{{

    /**
     * Surchargée ici pour mettre la propriété Generic à true.
     *
     * @access protected
     * @return void
     */
    protected function onBeforeHandlePostData()
    {
        $this->object->setGeneric(true);
    }

    // }}}
    // GenericActorAddEdit::onAfterHandlePostData() {{{

    /**
     * Appelée avant sauvegarde
     *
     * @access public
     * @return void
     */
    public function onAfterHandlePostData()
    {
        // checke que le nom n'est pas déjà utilisé
        $obj = Object::load('Actor', array('Name'=>$_POST['Actor_Name']));
        if ($obj instanceof Actor && $obj->getId() != $this->objID) {
            Template::errorDialog(
                sprintf(
                    _('An actor with the name "%s" already exists, please correct.'),
                    $this->object->getName()
                ), 
                $this->url
            );
            exit(1);
        }
        $obj = Object::load('Actor', array('Code'=>$_POST['Actor_Code']));
        if ($obj instanceof Actor && $obj->getId() != $this->objID) {
            Template::errorDialog(
                sprintf(
                    _('An actor with the code "%s" already exists, please correct.'),
                    $this->object->getCode()
                ), 
                $this->url
            );
            exit(1);
        }
    }

    // }}}
    // GenericActorAddEdit::onBeforeDelete() {{{

    /**
     * appelée avant suppression
     *
     * @access public
     * @return void
     */
    public function onBeforeDelete()
    {
        $col = Object::loadcollection('Actor', array('GenericActor'=>$this->objID));
        if ($col->getCount() > 0) {
            Template::errorDialog(
                sprintf(
                    _('Selected actors could not be modified because they are the generic actors of: %s'),
                    $col->toString()
                ), 
                $this->guessReturnURL()
            );
            exit(1);
        }
    }

    // }}}
    // GenericActorAddEdit::getFeatures() {{{

    /**
     * Surchargée ici pour retourner les features spécifiques.
     *
     * @access protected
     * @return array
     */
    protected function getFeatures() {
        return array('grid', 'add', 'edit', 'del');
    }

    // }}}
    // GenericActorAddEdit::getAddEditMapping() {{{

    /**
     * Surchargée ici pour retourner un mapping spécifique.
     *
     * @access protected
     * @return array
     */
    protected function getAddEditMapping()
    {
        return array(
            ''=>array(
               'Name'=>array('label'=>_('Name'), 'required'=>true),
               'Code'=>array('label'=>_('Code'), 'required'=>true)
            ) 
        );
    }

    // }}}
}

?>
