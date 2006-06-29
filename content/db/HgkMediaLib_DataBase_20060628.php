<?php
/**
 * central data base class handling the most data base queries occuring in different
 * classes.
 *
 * the intension is to have a base class for extension
 *
 * @package HGKMediaLib
 * @author Franz-Ferdinand Lehnert <franz.lehnert@winet.ch>
 * @version 0.1
 */

require_once('../global/customize.php');

class HGKMediaLib_DataBase
 {
    /**
     * db object
     *
     * @var object
     * @access private
     */

    protected $db;

    /**
     * constructor
     *
     * the constructor handles the data base connection
     *
     * @return void
     */
    function __construct()
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
    }


    /**
     * handles the db errors
     *
     * after avlidating with XMLReader the actual feed uses SimpleXML
     * n.b.: the functions with data base read/write action are
     * often using stored procedures programmed with pgpsql
     *
     * @param obj DB::ERROR
     * @return string error message (if the object is a DB::Error object)
     * @access private
     */
    private function handleDbError($obj)
    {
        if (DB::isError($obj)) {
//            if (DEBUG_FLAG) {
                echo '<pre>db:';
                print_r($this->db);
                echo '</pre>';
                die ($obj->getMessage());
//            } else {
//                throw new SoapFault("HgkMediaLib_DataBase",$obj->getMessage());
//            }
        }
    }

    /**
     * logs a soap session in data base
     *
     * @param string session
     * @param string user name
     * @param string domain
     * @return integer id of table session
     * @access private
     */
    protected function setSoapSession($session,$user,$domain)
    {
        $stm  = "SELECT nextval('session_id_seq') AS id";
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            $id = $row['id'];
        }
        $result->free();
        if ($id) {
            $stm  = "INSERT INTO \"session\" (ID,session_id,session_start";
            $stm .= ",user_name,domain_name)";
            $stm .= "VALUES ('".$id."','".$session."',localtimestamp(0),'".$user."'";
            $stm .= ",'".$domain."');";
            //echo $stm.'<br>';
            $result = $this->db->query($stm);
            $this->handleDbError($result);
        }
        return $id;
    }

    /**
     * logs a soap session in data base
     *
     * @param integer $db_session_id created id of the session table in db
     * @return integer id of table session
     * @access pro
     */
    protected function closeSoapSession($db_session_id)
    {
        $stm  = "UPDATE \"session\"";
        $stm .= " SET session_stop = localtimestamp(0)";
        $stm .= " WHERE id = ".$db_session_id;
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        return true;
    }

    /**
     * gets data field ids and data types of the data fields matching the subject
     * provided by the mediagonal frontend query parameter
     *
     * @param string $field_name: name of the data field provided
     *                  by the mediagonal frontend query parameter
     * @return array $data_type_array - format:
     * array(
     *  'DATATYPE_1' => 'id1,id2,id3,...idn',
     *  'DATATYPE_2' => 'id1,id2,id3,...idn',
     *      ...
     *  'DATATYPE_n' => 'id1,id2,id3,...idn'
     * )
     * @access protected
     */
    protected function getDataFieldParam($field_name)
    {
        $stm  = 'SELECT df.data_type AS type';
        $stm .= ' FROM data_field AS df';
        $stm .= '   INNER JOIN rel_template_field AS rtf';
        $stm .= '       ON df.id = rtf.data_field_id';
        $stm .= ' WHERE lower(rtf.field_name) = \''.strtolower($field_name).'\'';
//         echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        $data_types = array();
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            if (!in_array($row['type'],$data_types)) {
                $data_types[] = $row['type'];
            }
        }
        return $data_types;
    }

    /**
     * gets entity ids matching the mediagonal frontend query parameter
     *
     * @param string $clauses
     * @param string $sort_order value to be sorted by and param 'asc' or 'desc'
     * @param integer $limit number of returned rows
     * @param string $lang language of values
     * @return array id_array
     * @access protected
     */
    protected function getEntityIDs($clauses,$lang='de',$session)
    {
        /*$predicate_array = array(
            '=' => "= '{CONTENT}'",
            '!=' => "<> '{CONTENT}'",
            '~' => "LIKE '%{CONTENT}%'",
            '!~' => "NOT LIKE '%{CONTENT}%'",
            '<=' => "<= '%{CONTENT}%'",
            '>=' => ">= '%{CONTENT}%'"
        );*/

        $predicate_array = $this->getPredicateArray();
        
//         echo '<pre>';
//         print_r($clauses);
//         echo '</pre>';
        $i = 0;
        $id_string = '';
        $id_array = array(
            'work' => array(),
            'instance' => array(),
            'set' => array()
        );

        foreach ($clauses AS $clause) {
            $content = str_replace(
                '{CONTENT}',
                strtolower(utf8_encode($clause['object'])),
                $predicate_array[utf8_encode($clause['predicate'])]
            );
            if (array_key_exists('data_type',$clause)) {
                $stm  = 'SELECT DISTINCT c.entity_id,df.entity';
                $stm .= ' FROM content AS c';
                $stm .= ' LEFT JOIN data_field AS df';
                $stm .= '     ON c.data_field_id = df.id';
                $stm .= ' INNER JOIN rel_template_field AS rtf';
                $stm .= '     ON df.id = rtf.data_field_id';
                $stm .= ' WHERE';
                if (($id_string) && ($clause['connector'] == 'AND')) {
                    $stm .= ' entity_id IN ('.$id_string.')';
                    $stm .= ' '.$clause['connector'];
                }
                $stm .= ' (';
                $ii = 0;
                foreach ($clause['data_type'] AS $type) {
                    if ($ii > 0) {
                        $stm .= " OR";
                    }
                    $stm .= ' (lower(c."'.strtoupper($type).'") '.$content;
                    $stm .= ' AND lower(rtf.field_name) = \''.strtolower(utf8_encode($clause['subject'])).'\')';
                    $ii++;
                }
                $stm .= ')';
                $stm .= ' AND (SELECT id FROM language WHERE lang_abbr = \''.$lang.'\') = ANY (c.language_id)';

            } else {
                $stm  = 'SELECT';
                $stm .= ' DISTINCT c_2.entity_id,c_2.entity_abbr';
                $stm .= ' FROM content AS c_1';
                $stm .= ' INNER JOIN content AS c_2';
                $stm .= '     ON c_1.entity_id = c_2.person_id';
                $stm .= ' WHERE';
                if (($id_string) && ($clause['connector'] == 'AND')) {
                    $stm .= ' c_2.entity_id IN ('.$id_string.')';
                    $stm .= ' '.$clause['connector'];
                }
                $stm .= ' (lower(c_1."VARCHAR") '.$content;
                $stm .= ' AND c_2.data_field_id IS NULL';
                $stm .= ' AND lower(c_2."VARCHAR") = \''.strtolower(utf8_encode($clause['subject'])).'\')';
                $stm .= ' AND (SELECT id FROM language WHERE lang_abbr = \''.$lang.'\') = ANY (c_2.language_id)';

            }
            //echo $stm.'<br>';
            $result = $this->db->query($stm);
            $this->handleDbError($result);
            if ($clause['connector'] == 'AND') {
                $id_string = '';
                $id_array = array(
                    'work' => array(),
                    'instance' => array(),
                    'set' => array()
                );
            }
            $ii = 0;
            while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                $this->handleDbError($row);
                if ($row['entity_abbr']) {
                    if ($row['entity_abbr'] == 'ins') {
                        $entity = 'instance';
                    } else if ($row['entity_abbr'] == 'wor') {
                        $entity = 'work';
                    } else if ($row['entity_abbr'] == 'set') {
                        $entity = 'set';
                    }
                } else if ($row['entity']) {
                    $entity = $row['entity'];
                }
                if (!in_array($row['entity_id'],$id_array[$entity])) {
                    if ($ii > 0) {
                        $id_string .= ',';
                    }
                    $id_string .= $row['entity_id'];
                    $id_array[$entity][] = $row['entity_id'];
                }
                $ii++;
            }
        }

