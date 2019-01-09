<?php


namespace App\AppMain\Entity\Survey\Evaluation;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ev_indicator", schema="x_survey")
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\Survey\Evaluation\Criterion", inversedBy="indicators")
     */
    private $criterion;

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function getCriterion()
    {
        return $this->criterion;
    }


    public function setCriterion($criterion): void
    {
        $this->criterion = $criterion;
    }


}