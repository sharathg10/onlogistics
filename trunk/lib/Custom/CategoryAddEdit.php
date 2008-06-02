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

define('I_NOT_DELETED_ENTITY',  _('The following category could not be deleted because it is associated either to an offer on sale, a discount or a customer: %s'));
define('I_NOT_DELETED_ENTITIES',  _('The following categories could not be deleted because they are associated either to an offer on sale, a discount or a customer: %s'));

/**
 * CategoryAddEdit
 *
 */
class CategoryAddEdit extends GenericAddEdit {
    /**
     * pour les objets non supprimable
     */
    private $_notDeletedEntity = array();

    // CategoryAddEdit::__construct() {{{

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct($params=array()) {
        $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
        parent::__construct($params);
        $this->jsRequirements[] = 'JS_AjaxTools.php';
        $this->jsRequirements[] = 'js/includes/CategoryAddEdit.js';
    }

    // }}}
    // CategoryAddEdit::onBeforeDisplay() {{{

    public function onBeforeDisplay() {
        // remise ca
        $col = $this->object->getAnnualTurnoverDiscountPercentCollection(
            array(),
            array('Date'=>SORT_DESC)
        );
        if ($col->getCount() > 0) {
            $discount = $col->getItem(0)->getAmount();
        } else {
            $discount = 0;
        }
        $label = _('Annual turnover discount percent');
        $this->form->addElement(
            'text',
            'Discount',
            $label . ':',
            array('class'=>'textfield')
        );
        $msg = E_VALIDATE_FIELD . ' "'.$label.'" ' . E_VALIDATE_IS_DECIMAL;
        $this->form->addRule('Discount', $msg, 'regex', '/\d+[\.,]?\d*/', 'client');
        $this->formDefaults['Discount'] = $discount;
        $this->form->setDefaults($this->formDefaults);
    }
    // }}}
    // CategoryAddEdit::additionalFormContent() {{{

