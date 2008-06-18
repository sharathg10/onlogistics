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

class UploadedDocumentManager extends Upload {
    // UploadedDocumentManager::check() {{{

    /**
     * Batterie de vérifications des fichiers uploadés.
     *
     * Surchargée pour permettre un check plus strict.
     *
     * @access public
     * @return boolean
     * @throws Exception
     */
    public function check()
    {
        $this->maxsize = 6291456; // 6 mo
        if ($this->_checkdone) {
            return true;
        }
        // checke si le tableau a bien été initialisé
        if (!$this->infos) {
            throw new Exception(E_UPLOAD_FORM_NOT_POSTED);
        }
        // gestion des erreurs de base
        if ($this->infos['error'] > UPLOAD_ERR_OK) {
            // erreur, taille fichier dépassée
            switch ($this->infos['error']) {
                case UPLOAD_ERR_NO_FILE:
                    throw new Exception(E_UPLOAD_NO_FILE);
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception(sprintf(E_UPLOAD_MAX_SIZE, 
                        $this->infos['name'], $this->maxsize));
                case UPLOAD_ERR_PARTIAL:
                    throw new Exception(sprintf(E_UPLOAD_PARTIAL, 
                        $this->infos['name']));
                case UPLOAD_ERR_NO_TMP_DIR:
                    trigger_error(E_UPLOAD_TMP_DIR_MISSING, E_USER_ERROR);
                case UPLOAD_ERR_CANT_WRITE:
                    $tmpdir = ini_get('upload_tmp_dir');
                    trigger_error(sprintf(E_UPLOAD_DIR_WRITE, $tmpdir),
                        E_USER_ERROR);
                default:
                    // on ne devrait pas passer ici
                    trigger_error('Unknown error !');
            }
        }
        // check du type de fichier
        $col = Object::loadCollection(
            'MimeType',
            array('Id' => Preferences::get('UploadAllowedMimeTypes', 0))
        );
        $allowedMimeTypes = array_values($col->toArray('getContentType'));
		if (!in_array($this->infos['mime_type'], $allowedMimeTypes)) {
            throw new Exception(sprintf(
                E_UPLOAD_UNSUPPORTED_EXTENSION,
                $this->infos['name'],
                implode(', ', $allowedMimeTypes)
            ));
		}
        // check de la taille du fichier
        if ($this->infos['size'] > $this->maxsize) {
            throw new Exception(sprintf(E_UPLOAD_MAX_SIZE, 
                $this->infos['name'], $this->maxsize));
        }
        $this->_checkdone = true;
    }

    // }}}
    // UploadedDocumentManager::store() {{{

    /**
     * Stocke le document correspondant à l'objet passé en paramètre
     *
     * @param object instance d'UploadedDocument
     *
     * @return boolean
     * @access public
     * @throws Exception
     */
    public function store($doc)
    {
        $path = self::getUploadDir();
        if (!is_dir($path)) {
            mkdir($path, 0700, true);
        }
        $this->check();
        $mimetype = Object::load(
            'MimeType',
            array('ContentType' => $this->infos['mime_type'])
        );
        if (!($mimetype instanceof MimeType)) {
            // on devrait pas être ici
            trigger_error('Unknown mime type ' . $this->infos['mime_type'],
                E_USER_ERROR);
        }
        $doc->setMimeType($mimetype);
        if (parent::store($path, true, $doc->getFileName())) {
            $doc->save();
            return true;
        }
        return false;
    }

    // }}}
    // UploadedDocumentManager::delete() {{{

    /**
     * Supprime le document lié à l'objet UploadedDocument passé en paramètre.
     *
     * @param object instance d'UploadedDocument
     *
     * @return boolean
     * @access public
     * @throws Exception
     */
    public function delete($doc)
    {
        $file = self::getUploadDir() . $doc->getFileName();
        return @unlink($file);
    }

    // }}}
    // UploadedDocumentManager::download() {{{

    /**
     * Flushe les headers nécéssaires pour le download du document passé en 
     * paramètre.
     *
     * @param object instance d'UploadedDocument
     *
     * @return void
     * @access public
     * @static
     */
    public static function download($doc)
    {
        $file  = self::getUploadDir() . $doc->getFileName();
        $fname = sprintf(
            '%s.%s',
            preg_replace('/[\W ]/', '_', $doc->getName()),
            Tools::getValueFromMacro($doc, '%MimeType.Extension%')
        );
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: public');
        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename=' . $fname);
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . (string)filesize($file));
        @readfile($file);
        exit(0);
    }

    // }}}
    // UploadedDocumentManager::getUploadDir() {{{

    /**
     * Retourne le chemin complet vers le rép. d'upload.
     *
     * @return string
     * @access public
     * @static
     */
    public static function getUploadDir()
    {
        return GED_UPLOAD_DIR . DIRECTORY_SEPARATOR . ENVIRONMENT
           . DIRECTORY_SEPARATOR . Auth::getRealm() . DIRECTORY_SEPARATOR;
    }

    // }}}
}

?>
