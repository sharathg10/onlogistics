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

require_once('LangTools.php');
require_once('FormatNumber.php');

/**
 *
 */
class PrestationCostAddEdit extends GenericAddEdit {
    // Properties {{{

    /**
     * ID de la prestation associée.
     * @var int
     * @access private
     */
    private $_prestationID;

    /**
     * Tableau de correspondance contenant le nom de la classe et le label pour
     * chaque XXXPrestationCost/
     * @var array
     * @access private
     */
    private $_entityMapping = array();

    /**
     * Nom de la classe de l'object lié au XXXPrestationCost (Product, FlyType,
     * ConcreteProduct)
     * @var string
     * @access private
     */
    private $_linkObject = '';

    /**
     * Label de l'objet lié au XXXPrestationCost.
     * @var string
     * @access private
     */
    private $_linkLabel = '';

    // }}}
    // PrestationCostAddEdit::__construct() {{{

    /**
     * __construct
     *
     * @param array $params
     * @access public
     * @return void
     */
    public function __construct($params) {
        $params['use_session'] = true;
        $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_ADMIN_VENTES);
        parent::__construct($params);
        $this->addJSRequirements(
            'JS_AjaxTools.php',
            'js/includes/PrestationCostAddEdit.js'
        );

        if(isset($_SESSION['prestationID'])) {
            $this->_prestationID = $_SESSION['prestationID'];
        } else {
            $this->_prestationID = $_GET['prsId'];
        }
        Session::register('prestationID', $this->_prestationID);

        $this->returnURL = 'dispatcher.php?entity=' . $this->clsname .
            '&altname=PrestationCost&prsId=' . $this->_prestationID;

