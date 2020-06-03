<?php

namespace App\Services\ApiFrontend;

use App\AppMain\DTO\GeoObjectRatingDTO;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;

class GeoObjectRating
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRatingByUser(int $geoObjectId): array
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        // TODO: ORDER BY last completed survey
        $stmt = $conn->prepare('
            SELECT
                u.username AS username,
                cr.name AS criterion,
                round(AVG(gr.rating), 2) AS rating,
                (cr.metadata->\'max_points\') as max
            FROM
                x_survey.result_geo_object_rating gr
                    INNER JOIN
                x_survey.ev_criterion_subject cr ON gr.criterion_subject_id = cr.id
                    INNER JOIN
                x_main.user_base u ON gr.user_id = u.id
            WHERE
                gr.geo_object_id = ?
            GROUP BY
                cr.id, u.id
        ');

        $stmt->execute([$geoObjectId]);

        $result = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $rating = new GeoObjectRatingDTO();
            $rating->max = (float) $row['max'];
            $rating->rating = (float) $row['rating'];
            $rating->criterion = $row['criterion'];
            $rating->percentage = round(($rating->rating / $rating->max) * 100, 1);

            $result[$row['username']][] = $rating;
        }

        return $result;
    }

    public function getOverallRating(int $geoObjectId): array
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                cr.name AS criterion,
                round(AVG(gr.rating), 2) as rating,
                (cr.metadata->\'max_points\') as max
            FROM
                x_survey.result_geo_object_rating gr
                    INNER JOIN
                x_survey.ev_criterion_subject cr ON gr.criterion_subject_id = cr.id
            WHERE
                gr.geo_object_id = ?
            GROUP BY
                cr.id               
        ');

        $stmt->execute([$geoObjectId]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, GeoObjectRatingDTO::class);

        $result = [];

        /** @var GeoObjectRatingDTO $row */
        while ($row = $stmt->fetch()) {
            $row->max = (float) $row->max;
            $row->rating = (float) $row->rating;
            $row->percentage = round(($row->rating / $row->max) * 100, 1);

            $result[] = $row;
        }

        return $result;
    }
}
