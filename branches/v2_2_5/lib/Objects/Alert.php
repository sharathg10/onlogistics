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

class Alert extends _Alert {
    // Constructeur {{{

    /**
     * Alert::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}

    /**
     * Body string property
     *
     * @access private
     * @var string
     */
    private $_Body = '';

    /**
     * Subject string property
     *
     * @access private
     * @var string
     */
    private $_Subject = '';

    /**
     * Variable utilisée pour déterminer si l'alerte a été préparée
     * pour l'envoi, c'est à dire si les variables dynamiques ont été
     * assignées.
     *
     * @var boolean $hasBeenPrepared
     **/

    var $hasBeenPrepared = false;

    /**
     * Prepare l'alerte en vue de l'envoi.
     * En fait il s'agit ici d'assigner les variables dynamiques du corps de
     * l'alerte et/ou de son sujet.
     * Le tableau vars est un tableau multidimensionnel qui comporte
     * comme clef le nom de la variable à assigner et comme valeur sa valeur.
     *
     * @param array $vars
     * @access public
     * @return void
     **/
    function prepare($vars = array()){
        require_once('Objects/Alert.const.php');
        // Les donnees fixes par defaut, (initialement stockees en base)
        $staticDatas = getAlertContent($this->getId());
        $this->_Body = $staticDatas['body'];
        $this->_Subject = $staticDatas['subject'];

        // On parse les chaines et on remplace
        foreach($vars as $var=>$value){
            $this->_Body = str_replace(sprintf('{$%s}', $var), $value, $this->_Body);
            $this->_Subject = str_replace(sprintf('{$%s}', $var), $value, $this->_Subject);
        }
        if ($this->getTemplate() != '') {
            $smarty = new Template();
            foreach($vars as $var=>$value){
                $smarty->assign($var, $value);
            }
            $template = sprintf('Alert/%s', $this->getTemplate());
            if ($smarty->template_exists($template)) {
                $body = $smarty->fetch($template);
                $this->_Body = $body;
            }
        }
        else {
            $this->_Body .= "\n\n" . $this->getBodyAddon();
        }
        $this->hasBeenPrepared = true;
    }

    /**
     * Envoie l'alerte aux utilisateurs paramétrés pour la recevoir.
     * Une collection d'utilisateurs non paramétrés peut aussi être passée en
     * paramètre, il recevrons alors eux aussi l'alerte.
     *
     * @access public
     * @param UserAccountCollection $additionnalUsers
     * @param (?) $isHTML
     * @param array $filter: filtre supplementaire pour
     * $this->getUserAccountCollection(): attention, un array et pas un Filter!
     * @param mixed $attachment non vide si piece jointe
     * Possibilite de joindre un fichier ou une string en memoire; exple:
     *      array('content' => $pdfContent,
     *            'contentType' => 'application/pdf',
     *            'fileName' => 'facture.pdf',
     *            'isFile' => false)
     * @see http://pear.php.net/manual/fr/package.mail.mail-mime.addattachment.php
     * @param boolean $notification true pour accuse de reception
     * @param string $from mail de l'expediteur; si '', ce sera MAIL_SENDER
     *
     * @return mixed boolean true or Exception
     */
    function send($additionnalUsers=false, $isHTML=false, $filter=array(),
    $attachment=array(), $notification=false, $from=''){
        // si l'alerte n'a pas été préparée on retourne une exception
        if (!$this->hasBeenPrepared) {
            return new Exception(
                _('You must use the method Alert::prepare() before sending.')
            );
        }
        // on construit un tableau de destinataires
        $destinators = array();
        $userAccounts = $this->getUserAccountCollection($filter);

        for($i = 0; $i < $userAccounts->getCount(); $i++){
            $uac = $userAccounts->getItem($i);
            if ($uac->getEmail() != '') {
                $addr = sprintf('"%s" <%s>', $uac->getIdentity(), $uac->getEmail());
                $destinators[] = $addr;
            }
        } // for
        // s'il y a des destinataires supplémentaires on les concatène
        if ($additionnalUsers instanceof Collection) {
            for($i = 0; $i < $additionnalUsers->getCount(); $i++){
                $uac = $additionnalUsers->getItem($i);
                if ($uac->getEmail() != '') {
                    $addr = sprintf('"%s" <%s>', $uac->getIdentity(), $uac->getEmail());
                    $destinators[] = $addr;
                }
                unset($uac);
            } // for
        }
        // envoi du mail
        return MailTools::send(
            $destinators,
            $this->_Subject,
            $this->_Body,
            $isHTML,
            $attachment,
            $notification,
            $from);
    }

}

?>