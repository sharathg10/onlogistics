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

class Payment extends _Payment {
    // Constructeur {{{

    /**
     * Payment::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Command foreignkey property + getter/setter {{{

    /**
     * Command foreignkey
     *
     * @access private
     * @var mixed object Command or integer
     */
    private $_Command = false;

    /**
     * Instalment::getCommand
     *
     * @access public
     * @return object Command
     */
    public function getCommand() {
        if (is_int($this->_Command) && $this->_Command > 0) {
            $mapper = Mapper::singleton('Command');
            $this->_Command = $mapper->load(
                array('Id'=>$this->_Command));
        }
        return $this->_Command;
    }

    /**
     * Instalment::getCommandId
     *
     * @access public
     * @return integer
     */
    public function getCommandId() {
        if ($this->_Command instanceof Command) {
            return $this->_Command->getId();
        }
        return (int)$this->_Command;
    }

    /**
     * Instalment::setCommand
     *
     * @access public
     * @param object Command $value
     * @return void
     */
    public function setCommand($value) {
        if (is_numeric($value)) {
            $this->_Command = (int)$value;
        } else {
            $this->_Command = $value;
        }
    }

    // }}}
    /**
     * Recupere les factures associees, via InvoicePayment
     * @access public
     * @return void 
     **/
    function GetInvoiceCollection(){
        $InvoiceCollection = new Collection();  // la collection qui sera retournee
        $InvoiceCollection->acceptDuplicate = false;
        $InvoicePaymentCollection = $this->GetInvoicePaymentCollection();
        
        if (!Tools::isEmptyObject($InvoicePaymentCollection)) {
            for($i = 0; $i < $InvoicePaymentCollection->getCount(); $i++) {
                $InvoicePayment = $InvoicePaymentCollection->GetItem($i);
                $Invoice = $InvoicePayment->GetInvoice();
                $InvoiceCollection->SetItem($Invoice);
                unset($Invoice, $InvoicePayment);
            }    
        }
        return $InvoiceCollection;
    }


}

?>
