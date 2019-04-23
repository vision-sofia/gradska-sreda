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
            ->select('ST_Extent(gb.coordinates::geometry) as w')
            ->from('x_survey.gc_collection', 'c')
            ->innerJoin('c', ' x_survey.gc_collection_content', 'cc', 'c.id = cc.geo_collection_id')
            ->innerJoin('cc', 'x_geospatial.geo_object', 'g', 'cc.geo_object_id = g.id')
            ->innerJoin('g', 'x_geometry.geometry_base', 'gb', 'g.id = gb.geo_object_id')
            ->andWhere('c.user_id = :user_id')
            ->groupBy('c.id')
        ;

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
