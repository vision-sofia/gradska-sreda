<?php


namespace App\AppMain\Entity\Survey\Evaluation\Definition;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ev_indicator_definition", schema="x_survey")
 * @ORM\Entity()
 */
class Indicator implements UuidInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Evaluation\Subject\Criterion")
     * @ORM\JoinColumn(referencedColumnName="id", name="indicator_id", nullable=false)
     */
    private $subject;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Evaluation\Definition\Criterion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $criterion;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getCriterion()
    {
        return $this->criterion;
    }

    /**
     * @param mixed $criterion
     */
    public function setCriterion($criterion): void
    {
        $this->criterion = $criterion;
    }


}