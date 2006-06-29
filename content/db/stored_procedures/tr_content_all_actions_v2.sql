
-- This function returns true, if the column exists in the given no_table.

CREATE OR REPLACE FUNCTION sp_check_so_column(
    no_table    VARCHAR,
    no_column   VARCHAR
)
RETURNS BOOLEAN
AS $$
DECLARE
    query_str   VARCHAR;
    res_row     RECORD;
BEGIN
    query_str := 'SELECT table_name FROM information_schema.columns
        WHERE column_name = ' || quote_literal(lower(no_column));
    FOR res_row IN EXECUTE query_str
    LOOP
        IF (res_row.table_name = no_table) THEN
            RETURN TRUE;
        END IF;
    END LOOP;
    RETURN FALSE;
END;
$$ LANGUAGE 'plpgsql';


-- This function returns true, if the column exists in the given no_tablele.

CREATE OR REPLACE FUNCTION sp_select_role_entry(
    no_table    VARCHAR,
    no_column   VARCHAR,
    id_entity   BIGINT
)
RETURNS BIGINT[]
AS $$
DECLARE
    query_str   VARCHAR;
    res_row     RECORD;
BEGIN
    query_str := 'SELECT ' || quote_ident(no_column)
        || ' AS data FROM ' || quote_ident(no_table)
        || ' WHERE entity_id = ' || id_entity;
    FOR res_row IN EXECUTE query_str
    LOOP
        IF (res_row.data IS NOT NULL) THEN
            RETURN res_row.data;
        END IF;
    END LOOP;
    RETURN ('{}');
END;
$$ LANGUAGE 'plpgsql';


-- This function returns true, if the value in given no_tablele for the given column and
-- the row with given entity_id is null.

--DROP FUNCTION sp_get_so_action(varchar,varchar,bigint);
CREATE OR REPLACE FUNCTION sp_get_so_action(
    no_table    VARCHAR,
    no_label    VARCHAR,
    id_entity   BIGINT
)
RETURNS VARCHAR
AS $$
DECLARE
    id_query_str        VARCHAR;
    id_record           RECORD;
BEGIN
    id_query_str := 'SELECT COUNT(entity_id) AS counter FROM ' || quote_ident(no_table) || ' WHERE entity_id = '
        || id_entity;
    FOR id_record IN EXECUTE id_query_str LOOP
        IF (id_record.counter > 0) THEN
            RETURN 'update';
        ELSE
            RETURN 'insert';
        END IF;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';


-- This function updates a value in a row of a sort order no_tablele
-- with a given entity_id

CREATE OR REPLACE FUNCTION sp_update_so_entry(
    no_table    VARCHAR,
    no_label    VARCHAR,
    new_data    ANYELEMENT,
    id_entity   BIGINT
)
RETURNS VOID
AS $$
DECLARE
BEGIN
    EXECUTE ' UPDATE  ' || quote_ident(no_table)
        || ' SET ' || quote_ident(no_label) ||
        ' = ' || quote_literal(new_data)
        || ' WHERE entity_id = ' || id_entity;
    RETURN;
END;
$$ LANGUAGE 'plpgsql';


-- This function inserts a row in a sort order no_tablele.

CREATE OR REPLACE FUNCTION sp_insert_so_entry(
    no_table    VARCHAR,
    no_label    VARCHAR,
    new_data    ANYELEMENT,
    id_entity   BIGINT
)
RETURNS VOID
AS $$
DECLARE
BEGIN
    EXECUTE 'INSERT INTO ' || quote_ident(no_table) || ' (entity_id,'
        || quote_ident(no_label) || ') VALUES (' || id_entity || ','
        || quote_literal(new_data) || ')';
    RETURN;
    RETURN;
END;
$$ LANGUAGE 'plpgsql';


-- This function inserts a row in a sort order no_tablele.

-- DROP FUNCTION sp_check_black_list(varchar,integer);
CREATE OR REPLACE FUNCTION sp_check_black_list(
    content_data    VARCHAR,
    id_language     INTEGER[]
)
RETURNS BOOLEAN
AS $$
DECLARE
    id_black_list   INTEGER;
BEGIN
    SELECT
    INTO id_black_list
    id FROM search_black_list
    WHERE search_value = content_data
    AND language_id  = ANY(id_language);

    IF (id_black_list IS NULL) THEN
        RETURN TRUE;
    ELSE
        RETURN FALSE;
    END IF;
