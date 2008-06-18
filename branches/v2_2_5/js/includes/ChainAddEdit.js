/**
 * $Source: /home/cvs/onlogistics/js/includes/ChainAddEdit.js,v $
 * Fonctions JS pour le script d'ajout/edition de chaines => ChainAddEdit.php
 * 
 * @version $Id$
 * @copyright © 2005 - ATEOR - All rights reserved.
 */

// appel des fonctions au chargement de la page
connect(window, 'onload', function(){
	d1 = fw.ajax.updateSelectCustom('DepartureActor_ID', 'DepartureSite_ID', 'Site',
        'Owner', 'chainaddedit_getSiteCollection', true);
	d2 = fw.ajax.updateSelectCustom('ArrivalActor_ID', 'ArrivalSite_ID', 'Site',
        'Owner', 'chainaddedit_getSiteCollection', true);
    d1.addBoth(function() {
        fw.dom.selectOptionByValue(
            'DepartureSite_ID', 
            $('HiddenDepartureSite_ID').value
        );
    });
    d2.addBoth(function() {
        fw.dom.selectOptionByValue(
            'ArrivalSite_ID',
            $('HiddenArrivalSite_ID').value
        );
    });
});
