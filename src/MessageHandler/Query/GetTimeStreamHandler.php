<?php

namespace App\MessageHandler\Query;

use App\Entity\TimeStream;
use App\Message\Query\GetTimeStreamQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetTimeStreamHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(GetTimeStreamQuery $query): ?TimeStream
    {
        $repository = $this->entityManager->getRepository(TimeStream::class);
        return $repository->findOneBy(['userId' => $query->getUserId()]);
    }
}