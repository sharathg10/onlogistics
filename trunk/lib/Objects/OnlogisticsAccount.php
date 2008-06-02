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

class OnlogisticsAccount extends _OnlogisticsAccount {
    // Constructeur {{{

    /**
     * OnlogisticsAccount::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // getDatabaseName() {{{

    /**
     * Retourne le chemin complet du backup
     *
     * @access public
     * @return string
     */
    public function getDatabaseName() {
        $consts = self::getEnvironmentConstArray();
        return $consts[$this->getEnvironment()] . '_ol_' . $this->getName();
    }

    // }}}
    // getDSN() {{{

    /**
     * Retourne un dsn à partir des données de Account. 
     *
     * @access public
     * @param string $driver (default mysqlt)
     * @return string
     */
    public function getDSN($driver = 'mysqlt') {
        $master_slave_string = '';
        $dbname = $this->getDatabaseName();
        if (count($this->getChildAccountCollectionIds()) > 0) {
            // on a un master
            $master_slave_string = '/0';
        } else if ($this->getParentAccount() instanceof OnlogisticsAccount) {
            // on a un slave
            $ids = $this->getParentAccount()->getChildAccountCollectionIds();
            $dbname = $this->getParentAccount()->getDatabaseName();
            foreach ($ids as $i=>$id) {
                if ($this->getId() == $id) {
                    $master_slave_string .= '/' . ($i+1);
                }
            }
        }
        return sprintf(
            '%s://%s:%s@localhost/%s%s', $driver, DB_LOGIN, DB_PASSWORD,
            $dbname, $master_slave_string
        );
    }

    // }}}
    // canBeDeleted() {{{

    /**
     * Object::canBeDeleted()
     * Retourne true si l'objet peut être détruit en base de donnees.
     *
     * @access public
     * @return boolean
     */
    public function canBeDeleted() {
        parent::canBeDeleted();
        if (count($this->getChildAccountCollectionIds()) > 0) {
            throw new Exception(sprintf(
                _('Account "%s" cannot be deleted because it is parent of one or more accounts. Please delete these accounts first.'),
                $this->getName()
            ));
        }
        return true;
    }

    // }}}
    // canBeSaved() {{{

    /**
     * Object::canBeSaved()
     * Retourne true si l'objet peut être sauvé en base de donnees.
     *
     * @access public
     * @return boolean
     */
    public function canBeSaved() {
        parent::canBeSaved();
        $existing = Object::load('OnlogisticsAccount',
            array('Name'=>$this->getName(), 'Environment'=>$this->getEnvironment()));
        if ($existing instanceof OnlogisticsAccount && $existing->getId() != $this->getId()) {
            $envs = $this->getEnvironmentConstArray();
            throw new Exception(sprintf(
                _('An account with the name "%s" and environment "%s" already exists, please delete it first or provide an other name for this account.'),
                $this->getName(), $envs[$this->getEnvironment()]
            ));
        }
        return true;
    }

    // }}}
    // delete() {{{

    /**
     * Methode surchargée pour générer le descripteur
     *
     * @access public
     * @return boolean
     */
    public function delete() {
        parent::delete();
        require_once 'classes/DatabaseManager.php';
        $backup = new Backup();
        $backup->setName($this->getName());
        $backup->setEnvironment($this->getEnvironment());
        $backup->setDate(DateTimeTools::timeStampToMySQLDate(time()));
        DatabaseManager::dropDB(
            $this->getDatabaseName(),
            $backup->getBackupFilePath()
        );
        $backup->save();
    }

    // }}}
    // save() {{{

    /**
     * Methode surchargée pour générer le descripteur
     *
     * @access public
     * @return boolean
     */
    public function save($backup=false, $otherdb=false) {
        if (!$this->getCreationDate()) {
            $now = DateTimeTools::timeStampToMySQLDate(time());
            $this->setCreationDate($now);
            $this->setLastAccessDate($now);
        }
        $hasBeenInitialized = $this->hasBeenInitialized;
        parent::save();
        // on crée le descripteur si il s'agit d'un ajout et si ce n'est pas
        // un compte esclave
        if (!$hasBeenInitialized && !$this->getParentAccountId()) {
            require_once 'classes/DatabaseManager.php';
            if ($otherdb) {
                DatabaseManager::cloneDB($this->getDatabaseName(), $otherdb);
            } else {
                DatabaseManager::createDB($this->getDatabaseName(), $backup);
            }
        }
    }

    // }}}

}

?>