//         echo '<pre>';
//         print_r($id_array);
//         echo '</pre>';
        return $id_array;
    }

    private function getPredicateArray()
    {
        return $predicate_array = array (
            '=' => "~* '{CONTENT}'",
            '!=' => "!~* '{CONTENT}'",
            '~' => "~* '.*{CONTENT}.*'",
            '!~' => "!~* '.*{CONTENT}.*'",
            '<=' => "<= '{CONTENT}'",
            '>=' => ">= '{CONTENT}'"
        );
    }

    protected function createTempTable($entity,$ids,$session)
    {
        //select all data fields for creating table colums
        $stm  = 'SELECT DISTINCT rtf.field_name, df.data_type';
        $stm .= ' FROM data_field AS df';
        $stm .= ' INNER JOIN rel_template_field AS rtf ON rtf.data_field_id = df.id';
        $stm .= ' INNER JOIN template AS tpl ON rtf.template_id = tpl.id';
        $stm .= ' WHERE tpl.name = \''.str_replace('{ENTITY}',$entity,FRONTEND_SEARCH_TPL).'\'';
        $stm .= ' AND rtf.field_name NOT LIKE \'%_start\''; 
        $stm .= ' AND rtf.field_name NOT LIKE \'%_stop\''; 
        //echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        $i = 0;
        //create table
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            if ($i == 0){
                $stm  = 'CREATE TABLE '.$entity.'_'.$session.' (ID serial,entity_id bigint,';
                if ($entity == 'instance') {
                    $stm .= 'work_id bigint,';
                } else if ($entity == 'set') {
                    $stm .= 'instance_id bigint,master_set_id bigint,';
                }
            } else {
                $stm .= ',';
            }
            $stm .= strtolower($row['field_name']).' '.strtolower($row['data_type']);
            $i++;
            $column_array[strtolower($row['field_name'])] = $row['data_type'];
        }
        $result->free();
        $stm .= ', PRIMARY KEY(ID));';
        if ($entity == 'work') {
            $stm .= 'CREATE INDEX index_wID_'.$entity.'_'.$session.' ON '.$entity.'_'.$session.'(entity_id)';
        } else if ($entity == 'instance') {
            $stm .= 'CREATE INDEX index_iID_'.$entity.'_'.$session.' ON '.$entity.'_'.$session.'(entity_id);';
            $stm .= 'CREATE INDEX index_wID_'.$entity.'_'.$session.' ON '.$entity.'_'.$session.'(work_id)';
        } else if ($entity == 'set') {
            $stm .= 'CREATE INDEX index_sID_'.$entity.'_'.$session.' ON '.$entity.'_'.$session.'(entity_id);';
            $stm .= 'CREATE INDEX index_msID_'.$entity.'_'.$session.' ON '.$entity.'_'.$session.'(master_set_id);';
            $stm .= 'CREATE INDEX index_iID_'.$entity.'_'.$session.' ON '.$entity.'_'.$session.'(instance_id)';
        }
        //echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);

        //select person / group roles for adding table columns
        $stm  = 'SELECT "VARCHAR" AS role FROM content WHERE data_field_id IS NULL';
        $stm .= ' AND (person_id IS NOT NULL OR rel_person_group_id IS NOT NULL)';
        $stm .= ' AND entity_id IN ('.implode(',',$ids).')';
        //echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        //create table
        $role_array = array();
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            if (!in_array($row['role'],$role_array)) {
                $role_array[] = $row['role'];
                $stm  = 'ALTER TABLE '.$entity.'_'.$session;
                $stm .= ' ADD column '.$row['role'].' VARCHAR';
                //echo $stm.'<br>';
                $res = $this->db->query($stm);
                $this->handleDbError($res);
            } 
        }
        $result->free();
        

        foreach ($column_array AS $col_name => $data_type) {
            $stm  = 'SELECT c.entity_id';
            if ($entity == 'instance') {
                $stm .= ',i.work_id';
            } else if ($entity == 'set') {
                $stm .= ',s.instance_id,s.master_set_id';
            }
            $stm .= ',c."'.$data_type.'" AS '.$col_name;
            $stm .= ' FROM content AS c';
            if ($entity == 'instance') {
                $stm .= ' INNER JOIN instance AS i ON c.entity_id = i.id';
            } else if ($entity == 'set') {
                $stm .= ' INNER JOIN set AS s ON c.entity_id = s.id';
            }
            $stm .= ' INNER JOIN data_field AS df ON c.data_field_id = df.id';
            $stm .= ' INNER JOIN rel_template_field AS rtf ON df.id = rtf.data_field_id';
            $stm .= ' WHERE lower(rtf.field_name) = \''.strtolower($col_name).'\'';
            $stm .= ' AND c.entity_id IN ('.implode(',',$ids).')';
            $stm .= ' ORDER BY entity_id ASC';
            //echo $stm.'<br>';
            $result = $this->db->query($stm);
            $this->handleDbError($result);
            $i = 0;
            while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                $this->handleDbError($row);
                $column = array();
                $value = array();
                if ($entity == 'work'){
                    if ($row[$col_name]) {
                        $column[] = $col_name;
                        $value[] = $row[$col_name];
                    }
                    $column = array_merge(array('entity_id'),$column);
                    $value = array_merge(array($row['entity_id']),$value);
                } else if ($entity == 'instance') {
                    if ($row[$col_name]) {
                        $column[] = $col_name;
                        $value[] = $row[$col_name];
                    }
                    $column = array_merge(array('entity_id','work_id'),$column);
                    $value = array_merge(array($row['entity_id'],$row['work_id']),$value);
                } else if ($entity == 'set') {
                    if ($row[$col_name]) {
                        $column[] = $col_name;
                        $value[] = $row[$col_name];
                    }
                    $column[] = 'entity_id';
                    $value[] = $row['entity_id'];
                    if ($row['master_set_id']) {
                        $column[] = 'master_set_id';
                        $value[] = $row['master_set_id'];
                    }
                    $column = array_merge(array('instance_id'),$column);
                    $value = array_merge(array($row['instance_id']),$value);
                }
                foreach ($column AS $key => $col) {
                    if (
                        (!is_array($res_array[$row['entity_id']][$col])) ||
                            ((is_array($res_array[$row['entity_id']][$col])) &&
                            (!in_array($value[$key],$res_array[$row['entity_id']][$col])))
                    ) {
                        $res_array[$row['entity_id']][$col][] = $value[$key];
                    }
                }
                $i++;
            }
            $result->free();
        }
        /*echo '<pre>res_array:';
        print_r($res_array);
        echo '</pre>';*/

        //select person / group ids for the selected roles
        if (count($role_array)) {
            $role_string = implode(',',$role_array);
            $role_string = "'".str_replace(",","','",$role_string)."'";
            $stm  = 'SELECT entity_id,"VARCHAR" AS role,person_id, rel_person_group_id';
            $stm .= ' FROM content WHERE "VARCHAR" IN ('.$role_string.')';
            $stm .= ' AND entity_id IN ('.implode(",",array_keys($res_array)).')';
            //echo $stm.'<br>';
            $result = $this->db->query($stm);
            $this->handleDbError($result);
            while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                $this->handleDbError($row);
                //person_id
                if ($row['person_id']) {
                    if (
                        (!is_array($res_array[$row['entity_id']][$row['role']])) ||
                            ((is_array($res_array[$row['entity_id']][$row['role']])) &&
                            (!in_array($row['person_id'],$res_array[$row['entity_id']][$row['role']])))
                    ) {
                        $res_array[$row['entity_id']][$row['role']][] = $row['person_id'];
                    }
                }
                //group_id
                if ($row['rel_person_group_id']) {
                    if (
                        (!is_array($res_array[$row['entity_id']][$row['role']])) ||
                            ((is_array($res_array[$row['entity_id']][$row['role']])) &&
                            (!in_array($row['rel_person_group_id'],$res_array[$row['entity_id']][$row['role']])))
                    ) {
                        $res_array[$row['entity_id']][$row['role']][] = $row['rel_person_group_id'];
                    }
                }
            }
        }
        unset($role_array);
        /*echo '<pre>res_array:';
        print_r($res_array);
        echo '</pre>';*/

        $i = 0;
        foreach ($res_array AS $entity_id => $entries) {
            foreach ($entries AS $column => $value) {
                $entries[$column] = implode(',',$value);
            }
            $columns = array_keys($entries);
            /*echo '<pre>columns:';
            print_r($columns);
            echo '</pre>';*/
            $entity_handler = $this->db->autoPrepare($entity."_".$session, $columns, DB_AUTOQUERY_INSERT);
            $values = array_values($entries);
            /*echo '<pre>values:';
            print_r($values);
            echo '</pre>';*/
            $result = $this->db->execute($entity_handler, $values);
            $this->handleDbError($result);
            $i++;
        }
        unset($entity_handler);
    }

    protected function createTempPersonGroupTables($ids,$session)
    {
        //select person values and prepare arrays to create and fill the temp person table
        $stm  = 'SELECT DISTINCT';
        $stm .= ' c_1.person_id,';
        $stm .= ' c_2."VARCHAR" AS var,';
        $stm .= ' c_2."INTEGER" AS int,';
        $stm .= ' c_2."BOOLEAN" AS bool,';
        $stm .= ' c_2."NUMERIC" AS num,';
        $stm .= ' c_2."TIME" AS time,';
        $stm .= ' rtf.field_name';
        $stm .= ' FROM content AS c_1';
        $stm .= ' INNER JOIN content AS c_2';
        $stm .= '     ON c_1.person_id = c_2.entity_id';
        $stm .= ' INNER JOIN data_field AS df';
        $stm .= '     ON c_2.data_field_id = df.id';
        $stm .= ' INNER JOIN rel_template_field AS rtf';
        $stm .= '     ON rtf.data_field_id = df.id';
        $stm .= ' INNER JOIN template AS tpl';
        $stm .= '     ON rtf.template_id = tpl.id';
        $stm .= ' WHERE c_1.entity_id IN ('.implode(',',$ids).')';
        $stm .= ' AND c_1.data_field_id IS NULL';
        $stm .= ' AND tpl.name = \''.str_replace('{ENTITY}','person',FRONTEND_SEARCH_TPL).'\'';
        $stm .= ' ORDER BY person_id';
        //echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        $person_column_array = array();
        $person_column_array = array();
        $i = 0;
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $data = '';
            $role = $row['role'];
            if ($row['var']) {
                $data = $row['var'];
                $type = 'varchar';
            } else if ($row['int']) {
                $data = $row['int'];
                $type = 'integer';
            } else if ($row['bool']) {
                $data = $row['bool'];
                $type = 'boolean';
            } else if ($row['num']) {
                $data = $row['num'];
                $type = 'numeric';
            } else if ($row['time']) {
                $data = $row['time'];
                $type = 'time';
            }
            $person_column_array = array('person_id bigint');
            $person_title_array = array('person_id');
            if (!in_array($row['field_name'],$person_column_array)) {
                $person_column_array[] = $row['field_name'].' '.$type;
                $person_title_array[] = $row['field_name'];
            }
            $person_value_array[] = array((int)$row['person_id'],$data);
            $i++;
        }
        $result->free();

        //select group values and prepare arrays to create and fill the temp group table
        $stm  = 'SELECT';
        $stm .= ' c_1.rel_person_group_id,';
        $stm .= ' c_2."VARCHAR" AS var,';
        $stm .= ' c_2."INTEGER" AS int,';
        $stm .= ' c_2."BOOLEAN" AS bool,';
        $stm .= ' c_2."NUMERIC" AS num,';
        $stm .= ' c_2."TIME" AS time,';
        $stm .= ' rtf.field_name,';
        $stm .= ' rpg.person_id';
        $stm .= ' FROM content AS c_1';
        $stm .= ' INNER JOIN content AS c_2';
        $stm .= '     ON c_1.rel_person_group_id = c_2.entity_id';
        $stm .= ' INNER JOIN rel_person_group AS rpg';
        $stm .= '     ON c_1.rel_person_group_id = rpg.id';
        $stm .= ' INNER JOIN data_field AS df';
        $stm .= '     ON c_2.data_field_id = df.id';
        $stm .= ' INNER JOIN rel_template_field AS rtf';
        $stm .= '     ON rtf.data_field_id = df.id';
        $stm .= ' INNER JOIN template AS tpl';
        $stm .= '     ON rtf.template_id = tpl.id';
        $stm .= ' WHERE c_1.entity_id IN ('.implode(',',$ids).')';
        $stm .= ' AND c_1.data_field_id IS NULL';
        $stm .= ' AND tpl.name = \''.str_replace('{ENTITY}','person_group',FRONTEND_SEARCH_TPL).'\'';
        $stm .= ' ORDER BY rel_person_group_id';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        $value_array = array();
        $column_array = array();
        $i = 0;
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $data = '';
            $role = $row['role'];
            if ($row['var']) {
                $data = $row['var'];
                $type = 'varchar';
            } else if ($row['int']) {
                $data = $row['int'];
                $type = 'integer';
            } else if ($row['bool']) {
                $data = $row['bool'];
                $type = 'boolean';
            } else if ($row['num']) {
                $data = $row['num'];
                $type = 'numeric';
            } else if ($row['time']) {
                $data = $row['time'];
                $type = 'time';
            }
            $group_column_array = array(
                'rel_person_group_id bigint',
                'person_id bigint[]'
            );
            $group_title_array = array(
                'rel_person_group_id',
                'person_id'
            );
            if (!in_array($row['field_name'],$group_column_array)) {
                $group_column_array[] = $row['field_name'].' '.$type;
                $group_title_array[] = $row['field_name'];
            }
            $group_value_array[] = array(
                $row['rel_person_group_id'],
                $row['person_id'],
                $data
            );
            $i++;
        }
        $result->free();

        //create temp person table
        if (count($person_column_array)) {
            //create table
            $stm  = 'CREATE TABLE person_'.$session.'(ID serial,';
            $stm .= implode(',',$person_column_array).',PRIMARY KEY(ID));';
            $stm .= 'CREATE INDEX index_person_persID_'.$session;
            $stm .= ' ON person_'.$session.'(person_id)';
            $result = $this->db->query($stm);
            $this->handleDbError($result);
            //insert values
            $person_column_array = array_merge(array('entity_id','role'),$person_column_array);
            $person_handler = $this->db->autoPrepare("person_".$session, $person_title_array, DB_AUTOQUERY_INSERT);
            foreach ($person_value_array AS $values) {
                $result = $this->db->execute($person_handler, $values);
                $this->handleDbError($result);
            }
            unset($person_handler);
        }

        //create temp group table
        if (count($group_column_array)) {
            //create table
            $stm  = 'CREATE TABLE person_group_'.$session.'(ID serial,';
            $stm .= implode(',',$group_column_array).',PRIMARY KEY(ID));';
            $stm .= 'CREATE INDEX index_person_group_persID_'.$session;
            $stm .= ' ON person_group_'.$session.'(rel_person_group_id)';
            $result = $this->db->query($stm);
            $this->handleDbError($result);
            //insert values
            $group_column_array = array_merge(array('entity_id','role'),$group_column_array);
            $group_handler = $this->db->autoPrepare("person_group_".$session, $group_title_array, DB_AUTOQUERY_INSERT);
            foreach ($group_value_array AS $values) {
                $result = $this->db->execute($group_handler, $values);
                $this->handleDbError($result);
            }
            unset($group_handler);
        }
    }

    protected function dropTempTables($session)
    {
        $stm  = 'SELECT table_name FROM information_schema.tables';
        $stm .= ' WHERE table_name LIKE \'%_'.$session.'\'';
        //echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        if ($result) {
            while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                $this->handleDbError($row);
                $stm  = 'DROP TABLE '.$row['table_name'];
                //echo $stm.'<br>';
                $res = $this->db->query($stm);
                $this->handleDbError($res);
            }
            $result->free();
        }
    }

    protected function getWorks($table,$ids)
    {
        $stm  = 'SELECT work.id AS wor_id,';// work.creation_date AS wor_date,';
        $stm .= ' instance.id AS ins_id,';// instance.creation_date AS ins_date,';
        $stm .= ' set.id AS set_id, set.master_set_id';//, set.creation_date AS set_date';
        $stm .= ' FROM work';
        $stm .= '   INNER JOIN instance ON work.id = instance.work_id';
        $stm .= '   INNER JOIN set ON instance.id = set.instance_id';
//         $stm .= ' WHERE '.$table.'.id = '.$id;
        $stm .= ' WHERE '.$table.'.id IN ('.implode(',',$ids).')';
        $stm .= ' ORDER BY wor_id,ins_id,set_id';
        //echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        $i = 0;
        $ii = 0;
        $set_array = array();
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            $entity_array[$row['wor_id']] = array(
                $row['ins_id'] => array(
                    $row['set_id'] => array()
                )
            );
        }
        return $entity_array;
    }

    protected function getResult($session,$clauses,$sort_order,$limit,$entity_array)
    {
        require_once('./inc/HgkMediaLib_Struct_Entity.php');
        /*$predicate_array = array(
            '=' => "= '{CONTENT}'",
            '!=' => "<> '{CONTENT}'",
            '~' => "LIKE '%{CONTENT}%'",
            '!~' => "NOT LIKE '%{CONTENT}%'",
            '<=' => "<= '%{CONTENT}%'",
            '>=' => ">= '%{CONTENT}%'"
        );*/

        $predicate_array = $this->getPredicateArray();


        foreach ($entity_array As $work_id => $instance) {
//             $stm  = 'SELECT tmp_ins.entity_id as ins_id,tmp_ins.work_id AS wor_id,tmp_ins.titel AS ins_titel';
//             $stm .= ',tmp_ins.sende_datum,tmp_wor.titel AS wor_titel';
//             $stm .= ',tmp_set.entity_id AS set_id,tmp_set.titel AS set_titel,tmp_med.urn';
//             $stm .= ' FROM instance_'.$session.' AS tmp_ins';
//             $stm .= ' INNER JOIN work_'.$session.' AS tmp_wor ON tmp_ins.work_id = tmp_wor.entity_id';
//             $stm .= ' INNER JOIN set_'.$session.' AS tmp_set ON tmp_ins.entity_id = tmp_set.instance_id';
            $stm  = 'SELECT i.entity_id as id,i.titel AS titel';
            $stm .= ',i.collection,i.sende_datum as date,m.urn';
            $stm .= ' FROM instance_'.$session.' AS i';
            $stm .= ' INNER JOIN set_'.$session.' AS s ON i.entity_id = s.instance_id';
            $i = 0;
            foreach ($clauses AS $clause) {
                if (!$clause['data_type']) {
                    $stm .= ' LEFT JOIN person_'.$session.' AS p_'.$i;
                    $stm .= ' ON i.'.$clause['subject'].' IN (SELECT person_id FROM p_'.$i.')';
//                     $stm .= ' LEFT JOIN person_group_'.$session.' AS pers_group_'.$i;
//                     $stm .= ' ON ins.'.$clause['subject'].' IN (SELECT person_id FROM pers_group__'.$i.')';
                    $i++;
                }
            }
            $stm .= ' LEFT JOIN media AS m ON m.set_id = s.id';
            $stm .= '   AND m.mime_type = \'cov\'';
            $stm .= ' WHERE';
            foreach ($clauses AS $clause) {
                if ($clause['connector']) {
                    $stm .= ' '.$clause['connector'];
                }
                if ($clause['data_type']) {
                    $content = str_replace(
                        '{CONTENT}',
                        /*strtolower(*/utf8_encode($clause['object'])/*)*/,
                        $predicate_array[utf8_encode($clause['predicate'])]
                    );
                    $stm .= ' i.'.strtolower($clause['subject']).' '.$content;
                }
            }
            if (count($sort_order)) {
                $stm .= ' ORDER BY ';
                $i = 0;
                foreach ($sort_order AS $order => $direction) {
                    if ($i > 0) {
                        $stm .= ',';
                    }
                    $stm .= 'i.'.strtolower($order).' '.$direction;
                    $i++;
                }
            }
            //echo $stm.'<br>';
            //$stm .= ' INNER JOIN collection AS col ON col.id = ';
            //$stm .= ' INNER JOIN content AS con';
            $result = $this->db->query($stm);
            $this->handleDbError($result);
            $return_array = array();
            $ins_array = array();
            while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                $this->handleDbError($row);
                if (!in_array($row['id'],$ins_array)){
                    $ins_array[] = $row['id'];
                    $id = $row['id'];
                    $collection = $row['collection'];
                    $collectionId = $row['collection'];
                    $date = $row['date'];
                    $description = '';
                    $title = $row['titel'];
                    $coverMedia = $row['urn'];
                    $return_array[] = new HgkMediaLib_Struct_Entity($collection,$collectionId,$coverMedia,$date,$description,$id,$title);
                }
//                 $displayAttributes = array();
//                 $displayAttributes[$row['wor_id']] = $work;
//                 $displayAttributes[$row['wor_id']]['titel'] = $row['wor_titel'];
//                 $displayAttributes[$row['wor_id']][$row['ins_id']] = array(
//                     'titel' => $row['ins_titel'],
//                 );
//                 $displayAttributes[$row['wor_id']][$row['ins_id']][$row['set_id']] = array(
//                     'titel' => $row['set_titel'],
//                     'coverMedia' => $row['urn'],
//                 );
//                 $result_array[] = array(
//                     'collection' => '',
//                     'collectionId' => '',
//                     'date' => $row['ins_titel'],
//                     'description' => '',
//                     'id' => $row['ins_id'],
//                     'displayAttributes' => $displayAttributes,
//                 );
            }
            return $return_array;
        }
    }

    /**
     * checkes if the field name exists or not
     * if not: field name = 'function'
     *
     * @return array id_array
     * @access protected
     */
    protected function getFunctionFlag($field_name)
    {
        $stm  = "SELECT COUNT(rtf.id) AS nbr FROM rel_template_field AS rtf";
        $stm .= " INNER JOIN template AS t ON rtf.template_id = t.id";
        $stm .= " WHERE rtf.field_name = '".utf8_encode($field_name)."'";
        $stm .= " AND t.name LIKE 'default_%'";
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            if ($row['nbr']) {
                return false;
            } else {
                return true;
            }
        }
    }

    protected function getMainEntries($id,$entity)
    {
        $stm  = 'SELECT';
        $stm .= ' c."VARCHAR" AS var,';
        $stm .= ' c."INTEGER" AS int,';
        $stm .= ' c."BOOLEAN" AS bool,';
        $stm .= ' c."NUMERIC" AS num,';
        $stm .= ' c."TIME" AS time,';
        $stm .= ' rtf.field_name AS label';
        $stm .= ' FROM content AS c';
        $stm .= ' INNER JOIN data_field AS df';
        $stm .= '     ON c.data_field_id = df.id';
        $stm .= ' INNER JOIN rel_template_field AS rtf';
        $stm .= '     ON df.id = rtf.data_field_id';
        $stm .= ' INNER JOIN template AS tpl';
        $stm .= '     ON rtf.template_id = tpl.id';
        $stm .= ' WHERE c.entity_id = '.$id;
        $stm .= ' AND tpl.name = \''.str_replace('{ENTITY}',$entity,FRONTEND_SEARCH_TPL).'\'';
        echo $stm.'<br>';
        flush();
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            if ($row['var']) {
                $entry_array[$row['label']] = $row['var'];
            } else if ($row['int']) {
                $entry_array[$row['label']] = $row['int'];
            } else if ($row['bool']) {
                $entry_array[$row['label']] = $row['bool'];
            } else if ($row['num']) {
                $entry_array[$row['label']] = $row['num'];
            } else if ($row['time']) {
                $entry_array[$row['label']] = $row['time'];
            }
        }
        return $entry_array;
    }

    protected function getEntries($id,$entity)
    {
        $stm  = 'SELECT';
        $stm .= ' c."VARCHAR" AS var,';
        $stm .= ' c."INTEGER" AS int,';
        $stm .= ' c."BOOLEAN" AS bool,';
        $stm .= ' c."NUMERIC" AS num,';
        $stm .= ' c."TIME" AS time,';
        $stm .= ' rtf.field_name AS label';
        $stm .= ' FROM content AS c';
        $stm .= ' INNER JOIN data_field AS df';
        $stm .= '     ON c.data_field_id = df.id';
        $stm .= ' INNER JOIN rel_template_field AS rtf';
        $stm .= '     ON df.id = rtf.data_field_id';
        $stm .= ' INNER JOIN template AS tpl';
        $stm .= '     ON rtf.template_id = tpl.id';
        $stm .= ' WHERE c.entity_id = '.$id;
        $stm .= ' AND tpl.name = \''.str_replace('{ENTITY}',$entity,FRONTEND_SEARCH_TPL).'\'';
//         echo $stm.'<br>';
//         flush();
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
            if ($row['var']) {
                $entry_array[strtolower($row['label'])] = $row['var'];
            } else if ($row['int']) {
                $entry_array[strtolower($row['label'])] = $row['int'];
            } else if ($row['bool']) {
                $entry_array[strtolower($row['label'])] = $row['bool'];
            } else if ($row['num']) {
                $entry_array[strtolower($row['label'])] = $row['num'];
            } else if ($row['time']) {
                $entry_array[strtolower($row['label'])] = $row['time'];
            }
        }
        return $entry_array;
    }

    protected function getMediaData($set_id,$session)
    {
        $stm  = 'SELECT';
        $stm .= ' id,';
        $stm .= ' master_media_id,';
        $stm .= ' urn,';
        $stm .= ' collection_id,';
        $stm .= ' mime_type';
        $stm .= ' FROM media';
        $stm .= ' WHERE set_id = '.$set_id;
//         echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $media_array[$row['id']] = array(
                'media_id' => $row['id'],
                'master_media_id' => $row['master_media_id'],
                'mime_type' => $row['mime_type'],
                'urn' => $row['urn'],
                'collection' => array()
            );
            $coll_id_string = str_replace(array('{','}'),array('',''),$row['collection_id']);
            $coll_id_array = explode(',',$coll_id_string);
            foreach ($coll_id_array AS $id) {
                $media_array[$row['id']]['collection'][$id]['collection_id'] = $id;
                if (!$_SESSION['collection'][$id]) {
                    $coll_array = $this->getEntries($id,'collection');
                    if (count($coll_array) > 0) {
                        foreach ($coll_array as $c_label => $c_value) {
                            $media_array[$row['id']]['collection'][$id][$c_label] = $c_value;
                        }
                        $_SESSION['collection'] = array($id => $coll_array);
                    }
                } else {
                    foreach ($_SESSION['collection'][$id] as $c_label => $c_value) {
                        $media_array[$row['id']]['collection'][$id][$c_label] = $c_value;
                    }
                }
            }
        }
        return $media_array;
    }

    protected function getPersons($entity_id)
    {
        $stm  = 'SELECT';
        $stm .= ' c_1.person_id AS id,';
        $stm .= ' c_2."VARCHAR" AS var,';
        $stm .= ' c_2."INTEGER" AS int,';
        $stm .= ' c_2."BOOLEAN" AS bool,';
        $stm .= ' c_2."NUMERIC" AS num,';
        $stm .= ' c_2."TIME" AS time,';
        $stm .= ' rtf.field_name,';
        $stm .= ' c_1."VARCHAR" AS role';
        $stm .= ' FROM content AS c_1';
        $stm .= ' INNER JOIN content AS c_2';
        $stm .= '     ON c_1.person_id = c_2.entity_id';
        $stm .= ' INNER JOIN data_field AS df';
        $stm .= '     ON c_2.data_field_id = df.id';
        $stm .= ' INNER JOIN rel_template_field AS rtf';
        $stm .= '     ON rtf.data_field_id = df.id';
        $stm .= ' INNER JOIN template AS tpl';
        $stm .= '     ON rtf.template_id = tpl.id';
        $stm .= ' WHERE c_1.entity_id = '.$entity_id;
        $stm .= ' AND c_1.data_field_id IS NULL';
//         $stm .= ' AND (lower(rtf.field_name) = \'name\' OR lower(rtf.field_name) = \'vorname\')';
        $stm .= ' AND tpl.name = \''.str_replace('{ENTITY}','person',FRONTEND_SEARCH_TPL).'\'';
//         echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        $person_array = array();
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $data = '';
            $role = $row['role'];
            if (array_key_exists($role,$person_array)) {
                $data .= ', ';
            }
            if ($row['var']) {
                $data = $row['var'];
            } else if ($row['int']) {
                $data = $row['int'];
            } else if ($row['bool']) {
                $data = $row['bool'];
            } else if ($row['num']) {
                $data = $row['num'];
            } else if ($row['time']) {
                $data = $row['time'];
            }
            $person_array[$role][$row['id']][$row['field_name']] = $data;
        }
//         echo '<pre>';
//         print_r($person_array);
//         echo '</pre>';
        return $person_array;
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

    private function processDateValues($date)
    {

    }

    protected function deleteTmpSessionTables($table_array)
    {
        foreach ($table_array AS $table =>  $tmp_tables) {
            $stm  = "SELECT table_name FROM information_schema.tables";
            $stm .= " WHERE table_name LIKE '".$table."_%'";
            $stm .= " AND table_name NOT IN (".implode(',',$tmp_tables).")";
            $stm .= " AND table_name <> 'person_group'";
            echo $stm.'<br>';
            $result = $this->db->query($stm);
            $this->handleDbError($result);
            while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                $stm  = "DROP TABLE ".$row['table_name'];
                echo $stm.'<br>';
                $res = $this->db->query($stm);
                $this->handleDbError($res);
            }
        }
    }
}
?>
