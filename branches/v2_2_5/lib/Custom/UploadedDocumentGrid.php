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

class UploadedDocumentGrid extends GenericGrid
{
    // Constructeur {{{

    /**
     * Constructeur
     *
     * @param array $params tableau de paramètres
     * @return void
     */
    public function __construct($params=array()) {
        $params['itemsperpage'] = 200;
        parent::__construct($params);
    }
    
    // }}} 
    // UploadedDocumentGrid::onAfterBuildSearchForm() {{{

    /**
     * Ajout des champs de SearchForm nécessaires.
     *
     * @access protected
     * @return void
     */
    public function onAfterBuildSearchForm() {
        require_once('Objects/Task.inc.php');
        $tasks = SearchTools::createArrayIDFromCollection(
            'Task',
            array('Id' => getConsultingTaskIds()),
            MSG_SELECT_AN_ELEMENT
        );
        $this->searchForm->addElement(
            'select',
            'TaskId',
            _('Task'),
            array($tasks),
            array('Path'=> 'ActivatedChainTask.Task.Id')
        );
        $this->searchForm->addElement(
            'text',
            'CustomerName',
            _('Customer name'),
            array(),
            array('Path'=> 'Customer.Name')
        );
        $this->searchForm->addElement(
            'text',
            'CommandCommandNo',
            _('Order number'),
            array(),
            array('Path'=> 'ActivatedChainTask.ActivatedOperation.ActivatedChain.CommandItem().Command.CommandNo')
        );
    }

    // }}}
    // UploadedDocumentGrid::renderColumnActivatedChainTask() {{{

    /**
     * Surchargée pour afficher le nom de la tâche et non de l'ack.
     *
     * @access protected
     * @return void
     */
    public function renderColumnActivatedChainTask() {
        return $this->grid->newColumn(
            'FieldMapper',
            _('Task'), 
            array('Macro' => '%ActivatedChainTask.Task.Name|default@%')
        );
    }

    // }}}
    // UploadedDocumentGrid::additionalGridActions() {{{

    /**
     * Actions du grid additionnelles
     *
     * @access protected
     * @return void
     */
    protected function additionalGridActions() {
        $this->grid->newAction(
            'DownloadUploadedDocument',
            array('Caption'=>_('Download'))
        );
        $this->grid->newAction('Redirect', array(
            'Caption' => _('Assign'),
            'URL' => 'dispatcher.php?entity=ActivatedChainTask&amp;udId=%d'
        ));
        $this->grid->newAction('Redirect', array(
            'Caption' => _('Unassign'),
            'URL' => 'UploadedDocumentActivatedChainTask.php?action=unassign&amp;udId=%d'
        ));
    }

    // }}}
}

?>