END;
$$ LANGUAGE 'plpgsql';


-- This function inserts a row in a sort order no_tablele.

-- DROP FUNCTION sp_insert_search_entry(varchar,bigint,bigint[],bigint);
CREATE OR REPLACE FUNCTION sp_insert_search_entry(
    content_data    VARCHAR,
    id_entity       BIGINT,
    id_language     INTEGER[],
    id_data_field   BIGINT
)
RETURNS VOID
AS $$
DECLARE
    id_search_word              BIGINT;
    id_rel_search_word_entity   BIGINT;
    entity                      BIGINT;
    noe                         INTEGER;
BEGIN
    IF (sp_check_black_list(content_data,id_language)) THEN
        SELECT
        INTO id_search_word
        id FROM search_words
        WHERE search_value = content_data
        AND language_id  = ANY (id_language);

        IF (id_search_word IS NULL) THEN
            noe := array_upper(id_language,1);
            FOR i IN 1 .. noe LOOP
                SELECT
                INTO id_search_word
                nextval('search_words_id_seq');

                INSERT INTO search_words(id,search_value,language_id)
                VALUES(id_search_word,content_data,id_language[i]);

                INSERT INTO rel_search_word_entity(entity_id,search_word_id,data_field_id)
                VALUES(id_entity,id_search_word,id_data_field);
            END LOOP;
        ELSE
            SELECT
            INTO entity
            entity_id FROM rel_search_word_entity
            WHERE  search_word_id = id_search_word;

            IF (entity IS NULL) THEN
                INSERT INTO rel_search_word_entity(entity_id,search_word_id,data_field_id)
                VALUES(id_entity,id_search_word,id_data_field);
            END IF;
        END IF;
    END IF;
    RETURN;
END;
$$ LANGUAGE 'plpgsql';

-- DROP FUNCTION sp_create_content_data_array(varchar);
-- CREATE OR REPLACE FUNCTION sp_create_content_data_array(
--     content_data    VARCHAR
-- )
-- RETURNS VARCHAR[]
-- AS $$
-- DECLARE
--     content     VARCHAR;
-- BEGIN
--     content := regexp_replace(content_data,'[[:space:]]+',' ');
-- --     content := regexp_replace(content,'[[:punct:][^:][^-]]','');
--     RETURN string_to_array(content,' ');
-- END;
-- $$ LANGUAGE 'plpgsql';


-- Trigger that calls the function sp_update_order_entry to remove a column from
-- the no_tablele sort_order.

CREATE OR REPLACE FUNCTION tr_content_all_actions()
RETURNS trigger
AS $$
DECLARE
    no_label            VARCHAR; -- no_label name of the entry
    no_entity           VARCHAR; -- name of the entity the entry belongs to
    no_table            VARCHAR; -- name of the sort order no_tablele
    no_data_type        VARCHAR;
    query_str           VARCHAR;
    rows                INTEGER;
    entry_flag          BOOLEAN;
    id_data_field       BIGINT;
    action              VARCHAR;
