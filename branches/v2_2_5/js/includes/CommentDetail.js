var openerTextArea = opener.document.forms["otcreation"].comment;

onload = function GetInstructions() {
	with(document.forms["Detail"]) {
		detailField.value = openerTextArea.value;
	}
}

function UpdateInstructions() {
	with(document.forms["Detail"]) {
		openerTextArea.value = detailField.value;
	}
}