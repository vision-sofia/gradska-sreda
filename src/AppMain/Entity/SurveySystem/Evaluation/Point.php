<?php


namespace App\AppMain\Entity\SurveySystem\Evaluation;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ed_point", schema="x_survey")
 * @ORM\Entity()
 */
class Point implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", scale=1, precision=2)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\SurveySystem\Question\Answer")
     * @ORM\JoinColumn(referencedColumnName="id", name="answer_id", nullable=false)
     */
    private $answer;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\SurveySystem\Evaluation\Indicator")
     * @ORM\JoinColumn(referencedColumnName="id", name="indicator_id", nullable=false)
     */
    private $indicator;
}