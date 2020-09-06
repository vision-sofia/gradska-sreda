CREATE MATERIALIZED VIEW IF NOT EXISTS x_survey.ev_criterion_question AS
SELECT
    q.id as question_id,
    c.subject_id as subject_id
FROM
    x_survey.ev_criterion_definition c
        INNER JOIN
    x_survey.q_answer a ON a.id = c.answer_id
        INNER JOIN
    x_survey.q_question q ON a.question_id = q.id
GROUP BY
    c.subject_id, q.id
;
---
CREATE INDEX ON x_survey.ev_criterion_question(subject_id);
---
CREATE INDEX ON x_survey.ev_criterion_question(question_id)
;
---
CREATE UNIQUE INDEX ON x_survey.ev_criterion_question(subject_id, question_id)
;
---
CREATE OR REPLACE FUNCTION refresh_matview_ev_criterion_question()
    RETURNS TRIGGER LANGUAGE PLPGSQL
AS $$
BEGIN
    REFRESH MATERIALIZED VIEW CONCURRENTLY x_survey.ev_criterion_question;
    RETURN NULL;
END $$
;
---
CREATE TRIGGER trig_refresh_matview_ev_criterion_question
    AFTER INSERT
    OR UPDATE
    OR DELETE
    OR TRUNCATE
ON x_survey.ev_criterion_definition FOR EACH STATEMENT
EXECUTE PROCEDURE refresh_matview_ev_criterion_question()
;
