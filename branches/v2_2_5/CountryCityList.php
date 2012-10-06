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
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_DIR_COMMERCIAL,UserAccount::PROFILE_COMMERCIAL));


/*  Contruction du formulaire de recherche */
$form = new SearchForm('CountryCity');

$CountryArray = SearchTools::createArrayIDFromCollection(
        'Country', array(), _('Select a country'));
$form->addElement('select', 'Country', _('Country'), array($CountryArray));
$form->addElement('text', 'State', _('District'), array(),
        array('Path' => 'CityName.Department.State.Name'));
$form->addElement('text', 'Department', _('Department'), array(),
        array('Path' => 'CityName.Department.Name'));
$form->addElement('text', 'CityName', _('City'), array(),
        array('Path' => 'CityName.Name'));
$form->addElement('text', 'Zip', _('Zip code'), array(),
        array('Path' => 'Zip.Code'));
$form->addAction(array('URL' => 'CountryCityAddEdit.php',
        'Caption' => _('Add city')));
$form->addAction(array('URL' => 'DepartmentAddEdit.php',
		'Caption' => _('Add department')));
$form->addAction(array('URL' => 'StateAddEdit.php',
        'Caption' => _('Add district')));


/*  Affichage du Grid  */
if (true === $form->DisplayGrid()) {
	/*  Construction du filtre  */
	$Filter = SearchTools::FilterAssembler($form->BuildFilterComponentArray());

	$grid = new Grid();
	$grid->itemPerPage = 100;

	$grid->NewAction('Redirect', array('Caption' => _('City'),
        'Title' => _('Update city'),
        'URL' => 'CountryCityAddEdit.php?ccId=%d'));
	$grid->NewAction('Redirect', array('Caption' => _('Department'),
        'Title' => _('Update department'),
		'URL' => 'DepartmentAddEdit.php?ccId=%d'));
	$grid->NewAction('Redirect', array('Caption' => _('District'),
        'Title' => _('Update district'),
		'URL' => 'StateAddEdit.php?ccId=%d'));

	$grid->NewColumn('FieldMapper', _('City'), array('Macro' => '%CityName.Name%'));
	$grid->NewColumn('FieldMapper', _('Zip code'), array('Macro' => '%Zip.Code%'));
	$grid->NewColumn('FieldMapper', _('Department'),
            array('Macro' => '%CityName.Department.Name%'));
	$grid->NewColumn('FieldMapper', _('District'),
            array('Macro' => '%CityName.Department.State.Name%'));

	$Order = array('CityName.Name' => SORT_ASC);

	$form->displayResult($grid, true, $Filter, $Order);
} // fin FormSubmitted

else { // on n'affiche que le formulaire de recherche, pas le Grid
	Template::page('', $form->render() . '</form>');
}
?>
