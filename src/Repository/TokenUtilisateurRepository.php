<?php

namespace App\Repository;

use App\Entity\DoubleAuthentification;
use App\Entity\TokenUtilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TokenUtilisateur>
 */
class TokenUtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenUtilisateur::class);
    }

    //    /**
    //     * @return TokenUtilisateur[] Returns an array of TokenUtilisateur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TokenUtilisateur
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findValidCodeByUtilisateur(string $token, int $validDuration): ?TokenUtilisateur
    {
        $now = new \DateTimeImmutable();
        $validSince = $now->modify("-{$validDuration} seconds");
//        dd($validSince);
        return $this->createQueryBuilder('tu')
            ->where('tu.token = :token')
            ->andWhere('da.daty >= :validSince')
            ->setParameter('idUtilisateur', $token)
            ->setParameter('validSince', $validSince)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
