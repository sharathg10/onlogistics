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

//Database::connection()->debug = true;
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));

SearchTools::ProlongDataInSession();  // prolonge les datas en session
$query = (isset($_REQUEST['prmId']))?'prmId='.$_REQUEST['prmId']:'';

/*  Contruction du formulaire de recherche */
$form = new SearchForm();
if (isset($_REQUEST['prmId'])) {
    $form->addElement('hidden', 'prmId', $_REQUEST['prmId'], array(),
            array('Disable' => true));
}

$form->addElement('text', 'BaseReference', _('Reference'), array(),
        array('Operator' => 'Like'));
$form->addElement('text', 'Name', _('Designation'), array('style="width:100%;"'),
        array('Operator' => 'Like'));

$ProductKindArray = SearchTools::CreateArrayIDFromCollection('ProductKind', array(),
    _('Select one or more features'));

$form->addDynamicElement('select', 'ProductKind', _('Type'),
	array($ProductKindArray, 'multiple size="5"'),
	array('PropertyType' => 'IntValue'));

$form->addAction(array('URL' => 'PromotionAddEdit.php?' . $query,
					   'Caption' => A_CANCEL));

$pageContent = $form->Render();

/*  Affichage du Grid  */
if (true === $form->displayGrid()) {

	$Filter = SearchTools::filterAssembler($form->buildFilterComponentArray());

	define('PROMOTION_PRODUCT_ADDLIST_ITEMPERPAGE', 350);
	$grid = new Grid();

	$grid->itemPerPage = PROMOTION_PRODUCT_ADDLIST_ITEMPERPAGE;

	$grid->NewAction('Redirect', array(
            'Caption' => _('Limit offer on sale to these products'),
	        'TransmitedArrayName' => 'pdtId',
	        'URL' => 'PromotionAddEdit.php?'. $query . '&addRef=1'));
	$grid->NewAction('Redirect', array('Caption' => A_CANCEL,
			'URL' => 'PromotionAddEdit.php?' . $query));

	$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%BaseReference%'));
	$grid->NewColumn('FieldMapper', _('Designation'),array('Macro' => '%Name%'));

	$ProductMapper = Mapper::singleton('Product');

	$grid->setMapper($ProductMapper);
	$Order = array('BaseReference' => SORT_ASC);

	$result = $grid->execute($Filter, $Order);

	if (Tools::isException($result)) {
		Template::errorDialog($result->GetMessage(), $_SERVER['PHP_SELF'] . $query);
		exit;
	}
	else {
        Template::page(
            _('Limit offer on sale to some products'),
            $pageContent . $result . '</form>'
        );
	}
}

else {   //  on n'affiche que le formulaire de recherche, pas le Grid
    Template::page(
        _('Limit offer on sale to some products'),
        $pageContent . '</form>'
    );
}

?>