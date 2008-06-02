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

require_once('LangTools.php');
require_once('FormatNumber.php');

define('I_NOT_DELETED_ENTITY', _('The following service could not be deleted because it is linked to an invoice or to an order: %s'));
define('I_NOT_DELETED_ENTITIES', _('The following services could not be deleted because they are linked to an invoice or to an order: %s'));
/**
 * PrestationAddEdit 
 * 
 */
class PrestationAddEdit extends GenericAddEdit {
    private $_notDeleted = array();
    // PrestationAddEdit::__construct() {{{

    /**
     * __construct 
     * 
     * @param array $params 
     * @access public
     * @return void
     */
    public function __construct($params) {
        $params['use_session'] = true;
        parent::__construct($params);
        if($this->action != GenericController::FEATURE_DELETE) {
            // NOTE: _object n'est construit que lors de l'appel à render
            $prs = Object::load('Prestation', $this->objID);
            $this->title = _('Add/Update service') . ' - ' . $prs->getName();
            $this->addJSRequirements(
                'js/jscalendar/calendar.js',
                getJSCalendarLangFile(),
                'js/jscalendar/calendar-setup.js',
                'JS_AjaxTools.php',
                'js/includes/PrestationAddEdit.js'
            );
        }
    }

    // }}}
    // PrestationAddEdit::additionalFormContent() {{{

    /**
     * additionalContent 
     *
     * Ajoute les tableaux pour les grids éditables des PrestationCustomer et 
     * CostRange.
     * 
     * @access protected
     * @return void
     */
    protected function additionalFormContent() {
        // ajoute les grids éditables des CostRange et PrestationCustomer
        $collections = array('PrestationCustomer', 'CostRange');

        $html = '';
        foreach($collections as $name) {
            $o = Object::load($name);
            $mapping = $o->getMapping();
            $headerCells = '';
            $cells = 1;
            foreach ($mapping as $field=>$properties) {
                if(in_array('addedit', $properties['usedby'])) {
                    $headerCells .= '<td style="color:#ffffff;font-weight:bold;">'.$properties['shortlabel'].'</td>';
                }
                $cells++;
            }
            $headerCells .= "<td>&nbsp;</td>";

            $html .= "<tr><th colspan=\"3\">".$o->getObjectLabel()."</th> "
             . "<th><span id=\"searchform_switch\" onclick=\"fw.dom.toggleElement('".$name."_grid');\" title=\"Masquer/Afficher\"></span></th><tr>\n"
             . "<tr><td colspan=\"4\">\n"
             . "<div class=\"grid\" id=\"".$name."_grid\" style=\"display:none;\">\n"
             . "<table id=\"".$name."TABLE\" cellspacing=\"0\" "
             . "cellpadding=\"0\" border=\"0\" width=\"100%\">\n"
             . "<thead>\n"
             . "<tr>" . $headerCells . "</tr>\n"
             . "</thead>\n" 
             . "<tfoot>\n"
             . "<tr><td colspan=\"".$cells."\" align=\"right\">"
             . "<input type=\"button\" id=\"add".$name."\" class=\"button\" value=\""._('Add')."\"/>"
             . "</td></tr>\n"
             . "</tfoot>\n"
             . "<tbody></tbody>\n"
             . "</table>\n"
             . " </div>\n"
             . "</td></tr>\n";
        }

        // ajoute les layers pour les params specifique aux types de prestations
        $smarty = new Template();
        $smarty->assign('Prestation_FreePeriod', 
            $this->object->getFreePeriod());
        $smarty->assign('PotentialDate', $this->object->getPotentialDate());
        $smarty->assign('PotentialDate_Display', 
            I18N::formatDate($this->object->getPotentialDate()));
        $smarty->assign('Potential', 
            DateTimeTools::hundredthsOfHourToTime($this->object->getPotential()));
        $smarty->assign('checked', 
            ($this->object->getPotential()==0)?'potentialDate':'potentialHour');
        $smarty->assign('Prestation_Tolerance', $this->object->getTolerance());
        $smarty->assign('ToleranceTypeOptions', FormTools::writeOptionsFromArray(
            Prestation::getToleranceTypeConstArray(), 
            $this->object->getToleranceType(), true));
        
        $html .= '<tr><td colspan="4">' . 
            $smarty->fetch('Prestation/PrestationTypesLayers.html') 
            . '</td></tr>';
        
        return $html;
    }

