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
 * @version   SVN: $Id: WineDCAEdit.php 206 2008-10-02 14:45:37Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once('config.inc.php');
require_once('lib/Objects/Actor.inc.php');
require_once('lib/Objects/MovementType.const.php');

// sessions + auth + init vars {{{
$session = Session::singleton();
$auth = Auth::Singleton();
$auth->checkProfiles(array(
    UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, 
    UserAccount::PROFILE_GESTIONNAIRE_STOCK, UserAccount::PROFILE_OPERATOR,
    UserAccount::PROFILE_TRANSPORTEUR));

$itemsIds= SearchTools::requestOrSessionExist('itemsIds');
$startDate = SearchTools::requestOrSessionExist('startDate');
$endDate = SearchTools::requestOrSessionExist('endDate');

$selectDate = TRUE ;
$showGenerate = TRUE ;

// }}}
// Download DCA
if(isset($_REQUEST['print']) && $_REQUEST['print']==1) {

    if (!isset($_REQUEST['doc'])) {
        Template::errorDialog(E_MSG_TRY_AGAIN, 'javascript:window.close()', 
            BASE_POPUP_TEMPLATE);
    	exit();
    }

    $DCA = Object::load('AbstractDocument', $_REQUEST['doc']);
    if (! $DCA instanceof AbstractDocument) {
        Template::errorDialog(E_MSG_TRY_AGAIN, 'javascript:window.close()', 
            BASE_POPUP_TEMPLATE);
    	exit();
    }

    header('Pragma: public');
    header('Content-Type: application/force-download');
    header('Content-Disposition: attachment;filename='.$DCA->DocumentNo.".csv");
    echo $DCA->Document->Data ;
    exit();
}
// }}}
// Generation du DCA {{{
if (isset($_REQUEST['FormSubmitted']) && $_REQUEST['FormSubmitted'] == 'true') { 
    // si pas de lem, inutile d'aller plus loin
    // ou pas de date ...
    if(!$itemsIds && !$startDate ) {
        Template::errorDialog(E_MSG_TRY_AGAIN, 
            'javascript:window.close()', BASE_POPUP_TEMPLATE);
        exit();
    }
    // DCAEnt and DCADet ( selected movements ) {{{   
    // soit une entete + detail pour des mouvements selectionnes 
    if(isset($itemsIds) && ! empty($itemsIds) ) {
        require_once('WineDCATools.php');
        require_once('AbstractDocumentTools.php');
        $selectDate = FALSE ;

        $DCAdatas = generateDCA($itemsIds);

        if($DCAdatas == FALSE) {
            Template::errorDialog(_('All Selected movements have already been exported
                or none of them satisfy conditions to be exported'),
                'javascript:window.close()', BASE_POPUP_TEMPLATE);
            exit();
        }

        //  Commit de la transaction {{{
        Database::connection()->startTrans();

        $DCADate = date('Y-m-d H:i:s');
        $DCAid = generateClassId('WineDCA');
        
        // Fichier Entete ( DCAEnt / WineDCAHeader)
        $WineDCAHeader = Object::load('WineDCAHeader');
        $WineDCAHeader->setEditionDate($DCADate);
        $WineDCAHeader->setDocumentNo('DCAEnt-'.$DCAid);
        $pdfDocHeader = new Document ;        
        $pdfDocHeader->setType(Document::TYPE_CSV);
        $pdfDocHeader->setData($DCAdatas[0]);
        // on sauve le pdfdoc pour pouvoir le sauver ensuite via le doc dca
        saveInstance($pdfDocHeader, 'javascript:window.close()');
        $WineDCAHeader->setDocument($pdfDocHeader->getId());
        saveInstance($WineDCAHeader, 'javascript:window.close()');

        // Fichier Details ( DCADet / WineDCADetails )
        $WineDCADetails= Object::load('WineDCADetails');
        $WineDCADetails->setEditionDate($DCADate);
        $WineDCADetails->setDocumentNo('DCADet-'.$DCAid);
        $pdfDocDetails= new Document ;
        $pdfDocDetails->setType(Document::TYPE_CSV);
        $pdfDocDetails->setData($DCAdatas[1]);
        // cf au dessus 
        saveInstance($pdfDocDetails, 'javascript:window.close()');
        $WineDCADetails->setDocument($pdfDocDetails->getId());
        saveInstance($WineDCADetails, 'javascript:window.close()');

        if (Database::connection()->hasFailedTrans()) {
            trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
            Database::connection()->rollbackTrans();
            Template::errorDialog(E_ERROR_IMPOSSIBLE_ACTION, $_GET['returnURL']);
            exit;
        }

        // Enfin on update les LEMs ...
        updateLEMDCA($DCAdatas[2], $WineDCAHeader,1);
        Database::connection()->completeTrans();
    
        unset($_SESSION['itemIDs'],$startDate, $endDate);

        $message = _("DCA files have been generated").":<br/>
            ".$WineDCAHeader->DocumentNo." : 
            <a href=\"?print=1&doc=".$WineDCAHeader->Id."\">"._("Download file")."</a><br/>
            ".$WineDCADetails->DocumentNo." :
            <a href=\"?print=1&doc=".$WineDCADetails->Id."\">"._("Download file")."</a>" ;

        $showGenerate = FALSE ;

        // }}}

    }
    // }}}
    // DCAPeriodical ( selected dates) {{{   
    // soit un recap par mois si creneau de date ...
    if(isset($startDate) && isset($endDate)) {

        require_once('WineDCATools.php');
        require_once('AbstractDocumentTools.php');
        $DCAdatas = generateDCAPeriodical($startDate, $endDate);

        if($DCAdatas == FALSE) {
            Template::errorDialog(_('Movements found have already been exported
                or none of them satisfy conditions to be exported'),
                'javascript:window.close()', BASE_POPUP_TEMPLATE);
            exit();
        }

        //  Commit de la transaction {{{
        Database::connection()->startTrans();

        $DCAid = generateClassId('WineDCA');
        $DCADate = date('Y-m-d H:i:s');
        $WineDCAPeriodical = Object::load('WineDCAPeriodical');
        $WineDCAPeriodical->setEditionDate($DCADate);
        $WineDCAPeriodical->setDocumentNo('MvtPeriodical-'.$DCAid);

        $pdfDoc = new Document ;
        $pdfDoc->setType(Document::TYPE_CSV);
        $pdfDoc->setData($DCAdatas[0]);
        // cf au dessus 
        saveInstance($pdfDoc, 'javascript:window.close()');
        $WineDCAPeriodical->setDocument($pdfDoc->getId());
        saveInstance($WineDCAPeriodical, 'javascript:window.close()');

        if (Database::connection()->hasFailedTrans()) {
            trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
            Database::connection()->rollbackTrans();
            Template::errorDialog(E_ERROR_IMPOSSIBLE_ACTION, $_GET['returnURL']);
            exit;
        }
        updateLEMDCA($DCAdatas[1], $WineDCAPeriodical, 0) ;
        Database::connection()->completeTrans();

        $message = _("DCA has been generated").":<br/>
            ".$WineDCAPeriodical->DocumentNo." : 
            <a href=\"?print=1&doc=".$WineDCAPeriodical->Id."\">"._("Download file");

        $showGenerate = FALSE ;
        /// }}}
    }
    // }}}
    
// }}}
// Affichage form {{{ 
} else {
    if(isset($itemsIds) && ! empty($itemsIds) ) {
        $selectDate = FALSE ;
        $message = _("You're about to generate DCA files for movements ids ").":<br/>" . implode(", " , $itemsIds); 
    } else {
        $selectDate = TRUE ;
        $message = _("Please select dates to generate a periodial DCA") ;
    }
}
// }}}
// construction et rendu du formulaire {{{

// on stocke les lemID en session
$session->register('itemsIds', $itemsIds, 2);
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');
require_once ('HTML/QuickForm.php');
$smarty = new Template();
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form = new HTML_QuickForm('WineDCAEdit.php', 'post');
if($selectDate) {
	$form->addElement('date', 'startDate', _('Begin date'),
            array('format'    => I18N::getHTMLSelectDateFormat(),
				  'minYear'   => date('Y') - 5 ,
			      'maxYear'   => date('Y')));
	$form->addElement('date', 'endDate', _('End date'),
            array('format'    => I18N::getHTMLSelectDateFormat(),  /*   H:i  */
			      'minYear'   => date('Y') - 5,
                  'maxYear'   => date('Y')));
    $defaultValues = array( 
        'startDate' => array('d'=>'1', 'm'=>date('m'), 'Y'=>date('Y')),
        'endDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y'))
    );
    $form->setDefaults($defaultValues);
}

$form->addElement('hidden', 'FormSubmitted', 0, 'id="FormSubmitted"');
$form->accept($renderer);

$smarty->assign('form', $renderer->toArray());
$smarty->assign('showGenerate', $showGenerate);
$smarty->assign('selectDate', $selectDate );
$smarty->assign('message', $message);

$content = $smarty->fetch('WineDCA/WineDCAEdit.html');
$title = _('Export DCA');
$js = array('JS_AjaxTools.php', 'js/includes/WineDCAEdit.js');
Template::page($title, $content, $js, array(), BASE_POPUP_TEMPLATE);

// }}}

?>
