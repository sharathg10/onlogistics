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
 * @version   SVN: $Id: DocumentAppendixAddEdit.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * DocumentAppendixAddEdit
 *
 */
class DocumentAppendixAddEdit extends GenericAddEdit {
    // DocumentAppendixAddEdit::__construct() {{{

    /**
     * Constructeur
     *
     * @param array $params
     * @access public
     * @return void
     */
    public function __construct($params) {
        parent::__construct($params);
    }

    // }}}
    // DocumentAppendixAddEdit::onBeforeHandlePostData() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function onBeforeHandlePostData() {
        $titleSet  = isset($_REQUEST['DocumentAppendix_Title']) 
                     && !empty($_REQUEST['DocumentAppendix_Title']);
        $bodySet   = isset($_REQUEST['DocumentAppendix_Body']) 
                     && !empty($_REQUEST['DocumentAppendix_Body']);
        $imgSet    = isset($_FILES['DocumentAppendix_Image']) 
                     && $_FILES['DocumentAppendix_Image']['error'] == 0;
        // check if not both title/body and image have been filled
        if ($imgSet && ($titleSet || $bodySet)) {
            Template::errorDialog(_('You must provide either a title and body or an image, but not both.'));
            exit(1);
        }
        if ($imgSet) {
            if (strtolower($_FILES['DocumentAppendix_Image']['type']) != 'image/png') {
                Template::errorDialog(_('Images must be in "png" format'));
                exit(1);
            }
            if (intval($_FILES['DocumentAppendix_Image']['size']) > 1000000) {
                Template::errorDialog(_('Images must not exceed 1 megaoctet'));
                exit(1);
            }
        }
    }

    // }}}
}

?>
