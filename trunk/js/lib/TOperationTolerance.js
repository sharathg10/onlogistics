/**
 * Classe des tolerance des opérations
 *
 */
function TOperationTolerance(){
	this.OperationId = false;
	this.FrontTolerance = false;
	this.EndTolerance = false;
	this.TotalTolerance = false;
	this.BeginTolerance = false;
}
/**
 * Setter de OperationId
 * @param OperationId
 */
function TOperationTolerance_setOperationId(value){
	this.OperationId = value;
}
TOperationTolerance.prototype.setOperationId = TOperationTolerance_setOperationId;
/**
 * Getter de OperationId
 */
function TOperationTolerance_getOperationId(){
	return this.OperationId;
}
TOperationTolerance.prototype.getOperationId = TOperationTolerance_getOperationId;
/**
 * Setter de FrontTolerance
 * @param FrontTolerance
 */
function TOperationTolerance_setFrontTolerance(value){
	this.FrontTolerance = value;
}
TOperationTolerance.prototype.setFrontTolerance = TOperationTolerance_setFrontTolerance;
/**
 * Getter de FrontTolerance
 */
function TOperationTolerance_getFrontTolerance(){
	return this.FrontTolerance;
}
TOperationTolerance.prototype.getFrontTolerance = TOperationTolerance_getFrontTolerance;
/**
 * Setter de EndTolerance
 * @param EndTolerance
 */
function TOperationTolerance_setEndTolerance(value){
	this.EndTolerance = value;
}
TOperationTolerance.prototype.setEndTolerance = TOperationTolerance_setEndTolerance;
/**
 * Getter de EndTolerance
 */
function TOperationTolerance_getEndTolerance(){
	return this.EndTolerance;
}
TOperationTolerance.prototype.getEndTolerance = TOperationTolerance_getEndTolerance;
/**
 * Setter de TotalTolerance
 * @param TotalTolerance
 */
function TOperationTolerance_setTotalTolerance(value){
	this.TotalTolerance = value;
}
TOperationTolerance.prototype.setTotalTolerance = TOperationTolerance_setTotalTolerance;
/**
 * Getter de TotalTolerance
 */
function TOperationTolerance_getTotalTolerance(){
	return this.TotalTolerance;
}
TOperationTolerance.prototype.getTotalTolerance = TOperationTolerance_getTotalTolerance;
/**
 * Setter de BeginTolerance
 * @param BeginTolerance
 */
function TOperationTolerance_setBeginTolerance(value){
	this.BeginTolerance = value;
}
TOperationTolerance.prototype.setBeginTolerance = TOperationTolerance_setBeginTolerance;
/**
 * Getter de BeginTolerance
 */
function TOperationTolerance_getBeginTolerance(){
	return this.BeginTolerance;
}
TOperationTolerance.prototype.getBeginTolerance = TOperationTolerance_getBeginTolerance;
/**
 * Debug function
 * Dump in a message the content of the object
 *
 **/
function debug() {
	var msg = "+TOperationTolerance Constructor : " + this.constructor + "\n+ Properties : \n";
	msg += " Property  = " + typeof(this.getOperationId()) + " : " + this.getOperationId() + "\n";
	msg += " Property  = " + typeof(this.getFrontTolerance()) + " : " + this.getFrontTolerance() + "\n";
	msg += " Property  = " + typeof(this.getEndTolerance()) + " : " + this.getEndTolerance() + "\n";
	msg += " Property  = " + typeof(this.getTotalTolerance()) + " : " + this.getTotalTolerance() + "\n";
	msg += " Property  = " + typeof(this.getBeginTolerance()) + " : " + this.getBeginTolerance() + "\n";
	if(!confirm(msg)) {
		return false;
	}
}
TOperationTolerance.prototype.debug = debug;
