<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of Onlogistics, a web based ERP and supply chain 
 * management application. 
 *
 * Copyright (C) 2003-2008 ATEOR
 *
 * This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU Affero General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or (at your 
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public 
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5.1.0+
 *
 * @package   Onlogistics
 * @author    ATEOR dev team <dev@ateor.com>
 * @copyright 2003-2008 ATEOR <contact@ateor.com> 
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU AGPL
 * @version   SVN: $Id$
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

require_once('Objects/ProductHandingByCategory.php');
require_once('FormatNumber.php');

class GridColumnProductHandingByCategoryAddEdit extends AbstractGridColumn {
    /**
     *
     * @access private
     */
    private $_currencyId;
    private $_handing = 0;
    private $_handingType = ProductHandingByCategory::TYPE_PERCENT;
    private $_handingCurrency;

    /**
     * Constructor
     *
     * @access protected
     */
    function GridColumnProductHandingByCategoryAddEdit($title = '', $params = array()) {
        $params['Sortable'] = false;
		parent::__construct($title, $params);

        if (isset($params['handing'])) {
            $this->_handing = (float)$params['handing'];
        }
        if (isset($params['CurrencyId'])) {
            $this->_currencyId = $params['CurrencyId'];
        } else {
			Template::errorDialog(_('Error'), 'ProductHandingByCategoryList.php');
    		exit;
        }
        if(isset($params['handingType'])) {
            $this->_handingType = $params['handingType'];
        }
        if(isset($params['handingCurrency'])) {
            $this->_handingCurrency = $params['handingCurrency'];
        }
    }

    function Render($object) {
		$mapper = Mapper::singleton('PriceByCurrency');
        $method = $object instanceof ProductHandingByCategory?'getProductId':'getId';
		$PriceByCurrency = $mapper->load(array('Currency' => $this->_currencyId,
											 	    'Product' => $object->$method()));
		if (Tools::isEmptyObject($PriceByCurrency)) {
		    return _('N/A');
		}
		$result = I18N::formatNumber($PriceByCurrency->getPrice());
        if ($this->_handing > 0) {
            $newPrice = 0;
            if($this->_handingType == ProductHandingByCategory::TYPE_PERCENT) {
                $newPrice = $PriceByCurrency->getPrice() * (1 - ($this->_handing / 100));
            } else {
                if($this->_handingCurrency == $this->_currencyId) {
                    $newPrice = $PriceByCurrency->getPrice() - $this->_handing;
                }
            }
            $result .= ' <b>(' . I18N::formatNumber(troncature($newPrice))
                . ')</b>';
        }

		return $result;
    }
}

?>
