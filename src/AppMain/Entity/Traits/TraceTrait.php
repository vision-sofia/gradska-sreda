<?php

namespace App\AppMain\Entity\Traits;

use App\AppMain\Entity\User\UserInterface;

trait TraceTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="App\AppMain\Entity\User\User")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false, name="added_by")
     */
    private UserInterface $addedBy;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $addedAt;

    public function getAddedBy(): UserInterface
    {
        return $this->addedBy;
    }

    public function setAddedBy(UserInterface $addedBy): void
    {
        $this->addedBy = $addedBy;
    }

    public function setAddedAt(\DateTimeInterface $dateTimeImmutable): void
    {
        $this->addedAt = $dateTimeImmutable;
    }

    public function getAddedAt(): \DateTimeInterface
    {
        return $this->addedAt;
    }
}
