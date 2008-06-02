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
require_once('Objects/CustomerFrequency.php');

// authentification (droits définis dans le menu.ini)
$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

// création du grid
$grid = new Grid();
$grid->itemPerPage = 30;

// actions du grid {{{
$grid->NewAction('AddEdit',
    array(
        'Action' => 'Add',
        'EntityType' => 'CustomerFrequency',
        'Query'=>'retURL=' . $_SERVER['PHP_SELF']
    )
);

$grid->NewAction('Delete',
    array(
        'TransmitedArrayName'=>'cufIDs',
        'EntityType'=>'CustomerFrequency'
    )
);

// }}}

// colonnes du grid {{{
$grid->NewColumn('FieldMapper', _('Name'),
    array(
        'Macro' => '<a href="CustomerFrequencyAddEdit.php?cufID=%Id%&retURL='
            . $_SERVER['PHP_SELF'] . '">%Name%</a>'
    )
);
$grid->NewColumn('FieldMapper', _('Potential'),
    array('Macro' => '%Potential.Name%'));
$grid->NewColumn('FieldMapper', _('Min.'),
    array('Macro' => '%Potential.MinValue%'));
$grid->NewColumn('FieldMapper', _('Max.'),
    array('Macro' => '%Potential.MaxValue%'));
$grid->NewColumn('FieldMapper', _('Frequency'),
    array('Macro' => '%Frequency%'));
$grid->NewColumn('FieldMapperWithTranslation', _('Frequency type'),
    array(
        'Macro' => '%Type%',
        'TranslationMap'=>CustomerFrequency::getTypeConstArray()
    )
);
// }}}

// affichage de la page
Template::pageWithGrid($grid, 'CustomerFrequency', _('List of visit frequencies'),
    array(), array('Name'=>SORT_ASC));

?>
