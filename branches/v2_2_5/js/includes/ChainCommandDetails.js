openerTextArea = window.opener.document.forms[0].ccmComment;

onload = function() {
	with(document.forms[0]) {
		InstructionsField.value = openerTextArea.value;
	}
}

function UpdateInstructions() {
	with(document.forms[0]) {
		openerTextArea.value = InstructionsField.value;
	}
}