    // }}}
    // PrestationAddEdit::onBeforeHandlePostdata() {{{

    /**
     * onBeforeHandlePostdata 
     *
     * - Une Prestation ne peut pas être associée à une Operation si une autre 
     * Prestation est déjà associée à cette Operation avec un même client 
     * (PrestationCustomer.Actor).
     *
     * @access protected
     * @return void
     */
    protected function onBeforeHandlePostdata() {
        // Recherche les PrestationCustomer associées aux mêmes clients et à une 
        // Prestation associée à la même Operation
        /*if(isset($_POST['PrestationCustomer_Actor']) && 
        $_POST['Prestation_Operation_ID'] != '##') {
            $filter = array();
            $filter2 = array();

            $filter[] = SearchTools::NewFilterComponent('Actor', 
                'Actor.Id', 'In', 
                $_POST['PrestationCustomer_Actor'], 1, 'PrestationCustomer');
            $filter[] = SearchTools::NewFilterComponent('Id', 'Prestation.Id', 'NotEquals',
                $this->object->getId(), 1, 'PrestationCustomer');
            // ajoute les Operations
            if($_POST['Prestation_Operation_ID'] != '##') {
                $filter[] = SearchTools::NewFilterComponent('Operation', 
                    'Prestation.Operation.Id', 'Equals', 
                    $_POST['Prestation_Operation_ID'], 1, 'PrestationCustomer');
            }

            $filter = SearchTools::FilterAssembler($filter);

            $col = Object::loadCollection('PrestationCustomer', $filter, array(), 
            array('Id', 'Prestation'));
            if($col->getCount()>0) {       
                $result = array();
                $count = $col->getCount();
                for($i = 0; $i < $count; $i++) {
                    $item = $col->getItem($i);
                    $result[$item->getId()] = $item->getPrestation()->getName();
                }
                asort($result);

                Template::errorDialog(
                    _('Error while saving service, a conflict exists in the operation/actor pairs of the following services:') . 
                    '<br><ul><li>'.implode('</li><li>', $result).'</li></ul>', 
                    $this->url);
                exit;
            }
        }*/
    }

    // }}}
    // PrestationAddEdit::onAfterHandlePostData() {{{

