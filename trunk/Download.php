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

require_once('config.inc.php');

// Tous les utilisateurs ont accès à cette page une fois connectés
$auth = Auth::singleton();

// on parse les applications disponibles
$raw_app_array = explode(',', AVAILABLE_APPS);
$app_array = array();
foreach($raw_app_array as $str) {
    $str = trim($str);
    list($name, $file) = explode(':', $str);
    $app_array[$name] = $file;
}

// on parse les manuels disponibles
$man_array = array();
$manMapper = Mapper::singleton('Manual');
$manCol = $manMapper->loadCollection();
$count = $manCol->getCount();
for($i=0 ; $i<$count ; $i++) {
    $man = $manCol->getItem($i);
    $man_array[$man->getId()] = array(
        'name' => $man->getName(),
        'fr' => $man->getFrFile(),
        'en' => $man->getEnFile());
}

// l'utilisateur a cliqué sur un lien, on le redirige vers la bonne adresse 
// avec les crédentials de l'auth http
if (isset($_GET['app']) && isset($app_array[$_GET['app']])) {
    $url = DOWNLOAD_ADDRESS . '/' . $app_array[$_GET['app']];
    if (isset($_GET['md5'])) $url .= '.md5';
    header('Location: ' . $url);
    exit(0);
}
if (isset($_GET['man']) && isset($man_array[$_GET['man']])) {
    $url = DOWNLOAD_ADDRESS . '/' . $man_array[$_GET['man']][$_GET['lang']];
    header('Location: ' . $url);
    exit(0);
}
// affichage du template smarty
$smarty = new Template();
$smarty->assign('apps', $app_array);
$smarty->assign('mans', $man_array);
$smarty->assign('phpself', $_SERVER['PHP_SELF']);
Template::page(_('Onlogistics download zone'), $smarty->fetch('Download.html'));

?>
