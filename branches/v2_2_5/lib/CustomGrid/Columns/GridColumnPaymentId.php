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

class GridColumnPaymentId extends AbstractGridColumn {
    /**
     * Permet d'afficher l'Id, avec une info s'il a ete annule
     *
     * @access private
     */
    /**
     * Constructor
     *
     * @access protected
     */
    function __construct($title = array(), $params = array()) {
        parent::__construct($title, $params);
    }

    function render($object) { // $object est de type Payment
        $CancellationDate = $object->getCancellationDate();
        $Id = $object->getId();
        if ($CancellationDate == "0000-00-00 00:00:00" || empty($CancellationDate)) {
            return $Id;
        }
        $CancellationDate = I18N::formatDate($CancellationDate, I18N::DATE_SHORT);
        $result = $Id . '<a href="javascript:void(0);" title="'
            . sprintf(_('This payment was delete on %s'), $CancellationDate)
            . '" style="text-decoration:none"><font color="#FF0000"><b>*</b></font></a>';

        return $result;
    }
}

?>
