<?php

namespace App\MessageHandler\Query;

use App\Entity\TimeStream;
use App\Message\Query\GetListTimeStreamQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetListTimeStreamHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(GetListTimeStreamQuery $query)
    {
        $repository = $this->entityManager->getRepository(TimeStream::class);
        return $repository->findAll();
    }
}