<?php

namespace App\Message\Command;

use App\Entity\TimeStream;

class StopTimeStreamCommand
{
    private TimeStream $timeStream;

    public function __construct(TimeStream $timeStream)
    {
        $this->timeStream = $timeStream;
    }

    public function getTimeStream(): TimeStream
    {
        return $this->timeStream;
    }
}
