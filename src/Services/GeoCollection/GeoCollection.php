<?php


namespace App\Services\GeoCollection;

use App\AppMain\DTO\BoundingBoxDTO;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use PDO;

class GeoCollection
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findCompletion(int $collectionId): array
    {
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                COUNT(*) as total,
                COUNT(*) FILTER ( WHERE uc.is_completed = TRUE ) as completed
            FROM
                x_survey.gc_collection c
                    INNER JOIN
                x_survey.gc_collection_content gc ON c.id = gc.geo_collection_id
                    LEFT JOIN
                x_survey.result_user_completion uc ON uc.geo_object_id = gc.geo_object_id AND uc.user_id = c.user_id
            WHERE
                gc.geo_collection_id = :geo_collection_id
        ');

        $stmt->bindValue('geo_collection_id', $collectionId);
        $stmt->execute();

        $result = $stmt->fetch();

        if ($result['total'] > 0) {
            $percentage = round(($result['completed'] / $result['total']) * 100, 1);
        }

        return [
            'total' => $result['total'],
            'completed' => $result['completed'],
            'percentage' => $percentage ?? 0
        ];
    }

    public function findLength(int $collectionId): float
    {
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                SUM(ST_Length(gb.coordinates))
            FROM
                x_survey.gc_collection_content gc
                    INNER JOIN
                x_geometry.geometry_base gb ON gb.geo_object_id = gc.geo_object_id
            WHERE
                gc.geo_collection_id = ?
        ');

        $stmt->execute([$collectionId]);

        return round($stmt->fetchColumn(), 2);
    }

    public function findCollectionBoundingBox(int $userId, string $collectionUuid): BoundingBoxDTO
    {
        $conn = $this->em->getConnection();

        $qb = $conn->createQueryBuilder()
            ->select('ST_Extent(gb.coordinates::geometry) as w')
            ->from('x_survey.gc_collection', 'c')
            ->innerJoin('c', ' x_survey.gc_collection_content', 'cc', 'c.id = cc.geo_collection_id')
            ->innerJoin('cc', 'x_geospatial.geo_object', 'g', 'cc.geo_object_id = g.id')
            ->innerJoin('g', 'x_geometry.geometry_base', 'gb', 'g.id = gb.geo_object_id')
            ->andWhere('c.user_id = :user_id');

        if ($collectionUuid) {
            $qb->andWhere('c.uuid = :collection_uuid');
            $qb->setParameter('collection_uuid', $collectionUuid);
        }

        $sql = '
            WITH z AS (
                ' . $qb->getSQL() . '
            )
            SELECT
                st_xmin(w) as x_min,
                st_xmax(w) as x_max,
                st_ymin(w) as y_min,
                st_ymax(w) as y_max,
                ST_AsGeoJSON(
                    st_makeenvelope(
                        st_xmin(w),
                        st_xmax(w),
                        st_ymin(w),
                        st_ymax(w)
                    )
                ) as envelope
            FROM z
        ';

        try {
            $stmt = $conn->prepare($sql);

            foreach ($qb->getParameters() as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->bindValue('user_id', $userId);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS, BoundingBoxDTO::class);

            return $stmt->fetch();
        } catch (DBALException $e) {
        }

        return new BoundingBoxDTO();
    }

    public function findCollectionBoundingBoxByUser(int $userId): \Generator
    {
        $conn = $this->em->getConnection();

        $qb = $conn->createQueryBuilder()
            ->select('ST_Expand(ST_Extent(gb.coordinates::geometry), 0.00003) as w')
            ->from('x_survey.gc_collection', 'c')
            ->innerJoin('c', ' x_survey.gc_collection_content', 'cc', 'c.id = cc.geo_collection_id')
            ->innerJoin('cc', 'x_geospatial.geo_object', 'g', 'cc.geo_object_id = g.id')
            ->innerJoin('g', 'x_geometry.geometry_base', 'gb', 'g.id = gb.geo_object_id')
            ->andWhere('c.user_id = :user_id')
            ->groupBy('c.id');

        $sql = '
            WITH z AS (
                ' . $qb->getSQL() . '
            )
            SELECT
                st_xmin(w) as x_min,
                st_xmax(w) as x_max,
                st_ymin(w) as y_min,
                st_ymax(w) as y_max,
                ST_AsGeoJSON(
                    ST_ExteriorRing(st_makeenvelope(
                        st_xmin(w),
                        st_ymin(w),
                        st_xmax(w),
                        st_ymax(w)
                    ))
                ) as envelope
            FROM z
        ';

        try {
            $stmt = $conn->prepare($sql);

            $stmt->bindValue('user_id', $userId);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS, BoundingBoxDTO::class);

            while ($row = $stmt->fetch()) {
                yield $row;
            }
        } catch (DBALException $e) {
        }
    }
}
