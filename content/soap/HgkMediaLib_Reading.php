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
     * NB: according aggreement with mediagonal (hannes gassert) this function returns only
     * instance entities
     *
     * @param string $session
     * @param array $clauses
     * @param array $sortOrder
     * @param int $limit
     * @param string $lang
     * @access public
     * @return array of HGKMediaLib_Struct_Entity objects
     */
    public function find($session,$clauses,$sort_order,$limit=40,$lang='de')
    {
//         echo 'versuche jetzt was zu finden.<br>';
//         echo 'session: '.$session.'<br>';
        session_id($session);
        session_start();
        if ($session != $_SESSION['php_session']) {
            throw new SoapFault("Authentication Error","session doesn't exists");
        }
        if (DEBUG_FLAG) {
            echo '<pre>';
            print_r($clauses);
            echo '</pre>';
        }

        //ACHTUNG: this gives only instance ids !!!
        $id_array = $this->searchEntityIDs($clauses,$sort_order,$lang,$limit,$session);

        if (DEBUG_FLAG) {
            echo '<pre>id_array:';
            print_r($id_array);
            echo '</pre>';
        }

        $result = array();
        foreach ($id_array AS $instance_id)
        {
            $result[] = $this->getFindInformation($instance_id);
        }

        if (DEBUG_FLAG) {
            echo '<pre>result:';
            print_r($result);
            echo '</pre>';
        }
        return $result;
    }

    private function getFindInformation($entity_id)
    {
        require_once('./inc/HGKMediaLib_Struct_Entity.php');
        $find_infos = $this->searchFindInfos($entity_id);
        return $res_obj = new HgkMediaLib_Struct_Entity(
            $find_infos['collection'],
            '',
            $find_infos['cov'],
            $find_infos['date'],
            $find_infos['description'],
            $entity_id,
            $find_infos['title']
        );
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
        $ids = array($id);
        $work_tree = $this->getWorks('instance',$ids);
        
        if (DEBUG_FLAG) {
            echo '<pre>work tree:';
            print_r($work_tree);
            echo '</pre>';
        }

        $work_data_tree = $this->searchInformation($work_tree);
        
        if (DEBUG_FLAG) {
            echo '<pre>work data tree:';
            print_r($work_data_tree);
            echo '</pre>';
        }

        return $work_data_tree;

        
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
//         array(
//             'connector' => '',
//             'subject' => 'title',
//             'predicate' => '~',
//             'object' => 'Mord fahrpreis'
//         ),
       array(
           'connector' => '',
           'subject' => 'publisher',
           'predicate' => '~',
           'object' => 'zdf'
       ),
       array(
            'connector' => 'AND',
            'subject' => 'Schauspieler',
            'predicate' => '~',
            'object' => 'Mastroianni Marcello'
        )/*,array(
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
        'title' => 'asc'
    );
    $limit = 10;
    $lang = 'de';
    $read->find('0630ab629af6572f9e7e094dfebb5e3c',$clauses,$sort_order,$limit,$lang);
    $read->getInformation('368b9f95f37060276baffbb3caea03b2',59980);
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
