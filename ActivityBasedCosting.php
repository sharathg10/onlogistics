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
require_once('Objects/Task.inc.php');
require_once('Objects/ActivatedChainTask.php');

$auth = Auth::Singleton();
$auth->checkProfiles();

$form = new SearchForm('ActivatedChainTask');
// numéro de commande
$form->addElement('text', 'Command', _('Order'), array(), 
    array(
        'Path' => 'ActivatedOperation.ActivatedChain.CommandItem().Command.CommandNo'
    )
);
// ressources
$form->addElement('text', 'Ressource', _('Resource'), array(), 
    array(
        'Disable' => true
    )
);
// select nom de tâche
$form->addElement('select', 'Task', _('Task'), 
    array(SearchTools::createArrayIDFromCollection('Task', array(), _('Select a task'))), 
    array('Operator' => 'Equals')
);
// produit associé
$form->addElement('text', 'Product', _('Product'), array(), 
    array(
        'Disable' => true
    )
);
// dates
$form->addElement('checkbox', 'DateOrder1', _('Filter by date'),
    array(
        '', 'onClick="$(\\\'Date1\\\').style.display'
          . '=this.checked?\\\'block\\\':\\\'none\\\';"'
    )
);
$form->addDate2DateElement(
    array('Name'   => 'BeginDate', 'Path' => 'Begin'),
    array('Name'   => 'EndDate', 'Path' => 'Begin'),
    array(
        'EndDate' => array('d'=>date('d'), 'm'=>date('m'), 'Y'=>date('Y')),
        'BeginDate' => array('Y'=>date('Y'))
    )
);


// Affichage du Grid
if (true === $form->displayGrid()) {
    // Evite les interaction entre $_POST et $_SESSION
	SearchTools::cleanCheckBoxDataSession('DateOrder1');

	$grid = new Grid();
	$grid->itemPerPage = 300;

    // actions
	$grid->NewAction('Print', array());
	$grid->NewAction('Export', array('FileName' => 'ActivityBasedCosting'));

    // colonnes
    // numéro de commande
	$grid->NewColumn('FieldMapper', _('Order'),
        array(
            'Macro' => '%ActivatedOperation.ActivatedChain.CommandItem()[0].Command.CommandNo%',
            'Sortable' => false
        )
    );
    // référence de la chaîne
    $grid->NewColumn('FieldMapper', _('Chain'), 
        array('Macro'=>'%ActivatedOperation.ActivatedChain.Reference%'));
    // nom de la tâche
    $grid->NewColumn('FieldMapper', _('Task'),
        array('Macro'=>'%Task.Name%'));
    // état de la tâche
    $grid->NewColumn('FieldMapperWithTranslation', _('State'),
        array('Macro'=>'%State%', 
              'TranslationMap'=>ActivatedChainTask::getStateConstArray()));
    // date de début prévue
    $grid->NewColumn('FieldMapper', _('Expected beginning date'),
        array('Macro' => '%Begin|formatdate%'));
    // date de début réelle
    $grid->NewColumn('FieldMapper', _('Actual beginning date'),
        array('Macro' => '%RealBegin|formatdate%'));
    // durée prévue
	$grid->NewColumn('FieldMapper', _('Expected duration'),
        array('Macro' => '%Duration|formatduration%'));
    // durée réelle
	$grid->NewColumn('FieldMapper', _('Actual duration'),
        array('Macro' => '%RealDuration|formatduration%'));
    // coût prévu
	$grid->NewColumn('FieldMapper', _('Expected cost'),
        array('Macro' => '%Cost|formatnumber%'));
    // coût réel
	$grid->NewColumn('FieldMapper', _('Actual cost'),
        array('Macro' => '%RealCost|formatnumber%'));
    // modèle de ressources
	$grid->NewColumn('FieldMapper', _('Resource model'),
        array('Macro' => '%RessourceGroup.Name%'));

	// Construction du filtre et du tableau order
    $filterComponent = $form->buildFilterComponentArray();
    $rrgFilter = array();
    if(isset($_REQUEST['Ressource']) && !empty($_REQUEST['Ressource'])) {
        $rrgFilter['Ressource.Name'] = $_REQUEST['Ressource'];
    }
    if(isset($_REQUEST['Product']) && !empty($_REQUEST['Product'])) {
        $rrgFilter['Ressource.Product.BaseReference'] = $_REQUEST['Product'];
    }
    if(count($rrgFilter)>0) {
        $rgArray = array();
        $rrgCol = Object::loadCollection('RessourceRessourceGroup', $rrgFilter);
        $count = $rrgCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $rrg = $rrgCol->getItem($i);
            $rgArray[] = $rrg->getRessourceGroup();
        }
        $filterComponent[] = SearchTools::NewFilterComponent('RessourceGroup', 'RessourceGroup', 'In', $rgArray, 1);
    }
    $filter = SearchTools::filterAssembler($filterComponent);
    $order = array(
        'Begin'=>SORT_DESC
        //'ActivatedOperation.ActivatedChain.CommandItem()[0].Command.CommandNo' => SORT_ASC
    );
	$form->displayResult($grid, true, $filter, $order);
} else { 
    // on n'affiche que le formulaire de recherche, pas le Grid
	Template::page('', $form->render() . '</form>');
}

?>
