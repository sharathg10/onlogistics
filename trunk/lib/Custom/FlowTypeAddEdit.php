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

define('I_NOT_DELETED_ENTITY', _('The following element could not be deleted because it is linked to accounts or to expanses or receipts: %s'));
define('I_NOT_DELETED_ENTITIES', _('The following elements could not be deleted because they are linked to accounts or to expanses or receipts: %s'));

/**
 * FlowTypeAddEdit 
 * 
 */
class FlowTypeAddEdit extends GenericAddEdit {
    // FlowTypeAddedit::__construct() {{{

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
        $this->addJSRequirements(
            'JS_AjaxTools.php',
            'js/includes/FlowTypeAddEdit.js'
        );
    }

    // }}}
    // FlowTypeAddEdit::additionalFormContent() {{{

    /**
     * additionalContent 
     *
     * Ajoute les tableaux pour les grids éditables des FlowTypeItems.
     * 
     * @access protected
     * @return void
     */
    protected function additionalFormContent() {
        $this->form->addElement('hidden', 'FlowType_InvoiceType', 
            $this->object->getInvoiceType(), 'id="FlowType_InvoiceType"');
        // ajoute les grids éditables des CostRange et PrestationCustomer
        if($this->object->getInvoiceType() == 0) {
            $html = '';
            $o = Object::load('FlowTypeItem');
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
             . "<th>&nbsp;</th><tr>\n"
             . "<tr><td colspan=\"4\">\n"
             . "<div class=\"grid\" id=\"FlowTypeItem_grid\" >\n"
             . "<table id=\"FlowTypeItem_TABLE\" cellspacing=\"0\" "
             . "cellpadding=\"0\" border=\"0\" width=\"100%\">\n"
             . "<thead>\n"
             . "<tr>" . $headerCells . "</tr>\n"
             . "</thead>\n" 
             . "<tfoot>\n"
             . "<tr><td colspan=\"".$cells."\" align=\"right\">"
             . "<input type=\"button\" id=\"addFlowTypeItem\" class=\"button\" value=\""._('Add')."\"/>"
             . "</td></tr>\n"
             . "</tfoot>\n"
             . "<tbody></tbody>\n"
             . "</table>\n"
             . " </div>\n"
             . "</td></tr>\n";
        } else {
            $grid = new Grid();
            $grid->withNoCheckBox = true;
            $grid->withNoSortableColumn = true;
            $grid->newColumn('FieldMapper', _('Name'), array('Macro'=>'%Name%'));
            $grid->newColumn('FieldMapper', _('VAT'), array('Macro'=>'%TVA%'));
            $html =  "<tr><th colspan=\"4\">" .
                "</th><tr>\n" .
                "<tr><td colspan=\"4\">\n" .
                $grid->render('FlowTypeItem', false,
                    array('FlowType' => $this->object->getId()),
                    array('Name' => SORT_ASC),
                    'GridLite.html') .
                "</td></tr>";

        }
        return $html;
    }

    // }}}
    // FlowTypeAddEdit::onAfterHandlePostData() {{{

    /**
     * onAfterHandlePostData 
     *
     * sauve les FlowTypeItems 
     *
     * @access protected
     * @return void
     */
    protected function onAfterHandlePostData() {
        if($this->object->getInvoiceType() == 0) {
            $mapper = Mapper::singleton('FlowTypeItem');
            $mapper->delete($this->object->getFlowTypeItemCollectionIds());
        }
        if (isset($_POST['FlowTypeItem_ID']) && is_array($_POST['FlowTypeItem_ID'])) {
            for ($i=0; $i<count($_POST['FlowTypeItem_ID']); $i++) {
                if(empty($_POST['FlowTypeItem_Name'][$i])) {
                    Template::errorDialog(
                        _('You must provide the expenses and receipts lines name'), 
                        $this->url);
                    exit();
                }
                $o = Object::load('FlowTypeItem');
                $o->setId($_POST['FlowTypeItem_ID'][$i]);
                $o->setTVA($_POST['FlowTypeItem_TVA'][$i]);
                $o->setName($_POST['FlowTypeItem_Name'][$i]);
                $o->setFlowType($this->object->getId());
                try{
                    $o->save();
                } catch(Exception $exc) {
                    Template::errorDialog($exc->getMessage(), $this->url);
                    exit();
                }
            }
        }
    }

    // }}}
    // PrestationGrid::onBeforeDelete() {{{
    /**
     * _notDeleted 
     * 
     * @var array
     * @access private
     */
    private $_notDeleted = array();

    /**
     * onBeforeDelete 
     * 
     * @access protected
     * @return void
     */
    protected function onBeforeDelete() {
        $okForDelete = array();
        $flowTypeCol = Object::loadCollection('FlowType', array(
            'Id'=>$this->objID));
        $count = $flowTypeCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $flowType = $flowTypeCol->getItem($i);
            // ne doit pas être un de ceux utilisé pour les factures
            if($flowType->getInvoiceType() > 0) {
                $this->_notDeleted[] = $flowType->getName();
                continue;
            }
            // ne doit pas être lié à un flow
            $flowCol = Object::loadCollection('Flow', array(
                'FlowType'=>$flowType->getId()));
            if($flowCol->getCount()>0) {
                $this->_notDeleted[] = $flowType->getName();
                continue;
            }
            // ne doit pas être lié à un account
            $filter = SearchTools::newFilterComponent('FlowType', 'FlowType().Id',
                'Equals', $flowType->getId(), 1, 'Account');
            $accountCol = Object::loadCollection('Account', $filter);
            if($accountCol->getCount()>0) {
                $this->_notDeleted[] = $flowType->getName();
                continue;
            }

            $okForDelete[] = $flowType->getId();
        }
        $this->objID = $okForDelete;
    }

    // }}}
    // PrestationGrid::onAfterDelete() {{{
    
    /**
     * onAfterDelete 
     * 
     * @access protected
     * @return void
     */
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
