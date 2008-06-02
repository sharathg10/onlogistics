/**
 *
 * @version $Id: ZoneAddEdit.js,v 1.7 2008-05-07 16:36:39 ben Exp $
 * @copyright 2006 ATEOR - All rights reserved.
 */

function onLoad() {
    fw.ajax.updateSelect('country', 'state', 'State', 'Country');
    fw.ajax.updateSelect('country', 'department', 'Department', 'Country');
    fw.ajax.updateSelectCustom('country', 'site', 'Site', 'CountryCity.Country', 'zoneaddedit_getCollection');
    return false;
    // Pas trouve la possibilite de passer des arguments a la fction appelee
	// dans connect()...
}

connect(window, 'onload', onLoad);

/**
 * Verifie si on a bien selectionne au moins une Commune si clic sur 'Affecter les villes selectionnees'
 * oubien au moins un site si clic sur 'Affecter les sites selectionnes'
 *
 * @param string entity 'countrycity' or 'site'
 * @return boolean
 **/
function checkBeforeSubmit(entity) {
    if (entity == 'countrycity') {
        var selectedValues = fw.dom.getMultipleSelectValues('countrycity');
        if (selectedValues.length == 0) {
            alert(ZoneAddEdit_0);
            return false;
        }
    }
    else if (entity == 'site') {
        var selectedValues = fw.dom.getMultipleSelectValues('site');
        if (selectedValues.length == 0 || (selectedValues.length == 1 && selectedValues[0] == '##')) {
            alert(ZoneAddEdit_1);
            return false;
        }
    }
    return true;
}
