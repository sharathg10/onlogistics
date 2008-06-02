/**
 *
 * @version $Id: SiteAddEdit.js,v 1.8 2008-05-07 16:36:39 ben Exp $
 * @copyright 2007 ATEOR
 */
requiredFields = new Array(
	new Array('Site_Name', REQUIRED, NONE, SiteAddEdit_3),
/*	Commente pour madagascar: pas de zipcode
    new Array('Site_CountryCity_Zip_Code', REQUIRED, NONE, SiteAddEdit_0),*/
	new Array('Site_CountryCity_Country_Name', REQUIRED, NONE, SiteAddEdit_1)
);

function onCancel(retURL){
	if(window.opener) {
		return window.close();
	}
	window.location.href = retURL;
    return 1;
}

function isStorageSiteClicked(ownerform, displayAlert) {
    if (displayAlert) {
        alert(SiteAddEdit_2);
        ownerform.elements['IsStorageSite'].checked = true;
    }
    var elt = ownerform.elements['StockOwner_Id'];
    elt.disabled = (!ownerform.elements['IsStorageSite'].checked);
}

var validResult = false;

/**
 *
 * @access public
 * @return void
 **/
function validateform(addEditForm, requiredFields) {
    var siteName = addEditForm.elements['Site_Name'].value;
    // Dans un 1er temps car checkForm() contient form.submit()
    if (siteName != '') {
        var d = fw.ajax.call('siteaddedit_sitenameIsOk', siteName);
        var onSuccess = function (data) {
            if (data == true) {
                validResult = true;
            }else {
                //validResult = false;
                alert(SiteAddEdit_4);
            }
        }
        var onError = function (err) {
            //validResult = false;
            alert('Erreur: ' + err);
        }
        d.addCallbacks(onSuccess, onError);
        var myCallback2 = function () {
            if (validResult) {
                if (validateCity() == false || checkForm(addEditForm, requiredFields) == false) {
                    return false;
                }
            }
        }
        d.addCallback(myCallback2);
        return false;
    }
    else {
        if (validateCity() && checkForm(addEditForm, requiredFields)) {
            return true;
        }
        return false;
    }
}

/**
 * Checke si le zipcode ou le nom de commune sont saisis
 * @return boolean
 **/
function validateCity() {
    if ($('Site_CountryCity_Zip_Code').value == '' && $('Site_CountryCity_City_Name').value == '') {
        alert(SiteAddEdit_5);
        return false;
    }
    return true;
}