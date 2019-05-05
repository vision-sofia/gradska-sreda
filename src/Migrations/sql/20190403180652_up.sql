CREATE MATERIALIZED VIEW IF NOT EXISTS x_survey.geo_object_question AS
SELECT
    q.id as question_id,
    q.title as question_title,
    q.uuid as question_uuid,
    q.has_multiple_answers as question_has_multiple_answers,
    l.object_type_id as geo_object_type_id,
    s.id as survey_id,
    s.is_active as survey_is_active,
    c.id as survey_category_id,
    (SELECT
         jsonb_agg(row_to_json(z))
    FROM
        (WITH RECURSIVE rel_tree as (
            SELECT
                p.id,
                p.uuid,
                p.title,
                p.is_photo_enabled,
                p.is_free_answer,
                p.parent,
                array[id] as path
            FROM
                 x_survey.q_answer p
            WHERE
                p.parent is null
                AND p.question_id = q.id

            UNION ALL

            SELECT
                c.id,
                c.uuid,
                c.title,
                c.is_photo_enabled,
                c.is_free_answer,
                c.parent,
                p.path||c.parent as path
            FROM
                x_survey.q_answer c
                    JOIN
                rel_tree p on c.parent = p.id
            WHERE
                p.parent is null
                AND c.question_id = q.id
        )
        SELECT
            a.id,
            a.uuid,
            a.title,
            a.is_photo_enabled,
            a.is_free_answer,
            a.parent
        FROM
            rel_tree a
        ORDER BY
            path, id ASC
        ) z
    ) as question_answers
FROM
    x_survey.survey_element l
        INNER JOIN
    x_survey.survey_category c ON l.category_id = c.id
        INNER JOIN
    x_survey.q_question q ON q.category_id = c.id
        INNER JOIN
    x_survey.survey s ON c.survey_id = s.id
;
---
CREATE INDEX ON x_survey.geo_object_question(geo_object_type_id)
;
---
CREATE OR REPLACE FUNCTION refresh_matview_geo_object_question()
    RETURNS TRIGGER LANGUAGE PLPGSQL
AS $$
BEGIN
    REFRESH MATERIALIZED VIEW x_survey.geo_object_question;
    RETURN NULL;
END $$
;
---
CREATE TRIGGER trig_refresh_matview_geo_object_question
    AFTER INSERT
        OR UPDATE
        OR DELETE
        OR TRUNCATE
    ON x_survey.q_question FOR EACH STATEMENT
EXECUTE PROCEDURE refresh_matview_geo_object_question()
;