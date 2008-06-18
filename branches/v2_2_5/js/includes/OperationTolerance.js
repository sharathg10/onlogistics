connect(window, 'onload', function() {
    with(document.forms[0]) {
        FillWithOperation(elements["operation"],0);
        var CurrentObject = OperationToleranceCollection.getItem(0);
        FillToleranceFields(CurrentObject.getOperationId());
        Layer = MochiKit.DOM.getElement('HiddenFields');
        DivMessage = MochiKit.DOM.getElement('Message');
        ChangeCheckbox(CurrentObject);
    }
});

function ConfirmChanges(ModifiedObjectId){
    var ModifiedObject = eval('Operation' + ModifiedObjectId);
    var WidgetFrontTolerance = WidgetToTime(
        document.forms[0].elements["frontToleranceh"].value,
        document.forms[0].elements["frontTolerancemn"].value);
    var WidgetEndTolerance = WidgetToTime(
        document.forms[0].elements["endToleranceh"].value,
        document.forms[0].elements["endTolerancemn"].value);
    var WidgetTotalTolerance = WidgetToTime(
        document.forms[0].elements["totaltoleranceh"].value,
        document.forms[0].elements["totaltolerancemn"].value);

    if(WidgetFrontTolerance != ModifiedObject.getFrontTolerance() ||
            WidgetEndTolerance != ModifiedObject.getEndTolerance() ||
            WidgetTotalTolerance != ModifiedObject.getTotalTolerance()) {
        ModifiedObject.setFrontTolerance(WidgetFrontTolerance);
        ModifiedObject.setEndTolerance(WidgetEndTolerance);
        ModifiedObject.setTotalTolerance(WidgetTotalTolerance);	
        WriteToHiddenField(ModifiedObject);
        DivMessage.style.display = 'block';
        DivMessage.innerHTML = "<br />" + OperationTolerance_0 + "<br /><br />";
    }
    ModifiedObject = '';
}

function ChangeCheckbox(o) {
	var widget1 = document.forms[0].elements["type"][0];
    var widget2 = document.forms[0].elements["type"][1];
    widget1.checked = (o.getTotalTolerance() == '00:00:00' || o.getTotalTolerance() == '');
	widget2.checked = (o.getTotalTolerance() != '00:00:00' && o.getTotalTolerance() != '');
}

function WidgetToTime(hh,mm) {
    if (hh.length==1){hh='0' + hh;}
    if (mm.length==1){mm='0' + mm;}
    return hh + ':' + mm + ':00';
}	

function FillToleranceFields(opindex){
    var CurrentObject = eval('Operation' + opindex);
    ChangeCheckbox(CurrentObject);
    TimeToWidget(CurrentObject.getFrontTolerance(),
        document.forms[0].elements["frontToleranceh"],
        document.forms[0].elements["frontTolerancemn"]);
    TimeToWidget(CurrentObject.getEndTolerance(),
        document.forms[0].elements["endToleranceh"],
        document.forms[0].elements["endTolerancemn"]);
    TimeToWidget(CurrentObject.getTotalTolerance(),
        document.forms[0].elements["totaltoleranceh"],
        document.forms[0].elements["totaltolerancemn"]);		
    document.forms[0].elements["currentopindex"].value 
        = CurrentObject.getOperationId();
    CurrentObject = '';
}

function TimeToWidget(time,widgetHH,widgetMM) {
    var HH = explode (time,':',1);
    if (HH.length==1){HH='0'+HH;}
    var MM = explode (time,':',2);
    if (MM.length==1){MM='0'+MM;}
    widgetHH.value = HH;
    widgetMM.value = MM;
}

// ecriture des champ hidden dans la balise div HiddenFields

function WriteToHiddenField (operation){

    var valueToWrite = eval(document.forms[0].elements["type"][0].checked) 
        + "|" + operation.getFrontTolerance() 
        + "|" + operation.getEndTolerance() 
        + "|" + operation.getTotalTolerance();
    Layer.innerHTML = Layer.innerHTML 
        + "<input type=\"hidden\" name=\"OperationModified[" 
        + operation.getOperationId() 
        + "]" + "\" value = \"" 
        + valueToWrite + "\">\n";
}

function explode(expression,separator,what)
{
    if (expression!=null){
        var pattern = new RegExp("([0-9][0-9])"+ separator 
            +"([0-9][0-9])"+separator+"([0-9][0-9])","ig");
        pattern.exec(expression);
        if(what==1) { return RegExp.$1; } else { return RegExp.$2; }
    }
}
