<?php

namespace App\Services\Geospatial;

use App\AppMain\Entity\Geospatial\StyleCondition;
use App\AppMain\Entity\Geospatial\StyleGroup;
use Doctrine\ORM\EntityManagerInterface;

class Style
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getDynamicStyles(): array
    {
        /** @var StyleCondition[] $styleConditions */
        $styleConditions = $this->entityManager
            ->getRepository(StyleCondition::class)
            ->findBy([
                'isDynamic' => true,
            ])
        ;

        $result = [];

        foreach ($styleConditions as $styleCondition) {
            $result[$styleCondition->getAttribute()][$styleCondition->getValue()]['base_style'] = $styleCondition->getBaseStyle();
            $result[$styleCondition->getAttribute()][$styleCondition->getValue()]['hover_style'] = $styleCondition->getHoverStyle();
        }

        return $result;
    }

    public function getCompiledStyles(): array
    {
        /** @var StyleGroup[] $stylesGroups */
        $stylesGroups = $this->entityManager
            ->getRepository(StyleGroup::class)
            ->findAll()
        ;

        $result = [];

        foreach ($stylesGroups as $stylesGroup) {
            $result[$stylesGroup->getCode()] = $stylesGroup->getStyle();
        }

        return $result;
    }
}
