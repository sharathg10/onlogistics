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

define('I_NOT_DELETED_ENTITY', _('The following category could not be deleted because it is linked to expenses or receipts or to others categories: %s'));
define('I_NOT_DELETED_ENTITIES', _('The following categories could not be deleted because they are linked to expenses or receipts or to others categories: %s'));
class FlowCategoryAddEdit extends GenericAddEdit {
    /**
     * additionalContent 
     *
     * @access protected
     * @return void
     */
    protected function additionalFormContent() {
        if($this->object->getId() > 0) {
            $grid = new Grid();
            $grid->withNoCheckBox = true;
            $grid->withNoSortableColumn = true;
            $grid->newColumn('FieldMapper', _('Name'), array('Macro'=>'%Name%'));
            return  "<tr><th colspan=\"4\">" . _('Expenses or receipts linked to the category.') .
                "</th><tr>\n" .
                "<tr><td colspan=\"4\">\n" .
                $grid->render('FlowType', false,
                    array('FlowCategory' => $this->object->getId()),
                    array('Name' => SORT_ASC),
                    'GridLite.html') .
                    "</td></tr>";
        }
    }

    private $_notDeleted = array();

    protected function onBeforeDelete() {
        $okForDelete = array();
        $flowCategoryCol = Object::loadCollection('FlowCategory', array(
            'Id'=>$this->objID));
        $count = $flowCategoryCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $flowCategory = $flowCategoryCol->getItem($i);
            // ne doit pas avoir de catégorie fille
            $flowCatTmpCol = Object::loadCollection('FlowCategory', array(
                'Parent'=>$flowCategory->getId()));
            if($flowCatTmpCol->getCount()>0) {
                $this->_notDeleted[] = $flowCategory->getName();
                continue;
            }
            // ne doit pas être lié à un flowType
            $flowTypeCol = Object::loadCollection('FlowType', array(
                'FlowCategory'=>$flowCategory->getId()));
            if($flowTypeCol->getCount()>0) {
                $this->_notDeleted[] = $flowCategory->getName();
                continue;
            }

            $okForDelete[] = $flowCategory->getId();
        }

        $this->objID = $okForDelete;
    }

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
    
}

?>
