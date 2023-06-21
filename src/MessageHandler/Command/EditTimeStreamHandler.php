<?php

namespace App\MessageHandler\Command;

use App\Message\Command\EditTimeStreamCommand;
use App\Repository\TimeStreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EditTimeStreamHandler
{
    private TimeStreamRepository $repository;

    public function __construct(TimeStreamRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(EditTimeStreamCommand $command): void
    {
        $timeStream = $this->repository->find($command->getId());

        if (!$timeStream) {
            throw new \InvalidArgumentException('No TimeStream found for id '.$command->getId());
        }

        $timeStream->setStartTime($command->getStartTime());
        $timeStream->setEndTime($command->getEndTime());
        $timeStream->setIsStartTime($command->getIsStartTime());

        $this->repository->save($timeStream);
    }
}
