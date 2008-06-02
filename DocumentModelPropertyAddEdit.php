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
require_once('Objects/DocumentModel.php');
require_once ('Objects/Property.php');
require_once ('Objects/DocumentModelProperty.php');

define('E_CHECK_ORDER', 'Please verify properties order.');

$auth = Auth::Singleton();
$auth->checkProfiles(array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW));


// variables
$session = Session::singleton();
$displayGrid = isset($_REQUEST['grid'])?$_REQUEST['grid']:1;
$title = _('Select fields to display and select their order ')
           . _('then validate by clicking the "Ok" button and close the popup window');

// Vérifie que le domId est bien en session
if(isset($_SESSION['domId'])) {
    $domId = $_SESSION['domId'];
} else {
    Template::errorDialog(E_MSG_TRY_AGAIN, 'DocumentModelList.php', BASE_POPUP_TEMPLATE);
    exit;
}

// Supprime la "mémoire" des gridItems
$griditems = SearchTools::getGridItemsSessionName();
unset($_SESSION[$griditems]);

SearchTools::prolongDataInSession();

$DomMapper = Mapper::singleton('DocumentModel');
$Dom = $DomMapper->load(array('id' => $domId));
// Vérifie que la collection n'a pas déjà été modifiée sans être enregistrée.
if(isset($_SESSION['domPropCol'])) {
    $actualDomPropCol = $_SESSION['domPropCol'];
} else {
//    $DomMapper = Mapper::singleton('DocumentModel');
//	$Dom = $DomMapper->load(array('id' => $domId));
	$actualDomPropCol = $Dom->GetDocumentModelPropertyCollection();
}

//click sur ok
if(isset($_REQUEST['toRemove'])) {
    /**
     * Le tableau $_REQUEST['toRemove'] ne contient pas des id mais des
     * property ou propertyType, il faut mouliner pour passer en session
     * les ids des enregistrments à supprimer.
     */
    if(!isset($_SESSION['domPropToRemove'])) {
        $_SESSION['domPropToRemove'] = array();
    }

    $domPropCol = $Dom->getDocumentModelPropertyCollection();
    $count = $domPropCol->getCount();
    for ($i=0 ; $i<$count ; $i++) {
        $domProp = $domPropCol->getItem($i);
        if( (in_array($domProp->getPropertyId(), explode('|', $_REQUEST['toRemove']))
             && $domProp->getPropertyType()==0) ||
             (in_array($domProp->getPropertyType(), explode('|', $_REQUEST['toRemove']))
            && !($domProp->getProperty() instanceof Property))) {
            $_SESSION['domPropToRemove'][] = $domProp->getId();
        }
    }
}

