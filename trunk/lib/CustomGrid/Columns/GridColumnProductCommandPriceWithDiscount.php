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

class GridColumnProductCommandPriceWithDiscount extends AbstractGridColumn {
    /**
     * Object Customer
     *
     * @access protected
     */
    var $_actor;

    /**
     * Constructor
     *
     * @access protected
     */
    function __construct($title = '', $params = array()) {
        parent::__construct($title, $params);
        if (isset($params['actor'])) {
            $this->_actor = $params['actor'];
        }
    }

    public function render($object) {
        if ($object instanceof RTWModel || $object instanceof ProductModel) {
            $method = $object instanceof RTWModel ? 
                'getRTWProductCollection' : 'getProductCollection';
            $col = $object->$method();
            if (!count($col)) {
                return _('N/A');
            }
            $object = $col->getItem(0);
        }
        require_once('FormatNumber.php');
        $return = '<div class="grid_formatnumber">';
        // On récupère le prix exact du produit pour le client qui commande
        $act = $this->_actor;
        if (!($act instanceof actor)) {
            return $return . _('N/A') . '</div>';
        }
        // la devise du client
        $cur = $act->getCurrency();
        $curStr = $cur instanceof Currency?$cur->getSymbol():'&euro;';

        $unitHTForCustomerPrice = $object->getUnitHTForCustomerPrice($act);
        $alert = ''; // contiendra une infobulle si Promotion
        if ($unitHTForCustomerPrice < $object->getPriceByActor($act)) {
            // si promotion
            $Promotion = $object->getPromotion($act);
            if (!Tools::isEmptyObject($Promotion)) {
                $alert = "<a href=\"javascript:void(0);\" title=\""
                    . _('Offer on sale') . ": " .
                    $Promotion->getDisplayedRate($curStr) .
                    "\" style=\"text-decoration:none\">" .
                    "<font color=\"#FF0000\"><b>*</b></font></a>";
            }
        }
        // et de l'éventuelle remise par catégorie
        $catID = $act->getCategoryId();
        if ($catID > 0) {
            // on recherche une remise éventuelle
            require_once('SQLRequest.php');
            $rem = request_ProductHandingByCategory($object->getId(), $catID);
            if ($rem > 0) {
                $unitHTForCustomerPrice -= ($unitHTForCustomerPrice * ($rem/100));
            }
        }
        // On tient compte aussi de la Remise Exceptionnelle liee au Customer
        $remExp = $act->getRemExcep();
        if ($remExp > 0) {
            $unitHTForCustomerPrice -= ($unitHTForCustomerPrice * ($remExp/100));
        }
        $price = troncature($unitHTForCustomerPrice);
        $hidden = '<input type="hidden" name="HiddenPrice[]" size="4" value="'
                .$price . '">';
        $return .= sprintf('%s %s%s', I18N::formatCurrency($curStr, $price), $alert, $hidden);
        return $return . '</div>';

    }
}

?>
