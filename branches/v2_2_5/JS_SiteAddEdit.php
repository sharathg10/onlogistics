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

$session = Session::singleton();

$name = '';
$id   = 0;
if (isset($_REQUEST['sitName']) && isset($_REQUEST['sitId']) &&
    isset($_REQUEST['widgetName'])) {
	$wname = $_REQUEST['widgetName'];
	$name  = $_REQUEST['sitName'];
	$id    = $_REQUEST['sitId'];
}
if (isset($_SESSION['asPopup'])) {
    unset($_SESSION['asPopup']);
}
?>
<html>
<head>
	<title></title>
	<script type="text/javascript">
		function init(){
			var widget = window.opener.document.forms[0].elements['<?php echo $wname; ?>'];
			// ne marche pas, fait planter ie
			//widget.options[widget.options.length] = new Option('<?php echo $name; ?>', <?php echo $id; ?>, false, true);
			var elt = window.opener.document.createElement("OPTION");
			elt.text = '<?php echo $name; ?>';
			elt.value = <?php echo $id; ?>;
			elt.selected = true;
			widget.options.add(elt);
			window.opener.document.forms[0].elements['SiteAdded'].value = 1;
			window.close();
		}
	</script>
</head>
<body onload="init();">
</body>
</html>