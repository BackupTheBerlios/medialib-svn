CREATE OR REPLACE FUNCTION sp_insert_data (
    data_label          VARCHAR, -- data field label
    data_value          VARCHAR, -- value
    -- VARCHAR,INTEGER,NUMERIC,(DATE,)TIME,BOOLEAN (je eine funktion fÃ¼r jede Spalte in content!)
    data_type           VARCHAR,
    lang                VARCHAR[],
    table_name          VARCHAR,
    entityID            BIGINT, -- id of the entity the value belongs to; can be NULL
    foreign_key         BIGINT -- key of the entry in parent entity
) RETURNS BIGINT AS
$$
DECLARE
    id_data_field       BIGINT;
    id_language_array   content.language_id%Type;
    id_content          content.id%TYPE;
    id_rel_cont_cont    rel_content_container.id%TYPE;
    id_entity           BIGINT;
    ent_abbr            CHAR(3);

BEGIN
    --create data_field and get created or existing id
    IF data_label IS NOT NULL THEN
        SELECT
        INTO id_data_field
        sp_create_data_field(lower(data_label),table_name,data_type);
    ELSE
        id_data_field := NULL;
    END IF;

    --select language ids (array)
    IF lang[1] IS NOT NULL THEN
        SELECT
        INTO id_language_array
        sp_select_language_id_array(lang);
    ELSE
        id_language_array := '{}';
    END IF;

    --create entity (if id isn't given)
    IF entityID IS NOT NULL THEN
        id_entity := entityID;
    ELSE
        SELECT
        INTO id_entity
        sp_create_new_entity(table_name,foreign_key,NULL);
    END IF;

    --insert content
    IF (table_name <> 'person_group') THEN
        ent_abbr := substring(table_name from 1 for 3);
    ELSE
        ent_abbr := 'gro';
    END IF;

    --casted_value := CAST(data_value AS BOOLEAN);

    SELECT
    INTO id_content
    sp_insert_content(id_entity,id_data_field,id_language_array,data_value,data_type,ent_abbr,NULL);

    RETURN (id_entity);
END;
$$ LANGUAGE 'plpgsql';
--SELECT sp_insert_data('color','rot','VARCHAR','{de}','instance',2,null);
--SELECT sp_insert_data('Geburtstag','person','DATE',NULL,'2005-12-23',34,null);
--SELECT sp_insert_data('name','Eminem','VARCHAR','{"de"}','person',NULL,null);

