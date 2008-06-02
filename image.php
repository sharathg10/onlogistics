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

define('IGNORE_SESSION_TIMEOUT', true);
require_once('config.inc.php');

Upload::show($_GET['uuid'], isset($_GET['md5']));
/*$img = false;
$mapper = Mapper::singleton('Image');

$default_img = 'images/noimage.jpg';
if (isset($_GET['uuid'])) {
    $img = $mapper->load(array('UUID'=>md5($_GET['uuid'])), array(), true);
    if ($_GET['uuid'] == 'logo') {
        $default_img = 'images/dot_clear.gif';
    }
}

if (!($img instanceof Image)) {
    $fp = fopen($default_img, 'rb');
    $data = fread($fp, filesize($default_img));
    $name_ext = explode('.', $default_img);
    $ext = strtolower($name_ext[count($name_ext)-1]);
    fclose($fp);
} else {
    $data = base64_decode($img->getData());
    $ext  = strtolower($img->getExtension());
}
header("Content-Type: image/$ext\n");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo $data;
 */
?>
