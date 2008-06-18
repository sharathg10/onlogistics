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

require_once('Objects/Ressource.php');

/**
 * RessourceAddEdit
 *
 */
class RessourceGroupAddEdit extends GenericAddEdit {
    // RessourceGroupAddEdit::__construct() {{{

    /**
     * Constructeur
     *
     * @param array $params
     * @access public
     * @return void
     */
    public function __construct($params) {
        $params['use_session'] = true;
        $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
        parent::__construct($params);
        $this->addJSRequirements(
            'JS_AjaxTools.php',
            'js/includes/RessourceGroupAddEdit.js'
        );
    }

    // }}}
    // RessourceGroupAddEdit::onBeforeDelete() {{{

    /**
     * appelée avant suppression
     *
     * @access public
     * @return mixed
     */
    public function onBeforeDelete() {
        $col = Object::loadCollection('RessourceGroup', $this->objID);
        $count = $col->getCount();
        $errors = array();
        for ($i=0; $i<$count; $i++) {
            $obj = $col->getItem($i);
            if (count($obj->getChainTaskCollectionIds()) > 0) {
                $errors[] = $obj->getName();
            }
        }
        if (empty($errors)) {
            return true;
        } else if (count($errors) == 1) {
            $msg = sprintf(
                _('Resource "%s" could not be deleted because it is associated to one or more tasks'),
                $errors[0]
            );
        } else {
            $msg = sprintf(
                _('Resources "%s" could not be deleted because they are associated to one or more tasks'),
                implode('", "', $errors)
            );
        }
        Template::errorDialog($msg, $this->url);
        exit(1);
    }

    // }}}
    // RessourceGroupAddEdit::onAfterHandlePostData() {{{

    /**
     * Appelée avant sauvegarde
     *
     * @access public
     * @return void
     */
    public function onAfterHandlePostData() {
        if (isset($_POST['RRG_Ressource_ID']) && is_array($_POST['RRG_Ressource_ID'])) {
            $mapper = Mapper::singleton('RessourceRessourceGroup');
            $mapper->delete($this->object->getRessourceRessourceGroupCollectionIds());
            for ($i=0; $i<count($_POST['RRG_Ressource_ID']); $i++) {
                // construit le RRG
                $rrg = new RessourceRessourceGroup();
                $rrg->setRessourceGroup($this->object->getId());
                $rrg->setRessource($_POST['RRG_Ressource_ID'][$i]);
                $rrg->setRate($_POST['RRG_Rate'][$i]);
                $rrg->save();
            }
        }
    }

    // }}}
    // RessourceGroupAddEdit::additionalFormContent() {{{

    /**
     * Contenu du grid RessourceRessourceGroup
     *
     * @access public
     * @return void
     */
    public function additionalFormContent() {
        return "<tr><th colspan=\"4\">"._('Resources')."</th><tr>\n"
             . "<tr><td colspan=\"4\"><div align=\"right\">"
             . "<input type=\"button\" id=\"addRRG\" class=\"button\" "
             . "value=\""._('Add')."\"/></div></td></tr>\n"
             . "<tr><td colspan=\"4\"><ul id=\"rrgUL\" "
             . "style=\"margin:0;padding:0;\">"
             . "</ul></td></tr>\n";
    }

    // }}}
}

?>
