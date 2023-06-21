<?php

namespace App\Message\Command;

class EditTimeStreamCommand
{
    private int $id;
    private ?\DateTimeInterface $startTime;
    private ?\DateTimeInterface $endTime;
    private ?bool $isStartTime;

    public function __construct(int $id, ?\DateTimeInterface $startTime, ?\DateTimeInterface $endTime, ?bool $isStartTime)
    {
        $this->id = $id;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->isStartTime = $isStartTime;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function getIsStartTime(): ?bool
    {
        return $this->isStartTime;
    }
}
