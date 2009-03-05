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
 * @copyright 2003-2009 ATEOR <contact@ateor.com> 
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU AGPL
 * @version   SVN: $Id: GridColumnCommandItemList.php 282 2008-11-28 16:50:43Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 2.2.5
 * @filesource
 */

class GridColumnCommandRTWModelList extends SubGridColumn {
    /**
     * Permet d'afficher le detail des Commandes, avec les produits
     * regroupés par style / nom presse
     * 
     * @access private 
     */
    public function __construct($title = array(), $params = array()) {
        if (isset($params['PackagingUnitQty'])) {
            $this->_packagingUnitQty = $params['PackagingUnitQty'];
        }
        parent::__construct($title, $params);
    } 

    protected $gridInstance = false;

    public function render($object) {

        // Definition du subgrid ...
        $subGrid = new SubGrid();
        $subGrid-> NewColumn('FieldMapper', _('Name') , array('Macro' => "%Name%"));
        $subGrid-> NewColumn('FieldMapper', _('PressName') , array('Macro' => "%PressName%"));
        $subGrid-> NewColumn('FieldMapper', _('Description') , array('Macro' => "%Description%"));
        $subGrid-> NewColumn('FieldMapper', _('Quantity') , array('Macro' => "%Quantity%"));

        // On va chercher les donnees en base 
        $sql = "
SELECT 
    DISTINCT MDL._StyleNumber as Style, 
    I18n._StringValue_fr_FR as PressName, 
    MDL._Description as Designation, 
    SUM(CMD._Quantity) as Qty
FROM 
    Product as PDT , 
    RTWModel as MDL , 
    CommandItem as CMD, 
    RTWElement as ELM , 
    I18nString as I18n
WHERE 
    I18n._Id=ELM._Name 
    AND ELM._Id=MDL._PressName 
    AND PDT._Id=CMD._Product 
    AND MDL._Id=PDT._Model 
    AND CMD._Command='".$object->getId()."'
GROUP BY MDL._StyleNumber 
ORDER BY MDL._StyleNumber ASC";

        $rs = Database::connection()->execute($sql);

        // Puis on remplit la collection ... 
        $idx_id = 1 ; 
        $Collection = new Collection() ;
        while ($rs && !$rs->EOF) { 
            $ps = new ProductStyle();
            $ps->setId($idx_id);
            $ps->setName($rs->fields['Style']);
            $ps->setPressName($rs->fields['PressName']);
            $ps->setDescription($rs->fields['Designation']);
            $ps->setQuantity(intval($rs->fields['Qty']));

            $Collection->setItem($ps);
            unset($ps);
            $rs->moveNext();
            $idx_id++ ;
        }

        // assign d'une var (smarty)  pour interaction via js sur les id css
        //$subGrid->assign('commandId',$object->getId());

        $subGrid->setMapper($Collection);
        $result = $subGrid->render($Collection);
        return $result ;

    }
} 

?>
