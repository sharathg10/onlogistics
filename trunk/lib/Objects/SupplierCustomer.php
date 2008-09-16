<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IMPORTANT: This is a generated file, please do not edit.
 *
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

/**
 * SupplierCustomer class
 *
 * Class containing addon methods.
 */
class SupplierCustomer extends _SupplierCustomer {
    // Constructeur {{{

    /**
     * SupplierCustomer::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // SupplierCustomer::save() {{{

    /**
     * Surcharge save() pour la gestion de CustomerProductCommandBehaviour
     * en fonction de la Preference associeen et MAJ des cmdes client si necessaire
     * Remarque: pour l'instant, on ne cloture pas les cmdes qui devraient l'etre
     *
     * @access public
     * @return void
     */
    function save() {
        require_once('Objects/Preferences.const.php');
        $pdtCustomerCmdBehaviour = Preferences::get('CustomerProductCommandBehaviour');
        // Sert notamment lors d'une creation de SupplierCustomer
        if ($pdtCustomerCmdBehaviour != MANAGED_BY_SUPPLIERCUSTOMER) {
            $this->setCustomerProductCommandBehaviour($pdtCustomerCmdBehaviour);
        }
        // Si l'etat est NO_INVOICE_CLOSED_AFTER_DELIVERED, on decloture
        // des commandes dont le state le necessite
        if ($this->getCustomerProductCommandBehaviour() == NO_INVOICE_CLOSED_AFTER_DELIVERED) {
            $filter = array('SupplierCustomer' => $this->getId(),
                          'Type' => Command::TYPE_CUSTOMER,
                          'State' => array(Command::PREP_COMPLETE, Command::LIV_PARTIELLE),
                          'Closed' => 1);
        }
        // idem si facturable: aucune commande ne doit rester cloturee
        elseif ($this->getCustomerProductCommandBehaviour() == WITH_INVOICE) {
            $filter = array('SupplierCustomer' => $this->getId(),
                          'Type' => Command::TYPE_CUSTOMER,
                          'Closed' => 1);
        }
        if (isset($filter)) {
            $cmdColl = Object::loadCollection('ProductCommand', $filter);
            if (!Tools::isEmptyObject($cmdColl)) {
                $count = $cmdColl->getCount();
                for($i = 0; $i < $count; $i++){
                    $cmd = $cmdColl->getItem($i);
                    $cmd->setClosed(0);
                    $cmd->save();
                }
            }
        }
        parent::save();
    }

    // }}}
    // SupplierCustomer::getAnnualTurnoverDiscountPercent() {{{

    /**
     * Methode qui retourne la remise en pourcentage du chiffre d'affaire du 
     * CA. Si aucun pourcentage n'est trouvé la methode retourne false.
     * Si une date est passée, la méthode essaie de retourner le pourcentage 
     * qui était en vigueur à cette date.
     *
     * @access public
     * @param  string $date date au format mysql optionnel default null
     * @return mixed float ou false.
     */
    public function getAnnualTurnoverDiscountPercent($date=null) {
        $customer = $this->getCustomer();
        if (!($customer instanceof Actor)) {
            return false;
        }
        $category = $customer->getCategory();
        if (!($category instanceof Category)) {
            return false;
        }
        $col = $category->getAnnualTurnoverDiscountPercentCollection(
            array(),
            array('Date' => SORT_DESC)    
        );
        $count = $col->getCount();
        if ($count == 0) {
            // pas la peine d'aller plus loin
            return false;
        }
        if ($date === null || $count == 1) {
            // on retourne le seul item de la collection ou bien le plus récent 
            // si pas de date passée
            return $col->getItem(0)->getAmount();
        }
        // formatte la date en timestamp
        $date = DateTimeTools::mysqlDateToTimestamp($date);
        // on retrie la collection du plus ancien au plus récent cette fois
        $col->sort('Date', SORT_ASC);
        foreach ($col as $item) {
            if ($item->getDate('timestamp') >= $date) {
                return $item->getAmount();
            }
        }
        // aucun de trouvé on retourne le plus récent
        return $item->getAmount();
    }

    // }}}
    // SupplierCustomer::getAnnualTurnoverDiscountTotal() {{{

    /**
     * Methode qui retourne le montant remise total CA pour le couple
     * supplier-customer.
     *
     * @access public
     * @return mixed float or int (0)
     */
    public function getAnnualTurnoverDiscountTotal() {
        // le montant à retourner
        $amount = 0;
        // la collection des remises/an
        $col = $this->getAnnualTurnoverDiscountCollection();
        $count = $col->getCount();
        // chaque remise est reportable d'année en année
        foreach ($col as $item) {
            $amount += $item->getAmount();
        }
        return $amount;
    }

    // }}}
    // SupplierCustomer::updateAnnualTurnoverDiscount() {{{

    /**
     * Met à jour la remise total CA pour le couple supplier customer en 
     * ajoutant le montant passé en paramètre. IMPORTANT: ce montant peut être 
     * négatif bien sûr ! dans ce cas il est retranché.
     * si une date est passée, c'est la remise correspondant à cette date qui 
     * sera modifiée.
     *
     * @access public
     * @param  string $date date au format mysql optionnel default null
     * @return boolean true si la remise a été mise à jour et false sinon
     */
    public function updateAnnualTurnoverDiscount($amount, $date=null) {
        if ($date === null) {
            // cette année
            $year = date('Y', time());
        } else {
            $year = (int)substr($date, 0, 4);
        }
        $discount = Object::load(
            'AnnualTurnoverDiscount', 
            array('SupplierCustomer' => $this->getId(), 'Year' => $year)
        );
        if (!($discount instanceof AnnualTurnoverDiscount)) {
            if ($amount < 0) {
                // si on doit soustraire une somme, cas de suppression de
                // commande ou d'emission d'avoir, et qu'il n'y a pas de
                // remise paramétrée, on ne fait rien
                return false;
            } else {
                // sinon, cas de commande ou de suppression d'avoir, on crée
                // la remise
                $discount = new AnnualTurnoverDiscount();
                $discount->setYear($year);
                $discount->setSupplierCustomer($this->getId());
            }
        }
        $amount = $discount->getAmount() + $amount;
        // XXX supposition à confirmer: le montant de la remise ne peut être 
        // négatif, ça semble logique
        if ($amount < 0) {
            $amount = 0;
        }
        $discount->setAmount($amount);
        $discount->save();
        return true;
    }

    // }}}

}

?>
