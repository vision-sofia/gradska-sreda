<?php


namespace App\Services\GeoCollection;

use App\AppMain\DTO\BoundingBoxDTO;
use App\AppMain\Entity\UuidInterface;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Connection;
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

    /**
     * @return BoundingBoxDTO[]|\Generator
     */
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

    public function userGeoCollectionLinks(int $userId, float $simplifyTolerance): \Generator
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            WITH z AS (
                SELECT
                    g.id,
                    gb.coordinates
                FROM
                    x_survey.gc_collection c
                        INNER JOIN
                    x_survey.gc_collection_content cc ON c.id = cc.geo_collection_id
                        INNER JOIN
                    x_geospatial.geo_object g ON cc.geo_object_id = g.id
                        INNER JOIN
                    x_geometry.geometry_base gb ON g.id = gb.geo_object_id
                WHERE
                    user_id = :user_id
            )
            SELECT
                g.id,
                g.uuid,
                g.style_base,
                g.style_hover,
                g.name as geo_name,
                \'\' as type_name,
                ST_AsGeoJSON(ST_Simplify(gb.coordinates::geometry, :simplify_tolerance, true)) AS geometry,
                jsonb_build_object(
                    \'gc_edge\', 0
                ) as properties
            FROM
                x_geospatial.geo_object g
                    INNER JOIN
                x_geometry.geometry_base gb ON g.id = gb.geo_object_id
                    CROSS JOIN z
            WHERE
                st_touches(gb.coordinates::geometry, z.coordinates::geometry)
                AND NOT EXISTS(SELECT * FROM x_survey.gc_collection_content c WHERE c.geo_object_id = gb.geo_object_id)
        ');

        $stmt->bindValue('simplify_tolerance', $simplifyTolerance);
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    public function countInterconnectedClusters(string $geoCollectionUuid): ?int
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                array_length(ST_ClusterIntersecting(gb.coordinates::geometry), 1)
            FROM  
                x_survey.gc_collection c
                    INNER JOIN
                x_survey.gc_collection_content cc ON c.id = cc.geo_collection_id
                    INNER JOIN
                x_geospatial.geo_object go ON cc.geo_object_id = go.id
                    INNER JOIN
                x_geometry.geometry_base gb ON go.id = gb.geo_object_id
            WHERE 
                c.uuid = ?
        ');

        $stmt->execute([$geoCollectionUuid]);

        return $stmt->fetchColumn();
    }

    public function isTouchingGeoCollection(string $geoCollectionUuid, int $geoCollectionOwnerId, string $targetObjectUuid): bool
    {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            SELECT EXISTS(
                SELECT
                    *
                FROM
                    x_survey.gc_collection_content cc
                        INNER JOIN
                    x_survey.gc_collection c ON cc.geo_collection_id = c.id
                        INNER JOIN
                    x_geometry.geometry_base g ON g.geo_object_id = cc.geo_object_id
                        LEFT JOIN
                    x_geospatial.geo_object g1 ON g1.uuid = :geo_object_uuid
                        INNER JOIN
                    x_geometry.geometry_base gb1 ON g1.id = gb1.geo_object_id
                WHERE
                    c.uuid = :geo_collection_uuid
                    AND c.user_id = :user_id
                    AND ST_Touches(g.coordinates::geometry, gb1.coordinates::geometry)
            )
        ');

        $stmt->bindValue('geo_object_uuid', $targetObjectUuid);
        $stmt->bindValue('geo_collection_uuid', $geoCollectionUuid);
        $stmt->bindValue('user_id', $geoCollectionOwnerId);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function updateBBoxGeometry(int $geoCollectionId) {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            UPDATE
                x_survey.gc_collection c0
            SET
                bbox_geometry = (
                    WITH z AS (
                        SELECT
                            ST_Expand(ST_Extent(gb.coordinates::geometry), 0.00003) as w
                        FROM
                            x_survey.gc_collection c
                                INNER JOIN
                            x_survey.gc_collection_content cc ON c.id = cc.geo_collection_id
                                INNER JOIN
                            x_geospatial.geo_object g ON cc.geo_object_id = g.id
                                INNER JOIN x_geometry.geometry_base gb ON g.id = gb.geo_object_id
                        WHERE
                             c.id = c0.id
                        GROUP BY c.id
                    )
                    SELECT
                        ST_MakeEnvelope(
                            st_xmin(w),
                            st_ymin(w),
                            st_xmax(w),
                            st_ymax(w)
                        )
                    FROM z
                )
            WHERE
                c0.id = ?
        ');

        $stmt->execute([$geoCollectionId]);
    }

    public function updateBBoxMetadata(int $geoCollectionId) {
        /** @var Connection $conn */
        $conn = $this->em->getConnection();

        $stmt = $conn->prepare('
            UPDATE
                x_survey.gc_collection c0
            SET
                bbox_metadata = (
                    SELECT
                        row_to_json(z) as z
                    FROM
                    (
                    SELECT
                        ST_AsGeoJSON(bbox_geometry::geometry)::jsonb as geometry_bbox,
                        ST_AsGeoJSON(ST_ExteriorRing(bbox_geometry::geometry))::jsonb as geometry_exterior,
                        ST_AsGeoJSON(ST_Centroid(bbox_geometry::geometry))::jsonb as geometry_center,
                        ST_X(ST_Centroid(bbox_geometry::geometry)) as x_center,
                        ST_Y(ST_Centroid(bbox_geometry::geometry)) as y_center,
                        ST_XMin(bbox_geometry::geometry) as x_min,
                        ST_XMax(bbox_geometry::geometry) as x_max,
                        ST_Ymin(bbox_geometry::geometry) as y_min,
                        ST_YMax(bbox_geometry::geometry) as y_max
                    FROM
                        x_survey.gc_collection c
                    WHERE
                        c.id = c0.id
                    ) z
                ) 
            WHERE
                c0.id = ?
        ');

        $stmt->execute([$geoCollectionId]);
    }
}
