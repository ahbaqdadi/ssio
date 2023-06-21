<?php

namespace App\Entity;

use App\Repository\TimeStreamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Datasets\Unlabeled;

#[ORM\Entity(repositoryClass: TimeStreamRepository::class)]
class TimeStream
{
    public const START = 1;
    public const END = 0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $userId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column]
    private ?bool $isStartTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getIsStartTime(): ?bool
    {
        return $this->isStartTime;
    }

    public function setIsStartTime(bool $isStartTime): static
    {
        $this->isStartTime = $isStartTime;

        return $this;
    }

    public function aiChecking()
    {
        $estimator = PersistentModel::load(new Filesystem('../model.rbx'));

        $duration = null;
        if ($this->getStartTime() && $this->getEndTime()) {
            // Assuming getStartTime and getEndTime return DateTime objects
            $interval = $this->getStartTime()->diff($this->getEndTime());
            $duration = $interval->days * 24 * 60; // Convert to minutes
            $duration += $interval->h * 60;
            $duration += $interval->i;
        }

        $samples[] = [
            $this->getUserId(),
            $duration,
            $this->getIsStartTime() ? 1 : 0, // Convert boolean to integer
        ];

        $dataset = new Unlabeled($samples);

        return $estimator->predict($dataset)[0] == 1 ? 'this is not normal' : 'it is normal';
    }
}
