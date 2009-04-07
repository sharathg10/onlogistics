<?
error_reporting(E_ALL);

define('SKIP_CONNECTION', true);
define('MAPPER_CACHE_DISABLED', true);

require_once('config.inc.php');
require_once('lib/SQLRequest.php');
$only_dsn = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : false;
if($only_dsn == FALSE ) die("Vous devez specifier un dsn valide\n") ;

$dsn = constant($only_dsn);

echo "\n\n".$only_dsn."\n\n" ;
Database::connection($dsn);

$SelectSQL = "SELECT _Id, _Name, _Code FROM Actor" ;
$rs = executeSQL($SelectSQL);
$i = 0 ;
$error = "";
while (!$rs->EOF){
    $name = ereg_replace("[^[:alnum:]]","",$rs->fields['_Name']);
    $name = substr(strtoupper($name),0,5);
    
    $CheckSQL= "SELECT _Id FROM Actor WHERE _Code ='".$name."' AND _Id != ".$rs->fields['_Id']." LIMIT 1" ;
    $rsCheck = executeSQL($CheckSQL);

    if($rsCheck->RecordCount() == 0 ) {
            $tab = "" ;
            $UpdateSQL = "UPDATE Actor SET _Code ='".$name."' WHERE _Id=".$rs->fields['_Id']." LIMIT 1" ;
    } else {
            $j = 1 ;
            $tab = ">>";
            $name = substr($name,0,4).$j;
            $CheckSQL= "SELECT _Id FROM Actor WHERE _Code ='".$name."' AND _Id != ".$rs->fields['_Id']." LIMIT 1" ;
            $rsCheck = executeSQL($CheckSQL);
            while($rsCheck->RecordCount() > 0 ) {
                $tab .= ">>";
                $j++ ;
                $name = substr($name,0,4).$j;
                $CheckSQL= "SELECT _Id FROM Actor WHERE _Code ='".$name."' LIMIT 1" ;
                $rsCheck = executeSQL($CheckSQL);
            }
            $UpdateSQL ="UPDATE Actor SET _Code ='".$name."' WHERE _Id=".$rs->fields['_Id']." LIMIT 1" ;
    }

    $rsUpdate = executeSQL($UpdateSQL);
    if($rsUpdate==FALSE) $error .= "Erreur sur Update : ".$UpdateSQL." \n";
    echo $rs->fields['_Id']." -> ".$name."\n" ;
    $rs->moveNext();
    $i++;
}

$AlterSQL = "ALTER TABLE Actor ADD UNIQUE (_Code)";
$rsAlter = executeSQL($AlterSQL);
if($rsAlter==FALSE) $error .= "Erreur sur Alter : ".$AlterSQL." \n";

if ( $error != "" ) {
    echo $error ;
}

echo "\n\nDone.\n\n";

?>
