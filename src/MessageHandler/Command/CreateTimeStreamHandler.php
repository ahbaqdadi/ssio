<?php

namespace App\MessageHandler\Command;

use App\Entity\TimeStream;
use App\Message\Command\CreateTimeStreamCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateTimeStreamHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(CreateTimeStreamCommand $command): void
    {
        $timeStream = new TimeStream();
        $timeStream->setUserId($command->getUserId());
        $timeStream->setStartTime($command->getStartTime());
        $timeStream->setIsStartTime($command->isStartTime());

        $this->entityManager->persist($timeStream);
        $this->entityManager->flush();
    }
}