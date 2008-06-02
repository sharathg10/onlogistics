/**
 *
 * @version $Id: ProductQuantityByCategoryList.js,v 1.2 2008-05-07 16:36:39 ben Exp $
 * @copyright 2006 ATEOR
 */

requiredFields = new Array(
	new Array('CategoryIds[]', REQUIRED, NONE, ProductQuantityByCategoryList_0),
	new Array('MinimumQuantity', REQUIRED_AND_NOT_ZERO, NONE, ProductQuantityByCategoryList_1)
);