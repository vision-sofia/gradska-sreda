<?php

namespace App\AppMain\Repository\Survey\Spatial;

use App\AppMain\DTO\SurveyGeoObjectDTO;
use App\AppMain\Entity\Geospatial\GeoObjectInterface;
use App\AppMain\Entity\Survey\Spatial\SurveyGeoObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\Persistence\ManagerRegistry;

class SurveyGeoObjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, SurveyGeoObject::class);
    }

    /**
     * @throws DBALException
     */
    public function isInScope(GeoObjectInterface $geoObject): bool
    {
        $conn = $this->_em->getConnection();

        $stmt = $conn->prepare('
            SELECT EXISTS(
                SELECT
                    *
                FROM
                    x_survey.spatial_scope sc
                        INNER JOIN
                    x_survey.survey s ON sc.survey_id = s.id
                WHERE
                    s.is_active = TRUE
                    AND sc.geo_object_id = ?
            )
        ');

        $stmt->execute([$geoObject->getId()]);

        return $stmt->fetchColumn();
    }

    /**
     * @return SurveyGeoObjectDTO[]|\Generator
     *
     * @throws DBALException
     */
    public function findBySurvey(int $surveyId): \Generator
    {
        $conn = $this->_em->getConnection();

        $stmt = $conn->prepare('
            SELECT
                g.geo_object_id as id,
                g.geo_object_name as geo_name,
                g.base_style,
                g.hover_style,
                g.object_type_name as type_name,
                g.properties,
                st_asgeojson(gb.coordinates) as geometry,
                geometrytype(gb.coordinates) as geometry_type
            FROM
                x_survey.spatial_geo_object g
                    INNER JOIN
                x_geometry.geometry_base gb ON gb.geo_object_id = g.geo_object_id
                    INNER JOIN
                x_survey.survey s ON g.survey_id = s.id AND s.is_active = TRUE
            WHERE
                s.id = ?
        ');

        $stmt->execute([$surveyId]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, SurveyGeoObjectDTO::class);

        while ($row = $stmt->fetch()) {
            yield $row;
        }
    }
}
