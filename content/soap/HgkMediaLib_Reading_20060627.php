<?php
/**
 * Reading classes for hgkmedialib.
 *
 * This file handles the data base reading for the hgkmedialib front- and backend.
 * It contains the soap handler class HgkMediaLib_Reading
 * and the class HGKMediaLib_Struct_Entity for creating a entity struct
 *
 * HgkMediaLib_Struct_Entity creates a object that contains the entity data requested by frontend.
 * HgkMediaLib_Reading handles the search of entities.
 *
 * @package HGKMediaLib
 * @author Franz-Ferdinand Lehnert <franz.lehnert@winet.ch>
 */

require_once('../global/customize.php');
require_once(ROOT_PATH.'/db/HgkMediaLib_DataBase.php');
require_once(ROOT_PATH.'/soap/inc/HGKMediaLib_Struct_Information.php');
require_once(ROOT_PATH.'/soap/inc/HGKMediaLib_Struct_EntityNode.php');
require_once(ROOT_PATH.'/soap/inc/HGKMediaLib_Struct_Media.php');
require_once(ROOT_PATH.'/soap/inc/HGKMediaLib_Struct_Data.php');
require_once(ROOT_PATH.'/soap/inc/HGKMediaLib_Struct_InformationBlock.php');
require_once(ROOT_PATH.'/soap/inc/HGKMediaLib_Struct_Files.php');

/**
 * reading class for media lib
 * extends the cetral data base class
 *
 * this class will be used as the soap handling class for the mediagonal frontend
 *
 * @package HGKMediaLib
 * @author Franz-Ferdinand Lehnert <franz.lehnert@winet.ch>
 * @version 0.1
 */
