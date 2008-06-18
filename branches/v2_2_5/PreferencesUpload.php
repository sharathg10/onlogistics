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

require_once 'config.inc.php';
require_once('HTML/QuickForm.php');
require_once('HTML/QuickForm/advmultiselect.php');
require_once('HTML/QuickForm/Renderer/ArraySmarty.php');

$auth = Auth::singleton();
$auth->checkProfiles();

if (isset($_POST['Ok'])) {
    // le formulaire a été soumis
    Preferences::set('UploadAllowedMimeTypes', $_POST['MimeType_IDs']);
    Preferences::save();
    Template::infoDialog(
        _('Preferences were successfully saved.'),
        $_SERVER['PHP_SELF']
    );
    exit(0);
}

// traitement du template
$smarty   = new Template();
$renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form     = new HTML_QuickForm('PreferencesUpload', 'post');

// crée le select multiple avec advmultiselect
$arr = SearchTools::createArrayIDFromCollection(
    'MimeType',
    array(),
    '',
    'toString',
    array('Extension'=>SORT_ASC)
);
$form->addElement(
    'advmultiselect',
    'MimeType_IDs',
    array(
        _('Select file types you want to allow for upload'),
        _('Available file types'),
        _('Allowed file types')
    ),
    $arr,
    array('size' => '15', 'style'=>'width:100%;')
);
$form->setDefaults(array(
    'MimeType_IDs' => Preferences::get('UploadAllowedMimeTypes', array())
));
$form->accept($renderer);
$smarty->assign('form', $renderer->toArray());

// affichage de la page
$pageContent = $smarty->fetch('Preferences/PreferencesUpload.html');
Template::page(_('Upload preferences'), $pageContent);

?>
