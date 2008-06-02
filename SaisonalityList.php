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

require_once('config.inc.php');
$auth = Auth::Singleton();
$auth->checkProfiles();

if (isset($_REQUEST['renew'])) {  // si on veut renouveler des Saisonalites
    if (is_array($_REQUEST['SaisId'])) {
        // tableau des Id des Saisonality ne pouvant etre mises a jour
		$ErrorSaisIdArray = array();
		foreach($_REQUEST['SaisId'] as $i => $Id) {
			$Saisonality = Object::load('Saisonality', $Id);
			if (Tools::isEmptyObject($Saisonality)) {
			    continue;
			}
			// MAJ des dates de debut et fin: annee + 1
			// periode non expiree: on ne peut renouveler
			if (false == $Saisonality->renew()) {
			    $ErrorSaisIdArray[] = $Id;
				continue;
			}
            saveInstance($Saisonality, 'SaisonalityList.php');
			unset($Saisonality);
		}
		if (!empty($ErrorSaisIdArray)) {
		    $ErrorMsge = implode("</li><li>", $ErrorSaisIdArray);
			Template::errorDialog(
                sprintf(
                    _('The following seasonalities could not be renewed: %s'),
                    '<ul><li>' . $ErrorMsge . '</li></ul>'),
				'SaisonalityList.php');
		    exit;
		}
	}
}
$grid = new Grid();
$grid->itemPerPage = 100;

$grid->NewAction('Redirect', array(
        'Caption' => _('Renewal'),
		'TransmitedArrayName' => 'SaisId',
		'URL' => $_SERVER['PHP_SELF'].'?renew=1'));
$grid->NewAction('AddEdit', array('Action' => 'Add', 'URL' => 'SaisonalityAdd.php'));
$grid->NewAction('Delete', array('TransmitedArrayName' => 'SaisId',
		'EntityType' => 'Saisonality'));


$grid->NewColumn('FieldMapper', _('Seasonality'), array('Macro' =>'%Id%'));
// Les ProductKind
$grid->NewColumn('FieldMapper', _('Properties'),
        array('Macro' => '%ProductKindCollection%', 'Sortable' => false));
// Product.BaseReference
$grid->NewColumn('SaisonalityBaseReference', _('Reference'), array('Sortable' => false));
$grid->NewColumn('FieldMapper', _('Beginning date'),
        array('Macro' =>'%StartDate|formatdate@DATE_SHORT%'));
$grid->NewColumn('FieldMapper', _('End date'),
        array('Macro' =>'%EndDate|formatdate@DATE_SHORT%'));
$grid->NewColumn('FieldMapper', _('Ratio'),array('Macro' => '%Rate|formatnumber% %'));

Template::pageWithGrid($grid, 'Saisonality', '', array(), array('StartDate' => SORT_ASC));

?>
