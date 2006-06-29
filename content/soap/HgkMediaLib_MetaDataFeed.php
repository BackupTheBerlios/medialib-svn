<?php
/**
 * Soap Server for feed of transcoded meta data
 *
 *
 * PHP 5
 *
 *
 * @package    HgkMediaLib_backend
 * @author     Franz-Ferdinand Lehnert <franz.lehnert@winet.ch>
 * @copyright  2005-2006 winet Network Solutions AG
 */


/**
* Class for feeding the meta data provided by bitflux
*
* HgkMediaLib_MetaDataFeed will be used as feed handler class for the
* video data provided by the encoder/bitflux. It serves as class for
* a SOAP server.
* n.b. a lot of the functions with data base read/write action
* are using stored procedures programmed with pgpsql !!!
* therefor you will find pgsql specific syntax like '{$v
alue}' for an array entry
*
* @package    HgkMediaLib_backend
* @author     Franz-Ferdinand Lehnert <franz.lehnert@winet.ch>
* @copyright  2005-2006 winet Network Solutions AG
* @version    Release: 0.1
*/
Class HgkMediaLib_MetaDataFeed
{
    /**
     * Array with entities(tables which are only id container) existing in data base
     *
     * @private array
     * @access private
     */
    private $entities;

    /**
     * array with entity(table) ids created in the process of feeding
     *
     * @var array
     * @access private
     */
    private $entity_ids;

    /**
     * name of the entity(table) which is  currently in work
     *
     * @var string
     * @access private
     */
    private $current_entity;

    /**
     * id of the parent entity entry (e.g. work_id for instance, instance_id for set, etc.)
     *
     * @var string
     * @access private
     */
    private $parent_id;

    /**
     * id of the a master media or master set
     *
     * @var string
     * @access private
     */
    private $master_id;

    /**
     * db object
     *
     * @var object
     * @access private
     */
    private $db;

    /**
     * array with the data for the actual entity or other table
     *
     * @var array
     * @access private
     */
    private $temp_array;

    /**
     * handler for data inserts (work, instance, set, media, collection)
     *
     * @var object
     * @access private
     */
    private $data_handler;

    /**
     * group id for a person
     *
     * @var object
     * @access private
     */
    private $related_group_id;

    /**
     * handler for person data inserts
     *
     * @var object
     * @access private
     */
    private $person_handler;

    /**
     * handler for person data inserts
     *
     * @var object
     * @access private
     */
    private $group_handler;

    /**
     * handler for function(role) data inserts
     *
     * @var object
     */
    private $function_handler;

    /**
     * handler for function data inserts
     *
     * @var object
     * @access private
     */
    private $sequence_handler;

    /**
     * entity id of the actual entity
     *
     * @var integer
     * @access private
     */
    private $entity_id;

    /**
     * name of the entity where functional person or group data belongs to
     *
     * @var integer
     * @access private
     */
    private $related_entity_name;

    /**
     * id of the entity where functional person or group data belongs to
     *
     * @var integer
     * @access private
     */
    private $related_entity_id;

    /**
     * constructor
     *
     * the constructor handles the data base connection and the validating
     * of the xml string using a RelaxNG schema and XMLReader (only for this purpose!)
     *
     * the following functions are using SimpleXML
     *
     * @param string $xml_file  xml provided by encoder/bitflux
     *
     * @return void
     */
    public function __construct()
    {
        include_once('DB.php');

        $dns = array (
            'phptype'  => 'pgsql',
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'hostspec' => DB_HOST,
            'database' => DB_NAME,
            'key'      => '',
            'cert'     => '',
            'ca'       => '',
            'capath'   => '',
            'cipher'   => '',
        );

        $this->db = DB::connect($dns);
        $this->handleDbError($this->db);

        $this->entity_id = null;

        //$this->entities = $entities;
        $this->entities = array('work','instance','set','media','collection','person','person_group');
    }

    private function throwSoapFault($fault_code,$fault_string){
        throw new SoapFault($fault_code,$fault_string);
    }

    private function handleDbError($obj)
    {
        if (DB::isError($obj)) {
            if (DEBUG_FLAG) {
                echo '<pre>db:';
                print_r($this->db);
                echo '</pre>';
                die ($obj->getMessage());
            } else {
                $this->throwSoapFault('DB Error',$obj->getMessage());
            }
        }
    }

    /**
     * inserts all data contained in the xml string in data base
     *
     * after avlidating with XMLReader the actual feed uses SimpleXML
     * n.b.: the functions with data base read/write action are
     * often using stored procedures programmed with pgpsql
     *
     * @return boolean true if the xml string is valid
     * @access private
     */
    public function feedData($xml_feed_string)
    {
        set_time_limit(0);
        if ($xml_feed_string == '') {
            $this->throwSoapFault("Bad Argument","No XML string.");
        }

        if (!$this->validateString($xml_feed_string)) {
            $this->throwSoapFault("Validation Error","XML not valid.");
        } else {
            try {
                $sxo = simplexml_load_string($xml_feed_string);
                if ($sxo) {
                    foreach ($sxo AS $work) {
                        // create the entity ids array
                        $this->setEntityIdsArray();
                        // WORK //
                        $this->setCurrentEntity('work');
                        // create new work
                        $this->createEntity();
                        foreach ($work->children() AS $label_wor => $entry_wor) {
                            if (!in_array($label_wor,$this->entities)) {
                                // process work data
                                $this->processData($entry_wor,$label_wor);
                            }else if (in_array($label_wor,$this->entities)) {
                                // INSTANCE //
                                $this->setCurrentEntity($label_wor);
                                // create new instance
                                $this->createEntity();
                                foreach ($entry_wor->children() AS $label_ins => $entry_ins) {
                                    if (!in_array($label_ins,$this->entities)) {
                                        // process instance data
                                        $this->processData($entry_ins,$label_ins);
                                    }else if (in_array($label_ins,$this->entities)) {
                                        if ($label_ins == 'person_group') {
                                            //process group data relating to an instance
                                            $this->setRelatedEntityName($label_wor);
                                            $this->setRelatedEntityID(end($this->entity_ids[$label_wor]));
                                            $this->processPersonGroupData($entry_ins);
                                        }else {
                                            // SET //
                                            $this->setCurrentEntity($label_ins);
                                            // create new set
                                            $this->createEntity();
                                            $this->processAttributes($entry_ins->attributes());
                                            foreach ($entry_ins->children() AS $label_set => $entry_set) {
                                                if (!in_array($label_set,$this->entities)) {
                                                    // process set data
                                                    $this->processData($entry_set,$label_set);
                                                } else if (($label_set != 'set') && (in_array($label_set,$this->entities))) {
                                                    if ($label_set == 'person_group') {
                                                        //process group data relating to a set
                                                        $this->setRelatedEntityName($label_ins);
                                                        $this->setRelatedEntityID(end($this->entity_ids[$label_ins]));
                                                        $this->processPersonGroupData($entry_set);
                                                    }else {
                                                        // MEDIA & SEQUENCE //
                                                        $this->setCurrentEntity($label_set);
                                                        // create new media
                                                        $this->createEntity();
                                                        //process media attributes
                                                        $this->processAttributes($entry_set->attributes());
                                                        foreach ($entry_set->children() AS $label_med => $entry_med) {
                                                            $this->setCurrentEntity($label_med);
                                                            if ($label_med == 'sequence') {
                                                                //process sequence data
                                                                $this->processSequenceData($entry_med);
                                                            } else if ($label_med == 'collection') {
                                                                //process set data
                                                                $this->processCollectionData($entry_med);
                                                            } else {
                                                                //process media data
                                                                $this->setCurrentEntity('media');
                                                                $this->processData($entry_med,$label_med);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                    }
                } else {
                    $this->throwSoapFault("XML Error","XML string not processed");
                }
                return true;
            } catch (Exception $e) {
                $this->throwSoapFault("Feed Error","Insert faild: ".$e->getMessage);
            }
        }
    }

    /**
     * initiates the array with all table names to store the created data base ids
     *
     * @return boolean true if the xml string is valid
     * @access private
     */
    private function setEntityIdsArray()
    {
        $this->entity_ids = array(
            'work' => array(),
            'instance' => array(),
            'set' => array(),
            'media' => array(),
            'sequence' => array(),
            'audio_video_data' => array(),
            'person' => array(),
            'person_group' => array(),
            'collection' => array()
        );
    }

    /**
     * validates the xml string
     *
     * @return boolean true if the xml string is valid
     * @access private
     */
    private function validateString($xml_feed_string)
    {
        $reader = new XMLReader();
        $reader->XML($xml_feed_string);
        if ($reader->setRelaxNGSchema(RELAX_NG_IMPORT_SCHEMA)) {
            while ($reader->read()) {
            }
        }
        if (!$reader->isValid()) {
            return false;
        }
        $reader->close();
        return true;
    }

    /**
     * creates a new entry in one of the tables only containing ids
     *
     * @return void
     * @access private
     */
    private function createEntity()
    {
        if ($this->current_entity != 'collection') {
            $this->getParentEntityID();
        }else {
            $this->setParentEntityID();
        }
        $stm  = "SELECT sp_create_new_entity('".$this->current_entity."'";
        if ($this->parent_id) {
            $stm .= ",'".$this->parent_id."'";
            if ($this->master_id) {
                $stm .= ",'".$this->master_id."'";
            }else {
                $stm .= ",NULL";
            }
        }else {
            $stm .= ",NULL";
            if ($this->master_id) {
                $stm .= ",'".$this->master_id."'";
            }else {
                $stm .= ",NULL";
            }
        }
        $stm .= ") AS id";
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            if (
                ($this->current_entity != 'set') &&
                ($this->current_entity != 'media')
            ) {
                $this->saveEntityID($row['id']);
            }else {
                $this->setEntityID($row['id']);
            }
        }
        $result->free();
    }

    /**
     * sets value of the variable current_entity (table name)
     *
     * @return void
     * @access private
     */
    private function setCurrentEntity($entity)
    {
        $this->current_entity = $entity;
    }

    /**
     * sets value of the variable entity_id (id of the actual table and entry)
     *
     * @return void
     * @access private
     */
    private function setEntityID($id=null)
    {
        $this->entity_id = $id;
    }

    /**
     * stores the current entity id i the array entity_ids
     *
     * @return void
     * @access private
     */
    private function saveEntityID($id=null,$xml_id=null)
    {
        if ($xml_id) {
            $this->entity_ids[$this->current_entity][(int)$xml_id] = $id;
        } else {
            if (end($this->entity_ids[$this->current_entity]) != $id) {
                $this->entity_ids[$this->current_entity][] = $id;
            }
        }
    }

    /**
     * sets the name of the person/group related entity (table)
     *
     * @return void
     * @access private
     */
    private function setRelatedEntityName($entity)
    {
        $this->related_entity_name = $entity;
    }

    /**
     * sets the id of the person/group related entity entry
     *
     * @return void
     * @access private
     */
    private function setRelatedEntityID($id=null)
    {
        $this->related_entity_id = $id;
    }

    /**
     * sets the id of the group a person belongs to
     *
     * @return void
     * @access private
     */
    private function setRelatedGroupID($id=null)
    {
        $this->related_group_id = $id;
    }

    /**
     * sets the function flag, indicating the person/roup data as function(role) data
     *
     * @return void
     * @access private
     */
    private function setFunctionFlag($flag=false)
    {
        $this->function_flag = $flag;
    }

    /**
     * sets the id of the entity entry which is the parent entry of the actual data
     *
     * @return void
     * @access private
     */
    private function setParentEntityID($id=null)
    {
        $this->parent_id = $id;
    }

    /**
     * inserts a entry in the table audio_video_data and stores the id
     * in the array entity_ids for use for video_stream and audio_stream
     * entries
     *
     * @return void
     * @access private
     */
    private function createAvEntry()
    {
        $this->getParentEntityID();
        $stm  = "SELECT sp_create_new_audio_video_data(".$this->parent_id.") AS id";
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            $this->saveEntityID($row['id']);
        }
        $result->free();
    }

    /**
     * gets the id of the parent entity entry from the entity_ids array
     *
     * @return void
     * @access private
     */
    private function getParentEntityID()
    {
        reset($this->entity_ids);
        while (current($this->entity_ids)) {
            if (key($this->entity_ids) == $this->current_entity) {
                break;
            }
            next($this->entity_ids);
        }
        $parent_id_array = prev($this->entity_ids);
        if (is_array($parent_id_array)) {
            $this->parent_id = end($parent_id_array);
        }else {
            $this->parent_id = null;
        }
    }

    /**
     * processes all work, instance, set, media and collection data
     *
     * @return void
     * @access private
     */
    private function processData ($entry,$label='')
    {
        foreach ($entry->attributes() AS $attr_name => $attr_value) {
            $temp_array[0][$attr_name] = (string)$attr_value;
        }
        /*** insert data  ***/
        if ($label != 'acl') {
            $temp_array = $this->transformDate($temp_array);
        }
        foreach ($temp_array AS $this->temp_array) {
            if ($label != 'acl') {
                $this->executeDataInsert();
            } else if ($label == 'acl') {
                $this->executeAclInsert();
            }
        }
        unset($this->temp_array);
    }

    /**
     * processes attributes of set and media data (because of possible
     * relations between ids [master_set_id, master_media_id])
     *
     * @return void
     * @access private
     */
    private function processAttributes($entry)
    {
        foreach ($entry AS $label => $entry) {
            if ($label == 'id') {
                $this->saveEntityID($this->entity_id,$entry);
            }else {
                $attr_flag = true;
                if (substr($label,0,strlen('master')) == 'master') {
                    $value = $this->entity_ids[$this->current_entity][(string)$entry];
                }else {
                    $value = (string)$entry;
                }
                $columns[] = $label;
                $values[] = $value;
            }
        }
        if ($attr_flag) {
            $this->updateEntity($columns,$values);
        }
    }

    /**
     * processes collection datas
     *
     * @return void
     * @access private
     */
    private function processCollectionData($entry)
    {
        $i = 0;
        $insert_acl_flag = false;
        //extract data from xml
        foreach ($entry AS $label => $entry_col) {
            foreach ($entry_col->attributes() AS $label_col => $value_col) {
                if ($label != 'acl') {
                    $collection_array[$i][$label_col] = (string)$value_col;
                } else if ($label == 'acl') {
                    $acl_array[$label_col] = (string)$value_col;
                }
            }
            $i++;
        }
        //transform all data entries in julian date
        $collection_array = $this->transformDate($collection_array);

        //check if collection already exists
        $this->setEntityID(); //set $this->entity_id = NULL
        $this->getEntityID($collection_array);
//         echo '<pre>';
//         print_r($this->entity_ids);
//         echo '</pre>';
//         echo 'entity: '.$this->entity_id.'<br>';

        //create new colection entry, if collection does not exists
        if (!$this->entity_id) {
            $this->createEntity();
            if (count($acl_array) > 0) {
                $insert_acl_flag = true;
            }
        }else if (($this->entity_id) && (count($acl_array) > 0)) {
            $update_acl_flag = true;
        }

        //insert data
        foreach ($collection_array AS $this->temp_array) {
//             echo '<pre>this->temp_array';
//             print_r($this->temp_array);
//             echo '</pre>';
            $this->executeDataInsert();
        }
        //set collection id in media_entry
        $this->setCollectionID();
        //create acl entry
        if ($insert_acl_flag) {
            $this->temp_array = $acl_array;
            $this->executeAclInsert();
        }else if ($update_acl_flag) {
            $this->temp_array = $acl_array;
            $this->executeAclUpdate();
        }
        unset($this->temp_array);
    }

    /**
     * processes person and group datas
     *
     * @return void
     * @access private
     */
    private function processPersonGroupData($entry)
    {
        $this->setRelatedGroupID();
        $i = 0;
        $insert_acl_flag = false;
        foreach ($entry->children() AS $pg_label_entity => $pg_entry_entity) {
            $acl_flag = false;
            if ($pg_label_entity == 'group') {
                $this->setEntityID();
                $entity = 'person_group';
                $this->setCurrentEntity($entity);
            } else if ($pg_label_entity == 'person') {
                $this->setEntityID();
                $this->setCurrentEntity($pg_label_entity);
            }
            $temp_array = array();
            $i=0;
            foreach ($pg_entry_entity as $pg_label => $pg_entry) {
                foreach ($pg_entry->attributes() AS $label => $value) {
                    if ($pg_label != 'acl') {
                        $temp_array[$i][$label] = (string)$value;
                    } else if ($pg_label == 'acl') {
                        $acl_array[$label] = (string)$value;
                    }
                }
                $i++;
            }
            $temp_array = $this->transformDate($temp_array);
            $this->getEntityID($temp_array);
            echo 'id: '.$this->entity_id.'<br>';
            if ((!$this->entity_id) && (count($acl_array) > 0)) {
                $insert_acl_flag = true;
            }else if (($this->entity_id) && (count($acl_array) > 0)) {
                $update_acl_flag = true;

            }

            foreach ($temp_array AS $this->temp_array) {
                if (($this->temp_array['type'] == 'group_data') || ($this->temp_array['type'] == 'person_data')) {
                    if ($this->current_entity == 'person_group') {
                        $this->executeGroupInsert();
                    } else if ($this->current_entity == 'person') {
                        $this->executePersonInsert();
                    }
                } else if ($this->temp_array['type'] == 'function_data') {
                    if ($this->current_entity == 'person_group') {
                        $this->executeGroupFunctionInsert();
                    } else if ($this->current_entity == 'person') {
                        $this->executePersonFunctionInsert();
                    }
                }
            }
            if ($this->current_entity == 'person_group') {
                $this->setRelatedGroupID($this->entity_id);
            }

            if ($insert_acl_flag) {
                $this->temp_array = $acl_array;
                $this->executeAclInsert();
            }elseif ($update_acl_flag) {
                $this->temp_array = $acl_array;
                $this->executeAclUpdate();
            }
            unset($this->temp_array);
        }
    }

    /**
     * processes sequence datas
     *
     * @return void
     * @access private
     */
    private function processSequenceData($entry)
    {
        $i = 0;
        foreach ($entry->attributes() AS $label => $value) {
            $this->temp_array[$label] = (string)$value;
        }
        //creates a new Sequence and insert values belonging to the sequence
        $this->executeSequenceInsert();;
        unset ($this->temp_array);
        $i = 0;
        foreach ($entry AS $label => $values) {
            foreach ($values->attributes() AS $label_seq => $value_seq) {
                if ($label != 'acl') {
                    $temp_array[$i][$label_seq] = (string)$value_seq;
                } else if ($label == 'acl') {
                    $acl_array[$label_seq] = (string)$value_seq;
                }
            }
            $i++;
        }
        //create an acl entry for the sequence
        $this->temp_array = $acl_array;
        $this->executeAclInsert();
        unset ($this->temp_array);

        $seq = 0;
        $old_seq = 0;
        foreach ($temp_array AS $values) {
            $type = $values['type'];
            if ($type == 'audio_stream') {
                $this->setCurrentEntity('audio_video_data');
                $this->createAvEntry();
            }
            $this->setCurrentEntity($type);
            foreach ($values AS $label => $value) {
                if ($label != 'type') {
                    $this->temp_array[$label] = $value;
                }
            }
            $this->executeInsert();
            $old_seq = $seq;
            unset ($this->temp_array);
        }
    }

    /**
     * gets the mandatory default_template fields for the actual entity from db
     *
     * @return array $labels contains the names of the mandatory template fields
     * @access private
     */
    private function getDefaultTemplate()
    {
        $labels = array();

//         $stm  = "SELECT df.label";
//         $stm .= " FROM data_field AS df";
//         $stm .= " INNER JOIN rel_data_field_table AS rdt ON df.id = rdt.data_field_id";
//         $stm .= " INNER JOIN tables ON rdt.tables_id = tables.id";
//         $stm .= "   AND tables.table = '".$this->current_entity."'";
//         $stm .= " INNER JOIN rel_template_field AS rtf ON rdt.id = rtf.rel_data_field_table_id";
//         $stm .= "   AND rtf.mandatory = TRUE";
//         $stm .= " INNER JOIN template AS tpl ON rtf.template_id = tpl.id";
//         $stm .= " WHERE tpl.name = 'default_".$this->current_entity."'";

        $stm  = "SELECT df.label";
        $stm .= " FROM data_field AS df";
        $stm .= " WHERE entity = '".$this->current_entity."'";
        //$stm .= " AND rtf.mandatory = TRUE";
        //echo $stm.'<br>';

        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            $labels[] = $row['label'];
        }
        $result->free();
        return $labels;
    }

    /**
     * gets the id for a person, group or collection;
     * uses the mandatory default_template fields to check wether if the new data
     * are already in data base or not
     *
     * @return void
     * @access private
     */
    private function getEntityID($entries)
    {
        $labels = $this->getDefaultTemplate();
//         echo '<pre>labels:';
//         print_r($labels);
//         echo '</pre>';

        if (count($labels) > 0) {
            if ($this->current_entity == 'person_group') {
                $entity_abbr = 'gro';
            }else {
                $entity_abbr = substr($this->current_entity,0,3);
            }

            $entity_id = 0;
            $old_entity_id = 0;
            $break_flag = false;
            foreach ($entries AS $entry) {
                if (in_array(strtolower($entity_abbr.'_'.$entry['label']),$labels)) {
//                     $stm  = "SELECT MAX(rcc.entity_id) AS entity_id";
//                     $stm .= " FROM rel_content_container AS rcc";
//                     $stm .= " INNER JOIN content AS c ON rcc.content_id = c.id";
                    $stm  = "SELECT MAX(c.entity_id) AS entity_id";
                    $stm .= " FROM content AS c";
                    $stm .= " INNER JOIN data_field AS df ON c.data_field_id = df.id";
                    $stm .= " WHERE df.entity = '".$this->current_entity."'";
                    $stm .= " AND df.label = '".strtolower($entry['label'])."'";
                    $stm .= " AND c.\"".strtoupper($entry['data_type'])."\" = '".$entry['value']."'";
                    if ($old_entity_id) {
                        $stm .= "AND c.entity_id = ".$old_entity_id;
                    }
                    //echo $stm.'<br>';
                    $result = $this->db->query($stm);
                    $this->handleDbError($result);
                    while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                        $this->handleDbError($row);
                        $entity_id = $row['entity_id'];
                    }
                    $result->free();
                    if (($old_entity_id) && (!$entity_id)) {
                        $break_flag = true;
                        break;
                    }
                    $old_entity_id = $entity_id;
                }
            }
            if (!$break_flag) {
                $this->setEntityID($entity_id);
                if (
                    (array_key_exists($this->current_entity,$this->entity_ids)) &&
                    ($entity_id != end($this->entity_ids[$this->current_entity]))
                ) {
                    $this->saveEntityID($entity_id);
                }
            }
        }
    }

    /**
     * adds to every date entrie an *_start and *_stop entry as julian date for
     * calcultion reasons
     *
     * @return void
     * @access private
     */
    private function transformDate($entries)
    {
        $temp_entries = array();
        $i = 0;
        foreach ($entries AS $entry) {
            if (strtoupper($entry['data_type']) == 'DATE'){
                $temp_entries[$i] = $entry;
                $temp_entries[$i] ['data_type'] = 'VARCHAR';
                $i++;
                $temp_entries[$i] = $entry;
                $temp_entries[$i] ['label'] = $entry['label'].'_start';
                $temp_entries[$i] ['data_type'] = 'INTEGER';
                $temp_entries[$i] ['value'] = $this->gregorian2julian($entry['value']);
                $i++;
                $temp_entries[$i] = $entry;
                $temp_entries[$i] ['label'] = $entry['label'].'_stop';
                $temp_entries[$i] ['data_type'] = 'INTEGER';
                $temp_entries[$i] ['value'] = $this->gregorian2julian($entry['value']);
            }else {
                $temp_entries[] = $entry;
            }
            $i++;
        }
        return $temp_entries;
    }

    /**
     * transforms a given gregorian in the julian date
     *
     * @return void
     * @access private
     */
    private function gregorian2julian($date)
    {
        $exploded = explode('-',$date);
        return gregoriantojd((int)$exploded[1],(int)$exploded[2],(int)$exploded[0]);
    }

    /**
     * prepares a person data insert;
     * uses the prepare_function of DB.php and
     * the stored procedere sp_insert_person_data
     *
     * @return object handler for person data insert
     * @access private
     */
    private function preparePersonInsert()
    {
        $stm  = "SELECT sp_insert_person_data";
        $stm .= "(?,?,?,?,?,?,?,?,?,?) AS id";
        $handler = $this->db->prepare($stm);
        $this->handleDbError($handler);
        return $handler;
    }

    /**
     * executes a prepared person data insert
     * uses the execute_function of DB.php
     *
     * @return void
     * @access private
     */
    private function executePersonInsert()
    {
        if (!$this->person_handler) {
            $this->person_handler = $this->preparePersonInsert();
        }

//         echo '<pre>person: '.$this->entity_id;
//         print_r($this->temp_array);
//         echo '</pre>';

        //get entity_abbr
        $abbr = 'per';

        $person = array(
//             '{'.strtolower($this->temp_array['label']).'}',
            $abbr.'_'.strtolower($this->temp_array['label']),
//             '{'.$this->temp_array['value'].'}',
            $this->temp_array['value'],
            strtoupper($this->temp_array['data_type']),
            '{'.$this->temp_array['lang'].'}',
            $this->entity_id,
            $this->related_group_id,
            null,
            null,
//             null,
            null,
            null
        );
        $result = $this->db->execute($this->person_handler,$person);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);

            if (!$this->entity_id) {
                $this->setEntityID($row['id']);
                $this->saveEntityID($row['id']);
            }
        }
        $result->free();
    }

    /**
     * prepares a person_group data insert;
     * uses the prepare_function of DB.php
     * and the stored procedere sp_insert_group_data
     *
     * @return object handler for person data insert
     * @access private
     */
    private function prepareGroupInsert()
    {
        $stm  = "SELECT sp_insert_group_data";
        $stm .= "(?,?,?,?,?,?,?,?,?) AS id";
        $handler = $this->db->prepare($stm);
        $this->handleDbError($handler);
        return $handler;
    }


    /**
     * executes a prepared person_group data insert
     * uses the execute_function of DB.php
     *
     * @return void
     * @access private
     */
    private function executeGroupInsert()
    {
        //echo $this->entity_id.'<br>';
        if (!$this->group_handler) {
            $this->group_handler = $this->prepareGroupInsert();
        }

//         echo '<pre>person: '.$this->entity_id;
//         print_r($this->temp_array);
//         echo '</pre>';

        //get entity_abbr
        $abbr = 'gro';

        $group = array(
//             '{'.strtolower($this->temp_array['label']).'}',
            $abbr.'_'.strtolower($this->temp_array['label']),
//             '{'.$this->temp_array['value'].'}',
            $this->temp_array['value'],
            strtoupper($this->temp_array['data_type']),
            '{'.$this->temp_array['lang'].'}',
            $this->entity_id,
            NULL,
            NULL,
//             NULL,
            NULL,
            NULL
        );
//         echo '<pre>';
//         print_r($group);
//         echo '</pre>';
        $result = $this->db->execute($this->group_handler,$group);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            if (!$this->entity_id) {
                $this->setEntityID($row['id']);
                $this->saveEntityID($row['id']);
            }
        }
        $result->free();
    }

    /**
     * prepares a person/group function(role) data insert;
     * uses the prepare_function of db.php and
     * the stored procedere sp_insert_function_data
     *
     * @return object handler for function insert
     * @access private
     */
    private function prepareFunctionInsert()
    {
        $stm  = "SELECT sp_insert_function_data";
        $stm .= "(?,?,?,?,?)";
        $handler = $this->db->prepare($stm);
        $this->handleDbError($handler);
        return $handler;
    }

    /**
     * executes a prepared function(role) data insert for a group
     * uses the execute_function of DB.php
     *
     * @return void
     * @access private
     */
    private function executeGroupFunctionInsert()
    {
        if (!$this->function_handler) {
            $this->function_handler = $this->prepareFunctionInsert();
        }
        $function = array(
            null,
            $this->entity_id,
            //'{'.strtolower($this->temp_array['label']).'}',
            $this->temp_array['value'],
            $this->related_entity_name,
            $this->related_entity_id
        );
        $res = $this->db->execute($this->function_handler,$function);
        $this->handleDbError($res);
        $res->free();
    }

    /**
     * executes a prepared function(role) data insert for a person;
     * uses the execute_function of DB.php
     *
     * @return void
     * @access private
     */
    private function executePersonFunctionInsert()
    {
        if (!$this->function_handler) {
            $this->function_handler = $this->prepareFunctionInsert();
        }

//         echo '<pre>person: '.$this->entity_id;
//         print_r($this->temp_array);
//         echo '</pre>';

        $function = array(
            $this->entity_id,
            null,
            //'{'.strtolower($this->temp_array['label']).'}',
            $this->temp_array['value'],
            $this->related_entity_name,
            $this->related_entity_id
        );
        $res = $this->db->execute($this->function_handler,$function);
        $this->handleDbError($res);
        $res->free();
    }

    /**
     * gets the next id from sequence_id_seq sequence
     *
     * @return void
     * @access private
     */
    private function getNextSequenceID()
    {
        $stm  = "SELECT nextval('entity_id_seq') as id";
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            $this->setEntityID($row['id']);
            $this->saveEntityID($row['id']);
        }
        $result->free();
    }

    /**
     * prepares a sequence data insert;
     * uses the auto_prepare_function of DB.php
     *
     * @return object handler for sequence data insert
     * @access private
     */
    private function prepareSequenceInsert($table_fields)
    {
        $handler = $this->db->autoPrepare('sequence', $table_fields, DB_AUTOQUERY_INSERT);
        $this->handleDbError($handler);
        return $handler;
    }

    /**
     * executes a sequence data insert;
     * uses the auto_execute_function of DB.php
     *
     * @return void
     * @access private
     */
    private function executeSequenceInsert()
    {
        $this->getParentEntityID();
        $this->getNextSequenceID();
        $columns[] = 'id';
        $columns[] = 'media_id';
        $columns[] = 'creation_date';
        $values[] = $this->entity_id;
        $values[] = $this->parent_id;
        $values[] = date('Y-m-d');
        foreach ($this->temp_array AS $key => $value) {
            $columns[] = $key;
            $values[] = $value;
        }
        $sequence_handler = $this->prepareSequenceInsert($columns);
        $res = $this->db->execute($sequence_handler, $values);
        $this->handleDbError($res);
    }

    /**
     * gets the next id aof the sequence containing a table id
     *
     * @return void
     * @access private
     */
    private function getNextID()
    {
        $stm  = "SELECT nextval('".$this->current_entity."_id_seq') as id";
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            $this->setEntityID($row['id']);
        }
        $result->free();
    }

    /**
     * prepares the insert for image, text, audio, video data;
     * uses the auto_prepare_function of DB.php
     *
     * @return object handler for data insert
     * @access private
     */
    private function prepareInsert($table_fields)
    {
        $handler = $this->db->autoPrepare($this->current_entity, $table_fields, DB_AUTOQUERY_INSERT);
        $this->handleDbError($handler);
        return $handler;
    }

    /**
     * executes the insert of image, text, audio, video data;
     * uses the auto_execute_function of DB.php
     *
     * @return void
     * @access private
     */
    private function executeInsert()
    {
        if (($this->current_entity == 'audio_stream') || ($this->current_entity == 'video_stream')) {
            $desc = 'audio_video_data_id';
            $parent_id = end($this->entity_ids['audio_video_data']);
        } else if (($this->current_entity == 'text_data') || ($this->current_entity == 'image_data')) {
            $desc = 'sequence_id';
            $parent_id = end($this->entity_ids['sequence']);
        }
        $this->getNextID($pointer);
        $columns = array('id',$desc,'creation_date');
        $values = array($this->entity_id,$parent_id,date('Y-m-d'));
        foreach ($this->temp_array AS $key => $value) {
            $columns[] = $key;
            $values[] = $value;
        }
        $handler = $this->prepareInsert($columns);
        $res = $this->db->execute($handler, $values);
        $this->handleDbError($res);
    }

    /**
     * sets the collection id in the connected media entry
     *
     * @return void
     * @access private
     */
    private function setCollectionID()
    {
        $media_id = end($this->entity_ids['media']);
        $coll_id = end($this->entity_ids['collection']);
        $stm  = "SELECT collection_id FROM media";
        $stm .= " WHERE id = ".$media_id;
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            if ($row['collection_id']){
                $stm  = "UPDATE media";
                $stm .= " SET collection_id = array_append(collection_id,CAST(".$coll_id." AS BIGINT))";
                $stm .= " WHERE id = ".$media_id;
            } else {
                $stm  = "UPDATE media";
                $stm .= " SET collection_id = '{".$coll_id."}'";
                $stm .= " WHERE id = ".$media_id;
            }
        }
        $result = $this->db->query($stm);
        $this->handleDbError($result);
    }

    /**
     * prepares the insert for work, instance, set, media, collection data;
     * uses the prepare_function of db.php and
     * the stored procedere sp_insert_data
     *
     * @return object handler for data insert
     * @access private
     */
    private function prepareDataInsert()
    {
        $stm  = "SELECT sp_insert_data";
        $stm .= "(?,?,?,?,?,?,?) AS entity_id";
        $handler = $this->db->prepare($stm);
        $this->handleDbError($handler);
        return $handler;
    }

    /**
     * executes the insert of work, instance, set, media, collection data;
     * uses the execute_function of DB.php
     *
     * @return void
     * @access private
     */
    private function executeDataInsert()
    {
//         echo '<pre>';
//         print_r($this->temp_array);
//         echo '</pre>';
        if (!$this->data_handler) {
            $this->data_handler = $this->prepareDataInsert();
        }
        //echo 'handler: '.$this->data_handler.'<br>';
        $entity_id = end($this->entity_ids[$this->current_entity]);
        if ($this->temp_array['value']) {
            $value = $this->temp_array['value'];
        } else {
            $value = null;
        }

        //get entity_abbr
        $abbr = $this->getEntityAbbreviation($this->current_entity);

        $data = array(
            $abbr.'_'.str_replace('.','_',strtolower($this->temp_array['label'])),
            $value,
            strtoupper($this->temp_array['data_type']),
            '{'.$this->temp_array['lang'].'}',
            $this->current_entity,
            $entity_id,
            $this->parent_id
        );
//         echo '<pre>';
//         print_r($data);
//         echo '</pre>';
        $res = $this->db->execute($this->data_handler,$data);
        $this->handleDbError($res);
        //$res->free();
    }

    private function getEntityAbbreviation($entity)
    {
        if ($entity == 'person_group') {
            return 'gro';
        } else {
            return substr($entity,0,3);
        }
    }

    /**
     * executes an insert of an acl entry;
     * uses the auto_prepare and auto_execute functions of DB.php
     *
     * @return void
     * @access private
     */
    private function executeAclInsert()
    {
        $entity_id = end($this->entity_ids[$this->current_entity]);
        $columns = array_keys($this->temp_array);
        $values = array_values($this->temp_array);
        $columns[] = 'entity_id';
        $values[] = $entity_id;

        $handler = $this->db->autoPrepare('acl', $columns, DB_AUTOQUERY_INSERT);
        $res = $this->db->execute($handler, $values);
        $this->handleDbError($res);
    }

    /**
     * executes an update of an acl entry;
     * uses the auto_prepare and auto_execute functions of DB.php
     *
     * @return void
     * @access private
     */
    private function executeAclUpdate()
    {
        $entity_id = end($this->entity_ids[$this->current_entity]);
        $columns = array_keys($this->temp_array);
        $values = array_values($this->temp_array);
        $where_clause = 'entity_id = '.$entity_id;

        $handler = $this->db->autoPrepare('acl', $columns, DB_AUTOQUERY_UPDATE,$where_clause);
        $res = $this->db->execute($handler, $values);
        $this->handleDbError($res);
    }

    /**
     * adds attributres values to an before created table antry
     *
     * @return void
     * @access private
     */
    private function updateEntity($columns,$values)
    {
        $where_clause = "id = ".$this->entity_id;
        $handler = $this->db->autoPrepare($this->current_entity, $columns, DB_AUTOQUERY_UPDATE,$where_clause);
        $res = $this->db->execute($handler, $values);
        $this->handleDbError($res);
    }
}

