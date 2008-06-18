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

class GridColumnCronTaskSchedule extends AbstractGridColumn {
    /**
     * Constructor
     *
     * @return void
     **/
    function __construct($title = '', $params = array()) {
    	parent::__construct($title, $params);
    }

    /**
     *
     * @access public
     * @return string
     **/
    public function render($object){
        // si la tâche est mensuelle
        if ($object->getDayOfMonth() > 0) {
            return sprintf(_('Every %02d of the month at %02dH00'),
                $object->getDayOfMonth(), $object->getHourOfDay());
        }
        // si la tâche hebdomadaire
        $week_array = getDayOfWeekArray();
        if ($object->getDayOfWeek() != -1) {
            return sprintf(_('every  %s at %02dH00'),
                $week_array[$object->getDayOfWeek()], $object->getHourOfDay());
        }
        return sprintf(_('Everyday at %02dH00'), $object->getHourOfDay());
    }

}

?>