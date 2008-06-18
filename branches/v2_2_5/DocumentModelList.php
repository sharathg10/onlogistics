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
require_once('Objects/DocumentModel.inc.php');
$auth = Auth::Singleton();
$auth->checkProfiles();

$grid = new Grid();

$grid->NewAction('AddEdit', array('Action' => 'Add',
							      'EntityType' => 'DocumentModel',
								  'Profile' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)));

// TODO: faire un gridColumn adapté pour gestion des droits
$editMacro = !in_array($auth->getProfile(), array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW))?
        '%Name%':'<a href="DocumentModelAddEdit.php?domId=%Id%">%Name%</a>';
$grid->NewColumn('FieldMapper', _('Name'), array('Macro' => $editMacro));

$grid->NewColumn('FieldMapperWithTranslation', _('Document type'),
        array('Macro' =>'%DocType%','TranslationMap' => getDocumentTypeArray()));
$grid->NewColumn('FieldMapperWithTranslation', _('Default'),
    array('Macro' => '%Default%', 'TranslationMap' => array(
        0 => _('No'),
        1 => _('Yes'))));
$grid->NewColumn('FieldMapperWithTranslationExpression', _('Logo type'),
        array('Macro' =>'%LogoType%',
            'TranslationMap' => array(DocumentModel::NO_LOGO => _('None'),
           						      DocumentModel::EXPEDITOR => _('Order shipper'),
        							  DocumentModel::DESTINATOR => _('Order addressee'),
							          DocumentModel::ONE_ACTOR => _('%Actor.Name% logo'))));
$order = array('DocType' => SORT_ASC, 'Name' => SORT_ASC);
Template::pageWithGrid($grid, 'DocumentModel', '', array(), $order);

?>
