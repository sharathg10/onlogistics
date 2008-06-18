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

class GridActionRTWModelValidateSelected extends AbstractGridAction {
    // Constructeur {{{

    /**
     * Constructeur
     *
     * @param array $params tableau de paramètres
     * @return void
     */
    public function __construct($params=array()) {
        parent::__construct($params);
        $this->allowEmptySelection = false;
        $this->collection = $params['Collection'];
        $this->model      = $params['Model'];
    }

    // }}}
    // GridActionRTWModelCancelAll::execute() {{{

    /**
     * GridActionDownloadUploadedDocument::execute()
     * 
     */
    function execute($objects, $itemsIds) {
        if (count($itemsIds) == 0) {
            Template::errorDialog(I_NEED_SELECT_ITEM);
            exit(1);
        }
        $products = array();
        foreach ($this->collection as $i=>$product) {
            if (in_array($i, $itemsIds)) {
                $products[] = $product;
            }
        }
        require_once 'lib/RTWProductManager.php';
        try {
            RTWProductManager::deleteProducts($this->model);
            $col = RTWProductManager::saveProducts($this->model, $products);
            $this->model->save();
            $msg = _('All products combinations were successfully created. Here is the list of created products: %s');
            $ul  = "<ul>\n";
            foreach ($col as $product) {
                $ul .= "<li>" . $product->toString() . "</li>\n";
            }
            $ul .= "</ul>\n";
            $msg = sprintf($msg, $ul);
            Template::infoDialog($msg, 'dispatcher.php?entity=RTWModel');
            exit(0);
        } catch (Exception $exc) {
            Template::errorDialog(
                E_ERROR_GENERIC . ': ' . $exc->getMessage(),
                'dispatcher.php?entity=RTWModel'
            );
            exit(0);
        }
    } 

    // }}}    
} 

?>
