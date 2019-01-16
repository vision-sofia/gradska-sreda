DROP TRIGGER IF EXISTS REFRESH_MAT_VIEW_EV_CRITERION_QUESTION
ON x_survey.ev_criterion_definition;
---
DROP FUNCTION IF EXISTS refresh_mat_view_ev_criterion_question();
---
DROP MATERIALIZED VIEW IF EXISTS x_survey.ev_criterion_question;
