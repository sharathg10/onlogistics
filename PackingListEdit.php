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
require_once('GenerateDocument.php');

$auth = Auth::Singleton();
$auth->checkProfiles(array(
    UserAccount::PROFILE_ADMIN,
    UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,
    UserAccount::PROFILE_TRANSPORTEUR,
    UserAccount::PROFILE_GESTIONNAIRE_STOCK,
    UserAccount::PROFILE_RTW_SUPPLIER
));

$error = _('An error occurred, packing list could not be printed.');

if (!isset($_GET['reedit'])) {
    // prolonge les datas du form de recherche en session
    SearchTools::ProlongDataInSession();
    if (!isset($_REQUEST['boxIds'])) {
        Template::errorDialog(I_NEED_SELECT_ITEM, 'javascript:window.close();', BASE_POPUP_TEMPLATE);
        exit(1);
    }
    $boxCol = Object::loadCollection('Box', array('Id' => $_GET['boxIds']));
    if (count($boxCol) == 0) {
        Template::errorDialog(I_NEED_SELECT_ITEM, 'javascript:window.close();', BASE_POPUP_TEMPLATE);
        exit(1);
    }
    // check que les box sont compatibles:
    // - elles doivent toutes etre liees au meme destinataire/expediteur
    // - elles ne doivent pas etre deja affectees a une packinglist
    $firstBox   = $boxCol->getItem(0);
    $expSiteId  = $firstBox->getExpeditorSiteId();
    $destSiteId = $firstBox->getDestinatorSiteId();
    foreach ($boxCol as $box) {
        if ($box->getExpeditorSiteId() != $expSiteId || $box->getDestinatorSiteId() != $destSiteId) {
            Template::errorDialog(
                $error . '<br/>' . _('Expeditor and destinator must be the same for all selected boxes'),
                'javascript:window.close();', 
                BASE_POPUP_TEMPLATE
            );
            exit(1);
        }
        if (($pList = $box->getPackingList()) instanceof PackingList) {
            Template::errorDialog(
                $error . '<br/>' . sprintf(
                    _('Box "%s" is already in packing list "%s".'),
                    $box->getReference(), $pList->getDocumentNo()
                ),
                'javascript:window.close();', 
                BASE_POPUP_TEMPLATE
            );
            exit(1);
        }
    }

    // allright, on cree la packinglist
    Database::connection()->startTrans();

    $isReedition = false;
    $packingList = new PackingList();
    $packingList->generateId();
    $packingList->setDocumentNo($packingList->getId());
    $packingList->setEditionDate(date('Y-m-d H:i:s'));
    $documentModel = $packingList->findDocumentModel();
    if (false != $documentModel) {
        $packingList->setDocumentModel($documentModel);
    }
    $sc = Object::load('SupplierCustomer', array(
        'Supplier' => $firstBox->getExpeditorId(),
        'customer' => $firstBox->getDestinatorId(),
    ));
    if (!$sc instanceof SupplierCustomer) {
        $sc = Object::load('SupplierCustomer');
        $sc->setSupplier($firstBox->getExpeditorId());
        $sc->setCustomer($firstBox->getDestinatorId());
        $sc->save();
    }
    $packingList->setSupplierCustomer($sc);
    $packingList->save();

    // met a jour la fkey packinglist des box
    foreach ($boxCol as $box) {
        $box->setPackingList($packingList);
        $box->save();
    }

    // Commit de la transaction, si elle a réussi
    if (Database::connection()->hasFailedTrans()) {
        Database::connection()->rollbackTrans();
        Template::errorDialog(
            $error . "<br/>" . Database::connection()->errorMsg(),
            'javascript:window.close()',
            BASE_POPUP_TEMPLATE
        );
        exit(1);
    }
    Database::connection()->completeTrans();
} else if (isset($_REQUEST['pId'])) {
    // reedit=1 on arrive de la reedition des documents
    $isReedition = true;
    $packingList = Object::load('PackingList', array('Id' => $_REQUEST['pId']));
    if(!($packingList instanceof PackingList)) {
        Template::errorDialog(
            $error,
            'javascript:window.close();',
            BASE_POPUP_TEMPLATE
        );
        exit(1);
    }
} else {
    // rien a faire ici...
    Tools::redirectTo('home.php');
    exit(0);
}

generateDocument($packingList, $isReedition);

?>
