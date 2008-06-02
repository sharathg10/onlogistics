/**
 * $Id: TZone.js,v 1.3 2008-05-07 16:36:39 ben Exp $
 * Classe definissant l'objet Zone
 *
 */
function TZone(id, name){
	this.Id = id;
	this.Name = name;
}
/**
 * Setter de Id
 * @param Id
 */
function TZone_setId(value){
	this.Id = value;
}
TZone.prototype.setId = TZone_setId;
/**
 * Getter de Id
 */
function TZone_getId(){
	return this.Id;
}
TZone.prototype.getId = TZone_getId;
/**
 * Setter de Name
 * @param Name
 */
function TZone_setName(value){
	this.Name = value;
}
TZone.prototype.setName = TZone_setName;
/**
 * Getter de Name
 */
function TZone_getName(){
	return this.Name;
}
TZone.prototype.getName = TZone_getName;

/**
 * Debug function
 * Dump in a message the content of the object
 *
 **/
function debug() {
	var msg = "+TZone Constructor : " + this.constructor + "\n+ Properties : \n";
	msg += " Property  = " + typeof(this.getId()) + " : " + this.getId() + "\n";
	msg += " Property  = " + typeof(this.getName()) + " : " + this.getName() + "\n";
	if(!confirm(msg)) {
		return false;
	}
}
TZone.prototype.debug = debug;