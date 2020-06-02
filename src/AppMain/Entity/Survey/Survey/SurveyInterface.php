<?php

namespace App\AppMain\Entity\Survey\Survey;

interface SurveyInterface
{
    public function getId();

    public function getName();

    public function setName(string $name);

    public function getIsActive();

    public function setIsActive(bool $isActive);

    public function getStartDate();

    public function setStartDate(\DateTimeInterface $startDate);

    public function getEndDate();

    public function setEndDate(\DateTimeInterface $endDate);
}
