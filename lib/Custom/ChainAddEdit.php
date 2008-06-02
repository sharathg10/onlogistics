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

class ChainAddEdit extends GenericAddEdit {
    // ChainAddEdit::__construct() {{{

    /**
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct($params)
    {
        $params['use_session'] = true;
        $params['use_ajax'] = true;
        $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
        parent::__construct($params);
        $this->addJSRequirements('js/includes/ChainAddEdit.js');
    }

    // }}}
    // ChainAddEdit::onAfterHandlePostData() {{{

    /**
     * Appelée avant sauvegarde
     *
     * @access public
     * @return void
     */
    public function onAfterHandlePostData()
    {

        if ($_POST['DepartureActor_ID'] == $_POST['ArrivalActor_ID'] &&
            $_POST['DepartureSite_ID'] == $_POST['ArrivalSite_ID'])
        {
            Template::errorDialog(
                _("Chain departure and arrival actor cannot be the same, please correct."),
                'javascript: history.go(-1)'
            );
            exit(1);
        }
        require_once 'Objects/ActorSiteTransition.php';
        require_once 'Objects/Chain.php';
        $ast = $this->object->getSiteTransition();
        if (!($ast instanceof ActorSiteTransition)) {
            $ast = new ActorSiteTransition();
        }
        $ast->setDepartureActor($_POST['DepartureActor_ID']);
        $ast->setDepartureSite($_POST['DepartureSite_ID']);
        $ast->setArrivalActor($_POST['ArrivalActor_ID']);
        $ast->setArrivalSite($_POST['ArrivalSite_ID']);
        $ast->save();
        $this->object->setSiteTransition($ast);
        if ($this->object->getState() < Chain::CHAIN_STATE_CREATED) {
            $this->object->setState(Chain::CHAIN_STATE_CREATED);
        }
        $this->object->setCreatedDate(date('Y-m-d H:i:s', time()));
    }

    // }}}
    // ChainAddEdit::renderSiteTransition() {{{

    /**
     * renderSiteTransition 
     * 
     * @access public
     * @return void
     */
    public function renderSiteTransition() {
        $filter  = array('Active' => 1);
        $actIDs  = SearchTools::createArrayIDFromCollection('Actor', $filter);
        $siteIDs = SearchTools::createArrayIDFromCollection('Site');
        $this->form->addElement(
            'select',
            'DepartureActor_ID',
            _('Departure actor'),
            $actIDs,
            array(
                'class'=>'select',
                'onchange'=>"fw.ajax.updateSelectCustom(this.id, 'DepartureSite_ID', 'Site', 'Owner', 'chainaddedit_getSiteCollection', true);",
                'id'=>'DepartureActor_ID'
            )
        );
        $this->form->addElement(
            'select',
            'DepartureSite_ID',
            _('Departure site'),
            $siteIDs,
            array(
                'class'=>'select',
                'id'=>'DepartureSite_ID'
            )
        );
        $this->form->addElement(
            'select',
            'ArrivalActor_ID',
            _('Arrival actor'),
            $actIDs,
            array(
                'class'=>'select',
                'onchange'=>"fw.ajax.updateSelectCustom(this.id, 'ArrivalSite_ID', 'Site', 'Owner', 'chainaddedit_getSiteCollection', true);",
                'id'=>'ArrivalActor_ID'
            )
        );
        $this->form->addElement(
            'select',
            'ArrivalSite_ID',
            _('Arrival site'),
            $siteIDs,
            array(
                'class'=>'select',
                'id'=>'ArrivalSite_ID'
            )
        );
        $ast = $this->object->getSiteTransition();
        if ($ast instanceof ActorSiteTransition) {
            $this->form->addElement(
                'hidden',
                'HiddenDepartureSite_ID',
                $ast->getDepartureSiteId(),
                'id="HiddenDepartureSite_ID"'
            );
            $this->form->addElement(
                'hidden',
                'HiddenArrivalSite_ID',
                $ast->getArrivalSiteId(),
                'id="HiddenArrivalSite_ID"'
            );
            $this->formDefaults['DepartureActor_ID'] = $ast->getDepartureActorId();
            $this->formDefaults['DepartureSite_ID'] = $ast->getDepartureSiteId();
            $this->formDefaults['ArrivalActor_ID'] = $ast->getArrivalActorId();
            $this->formDefaults['ArrivalSite_ID'] = $ast->getArrivalSiteId();
        }
    }

    // }}}
}

?>
