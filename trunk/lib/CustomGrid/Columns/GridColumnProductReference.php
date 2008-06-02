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

require_once('Objects/Command.php');

class GridColumnProductReference extends AbstractGridColumn {

    /**
     * Constructor
     *
     * @access protected
     */
    function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
		$this->_cmdType = $params['cmdType'];
		if (isset($params['cmdDestinator'])) {
		    $this->_cmdDestinator = $params['cmdDestinator'];
		}
    }

    private $_cmdType = -1;
    private $_cmdDestinator = false;

    public function render($object) {
        if ($object instanceof Product) {
            $product = $object;
        } else if ($object instanceof CommandItem) {
		    $product = $object->getProduct();
        } else if ($object instanceof ActivatedMovement) {
            $commandItem = $object->getProductCommandItem();
		    $product = $commandItem->getProduct();
        } else {
            trigger_error('GridColumnInvoiceAddEditBaseReference: cette '
                . 'colonne est valide pour les grids sur les product, les '
                . ' commanditem et activatedmovement uniquement', E_USER_ERROR);
        }
		if ($this->_cmdType == Command::TYPE_SUPPLIER) {
            $ref = $product->getReferenceByActor();
            if (!empty($ref)) {
                return $ref;
            }
		}
		elseif ($this->_cmdDestinator != false) {
		    $ref = $product->getReferenceByActor($this->_cmdDestinator);
            if (!empty($ref)) {
                return $ref;
            }
		}
		return $product->getBaseReference();
    }
}

?>