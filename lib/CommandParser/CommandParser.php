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

define('ERROR_DESTINATOR', _('Order addressee was not found in the database.'));
define('ERROR_DESTINATOR_SITE', _('Order addressee site was not found in the database.'));
define('ERROR_EXPEDITOR_SITE', _('Shipper site was not found in the database.'));
define('ERROR_INCOTERM', _('Incoterm "FCA" was not found in the database'));

// }}}

/**
 * Classe de gestion du parsing d'une commande onlogistics via fichier xml.
 * Possibilité de créer un classe mere si d'autres formats que le xml doivent
 * etre traités. Le but etant d'avoir les mêmes interfaces...
 *
 * Usage:
 * <code>
 * require_once('lib/CommandParser/CommandParser.php');
 *
 * $xml = file_get_contents('test.xml');
 *
 * $CommandParser = new CommandParser();
 * try {
 *     $check = $CommandParser->checkInputData($xml);
 *     $CommandParser->buildCommandData();
 * } catch (Exception $e) {
 *     echo $e->getMessage();
 *     exit;
 * }
 * require_once('CommandManager.php');
 * $manager = new CommandManager(array(
 *     'CommandType'        => 'ProductCommand',
 *     'ProductCommandType' => Command::TYPE_CUSTOMER,
 *     'UseTransaction'     => true
 * ));
 * // checks et formatage des floats
 * $CommandParser->commandData = CommandManager::checkParams($CommandParser->commandData);
 * $result = $manager->createCommand($CommandParser->commandData);
 *
 * foreach($CommandParser->commandItemData as $cmdItemData) {
 *     $manager->addCommandItem($cmdItemData);
 * }
 * </code>
 *
 * @package    onlogistics
 * @subpackage lib
 */
class CommandParser{
    // propriétés {{{

    /**
     * Le contenu xml a parser
     *
     * @var    object $_parser instance du parser xml correspondant au type de xml
     * @access private
     */
    private $_parser = false;

    /**
     * Le type de fichier xml, suivant comment a ete genere ce xml.
     * definit la methode de parsing
     *
     * @var    string $_driver le type de fichier xml
     * @access private
     */
    private $_driver = '';

    /**
     * L'objet Command
     *
     * @var    object SimpleXMLElement $xmlCommand
     * @access public
     */
    public $xmlCommand = false;

    /**
     *
     * @var    array Les donnees de la commande
     * @access private
     */
    public $commandData = array();
    /**
     *
     * @var    array les donnees des commandItems
     * @access private
     */
    public $commandItemData = array();

    // CommandParser::__construct() {{{
    /**
     * Constructor
     *
     * @param array $commandData
     * @access public
     */
    public function __construct($commandData=array()) {
        $this->commandData = $commandData;
    }

    // }}}
    // CommandParser::checkInputData() {{{

    /**
     * Verifie la validite vis a vis de la dtd, detecte le type de xml.
     * Remarque: la validation / dtd distante est incompatible avec conf serveur:
     * chargement des url externes interdit => dtds stockees en local (gain perfs)
     *
     * @access public
     * @param string $xml
     * @return mixed true ou une exception
     **/
    public function checkInputData($xml)
    {
        // check validite / dtd....
        try {
            // Pour cxml:
            // http://xml.cxml.org/schemas/cXML/[xxxx]/cXML.dtd (xxxx=1.2.017, ...)
            //$xml = file_get_contents('test.xml');
            // On recupere le type de xml et l'url exacte de la dtd
            $doctypeMatch = '/<\!DOCTYPE ([a-z]+) .+ "(http:\/\/.+\/.+\.dtd)">/i';
            $res = preg_match($doctypeMatch, $xml, $patterns);

            $this->_driver = ucfirst(strtolower($patterns[1]));  // le DOCTYPE en fait
            $dtd = $patterns[2];
            // On remplace par le chemin vers une dtd locale (droits + perfs)
            $xml = preg_replace(
                    '/ "http:\/\/.+\/(.+\.dtd)">/i',
                    ' "lib/CommandParser/Dtd/$1">',
                    $xml);
            //$xml = preg_replace('/SYSTEM "http:\/\/.+\/(.+\.dtd)"/i', 'SYSTEM "dtd/$1"', $xml);
            // Chargement de l'objet SimpleXMLElement si validation dtd ok
            $simpleXmlElt = simplexml_load_string(
                    $xml, 'SimpleXMLElement', LIBXML_DTDVALID);
            if (!$simpleXmlElt) {
                throw new Exception('Invalid document xml.');
            }
            $this->xmlCommand = $simpleXmlElt;
            $parserClassName = $this->_driver . 'Parser';
            $driverFile = 'CommandParser/Drivers/' . $parserClassName . '.php';
            if (!file_exists(PROJECT_ROOT . '/' . LIB_DIR . '/' . $driverFile)) {
                throw new Exception('XML format not supported.');
            }
            include_once $driverFile;
            $this->_parser = new $parserClassName($this);
        } catch (Exception $e) {
            throw $e;
        }
        return true;
    }

    // }}}
    // CommandParser::buildCommandData() {{{

