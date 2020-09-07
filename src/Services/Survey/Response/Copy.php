<?php

namespace App\Services\Survey\Response;

use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;

class Copy
{
    private Connection  $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    public function proceed(int $geoObjectId, int $userId): void
    {
        $this->conn->beginTransaction();

        $sourceGeoObjectId = $this->findSourceGeoObjectId($geoObjectId, $userId);

        if ($sourceGeoObjectId === null) {
            return;
        }

        $stmt = $this->conn->prepare('
            INSERT INTO x_survey.response_location (
                user_id,
                geo_object_id,
                uuid,
                survey_id
            )
            SELECT
                :user_id,
                :geo_object_id,
                :uuid,
                survey_id
            FROM
                x_survey.response_location
            WHERE
                geo_object_id = :source_geo_object_id
        ');

        $stmt->bindValue('user_id', $userId);
        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->bindValue('uuid', Uuid::uuid4());
        $stmt->bindValue('source_geo_object_id', $sourceGeoObjectId);
        $stmt->execute();

        $locationId = $this->conn->lastInsertId();

        $referenceQuestionStmt = $this->conn->prepare('
            SELECT
                q.id
            FROM
                x_survey.response_question q
                    INNER JOIN
                x_survey.response_location l ON q.location_id = l.id
            WHERE
                l.geo_object_id = :geo_object_id
                AND l.user_id = :user_id
        ');

        $referenceQuestionStmt->bindValue('geo_object_id', $sourceGeoObjectId);
        $referenceQuestionStmt->bindValue('user_id', $userId);
        $referenceQuestionStmt->execute();

        $targetQuestionStmt = $this->conn->prepare('
            INSERT INTO x_survey.response_question (
                user_id,
                location_id,
                question_id,
                geo_object_id,
                answered_at,
                updated_at,
                is_latest,
                uuid,
                is_completed
            )
            SELECT
                user_id,
                :location_id,
                question_id,
                :geo_object_id,
                answered_at,
                updated_at,
                true,
                :uuid,
                is_completed
            FROM
                x_survey.response_question q
            WHERE
                q.id = :question_id
        ');

        $referenceAnswersStmt = $this->conn->prepare('
            SELECT
                id
            FROM
                x_survey.response_answer
            WHERE
                question_id = :question_id
        ');

        $stmtCopyAnswersStmt = $this->conn->prepare('
            INSERT INTO x_survey.response_answer (
                question_id,
                answer_id,
                explanation,
                uuid,
                is_completed,
                answered_at
            )
            SELECT
                :question_id,
                answer_id,
                explanation,
                :uuid,
                is_completed,
                answered_at
            FROM
                x_survey.response_answer
            WHERE
                id = :answer_id
        ');

        while ($question = $referenceQuestionStmt->fetch(\PDO::FETCH_OBJ)) {
            $targetQuestionStmt->bindValue('location_id', $locationId);
            $targetQuestionStmt->bindValue('geo_object_id', $geoObjectId);
            $targetQuestionStmt->bindValue('question_id', $question->id);
            $targetQuestionStmt->bindValue('uuid', Uuid::uuid4());
            $targetQuestionStmt->execute();

            $questionId = $this->conn->lastInsertId();

            $referenceAnswersStmt->bindValue('question_id', $question->id);
            $referenceAnswersStmt->execute();

            while ($answer = $referenceAnswersStmt->fetch(\PDO::FETCH_OBJ)) {
                $stmtCopyAnswersStmt->bindValue('question_id', $questionId);
                $stmtCopyAnswersStmt->bindValue('answer_id', $answer->id);
                $stmtCopyAnswersStmt->bindValue('uuid', Uuid::uuid4());
                $stmtCopyAnswersStmt->execute();
            }
        }

        $this->conn->commit();
    }

    public function findSourceGeoObjectId(int $geoObjectId, int $userId): ?int
    {
        $stmt = $this->conn->prepare(/* @lang PostgreSQL */ '
            WITH z AS (
                SELECT
                    g.coordinates,
                    gs.object_type_id
                FROM
                    x_geometry.geometry_base g
                        INNER JOIN
                    x_geospatial.geo_object gs ON g.geo_object_id = gs.id
                WHERE
                    gs.id = :geo_object_id
            )
            SELECT
                o.id
            FROM
                x_geometry.geometry_base r
                    INNER JOIN
                x_geospatial.geo_object o ON r.geo_object_id = o.id
                    INNER JOIN
                x_survey.response_location l ON o.id = l.geo_object_id
                    CROSS JOIN z
            WHERE
                ST_Touches(r.coordinates, z.coordinates)
                AND o.object_type_id = z.object_type_id
                AND l.user_id = :user_id
            ORDER BY
                l.updated_at DESC
            LIMIT 1
        ');

        $stmt->bindValue('geo_object_id', $geoObjectId);
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();

        $result = $stmt->fetchColumn();

        if ($result) {
            return $result;
        }

        return null;
    }
}
