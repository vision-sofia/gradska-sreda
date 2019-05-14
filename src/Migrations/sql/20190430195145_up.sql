CREATE MATERIALIZED VIEW IF NOT EXISTS x_geometry.simplified_geo AS
SELECT
    g.geo_object_id,
    s.tolerance as simplify_tolerance,
    ST_AsGeoJSON(ST_Simplify(g.coordinates::geometry, s.tolerance, true)) geometry,
    g.coordinates::geometry
FROM
    x_geometry.geometry_base g
        CROSS JOIN
    x_geospatial.simplify s
;
---
CREATE INDEX ON x_geometry.simplified_geo (simplify_tolerance)
;
---
CREATE INDEX ON x_geometry.simplified_geo (geo_object_id)
;
---
CREATE INDEX ON x_geometry.simplified_geo USING GIST (coordinates)
;