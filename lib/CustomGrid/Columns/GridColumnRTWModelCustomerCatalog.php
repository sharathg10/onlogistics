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
 * @version   SVN: $Id: GridColumnProductCommandPriceWithDiscount.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

class GridColumnRTWModelCustomerCatalog extends AbstractGridColumn
{
    protected $actor = false;

    /**
     * Constructor
     *
     * @access protected
     */
    function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
        if (isset($params['actor'])) {
            $this->actor = $params['actor'];
        }
    }

    public function render($object) {
        $col = $object->getRTWProductCollection(
            array('Activated' => 1, 'Affected' => 1),
            array('Size.Name' => SORT_ASC),
            array('Size')
        );
        $ths = '';
        $tds = '';
        $modelId = $object->getId();
        $thTpl = '<th><input type="checkbox" id="cb_%s" name="gridItems[]" value="%s" onclick="fw.grid.handleCBDeselect(this);cbUnselected(this);updateLineTotal(%s);"%s/>%s</th>';
        $tdTpl = '<td><input type="text" size="2" class="qty_item" name="qty_%s" id="qty_%s" value="%s" onkeyup="checkUncheck(%s);updateLineTotal(%s);"/></td>';
        foreach ($col as $item) {
            $id = $item->getId();
            $qty = (isset($_SESSION['catalogQties'][$id]))? $_SESSION['catalogQties'][$id] : '';
            $checked = $qty > 0 ? ' checked="checked"' : '';
            if ($item->getSizeId()) {
                $ths .= sprintf($thTpl, $id, $id, $modelId, $checked, $item->getSize()->getName());
                $tds .= sprintf($tdTpl, $id, $id, $qty, $id, $modelId);
            }
        }
        return sprintf(
            '<div>%s</div><table id="table_%s" border="0"><tr>%s</tr><tr>%s</tr></table>',
            $object->getDescription(),
            $modelId,
            $ths,
            $tds
        );
    }
}

?>
