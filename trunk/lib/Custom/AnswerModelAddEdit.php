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

define('I_NOT_DELETED_ENTITY', _('The following answer could not be deleted because it is associated to a question model: %s'));
define('I_NOT_DELETED_ENTITIES',  _('The following answers could not be deleted because they are associated to a question model: %s'));

/**
 *
 */
class AnswerModelAddEdit extends GenericAddEdit {
    private $_notDeletedEntity = array();
    // AnswerModelAddEdit::__construct() {{{

    /**
     * __construct 
     * 
     * @param array $params 
     * @access public
     * @return void
     */
    public function __construct($params=array()) {
        $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
        parent::__construct($params); 
    }

    // }}}
    // AnswerModelAddEdit::onBeforeDelete() {{{

    /**
     * onBeforeDelete 
     * 
     * @access protected
     * @return void
     */
    protected function onBeforeDelete() {
        $objectMapper = Mapper::singleton('AnswerModel');
        $objectCol = $objectMapper->loadCollection(
			array('Id' => $this->objID));

        //pour la vérification dans la boucle
        $linkQuestionMapper = Mapper::singleton('LinkQuestionAnswerModel');

        $okForDelete = array();
        $count = $objectCol->getCount();
        for($i=0 ; $i<$count ; $i++){
            $object = $objectCol->getItem($i);
 	        /*
	        Ne sont supprimable que les réponses liées
	        à aucune QuestionModel.
	        */
            $linkQuestion = $linkQuestionMapper->loadCollection(
                array('AnswerModel'=>$object->getId()));
            if($linkQuestion->getCount()==0 ) {
                //on peut supprimer
                $okForDelete[] = $object->getId();
        	} else {
	            //ajout dans le tableau des non suprimées
                $this->_notDeletedEntity[] = $object->getValue();
	        }       
        }
        $this->objID = $okForDelete;
    }

    // }}}
    // AnswerModelAddEdit::onAfterDelete() {{{

    /**
     * onAfterDelete 
     * 
     * @access protected
     * @return void
     */
    protected function onAfterDelete() {
        // redirige vers un message d'info
        $msg = false;
        if (count($this->_notDeletedEntity) == 1) {
            $msg = sprintf(I_NOT_DELETED_ENTITY, $this->_notDeletedEntity[0]);
        } else if (count($this->_notDeletedEntity) > 1) {
            $str = "<ul><li>" . implode("</li><li>", $this->_notDeletedEntity) . "</li></ul>"; 
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
