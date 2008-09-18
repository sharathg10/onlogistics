/**
 *
 * @version $Id$
 * @copyright 2007 ATEOR
 */

connect(window, 'onload', function() {
    isAeroActor();
    if (!$('isCustomer').checked && !$('isSupplier').checked) {
        $('isNothing').checked = true;
    }
});

requiredFields = new Array(
	new Array('Actor_Name', REQUIRED, NONE, ActorAddEdit_0),
	new Array('SupplierCustomer_MaxIncur', NONE, 'int', ActorAddEdit_2)
);

function isAeroActor() {
	var className
	className = document.forms[0].elements["Actor_ClassName"].value;
	$('Aero').style.display=(className.substring(0, 4) == "Aero")?"block":"none";
	var mustChange = false;
	if (className == "AeroCustomer") {
		var disabled1 = true;
		var disabled2 = false;
		mustChange = true;
	} else if (className == "AeroInstructor") {
		var disabled1 = false;
		var disabled2 = true;
		mustChange = true;
	}
	if (true == mustChange) {
		document.forms[0].elements["Actor_Trainee"][0].disabled = disabled2;
		document.forms[0].elements["Actor_Trainee"][1].disabled = disabled2;
		document.forms[0].elements["Actor_SoloFly"][0].disabled = disabled2;
		document.forms[0].elements["Actor_SoloFly"][1].disabled = disabled2;
	}
	if (className == "Supplier") {
		$('DocumentModel').style.display="none";
	}
	else {
        $('DocumentModel').style.display=document.forms[0].elements["IsCustomerOrSupplier"].value;
	}
}

/**
 * Affiche une alerte si aucune Category n'est affectee a l'Actor
 * dans le cas ou "Celle par défaut" est selectionne dans le select de la
 * Fréquence de visite
 *
 * @return void
 **/
function checkForDefault() {
    with (document.forms[0]) {
        widget = elements["CustomerProperties_PersonalFrequency_ID"];
        customerFrequencyId = widget.options[widget.selectedIndex].value;
        if (customerFrequencyId == -1) {
            categWidget = elements["Actor_Category_ID"];
            if (categWidget.options[categWidget.selectedIndex].value == 0) {
                alert(ActorAddEdit_14);
            }
        }
    }
}

/**
 *
 * Verifie que pour un Supplier tel que son OnlogisticsAccount soit non null,
 * dans sa base, l'Actor connecte et ce Supplier soient bien existants
 * @return boolean
 **/
function checkActors() {
    if ($('Actor_OnlogisticsAccount').value == '##') {
        return true;
    }
    var d = fw.ajax.call('actoraddedit_checkActors', $('Actor_Name').value, $('Actor_OnlogisticsAccount').value);
    var onSuccess = function (data) {
        if (data.isError) {
            alert(E_Error_Title + ': ' + data.errorMessage + '.');
            var myCallback2 = function () {
                fw.dom.selectOptionByValue($('Actor_OnlogisticsAccount'), '##');
            }
            d.addCallback(myCallback2);
            return false;
        }
        if (data == false) {
            alert(ActorAddEdit_15 + $('Actor_OnlogisticsAccount').value + ' ' + ActorAddEdit_16 + '.');
            var myCallback2 = function () {
                fw.dom.selectOptionByValue($('Actor_OnlogisticsAccount'), '##');
            }
            d.addCallback(myCallback2);
            return false;
        }
        /*if (data != true) {  // string: Exception->getMessage()
            alert(E_Error_Title + ': ' + data + '.');
            return false;
        }*/
        return true;
    }
    var onError = function (err) {
        alert(E_Error_Title + ': ' + err);
        var myCallback2 = function () {
            fw.dom.selectOptionByValue($('Actor_OnlogisticsAccount'), '##');
        }
        d.addCallback(myCallback2);
        return false;
    }
    d.addCallbacks(onSuccess, onError);
    return true;
}
