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

require_once 'UploadedDocumentManager.php';
require_once 'AlertSender.php';

/**
 * UploadedDocumentAddEdit
 */
class UploadedDocumentAddEdit extends GenericAddEdit
{
    // Properties {{{

    /**
     * Copie avant suppression pour avoir les infos de l'objet.
     *
     * @var object $objectCollection
     * @access protected
     */
    var $objectCollection = false;

    // }}}
    // UploadedDocumentAddEdit::onBeforeDisplay() {{{

    /**
     * Appelée avant sauvegarde
     *
     * @access public
     * @return void
     */
    public function onBeforeDisplay()
    {
        $this->form->addElement('file', 'file', _('File to upload'),
            array('style="width:100%;"'));
    }

    // }}}
    // UploadedDocumentAddEdit::getFilterForCustomer() {{{

    /**
     * Pour éliminer du select les acteurs génériques et non actifs
     *
     * @access public
     * @return void
     */
    public function getFilterForCustomer()
    {
        return array('Generic' => false, 'Active' => true);
    }

    // }}}
    // UploadedDocumentAddEdit::onAfterHandlePostData() {{{

    /**
     * Appelée avant sauvegarde
     *
     * @access public
     * @return void
     */
    public function onAfterHandlePostData()
    {
        if (isset($_FILES['file']) && is_array($_FILES['file']) 
            && $_FILES['file']['size'] > 0) {
            try {
                $uploader = new UploadedDocumentManager('file');
                $uploader->store($this->object);
            } catch (Exception $exc) {
                Template::errorDialog($exc->getMessage(), $this->guessReturnURL());
                exit(1);
            }
        }
        $this->object->setUserAccount($this->auth->getUserId());
        $this->object->setLastModificationDate(date('Y-m-d H:i:s', time()));
        if ($this->action == self::FEATURE_ADD) {
            $this->object->setCreationDate(date('Y-m-d H:i:s', time()));
            AlertSender::send_ALERT_GED_DOCUMENT_UPLOADED($this->object);
        } else {
            AlertSender::send_ALERT_GED_DOCUMENT_UPDATED($this->object);
        }
    }

    // }}}
    // UploadedDocumentAddEdit::onBeforeDelete() {{{

    /**
     * Appelée avant sauvegarde
     *
     * @access public
     * @return void
     */
    public function onBeforeDelete()
    {
        $this->objectCollection = Object::loadCollection(
            'UploadedDocument', 
            array('Id' => $this->objID)
        );
    }

    // }}}
    // UploadedDocumentAddEdit::onAfterDelete() {{{

    /**
     * Appelée avant sauvegarde
     *
     * @access public
     * @return void
     */
    public function onAfterDelete()
    {
        foreach ($this->objectCollection as $item) {
            AlertSender::send_ALERT_GED_DOCUMENT_DELETED($item);
        }
    }

    // }}}
}

?>
