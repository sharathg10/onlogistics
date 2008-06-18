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

function prebuildPage($engine) {
    $auth = Auth::Singleton();
    Session::Singleton();
    // select de langue
    $engine->assign('languages', I18N::getSupportedLocales());
    $engine->assign('HtmlCharset', I18N::getLocaleEncoding());
    $engine->assign('IsUserConnected', $auth->isUserConnected());
    // css override
    $cssArray = glob(PROJECT_ROOT.'/css/override/*_' . Auth::getRealm() . '.css');
    if (is_array($cssArray)) {
        $cssArray = array_map(
            create_function('$a', 'return "css/override/".basename($a);'),
            $cssArray
        );
        $engine->assign('CustomCSS', $cssArray);
    }
    if ($auth->isUserConnected()) {
        // user connecté
        $identity = sprintf('%s@%s', $auth->getIdentity(), $auth->getRealm());
        // logo et slogan
        $mapper = Mapper::singleton('Actor');
        $act = $mapper->load(array('DatabaseOwner'=>1));
        if ($act instanceof Actor) {
            if (!DEV_VERSION) {
                $engine->assign('Logo', 'Logo.php?showDefaultLogo=1&actID='.$act->getId());
            } else {
                $engine->assign('Logo', 'images/logo.png');
            }
            $slogan = $act->getSlogan();
            if (!empty($slogan)) {
                $engine->assign('Slogan', $slogan);
            }
        } else {
            $engine->assign('Logo', 'images/logo.png');
        }
        // Menu
        Timer::start('Menu construction');
        require('menu.inc.php');
        $nav = Navigation::singleton();
        $nav->authFunction = array(Auth::singleton(), 'checkProfiles');
        $nav->useCookie = true;
        $menu = $nav->render();
        Timer::stop('Menu construction');
        $engine->assign('Menu', $menu);
        $engine->assign('UserIdentity', $identity);
        // gestion aide: uniquement si connecté
        $mapper = Mapper::singleton('HelpPage');
        $path = UrlTools::getPageNameFromURL();
        $helpPage = $mapper->load(array('FileName'=>$path));
        if ($helpPage instanceof HelpPage) {
            $engine->assign('HelpAvailable', 1);
            $engine->assign('HelpContent', $helpPage->render($engine));
        }
    }
    // footer
    if (defined('ONLOGISTICS_VERSION')) {
        $engine->assign('OnlogisticsVersion', ONLOGISTICS_VERSION);
    } 
}

/**
 * prebuildPopupPage()
 * Fonction appelée avant le render de chaque popup.
 *
 * @access public
 * @param  object $engine
 * @return void
 */
function prebuildPopupPage($engine) {
    Auth::Singleton();
    Session::Singleton();
    $engine->assign('HtmlCharset', I18N::getLocaleEncoding());
    // css override
    $cssArray = glob(PROJECT_ROOT.'/css/override/*_' . Auth::getRealm() . '.css');
    if (is_array($cssArray)) {
        $cssArray = array_map(
            create_function('$a', 'return "css/override/".basename($a);'),
            $cssArray
        );
        $engine->assign('CustomCSS', $cssArray);
    }
}

?>
