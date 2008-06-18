/*
 * @version $Id$
 * @copyright 2006 ATEOR
 **/

function onLoad() {
    // Ici, on construit 2 Deferred imbriqués!!
    var dd = fw.ajax.updateSelect('country', 'state', 'State', 'Country');
    var myCallback = function () {
        fw.dom.selectOptionByValue('state', $('stateId').value);
    }
    dd.addCallback(myCallback);

    var myCallback2 = function () {
        var ddd = fw.ajax.updateSelect('state', 'department', 'Department', 'State');
        var myCallback3 = function () {
            fw.dom.selectOptionByValue('department', $('departmentId').value);
        }
        ddd.addCallback(myCallback3);
    }
    dd.addCallback(myCallback2);
}

connect(window, 'onload', onLoad);
