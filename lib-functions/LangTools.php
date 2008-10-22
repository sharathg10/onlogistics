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

function getJSCalendarLangFile() {
    // on regarde s'il existe un fichier de trad pour la langue courante
    $jsfile = 'js/jscalendar/lang/calendar-%s.js';
    $file   = sprintf($jsfile, I18N::getLocaleCode(true));
    if (file_exists(PROJECT_ROOT . '/' . $file)) {
        return $file;
    }
    // par defaut on retourne le fichier pour la langue anglaise
    return sprintf($jsfile, 'en');
}

/**
 * Retourne le paramètre de langue à passer à Numbers_Words::toWords() en 
 * fonction de la langue courante.
 *
 * Attention: ne pas enlever les @ devant les include_once, et ne pas
 * remplacer par require_once.
 * 
 * @param access public
 * @return string
 */
function getNumberWordsLangParam() {
    $default = 'fr';
    // on essaie de voir s'il existe une trad avec le code abrégé (ex. fr)
    $code = I18N::getLocaleCode(true);
    $exists = @include_once('Numbers/Words/lang.' . $code . '.php');
    if ($exists) {
        return $code;
    }
    // sinon on essaie avec le code entier (ex. en_GB)
    $code = I18N::getLocaleCode();
    $exists = @include_once('Numbers/Words/lang.' . $code . '.php');
    if ($exists) {
        return $code;
    }
    return $default;
}

?>