require_once('../global/customize.php');

if (DEBUG_FLAG) {

    $file = '../feed/feed-imported.xml';
//     $file = '../feed/newfeed.xml';
    $handle = fopen($file,'r');
    $xml_string = fread($handle,filesize($file));
    fclose($handle);

    $xml_string = str_replace(
        array('<person>','</person>'),
        array('<person_group><person>','</person></person_group>'),
        $xml_string
    );

//     $reader = new XMLReader();
//     $reader->XML($xml_string);
//     if ($reader->setRelaxNGSchema(RELAX_NG_IMPORT_SCHEMA)) {
//         while ($reader->read()) {
//         }
//     }
//     if (!$reader->isValid()) {
//         die ('invalid document<br>');
//     }
//     $reader->close();

    $file = '../feed/feed.xml';
    $handle = fopen($file,'r');
    $xml_frame_string = fread($handle,filesize($file));
    fclose($handle);

    $feed = new HgkMediaLib_MetaDataFeed();

    $sxo = simplexml_load_string($xml_string);

    $start = 0;
//     echo 'start: '.$start.'<br>';
    $i = 1;
    foreach ($sxo->children() AS $work) {
        if ($i > $start) {
            $xml_string = $work->asXML();
            $xml_string = str_replace('{work}',$xml_string,$xml_frame_string);

            $feed->feedData($xml_string);
        }
//         echo 'i: '.$i.'<br>';
        $i++;
    }
} else {
    ini_set('soap.wsdl_cache_enabled', '0');
    try {
        $soap = new SoapServer(ROOT_PATH.'soap/wsdl/HgkMediaLib_MetaDataFeed.wsdl');
        $soap->setClass("HgkMediaLib_MetaDataFeed");
        $soap->handle();
    } catch (SoapFault $f) {
        throw new SoapFault();
    }
}
?>
