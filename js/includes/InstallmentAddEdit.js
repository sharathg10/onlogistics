/**
 *
 * @version $Id$
 * @copyright 2006 ATEOR
 **/

connect(window, 'onload', function() {
		RecalculateToPay();
		RecalculateUpdateIncur();
});

function RecalculateUpdateIncur(){
	UpdateIncur = fw.i18n.extractNumber(document.forms[0].elements["HiddenUpdateIncur"].value);
	MaxIncur = fw.i18n.extractNumber(document.forms[0].elements["HiddenMaxIncur"].value);
	Installment = fw.i18n.extractNumber(document.forms[0].elements["Installment"].value);
	//alert("Installment = "+Installment);
	UpdateIncur = subs(UpdateIncur, Installment);
	UpdateIncur = troncature(div(mul(100, UpdateIncur), 100));
    document.forms[0].elements["UpdateIncur"].value = isNaN(UpdateIncur)?" ---":fw.i18n.formatNumber(UpdateIncur);
    document.forms[0].elements["MaxIncur"].value = isNaN(MaxIncur)?"---":fw.i18n.formatNumber(MaxIncur);
}

function RecalculateToPay(){
	TotalTTC = fw.i18n.extractNumber(document.forms[0].elements["TotalTTC"].value);
	installment = fw.i18n.extractNumber(document.forms[0].elements["Installment"].value);
	ToPay = subs(TotalTTC, installment);
	if (installment < 0) {
		alert(InstallmentAdd_0);
		document.forms[0].elements["Installment"].value = 0;
		RecalculateUpdateIncur();
	}
	if (ToPay < 0) {
		alert(InstallmentAdd_1);
		document.forms[0].elements["Installment"].value = 0;
		RecalculateUpdateIncur();
		ToPay = TotalTTC;
	}else {
		ToPay = troncature(div(mul(100, ToPay), 100));
	}
    document.forms[0].elements["ToPay"].value = isNaN(ToPay)?"---":fw.i18n.formatNumber(ToPay);
    document.forms[0].elements["TotalTTC"].value = isNaN(TotalTTC)?"---":fw.i18n.formatNumber(TotalTTC);
}
