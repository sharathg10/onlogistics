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

session_cache_limiter('private');

require_once('config.inc.php');
require_once('GenerateDocument.php');

$auth = Auth::Singleton();

$errorBody = _('An error occurred, packing list could not be printed.');

if(!isset($_GET['reedit'])) {
    SearchTools::ProlongDataInSession();  // prolonge les datas du form de recherche en session
    if (!isset($_REQUEST['boxIds'])) {
        Template::errorDialog(I_NEED_SELECT_ITEM, 'javascript:window.close();', BASE_POPUP_TEMPLATE);
        exit(1);
    }

    $boxCol = Object::loadCollection('Box', array('Id' => $_GET['boxIds']));
    if (count($boxCol) == 0) {
        Template::errorDialog(I_NEED_SELECT_ITEM, 'javascript:window.close();', BASE_POPUP_TEMPLATE);
        exit(1);
    }

    $isReedition = false; // inutilise
    $packingList = Object::load('PackingList');
    $packingList->generateId();
    $packingList->setDocumentNo($packingList->getId());
    $packingList->setEditionDate(date('Y-m-d H:i:s'));
    $documentModel = $packingList->findDocumentModel();
    if (false != $documentModel) {
        $packingList->setDocumentModel($documentModel);
    }
    
    $packingList->setBox($this);
    $scMapper = Mapper::singleton('SupplierCustomer');
    $sc = $scMapper->load(array(
        'Supplier' => $this->getExpeditorId(),
        'customer' => $this->getDestinatorId()
    ));
    if (Tools::isEmptyObject($sc)) {
        $sc = Object::load('SupplierCustomer');
        $sc->setSupplier($this->getExpeditor());
        $sc->setCustomer($this->getDestinator());
        $sc->save();
    }
        $packingList->setSupplierCustomer($sc);
        $packingList->save();

    Database::connection()->startTrans();
    $packingList = $box->createPackingList();

    // Commit de la transaction, si elle a réussi
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        Database::connection()->rollbackTrans();
        Template::errorDialog($errorBody, $returnURL);
        exit;
    }
    Database::connection()->completeTrans();
} else {
    // reedit=1 on arrive de la reedition des documents
    $isReedition = true;
    $mapper = Mapper::singleton('PackingList');
    $packingList = $mapper->load(array('Id' => $_GET['pId']));
    if(!($packingList instanceof PackingList)) {
        Template::errorDialog($errorBody, 'javascript:window.close();', BASE_POPUP_TEMPLATE);
    }
}

generateDocument($packingList, $isReedition);

?>
