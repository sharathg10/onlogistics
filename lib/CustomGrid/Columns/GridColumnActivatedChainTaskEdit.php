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

require_once('Objects/Task.inc.php');

/**
 * GridColumnActivatedChainTaskEdit
 *
 **/
class GridColumnActivatedChainTaskEdit extends AbstractGridColumn {

    /**
     * Url de retour
     *
     * @var    string $retURL
     * @access protected
     */
    protected $retURL = 'ActivatedChainTaskList.php';

    /**
     * Constructor
     *
     * @access protected
     */
    function GridColumnActivatedChainTaskEdit($title = '', $params = array()) {
        parent::__construct($title, $params);
        $this->sortable = false;
        if (isset($params['retURL'])) {
            $this->retURL = $params['retURL'];
        }
    }

    /**
     * GridColumnActivatedChainTaskEdit::render()
     *
     * @param  ActivatedChainTask $object
     * @return string
     */
    function render($object) {
        $tskID = $object->getTaskId();
        if ($tskID == TASK_FLY_PREPARATION) {
            $url = 'FlightPreparationEdit.php?';
        } else if ($tskID == TASK_FLY) {
            $url = 'FlightEdit.php?';
        } else if ($tskID == TASK_ASSEMBLY || $tskID == TASK_SUIVI_MATIERE) {
            $url = 'AssemblyEdit.php?'; 
        } else if (isProductionTask($object)) {
            $url = 'ProductionTaskValidation.php?';
        } else {
            $url = false;
        }
        if ($url) {
            $ret = sprintf(
                '<a href="%sackId=%s&retURL=%s" title="Executer">%s</a>', 
                $url, $object->getId(), $this->retURL, $object->getTask()->getName()
            );
        } else {
            $ret = $object->getTask()->getName();
        }
        if ($object->getInstructions() != '') {
            $ret .= '&nbsp;(<a href="javascript:void(0);" title="'
                 .  _('Afficher les instructions de travail')
                 .  '" onclick="window.open(\'ActivatedChainTaskInstructions.php?ackId='
                 .  $object->getId() . '\', \'popup\', \'width=800,height=400,scrollbars=yes\');">' 
                 .  _('instructions') . '</a>)';
        }
        return $ret;
    }
}

?>