    /**
     * Sauvegarde les CostRange et PrestationCustomer
     *
     * @return void
     */
    protected function onAfterHandlePostData() {
        $mapper = Mapper::singleton('CostRange');
        $mapper->delete($this->object->getCostRangeCollectionIds());
        if (isset($_POST['CostRange_ID']) && is_array($_POST['CostRange_ID'])) {
            for ($i=0; $i<count($_POST['CostRange_ID']); $i++) {
                $CR = Object::load('CostRange');
                $CR->setId($_POST['CostRange_ID'][$i]);
                $CR->setCost(troncature($_POST['CostRange_Cost'][$i], 3));
                $CR->setCostType($_POST['CostRange_CostType'][$i]);
                $CR->setBeginRange(troncature($_POST['CostRange_BeginRange'][$i]));
                $CR->setEndRange(troncature($_POST['CostRange_EndRange'][$i]));
                $CR->setDepartureZone($_POST['CostRange_DepartureZone'][$i]);
                $CR->setArrivalZone($_POST['CostRange_ArrivalZone'][$i]);
                $CR->setStore($_POST['CostRange_Store'][$i]);
                $CR->setProductType($_POST['CostRange_ProductType'][$i]);
                $CR->setUnitType($_POST['CostRange_UnitType'][$i]);
                $CR->setPrestation($this->object->getId());
                // Object::canBeSaved est surchargé dans CostRange pour la 
                // validation
                try{
                    $CR->save();
                } catch(Exception $exc) {
                    Template::errorDialog($exc->getMessage(), $this->url);
                    exit();
                }
            }
        }

        $mapper = Mapper::singleton('PrestationCustomer');
        $mapper->delete($this->object->getPrestationCustomerCollectionIds());
        if (isset($_POST['PrestationCustomer_ID']) && is_array($_POST['PrestationCustomer_ID'])) {
            for($i=0 ; $i<count($_POST['PrestationCustomer_ID']) ; $i++) {
                $PC = Object::load('PrestationCustomer');
                $PC->setId($_POST['PrestationCustomer_ID'][$i]);
                $PC->setActor($_POST['PrestationCustomer_Actor'][$i]);
                $PC->setName($_POST['PrestationCustomer_Name'][$i]);
                $PC->setPrestation($this->object->getId());
                try {
                    $PC->save();
                } catch(Exception $exc) {
                    Template::errorDialog($exc->getMessage(), $this->url);
                    exit();
                }
            }
        }
        // gestion des layers
        if($_POST['Prestation_Type'] == Prestation::PRESTATION_TYPE_MAINTENANCE) {
            if($_POST['potential'] == 'potentialHour') {
                $this->object->setPotential(DateTimeTools::getHundredthsOfHour(
                    $_POST['Prestation_Potential']));        
            } elseif($_POST['potential'] == 'potentialDate') {
                $this->object->setPotentialDate($_POST['Prestation_PotentialDate']);        
            }
            $this->object->setTolerance($_POST['Prestation_Tolerance']);        
            $this->object->setToleranceType($_POST['Prestation_ToleranceType']);        
        } elseif($_POST['Prestation_Type'] == Prestation::PRESTATION_TYPE_STOCKAGE) {
            $this->object->setFreePeriod($_POST['Prestation_FreePeriod']);
        }

    }

    // }}}
    // PrestationAddEdit::renderType() {{{

    /**
     * renderType 
     * 
     * @access public
     * @return void
     */
    public function renderType() {
        $this->form->addElement('select', 'Prestation_Type', _('Type').' * : ',
            Prestation::getTypeConstArray(), 
            'id="Prestation_Type" onChange="displayLayers();" style="width:100%;"');
        if($this->action == GenericController::FEATURE_ADD && !$this->objID) {
            $this->formDefaults['Prestation_Type'] = Prestation::PRESTATION_TYPE_HIRING;
        } else {
            $this->formDefaults['Prestation_Type'] = 
                $this->object->getType();
        }
    }
    
    // }}}
    // PrestationGrid::onBeforeDelete() {{{

    protected function onBeforeDelete() {
        $okForDelete = array();    
        $prsCol = Object::loadCollection('Prestation', array(
            'Id'=>$this->objID));
        $count = $prsCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $prs = $prsCol->getItem($i);
            $invoiceItemCol = Object::loadCollection('InvoiceItem', 
                array('Prestation' => $prs->getId()));
            $commandItemCol = Object::loadCollection('PrestationCommandItem', 
                array('Prestation' => $prs->getId()));
            if($invoiceItemCol->getCount()>0 || $commandItemCol->getCount()>0) {
                $this->_notDeleted[] = $prs->getName();
            } else {
                $okForDelete[] = $prs->getId(); 
            }
        }
        $this->objID = $okForDelete;
    }

    // }}}
    // PrestationGrid::onAfterDelete() {{{
    
    protected function onAfterDelete() {
        // redirige vers un message d'info
        $msg = false;
        if (count($this->_notDeleted) == 1) {
            $msg = sprintf(I_NOT_DELETED_ENTITY, $this->_notDeleted[0]);
        } else if (count($this->_notDeleted) > 1) {
            $str = "<ul><li>" . implode("</li><li>", $this->_notDeleted) . "</li></ul>"; 
            $msg = sprintf(I_NOT_DELETED_ENTITIES, $str);
        }

        if($msg) {
            Template::infoDialog($msg, $this->guessReturnURL());
            exit();
        }
    }
    
    // }}}
}

?>
