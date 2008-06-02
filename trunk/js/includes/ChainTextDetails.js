var openerTextArea = opener.document.forms["TaskDetails"].WorkInstructions;

onload = function GetInstructions() {
	with(document.forms["TextDetail"]) {
		InstructionsField.value = openerTextArea.value;
	}
}

function UpdateInstructions() {
	with(document.forms["TextDetail"]) {
		openerTextArea.value = InstructionsField.value;
	}
}
