<?php

namespace App\AppMain\Repository\Geospatial;

use App\AppMain\Entity\Geospatial\GeoObjectInterface;
use Doctrine\ORM\EntityRepository;


class GeoObjectRepository extends EntityRepository
{
    public function isAvailableForSurvey(GeoObjectInterface $geoObject): ?bool
    {
        $conn = $this->_em->getConnection();

        $stmt = $conn->prepare('
            SELECT EXISTS(
                SELECT
                    *
                FROM 
                    x_survey.survey_scope sc
                        INNER JOIN
                    x_survey.survey s ON sc.survey_id = s.id
                WHERE
                    sc.geo_object_id = :geo_object_id
                    AND s.is_active = TRUE
            )                
        ');

        $stmt->bindValue('geo_object_id', $geoObject->getId());
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}
