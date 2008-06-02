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

require_once 'lib/RTWProductManager.php';

/**
 * RTWProductGrid
 */
class InactiveRTWProductGrid extends GenericGrid {
    // proprietes {{{

    protected $collection = false;
    protected $model = false;

    // }}}
    // Constructeur {{{

    /**
     * Constructeur
     *
     * @param array $params tableau de paramètres
     * @return void
     */
    public function __construct($params=array()) {
        $session = Session::singleton();
        if (!isset($_SESSION['_RTWModel_'])) {
            Template::errorDialog(E_ERROR_GENERIC);
            exit(1);
        }
        $session->prolong('_RTWModel_', 1);
        $params['itemsperpage']     = 1000;
        $params['showPrintAction']  = false;
        $params['showExportAction'] = false;
        $params['profiles']        = array(
            UserAccount::PROFILE_ADMIN,
            UserAccount::PROFILE_PRODUCT_MANAGER
        );
        parent::__construct($params);
        $this->model      = $_SESSION['_RTWModel_'];
        $this->collection = RTWProductManager::createProducts($this->model);
    }

    // }}}
    // InactiveRTWProductGrid::render() {{{

    /**
     * Effectue l'affichage du SearchForm et du Grid.
     *
     * @param string $title titre du grid
     * @return void
     */
    public function render($title=false, $template=false) {
        $this->includeSessionRequirements();
        $this->session = Session::singleton();
        unset($_SESSION['_' . $this->clsname . '_']);
        $this->auth();
        $title    = !$title ? $this->title : $title;
        $template = !$template ? $this->htmlTemplate : $template;
        $this->buildGrid();
        $action = UrlTools::compliantURL($_SERVER['REQUEST_URI']);
        if ($this->grid->isPendingAction()) {
            return $this->grid->dispatchAction($this->collection);
        }
        $result = $this->grid->render($this->collection, true);
        Template::page(
            $title,
            '<form id="'.$this->clsname.'Grid" action="'.$action.'" method="post">'
            . $result . '</form>',
            array(),
            array(),
            $template
        );
    }

    // }}}
    // InactiveRTWProductGrid::getFeatures() {{{

    /**
     * Seulement grid.
     *
     * @param array $params tableau de paramètres
     * @return void
     */
    public function getFeatures() {
        return array(GenericController::FEATURE_GRID);
    }

    // }}}
    // InactiveRTWProductGrid::additionalGridActions() {{{

    /**
     * additionalGridActions
     *
     * @access protected
     * @return void
     */
    protected function additionalGridActions() {
        $this->grid->newAction('Cancel');
        $this->grid->newAction('RTWModelValidateSelected', array(
            'Collection' => $this->collection,
            'Model'      => $this->model,
            'Caption'    => _('Validate selected items')
        ));
    }

    // }}}
}

?>
