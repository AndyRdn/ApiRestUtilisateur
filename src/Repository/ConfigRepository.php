<?php

namespace App\Repository;

use App\Entity\Config;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }

    public function findByNom(string $nom): ?Config
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.nom = :nom')
            ->setParameter('nom', $nom)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function update(Config $config, bool $flush = true): void
    {
        $this->getEntityManager()->persist($config);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}