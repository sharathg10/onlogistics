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

class ForecastFlowAddEdit extends GenericAddEdit {
    /**
     * onBeforeDisplay 
     * 
     * @access protected
     * @return void
     */
    protected function onBeforeDisplay() {
        // euro par default
        $this->formDefaults['ForecastFlow_Currency_ID'] = 1;
        $this->form->setDefaults($this->formDefaults);
    }

    /**
     * onAfterHandlePostData 
     * 
     * @access protected
     * @return void
     */
    protected function onAfterHandlePostData() {
        $d1 = explode('-', $this->object->getBeginDate());
        $d2 = explode('-', $this->object->getEndDate());
        $ts1 = mktime(0,0,0, $d1[1], $d1[2], $d1[0]);
        $ts2 = mktime(0,0,0, $d2[1], $d2[2], $d2[0]);
        if($d1[1] != $d2[1] || $d1[0] != $d2[0]) {
            $m1 = $d1[1];
            $m2 = $d2[1];
            $y1 = $d1[0];
            $y2 = $d2[0];
            // on modifie la date de fin de la charge existante
            $ts = mktime(23,59,59, $m1, date('t', $ts1), $y1);
            $date = date('Y-m-d', $ts);
            $this->object->setEndDate($date);
            // les mois/années sont différent on crée une prévision par mois
            while($m1 != $m2 || $y1 != $y2) {
                // calule les dates
                $d1 = date('Y-m-d', $ts+1);
                $ts = mktime(23,59,59, date('m', $ts+1), date('t', $ts+1), date('Y', $ts+1));
                $d2 = date('Y-m-d', $ts);
                
                // crée l'objet
                $o = Tools::duplicateObject($this->object);
                $o->setBeginDate($d1);
                $o->setEndDate($d2);
                $o->setFlowTypeItemCollection($this->object->getFlowTypeItemCollection());
                $o->save();

                // réinitialise les var
                $d2 = explode('-', $d2);
                $m1 = $d2[1];
                $y1 = $d2[0];
            }
        }
    }
}

?>
