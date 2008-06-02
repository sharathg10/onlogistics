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

require_once('Objects/SpreadSheetColumn.php');
require_once('SpreadSheetAddEditTools.php');

/**
 * SpreadSheetAddEdit
 *
 */
class SpreadSheetAddEdit extends GenericAddEdit {
    //SpreadSheetAddEdit::__construct() {{{

    /**
     * Constructeur
     *
     * @param array $params
     * @access public
     * @return void
     */
    public function __construct($params) {
        $params['use_session'] = true;
        $params['use_ajax'] = true;
        parent::__construct($params);
        $this->addJSRequirements('js/includes/SpreadSheetAddEdit.js');
    }

    // }}}
    // SpreadSheetAddEdit::onAfterHandlePostData() {{{

    /**
     * Appelée avant sauvegarde
     *
     * @access public
     * @return void
     */
    public function onAfterHandlePostData() {
        if (isset($_POST['SSC_Name']) && is_array($_POST['SSC_Name'])) {
            $mapper = Mapper::singleton('SpreadSheetColumn');
            $mapper->delete($this->object->getSpreadSheetColumnCollectionIds());
            for ($i=0; $i<count($_POST['SSC_Name']); $i++) {
                // construit la colonne
                $ssc = new SpreadSheetColumn();
                $ssc->setSpreadSheet($this->object->getId());
                list($pname, $ptype, $pclass) = explode(':', $_POST['SSC_PropertyType'][$i]);
                $ssc->setPropertyName($pname);
                $ssc->setPropertyType($ptype);
                $ssc->setPropertyClass($pclass);
                $ssc->setFkeyPropertyName($_POST['SSC_FkeyPropertyName'][$i]);
                $ssc->setName($_POST['SSC_Name'][$i]);
                $ssc->setComment($_POST['SSC_Comment'][$i]);
                $ssc->setDefault($_POST['SSC_Default'][$i]);
                $ssc->setWidth($_POST['SSC_Width'][$i]);
                $ssc->setRequired(isset($_POST['SSC_Required'][$i]));
                $ssc->setOrder($_POST['SSC_Order'][$i]);
                $ssc->save();
            }
        }
    }

    // }}}
    // SpreadSheet_Entity::additionalFormContent() {{{

    /**
     * Colonnes du tableur
     *
     * @access public
     * @return string
     */
    public function additionalFormContent() {
        return "<tr>\n<th colspan=\"4\">Ressources</th>\n<tr>\n"
             . "<tr>\n<td colspan=\"4\"><div align=\"right\">"
             . "<input type=\"button\" id=\"addSSC\" class=\"button\" "
             . "value=\"Ajouter\"/></div></td>\n</tr>\n"
             . "<tr>\n<td colspan=\"4\">\n<ul id=\"sscUL\" "
             . "style=\"margin:0;padding:0;\">" 
             . "</ul>\n</td>\n</tr>\n";
    }

    // }}}
}

?>
