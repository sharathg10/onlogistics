function checkIsSingle(elementsName, input)
{
    // on décoche pas de verif à faire
    if(input.checked == false) {
        return true;
    }
    // on coche
    var elementsArray = document.getElementsByName(elementsName);
    for(var i=0 ; i<elementsArray.length ; i++) {
        if(elementsArray[i].checked == true && elementsArray[i].value != input.value) {
            elementsArray[i].checked = false;
        }
    }
}

/*function checkBeforeSubmit()
{
    if(document.forms[0].elements['Action_State'][0].options[0].selected==true
    && document.forms[0].elements['Action_State'][1].options[0].selected==true) {
        alert('Vous devez indiquer un statut');
        return false;
    }
    document.forms[0].submit();
}*/

function synchronizeActionStates(element)
{
    for(var i=0 ; i<element.options.length ; i++ ) {
        if(element.options[i].selected==true) {
            document.forms[0].elements['Action_State'][0].options[i].selected=true;
            document.forms[0].elements['Action_State'][1].options[i].selected=true;
        }
    }
    
}