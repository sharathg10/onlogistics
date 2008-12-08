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
