
-- This stored procedure checks if a sort order table exists or not.

CREATE OR REPLACE FUNCTION sp_check_so_table(
    no_table    VARCHAR
) RETURNS BOOLEAN AS
$$
DECLARE
    query_str   VARCHAR;
    res_row     RECORD;
BEGIN
    query_str := 'SELECT table_name FROM information_schema.tables
        WHERE table_name = ' || quote_literal(no_table);
    FOR res_row IN EXECUTE query_str
    LOOP
        IF (res_row.table_name IS NOT NULL) THEN
            RETURN true;
        ELSE
            RETURN false;
        END IF;
    END LOOP;
    RETURN false;
END;
$$ LANGUAGE 'plpgsql';



-- This stored procedure creates a sort order table. This table contains all
-- data fields and there content a query can be sorted by.
-- If the table doesn't exists it will be ceated. Otherwise only a column with the
-- right datatype will be added.

CREATE OR REPLACE FUNCTION sp_insert_so_column(
    no_table    VARCHAR,
    no_label    VARCHAR,
    no_type     VARCHAR
) RETURNS VOID AS
$$
DECLARE
    sort_order_table    VARCHAR;
    type_of_data        VARCHAR;
BEGIN
    IF (no_type IS NULL) THEN
        type_of_data := 'VARCHAR';
    ELSE
        type_of_data := no_type;
    END IF;

    --if not create them
    IF NOT (sp_check_so_table(no_table)) THEN
        EXECUTE 'CREATE TABLE ' || quote_ident(no_table)
            || ' (entity_id BIGINT NOT NULL,creation_date DATE,PRIMARY KEY (entity_id))';
--         IF (no_table = 'sort_order') THEN
--             ALTER TABLE sort_order
--             ADD COLUMN person_id BIGINT[] NULL;
--
--             ALTER TABLE sort_order
--             ADD COLUMN rel_person_group_id BIGINT[] NULL;
--         END IF;
    END IF;

    --add new column
    IF NOT (sp_check_so_column(no_table,no_label)) THEN
        EXECUTE 'ALTER TABLE ' || quote_ident(no_table)
            || ' ADD COLUMN ' || quote_ident(no_label)
            || ' ' || type_of_data || ' NULL';
    END IF;
    RETURN;
END;
$$ LANGUAGE 'plpgsql';



-- This stored procedure renames a column of a sort order table.

CREATE OR REPLACE FUNCTION sp_update_so_column(
    no_table     VARCHAR,
    old_column   VARCHAR,
    new_column   VARCHAR
) RETURNS VOID AS
$$
DECLARE
    sort_order_table    VARCHAR;
BEGIN
    --check existens of table
    IF (sp_check_so_table(no_table)) THEN
        --check existence of column
        IF (sp_check_so_column(no_table,old_column)) THEN
            EXECUTE 'ALTER TABLE ' || quote_ident(no_table)
            || ' RENAME COLUMN ' || quote_ident(old_column)
            || ' TO ' || quote_ident(new_column);
        END IF;
    END IF;
    RETURN;
END;
$$ LANGUAGE 'plpgsql';



-- This stored procedure removes a column from the table sort_order if it exists.

CREATE OR REPLACE FUNCTION sp_delete_so_column(
    no_table     VARCHAR,
    no_label     VARCHAR
) RETURNS VOID AS
$$
DECLARE
BEGIN
    -- check existence of the column in a table and remove the column
    IF (sp_check_so_column(no_table,no_label)) THEN
        EXECUTE 'ALTER TABLE ' || quote_ident(no_table) || ' DROP COLUMN '
        || quote_ident(no_label);
    END IF;
    RETURN;
END;
$$ LANGUAGE 'plpgsql';



-- This stored procedure removes a column from the table sort_order if it exists.

CREATE OR REPLACE FUNCTION sp_insert_default_template_entry(
    no_template     VARCHAR,
    no_field        VARCHAR,
    id_data_field   BIGINT,
    id_template     BIGINT
) RETURNS VOID AS
$$
DECLARE
BEGIN
    EXECUTE 'INSERT INTO template (template_id,template_name,data_field_id,search,view_edit)
        VALUES(' || id_template || ',' || quote_literal(no_template) || ',' || id_data_field
        || ',' || quote_literal(no_field) || ',' || quote_literal(no_field) || ')';
    RETURN;
END;
$$ LANGUAGE 'plpgsql';



-- Trigger that calls the function sp_update_order_entry to remove a column from
-- the table sort_order.

CREATE OR REPLACE FUNCTION tr_data_field_all_actions()
RETURNS trigger
AS $$
DECLARE
    entity      VARCHAR;
    no_table    VARCHAR;
BEGIN
    --set tab
    IF (TG_OP = 'DELETE') THEN
       entity := OLD.entity;
    ELSE
       entity := NEW.entity;
    END IF;

    IF (entity = 'person') THEN
        no_table := 'sort_order_person';
    ELSEIF (entity = 'person_group') THEN
        no_table := 'sort_order_group';
    ELSE
        no_table := 'sort_order';
    END IF;

    IF (TG_OP = 'UPDATE') THEN
        IF (NEW.label <> OLD.label) THEN
            EXECUTE sp_update_so_column(no_table,lower(OLD.label),lower(NEW.label));
        END IF;
        RETURN NEW;
    ELSEIF (TG_OP = 'INSERT') THEN
        EXECUTE sp_insert_so_column(no_table,lower(NEW.label),lower(NEW.data_type));
        EXECUTE sp_create_acl_entry('u',NEW.id,'INSERT','*/*',true,false,false,false,false,false);
        EXECUTE sp_insert_default_template_entry('default_template',NEW.label,NEW.id,1);
        EXECUTE sp_insert_default_template_entry('frontend_search_template',NEW.label,NEW.id,2);
        RETURN NEW;
    ELSEIF (TG_OP = 'DELETE') THEN
        EXECUTE sp_delete_so_column(no_table,lower(OLD.label));
        RETURN NEW;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

-- DROP TRIGGER tr_data_field_all_actions ON data_field;
--
-- CREATE TRIGGER tr_data_field_all_actions AFTER INSERT OR UPDATE OR DELETE
-- ON data_field
-- FOR EACH ROW EXECUTE PROCEDURE tr_data_field_all_actions();