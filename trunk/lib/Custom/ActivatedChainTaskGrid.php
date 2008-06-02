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

class ActivatedChainTaskGrid extends GenericGrid {
    // Constructeur {{{

    /**
     * Constructeur
     *
     * @param string $entity nom de l'objet
     * @param array $params tableau de paramètres
     * @return void
     */
    public function __construct($params=array()) {
        $params['itemsperpage'] = 200;
        parent::__construct($params);
        $session = Session::singleton();
        if (isset($_REQUEST['udId'])) {
            $session->register('udId', $_REQUEST['udId'], 2);
        } else if (isset($_SESSION['udId'])) {
            $session->prolong('udId', 2);
        }
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
        $this->searchForm->addElement(
            'text',
            'CommandNo',
            _('Order number'),
            array(),
            array('Path'=> 'ActivatedOperation.ActivatedChain.CommandItem().Command.CommandNo')
        );
        $this->searchForm->addElement(
            'text',
            'Customer',
            _('Customer'),
            array(),
            array('Path'=> 'ActivatedOperation.Operation.Name')
        );
        $operations = SearchTools::createArrayIDFromCollection(
            'Operation',
            array(),
            MSG_SELECT_AN_ELEMENT
        );
        $this->searchForm->addElement(
            'select',
            'OperationId',
            _('Operation'),
            array($operations),
            array('Path'=> 'ActivatedOperation.Operation.Id')
        );
        $tasks = SearchTools::createArrayIDFromCollection(
            'Task',
            array(),
            MSG_SELECT_AN_ELEMENT
        );
        $this->searchForm->addElement(
            'select',
            'TaskId',
            _('Task'),
            array($tasks),
            array('Path'=> 'Task.Id')
        );
        $this->searchForm->addElement(
            'checkbox',
            'DateOrder1',
            _('Filter by date'),
            array(
                '',
			    'onClick="$(\\\'Date1\\\').style.display=this.checked?\\\'block\\\':\\\'none\\\';"'
            )
        );
        $this->searchForm->addDate2DateElement(
            array('Name'   => 'BeginDate', 'Path' => 'Begin'),
            array('Name'   => 'EndDate', 'Path' => 'Begin'),
            array(
                'EndDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')),
        	    'BeginDate' => array('Y'=>date('Y'))
            )
        );
    }

    // }}}
    // UploadedDocumentGrid::additionalGridColumns() {{{

    /**
     * Actions du grid additionnelles
     *
     * @access protected
     * @return void
     */
    protected function additionalGridColumns() {
        $this->grid->newColumn('FieldMapper', _('Order'), array(
            'Macro'    => '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.CommandNo%',
            'Sortable' => false
        ));
        $this->grid->newColumn('FieldMapper', _('Customer'), array(
            'Macro'    => '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.Destinator.Name%',
            'Sortable' => false
        ));
        $this->grid->newColumn('FieldMapper', _('Operation'), array(
            'Macro' => '%ActivatedOperation.Operation.Name%'
        ));
        $this->grid->newColumn('FieldMapper', _('Task'), array(
            'Macro' => '%Task.Name%'
        ));
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
        if (isset($_SESSION['udId']) || isset($_REQUEST['udId'])) {
            $this->grid->newAction('Redirect', array(
                'Caption' => _('Assign document to selected task'),
                'URL' => 'UploadedDocumentActivatedChainTask.php?action=assign&ackId=%d',
            ));
        }
    }

    // }}}
    // ActivatedChainTaskGrid::getGridSortOrder() {{{

    /**
     * Méthode à surcharger dans les classes filles
     * Par defaut: tri croissant sur la 1ere colonne
     *
     * <code>
     * protected function getGridSortOrder() {
     *     return array('Number'=>SORT_ASC);
     * }
     * </code>
     *
     * @access protected
     * @return array
     */
    protected function getGridSortOrder() {
        return array('ActivatedOperation.ActivatedChain.Id'=>SORT_ASC);
    }

    // }}}
}

?>
