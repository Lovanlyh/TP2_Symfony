<?php

namespace App\Repository;

use App\Entity\Infraction;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Infraction>
 */
class InfractionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Infraction::class);
    }

    /**
     * @return Infraction[]
     */
    public function findByFilters(?int $teamId, ?int $driverId, ?DateTimeInterface $date): array
    {
        $qb = $this->createQueryBuilder('i')
            ->leftJoin('i.team', 't')->addSelect('t')
            ->leftJoin('i.driver', 'd')->addSelect('d')
            ->orderBy('i.occurredAt', 'DESC');

        if ($teamId !== null) {
            $qb->andWhere('t.id = :teamId')->setParameter('teamId', $teamId);
        }

        if ($driverId !== null) {
            $qb->andWhere('d.id = :driverId')->setParameter('driverId', $driverId);
        }

        if ($date !== null) {
            $start = $date->setTime(0, 0, 0);
            $end = $date->setTime(23, 59, 59);
            $qb->andWhere('i.occurredAt BETWEEN :start AND :end')
               ->setParameter('start', $start)
               ->setParameter('end', $end);
        }

        return $qb->getQuery()->getResult();
    }
}
