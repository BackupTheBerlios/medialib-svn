<?php
require_once('../global/customize.php');
require_once('./HgkMediaLib_DataBase.php');

class HGKMediaLib_ManageDataBase extends HGKMediaLib_DataBase
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function removeSessionTables()
    {
        //get existing sessions
        $session_path = SESSION_SAVE_PATH;
        $sessions = scandir($session_path);
        /*echo '<pre>';
        print_r($sessions);
        echo '</pre>';*/
        $entities = array('work','instance','set','person');
        foreach ($sessions AS $session) {
            if (substr($session,0,5) == 'sess_') {
                $id = substr($session,5);
                foreach ($entities AS $entity) {
                    $table_array[$entity][] = "'".$entity."_".substr($session,5)."'"; 
                }
            }
        }
        /*echo '<pre>';
        print_r($table_array);
        echo '</pre>';*/

        //remove tables from old sessions
        $this->deleteTmpSessionTables($table_array);
    }
}

$test = new HGKMediaLib_ManageDataBase();
$test->removeSessionTables();
?>
