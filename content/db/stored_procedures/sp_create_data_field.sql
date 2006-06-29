CREATE OR REPLACE FUNCTION sp_create_data_field(
    data_field_name     VARCHAR,
    table_name          VARCHAR,
    type_of_data        VARCHAR
) RETURNS BIGINT AS
$$
DECLARE
    id_data_field       BIGINT;
    ent_abbr            CHAR(3);
BEGIN

    -- check if the data field exists
    IF (type_of_data IS NOT NULL) THEN
        SELECT
        INTO id_data_field
        id
        FROM data_field
        WHERE label = data_field_name
        AND entity = table_name
        AND data_type = type_of_data;
    ELSE
        SELECT
        INTO id_data_field
        id
        FROM data_field
        WHERE label = data_field_name
        AND entity = table_name
        AND data_type IS NULL;
    END IF;

    IF (id_data_field IS NULL) THEN
        -- create_new_data_field
        SELECT
        INTO id_data_field
        nextval('entity_id_seq');

        INSERT INTO data_field (id,label,data_type,entity)
        VALUES (id_data_field,data_field_name,type_of_data,table_name);
    END IF;
    RETURN (id_data_field);
END;
$$ LANGUAGE 'plpgsql';