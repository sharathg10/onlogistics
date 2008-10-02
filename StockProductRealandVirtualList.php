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
require_once('SQLRequest.php');
require_once('Objects/Product.inc.php');
require_once('Objects/MovementType.const.php');
require_once('MixedObjects/ProductQuantity.php');

$auth = Auth::Singleton();
$auth->checkProfiles();
// Les donnees affichees dependent de l'Actor relie a l'user connecte
//Database::connection()->debug = true;
define('STOCK_ITEMPERPAGE', 300);

/*  Contruction du formulaire de recherche */
$form = new SearchForm('Licence');
// On fait suivre ce qui passe dans l'URL
//$form->BuildHiddenField(array('actorID' => $_REQUEST['actorID']));
$form->addElement('text', 'BaseReference', _('Reference'));
$form->addElement('text', 'Name', _('Designation'));

if (in_array($auth->getProfile(), array(UserAccount::PROFILE_OWNER_CUSTOMER, UserAccount::PROFILE_SUPPLIER_CONSIGNE))) {
    $owners = SearchTools::createArrayIDFromCollection(
            'Actor', array('Id' => $auth->getActorId()));
}else {
    $owners = SearchTools::createArrayIDFromCollection(
            'Actor', array(),_('Select an actor'));
}
$form->addElement('select', 'Owner', _('Owner'), array($owners));
$checked = (!isset($_REQUEST['formSubmitted']))?array('', 'checked="checked"'):array();
$form->addElement('checkbox', 'Activated', _('Active'), $checked);
$form->addElement('checkbox', 'NotActivated', _('Inactive'), array(),
        array('Path' => 'Activated', 'Operator' => 'NotEquals'));
$form->addElement('checkbox', 'WithDate', _('At a fixed date'),
        array('', 'onclick="checkDate();"'));
$dateFormat = array('minYear' => date('Y') - 12,
				    'maxYear' => date('Y'),
				    'Value' => array('Date' => array('Y' => date('Y'))));
$form->addElement('date', 'Date', '', $dateFormat);


