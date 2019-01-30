CREATE OR REPLACE FUNCTION x_survey.trig_geometry_bi()
    RETURNS trigger AS
$$
DECLARE
    geometry_type text;
BEGIN
    geometry_type := geometrytype(NEW.coordinates);
    IF geometry_type = 'MULTILINESTRING' THEN
        INSERT INTO x_geometry.multiline(geo_object_id, uuid, coordinates, metadata)
            SELECT geo_object_id, uuid, coordinates, metadata FROM (SELECT NEW.*) As foo;
    END IF;
    RETURN NULL;
END;
$$
LANGUAGE 'plpgsql' VOLATILE;
---
CREATE TRIGGER trig_geometry_bi BEFORE INSERT
    ON x_geometry.geometry_base FOR EACH ROW
EXECUTE PROCEDURE x_survey.trig_geometry_bi();
