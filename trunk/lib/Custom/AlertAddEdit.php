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

class AlertAddEdit extends GenericAddEdit {
    // AlertAddEdit::__construct() {{{

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct($params) {
        parent::__construct($params);
    }

    // }}}
    // AlertAddEdit::onBeforeDisplay() {{{

    /**
     * onBeforeDisplay 
     * 
     * @access protected
     * @return void
     */
    protected function onBeforeDisplay() {
        require_once('Objects/Alert.const.php');
        $this->initialize();
        // Alert_Name est sujet a l'i18n => non modifiable!!
        $this->form->getElement('Alert_Name')->setAttribute('readonly', 'readonly');
        $staticDatas = getAlertContent($this->object->getId());
        $this->assignTemplateVar($staticDatas);
    }

    // }}}
    // AlertAddEdit::render() {{{

    /**
     * render 
     * 
     * @access public
     * @return void
     */
    public function render() {
        parent::render('Alert/AlertAddEdit.html');
    }

    // }}}
    // AlertAddEdit::additionalFormContent() {{{

    /**
     * additionalContent 
     * 
     * @access public
     * @return void
     */
    public function additionalFormContent() {
        $uacCollection = $this->object->getUserAccountCollection(array(),
            array('Identity'=>SORT_ASC), array('Identity'));

        $grid = new Grid();
        $grid->withNoCheckBox = true;
        $grid->withNoSortableColumn = true;
        $grid->newColumn('FieldMapper', '', array('Macro'=>'%Identity%'));
        return "<tr><th colspan=\"2\">" .
            _('Users set to receive this alert') .
            "</th><tr>\n" .
            "<tr><td colspan=\"2\">\n" .
            $grid->render($uacCollection, false, array(), array(), 'GridLite.html') .
            "</td></tr>";
    }

    // }}}
}

?>