    /**
     * Construit le tableau des donnees de la commande a passer au commanManager
     *
     * @access public
     * @return mixed true ou une exception
     **/
    public function buildCommandData() {
        if (method_exists($this->_parser, 'buildNeededData')) {
            $this->_parser->buildNeededData();
        }
        $commandData = $this->commandData;
        $commandSchema = $this->_parser->commandSchema;
        $baseXmlElement = $this->xmlCommand->xpath($this->_parser->xmlCommandPath);
        // On prend le 1er trouve pour la cmde
        $baseXmlElement = $baseXmlElement[0];
        foreach($commandSchema as $attr=>$model) {
            // Les donnees de $commandData ecrasent celles dans le xml si besoin
            if (isset($commandData[$attr])) {
                continue;
            }
            $value = $this->getElementValue($attr, $baseXmlElement);
            if ($value !== false) {
                $commandData[$attr] = $value;
            }
        }
        // TODO: MUTUALISER avec api_site, dans CommandManager ??
        // check que le destinator existe
        $actor = Object::load('Customer', $commandData['Destinator']);
        if(!($actor instanceof Customer)) {
            throw new Exception(ERROR_DESTINATOR);
        }
        // check pour le site destinataire
        if(!isset($commandData['DestinatorSite'])) {
            $destSite = $actor->getMainSite();
            if(!($destSite instanceof Site)) {
                throw new Exception(ERROR_DESTINATOR_SITE);
            }
            $commandData['DestinatorSite'] = $destSite->getId();
        } else {
            $destSite = Object::load('Site', $commandData['DestinatorSite']);
            if(!($destSite instanceof Site) || $destSite->getOwnerId() != $commandData['Destinator']) {
                throw new Exception( ERROR_DESTINATOR_SITE );
            }
            $commandData['DestinatorSite'] = $destSite->getId();
        }
        // check pour l'expediteur et le site expediteur
        $exp = $expSite = false;
        if(isset($commandData['Expeditor'])) {
            $exp = Object::load('Actor', $commandData['Expeditor']);
        }
        if(!($exp instanceof Actor)) {
            $exp = Object::load('Actor', array('DatabaseOwner'=>1));
        }
        if(isset($commandData['ExpeditorSite'])) {
            $expSite = Object::load('Site', $commandData['ExpeditorSite']);
        } else {
            $expSite = $exp->getMainSite();
        }
        if(!($expSite instanceof Site) || $expSite->getOwnerId() != $exp->getId()) {
            throw new Exception(ERROR_EXPEDITOR_SITE);
        }
        $commandData['Expeditor'] = $exp->getId();
        $commandData['ExpeditorSite'] = $expSite->getId();

        // valeurs par défaut
        $commandData['Customer'] = $commandData['Destinator'];
        $incoterm = Object::load('Incoterm', array('Code'=>'FCA'));
        if(!($incoterm instanceof Incoterm)) {
            throw new Exception(_('Incoterm "FCA" was not found in the database'));
        }
        $commandData['Incoterm'] = $incoterm->getId();
// /TODO: MUTUALISER

        // Pareil pour chaque commandItem
        $commandItemData = $this->commandItemData;
        $commandItemSchema = $this->_parser->commandItemSchema;
        $baseXmlElementArray = $this->xmlCommand->xpath($this->_parser->xmlCommandItemPath);
        $count = count($baseXmlElementArray);
        for($i = 0; $i < $count; $i++){
            foreach($commandItemSchema as $attr=>$model) {
                // Les donnees de $commandItemData ecrasent celles dans le xml si besoin
                if (isset($commandItemData[$i][$attr])) {
                    continue;
                }
                $value = $this->getElementValue(
                        $attr, $baseXmlElementArray[$i], 'CommandItem');
                if ($value !== false) {
                    $commandItemData[$i][$attr] = $value;
                }
            }
        }

        $this->commandData = $commandData;
        $this->commandItemData = $commandItemData;
        return true;
    }

    // }}}
    // CommandParser::getElementValue() {{{

    /**
     * Recupere dans le xml la valeur d'un attribut
     *
     * @access public
     * @param string $name
     * @param object SimpleXmlElement $baseXmlElement
     * @param string $type: 'Command' ou 'CommandItem'
* TODO: supprimer le param 2 ou 3
     * @return mixed string ou false
     **/
    function getElementValue($name, $baseXmlElement, $type='Command') {
        $schema = ($type=='Command')?
            $this->_parser->commandSchema:$this->_parser->commandItemSchema;
        if (!isset($schema[$name])) {
            return false;
        }
/*        $basePath = ($type=='Command')?
            $this->_parser->xmlCommandPath:$this->_parser->xmlCommandItemPath;*/
        $data = $schema[$name];
        $search = array();
        // Il faut faire une "recherche" dans ce cas
        // on ne cherchera pas forcement l'info dans $search[0]
        if (isset($data['filter'])) {
            /* TODO: gerer l'attribut 'filter' */
        }
        else {
            $search = empty($data['path'])?
                $baseXmlElement:$baseXmlElement->xpath($data['path']);
        }
        if (empty($search)) {
            return false;
        }
        // Pour la Commande, on prend le 1er trouve ($search[0])
        // Selon si contenu direct de la balise ou celui d'un de ses attributs
        return (isset($data['attr']))?
                (string)$search[0][$data['attr']]:(string)$search[0];

    }

    // }}}

}



/////////// #### Class DRIVER #### ////////////

?>
