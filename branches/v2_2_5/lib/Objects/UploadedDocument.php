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

class UploadedDocument extends _UploadedDocument {
    // Constructeur {{{

    /**
     * UploadedDocument::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // UploadedDocument::canBeDeleted() {{{

    /**
     * Le document ne peut être supprimé s'il est lié à une ou plusieurs
     * tâches.
     *
     * @access public
     * @return boolean
     */
    public function canBeDeleted() {
        $ack = $this->getActivatedChainTask();
        if ($ack instanceof ActivatedChainTask) {
            throw new Exception(sprintf(
                _('assigned to activated chain task "%s".'),
                Tools::getValueFromMacro($ack, '%Task.Name%')
            ));
        }
        return parent::canBeDeleted();
    }

    // }}}
    // UploadedDocument::getFileName() {{{

    /**
     * Retourne le nom du fichier stocké sur le disque.
     *
     * @access public
     * @return boolean
     */
    public function getFileName() {
        $mimetype = $this->getMimetype();
        if (!($mimetype instanceof MimeType)) {
            // on ne devrait pas être là
            trigger_error('UploadedDocument with id ' . $this->getId()
                . ' does not have a mime type !', E_USER_ERROR);
        }
        return $this->getId() . '.' . $mimetype->getExtension(); 
    }

    // }}}

}

?>