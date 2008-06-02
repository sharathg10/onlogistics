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

require_once('Objects/Ressource.php');

/**
 * RessourceGroupGrid
 *
 */
class RessourceGroupGrid extends GenericGrid
{
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
    }
    
    // }}} 
    // RessourceGroupGrid::onAfterBuildSearchForm {{{

    /**
     * Méthode pour ajouter des champs du form custom
     *
     * @access public
     */
    public function onAfterBuildSearchForm() {
        $this->searchForm->addElement( 'text', 'RessourceGroup_ChainTask_Name',
            _('Task associated to the model'), array(), 
            array('Path'=>'ChainTask().Task.Name') 
        );
        $this->searchForm->addElement('select', 
            'RessourceRessourceGroup_Ressource_Type', _('Resource type'), 
            array(
                (array('##' => MSG_SELECT_MANY_ELEMENTS) + Ressource::getTypeConstArray()),
                'multiple size="5"'
            ), 
            array(
                'Path' => 'RessourceRessourceGroup().Ressource.Type',
                'Operator' => 'In'
            )
        );
    }
    // }}} 
    // RessourceGroupGrid::additionalGridActions() {{{

    /**
     * additionalGridActions 
     * 
     * @access public
     * @return void
     */
    public function additionalGridActions() {
        // action copier
        $this->grid->newAction('Redirect', array(
                'Caption' => _('Copy'),
                'Title'   => _('Copy resource model'),
                'URL'     => 'RessourceGroupDuplicate.php?rsgID=%d'
            )
        );
    }

    // }}}
}

?>
