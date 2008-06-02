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

if (php_sapi_name() != 'cli') {
    exit(1);
}

// pour debugguer
// require_once('config.inc.php');
require_once('Objects/OccupiedLocation.php');
require_once('Objects/Prestation.php');

$olMapper = Mapper::singleton('OccupiedLocation');

// Blindage: test si la cron n'a pas deja ete executee ce jour.
$ol = $olMapper->load(array('CreationDate' => date('Y-m-d', time())));
if ($ol instanceof OccupiedLocation) {
    exit;
}

// Recuperation du delai de carence si existe
$pMapper = Mapper::singleton('Prestation');
$Prestation = $pMapper->load(array('Type' => Prestation::PRESTATION_TYPE_STOCKAGE));
$freePeriod = (Tools::isEmptyObject($Prestation))?0:$Prestation->getFreePeriod();
// la ValidityDate systematique (86400: nb de secondes dans 1 jour)
// Date du jour + (nb de jours du delai de carence)
$secDelai = 86400 * $freePeriod;
$validityDate = substr(
        DateTimeTools::dateModeler(date('Y-m-d 00:00:00', time()), $secDelai), 0, 10);


// Determination des Location impliques dans les LPQ a traiter
$lpqMapper = Mapper::singleton('LocationProductQuantities');
$FilterCmptArray = array();
// Le FilterCopnt suivant sert en fait pour la jointure
$FilterCmptArray[] = SearchTools::NewFilterComponent(
        'qty', 'RealQuantity', 'NotEquals',0, 1, 'Location');
$FilterCmptArray[] = SearchTools::NewFilterComponent(
        'Location.Activated', '', 'Equals', 1, 1);
$filter = SearchTools::filterAssembler($FilterCmptArray);
$lpqCollection = $lpqMapper->loadCollection(
        $filter, array(), array('Location', 'Product', 'RealQuantity'));

$lpqCount = $lpqCollection->getCount();

// La veille, au format date de MySQL
$yesterday = substr(DateTimeTools::DateModeler(date('Y-m-d 00:00:00', time()), -86400), 0, 10);

for($i=0 ; $i<$lpqCount ; $i++) {
    $lpq = $lpqCollection->getItem($i);
    $OccupiedLocation = new OccupiedLocation();
    $OccupiedLocation->setCreationDate(date('Y-m-d', time()));
    $OccupiedLocation->setLocation($lpq->getLocationId());
    $OccupiedLocation->setProduct($lpq->getProductId());
    $OccupiedLocation->setQuantity($lpq->getRealQuantity());
    
    $OccupiedLocation->setValidityDate($validityDate);
    
    if($freePeriod != 0) {
        // si il y a un délai de carrence, il se peut que la date de validité 
        // change en fonction des OccupiedLocation créés la veille.
        $olTest = $olMapper->load(array('Location' => $lpq->getLocationId(),
            'Product' => $lpq->getProductId(), 'CreationDate' => $yesterday));
        if ($olTest instanceof OccupiedLocation) {
            // il y a un OccupiedLocation avec le Location et le Product créé la 
            // veille.
            $OccupiedLocation->setValidityDate($olTest->getValidityDate());
        }
    }
    
    $OccupiedLocation->save();
}

?>