BEGIN
    IF (TG_OP != 'DELETE') THEN
        id_data_field := NEW.data_field_id;
    ELSE
        id_data_field := OLD.data_field_id;
    END IF ;

    --set no_table
    SELECT
    INTO no_entity
    entity
    FROM data_field
    WHERE id = id_data_field;

    IF (no_entity IS NOT NULL) THEN
        IF (no_entity = 'person') THEN
            no_table := 'sort_order_person';
        ELSEIF (no_entity = 'person_group') THEN
            no_table := 'sort_order_group';
        ELSE
            no_table := 'sort_order';
        END IF;
    ELSE
        no_table := 'sort_order';
    END IF;

    --set no_label
    SELECT
    INTO no_label
    lower(label)
    FROM data_field
    WHERE id = id_data_field;

    --set name of data type
    SELECT
    INTO no_data_type
    data_type
    FROM data_field
    WHERE id = id_data_field;

    IF (TG_OP = 'UPDATE') THEN
        IF (no_data_type IS NULL) THEN
            DECLARE
                id_person_group     BIGINT;
                id_entity           BIGINT := NEW.entity_id;
                pg_array            BIGINT[] := '{}';
                new_pg_array        BIGINT[] := '{}';
            BEGIN
                IF (NEW.person_id IS NOT NULL) THEN
                    id_person_group := NEW.person_id;
                ELSEIF (NEW.rel_person_group_id IS NOT NULL) THEN
                    id_person_group := NEW.rel_person_group_id;
                END IF;

                pg_array := sp_select_role_entry(no_table,no_label,id_entity);

                new_pg_array := array_append(pg_array,id_person_group);

                EXECUTE 'UPDATE sort_order SET ' || quote_ident(no_label) ||' = \'{' || array_to_string(new_pg_array,',') || '}\'
                    WHERE entity_id = ' || id_entity;
            END;
        ELSE
            IF (NEW."VARCHAR" IS NOT NULL) THEN
                IF (NEW."VARCHAR" <> OLD."VARCHAR") THEN
                    PERFORM sp_update_so_entry(no_table,no_label,NEW."VARCHAR",NEW.entity_id);
                END IF;
            ELSEIF (NEW."INTEGER" IS NOT NULL) THEN
                IF (NEW."INTEGER" <> OLD."INTEGER") THEN
                    PERFORM sp_update_so_entry(no_table,no_label,NEW."INTEGER",NEW.entity_id);
                END IF;
            ELSEIF (NEW."TIME" IS NOT NULL) THEN
                IF (NEW."TIME" <> OLD."TIME") THEN
                    PERFORM sp_update_so_entry(no_table,no_label,NEW."TIME",NEW.entity_id);
                END IF;
            ELSEIF (NEW."BOOLEAN" IS NOT NULL) THEN
                IF (NEW."BOOLEAN" <> OLD."BOOLEAN") THEN
                    PERFORM sp_update_so_entry(no_table,no_label,NEW."BOOLEAN",NEW.entity_id);
                END IF;
            ELSEIF (NEW."NUMERIC" IS NOT NULL) THEN
                IF (NEW."NUMERIC" <> OLD."NUMERIC") THEN
                    PERFORM sp_update_so_entry(no_table,no_label,NEW."NUMERIC",NEW.entity_id);
                END IF;
            END IF;
        END IF;
        RETURN NEW;
    ELSEIF (TG_OP = 'INSERT') THEN
        IF (no_data_type IS NULL) THEN
            DECLARE
                id_person_group     BIGINT;
                id_entity           BIGINT := NEW.entity_id;
                nor                 INTEGER;
                pg_array            BIGINT[] := '{}';
                new_pg_array        BIGINT[] := '{}';
            BEGIN
                IF (NEW.person_id > 0) THEN
                    id_person_group := NEW.person_id;
                ELSEIF (NEW.rel_person_group_id > 0) THEN
                    id_person_group := NEW.rel_person_group_id;
                END IF;

                IF NOT (sp_check_so_column('sort_order',no_label)) THEN
                    PERFORM sp_insert_so_column('sort_order',no_label,'BIGINT[]');
                    new_pg_array[1] := id_person_group;
                ELSE
                    pg_array := sp_select_role_entry(no_table,no_label,id_entity);

                    new_pg_array := array_append(pg_array,id_person_group);
                END IF;

                IF (sp_check_so_table('sort_order')) THEN
                    SELECT
                    INTO nor
                    count(entity_id)
                    FROM sort_order
                    WHERE entity_id = id_entity;
                ELSE
                    nor := 0;
                END IF;

                IF (nor = 0) THEN
                    EXECUTE 'INSERT INTO ' || quote_ident(no_table) || ' (entity_id,' || quote_ident(no_label) || ') VALUES ('
                        || id_entity || ',\'{' || array_to_string(new_pg_array,',') || '}\')';
                ELSEIF (nor > 0) THEN
                    EXECUTE 'UPDATE sort_order SET ' || quote_ident(no_label) || ' = \'{' || array_to_string(new_pg_array,',')
                    || '}\' WHERE entity_id = ' || id_entity;
                END IF;
            END;
        ELSE
            IF (sp_check_so_column(no_table,no_label)) THEN
                action := sp_get_so_action(no_table,no_label,NEW.entity_id);
            END IF;
            IF (NEW."VARCHAR" IS NOT NULL) THEN
                IF (action = 'update') THEN
                    PERFORM sp_update_so_entry(no_table,no_label,NEW."VARCHAR",NEW.entity_id);
                ELSEIF (action = 'insert') THEN
                    PERFORM sp_insert_so_entry(no_table,no_label,NEW."VARCHAR",NEW.entity_id);
                END IF;
            ELSEIF (NEW."INTEGER" IS NOT NULL) THEN
                IF (action = 'update') THEN
                    PERFORM sp_update_so_entry(no_table,no_label,NEW."INTEGER",NEW.entity_id);
                ELSEIF (action = 'insert') THEN
                    PERFORM sp_insert_so_entry(no_table,no_label,NEW."INTEGER",NEW.entity_id);
                END IF;
            ELSEIF (NEW."TIME" IS NOT NULL) THEN
                IF (action = 'update') THEN
                    PERFORM sp_update_so_entry(no_table,no_label,NEW."TIME",NEW.entity_id);
                ELSEIF (action = 'insert') THEN
                    PERFORM sp_insert_so_entry(no_table,no_label,NEW."TIME",NEW.entity_id);
                END IF;
            ELSEIF (NEW."BOOLEAN" IS NOT NULL) THEN
                IF (action = 'update') THEN
                    PERFORM sp_update_so_entry(no_table,no_label,NEW."BOOLEAN",NEW.entity_id);
                ELSEIF (action = 'insert') THEN
                    PERFORM sp_insert_so_entry(no_table,no_label,NEW."BOOLEAN",NEW.entity_id);
                END IF;
            ELSEIF (NEW."NUMERIC" IS NOT NULL) THEN
                IF (action = 'update') THEN
                    PERFORM sp_update_so_entry(no_table,no_label,NEW."NUMERIC",NEW.entity_id);
                ELSEIF (action = 'insert') THEN
                    PERFORM sp_insert_so_entry(no_table,no_label,NEW."NUMERIC",NEW.entity_id);
                END IF;
            END IF;
        END IF;
        RETURN NEW;
    ELSEIF (TG_OP = 'DELETE') THEN
        IF (no_data_type IS NULL) THEN
            DECLARE
                id_person_group     BIGINT;
                id_entity           BIGINT := OLD.entity_id;
                pg_array            BIGINT[] := '{}';
                new_pg_array        BIGINT[] := '{}';
                nbr_of_elements     INTEGER;
            BEGIN
                IF (OLD.person_id IS NOT NULL) THEN
                    id_person_group := OLD.person_id;
                ELSEIF (OLD.rel_person_group_id IS NOT NULL) THEN
                    id_person_group := OLD.rel_person_group_id;
                END IF;

                IF (sp_check_so_column(no_table,no_label)) THEN
                    pg_array := sp_select_role_entry(no_table,no_label,id_entity);

                    nbr_of_elements := array_upper(pg_array,1);

                    IF (nbr_of_elements > 1) THEN
                        FOR i IN 1 .. nbr_of_elements LOOP
                            IF (pg_array[i] != id_person_group) THEN
                                new_pg_array = array_append(new_pg_array,pg_array[i]);
                            END IF;
                        END LOOP;
                        EXECUTE 'UPDATE sort_order
                            SET ' || quote_ident(no_label) || ' = \'{' || array_to_string(new_pg_array,',') || '}\'
                            WHERE entity_id = ' || id_entity;
                    ELSE
                        EXECUTE 'UPDATE sort_order
                            SET ' || quote_ident(no_label) || ' = NULL
                            WHERE entity_id = ' || id_entity;
                    END IF;
                END IF;
            END;
        ELSE
            SELECT INTO rows
            COUNT(id) FROM content
            WHERE entity_id = OLD.entity_id;

            IF (rows > 1) THEN
                IF (sp_check_so_column(no_table,no_label)) THEN
                    EXECUTE 'UPDATE ' || quote_ident(no_table)
                        || ' SET ' || quote_ident(no_label) || ' = NULL
                        WHERE entity_id = ' || OLD.entity_id;
                END IF;
            ELSE
                IF (sp_check_so_column(no_table,no_label)) THEN
                    EXECUTE 'DELETE FROM ' || quote_ident(no_table)
                        ||' WHERE entity_id = ' || OLD.entity_id;
                END IF;
            END IF;
        END IF;
        RETURN NEW;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

-- DROP TRIGGER tr_content_all_actions ON content;
--
-- CREATE TRIGGER tr_content_all_actions AFTER INSERT OR UPDATE OR DELETE
-- ON content
-- FOR EACH ROW EXECUTE PROCEDURE tr_content_all_actions();