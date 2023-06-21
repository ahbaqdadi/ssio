<?php

namespace App\Repository;

use App\Entity\TimeStream;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeStream>
 *
 * @method TimeStream|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeStream|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeStream[]    findAll()
 * @method TimeStream[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeStreamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeStream::class);
    }

    public function save(TimeStream $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TimeStream $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findRunningForUser($userId)
    {
        return $this->createQueryBuilder('t')
            ->where('t.userId = :user')
            ->andWhere('t.isStartTime = :isStartTime')
            ->setParameters([
                'user' => $userId,
                'isStartTime' => true
            ])
            ->orderBy('t.startTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function delete(TimeStream $timeStream): void
    {
        $this->_em->remove($timeStream);
        $this->_em->flush();
    }
}
