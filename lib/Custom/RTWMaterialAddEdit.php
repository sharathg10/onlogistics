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

class RTWMaterialAddEdit extends GenericAddEdit {
    // RTWMaterialAddEdit::__construct() {{{

    /**
     * __construct 
     * 
     * @param array $params 
     * @access public
     * @return void
     */
    public function __construct($params) {
        parent::__construct($params);
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
        $return = parent::getAddEditMapping();
        $first  = & $return['']; // oui...
        $name   = array('Name' => array(
            'label'        => _('Commercial designation'),
            'inplace_edit' => false,
            'required'     => true,
            'add_button'   => false
        ));
        $first = $name + $first;
        return $return;
    }

    // }}}
    // RTWMaterialAddEdit::onBeforeDisplay() {{{

    /**
     * additionalContent 
     *
     * Ajoute les tableaux pour les grids éditables des RTWMaterialCustomer et 
     * CostRange.
     * 
     * @access protected
     * @return void
     */
    protected function onBeforeDisplay() {
        $supplier = $this->object->getMainSupplier();
        // fournisseur
        $this->form->addElement('static');
        $arr = SearchTools::createArrayIDFromCollection(
            'Supplier',
            array('Active'=>1),
            '',
            'Name',
            array('Name'=>SORT_ASC)
        );
        $elt = HTML_QuickForm::createElement(
            'select',
            'Supplier_ID',
            _('Supplier'),
            $arr,
            array('style'=>'width:100%;'));
        $this->form->addElement($elt);
        $this->formDefaults['Supplier_ID'] = $supplier instanceof Supplier ? $supplier->getId() : 0;
        // ref. fournisseur
        $elt = HTML_QuickForm::createElement(
            'text',
            'Supplier_Reference',
            _('Supplier reference'),
            'class="textfield"');
        $this->form->addElement($elt);
        $this->formDefaults['Supplier_Reference'] = $this->object->getReferenceByActor($supplier);
        $this->form->setDefaults($this->formDefaults);
    }

    // }}}
    // RTWMaterialAddEdit::onAfterHandlePostData() {{{

    /**
     * Méthode appelée dans la transaction après ajout ou édition de l'objet.
     *
     * @access protected
     * @return void
     */
    protected function onAfterHandlePostData() {
        require_once 'RTWMaterialManager.php';
        $values  = $this->form->exportValues();
        $manager = new RTWMaterialManager();
        $manager->createMaterials($this->object, $values);
    }

    // }}}
}

?>
