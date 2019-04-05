<?php

namespace App\AppMain\Entity\Survey\Evaluation\Subject;

use App\AppMain\Entity\Survey\Survey\Category;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ev_criterion_subject_metadata", schema="x_survey")
 * @ORM\Entity(readOnly=true)
 */
class Metadata
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="App\AppMain\Entity\Survey\Evaluation\Subject\Criterion")
     * @ORM\JoinColumn(name="criterion_subject_id")
     */
    private $id;

    /**
     * @ORM\Column(name="max_points", type="decimal", precision=3, scale=1)
     */
    private $maxPoints;
}
