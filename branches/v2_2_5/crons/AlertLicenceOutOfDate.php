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

require_once('AlertSender.php');

$AeroActorMapper = Mapper::singleton('AeroActor');
$AeroActorCollection = $AeroActorMapper->loadCollection(
		array('Active' => 1, 'ClassName' => array('AeroCustomer', 'AeroInstructor',
												  'AeroSupplier', 'AeroOperator')),
		array('Name' => SORT_ASC));

$adminBody = '';
$count = $AeroActorCollection->getCount();

for($i = 0; $i < $count; $i++) {
	$AeroActor = $AeroActorCollection->getItem($i);
	$LicenceCollection = $AeroActor->getLicenceCollection();

	if (Tools::isEmptyObject($LicenceCollection)) {
	    continue;
	}

	$body = '';
	$Lcount = $LicenceCollection->getCount();
	for($j = 0; $j < $Lcount; $j++) {
		$Licence = $LicenceCollection->getItem($j);
		if ($Licence->getToBeChecked() == 0) {
		    continue;
		}
		if ($Licence->getEndDate() < date('Y-m-d H:i:s', time())) {
			$body .= ' - ' . _('License number ') . ' ' . $Licence->getNumber()
                . ', ' . _('type') . ' '
				. Tools::getValueFromMacro($Licence, '%LicenceType.Name%')
				. ' : '._('Deadline').' :'
                . $Licence->getEndDate('localedate_short') . "\r\n";
		}

		// Les qualifications associees perimees:
		$FilterComponent = SearchTools::NewFilterComponent('EndDate', '', 'LowerThan',
            date('Y-m-d H:i:s', time()), 1);
		$RatingCollection = $Licence->getRatingCollection(
				$FilterComponent, array(),
				array('EndDate', 'Type', 'FlyType'));
		if (Tools::isEmptyObject($RatingCollection)) {
		    continue;
		}
		$Rcount = $RatingCollection->getCount();
		for($k = 0; $k < $Rcount; $k++) {
			$Rating = $RatingCollection->getItem($k);
			if ($body == '') {
			    $body .= ' - ' . _('License number ') . ' ' . $Licence->getNumber()
                . ', ' . _('type') . ' '
				. Tools::getValueFromMacro($Licence, '%LicenceType.Name%')
				. ' : ' . _('Deadline') . ' :'
                . $Licence->getEndDate('localedate_short') . "\r\n";
			}
			$body .= '    - ' . _('Qualification') . ' '
                . Tools::getValueFromMacro($Rating, '%Type.Name%') . ' , type '
                . Tools::getValueFromMacro($Rating, '%FlyType.Name%')
                . ': ' . _('Deadline') . ' :'
				. $Rating->getEndDate('localedate_short') . "\r\n";
		}
	}

	// L'alerte envoyee aux non-Admin: par Actor
	if ($body != '') {
	    // On envoie l' alerte
		//echo "xxxxxxxxxxxxxxxx On envoie une alerte!!<br>";
	    AlertSender::send_ALERT_LICENCE_OUT_OF_DATE($body, $AeroActor);
		$adminBody .= _('Actor') . ': ' . $AeroActor->getName() . "\r\n";
		$adminBody .= $body . "\r\n";
	}
}

if ($adminBody != '') {
    // On envoie l' alerte
	//echo "xxxxxxxxxxxxxxxx On envoie l' alerte ADMIN!!";
    AlertSender::send_ALERT_LICENCE_OUT_OF_DATE_ADMIN($adminBody);
}

?>
