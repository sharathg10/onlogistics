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
 * @version   SVN: $Id: TermsOfPaymentAddEdit.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * TermsOfPaymentAddEdit
 *
 */
class TermsOfPaymentAddEdit extends GenericAddEdit {
    // TermsOfPaymentAddEdit::__construct() {{{

    /**
     * Constructeur
     *
     * @param array $params
     * @access public
     * @return void
     */
    public function __construct($params) {
        $params['use_session'] = false;
        $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
        parent::__construct($params);
        $this->addJSRequirements(
            'JS_AjaxTools.php',
            'js/includes/TermsOfPaymentAddEdit.js'
        );
    }

    // }}}
    // TermsOfPaymentAddEdit::onAfterHandlePostData() {{{

    /**
     * Appelée avant sauvegarde
     *
     * @access public
     * @return void
     */
    public function onAfterHandlePostData() {
        $mapper = Mapper::singleton('TermsOfPaymentItem');
        $mapper->delete($this->object->getTermsOfPaymentItemCollectionIds());
        if (isset($_POST['TOPI_PaymentOption']) && is_array($_POST['TOPI_PaymentOption'])) {
            for ($i=0; $i<count($_POST['TOPI_PaymentOption']); $i++) {
                // construit le PriceByCurrency
                $topi = new TermsOfPaymentItem();
                $val  = isset($_POST['TOPI_PercentOfTotal'][$i]) ?
                    $_POST['TOPI_PercentOfTotal'][$i] : 0;
                $topi->setPercentOfTotal($val);
                $val  = isset($_POST['TOPI_PaymentDelay'][$i]) ?
                    $_POST['TOPI_PaymentDelay'][$i] : 0;
                $topi->setPaymentDelay($val);
                $topi->setPaymentOption($_POST['TOPI_PaymentOption'][$i]);
                $topi->setPaymentEvent($_POST['TOPI_PaymentEvent'][$i]);
                $topi->setTermsOfPayment($this->objID);
                $topi->save();
            }
        }
    }

    // }}}
    // TermsOfPaymentAddEdit::additionalFormContent() {{{

    /**
     * Contenu du grid RessourceRessourceGroup
     *
     * @access public
     * @return void
     */
    public function additionalFormContent() {
        $header = _('Terms of payment lines');
        return  "<tr><th colspan=\"4\">$header</th><tr>\n"
             . "<tr><td colspan=\"4\"><div align=\"right\">"
             . "<input type=\"button\" id=\"addTOPI\" class=\"button\" "
             . "value=\""._('Add')."\"/></div></td></tr>\n"
             . "<tr><td colspan=\"4\"><ul id=\"TOPIUL\" "
             . "style=\"margin:0;padding:0;\">"
             . "</ul></td></tr>\n";
    }

    // }}}
}

?>