class HGKMediaLib_Reading extends HGKMediaLib_DataBase
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Find using an extended query.
     *
     * One clause looks like this:
     *
     * array(
     *  'connector' => {AND, OR}
     *  'subject'   => '..' // name of property to be matched against, set is to be defined
     *  'predicate' => {'=', '~', '!=', '!~'} // equal, like, not equal, not like
     *  'object'    => '..' // property value to match
     *  )
     *
     * $clauses can contain any number of these.
     *
     * The $sortOrder is an associative array, with the lable as key, and the order as value, e.g.
     * array(
     *   'insertionDate' => 'descending'
     *   'workTitle'     => 'ascending'
     *   )
     *
     *
     * NB: this function returns (at the moment) only instance information because
     * here are stored the most important data
     *
     * @param string $session
     * @param array $clauses
     * @param array $sortOrder
     * @param int $limit
     * @param string $lang
     * @access public
     * @return array of HGKMediaLib_Struct_Entity objects
     */
    public function find($session, $clauses, $sort_order, $limit = 40, $lang = 'de')
    {
//         echo 'versuche jetzt was zu finden.<br>';
//         echo 'session: '.$session.'<br>';
        session_id($session);
        session_start();
        if ($session != $_SESSION['php_session']) {
            throw new SoapFault("Authentication Error","session doesn't exists");
        }
        $_SESSION['find'] = '';
        $this->dropTempTables($session);
        $clauses = $this->getDataType($clauses);

        /*echo '<pre>';
        print_r($clauses);
        echo '</pre>';*/

        $id_array = $this->getEntityIDs($clauses,$lang,$session);
        /*echo '<pre>id_array:';
        print_r($id_array);
        echo '</pre>';*/

        if (count($id_array)) {
            $entity_array = array();
            foreach ($id_array AS $entity => $ids) {
                if (count($ids)) {
                    $tmp_array = $this->getWorks($entity,$ids);
                    foreach ($tmp_array AS $work_id => $work) {
                        if (!array_key_exists($work_id,$entity_array)) {
                            $entity_array[$work_id] = $work;
                            $work_id_array['work'][] = $work_id;
                            foreach ($work AS $instance_id => $instance) {
                                $work_id_array['instance'][] = $instance_id;
                                foreach ($instance AS $set_id => $set) {
                                    $work_id_array['set'][] = $set_id;
                                }
                            }
                        }
                    }
                }
            }
            foreach ($work_id_array AS $entity => $ids) {
                if (count($ids)) {
                    $this->createTempTable($entity,$ids,$session);
                    $this->createTempPersonGroupTables($ids,$session);
                }
            }
    
            $result = $this->getResult($session,$clauses,$sort_order,$limit,$entity_array);
    
            /*echo '<pre>result:';
            print_r($result);
            echo '</pre>';*/
            return $result;
        }
    }

    /**
     * The getInformation() method returns a HGKMediaLib_Struct_Information object
     * 
     * @param int $sessionId 
     * @param mixed $id database entity id of a Set, Instance or Work
     * @param string $lang 
     * @access public
     * @return object HGKMediaLib_Struct_Information
     */
    public function getInformation($sessionId, $id, $lang = 'de')
    {
        $count = rand(3,5);

        
        $info = new HGKMediaLib_Struct_Information();
        $info->collection = 'Drama';
        $info->collectionId = 'cIDooo456';
        $info->date = '2006:04:19:23:23';
        $info->description = 'this movie is goooooooood';
        $info->id = 'entityXooo8738742949459282';
        $info->informationBlocks = array();
        $info->title = 'Set'; 

        for ($i = 3; $i < $count; $i++){
            $info->title = 'Sub' . $info->title;
        }
        
        $info->subtree = $this->_generateSubTree(7 - $count, $info->title);

        for ($i = 0; $i < $count; $i++){
            $info->informationBlocks[] = new HGKMediaLib_Struct_InformationBlock();
            $info->informationBlocks[$i]->files = array();
            for ($j = 0; $j < 7; $j++){
                $info->informationBlocks[$i]->files[] = new HGKMediaLib_Struct_Files();
                $info->informationBlocks[$i]->files[$j]->name = 'file 00' . $i . "-" . $j;
                $info->informationBlocks[$i]->files[$j]->urn = 'urn  00' . $i . "-" . $j;
            }

            // generate dummy names
            $info->informationBlocks[$i]->title = ($i == 0) ? 'Werk' : (($i == 1) ? 'Instanz' : 'Set');
            if ($i > 2) {
                for ($j = 2; $j < $i; $j++){
                    $info->informationBlocks[$i]->title = 'Sub' . $info->informationBlocks[$i]->title;
                }
            }
            
            // generate some dummy data
            $info->informationBlocks[$i]->data = array();
            for ($j = 0; $j < 6; $j++){
                $info->informationBlocks[$i]->data[] = new HGKMediaLib_Struct_Data;
                $info->informationBlocks[$i]->data[$j]->label = 'label' . $i . "-" . $j;
                $info->informationBlocks[$i]->data[$j]->name = 'name' . $i . "-" . $j;
                $info->informationBlocks[$i]->data[$j]->value = 'value' . $i . "-" . $j;
            }

            // generate dummy id
            $info->informationBlocks[$i]->id = 'idOf' . $info->informationBlocks[$i]->title; 

        }

        for ($i = $count; $i < 7; $i++) {

        }
        return $info;
    }

    private function _generateSubTree($level, $title){
        if ($level <= 0) {
            $result = array();
            $count = rand(2,3);
            for ($i = 0; $i < $count; $i++) {
                $result[] = new HGKMediaLib_Struct_Media();
                $result[$i]->data = array();
                for ($j = 0; $j < 2; $j++){
                    $result[$i]->data[] = new HGKMediaLib_Struct_Data;
                    $result[$i]->data[$j]->label = 'Media label' . $j;
                    $result[$i]->data[$j]->name = 'Media name' . $j;
                    $result[$i]->data[$j]->value = 'Media value' . $j;
                }
                $result[$i]->id     = 'mediaId' . $i;
                $result[$i]->name   = 'MPEG' . $i;
                $result[$i]->urn    = 'urn://blah';
            }
            $result[] = new HGKMediaLib_Struct_Media();
            end($result)->data = array();
            end($result)->id = 'vbmID';
            end($result)->name = 'VBM-HGKZ-' . $i;
            end($result)->urn = 'media1.hgkz.ch/tmp/pictures';
            
            $result[] = new HGKMediaLib_Struct_Media();
            end($result)->data = array();
            end($result)->id = 'covID';
            end($result)->name = 'COV-HGKZ-' . $i;
            end($result)->urn = 'media1.hgkz.ch/tmp/pictures/1.jpg';
            
            return $result;
        }
        $result = array();
        $count = rand(2,3);
        for ($i = 0; $i < $count; $i++) {
            $result[] = new HGKMediaLib_Struct_EntityNode();
            $result[$i]->description = "This is the description of Sub$title no: $i";
            $result[$i]->id = "SomeSetID $level : $i";
            $result[$i]->title = "Sub$title no: $i";
            $result[$i]->subtree = $this->_generateSubTree($level - 1, "Sub" . $title);
        }
        return $result;
    }

    /**
     * arrange the sets in the work_array by putting all sets  with a master set id in their master set
     *
     * @param array work_array: array with serach results ordered by the entitiy hierarchy
     * @return array work_array
     * @access protected
     */
    private function arrangeSets($work_array)
    {
        foreach ($work_array AS $work_id => $work) {
            foreach ($work['instance'] AS $instance_id => $instance) {
                $i = -1;
                while ($i <= count($instance['set'])) {
                    foreach ($instance['set'] AS $set_id => $set_array) {
                        if ($master_id = $set_array['master_set_id']) {
                            $work_array[$work_id]['instance'][$instance_id]['set'][$master_id]['set'][$set_id] = $set_array;
                        }
                    }
                    $i++;
                }

                foreach ($instance['set'] AS $set_id => $set_array) {
                    if ($master_id = $set_array['master_set_id']) {
                        unset($work_array[$work_id]['instance'][$instance_id]['set'][$set_id]);
                    }
                }
            }
        }
        return $work_array;
    }

    /**
     * gets data field ids and data types of the data fields matching the subject
     * provided by the mediagonal frontend query parameter
     *
     * @param array clauses: provided by the mediagonal frontend, modified by previous functions
     * @return array $clauses
     * @access protected
     */
    private function getDataType($clauses)
    {
        $i = 0;
        foreach ($clauses AS $clause) {
            $data_type_array = $this->getDataFieldParam($clause['subject']);
            if (count($data_type_array)){
                $clauses[$i]['data_type'] = $data_type_array;
            }
            $i++;
        }
        //$clauses[0]['data_type'] = array('VARCHAR');
        return $clauses;
    }
}

