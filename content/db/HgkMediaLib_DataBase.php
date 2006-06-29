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
    protected function searchEntityIDs($clauses,$sort_order,$lang,$limit,$session)
    {
        //person search attribute
        $person_search_attributes = array(
            'name'
        );

        //group search attributes ACHTUNG: IMPLEMENTIEREN !!!!!!!

        //get person template information
        $stm  = "SELECT df.id, df.label,df.data_type,tpl.search,tpl.view_edit";
        $stm .= " FROM data_field AS df";
        $stm .= " INNER JOIN template AS tpl ON df.id = tpl.data_field_id";
        $stm .= " WHERE tpl.template_name = '".FRONTEND_SEARCH_TPL."'";
        $stm .= " AND";
        $i = 0;
        foreach ($person_search_attributes AS $attribute) {
            if ($i > 0) {
                $stm .= " OR";
            }
            $stm .= " tpl.search = '".strtolower($attribute)."'";
            $i++;
        }
//         echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $person_attribute_array[$row['search']] = array(
                'search' => $row['search'],
                'label' => $row['label'],
                'data_field_id' =>  $row['id'],
                'data_type' =>  $row['data_type'],
                'view' =>  $row['view_edit']
            );
        }
        $result->free();

        //get content template information
        $stm  = "SELECT df.id, df.label,df.data_type,df.entity,tpl.search,tpl.view_edit";
        $stm .= " FROM data_field AS df";
        $stm .= " INNER JOIN template AS tpl ON df.id = tpl.data_field_id";
        $stm .= " WHERE tpl.template_name = '".FRONTEND_SEARCH_TPL."'";
        $stm .= " AND";
        $i = 0;
        foreach ($clauses AS $clause) {
            if ($i > 0) {
                $stm .= " OR";
            }
            $stm .= " tpl.search = '".strtolower($clause['subject'])."'";
            $i++;
        }
//         echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            foreach ($clauses AS $key => $clause) {
                if ($row['search'] == strtolower($clause['subject'])) {
                    $clauses[$key] = $clause;
                    $clauses[$key]['label'] = $row['label'];
                    $clauses[$key]['data_field_id'] =  $row['id'];
                    $clauses[$key]['data_type'] =  $row['data_type'];
                    $clauses[$key]['view'] =  $row['view_edit'];
                    if (!$row['data_type']) {
                        $clauses[$key]['person'] = $person_attribute_array;
                    }
                }
            }
        }
        $result->free();

        //get sort order information
        $stm  = "SELECT df.entity,df.label,tpl.search";
        $stm .= " FROM data_field AS df";
        $stm .= " INNER JOIN template AS tpl ON df.id = tpl.data_field_id";
        $stm .= " WHERE tpl.template_name = '".FRONTEND_SEARCH_TPL."'";
        $stm .= " AND tpl.search IN ('".strtolower(implode("','",array_keys($sort_order)))."')";
//         echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $soa = $this->getSortOrderAbbr($row['entity']);
            $tmp_sort_order[$soa.'.'.$row['label']] = $sort_order[$row['search']];
        }
        $sort_order  =  $tmp_sort_order;