    /**
     *
     * @access public
     * @return string
     */
    public function additionalFormContent() {
        // grid des Actor 
        $grid = new Grid();
        $grid->withNoCheckBox = true;
        $grid->itemPerPage = 300;

        $grid->NewColumn('FieldMapper', _('Name'), array('Macro' => '%Name%', 'Sortable'=>false));
        $catID = $this->object->getId();
        $gridRender = $grid->render('Actor', true,
            array('Category'=>$catID>0?$catID:-1),
            array('Name'=>SORT_ASC), 'GridLite.html');
        $result = "\n<tr><th colspan=\"4\">" .
            _('List of actors associated to category') .
            "</th><tr>\n" .
            "<tr><td colspan=\"4\">\n" . $gridRender . "</td></tr>";
        
        // variables communes aux grid des MiniAmountToOrder et HandingByRange
        $catID = $this->object->getId();
        $currencyColl = Object::loadCollection(
            'Currency',
            array(),
            array('Name' => SORT_ASC),
            array('Name')
        );
        // grid des MiniAmountToOrder 
        $grid = new Grid();
        $grid->withDeleteButton = true;
        $grid->assign('WithDeleteButton', 1);
        $grid->assign('AddButtonName', 'addMiniAmountToOrderItemButton');
        $grid->assign('AddButtonAction', 'addMiniAmountToOrderItem');
        // Ces 2 colonnes autorisent les saisies
        $grid->NewColumn(
            'FieldMapper',
            _('Currency'), 
            array(
                'Macro' => '%Currency.Id%', 
                'Render' => array(
                    'Name' => 'MiniAmountToOrder_Currency',
                    'Type' => 'select',
                    'Coll' => $currencyColl
                )
            )
        );        
        $grid->NewColumn(
            'FieldMapper',
            _('Amount'), 
            array(
                'Macro' => '%Amount%',
                'Render' => array(
                    'Name' => 'MiniAmountToOrder_Amount',
                    'Type' => 'text'
                )
            )
        );
        $gridRender = $grid->render(
            'MiniAmountToOrder',
            false,
            array('Category'=>$catID>0?$catID:-1),
            array('Currency.Name'=>SORT_ASC),
            'GridEditable.html'
        );
        $result .=  "\n<tr><th colspan=\"4\">" .
            _('List of minimum amounts to order associated to category') .
            "</th><tr>\n" .
            "<tr><td colspan=\"4\">\n" .
            $gridRender .
            "</td></tr>\n";
        // grid des HandingByRange 
        $grid = new Grid();
        $grid->withDeleteButton = true;
        $grid->assign('WithDeleteButton', 1);
        $grid->NewColumn(
            'FieldMapper',
            _('Percent'), 
            array(
                'Macro' => '%Percent%',
                'Render' => array(
                    'Name' => 'HandingByRange_Percent',
                    'Type' => 'text'
                )
            )
        );
        $grid->NewColumn(
            'FieldMapper',
            _('Order minimum amount'), 
            array(
                'Macro' => '%Minimum%',
                'Render' => array(
                    'Name' => 'HandingByRange_Minimum',
                    'Type' => 'text'
                )
            )
        );
        $grid->NewColumn(
            'FieldMapper',
            _('Order maximum amount'), 
            array(
                'Macro' => '%Maximum%',
                'Render' => array(
                    'Name' => 'HandingByRange_Maximum',
                    'Type' => 'text'
                )
            )
        );
        $grid->NewColumn(
            'FieldMapper',
            _('Currency'), 
            array(
                'Macro' => '%Currency.Id%', 
                'Render' => array(
                    'Name' => 'HandingByRange_Currency',
                    'Type' => 'select',
                    'Coll' => $currencyColl
                )
            )
        );
        $gridRender = $grid->render(
            'HandingByRange',
            false,
            array('Category'=>$catID>0?$catID:-1),
            array('Minimum'=>SORT_ASC),
            'GridEditable.html'
        );
        $result .=  "\n<tr><th colspan=\"4\">" .
            _('List of handings by range') .
            "</th><tr>\n" .
            "<tr><td colspan=\"4\">\n" .
            $gridRender .
            "</td></tr>\n";
        return $result;
    }

    // }}}
    // CategoryAddEdit::onAfterHandlePostData() {{{

    /**
     * Méthode appelée dans la transaction après ajout ou édition de l'objet.
     * Sert a sauver correctement les MiniAmountToOrder
     *
     * @access protected
     * @return void 
     */
    protected function onAfterHandlePostData() {
        // remise ca
        if (isset($_POST['Discount']) && $_POST['Discount'] > 0) {
            $col = $this->object->getAnnualTurnoverDiscountPercentCollection(
                array(),
                array('Date' => SORT_DESC)
            );
            if ($col->getCount() == 0 || 
                $col->getItem(0)->getAmount() != $_POST['Discount']) {
                // on crée une nouvelle remise
                $d = new AnnualTurnoverDiscountPercent();
                $d->setDate(DateTimeTools::timeStampToMySQLDate(time()));
                $d->setAmount($_POST['Discount']);
                $d->setCategory($this->object->getId());
                $d->save();
            }
        }
        //Database::connection()->debug = true;
        if (isset($_REQUEST['MiniAmountToOrder_Currency'])) {
            $mapper = Mapper::singleton('MiniAmountToOrder');
            // Servira a supprimer les 'anciens' MiniAmountToOrder (mato)
            $matoIds = array();
            $initMatoIds = $this->object->getMiniAmountToOrderCollectionIds();
            foreach ($_REQUEST['MiniAmountToOrder_Currency'] as $key => $curId) {
        	    $miniAmountToOrder = Object::load(
                    'MiniAmountToOrder',
                    $_REQUEST['MiniAmountToOrder_Id'][$key]
                );
                $miniAmountToOrder->setCategory($this->objID);
                $miniAmountToOrder->setCurrency($curId);
                $miniAmountToOrder->setAmount($_REQUEST['MiniAmountToOrder_Amount'][$key]);
                saveInstance($miniAmountToOrder, $this->returnURL);
                $matoIds[] = $miniAmountToOrder->getId();
            }
            // Suppression des MiniAmountToOrder obsoletes
            $mapper->delete(array_diff($initMatoIds, $matoIds));        
        }
        if (isset($_REQUEST['HandingByRange_Currency'])) {
            $mapper = Mapper::singleton('HandingByRange');
            // Servira a supprimer les 'anciens' MiniAmountToOrder (mato)
            $matoIds = array();
            $initMatoIds = $this->object->getHandingByRangeCollectionIds();
            foreach ($_REQUEST['HandingByRange_Currency'] as $key => $curId) {
                $handingByRange = Object::load(
                    'HandingByRange',
                    $_REQUEST['HandingByRange_Id'][$key]
                );
                $handingByRange->setCategory($this->objID);
                $handingByRange->setCurrency($curId);
                $handingByRange->setPercent($_REQUEST['HandingByRange_Percent'][$key]);
                $handingByRange->setMinimum($_REQUEST['HandingByRange_Minimum'][$key]);
                $handingByRange->setMaximum($_REQUEST['HandingByRange_Maximum'][$key]);
                saveInstance($handingByRange, $this->returnURL);
                $matoIds[] = $handingByRange->getId();
            }
            // Suppression des MiniAmountToOrder obsoletes
            $mapper->delete(array_diff($initMatoIds, $matoIds));        
        }
    }

