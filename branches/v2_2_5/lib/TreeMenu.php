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

class TreeMenu {
    /**
     * Contenu du menu
     *
     * @access private
     */
    var $_items;

    /**
     * Contient les images a utiliser
     * @var
     * @access private
     */
    var $_templateFile;

    /**
     * Contient les infos pour construire un template dynamique
     *
     * @var
     * @access private
     */
    var $dynTemplate;

    /**
     * Les fichiers js à inclure avec le TreeMenu
     *
     * @access private
     */
    var $_JSRequirements = array();

    /**
     * TreeMenu::getJSRequirements()
     *
     * @return array
     */
    function getJSRequirements() {
        return $this->_JSRequirements;
    }

    /**
     * Constructor
	 *
     * @access protected
     */
    function TreeMenu($imageTpl='js/includes/ttm_tree_tpl.js') {
        $this->_templateFile = $imageTpl;
        $this->_JSRequirements = array('js/tigra_tree_menu/tree.js',
                                       $this->_templateFile);
    }

    /**
     * Retourne une string: declaration du tableau js
     * equivalent a $PHParray
     *
     * @access public
     * @param mixed $phpArray array
     * @param string $JSarrayName nom qu'aura le tableau js
     * @return string
     */
    function buildJSitems($phpArray, $JSarrayName='treeItems') {
       $return = $JSarrayName.' = new Array();';
       for($i = 0; $i < count($phpArray); $i++) {
          if(!is_array($phpArray[$i])) {
            $add = ($phpArray[$i] === 0 || $phpArray[$i] == 'null')?
                    $phpArray[$i]:'"' . $phpArray[$i] . '"';
             $return .= $JSarrayName.'['.$i.'] = ' . $add . ';';
          }
          else{
             $return .= $this->buildJSitems($phpArray[$i], $JSarrayName.'['.$i.']');
          }
       }
       return $return;
    }

    /**
     * Retourne un tableau d'includes js
     *
     * @access public
     * @param string $JSarrayName nom qu'aura le tableau js des items
     * @return array of strings
     */
    function render($JSarrayName='treeItems') {
        $jsItems = $this->buildJSitems($this->_items, $JSarrayName);
        $return = '<script type="text/javascript">';
	    $return .= $jsItems;
		$return .= 'new tree (' . $JSarrayName . ', TREE_TPL);';
	    $return .= '</script>';
        return $return;
    }

}

?>