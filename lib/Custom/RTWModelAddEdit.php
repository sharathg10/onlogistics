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
        if (!$this->object->canBeDeleted()) {
            Template::errorDialog(
                _('This model can not be modified because it is already used in one or more orders'),
                $this->guessReturnURL()
            );
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
        return array('MaterialType' => RTWMaterial::TYPE_RAW_MATERIAL);
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
}

?>
