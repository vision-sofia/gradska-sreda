<?php

namespace App\DataFixtures\Survey;

use App\AppMain\Entity\Geospatial\ObjectType;
use App\AppMain\Entity\Survey\Survey\AuxiliaryObjectType;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\DataFixtures\Geospatial\ObjectTypeFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AxFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            ObjectTypeFixtures::class,
            SurveyFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->data() as $value) {
            $survey = $manager->getRepository(Survey::class)
                ->findOneBy(['name' => $value['survey']])
            ;

            if (null === $survey) {
                break;
            }

            foreach ($value['auxiliary_object'] as $item) {
                $geoObjectType = $manager->getRepository(ObjectType::class)
                    ->findOneBy(['name' => $item['object_type_name']])
                ;

                if (null === $geoObjectType) {
                    break;
                }

                $auxiliaryObjectType = new AuxiliaryObjectType();
                $auxiliaryObjectType->setGeoObjectType($geoObjectType);
                $auxiliaryObjectType->setBehavior($item['behavior']);

                if ('info' === $item['behavior']) {
                    $auxiliaryObjectType->setSurvey($survey);
                }

                $manager->persist($auxiliaryObjectType);
                $manager->flush();
            }
        }
    }

    private function data(): array
    {
        return [
            [
                'survey' => 'Анкета',
                'auxiliary_object' => [
                  /*  [
                        'object_type_name' => 'Подлез',
                        'behavior' => 'info',
                    ], [
                        'object_type_name' => 'Стълбище',
                        'behavior' => 'info',
                    ], */ [
                        'object_type_name' => 'Паркинг',
                        'behavior' => 'info',
                    ], [
                        'object_type_name' => 'Пътно платно',
                        'behavior' => 'info',
                    ], [
                        'object_type_name' => 'Спирка на метро',
                        'behavior' => 'info',
                    ], [
                        'object_type_name' => 'Спирка на градски транспорт',
                        'behavior' => 'info',
                    ],[
                        'object_type_name' => 'Строителна граница',
                        'behavior' => 'info',
                    ],/* [
                        'object_type_name' => 'Градоустройствена единица',
                        'behavior' => 'navigation',
                    ], [
                        'object_type_name' => 'Административен райони',
                        'behavior' => 'navigation',
                    ],*/
                ],
            ],
        ];
    }
}
