/**
 *
 * @version $Id: ProductHandingByCategoryAddEdit.js,v 1.2 2008-05-07 16:36:39 ben Exp $
 * @copyright 2006 ATEOR
 */

requiredFields = new Array(
	new Array('handing', 
		REQUIRED_AND_NOT_ZERO, 'float', ProductHandingByCategoryAddEdit_0),
	new Array('CategoryIds[]', 
		REQUIRED_AND_NOT_ZERO, 'select', ProductHandingByCategoryAddEdit_1)
);