    // }}}
    // CategoryAddEdit::onBeforeDelete() {{{
    
    /**
     * onBeforeDelete 
     * 
     * @access protected
     * @return void
     */
    protected function onBeforeDelete() {
        $objectMapper = Mapper::singleton('Category');
        $objectCol = $objectMapper->loadCollection(
			array('Id' => $this->objID));

        //pour la vérification dans la boucle
        $actorMapper = Mapper::singleton('Actor');

        // les id des catégorie que l'on peut supprimmer
        $okFordelete = array();

        $count = $objectCol->getCount();
        for($i=0 ; $i<$count ; $i++){
            $object = $objectCol->getItem($i);
	        //Vérifie q'une catégorie n'est pas lié à un acteur
	        $actorCol = $actorMapper->loadCollection(array('Category'=>$object->getId()));

	        //La catégorie est-elle liée à au moins une remise produit via
	        //pdtCategory_ToCategory ?
	        $phc = count($object->getProductHandingByCategoryCollectionIds());

	        //La catégorie est-elle liée à au moins une promotion via
	        //prmCategory_ToCategory ?
            $prm = count($object->getPromotionCollectionIds());

            if(Tools::isEmptyObject($actorCol) && $phc==0 && $prm==0 
                && Preferences::get('WSActorCategory')!=$object->getId()){
                //on peut supprimer la catégorie
                $okFordelete[] = $object->getId();
        	} else {
	            //ajout de la catégorie dans le tableau des catégories non suprimées
                $this->_notDeletedEntity[] = $object->getName();
        	}
	    }
        $this->objID = $okFordelete;
    }

    // }}}
    // CategoryAddEdit::onAfterDelete() {{{

    /**
     * onAfterDelete 
     * 
     * @access public
     * @return void
     */
    public function onAfterDelete() {
        // redirige vers un message d'info
        $msg = false;
        if (count($this->_notDeletedEntity) == 1) {
            $msg = sprintf(I_NOT_DELETED_ENTITY, $this->_notDeletedEntity[0]);
        } else if (count($this->_notDeletedEntity) > 1) {
            $str = "<ul><li>" . implode("</li><li>", $this->_notDeletedEntity) . "</li></ul>"; 
            $msg = sprintf(I_NOT_DELETED_ENTITIES, $str);
        }

        if($msg) {
            Template::infoDialog($msg, $this->guessReturnURL());
            exit();
        }
    }
    
    // }}}
}

?>
