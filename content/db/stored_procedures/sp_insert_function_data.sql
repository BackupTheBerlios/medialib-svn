CREATE OR REPLACE FUNCTION sp_insert_function_data (
    id_person               BIGINT,
    id_rel_person_group     BIGINT,
    function_label          VARCHAR,
    related_entity          VARCHAR,
    rel_entity_id           BIGINT
) RETURNS VOID AS
$$
DECLARE
    id_data_field           BIGINT;
    id_content              BIGINT;
    ent_abbr                CHAR(3);
BEGIN

    IF (related_entity <> 'person_group') THEN
        ent_abbr := substring(related_entity from 1 for 3);
    ELSE
        ent_abbr := 'gro';
    END IF;

    SELECT
    INTO id_data_field
    sp_create_data_field(lower(ent_abbr || '_' || function_label),related_entity,NULL);

    IF id_person IS NOT NULL THEN

        IF (id_data_field IS NOT NULL) THEN
            --check content entry
            SELECT
            INTO id_content
            id
            FROM content
            WHERE person_id = id_person
            AND entity_id = rel_entity_id
            AND data_field_id IS NULL;

            IF (id_content IS NULL) THEN
                EXECUTE 'INSERT INTO content(data_field_id,entity_id,person_id)
                    VALUES(' || id_data_field || ',' || rel_entity_id || ',' || id_person || ')';
            END IF;
        END IF;

    ELSEIF id_rel_person_group IS NOT NULL THEN

        IF (id_data_field IS NOT NULL) THEN
            --check content entry
            SELECT
            INTO id_content
            id
            FROM content
            WHERE rel_person_group_id = id_rel_person_group
            AND entity_id = rel_entity_id
            AND data_field_id IS NULL;

            IF (id_content IS NULL) THEN
                EXECUTE 'INSERT INTO content(data_field_id,entity_id,rel_person_group_id)
                    VALUES(' || id_data_field || ',' || rel_entity_id || ',' || id_rel_person_group || ')';
            END IF;
        END IF;

    END IF;
    RETURN;
END;
$$ LANGUAGE 'plpgsql';