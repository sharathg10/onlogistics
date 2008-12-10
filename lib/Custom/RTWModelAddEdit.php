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

class RTWModelAddEdit extends GenericAddEdit {
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
    // RTWModelAddEdit::onBeforeDisplay() {{{

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
    // RTWModelAddEdit::delete() {{{

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
                $pdtCol = $o->getRTWProductCollection();
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
    // RTWModelAddEdit::onBeforeHandlePostData() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function onBeforeHandlePostData() {
        $elts = array('RTWModel_Image', 'RTWModel_ColorImage');
        foreach ($elts as $elt) {
            if (isset($_FILES[$elt]) && $_FILES[$elt]['error'] == 0) {
                if (strtolower($_FILES[$elt]['type']) != 'image/png') {
                    Template::errorDialog(_('Images must be in "png" format'));
                    exit(1);
                }
                if (intval($_FILES[$elt]['size']) > 1000000) {
                    Template::errorDialog(_('Images must not exceed 1 megaoctet'));
                    exit(1);
                }
            }
        }
        $col = Object::loadCollection('RTWModel', array(
            'StyleNumber' => trim($_POST['RTWModel_StyleNumber'])
        ));
        $count = $this->action == GenericController::FEATURE_EDIT ? 2 : 1;
        if (count($col) == $count) {
            Template::errorDialog(_('A worksheet with this style number already exists, please choose another one'));
            exit(1);
        }
    }

    // }}}
    // RTWModelAddEdit::onAfterHandlePostData() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function onAfterHandlePostData() {
        require_once 'RTWProductManager.php';
        try {
            RTWProductManager::createProducts($this->object);
        } catch (Exception $exc) {
            Template::errorDialog($exc->getMessage(), $this->guessReturnURL());
            exit(1);
        }
    }

    // }}}
    // RTWModelAddEdit::getFilterForMaterial1() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForMaterial1() {
        return array('MaterialType' => RTWMaterial::TYPE_RAW_MATERIAL);
    }

    // }}}
    // RTWModelAddEdit::getFilterForMaterial2() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForMaterial2() {
        return $this->getFilterForMaterial1();
    }

    // }}}
    // RTWModelAddEdit::getFilterForMaterial3() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForMaterial3() {
        return $this->getFilterForMaterial1();
    }

    // }}}
    // RTWModelAddEdit::getFilterForAccessory1() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForAccessory1() {
        return array('MaterialType' => RTWMaterial::TYPE_ACCESSORY);
    }

    // }}}
    // RTWModelAddEdit::getFilterForAccessory2() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForAccessory2() {
        return $this->getFilterForAccessory1();
    }

    // }}}
    // RTWModelAddEdit::getFilterForAccessory3() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForAccessory3() {
        return $this->getFilterForAccessory1();
    }

    // }}}
    // RTWModelAddEdit::getFilterForHeelReference() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForHeelReference() {
        return array('MaterialType' => RTWMaterial::TYPE_HEEL);
    }

    // }}}
    // RTWModelAddEdit::getFilterForSole() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForSole() {
        return array('MaterialType' => RTWMaterial::TYPE_SOLE);
    }

    // }}}
    // RTWModelAddEdit::getFilterForBox() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForBox() {
        return array('MaterialType' => RTWMaterial::TYPE_PACKAGING);
    }

    // }}}
    // RTWModelAddEdit::getFilterForHandBag() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForHandBag() {
        return array('MaterialType' => RTWMaterial::TYPE_PACKAGING);
    }

    // }}}
    // RTWModelAddEdit::getFilterForLining() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForLining() {
        return array('MaterialType' => RTWMaterial::TYPE_RAW_MATERIAL);
    }

    // }}}
    // RTWModelAddEdit::getFilterForInsole() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForInsole() {
        return array('MaterialType' => RTWMaterial::TYPE_RAW_MATERIAL);
    }

    // }}}
    // RTWModelAddEdit::getFilterForUnderSole() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForUnderSole() {
        return array('MaterialType' => RTWMaterial::TYPE_RAW_MATERIAL);
    }

    // }}}
    // RTWModelAddEdit::getFilterForMediaPlanta() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForMediaPlanta() {
        return array('MaterialType' => RTWMaterial::TYPE_RAW_MATERIAL);
    }

    // }}}
    // RTWModelAddEdit::getFilterForLagrima() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForLagrima() {
        return array('MaterialType' => RTWMaterial::TYPE_RAW_MATERIAL);
    }

    // }}}
    // RTWModelAddEdit::getFilterForHeelCovering() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForHeelCovering() {
        return array('MaterialType' => RTWMaterial::TYPE_RAW_MATERIAL);
    }

    // }}}
    // RTWModelAddEdit::getFilterForSelvedge() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForSelvedge() {
        return array('MaterialType' => RTWMaterial::TYPE_THREAD);
    }

    // }}}
    // RTWModelAddEdit::getFilterForThread1() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForThread1() {
        return array('MaterialType' => RTWMaterial::TYPE_THREAD);
    }

    // }}}
    // RTWModelAddEdit::getFilterForThread2() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForThread2() {
        return array('MaterialType' => RTWMaterial::TYPE_THREAD);
    }

    // }}}
    // RTWModelAddEdit::getFilterForBamboo() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function getFilterForBamboo() {
        return array('MaterialType' => RTWMaterial::TYPE_BAMBOO);
    }

    // }}}
    // RTWModelAddEdit::renderImage() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderImage() {
        $this->form->addElement('file', 'RTWModel_Image', _('Black and white image'), array('class' => 'textfield'));
        if ($this->object->getImage() != '') {
            $this->form->addElement('static', 'ImageLink', '', 
                  '<a href="javascript:void(0);" '
                  . 'onclick="setElementPosition(\'ImagePreview\', {\'x\':15, \'y\': elementPosition(this).y + 15});'
                  . 'fw.dom.toggleElement(\'ImagePreview\')">' . _('Show/hide current image') . '</a>');
        } else {
            $this->form->addElement('static');
        }
    }

    // }}}
    // RTWModelAddEdit::renderColorImage() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderColorImage() {
        $this->form->addElement('file', 'RTWModel_ColorImage', _('Color image'), array('class' => 'textfield'));
        if ($this->object->getColorImage() != '') {
            $this->form->addElement('static', 'ColorImageLink', '', 
                '<a href="javascript:void(0);" '
                . 'onclick="setElementPosition(\'ColorImagePreview\', {\'x\':15, \'y\': elementPosition(this).y + 15});'
                . 'fw.dom.toggleElement(\'ColorImagePreview\')">' . _('Show/hide current image') . '</a>');
        } else {
            $this->form->addElement('static');
        }
    }

    // }}}
    // RTWModelAddEdit::renderHeelReferenceQuantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderHeelReferenceQuantity() {
        $this->_renderQuantityWidget('HeelReference', _('Heel reference quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderSoleQuantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderSoleQuantity() {
        $this->_renderQuantityWidget('Sole', _('Sole quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderBoxQuantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderBoxQuantity() {
        $this->_renderQuantityWidget('Box', _('Box quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderHandBagQuantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderHandBagQuantity() {
        $this->_renderQuantityWidget('HandBag', _('Hand bag quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderMaterial1Quantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderMaterial1Quantity() {
        $this->_renderQuantityWidget('Material1', _('Material 1 quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderMaterial2Quantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderMaterial2Quantity() {
        $this->_renderQuantityWidget('Material2', _('Material 2 quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderMaterial3Quantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderMaterial3Quantity() {
        $this->_renderQuantityWidget('Material3', _('Material 3 quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderAccessory1Quantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderAccessory1Quantity() {
        $this->_renderQuantityWidget('Accessory1', _('Accessory 1 quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderAccessory2Quantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderAccessory2Quantity() {
        $this->_renderQuantityWidget('Accessory2', _('Accessory 2 quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderAccessory3Quantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderAccessory3Quantity() {
        $this->_renderQuantityWidget('Accessory3', _('Accessory 3 quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderLiningQuantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderLiningQuantity() {
        $this->_renderQuantityWidget('Lining', _('Lining quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderInsoleQuantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderInsoleQuantity() {
        $this->_renderQuantityWidget('Insole', _('Insole quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderUnderSoleQuantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderUnderSoleQuantity() {
        $this->_renderQuantityWidget('UnderSole', _('Under sole quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderMediaPlantaQuantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderMediaPlantaQuantity() {
        $this->_renderQuantityWidget('MediaPlanta', _('Media planta quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderLagrimaQuantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderLagrimaQuantity() {
        $this->_renderQuantityWidget('Lagrima', _('Lagrima quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderHeelCoveringQuantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderHeelCoveringQuantity() {
        $this->_renderQuantityWidget('HeelCovering', _('Heel covering quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderSelvedgeQuantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderSelvedgeQuantity() {
        $this->_renderQuantityWidget('Selvedge', _('Selvedge quantity'));
    }

    // }}}
    // RTWModelAddEdit::renderBambooQuantity() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function renderBambooQuantity() {
        $this->_renderQuantityWidget('Bamboo', _('Bamboo quantity'));
    }

    // }}}
    // RTWModelAddEdit::_renderQuantityWidget() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function _renderQuantityWidget($name, $label) {
        $elts = array();
        $elts[] = $this->form->createElement(
            'text',
            'RTWModel_' . $name . 'Quantity',
            '',
            array('style' => 'width: 70px;')
        );
        $elts[] = $this->form->createElement(
            'radio',
            'RTWModel_' . $name . 'Nomenclature',
            '',
            A_YES,
            1,
            array('id' => 'RTWModel_' . $name . 'Nomenclature_0')
        );
        $elts[] = $this->form->createElement(
            'radio',
            'RTWModel_' . $name . 'Nomenclature',
            '',
            A_NO,
            0,
            array('id' => 'RTWModel_' . $name . 'Nomenclature_1')
        );
        $label .= ' / ' . _('Nomenclature');
        $this->form->addGroup($elts, $name . '_Group', $label, null, false);
        $getterQ = 'get' . $name . 'Quantity';
        $getterN = 'get' . $name . 'Nomenclature';
        $this->form->setDefaults(array(
            'RTWModel_' . $name . 'Quantity'     => I18N::formatNumber($this->object->$getterQ(), 3),
            'RTWModel_' . $name . 'Nomenclature' => $this->object->$getterN(),
        ));
    }

    // }}}
    // RTWModelAddEdit::postContent() {{{

    /**
     * 
     * @access protected
     * @return void
     */
    protected function postContent() {
        $html = '';
        $img  = urlencode($this->object->getImage());
        if ($img != '') {
            $html .= '<div id="ImagePreview" style="border: 1px black solid;position:absolute;z-index:10;display:none;"><img src="image.php?uuid='.$img.'&md5=1"/></div>';
        }
        $img  = urlencode($this->object->getColorImage());
        if ($img != '') {
            $html .= '<div id="ColorImagePreview" style="border: 1px black solid;position:absolute;z-index:10;display:none;"><img src="image.php?uuid='.$img.'&md5=1"/></div>';
        }
        return $html;
    }

    // }}}
}

?>
