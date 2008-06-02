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

require_once CUSTOM_CONTROLLER_DIR . '/RessourceAddEdit.php';


/**
 * ProductRessourceAddEdit
 *
 */
class ProductRessourceAddEdit extends RessourceAddedit {
    // ProductRessourceAddEdit::__construct() {{{

    /**
     * Constructor
     *
     * @param array $params
     * @access public
     */
    public function __construct($params) {
        $params['title'] = _('Add or update product resource');
        parent::__construct($params);
    }

    // }}}
    // ProductRessourceAddEdit::onAfterHandlePostData() {{{

    /**
     * Appelée avant sauvegarde
     *
     * @access public
     * @return void
     */
    public function onAfterHandlePostData()
    {
        $this->object->setName($this->object->getProduct()->getBaseReference());
        $this->object->setType(Ressource::RESSOURCE_TYPE_PRODUCT);
    }

    // }}}
    // ProductRessourceAddEdit::renderProduct() {{{

    /**
     * @access public
     * @return void
     */
    public function renderProduct() {
        $list = array();
        $pdtCol = Object::loadCollection('Product', array('Activated'=>1),
            array('BaseReference'=>SORT_ASC, 'Name'=>SORT_ASC));
        $count = $pdtCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $pdt = $pdtCol->getItem($i);
            $list[$pdt->getId()] = $pdt->getBaseReference() . ' - ' . $pdt->getName();
        }

        $elt = HTML_QuickForm::createElement(
            'select',
            'Ressource_Product_ID',
            _('Product'),
            $list,
            'id="Ressource_Product_ID"'
        );
        $elt->setAttribute('class', 'select required_element');
        $this->form->addElement($elt);
        $this->formDefaults['Ressource_Product_ID'] = $this->object->getProductId();
        // ajoute une validation numérique (!= '##')
        $msg = sprintf(
            E_VALIDATE_FIELD . ' "%s" ' . E_VALIDATE_IS_REQUIRED,
            _('Product')
        );
        $this->form->addRule('Ressource_Product_ID', $msg, 'numeric', '', 'client');
        // Juste pour afficher l'asterisque:
        $this->form->addRule('Ressource_Product_ID', $msg, 'required', '', 'client');
    }

    // }}}
    // ProductRessourceAddEdit::getFeatures() {{{

    /**
     * Surchargée ici pour retourner les features spécifiques.
     *
     * @access protected
     * @return array
     */
    protected function getFeatures() {
        return array('grid', 'add', 'edit', 'del');
    }

    // }}}
    // ProductRessourceAddEdit::getAddEditMapping() {{{

    /**
     * Surchargée ici pour retourner un mapping spécifique.
     *
     * @access protected
     * @return array
     */
    protected function getAddEditMapping()
    {
        return array(
            ''=>array(
               'Product'=>array('label'=>_('Related product'), 'required'=>true),
               'Quantity'=>array('label'=>_('Quantity')),
               'CostType'=>array('label'=>_('Cost unit')),
            ) 
        );
    }

    // }}}
}

?>