if(isset($_REQUEST['gridItems'])) {
    /*
     * Effectue les modifications et enregistre la collection en session
     * $_SESSION['domPropCol'] pour l'enregistrer avec une transaction lors du click sur ok
     *dans documentModelAddEdit.php
     */

    //collection de documentModelProperty qui sera mise en session
    $newDomPropCol = new Collection;

    $usingOrder = array();        // pour checker l'ordre des property
    //$domPropToRemove = array();   // tableau d'id des property à supprimer
    $traitedProperties = array(); // pour ne pas traiter deux fois la même property



    // cas du grid 1
    if(isset($_REQUEST['DocumentModelProperty_Order'])) {
        $count = $actualDomPropCol->getCount();
        //pour chaque ancienne property
        for ($i=0 ; $i<$count ; $i++) {
            $domProperty = $actualDomPropCol->getItem($i);
            if(in_array($domProperty->getPropertyId(), $_REQUEST['gridItems'])) {
                //vérification sur l'ordre
                $propOrder = $_REQUEST['DocumentModelProperty_Order'][$domProperty->getPropertyId()];
                if( ($propOrder <= 0) || (in_array($propOrder, $usingOrder)) ) {
                    Template::errorDialog(E_CHECK_ORDER,
                        'DocumentModelPropertyAddEdit.php?grid=' . $displayGrid,
                        BASE_POPUP_TEMPLATE);
        	        exit;
                }
                $domProperty->setOrder($propOrder);
                $traitedProperties[] = $domProperty->getPropertyId();
                $usingOrder[] = $propOrder;
                $newDomPropCol->setItem($domProperty);
                unset($domProperty);
            }
        }
        //pour chaque nouvelle property
        foreach ($_REQUEST['gridItems'] as $key=>$propertyId) {
            // si elle n'à pas déjà été traité
            if(!in_array($propertyId, $traitedProperties)) {
                //vérification sur l'ordre
                $propOrder = $_REQUEST['DocumentModelProperty_Order'][$propertyId];
                if( ($propOrder <= 0) || (in_array($propOrder, $usingOrder)) ) {
                    Template::errorDialog(E_CHECK_ORDER,
                        'DocumentModelPropertyAddEdit.php?grid=' . $displayGrid,
                        BASE_POPUP_TEMPLATE);
        	        exit;
                }
                //création d'une nouvelle domproperty
                $domProperty = new DocumentModelProperty();
                $domProperty->setOrder($propOrder);
                $domProperty->setDocumentModel($domId);
                $domProperty->setProperty($propertyId);
                $domProperty->setPropertyType(0);
                //on l'ajoute
                $newDomPropCol->setItem($domProperty);
                unset($domProperty);
                $usingOrder[] = $propOrder;
            }
        }

        /*
         * Il faut compléter la colection à mettre en session avec les domproperty
         * du second grid
         */
        $count=$actualDomPropCol->getCount();
        for ($i=0 ; $i<$count ; $i++) {
            $domProperty = $actualDomPropCol->getItem($i);
            if(!($domProperty->getProperty() instanceof Property)) {
                $newDomPropCol->setItem($domProperty);
            }
            unset($domProperty);
        }
    } elseif (isset($_REQUEST['DocumentModelCell_Order'])) {

        $count = $actualDomPropCol->getCount();
        //pour chaque ancienne property
        for ($i=0 ; $i<$count ; $i++) {
            $domProperty = $actualDomPropCol->getItem($i);
            if(in_array($domProperty->getPropertyType(), $_REQUEST['gridItems'])) {
                //vérification sur l'ordre
                $propOrder = $_REQUEST['DocumentModelCell_Order'][$domProperty->getPropertyType()];
                if( ($propOrder <= 0) || (in_array($propOrder, $usingOrder)) ) {
                    Template::errorDialog(E_CHECK_ORDER,
                        'DocumentModelPropertyAddEdit.php?grid='.$displayGrid,
                        BASE_POPUP_TEMPLATE);
        	        exit;
                }
                $domProperty->setOrder($propOrder);
                $traitedProperties[] = $domProperty->getPropertyType();
                $usingOrder[] = $propOrder;
                $newDomPropCol->setItem($domProperty);
                unset($domProperty);
            }
        }
        //pour chaque nouvelle property
        foreach ($_REQUEST['gridItems'] as $key=>$propertyType) {
            // si elle n'à pas déjà été traité
            if(!in_array($propertyType, $traitedProperties)) {
                //vérification sur l'ordre
                $propOrder = $_REQUEST['DocumentModelCell_Order'][$propertyType];
                if( ($propOrder <= 0) || (in_array($propOrder, $usingOrder)) ) {
                    Template::errorDialog(E_CHECK_ORDER,
                        'DocumentModelPropertyAddEdit.php?grid='.$displayGrid,
                        BASE_POPUP_TEMPLATE);
        	        exit;
                }
                //création d'une nouvelle domproperty

                unset($domProperty);
                $domProperty = new DocumentModelProperty();
                $domProperty->setOrder($propOrder);
                $domProperty->setDocumentModel($domId);
                $domProperty->setPropertyType($propertyType);
                //on l'ajoute
                $newDomPropCol->setItem($domProperty);
                unset($domProperty);
                $usingOrder[] = $propOrder;
            }
        }

        /*
        Il faut compléter la colection à mettre en session avec les domproperty
        du premier grid
        */

        $count=$actualDomPropCol->getCount();
        for ($i=0 ; $i<$count ; $i++) {
            $domProperty = $actualDomPropCol->getItem($i);
            if($domProperty->getPropertyType()==0) {
                $newDomPropCol->setItem($domProperty);
            }
            unset($domProperty);
        }
    }

    $actualDomPropCol = $newDomPropCol;
    unset($_SESSION['domPropCol']);
    $_SESSION['domPropCol'] = $newDomPropCol;
} elseif (isset($_REQUEST['toRemove'])) {
    // cas où l'on supprime toutes les propertys
    $actualDomPropCol = false;
}

