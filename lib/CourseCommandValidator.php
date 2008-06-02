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

require_once('ExceptionCodes.php');

// error messages {{{

/**
 * Constantes messages d'erreurs
 *
 */
define('E_LICENCE_NOTFOUND', _("Customer \"%s\" has no license, nor authorization for selected airplane"));
define('E_LICENCE_VALIDITY', _("Licence expiration date exceeded for selected airplane"));
define('E_NO_CONCRETEPRODUCT', _("No airplane of this type found, please select another airplane type."));
define('E_NO_CONCRETEPRODUCT_AVAILABLE', _("Airplane is not available for selected period, please select another airplane or another period."));
define('E_NO_INSTRUCTOR_AVAILABLE',  _("No instructor available for selected period, please select another period."));
define('E_WEIGHTBYSEAT_OVER', _("Maximum weight by seat exceeded for selected airplane"));

// }}}

/**
 * CourseCommandValidator class
 * Classe de validation de la commande de cours.
 *
 * @package    onlogistics
 * @subpackage lib
 */
class CourseCommandValidator
{
    // properties {{{

    /**
     * La commande en cours
     *
     * @var    object Command $_command
     * @access private
     */
    private $_command = false;
    
    // }}}
    // constructor {{{

	/**
     * Constructor
     *
     * @access protected
     */
	function CourseCommandValidator($command){
		$this->_command = $command;
	}

    // }}}
    // CourseCommandValidator::validate() {{{

    /**
     * Retourne RET_COMMAND_OK si la commande est ok, RET_COMMAND_BLOCKED si
     * la commande est ok mais doit être blockée, ou une Exception avec son
     * message si pas ok.
     *
     * @access public
     * @return void
     **/
    function validate(){
        // variables
    	$customer = $this->_command->getCustomer();
        $ftype    = $this->_command->getFlyType();
        // *** Controles sur le client ***
        // on vérifie la présence d'une licence pour ce type ou que le type
        // est  dans les types autorisés
        $lic = $customer->findLicenceByFlyType($ftype);
        if ($lic) {
            // on vérifie la validité de la licence à la date de la commande
            if ($this->_command->getWishedStartDate() > $lic->getEndDate()) {
                return new Exception(E_LICENCE_VALIDITY);
            }
            /*
            // on vérifie la validité du certificat à la date de la commande
            if ($this->_command->getWishedStartDate() >
                $lic->getCertificateEndDate()) {
                return new Exception(E_CERTIFICATE_VALIDITY);
            }
            */
        } else {
            // le client n'a pas de licence, mais si le flytype est dans ses
            // flytypes autorisés c'est ok, sinon on retoune une exception
            $auth_ftypes = $customer->getAuthorizedFlyTypeCollectionIds();
            if (!in_array($ftype->getId(), $auth_ftypes)) {
                return new Exception(
                    sprintf(E_LICENCE_NOTFOUND, $customer->getName()));
            }
        }

        // Si le client est stagiaire et qu’il n’a pas volé depuis 10 jours
        // FIXME : à déporter ailleurs
        /*
        $diff = DateTimeTools::mySQLDateSubstract(
            $this->_command->getBeginDate(), $customer->getLastFlyDate());
        if ($customer->getTrainee() && $diff > 864000) { // 864000 = 10 jours
            $ret = RET_COMMAND_WARN_TRAINEE;
        }
        */
        // *** Controles sur l'instructeur, s'il existe... ***
        if (!$this->_command->getSolofly()) {
            $instructor = $this->_command->findInstructor();
            if (!$instructor) {
                return new Exception(E_NO_INSTRUCTOR_AVAILABLE);
            }
        }

        // *** Controles sur l'appareil ***
        // on vérifie l'existence de l'hélico
        $ccp = $this->_command->findAeroConcreteProduct();
        // pas d'appareil trouvé
        if ($ccp === -1) {
            return new Exception(E_NO_CONCRETEPRODUCT, EXCEP_NO_CONCRETE_PRODUCT);
        }
        // aucun appareil dispo
        if ($ccp === false) {
            return new Exception(
                    E_NO_CONCRETEPRODUCT_AVAILABLE, EXCEP_NO_CONCRETE_PRODUCT_AVAILABLE);
        }
        // on vérifie que le poids total de l'instructeur + client ne dépasse
        // pas le poids total de l'appareil
        $custw = $customer->getWeight();
        $instw = 0;
        if (isset($instructor)) {
            $instw = $instructor->getWeight();
        }
        if (($custw + $instw) > $ccp->getMaxWeightOnTakeOff()) {
            return new Exception(E_MAXWEIGHT_OVER);
        }
        // on vérifie le poids par siège
        $maxweightbyseat = $ccp->getMaxWeightBySeat();
        if ($custw > $maxweightbyseat || $instw > $maxweightbyseat) {
            return new Exception(E_WEIGHTBYSEAT_OVER);
        }
        return true;
    }

    // }}}
}

?>
