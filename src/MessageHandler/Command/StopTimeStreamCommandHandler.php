<?php

namespace App\MessageHandler\Command;

use App\Message\Command\StopTimeStreamCommand;
use App\Repository\TimeStreamRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class StopTimeStreamCommandHandler implements MessageHandlerInterface
{
    private TimeStreamRepository $timeStreamRepository;

    public function __construct(TimeStreamRepository $timeStreamRepository)
    {
        $this->timeStreamRepository = $timeStreamRepository;
    }

    public function __invoke(StopTimeStreamCommand $command)
    {
        $timeStream = $command->getTimeStream();
        $timeStream->setIsStartTime(false);
        $timeStream->setEndTime(new \DateTimeImmutable());
        $this->timeStreamRepository->save($timeStream);
    }
}