// Construction du grid
$grid = new Grid();
$selectedItems = array();

if($displayGrid==1) {
    /*
     * Gestion des property affichablent dans le champ designation
     * le grid est basé sur l'entité Property, les property se trouvant dans
     * DocumentModelProperty.Property avec
     * DocumentModelProperty.PropertyType = 0 sont cochées
     */
    $propertyMapper = Mapper::singleton('Property');
    $propertyCol = $propertyMapper->loadCollection();

    // sélectionne les propertydéjà utilisées
    if ($actualDomPropCol) {
        $count = $actualDomPropCol->getCount();
        for ($i=0 ; $i<$count ; $i++) {
            $domproperty = $actualDomPropCol->getItem($i);
            if($domproperty->getPropertyId() > 0) {
                $selectedItems[] = $domproperty->getPropertyId();
            }
        }
    }
    $grid->NewColumn('FieldMapper', _('Designation'),
    array('Macro'=>'%DisplayName%'));

    $collection = $propertyCol;
} elseif ($displayGrid==2) {
    /*
     * Gestion de la personnalisation des cellules du tableau
     * le grid est basé sur les constantes correspondant au
     * PropertyType d'une DocumentModelProperty sont cochées
     */
    require_once ('Objects/DocumentModelProperty.inc.php');

    $domPropertyCol = new Collection();
    $domPropertyCol->acceptDuplicate = false;
    $cells = DocumentModelProperty::getPropertyTypeConstArray();

    $alreadyDo = array();
    if($actualDomPropCol) {
        // ajoute et sélectionne les DocumentModelProperty
        $count = $actualDomPropCol->getCount();
        for ($i=0 ; $i<$count ; $i++) {
            $domProperty = $actualDomPropCol->getItem($i);
            if($domProperty->getPropertyType() != 0) {
                $domProperty->setId($domProperty->getPropertyType());
                $domPropertyCol->setItem($domProperty);
                $alreadyDo[] = $domProperty->getPropertyType();
                $selectedItems[] = $domProperty->getId();
            }
            unset($domProperty);
        }
    }

    // Ajoute les autres DocumentModelProperty
    foreach ($cells as $key=>$value) {
        if(!in_array($key, $alreadyDo) && $key>0) {
            $domProperty = new DocumentModelProperty();
            $domProperty->setId($key);
            $domProperty->setPropertyType($key);
            $domPropertyCol->setItem($domProperty);
            unset($domProperty);
        }
    }
    $grid->NewColumn('FieldMapperWithTranslation',
                     _('Cell title'),
                     array('Macro'=>'%PropertyType%',
                           'TranslationMap' =>  $cells));

    $collection = $domPropertyCol;
}

$grid->NewColumn('PropertyOrder', _('Order'), array('Sortable'=>false));
$grid->setPreselectedItems($selectedItems);
$grid->NewAction('Redirect',
        array('Caption' => _('Save'),
              'URL' => 'DocumentModelPropertyAddEdit.php?domId='.$domId));
$grid->NewAction('Close');
// Affichage
$render = '<br />' . $title.'<br /><br /><form>' . $grid->render($collection)
        . '</form>';
Template::page('', $render, array(), array(), BASE_POPUP_TEMPLATE);

?>
