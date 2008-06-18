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

class GridColumnProductSubstitutionLink extends AbstractGridColumn {
    /**
     * Constructor
     * 
     * @access protected 
     */
    function GridColumnProductSubstitutionLink($title = '', $params = array()) {
        parent::__construct($title, $params);
    } 

    function Render($object) {
        $Interchangeable = $object->GetInterchangeable();
        $selected0 = $selected1 = '';
        $select = "<input type=\"hidden\" name=\"HiddenPdtSubstId[]\" value=\"" . $object->GetId() . "\"> 
		<select name=\"Interchangeable[]\">";

        if ($Interchangeable == -1) {
            $select .= "<option value=\"-1\">"._('Link type')."</option>";
        } elseif ($Interchangeable == 0 || $Interchangeable == 1) {
            $selected0 = ($Interchangeable == 0)?"selected":"";
            $selected1 = ($Interchangeable == 1)?"selected":"";
        } elseif ($Interchangeable <> -1) {
            return "Erreur";
        } 
        $select .= "
		<option value=\"0\" " . $selected0 . ">". _('Replaceable')."</option>
		<option value=\"1\" " . $selected1 . ">"._('Interchangeable')."</option>
		</select>";
        return $select;
    } 
} 

?>