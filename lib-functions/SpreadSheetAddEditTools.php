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

function getProperties($class, $withFkeysAndLinks = true) {
    require_once('Objects/' . $class . '.php');
    $return = array();
    $kkeys  = array();
    // on récupère le tableau des propriétés de l'entité
    if ($class == 'Product') {
        // pour product on récupère aussi les propriétés dynamiques
        $tmp_product = new Product();
        $props = $tmp_product->getAllClassProperties(true);
        unset($tmp_product);
    } else {
        $props = call_user_func(array($class, 'getProperties'));
    }
    // propriétés simples et foreignkeys
    foreach ($props as $name=>$type) {
        if (is_string($type)) {
            if ($withFkeysAndLinks) {
                $return[] = array(
                    'name'=>$name,
                    'type'=>Object::TYPE_FKEY,
                    'class'=>$type
                );
            }
        } else {
            $return[] = array('name'=>$name, 'type'=>$type, 'class'=>'');
        }
    }
    // les liens 1..* et *..*
    if ($withFkeysAndLinks) {
        $links = call_user_func(array($class, 'getLinks'));
        foreach ($links as $link=>$data) {
            if ($data['multiplicity'] == 'onetoone') {
                continue;
            }
            $type = $data['multiplicity'] == 'onetomany'?
                    Object::TYPE_ONETOMANY:Object::TYPE_MANYTOMANY;
            $return[] = array(
                'name'  => $link . 'Collection',
                'type'  => $type,
                'class' => $data['linkClass']
            );
        }
    }
    // trier le tableau par ses clefs
    ksort($return);
    return $return;
}

?>
