<?php
foreach ($clauses AS $clause) {
    $content = str_replace('{CONTENT}',' \''.strtolower($clause['object']).'\'',$predicate_array[$clause['predicate']]);
//             echo $content.'<br>';
    if ((!$clause['function_flag']) && (!$clause['person_flag'])) {
        $stm = 'SELECT DISTINCT entity_id';
        $stm .= ' FROM view_entity_entries';
        $stm .= ' WHERE';
        if ($id_string) {
            $stm .= ' entity_id IN ('.$id_string.')';
        }
        $stm .= ' '.$clause['connector'];
        $stm .= ' (';
        $ii = 0;
        foreach ($clause['data_type'] AS $type) {
            if ($ii > 0) {
                $stm .= " OR";
            }
            $stm .= ' lower(entity_'.strtolower($type).') '.$content;
            $stm .= ' AND lower(label) = \''.strtolower($clause['subject']).'\'';
            $ii++;
        }
        $stm .= ')';
    }/* else if (($clause['function_flag']) || ($clause['person_flag'])) {
        $stm = 'SELECT DISTINCT vp.entity_id, vpg.entity_id';
        $stm .= ' FROM view_person AS vp, view_person_group AS vpg';
        $stm .= ' WHERE';
        if ($id_string) {
            $stm .= ' (vp.entity_id IN ('.$id_string.')';
            $stm .= ' OR vpg.entity_id IN ('.$id_string.'))';
        }
        $stm .= ' '.$clause['connector'];
    $stm .= ' (';
    if ((!$clause['function_flag']) && (!$clause['person_flag'])) {
        $ii = 0;
        foreach ($clause['data_type'] AS $type) {
            if ($ii > 0) {
                $stm .= " OR";
            }
            $stm .= ' lower(entity_'.strtolower($type).') '.$content;
            $stm .= ' AND lower(label) = \''.strtolower($clause['subject']).'\'';
            $ii++;
        }
        $stm .= ')';
    } else {
        $ii = 0;
        foreach ($clause['data_type'] AS $type) {
            if ($ii > 0) {
                $stm .= " OR";
            }
            if ($clause['function_flag']) {
                $stm .= '(lower(vp.function_'.strtolower($type).') '.$content.'';
                $stm .= ' OR lower(vpg.function_'.strtolower($type).') '.$content.')';
            } else if ($clause['person_flag']) {
                $stm .= '(lower(vp.person_'.strtolower($type).') '.$content;
                $stm .= ' AND lower(vp.label) = \''.strtolower($clause['subject']).'\')';
                $stm .= ' OR (lower(vpg.group_'.strtolower($type).') '.$content;
                $stm .= ' AND lower(vpg.label) = \''.strtolower($clause['subject']).'\')';
            }
            $ii++;
        }
        $stm .= ')';*/
    echo $stm.'<br>';
    exit;
?>