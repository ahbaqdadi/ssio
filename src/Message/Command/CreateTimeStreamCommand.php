<?php

namespace App\Message\Command;

use App\Entity\TimeStream;

class CreateTimeStreamCommand
{
    private int $userId;
    private \DateTimeInterface $startTime;
    private bool $isStartTime;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
        $this->startTime = new \DateTimeImmutable();
        $this->isStartTime = TimeStream::START;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getStartTime(): \DateTimeInterface
    {
        return $this->startTime;
    }

    public function isStartTime(): bool
    {
        return $this->isStartTime;
    }
}