//         echo '<pre>sort_order:';
//         print_r($sort_order);
//         echo '</pre>';
//         echo '<pre>clauses_content:';
//         print_r($clauses_content);
//         echo '</pre>';
//         echo '<pre>clauses_persons:';
//         print_r($clauses_persons);
//         echo '</pre>';


        $predicate_array = $this->getPredicateArray();
        $i = 0;
        $id_string = '';
        $nbr_of_clauses = count($clauses);
        foreach ($clauses AS $clause) {
            $stm  = "SELECT DISTINCT c.entity_id";
            if ($i == ($nbr_of_clauses-1)) {
                foreach ($sort_order AS $column => $direction) {
                    $stm .= ",".$column;
                }
            }
            $stm .= " FROM content AS c";
            if ($i == ($nbr_of_clauses-1)) {
                $stm .= " INNER JOIN sort_order AS so ON c.entity_id = so.entity_id";
                if ($clause['person']) {
                    $stm .= " LEFT JOIN sort_order_person AS sop ON c.person_id = sop.entity_id";
                }
            }
            if ($clause['person']) {
                $stm .= " INNER JOIN content AS c_per ON c.person_id = c_per.entity_id";
            }
            $content = str_replace(
                '{CONTENT}',
                strtolower(utf8_encode($clause['object'])),
                $predicate_array[utf8_encode($clause['predicate'])]
            );
            $stm .= ' WHERE';
            if (($id_string) && ($clause['connector'] == 'AND')) {
                $stm .= ' c.entity_id IN ('.$id_string.')';
                $stm .= ' '.$clause['connector'];
            }
            $object_array = explode(' ',$clause['object']);

            $stm .= " (";
            $ii = 0;
            foreach ($object_array AS $object) {
                $content = str_replace(
                    '{CONTENT}',
                    strtolower(utf8_encode($object)),
                    $predicate_array[utf8_encode($clause['predicate'])]
                );
                if ($ii > 0) {
                    $stm .= " OR";
                }
                $stm .= " (";
                if (array_key_exists('person',$clause)) {
                    $iii = 0;
                    foreach ($clause['person'] AS $search_name => $values) {
                        if ($iii > 0) {
                            $stm .= " OR";
                        }
                        $stm .= "(";
                        $stm .= "c.data_field_id = ".$clause['data_field_id'];
                        $stm .= " AND c_per.data_field_id = ".$values['data_field_id'];
                        $stm .= " AND c_per.\"".$values['data_type']."\"".$content;
                        $stm .= ")";
                        $iii++;
                    }
                } else {
                    $content = str_replace(
                        '{CONTENT}',
                        strtolower(utf8_encode($object)),
                        $predicate_array[utf8_encode($clause['predicate'])]
                    );
                    $stm .= " c.data_field_id = ".$clause['data_field_id'];
                    $stm .= " AND c.\"".$clause['data_type']."\"".$content;
                }
                $stm .= ")";
                $ii++;
            }
            $stm .= ") ";
            if (!array_key_exists('person',$clause)) {
                $stm .= ' AND (SELECT id FROM language WHERE lang_abbr = \''.$lang.'\') = ANY (c.language_id)';
            }
            if ($i == ($nbr_of_clauses-1)) {
                $stm .= " ORDER BY";
                $iii = 0;
                foreach ($sort_order AS $column => $direction) {
                    if ($iii > 0) {
                        $stm .= ",";
                    }
                    $stm .= " ".$column." ".$direction;
                    $iii++;
                }
            }
//             echo $stm.'<br>';
            $result = $this->db->query($stm);
            $this->handleDbError($result);
            if ($clause['connector'] == 'AND') {
                $id_string = '';
            }
            $iiii = 0;
            while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
                $this->handleDbError($row);
                if ($iiii > 0) {
                        $id_string .= ',';
                    }
                $id_string .= $row['entity_id'];
                $iiii++;
            }
            $i++;
        }
        return explode(',',$id_string);
    }

    private function getSortOrderAbbr($entity)
    {
        if ($entity == 'person_group') {
            $sort_order_abbr = 'sog';
        } else if ($entity == 'person') {
            $sort_order_abbr = 'sop';
        } else {
            $sort_order_abbr = 'so';
        }
        return $sort_order_abbr;
    }

    private function getPredicateArray()
    {
        return $predicate_array = array (
            '=' => " ~* '{CONTENT}'",
            '!=' => " !~* '{CONTENT}'",
            '~' => " ~* '{CONTENT}.*'",
            '!~' => " !~* '{CONTENT}.*'",
            '<=' => " <= '{CONTENT}'",
            '>=' => " >= '{CONTENT}'"
        );
    }

    protected function searchFindInfos($entity_id)
    {
        $find_infos = array();
        $stm  = "SELECT ins.creation_date AS date,tpl.search,c.\"VARCHAR\" AS data,med.urn AS cov";
        $stm .= " FROM template AS tpl";
        $stm .= " INNER JOIN content AS c ON tpl.data_field_id = c.data_field_id";
        $stm .= "   AND tpl.search IN ('keywords','title')";
        $stm .= " INNER JOIN instance AS ins ON c.entity_id = ins.id";
        $stm .= " INNER JOIN set ON ins.id = set.instance_id";
        $stm .= " LEFT JOIN media AS med ON set.id = med.set_id";
        $stm .= "   AND mime_type = 'cov'";
        $stm .= " WHERE c.entity_id = ".$entity_id;
        //echo $stm.'<br>';
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            if ($row['search'] == 'keywords')  {
                $find_infos['collection'] = $row['data'];
            } else {
                $find_infos[$row['search']] = $row['data'];
                if ($row['search'] == 'title')  {
                    $find_infos['description'] = $row['data'];
                }
            }
            $find_infos['date'] = $row['date'];
            $find_infos['cov'] = $row['cov'];
        }
        $result->free();

        return $find_infos;
    }

    protected function searchInformation($work_tree)
    {
        foreach ($work_tree AS $work_id => $instance) {
            $work_data_tree['work'][$work_id]['work_id'] = $work_id;
            $work_data_tree['work'][$work_id] = $this->getEntries($work_id,'work');
            foreach ($instance AS $inst_id => $set) {
                $work_data_tree['work'][$work_id]['instance'][$inst_id]['instance_id'] = $inst_id;
                $work_data_tree['work'][$work_id]['instance'][$inst_id] = $this->getEntries($inst_id,'instance');
                foreach ($set AS $set_id => $set_info) {
                    $work_data_tree['work'][$work_id]['instance'][$inst_id]['set'][$set_id]['set_id'] = $set_id;
                    $work_data_tree['work'][$work_id]['instance'][$inst_id]['set'][$set_id] = $this->getEntries($set_id,'set');
                    $media_array = $this->getMediaData($set_id);
                    $work_data_tree['work'][$work_id]['instance'][$inst_id]['set'][$set_id]['media'][$media_array['media_id']] = $media_array;
                }
            }
        }
        return $work_data_tree;
    }

    private function searchValue($entity_id,$search_values)
    {
        $stm .= "SELECT";
        foreach ($ssearch_values AS $search) {
            
        }
    }

    private function getTemplate($template_name)
    {
        //ACHTUNG: HIER ACL IMPLEMENTIEREN !!!!
        $template = array();
        $stm  = "SELECT tpl.search,tpl.view_edit,df.label,df.id";
        $stm .= " FROM data_field AS df";
        $stm .= " INNER JOIN template AS tpl ON df.id = tpl.data_field_id";
        $stm .= " WHERE tpl.template_name = '".$template_name."'";
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $template[$row['search']][] = array(
                'label' => $row['label'],
                'search' => $row['search'],
                'view_edit' => $row['view_edit'],
                'data_field_id' => $row['id']
            );
        }
        return $template;
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
//             $stm .= ',tmp_set.entity_i d AS set_id,tmp_set.titel AS set_titel,tmp_med.urn';
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

    protected function getEntries($entity_id,$entity)
    {
        $stm  = 'SELECT entity_id,';
        $stm .= ' c."VARCHAR" AS var,';
        $stm .= ' c."INTEGER" AS int,';
        $stm .= ' c."BOOLEAN" AS bool,';
        $stm .= ' c."NUMERIC" AS num,';
        $stm .= ' c."TIME" AS time,';
        $stm .= ' df.label,';
        $stm .= ' tpl.view_edit';
        $stm .= ' FROM content AS c';
        $stm .= ' INNER JOIN data_field AS df';
        $stm .= '     ON c.data_field_id = df.id';
        $stm .= ' INNER JOIN template AS tpl';
        $stm .= '     ON df.id = tpl.data_field_id';
        $stm .= ' WHERE c.entity_id = '.$entity_id;
        $stm .= ' AND df.entity = \''.$entity.'\'';
        $stm .= ' AND tpl.template_name = \''.FRONTEND_SEARCH_TPL.'\'';
//         echo $stm.'<br>';
//         flush();
        $result = $this->db->query($stm);
        $this->handleDbError($result);
        $entry_array = array();
        while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $this->handleDbError($row);
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
            $entry_array[$row['view_edit']] = array(
                'label' => $row['label'],
                'name' =>  $row['view_edit'],
                'value' => $data
            );
        }
        return $entry_array;
    }

    protected function getMediaData($set_id,$session='')
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
}
?>
