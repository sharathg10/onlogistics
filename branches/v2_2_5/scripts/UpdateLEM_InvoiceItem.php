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
    $dsnArray = array('DSN_SERVAM');
}
else {
    require_once('../config.inc.php');
    $dsnArray = $GLOBALS['DSNS'];
}

require_once('../config.inc.php');

// Selection des LEM factures, dont la commande associee n'est liee
// qu'a une seule facture
$sql = 'select distinct(LEM._Id) as lemId, INV._Id as invId
from LocationExecutedMovement LEM, ExecutedMovement EXM, ActivatedMovement ACM, AbstractDocument INV
where LEM._IsFactured=1 and LEM._ExecutedMovement = EXM._Id and EXM._ActivatedMovement = ACM._Id
and ACM._ProductCommand in (
select distinct C._Id
from Command C, AbstractDocument A
where A._Command=C._Id and A._ClassName="Invoice"
group by C._Id
having count(distinct(A._Id))=1)
and INV._Command=ACM._ProductCommand and INV._ClassName="Invoice";';


foreach ($dsnArray as $dsn) {
    $GLOBALS['transaction'] = NewADOConnection(constant($dsn));
    Database::connection()->debug = false;
    Database::connection()->connect();
    set_time_limit(900);  // 15 minutes

    $rs = Database::connection()->execute($sql);

    //On demarre la transaction
    Database::connection()->startTrans();

    while (!$rs->EOF) {
        $sqlUpdate = 'UPDATE LocationExecutedMovement LEM
        SET _InvoiceItem=(
        select distinct(II._Id) from InvoiceItem II, AbstractDocument A, Product P
        where II._Invoice=' . (int)$rs->fields['invId'] . ' and LEM._Product=P._Id
        and II._Reference=P._BaseReference)
        where LEM._Id='. (int)$rs->fields['lemId'] .';';
        Database::connection()->execute($sqlUpdate);
        $rs->MoveNext();
    }

    //On commite
    if (Database::connection()->hasFailedTrans()) {
        trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
    	Database::connection()->rollbackTrans();
    	echo 'error execution sql';
    	exit;
    }
    Database::connection()->completeTrans();

    echo 'MAJ terminée pour ' . $dsn . ". \n  ";
    Database::connection()->close();
    unset($rs);
}

?>