/*
 * @version $Id$
 * @copyright 2006 ATEOR
 **/

function onLoad() {
    var dd = fw.ajax.updateSelect('country', 'state', 'State', 'Country');
    var myCallback = function () {
        fw.dom.selectOptionByValue('state', $('stateId').value)
    }
    dd.addCallback(myCallback);
}

connect(window, 'onload', onLoad);
