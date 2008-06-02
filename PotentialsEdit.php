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
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_AERO_OPERATOR));

$serCcp = Mapper::singleton('ConcreteProduct');
$smarty = new Template();

$content ='';

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'home.php';
SearchTools::prolongDataInSession();


// récupération des ConcreteProduct_Imatriculation dont le Product à un FlyType non nul :
//$filter = SearchTools::NewFilterComponent('FlyType', 'Product.FlyType', 'NotEquals', 0, 1);
$filter = SearchTools::NewFilterComponent('FlyType', 'Product.ClassName', 'Equals', 'AeroProduct', 1);
$fltArray = SearchTools::createArrayIDFromCollection('ConcreteProduct', $filter, _('Select a matriculation'));
// modificatin du résultat pour smarty
$selectArray = array();
$i = 0;
foreach ($fltArray as $key=>$value) {
    $selectArray[$i]['id'] = $key;
    $selectArray[$i]['name'] = $value;
    $i++;
}

$smarty->assign('retURL', $retURL);
$smarty->assign('selectArray', $selectArray);

// si le formulaire à été posté (onChange sur la liste)
if(!empty($_POST['Id']) && $_POST['Id'] != '##') {

    $ccpId = $_POST['Id'];

    $ccp = $serCcp->load(array('Id' => $ccpId));

    $ccpClassName = get_class($ccp);

    $smarty->assign('ccpId', $ccpId);
    $smarty->assign('className', $ccpClassName);

    // si le formulaire à été posté via le bouton validé
    if (!empty($_POST['formSubmitted'])) {

        //vérification et mise à jour des nouveaux potentiels
        //convertion des heures
        $fields = array('RealHourSinceOverall', 'RealHourSinceRepared');
        foreach($fields as $field){
            $name = $ccpClassName . '_' . $field;

            if(!empty($_POST[$name . '_Hours']) || !empty($_POST[$name . '_Minutes'])) {
                $_POST[$field . '_Hours'] = empty($_POST[$name . '_Hours'])?
                    '00':$_POST[$name.'_Hours'];
                $_POST[$field . '_Minutes'] = empty($_POST[$name . '_Minutes'])?
                    '00':$_POST[$name . '_Minutes'];
                $time = $_POST[$name . '_Hours'] . ':' . $_POST[$name . '_Minutes'];
                $_POST[$name] = DateTimeTools::getHundredthsOfHour($time);
            }
        }
        // suppression des champs non renseigné
        $fields = array('RealLandingSinceOverall', 'RealLandingSinceRepared', 'RealCycleSinceOverall', 'RealCycleSinceRepared');
        foreach ($fields as $field) {
            $name = $ccpClassName . '_' . $field;
            if(empty($_POST[$name])) {
                unset($_POST[$name]);
            }
        }

        // MAJ des potentiels virtuels
        $fields = array('HourSinceOverall');
        foreach ($fields as $field) {
            $realName = $ccpClassName . '_Real' . $field;
            //si le potentiel réel à été MAJ, on MAJ le virtuel correspondant
            if(!empty($_POST[$realName])) {
                $virtualName = $ccpClassName . '_Virtual' . $field;
                $getterOldReal = 'getReal' . $field;
                $getterOldVirtual = 'getVirtual' . $field;

                $_POST[$virtualName] = $ccp->$getterOldVirtual()
                                       - $ccp->$getterOldReal()
                                       + $_POST[$realName];
            }
        }

        Database::connection()->startTrans();
        // Enregistrement des données
        FormTools::autoHandlePostData($_POST, $ccp);
        saveInstance($ccp, $_SERVER['PHP_SELF']);

        // commit
        if (Database::connection()->hasFailedTrans()) {
            trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
            Database::connection()->rollbackTrans();
            Template::errorDialog(E_BODY, $_SERVER['PHP_SELF']);
            Exit;
        }
        Database::connection()->completeTrans();
        Tools::redirectTo($retURL);
        exit;
    }

    //valeurs actuels des potentiels
    $val = $ccp->getRealHourSinceOverall();
    $hhmm[0] = explode(':', DateTimeTools::hundredthsOfHourToTime($val));

    $val = $ccp->getRealHourSinceRepared();
    $hhmm[1] = explode(':', DateTimeTools::hundredthsOfHourToTime($val));

    $smarty->assign('Actual_RHSO_Hours', $hhmm[0][0]);
    $smarty->assign('Actual_RHSO_Minutes', $hhmm[0][1]);
    $smarty->assign('Actual_RHSR_Hours', $hhmm[1][0]);
    $smarty->assign('Actual_RHSR_Minutes', $hhmm[1][1]);
    $smarty->assign('Actual_RLSO', $ccp->getRealLandingSinceOverall());
    $smarty->assign('Actual_RLSR', $ccp->getRealLandingSinceRepared());
    $smarty->assign('Actual_RCSO', $ccp->getRealCycleSinceOverall());
    $smarty->assign('Actual_RCSR', $ccp->getRealCycleSinceRepared());
}

$template = 'ConcreteProduct/PotentialsEdit.html';
$content = $smarty->fetch($template);
$js = array(
	    'js/lib-functions/checkForm.js',
	    'js/includes/PotentialsEdit.js'
);
Template::page(_('Update potentials'), $content, $js);

?>