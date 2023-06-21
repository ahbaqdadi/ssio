<?php

namespace App\MessageHandler\Command;

use App\Message\Command\DeleteTimeStreamCommand;
use App\Repository\TimeStreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteTimeStreamHandler
{
    private TimeStreamRepository $repository;

    public function __construct(TimeStreamRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(DeleteTimeStreamCommand $command): void
    {
        $timeStream = $this->repository->find($command->getId());

        if (!$timeStream) {
            throw new \InvalidArgumentException('No TimeStream found for id '.$command->getId());
        }

        $this->repository->delete($timeStream);
    }
}