if (DEBUG_FLAG) {
    $read = new HGKMediaLib_Reading();
    $clauses = array(
        array(
            'connector' => '',
            'subject' => 'Titel',
            'predicate' => '~',
            'object' => 'Mord'
        ),
        array(
            'connector' => 'AND',
            'subject' => 'fernseh_sender',
            'predicate' => '=',
            'object' => 'ZDF'
        )/*,
        array(
            'connector' => 'AND',
            'subject' => 'Schauspieler',
            'predicate' => '~',
            'object' => 'Mastroianni'
        ),array(
            'connector' => '',
            'subject' => 'Schauspieler',
            'predicate' => '~',
            'object' => 'Montand Yves'
        )
        array(
            'connector' => 'AND',
            'subject' => 'Titel',
            'predicate' => '=',
            'object' => 'Lieben Sie Brahms'
        ),
        array(
            'connector' => 'OR',
            'subject' => 'Schauspieler',
            'predicate' => '~',
            'object' => 'Angst'
        ),
        array(
            'connector' => 'OR',
            'subject' => 'Thema',
            'predicate' => '~',
            'object' => 'Angst'
        ),
        array(
            'connector' => 'OR',
            'subject' => 'Signatur',
            'predicate' => '~',
            'object' => 'Angst'
        )*//*,
        array(
            'connector' => 'AND',
            'subject' => 'Schauspieler',
            'predicate' => '=',
            'object' => 'Bisset Jacqueline'
        ),*/
    );
    $sort_order = array(
        'Titel' => 'asc'
    );
    $limit = 10;
    $lang = 'de';
    $read->find('8a82d55bad5b6b3843e5fb536e25f054',$clauses,$sort_order,$limit,$lang);
} else if (!DEBUG_FLAG) {
    ini_set('soap.wsdl_cache_enabled', '0');
    try {
        $soap = new SoapServer(ROOT_PATH.'soap/wsdl/HgkMediaLib_Reading.wsdl');
        $soap->setClass("HGKMediaLib_Reading");
        $soap->handle();
    } catch (SoapFault $f) {
        throw new SoapFault();
    }
}

?>
