DROP TRIGGER IF EXISTS trig_refresh_matview_ev_criterion_question
ON x_survey.ev_criterion_definition
;
---
DROP FUNCTION IF EXISTS refresh_matview_ev_criterion_question()
;
---
DROP MATERIALIZED VIEW IF EXISTS x_survey.ev_criterion_question
;