        $this->_entityMapping = array(
            'ProductPrestationCost'         => array(
                'clsname'=>'Product', 'label'=>_('product')),
            'ConcreteProductPrestationCost' => array(
                'clsname'=>'ConcreteProduct', 'label'=>_('SN/Lot')),
            'FlyTypePrestationCost'         => array(
                'clsname'=>'FlyType', 'label'=>_('airplane types')));
        $this->_linkObject = $this->_entityMapping[$this->clsname]['clsname'];
        $this->_linkLabel = $this->_entityMapping[$this->clsname]['label'];
        $prs = Object::load('Prestation', $this->_prestationID);
        $this->title = $this->title . ' ' . $prs->getName();
    }

    // }}}
    // PrestationCostAddEdit::additionalFormContent() {{{

    /**
     * Ajoute le grid étitable des CostRange
     *
     * @return string
     * @todo rende celà générique
     */
    protected function additionalFormContent() {
        $collections = array('CostRange');

        $html = '';
        foreach($collections as $name) {
            $o = Object::load($name);
            $mapping = $o->getMapping();
            $headerCells = '';
            $cells = 1;
            foreach ($mapping as $field=>$properties) {
                if(in_array('addedit', $properties['usedby'])) {
                    $headerCells .= '<td>'.$properties['shortlabel'].'</td>';
                }
                $cells++;
            }
            $headerCells .= "<td>&nbsp;</td>";

            $html .= "<tr><th colspan=\"3\">".$o->getObjectLabel()."</th> "
             . "<th><span id=\"searchform_switch\" onclick=\"fw.dom.toggleElement('"
             . $name . "_grid');\" title=\"" . _('Show/Hide') . "\"></span></th><tr>\n"
             . "<tr><td colspan=\"4\">\n"
             . "<div class=\"grid\" id=\"".$name."_grid\">\n"
             . "<table id=\"".$name."TABLE\" cellspacing=\"0\" "
             . "cellpadding=\"0\" border=\"0\" width=\"100%\">\n"
             . "<thead>\n"
             . "<tr>" . $headerCells . "</tr>\n"
             . "</thead>\n"
             . "<tfoot>\n"
             . "<tr><td colspan=\"".$cells."\" align=\"right\">"
             . "<input type=\"button\" id=\"add".$name."\" class=\"button\" value=\"" . _('Add') . "\"/>"
             . "</td></tr>\n"
             . "</tfoot>\n"
             . "<tbody></tbody>\n"
             . "</table>\n"
             . " </div>\n"
             . "</td></tr>\n";
        }
        return $html;
    }

    // }}}
    // PrestationCostAddEdit::onAfterHandlePostData() {{{

    /**
     * Sauve les costRange et affecte la prestation au PrestationCost
     *
     * @return void
     * @todo rendre celà générique
     */
    protected function onAfterHandlePostData() {
        $mapper = Mapper::singleton('CostRange');
        $mapper->delete($this->object->getCostRangeCollectionIds());
        if (isset($_POST['CostRange_ID']) && is_array($_POST['CostRange_ID'])) {
            for ($i=0; $i<count($_POST['CostRange_ID']); $i++) {
                $CR = Object::load('CostRange');
                $CR->setId($_POST['CostRange_ID'][$i]);
                $CR->setCost(troncature($_POST['CostRange_Cost'][$i], 3));
                $CR->setCostType($_POST['CostRange_CostType'][$i]);
                $CR->setBeginRange(troncature($_POST['CostRange_BeginRange'][$i]));
                $CR->setEndRange(troncature($_POST['CostRange_EndRange'][$i]));
                $CR->setDepartureZone($_POST['CostRange_DepartureZone'][$i]);
                $CR->setArrivalZone($_POST['CostRange_ArrivalZone'][$i]);
                $CR->setStore($_POST['CostRange_Store'][$i]);
                $CR->setProductType($_POST['CostRange_ProductType'][$i]);
                $CR->setPrestationCost($this->object->getId());
                try{
                    $CR->save();
                } catch(Exception $exc) {
                    Template::errorDialog($exc->getMessage(), $this->url);
                    exit();
                }
            }
        }
        $this->object->setPrestation($this->_prestationID);
    }

    // }}}
    // PrestationCostAddEdit::onBeforeHandlePostdata() {{{

    /**
     * onBeforeHandlePostdata
     *
     * - un PrestationCost ne peut être associé à une Prestation et à un
     * Product/ConcreteProduct/FlyType que si aucun autre PrestationCost associè
     * au même Product/ConcreteProduct/FlyType n'est associé à une Prestation
     * avec un même client (Prestation.PrestationCustomer().Actor).
     *
     * - un PrestationCost ne peut être associé à une Prestation et à un
     * Product/ConcreteProduct/FlyType que si aucun autre PrestationCost associé
     * au même Product/ConcreteProduct/FlyType n'est associé à une Prestation
     * avec une même Operation (Prestation.Operation) ou/et un même MovementType
     * (Prestation.MovementType())
     *
     * @access protected
     * @return void
     */
    protected function onBeforeHandlePostdata() {
        if (!isset($_POST['CostRange_ID'])) {
            Template::infoDialog(sprintf(
                _('You did not defined prices for selected %s.'),
                $this->_linkLabel),
            $this->guessReturnURL());
            exit();
        }
        if(empty($_POST['advmultiselect'.$this->clsname.'_'.$this->_linkObject.'_IDs'])) {
            Template::errorDialog(
                sprintf(_('Please select a(n) %s.'), $this->_linkLabel), 
                $this->url);
            exit;
        }
        $prs = Object::load('Prestation', $this->_prestationID);
        $actorIds = $prs->getActorIds();
        $mvtTypeColIds = $prs->getMovementTypeCollectionIds();
        $opId = $prs->getOperationId();

        $filter = array();
        $filter[] = SearchTools::NewFilterComponent($this->_linkObject,
                    $this->_linkObject.'().Id', 'In',
                    $_POST['advmultiselect'.$this->clsname.'_'.$this->_linkObject.'_IDs'], 1,
                    $this->clsname);
        $filter[] = SearchTools::NewFilterComponent('Prestation',
            'Prestation', 'NotEquals', $this->_prestationID, 1);
        if(count($actorIds)>0) {
            $filter[] = SearchTools::NewFilterComponent('Actor',
                'Prestation.PrestationCustomer().Actor.Id', 'In',
                $actorIds, 1, $this->clsname);
        }

        $filter = SearchTools::filterAssembler($filter);
        $col = Object::loadCollection($this->clsname, $filter, array(),
            array('Id', 'Prestation'));

        $errorMsgs = array();
        $count = $col->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $item = $col->getItem($i);
            $prs = $item->getPrestation();
            if($opId>0) {
                if($prs->getOperationId() == $opId) {
                    $errorMsgs[] = $prs->toString();
                }
            }
            if($mvtTypeColIds) {
                if(count(array_intersect($mvtTypeColIds, 
                    $prs->getMovementTypeCollectionIds()))) {
                    $errorMsgs[] = $prs->toString();
                }
            }
        }
        if(count($errorMsgs)>0) {
            Template::errorDialog(sprintf(
                _('A conflict exists with prices by %s defined for the following services:'),
                $this->_linkLabel) . '<br><ul><li>'.implode('</li><li>', $errorMsgs).'</li></ul>',
                $this->url);
            exit;
        }
    }

    // }}}
    // PrestationCost::renderProduct() {{{

    /**
     * renderConcreteProduct
     *
     * @access protected
     * @return void
     */
    protected function renderProduct() {
        require_once('HTML/QuickForm/advmultiselect.php');

        $list = array();
        $pdtCol = Object::loadCollection('Product', array('Activated'=>1),
            array('BaseReference'=>SORT_ASC, 'Name'=>SORT_ASC));
        $count = $pdtCol->getCount();
        for($i=0 ; $i<$count ; $i++) {
            $pdt = $pdtCol->getItem($i);
            $list[$pdt->getId()] = $pdt->getBaseReference() . ' - ' . $pdt->getName();
        }
        $elt = HTML_QuickForm::createElement(
            'advmultiselect',
            'advmultiselectProductPrestationCost_Product_IDs',
            array(_('Products'), _('Products'), _('assigned')),
            $list,
            array('size'=>8, 'style'=>'width:100%;'));
        $this->form->addElement($elt);
        $this->formDefaults['advmultiselectProductPrestationCost_Product_IDs'] = 
            $this->object->getProductCollectionIds();

    }

    // }}}
    // PrestationCost::renderConcreteProduct() {{{

    /**
     * renderConcreteProduct
     *
     * @access protected
     * @return void
     */
    protected function renderConcreteProduct() {
        require_once('HTML/QuickForm/advmultiselect.php');

        $ccpCol = Object::loadCollection('ConcreteProduct', array(),
            array('Product.BaseReference'=>SORT_ASC));
        $count = $ccpCol->getCount();
        $ccpList = array();
        for($i=0 ; $i<$count ; $i++) {
            $ccp = $ccpCol->getItem($i);
            $pdt = $ccp->getProduct();
            $ccpList[$ccp->getId()] = $pdt->getBaseReference() . ' - ' . $ccp->toString();
        }
        $elt = HTML_QuickForm::createElement(
            'advmultiselect',
            'advmultiselectConcreteProductPrestationCost_ConcreteProduct_IDs',
            array(_('SN/Lot'), _('Available SN/Lot'), _('assigned')),
            $ccpList,
            array('size'=>8, 'style'=>'width:100%;'));
        $this->form->addElement($elt);
        $this->formDefaults['advmultiselectConcreteProductPrestationCost_ConcreteProduct_IDs'] = 
            $this->object->getConcreteProductCollectionIds();
    }

    // }}}
}

?>
