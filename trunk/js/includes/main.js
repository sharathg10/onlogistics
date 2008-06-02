/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * $Source: /home/cvs/onlogistics/js/includes/main.js,v $
 *
 * Fichier inclus dans toutes les pages de l'application.
 *
 * @version   CVS: $Id: main.js,v 1.10 2008-05-07 16:36:39 ben Exp $
 * @copyright 2002-2006 ATEOR - All rights reserved
 */

/**
 * Globales nécessaires au widget wysiwyg actuel htmlarea
 *
 */
var _editor_url = "js/htmlarea/";
var _editor_lang = "fr";

/**
 * Script du onload de chaque page.
 *
 */
connect(window, 'onload', function() {
    // Mise du focus sur le bouton OK des Dialog
    if ($('okDialogButton')) {
        $('okDialogButton').focus();
    }
    // action sur le bouton d'affichage/masquage du logo
    if ($('logo_switch')) {
        connect($('logo_switch'), 'onclick', function() {
            toggleElementClass('css_disabled', $('header_logo'));
            toggleElementClass('state_show', $('logo_switch'));
            fw.cookie.create('ol_showhideCookie', $('header_logo').className);
        });
    }
    // action sur le bouton d'affichage/masquage du logo
    if ($('timer_link')) {
        connect($('timer_link'), 'onmouseover', function() {
            showElement($('timer'));
        });
        connect($('timer_link'), 'onmouseout', function() {
            hideElement($('timer'));
        });
    }
    // la locale de l'application
    var locale = fw.i18n.getLocale();
    // select langue page login
	var langSelect = $('login_language_select');
    if (langSelect) {
        if (locale) {
            fw.dom.selectOptionByValue(langSelect, locale);
        }
        connect(langSelect, 'onchange', function() {
            fw.i18n.setLocale(langSelect.value);
        });
    }
    // select langue haut de page sur toute l'appli
	var topLangSelect = $('language_select');
    if (topLangSelect) {
        if (locale) {
            fw.dom.selectOptionByValue(topLangSelect, locale);
        }
        connect(topLangSelect, 'onchange', function() {
            fw.i18n.setLocale(topLangSelect.value);
            location.reload();
        });
    }
    // action onclick sur le bouton d'aide
	var helpButton = $('help_button');
    if (helpButton) {
        connect(helpButton, 'onclick', function() {
            fw.dom.toggleElement($('help_layer'));
        });
    }
    // action onclick sur le bouton de fermeture du layer d'aide
    // et drag sur le layer d'aide
	var helpCloseButton = $('help_close_button');
    if (helpCloseButton) {
        connect(helpCloseButton, 'onclick', function() {
            fw.dom.toggleElement($('help_layer'));
        });
        new Draggable($('help_layer'));
    }
    var cookie = fw.cookie.read('ol_showhideCookie');
    if (cookie != null && cookie != "") {
        $('header_logo').className = cookie;
        $('logo_switch').className = 'state_show';
    }
});