/*  Affichage du Grid  */
if (true === $form->displayGrid()) {
	/* ATTENTION: comportement particulier ici:
	 * Si on selectionne une date donnee, on va chercher les infos dans les LPQ,
	 * puis les LEM, sinon dans les LPQ seulement
	*/
	// Evite les interaction entre $_POST et $_SESSION
	SearchTools::cleanYesNoDataSession('Activated', 'NotActivated');
	SearchTools::cleanCheckBoxDataSession('WithDate');

    // Met les criteres de recherche en session (habituellmt fait
    // par $form->buildFilterComponentArray() )
    SearchTools::inputDataInSession();
	// On a selectionne ou non une date pour la recherche
	$withDate = SearchTools::requestOrSessionExist('WithDate') !== false;
    // init du grid
	$grid = new Grid();
	$grid->itemPerPage = STOCK_ITEMPERPAGE;
	$grid->withNoSortableColumn = true;
	$grid->withNoCheckBox = true;
	$grid->customizationEnabled = true;
	// Ici pour corriger le itemPerPage en fonction d'une pref, si necessaire
	$grid->checkPreferences();

	$grid->NewAction('Export', array('FileName' => 'Stock'));
	$grid->NewColumn('FieldMapper', _('Reference'), array('Macro' => '%BaseReference%'));
	$grid->NewColumn('FieldMapper', _('Designation'), array('Macro' => '%Name%'));
	$grid->NewColumn('FieldMapper', _('Quantity'),
            array('Macro' => '%Quantity|formatnumber@3@1% %SellUnitTypeShortName%',
                  'DataType' => 'numeric',
                  'Sortable' => false));
	$acmEntryCol = $grid->NewColumn('FieldMapper', _('Entries'),
            array('Macro' => '%ACMEntryQuantity|formatnumber@3@1%',
                  'DataType' => 'numeric'));
	$acmExitCol  = $grid->NewColumn('FieldMapper', _('Issues'),
            array('Macro' => '%ACMExitQuantity|formatnumber@3@1%',
                  'DataType' => 'numeric'));
	// Affiche ssi pas de critere de date saisi
    $grid->NewColumn('FieldMapper', _('Virtual qty'),
            array('Macro' => '%VirtualQuantity|formatnumber@3@1%',
                  'DataType' => 'numeric',
                  'Enabled' => (!$withDate)));
	$grid->NewColumn('FieldMapper', _('Minimum qty'),
            array('Macro' => '%MiniQuantity|formatnumber@3@1%',
                  'DataType' => 'numeric'));
	$grid->NewColumn('FieldMapperWithTranslation', _('Category'), array(
		    'Macro' => '%Category%', 'TranslationMap' => getCategoryArray(),
		    'Sortable' => false));
	$pdtTypeCol = $grid->NewColumn('FieldMapper', _('Type'), array(
		    'Macro' => '%ProductType%', 'Sortable' => false));
    $grid->hiddenColumnsByDefault = array($acmEntryCol->index,
        $acmExitCol->index, $pdtTypeCol->index);
    $grid->checkPreferences(); // nécessaire ici

	$Collection = new Collection();
	$sql = request_StockProductRealandVirtualList(
            $auth->getActorId(), $auth->getProfile(), $withDate);
	if (isset($_REQUEST['export'])) {  // si export demande!
		$rs = Database::connection()->execute($sql);
	} else {
		/* Naturellement, la pagination ne marche pas qd on passe une collection
		 a Grid->render(): il faut ajouter les 5 lignes suivantes */
		$pageIndex = isset($_REQUEST['PageIndex'])?$_REQUEST['PageIndex']:0;
		$rs = Database::connection()->pageExecute($sql, $grid->itemPerPage, $pageIndex);
		$Collection->lastPageNo = $rs->_lastPageNo;
		$Collection->currentPage = $rs->_currentPage;
	}

	while ($rs && !$rs->EOF) {
		$pq = new ProductQuantity();
		$pq->setId($rs->fields['pdtId']);
		$pq->setBaseReference($rs->fields['baseReference']);
		$pq->setName($rs->fields['pdtName']);
		$pq->setSellUnitTypeShortName($rs->fields['shortName']);
		$pq->setQuantity((isset($rs->fields['qty']))?$rs->fields['qty']:0);
		$pq->setVirtualQuantity($rs->fields['virtualQuantity']);
		$pq->setMiniQuantity($rs->fields['minimumStoredQuantity']);
		$pq->setCategory((isset($rs->fields['category']))?$rs->fields['category']:0);
		$pq->setProductType((isset($rs->fields['productType']))?$rs->fields['productType']:0);
        // entrées prévues, non affiché par défaut
        if (!in_array($acmEntryCol->index, $grid->hiddenColumnsByUser)) {
            $rs_acm_entries = request_stockACMQuantity($rs->fields['pdtId'], true, $withDate);
            if ($rs_acm_entries) {
                $pq->setACMEntryQuantity($rs_acm_entries->fields['quantity']);
            }
        }
        // sorties prévues, non affiché par défaut
        if (!in_array($acmExitCol->index, $grid->hiddenColumnsByUser)) {
            $rs_acm_exits   = request_stockACMQuantity($rs->fields['pdtId'], false, $withDate);
            if ($rs_acm_exits) {
                $pq->setACMExitQuantity($rs_acm_exits->fields['quantity']);
            }
        }
        $Collection->setItem($pq);
        unset($pq);
        $rs->moveNext();
	}
	// Placé apres les setItem(), sinon, écrasé...
	if (!isset($_REQUEST['export'])) {  // si pas dans le cadre d'un export
		$Collection->totalCount = $rs ? $rs->maxRecordCount() : 0;
	}
	if ($rs && $withDate) {
		// Les LEM:
		// Si pas de critere sur le Product, intervention de la pagination:
		// inutile de parcourir les LEM concernant les Product hors pagination!
        $baseReference = SearchTools::requestOrSessionExist('BaseReference');
        $name = SearchTools::requestOrSessionExist('Name');
		if (($baseReference !== false && $baseReference != '')
                || ($name !== false && $name != '')
				|| (SearchTools::requestOrSessionExist('Activated') !== false)
                || (SearchTools::requestOrSessionExist('NotActivated') !== false)) {
		    $rs->moveLast();
			$maxBaseReference = $rs->fields['baseReference'];
			$addWhere = 'PDT._BaseReference <= "' . $maxBaseReference . '" AND ';
		}
		else {
			$addWhere = '';
		}

		$sql2 = request_StockProductAtDate(
                $auth->getActorId(), $auth->getProfile(), $addWhere);
		$rs2 = Database::connection()->execute($sql2);
		while (!$rs2->EOF){
			$item = $Collection->getItemById($rs2->fields['pdtId']);
			if (false === $item) {
                $rs2->moveNext();
				continue;
			}
			// Si entree et non annulateur ou EXM associe est une MovementType::TYPE_EXIT et lem annulateur
			$coef = (($rs2->fields['entryExit'] == MovementType::TYPE_ENTRY && $rs2->fields['cancelledMvt'] == 0)
					|| ($rs2->fields['entryExit'] == MovementType::TYPE_EXIT && $rs2->fields['cancelledMvt'] > 0))?-1:1;
			$item->setQuantity($item->getQuantity() + ($coef * $rs2->fields['qty']));
			$rs2->moveNext();
		}
	}

	$js = array('js/includes/StockProductRealandVirtualList.js');

    $form->setItemsCollection($Collection);
    // avec $form->displayResult($grid, true, array(), array(), '', $js);
    // l'extraction csv ne tient pas compte des critères du searchform.
    Template::page('', $form->render() . $grid->render($Collection, true) . '</form>', $js);
}

else {   //  on n'affiche que le formulaire de recherche, pas le Grid
    SearchTools::saveLastEntitySearched();
    Template::page('', $form->render() . '</form>',
            array('js/includes/StockProductRealandVirtualList.js'));
}
?>
