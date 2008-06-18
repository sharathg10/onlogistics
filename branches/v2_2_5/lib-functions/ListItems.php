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

function itemsToArray($table, $itemlabel, $itemvalue,$filtered = FALSE){

  if($filtered == FALSE) {
    $result = Database::connection()->Execute("SELECT ".$itemlabel.", ".$itemvalue." FROM ".$table);
  }
  else {
    $result = Database::connection()->Execute("SELECT ".$itemlabel.", ".$itemvalue." FROM ".$table." WHERE ".$itemvalue."= '$filtered'");
  }

  $items_array = array();
  while(!$result->EOF) {

	 $items_array[$result->fields[1]] = $result->fields[0];
	 $result->MoveNext();
  }

  return $items_array;
}

/**
 * Retourne le code HTML à partir des valeurs du tableau $items_array
 * Possibilite d'afficher des items importants d'une autre couleur
 * @param $array array (int value => string value)
 * @param $selected_value integer
 * @param $importantItems array of integer : items qui doivent s'afficher d'une autre couleur (rouge par defaut)
 * @param $color string : couleur en hexa pour les irems importants
 * @param $disabledItems array of integer : items qui doivent etre desactives (marche pas sous IE!!!!)
 * @return
 **/

function itemsArrayToHtml($array, $selected_value=NULL, $importantItems = array(), $color='#FF0000', $disabledItems=array() ){
  $html_code = '';
  foreach($array as $key=>$val) {
  	$style = (count($importantItems)>0 && in_array($key, $importantItems))?' STYLE="color:' . $color .'"':'';
    $disabled = (count($disabledItems)>0 && in_array($key, $disabledItems))?' disabled="disabled"':'';
	if ($key == $selected_value) {
	  $html_code .= "\n\t<option value=\"".$key.'" '. $style.$disabled.' selected="selected">'.$val.'</option>';
    } else {
	  $html_code .= "\n\t<option value=\"".$key.'" '. $style.$disabled.'>'.$val.'</option>';
	}
  }
  return $html_code;
}

?>