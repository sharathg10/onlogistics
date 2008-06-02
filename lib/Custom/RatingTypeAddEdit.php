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

class RatingTypeAddEdit extends GenericAddEdit {
    // RatingTypeAddEdit::onBeforeHandlePostData() {{{
    
    /**
     * onBeforeHandlePostData 
     * 
     * @access protected
     * @return void
     */
    protected function onBeforeHandlePostData() {
        $AlreadyExist = _('A qualification type already exists with the name "%s", please correct.');
        $RatingTypeMapper = Mapper::singleton('RatingType');
        $name = $_POST['RatingType_Name'];
        if (($name != $this->object->getName())
        && ($RatingTypeMapper->alreadyExists(array('Name'=>$name)))) { 
            Template::infoDialog(sprintf($AlreadyExist, $name), $this->guessReturnURL());
            exit;
        } 
    }

    // }}}
    // RatingTypeAddEdit::onBeforeDelete() {{{

    /**
     * onBeforeDelete 
     * 
     * @access protected
     * @return void
     */
    protected function onBeforeDelete() {
        $ratingTypeMapper = Mapper::singleton('RatingType');
        $ratingTypeCol = $ratingTypeMapper->loadCollection(array('Id'=>$this->objID));
        $count = $ratingTypeCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $ratingType = $ratingTypeCol->getItem($i);
            if(!$ratingType->IsDeletable()) {
                Template::infoDialog(
                    _('Some qualification types could not be deleted because they are associated to a qualification defined for an actor.'), 
                    $this->guessReturnURL());
                exit();
            }
        }
    }

    // }}}
}

?>
