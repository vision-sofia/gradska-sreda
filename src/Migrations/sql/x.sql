CREATE MATERIALIZED VIEW IF NOT EXISTS x_survey.geo_object_question AS
SELECT
    q.id,
    q.title,
    q.uuid,
    q.has_multiple_answers,
    l.object_type_id,
    s.id as survey_id,
    s.is_active as survey_is_active,
    c.id as survey_category_id,
    (SELECT
        jsonb_agg(row_to_json(z))
    FROM
        (SELECT
             a.id,
             a.uuid,
             a.title,
             a.is_photo_enabled,
             a.is_free_answer,
             a.parent
        FROM
             x_survey.q_answer a
        WHERE
             a.question_id = q.id
        ) z
    ) as answers
FROM
    x_survey.survey_element l
        INNER JOIN
    x_survey.survey_category c ON l.category_id = c.id
        INNER JOIN
    x_survey.q_question q ON q.category_id = c.id
        INNER JOIN
    x_survey.survey s ON c.survey_id = s.id
ORDER BY
    s.id ASC,
    q.id ASC
;