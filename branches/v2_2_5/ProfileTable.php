<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * $Source$
 *
 * @version   CVS: $Id$
 * @package   
 * @author    David JEAN LOUIS <izimobil@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright 2002-2006 David JEAN LOUIS - All rights reserved
 */

require_once 'config.inc.php';
require_once 'menu.inc.php';

$profilesArray = UserAccount::getProfileConstArray();
$profilesArray[-1] = 'Root';

function smarty_modifier_getprofile($string)
{
    global $profilesArray;
    return isset($profilesArray[$string]) ? $profilesArray[$string] : $string;
}

$smarty = new Template();
$smarty->assign('menu', $menu_metadata);
$smarty->register_modifier('getprofile', 'smarty_modifier_getprofile');

$pageContent = $smarty->fetch('ProfileTable.html');
Template::page(_('Profiles reference table'), $pageContent);

?>
