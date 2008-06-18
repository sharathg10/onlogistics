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

class GridColumnActionWishedDate extends AbstractGridColumn {
    /**
     * Constructor
     * @access protected
     */
    function GridColumnActionWishedDate($title = '', $params = array()) {
        parent::__construct($title, $params);
        if (isset($params['Limit'])) {
            $this->_limit = $params['Limit'];
        }
        $this->sortable = false;
    }
    /**
     * Determine la colonne pour laquelle la classe est instanciee:
     * 'Prochaine visite', ou 'Visite dès' (week -2), ou 'Date limite' (week +2)
     *
     * @var string $_method
     **/
    var $_limit = '';

    /**
     * GridColumnActionWishedDate::render()
     *
     * @param Object $object un Actor
     * @return string
     **/
    function render($object) {
        $ActionMapper = Mapper::singleton('Action');
        $ActionColl = $ActionMapper->loadCollection(
                array('Actor' => $object->getId(),
                      'State' => Action::ACTION_STATE_TODO),
                array('WishedDate' => SORT_ASC));
        if (!Tools::isEmptyObject($ActionColl)) {
            $Action = $ActionColl->getItem(0);
            $marge = 0;  // 604800: nb de secondes dans 1 semaine

            if ($this->_limit == 'begin') {
                $marge = -604800 * 2;
            }elseif ($this->_limit == 'end') {
                $marge = 604800 * 2;
            }
            return _('Week ')
                    . date('W', $Action->getWishedDate('timestamp') + $marge);
        }
        return _('N/A');
    }
}

?>