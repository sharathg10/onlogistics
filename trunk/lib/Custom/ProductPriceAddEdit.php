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
 * @version   SVN: $Id: ProductPriceAddEdit.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

/**
 * ProductPriceAddEdit
 *
 */
class ProductPriceAddEdit extends GenericAddEdit {
    // ProductPriceAddEdit::__construct() {{{

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
            'js/includes/ProductPriceAddEdit.js'
        );
    }

    // }}}
    // RTWMaterialAddEdit::getAddEditMapping() {{{

    /**
     * Surchargée pour tweaker le mapping.
     *
     * @access protected
     * @return array le tableau correspondant au mapping du formulaire
     */
    protected function getAddEditMapping() {
        if (isset($_REQUEST['supplier'])) {
            $title = _('You can select another supplier to edit his prices');
            return array($title => array('ActorProduct' => array(
                'label'        => _('Supplier'),
                'inplace_edit' => false,
                'required'     => true,
                'add_button'   => false
            )));
        }
        return array();
    }

    // }}}
    // ProductPriceAddEdit::renderSupplier() {{{

    /**
     * Appelée avant affichage
     *
     * @access public
     * @return void
     */
    public function renderActorProduct() {
        $this->form->addElement('hidden', 'supplier', 1);
        $supplierArray = $this->object
            ->getActorProductCollection(array(), array('Priority'=>SORT_DESC))
            ->toArray('getActor', false);
        $this->form->addElement('select', 'ActorProduct_ID',
             _('Supplier'), $supplierArray, 'id="ActorProduct_ID" style="width:100%;"');
    }

    // }}}
    // ProductPriceAddEdit::onAfterHandlePostData() {{{

    /**
     * Appelée avant sauvegarde
     *
     * @access public
     * @return void
     */
    public function onAfterHandlePostData() {
       if (isset($_POST['ActorProduct_ID'])) {
            $setter = 'setActorProduct';
            $obj = Object::load('ActorProduct', $_POST['ActorProduct_ID']);
        } else {
            $setter = 'setProduct';
            $obj = &$this->object;
        }
        $mapper = Mapper::singleton('PriceByCurrency');
        $mapper->delete($obj->getPriceByCurrencyCollectionIds());
        if (isset($_POST['PBC_Price']) && is_array($_POST['PBC_Price'])) {
            for ($i=0; $i<count($_POST['PBC_Price']); $i++) {
                // construit le PriceByCurrency
                $pbc = new PriceByCurrency();
                $pbc->setRecommendedPrice($_POST['PBC_RecommendedPrice'][$i]);
                $pbc->setPrice($_POST['PBC_Price'][$i]);
                $pbc->setCurrency($_POST['PBC_Currency_ID'][$i]);
                $pbc->setPricingZone($_POST['PBC_PricingZone_ID'][$i]);
                $pbc->$setter($obj->getId());
                $pbc->save();
            }
        }
    }

    // }}}
    // ProductPriceAddEdit::additionalFormContent() {{{

    /**
     * Contenu du grid RessourceRessourceGroup
     *
     * @access public
     * @return void
     */
    public function additionalFormContent() {
        if (isset($_REQUEST['supplier'])) {
            $title = _('Supplier prices for product "%s"');
            $header = _('Supplier prices');
        } else {
            $title = _('Selling prices for product "%s"');
            $this->formTitle = _('Selling prices');
            $header = '';
        }
        $this->title = sprintf($title, $this->object->getName());
        return  "<tr><th colspan=\"4\">$header</th><tr>\n"
             . "<tr><td colspan=\"4\"><div align=\"right\">"
             . "<input type=\"button\" id=\"addPBC\" class=\"button\" "
             . "value=\""._('Add')."\"/></div></td></tr>\n"
             . "<tr><td colspan=\"4\"><ul id=\"PBCUL\" "
             . "style=\"margin:0;padding:0;\">"
             . "</ul></td></tr>\n";
    }

    // }}}
}

?>
