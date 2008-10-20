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
 * @version   SVN: $Id: GridActionDownloadUploadedDocument.php 9 2008-06-06 09:12:09Z izimobil $
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */


class GridActionActivatedMovementListExport extends AbstractGridAction
{ 
    /**
     * GridActionDownloadUploadedDocument::__construct()
     * 
     */
    function __construct($params = array())
    {
        $this->filter = $params['Filter'];
        $this->order  = $params['Order'];
        parent::__construct($params);
    }

    /**
     * GridActionDownloadUploadedDocument::execute()
     * 
     */
    function execute($objects, $itemsIds)
    {
        $acmCol = Object::loadCollection('ActivatedMovement', $this->filter, $this->order);
        $sizes  = Object::loadCollection('RTWSize', array(), array('Name' => SORT_ASC));
        $sizesCount = $sizes->getCount();
        // commandNo, mvttype, exp., dest., model, size ..., begin date
        $registry = array();
        $headers  = array(_('Order number'), _('Model'), _('Movement type'), _('Expeditor'), _('Adressee'), _('Begin date'));
        $start = count($headers);
        foreach ($headers as &$header) {
            $header = Grid::formatDataForExport($header);
        }
        foreach ($sizes as $size) {
            $headers[] = Grid::formatDataForExport($size->getName());
        }
        $headers[] = Grid::formatDataForExport(_('Total quantity'));
        $registry['headers'] = $headers;
        foreach ($acmCol as $item) {
            $product = $item->getProduct();
            $commandId = $item->getProductCommandId();
            $model   = $product->getModel();
            if ($model instanceof RTWModel) {
                $modelId = 'M'.$model->getId();
                $modelSN = $model->getStyleNumber();
            } else {
                $modelId = 'A'.$item->getId();
                $modelSN = $product->getBaseReference();
            }
            $index = $commandId . '_' . $modelId;
            if (!isset($registry[$index])) {
                $registry[$index] = array(
                    Grid::formatDataForExport(Tools::getValueFromMacro($item, '%ProductCommand.commandNo%')),
                    Grid::formatDataForExport($modelSN),
                    Grid::formatDataForExport(Tools::getValueFromMacro($item, '%Type%')),
                    Grid::formatDataForExport(Tools::getValueFromMacro($item, '%ProductCommand.Expeditor.Name%')),
                    Grid::formatDataForExport(Tools::getValueFromMacro($item, '%ProductCommand.Destinator.Name%')),
                    Grid::formatDataForExport(Tools::getValueFromMacro($item, '%StartDate|formatdate%')),
                );
                // sizes qties
                for ($i=0; $i<$sizesCount; $i++) {
                    $registry[$index][] = '';
                }
                // total qty
                $registry[$index][] = 0;
            }
            if ($model instanceof RTWModel) {
                $totalQty = 0;
                for ($i=0; $i<$sizesCount; $i++) {
                    $size = $sizes->getItem($i);
                    if ($product->getSizeId() == $size->getId()) {
                        $qty = $item->getQuantity();
                        $totalQty += $qty;
                        $registry[$index][$start+$i] = Grid::formatDataForExport(
                            $this->getQuantity($item)
                        );
                    }
                }
                $registry[$index][$start+$i] += (int)$totalQty;
            } else {
                $registry[$index][count($registry[$index])-1] = $this->getQuantity($item);
            }
        }
        header('Pragma: public');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment;filename=movements.csv');
        foreach ($registry as $id => $lineArray) {
            echo implode(';', $lineArray) . "\n";
        }
        exit(0);
    } 

    /**
     * Return the movement qty formated.
     *
     * @param ActivatedMovement $object
     * @param float             $qty
     *
     * @return string
     */
    protected function getQuantity($object, $qty = null)
    {
        if ($qty == null) {
            $remainingQuantity = $object->getRemainingQuantity();
            $product = $object->getProduct();
            $qty = ($remainingQuantity < $object->getQuantity())?
                I18N::formatNumber($remainingQuantity, 3, true) . "/"
                    . I18N::formatNumber($object->getQuantity(), 3, true):
                I18N::formatNumber($remainingQuantity, 3, true);
        }
        return $qty . $product->getMeasuringUnit();
    }
} 

?>
