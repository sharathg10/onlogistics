<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * $Source: /home/cvs/codegen/codegentemplates.py,v $
 *
 * Ceci est un fichier généré, NE PAS EDITER, éditez plutôt le fichier
 * .addon correspondant qui doit se trouver dans le répertoire Addons.
 *
 * @copyright 2002-2006 ATEOR - All rights reserved
 */

/**
 * Document class
 *
 * Classe contenant des méthodes additionnelles
 */
class Document extends _Document {
    // Constructeur {{{

    /**
     * Document::__construct()
     * Constructeur
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    // }}}
    // Document::getData() {{{

    /**
     * Override getData() to handle the new way of storing PDF documents.
     *
     * Now documents are stored in the file system.
     * The "defined" trick allow to keep the old behaviour if needed, to switch 
     * to the old behaviour just remove the ARCHIVED_DOCUMENTS_DIR constant in 
     * the project.conf file.
     * 
     * @access public
     * @return string 
     */
    public function getData() {
        // uncomment the @ operator if something goes wrong...
        return @file_get_contents($this->getDocumentFullPath());
    }

    // }}}
    // Document::setData() {{{

    /**
     * Override setData() to handle the new way of storing PDF documents.
     *
     * Now documents are stored in the file system.
     * The "defined" trick allow to keep the old behaviour if needed, to switch 
     * to the old behaviour just remove the ARCHIVED_DOCUMENTS_DIR constant in 
     * the project.conf file.
     * 
     * @access public
     * @param  string $data The document data.
     * @return boolean 
     */
    public function setData($data) {
        // uncomment the @ operator if something goes wrong...
        return @file_put_contents($this->getDocumentFullPath(), $data);
    }

    // }}}
    // Document::getDocumentFullPath() {{{

    /**
     * Returns the document absolute path.
     * The path is the concatenation of the ARCHIVED_DOCUMENTS_DIR base 
     * directory, the environment (current, prod...) and the account realm (the 
     * one used to login to the app, i.e. login@realm).
     *
     * This method is public to allow it's use in other scripts.
     * 
     * @access public
     * @return string 
     */
    public function getDocumentFullPath() {
        if (!defined('ARCHIVED_DOCUMENTS_DIR')) {
            return false;
        }
        $basedir = ARCHIVED_DOCUMENTS_DIR . DIRECTORY_SEPARATOR . ENVIRONMENT
                 . DIRECTORY_SEPARATOR . Auth::getRealm() . DIRECTORY_SEPARATOR;
        if (!is_dir($basedir)) {
            // create the dir hierarchy if it doesn't exists
            // the "true" argument is equivalent to "mkdir -p"
            mkdir($basedir, 0775, true);
        }
        $typemap = array(
            Document::TYPE_TXT => 'txt', 
            Document::TYPE_PDF => 'pdf', 
            Document::TYPE_CSV => 'csv'
        );
        $type = $this->getType();
        return $basedir . $this->getId() . 
            (isset($typemap[$type]) ? '.' . $typemap[$type] : '');
    }

    // }}}
}

?>
