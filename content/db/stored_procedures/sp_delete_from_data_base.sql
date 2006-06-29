CREATE OR REPLACE FUNCTION sp_delete_from_data_base() RETURNS VOID AS
$$
BEGIN
    DROP TRIGGER tr_content_all_actions ON content;
    IF (sp_check_so_table('sort_order')) THEN
        DROP TABLE sort_order;
        CREATE TABLE sort_order(entity_id BIGSERIAL);
    END IF;
    IF (sp_check_so_table('sort_order_person')) THEN
        DROP TABLE sort_order_person;
        CREATE TABLE sort_order_person(entity_id BIGSERIAL);
    END IF;
    IF (sp_check_so_table('sort_order_person')) THEN
        DROP TABLE sort_order_group;
        CREATE TABLE sort_order_group(entity_id BIGSERIAL);
    END IF;
    DELETE FROM content;

    CREATE TRIGGER tr_content_all_actions AFTER INSERT OR UPDATE OR DELETE
    ON content
    FOR EACH ROW EXECUTE PROCEDURE tr_content_all_actions();

    DELETE FROM rel_content_container;
    DELETE FROM data_field;
    DELETE FROM person;
    DELETE FROM person_group;
    DELETE FROM "work";
    DELETE FROM acl;
    DELETE FROM collection;
    RETURN;
END;
$$ LANGUAGE 'plpgsql';