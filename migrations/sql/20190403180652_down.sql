DROP TRIGGER IF EXISTS trig_refresh_matview_geo_object_question
ON x_survey.q_question
;
---
DROP FUNCTION IF EXISTS refresh_matview_geo_object_question()
;
---
DROP MATERIALIZED VIEW IF EXISTS x_survey.geo_object_question
;
