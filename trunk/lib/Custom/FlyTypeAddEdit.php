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

define('I_NOT_DELETED_FLYTYPE', _('The following airplane type could not be deleted because it is associated to a service or an existing airplane: %s'));
define('I_NOT_DELETED_FLYTYPES', _('The following airplane types could not be deleted because they are associated to a service or an existing airplane: %s'));
define('I_DELETED_FLYTYPE', _('Selected items were successfully deleted. '));

/**
 * FlyTypeAddEdit
 *
 */
class FlyTypeAddEdit extends GenericAddEdit {
    // FlyTypeAddEdit::__construct() {{{

    /**
     * Constructor
     *
     * @param array $params
     * @access public
     */
    public function __construct($params) {
        parent::__construct($params);
        $this->returnURL = 'dispatcher.php?entity=FlyType';
    }

    // }}}
    // FlyTypeAddEdit::delete() {{{

    /**
     * delete 
     * 
     * @access protected
     * @return void
     */
    protected function delete() {
        $mapper = Mapper::singleton('FlyType');
        //charge la collection des flytypes à supprimer
        $flyTypeCol = $mapper->loadCollection(array('Id' => $this->objID));
        $count = $flyTypeCol->getCount();

        //pour les flyType non supprimées
        $notDeletedFlyType = array();

        //pour la vérif
        $productMapper = Mapper::singleton('Product');
        $ftpcMapper = Mapper::singleton('FlyTypePrestationCost');
        //On demarre la transaction
        Database::connection()->startTrans();

        for($i=0 ; $i<$count ; $i++){
        	$flyType = $flyTypeCol->getItem($i);

        	//Est-il lié à au moins un Product (Product_FlyType)
        	$productCol = $productMapper->loadCollection(
                    array('FlyType'=>$flyType->getId())
        	);

        	//Est-il lié à au moins une Prestation via FlyTypePrestationCost
        	$prs = count($flyType->getFlyTypePrestationCostCollectionIds());

        	//Est-il lié à au moins un coût d'une prestation via FlyTypePrestationCost
        	$ftpcCol = $ftpcMapper->loadCollection(
        		  array('FlyType'=>$flyType->getId())
        	);

        	if(Tools::isEmptyObject($productCol) && $prs==0
            && Tools::isEmptyObject($ftpcCol)){
        		$mapper->delete($flyType->getId());
        	}else{
        		$notDeletedFlyType[] = $flyType->getName();
        	}
        }

        //On commite
        if (Database::connection()->hasFailedTrans()) {
            trigger_error(Database::connection()->errorMsg(), E_USER_WARNING);
        	Database::connection()->rollbackTrans();
        	Template::errorDialog(E_ERROR_IMPOSSIBLE_ACTION, $this->_returnURL);
        	exit;
        }
        Database::connection()->completeTrans();

        // redirige vers un message d'info
        if (count($notDeletedFlyType) == 1) {
            $msg = sprintf(I_NOT_DELETED_FLYTYPE, $notDeletedFlyType[0]);
        } else if (count($notDeletedFlyType) > 1) {
            $str = "<ul><li>" . implode("</li><li>", $notDeletedFlyType) . "</li></ul>";
            $msg = sprintf(I_NOT_DELETED_FLYTYPES, $str);
        } else {
            $msg = I_DELETED_FLYTYPE;
        }

        Template::infoDialog($msg, $this->_returnURL);
        exit;
    }

    // }}}
}

?>
