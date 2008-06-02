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

class TVAAddEdit extends GenericAddEdit {
    // GenericActorAddEdit::onBeforeDelete() {{{

    /**
     * appelée avant suppression
     *
     * @access public
     * @return void
     */
    public function onBeforeDelete()
    {
        require_once 'SQLRequest.php';
        try {
            if (is_array($this->objID)) {
                foreach ($this->objID as $id) {
                    request_tvaIsDeletable($id);
                }
            } else {
                request_tvaIsDeletable($this->objID);
            }
        } catch (Exception $exc) {
            Template::errorDialog($exc->getMessage(), $this->guessReturnURL());
            exit(1);
        }
    }

    // }}}
    // TVAAddEdit::renderType() {{{
    
    public function renderType() {
        $label = '<div class="form_error">*</div>' . _('Type');
        $ename = 'TVA_Type';
        // En mode edition, le type est DISABLED
        $disabled = ($this->action == GenericController::FEATURE_ADD)?'':' disabled="disabled"';
        $elt = HTML_QuickForm::createElement('select', $ename, $label,
            TVA::getTypeConstArray(), 
            'id="'.$ename.'"' . $disabled . ' style="width:100%;"');
        $elt->setAttribute('class', $elt->getAttribute('class') . ' required_element');
        $this->form->addElement($elt);
        $this->formDefaults[$ename] = $this->object->getType();
    }
    
    // }}}
    // TVAAddEdit::renderRate() {{{
    
    public function renderRate() {
        $label = '<div class="form_error">*</div>' . _('Rate');
        $ename = 'TVA_Rate';
        // En mode edition, le Rate est DISABLED
        $disabled = ($this->action == GenericController::FEATURE_ADD)?'':' disabled="disabled"';
        $elt = HTML_QuickForm::createElement('text', $ename, $label,
            'id="'.$ename.'"' . $disabled . ' style="width:100%;"');
        $elt->setAttribute('class', $elt->getAttribute('class') . ' required_element');
        $this->form->addElement($elt);
        $this->formDefaults[$ename] = I18N::formatNumber($this->object->getRate());
    }
    
    // }}}
}

?>