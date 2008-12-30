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
 * @version   SVN: $Id: ProductModelAddEdit.php 287 2008-12-10 17:36:46Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

class ProductModelAddEdit extends GenericAddEdit {
    // Constructeur {{{

    /**
     * Constructeur
     *
     * @param array $params tableau de paramètres
     * @return void
     */
    public function __construct($params=array()) {
        $params['use_session'] = true;
        parent::__construct($params);
    }

    // }}}
    // ProductModelAddEdit::onBeforeDisplay() {{{

    /**
     * Appelé avant affichage
     *
     * @return void
     */
    public function onBeforeDisplay() {
        try {
            $this->object->canBeDeleted();
        } catch (Exception $exc) {
            Template::errorDialog(
                _('This model can not be modified because it is already used in one or more orders'),
                $this->guessReturnURL()
            );
            exit(1);
        }
    }

    // }}}
    // ProductModelAddEdit::delete() {{{

    /**
     * Méthode qui gère l'action delete, supprime l'objet dans une transaction.
     *
     * @access protected
     * @return void
     */
    protected function delete() {
        $this->onBeforeDelete();
        Database::connection()->startTrans();
        $mapper = Mapper::singleton($this->clsname);
        $emptyForDeleteProperties = call_user_func(array($this->clsname,
            'getEmptyForDeleteProperties'));
        $notDeletedObjects = array();
        // il y a des check auto on supprime un à un car les verif ne sont
        // pas faites par Mapper::delete() mais par Object::delete()
        $col = $mapper->loadCollection(array('Id'=>$this->objID));
        $count = $col->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $o = $col->getItem($i);
            try {
                $pdtCol = $o->getProductCollection();
                foreach ($pdtCol as $pdt) {
                    foreach ($pdt->getChainCollection() as $chain) {
                        if ($chain->getReference() == $pdt->getBaseReference()) {
                            $chain->delete();
                        }
                    }
                }
                $o->delete();
            } catch (Exception $exc) {
                $notDeletedObjects[] = $o->toString(); //. ': ' . $exc->getMessage();
            }
        }
        if (Database::connection()->hasFailedTrans()) {
            $err = Database::connection()->errorMsg();
            trigger_error($err, E_USER_WARNING);
            Database::connection()->rollbackTrans();
            Template::errorDialog(E_ERROR_SQL . '.<br/>' . $err, $this->guessReturnURL());
            exit;
        }
        Database::connection()->completeTrans();
        if(!empty($notDeletedObjects)) {
            Template::infoDialog(
                sprintf(I_NOT_DELETED_WITH_LIST,
                implode('</li><li>', $notDeletedObjects)),
                $this->guessReturnURL());
            exit;
        }
        $this->onAfterDelete();
    }

    // }}}
    // ProductModelAddEdit::onAfterHandlePostData() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function onAfterHandlePostData() {
        require_once 'ProductManager.php';
        try {
            ProductManager::createProducts($this->object);
        } catch (Exception $exc) {
            Template::errorDialog($exc->getMessage(), $this->guessReturnURL());
            exit(1);
        }
    }

    // }}}
}

?>
