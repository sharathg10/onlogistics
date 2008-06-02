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

require_once('Objects/CronTask.inc.php');

/**
 * CronTaskAddEdit
 *
 */
class CronTaskAddEdit extends GenericAddEdit {
    //CronTaskAddEdit::__construct() {{{

    /**
     * __construct 
     * 
     * @param mixed $params 
     * @access public
     * @return void
     */
	public function __construct($params) {
        $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
		parent::__construct($params);
    }

    // }}}
    // CronTaskAddEdit::renderScriptName() {{{
	
    /**
     * renderScriptName 
     * 
     * @access public
     * @return void
     */
    public function renderScriptName() {
	    $elt = HTML_QuickForm::createElement(
            'select',
            'CronTask_ScriptName',
            _('Script') . ':',
            getCronTaskArray());
        $this->form->addElement($elt);
        $this->formDefaults['CronTask_ScriptName'] = $this->object->getScriptName();
	}

    // }}}
    // CronTaskAddEdit::renderDayOfWeek() {{{
	
    /**
     * renderDayOfWeek 
     * 
     * @access public
     * @return void
     */
	public function renderDayOfWeek() {
	    $elt = HTML_QuickForm::createElement(
             'select',
             'CronTask_DayOfWeek',
             _('Day of week') . ':',
             getDayOfWeekArray());
        
        $this->form->addElement($elt);
        $this->formDefaults['CronTask_DayOfWeek'] = 
            $this->object->getDayOfWeek();
    } 
    
    // }}}
    // CronTaskAddEdit::renderDayOfMonth() {{{
	
    /**
     * renderDayOfMonth 
     * 
     * @access public
     * @return void
     */
	public function renderDayOfMonth() {
	    $elt = HTML_QuickForm::createElement(
             'select',
             'CronTask_DayOfMonth',
             _('Day of month') . ':',
             getDayOfMonthArray());
        
        $this->form->addElement($elt);
        $this->formDefaults['CronTask_DayOfMonth'] = 
            $this->object->getDayOfMonth();
    }

    // }}}
    // CronTaskAddEdit::renderHourOfDay() {{{

    /**
     * renderHourOfDay 
     * 
     * @access public
     * @return void
     */
	public function renderHourOfDay() {
	    $elt = HTML_QuickForm::createElement(
             'select',
             'CronTask_HourOfDay',
             _('Hour of day') . ':',
             getHourOfDayArray());
        
        $this->form->addElement($elt);
        $this->formDefaults['CronTask_HourOfDay'] = 
            $this->object->getHourOfDay();
    }

    // }}}
}
?>
