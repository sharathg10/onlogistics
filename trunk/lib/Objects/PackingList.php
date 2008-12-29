<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IMPORTANT: This is a generated file, please do not edit.
 *
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

/**
 * PackingList class
 *
 * Class containing addon methods.
 */
class PackingList extends _PackingList {
    // Constructeur {{{

    /**
     * PackingList::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // getLogo() {{{

    /**
     * Retourne le logo (sous forme base64), ou une string vide.
     *
     * @access public
     * @return string 
     */
    function getLogo() {
        $dm = $this->getDocumentModel();
        if (!$dm instanceof DocumentModel) {
            return '';
        }
        $boxCol = $this->getBoxCollection();
        $box    = $boxCol->getItem(0);
        switch($dm->getLogoType()) {
            case DocumentModel::EXPEDITOR:
                $actor = $box->getExpeditor();
                break;
            case DocumentModel::DESTINATOR: 
                $actor = $box->getDestinator();
                break;
            case DocumentModel::ONE_ACTOR: 
                $actor = $dm->getActor();
                break;
            default:
                return '';
        }
        if ($actor instanceof Actor) { 
            return $actor->getLogo();
        }
        return '';
    }

    // }}}
    // getCommandCollection() {{{

    /**
     * Retourne la collection de commandes de la packinglist.
     *
     * @access public
     * @return Collection 
     */
    function getCommandCollection() {
        $boxCol = $this->getBoxCollection();
        $cmdCol = new Collection('ProductCommand', false);
        foreach ($boxCol as $box) {
            $cmi = $box->getCommandItem();
            if ($cmi instanceof CommandItem) {
                $cmdCol->setItem($cmi->getCommand());
            }
        }
        return $cmdCol;
    }

    // }}}

}

?>