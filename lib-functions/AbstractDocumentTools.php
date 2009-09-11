<? 
// generateClassId() {{{

/**
* Génére un ID pour l'objet en cours.
*
* @access public
* @return integer
*/
function generateClassId($table) {
    // on vérifie l'existance d'un id dans la table de hash des ids
    // on utilise FOR UPDATE pour locker la table en cas d'accès
    // concurrents.
    // XXX il faudra voir à utiliser rowLock de ADODB
    // Cela ne semble pas marcher, surement à cause du select qui est fait
    // après.
    //Database::connection()->rowLock('IdHashTable', '_Table=\''
    //    . $this->_tbname . '\'');
    $sql = 'SELECT _Id FROM IdHashTable WHERE _Table=\'' . $table
        . '\' FOR UPDATE';
    $exists = Database::connection()->execute($sql);
    if (false == $exists || $exists->EOF) {
        // on insère le champs dans la table de hash
        $sql = sprintf(
                'INSERT INTO IdHashTable (_Table, _Id) ' .
                'SELECT \'%s\', MAX(_ClassId) FROM AbstractDocument WHERE _ClassName=\'%s\'',
                $table,$table
        );
        $result = Database::connection()->execute($sql);
        if (false == $result) {
            if (DEV_VERSION) echo $sql . '<br />';
            trigger_error(Database::connection()->errorMsg(), E_USER_ERROR);
        }
        return generateClassId($table);
    } else {
        // l'entrée existe on l'update après l'avoir incrémentée
        $id = (int)$exists->fields[0] + 1;
        $sql = 'UPDATE IdHashTable SET _Id=' . $id . ' WHERE _Table=\'' .
            $table . '\'';
    }
    $result = Database::connection()->execute($sql);
    if (false == $result) {
        if (DEV_VERSION) echo $sql . '<br />';
        trigger_error(Database::connection()->errorMsg(), E_USER_ERROR);
    }
    return $id;
}

// }}}
?>
