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

class GridColumnInvoiceAddEditBaseReference extends AbstractGridColumn {

    /**
     * Constructor
     */
    function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
		$this->_cmdType = $params['cmdType'];
    }

    private $_cmdType;

    function render($object) {
		require_once('Objects/ActivatedMovement.php');
		require_once('Objects/LocationExecutedMovement.php');
		require_once('Objects/Command.php');
		require_once('Objects/Command.const.php');
		$AMovement = $object->getActivatedMovement();
		$Product = $object->getProduct();
		$return = $Product->getBaseReference();
		// Si Command Fournisseur, pas de reintegration possible
        // => pas d'info-bulle a ajouter
		if ($this->_cmdType == Command::TYPE_SUPPLIER) {
            // on retourne en priorité la ref. fournisseur
            $ref = $Product->getReferenceByActor();
		    return empty($ref)?$return:$ref;
		}
        if ($AMovement->getHasBeenFactured() != ActivatedMovement::ACM_FACTURE_PARTIEL) {
            return $return;
        }
		// Idem si Command Client et pas encore facturee, meme partiellement
		$EXMovement = $AMovement->getExecutedMovement();
		// Les LEM concernes par la ligne du Grid traitee:
		// LEMs non annulateurs, non factures, lies au Product correspondant
		$FilterComponentArray = array(); // Tableau de filtres
        $FilterComponentArray[] = SearchTools::newFilterComponent(
                'Cancelled', '', 'Equals', -1, 1);
        $FilterComponentArray[] = SearchTools::newFilterComponent(
                'InvoiceItem', '', 'NotEquals', 0, 1);
        $filter = SearchTools::filterAssembler($FilterComponentArray);

        $LEMCollection = $EXMovement->getLocationExecutedMovementCollection($filter);

		if ($LEMCollection->getCount() == 0) {
		    return $return;
		}
		$msg = _("This reference was subject to a reinstatement, ");
        $msg .= _("please be careful to not charge it twice.");
		$addon = '&nbsp;<a href="javascript:void(0);" title="'. $msg
			 . '" class="rouge">*</a>';

		return $return . $addon;
    }
}

?>